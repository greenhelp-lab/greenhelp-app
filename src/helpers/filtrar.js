const areaButtons = document.querySelectorAll('.area-icon');
const servicos = document.querySelectorAll('.service-cards .service-card');
const selectedAreaSpan = document.getElementById('selected-area');

areaButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    const isActive = btn.classList.contains('active');
    const areaSelecionada = btn.getAttribute('data-area');
    const nomeArea = btn.textContent.trim();

    // Remove 'active' de todos
    areaButtons.forEach(b => b.classList.remove('active'));

    if (!isActive) {
      // Ativa o botão clicado
      btn.classList.add('active');

      // Mostra apenas os serviços da área selecionada
      servicos.forEach(card => {
        card.style.display =
          card.getAttribute('data-area') === areaSelecionada ? 'flex' : 'none';
      });

      // Atualiza o nome exibido
      selectedAreaSpan.textContent = nomeArea;
    } else {
      // Nenhum botão ativo → mostra todos os serviços
      servicos.forEach(card => (card.style.display = 'flex'));

      // Mostra "Todos" como área atual
      selectedAreaSpan.textContent = 'Todos';
    }
  });
});