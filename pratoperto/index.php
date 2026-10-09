<?php
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self'; font-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'self'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>PratoPerto — seu próximo pedido começa aqui</title>
<meta name="description" content="Explore restaurantes, monte seu carrinho e acompanhe pedidos em uma demonstração local de delivery.">
<meta name="theme-color" content="#176648">
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="assets/css/style.css"><script defer src="assets/js/app.js"></script>
</head>
<body>
<a class="skip-link" href="#main">Pular para o conteúdo</a>
<header class="site-header"><div class="shell header-inner">
<a class="brand" href="#/inicio" aria-label="PratoPerto, início"><img src="assets/img/favicon.svg" width="36" height="36" alt="">pratoperto</a>
<nav class="desktop-nav" aria-label="Principal"><a href="#/inicio" data-nav="inicio">Descobrir</a><a href="#/pedidos" data-nav="pedidos">Meus pedidos</a></nav>
<button class="address-button" data-action="address"><span class="pin" aria-hidden="true">⌖</span><span><small>Entregar em</small><strong id="header-address">Escolha seu endereço</strong></span><span aria-hidden="true">⌄</span></button>
<button class="header-cart" data-action="cart" aria-label="Abrir carrinho"><span aria-hidden="true"><svg class="inline-icon cart-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9H20L18 20H6ZM8 9L12 3L16 9M9 12V16M15 12V16"/></svg></span><span><strong id="header-total">R$ 0,00</strong><small id="header-count">Seu carrinho</small></span><b id="cart-badge" hidden>0</b></button>
</div></header>
<div class="demo-strip">Modo demonstração: restaurantes fictícios e pedidos sem cobrança. <a href="#/ajuda">Entenda como funciona <svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6H18V18"/></svg></a></div>
<main id="main" tabindex="-1"><div class="shell loading" role="status">Preparando algo gostoso…</div></main>
<footer class="site-footer"><div class="shell"><a class="brand" href="#/inicio"><img src="assets/img/favicon.svg" width="28" height="28" alt="">pratoperto</a><p>Boas escolhas, bem perto.</p><a href="#/ajuda">Ajuda e privacidade</a><small>Projeto independente de demonstração · Dados fictícios</small></div></footer>
<nav class="mobile-nav" aria-label="Navegação móvel"><a href="#/inicio" data-nav="inicio"><span aria-hidden="true"><svg class="inline-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11L12 3L21 11M6 9V21H18V9M10 21V15H14V21"/></svg></span>Descobrir</a><a href="#/pedidos" data-nav="pedidos"><span aria-hidden="true">▤</span>Pedidos</a><button data-action="cart"><span aria-hidden="true"><svg class="inline-icon cart-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9H20L18 20H6ZM8 9L12 3L16 9M9 12V16M15 12V16"/></svg></span>Carrinho <b id="mobile-count">0</b></button><a href="#/ajuda" data-nav="ajuda"><span aria-hidden="true">?</span>Ajuda</a></nav>
<dialog id="modal" aria-labelledby="modal-title"><button class="close-button" data-action="close" aria-label="Fechar janela">×</button><div id="modal-content"></div></dialog>
<dialog id="cart-dialog" class="cart-dialog" aria-labelledby="cart-title"><button class="close-button" data-action="closecart" aria-label="Fechar carrinho">×</button><div id="cart-content"></div></dialog>
<div id="toast" class="toast" role="status" aria-live="polite" hidden></div>
<noscript><p class="shell notice">Ative o JavaScript para usar a demonstração. Não há conexão com serviços de pagamento.</p></noscript>
</body></html>
