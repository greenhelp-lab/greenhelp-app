document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const closeButton = document.getElementById('close-btn');
  const params = new URLSearchParams(window.location.search);

  if (closeButton) {
    closeButton.addEventListener('click', () => {
      window.location.href = `${baseUrl}/src/pages/painel_admin/painel_admin.php`;
    });
  }

  if (params.get('status') === 'success') {
    if (window.GreenHelpFeedback) {
      window.GreenHelpFeedback.notify('Registro salvo com sucesso.', 'success');
    } else {
      document.querySelector('.pop-salvo')?.classList.add('show');
    }
    setTimeout(() => {
      window.location.href = `${baseUrl}/src/pages/painel_admin/painel_admin.php`;
    }, 1600);
  }
});
