document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const form = document.getElementById('formConta');
  if (!form) return;

  const editButton = document.getElementById('btnEditar');
  const saveButton = document.getElementById('btnSalvar');
  const deleteButton = document.getElementById('btnDelete');
  const feedback = window.GreenHelpFeedback;
  const inputs = {
    nome: document.getElementById('inpNome'),
    tel: document.getElementById('inpTel'),
    email: document.getElementById('inpEmail'),
    ativo: document.getElementById('inpAtivo')
  };

  function setEditing(active) {
    inputs.nome.readOnly = !active;
    inputs.tel.readOnly = !active;
    inputs.email.readOnly = !active;
    inputs.ativo.readOnly = !active;
    editButton.disabled = active;
    if (active) inputs.nome.focus();
  }

  function notify(message, type) {
    if (feedback) {
      feedback.notify(message, type);
      return;
    }

    window.alert(message);
  }

  editButton.addEventListener('click', () => setEditing(true));

  form.addEventListener('submit', async event => {
    event.preventDefault();

    const payload = {
      id: form.querySelector('input[name="id"]').value,
      nome: inputs.nome.value.trim(),
      telefone: inputs.tel.value.trim(),
      email: inputs.email.value.trim(),
      ativo: inputs.ativo.value.trim()
    };

    if (!payload.nome || !payload.email) {
      notify('Preencha nome e email.', 'warning');
      return;
    }

    try {
      saveButton.disabled = true;
      const response = await fetch(`${baseUrl}/src/controllers/painel_admin/atualizar_admin_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload),
        credentials: 'include'
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.ok) throw new Error(data.error || 'Erro ao salvar');

      setEditing(false);
      notify('Admin atualizado com sucesso.', 'success');
    } catch (error) {
      notify('Falha ao salvar: ' + error.message, 'error');
    } finally {
      saveButton.disabled = false;
    }
  });

  deleteButton.addEventListener('click', async () => {
    const confirmed = feedback
      ? await feedback.confirm('Tem certeza que deseja excluir este admin? Esta ação não pode ser desfeita.', {
        title: 'Excluir admin',
        confirmText: 'Excluir'
      })
      : window.confirm('Tem certeza que deseja excluir este admin? Esta ação não pode ser desfeita.');

    if (!confirmed) return;

    try {
      const id = form.querySelector('input[name="id"]').value;
      const response = await fetch(`${baseUrl}/src/controllers/painel_admin/deletar_admin_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id }),
        credentials: 'include'
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.ok) throw new Error(data.error || 'Erro ao deletar');

      window.location.href = `${baseUrl}/src/pages/painel_admin/painel_admin.php`;
    } catch (error) {
      notify('Falha ao deletar: ' + error.message, 'error');
    }
  });

  setEditing(false);
});
