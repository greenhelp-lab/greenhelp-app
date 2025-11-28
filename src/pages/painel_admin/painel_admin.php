<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

session_start();
$is_admin = isset($_SESSION['papel']) && $_SESSION['papel'] === 'admin';
$is_admin = false ?? die;


$totClientes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE papel = 'cliente'")->fetchColumn();
$totAdmins   = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE papel = 'admin'")->fetchColumn();
$totServicos = $pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
$totFaturamento = $pdo->query("SELECT SUM(valor_total) FROM servicos_andamento WHERE status = 'concluido'")->fetchColumn();
$totServicosAndamento = $pdo->query("SELECT COUNT(*) FROM servicos_andamento WHERE status = 'em andamento'")->fetchColumn();
$totServicosConcluidos = $pdo->query("SELECT COUNT(*) FROM servicos_andamento WHERE status = 'concluido'")->fetchColumn();
$totServicosCancelados = $pdo->query("SELECT COUNT(*) FROM servicos_andamento WHERE status = 'cancelado'")->fetchColumn();
$totServicosPendentes = $pdo->query("SELECT COUNT(*) FROM servicos_andamento WHERE status = 'pendente'")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Admin</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/painel_admin/painel_admin.css">
</head>

<body>
  <?php include_once BASE_PATH . '/src/pages/partials/header_admin.php'; ?>

  <h1 class="shop-title">Painel Admin</h1>
  <div class="shop-container">
    <p>Bem-vindo ao painel de administração. Aqui você pode gerenciar usuários, serviços e configurações do sistema.</p>
  </div>

  <!-- Tab com atalhos/ações rápidas -->
  <div class="actions-tab" aria-label="Ações do administrador">
    <h3>Ações rápidas</h3>
    <div>
      <button class="action-btn" id="create-service-btn">Criar serviço</button>
      <button class="action-btn" id="create-admin-btn">Novo admin</button>
      <button class="action-btn" id="create-client-btn">Novo cliente</button>
    </div>
  </div>

  <!-- Dashboard principal -->
  <main class="dashboard">
    <h2>Visão geral</h2>

    <!-- Cards de métricas -->
    <div class="dash-cards">
      <div class="dash-card">
        <h4>Clientes</h4>
        <span class="value"><?php echo $totClientes; ?></span>
        <span class="meta">Clientes na plataforma</span>
      </div>
      <div class="dash-card">
        <h4>Admins</h4>
        <span class="value"><?php echo $totAdmins; ?></span>
        <span class="meta">Admins na plataforma</span>
      </div>
      <div class="dash-card">
        <h4>Serviços</h4>
        <span class="value"><?php echo $totServicos; ?></span>
        <span class="meta">Serviços cadastrados</span>
      </div>
      <div class="dash-card">
        <h4>Faturamento</h4>
        <span class="value"><?php echo "R$ " . number_format($totFaturamento, 2, ',', '.'); ?></span>
        <span class="meta">decorrente de serviços concluidos</span>
      </div>
      <div class="dash-card">
        <h4>Serviços Pendentes</h4>
        <span class="value"><?php echo $totServicosPendentes; ?></span>
        <span class="meta">Serviços pendentes</span>
      </div>
      <div class="dash-card">
        <h4>Serviços em Andamento</h4>
        <span class="value"><?php echo $totServicosAndamento; ?></span>
        <span class="meta">Serviços em andamento</span>
      </div>
      <div class="dash-card">
        <h4>Serviços Concluidos</h4>
        <span class="value"><?php echo $totServicosConcluidos; ?></span>
        <span class="meta">Serviços concluídos</span>
      </div>
      <div class="dash-card">
        <h4>Serviços Cancelados</h4>
        <span class="value"><?php echo $totServicosCancelados; ?></span>
        <span class="meta">Serviços cancelados</span>
      </div>
    </div>

  </main>

  <!-- Seção de registros de usuários -->
  <section class="records">
    <h2 id="records-heading">Clientes da Plataforma</h2>
    <p class="records-intro">Busque e visualize clientes. Clique em um cliente para ver detalhes.</p>

    <div class="records-search" aria-label="Busca de usuários">
      <input id="client-search" class="search-input" placeholder="Buscar por nome, e-mail ou empresa" />
      <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M22.0171 19.3935C23.8326 16.9158 24.6457 13.8439 24.2938 10.7923C23.942 7.74072 22.451 4.93455 20.1192 2.9352C17.7875 0.93585 14.7869 -0.109228 11.7178 0.00904609C8.64871 0.12732 5.73744 1.40022 3.56643 3.5731C1.39542 5.74597 0.124773 8.65856 0.00871192 11.7282C-0.10735 14.7978 0.939728 17.798 2.94046 20.1287C4.9412 22.4593 7.74804 23.9485 10.7994 24.2983C13.8508 24.648 16.9218 23.8326 19.3978 22.015H19.396C19.451 22.0901 19.5122 22.1619 19.5797 22.2307L26.7982 29.4502C27.1497 29.8021 27.6267 29.9998 28.124 30C28.6214 30.0002 29.0985 29.8027 29.4503 29.4511C29.8021 29.0995 29.9998 28.6225 30 28.1251C30.0002 27.6277 29.8028 27.1505 29.4512 26.7987L22.2327 19.5792C22.1657 19.5113 22.0936 19.4505 22.0171 19.3935ZM22.5008 12.1853C22.5008 13.5397 22.2341 14.8808 21.7159 16.1321C21.1976 17.3834 20.4381 18.5204 19.4805 19.4781C18.5229 20.4358 17.3861 21.1955 16.135 21.7138C14.8839 22.2321 13.5429 22.4988 12.1887 22.4988C10.8345 22.4988 9.49357 22.2321 8.24245 21.7138C6.99133 21.1955 5.85453 20.4358 4.89696 19.4781C3.93939 18.5204 3.17981 17.3834 2.66158 16.1321C2.14334 14.8808 1.87661 13.5397 1.87661 12.1853C1.87661 9.44996 2.96306 6.82666 4.89696 4.89249C6.83086 2.95832 9.45378 1.87172 12.1887 1.87172C14.9237 1.87172 17.5466 2.95832 19.4805 4.89249C21.4144 6.82666 22.5008 9.44996 22.5008 12.1853Z" fill="currentColor" />
      </svg>
    </div>

    <div class="records-box" id="clients-list" aria-live="polite">
      <!-- Registros de exemplo, criando divs para cada usuário de forma dinâmica -->
      <?php include_once BASE_PATH . "/src/controllers/painel_admin/listar_clientes.php"; ?>
    </div>
  </section>

  <section class="records">
    <h2 id="records-heading">Admins da Plataforma</h2>
    <p class="records-intro">Busque e visualize administradores. Clique em um admin para ver detalhes e ações administrativas.</p>

    <div class="records-search" aria-label="Busca de usuários">
      <input id="admin-search" class="search-input" placeholder="Buscar por nome ou e-mail" />
      <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M22.0171 19.3935C23.8326 16.9158 24.6457 13.8439 24.2938 10.7923C23.942 7.74072 22.451 4.93455 20.1192 2.9352C17.7875 0.93585 14.7869 -0.109228 11.7178 0.00904609C8.64871 0.12732 5.73744 1.40022 3.56643 3.5731C1.39542 5.74597 0.124773 8.65856 0.00871192 11.7282C-0.10735 14.7978 0.939728 17.798 2.94046 20.1287C4.9412 22.4593 7.74804 23.9485 10.7994 24.2983C13.8508 24.648 16.9218 23.8326 19.3978 22.015H19.396C19.451 22.0901 19.5122 22.1619 19.5797 22.2307L26.7982 29.4502C27.1497 29.8021 27.6267 29.9998 28.124 30C28.6214 30.0002 29.0985 29.8027 29.4503 29.4511C29.8021 29.0995 29.9998 28.6225 30 28.1251C30.0002 27.6277 29.8028 27.1505 29.4512 26.7987L22.2327 19.5792C22.1657 19.5113 22.0936 19.4505 22.0171 19.3935ZM22.5008 12.1853C22.5008 13.5397 22.2341 14.8808 21.7159 16.1321C21.1976 17.3834 20.4381 18.5204 19.4805 19.4781C18.5229 20.4358 17.3861 21.1955 16.135 21.7138C14.8839 22.2321 13.5429 22.4988 12.1887 22.4988C10.8345 22.4988 9.49357 22.2321 8.24245 21.7138C6.99133 21.1955 5.85453 20.4358 4.89696 19.4781C3.93939 18.5204 3.17981 17.3834 2.66158 16.1321C2.14334 14.8808 1.87661 13.5397 1.87661 12.1853C1.87661 9.44996 2.96306 6.82666 4.89696 4.89249C6.83086 2.95832 9.45378 1.87172 12.1887 1.87172C14.9237 1.87172 17.5466 2.95832 19.4805 4.89249C21.4144 6.82666 22.5008 9.44996 22.5008 12.1853Z" fill="currentColor" />
      </svg>
    </div>

    <div class="records-box" id="admins-list" aria-live="polite">
      <!-- Registros de exemplo, criando divs para cada usuário de forma dinâmica -->
      <?php include_once BASE_PATH . "/src/controllers/painel_admin/listar_admins.php"; ?>
    </div>
  </section>

  <section class="records">
    <h2 id="records-heading">Serviços da Plataforma</h2>
    <p class="records-intro">Busque e visualize serviços. Clique em um serviço para ver detalhes e ações administrativas.</p>

    <div class="records-search" aria-label="Busca de serviços">
      <input id="service-search" class="search-input" placeholder="Buscar por nome, categoria ou descrição" />
      <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M22.0171 19.3935C23.8326 16.9158 24.6457 13.8439 24.2938 10.7923C23.942 7.74072 22.451 4.93455 20.1192 2.9352C17.7875 0.93585 14.7869 -0.109228 11.7178 0.00904609C8.64871 0.12732 5.73744 1.40022 3.56643 3.5731C1.39542 5.74597 0.124773 8.65856 0.00871192 11.7282C-0.10735 14.7978 0.939728 17.798 2.94046 20.1287C4.9412 22.4593 7.74804 23.9485 10.7994 24.2983C13.8508 24.648 16.9218 23.8326 19.3978 22.015H19.396C19.451 22.0901 19.5122 22.1619 19.5797 22.2307L26.7982 29.4502C27.1497 29.8021 27.6267 29.9998 28.124 30C28.6214 30.0002 29.0985 29.8027 29.4503 29.4511C29.8021 29.0995 29.9998 28.6225 30 28.1251C30.0002 27.6277 29.8028 27.1505 29.4512 26.7987L22.2327 19.5792C22.1657 19.5113 22.0936 19.4505 22.0171 19.3935ZM22.5008 12.1853C22.5008 13.5397 22.2341 14.8808 21.7159 16.1321C21.1976 17.3834 20.4381 18.5204 19.4805 19.4781C18.5229 20.4358 17.3861 21.1955 16.135 21.7138C14.8839 22.2321 13.5429 22.4988 12.1887 22.4988C10.8345 22.4988 9.49357 22.2321 8.24245 21.7138C6.99133 21.1955 5.85453 20.4358 4.89696 19.4781C3.93939 18.5204 3.17981 17.3834 2.66158 16.1321C2.14334 14.8808 1.87661 13.5397 1.87661 12.1853C1.87661 9.44996 2.96306 6.82666 4.89696 4.89249C6.83086 2.95832 9.45378 1.87172 12.1887 1.87172C14.9237 1.87172 17.5466 2.95832 19.4805 4.89249C21.4144 6.82666 22.5008 9.44996 22.5008 12.1853Z" fill="currentColor" />
      </svg>
    </div>

    <div class="records-box" id="services-list" aria-live="polite">
      <?php include_once BASE_PATH . "/src/controllers/painel_admin/listar_servicos_admin.php"; ?>
    </div>
  </section>



  <?php include BASE_PATH . '/src/pages/partials/footer.php'; ?>

  <script>
    document.getElementById("create-service-btn").addEventListener("click", function() {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/criar_servico.php";
    });
    document.getElementById("create-admin-btn").addEventListener("click", function() {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/criar_admin.php";
    });
    document.getElementById("create-client-btn").addEventListener("click", function() {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/criar_cliente.php";
    });

    document.addEventListener("DOMContentLoaded", () => {
      const clientSearch = document.getElementById("client-search");
      const adminSearch = document.getElementById("admin-search");
      const clientsList = document.getElementById("clients-list");
      const adminsList = document.getElementById("admins-list");
      const serviceSearch = document.getElementById("service-search");
      const servicesList = document.getElementById("services-list");
      const base = window.location.origin + "/greenhelp-app/src/controllers/painel_admin/";

      // Busca de clientes
      if (clientSearch && clientsList) {
        clientSearch.addEventListener("input", () => {
          const termo = clientSearch.value.trim();
          const url = base + "listar_clientes.php?q=" + encodeURIComponent(termo);

          fetch(url)
            .then(res => res.text())
            .then(html => clientsList.innerHTML = html)
            .catch(err => console.error("Erro ao buscar clientes:", err));
        });
      }

      // Busca de administradores
      if (adminSearch && adminsList) {
        adminSearch.addEventListener("input", () => {
          const termo = adminSearch.value.trim();
          const url = base + "listar_admins.php?q=" + encodeURIComponent(termo);

          fetch(url)
            .then(res => res.text())
            .then(html => adminsList.innerHTML = html)
            .catch(err => console.error("Erro ao buscar administradores:", err));
        });
      }

      // Busca de serviços
      if (serviceSearch && servicesList) {
        serviceSearch.addEventListener("input", () => {
          const termo = serviceSearch.value.trim();
          const url = base + "listar_servicos_admin.php?q=" + encodeURIComponent(termo);

          fetch(url)
            .then(res => res.text())
            .then(html => servicesList.innerHTML = html)
            .catch(err => console.error("Erro ao buscar serviços:", err));
        });
      }
    });
  </script>


</body>

</html>