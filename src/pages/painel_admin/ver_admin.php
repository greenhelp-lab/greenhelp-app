<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once BASE_PATH . '/src/controllers/painel_admin/require_admin.php';
$userId = (int)$_GET['id'];


$st = $pdo->prepare("SELECT nome, email, telefone, avatar_path, papel, ativo FROM usuarios WHERE id = :id LIMIT 1");
$st->execute([':id' => $userId]);
$usuario = $st->fetch();

$avatarUrl = BASE_URL . '/public/imgs/add-photo.svg';
if ($usuario && !empty($usuario['avatar_path'])) {
  $avatarUrl = $usuario['avatar_path'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/painel_admin/ver_usuario.css">
  <title>Ver Admin</title>
</head>

<body data-base-url="<?= BASE_URL; ?>">

  <?php include_once BASE_PATH . "/src/pages/partials/header_admin.php"; ?>

  <main class="account-main center-layout">
    <div class="conta-container">

      <section class="cliente-info">
        <h1 class="account-title">Sobre o Admin</h1>

        
        <div class="photo-card">
          <img id="fotoUsuario"
            src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES) ?>"
            alt="Foto do usuário">
        </div>

        
        <form id="formConta" class="account-form" autocomplete="off">
          <input type="hidden" name="id" value="<?= (int)$userId ?>">

          <div class="form-grid cliente-fields">

            <div class="field">
              <label for="inpNome">Nome</label>
              <input id="inpNome"
                type="text"
                name="nome"
                value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="inpTel">Telefone</label>
              <input id="inpTel"
                type="tel"
                name="telefone"
                value="<?= htmlspecialchars($usuario['telefone'], ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="inpEmail">Email</label>
              <input id="inpEmail"
                type="email"
                name="email"
                value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="inpPapel">Papel</label>
              <input id="inpPapel"
                type="text"
                name="papel"
                value="<?= htmlspecialchars(ucfirst($usuario['papel']), ENT_QUOTES) ?>"
                readonly disabled>
            </div>

            <div class="field">
              <label for="inpAtivo">Ativado? (0 = não, 1 = sim)</label>
              <input id="inpAtivo"
                type="number"
                min="0"
                max="1"
                name="ativo"
                value="<?= (int)$usuario['ativo'] ?>"
                readonly>
            </div>

          </div>

      </section>

      <section class="actions">

        <div class="action-row primary-actions">
          <button type="button" id="btnEditar" class="btn edit-button">Editar</button>
          <button type="submit" id="btnSalvar" class="btn save-button">Salvar</button>
        </div>

        <div class="action-row danger-zone">
          <button type="button" id="btnDelete" class="btn delete-account-button">Deletar Conta</button>
        </div>

      </section>

      </form>

    </div>
  </main>

  <img src="<?= BASE_URL; ?>/public/imgs/engines-icons.svg"
    alt="Ícones decorativos"
    class="engines-icons">

  <script src="<?= BASE_URL; ?>/public/js/painel_admin/ver_admin.js"></script>
</body>

</html>
