<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('DREAMLAST_ERP');
    session_start();
}
define('APP_NAME','Dreamlast ERP');
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$modulePosition = strpos($scriptPath, '/modules/');
$basePath = $modulePosition !== false ? substr($scriptPath, 0, $modulePosition) : dirname($scriptPath);
define('BASE_URL', rtrim($basePath, '/'));
require_once __DIR__.'/database.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function money($v){return 'R$ '.number_format((float)$v,2,',','.');}
function auth(){ if(empty($_SESSION['user'])){ header('Location: '.BASE_URL.'/login.php'); exit; } }
function flash($k,$v=null){ if($v!==null){$_SESSION['_flash'][$k]=$v; return;} $x=$_SESSION['_flash'][$k]??null; unset($_SESSION['_flash'][$k]); return $x; }
