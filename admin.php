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
    $name = trim($_POST['page_name'] ?? '');
    if ($name) {
        $stmt = $pdo->prepare('INSERT INTO pages (nome) VALUES (?)');
        $stmt->execute([$name]);
    }
}

// Create new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $pageId = (int)($_POST['page_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? '');
    $link = trim($_POST['affiliate_link'] ?? '');
    $imageBase64 = '';
    if (!empty($_FILES['image']['tmp_name'])) {
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        $data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
        $imageBase64 = 'data:' . $mime . ';base64,' . $data;
    }
    if ($pageId && $name && $link) {
        $stmt = $pdo->prepare('INSERT INTO products (page_id, nome, link_afiliado, imagem_base64) VALUES (?,?,?,?)');
        $stmt->execute([$pageId, $name, $link, $imageBase64]);
    }
}

$pages = $pdo->query('SELECT id, nome FROM pages ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query('SELECT p.id, p.nome, p.link_afiliado, p.imagem_base64, pg.nome AS page_name FROM products p JOIN pages pg ON p.page_id = pg.id ORDER BY p.id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI', sans-serif; background:#fefcfb; color:#2c2c2c; padding:20px; }
        h1 { font-size:2rem; color:#6f5d90; margin-bottom:20px; text-align:center; }
        form { margin-bottom:40px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.05); }
        form div { margin-bottom:10px; }
        label { display:block; margin-bottom:5px; }
        input[type="text"], input[type="url"], select { width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; }
        input[type="file"] { border:1px solid #ccc; padding:8px; border-radius:4px; width:100%; }
        button { background:#8c7dbf; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; }
        button:hover { background:#7a6ab0; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        th { background:#eee; }
        img { max-width:100px; }
    </style>
</head>
<body>
    <h1>Painel Administrativo</h1>

    <form method="post">
        <h2>Nova Página</h2>
        <div>
            <label for="page_name">Nome da página</label>
            <input type="text" id="page_name" name="page_name" required>
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
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
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
                <th>Imagem</th>
                <th>Link</th>
                <th>QR</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['page_name']) ?></td>
                <td><?= htmlspecialchars($prod['nome']) ?></td>
                <td><?php if ($prod['imagem_base64']): ?><img src="<?= $prod['imagem_base64'] ?>" alt="<?= htmlspecialchars($prod['nome']) ?>"><?php endif; ?></td>
                <td><a href="<?= htmlspecialchars($prod['link_afiliado']) ?>" target="_blank">Link</a></td>
                <td><img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode($prod['link_afiliado']) ?>" alt="QR"></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
