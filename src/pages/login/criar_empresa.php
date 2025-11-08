<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Conta | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Criar Conta</h1>
          <p class="lead">Olá! Preencha algumas informações sobre a sua empresa</p>
        </div>

        <!-- Formulário -->
        <form class="auth-form" action="<?php echo BASE_URL; ?>/src/actions/criar_empresa_action.php" method="post">

          <div class="form-group">
            <label for="business_name">Nome da Empresa</label>
            <input type="text" id="business_name" name="business_name" placeholder="Digite o nome da sua empresa" required>
          </div>

          <div class="form-group">
            <label for="business_cnpj">CNPJ da Empresa</label>
            <input type="text" id="business_cnpj" name="business_cnpj" placeholder="Digite o CNPJ da sua empresa" pattern="[0-9]*" required>
          </div>

          <div class="form-group">
            <label for="business_industry">Setor da Empresa</label>
            <input type="text" id="business_industry" name="business_industry" placeholder="Digite o setor da sua empresa" required>
          </div>

          <div class="form-group">
            <label for="business_size">Tamanho da Empresa</label>
            <input type="text" id="business_size" name="business_size" placeholder="Digite o tamanho da sua empresa" required>
          </div>

          <button type="submit">Criar Conta</button>
        </form>

        <div class="auth-links">
          <p class="help-text">Já tem uma conta? <a href="<?php echo BASE_URL; ?>/src/pages/login/login.php">Login</a></p>
        </div>

        <a href="javascript:history.back()" class="back-button">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" fill="none">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </a>
      </div>
    </section>
  </main>
</body>
</html>
