<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

if (empty($_SESSION['user_id'])) {
  header('Location: ' . BASE_URL . '/src/pages/login/login.php');
  exit;
}

$stUsuario = $pdo->prepare("SELECT id, nome, empresa_id FROM usuarios WHERE id = :id AND ativo = 1 LIMIT 1");
$stUsuario->execute([':id' => $_SESSION['user_id']]);
$usuarioLogado = $stUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuarioLogado) {
  session_destroy();
  header('Location: ' . BASE_URL . '/src/pages/login/login.php');
  exit;
}

$_SESSION['user_id'] = (int)$usuarioLogado['id'];
$_SESSION['empresa_id'] = $usuarioLogado['empresa_id'] ? (int)$usuarioLogado['empresa_id'] : null;
$partesNome = preg_split('/\s+/', trim($usuarioLogado['nome']));
$_SESSION['primeiro_nome'] = $partesNome[0] ?? '';

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/controllers/home/areasPontuacaoController.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$logoUrl = BASE_URL . '/public/imgs/add-photo.svg';
$empresaDados = [
  'nome' => '',
  'cnpj' => '',
  'porte' => '',
  'setor_atuacao' => '',
  'endereco' => ''
];

$empresaId = $_SESSION['empresa_id'];

if ($empresaId) {
  $st = $pdo->prepare("SELECT nome, cnpj, porte, setor_atuacao, endereco, logo_path FROM empresas WHERE id = :id");
  $st->execute([':id' => $empresaId]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $empresaDados = [
      'nome' => (string)($row['nome'] ?? ''),
      'cnpj' => (string)($row['cnpj'] ?? ''),
      'porte' => (string)($row['porte'] ?? ''),
      'setor_atuacao' => (string)($row['setor_atuacao'] ?? ''),
      'endereco' => (string)($row['endereco'] ?? '')
    ];
    if (!empty($row['logo_path'])) {
      $logoUrl = $row['logo_path'];
    }
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

    <section class="empresa-panel" aria-labelledby="empresa-title">
      <div class="empresa-panel-header">
        <div>
          <h2 id="empresa-title">Dados da empresa</h2>
          <p>Informações usadas para identificar sua empresa nos serviços contratados.</p>
        </div>

        <div class="empresa-actions empresa-actions-top">
          <button type="button" class="btn edit-button" id="btnEditar">Editar</button>
          <button type="submit" form="formEmpresa" class="btn save-button" id="btnSalvar">Salvar</button>
        </div>
      </div>

      <div class="empresa-content">
        <div class="empresa-logo-area">
          <button type="button" id="btnLogo" aria-label="Alterar logo da empresa" disabled>
            <img id="imgLogo" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES) ?>" alt="Logo da empresa">
          </button>
          <input type="file" id="inpLogo" name="logo" accept="image/*" hidden>
          <span>Logo da empresa</span>
        </div>

        <form id="formEmpresa" class="form-empresa">
          <div class="empresa-fields">
            <label>
              <span>Empresa</span>
              <input readonly id="empresa" type="text" placeholder="Nome da empresa" value="<?= htmlspecialchars($empresaDados['nome'], ENT_QUOTES) ?>">
            </label>

            <label>
              <span>CNPJ</span>
              <input readonly id="cnpj" type="text" placeholder="CNPJ" value="<?= htmlspecialchars($empresaDados['cnpj'], ENT_QUOTES) ?>">
            </label>

            <label>
              <span>Porte</span>
              <input readonly id="perfil" type="text" placeholder="Tamanho da empresa" value="<?= htmlspecialchars($empresaDados['porte'], ENT_QUOTES) ?>">
            </label>

            <label>
              <span>Setor</span>
              <input readonly id="industria" type="text" placeholder="Indústria" value="<?= htmlspecialchars($empresaDados['setor_atuacao'], ENT_QUOTES) ?>">
            </label>

            <label class="empresa-field-wide">
              <span>Endereço</span>
              <input readonly id="endereco" type="text" placeholder="Endereço" maxlength="200" value="<?= htmlspecialchars($empresaDados['endereco'], ENT_QUOTES) ?>">
            </label>
          </div>

          <div class="empresa-actions empresa-actions-bottom">
            <button type="button" class="btn edit-button" data-edit-trigger>Editar</button>
            <button type="submit" class="btn save-button" data-save-trigger>Salvar</button>
          </div>
        </form>
      </div>
    </section>

    
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
          <p class="no-services">Você ainda não tem serviços em andamento. Visite nosso <a href="<?= BASE_URL ?>/src/pages/marketplace/marketplace.php">marketplace</a> para começar!</p>
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
          <?php $pontuacaoTotal = 0; ?>
          <?php foreach ($areasPontuacao as $area): ?>
            <?php

            $areaId   = $area['id'];
            $areaNome = $area['nome'];
            $iconUrl  = $area['icone'];
            $pontos   = $area['pontos'];
            $nivel    = $area['nivel'];
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
                  <div class="progress" style="width: <?= $progress ?>%"></div>
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
