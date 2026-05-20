(function () {
  const timers = new Map();

  function root() {
    let element = document.querySelector('.app-toast-region');
    if (element) return element;

    element = document.createElement('div');
    element.className = 'app-toast-region';
    element.setAttribute('aria-live', 'polite');
    element.setAttribute('aria-atomic', 'true');
    document.body.appendChild(element);
    return element;
  }

  function notify(message, type, options) {
    const settings = options || {};
    const toast = document.createElement('div');
    toast.className = `app-toast app-toast-${type || 'info'}`;
    toast.innerHTML = `<strong>${settings.title || titleByType(type)}</strong><span>${message}</span>`;
    root().appendChild(toast);

    const timeout = window.setTimeout(() => close(toast), settings.duration || 3200);
    timers.set(toast, timeout);

    toast.addEventListener('click', () => close(toast));
    return toast;
  }

  function close(toast) {
    if (!toast || toast.classList.contains('is-leaving')) return;
    window.clearTimeout(timers.get(toast));
    timers.delete(toast);
    toast.classList.add('is-leaving');
    window.setTimeout(() => toast.remove(), 180);
  }

  function titleByType(type) {
    if (type === 'success') return 'Tudo certo';
    if (type === 'error') return 'Algo deu errado';
    if (type === 'warning') return 'Atenção';
    return 'Aviso';
  }

  function confirmAction(message, options) {
    const settings = options || {};

    return new Promise(resolve => {
      const overlay = document.createElement('div');
      overlay.className = 'app-confirm-overlay';
      overlay.innerHTML = `
        <section class="app-confirm-box" role="dialog" aria-modal="true">
          <h2>${settings.title || 'Confirmar ação'}</h2>
          <p>${message}</p>
          <div class="app-confirm-actions">
            <button type="button" class="app-confirm-cancel">${settings.cancelText || 'Cancelar'}</button>
            <button type="button" class="app-confirm-ok">${settings.confirmText || 'Confirmar'}</button>
          </div>
        </section>
      `;

      document.body.appendChild(overlay);

      const finish = value => {
        overlay.classList.add('is-leaving');
        window.setTimeout(() => overlay.remove(), 160);
        resolve(value);
      };

      overlay.querySelector('.app-confirm-cancel').addEventListener('click', () => finish(false));
      overlay.querySelector('.app-confirm-ok').addEventListener('click', () => finish(true));
      overlay.addEventListener('click', event => {
        if (event.target === overlay) finish(false);
      });
    });
  }

  window.GreenHelpFeedback = {
    notify,
    confirm: confirmAction
  };
})();
