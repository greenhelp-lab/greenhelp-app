document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const modal = document.getElementById('confirmModal');
  const cartContainer = document.getElementById('cartItems');
  const totalElement = document.getElementById('total');
  const itemsCountElement = document.getElementById('items-count');
  const checkoutButton = document.getElementById('finalizar-compra');
  const continueButton = document.getElementById('continuar-compra');
  const cancelButton = document.getElementById('cancelar-compra');
  const confirmButton = document.getElementById('confirmar-compra');
  const feedback = window.GreenHelpFeedback;

  if (!cartContainer || !totalElement || !itemsCountElement || !checkoutButton) return;

  function notify(message, type) {
    if (feedback) {
      feedback.notify(message, type);
      return;
    }

    window.alert(message);
  }

  function selectedItems() {
    return Array.from(document.querySelectorAll('.cart-item.selected-service'));
  }

  function updateTotal() {
    const items = selectedItems();
    const total = items.reduce((sum, item) => sum + Number(item.dataset.price || 0), 0);

    totalElement.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    itemsCountElement.textContent = items.length;
    checkoutButton.disabled = items.length === 0;
    checkoutButton.style.opacity = items.length === 0 ? '0.5' : '1';
    checkoutButton.style.cursor = items.length === 0 ? 'not-allowed' : 'pointer';
  }

  function closeModal() {
    if (modal) modal.style.display = 'none';
  }

  async function removeItem(button) {
    const cartId = button.dataset.id;
    const cartItem = button.closest('.cart-item');

    try {
      const response = await fetch(`${baseUrl}/src/controllers/marketplace/remover_do_carrinho_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `cart_id=${encodeURIComponent(cartId)}`
      });

      const data = await response.json();

      if (!data.success) {
        notify(data.message || 'Erro ao remover item.', 'error');
        return;
      }

      cartItem.remove();
      updateTotal();
      notify('Serviço removido do carrinho.', 'success');

      if (document.querySelectorAll('.cart-item').length === 0) {
        window.setTimeout(() => window.location.reload(), 700);
      }
    } catch (error) {
      notify('Erro ao remover item do carrinho.', 'error');
    }
  }

  async function finishOrder() {
    const items = selectedItems().map(item => item.dataset.id);

    if (items.length === 0) {
      notify('Selecione pelo menos um serviço.', 'warning');
      return;
    }

    try {
      const response = await fetch(`${baseUrl}/src/controllers/marketplace/finalizar_compra_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ items })
      });

      if (!response.ok) {
        notify('Erro no servidor ao finalizar compra.', 'error');
        return;
      }

      const data = await response.json();

      if (data.success) {
        closeModal();
        notify('Compra finalizada com sucesso.', 'success');
        window.setTimeout(() => {
          window.location.href = `${baseUrl}/src/pages/home/home.php`;
        }, 900);
        return;
      }

      notify(data.message || 'Erro ao finalizar compra.', 'error');
    } catch (error) {
      notify('Erro ao processar a compra.', 'error');
    }
  }

  cartContainer.addEventListener('click', event => {
    const removeButton = event.target.closest('.item-remove');
    if (removeButton) {
      event.stopPropagation();
      removeItem(removeButton);
      return;
    }

    const card = event.target.closest('.cart-item');
    if (!card) return;

    card.classList.toggle('selected-service');
    updateTotal();
  });

  checkoutButton.addEventListener('click', () => {
    if (!checkoutButton.disabled && modal) modal.style.display = 'grid';
  });

  if (continueButton) {
    continueButton.addEventListener('click', () => {
      window.location.href = `${baseUrl}/src/pages/marketplace/marketplace.php`;
    });
  }

  if (cancelButton) cancelButton.addEventListener('click', closeModal);
  if (confirmButton) confirmButton.addEventListener('click', finishOrder);

  if (modal) {
    modal.addEventListener('click', event => {
      if (event.target === modal) closeModal();
    });
  }

  updateTotal();
});
