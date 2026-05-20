const HOME_API = '/greenhelp-app/src/controllers/home';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formEmpresa');
  const editButton = document.getElementById('btnEditar');
  const saveButton = document.getElementById('btnSalvar');
  const editButtons = Array.from(document.querySelectorAll('#btnEditar, [data-edit-trigger]'));
  const saveButtons = Array.from(document.querySelectorAll('#btnSalvar, [data-save-trigger]'));
  const logoButton = document.getElementById('btnLogo');
  const logoImage = document.getElementById('imgLogo');
  const logoInput = document.getElementById('inpLogo');
  const feedback = window.GreenHelpFeedback;

  const fields = {
    empresa: document.getElementById('empresa'),
    cnpj: document.getElementById('cnpj'),
    perfil: document.getElementById('perfil'),
    industria: document.getElementById('industria'),
    endereco: document.getElementById('endereco')
  };

  if (!form || !editButton || !saveButton) return;

  function notify(message, type) {
    if (feedback) {
      feedback.notify(message, type);
      return;
    }

    window.alert(message);
  }

  function setEditing(active) {
    Object.values(fields).forEach(input => {
      if (input) input.readOnly = !active;
    });

    if (logoButton) logoButton.disabled = !active;
    saveButtons.forEach(button => {
      button.disabled = false;
    });
    editButtons.forEach(button => {
      button.disabled = active;
    });
    if (active && fields.empresa) fields.empresa.focus();
  }

  async function loadCompany() {
    try {
      const response = await fetch(`${HOME_API}/read_empresa_controller.php`, {
        credentials: 'include'
      });

      if (!response.ok) return;

      const data = await response.json();
      if (!data.ok || !data.empresa) return;

      const empresa = data.empresa;
      fields.empresa.value = empresa.nome || '';
      fields.cnpj.value = empresa.cnpj || '';
      fields.perfil.value = empresa.porte || '';
      fields.industria.value = empresa.setor_atuacao || '';
      fields.endereco.value = empresa.endereco || '';
    } catch (error) {}
  }

  async function uploadLogo(file) {
    const formData = new FormData();
    formData.append('logo', file);

    const response = await fetch(`${HOME_API}/upload_logo_controller.php`, {
      method: 'POST',
      body: formData,
      credentials: 'include'
    });

    const text = await response.text();
    let data = {};

    try {
      data = JSON.parse(text);
    } catch {
      throw new Error(text || ('HTTP ' + response.status));
    }

    if (!response.ok || !data?.url) {
      throw new Error(data.msg || data.error || ('HTTP ' + response.status));
    }

    return data.url;
  }

  if (logoButton && logoInput && logoImage) {
    logoButton.addEventListener('click', () => logoInput.click());

    logoInput.addEventListener('change', async () => {
      const file = logoInput.files?.[0];
      const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
      const maxSize = 3 * 1024 * 1024;

      if (!file) return;

      if (!validTypes.includes(file.type)) {
        notify('Use imagem JPG, PNG ou WEBP.', 'warning');
        logoInput.value = '';
        return;
      }

      if (file.size > maxSize) {
        notify('A imagem deve ter até 3MB.', 'warning');
        logoInput.value = '';
        return;
      }

      const previewUrl = URL.createObjectURL(file);
      logoImage.src = previewUrl;
      logoImage.onload = () => URL.revokeObjectURL(previewUrl);

      try {
        const url = await uploadLogo(file);
        logoImage.src = url + '?t=' + Date.now();
        notify('Logo da empresa atualizada.', 'success');
      } catch (error) {
        notify('Falha no upload: ' + error.message, 'error');
        logoInput.value = '';
      }
    });
  }

  editButtons.forEach(button => {
    button.addEventListener('click', () => setEditing(true));
  });

  form.addEventListener('submit', async event => {
    event.preventDefault();

    const payload = {
      nome: fields.empresa.value.trim(),
      cnpj: fields.cnpj.value.trim(),
      porte: fields.perfil.value.trim(),
      setor_atuacao: fields.industria.value.trim(),
      endereco: fields.endereco.value.trim()
    };

    if (!payload.nome) {
      notify('Informe o nome da empresa.', 'warning');
      fields.empresa.focus();
      return;
    }

    try {
      saveButtons.forEach(button => {
        button.disabled = true;
      });
      const response = await fetch(`${HOME_API}/update_empresa_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload),
        credentials: 'include'
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok || !data.ok) {
        notify('Erro ao salvar: ' + (data.error || response.status), 'error');
        return;
      }

      setEditing(false);
      notify('Dados da empresa atualizados.', 'success');
    } catch (error) {
      notify('Falha ao salvar: ' + error.message, 'error');
    } finally {
      saveButtons.forEach(button => {
        button.disabled = false;
      });
    }
  });

  loadCompany();
  setEditing(false);
});
