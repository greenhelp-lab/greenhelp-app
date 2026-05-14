<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit('Não autenticado');
}
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

<body>

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
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
          id: form.querySelector('input[name="id"]').value,
          nome: inputs.nome.value.trim(),
          telefone: inputs.tel.value.trim(),
          email: inputs.email.value.trim(),
          ativo: inputs.ativo.value.trim()
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