<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/qrcode.php';
$pdo = getPDO();

$id = (int)($_GET['id'] ?? 0);
$pageStmt = $pdo->prepare('SELECT * FROM tb_pagina_afiliados WHERE id = ?');
$pageStmt->execute([$id]);
$page = $pageStmt->fetch();
if (!$page) {
    http_response_code(404);
    echo 'Página não encontrada';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $url = $_POST['url_afiliado'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $imagem = $_POST['imagem'] ?? null;
    if ($nome && $url) {
        $stmt = $pdo->prepare('INSERT INTO tb_produto (nome, url_afiliado, preco, imagem) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nome, $url, $preco, $imagem]);
        $produtoId = $pdo->lastInsertId();
        $linkStmt = $pdo->prepare('INSERT INTO tb_pagina_produto (pagina_id, produto_id) VALUES (?, ?)');
        $linkStmt->execute([$id, $produtoId]);
        header('Location: page.php?id=' . $id);
        exit;
    }
}

$produtos = $pdo->prepare('SELECT p.* FROM tb_produto p JOIN tb_pagina_produto pp ON p.id = pp.produto_id WHERE pp.pagina_id = ?');
$produtos->execute([$id]);
$produtos = $produtos->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Produtos da página</title>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head>
<body class='container py-4'>
<h1>Produtos - <?= htmlspecialchars($page['titulo']) ?></h1>
<p><a href='index.php' class='btn btn-secondary'>Voltar</a></p>
<table class='table'>
<thead><tr><th>Nome</th><th>Preço</th><th>QR</th><th>Link</th></tr></thead>
<tbody>
<?php foreach ($produtos as $prod): ?>
<tr>
<td><?= htmlspecialchars($prod['nome']) ?></td>
<td>R$ <?= number_format($prod['preco'],2,',','.') ?></td>
<td><img src='<?= generateQrBase64($prod['url_afiliado']) ?>' width='80' height='80' alt='QR'></td>
<td><a href='<?= htmlspecialchars($prod['url_afiliado']) ?>' target='_blank'>Acessar</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<h2>Adicionar produto</h2>
<form method='post' class='row g-3'>
<div class='col-md-4'>
<label class='form-label'>Nome</label>
<input type='text' name='nome' class='form-control' required>
</div>
<div class='col-md-4'>
<label class='form-label'>Preço</label>
<input type='number' step='0.01' name='preco' class='form-control' required>
</div>
<div class='col-md-4'>
<label class='form-label'>URL Afiliado</label>
<input type='url' name='url_afiliado' class='form-control' required>
</div>
<div class='col-md-6'>
<label class='form-label'>URL da Imagem</label>
<input type='url' name='imagem' class='form-control'>
</div>
<div class='col-12'>
<button type='submit' class='btn btn-success'>Adicionar</button>
</div>
</form>
</body>
</html>
