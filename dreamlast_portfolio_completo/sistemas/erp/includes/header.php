<?php require_once __DIR__.'/../config/app.php'; auth(); $pageTitle=$pageTitle??'Dashboard'; ?>
<!doctype html><html lang="pt-BR"><head><link rel="icon" type="image/png" href="<?=BASE_URL?>/assets/favicon.png"><link rel="shortcut icon" href="<?=BASE_URL?>/assets/favicon.png"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($pageTitle)?> | <?=APP_NAME?></title>
<link href="<?=BASE_URL?>/assets/vendor/bootstrap.min.css" rel="stylesheet">
<link href="<?=BASE_URL?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?=BASE_URL?>/assets/css/style.css" rel="stylesheet"></head><body>
<div class="app-shell"><aside class="sidebar"><div class="brand"><span class="brand-mark">D</span><div><strong>Dreamlast</strong><small>ERP Suite</small></div></div>
<nav class="nav flex-column">
<a href="<?=BASE_URL?>/index.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
<a href="<?=BASE_URL?>/modules/clientes/index.php"><i class="bi bi-people"></i> Clientes</a>
<a href="<?=BASE_URL?>/modules/fornecedores/index.php"><i class="bi bi-truck"></i> Fornecedores</a>
<a href="<?=BASE_URL?>/modules/produtos/index.php"><i class="bi bi-box-seam"></i> Produtos</a>
<a href="<?=BASE_URL?>/modules/estoque/index.php"><i class="bi bi-boxes"></i> Estoque</a>
<a href="<?=BASE_URL?>/modules/vendas/index.php"><i class="bi bi-cart-check"></i> Vendas</a>
<a href="<?=BASE_URL?>/modules/compras/index.php"><i class="bi bi-bag-check"></i> Compras</a>
<a href="<?=BASE_URL?>/modules/financeiro/index.php"><i class="bi bi-cash-stack"></i> Financeiro</a>
<a href="<?=BASE_URL?>/modules/funcionarios/index.php"><i class="bi bi-person-badge"></i> Funcionários</a>
<a href="<?=BASE_URL?>/modules/relatorios/index.php"><i class="bi bi-bar-chart"></i> Relatórios</a>
<a href="<?=BASE_URL?>/modules/configuracoes/index.php"><i class="bi bi-gear"></i> Configurações</a>
</nav></aside><main class="main"><header class="topbar"><div><h1><?=e($pageTitle)?></h1><p><?=date('d/m/Y')?> · visão operacional</p></div><div class="d-flex align-items-center gap-3"><span class="badge text-bg-light"><?=e($_SESSION['user']['nome'])?></span><a class="btn btn-outline-secondary btn-sm" href="<?=BASE_URL?>/logout.php"><i class="bi bi-box-arrow-right"></i></a></div></header><section class="content">
<?php if($m=flash('ok')):?><div class="alert alert-success"><?=e($m)?></div><?php endif;?>
<?php if($m=flash('err')):?><div class="alert alert-danger"><?=e($m)?></div><?php endif;?>
