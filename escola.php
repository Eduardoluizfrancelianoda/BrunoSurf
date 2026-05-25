<?php
// ── Configuração do banco ────────────────────────────────────
require_once 'db.php'; // conexão centralizada

// Filtros
$nivel = $_GET['nivel'] ?? '';
$ordem = $_GET['ordem'] ?? 'dificuldade';

$where  = ['disponivel = TRUE'];
$params = [];

if (in_array($nivel, ['iniciante', 'intermediario', 'avancado'])) {
    $where[]       = 'nivel = :nivel';
    $params['nivel'] = $nivel;
}

$order_map = [
    'dificuldade' => "FIELD(nivel,'iniciante','intermediario','avancado')",
    'preco_asc'   => 'preco_individual ASC',
    'preco_desc'  => 'preco_individual DESC',
    'duracao'     => 'duracao_minutos ASC',
];
$order_sql = $order_map[$ordem] ?? $order_map['dificuldade'];
$where_sql = implode(' AND ', $where);

try {
    $pdo = conectar();

    $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM aulas WHERE $where_sql");
    $stmt_total->execute($params);
    $total = (int)$stmt_total->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM aulas WHERE $where_sql ORDER BY $order_sql");
    $stmt->execute($params);
    $aulas = $stmt->fetchAll();

    $erro = null;
} catch (PDOException $e) {
    $aulas = [];
    $total = 0;
    $erro  = htmlspecialchars($e->getMessage());
}

function esc(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function brl(float $v): string  { return 'R$' . number_format($v, 0, ',', '.'); }

$nivel_label = ['iniciante' => 'Iniciante', 'intermediario' => 'Intermediário', 'avancado' => 'Avançado'];
$nivel_color = ['iniciante' => '#2ec4c4', 'intermediario' => '#e8622a', 'avancado' => '#e8322a'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aulas – Bruno Surf</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/aulas.css">
</head>
<body>

<nav>
  <a href="index.php" class="logo">Bruno <span>surf</span></a>
  <ul class="nav-links">
    <li><a href="equipamentos.php">Equipamentos</a></li>
    <li><a href="aulas.php" class="active">Aulas</a></li>
    <li><a href="galeria.php">Galeria</a></li>
    <li><a href="clima.php">Clima</a></li>
  </ul>
</nav>

<div class="page">

  <p class="eyebrow">Escola de Surf</p>
  <h1 class="page-title">Aprenda a surfar com quem <em>vive o mar</em></h1>
  <p class="page-desc">Aulas ministradas por Bruno Nascimento, instrutor certificado com mais de 8 anos de experiência. Do remo até a primeira onda em pé.</p>

  <?php if ($erro): ?>
    <div class="alert">⚠️ Erro ao conectar ao banco de dados: <?= $erro ?></div>
  <?php endif; ?>

  <!-- Toolbar -->
  <div class="toolbar">
    <span class="toolbar-count"><strong><?= $total ?></strong> Aula<?= $total !== 1 ? 's' : '' ?> encontrada<?= $total !== 1 ? 's' : '' ?></span>
    <div class="toolbar-right">
      <!-- Filtro por nível -->
      <div class="nivel-tabs">
        <a class="tab <?= $nivel === '' ? 'tab-all' : '' ?>"
           href="aulas.php?ordem=<?= esc($ordem) ?>"
           style="<?= $nivel === '' ? 'border-color:var(--teal);color:var(--teal);background:#e8f9f9' : '' ?>">Todos</a>
        <?php foreach (['iniciante','intermediario','avancado'] as $n): ?>
          <a class="tab <?= $nivel === $n ? 'active-'.$n : '' ?>"
             href="aulas.php?nivel=<?= $n ?>&ordem=<?= esc($ordem) ?>">
            <?= $nivel_label[$n] ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Ordenação -->
      <select class="select-ordem"
              onchange="window.location='aulas.php?<?= $nivel ? 'nivel='.esc($nivel).'&' : '' ?>ordem='+this.value">
        <option value="dificuldade" <?= $ordem==='dificuldade'?'selected':'' ?>>Dificuldade</option>
        <option value="preco_asc"   <?= $ordem==='preco_asc'  ?'selected':'' ?>>Menor Preço</option>
        <option value="preco_desc"  <?= $ordem==='preco_desc' ?'selected':'' ?>>Maior Preço</option>
        <option value="duracao"     <?= $ordem==='duracao'    ?'selected':'' ?>>Duração</option>
      </select>
    </div>
  </div>

  <!-- Cards -->
  <div class="cards-grid">
    <?php if (empty($aulas)): ?>
      <div class="no-results">Nenhuma aula encontrada.</div>
    <?php else: ?>
      <?php foreach ($aulas as $a):
        $cor   = $nivel_color[$a['nivel']] ?? '#2ec4c4';
        $label = $nivel_label[$a['nivel']] ?? $a['nivel'];
        $itens = array_filter(array_map('trim', explode(',', $a['itens_inclusos'] ?? '')));
      ?>
      <div class="aula-card">
        <span class="badge" style="border-color:<?= $cor ?>;color:<?= $cor ?>"><?= esc($label) ?></span>
        <h3 class="card-titulo"><?= esc($a['titulo']) ?></h3>
        <p class="card-desc"><?= esc($a['descricao']) ?></p>

        <?php if (!empty($itens)): ?>
        <ul class="card-lista">
          <?php foreach ($itens as $item): ?>
            <li><?= esc($item) ?></li>
          <?php endforeach; ?>
          <li>Turma máx. <?= (int)$a['max_alunos'] ?> pessoa<?= $a['max_alunos'] > 1 ? 's' : '' ?></li>
          <li>Duração: <?= (int)$a['duracao_minutos'] >= 60
              ? floor($a['duracao_minutos']/60).'h'.($a['duracao_minutos']%60 ? str_pad($a['duracao_minutos']%60,2,'0',STR_PAD_LEFT) : '')
              : $a['duracao_minutos'].'min' ?></li>
        </ul>
        <?php endif; ?>

        <div class="card-footer">
          <div class="card-preco"><?= brl($a['preco_individual']) ?> <span>/ pessoa</span></div>
          <?php if (!empty($a['preco_pacote5'])): ?>
            <div class="card-pacote">Pacote 5 aulas: <?= brl($a['preco_pacote5']) ?></div>
          <?php endif; ?>
          <a class="btn-agendar" href="agendamento.php?aula_id=<?= (int)$a['id'] ?>">agendar</a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>
</body>
</html>