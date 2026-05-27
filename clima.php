<?php
$cidades = [
    'São Paulo, SP' => [
        'icon' => '🌧️',
        'temp' => '17°',
        'vento' => '↑ ESE · 9km/h',
        'umidade' => '↑ 72%',
        'pressao' => '1021hPa',
        'arco' => 'Baixa probabilidade de formação',
        'raios' => '0 na última hora',
        'queimadas' => '0 na última hora',
        'vento_qualidade' => 'Ar limpo e qualidade elevada ideal para surfar.',
        'uv' => 'Baixo · Radiação baixa e exposição segura.',
        'gripe' => 'Baixo · Tempo estável e temperaturas amenas.',
    ],
    'Rio de Janeiro, RJ' => [
        'icon' => '☀️',
        'temp' => '26°',
        'vento' => '↑ ENE · 14km/h',
        'umidade' => '↑ 68%',
        'pressao' => '1018hPa',
        'arco' => 'Chance média de formação',
        'raios' => '1 na última hora',
        'queimadas' => '0 na última hora',
        'vento_qualidade' => 'Bom vento para aulas e treinos.',
        'uv' => 'Alto · Proteção solar recomendada.',
        'gripe' => 'Moderado · Temperatura quente e úmida.',
    ],
    'Florianópolis, SC' => [
        'icon' => '⛅',
        'temp' => '21°',
        'vento' => '↑ SSW · 11km/h',
        'umidade' => '↑ 75%',
        'pressao' => '1019hPa',
        'arco' => 'Baixa probabilidade de formação',
        'raios' => '0 na última hora',
        'queimadas' => '0 na última hora',
        'vento_qualidade' => 'Vento estável, bom para surf e windsurf.',
        'uv' => 'Médio · Use proteção nos horários de pico.',
        'gripe' => 'Baixo · Clima ameno e confortável.',
    ],
    'Salvador, BA' => [
        'icon' => '☀️',
        'temp' => '28°',
        'vento' => '↑ NE · 16km/h',
        'umidade' => '↑ 70%',
        'pressao' => '1015hPa',
        'arco' => 'Média probabilidade de formação',
        'raios' => '0 na última hora',
        'queimadas' => '0 na última hora',
        'vento_qualidade' => 'Vento forte e constante, ideal para esportes de praia.',
        'uv' => 'Muito alto · Protetor solar obrigatório.',
        'gripe' => 'Baixo · Tempo quente e seco.',
    ],
];

$selectedCity = $_GET['cidade'] ?? 'São Paulo, SP';
if (!array_key_exists($selectedCity, $cidades)) {
    $selectedCity = 'São Paulo, SP';
}
$weather = $cidades[$selectedCity];

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
    <title>Clima – Bruno Surf</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/equipamentos.css">
    <style>
      .clima-section {
        background: #f9fbfc;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 22px 55px rgba(0,0,0,0.05);
      }
      .clima-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
      }
      .clima-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
      }
      .clima-card h4 {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--teal-dark);
        margin-bottom: 18px;
      }
      .temp-display {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 24px;
      }
      .temp-icon {
        font-size: 3rem;
      }
      .temp-num {
        font-size: 3rem;
        font-weight: 700;
        color: var(--dark);
      }
      .clima-stats {
        display: grid;
        gap: 12px;
      }
      .stat-row {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--mid);
        font-size: 0.85rem;
      }
      .stat-label {
        min-width: 90px;
        padding: 7px 10px;
        border-radius: 10px;
        text-transform: uppercase;
        font-size: 0.72rem;
        font-weight: 700;
        background: #eef7f7;
        color: var(--teal-dark);
        text-align: center;
      }
      .stat-val {
        color: var(--mid);
      }
      .registros {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
      }
      .registro {
        flex: 1 1 140px;
        min-width: 140px;
        background: var(--light-bg);
        border-radius: 14px;
        padding: 18px 16px;
        text-align: center;
      }
      .registro .icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        font-size: 0.85rem;
        margin: 0 auto 8px;
      }
      .registro .icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
      }
      .registro .label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 4px;
      }
      .registro .val {
        font-size: 0.83rem;
        color: var(--mid);
      }
      .condicoes {
        display: grid;
        gap: 14px;
      }
      .cond-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px;
        background: var(--light-bg);
        border-radius: 14px;
      }
      .cond-left {
        display: flex;
        align-items: center;
        gap: 14px;
      }
      .cond-left .icon {
        font-size: 1.15rem;
      }
      .cond-left .icon img {
        width: 22px;
        height: 22px;
        object-fit: contain;
      }
      .cond-info h5 {
        font-size: 0.88rem;
        margin-bottom: 4px;
        font-weight: 700;
      }
      .cond-info p {
        margin: 0;
        color: var(--mid);
        font-size: 0.82rem;
      }
      .dot {
        flex-shrink: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--teal);
      }
      .center-btn-teal {
        text-align: center;
        margin-top: 32px;
      }
      .btn-teal {
        padding: 12px 34px;
        background: var(--orange);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
      }
      .city-filter {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
        flex-wrap: wrap;
      }
      .city-filter label {
        font-size: 0.86rem;
        font-weight: 700;
        color: var(--mid);
      }
      .city-filter select {
        min-width: 220px;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
        color: var(--dark);
        font-size: 0.95rem;
      }
      @media (max-width: 780px) {
        .clima-grid { grid-template-columns: 1fr; }
        .page { padding: 36px 18px 60px; }
      }
    </style>
</head>

<body>

    <!-- NAV -->
    <nav>
        <a href="index.html" class="logo">Bruno <span>surf</span></a>
        <ul class="nav-links">
            <li><a href="equipamentos.php">Equipamentos</a></li>
            <li><a href="escola.php">Aulas</a></li>
            <li><a href="galeria.php">Galeria</a></li>
            <li><a href="clima.php" class="active">Clima</a></li>
        </ul>
    </nav>

    <div class="page">
        <p class="page-eyebrow">Previsão do Tempo</p>
        <h1 class="page-title">Clima <em>para sua próxima sessão</em></h1>
        <p class="page-desc">Confira dados de vento, umidade, pressão, arco-íris e condições especiais antes de ir para a praia.</p>

        <section class="clima-section">
            <form method="GET" action="clima.php" class="city-filter">
                <label for="cidade">Selecione a cidade:</label>
                <select name="cidade" id="cidade" onchange="this.form.submit()">
                    <?php foreach (array_keys($cidades) as $cidadeNome): ?>
                        <option value="<?= esc($cidadeNome) ?>" <?= $cidadeNome === $selectedCity ? 'selected' : '' ?>><?= esc($cidadeNome) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="clima-grid">
                <div class="clima-card">
                    <h4>Tempo agora em <?= esc($selectedCity) ?></h4>
                    <div class="temp-display">
                        <span class="temp-icon"><?= esc($weather['icon']) ?></span>
                        <span class="temp-num"><?= esc($weather['temp']) ?></span>
                    </div>
                    <div class="clima-stats">
                        <div class="stat-row">
                            <span class="stat-label">Vento</span>
                            <span class="stat-val"><?= esc($weather['vento']) ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Umidade</span>
                            <span class="stat-val"><?= esc($weather['umidade']) ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Pressão</span>
                            <span class="stat-val"><?= esc($weather['pressao']) ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Arco-íris</span>
                            <span class="stat-val"><?= esc($weather['arco']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="clima-card">
                    <div class="registros">
                        <div class="registro">
                            <span class="icon"><img src="icons/transferir_(1).png" alt=""></span>
                            <span class="label">Raios</span>
                            <span class="val"><?= esc($weather['raios']) ?></span>
                        </div>
                        <div class="registro">
                            <span class="icon"><img src="icons/transferir_(2).png" alt=""></span>
                            <span class="label">Queimadas</span>
                            <span class="val"><?= esc($weather['queimadas']) ?></span>
                        </div>
                    </div>
                    <div class="condicoes">
                        <div class="cond-row">
                            <div class="cond-left">
                                <span class="icon"><img src="icons/wind.png" alt=""></span>
                                <div class="cond-info">
                                    <h5>Qualidade do Vento</h5>
                                    <p><?= esc($weather['vento_qualidade']) ?></p>
                                </div>
                            </div>
                            <div class="dot"></div>
                        </div>
                        <div class="cond-row">
                            <div class="cond-left">
                                <span class="icon"><img src="icons/sun.png" alt=""></span>
                                <div class="cond-info">
                                    <h5>Índice UV</h5>
                                    <p><?= esc($weather['uv']) ?></p>
                                </div>
                            </div>
                            <div class="dot"></div>
                        </div>
                        <div class="cond-row">
                            <div class="cond-left">
                                <span class="icon"><img src="icons/germ.png" alt=""></span>
                                <div class="cond-info">
                                    <h5>Gripe e Resfriado</h5>
                                    <p><?= esc($weather['gripe']) ?></p>
                                </div>
                            </div>
                            <div class="dot"></div>
                        </div>
                    </div>
                </div>
            </div>

           
        </section>
    </div>

</body>

</html>
