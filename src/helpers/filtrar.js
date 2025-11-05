// filtrar.js — script simples para filtrar cards por área
document.addEventListener('DOMContentLoaded', function () {
  var areaButtons = document.querySelectorAll('.area-icon');
  var servicos = document.querySelectorAll('.service-card');
  var selectedAreaSpan = document.getElementById('selected-area');
  var btnLimpar = document.getElementById('btn-limpar');
  var areasAtivas = new Set();

  // mostra todos os cards e atualiza o texto
  function showAllServices() {
    servicos.forEach(function (card) { card.style.display = 'flex'; });
    if (selectedAreaSpan) selectedAreaSpan.textContent = 'Todos';
  }

  // aplica o filtro atual baseado no conjunto areasAtivas
  function filterServices() {
    if (areasAtivas.size === 0) {
      showAllServices();
      return;
    }

    servicos.forEach(function (card) {
      var cardArea = card.getAttribute('data-area');
      card.style.display = areasAtivas.has(cardArea) ? 'flex' : 'none';
    });
    if (selectedAreaSpan) selectedAreaSpan.textContent = Array.from(areasAtivas).join(', ');
  }

  // limpa filtros
  function resetFilters() {
    areasAtivas.clear();
    areaButtons.forEach(function (b) { b.classList.remove('active'); });
    showAllServices();
  }

  // guarda estado inicial e adiciona listeners
  showAllServices();
  if (btnLimpar) btnLimpar.addEventListener('click', resetFilters);

  areaButtons.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var areaSelecionada = btn.getAttribute('data-area');
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
});