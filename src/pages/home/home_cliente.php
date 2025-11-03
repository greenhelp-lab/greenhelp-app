<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
session_start();

// (opcional) desabilita cache desta página
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$logoUrl = BASE_URL . '/public/imgs/add-photo.svg'; // padrão

if (!empty($_SESSION['user_id'])) {
  // tenta pegar empresa da sessão; se não tiver, busca a empresa do próprio usuário
  $empresaId = $_SESSION['empresa_id'] ?? null;
  if (!$empresaId) {
    $st = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id = :uid ORDER BY id DESC LIMIT 1");
    $st->execute([':uid' => $_SESSION['user_id']]);
    $empresaId = $st->fetchColumn() ?: null;
    if ($empresaId) $_SESSION['empresa_id'] = (int)$empresaId;
  }

  // se tiver empresa, carrega o logo salvo; senão fica no ícone padrão
  if ($empresaId) {
    $st = $pdo->prepare("SELECT logo_path FROM empresas WHERE id = :id");
    $st->execute([':id' => $empresaId]);
    $row = $st->fetchColumn();
    if (!empty($row)) $logoUrl = $row;
  }
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
  <input disabled id="empresa" type="text" placeholder="Nome da Empresa">
  <input disabled id="cnpj" type="text" placeholder="CNPJ">
  <input disabled id="perfil" type="text" placeholder="Tamanho da Empresa">
  <input disabled id="industria" type="tel" placeholder="Indústria">

  <!-- BOTÕES -->
 <div class="form-actions">
  <button type="button" class="btn edit-button">Editar</button>
  <button type="submit" class="btn save-button">Salvar</button>
</div>
</form>


    <!-- ====== SEÇÃO RESTAURADA: Pontuações Verdes ====== -->
    <section class="pontuacoes">
      <div class="pontuacoes-header">
        <!-- ajuste o nome do arquivo se no seu /public/imgs for sem acento -->
        <img src="<?= BASE_URL; ?>/public/imgs/pontuação_verde.png" alt="Pontuações Verdes" class="pontuacoes-img">
        <h2 class="pontuacoes-title">Pontuações Verdes</h2>
      </div>

      <p class="pontuacoes-desc">
        Ganhe mais pontos através da <span class="cor_verde">compra de serviços</span> e
        <span class="cor_verde">melhoras sustentáveis</span> na sua empresa
      </p>

      <div class="niveis">
        <div class="nivel-card">
          <div class="nivel-left">
            <span class="nivel">Nível 7</span>
            <span class="faltam">Faltam 1435 pontos</span>
          </div>
          <div class="nivel-desc">Infraestrutura Eficiente</div>
          <div class="progress-bar"><div class="progress" style="width:60%;"></div></div>
        </div>

        <div class="nivel-card">
          <div class="nivel-left">
            <span class="nivel">Nível 7</span>
            <span class="faltam">Faltam 1435 pontos</span>
          </div>
          <div class="nivel-desc">Energia Renovável</div>
          <div class="progress-bar"><div class="progress" style="width:60%;"></div></div>
        </div>

        <div class="nivel-card">
          <div class="nivel-left">
            <span class="nivel">Nível 7</span>
            <span class="faltam">Faltam 1435 pontos</span>
          </div>
          <div class="nivel-desc">Computação em Nuvem</div>
          <div class="progress-bar"><div class="progress" style="width:60%;"></div></div>
        </div>

        <div class="nivel-card">
          <div class="nivel-left">
            <span class="nivel">Nível 7</span>
            <span class="faltam">Faltam 1435 pontos</span>
          </div>
          <div class="nivel-desc">Políticas Sustentáveis</div>
          <div class="progress-bar"><div class="progress" style="width:60%;"></div></div>
        </div>
      </div>

      <p class="pontuacao-total">Pontuação Total: <strong>4769</strong></p>
    </section>
    <!-- ====== /SEÇÃO RESTAURADA ====== -->
  </main>
  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>

<script>
const UPLOAD_LOGO_URL = '<?= rtrim(BASE_URL, '/') ?>/src/actions/upload_logo.php';

(async function () {
  const btn  = document.getElementById('btnLogo');
  const img  = document.getElementById('imgLogo');
  const inp  = document.getElementById('inpLogo');
  const MAX  = 3 * 1024 * 1024;
  const ok   = ['image/jpeg','image/png','image/webp'];

  // guarda o src inicial (não vamos trocar no READ)
  const initialSrc = img.getAttribute('src') || '';

  // === READ: busca dados da empresa do usuário logado (apenas inputs) ===
  try {
    const res = await fetch('<?= rtrim(BASE_URL, '/') ?>/src/controllers/get_empresa_controller.php', {
      credentials: 'include'
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const json = await res.json();

    if (json.ok && json.empresa) {
      const e = json.empresa;
      document.getElementById("empresa").value   = e.nome || '';
      document.getElementById("cnpj").value      = e.cnpj || '';
      document.getElementById("perfil").value    = e.porte || '';
      document.getElementById("industria").value = e.setor_atuacao || '';

      // NÃO altere a imagem aqui.
      // Se quiser, apenas defina caso esteja no placeholder:
      // const isPlaceholder = /add-photo\.svg$/i.test(initialSrc);
      // if (isPlaceholder && e.logo_path) { img.src = (e.logo_path.startsWith('http') ? e.logo_path : '<?= rtrim(BASE_URL, '/') ?>' + (e.logo_path.startsWith('/') ? e.logo_path : '/' + e.logo_path)) + '?t=' + Date.now(); }
    }
  } catch (err) {
    console.error('get_empresa_controller:', err);
  }

  // === Upload de logo (único lugar que troca a imagem) ===
  btn.addEventListener('click', () => inp.click());

  inp.addEventListener('change', async () => {
    const file = inp.files?.[0]; if (!file) return;
    if (!ok.includes(file.type)) { alert('JPG/PNG/WEBP'); inp.value=''; return; }
    if (file.size > MAX) { alert('Até 3MB'); inp.value=''; return; }

    // preview imediato
    const t = URL.createObjectURL(file);
    img.src = t; img.onload = () => URL.revokeObjectURL(t);

    // upload
    const fd = new FormData(); fd.append('logo', file);
    try {
      const r = await fetch(UPLOAD_LOGO_URL, { method:'POST', body: fd, credentials:'include' });
      const text = await r.text();
      if (!r.ok) throw new Error(text || ('HTTP ' + r.status));
      const data = JSON.parse(text);

      // ao concluir, usa a URL final (furando cache)
      if (data.url) img.src = data.url + '?t=' + Date.now();
    } catch (e) {
      alert('Falha no upload: ' + e.message);
      inp.value = '';
      // se quiser, restaura a imagem inicial:
      // img.src = initialSrc;
    }
  });
})();
</script>


</body>
</html>