<?php
// Simple affiliate page display without frameworks or composer
// Adjust database credentials as needed
$host = 'localhost';
$db   = 'afiliados';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Load page by slug
$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    die('Página não encontrada');
}

// Fetch page
$stmt = $pdo->prepare('SELECT id, titulo, slug FROM tb_pagina_afiliados WHERE slug = ?');
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$page) {
    die('Página não encontrada');
}

// Fetch products for page
$stmt = $pdo->prepare('SELECT pr.nome, pr.url_afiliado, pr.preco, pr.imagem
                        FROM tb_produto pr
                        JOIN tb_pagina_produto pp ON pr.id = pp.produto_id
                        WHERE pp.pagina_id = ?');
$stmt->execute([$page['id']]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$pageUrl = $baseUrl . '?slug=' . urlencode($page['slug']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['titulo']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="page-qr">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($pageUrl) ?>" alt="QR da página">
    </div>
    <h1><?= htmlspecialchars($page['titulo']) ?></h1>
    <?php foreach ($products as $prod): ?>
        <div class="product">
            <?php if ($prod['imagem']): ?>
                <img src="<?= htmlspecialchars($prod['imagem']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
            <?php endif; ?>
            <h2><?= htmlspecialchars($prod['nome']) ?></h2>
            <p><strong>Preço:</strong> R$ <?= number_format($prod['preco'], 2, ',', '.') ?></p>
            <a class="cta" href="<?= htmlspecialchars($prod['url_afiliado']) ?>" target="_blank">Comprar</a>
            <div class="qr">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($prod['url_afiliado']) ?>" alt="QR de <?= htmlspecialchars($prod['nome']) ?>">
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
