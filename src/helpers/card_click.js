(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.querySelector('.service-cards');
    if (!container) return;

    container.addEventListener('click', function (e) {
      if (e.target.closest && e.target.closest('.btn-add-cart')) return;

      let card = e.target.closest && e.target.closest('.service-card');
      if (!card) return;

      let id = card.getAttribute('data-id');
      if (!id) return;

      window.location.href = '/greenhelp-app/src/pages/marketplace/sobre_servico.php?id=' + encodeURIComponent(id);
    });
  });
})();
