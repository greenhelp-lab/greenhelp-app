<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

$id = $_GET['id'] ?? null;
$servico = null;

if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
  $stmt->execute([$id]);
  $servico = $stmt->fetch();
}

$query = $pdo->query("SELECT id, nome FROM areas_sustentaveis ORDER BY nome ASC");
$areas = $query->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Serviço</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/painel_admin/ver_servico.css" />
</head>

<body>
  <?php include_once BASE_PATH . '/src/pages/partials/header_admin.php'; ?>
  <div class="card">
    <div class="header">
      <div class="title">Serviço</div>
      <button class="close" id="close-btn" aria-label="Fechar">✕</button>
    </div>

    <?php if ($servico): ?>
      <form id="formServico">
        <input type="hidden" name="id" value="<?php echo $servico['id']; ?>" />

        <div class="field">
          <label for="nome">Nome</label>
          <input id="nome" name="nome" class="input pill" type="text" value="<?php echo htmlspecialchars($servico['nome']); ?>" readonly />
        </div>

        <div class="field">
          <label for="area">Área</label>
          <select id="area" name="area_id" class="input pill" disabled>
            <?php foreach ($areas as $area): ?>
              <option value="<?php echo $area['id']; ?>" <?php echo $servico['area_id'] == $area['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($area['nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="preco">Preço Total (R$)</label>
          <input id="preco" name="preco" class="input pill" type="number" step="0.01" value="<?php echo $servico['preco']; ?>" readonly />
        </div>

        <div class="field">
          <label for="prazo">Prazo Estimado (Dias)</label>
          <input id="prazo" name="prazo" class="input pill" type="number" value="<?php echo $servico['prazo']; ?>" readonly />
        </div>

        <div class="field">
          <label for="pontos">Pontos</label>
          <input id="pontos" name="pontos" class="input pill" type="number" value="<?php echo $servico['pontos']; ?>" readonly />
        </div>

        <div class="field">
          <label for="categoria">Categoria</label>
          <input id="categoria" name="categoria" class="input pill" type="text" value="<?php echo htmlspecialchars($servico['categoria']); ?>" readonly />
        </div>

        <div class="field">
          <label for="descricao">Descrição Curta</label>
          <textarea id="descricao" name="descricao" rows="2" readonly><?php echo htmlspecialchars($servico['descricao']); ?></textarea>
        </div>

        <div class="field">
          <label for="descricao_longa">Descrição Longa</label>
          <textarea id="descricao_longa" name="descricao_longa" rows="4" readonly><?php echo htmlspecialchars($servico['descricao_longa']); ?></textarea>
        </div>

        <div class="field">
          <label for="itens_incluidos">Itens Incluídos</label>
          <textarea id="itens_incluidos" name="itens_incluidos" rows="5" readonly><?php echo htmlspecialchars($servico['itens_incluidos']); ?></textarea>
        </div>

        <div class="field">
          <label for="garantia">Garantia</label>
          <input id="garantia" name="garantia" class="input pill" type="text" value="<?php echo htmlspecialchars($servico['garantia']); ?>" readonly />
        </div>

        <div class="field">
          <label for="contato">E-mail de Contato</label>
          <input id="contato" name="contato" class="input pill" type="email" value="<?php echo htmlspecialchars($servico['contato']); ?>" readonly />
        </div>

        <div class="field">
          <label for="disponivel">Disponível? (0 = Não, 1 = Sim)</label>
          <input id="disponivel" name="disponivel" class="input pill" type="number" value="<?php echo $servico['disponivel']; ?>" readonly />
        </div>

        <div class="footer">
          <a class="btn btn-view" href="<?php echo BASE_URL; ?>/src/pages/marketplace/sobre_servico.php?id=<?php echo $servico['id']; ?>" target="_blank">👁️ Ver como Cliente</a>
          <button type="button" id="btnEditar" class="btn btn-edit">Editar</button>
          <button type="submit" id="btnSalvar" class="btn btn-save" style="display:none;">Salvar</button>
          <button type="button" id="btnDelete" class="btn btn-del">Deletar</button>
        </div>
      </form>
    <?php else: ?>
      <p style="text-align:center;">Serviço não encontrado.</p>
    <?php endif; ?>

  </div>

  <script>
    const API = '<?php echo rtrim(BASE_URL, '/'); ?>';

    document.getElementById("close-btn").addEventListener("click", () => {
      window.location.href = API + "/src/pages/painel_admin/painel_admin.php";
    });

    (function() {
      const form = document.getElementById('formServico');
      if (!form) return;

      const btnEditar = document.getElementById('btnEditar');
      const btnSalvar = document.getElementById('btnSalvar');
      const btnDelete = document.getElementById('btnDelete');

      const inputs = {
        nome: document.getElementById('nome'),
        area_id: document.getElementById('area'),
        preco: document.getElementById('preco'),
        prazo: document.getElementById('prazo'),
        pontos: document.getElementById('pontos'),
        categoria: document.getElementById('categoria'),
        descricao: document.getElementById('descricao'),
        descricao_longa: document.getElementById('descricao_longa'),
        itens_incluidos: document.getElementById('itens_incluidos'),
        garantia: document.getElementById('garantia'),
        contato: document.getElementById('contato'),
        disponivel: document.getElementById('disponivel')
      };

      function setEditing(on) {
        inputs.nome.readOnly = !on;
        inputs.area_id.disabled = !on;
        inputs.preco.readOnly = !on;
        inputs.prazo.readOnly = !on;
        inputs.pontos.readOnly = !on;
        inputs.categoria.readOnly = !on;
        inputs.descricao.readOnly = !on;
        inputs.descricao_longa.readOnly = !on;
        inputs.itens_incluidos.readOnly = !on;
        inputs.garantia.readOnly = !on;
        inputs.contato.readOnly = !on;
        inputs.disponivel.readOnly = !on;

        btnEditar.style.display = on ? 'none' : 'inline-block';
        btnSalvar.style.display = on ? 'inline-block' : 'none';

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
          area_id: inputs.area_id.value,
          preco: inputs.preco.value,
          prazo: inputs.prazo.value,
          pontos: inputs.pontos.value,
          categoria: inputs.categoria.value.trim(),
          descricao: inputs.descricao.value.trim(),
          descricao_longa: inputs.descricao_longa.value.trim(),
          itens_incluidos: inputs.itens_incluidos.value.trim(),
          garantia: inputs.garantia.value.trim(),
          contato: inputs.contato.value.trim(),
          disponivel: inputs.disponivel.value
        };

        if (!payload.nome) {
          alert('Preencha o nome do serviço.');
          return;
        }

        try {
          btnSalvar.disabled = true;
          const res = await fetch(`${API}/src/controllers/painel_admin/atualizar_servico_controller.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });

          const j = await res.json().catch(() => ({}));
          if (!res.ok || !j.ok) throw new Error(j.error || 'Erro ao salvar');

          setEditing(false);
          alert('Serviço atualizado com sucesso!');
        } catch (err) {
          alert('Falha ao salvar: ' + err.message);
        } finally {
          btnSalvar.disabled = false;
        }
      });

      // DELETAR CONTA via AJAX
      btnDelete.addEventListener('click', async () => {
        if (!confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.')) return;
        try {
          const id = form.querySelector('input[name="id"]').value;
          const r = await fetch(`${API}/src/controllers/painel_admin/deletar_servico_controller.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
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