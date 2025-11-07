<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

$id = $_GET['id'] ?? null;
$servico = null;

if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
  $stmt->execute([$id]);
  $servico = $stmt->fetch(PDO::FETCH_ASSOC);
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
      <form method="POST" action="<?php echo BASE_URL; ?>/src/actions/editar_servico_action.php">
        <input type="hidden" name="id" value="<?php echo $servico['id']; ?>" />

        <div class="field">
          <label for="nome">Nome</label>
          <input id="nome" name="nome" class="input pill" type="text" value="<?php echo htmlspecialchars($servico['nome']); ?>" />
        </div>

        <div class="field">
          <label for="area">Área</label>
          <select id="area" name="area_id" class="input pill">
            <?php foreach ($areas as $area): ?>
              <option value="<?php echo $area['id']; ?>" <?php echo $servico['area_id'] == $area['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($area['nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="preco">Preço Total</label>
          <input id="preco" name="preco" class="input pill" type="number" step="0.01" value="<?php echo $servico['preco']; ?>" />
        </div>

        <div class="field">
          <label for="prazo">Prazo Estimado (Dias)</label>
          <input id="prazo" name="prazo_dias" class="input pill" type="number" value="<?php echo $servico['prazo']; ?>" />
        </div>

        <div class="field">
          <label for="pontos">Pontos</label>
          <input id="pontos" name="pontos" class="input pill" type="number" value="<?php echo $servico['pontos']; ?>" />
        </div>

        <div class="field">
          <label for="categoria">Categoria</label>
          <input id="categoria" name="categoria" class="input pill" type="text" value="<?php echo htmlspecialchars($servico['categoria']); ?>" />
        </div>

        <div class="field">
          <label for="descricao">Descrição Curta</label>
          <textarea id="descricao" name="descricao" rows="2"><?php echo htmlspecialchars($servico['descricao']); ?></textarea>
        </div>

        <div class="field">
          <label for="descricao_longa">Descrição Longa</label>
          <textarea id="descricao_longa" name="descricao_longa" rows="4"><?php echo htmlspecialchars($servico['descricao_longa']); ?></textarea>
        </div>

        <div class="field">
          <label for="itens_incluidos">Itens Incluídos</label>
          <textarea id="itens_incluidos" name="itens_incluidos" rows="5"><?php echo htmlspecialchars($servico['itens_incluidos']); ?></textarea>
        </div>

        <div class="field">
          <label for="garantia">Garantia</label>
          <input id="garantia" name="garantia" class="input pill" type="text" value="<?php echo htmlspecialchars($servico['garantia']); ?>" />
        </div>

        <div class="field">
          <label for="contato">E-mail de Contato</label>
          <input id="contato" name="contato" class="input pill" type="email" value="<?php echo htmlspecialchars($servico['contato']); ?>" />
        </div>

        <div class="field">
          <label for="contato">Disponível? (0 para Não, 1 para Sim)</label>
          <input id="disponivel" name="disponivel" class="input pill" type="number" value="<?php echo htmlspecialchars($servico['disponivel']); ?>" />
        </div>

        <div class="footer">
          <a class="btn btn-view" href="<?php echo BASE_URL; ?>/src/pages/marketplace/sobre_servico.php?id=<?php echo $servico['id']; ?>" target="_blank">👁️ Ver como Cliente</a>

          <form id="delete-form" method="POST" action="<?php echo BASE_URL; ?>/src/actions/excluir_servico_action.php" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $servico['id']; ?>">
            <button type="button" class="btn btn-del" onclick="confirmarExclusao()">Deletar</button>
          </form>

          <button type="submit" class="btn btn-edit">Editar</button>
          <button type="submit" class="btn btn-save">Salvar</button>
        </div>
      </form>
    <?php else: ?>
      <p style="text-align:center;">Serviço não encontrado.</p>
    <?php endif; ?>

  </div>

  <script>
    // Fecha e retorna ao painel
    document.getElementById("close-btn").addEventListener("click", () => {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
    });

    function confirmarExclusao() {
      const confirmar = confirm("Tem certeza que deseja excluir este serviço?");
      if (confirmar) {
        document.getElementById('delete-form').submit();
      }
    }
  </script>

</body>

</html>