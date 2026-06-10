<?php
require_once 'carrinho_helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['equipamento_id'] ?? 0);
    $qtd = max(1, (int) ($_POST['quantidade'] ?? 1));

    if ($id > 0) {
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id] += $qtd;
        } else {
            $_SESSION['carrinho'][$id] = $qtd;
        }
    }
}

// Volta para a página de onde veio (ou para o catálogo, por padrão)
$voltar = $_POST['voltar'] ?? 'equipamentos.php';
header('Location: ' . $voltar);
exit;
