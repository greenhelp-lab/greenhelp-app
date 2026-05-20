document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const form = document.getElementById('formServico');
  if (!form) return;

  const editButton = document.getElementById('btnEditar');
  const saveButton = document.getElementById('btnSalvar');
  const deleteButton = document.getElementById('btnDelete');
  const feedback = window.GreenHelpFeedback;
  const inputs = {
    nome: document.getElementById('nome'),
    areaId: document.getElementById('area'),
    preco: document.getElementById('preco'),
    prazo: document.getElementById('prazo'),
    pontos: document.getElementById('pontos'),
    categoria: document.getElementById('categoria'),
    descricao: document.getElementById('descricao'),
    descricaoLonga: document.getElementById('descricao_longa'),
    itensIncluidos: document.getElementById('itens_incluidos'),
    garantia: document.getElementById('garantia'),
    contato: document.getElementById('contato'),
    disponivel: document.getElementById('disponivel')
  };

  function setEditing(active) {
    inputs.nome.readOnly = !active;
    inputs.areaId.disabled = !active;
    inputs.preco.readOnly = !active;
    inputs.prazo.readOnly = !active;
    inputs.pontos.readOnly = !active;
    inputs.categoria.readOnly = !active;
    inputs.descricao.readOnly = !active;
    inputs.descricaoLonga.readOnly = !active;
    inputs.itensIncluidos.readOnly = !active;
    inputs.garantia.readOnly = !active;
    inputs.contato.readOnly = !active;
    inputs.disponivel.readOnly = !active;
    editButton.style.display = active ? 'none' : 'inline-block';
    saveButton.style.display = active ? 'inline-block' : 'none';
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
      area_id: inputs.areaId.value,
      preco: inputs.preco.value,
      prazo: inputs.prazo.value,
      pontos: inputs.pontos.value,
      categoria: inputs.categoria.value.trim(),
      descricao: inputs.descricao.value.trim(),
      descricao_longa: inputs.descricaoLonga.value.trim(),
      itens_incluidos: inputs.itensIncluidos.value.trim(),
      garantia: inputs.garantia.value.trim(),
      contato: inputs.contato.value.trim(),
      disponivel: inputs.disponivel.value
    };

    if (!payload.nome) {
      notify('Preencha o nome do serviço.', 'warning');
      return;
    }

    try {
      saveButton.disabled = true;
      const response = await fetch(`${baseUrl}/src/controllers/painel_admin/atualizar_servico_controller.php`, {
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
      notify('Serviço atualizado com sucesso.', 'success');
    } catch (error) {
      notify('Falha ao salvar: ' + error.message, 'error');
    } finally {
      saveButton.disabled = false;
    }
  });

  deleteButton.addEventListener('click', async () => {
    const confirmed = feedback
      ? await feedback.confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.', {
        title: 'Excluir serviço',
        confirmText: 'Excluir'
      })
      : window.confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.');

    if (!confirmed) return;

    try {
      const id = form.querySelector('input[name="id"]').value;
      const response = await fetch(`${baseUrl}/src/controllers/painel_admin/deletar_servico_controller.php`, {
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
