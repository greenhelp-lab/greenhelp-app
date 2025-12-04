<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci a Senha | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Redefinir Senha</h1>
          <p class="lead">Digite seu e-mail para o processo de verificação, enviaremos um código.</p>
        </div>

        <form class="auth-form" action="" method="post">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail cadastrado" required>
          </div>

          <button type="submit">Enviar Código de Redefinição</button>
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

  <div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>

<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>

</body>

</html>