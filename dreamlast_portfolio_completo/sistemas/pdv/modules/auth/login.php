<?php
if(isLogged()) redirect('index.php?page=dashboard');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=$pdo->prepare('SELECT * FROM users WHERE email=? AND active=1 LIMIT 1'); $st->execute([trim($_POST['email']??'')]); $u=$st->fetch();
  if($u && password_verify($_POST['password']??'',$u['password'])){ $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']]; redirect('index.php?page=dashboard'); }
  $error='E-mail ou senha inválidos.';
}
$title='Entrar - Dreamlast PDV'; require __DIR__.'/../../includes/header.php';
?>
<div class="login-shell"><div class="login-card"><div class="login-brand"><span class="brand-mark big">D</span><h1>Dreamlast PDV</h1><p>Gestão comercial simples, rápida e profissional.</p></div><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post" class="stack"><label>E-mail<input type="email" name="email" value="admin@dreamlast.com" required></label><label>Senha<input type="password" name="password" value="admin123" required></label><button class="btn primary wide">Entrar no sistema</button></form><small>Primeiro acesso: admin@dreamlast.com / admin123</small></div></div>
<?php require __DIR__.'/../../includes/footer.php'; ?>
