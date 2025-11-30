const UPLOAD_LOGO_URL = '/greenhelp-app/src/controllers/home/upload_logo_controller.php';

(async function () {
  const btn = document.getElementById('btnLogo');
  const img = document.getElementById('imgLogo');
  const inp = document.getElementById('inpLogo');
  const MAX = 3 * 1024 * 1024;
  const ok = ['image/jpeg', 'image/png', 'image/webp'];

  try {
    const res = await fetch('/greenhelp-app/src/controllers/home/read_empresa_controller.php', {
      credentials: 'include'
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const json = await res.json();
    if (json.ok && json.empresa) {
      const e = json.empresa;
      document.getElementById("empresa").value = e.nome || '';
      document.getElementById("cnpj").value = e.cnpj || '';
      document.getElementById("perfil").value = e.porte || '';
      document.getElementById("industria").value = e.setor_atuacao || '';
      document.getElementById("endereco").value = e.endereco || ''; // NOVO
    }
  } catch (err) {
    console.error('read_empresa_controller:', err);
  }

  btn.addEventListener('click', () => inp.click());
  inp.addEventListener('change', async () => {
    const file = inp.files?.[0];
    if (!file) return;
    if (!ok.includes(file.type)) {
      alert('JPG/PNG/WEBP');
      inp.value = '';
      return;
    }
    if (file.size > MAX) {
      alert('Até 3MB');
      inp.value = '';
      return;
    }

    const t = URL.createObjectURL(file);
    img.src = t;
    img.onload = () => URL.revokeObjectURL(t);

    const fd = new FormData();
    fd.append('logo', file);

    try {
      const r = await fetch(UPLOAD_LOGO_URL, {
        method: 'POST',
        body: fd,
        credentials: 'include'
      });

      const text = await r.text();
      let data = {};
      try {
        data = JSON.parse(text);
      } catch {
        throw new Error(text || ('HTTP ' + r.status));
      }

      if (!r.ok || !data?.url) {
        const msg = data.msg || data.error || ('HTTP ' + r.status);
        throw new Error(msg);
      }

      img.src = data.url + '?t=' + Date.now();
    } catch (e) {
      alert('Falha no upload: ' + e.message);
      inp.value = '';
    }
  });
})();

(async function () {
  const form = document.getElementById('formEmpresa');
  const btnEdit = document.getElementById('btnEditar');
  const btnSave = document.getElementById('btnSalvar');
  const btnLogo = document.getElementById('btnLogo');

  const f = {
    empresa: document.getElementById('empresa'),
    cnpj: document.getElementById('cnpj'),
    perfil: document.getElementById('perfil'),
    industria: document.getElementById('industria'),
    endereco: document.getElementById('endereco')
  };

  function setEditing(on) {
    Object.values(f).forEach(i => i.readOnly = !on);
    if (btnLogo) btnLogo.disabled = !on;
    btnSave.disabled = false;
    btnEdit.disabled = on;
    if (on) f.empresa.focus();
  }

  try {
    const res = await fetch(`/greenhelp-app/src/controllers/home/read_empresa_controller.php`, {
      credentials: 'include'
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const j = await res.json();
    if (j.ok && j.empresa) {
      const e = j.empresa;
      f.empresa.value = e.nome || '';
      f.cnpj.value = e.cnpj || '';
      f.perfil.value = e.porte || '';
      f.industria.value = e.setor_atuacao || '';
      f.endereco.value = e.endereco || '';
    }
  } catch (err) {
    console.error(err);
  }

  btnEdit.addEventListener('click', () => setEditing(true));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
      nome: f.empresa.value.trim(),
      cnpj: f.cnpj.value.trim(),
      porte: f.perfil.value.trim(),
      setor_atuacao: f.industria.value.trim(),
      endereco: f.endereco.value.trim()
    };
    if (!payload.nome) {
      alert('Informe o nome da empresa.');
      f.empresa.focus();
      return;
    }

    try {
      btnSave.disabled = true;
      const r = await fetch(`/greenhelp-app/src/controllers/home/update_empresa_controller.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload),
        credentials: 'include'
      });
      const j = await r.json().catch(() => ({}));
      if (!r.ok || !j.ok) {
        alert('Erro ao salvar: ' + (j.error || r.status));
        return;
      }
      setEditing(false);
    } catch (e) {
      alert('Falha ao salvar: ' + e.message);
    } finally {
      btnSave.disabled = false;
    }
  });

  setEditing(false);
})();