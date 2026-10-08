<?php
require __DIR__.'/includes/site.php';
$isHome = true;
$title = 'Dream Last | Sites e sistemas para negócios reais';
$description = 'Sites, sistemas web e plataformas personalizadas para empresas. Conheça os projetos PDV Pro, ERP e CRM desenvolvidos pela Dream Last.';
$sitePath = rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/') . '/';
$pagePath = $sitePath;
require __DIR__.'/includes/header.php';
?>
<main id="conteudo">
<section id="inicio" class="hero container">
<div class="hero-intro"><span class="eyebrow"><span class="tiny-square"></span> DREAM LAST / TECNOLOGIA SOB MEDIDA</span><span class="hero-index">PORTFÓLIO — <?= date('Y') ?></span></div>
<div class="hero-grid">
<div class="hero-copy"><h1>Sites e sistemas<br>para <em>negócios reais.</em></h1><p>Desenvolvemos sites, sistemas personalizados e plataformas web para empresas. Da presença digital às ferramentas que fazem sua operação funcionar.</p><div class="actions"><a class="button button-dark" href="#projetos">Conhecer projetos <?= arrow() ?></a><a class="text-link" href="<?= escape($contactUrl) ?>">Solicitar orçamento <?= arrow() ?></a></div></div>
<a class="hero-project" href="projetos/erp/" aria-label="Conhecer o projeto Dream Last ERP"><div class="hero-project-top"><span>EM FOCO</span><span>02 / ERP</span></div><div class="hero-image"><?php screenshot($projects['erp'], './', false, true); ?></div><div class="hero-project-bottom"><span>Gestão que conecta a operação.</span><?= arrow() ?></div></a>
</div>
<div class="hero-bottom"><p>Sites. Sistemas. Soluções personalizadas.</p><a href="#projetos">Explore nosso trabalho <span aria-hidden="true">↓</span></a></div>
</section>
<section id="projetos" class="projects section-pad">
<div class="container">
<div class="section-heading"><div><span class="eyebrow">01 / PROJETOS SELECIONADOS</span><h2>O trabalho fala<br>por aqui.</h2></div><p>Sistemas desenvolvidos pela Dream Last.<br>Conheça a solução e explore as demonstrações.</p></div>
<?php foreach ($projects as $project): ?>
<article class="project-row reveal">
<a class="project-visual" href="projetos/<?= escape($project['slug']) ?>/" aria-label="Ver projeto <?= escape($project['name']) ?>"><div class="visual-bar"><span><?= escape($project['name']) ?></span><span aria-hidden="true">↗</span></div><?php screenshot($project, './'); ?><div class="visual-caption"><span>INTERFACE REAL · DADOS DE DEMONSTRAÇÃO</span><span><?= escape($project['screen']) ?></span></div></a>
<div class="project-copy"><div class="project-meta"><span><?= escape($project['number']) ?> / <?= escape($project['category']) ?></span><span>PROJETO DREAM LAST</span></div><h3><?= escape($project['name']) ?></h3><p class="project-headline"><?= escape($project['headline']) ?></p><p><?= escape($project['summary']) ?></p><ul class="technology-list" aria-label="Tecnologias"><?php foreach ($project['technologies'] as $tech): ?><li><?= escape($tech) ?></li><?php endforeach; ?></ul><a class="text-link" href="projetos/<?= escape($project['slug']) ?>/">Conhecer o projeto <?= arrow() ?></a><a class="demo-link" href="sistemas/<?= escape($project['slug']) ?>/" target="_blank" rel="noopener noreferrer">Abrir demonstração <span class="sr-only">(nova aba)</span><span aria-hidden="true">↗</span></a></div>
</article>
<?php endforeach; ?>
</div>
</section>
<section id="servicos" class="services section-pad">
<div class="container services-grid"><div><span class="eyebrow">02 / O QUE DESENVOLVEMOS</span><h2>A solução certa<br>para a sua rotina.</h2><p class="section-description">Cada empresa tem processos próprios. Nosso trabalho é entender esses processos e construir a ferramenta que faz sentido para eles.</p><a class="text-link" href="<?= escape($contactUrl) ?>">Conversar sobre meu projeto <?= arrow() ?></a></div><div class="service-list">
<article class="service-item"><span>01</span><div><h3>Sites & presença digital</h3><p>Sites institucionais, portais e landing pages com informação clara e navegação cuidadosa.</p></div></article>
<article class="service-item"><span>02</span><div><h3>Sistemas web personalizados</h3><p>Sistemas empresariais, plataformas administrativas e ferramentas para processos internos.</p></div></article>
<article class="service-item"><span>03</span><div><h3>Dashboards & gestão</h3><p>Painéis, relatórios e indicadores para acompanhar vendas, atendimento e operação.</p></div></article>
<article class="service-item"><span>04</span><div><h3>Automações & integrações</h3><p>Conexões entre ferramentas e rotinas automatizadas para reduzir tarefas repetitivas.</p></div></article>
</div></div>
</section>
<section id="sobre" class="about section-pad"><div class="container about-grid"><div><span class="eyebrow">03 / SOBRE A DREAM LAST</span><h2>Por trás da tela,<br>pessoas que constroem.</h2><div class="about-signature"><span class="brand-mark large" aria-hidden="true">DL<span class="mark-dot"></span></span><span>DESENVOLVIMENTO<br>COM IDENTIDADE.</span></div></div><div class="about-copy"><p class="lead">Criamos soluções digitais para empresas que precisam organizar processos e apresentar seu trabalho com clareza.</p><p>A Dream Last é administrada por Marcovic de Luna e Matheus Henrique. Desenvolvemos desde sites institucionais até sistemas de gestão, com funcionalidades adaptadas à realidade de cada negócio.</p><p>Integrações, painéis administrativos, automações e relatórios entram no projeto quando ajudam a resolver uma necessidade concreta.</p><div class="founders"><div><strong>Marcovic de Luna</strong><span>Co-administrador</span></div><div><strong>Matheus Henrique</strong><span>Co-administrador</span></div></div></div></div></section>
<section id="processo" class="process section-pad"><div class="container"><div class="section-heading"><div><span class="eyebrow">04 / COMO TRABALHAMOS</span><h2>Um processo claro.<br>Do começo em diante.</h2></div><p>Você acompanha as decisões.<br>A solução evolui com o negócio.</p></div><ol class="process-list"><li><span>01</span><h3>Entendemos</h3><p>O negócio, as pessoas e o que precisa funcionar melhor.</p></li><li><span>02</span><h3>Planejamos</h3><p>Escopo, estrutura e uma experiência coerente com a marca.</p></li><li><span>03</span><h3>Desenvolvemos</h3><p>Transformamos o planejamento em uma solução funcional.</p></li><li><span>04</span><h3>Testamos</h3><p>Revisamos navegação, interfaces e os fluxos do sistema.</p></li><li><span>05</span><h3>Entregamos</h3><p>Preparamos a publicação e orientamos o uso da solução.</p></li><li><span>06</span><h3>Evoluímos</h3><p>Ajustes e novas funções conforme surgem necessidades.</p></li></ol></div></section>
<section id="contato" class="contact section-pad"><div class="container contact-grid"><div><span class="eyebrow">05 / VAMOS CONVERSAR</span><h2>Tem um projeto<br>em mente?</h2><p>Conte o que sua empresa precisa.<br>Vamos pensar na solução juntos.</p></div><div class="contact-actions"><a class="button button-light" href="<?= escape($contactUrl) ?>">Falar com a Dream Last <?= arrow() ?></a><a class="contact-email" href="mailto:<?= escape($contactEmail) ?>"><?= escape($contactEmail) ?></a><a class="contact-gmail" href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=contato.dreamlast@gmail.com&amp;su=Projeto%20Dream%20Last" target="_blank" rel="noopener noreferrer">Prefere usar o Gmail? <span class="sr-only">Abrir em nova aba.</span>↗</a></div></div></section>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>
