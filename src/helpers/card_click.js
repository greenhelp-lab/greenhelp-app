(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // container onde os cards são inseridos
    var container = document.querySelector('.service-cards');
    if (!container) return; // nada a fazer se não existir

    container.addEventListener('click', function (e) {
      // se clicou no botão de adicionar ao carrinho (ou em seu filho), não navegar
      if (e.target.closest && e.target.closest('.btn-add-cart')) return;

      // procura o card pai mais próximo
      let card = e.target.closest && e.target.closest('.service-card');
      if (!card) return; // clique fora de um card

      // pega o id do serviço armazenado no atributo data-id
      let id = card.getAttribute('data-id');
      if (!id) return;

      // navega para a página de detalhe (id na querystring)
      window.location.href = '/greenhelp-app/src/pages/marketplace/sobre_servico.php?id=' + encodeURIComponent(id);
    });
  });
})();
