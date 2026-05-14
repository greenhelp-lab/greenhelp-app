document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const empresasDataElement = document.getElementById('empresas-data');
  const empresasData = empresasDataElement ? JSON.parse(empresasDataElement.textContent || '[]') : [];
  const empresasMap = {};

  empresasData.forEach(empresa => {
    empresasMap[String(empresa.id)] = empresa;
  });

  const form = document.getElementById('formConta');
  if (!form) return;

  const editButton = document.getElementById('btnEditar');
  const saveButton = document.getElementById('btnSalvar');
  const deleteButton = document.getElementById('btnDelete');
  const empresaSelect = document.getElementById('empresa_select');

  const inputs = {
    nome: document.getElementById('inpNome'),
    tel: document.getElementById('inpTel'),
    email: document.getElementById('inpEmail'),
    ativo: document.getElementById('inpAtivo')
  };

  const empresaInputs = {
    nome: document.getElementById('empresa_nome'),
    cnpj: document.getElementById('empresa_cnpj'),
    setor: document.getElementById('empresa_setor'),
    porte: document.getElementById('empresa_porte')
  };

  let editingMode = false;

  function clearEmpresaFields() {
    empresaInputs.nome.value = '';
    empresaInputs.cnpj.value = '';
    empresaInputs.setor.value = '';
    empresaInputs.porte.value = '';
  }

  function populateEmpresaFieldsById(id) {
    if (!id || id === 'new') {
      clearEmpresaFields();
      return;
    }

    const empresa = empresasMap[String(id)];
    if (!empresa) {
      clearEmpresaFields();
      return;
    }

    empresaInputs.nome.value = empresa.nome || '';
    empresaInputs.cnpj.value = empresa.cnpj || '';
    empresaInputs.setor.value = empresa.setor_atuacao || '';
    empresaInputs.porte.value = empresa.porte || '';
  }

  function updateEmpresaFieldsEditable(active) {
    Object.values(empresaInputs).forEach(input => {
      input.readOnly = !active;
    });
  }

  function setEditing(active) {
    editingMode = active;
    inputs.nome.readOnly = !active;
    inputs.tel.readOnly = !active;
    inputs.email.readOnly = !active;
    inputs.ativo.readOnly = !active;
    editButton.disabled = active;
    empresaSelect.disabled = !active;
    updateEmpresaFieldsEditable(active);
    if (active) inputs.nome.focus();
  }

  if (empresaSelect) {
    populateEmpresaFieldsById(empresaSelect.value || document.body.dataset.empresaId || '');
    empresaSelect.addEventListener('change', event => {
      populateEmpresaFieldsById(event.target.value);
      updateEmpresaFieldsEditable(editingMode);
    });
  }

  editButton.addEventListener('click', () => setEditing(true));

  form.addEventListener('submit', async event => {
    event.preventDefault();

    const payload = {
      id: form.querySelector('input[name="id"]').value,
      nome: inputs.nome.value.trim(),
      telefone: inputs.tel.value.trim(),
      email: inputs.email.value.trim(),
      ativo: inputs.ativo.value.trim(),
      empresa_id: empresaSelect ? empresaSelect.value : null,
      empresa_data: {
        nome: empresaInputs.nome.value || '',
        cnpj: empresaInputs.cnpj.value || '',
        setor: empresaInputs.setor.value || '',
        porte: empresaInputs.porte.value || ''
      }
    };

    try {
      saveButton.disabled = true;

      const response = await fetch(`${baseUrl}/src/controllers/painel_admin/atualizar_cliente_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify(payload)
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.ok) throw new Error(data.error || 'Erro ao salvar');

      setEditing(false);
      alert('Cliente atualizado com sucesso!');
    } catch (error) {
      alert('Falha ao salvar: ' + error.message);
    } finally {
      saveButton.disabled = false;
    }
  });

  deleteButton.addEventListener('click', async () => {
    if (!confirm('Deseja excluir este cliente?')) return;

    try {
      const id = form.querySelector('input[name="id"]').value;

      const response = await fetch(`${baseUrl}/src/controllers/painel_admin/deletar_cliente_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ id })
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.ok) throw new Error(data.error || 'Erro ao deletar');

      window.location.href = `${baseUrl}/src/pages/painel_admin/painel_admin.php`;
    } catch (error) {
      alert('Falha ao deletar: ' + error.message);
    }
  });

  document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', async event => {
      const serviceId = Number(event.target.dataset.servicoId);
      const newStatus = event.target.value;
      const oldStatus = event.target.dataset.currentStatus;

      try {
        const response = await fetch(`${baseUrl}/src/controllers/painel_admin/atualizar_status_servico_controller.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          credentials: 'include',
          body: JSON.stringify({
            id: serviceId,
            status: newStatus
          })
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.ok) throw new Error(data.error || 'Erro ao atualizar');

        event.target.dataset.currentStatus = newStatus;
      } catch (error) {
        alert('Falha ao atualizar status: ' + error.message);
        event.target.value = oldStatus;
      }
    });
  });

  setEditing(false);
});
