<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit('Não autenticado');
}
$userId = (int) $_SESSION['user_id'];

/* -------- CARREGAR DADOS -------- */
$st = $pdo->prepare("SELECT nome, email, telefone, avatar_path, papel FROM usuarios WHERE id = :id LIMIT 1");
$st->execute([':id' => $userId]);
$usuario = $st->fetch(PDO::FETCH_ASSOC);

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

<body>

  <?php include_once BASE_PATH . "/src/pages/partials/header_cliente.php"; ?>

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

  <script>
    const API = '<?= rtrim(BASE_URL, '/') ?>';
    const UPLOAD_URL = API + '/src/actions/upload_avatar.php';

    (function() {
      const form = document.getElementById('formConta');
      const btnFoto = document.getElementById('btnFoto');
      const img = document.getElementById('fotoUsuario');
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
        btnFoto.disabled = !on;
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

      // upload de avatar
      const okMimes = ['image/jpeg', 'image/png', 'image/webp'];
      const MAX = 3 * 1024 * 1024;

      btnFoto.addEventListener('click', () => inputFile.click());
      inputFile.addEventListener('change', async () => {
        const file = inputFile.files?.[0];
        if (!file) return;
        if (!okMimes.includes(file.type)) {
          alert('JPG/PNG/WEBP');
          inputFile.value = '';
          return;
        }
        if (file.size > MAX) {
          alert('Até 3MB');
          inputFile.value = '';
          return;
        }

        const t = URL.createObjectURL(file);
        img.src = t;
        img.onload = () => URL.revokeObjectURL(t);

        const fd = new FormData();
        fd.append('foto', file);

        try {
          const r = await fetch(UPLOAD_URL, {
            method: 'POST',
            body: fd,
            credentials: 'include'
          });
          if (!r.ok) throw new Error(await r.text());
          const data = await r.json();
          if (data.url) img.src = data.url + '?t=' + Date.now();
        } catch (e) {
          alert('Falha no upload: ' + e.message);
          inputFile.value = '';
        }
      });
    })();
  </script>
</body>

</html>