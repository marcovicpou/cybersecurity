<?php
$base = $base ?? './';
$isHome = $isHome ?? false;
$home = $isHome ? '' : $base;
$canonical = public_origin() . ($pagePath ?? '/');
$socialImage = public_origin() . ($sitePath ?? '/') . 'assets/img/og-cover.png';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= escape($description) ?>">
<meta name="theme-color" content="#151515">
<title><?= escape($title) ?></title>
<link rel="canonical" href="<?= escape($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="pt_BR">
<meta property="og:site_name" content="Dream Last">
<meta property="og:title" content="<?= escape($title) ?>">
<meta property="og:description" content="<?= escape($description) ?>">
<meta property="og:url" content="<?= escape($canonical) ?>">
<meta property="og:image" content="<?= escape($socialImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" type="image/svg+xml" href="<?= escape($base) ?>assets/img/favicon.svg">
<link rel="stylesheet" href="<?= escape($base) ?>assets/css/style.css">
<script src="<?= escape($base) ?>assets/js/main.js" defer></script>
<noscript><link rel="stylesheet" href="<?= escape($base) ?>assets/css/no-script.css"></noscript>
<script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'Organization','name'=>'Dream Last','url'=>public_origin().($sitePath ?? '/'),'email'=>$contactEmail,'founder'=>[['@type'=>'Person','name'=>'Marcovic de Luna'],['@type'=>'Person','name'=>'Matheus Henrique']]], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP) ?></script>
</head>
<body>
<a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
<header class="site-header">
<div class="container header-inner">
<a class="brand" href="<?= escape($home) ?>#inicio" aria-label="Dream Last — início"><span class="brand-mark" aria-hidden="true">DL<span class="mark-dot"></span></span><span class="brand-name">DREAM LAST<span>DESENVOLVIMENTO DIGITAL</span></span></a>
<button class="menu-toggle" aria-controls="navigation" aria-expanded="false" aria-label="Abrir menu"><span></span><span></span></button>
<nav id="navigation" class="navigation" aria-label="Navegação principal">
<a href="<?= escape($home) ?>#projetos">Projetos</a><a href="<?= escape($home) ?>#sobre">Sobre</a><a href="<?= escape($home) ?>#servicos">Serviços</a><a href="<?= escape($home) ?>#contato">Contato</a>
<a class="button button-small button-dark" href="<?= escape($contactUrl) ?>">Solicitar projeto <?= arrow() ?></a>
</nav>
</div>
</header>
