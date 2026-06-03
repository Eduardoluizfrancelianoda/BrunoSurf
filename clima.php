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
    <link rel="stylesheet" href="css/clima.css">
</head>

<body>

    <!-- NAV -->
    <nav>
        <a href="index.php" class="logo">Bruno <span>surf</span></a>
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
                            <span class="stat-label vento">Vento</span>
                            <span class="stat-val"><?= esc($weather['vento']) ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label umidade">Umidade</span>
                            <span class="stat-val"><?= esc($weather['umidade']) ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label pressao">Pressão</span>
                            <span class="stat-val"><?= esc($weather['pressao']) ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label arco">Arco-íris</span>
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
