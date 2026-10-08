<?php
session_name('DREAMLAST_PDV');
session_start();
require __DIR__.'/config/database.php';
require __DIR__.'/includes/functions.php';
$page=$_GET['page']??(isLogged()?'dashboard':'login');
$routes=[
'login'=>'modules/auth/login.php','logout'=>'modules/auth/logout.php','dashboard'=>'modules/dashboard/index.php',
'products'=>'modules/products/index.php','customers'=>'modules/customers/index.php','pos'=>'modules/pos/index.php','sale_create'=>'modules/pos/create_sale.php',
'sales'=>'modules/sales/index.php','sale_view'=>'modules/sales/view.php','cash'=>'modules/cash/index.php','reports'=>'modules/reports/index.php',
'users'=>'modules/users/index.php','settings'=>'modules/settings/index.php','suppliers'=>'modules/suppliers/index.php','purchases'=>'modules/purchases/index.php',
'finance'=>'modules/finance/index.php','quotes'=>'modules/quotes/index.php','quote_view'=>'modules/quotes/view.php','returns'=>'modules/returns/index.php','inventory'=>'modules/inventory/index.php','audit'=>'modules/audit/index.php'
];
if(!isset($routes[$page])) $page='dashboard';
if($page!=='login' && $page!=='logout') requireLogin();
require __DIR__.'/'.$routes[$page];
