<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit('Não autenticado');
}
$userId = (int)$_GET['id'];

$st = $pdo->prepare("SELECT nome, email, telefone, avatar_path, papel, empresa_id FROM usuarios WHERE id = :id LIMIT 1");
$st->execute([':id' => $userId]);
$usuario = $st->fetch();

$avatarUrl = BASE_URL . '/public/imgs/add-photo.svg';
if ($usuario && !empty($usuario['avatar_path'])) {
  $avatarUrl = $usuario['avatar_path'];
}

// carregar dados da empresa associada (se houver)
$empresa = null;
if (!empty($usuario['empresa_id'])) {
  $es = $pdo->prepare("SELECT id, nome, cnpj, setor_atuacao, porte FROM empresas WHERE id = :id LIMIT 1");
  $es->execute([':id' => (int)$usuario['empresa_id']]);
  $empresa = $es->fetch();
}
// carregar lista de empresas para select de associação/edit
try {
  $empresasStmt = $pdo->query("SELECT id, nome, cnpj, setor_atuacao, porte FROM empresas ORDER BY nome");
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
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/conta/conta.css">
  <title>Ver Cliente</title>
</head>

<body>

  <?php include_once BASE_PATH . "/src/pages/partials/header_admin.php"; ?>

  <main class="account-main">
    <div class="conta-container">
      <h1 class="account-title">Sobre o Cliente</h1>

      <div class="user-photo">
        <img id="fotoUsuario" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES) ?>" alt="Foto do usuário">
      </div>

      <form id="formConta" class="account-form" autocomplete="off">
        <input type="hidden" name="id" value="<?= (int)$userId ?>">
        <input id="inpNome" type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($usuario['nome'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpTel" type="tel" name="telefone" placeholder="Telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpEmail" type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES) ?>" readonly>
        <input id="inpPapel" type="text" name="papel" placeholder="Papel" value="<?= htmlspecialchars(ucfirst($usuario['papel'] ?? ''), ENT_QUOTES) ?>" readonly disabled>

        <div class="empresa-form" style="margin-top:12px;">
          <label for="empresa_select">Empresa / Associação</label>
          <select id="empresa_select" name="empresa_select" class="input pill">
            <option value="">-- Nenhuma --</option>
            <?php foreach ($empresas as $empOpt): ?>
              <option value="<?= (int)$empOpt['id'] ?>" <?= ($empresa && $empresa['id'] == $empOpt['id']) ? 'selected' : '' ?>><?= htmlspecialchars($empOpt['nome'], ENT_QUOTES) ?><?= $empOpt['cnpj'] ? ' — ' . htmlspecialchars($empOpt['cnpj'], ENT_QUOTES) : '' ?></option>
            <?php endforeach; ?>
            <option value="new">Criar nova empresa</option>
          </select>

          <div id="empresa-fields" style="margin-top:8px;">
            <div class="field">
              <label for="empresa_nome">Nome da Empresa</label>
              <input id="empresa_nome" name="empresa_nome" class="input pill" type="text" value="<?= htmlspecialchars($empresa['nome'] ?? '', ENT_QUOTES) ?>" readonly />
            </div>
            <div class="field">
              <label for="empresa_cnpj">CNPJ</label>
              <input id="empresa_cnpj" name="empresa_cnpj" class="input pill" type="text" value="<?= htmlspecialchars($empresa['cnpj'] ?? '', ENT_QUOTES) ?>" readonly />
            </div>
            <div class="field">
              <label for="empresa_setor">Setor</label>
              <input id="empresa_setor" name="empresa_setor" class="input pill" type="text" value="<?= htmlspecialchars($empresa['setor_atuacao'] ?? '', ENT_QUOTES) ?>" readonly />
            </div>
            <div class="field">
              <label for="empresa_porte">Porte</label>
              <input id="empresa_porte" name="empresa_porte" class="input pill" type="text" value="<?= htmlspecialchars($empresa['porte'] ?? '', ENT_QUOTES) ?>" readonly />
            </div>

            <?php if ($empresa): ?>
              <div class="field">
                <label><input type="checkbox" id="empresa_update" /> Atualizar dados da empresa associada</label>
              </div>
            <?php endif; ?>
          </div>
        </div>

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
        papel: document.getElementById('inpPapel')
      };

      // empresa controls
      const empresaSelect = document.getElementById('empresa_select');
      const empresaFields = {
        nome: document.getElementById('empresa_nome'),
        cnpj: document.getElementById('empresa_cnpj'),
        setor: document.getElementById('empresa_setor'),
        porte: document.getElementById('empresa_porte')
      };
      const empresaUpdateCheckbox = document.getElementById('empresa_update');

      // map of empresas for quick lookup
      const empresasData = <?= json_encode($empresas, JSON_HEX_TAG) ?> || [];
      const empresasMap = {};
      empresasData.forEach(e => empresasMap[e.id] = e);

      function setEditing(on) {
        inputs.nome.readOnly = !on;
        inputs.tel.readOnly = !on;
        inputs.email.readOnly = !on;
        btnEditar.disabled = on;
        // when enabling edit, enable empresa select and enable fields appropriately
        if (empresaSelect) empresaSelect.disabled = !on;
        if (!on) {
          // reset empresa fields to readonly when leaving edit mode
          Object.values(empresaFields).forEach(i => i && (i.readOnly = true));
          if (empresaUpdateCheckbox) empresaUpdateCheckbox.checked = false;
        } else {
          // if editing and a company is selected, allow toggling update via checkbox
          if (empresaSelect && empresaSelect.value === 'new') {
            // creating new company -> make fields editable and required
            Object.values(empresaFields).forEach(i => i && (i.readOnly = false));
            document.getElementById('empresa_nome').required = true;
          } else if (empresaSelect && empresaSelect.value) {
            // existing company selected -> keep fields readonly unless checkbox checked
            Object.values(empresaFields).forEach(i => i && (i.readOnly = true));
          }
        }
        if (on) inputs.nome.focus();
      }

      setEditing(false);
      btnEditar.addEventListener('click', () => setEditing(true));

      // empresa select change handling
      if (empresaSelect) {
        empresaSelect.addEventListener('change', () => {
          const v = empresaSelect.value;
          if (v === 'new') {
            // clear fields and make editable
            Object.values(empresaFields).forEach(i => i && (i.value = ''));
            Object.values(empresaFields).forEach(i => i && (i.readOnly = false));
            if (empresaUpdateCheckbox) empresaUpdateCheckbox.checked = false;
          } else if (v === '') {
            // no company
            Object.values(empresaFields).forEach(i => i && (i.value = '', i.readOnly = true));
            if (empresaUpdateCheckbox) empresaUpdateCheckbox.checked = false;
          } else {
            // existing company - populate
            const obj = empresasMap[v];
            if (obj) {
              empresaFields.nome.value = obj.nome || '';
              empresaFields.cnpj.value = obj.cnpj || '';
              empresaFields.setor.value = obj.setor_atuacao || '';
              empresaFields.porte.value = obj.porte || '';
            }
            Object.values(empresaFields).forEach(i => i && (i.readOnly = true));
          }
        });
      }

      if (empresaUpdateCheckbox) {
        empresaUpdateCheckbox.addEventListener('change', () => {
          const checked = empresaUpdateCheckbox.checked;
          Object.values(empresaFields).forEach(i => i && (i.readOnly = !checked));
        });
      }

      // SALVAR via AJAX
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
          id: form.querySelector('input[name="id"]').value,
          nome: inputs.nome.value.trim(),
          telefone: inputs.tel.value.trim(),
          email: inputs.email.value.trim()
        };

        // include empresa association/update info
        if (empresaSelect) {
          payload.empresa_select = empresaSelect.value; // '', 'new' or id
          payload.empresa = {
            nome: empresaFields.nome.value.trim(),
            cnpj: empresaFields.cnpj.value.trim(),
            setor_atuacao: empresaFields.setor.value.trim(),
            porte: empresaFields.porte.value.trim()
          };
          payload.empresa_update = !!(empresaUpdateCheckbox && empresaUpdateCheckbox.checked);
        }

        if (!payload.nome || !payload.email) {
          alert('Preencha nome e email.');
          return;
        }

        try {
          btnSalvar.disabled = true;
          const res = await fetch(`${API}/src/controllers/painel_admin/atualizar_cliente_controller.php`, {
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
          alert('Cliente atualizado com sucesso!');
        } catch (err) {
          alert('Falha ao salvar: ' + err.message);
        } finally {
          btnSalvar.disabled = false;
        }
      });

      // DELETAR CLIENTE via AJAX
      btnDelete.addEventListener('click', async () => {
        if (!confirm('Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.')) return;
        try {
          const id = form.querySelector('input[name="id"]').value;
          const r = await fetch(`${API}/src/controllers/painel_admin/deletar_cliente_controller.php`, {
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