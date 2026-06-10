<?php
require_once 'db.php';
require_once 'carrinho_helpers.php';

// ── Validação ────────────────────────────────────────────────
$nome = trim($_POST['nome_completo'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$contato = trim($_POST['contato'] ?? '');

$erros = [];

if ($nome === '' || mb_strlen($nome) < 3) {
    $erros[] = 'Informe seu nome completo.';
}
if (!validar_cpf($cpf)) {
    $erros[] = 'CPF inválido. Verifique os números digitados.';
}
if ($contato === '' || mb_strlen($contato) < 8) {
    $erros[] = 'Informe um telefone/WhatsApp válido para contato.';
}
if (empty($_SESSION['carrinho'])) {
    $erros[] = 'Seu carrinho está vazio.';
}

// ── Se houver erro, volta pro carrinho com aviso ──────────────
if (!empty($erros)) {
    $_SESSION['erro_pedido'] = implode('<br>', $erros);
    $_SESSION['form_anterior'] = ['nome_completo' => $nome, 'cpf' => $cpf, 'contato' => $contato];
    header('Location: carrinho.php');
    exit;
}

// ── Monta o pedido ────────────────────────────────────────────
try {
    $pdo = conectar();

    $ids = array_keys($_SESSION['carrinho']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();

    if (empty($produtos)) {
        throw new Exception('Os itens do carrinho não foram encontrados.');
    }

    $itens = [];
    $total = 0.0;
    foreach ($produtos as $p) {
        $qtd = (int) $_SESSION['carrinho'][$p['id']];
        $subtotal = $p['preco_pix'] * $qtd;
        $itens[] = [
            'equipamento_id' => $p['id'],
            'nome' => $p['nome'],
            'quantidade' => $qtd,
            'preco_unitario' => $p['preco_pix'],
            'subtotal' => $subtotal,
        ];
        $total += $subtotal;
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "INSERT INTO pedidos (nome_completo, cpf, contato, valor_total, chave_pix, status)
         VALUES (:nome, :cpf, :contato, :total, :chave_pix, 'aguardando_pagamento')"
    );
    $stmt->execute([
        'nome' => $nome,
        'cpf' => formatar_cpf($cpf),
        'contato' => $contato,
        'total' => $total,
        'chave_pix' => PIX_CHAVE,
    ]);
    $pedido_id = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare(
        "INSERT INTO pedido_itens (pedido_id, equipamento_id, nome_equipamento, quantidade, preco_unitario, subtotal)
         VALUES (:pedido_id, :equipamento_id, :nome, :quantidade, :preco_unitario, :subtotal)"
    );
    foreach ($itens as $item) {
        $stmt->execute([
            'pedido_id' => $pedido_id,
            'equipamento_id' => $item['equipamento_id'],
            'nome' => $item['nome'],
            'quantidade' => $item['quantidade'],
            'preco_unitario' => $item['preco_unitario'],
            'subtotal' => $item['subtotal'],
        ]);
    }

    $pdo->commit();

    // Carrinho esvaziado após pedido confirmado
    $_SESSION['carrinho'] = [];

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['erro_pedido'] = 'Não foi possível finalizar o pedido. Tente novamente.<br><small>' . htmlspecialchars($e->getMessage()) . '</small>';
    header('Location: carrinho.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido confirmado – Bruno Surf</title>
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
        <a href="carrinho.php" class="cart-link">
          <svg viewBox="0 0 24 24" class="cart-icon">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.6 13.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6" />
          </svg>
          Carrinho
        </a>
      </li>
    </ul>
  </nav>

  <div class="page">

    <div class="confirm-box">
      <div class="confirm-icon">✓</div>
      <p class="page-eyebrow" style="text-align:center">Pedido nº <?= str_pad($pedido_id, 6, '0', STR_PAD_LEFT) ?>
      </p>
      <h1 class="page-title" style="text-align:center">Pedido <em>recebido</em>!</h1>
      <p class="page-desc" style="text-align:center; margin-left:auto; margin-right:auto;">
        Obrigado, <?= esc($nome) ?>! Reservamos seus itens por <strong>30 minutos</strong>. Conclua o pagamento
        via PIX abaixo e envie o comprovante pelo WhatsApp para confirmar.
      </p>

      <div class="pix-card" style="margin: 0 auto 32px; max-width: 420px;">
        <h2>Pagamento via PIX</h2>
        <div class="pix-box">
          <span class="pix-label">Chave PIX (CNPJ)</span>
          <div class="pix-key-row">
            <code id="pixKey"><?= esc(PIX_CHAVE) ?></code>
            <button type="button" class="btn-copiar" onclick="copiarPix()">Copiar</button>
          </div>
          <span class="pix-recebedor"><?= esc(PIX_NOME_RECEBEDOR) ?></span>
        </div>
        <div class="pix-total">Valor total: <strong><?= brl($total) ?></strong></div>
      </div>

      <div class="resumo-pedido">
        <h2>Resumo do pedido</h2>
        <table class="tabela-resumo">
          <?php foreach ($itens as $item): ?>
            <tr>
              <td><?= esc($item['nome']) ?> <span class="qtd-x">x<?= $item['quantidade'] ?></span></td>
              <td class="valor"><?= brl($item['subtotal']) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr class="total-row">
            <td>Total</td>
            <td class="valor"><?= brl($total) ?></td>
          </tr>
        </table>

        <p class="dados-cliente">
          <strong>Nome:</strong> <?= esc($nome) ?><br>
          <strong>CPF:</strong> <?= esc(formatar_cpf($cpf)) ?><br>
          <strong>Contato:</strong> <?= esc($contato) ?>
        </p>
      </div>

      <div class="confirm-actions">
        <a href="https://wa.me/5511999999999?text=Ol%C3%A1!%20Acabei%20de%20fazer%20o%20pedido%20n%C2%BA%20<?= $pedido_id ?>%20e%20vou%20enviar%20o%20comprovante%20do%20PIX."
          class="btn-finalizar" target="_blank">Enviar comprovante no WhatsApp</a>
        <a href="equipamentos.php" class="btn-atualizar">Voltar para equipamentos</a>
      </div>
    </div>

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
  </script>

</body>

</html>
