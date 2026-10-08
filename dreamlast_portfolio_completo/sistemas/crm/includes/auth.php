<?php
session_name('DREAMLAST_CRM');
session_start();
require_once __DIR__.'/functions.php';
$config=require __DIR__.'/../config/database.php';
try {
    $dsn="mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo=new PDO($dsn,$config['user'],$config['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch(Throwable $e) {
    if(basename($_SERVER['PHP_SELF'])!=='setup.php') redirect('setup.php');
}
if(empty($_SESSION['user']) && basename($_SERVER['PHP_SELF'])!=='login.php' && basename($_SERVER['PHP_SELF'])!=='setup.php') redirect('login.php');
function can(string $module): bool {
    $role=$_SESSION['user']['role']??'';
    if($role==='Administrador') return true;
    $map=[
        'Gerente'=>['dashboard','clientes','leads','oportunidades','tarefas','projetos','tickets','financeiro','relatorios','agenda'],
        'Vendas'=>['dashboard','clientes','leads','oportunidades','tarefas','agenda'],
        'Suporte'=>['dashboard','clientes','tickets','tarefas','agenda'],
        'Financeiro'=>['dashboard','clientes','financeiro','relatorios'],
    ];
    return in_array($module,$map[$role]??[],true);
}
