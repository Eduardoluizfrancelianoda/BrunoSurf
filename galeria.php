<?php
$images = [];
$allowed = ['jpg','jpeg','png','gif','webp','avif'];
$dir = __DIR__ . '/IMG';
if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed, true)) {
            $images[] = 'IMG/' . $file;
        }
    }
}
sort($images);

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria – Bruno Surf</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/galeria.css">
</head>

<body>

    <nav>
        <a href="index.php" class="logo">Bruno <span>surf</span></a>
        <ul class="nav-links">
            <li><a href="equipamentos.php">Equipamentos</a></li>
            <li><a href="escola.php">Aulas</a></li>
            <li><a href="galeria.php" class="active">Galeria</a></li>
            <li><a href="clima.php">Clima</a></li>
        </ul>
    </nav>

    <div class="page">
        <p class="page-eyebrow">Galeria</p>
        <h1 class="page-title">Momentos em <em>ondas</em></h1>
        <p class="page-desc">Imagens de surf, treino e lifestyle.</p>

        <section class="gallery-section">
            <div class="gallery-grid">
                <?php if (empty($images)): ?>
                    <div class="empty-gallery">Nenhuma imagem encontrada na pasta <strong>IMG</strong>.</div>
                <?php else: ?>
                    <?php foreach ($images as $img): ?>
                        <div class="gallery-item">
                            <img src="<?= esc($img) ?>" alt="<?= esc(pathinfo($img, PATHINFO_FILENAME)) ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

</body>

</html>
