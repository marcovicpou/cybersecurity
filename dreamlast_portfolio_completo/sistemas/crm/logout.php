<?php
require_once __DIR__.'/includes/auth.php';
if(!empty($_SESSION['user']['id'])) $pdo->prepare('INSERT INTO audit_logs(user_id,action,details,ip_address) VALUES(?,?,?,?)')->execute([$_SESSION['user']['id'],'LOGOUT','Sessão encerrada',$_SERVER['REMOTE_ADDR']??'']);
session_destroy(); header('Location: login.php'); exit;
