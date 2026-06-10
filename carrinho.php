<?php
require_once 'db.php';
require_once 'carrinho_helpers.php';

$itens = [];
$total = 0.0;

if (!empty($_SESSION['carrinho'])) {
    try {
        $pdo = conectar();
        $ids = array_keys($_SESSION['carrinho']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $produtos = $stmt->fetchAll();

        foreach ($produtos as $p) {
            $qtd = $_SESSION['carrinho'][$p['id']];
            $subtotal = $p['preco_pix'] * $qtd;
            $itens[] = [
                'id' => $p['id'],
                'nome' => $p['nome'],
                'imagem_url' => $p['imagem_url'],
                'preco_pix' => $p['preco_pix'],
                'estoque' => $p['estoque'],
                'disponivel' => $p['disponivel'],
                'quantidade' => $qtd,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }
    } catch (PDOException $e) {
        $erro = 'Não foi possível carregar o carrinho.<br><small>' . htmlspecialchars($e->getMessage()) . '</small>';
    }
}

$cart_count = carrinho_total_itens();

// Erros vindos da validação do checkout (finalizar_pedido.php)
$erro_pedido = $_SESSION['erro_pedido'] ?? null;
$form_anterior = $_SESSION['form_anterior'] ?? [];
unset($_SESSION['erro_pedido'], $_SESSION['form_anterior']);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrinho – Bruno Surf</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="css/equipamentos.css">
  <link rel="stylesheet" href="css/carrinho.css">
</head>

<body>

  <!-- NAV -->
  <nav>
    <a href="index.php" class="logo">Bruno <span>surf</span></a>
    <ul class="nav-links">
      <li><a href="equipamentos.php">Equipamentos</a></li>
      <li><a href="escola.php">Aulas</a></li>
      <li><a href="galeria.php">Galeria</a></li>
      <li><a href="clima.php">Clima</a></li>
      <li>
        <a href="carrinho.php" class="active cart-link">
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

    <p class="page-eyebrow">Seu pedido</p>
    <h1 class="page-title">Carrinho de <em>compras</em></h1>
    <p class="page-desc">Revise os itens, ajuste as quantidades e finalize sua reserva. O pagamento é feito via PIX.
    </p>

    <?php if (!empty($erro)): ?>
      <div class="alert-erro">⚠️ <?= $erro ?></div>
    <?php endif; ?>

    <?php if (!empty($erro_pedido)): ?>
      <div class="alert-erro">⚠️ <?= $erro_pedido ?></div>
    <?php endif; ?>

    <?php if (empty($itens)): ?>

      <div class="cart-empty">
        <svg viewBox="0 0 24 24" class="cart-empty-icon">
          <circle cx="9" cy="21" r="1" />
          <circle cx="20" cy="21" r="1" />
          <path d="M1 1h4l2.6 13.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6" />
        </svg>
        <p>Seu carrinho está vazio.</p>
        <a href="equipamentos.php" class="btn-aplicar">Ver equipamentos</a>
      </div>

    <?php else: ?>

      <!-- Lista de itens -->
      <form method="POST" action="remover_carrinho.php">
        <div class="cart-list">
          <?php foreach ($itens as $item): ?>
            <div class="cart-item">
              <div class="cart-item-img">
                <?php if (!empty($item['imagem_url']) && file_exists($item['imagem_url'])): ?>
                  <img src="<?= esc($item['imagem_url']) ?>" alt="<?= esc($item['nome']) ?>">
                <?php else: ?>
                  <img src="IMG/chinelo.jpg" alt="<?= esc($item['nome']) ?>">
                <?php endif; ?>
              </div>

              <div class="cart-item-info">
                <p class="cart-item-name"><?= esc($item['nome']) ?></p>
                <?php if (!$item['disponivel']): ?>
                  <span class="badge-indisponivel">Indisponível</span>
                <?php endif; ?>
                <p class="cart-item-price"><?= brl($item['preco_pix']) ?> <span>no pix / un.</span></p>
              </div>

              <div class="cart-item-qty">
                <label>Qtd.</label>
                <input type="number" name="quantidades[<?= $item['id'] ?>]" value="<?= $item['quantidade'] ?>"
                  min="0" max="<?= max(1, (int) $item['estoque']) ?>">
              </div>

              <div class="cart-item-subtotal">
                <?= brl($item['subtotal']) ?>
              </div>

              <a class="cart-item-remove" href="remover_carrinho.php?remover=<?= $item['id'] ?>" title="Remover item">
                <svg viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </a>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="cart-actions">
          <button type="submit" class="btn-atualizar">Atualizar carrinho</button>
          <div class="cart-total">Total: <strong><?= brl($total) ?></strong></div>
        </div>
      </form>

      <!-- Checkout -->
      <div class="checkout-grid">

        <div class="checkout-form">
          <h2>Dados para finalizar</h2>
          <p class="checkout-sub">Como não temos sistema de login, precisamos desses dados para confirmar sua
            reserva e entrar em contato.</p>

          <form method="POST" action="finalizar_pedido.php" class="form-dados">
            <div class="form-group">
              <label>Nome completo</label>
              <input type="text" name="nome_completo" placeholder="Seu nome completo" required
                value="<?= esc($form_anterior['nome_completo'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label>CPF</label>
              <input type="text" name="cpf" placeholder="000.000.000-00" maxlength="14" required
                value="<?= esc($form_anterior['cpf'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label>Contato (WhatsApp)</label>
              <input type="text" name="contato" placeholder="(11) 99999-9999" required
                value="<?= esc($form_anterior['contato'] ?? '') ?>">
            </div>

            <button type="submit" class="btn-finalizar">Finalizar pedido — <?= brl($total) ?></button>
          </form>
        </div>

        <div class="pix-card">
          <h2>Pagamento via PIX</h2>
          <p class="pix-desc">Após finalizar o pedido, copie a chave abaixo e realize o pagamento pelo app do seu
            banco. O pedido fica reservado por 30 minutos.</p>

          <div class="pix-box">
            <span class="pix-label">Chave PIX (CNPJ)</span>
            <div class="pix-key-row">
              <code id="pixKey"><?= esc(PIX_CHAVE) ?></code>
              <button type="button" class="btn-copiar" onclick="copiarPix()">Copiar</button>
            </div>
            <span class="pix-recebedor"><?= esc(PIX_NOME_RECEBEDOR) ?></span>
          </div>

          <ul class="pix-steps">
            <li>1. Finalize o pedido ao lado</li>
            <li>2. Copie a chave PIX</li>
            <li>3. Pague o valor total no app do seu banco</li>
            <li>4. Envie o comprovante pelo WhatsApp</li>
          </ul>
        </div>

      </div>

    <?php endif; ?>

  </div>

  <div class="toast" id="toast">Chave PIX copiada!</div>

  <script>
    function copiarPix() {
      const texto = document.getElementById('pixKey').textContent.trim();
      navigator.clipboard.writeText(texto).then(() => {
        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
      });
    }

    // Máscara simples de CPF
    const cpfInput = document.querySelector('input[name="cpf"]');
    if (cpfInput) {
      cpfInput.addEventListener('input', () => {
        let v = cpfInput.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        cpfInput.value = v;
      });
    }
  </script>

</body>

</html>
