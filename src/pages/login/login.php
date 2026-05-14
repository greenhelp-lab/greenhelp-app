<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Login</h1>
          <p class="lead">Bem-vindo, GreenHelper!<br>Por favor, faça login para continuar.</p>
        </div>

        <form class="auth-form" action="<?= BASE_URL ?>/src/controllers/auth/login_controller.php" method="post">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Digite seu email">
          </div>

          <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha">
          </div>

          <?php
          session_start();
          if (isset($_SESSION['mensagem_erro'])) {
            echo '<p style="color: var(--danger);">' . $_SESSION['mensagem_erro'] . '</p>';
            unset($_SESSION['mensagem_erro']);
          }
          ?>

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
<?php include BASE_PATH . "/src/pages/partials/vlibras.php"; ?>

</body>

</html>
