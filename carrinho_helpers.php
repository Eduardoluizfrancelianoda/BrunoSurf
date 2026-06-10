<?php
// ── Funções auxiliares do carrinho ──────────────────────────
// Incluir este arquivo em qualquer página que use o carrinho.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = []; // [equipamento_id => quantidade]
}

if (!function_exists('brl')) {
    function brl(float $v): string
    {
        return 'R$' . number_format($v, 2, ',', '.');
    }
}

if (!function_exists('esc')) {
    function esc(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    }
}

// Total de itens (somando quantidades) — usado no badge do carrinho
function carrinho_total_itens(): int
{
    return array_sum($_SESSION['carrinho']);
}

// Valida um CPF (formato + dígitos verificadores)
function validar_cpf(string $cpf): bool
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) !== 11) return false;
    if (preg_match('/^(\d)\1{10}$/', $cpf)) return false; // 111.111.111-11 etc.

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int) $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ((int) $cpf[$t] !== $digito) return false;
    }

    return true;
}

// Formata CPF para 000.000.000-00
function formatar_cpf(string $cpf): string
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

// Chave PIX fictícia da loja (apenas para demonstração)
define('PIX_CHAVE', '29.183.475/0001-02');
define('PIX_NOME_RECEBEDOR', 'Bruno Nascimento - Bruno Surf & Beach');
