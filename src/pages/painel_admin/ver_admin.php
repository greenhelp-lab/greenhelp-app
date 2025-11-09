<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit('Não autenticado');
}
$userId = (int)$_GET['id'];

/* -------- CARREGAR DADOS -------- */
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
  <title>Ver Admin</title>
</head>

<body>

  <?php include_once BASE_PATH . "/src/pages/partials/header_admin.php"; ?>

  <main class="account-main">
    <div class="conta-container">
      <h1 class="account-title">Sobre o Admin</h1>

      <div class="user-photo">
        <img id="fotoUsuario" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES) ?>" alt="Foto do usuário">
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

  <script>
    const API = '<?= rtrim(BASE_URL, '/') ?>';

    (function() {
      const form = document.getElementById('formConta');
      const inputFile = document.getElementById('inpFoto');
      const btnEditar = document.getElementById('btnEditar');
      const btnSalvar = document.getElementById('btnSalvar');
      const btnDelete = document.getElementById('btnDelete');

      const inputs = {
        nome: document.getElementById('inpNome'),
        tel: document.getElementById('inpTel'),
        email: document.getElementById('inpEmail'),
        papel: document.getElementById('inpPapel')
      };

      function setEditing(on) {
        inputs.nome.readOnly = !on;
        inputs.tel.readOnly = !on;
        inputs.email.readOnly = !on;
        btnEditar.disabled = on;
        if (on) inputs.nome.focus();
      }

      setEditing(false);
      btnEditar.addEventListener('click', () => setEditing(true));

      // salvar via AJAX (sem reload)
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
          nome: inputs.nome.value.trim(),
          telefone: inputs.tel.value.trim(),
          email: inputs.email.value.trim()
        };

        if (!payload.nome || !payload.email) {
          alert('Preencha nome e email.');
          return;
        }

        try {
          btnSalvar.disabled = true;
          const res = await fetch(`${API}/src/controllers/update_usuario_controller.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload),
            credentials: 'include'
          });

          const j = await res.json().catch(() => ({}));
          if (!res.ok || !j.ok) throw new Error(j.error || 'Erro ao salvar');

          setEditing(false);
        } catch (err) {
          alert('Falha ao salvar: ' + err.message);
        } finally {
          btnSalvar.disabled = false;
        }
      });

      // DELETAR CONTA (AJAX) -> usa redirect do backend ou fallback para /src/pages/login/login.php
      btnDelete.addEventListener('click', async () => {
        if (!confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) return;
        try {
          const r = await fetch(`${API}/src/controllers/delete_usuario_controller.php`, {
            method: 'POST',
            credentials: 'include'
          });
          const j = await r.json().catch(() => ({}));
          if (!r.ok || !j.ok) throw new Error(j.error || 'Erro ao deletar');

          const to = j.redirect || ('<?= rtrim(BASE_URL, '/') ?>/src/pages/login/login.php');
          window.location.href = to;
        } catch (err) {
          alert('Falha ao deletar conta: ' + err.message);
        }
      });

    })();
  </script>
</body>

</html>