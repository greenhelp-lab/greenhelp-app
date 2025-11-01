<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenHelp Home</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/home/home_admin.css">
</head>

<body class="page-admin">

  <!-- <?php phpinfo(); ?> -->
  <?php include BASE_PATH . '/src/pages/partials/header.php'; ?> <!-- 'include' header php for code optimization -->
  <main class="container">
    <!-- TÍTULO E TOGGLER -->
    <div class="header-row">
      <h1 class="title">Home</h1>
      <div class="toggles">
        <button class="pill active">Tarefas</button>
        <button class="pill">Empresas</button>
      </div>
    </div>

    <!-- KPIS GLOBAIS -->
    <section class="kpis">
      <h2>KPIs Globais</h2>

      <div class="kpi-card">
        <div class="kpi-label">Faturamento neste Ano</div>
        <div class="kpi-value">R$5.560.000</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Faturamento Último Mês</div>
        <div class="kpi-value">R$430.000</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Faturamento Último Semestre</div>
        <div class="kpi-value">R$2.830.000</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Média Avaliações dos Clientes</div>
        <div class="kpi-value large">4,8</div>
      </div>
    </section>

    <!-- TAREFAS -->
    <section class="tarefas">
      <h2>Tarefas</h2>

      <div class="kpi-card">
        <div class="kpi-label">Total de Tarefas Para Realizar</div>
        <div class="kpi-value">340</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Total de Tarefas Em Progresso</div>
        <div class="kpi-value positive">+85</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Total de Tarefas para Revisão</div>
        <div class="kpi-value">74</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Tarefas com Prazo para Esta Semana</div>
        <div class="kpi-value">135</div>
      </div>

      <button class="btn-criar">Criar Tarefa</button>
    </section>

    <!-- BARRA DE PESQUISA + LISTA / FILTROS -->
    <section class="busca-lista">
      <div class="search-wrap">
        <input type="text" placeholder="Pesquisar...">
        <button class="search-btn">🔍</button>
      </div>

      <div class="small-tabs">
        <button class="small-pill active">Lista</button>
        <button class="small-pill">Filtros</button>
      </div>

      <ul class="company-list">
        <li><span class="company">Nova Geek Informática</span><span class="tag epp">EPP</span></li>
        <li><span class="company">Hospital Albert Einstein</span><span class="tag medio">Médio Porte</span></li>
        <li><span class="company">Guardião da Infância</span><span class="tag ong">ONG</span></li>
        <li><span class="company">Friends Forever</span><span class="tag ong">ONG</span></li>
        <li><span class="company">Hegel Development</span><span class="tag epp">EPP</span></li>
        <li><span class="company">Playtime Corporation</span><span class="tag grande">Grande Porte</span></li>
        <li><span class="company">Automóveis Lechamp</span><span class="tag grande">Grande Porte</span></li>
        <li><span class="company">Melo Bank</span><span class="tag grande">Grande Porte</span></li>
        <li><span class="company">Kero Productions</span><span class="tag me">ME</span></li>
      </ul>
    </section>

    <!-- EMPRESAS (resumido) -->
    <section class="empresas">
      <h2>Empresas</h2>
      <div class="kpi-card">
        <div class="kpi-label">Total de Empresas na Plataforma</div>
        <div class="kpi-value">340</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Novas Empresas neste Ano</div>
        <div class="kpi-value positive">+85</div>
      </div>
    </section>
    <?php include BASE_PATH . '/src/pages/partials/footer.php'; ?> <!-- 'include' footer php for code optimization -->
</body>

</html>