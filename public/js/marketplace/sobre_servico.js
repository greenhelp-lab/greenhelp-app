document.addEventListener('DOMContentLoaded', () => {
  if (document.body.dataset.isAdmin !== '1') return;

  document.querySelectorAll('.cta-area button').forEach(button => {
    button.addEventListener('click', event => event.preventDefault());
    button.style.opacity = '0.5';
    button.style.cursor = 'not-allowed';
  });
});
