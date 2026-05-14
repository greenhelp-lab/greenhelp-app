document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = document.body.dataset.baseUrl || '/greenhelp-app';
  const form = document.getElementById('formConta');
  const photoButton = document.getElementById('btnFoto');
  const photo = document.getElementById('fotoUsuario');
  const fileInput = document.getElementById('inpFoto');
  const editButton = document.getElementById('btnEditar');
  const saveButton = document.getElementById('btnSalvar');
  const deleteButton = document.getElementById('btnDelete');

  if (!form || !photoButton || !photo || !fileInput || !editButton || !saveButton || !deleteButton) return;

  const inputs = {
    nome: document.getElementById('inpNome'),
    tel: document.getElementById('inpTel'),
    email: document.getElementById('inpEmail')
  };

  function setEditing(active) {
    inputs.nome.readOnly = !active;
    inputs.tel.readOnly = !active;
    inputs.email.readOnly = !active;
    photoButton.disabled = !active;
    editButton.disabled = active;
    if (active) inputs.nome.focus();
  }

  editButton.addEventListener('click', () => setEditing(true));

  form.addEventListener('submit', async event => {
    event.preventDefault();

    const payload = {
      nome: inputs.nome.value.trim(),
      telefone: inputs.tel.value.trim(),
      email: inputs.email.value.trim()
    };

    if (!payload.nome || !payload.email) {
      alert('Preencha nome e email.');
      return;
    }

    try {
      saveButton.disabled = true;
      const response = await fetch(`${baseUrl}/src/controllers/home/update_usuario_controller.php`, {
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
    } catch (error) {
      alert('Falha ao salvar: ' + error.message);
    } finally {
      saveButton.disabled = false;
    }
  });

  deleteButton.addEventListener('click', async () => {
    if (!confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) return;

    try {
      const response = await fetch(`${baseUrl}/src/controllers/home/delete_usuario_controller.php`, {
        method: 'POST',
        credentials: 'include'
      });

      const text = await response.text();
      let data = {};

      try {
        data = JSON.parse(text);
      } catch {
        data = {};
      }

      if (!response.ok || !data.ok) {
        throw new Error(data.error || text || `HTTP ${response.status}`);
      }

      window.location.href = data.redirect || `${baseUrl}/src/pages/login/login.php`;
    } catch (error) {
      alert('Falha ao deletar conta: ' + error.message);
    }
  });

  photoButton.addEventListener('click', () => fileInput.click());

  fileInput.addEventListener('change', async () => {
    const file = fileInput.files?.[0];
    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxSize = 3 * 1024 * 1024;

    if (!file) return;

    if (!validTypes.includes(file.type)) {
      alert('JPG/PNG/WEBP');
      fileInput.value = '';
      return;
    }

    if (file.size > maxSize) {
      alert('Até 3MB');
      fileInput.value = '';
      return;
    }

    const previewUrl = URL.createObjectURL(file);
    photo.src = previewUrl;
    photo.onload = () => URL.revokeObjectURL(previewUrl);

    const formData = new FormData();
    formData.append('foto', file);

    try {
      const response = await fetch(`${baseUrl}/src/controllers/home/upload_avatar_controller.php`, {
        method: 'POST',
        body: formData,
        credentials: 'include'
      });

      const text = await response.text();
      let data = {};

      try {
        data = JSON.parse(text);
      } catch {
        throw new Error(text || `HTTP ${response.status}`);
      }

      if (!response.ok || !data.ok) {
        throw new Error(data.error || text || `HTTP ${response.status}`);
      }

      if (data.url) {
        photo.src = data.url + '?t=' + Date.now();
      }
    } catch (error) {
      alert('Falha no upload: ' + error.message);
      fileInput.value = '';
    }
  });

  setEditing(false);
});
