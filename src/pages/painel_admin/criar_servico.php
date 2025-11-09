<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

// Busca as áreas sustentáveis do banco
$query = $pdo->query("SELECT id, nome FROM areas_sustentaveis ORDER BY nome ASC");
$areas = $query->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Criar Serviço</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/painel_admin/criar_servico.css" />
</head>

<body>
  <?php include_once BASE_PATH . '/src/pages/partials/header_admin.php'; ?>
  <div class="card">
    <div class="header">
      <div class="title">Criar Serviço</div>
      <button class="close" id="close-btn" aria-label="Fechar">✕</button>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/src/actions/criar_servico_action.php">
      <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" class="input pill" type="text" required />
      </div>

      <div class="field">
        <label for="area">Área</label>
        <select id="area" name="area_id" class="input pill" required>
          <option value="">Selecione uma área</option>
          <?php foreach ($areas as $area): ?>
            <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['nome']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="preco">Preço Total (R$)</label>
        <input id="preco" name="preco" class="input pill" type="number" step="0.01" required />
      </div>

      <div class="field">
        <label for="prazo">Prazo Estimado (dias)</label>
        <input id="prazo" name="prazo_dias" class="input pill" type="number" required />
      </div>

      <div class="field">
        <label for="pontos">Pontos</label>
        <input id="pontos" name="pontos" class="input pill" type="number" required />
      </div>

      <div class="field">
        <label for="categoria">Categoria</label>
        <input id="categoria" name="categoria" class="input pill" type="text" />
      </div>

      <div class="field">
        <label for="descricao">Descrição Curta</label>
        <textarea id="descricao" name="descricao" rows="2"></textarea>
      </div>

      <div class="field">
        <label for="descricao_longa">Descrição Longa</label>
        <textarea id="descricao_longa" name="descricao_longa" rows="4"></textarea>
      </div>

      <div class="field">
        <label for="itens_incluidos">Itens Incluídos (um por linha)</label>
        <textarea id="itens_incluidos" name="itens_incluidos" rows="5" placeholder="Exemplo:&#10;Diagnóstico técnico&#10;Instalação&#10;Monitoramento remoto"></textarea>
      </div>

      <div class="field">
        <label for="garantia">Garantia</label>
        <input id="garantia" name="garantia" class="input pill" type="text" placeholder="Ex: 12 meses" />
      </div>

      <div class="field">
        <label for="contato">E-mail de Contato</label>
        <input id="contato" name="contato" class="input pill" type="email" />
      </div>


      <div class="footer">
        <button type="reset" class="btn btn-descartar">Descartar</button>
        <button type="submit" class="btn btn-criar">Criar</button>
      </div>
    </form>
  </div>

  <script>
    // Fecha e retorna ao painel
    document.getElementById("close-btn").addEventListener("click", () => {
      window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
    });

    // Redireciona após criação bem-sucedida
    const params = new URLSearchParams(window.location.search);
    if (params.get('status') === 'success') {
      document.querySelector('.pop-salvo')?.classList.add('show');
      setTimeout(() => {
        window.location.href = "<?php echo BASE_URL; ?>/src/pages/painel_admin/painel_admin.php";
      }, 2000);
    }
  </script>
</body>

</html>