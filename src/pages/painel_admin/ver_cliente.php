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

<body data-base-url="<?= BASE_URL; ?>" data-empresa-id="<?= htmlspecialchars((string)($usuario['empresa_id'] ?? ''), ENT_QUOTES) ?>">

  <?php include_once BASE_PATH . "/src/pages/partials/header_admin.php"; ?>

  <main class="account-main center-layout">
    <div class="conta-container">

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

          </div>

      </section>

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
                name="empresa_nome"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['nome'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="empresa_cnpj">CNPJ</label>
              <input id="empresa_cnpj"
                name="empresa_cnpj"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['cnpj'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="empresa_setor">Setor</label>
              <input id="empresa_setor"
                name="empresa_setor"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['setor_atuacao'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

            <div class="field">
              <label for="empresa_porte">Porte</label>
              <input id="empresa_porte"
                name="empresa_porte"
                type="text"
                class="input pill"
                value="<?= htmlspecialchars($empresa['porte'] ?? '', ENT_QUOTES) ?>"
                readonly>
            </div>

          </div>
        </div>
        <div class="servicos-card mt-12">
          <h3 class="empresa-subtitle">Serviços Contratados</h3>

          <div class="servicos-grid-admin">
            <?php
            $servicosStmt = $pdo->prepare("SELECT sa.id, sa.status, sa.valor_total, sa.data_inicio, s.nome, s.descricao, s.id as servico_id, a.nome as area_nome, a.imagem_url as area_img 
              FROM servicos_andamento sa
              INNER JOIN servicos s ON sa.servico_id = s.id
              LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
              WHERE sa.usuario_id = :cliente_id 
              ORDER BY sa.data_inicio DESC");

            $servicosStmt->execute([':cliente_id' => (int)$userId]);
            $servicos = $servicosStmt->fetchAll();

            if (empty($servicos)):
            ?>
              <p class="no-services">Nenhum serviço contratado.</p>
              <?php
            else:
              foreach ($servicos as $servico):
                $status_class = match ($servico['status']) {
                  'pendente' => 'status-pendente',
                  'em andamento' => 'status-andamento',
                  'concluido' => 'status-concluido',
                  'cancelado' => 'status-cancelado',
                  default => ''
                };
              ?>
                <div class="servico-card-admin">
                  <div class="servico-header">
                    <?php if ($servico['area_img']): ?>
                      <img src="<?= htmlspecialchars($servico['area_img'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($servico['area_nome'], ENT_QUOTES) ?>" class="area-icon">
                    <?php endif; ?>
                    <div class="status-edit-wrapper">
                      <select class="status-select" data-servico-id="<?= (int)$servico['id'] ?>" data-current-status="<?= htmlspecialchars($servico['status'], ENT_QUOTES) ?>">
                        <option value="pendente" <?= $servico['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="em andamento" <?= $servico['status'] === 'em andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="concluido" <?= $servico['status'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                        <option value="cancelado" <?= $servico['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                      </select>
                    </div>
                  </div>
                  <div class="servico-body">
                    <h3><?= htmlspecialchars($servico['nome'], ENT_QUOTES) ?></h3>
                    <p class="area-nome"><?= htmlspecialchars($servico['area_nome'], ENT_QUOTES) ?></p>
                    <p class="descricao"><?= htmlspecialchars($servico['descricao'], ENT_QUOTES) ?></p>
                  </div>
                  <div class="servico-footer">
                    <span class="data">Adquirido: <?= date('d/m/Y', strtotime($servico['data_inicio'])) ?></span>
                    <span class="preco">R$ <?= number_format($servico['valor_total'], 2, ',', '.') ?></span>
                  </div>
                </div>
            <?php
              endforeach;
            endif;
            ?>
      </section>

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

  <script id="empresas-data" type="application/json"><?= json_encode($empresas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?></script>
  <script src="<?= BASE_URL; ?>/public/js/painel_admin/ver_cliente.js"></script>


</body>

</html>
