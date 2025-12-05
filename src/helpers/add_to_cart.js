(function () {
  'use strict';

  function handleClick(event) {
    // procura o botão .btn-add-cart mais próximo do ponto clicado
    var btn = event.target && event.target.closest && event.target.closest('.btn-add-cart');
    if (!btn) return;

    var servicoId = btn.dataset.id;
    if (!servicoId) return; // id inválido

    // evita clique duplo: se já está desabilitado
    if (btn.disabled) return;

    // feedback visual 
    btn.disabled = true;
    var prevText = btn.innerText;
    btn.innerText = 'Adicionando...';

    var form = new URLSearchParams();
    form.append('servico_id', servicoId);

    // requisição para o servidor
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
        console.error('add_to_cart error', err);
        alert('Erro de rede. Tente novamente.');
        btn.innerText = prevText;
        btn.disabled = false;
      });
  }

  document.addEventListener('click', handleClick, false);
})();
