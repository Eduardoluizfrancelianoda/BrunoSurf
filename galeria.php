<?php
require_once 'carrinho_helpers.php'; // sessão + funções do carrinho

$cart_count = carrinho_total_itens();
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
    <link rel="stylesheet" href="css/carrinho.css">
</head>

<body>

    <nav>
        <a href="index.php" class="logo">Bruno <span>surf</span></a>
        <ul class="nav-links">
            <li><a href="equipamentos.php">Equipamentos</a></li>
            <li><a href="escola.php">Aulas</a></li>
            <li><a href="galeria.php" class="active">Galeria</a></li>
            <li><a href="clima.php">Clima</a></li>
            <li>
        <a href="carrinho.php" class="cart-link">
          <svg viewBox="0 0 24 24" class="cart-icon">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.6 13.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6" />
          </svg>
          Carrinho
          <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?= $cart_count ?></span>
          <?php endif; ?>
        </a>
      </li>
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
