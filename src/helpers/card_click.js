/**
 * card_click.js — lógica simples para abrir a página de detalhe do serviço
 * - Usa delegação: escuta cliques no container `.service-cards`.
 * - Se o clique estiver dentro de `.btn-add-cart`, NÃO navega (permite adicionar ao carrinho).
 * - Se o clique atingir um `.service-card`, lê `data-id` e vai para sobre_servico.php?id=...
 * Comentários em português e código intencionalmente curto e direto.
 */
(function () {
  'use strict';

  // evita rodar antes do DOM estar pronto
  document.addEventListener('DOMContentLoaded', function () {
    // container onde os cards são inseridos (presente em marketplace.php)
    var container = document.querySelector('.service-cards');
    if (!container) return; // nada a fazer se não existir

    container.addEventListener('click', function (e) {
      // se clicou no botão de adicionar ao carrinho (ou em seu filho), não navegar
      if (e.target.closest && e.target.closest('.btn-add-cart')) return;

      // procura o card pai mais próximo
      var card = e.target.closest && e.target.closest('.service-card');
      if (!card) return; // clique fora de um card

      // pega o id do serviço armazenado no atributo data-id
      var id = card.getAttribute('data-id');
      if (!id) return;

      // navega para a página de detalhe (id na querystring)
      window.location.href = '/greenhelp-app/src/pages/servicos/sobre_servico.php?id=' + encodeURIComponent(id);
    });
  });
})();
