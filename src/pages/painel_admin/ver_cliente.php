<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit('Não autenticado');
}

$userId = (int)($_GET['id'] ?? 0);

$st = $pdo->prepare("SELECT id, nome, email, telefone, avatar_path, papel, empresa_id, ativo 
                     FROM usuarios 
                     WHERE id = :id LIMIT 1");
$st->execute([':id' => $userId]);
$usuario = $st->fetch();

if (!$usuario) {
  http_response_code(404);
  exit('Usuário não encontrado');
}

$avatarUrl = BASE_URL . '/public/imgs/add-photo.svg';
if (!empty($usuario['avatar_path'])) {
  $avatarUrl = $usuario['avatar_path'];
}

$empresa = null;
if (!empty($usuario['empresa_id'])) {
  $es = $pdo->prepare("SELECT id, nome, cnpj, setor_atuacao, porte 
                       FROM empresas 
                       WHERE id = :id LIMIT 1");
  $es->execute([':id' => (int)$usuario['empresa_id']]);
  $empresa = $es->fetch();
}

try {
  $empresasStmt = $pdo->query("SELECT id, nome, cnpj, setor_atuacao, porte 
                               FROM empresas ORDER BY nome");
  $empresas = $empresasStmt->fetchAll();
} catch (Exception $e) {
  $empresas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/painel_admin/ver_usuario.css">

  <title>Ver Cliente</title>
</head>

<body>

  <?php include_once BASE_PATH . "/src/pages/partials/header_admin.php"; ?>

  <main class="account-main center-layout">
    <div class="conta-container">

      <!-- ============================
            CLIENTE
    ============================= -->
      <section class="cliente-info">
        <h1 class="account-title">Sobre o Cliente</h1>

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

            <!-- NOVO CAMPO ATIVO -->
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

          </div><!-- grid -->

      </section>

      <!-- ============================
            EMPRESA
    ============================= -->
      <section class="empresa-info">
        <h2 class="section-title">Empresa vinculada</h2>

        <div class="empresa-select-wrapper">
          <label for="empresa_select">Empresa / Associação</label>
          <select id="empresa_select" name="empresa_select" class="input pill" disabled>
            <option value="">-- Nenhuma --</option>

            <?php foreach ($empresas as $emp): ?>
              <option value="<?= (int)$emp['id'] ?>"
                <?= ($empresa && $empresa['id'] == $emp['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($emp['nome'], ENT_QUOTES) ?>
                <?= $emp['cnpj'] ? ' — ' . htmlspecialchars($emp['cnpj'], ENT_QUOTES) : '' ?>
              </option>
            <?php endforeach; ?>

            <option value="new">Criar nova empresa</option>
          </select>
        </div>

        <div class="empresa-card mt-12">
          <h3 class="empresa-subtitle">Dados da empresa</h3>

          <div id="empresa-fields" class="form-grid empresa-fields">

            <div class="field">
              <label for="empresa_nome">Nome</label>
              <input id="empresa_nome"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['nome'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="empresa_cnpj">CNPJ</label>
              <input id="empresa_cnpj"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['cnpj'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="empresa_setor">Setor</label>
              <input id="empresa_setor"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['setor_atuacao'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="empresa_porte">Porte</label>
              <input id="empresa_porte"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['porte'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <?php if ($empresa): ?>
              <div class="field">
                <label>
                  <input type="checkbox" id="empresa_update" disabled>
                  Atualizar dados da empresa associada
                </label>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </section>

      <!-- ============================
            AÇÕES
    ============================= -->
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
        ativo: document.getElementById('inpAtivo'),
        papel: document.getElementById('inpPapel')
      };

      const empresaSelect = document.getElementById('empresa_select');
      const empresaUpdate = document.getElementById('empresa_update');

      function setEditing(on) {
        inputs.nome.readOnly = !on;
        inputs.tel.readOnly = !on;
        inputs.email.readOnly = !on;
        inputs.ativo.readOnly = !on;

        btnEditar.disabled = on;

        if (empresaSelect) empresaSelect.disabled = !on;
        if (empresaUpdate) empresaUpdate.disabled = !on;

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
          ativo: inputs.ativo.value.trim(),
          empresa_id: empresaSelect ? empresaSelect.value : null,
          empresa_update: empresaUpdate ? empresaUpdate.checked : false,
          empresa_data: {
            nome: document.getElementById('empresa_nome')?.value || '',
            cnpj: document.getElementById('empresa_cnpj')?.value || '',
            setor: document.getElementById('empresa_setor')?.value || '',
            porte: document.getElementById('empresa_porte')?.value || ''
          }
        };

        try {
          btnSalvar.disabled = true;

          const res = await fetch(`${API}/src/controllers/painel_admin/atualizar_cliente_controller.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify(payload)
          });

          const j = await res.json().catch(() => ({}));
          if (!res.ok || !j.ok) throw new Error(j.error || 'Erro ao salvar');

          setEditing(false);
          alert('Cliente atualizado com sucesso!');
        } catch (err) {
          alert('Falha ao salvar: ' + err.message);
        } finally {
          btnSalvar.disabled = false;
        }
      });

      btnDelete.addEventListener('click', async () => {
        if (!confirm('Deseja excluir este cliente?')) return;

        try {
          const id = form.querySelector('input[name="id"]').value;

          const r = await fetch(`${API}/src/controllers/painel_admin/deletar_cliente_controller.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
              id
            })
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