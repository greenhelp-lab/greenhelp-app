const HOME_API = '/greenhelp-app/src/controllers/home';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formEmpresa');
  const editButton = document.getElementById('btnEditar');
  const saveButton = document.getElementById('btnSalvar');
  const logoButton = document.getElementById('btnLogo');
  const logoImage = document.getElementById('imgLogo');
  const logoInput = document.getElementById('inpLogo');

  const fields = {
    empresa: document.getElementById('empresa'),
    cnpj: document.getElementById('cnpj'),
    perfil: document.getElementById('perfil'),
    industria: document.getElementById('industria'),
    endereco: document.getElementById('endereco')
  };

  if (!form || !editButton || !saveButton) return;

  function setEditing(active) {
    Object.values(fields).forEach(input => {
      if (input) input.readOnly = !active;
    });

    if (logoButton) logoButton.disabled = !active;
    saveButton.disabled = false;
    editButton.disabled = active;
    if (active && fields.empresa) fields.empresa.focus();
  }

  async function loadCompany() {
    try {
      const response = await fetch(`${HOME_API}/read_empresa_controller.php`, {
        credentials: 'include'
      });

      if (!response.ok) throw new Error('HTTP ' + response.status);

      const data = await response.json();
      if (!data.ok || !data.empresa) return;

      const empresa = data.empresa;
      fields.empresa.value = empresa.nome || '';
      fields.cnpj.value = empresa.cnpj || '';
      fields.perfil.value = empresa.porte || '';
      fields.industria.value = empresa.setor_atuacao || '';
      fields.endereco.value = empresa.endereco || '';
    } catch (error) {
      console.error(error);
    }
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
        alert('JPG/PNG/WEBP');
        logoInput.value = '';
        return;
      }

      if (file.size > maxSize) {
        alert('Até 3MB');
        logoInput.value = '';
        return;
      }

      const previewUrl = URL.createObjectURL(file);
      logoImage.src = previewUrl;
      logoImage.onload = () => URL.revokeObjectURL(previewUrl);

      try {
        const url = await uploadLogo(file);
        logoImage.src = url + '?t=' + Date.now();
      } catch (error) {
        alert('Falha no upload: ' + error.message);
        logoInput.value = '';
      }
    });
  }

  editButton.addEventListener('click', () => setEditing(true));

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
      alert('Informe o nome da empresa.');
      fields.empresa.focus();
      return;
    }

    try {
      saveButton.disabled = true;
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
        alert('Erro ao salvar: ' + (data.error || response.status));
        return;
      }

      setEditing(false);
    } catch (error) {
      alert('Falha ao salvar: ' + error.message);
    } finally {
      saveButton.disabled = false;
    }
  });

  loadCompany();
  setEditing(false);
});
