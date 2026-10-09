<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
try {
    start_visitor();
    $pdo=db();$visitor=ensure_visitor($pdo);
    $action=$_GET['action'] ?? 'bootstrap';
    $mutations=['address','favorite','cart','coupon','checkout','cancel','reset'];
    $input=[];
    if(in_array($action,$mutations,true)) {
        if($_SERVER['REQUEST_METHOD']!=='POST') fail('Use POST para essa ação.',405);
        if((int)($_SERVER['CONTENT_LENGTH'] ?? 0)>12000) fail('Solicitação muito grande.',413);
        if(!hash_equals($_SESSION['csrf'],$_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) fail('A sessão precisa ser atualizada. Recarregue a página.',403);
        $input=json_decode(file_get_contents('php://input'),true,16);
        if(!is_array($input)) fail('Os dados enviados são inválidos.',400);
    } elseif($_SERVER['REQUEST_METHOD']!=='GET') fail('Método inválido.',405);
    if($action==='bootstrap') {
        $products=rows($pdo,'SELECT * FROM products');
        foreach($products as &$p) {$p['options']=json_decode($p['options_json'],true);unset($p['options_json']);}unset($p);
        echo json_encode(['ok'=>true,'restaurants'=>rows($pdo,'SELECT * FROM restaurants'),'products'=>$products,'csrf'=>$_SESSION['csrf']]+current_state($pdo,$visitor),JSON_UNESCAPED_UNICODE);exit;
    }
    if($action==='address') {
        $limits=['street'=>100,'number'=>12,'district'=>80,'city'=>80,'extra'=>120];$address=[];
        foreach($limits as $field=>$max) {
            $value=$input[$field] ?? '';
            if(!is_string($value)) fail('Confira os campos do endereço.');
            $value=trim($value);
            if(mb_strlen($value)>$max) fail('O campo do endereço está muito longo.');
            if($field!=='extra' && $value==='') fail('Preencha rua, número, bairro e cidade.');
            $address[$field]=$value;
        }
        $pdo->prepare('UPDATE visitors SET address_json=? WHERE id=?')->execute([json_encode($address,JSON_UNESCAPED_UNICODE),$visitor]);
    } elseif($action==='favorite') {
        $id=$input['restaurant_id'] ?? '';
        if(!is_string($id) || !row($pdo,'SELECT id FROM restaurants WHERE id=?',[$id])) fail('Restaurante não encontrado.',404);
        if(row($pdo,'SELECT restaurant_id FROM favorites WHERE visitor_id=? AND restaurant_id=?',[$visitor,$id])) $pdo->prepare('DELETE FROM favorites WHERE visitor_id=? AND restaurant_id=?')->execute([$visitor,$id]);
        else $pdo->prepare('INSERT INTO favorites(visitor_id,restaurant_id) VALUES(?,?)')->execute([$visitor,$id]);
    } elseif($action==='cart') {
        $pdo->beginTransaction();$cart=version_check($pdo,$visitor,$input);
        $op=$input['op'] ?? '';
        if($op==='add') {
            $productId=$input['product_id'] ?? '';
            if(!is_string($productId)) fail('Produto inválido.');
            $product=row($pdo,'SELECT p.*,r.is_open FROM products p JOIN restaurants r ON r.id=p.restaurant_id WHERE p.id=?',[$productId]);
            if(!$product || !(int)$product['available']) fail('Esse item está indisponível.');
            if(!(int)$product['is_open']) fail('Esse restaurante está fechado para novos pedidos.');
            $qty=$input['quantity'] ?? 0;$option=$input['option_index'] ?? 0;$note=$input['note'] ?? '';
            if(!is_int($qty) || $qty<1 || $qty>20) fail('Escolha de 1 a 20 unidades.');
            if(!is_int($option) || !array_key_exists($option,json_decode($product['options_json'],true))) fail('Escolha uma opção válida.');
            if(!is_string($note) || mb_strlen($note)>180) fail('A observação deve ter até 180 caracteres.');
            $note=trim($note);
            if($cart['restaurant_id'] && $cart['restaurant_id']!==$product['restaurant_id']) {
                if(($input['replace'] ?? false)!==true) fail('Seu carrinho é de outro restaurante.',409,['code'=>'RESTAURANT_CONFLICT']);
                $pdo->prepare('DELETE FROM cart_items WHERE visitor_id=?')->execute([$visitor]);
                $pdo->prepare('UPDATE carts SET coupon=? WHERE visitor_id=?')->execute(['',$visitor]);
            }
            $signature=hash('sha256',$productId.'|'.$option.'|'.$note);
            $existing=row($pdo,'SELECT * FROM cart_items WHERE visitor_id=? AND signature=?',[$visitor,$signature]);
            if($existing) {
                if((int)$existing['quantity']+$qty>20) fail('O limite é de 20 unidades do mesmo item.');
                $pdo->prepare('UPDATE cart_items SET quantity=quantity+? WHERE id=? AND visitor_id=?')->execute([$qty,$existing['id'],$visitor]);
            } else $pdo->prepare('INSERT INTO cart_items(id,visitor_id,product_id,quantity,option_index,note,signature) VALUES(?,?,?,?,?,?,?)')->execute([uuid(),$visitor,$productId,$qty,$option,$note,$signature]);
            $pdo->prepare('UPDATE carts SET restaurant_id=? WHERE visitor_id=?')->execute([$product['restaurant_id'],$visitor]);
        } elseif($op==='set' || $op==='remove') {
            $id=$input['item_id'] ?? '';
            if(!is_string($id) || !row($pdo,'SELECT id FROM cart_items WHERE id=? AND visitor_id=?',[$id,$visitor])) fail('Item não encontrado.',404);
            if($op==='remove') $pdo->prepare('DELETE FROM cart_items WHERE id=? AND visitor_id=?')->execute([$id,$visitor]);
            else {
                $qty=$input['quantity'] ?? 0;
                if(!is_int($qty) || $qty<1 || $qty>20) fail('Escolha de 1 a 20 unidades.');
                $pdo->prepare('UPDATE cart_items SET quantity=? WHERE id=? AND visitor_id=?')->execute([$qty,$id,$visitor]);
            }
        } elseif($op==='clear') $pdo->prepare('DELETE FROM cart_items WHERE visitor_id=?')->execute([$visitor]);
        else fail('Ação de carrinho inválida.');
        if(!row($pdo,'SELECT id FROM cart_items WHERE visitor_id=?',[$visitor])) $pdo->prepare('UPDATE carts SET restaurant_id=NULL,coupon=? WHERE visitor_id=?')->execute(['',$visitor]);
        $pdo->prepare('UPDATE carts SET version=version+1 WHERE visitor_id=?')->execute([$visitor]);$pdo->commit();
    } elseif($action==='coupon') {
        $pdo->beginTransaction();version_check($pdo,$visitor,$input);
        $code=$input['code'] ?? '';
        if(!is_string($code)) fail('Cupom inválido.');
        $code=strtoupper(trim($code));
        if(!in_array($code,['','BEMVINDO10','ENTREGAGRATIS'],true)) fail('Esse cupom não está disponível nesta demonstração.');
        if(!current_state($pdo,$visitor)['cart']['items']) fail('Adicione um item antes de aplicar um cupom.');
        $pdo->prepare('UPDATE carts SET coupon=?,version=version+1 WHERE visitor_id=?')->execute([$code,$visitor]);$pdo->commit();
    } elseif($action==='checkout') {
        $key=$input['idempotency_key'] ?? '';
        if(!is_string($key) || !preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/D',$key)) fail('Identificador do pedido inválido.');
        $pdo->exec('BEGIN IMMEDIATE');
        $old=row($pdo,'SELECT id FROM orders WHERE visitor_id=? AND idempotency_key=?',[$visitor,$key]);
        if($old) {$pdo->exec('COMMIT');echo json_encode(['ok'=>true,'order'=>order_data($pdo,$visitor,$old['id'])]+current_state($pdo,$visitor),JSON_UNESCAPED_UNICODE);exit;}
        $cart=version_check($pdo,$visitor,$input);
        $state=current_state($pdo,$visitor);$c=$state['cart'];
        if(!$c['items']) fail('Seu carrinho está vazio.');
        if(!(int)$c['restaurant']['is_open']) fail('Esse restaurante está fechado.');
        if($c['remaining_minimum']>0) fail('Seu pedido ainda não atingiu o valor mínimo.');
        if(empty($state['address']['street'])) fail('Adicione um endereço de demonstração.');
        foreach($c['items'] as $item) if(!(int)$item['available']) fail('Um item ficou indisponível. Atualize o carrinho.');
        $payment=$input['payment'] ?? '';
        if(!in_array($payment,['demo_pix','demo_card','demo_cash'],true)) fail('Escolha uma forma de pagamento de demonstração.');
        $attempts=rows($pdo,'SELECT attempted_at FROM checkout_attempts WHERE visitor_id=? AND attempted_at>?',[$visitor,time()-60]);
        if(count($attempts)>=6) fail('Aguarde um minuto antes de fazer outro pedido.',429);
        $pdo->prepare('INSERT INTO checkout_attempts(visitor_id,attempted_at) VALUES(?,?)')->execute([$visitor,time()]);
        $id=uuid();
        $pdo->prepare('INSERT INTO orders(id,visitor_id,restaurant_id,created_at,status,address_json,subtotal,fee,discount,total,coupon,payment,idempotency_key) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([$id,$visitor,$c['restaurant']['id'],now_utc(),'confirmed',json_encode($state['address'],JSON_UNESCAPED_UNICODE),$c['subtotal'],$c['fee'],$c['discount'],$c['total'],$c['coupon'],$payment,$key]);
        foreach($c['items'] as $item) $pdo->prepare('INSERT INTO order_items(id,order_id,name,option_name,note,quantity,unit_price) VALUES(?,?,?,?,?,?,?)')->execute([uuid(),$id,$item['name'],$item['option_name'],$item['note'],$item['quantity'],$item['unit_price']]);
        $pdo->prepare('DELETE FROM cart_items WHERE visitor_id=?')->execute([$visitor]);
        $pdo->prepare('UPDATE carts SET restaurant_id=NULL,coupon=?,version=version+1 WHERE visitor_id=?')->execute(['',$visitor]);
        $pdo->exec('COMMIT');
        echo json_encode(['ok'=>true,'order'=>order_data($pdo,$visitor,$id)]+current_state($pdo,$visitor),JSON_UNESCAPED_UNICODE);exit;
    } elseif($action==='orders') {
        $list=rows($pdo,'SELECT o.id,o.created_at,o.status,o.total,r.name restaurant_name,r.kind FROM orders o JOIN restaurants r ON r.id=o.restaurant_id WHERE o.visitor_id=? ORDER BY o.created_at DESC,o.rowid DESC',[$visitor]);
        echo json_encode(['ok'=>true,'orders'=>$list],JSON_UNESCAPED_UNICODE);exit;
    } elseif($action==='order') {
        $order=order_data($pdo,$visitor,(string)($_GET['id'] ?? ''));
        if(!$order) fail('Pedido não encontrado para este visitante.',404);
        echo json_encode(['ok'=>true,'order'=>$order],JSON_UNESCAPED_UNICODE);exit;
    } elseif($action==='cancel') {
        $id=$input['id'] ?? '';
        if(!is_string($id) || !order_data($pdo,$visitor,$id)) fail('Pedido não encontrado.',404);
        $pdo->prepare("UPDATE orders SET status='cancelled',cancelled_at=? WHERE id=? AND visitor_id=? AND status='confirmed'")->execute([now_utc(),$id,$visitor]);
        echo json_encode(['ok'=>true,'order'=>order_data($pdo,$visitor,$id)],JSON_UNESCAPED_UNICODE);exit;
    } elseif($action==='reset') {
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM visitors WHERE id=?')->execute([$visitor]);
        $pdo->commit();session_regenerate_id(true);$_SESSION['visitor']=uuid();$_SESSION['csrf']=bin2hex(random_bytes(24));
        $visitor=ensure_visitor($pdo);
        echo json_encode(['ok'=>true,'csrf'=>$_SESSION['csrf']]+current_state($pdo,$visitor),JSON_UNESCAPED_UNICODE);exit;
    } else fail('Ação não encontrada.',404);
    echo json_encode(['ok'=>true]+current_state($pdo,$visitor),JSON_UNESCAPED_UNICODE);
} catch(Throwable $error) {
    if(isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    if(isset($pdo)) {try{$pdo->exec('ROLLBACK');}catch(Throwable $ignore){}}
    error_log('PratoPerto API: '.get_class($error));
    fail($error instanceof RuntimeException && str_contains($error->getMessage(),'pdo_sqlite') ? $error->getMessage() : 'Não foi possível concluir agora. Tente novamente.',503);
}
