<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
// sessão e conexão com o banco
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

// id do usuário logado
$usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  header('Location: ' . BASE_URL . '/src/pages/login/login.php');
  exit;
}

// busca itens do carrinho para o usuário logado
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
  $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seu Carrinho</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/servicos/carrinho.css">
</head>

<body>
  <?php include_once BASE_PATH . '/src/pages/partials/header.php'; ?>
  <div class="cart-main">
    <section class="cart-content">
      <div class="cart-items" id="cartItems">
        <?php if (!empty($itens)): ?>
          <?php foreach ($itens as $item): ?>
            <article class="cart-item" data-id="<?= htmlspecialchars($item['cart_id']) ?>" data-price="<?= htmlspecialchars($item['preco']) ?>">
              <div class="select-box">
                <input type="checkbox" class="select-service" data-price="<?= htmlspecialchars($item['preco']) ?>" aria-label="Selecionar serviço para pagamento">
              </div>
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
            <a href="<?php echo BASE_URL; ?>/src/pages/servicos/marketplace.php" class="btn-primary">Ver serviços</a>
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

          <button type="button" class="btn-ghost btn-continue" onclick="window.location.href='<?php echo BASE_URL; ?>/src/pages/servicos/marketplace.php'">
            Continuar comprando
          </button>
        </div>
      </aside>

    </section>
  </div>

  <!-- Modal de confirmação -->
  <div class="modal" id="confirmModal" style="display: none;">
    <div class="modal-card">
      <h3>Confirmar compra</h3>
      <p>Deseja finalizar a compra dos serviços selecionados?</p>
      <div class="modal-actions">
        <button type="button" class="btn-ghost" onclick="closeModal()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="finalizarCompra()">Confirmar</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('confirmModal');
      const checkboxes = document.querySelectorAll('.select-service');
      const totalElement = document.getElementById('total');
      const itemsCountElement = document.getElementById('items-count');
      const btnCheckout = document.getElementById('finalizar-compra');

      // Atualiza o total e contagem sempre que uma checkbox muda
      function updateTotal() {
        let total = 0;
        let count = 0;

        checkboxes.forEach(checkbox => {
          if (checkbox.checked) {
            total += parseFloat(checkbox.dataset.price);
            count++;
          }
        });

        totalElement.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        itemsCountElement.textContent = count;

        // Habilita/desabilita botão de checkout
        btnCheckout.disabled = count === 0;
        if (count === 0) {
          btnCheckout.style.opacity = '0.5';
          btnCheckout.style.cursor = 'not-allowed';
        } else {
          btnCheckout.style.opacity = '1';
          btnCheckout.style.cursor = 'pointer';
        }
      }

      // Adiciona listeners para checkboxes
      checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);
      });

      // Remover item do carrinho
      document.querySelectorAll('.item-remove').forEach(btn => {
        btn.addEventListener('click', async function() {
          const cartId = this.dataset.id;
          const cartItem = this.closest('.cart-item');

          try {
            const response = await fetch(`${window.location.origin}/greenhelp-app/src/actions/remover_do_carrinho.php`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `cart_id=${cartId}`
            });

            const data = await response.json();

            if (data.success) {
              cartItem.remove();
              updateTotal();

              // Se não houver mais itens, recarrega a página
              if (document.querySelectorAll('.cart-item').length === 0) {
                window.location.reload();
              }
            } else {
              alert(data.message || 'Erro ao remover item');
            }
          } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao remover item do carrinho');
          }
        });
      });

      // Mostrar modal ao clicar em finalizar compra
      btnCheckout.addEventListener('click', function() {
        if (!this.disabled) {
          modal.style.display = 'grid';
        }
      });

      // Fechar modal
      window.closeModal = function() {
        modal.style.display = 'none';
      };

      // Finalizar compra
      window.finalizarCompra = async function() {
        const selectedItems = [];
        document.querySelectorAll('.cart-item').forEach(item => {
          const checkbox = item.querySelector('.select-service');
          if (checkbox && checkbox.checked) {
            selectedItems.push(item.dataset.id);
          }
        });

        if (selectedItems.length === 0) {
          alert('Selecione pelo menos um serviço');
          return;
        }

        try {
          const response = await fetch(`${window.location.origin}/greenhelp-app/src/actions/finalizar_compra.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              items: selectedItems
            })
          });

          const data = await response.json();

          if (data.success) {
            // Marca itens como processados e remove controles
            data.purchased_ids.forEach(id => {
              const item = document.querySelector(`.cart-item[data-id="${id}"]`);
              if (item) {
                item.classList.add('purchased');
                const checkbox = item.querySelector('.select-service');
                if (checkbox) checkbox.disabled = true;
                const removeBtn = item.querySelector('.item-remove');
                if (removeBtn) removeBtn.remove();
              }
            });

            // Redireciona para home cliente
            window.location.href = `${window.location.origin}/greenhelp-app/src/pages/home/home_cliente.php`;
          } else {
            alert(data.message || 'Erro ao finalizar compra');
          }
        } catch (error) {
          console.error('Erro:', error);
          alert('Erro ao processar a compra');
        }
      };

      // Fechar modal se clicar fora
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeModal();
        }
      });

      // Inicializa total
      updateTotal();
    });
  </script>

  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>
</body>

</html>