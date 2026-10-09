<?php
declare(strict_types=1);
function uuid(): string {
    $bytes=random_bytes(16);
    $bytes[6]=chr((ord($bytes[6])&0x0f)|0x40);
    $bytes[8]=chr((ord($bytes[8])&0x3f)|0x80);
    $hex=bin2hex($bytes);
    return substr($hex,0,8).'-'.substr($hex,8,4).'-'.substr($hex,12,4).'-'.substr($hex,16,4).'-'.substr($hex,20);
}
function now_utc(): string { return gmdate('Y-m-d\TH:i:s\Z'); }
function start_visitor(): void {
    session_name('PRATOPERTO_DEMO');
    session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','path'=>'/']);
    ini_set('session.use_strict_mode','1');
    session_start();
    if(empty($_SESSION['visitor'])) $_SESSION['visitor']=uuid();
    if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(24));
}
function db(): PDO {
    static $pdo;
    if($pdo) return $pdo;
    if(!in_array('sqlite',PDO::getAvailableDrivers(),true)) throw new RuntimeException('Habilite pdo_sqlite no PHP para executar a demonstração.');
    $directory=getenv('PRATOPERTO_DATA_DIR') ?: sys_get_temp_dir().'/pratoperto-'.substr(hash('sha256',dirname(__DIR__)),0,16);
    if(!is_dir($directory) && !mkdir($directory,0700,true) && !is_dir($directory)) throw new RuntimeException('Não foi possível preparar o armazenamento local.');
    $pdo=new PDO('sqlite:'.$directory.'/delivery.sqlite',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $pdo->exec('PRAGMA foreign_keys=ON; PRAGMA busy_timeout=5000; PRAGMA journal_mode=WAL;');
    $pdo->exec(file_get_contents(__DIR__.'/../replica/schema.sql'));
    if(!(int)$pdo->query('SELECT COUNT(*) FROM restaurants')->fetchColumn()) {
        require_once __DIR__.'/seed.php';
        [$restaurants,$products]=catalog_seed();
        $pdo->beginTransaction();
        $insert=$pdo->prepare('INSERT OR IGNORE INTO restaurants(id,slug,name,category,kind,tag,description,fee,minimum,eta_min,eta_max,is_open,color) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
        foreach($restaurants as $r) $insert->execute(array_values($r));
        $insert=$pdo->prepare('INSERT OR IGNORE INTO products(id,restaurant_id,name,description,price,image,group_name,available,options_json) VALUES(?,?,?,?,?,?,?,?,?)');
        foreach($products as $p) { $p['options']=json_encode($p['options'],JSON_UNESCAPED_UNICODE); $insert->execute(array_values($p)); }
        $pdo->commit();
    }
    return $pdo;
}
function ensure_visitor(PDO $pdo): string {
    $id=$_SESSION['visitor'];
    $pdo->prepare('INSERT OR IGNORE INTO visitors(id,created_at) VALUES(?,?)')->execute([$id,now_utc()]);
    $pdo->prepare('INSERT OR IGNORE INTO carts(visitor_id) VALUES(?)')->execute([$id]);
    return $id;
}
function fail(string $message,int $status=422,array $extras=[]): void {
    http_response_code($status);
    echo json_encode(['ok'=>false,'message'=>$message]+$extras,JSON_UNESCAPED_UNICODE);
    exit;
}
function row(PDO $pdo,string $sql,array $args=[]): ?array {
    $q=$pdo->prepare($sql);$q->execute($args);return $q->fetch() ?: null;
}
function rows(PDO $pdo,string $sql,array $args=[]): array {
    $q=$pdo->prepare($sql);$q->execute($args);return $q->fetchAll();
}
function current_state(PDO $pdo,string $visitor): array {
    $cart=row($pdo,'SELECT * FROM carts WHERE visitor_id=?',[$visitor]);
    $restaurant=$cart['restaurant_id'] ? row($pdo,'SELECT * FROM restaurants WHERE id=?',[$cart['restaurant_id']]) : null;
    $items=[];$subtotal=0;
    foreach(rows($pdo,'SELECT ci.*,p.name,p.price,p.image,p.available,p.options_json FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.visitor_id=? ORDER BY ci.rowid',[$visitor]) as $item) {
        $option=json_decode($item['options_json'],true)[$item['option_index']] ?? ['name'=>'','extra'=>0];
        $price=(int)$item['price']+(int)$option['extra'];
        $item['unit_price']=$price;$item['total']=$price*(int)$item['quantity'];$item['option_name']=$option['name'];
        unset($item['options_json'],$item['signature'],$item['visitor_id']);
        $subtotal+=$item['total'];$items[]=$item;
    }
    $fee=$items ? (int)$restaurant['fee'] : 0;
    $discount=$cart['coupon']==='BEMVINDO10' ? min(1500,intdiv($subtotal,10)) : 0;
    if($cart['coupon']==='ENTREGAGRATIS') $discount=$fee;
    $address=json_decode(row($pdo,'SELECT address_json FROM visitors WHERE id=?',[$visitor])['address_json'],true);
    $favorites=array_column(rows($pdo,'SELECT restaurant_id FROM favorites WHERE visitor_id=?',[$visitor]),'restaurant_id');
    return ['cart'=>['version'=>(int)$cart['version'],'restaurant'=>$restaurant,'items'=>$items,'subtotal'=>$subtotal,'fee'=>$fee,'discount'=>$discount,'total'=>$subtotal+$fee-$discount,'coupon'=>$cart['coupon'],'count'=>array_sum(array_column($items,'quantity')),'remaining_minimum'=>$restaurant ? max(0,(int)$restaurant['minimum']-$subtotal) : 0],'address'=>$address,'favorites'=>$favorites];
}
function version_check(PDO $pdo,string $visitor,array $input): array {
    $cart=row($pdo,'SELECT * FROM carts WHERE visitor_id=?',[$visitor]);
    if(!isset($input['version']) || !is_int($input['version']) || $input['version']!==(int)$cart['version']) {
        if($pdo->inTransaction()) $pdo->rollBack();
        fail('Seu carrinho mudou em outra aba. Confira a versão atual e tente novamente.',409,['state'=>current_state($pdo,$visitor)]);
    }
    return $cart;
}
function order_data(PDO $pdo,string $visitor,string $id): ?array {
    $order=row($pdo,'SELECT o.*,r.name restaurant_name,r.eta_min,r.eta_max,r.kind FROM orders o JOIN restaurants r ON r.id=o.restaurant_id WHERE o.id=? AND o.visitor_id=?',[$id,$visitor]);
    if(!$order) return null;
    $order['address']=json_decode($order['address_json'],true);unset($order['address_json'],$order['visitor_id'],$order['idempotency_key']);
    $order['items']=rows($pdo,'SELECT name,option_name,note,quantity,unit_price FROM order_items WHERE order_id=?',[$id]);
    return $order;
}
