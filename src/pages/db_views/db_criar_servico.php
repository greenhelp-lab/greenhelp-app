<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/actions/serviços.php';

$model = new Servico($pdo);
$areas = $model->listarAreas();
$servicos = $model->listarTodos();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Criar e Listar Serviços</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/db_views/db_criar_servico.css" />
</head>

<body>
  <div class="card">
    <div class="header">
      <div class="title">Criar Serviço</div>
      <button class="close" aria-label="Fechar">✕</button>
    </div>

    <form action="<?php echo BASE_URL; ?>/src/controllers/serviço_controller.php?action=criar" method="POST">
      <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" class="input pill" type="text" required />
      </div>

      <div class="field">
        <label for="area_id">Área</label>
        <select id="area_id" name="area_id" class="input pill" required>
          <option value="">Selecione a área</option>
          <?php foreach ($areas as $area): ?>
            <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="preco">Preço Total</label>
        <input id="preco" name="preco" class="input pill" type="number" step="0.01" required />
      </div>

      <div class="field">
        <label for="prazo">Prazo</label>
        <input id="prazo" name="prazo" class="input pill" type="text" placeholder="Ex: 15 dias" />
      </div>

      <div class="field">
        <label for="pontos">Pontos</label>
        <input id="pontos" name="pontos" class="input pill" type="number" />
      </div>

      <div class="field">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao"></textarea>
      </div>

      <div class="field">
        <label for="descricao_longa">Descrição Longa</label>
        <textarea id="descricao_longa" name="descricao_longa" placeholder="Mais detalhes sobre o serviço"></textarea>
      </div>

      <div class="field">
        <label for="itens_incluidos">Itens Incluídos</label>
        <input id="itens_incluidos" name="itens_incluidos" class="input pill" type="text" placeholder="Ex: Material, transporte..." />
      </div>

      <div class="footer">
        <button type="reset" class="btn btn-descartar">Descartar</button>
        <button type="submit" class="btn btn-criar">Criar</button>
      </div>
    </form>
  </div>

  <?php include BASE_PATH . "/src/pages/partials/pop-ups/pop_salvo.html"; ?>
</body>

</html>