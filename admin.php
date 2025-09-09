<?php
// Painel administrativo simples
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

// Create new page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_page'])) {
    $title = trim($_POST['page_title'] ?? '');
    $slug  = trim($_POST['page_slug'] ?? '');
    if ($title && $slug) {
        $stmt = $pdo->prepare('INSERT INTO tb_pagina_afiliados (titulo, slug) VALUES (?, ?)');
        $stmt->execute([$title, $slug]);
    }
}

// Create new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $pageId = (int)($_POST['page_id'] ?? 0);
    $name   = trim($_POST['product_name'] ?? '');
    $link   = trim($_POST['affiliate_link'] ?? '');
    $price  = str_replace(',', '.', $_POST['price'] ?? '0');
    $imageBase64 = '';
    if (!empty($_FILES['image']['tmp_name'])) {
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        $data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
        $imageBase64 = 'data:' . $mime . ';base64,' . $data;
    }
    if ($pageId && $name && $link) {
        $stmt = $pdo->prepare('INSERT INTO tb_produto (nome, url_afiliado, preco, imagem) VALUES (?,?,?,?)');
        $stmt->execute([$name, $link, (float)$price, $imageBase64]);
        $prodId = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO tb_pagina_produto (pagina_id, produto_id) VALUES (?, ?)');
        $stmt->execute([$pageId, $prodId]);
    }
}

// Retrieve pages; if the `id` column is absent (older schema) fall back to `pagina_id`
try {
    $pages = $pdo->query('SELECT id, titulo FROM tb_pagina_afiliados ORDER BY titulo')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pages = $pdo->query('SELECT pagina_id AS id, titulo FROM tb_pagina_afiliados ORDER BY titulo')->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve products and join pages, accommodating either `id` or `pagina_id`
try {
    $products = $pdo->query('SELECT pr.id, pr.nome, pr.url_afiliado, pr.preco, pr.imagem, pa.titulo AS page_title
                          FROM tb_produto pr
                          JOIN tb_pagina_produto pp ON pr.id = pp.produto_id
                          JOIN tb_pagina_afiliados pa ON pp.pagina_id = pa.id
                          ORDER BY pr.id DESC')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = $pdo->query('SELECT pr.id, pr.nome, pr.url_afiliado, pr.preco, pr.imagem, pa.titulo AS page_title
                          FROM tb_produto pr
                          JOIN tb_pagina_produto pp ON pr.id = pp.produto_id
                          JOIN tb_pagina_afiliados pa ON pp.pagina_id = pa.pagina_id
                          ORDER BY pr.id DESC')->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Painel Administrativo</h1>

    <form method="post">
        <h2>Nova Página</h2>
        <div>
            <label for="page_title">Título da página</label>
            <input type="text" id="page_title" name="page_title" required>
        </div>
        <div>
            <label for="page_slug">Slug</label>
            <input type="text" id="page_slug" name="page_slug" required>
        </div>
        <button type="submit" name="create_page">Criar Página</button>
    </form>

    <form method="post" enctype="multipart/form-data">
        <h2>Novo Produto</h2>
        <div>
            <label for="page_id">Página</label>
            <select name="page_id" id="page_id" required>
                <option value="">Selecione</option>
                <?php foreach ($pages as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['titulo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="product_name">Nome do produto</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>
        <div>
            <label for="affiliate_link">Link de afiliado</label>
            <input type="url" id="affiliate_link" name="affiliate_link" required>
        </div>
        <div>
            <label for="price">Preço</label>
            <input type="text" id="price" name="price" required>
        </div>
        <div>
            <label for="image">Imagem</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit" name="create_product">Adicionar Produto</button>
    </form>

    <h2>Produtos cadastrados</h2>
    <table>
        <thead>
            <tr>
                <th>Página</th>
                <th>Produto</th>
                <th>Preço</th>
                <th>Imagem</th>
                <th>Link</th>
                <th>QR</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['page_title']) ?></td>
                <td><?= htmlspecialchars($prod['nome']) ?></td>
                <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                <td><?php if ($prod['imagem']): ?><img src="<?= htmlspecialchars($prod['imagem']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>"><?php endif; ?></td>
                <td><a href="<?= htmlspecialchars($prod['url_afiliado']) ?>" target="_blank">Link</a></td>
                <td><img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode($prod['url_afiliado']) ?>" alt="QR"></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
