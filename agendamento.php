<?php
require_once 'db.php';

// ── Busca aulas disponíveis para o select ────────────────────
try {
    $pdo   = conectar();
    $aulas = $pdo->query(
        "SELECT id, titulo, nivel, preco_individual, preco_pacote5, duracao_minutos
         FROM aulas WHERE disponivel = TRUE
         ORDER BY FIELD(nivel,'iniciante','intermediario','avancado')"
    )->fetchAll();
    $erro_db = null;
} catch (PDOException $e) {
    $aulas   = [];
    $erro_db = htmlspecialchars($e->getMessage());
}

// ── Pré-seleciona aula se vier da página de aulas ────────────
$aula_presel = (int)($_GET['aula_id'] ?? 0);

// ── Processamento do formulário (POST) ──────────────────────
$sucesso  = false;
$erros    = [];
$form     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza
    $form = [
        'nome_completo'    => trim($_POST['nome_completo']    ?? ''),
        'whatsapp'         => trim($_POST['whatsapp']         ?? ''),
        'aula_id'          => (int)($_POST['aula_id']         ?? 0),
        'data_preferida'   => trim($_POST['data_preferida']   ?? ''),
        'horario'          => trim($_POST['horario']          ?? ''),
        'num_pessoas'      => max(1,(int)($_POST['num_pessoas'] ?? 1)),
        'metodo_pagamento' => trim($_POST['metodo_pagamento'] ?? ''),
        'observacoes'      => trim($_POST['observacoes']      ?? ''),
    ];

    // Validações
    if (strlen($form['nome_completo']) < 3)        $erros[] = 'Nome completo é obrigatório.';
    if (!preg_match('/\d{8,}/', preg_replace('/\D/','',$form['whatsapp'])))
                                                    $erros[] = 'WhatsApp inválido.';
    if ($form['aula_id'] <= 0)                      $erros[] = 'Selecione um tipo de aula.';
    if (empty($form['data_preferida']))             $erros[] = 'Data preferida é obrigatória.';
    elseif (strtotime($form['data_preferida']) < strtotime('today'))
                                                    $erros[] = 'A data não pode ser no passado.';
    if (empty($form['horario']))                    $erros[] = 'Horário é obrigatório.';
    if (empty($form['metodo_pagamento']))           $erros[] = 'Selecione um método de pagamento.';

    if (empty($erros)) {
        try {
            // Busca dados da aula para calcular valor
            $stmt_aula = $pdo->prepare("SELECT * FROM aulas WHERE id = :id AND disponivel = TRUE");
            $stmt_aula->execute([':id' => $form['aula_id']]);
            $aula_sel = $stmt_aula->fetch();

            if (!$aula_sel) {
                $erros[] = 'Aula não encontrada.';
            } else {
                // Verifica vagas disponíveis
                $stmt_vagas = $pdo->prepare(
                    "SELECT COALESCE(SUM(num_pessoas),0) FROM agendamentos
                     WHERE aula_id = :aula_id AND data_preferida = :data
                     AND horario = :horario AND status != 'cancelado'"
                );
                $stmt_vagas->execute([
                    ':aula_id' => $form['aula_id'],
                    ':data'    => $form['data_preferida'],
                    ':horario' => $form['horario'],
                ]);
                $vagas_ocupadas = (int)$stmt_vagas->fetchColumn();
                $vagas_livres   = $aula_sel['max_alunos'] - $vagas_ocupadas;

                if ($form['num_pessoas'] > $vagas_livres) {
                    $erros[] = "Vagas insuficientes neste horário. Disponíveis: $vagas_livres.";
                } else {
                    // Calcula valor total
                    $valor_total = $aula_sel['preco_individual'] * $form['num_pessoas'];

                    // Insere agendamento
                    $stmt_insert = $pdo->prepare(
                        "INSERT INTO agendamentos
                         (nome_completo, whatsapp, aula_id, data_preferida, horario,
                          num_pessoas, metodo_pagamento, observacoes, valor_total)
                         VALUES
                         (:nome, :whatsapp, :aula_id, :data, :horario,
                          :num_pessoas, :metodo, :obs, :valor)"
                    );
                    $stmt_insert->execute([
                        ':nome'        => $form['nome_completo'],
                        ':whatsapp'    => $form['whatsapp'],
                        ':aula_id'     => $form['aula_id'],
                        ':data'        => $form['data_preferida'],
                        ':horario'     => $form['horario'],
                        ':num_pessoas' => $form['num_pessoas'],
                        ':metodo'      => $form['metodo_pagamento'],
                        ':obs'         => $form['observacoes'] ?: null,
                        ':valor'       => $valor_total,
                    ]);

                    $sucesso = true;
                    $form    = []; // limpa form
                }
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao salvar agendamento: ' . htmlspecialchars($e->getMessage());
        }
    }
}

function esc(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function brl(float $v): string  { return 'R$' . number_format($v, 0, ',', '.'); }
function fval(string $k, array $f): string { return esc($f[$k] ?? ''); }

$nivel_label = ['iniciante'=>'Iniciante','intermediario'=>'Intermediário','avancado'=>'Avançado'];
$metodos = [
    'visa'    => ['label'=>'Visa',    'cor'=>'#1a1f71'],
    'elo'     => ['label'=>'Elo',     'cor'=>'#ffcb05'],
    'pix'     => ['label'=>'Pix',     'cor'=>'#32bcad'],
    'dinners' => ['label'=>'Diners',  'cor'=>'#004a97'],
    'mastercard'=>['label'=>'Master', 'cor'=>'#eb001b'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agendamento – Bruno Surf</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/agendamento.css">
</head>
<body>

<nav>
  <a href="index.html" class="logo">Bruno <span>surf</span></a>
  <ul class="nav-links">
    <li><a href="equipamentos.php">Equipamentos</a></li>
    <li><a href="escola.php" class="active">Aulas</a></li>
    <li><a href="galeria.php">Galeria</a></li>
    <li><a href="clima.php">Clima</a></li>
  </ul>
</nav>

<div class="layout">

  <!-- COLUNA ESQUERDA -->
  <aside class="info-card">
    <p class="info-eyebrow">Reservas Online</p>
    <h2 class="info-title">Reserve sua <em>experiência</em></h2>
    <p class="info-desc">Garante sua vaga com antecedência.<br>Respondemos em até 2 horas pelo WhatsApp.</p>

    <div class="contact-list">
      <div class="contact-item">
        <div class="contact-icon">📱</div>
        <div class="contact-info">
          <strong>Whatsapp</strong>
          <span>11 99999-9999 — resposta rápida</span>
        </div>
      </div>
      <div class="contact-item">
        <div class="contact-icon">📸</div>
        <div class="contact-info">
          <strong>Instagram</strong>
          <span>@brunosurf — fotos e novidades</span>
        </div>
      </div>
      <div class="contact-item">
        <div class="contact-icon">📍</div>
        <div class="contact-info">
          <strong>Localização</strong>
          <span>Praia Central, barraca azul nº 12</span>
        </div>
      </div>
    </div>
  </aside>

  <!-- COLUNA DIREITA -->
  <section class="form-card">

    <?php if ($sucesso): ?>
      <!-- SUCESSO -->
      <div class="sucesso-box">
        <div class="sucesso-icon">🏄</div>
        <h3>Agendamento enviado!</h3>
        <p>Recebemos sua solicitação.<br>Bruno vai confirmar sua vaga em até 2h pelo WhatsApp.</p>
        <a href="escola.php" class="btn-voltar">Ver mais aulas</a>
      </div>

    <?php else: ?>
      <h2 class="form-title">formulário de<br>agendamento</h2>

      <?php if ($erro_db): ?>
        <div class="erros-box"><p>⚠️ Erro no banco de dados: <?= $erro_db ?></p></div>
      <?php endif; ?>

      <?php if (!empty($erros)): ?>
        <div class="erros-box">
          <?php foreach ($erros as $e): ?>
            <p>• <?= esc($e) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="agendamento.php">

        <!-- Nome + WhatsApp -->
        <div class="form-row cols-2">
          <div class="form-group">
            <label for="nome_completo">Nome completo</label>
            <input type="text" id="nome_completo" name="nome_completo"
                   placeholder="Seu Nome" required
                   value="<?= fval('nome_completo',$form) ?>">
          </div>
          <div class="form-group">
            <label for="whatsapp">Whatsapp</label>
            <input type="tel" id="whatsapp" name="whatsapp"
                   placeholder="11 99999-9999" required
                   value="<?= fval('whatsapp',$form) ?>">
          </div>
        </div>

        <!-- Tipo de serviço (aula) -->
        <div class="form-row">
          <div class="form-group">
            <label for="aula_id">Tipo de serviço</label>
            <select id="aula_id" name="aula_id" class="select-aula" required onchange="atualizarVagas()">
              <option value="">Selecione uma aula…</option>
              <?php foreach ($aulas as $a): ?>
                <?php $sel = (($form['aula_id'] ?? $aula_presel) == $a['id']) ? 'selected' : ''; ?>
                <option value="<?= (int)$a['id'] ?>" <?= $sel ?>
                  data-preco="<?= (int)$a['preco_individual'] ?>">
                  aula de surf — <?= esc($nivel_label[$a['nivel']] ?? $a['nivel']) ?>
                  · <?= brl($a['preco_individual']) ?>/pessoa
                  <?= $a['duracao_minutos'] >= 60 ? '· '.floor($a['duracao_minutos']/60).'h' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Data + Horário + Pessoas -->
        <div class="form-row cols-3">
          <div class="form-group">
            <label for="data_preferida">Data preferida</label>
            <input type="date" id="data_preferida" name="data_preferida" required
                   min="<?= date('Y-m-d') ?>"
                   value="<?= fval('data_preferida',$form) ?>"
                   onchange="atualizarVagas()">
          </div>
          <div class="form-group">
            <label for="horario">Horário</label>
            <input type="time" id="horario" name="horario" required
                   min="06:00" max="18:00" step="1800"
                   value="<?= fval('horario',$form) ?: '07:00' ?>"
                   onchange="atualizarVagas()">
          </div>
          <div class="form-group">
            <label for="num_pessoas">Pessoas</label>
            <input type="number" id="num_pessoas" name="num_pessoas"
                   min="1" max="5" value="<?= (int)($form['num_pessoas'] ?? 1) ?>">
          </div>
        </div>
        <p class="vagas-info" id="vagasInfo"></p>

        <!-- Método de pagamento -->
        <div style="margin-bottom:16px">
          <p class="metodos-label">Método de Pagamento</p>
          <div class="metodos-grid">
            <?php foreach ($metodos as $key => $m): ?>
              <?php $checked = (($form['metodo_pagamento'] ?? '') === $key) ? 'checked' : ''; ?>
              <input class="metodo-opt" type="radio" name="metodo_pagamento"
                     id="met_<?= $key ?>" value="<?= $key ?>" <?= $checked ?>>
              <label for="met_<?= $key ?>">
                <span class="metodo-dot" style="background:<?= $m['cor'] ?>"></span>
                <?= $m['label'] ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Observações -->
        <div class="form-group">
          <label for="observacoes">Observações (opcional)</label>
          <textarea id="observacoes" name="observacoes"
                    placeholder="nível de experiência, quantidade de pessoas, pedidos especiais..."><?= fval('observacoes',$form) ?></textarea>
        </div>

        <button type="submit" class="btn-confirmar">confirmar</button>
      </form>
    <?php endif; ?>

  </section>
</div>

<script>
// Verifica vagas disponíveis via fetch (AJAX) sem recarregar a página
async function atualizarVagas() {
  const aulaId = document.getElementById('aula_id').value;
  const data   = document.getElementById('data_preferida').value;
  const hora   = document.getElementById('horario').value;
  const info   = document.getElementById('vagasInfo');
  const maxEl  = document.getElementById('aula_id').selectedOptions[0];

  if (!aulaId || !data || !hora) { info.textContent = ''; return; }

  const max = parseInt(maxEl?.dataset?.max || '5');

  try {
    const res  = await fetch(`vagas.php?aula_id=${aulaId}&data=${data}&horario=${hora}`);
    const json = await res.json();
    const livres = max - (json.ocupadas || 0);
    info.style.color = livres > 0 ? '#1a9999' : '#c0392b';
    info.textContent = livres > 0
      ? `✓ ${livres} vaga${livres > 1 ? 's' : ''} disponível${livres > 1 ? 'is' : ''} neste horário`
      : '✗ Sem vagas neste horário — escolha outra data ou horário';

    // Atualiza máximo do campo pessoas
    document.getElementById('num_pessoas').max = livres;
  } catch(e) {
    info.textContent = '';
  }
}
</script>

</body>
</html>