<?php
session_name('DREAMLAST_CRM');
session_start();
$config=require __DIR__.'/config/database.php';
$msg=''; $ok=false;
if($_SERVER['REQUEST_METHOD']==='POST'){
try{
 $pdo=new PDO("mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}",$config['user'],$config['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
 $pdo->exec("USE `{$config['dbname']}`");
 $sql=file_get_contents(__DIR__.'/database/schema.sql');
 $pdo->exec($sql);
 $check=$pdo->prepare('SELECT id FROM users WHERE email=?'); $check->execute(['admin@dreamlast.com.br']);
 if(!$check->fetch()){
   $hash=password_hash('Dream@123',PASSWORD_DEFAULT);
   $pdo->prepare('INSERT INTO users(name,email,password_hash,role,active) VALUES(?,?,?,?,1)')->execute(['Administrador Dreamlast','admin@dreamlast.com.br',$hash,'Administrador']);
 }
 $ok=true; $msg='CRM instalado com sucesso. O banco e os dados de demonstração estão prontos.';
}catch(Throwable $e){$msg='Erro na instalação: '.$e->getMessage();}
}
?><!doctype html><html lang="pt-BR"><head><link rel="icon" type="image/png" href="assets/favicon.png"><link rel="shortcut icon" href="assets/favicon.png"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalar Dreamlast CRM</title><link rel="stylesheet" href="assets/css/app.css"></head><body class="login-body"><div class="install-card"><div class="logo-big">D</div><span class="eyebrow">INSTALADOR</span><h1>Dreamlast CRM</h1><p class="muted">Este assistente cria o banco <b>dreamlast_crm</b>, todas as tabelas e registros de demonstração.</p><?php if($msg):?><div class="alert <?=$ok?'success':'danger'?>"><?=htmlspecialchars($msg)?></div><?php endif;?><?php if($ok):?><a class="btn primary full" href="login.php">Abrir CRM</a><div class="demo-box"><b>Login</b><small>admin@dreamlast.com.br • Dream@123</small></div><?php else:?><form method="post"><button class="btn primary full">Instalar agora</button></form><p class="tiny">Requisito: Apache e MySQL iniciados no XAMPP. Configuração padrão: usuário root sem senha.</p><?php endif;?></div></body></html>
