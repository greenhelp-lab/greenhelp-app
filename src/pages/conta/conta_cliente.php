<?php
// conta_cliente.php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
session_start();

// pegue o id do usuário logado
$userId = $_SESSION['user_id'] ?? null;

// busca a URL do avatar no BD (fallback pro ícone padrão)
$avatarUrl = BASE_URL . '/public/imgs/add-photo.svg';
if ($userId) {
    $st = $pdo->prepare("SELECT avatar_path FROM usuarios WHERE id = :id");
    $st->execute([':id' => $userId]);
    $row = $st->fetchColumn();
    if (!empty($row)) $avatarUrl = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/conta/conta_cliente.css">
  <title>Conta</title>
</head>
<body>

  <?php include BASE_PATH . '/src/pages/partials/header.php'; ?>

  <main class="account-main">
    <div class="conta-container">
      <h1 class="account-title">Meu Usuário</h1>

      <div class="user-photo">
        <button type="button" id="btnFoto" aria-label="Alterar foto do usuário">
          <img id="fotoUsuario" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES) ?>" alt="Foto do usuário">
        </button>
        <input type="file" id="inpFoto" name="foto" accept="image/*" hidden>
      </div>

      <form class="account-form" method="post" enctype="multipart/form-data">
        <input type="text" placeholder="Nome Completo">
        <input type="tel" placeholder="Telefone">
        <input type="email" placeholder="Email">
        <input type="password" placeholder="Senha">
        <input type="text" placeholder="Empresa">

        <div class="action-buttons-top">
          <button type="button" class="btn swap-account-button">Trocar Conta</button>
          <button type="button" class="btn edit-button">Editar</button>
        </div>

        <div class="action-buttons-bottom">
          <button type="button" class="btn delete-account-button">Deletar Conta</button>
          <button type="submit" class="btn save-button">Salvar</button>
        </div>
      </form>
    </div>
  </main>

  <img src="<?= BASE_URL; ?>/public/imgs/engines-icons.svg" alt="Ícones de engrenagens decorativas" class="engines-icons">

  <script>
  const UPLOAD_URL = '<?= rtrim(BASE_URL, '/') ?>/src/actions/upload_avatar.php';

  (function () {
    const btn   = document.getElementById('btnFoto');
    const img   = document.getElementById('fotoUsuario');
    const input = document.getElementById('inpFoto');
    const MAX = 3 * 1024 * 1024;
    const ok = ['image/jpeg','image/png','image/webp'];

    btn.addEventListener('click', () => input.click());

    input.addEventListener('change', async () => {
      const file = input.files?.[0];
      if (!file) return;

      if (!ok.includes(file.type)) { alert('JPG/PNG/WEBP'); input.value=''; return; }
      if (file.size > MAX) { alert('Até 3MB'); input.value=''; return; }

      // preview imediato
      const t = URL.createObjectURL(file);
      img.src = t;
      img.onload = () => URL.revokeObjectURL(t);

      // upload automático
      const fd = new FormData();
      fd.append('foto', file);

      try {
        const r = await fetch(UPLOAD_URL, { method: 'POST', body: fd, credentials: 'include' });
        if (!r.ok) throw new Error(await r.text());
        const data = await r.json();

        // usa a URL final (e quebra cache)
        if (data.url) img.src = data.url + '?t=' + Date.now();
      } catch (e) {
        alert('Falha no upload: ' + e.message);
        input.value = '';
      }
    });
  })();
  </script>
</body>
</html>
