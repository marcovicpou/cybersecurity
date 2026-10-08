<?php
require __DIR__.'/includes/site.php';
header('Content-Type: application/xml; charset=utf-8');
$basePath = rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'] ?? '/sitemap.php')), '/') . '/';
$rootUrl = public_origin() . $basePath;
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url><loc><?= escape($rootUrl) ?></loc></url>
<?php foreach ($projects as $project): ?>
<url><loc><?= escape($rootUrl.'projetos/'.$project['slug'].'/') ?></loc></url>
<?php endforeach; ?>
</urlset>
