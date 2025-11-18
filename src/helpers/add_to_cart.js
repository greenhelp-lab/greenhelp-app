// add_to_cart.js
// Escuta cliques em botões com a classe `.btn-add-cart` e envia POST para o endpoint
(function () {
  'use strict';

  /**
   * handleClick: função que trata o clique no botão de adicionar ao carrinho
   * - encontra o botão mais próximo a partir do event.target
   * - evita clique duplo
   * - mostra texto de progresso e faz fetch para o endpoint PHP
   */
  function handleClick(event) {
    // procura o botão .btn-add-cart mais próximo do ponto clicado
    var btn = event.target && event.target.closest && event.target.closest('.btn-add-cart');
    if (!btn) return; // não é um clique para adicionar

    var servicoId = btn.dataset.id;
    if (!servicoId) return; // id inválido

    // evita clique duplo: se já está desabilitado, sai
    if (btn.disabled) return;

    // feedback visual simples
    btn.disabled = true;
    var prevText = btn.innerText;
    btn.innerText = 'Adicionando...';

    // prepara dados do POST (form-urlencoded)
    var form = new URLSearchParams();
    form.append('servico_id', servicoId);

    // envia requisição para o servidor
    fetch('/greenhelp-app/src/controllers/marketplace/adicionar_ao_carrinho_controller.php', {
      method: 'POST',
      body: form,
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data && data.success) {
          // sucesso: marca botão como adicionado
          btn.innerText = 'Adicionado';
          btn.disabled = true;
          btn.classList.add('added');
        } else {
          // falha: mostra mensagem e restaura botão
          var msg = (data && data.message) ? data.message : 'Erro ao adicionar ao carrinho.';
          alert(msg);
          btn.innerText = prevText;
          btn.disabled = false;
        }
      })
      .catch(function (err) {
        // erro de rede
        console.error('add_to_cart error', err);
        alert('Erro de rede. Tente novamente.');
        btn.innerText = prevText;
        btn.disabled = false;
      });
  }

  // Delegação simples: escuta todos os cliques do documento e processa se for .btn-add-cart
  document.addEventListener('click', handleClick, false);
})();
