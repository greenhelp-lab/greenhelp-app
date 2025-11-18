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
        <input type="hidden" name="id" value="<?= (int)$userId ?>">
        <input id="inpNome" type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($usuario['nome'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpTel" type="tel" name="telefone" placeholder="Telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpEmail" type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpPapel" type="text" name="papel" placeholder="Papel" value="<?= htmlspecialchars(ucfirst($usuario['papel'] ?? ''), ENT_QUOTES) ?>" readonly disabled>
        <label for="inpAtivo">Ativado? (0 para Não, 1 para Sim)</label>
        <input id="inpAtivo" type="number" name="ativo" placeholder="Ativado?" value="<?= htmlspecialchars((string)$usuario['ativo'], ENT_QUOTES) ?>" readonly>

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
      if (!form) return;

      const btnEditar = document.getElementById('btnEditar');
      const btnSalvar = document.getElementById('btnSalvar');
      const btnDelete = document.getElementById('btnDelete');

      const inputs = {
        nome: document.getElementById('inpNome'),
        tel: document.getElementById('inpTel'),
        email: document.getElementById('inpEmail'),
        papel: document.getElementById('inpPapel'),
        ativo: document.getElementById('inpAtivo')
      };

      function setEditing(on) {
        inputs.nome.readOnly = !on;
        inputs.tel.readOnly = !on;
        inputs.email.readOnly = !on;
        inputs.ativo.readOnly = !on;
        btnEditar.disabled = on;
        if (on) inputs.nome.focus();
      }

      setEditing(false);
      btnEditar.addEventListener('click', () => setEditing(true));

      // SALVAR via AJAX
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
          id: form.querySelector('input[name="id"]').value,
          nome: inputs.nome.value.trim(),
          telefone: inputs.tel.value.trim(),
          email: inputs.email.value.trim(),
          ativado: inputs.ativo.value.trim()
        };

        if (!payload.nome || !payload.email) {
          alert('Preencha nome e email.');
          return;
        }

        try {
          btnSalvar.disabled = true;
          const res = await fetch(`${API}/src/controllers/painel_admin/atualizar_admin_controller.php`, {
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
          alert('Admin atualizado com sucesso!');
        } catch (err) {
          alert('Falha ao salvar: ' + err.message);
        } finally {
          btnSalvar.disabled = false;
        }
      });

      // DELETAR ADMIN via AJAX
      btnDelete.addEventListener('click', async () => {
        if (!confirm('Tem certeza que deseja excluir este admin? Esta ação não pode ser desfeita.')) return;
        try {
          const id = form.querySelector('input[name="id"]').value;
          const r = await fetch(`${API}/src/controllers/painel_admin/deletar_admin_controller.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              id
            }),
            credentials: 'include'
          });
          const j = await r.json().catch(() => ({}));
          if (!r.ok || !j.ok) throw new Error(j.error || 'Erro ao deletar');

          window.location.href = API + "/src/pages/painel_admin/painel_admin.php";
        } catch (err) {
          alert('Falha ao deletar: ' + err.message);
        }
      });

    })();
  </script>
</body>

</html>