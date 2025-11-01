<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Login</h1>
          <p class="lead">Bem-vindo, GreenHelper!<br>Por favor, faça login para continuar.</p>
        </div>

        <form class="auth-form" action="<?= BASE_URL ?>/src/controllers/login_controller.php" method="post">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Digite seu email">
          </div>

          <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" placeholder="Digite sua senha">
          </div>

          <div class="form-group-remember">
            <label for="remember-me">
              <input type="checkbox" id="remember-me" name="remember-me"> Lembrar-me
            </label>
          </div>

          <button type="submit">Login</button>
        </form>

        <div class="auth-links">
          <a href="<?php echo BASE_URL; ?>/src/pages/login/esqueci_senha.php">Esqueceu sua senha?</a>
          <p class="help-text">Não tem uma conta? <a href="<?php echo BASE_URL; ?>/src/pages/login/criar_empresa.php">Crie uma</a></p>
        </div>

        <a href="<?php echo BASE_URL; ?>/public/sobre_nos.html" class="back-button">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" fill="none">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </a>
      </div>
    </section>
  </main>
</body>

</html>