<?php
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($v){ return 'R$ '.number_format((float)$v,2,',','.'); }
function redirect($url){ header('Location: '.$url); exit; }
function isLogged(){ return !empty($_SESSION['user']); }
function requireLogin(){ if(!isLogged()) redirect('index.php?page=login'); }
function role(){ return $_SESSION['user']['role']??'cashier'; }
function isAdmin(){ return role()==='admin'; }
function can($permission){
  $map=[
    'admin'=>['*'],
    'manager'=>['dashboard','pos','products','customers','sales','cash','reports','suppliers','purchases','finance','quotes','returns','inventory'],
    'cashier'=>['dashboard','pos','customers','sales','cash','quotes']
  ];
  $allowed=$map[role()]??[];
  return in_array('*',$allowed,true)||in_array($permission,$allowed,true);
}
function requirePermission($permission){ if(!can($permission)){ flash('error','Seu perfil não possui permissão para acessar este módulo.'); redirect('index.php?page=dashboard'); } }
function csrf(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function checkCsrf(){ if(($_POST['csrf']??'') !== ($_SESSION['csrf']??'')) die('Token CSRF inválido.'); }
function flash($key,$value=null){ if($value!==null){$_SESSION['flash'][$key]=$value;return;} $v=$_SESSION['flash'][$key]??null; unset($_SESSION['flash'][$key]); return $v; }
function setting(PDO $pdo,$key,$default=''){ $st=$pdo->prepare('SELECT value FROM settings WHERE `key`=?'); $st->execute([$key]); $v=$st->fetchColumn(); return ($v!==false && $v!=='')?$v:$default; }
function paymentLabel($m){ return ['cash'=>'Dinheiro','pix'=>'PIX','debit'=>'Débito','credit'=>'Crédito','voucher'=>'Vale','crediario'=>'Crediário'][$m]??ucfirst((string)$m); }
function openCash(PDO $pdo,$uid){ $st=$pdo->prepare("SELECT * FROM cash_sessions WHERE user_id=? AND status='open' ORDER BY id DESC LIMIT 1");$st->execute([$uid]);return $st->fetch(); }
function saleReturnedTotal(PDO $pdo,$saleId){$st=$pdo->prepare("SELECT COALESCE(SUM(total),0) FROM sale_returns WHERE sale_id=?");$st->execute([$saleId]);return (float)$st->fetchColumn();}
function audit(PDO $pdo,$action,$entity,$entityId=null,$details=null){
  try{$st=$pdo->prepare('INSERT INTO audit_logs(user_id,action,entity,entity_id,details,ip) VALUES(?,?,?,?,?,?)');$st->execute([$_SESSION['user']['id']??null,$action,$entity,$entityId,$details,$_SERVER['REMOTE_ADDR']??null]);}catch(Throwable $e){}
}
function salePayments(PDO $pdo,$saleId){$st=$pdo->prepare('SELECT method,amount,installments FROM sale_payments WHERE sale_id=? ORDER BY id');$st->execute([$saleId]);return $st->fetchAll();}
