<?php
$host = '127.0.0.1';
$db   = 'dreamlast_pdv';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
try { $pdo = new PDO($dsn,$user,$pass,$options); } catch (PDOException $e) { die('Erro ao conectar ao banco. Importe sql/dreamlast_pdv.sql e revise config/database.php.'); }
