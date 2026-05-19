document.addEventListener('DOMContentLoaded', () => {
  const empresaSelect = document.getElementById('empresa_id');
  const newFields = document.getElementById('new-company-fields');
  const form = document.getElementById('criarClienteForm');

  if (!empresaSelect || !newFields || !form) return;

  function toggleCompanyFields() {
    const creatingCompany = empresaSelect.value === 'new';
    newFields.style.display = creatingCompany ? 'block' : 'none';
    newFields.querySelectorAll('input').forEach(input => {
      input.required = creatingCompany;
    });
  }

  empresaSelect.addEventListener('change', toggleCompanyFields);
  toggleCompanyFields();

  form.addEventListener('submit', event => {
    if (empresaSelect.value !== 'new') return;

    const nome = document.getElementById('empresa_nome').value.trim();
    if (!nome) {
      event.preventDefault();
      alert('Preencha o nome da empresa/associação ao criar uma nova.');
    }
  });
});
