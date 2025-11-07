function inicializarFiltros() {
  const areaButtons = document.querySelectorAll('.area-icon');
  const servicos = document.querySelectorAll('.service-card');
  const selectedAreaSpan = document.getElementById('selected-area');
  const btnLimpar = document.getElementById('btn-limpar');
  const areasAtivas = new Set();

  function showAllServices() {
    servicos.forEach(card => card.style.display = 'flex');
    if (selectedAreaSpan) selectedAreaSpan.textContent = 'Todos';
  }

  function filterServices() {
    if (areasAtivas.size === 0) {
      showAllServices();
      return;
    }
    servicos.forEach(card => {
      const cardArea = card.getAttribute('data-area');
      card.style.display = areasAtivas.has(cardArea) ? 'flex' : 'none';
    });
    if (selectedAreaSpan)
      selectedAreaSpan.textContent = Array.from(areasAtivas).join(', ');
  }

  function resetFilters() {
    areasAtivas.clear();
    areaButtons.forEach(b => b.classList.remove('active'));
    showAllServices();
  }

  showAllServices();
  if (btnLimpar) btnLimpar.addEventListener('click', resetFilters);

  areaButtons.forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const areaSelecionada = btn.getAttribute('data-area');
      if (!areaSelecionada) return;

      if (btn.classList.contains('active')) {
        btn.classList.remove('active');
        areasAtivas.delete(areaSelecionada);
      } else {
        btn.classList.add('active');
        areasAtivas.add(areaSelecionada);
      }

      filterServices();
    });
  });
}

document.addEventListener('DOMContentLoaded', inicializarFiltros);
