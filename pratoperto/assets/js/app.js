(function () {
'use strict';
const $ = (selector, root = document) => root.querySelector(selector);
const main = $('#main'), modal = $('#modal'), cartDialog = $('#cart-dialog');
let state = {restaurants: [], products: [], favorites: [], address: {}, cart: null, csrf: '', loaded: false};
let filters = {query: '', category: 'Todos', free: false, open: false, favorites: false, sort: 'recommended'};
let routeSequence = 0, submitting = false, toastTimer, currentProduct = null;
const esc = value => String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
const money = value => new Intl.NumberFormat('pt-BR', {style: 'currency', currency: 'BRL'}).format(Number(value) / 100);
const fold = value => String(value).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
const art = (kind, alt = '', cls = '') => '<img class="' + cls + '" src="assets/img/' + esc(kind) + '.svg" alt="' + esc(alt) + '" loading="lazy" width="600" height="440">';
const uuid = () => {
  if (crypto.randomUUID) return crypto.randomUUID();
  const b = crypto.getRandomValues(new Uint8Array(16)); b[6] = (b[6] & 15) | 64; b[8] = (b[8] & 63) | 128;
  const h = Array.from(b, n => n.toString(16).padStart(2, '0')).join('');
  return h.slice(0,8)+'-'+h.slice(8,12)+'-'+h.slice(12,16)+'-'+h.slice(16,20)+'-'+h.slice(20);
};
function toast(message) { const el = $('#toast'); el.textContent = message; el.hidden = false; clearTimeout(toastTimer); toastTimer = setTimeout(() => el.hidden = true, 4800); }
function apply(data) {
  for (const key of ['cart','address','favorites','csrf']) if (key in data) state[key] = data[key];
  updateHeader();
}
async function api(action, body, id) {
  let response;
  try {
    response = await fetch('api.php?action=' + encodeURIComponent(action) + (id ? '&id=' + encodeURIComponent(id) : ''), {
      method: body === undefined ? 'GET' : 'POST', cache: 'no-store',
      headers: body === undefined ? {} : {'Content-Type':'application/json', 'X-CSRF-Token':state.csrf}, body: body === undefined ? undefined : JSON.stringify(body)
    });
  } catch (_) { throw new Error('Sem conexão com o servidor local. Verifique o XAMPP e tente novamente.'); }
  let data;
  try { data = await response.json(); } catch (_) { throw new Error('O servidor não respondeu corretamente. Confira a configuração do PHP.'); }
  if (!response.ok || !data.ok) {
    if (data.state) apply(data.state);
    const error = new Error(data.message || 'Não foi possível concluir. Tente novamente.');
    error.code = data.code; error.status = response.status; throw error;
  }
  apply(data); return data;
}
function updateHeader() {
  const c = state.cart;
  $('#header-address').textContent = state.address.street ? state.address.street + ', ' + state.address.number : 'Escolha seu endereço';
  $('#header-total').textContent = money(c ? c.total : 0);
  $('#header-count').textContent = c && c.count ? c.count + (c.count === 1 ? ' item' : ' itens') : 'Seu carrinho';
  $('#cart-badge').textContent = c ? c.count : 0; $('#cart-badge').hidden = !c || !c.count;
  $('#mobile-count').textContent = c ? c.count : 0;
  const route = location.hash.split('/')[1] || 'inicio';
  document.querySelectorAll('[data-nav]').forEach(link => { if (link.dataset.nav === route) link.setAttribute('aria-current','page'); else link.removeAttribute('aria-current'); });
}
function routeTo(route) { if (location.hash === '#/' + route) renderRoute(); else location.hash = '/' + route; }
function showDialog(content) { $('#modal-content').innerHTML = content; if (!modal.open) modal.showModal(); }
function title(text, small = '') { return '<div class="dialog-heading">' + (small ? '<span class="eyebrow">'+esc(small)+'</span>' : '') + '<h2 id="modal-title">'+esc(text)+'</h2></div>'; }
function errorBox() { return '<p class="form-error" role="alert" hidden></p>'; }
function focusAction(action,value,field='value') {
  const candidate=document.querySelector('[data-action="'+action+'"][data-'+field+'="'+CSS.escape(value)+'"]');
  if(candidate&&!candidate.disabled) candidate.focus({preventScroll:true});
}
function setError(form, message) { const el = $('.form-error',form); if (el) {el.textContent = message; el.hidden = false;} else toast(message); }
function home() {
  main.innerHTML = '<div class="shell home"><section class="hero"><div class="hero-content"><span class="eyebrow">COMIDA BOA. DO SEU JEITO.</span><h1>Hoje combina<br>com o quê<span>?</span></h1><p>Seu favorito de sempre ou uma nova descoberta.<br>Encontre uma boa escolha aqui perto.</p><form id="search-form" class="search"><label class="sr-only" for="search">Buscar restaurantes ou pratos</label><span aria-hidden="true">⌕</span><input id="search" type="search" placeholder="Restaurante, prato ou vontade…" value="'+esc(filters.query)+'"><button type="submit" aria-label="Buscar"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12H20M13 5L20 12L13 19"/></svg></button></form><small>Uma seleção fictícia para explorar o delivery.</small></div><div class="hero-art" aria-hidden="true"><span class="orbit one"></span><span class="orbit two"></span>'+art('bowl','','hero-bowl')+art('burger','','hero-burger')+'<span class="art-note"><i></i>Uma pausa bem servida.</span></div></section><section class="categories" aria-label="Categorias">'+[
    ['Todos','coffee','Todas as vontades'],['Hambúrgueres','burger','Hambúrgueres'],['Pizza','pizza','Pizza'],['Saudável','bowl','Saudável'],['Japonesa','sushi','Japonesa'],['Massas','pasta','Massas'],['Doces','cake','Doces']
  ].map(([cat,kind,label])=>'<button class="category '+(filters.category===cat?'active':'')+'" data-action="category" data-value="'+cat+'" aria-pressed="'+(filters.category===cat)+'">'+art(kind)+'<span>'+label+'</span></button>').join('')+'</section><section class="offers" aria-label="Cupons da demonstração"><div class="offer"><span class="offer-icon" aria-hidden="true">%</span><div><small>UM BOM COMEÇO</small><h2>10% para experimentar</h2><p>Use <b>BEMVINDO10</b> · até R$ 15 de desconto.</p></div><span class="offer-tag">Cupom de teste</span></div><div class="offer second"><span class="offer-icon" aria-hidden="true"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6H18V18"/></svg></span><div><small>MAIS SABOR, MENOS TAXA</small><h2>A entrega fica por nossa conta</h2><p>Use <b>ENTREGAGRATIS</b> no checkout de teste.</p></div></div></section><section class="catalog" aria-labelledby="catalog-title"><div class="section-heading"><div><span class="eyebrow">SEU PRÓXIMO FAVORITO</span><h2 id="catalog-title">Uma boa pedida por perto</h2></div><label class="sort-label">Ordenar por <select id="sort"><option value="recommended">Nossa seleção</option><option value="time">Menor tempo</option><option value="fee">Menor taxa</option><option value="minimum">Menor pedido mínimo</option></select></label></div><div class="filters"><button data-action="filter" data-value="free" aria-pressed="'+filters.free+'">Entrega grátis</button><button data-action="filter" data-value="open" aria-pressed="'+filters.open+'">Abertos agora</button><button data-action="filter" data-value="favorites" aria-pressed="'+filters.favorites+'">♡ Favoritos</button><span id="result-count" role="status" aria-live="polite"></span></div><div id="restaurant-grid" class="restaurant-grid"></div></section><section class="how"><span class="eyebrow">SIMPLES, COMO DEVE SER</span><h2>Da vontade ao primeiro pedido.</h2><div class="how-grid"><div><b>01</b><h3>Encontre sua vontade</h3><p>Explore a seleção ou busque aquele prato que você já tem em mente.</p></div><div><b>02</b><h3>Deixe com a sua cara</h3><p>Escolha a porção, escreva uma observação e confira todos os valores.</p></div><div><b>03</b><h3>Experimente o caminho</h3><p>Confirme um pedido fictício e veja como funciona o acompanhamento.</p></div></div></section></div>';
  $('#sort').value = filters.sort; renderCatalog();
}
function renderCatalog() {
  const grid = $('#restaurant-grid'); if (!grid) return;
  const query = fold(filters.query);
  let list = state.restaurants.filter(r => (filters.category==='Todos'||r.category===filters.category) && (!filters.free||r.fee===0) && (!filters.open||r.is_open) && (!filters.favorites||state.favorites.includes(r.id)) && (!query || fold(r.name+' '+r.category+' '+state.products.filter(p=>p.restaurant_id===r.id).map(p=>p.name).join(' ')).includes(query)));
  if(filters.sort==='time') list.sort((a,b)=>a.eta_min-b.eta_min);
  if(filters.sort==='fee') list.sort((a,b)=>a.fee-b.fee);
  if(filters.sort==='minimum') list.sort((a,b)=>a.minimum-b.minimum);
  $('#result-count').textContent = list.length+' restaurantes';
  grid.innerHTML = list.length ? list.map(r => '<article class="restaurant-card '+(!r.is_open?'is-closed':'')+'"><div class="restaurant-cover" style="background:'+esc(r.color)+'"><a href="#/restaurante/'+esc(r.slug)+'" tabindex="-1" aria-hidden="true">'+art(r.kind)+'</a><span class="card-tag">'+esc(r.tag)+'</span><button class="favorite" data-action="favorite" data-id="'+r.id+'" aria-label="Favoritar '+esc(r.name)+'" aria-pressed="'+state.favorites.includes(r.id)+'">'+(state.favorites.includes(r.id)?'♥':'♡')+'</button>'+(!r.is_open?'<span class="closed-label">Fechado agora</span>':'')+'</div><div class="card-body"><a class="restaurant-name" href="#/restaurante/'+esc(r.slug)+'">'+esc(r.name)+' <span aria-hidden="true"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6H18V18"/></svg></span></a><p>'+esc(r.category)+'<span class="dot">·</span>'+r.eta_min+'–'+r.eta_max+' min</p><div class="card-bottom"><span class="'+(r.fee===0?'free-delivery':'')+'">'+(r.fee===0?'Entrega grátis':'Entrega '+money(r.fee))+'</span><small>Mín. '+money(r.minimum)+'</small></div></div></article>').join('') : '<div class="empty"><span aria-hidden="true">⌕</span><h3>Nenhuma pedida por aqui ainda.</h3><p>Tente outro termo ou retire um filtro.</p><button class="button secondary" data-action="resetfilters">Limpar filtros</button></div>';
}
function restaurantPage(slug) {
  const r = state.restaurants.find(r=>r.slug===slug);
  if(!r) {main.innerHTML='<div class="shell empty"><h1>Restaurante não encontrado</h1><a class="button" href="#/inicio">Voltar para descobrir</a></div>';return;}
  const products = state.products.filter(p=>p.restaurant_id===r.id);
  main.innerHTML = '<div class="shell restaurant-page"><a class="back-link" href="#/inicio"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H4M11 5L4 12L11 19"/></svg> Todos os restaurantes</a><section class="restaurant-hero"><div><span class="eyebrow">'+esc(r.category)+'</span><h1>'+esc(r.name)+'</h1><p>'+esc(r.description)+'</p><div class="restaurant-details"><span>'+r.eta_min+'–'+r.eta_max+' min <small>Estimativa fictícia</small></span><span>'+(r.fee===0?'Entrega grátis':money(r.fee)+' de entrega')+'</span><span>Pedido mínimo '+money(r.minimum)+'</span></div><button class="button secondary" data-action="favorite" data-id="'+r.id+'" aria-pressed="'+state.favorites.includes(r.id)+'">'+(state.favorites.includes(r.id)?'♥ Nos seus favoritos':'♡ Guardar nos favoritos')+'</button></div>'+art(r.kind, 'Ilustração de '+r.category)+'</section>'+(!r.is_open?'<p class="notice">Este restaurante está fechado na demonstração. Explore o cardápio ou escolha outro restaurante para pedir.</p>':'')+'<div class="menu-layout"><div>'+[...new Set(products.map(p=>p.group_name))].map(group=>'<section class="menu-section"><h2>'+esc(group)+'</h2><div class="product-grid">'+products.filter(p=>p.group_name===group).map(p=>'<button class="product-card" data-action="product" data-id="'+p.id+'" '+(!p.available||!r.is_open?'disabled':'')+'><span class="product-copy"><strong>'+esc(p.name)+'</strong><span>'+esc(p.description)+'</span><b>'+money(p.price)+'</b>'+(!p.available?'<small>Indisponível no momento</small>':'')+'</span><span class="product-picture">'+art(p.image)+'<span class="add-mark" aria-hidden="true">+</span></span></button>').join('')+'</div></section>').join('')+'</div><aside class="menu-aside"><span class="eyebrow">FEITO DO SEU JEITO</span><h2>Um detalhe faz<br>toda a diferença.</h2><p>Ao escolher um prato, você pode ajustar a porção e deixar uma observação de teste.</p><button class="button" data-action="cart">Ver meu carrinho <svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12H20M13 5L20 12L13 19"/></svg></button><small>Sem pedidos reais ou cobrança.</small></aside></div></div>';
}
function showAddress() {
  const a = state.address;
  showDialog(title('Onde chega sua próxima pedida?', 'ENDEREÇO DE DEMONSTRAÇÃO')+'<p>Use um endereço fictício. Ele fica apenas no servidor local de teste.</p><form id="address-form" class="address-form"><label>Rua<input name="street" required maxlength="100" autocomplete="off" value="'+esc(a.street)+'" placeholder="Rua das Palmeiras"></label><div class="form-row"><label>Número<input name="number" required maxlength="12" value="'+esc(a.number)+'" placeholder="120"></label><label>Bairro<input name="district" required maxlength="80" value="'+esc(a.district)+'" placeholder="Jardim"></label></div><label>Cidade<input name="city" required maxlength="80" value="'+esc(a.city)+'" placeholder="Cidade de Teste"></label><label>Complemento <small>opcional</small><input name="extra" maxlength="120" value="'+esc(a.extra)+'" placeholder="Apartamento, referência…"></label>'+errorBox()+'<button class="button full" type="submit">Salvar endereço</button><button class="text-button" type="button" data-action="exampleaddress">Preencher com um exemplo fictício</button></form>');
}
function showProduct(id) {
  currentProduct = state.products.find(p=>p.id===id); if(!currentProduct) return;
  const p = currentProduct;
  showDialog('<div class="product-modal-art">'+art(p.image)+'</div>'+title(p.name)+'<p>'+esc(p.description)+'</p><form id="product-form"><fieldset><legend>Escolha sua porção</legend>'+p.options.map((o,i)=>'<label class="option-row"><input type="radio" name="option" value="'+i+'" '+(i===0?'checked':'')+'><span>'+esc(o.name)+'</span><b>'+(o.extra?'+ '+money(o.extra):'Incluso')+'</b></label>').join('')+'</fieldset><label class="note-label">Alguma observação? <small>opcional · até 180 caracteres</small><textarea name="note" maxlength="180" rows="2" placeholder="Ex.: sem cebola. Use apenas informações fictícias."></textarea></label><div class="product-controls"><div class="stepper"><button type="button" data-action="productstep" data-step="-1" aria-label="Diminuir quantidade">−</button><output id="product-quantity" aria-live="polite">1</output><button type="button" data-action="productstep" data-step="1" aria-label="Aumentar quantidade">+</button></div><button class="button" type="submit">Adicionar <span id="product-price">'+money(p.price)+'</span></button></div>'+errorBox()+'</form>');
}
function costs(c) {
  return '<dl class="costs"><div><dt>Subtotal dos itens</dt><dd>'+money(c.subtotal)+'</dd></div><div><dt>Entrega</dt><dd>'+(c.fee?money(c.fee):'Grátis')+'</dd></div>'+(c.discount?'<div class="discount"><dt>Desconto '+esc(c.coupon)+'</dt><dd>− '+money(c.discount)+'</dd></div>':'')+'<div class="total"><dt>Total</dt><dd>'+money(c.total)+'</dd></div></dl>';
}
function cartContent() {
  const c = state.cart; if(!c) return;
  $('#cart-content').innerHTML = '<span class="eyebrow">UMA BOA ESCOLHA</span><h2 id="cart-title">Seu carrinho</h2>' + (!c.items.length ? '<div class="empty"><span aria-hidden="true"><svg class="inline-icon cart-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9H20L18 20H6ZM8 9L12 3L16 9M9 12V16M15 12V16"/></svg></span><h3>Ainda tem espaço para uma boa pedida.</h3><p>Encontre um restaurante e escolha seu primeiro prato.</p><button class="button" data-action="discover">Explorar restaurantes</button></div>' : '<p class="cart-restaurant">'+esc(c.restaurant.name)+'</p><div class="cart-items">'+c.items.map(item=>'<article class="cart-item">'+art(item.image)+'<div><h3>'+esc(item.name)+'</h3><small>'+esc(item.option_name)+'</small>'+(item.note?'<p class="item-note">'+esc(item.note)+'</p>':'')+'<strong>'+money(item.total)+'</strong><div class="item-actions"><div class="stepper"><button data-action="cartqty" data-id="'+item.id+'" data-quantity="'+(item.quantity-1)+'" aria-label="Diminuir '+esc(item.name)+'">−</button><span>'+item.quantity+'</span><button data-action="cartqty" data-id="'+item.id+'" data-quantity="'+(item.quantity+1)+'" '+(item.quantity>=20?'disabled':'')+' aria-label="Aumentar '+esc(item.name)+'">+</button></div><button class="text-button" data-action="remove" data-id="'+item.id+'" aria-label="Remover '+esc(item.name)+'">Remover</button></div></div></article>').join('')+'</div>'+costs(c)+(c.remaining_minimum?'<p class="notice">Faltam '+money(c.remaining_minimum)+' para o pedido mínimo.</p>':'')+'<button class="button full" data-action="checkout" '+(c.remaining_minimum?'disabled':'')+'>Continuar para o pedido <svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12H20M13 5L20 12L13 19"/></svg></button><p class="fineprint">Pedido fictício. Nenhum valor será cobrado.</p>');
}
function showCart() {cartContent(); if(state.cart && !cartDialog.open) cartDialog.showModal();}
function checkoutPage() {
  const c = state.cart;
  if(!c.items.length) {main.innerHTML='<div class="shell empty"><h1>Seu carrinho está vazio</h1><p>Escolha um prato para começar seu pedido de teste.</p><a class="button" href="#/inicio">Descobrir restaurantes</a></div>';return;}
  main.innerHTML='<div class="shell checkout-page"><a class="back-link" href="#/restaurante/'+esc(c.restaurant.slug)+'"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H4M11 5L4 12L11 19"/></svg> Voltar ao cardápio</a><span class="eyebrow">QUASE NA MESA</span><h1>Confira sua boa pedida.</h1><p class="page-intro">Tudo às claras. Este pedido é apenas uma demonstração.</p><div class="checkout-layout"><div><section class="checkout-panel"><div class="section-heading"><h2><span class="step-number">1</span>Endereço de teste</h2><button class="text-button" data-action="address">'+(state.address.street?'Alterar':'Adicionar')+'</button></div>'+(state.address.street?'<p><strong>'+esc(state.address.street)+', '+esc(state.address.number)+'</strong><br>'+esc(state.address.district)+' · '+esc(state.address.city)+(state.address.extra?'<br>'+esc(state.address.extra):'')+'</p>':'<p>Escolha um endereço fictício para continuar.</p>')+'</section><section class="checkout-panel"><h2><span class="step-number">2</span>Pagamento simulado</h2><p>Nenhuma chave Pix ou dado de cartão é necessário.</p><fieldset class="payment-options"><legend class="sr-only">Forma de pagamento de demonstração</legend>'+[['demo_pix','Pix de teste','Sem QR code ou transferência'],['demo_card','Cartão de teste','Sem informar dados do cartão'],['demo_cash','Na entrega de teste','Nenhuma entrega será realizada']].map(([value,label,small],i)=>'<label class="payment-option"><input type="radio" name="payment" value="'+value+'" '+(i===0?'checked':'')+'><span><strong>'+label+'</strong><small>'+small+'</small></span></label>').join('')+'</fieldset></section><section class="checkout-panel"><h2><span class="step-number">3</span>Um desconto combina?</h2><form id="coupon-form" class="coupon-form"><label class="sr-only" for="coupon">Código do cupom</label><input id="coupon" name="code" maxlength="30" placeholder="Seu cupom de teste" value="'+esc(c.coupon)+'"><button class="button secondary" type="submit">Aplicar</button>'+errorBox()+'</form>'+(c.coupon?'<p class="coupon-success">✓ '+esc(c.coupon)+' aplicado <button class="text-button" data-action="removecoupon">Remover</button></p>':'')+'<p class="fineprint">BEMVINDO10: 10%, até R$ 15 · ENTREGAGRATIS: desconto da taxa. Um cupom por pedido.</p></section></div><aside class="order-summary"><span class="eyebrow">RESUMO DO PEDIDO</span><h2>'+esc(c.restaurant.name)+'</h2>'+c.items.map(item=>'<div class="summary-item"><span><b>'+item.quantity+'×</b> '+esc(item.name)+'<small>'+esc(item.option_name)+'</small></span><strong>'+money(item.total)+'</strong></div>').join('')+costs(c)+(c.remaining_minimum?'<p class="notice">Faltam '+money(c.remaining_minimum)+' para o mínimo.</p>':'')+(!state.address.street?'<p class="notice">Adicione seu endereço de teste.</p>':'')+'<p id="checkout-error" class="form-error" role="alert" hidden></p><button class="button full" data-action="placeorder" '+(!state.address.street||c.remaining_minimum?'disabled':'')+'>Confirmar demonstração <svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12H20M13 5L20 12L13 19"/></svg></button><p class="fineprint">Ao confirmar, você cria um registro local fictício. Sem cobrança ou contato com restaurantes.</p></aside></div></div>';
  main.focus({preventScroll:true});
}
function orderTimeline(o) {
  if(o.status==='cancelled') return '<p class="notice">Pedido de demonstração cancelado. Nenhuma cobrança foi feita.</p>';
  const elapsed = Math.max(0,(Date.now()-Date.parse(o.created_at))/1000);
  const current = Math.min(3,Math.floor(elapsed/30));
  return '<ol class="timeline">'+['Confirmado','Preparando','A caminho','Concluído'].map((label,i)=>'<li class="'+(i<=current?'done':'')+'" '+(i===current?'aria-current="step"':'')+'><span aria-hidden="true">'+(i<=current?'✓':i+1)+'</span><strong>'+label+'</strong></li>').join('')+'</ol><p class="fineprint">Linha do tempo automática e simulada: avança a cada 30 segundos. Não representa uma entrega real.</p>';
}
function orderPage(o) {
  main.innerHTML='<div class="shell order-page"><a class="back-link" href="#/pedidos"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H4M11 5L4 12L11 19"/></svg> Meus pedidos</a><section class="order-success"><div class="success-mark" aria-hidden="true">'+(o.status==='cancelled'?'×':'✓')+'</div><span class="eyebrow">PEDIDO DE DEMONSTRAÇÃO</span><h1>'+(o.status==='cancelled'?'Pedido cancelado.':'Boa escolha. Pedido criado!')+'</h1><p>'+esc(o.restaurant_name)+' · Pedido '+esc(o.id.slice(0,8))+'</p><div id="order-timeline">'+orderTimeline(o)+'</div></section><div class="order-detail-grid"><section class="checkout-panel"><h2>Um pedido, todos os detalhes.</h2><p class="fineprint">'+new Date(o.created_at).toLocaleString('pt-BR')+'</p>'+o.items.map(item=>'<div class="summary-item"><span><b>'+item.quantity+'×</b> '+esc(item.name)+'<small>'+esc(item.option_name)+(item.note?' · '+esc(item.note):'')+'</small></span><strong>'+money(item.quantity*item.unit_price)+'</strong></div>').join('')+costs(o)+'</section><section class="checkout-panel"><h2>Endereço fictício</h2><p>'+esc(o.address.street)+', '+esc(o.address.number)+'<br>'+esc(o.address.district)+' · '+esc(o.address.city)+'</p><h3>Pagamento de demonstração</h3><p>'+({'demo_pix':'Pix de teste','demo_card':'Cartão de teste','demo_cash':'Na entrega de teste'}[o.payment]||'Simulado')+'</p>'+(o.status==='confirmed'?'<button class="button secondary" data-action="cancel" data-id="'+o.id+'">Cancelar pedido de teste</button>':'')+'<p class="fineprint">Nenhum pagamento foi processado.</p></section></div><a class="button" href="#/inicio">Explorar mais sabores <svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12H20M13 5L20 12L13 19"/></svg></a></div>';
}
function ordersPage(list) {
  main.innerHTML='<div class="shell history-page"><span class="eyebrow">SUAS BOAS ESCOLHAS</span><h1>Meus pedidos</h1><p class="page-intro">Os pedidos de teste feitos neste navegador.</p>'+(list.length?'<div class="history-list">'+list.map(o=>'<a class="history-card" href="#/pedido/'+o.id+'">'+art(o.kind)+'<div><span class="status-badge '+(o.status==='cancelled'?'cancelled':'')+'">'+(o.status==='cancelled'?'Cancelado':'Pedido de teste')+'</span><h2>'+esc(o.restaurant_name)+'</h2><p>'+new Date(o.created_at).toLocaleString('pt-BR')+' · '+esc(o.id.slice(0,8))+'</p></div><strong>'+money(o.total)+' <span aria-hidden="true"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6H18V18"/></svg></span></strong></a>').join('')+'</div>':'<div class="empty"><span aria-hidden="true">▤</span><h2>Seu primeiro pedido está por vir.</h2><p>Explore a seleção e experimente o caminho completo.</p><a class="button" href="#/inicio">Encontrar uma boa pedida</a></div>')+'</div>';
}
function helpPage() {
  main.innerHTML='<div class="shell help-page"><span class="eyebrow">SEM COMPLICAÇÃO</span><h1>Uma experiência para testar.</h1><p class="page-intro">PratoPerto é um projeto independente de demonstração local.</p><div class="help-grid"><section class="checkout-panel"><h2>O que você pode experimentar?</h2><p>Busca, categorias, favoritos, cardápios, porções, carrinho, cupons, checkout, histórico e cancelamento.</p><p>Restaurantes, pratos e preços são fictícios. Não há parceiros reais, avaliações de clientes ou integração com entregadores.</p></section><section class="checkout-panel"><h2>Pagamento e entrega</h2><p>Todo pagamento é simulado. Nunca informe dados de cartão, senha, telefone ou informações pessoais.</p><p>O acompanhamento avança automaticamente a cada 30 segundos. Não representa uma entrega real.</p></section><section class="checkout-panel"><h2>Seus dados ficam onde?</h2><p>O servidor local guarda endereço de teste, favoritos, carrinho e pedidos em SQLite. Um cookie de sessão identifica este navegador.</p><p>Não usamos serviços de terceiros, analytics ou envio de dados a restaurantes. Você pode apagar seus registros locais abaixo.</p><button class="button secondary" data-action="reset">Apagar meus dados de teste</button></section><section class="checkout-panel"><h2>Cupons da demonstração</h2><p><strong>BEMVINDO10:</strong> 10% sobre o subtotal, até R$ 15 por pedido.</p><p><strong>ENTREGAGRATIS:</strong> desconta o valor da taxa de entrega. Um cupom por pedido. O mínimo do restaurante considera o subtotal dos itens.</p><p>O carrinho aceita um restaurante por vez. Ao trocar, você confirma a substituição dos itens.</p></section></div><a class="button" href="#/inicio">Voltar para descobrir <svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12H20M13 5L20 12L13 19"/></svg></a></div>';
}
async function renderRoute() {
  if(!state.loaded) return;
  const sequence=++routeSequence; updateHeader(); modal.close(); cartDialog.close();
  const parts=(location.hash||'#/inicio').slice(2).split('/');
  window.scrollTo(0,0);
  try {
    if(parts[0]==='inicio'||!parts[0]) home();
    else if(parts[0]==='restaurante') restaurantPage(parts[1]);
    else if(parts[0]==='checkout') checkoutPage();
    else if(parts[0]==='ajuda') helpPage();
    else if(parts[0]==='pedidos'||parts[0]==='pedido') {
      main.innerHTML='<div class="shell loading" role="status">Carregando seus pedidos…</div>';
      const data=await api(parts[0]==='pedidos'?'orders':'order',undefined,parts[1]);
      if(sequence!==routeSequence) return;
      if(parts[0]==='pedidos') ordersPage(data.orders); else {orderPage(data.order); main.dataset.order=JSON.stringify(data.order);}
    } else main.innerHTML='<div class="shell empty"><h1>Essa página não está no cardápio.</h1><a class="button" href="#/inicio">Voltar ao início</a></div>';
    document.title = (parts[0]==='restaurante'?state.restaurants.find(r=>r.slug===parts[1])?.name:({inicio:'Descobrir',checkout:'Confira seu pedido',pedido:'Seu pedido de teste',pedidos:'Meus pedidos',ajuda:'Ajuda e privacidade'}[parts[0]]))+' · PratoPerto';
    main.focus({preventScroll:true});
  } catch(error) { if(sequence===routeSequence) main.innerHTML='<div class="shell empty"><h1>Não foi possível abrir esta página.</h1><p>'+esc(error.message)+'</p><button class="button" data-action="retry">Tentar novamente</button><a class="back-link" href="#/inicio">Voltar ao início</a></div>'; }
}
function updateProductPrice() {
  if(!currentProduct||!$('#product-form')) return;
  const option=Number($('input[name="option"]:checked').value), quantity=Number($('#product-quantity').value||$('#product-quantity').textContent);
  $('#product-price').textContent=money((currentProduct.price+currentProduct.options[option].extra)*quantity);
}
async function addProduct(body) {
  try {await api('cart',body); modal.close(); toast('Boa escolha! Item adicionado ao carrinho.');}
  catch(error) {
    if(error.code==='RESTAURANT_CONFLICT') showDialog(title('Trocar de restaurante?')+'<p>Seu carrinho já tem itens de '+esc(state.cart.restaurant.name)+'. Ao continuar, os itens e o cupom serão substituídos.</p><button class="button full" data-action="confirmreplace" data-body="'+esc(JSON.stringify(body))+'">Substituir carrinho</button><button class="text-button" data-action="close">Manter meu carrinho</button>'+errorBox());
    else setError($('#product-form')||$('#modal-content'),error.message);
  }
}
document.addEventListener('click',async event=>{
  const button=event.target.closest('[data-action]'); if(!button||button.disabled) return;
  const action=button.dataset.action;
  try {
    if(action==='address') showAddress();
    else if(action==='cart') showCart();
    else if(action==='close') modal.close();
    else if(action==='closecart') cartDialog.close();
    else if(action==='discover') {cartDialog.close();routeTo('inicio');}
    else if(action==='product') showProduct(button.dataset.id);
    else if(action==='category') {filters.category=button.dataset.value;home();focusAction(action,button.dataset.value);}
    else if(action==='filter') {filters[button.dataset.value]=!filters[button.dataset.value];home();focusAction(action,button.dataset.value);}
    else if(action==='resetfilters') {filters={query:'',category:'Todos',free:false,open:false,favorites:false,sort:'recommended'};home();}
    else if(action==='favorite') {
      button.disabled=true; await api('favorite',{restaurant_id:button.dataset.id});
      if($('#restaurant-grid')) renderCatalog(); else restaurantPage(location.hash.split('/')[2]);
      focusAction('favorite',button.dataset.id,'id');
      toast(state.favorites.includes(button.dataset.id)?'Guardado nos seus favoritos.':'Removido dos favoritos.');
    }
    else if(action==='exampleaddress') { const form=$('#address-form'); for(const [key,value] of Object.entries({street:'Rua das Palmeiras',number:'120',district:'Jardim',city:'Cidade de Teste',extra:''})) form.elements[key].value=value; }
    else if(action==='productstep') { const output=$('#product-quantity'); const quantity=Math.max(1,Math.min(20,Number(output.textContent)+Number(button.dataset.step))); output.value=quantity; output.textContent=quantity; updateProductPrice(); }
    else if(action==='cartqty'||action==='remove') {
      button.disabled=true; const quantity=Number(button.dataset.quantity);
      await api('cart',{op:action==='remove'||quantity<1?'remove':'set',item_id:button.dataset.id,quantity,version:state.cart.version}); cartContent();
    }
    else if(action==='checkout') {cartDialog.close();routeTo('checkout');}
    else if(action==='removecoupon') {button.disabled=true;await api('coupon',{version:state.cart.version,code:''});checkoutPage();}
    else if(action==='confirmreplace') {button.disabled=true;await addProduct({...JSON.parse(button.dataset.body),replace:true,version:state.cart.version});}
    else if(action==='placeorder') {
      if(submitting) return; submitting=true;button.disabled=true;button.textContent='Criando pedido de teste…';
      const version=state.cart.version, storageKey='pratoperto-checkout-'+version;
      let key;try {key=sessionStorage.getItem(storageKey)||uuid();sessionStorage.setItem(storageKey,key);}catch(_){key=uuid();}
      try {
        const data=await api('checkout',{version,idempotency_key:key,payment:$('input[name="payment"]:checked').value});
        try{sessionStorage.removeItem(storageKey);}catch(_){}
        routeTo('pedido/'+data.order.id);
      } catch(error) {checkoutPage();const el=$('#checkout-error');if(el){el.hidden=false;el.textContent=error.message;}else toast(error.message);}
      finally{submitting=false;}
    }
    else if(action==='cancel') showDialog(title('Cancelar este pedido de teste?')+'<p>O registro ficará no histórico como cancelado. Não existe cobrança para estornar.</p><button class="button full" data-action="confirmcancel" data-id="'+button.dataset.id+'">Confirmar cancelamento</button><button class="text-button" data-action="close">Continuar com o pedido</button>'+errorBox());
    else if(action==='confirmcancel') {button.disabled=true;await api('cancel',{id:button.dataset.id});modal.close();await renderRoute();toast('Pedido de teste cancelado.');}
    else if(action==='reset') showDialog(title('Apagar seus dados de teste?')+'<p>Endereço, favoritos, carrinho e pedidos deste visitante serão removidos do servidor local. Esta ação não afeta outros navegadores.</p><button class="button full" data-action="confirmreset">Apagar meus registros locais</button><button class="text-button" data-action="close">Manter meus dados</button>'+errorBox());
    else if(action==='confirmreset') {button.disabled=true;await api('reset',{});modal.close();routeTo('inicio');toast('Seus dados locais foram apagados.');}
    else if(action==='retry') await initialize();
  } catch(error) {toast(error.message);if(cartDialog.open)cartContent();}
  finally {if(button.isConnected)button.disabled=false;}
});
document.addEventListener('submit',async event=>{
  const form=event.target;
  if(!['search-form','address-form','product-form','coupon-form'].includes(form.id)) return;
  event.preventDefault();
  if(form.id==='search-form') {filters.query=form.elements.search.value;renderCatalog();$('#catalog-title').scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth',block:'start'});return;}
  const button=$('button[type="submit"]',form);if(button.disabled)return;button.disabled=true;
  try {
    if(form.id==='address-form') {await api('address',Object.fromEntries(new FormData(form)));modal.close();if(location.hash==='#/checkout')checkoutPage();toast('Endereço de teste salvo.');}
    else if(form.id==='product-form') await addProduct({op:'add',version:state.cart.version,product_id:currentProduct.id,quantity:Number($('#product-quantity').textContent),option_index:Number(form.elements.option.value),note:form.elements.note.value});
    else if(form.id==='coupon-form') {await api('coupon',{version:state.cart.version,code:form.elements.code.value});checkoutPage();toast('Cupom de teste aplicado.');}
  } catch(error){setError(form,error.message);}
  finally{if(button.isConnected)button.disabled=false;}
});
document.addEventListener('input',event=>{if(event.target.id==='search'){filters.query=event.target.value;renderCatalog();}});
document.addEventListener('change',event=>{if(event.target.id==='sort'){filters.sort=event.target.value;renderCatalog();}if(event.target.name==='option')updateProductPrice();});
for(const dialog of [modal,cartDialog]) {
  dialog.addEventListener('click',event=>{if(event.target===dialog){const r=dialog.getBoundingClientRect();if(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom)dialog.close();}});
  dialog.addEventListener('keydown',event=>{
    if(event.key!=='Tab') return;
    const controls=Array.from(dialog.querySelectorAll('button:not(:disabled),a[href],input:not(:disabled),textarea:not(:disabled),select:not(:disabled),[tabindex="0"]')).filter(el=>el.getClientRects().length);
    const first=controls[0],last=controls[controls.length-1];
    if(!first){event.preventDefault();dialog.focus();return;}
    if(event.shiftKey&&(document.activeElement===first||!dialog.contains(document.activeElement))){event.preventDefault();last.focus();}
    else if(!event.shiftKey&&(document.activeElement===last||!dialog.contains(document.activeElement))){event.preventDefault();first.focus();}
  });
}
window.addEventListener('hashchange',renderRoute);
window.addEventListener('offline',()=>toast('Sem conexão. Confira o servidor local antes de continuar.'));
window.addEventListener('online',()=>toast('Conexão restabelecida. Você pode tentar novamente.'));
document.addEventListener('visibilitychange',async()=>{if(!document.hidden&&state.loaded){const old=state.cart.version;try{const data=await api('bootstrap');if(data.cart.version!==old){if(cartDialog.open)cartContent();if(location.hash==='#/checkout')checkoutPage();toast('Carrinho atualizado com as alterações de outra aba.');}}catch(_){}}});
setInterval(()=>{if(location.hash.startsWith('#/pedido/')&&main.dataset.order&&$('#order-timeline'))$('#order-timeline').innerHTML=orderTimeline(JSON.parse(main.dataset.order));},15000);
async function initialize() {
  try{const data=await api('bootstrap');state.restaurants=data.restaurants;state.products=data.products;state.loaded=true;await renderRoute();}
  catch(error){main.innerHTML='<div class="shell empty"><h1>Vamos preparar o ambiente?</h1><p>'+esc(error.message)+'</p><button class="button" data-action="retry">Tentar novamente</button><p>Ative Apache, pdo_sqlite e mbstring no XAMPP.</p></div>';}
}
initialize();
})();
