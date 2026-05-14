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

  if (!cartContainer || !totalElement || !itemsCountElement || !checkoutButton) return;

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
        alert(data.message || 'Erro ao remover item');
        return;
      }

      cartItem.remove();
      updateTotal();

      if (document.querySelectorAll('.cart-item').length === 0) {
        window.location.reload();
      }
    } catch (error) {
      alert('Erro ao remover item do carrinho');
    }
  }

  async function finishOrder() {
    const items = selectedItems().map(item => item.dataset.id);

    if (items.length === 0) {
      alert('Selecione pelo menos um serviço');
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
        alert('Erro no servidor ao finalizar compra');
        return;
      }

      const data = await response.json();

      if (data.success) {
        window.location.href = `${baseUrl}/src/pages/home/home.php`;
        return;
      }

      alert(data.message || 'Erro ao finalizar compra');
    } catch (error) {
      alert('Erro ao processar a compra');
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
