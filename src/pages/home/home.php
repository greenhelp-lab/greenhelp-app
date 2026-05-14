<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/controllers/home/areasPontuacaoController.php';
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$logoUrl = BASE_URL . '/public/imgs/add-photo.svg';

if (!empty($_SESSION['user_id'])) {

  $empresaId = $_SESSION['empresa_id'];
  if (!$empresaId) {
    $st = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id = :uid ORDER BY id DESC LIMIT 1");
    $st->execute([':uid' => $_SESSION['user_id']]);
    $empresaId = $st->fetchColumn() ?: null;
    if ($empresaId) $_SESSION['empresa_id'] = (int)$empresaId;
  }

  if ($empresaId) {
    $st = $pdo->prepare("SELECT logo_path FROM empresas WHERE id = :id");
    $st->execute([':id' => $empresaId]);
    $row = $st->fetchColumn();
    if (!empty($row)) $logoUrl = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenHelp Home</title>
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/home/home.css">
</head>
<body>
  <?php include_once BASE_PATH . "/src/pages/partials/header_cliente.php"; ?>

  <main class="container">
    <h1 class="welcome-title">Bem vindo(a), <?= htmlspecialchars($_SESSION['primeiro_nome'] ?? ''); ?>!</h1>

    <div class="user-photo">
      <button type="button" id="btnLogo" aria-label="Alterar logo da empresa" disabled>
        <img id="imgLogo" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES) ?>" alt="Logo da empresa">
      </button>
      <input type="file" id="inpLogo" name="logo" accept="image/*" hidden>
    </div>

    <h2>Sobre Sua Empresa</h2>

    <form id="formEmpresa" class="form-empresa">
      <input readonly id="empresa" type="text" placeholder="Nome da Empresa">
      <input readonly id="cnpj" type="text" placeholder="CNPJ">
      <input readonly id="perfil" type="text" placeholder="Tamanho da Empresa">
      <input readonly id="industria" type="text" placeholder="Indústria">
      <input readonly id="endereco" type="text" placeholder="Endereço" maxlength="200"> 

      <div class="form-actions" style="display:flex; gap:12px; margin-top:12px;">
        <button type="button" class="btn edit-button" id="btnEditar">Editar</button>
        <button type="submit" class="btn save-button" id="btnSalvar">Salvar</button>
      </div>
    </form>

    
    <?php
    $sql = "SELECT sa.id, sa.status, sa.valor_total, sa.data_inicio, 
                   s.nome, s.descricao,
                   a.nome as area_nome, a.imagem_url as area_img
            FROM servicos_andamento sa
            INNER JOIN servicos s ON sa.servico_id = s.id
            LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
            WHERE sa.usuario_id = :usuario_id
            ORDER BY sa.data_inicio DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
    $stmt->execute();
    $servicos = $stmt->fetchAll();
    ?>

    <section class="servicos-andamento">
      <div class="section-header">
        <h2>Serviços Contratados</h2>
      </div>
      <div class="servicos-grid">
        <?php if (!empty($servicos)): ?>
          <?php foreach ($servicos as $servico):
            $status_class = match ($servico['status']) {
              'pendente' => 'status-pendente',
              'em andamento' => 'status-andamento',
              'concluido' => 'status-concluido',
              'cancelado' => 'status-cancelado',
              default => ''
            }; ?>
            <div class="servico-card">
              <div class="servico-header">
                <?php if ($servico['area_img']): ?>
                  <img src="<?= htmlspecialchars($servico['area_img']) ?>" alt="<?= htmlspecialchars($servico['area_nome']) ?>" class="area-icon">
                <?php endif; ?>
                <span class="status-badge <?= $status_class ?>"><?= ucfirst($servico['status']) ?></span>
              </div>
              <div class="servico-body">
                <h3><?= htmlspecialchars($servico['nome']) ?></h3>
                <p class="area-nome"><?= htmlspecialchars($servico['area_nome']) ?></p>
                <p class="descricao"><?= htmlspecialchars($servico['descricao']) ?></p>
              </div>
              <div class="servico-footer">
                <span class="data">Adquirido: <?= date('d/m/Y', strtotime($servico['data_inicio'])) ?></span>
                <span class="preco">R$ <?= number_format($servico['valor_total'], 2, ',', '.') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-services">Você ainda não tem serviços em andamento. Visite nosso <a href="<?= BASE_URL ?>/src/pages/servicos/marketplace.php">marketplace</a> para começar!</p>
        <?php endif; ?>
      </div>
    </section>
    
    <section class="pontuacoes">
      <div class="pontuacoes-header">
        <h2 class="pontuacoes-title">Pontuações Verdes</h2>
      </div>
      <p class="pontuacoes-desc">
        Ganhe mais pontos através da <span class="cor_verde">compra de serviços</span> e
        <span class="cor_verde">melhoras sustentáveis</span> na sua empresa
      </p>
      <div class="niveis">
          <?php foreach ($areasPontuacao as $area): ?>
            <?php

            $areaId   = $area['id'];
            $areaNome = $area['nome'];
            $iconUrl  = $area['icone'];
            $pontos   = $area['pontos'];
            $nivel    = $area['nivel'];
            $pontuacaoTotal = 0;
            $pontuacaoTotal += $pontos;
            $progress = ($pontos % 100);
            $faltam   = 100 - $progress;
            ?>
            <div class="nivel-card" 
                data-area-id="<?= $areaId ?>" 
                data-nivel="<?= $nivel ?>">

              <div class="nivel-card-header">

                <?php if (!empty($iconUrl)): ?>
                  <img class="area-icon" 
                      src="<?= htmlspecialchars($iconUrl) ?>" 
                      alt="<?= htmlspecialchars($areaNome) ?>">
                <?php endif; ?>

                <div class="nivel-titles">
                  <h3 class="nivel-desc"><?= htmlspecialchars($areaNome) ?></h3>

                  <p class="nivel-meta">
                    <span class="nivel">Nível <?= $nivel ?></span>
                    <span class="faltam">• Faltam <?= $faltam ?> pts</span>
                  </p>
                </div>

              </div>

              <div class="nivel-card-body">
                <div class="progress-bar" aria-hidden="true">
                  <div class="progress" data-progress="<?= $progress ?>"></div>
                </div>

                <div class="nivel-stats">
                  <span class="pontos"><?= $pontos ?> pts</span>
                  <span class="percent"><?= $progress ?>%</span>
                </div>
              </div>

            </div>

          <?php endforeach; ?>
      </div>

      <p class="pontuacao-total">Pontuação Total: <strong><?= $pontuacaoTotal ?></strong></p>
    </section>

  </main>

  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>

  <script src="<?= BASE_URL; ?>/src/helpers/home.js"></script>
</body>

</html>