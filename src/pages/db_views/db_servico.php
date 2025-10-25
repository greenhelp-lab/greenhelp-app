<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Serviço</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/db_views/db_servico.css" />
</head>

<body>
  <div class="card">
    <div class="header">
      <div class="title">Serviço</div>
      <button class="close" aria-label="Fechar">✕</button>
    </div>

    <form>
      <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" class="input pill" type="text" />
      </div>

      <div class="field">
        <label for="data">Data de Criação</label>
        <input id="data" class="input pill" type="date" />
      </div>

      <div class="field">
        <label for="area">Área</label>
        <input id="area" class="input pill" type="text" />
      </div>

      <div class="field">
        <label for="preco">Preço Total</label>
        <input id="preco" class="input pill" type="number" step="0.01" />
      </div>

      <div class="field">
        <label for="prazo">Prazo Estimado</label>
        <input id="prazo" class="input pill" type="text" />
      </div>

      <div class="field">
        <label for="pontos">Pontos</label>
        <input id="pontos" class="input pill" type="number" />
      </div>

      <div class="field">
        <label for="id">ID</label>
        <input id="id" class="input pill" type="text" />
      </div>

      <div class="field">
        <label for="descricao">Descrição</label>
        <textarea id="descricao"></textarea>
      </div>

      <div class="footer">
        <button type="button" class="btn btn-del" onclick="showDeletePopup()">Deletar</button>
        <button type="button" class="btn btn-edit">Editar</button>
        <button type="submit" class="btn btn-save">Salvar</button>
      </div>
    </form>
  </div>
  <?php include BASE_PATH . "/src/pages/partials/pop-ups/pop_deletar.html"; ?>
</body>

</html>