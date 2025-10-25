<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Conta | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Criar Conta</h1>
          <p class="lead">Use um email confiável (entraremos em contato por ele)</p>
        </div>

        <form class="auth-form" action="" method="post">
          <div class="form-group">
            <label for="client_name">Nome Completo</label>
            <input type="text" id="client_name" name="client_name" placeholder="Digite seu nome completo">
          </div>

          <div class="form-group">
            <label for="business_email">Seu Email</label>
            <input type="email" id="business_email" name="business_email" placeholder="Digite seu email">
          </div>

          <div class="form-group">
            <label for="business_password">Senha</label>
            <input type="password" id="business_password" name="business_password" placeholder="Digite sua senha">
          </div>

          <div class="form-group">
            <label for="password_confirm">Confirmar Senha</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirme sua senha">
            <p class="help-text">Crie uma senha forte com pelo menos 8 caracteres</p>
          </div>

          <button type="submit">Criar Conta</button>
        </form>

        <div class="auth-links">
          <p class="help-text">Já tem uma conta? <a href="<?php echo BASE_URL; ?>/login">Login</a></p>
        </div>

        <a href="<?php echo BASE_URL; ?>/login/create-account" class="back-button">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" fill="none">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </a>
      </div>
    </section>
  </main>
</body>

</html>