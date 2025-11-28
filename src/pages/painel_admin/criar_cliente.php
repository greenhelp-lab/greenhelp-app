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
  <title>Criar Cliente</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/painel_admin/criar_registro.css" />
</head>

<body>
  <?php include_once BASE_PATH . '/src/pages/partials/header_admin.php'; ?>
  <div class="card">
    <div class="header">
      <div class="title">Criar Cliente</div>
      <button class="close" id="close-btn" aria-label="Fechar">✕</button>
    </div>

    <?php
    // carregar empresas existentes para associar
    try {
      $empresasStmt = $pdo->query("SELECT id, nome, cnpj FROM empresas ORDER BY nome");
      $empresas = $empresasStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      $empresas = [];
    }
    ?>

    <form id="criarClienteForm" method="POST" action="<?php echo BASE_URL; ?>/src/controllers/painel_admin/criar_cliente_controller.php">
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
        <label for="empresa_id">Empresa / Associação</label>
        <select id="empresa_id" name="empresa_id" class="input pill">
          <option value="">-- Nenhuma / Não informar --</option>
          <?php foreach ($empresas as $emp): ?>
            <option value="<?= (int)$emp['id'] ?>"><?= htmlspecialchars($emp['nome']) ?><?= $emp['cnpj'] ? ' — ' . htmlspecialchars($emp['cnpj']) : '' ?></option>
          <?php endforeach; ?>
          <option value="new">Criar nova empresa / associação</option>
        </select>
      </div>

      <div id="new-company-fields" style="display:none; margin-top:8px;">
        <div class="field">
          <label for="empresa_nome">Nome da Empresa / Associação</label>
          <input id="empresa_nome" name="empresa_nome" class="input pill" type="text" />
        </div>
        <div class="field">
          <label for="empresa_cnpj">CNPJ</label>
          <input id="empresa_cnpj" name="empresa_cnpj" class="input pill" type="text" />
        </div>
        <div class="field">
          <label for="empresa_setor">Setor de atuação</label>
          <input id="empresa_setor" name="empresa_setor" class="input pill" type="text" />
        </div>
        <div class="field">
          <label for="empresa_porte">Porte</label>
          <input id="empresa_porte" name="empresa_porte" class="input pill" type="text" />
        </div>
      </div>

      <div class="field">
        <label for="senha">Senha</label>
        <input id="senha" name="senha" class="input pill" type="password" required />
      </div>

      <div class="footer">
        <button type="reset" class="btn btn-descartar">Descartar</button>
        <button type="submit" class="btn btn-criar">Criar Cliente</button>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("close-btn").addEventListener("click", () => {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
    });

    const params = new URLSearchParams(window.location.search);
    if (params.get('status') === 'success') {
      // simples feedback visual, reusa pop-salvo se existir
      document.querySelector('.pop-salvo')?.classList.add('show');
      setTimeout(() => {
        window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
      }, 1600);
    }

    // Toggle new company fields when user chooses to create a new empresa
    (function() {
      const empresaSelect = document.getElementById('empresa_id');
      const newFields = document.getElementById('new-company-fields');
      const form = document.getElementById('criarClienteForm');

      if (!empresaSelect || !newFields || !form) return;

      function toggle() {
        if (empresaSelect.value === 'new') {
          newFields.style.display = 'block';
          // make new company fields required when creating
          newFields.querySelectorAll('input').forEach(i => i.required = true);
        } else {
          newFields.style.display = 'none';
          newFields.querySelectorAll('input').forEach(i => i.required = false);
        }
      }

      empresaSelect.addEventListener('change', toggle);
      toggle();

      // client-side validation: if new company chosen, ensure empresa_nome filled
      form.addEventListener('submit', (e) => {
        if (empresaSelect.value === 'new') {
          const nome = document.getElementById('empresa_nome').value.trim();
          if (!nome) {
            e.preventDefault();
            alert('Preencha o nome da empresa/associação ao criar uma nova.');
          }
        }
      });
    })();
  </script>
</body>

</html>