<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova Senha| GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Criar Nova Senha</h1>
          <p class="lead">Escolha uma senha forte que você lembrará</p>
        </div>

        <form class="auth-form" action="" method="post">
          <div class="form-group">
            <label for="new_password">Nova Senha</label>
            <input type="password" id="new_password" name="new_password" placeholder="Digite sua nova senha">
            <p class="help-text">Use pelo menos 8 caracteres com letras e números</p>
          </div>

          <div class="form-group">
            <label for="confirm_new_password">Confirmar Senha</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirme sua nova senha">
          </div>

          <button type="submit">Criar Senha</button>
        </form>

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