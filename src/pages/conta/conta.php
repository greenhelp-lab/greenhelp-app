<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit('Não autenticado');
}
$userId = (int) $_SESSION['user_id'];

$st = $pdo->prepare("SELECT nome, email, telefone, avatar_path, papel FROM usuarios WHERE id = :id LIMIT 1");
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
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/conta/conta.css">
  <title>Conta</title>
</head>

<body data-base-url="<?= BASE_URL; ?>">

  <?php if (isset($_SESSION['papel']) && $_SESSION['papel'] === 'admin') : {
      include_once BASE_PATH . "/src/pages/partials/header_admin.php";
    }
  else : {
      include_once BASE_PATH . "/src/pages/partials/header_cliente.php";
    }
  endif; ?>

  <main class="account-main">
    <div class="conta-container">
      <h1 class="account-title">Meu Usuário</h1>

      <div class="user-photo">
        <button type="button" id="btnFoto" aria-label="Alterar foto do usuário" disabled>
          <img id="fotoUsuario" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES) ?>" alt="Foto do usuário">
        </button>
        <input type="file" id="inpFoto" name="foto" accept="image/*" hidden>
      </div>

      <form id="formConta" class="account-form" autocomplete="off">
        <input id="inpNome" type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($usuario['nome'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpTel" type="tel" name="telefone" placeholder="Telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpEmail" type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpPapel" type="text" name="papel" placeholder="Papel" value="<?= htmlspecialchars(ucfirst($usuario['papel'] ?? ''), ENT_QUOTES) ?>" readonly>

        <div class="action-buttons-top">
          <button type="button" id="btnEditar" class="btn edit-button">Editar</button>
          <button type="submit" id="btnSalvar" class="btn save-button">Salvar</button>
        </div>

        <div class="action-buttons-bottom">
          <button type="button" id="btnDelete" class="btn delete-account-button">Deletar Conta</button>
        </div>
      </form>
    </div>
  </main>

  <img src="<?= BASE_URL; ?>/public/imgs/engines-icons.svg" alt="Ícones de engrenagens decorativas" class="engines-icons">

  <script src="<?= BASE_URL; ?>/public/js/conta/conta.js"></script>
</body>

</html>
