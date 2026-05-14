<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  header('Location: ' . BASE_URL . '/src/pages/login/login.php');
  exit;
}

$itens = [];
if (!empty($usuario_id)) {
  $sql = "SELECT c.id AS cart_id,
                   s.id AS servico_id,
                   s.nome,
                   s.descricao,
                   s.preco,
                   s.prazo,
                   a.nome AS area_nome,
                   a.imagem_url AS area_img
            FROM carrinho c
            JOIN servicos s ON c.servico_id = s.id
            LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
            WHERE c.usuario_id = :usuario_id AND c.status = 'pendente'";

  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
  $stmt->execute();
  $itens = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seu Carrinho</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/marketplace/carrinho.css">
</head>

<body data-base-url="<?php echo BASE_URL; ?>">
  <?php include_once BASE_PATH . "/src/pages/partials/header_cliente.php"; ?>
  <div class="cart-main">
    <section class="cart-content">
      <div class="cart-items" id="cartItems">
        <?php if (!empty($itens)): ?>
          <?php foreach ($itens as $item): ?>
            <article class="cart-item" data-id="<?= htmlspecialchars($item['cart_id']) ?>" data-price="<?= htmlspecialchars($item['preco']) ?>">
              <div class="item-left">
                <div class="item-thumb" aria-hidden="true">
                  <?php if (!empty($item['area_img'])): ?>
                    <img src="<?= htmlspecialchars($item['area_img']) ?>" alt="Ícone da área" class="thumb-icon" />
                  <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="thumb-icon">
                      <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" />
                      <path d="M12 16V8M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                  <?php endif; ?>
                </div>
                <div class="item-info">
                  <h3 class="item-title"><?= htmlspecialchars($item['nome']) ?></h3>
                  <p class="item-desc"><?= htmlspecialchars($item['descricao']) ?></p>
                  <div class="item-meta">
                    <span class="item-area"><?= htmlspecialchars($item['area_nome']) ?></span>
                    <?php if (!empty($item['prazo'])): ?>
                      <span class="item-leadtime">Prazo: <?= htmlspecialchars($item['prazo']) ?> dias</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="item-right">
                <div class="item-price">R$ <?= number_format($item['preco'], 2, ',', '.') ?></div>
                <button type="button" class="item-remove" aria-label="Remover item" data-id="<?= htmlspecialchars($item['cart_id']) ?>">
                  <svg viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                  </svg>
                </button>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="cart-empty" id="cartEmpty">
            <h2>Seu carrinho está vazio</h2>
            <p>Explore os serviços e adicione soluções sustentáveis ao seu carrinho.</p>
            <a href="<?php echo BASE_URL; ?>/src/pages/marketplace/marketplace.php" class="btn-primary">Ver serviços</a>
          </div>
        <?php endif; ?>
      </div>

      <aside class="cart-summary" id="cartSummary">
        <div class="summary-card">
          <h2>Resumo do pedido</h2>
          <div class="summary-row">
            <span>Serviços selecionados:</span>
            <span id="items-count">0</span>
          </div>
          <div class="summary-row total-row">
            <strong>Total</strong>
            <strong id="total">R$ 0,00</strong>
          </div>

          <button type="button" id="finalizar-compra" class="btn-primary btn-checkout" disabled>
            Finalizar compra
          </button>

          <button type="button" id="continuar-compra" class="btn-ghost btn-continue">
            Continuar comprando
          </button>
        </div>
      </aside>

    </section>
  </div>

  <div class="modal" id="confirmModal" style="display: none;">
    <div class="modal-card">
      <h3>Confirmar compra</h3>
      <p>Deseja finalizar a compra dos serviços selecionados?</p>
      <div class="modal-actions">
        <button type="button" id="cancelar-compra" class="btn-ghost">Cancelar</button>
        <button type="button" id="confirmar-compra" class="btn-primary">Confirmar</button>
      </div>
    </div>
  </div>

  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>

  <script src="<?php echo BASE_URL; ?>/public/js/marketplace/carrinho.js"></script>
</body>

</html>
