<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Serviços</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/painel_admin/painel_admin.css">
</head>

<body>
  <?php include BASE_PATH . '/src/pages/partials/header.php'; ?>
  <h1 class="shop-title">Painel de Admin</h1>
  <div class="shop-container">
    <p>Bem-vindo ao painel de administração. Aqui você pode gerenciar usuários, serviços e configurações do sistema.</p>
  </div>

  <!-- Tab com atalhos/ações rápidas -->
  <div class="actions-tab" aria-label="Ações do administrador">
    <h3>Ações rápidas</h3>
    <div>
      <button class="action-btn">Criar serviço</button>
      <button class="action-btn">Novo usuário</button>
      <button class="action-btn">Exportar registros</button>
    </div>
  </div>

  <!-- Dashboard principal -->
  <main class="dashboard" role="main" aria-labelledby="dashboard-heading">
    <h2>Visão geral</h2>

    <!-- Cards de métricas -->
    <div class="dash-cards">
      <div class="dash-card">
        <h4>Usuários</h4>
        <span class="value">1.234</span>
        <span class="meta">Usuários ativos na última semana</span>
      </div>
      <div class="dash-card">
        <h4>Serviços</h4>
        <span class="value">87</span>
        <span class="meta">Serviços cadastrados</span>
      </div>
      <div class="dash-card">
        <h4>Receita (Mês)</h4>
        <span class="value">R$ 12.430</span>
        <span class="meta">Estimativa mensal</span>
      </div>
      <div class="dash-card">
        <h4>Lucro Total (Último Mês)</h4>
        <span id="m-revenue" class="value" aria-live="polite">R$ --</span>
        <span class="meta">Comparado ao mês anterior</span>
      </div>
      <div class="dash-card">
        <h4>Serviço Mais Vendido</h4>
        <span id="m-top-service" class="value">-</span>
        <span class="meta">Top produto na loja</span>
      </div>
      <div class="dash-card">
        <h4>Área Mais Popular</h4>
        <span id="m-top-area" class="value">-</span>
        <span class="meta">Áreas com maior demanda</span>
      </div>
      <div class="dash-card">
        <h4>Satisfação Média</h4>
        <span id="m-sat" class="value">-</span>
        <span class="meta">Avaliações dos clientes</span>
      </div>
      <div class="dash-card">
        <h4>Vendas Mensais</h4>
        <span>Último mês: <strong id="sales-last">R$ --</strong></span>
        <span>Tendência: <small id="sales-trend">Positiva</small></span>
        <!-- strong small como fáceis identificadores para substituição dinâmica dos valores de ùltimo Mês e Tendência -->
      </div>
    </div>

  </main>

  <!-- Seção de registros de usuários -->
  <section class="records">
    <h2 id="records-heading">Usuários da Plataforma</h2>
    <p class="records-intro">Busque e visualize usuários. Clique em um usuário para ver detalhes e ações administrativas.</p>

    <div class="records-search" aria-label="Busca de usuários">
      <input id="user-search" class="search-input" placeholder="Buscar por nome, e-mail ou ID" />
      <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M22.0171 19.3935C23.8326 16.9158 24.6457 13.8439 24.2938 10.7923C23.942 7.74072 22.451 4.93455 20.1192 2.9352C17.7875 0.93585 14.7869 -0.109228 11.7178 0.00904609C8.64871 0.12732 5.73744 1.40022 3.56643 3.5731C1.39542 5.74597 0.124773 8.65856 0.00871192 11.7282C-0.10735 14.7978 0.939728 17.798 2.94046 20.1287C4.9412 22.4593 7.74804 23.9485 10.7994 24.2983C13.8508 24.648 16.9218 23.8326 19.3978 22.015H19.396C19.451 22.0901 19.5122 22.1619 19.5797 22.2307L26.7982 29.4502C27.1497 29.8021 27.6267 29.9998 28.124 30C28.6214 30.0002 29.0985 29.8027 29.4503 29.4511C29.8021 29.0995 29.9998 28.6225 30 28.1251C30.0002 27.6277 29.8028 27.1505 29.4512 26.7987L22.2327 19.5792C22.1657 19.5113 22.0936 19.4505 22.0171 19.3935ZM22.5008 12.1853C22.5008 13.5397 22.2341 14.8808 21.7159 16.1321C21.1976 17.3834 20.4381 18.5204 19.4805 19.4781C18.5229 20.4358 17.3861 21.1955 16.135 21.7138C14.8839 22.2321 13.5429 22.4988 12.1887 22.4988C10.8345 22.4988 9.49357 22.2321 8.24245 21.7138C6.99133 21.1955 5.85453 20.4358 4.89696 19.4781C3.93939 18.5204 3.17981 17.3834 2.66158 16.1321C2.14334 14.8808 1.87661 13.5397 1.87661 12.1853C1.87661 9.44996 2.96306 6.82666 4.89696 4.89249C6.83086 2.95832 9.45378 1.87172 12.1887 1.87172C14.9237 1.87172 17.5466 2.95832 19.4805 4.89249C21.4144 6.82666 22.5008 9.44996 22.5008 12.1853Z" fill="currentColor" />
      </svg>
    </div>

    <div class="records-actions">
      <div>
        <button class="btn-import">Importar CSV</button>
      </div>
      <div>
        <button class="btn-filtros">Filtrar</button>
      </div>
    </div>

    <div class="records-box" id="users-list" aria-live="polite">
      <!-- Registros de exemplo, criando divs para cada usuário de forma dinâmica -->
      <div class="record" data-user-id="1">
        <div>
          <div class="record-title">Ana Silva</div>
          <div class="record-date">ana@exemplo.com • Administrador</div>
        </div>
        <div>
          <button class="btn-ver" onclick="window.location.href='<?php echo BASE_URL; ?>/admin/usuarios.php?id=1'">Ver</button>
        </div>
      </div>
      <div class="record" data-user-id="2">
        <div>
          <div class="record-title">João Pereira</div>
          <div class="record-date">joao@exemplo.com • Cliente</div>
        </div>
        <div>
          <button class="btn-ver" onclick="window.location.href='<?php echo BASE_URL; ?>/admin/usuarios.php?id=2'">Ver</button>
        </div>
      </div>
      <!-- mais registros -->
    </div>
  </section>

  <?php include BASE_PATH . '/src/pages/partials/footer.php'; ?>
</body>

</html>