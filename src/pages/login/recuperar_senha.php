<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperação de Senha | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Recuperação de Senha</h1>
          <p class="lead">Digite o código de 6 dígitos enviado para o seu email</p>
        </div>

        <form class="auth-form" action="" method="post">
          <div class="form-group">
            <label for="code">Código de Verificação</label>
            <input type="number" id="code" name="code" placeholder="Digite o código de 6 dígitos" required>
            <p class="help-text">Verifique sua caixa de entrada e pasta de spam</p>
          </div>

          <div class="form-action">
            <button type="button" class="link-button">Reenviar Código</button>
            <button type="submit">Verificar Código</button>
          </div>
        </form>

        <div class="auth-links">
          <p class="help-text">Lembrou sua senha? <a href="<?php echo BASE_URL; ?>/login">Login</a></p>
        </div>

        <a href="javascript:history.back()" class="back-button">
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