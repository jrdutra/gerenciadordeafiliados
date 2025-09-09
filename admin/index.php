<?php
require_once __DIR__ . '/../includes/db.php';
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $slug = $_POST['slug'] ?? '';
    if ($titulo && $slug) {
        $stmt = $pdo->prepare('INSERT INTO tb_pagina_afiliados (titulo, slug) VALUES (?, ?)');
        $stmt->execute([$titulo, $slug]);
        header('Location: index.php');
        exit;
    }
}

$pages = $pdo->query('SELECT * FROM tb_pagina_afiliados')->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Painel Admin</title>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head>
<body class='container py-4'>
<h1>Páginas</h1>
<table class='table'>
<thead><tr><th>Título</th><th>Slug</th><th>Ações</th></tr></thead>
<tbody>
<?php foreach ($pages as $page): ?>
<tr>
<td><?= htmlspecialchars($page['titulo']) ?></td>
<td><?= htmlspecialchars($page['slug']) ?></td>
<td>
<a class='btn btn-sm btn-primary' href='page.php?id=<?= $page['id'] ?>'>Gerenciar produtos</a>

<a class='btn btn-sm btn-secondary' href='../public/page.php?slug=<?= $page['slug'] ?>' target='_blank'>Ver</a>

</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<h2>Criar nova página</h2>
<form method='post' class='row g-3'>
<div class='col-md-6'>
<label class='form-label'>Título</label>
<input type='text' name='titulo' class='form-control' required>
</div>
<div class='col-md-6'>
<label class='form-label'>Slug</label>
<input type='text' name='slug' class='form-control' required>
</div>
<div class='col-12'>
<button type='submit' class='btn btn-success'>Criar</button>
</div>
</form>
</body>
</html>
