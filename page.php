<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/qrcode.php';
$config = require __DIR__ . '/config/config.php';
$pdo = getPDO();

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare('SELECT * FROM tb_pagina_afiliados WHERE slug = ?');
$stmt->execute([$slug]);
$page = $stmt->fetch();
if (!$page) {
    http_response_code(404);
    echo 'Página não encontrada';
    exit;
}

$productsStmt = $pdo->prepare('SELECT p.* FROM tb_produto p JOIN tb_pagina_produto pp ON p.id = pp.produto_id WHERE pp.pagina_id = ?');
$productsStmt->execute([$page['id']]);
$products = $productsStmt->fetchAll();

$pageUrl = rtrim($config['base_url'], '/') . '/page.php?slug=' . urlencode($page['slug']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title><?= htmlspecialchars($page['titulo']) ?></title>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head>
<body class='container py-4'>
<h1><?= htmlspecialchars($page['titulo']) ?></h1>
<div class='mb-4'>
<strong>QR da página:</strong><br>
<img src='<?= generateQrBase64($pageUrl) ?>' width='120' height='120' alt='QR da página'>
</div>
<div class='row'>
<?php foreach ($products as $prod): ?>
<div class='col-md-4 mb-4'>
<div class='card h-100'>
<?php if ($prod['imagem']): ?>
<img src='<?= htmlspecialchars($prod['imagem']) ?>' class='card-img-top' alt='<?= htmlspecialchars($prod['nome']) ?>'>
<?php endif; ?>
<div class='card-body'>
<h5 class='card-title'><?= htmlspecialchars($prod['nome']) ?></h5>
<p class='card-text'>R$ <?= number_format($prod['preco'],2,',','.') ?></p>
<a href='<?= htmlspecialchars($prod['url_afiliado']) ?>' target='_blank' class='btn btn-primary mb-2'>Comprar</a>
<div>
<img src='<?= generateQrBase64($prod['url_afiliado']) ?>' width='100' height='100' alt='QR do produto'>
</div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</body>
</html>
