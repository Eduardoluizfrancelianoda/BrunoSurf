<?php

require_once 'db.php'; // conexão centralizada
require_once 'carrinho_helpers.php'; // sessão + funções do carrinho

$cart_count = carrinho_total_itens();

// ── Filtros recebidos via GET ────────────────────────────────
$categoria = trim($_GET['categoria'] ?? '');
$marca = trim($_GET['marca'] ?? '');
$preco_max = (float) ($_GET['preco_max'] ?? 0);
$disponivel = $_GET['disponivel'] ?? '';
$ordem = $_GET['ordem'] ?? 'lancamento';
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$por_pagina = 8;

// ── Monta a query dinamicamente ─────────────────────────────
$where = ['1 = 1'];
$params = [];

if ($categoria !== '') {
  $where[] = 'categoria = :categoria';
  $params['categoria'] = $categoria;
}
if ($marca !== '') {
  $where[] = 'marca = :marca';
  $params['marca'] = $marca;
}
if ($preco_max > 0) {
  $where[] = 'preco_pix <= :preco_max';
  $params['preco_max'] = $preco_max;
}
if ($disponivel === '1') {
  $where[] = 'disponivel = TRUE';
} elseif ($disponivel === '0') {
  $where[] = 'disponivel = FALSE';
}

$order_map = [
  'lancamento' => 'id DESC',
  'preco_asc' => 'preco_pix ASC',
  'preco_desc' => 'preco_pix DESC',
  'nome' => 'nome ASC',
];
$order_sql = $order_map[$ordem] ?? 'id DESC';

$where_sql = implode(' AND ', $where);

try {
  $pdo = conectar();

  // Total de registros (para paginação)
  $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM equipamentos WHERE $where_sql");
  $stmt_total->execute($params);
  $total = (int) $stmt_total->fetchColumn();
  $total_paginas = max(1, (int) ceil($total / $por_pagina));
  $pagina = min($pagina, $total_paginas);
  $offset = ($pagina - 1) * $por_pagina;

  // Produtos da página atual
  $sql = "SELECT * FROM equipamentos WHERE $where_sql ORDER BY $order_sql LIMIT :limit OFFSET :offset";
  $stmt = $pdo->prepare($sql);
  foreach ($params as $k => $v)
    $stmt->bindValue(":$k", $v);
  $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
  $produtos = $stmt->fetchAll();

  // Valores únicos para os selects de filtro
  $categorias = $pdo->query("SELECT DISTINCT categoria FROM equipamentos ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
  $marcas = $pdo->query("SELECT DISTINCT marca     FROM equipamentos WHERE marca IS NOT NULL ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);

  $erro = null;
} catch (PDOException $e) {
  $produtos = [];
  $total = 0;
  $total_paginas = 1;
  $categorias = [];
  $marcas = [];
  $erro = 'Não foi possível conectar ao banco de dados. Verifique as credenciais em equipamentos.php.<br><small>' . htmlspecialchars($e->getMessage()) . '</small>';
}

// ── Helpers ─────────────────────────────────────────────────
// brl() e esc() já estão definidas em carrinho_helpers.php

function qstr(array $extra = []): string
{
  $base = array_filter([
    'categoria' => $_GET['categoria'] ?? '',
    'marca' => $_GET['marca'] ?? '',
    'preco_max' => $_GET['preco_max'] ?? '',
    'disponivel' => $_GET['disponivel'] ?? '',
    'ordem' => $_GET['ordem'] ?? '',
  ]);
  return http_build_query(array_merge($base, $extra));
}
function sel(string $campo, string $valor): string
{
  return (($_GET[$campo] ?? '') === $valor) ? 'selected' : '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Equipamentos – Bruno Surf</title>
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
      <li><a href="equipamentos.php" class="active">Equipamentos</a></li>
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
          <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?= $cart_count ?></span>
          <?php endif; ?>
        </a>
      </li>
    </ul>
  </nav>

  <div class="page">

    <!-- Cabeçalho -->
    <p class="page-eyebrow">Aluguel de Equipamentos</p>
    <h1 class="page-title">Tudo que você precisa <em>na praia</em></h1>
    <p class="page-desc">Equipamentos em ótimo estado, com opção de retirada na loja ou delivery na praia. Reserve com
      antecedência ou venha direto!</p>

    <?php if ($erro): ?>
      <div class="alert-erro">⚠️ <?= $erro ?></div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="toolbar">
      <span class="toolbar-count">
        <strong><?= $total ?></strong> produto<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?>
      </span>
      <div class="toolbar-right">
        <button class="btn-filtrar" id="toggleFiltro" type="button">
          <svg viewBox="0 0 24 24">
            <line x1="4" y1="6" x2="20" y2="6" />
            <line x1="8" y1="12" x2="16" y2="12" />
            <line x1="11" y1="18" x2="13" y2="18" />
          </svg>
          Filtrar
        </button>
        <span class="ordenar-label">Ordenar por:</span>
        <!-- Ordenação muda via GET, mantendo outros filtros -->
        <select class="select-ordenar"
          onchange="window.location='equipamentos.php?<?= esc(qstr()) ?>&ordem='+this.value">
          <option value="lancamento" <?= sel('ordem', 'lancamento') ?>>Data de Lançamento</option>
          <option value="preco_asc" <?= sel('ordem', 'preco_asc') ?>>Menor Preço</option>
          <option value="preco_desc" <?= sel('ordem', 'preco_desc') ?>>Maior Preço</option>
          <option value="nome" <?= sel('ordem', 'nome') ?>>Nome A–Z</option>
        </select>
      </div>
    </div>

    <!-- Painel de filtros (form GET) -->
    <div
      class="filter-panel <?= (isset($_GET['categoria']) || isset($_GET['marca']) || isset($_GET['preco_max']) || isset($_GET['disponivel'])) ? 'open' : '' ?>"
      id="filterPanel">
      <form method="GET" action="equipamentos.php" style="display:contents">
        <!-- mantém a ordenação ao aplicar filtros -->
        <?php if ($ordem !== 'lancamento'): ?>
          <input type="hidden" name="ordem" value="<?= esc($ordem) ?>">
        <?php endif; ?>

        <div class="filter-group">
          <label>Categoria</label>
          <select name="categoria">
            <option value="">Todas</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= esc($cat) ?>" <?= sel('categoria', $cat) ?>><?= esc(ucfirst($cat)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <label>Marca</label>
          <select name="marca">
            <option value="">Todas</option>
            <?php foreach ($marcas as $m): ?>
              <option value="<?= esc($m) ?>" <?= sel('marca', $m) ?>><?= esc($m) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <label>Preço pix até</label>
          <input type="number" name="preco_max" min="0" step="0.01" placeholder="Ex: 300"
            value="<?= esc($_GET['preco_max'] ?? '') ?>">
        </div>

        <div class="filter-group">
          <label>Disponível</label>
          <select name="disponivel">
            <option value="">Todos</option>
            <option value="1" <?= sel('disponivel', '1') ?>>Sim</option>
            <option value="0" <?= sel('disponivel', '0') ?>>Não</option>
          </select>
        </div>

        <button class="btn-aplicar" type="submit">Aplicar</button>
        <a class="btn-limpar" href="equipamentos.php">Limpar</a>
      </form>
    </div>

    <!-- Grid de produtos -->
    <div class="products-grid">
      <?php if (empty($produtos)): ?>
        <div class="no-results">Nenhum produto encontrado para os filtros selecionados.</div>

      <?php else: ?>
        <?php foreach ($produtos as $p): ?>
          <div class="product-card">
            <div class="product-img">
              <?php if (!empty($p['imagem_url']) && file_exists($p['imagem_url'])): ?>
                <img src="<?= esc($p['imagem_url']) ?>" alt="<?= esc($p['nome']) ?>">
              <?php else: ?>
                <img src="IMG/chinelo.jpg" alt="<?= esc($p['nome']) ?>" class="placeholder-img">
              <?php endif; ?>
            </div>

            <div class="product-info">
              <p class="product-name"><?= esc($p['nome']) ?></p>
              <p class="product-price-old">
                <?= brl($p['preco_parcelado']) ?> por mês<br>
                em <?= (int) $p['num_parcelas'] ?>x sem juros ou
              </p>
              <p class="product-price-pix"><?= brl($p['preco_pix']) ?> no pix</p>
              <?php if (!$p['disponivel']): ?>
                <span class="badge-indisponivel">Indisponível</span>
              <?php endif; ?>
            </div>

            <form class="product-add-form" method="POST" action="adicionar_carrinho.php">
              <input type="hidden" name="equipamento_id" value="<?= (int) $p['id'] ?>">
              <input type="hidden" name="voltar" value="<?= esc('equipamentos.php?' . qstr(['pagina' => $pagina])) ?>">
              <input type="number" name="quantidade" value="1" min="1" max="<?= max(1, (int) $p['estoque']) ?>"
                <?= !$p['disponivel'] ? 'disabled' : '' ?>>
              <button type="submit" class="btn-add-cart" <?= !$p['disponivel'] ? 'disabled' : '' ?>>
                <?= $p['disponivel'] ? 'Adicionar ao carrinho' : 'Indisponível' ?>
              </button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Paginação -->
    <?php if ($total_paginas > 1): ?>
      <div class="pagination">
        <!-- Anterior -->
        <a class="page-btn arrow <?= $pagina <= 1 ? 'disabled' : '' ?>"
          href="?<?= esc(qstr(['pagina' => $pagina - 1])) ?>">‹</a>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <a class="page-btn <?= $i === $pagina ? 'active' : '' ?>" href="?<?= esc(qstr(['pagina' => $i])) ?>"><?= $i ?></a>
        <?php endfor; ?>

        <!-- Próxima -->
        <a class="page-btn arrow <?= $pagina >= $total_paginas ? 'disabled' : '' ?>"
          href="?<?= esc(qstr(['pagina' => $pagina + 1])) ?>">›</a>
      </div>
    <?php endif; ?>

  </div>

  <script>
    // Toggle do painel de filtros
    document.getElementById('toggleFiltro').addEventListener('click', () => {
      document.getElementById('filterPanel').classList.toggle('open');
    });
  </script>

</body>

</html>