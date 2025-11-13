<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';
session_start();

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Criar Admin</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/painel_admin/criar_registro.css" />
</head>

<body>
  <?php include_once BASE_PATH . '/src/pages/partials/header_admin.php'; ?>
  <div class="card">
    <div class="header">
      <div class="title">Criar Admin</div>
      <button class="close" id="close-btn" aria-label="Fechar">✕</button>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/src/actions/painel_admin/criar_admin_action.php">
      <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" class="input pill" type="text" required />
      </div>

      <div class="field">
        <label for="email">E-mail</label>
        <input id="email" name="email" class="input pill" type="email" required />
      </div>

      <div class="field">
        <label for="telefone">Telefone</label>
        <input id="telefone" name="telefone" class="input pill" type="tel" />
      </div>

      <div class="field">
        <label for="senha">Senha</label>
        <input id="senha" name="senha" class="input pill" type="password" required />
      </div>

      <div class="footer">
        <button type="reset" class="btn btn-descartar">Descartar</button>
        <button type="submit" class="btn btn-criar">Criar Admin</button>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("close-btn").addEventListener("click", () => {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
    });

    const params = new URLSearchParams(window.location.search);
    if (params.get('status') === 'success') {
      document.querySelector('.pop-salvo')?.classList.add('show');
      setTimeout(() => {
        window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
      }, 1600);
    }
  </script>
</body>

</html>