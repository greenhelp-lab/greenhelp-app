<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
session_start();

// pega empresa da sessão (ajuste se sua chave for outra)
$empresaId = $_SESSION['empresa_id'] ?? null;

// busca logo no BD (fallback pro ícone padrão)
$logoUrl = BASE_URL . '/public/imgs/add-photo.svg';
if ($empresaId) {
  $st = $pdo->prepare("SELECT logo_path FROM empresas WHERE id = :id");
  $st->execute([':id'=>$empresaId]);
  $x = $st->fetchColumn();
  if (!empty($x)) $logoUrl = $x;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenHelp Home</title>
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/home/home_cliente.css">
</head>
<body>
  <?php include BASE_PATH . "/src/pages/partials/header.php"; ?>
  <main class="container">
    <h1>Home</h1>

    <div class="user-photo">
      <button type="button" id="btnLogo" aria-label="Alterar logo da empresa">
        <img id="imgLogo" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES) ?>" alt="Logo da empresa">
      </button>
      <input type="file" id="inpLogo" name="logo" accept="image/*" hidden>
    </div>

    <h2>Nome da empresa</h2>

    <form>
      <input type="text" placeholder="Nome Completo">
      <input type="text" placeholder="Cargo">
      <input type="text" placeholder="Perfil">
      <input type="tel" placeholder="Telefone">
      <input type="email" placeholder="Email">
      <input type="password" placeholder="Senha">
      <input type="text" placeholder="ID da Empresa">
    </form>

    <!-- ... resto da página ... -->
  </main>
  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>

  <script>
  const UPLOAD_LOGO_URL = '<?= rtrim(BASE_URL, '/') ?>/src/actions/upload_logo.php';

  (function () {
    const btn  = document.getElementById('btnLogo');
    const img  = document.getElementById('imgLogo');
    const inp  = document.getElementById('inpLogo');
    const MAX  = 3 * 1024 * 1024;
    const ok   = ['image/jpeg','image/png','image/webp'];

    btn.addEventListener('click', () => inp.click());

    inp.addEventListener('change', async () => {
      const file = inp.files?.[0]; if (!file) return;
      if (!ok.includes(file.type)) { alert('JPG/PNG/WEBP'); inp.value=''; return; }
      if (file.size > MAX) { alert('Até 3MB'); inp.value=''; return; }

      // preview imediato
      const t = URL.createObjectURL(file);
      img.src = t; img.onload = () => URL.revokeObjectURL(t);

      // upload automático
      const fd = new FormData(); fd.append('logo', file);
      try {
        const r = await fetch(UPLOAD_LOGO_URL, { method: 'POST', body: fd, credentials: 'include' });
        if (!r.ok) throw new Error(await r.text());
        const data = await r.json();
        if (data.url) img.src = data.url + '?t=' + Date.now(); // fura cache
      } catch (e) {
        alert('Falha no upload: ' + e.message);
        inp.value = '';
      }
    });
  })();
  </script>
</body>
</html>
