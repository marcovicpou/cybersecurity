<?php
function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function arrow() {
    return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
function public_origin() {
    $configured = getenv('DREAMLAST_PUBLIC_URL');
    if ($configured && filter_var($configured, FILTER_VALIDATE_URL) && in_array(parse_url($configured, PHP_URL_SCHEME), ['http','https'], true)) {
        return rtrim($configured, '/');
    }
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (!preg_match('/^[a-zA-Z0-9.-]+(?::[0-9]{1,5})?$/D', $host)) $host = 'localhost';
    $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    return $scheme . '://' . $host;
}
function screenshot($project, $base, $detail=false, $eager=false) {
    $file = $project['slug'] . ($detail ? '-detail' : '');
    $label = $detail ? $project['detail_screen'] : $project['screen'];
    echo '<picture><source type="image/webp" srcset="' . escape($base.'assets/img/'.$file.'-720.webp') . ' 720w, ' . escape($base.'assets/img/'.$file.'.webp') . ' 1440w" sizes="(max-width: 760px) 100vw, 64vw"><img src="' . escape($base.'assets/img/'.$file.'.webp') . '" width="1440" height="1000" loading="' . ($eager ? 'eager' : 'lazy') . '" decoding="async"' . ($eager ? ' fetchpriority="high"' : '') . ' alt="' . escape($label.' do '.$project['name'].', com dados de demonstração') . '"></picture>';
}
$projects = require __DIR__.'/projects.php';
$contactEmail = 'contato.dreamlast@gmail.com';
$contactUrl = 'mailto:'.$contactEmail.'?subject=Projeto%20com%20a%20Dream%20Last';
