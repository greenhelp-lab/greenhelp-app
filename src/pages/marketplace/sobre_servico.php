<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

session_start();
$is_admin = isset($_SESSION['papel']) && $_SESSION['papel'] === 'admin';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Serviço inválido.");
}

$servico_id = (int)$_GET['id'];

$sql = "SELECT  s.nome, s.descricao, s.preco, s.pontos, s.categoria, s.descricao_longa, s.itens_incluidos, s.garantia, 
                s.prazo, s.contato, s.area_id, a.nome AS area_nome, a.imagem_url AS area_img
        FROM servicos s
        LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
        WHERE s.id = :id AND s.disponivel = 1
        ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $servico_id);
$stmt->execute();

$servico = $stmt->fetch();

if (!$servico) {
  die("Serviço não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sobre o Serviço</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/marketplace/sobre_servico.css">
  <script src="/greenhelp-app/src/helpers/add_to_cart.js"></script>
</head>

<body>
  <?php if ($is_admin = true) : {
      include_once BASE_PATH . "/src/pages/partials/header_admin.php";
    }
  else : {
      include_once BASE_PATH . "/src/pages/partials/header_cliente.php";
    }
  endif; ?>

  <main class="service-detail-page">
    <div class="detail-block">
      <div class="detail-header">
        <div class="detail-title-area">
          <h1 class="service-name"><?= htmlspecialchars($servico['nome']) ?></h1>
          <div class="service-meta">
            <div class="service-area">
              <?php if (!empty($servico['area_img'])): ?>
                <img class="service-card-icon" src="<?= htmlspecialchars($servico['area_img']) ?>" alt="<?= htmlspecialchars($servico['area_nome'] ?? '') ?>" style="width:40px; height:40px;">
              <?php endif; ?>
              <div class="service-area-text">
                <strong>Área:</strong>
                <span><?= htmlspecialchars($servico['area_nome'] ?? 'Não definida') ?></span>
              </div>
            </div>

            <div class="service-score">
              <span class="score-value"><?= number_format($servico['pontos'] ?? 0, 0, ',', '.') ?></span>
              <span class="score-label">pontos de impacto sustentável</span>
            </div>

            <div class="service-price">
              <span class="price-label">Preço a partir de</span>
              <span class="price-value">R$ <?= number_format($servico['preco'] ?? 0, 2, ',', '.') ?></span>
            </div>
          </div>
        </div>
      </div>

      <section class="detail-main">
        <article class="detail-description">
          <h2>Detalhes do Serviço</h2>
          <p><?= nl2br(htmlspecialchars($servico['descricao_longa'] ?? $servico['descricao'] ?? '')) ?></p>

          <h3>O que está incluído</h3>
          <ul class="included-list">
            <!-- Lista o conteúddo do campo itens_incluidos do serviço no banco de dados - cada quebra de linha ("\n") cria um novo <li> -->
            <?php foreach (explode("\n", $servico['itens_incluidos']) as $item): ?>
              <li><?= trim($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>

        <aside class="detail-aside">
          <div class="aside-card">
            <h3>Detalhes Rápidos</h3>
            <dl class="meta-list">
              <div class="meta-row">
                <dt>Garantia</dt>
                <dd><?= htmlspecialchars($servico['garantia'] ?? '-') ?></dd>
              </div>
              <div class="meta-row">
                <dt>Prazo estimado</dt>
                <dd><?= htmlspecialchars($servico['prazo'] ?? '-') ?> dias</dd>
              </div>
              <div class="meta-row">
                <dt>Acompanhamento</dt>
                <dd>Atualizações de status via Registros na plataforma e emails sobre o andamento do serviço</dd>
              </div>
              <div class="meta-row">
                <dt>Contato Técnico</dt>
                <dd><a href="mailto:suporte@greenhelp.com">suporte@greenhelp.com</a></dd>
              </div>
            </dl>

            <div class="cta-area">
              <button class="btn-primary btn-hire">
                <span>Contratar serviço</span>
                <svg class="btn-icon" viewBox="0 0 42 39" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16233 7.36962C6.8847 5.62044 5.40643 4.33522 3.67211 4.33522H2.12146C0.949817 4.33522 0 3.36474 0 2.16761C0 0.970474 0.949817 0 2.12146 0H3.67211C7.18499 0 10.2204 2.39683 11.1564 5.7803H35.2849C36.8471 5.7803 38.1254 7.07769 37.9902 8.66784C37.6473 12.695 36.6477 16.1226 35.7143 18.4632C35.0309 20.1765 33.5883 21.4005 31.8139 21.7702C29.9622 22.1562 27.1377 22.5471 23.2933 22.5471C20.5408 22.5471 18.2219 22.3467 16.3839 22.0913C15.3514 21.9478 14.4221 21.5632 13.6236 20.9974L14.0524 23.6995C14.33 25.4486 15.8083 26.7339 17.5426 26.7339H33.2364C34.408 26.7339 35.3578 27.7044 35.3578 28.9015C35.3578 30.0986 34.408 31.0691 33.2364 31.0691H17.5426C13.7272 31.0691 10.4749 28.2417 9.86418 24.3934L7.16233 7.36962ZM14.4359 39C16.1843 39 17.6016 37.552 17.6016 35.7656C17.6016 33.9792 16.1843 32.531 14.4359 32.531C12.6876 32.531 11.2703 33.9792 11.2703 35.7656C11.2703 37.552 12.6876 39 14.4359 39ZM36.4022 35.7656C36.4022 37.552 34.9848 39 33.2364 39C31.488 39 30.0709 37.552 30.0709 35.7656C30.0709 33.9792 31.488 32.531 33.2364 32.531C34.9848 32.531 36.4022 33.9792 36.4022 35.7656Z" fill="currentColor" />
                </svg>
              </button>
              <!-- Botão reutilizável para adicionar ao carrinho -->
              <button class="btn-add-cart btn-ghost" data-id="<?= htmlspecialchars($servico_id) ?>">
                <span>Adicionar ao carrinho</span>
              </button>
            </div>

            <div class="trust-badge">
              <small>Projeto avaliado por equipe técnica GreenHelp</small>
            </div>
          </div>
        </aside>
      </section>

      <section class="detail-related">
        <h3>Serviços relacionados</h3>
        <div class="related-list">
          <a class="related-item" href="#">Monitoramento Remoto</a>
          <a class="related-item" href="#">Otimização Energética</a>
          <a class="related-item" href="#">Manutenção Preditiva</a>
        </div>
      </section>
    </div>
  </main>
  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const isAdmin = <?= json_encode($is_admin) ?>;
      if (isAdmin) {
        document.querySelectorAll(".cta-area button").forEach(btn => {
          btn.addEventListener("click", e => e.preventDefault());
          btn.style.opacity = '0.5';
          btn.style.cursor = 'not-allowed';
        });
      }
    });
  </script>
</body>

</html>