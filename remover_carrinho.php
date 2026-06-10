<?php
require_once 'carrinho_helpers.php';

// Remover item via link (?remover=ID)
if (isset($_GET['remover'])) {
    $id = (int) $_GET['remover'];
    unset($_SESSION['carrinho'][$id]);
    header('Location: carrinho.php');
    exit;
}

// Atualizar quantidades via formulário (POST quantidades[id] = qtd)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantidades'])) {
    foreach ($_POST['quantidades'] as $id => $qtd) {
        $id = (int) $id;
        $qtd = (int) $qtd;

        if ($qtd <= 0) {
            unset($_SESSION['carrinho'][$id]);
        } elseif (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id] = $qtd;
        }
    }
}

header('Location: carrinho.php');
exit;
