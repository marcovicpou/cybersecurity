<?php
require_once __DIR__.'/includes/auth.php';
if(!empty($_SESSION['user'])) redirect('index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email=trim($_POST['email']??''); $password=$_POST['password']??'';
    $st=$pdo->prepare('SELECT * FROM users WHERE email=? AND active=1 LIMIT 1'); $st->execute([$email]); $u=$st->fetch();
    if($u && password_verify($password,$u['password_hash'])){
        $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        $pdo->prepare('UPDATE users SET last_login=NOW() WHERE id=?')->execute([$u['id']]);
        $pdo->prepare('INSERT INTO audit_logs(user_id,action,details,ip_address) VALUES(?,?,?,?)')->execute([$u['id'],'LOGIN','Acesso realizado',$_SERVER['REMOTE_ADDR']??'']);
        redirect('index.php');
    }
    $error='E-mail ou senha inválidos.';
}
?>
<!doctype html><html lang="pt-BR"><head><link rel="icon" type="image/png" href="assets/favicon.png"><link rel="shortcut icon" href="assets/favicon.png"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dreamlast CRM • Login</title><link rel="stylesheet" href="assets/css/app.css"></head>
<body class="login-body"><div class="login-shell"><section class="login-brand"><div class="logo-big">D</div><h1>Dreamlast CRM</h1><p>Relacionamentos, vendas e operação em um único lugar.</p><div class="login-points"><span>✓ Pipeline comercial</span><span>✓ Atendimento e projetos</span><span>✓ Financeiro e relatórios</span></div></section><section class="login-card"><div><span class="eyebrow">ACESSO SEGURO</span><h2>Bem-vindo de volta</h2><p class="muted">Entre para acessar o painel corporativo.</p></div><?php if($error):?><div class="alert danger"><?=e($error)?></div><?php endif;?><form method="post"><label>E-mail<input type="email" name="email" required value="admin@dreamlast.com.br"></label><label>Senha<input type="password" name="password" required value="Dream@123"></label><button class="btn primary full">Entrar no CRM</button></form><div class="demo-box"><b>Acesso de demonstração</b><small>admin@dreamlast.com.br • Dream@123</small></div></section></div></body></html>
