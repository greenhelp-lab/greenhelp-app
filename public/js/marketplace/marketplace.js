document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const cartButton = document.getElementById('cart-shortcut');
  const searchInput = document.getElementById('shop-search');
  const cardsContainer = document.querySelector('.service-cards');

  if (cartButton) {
    cartButton.addEventListener('click', () => {
      window.location.href = `${baseUrl}/src/pages/marketplace/carrinho.php`;
    });
  }

  if (!searchInput || !cardsContainer) return;

  searchInput.addEventListener('input', () => {
    const term = searchInput.value.trim();
    const url = `${baseUrl}/src/controllers/marketplace/listar_servicos.php?q=${encodeURIComponent(term)}`;

    fetch(url)
      .then(response => response.text())
      .then(html => {
        cardsContainer.innerHTML = html;
        if (typeof inicializarFiltros === 'function') inicializarFiltros();
      })
      .catch(error => console.error('Erro ao buscar serviços:', error));
  });
});
