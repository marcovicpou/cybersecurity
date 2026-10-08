<?php
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function money($value): string { return 'R$ ' . number_format((float)$value, 2, ',', '.'); }
function redirect($url): never { header('Location: '.$url); exit; }
function flash(string $type, string $message): void { $_SESSION['flash']=['type'=>$type,'message'=>$message]; }
function getFlash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(): void { if(!hash_equals($_SESSION['csrf']??'', $_POST['csrf']??'')) { http_response_code(419); exit('Sessão expirada. Atualize a página.'); } }
function badgeClass(string $status): string {
    $s=mb_strtolower($status);
    if(str_contains($s,'ganh')||str_contains($s,'concl')||str_contains($s,'pago')||str_contains($s,'ativo')||str_contains($s,'resol')) return 'success';
    if(str_contains($s,'perdid')||str_contains($s,'cancel')||str_contains($s,'atras')||str_contains($s,'inativo')) return 'danger';
    if(str_contains($s,'negocia')||str_contains($s,'andamento')||str_contains($s,'pendente')||str_contains($s,'aberto')) return 'warning';
    return 'info';
}
