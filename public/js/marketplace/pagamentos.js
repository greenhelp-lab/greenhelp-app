document.addEventListener('DOMContentLoaded', () => {
  const backButton = document.getElementById('btn-voltar');
  if (backButton) {
    backButton.addEventListener('click', () => history.back());
  }
});
