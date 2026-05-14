document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const controllerUrl = `${baseUrl}/src/controllers/painel_admin/`;

  const routes = {
    'create-service-btn': `${baseUrl}/src/pages/painel_admin/criar_servico.php`,
    'create-admin-btn': `${baseUrl}/src/pages/painel_admin/criar_admin.php`,
    'create-client-btn': `${baseUrl}/src/pages/painel_admin/criar_cliente.php`
  };

  Object.entries(routes).forEach(([id, url]) => {
    const button = document.getElementById(id);
    if (button) {
      button.addEventListener('click', () => {
        window.location.href = url;
      });
    }
  });

  function bindSearch(inputId, listId, endpoint, errorMessage) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);

    if (!input || !list) return;

    input.addEventListener('input', () => {
      const term = input.value.trim();
      const url = `${controllerUrl}${endpoint}?q=${encodeURIComponent(term)}`;

      fetch(url)
        .then(response => response.text())
        .then(html => {
          list.innerHTML = html;
        })
        .catch(error => console.error(errorMessage, error));
    });
  }

  bindSearch('client-search', 'clients-list', 'listar_clientes.php', 'Erro ao buscar clientes:');
  bindSearch('admin-search', 'admins-list', 'listar_admins.php', 'Erro ao buscar administradores:');
  bindSearch('service-search', 'services-list', 'listar_servicos_admin.php', 'Erro ao buscar serviços:');
});
