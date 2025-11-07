<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$logoUrl = BASE_URL . '/public/imgs/add-photo.svg';

if (!empty($_SESSION['user_id'])) {
  $empresaId = $_SESSION['empresa_id'] ?? null;
  if (!$empresaId) {
    $st = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id = :uid ORDER BY id DESC LIMIT 1");
    $st->execute([':uid' => $_SESSION['user_id']]);
    $empresaId = $st->fetchColumn() ?: null;
    if ($empresaId) $_SESSION['empresa_id'] = (int)$empresaId;
  }

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
  <link rel="stylesheet" href="<?= BASE_URL; ?>/public/css/home/home.css">
</head>

<body>
  <?php include_once BASE_PATH . "/src/pages/partials/header_cliente.php"; ?>

  <main class="container">
    <h1>Home</h1>

    <div class="user-photo">
      <button type="button" id="btnLogo" aria-label="Alterar logo da empresa" disabled>
        <img id="imgLogo" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES) ?>" alt="Logo da empresa">
      </button>
      <input type="file" id="inpLogo" name="logo" accept="image/*" hidden>
    </div>

    <h2>Sobre Sua Empresa</h2>

    <form id="formEmpresa" class="form-empresa" style="margin:24px 0;">
      <input readonly id="empresa" type="text" placeholder="Nome da Empresa">
      <input readonly id="cnpj" type="text" placeholder="CNPJ">
      <input readonly id="perfil" type="text" placeholder="Tamanho da Empresa">
      <input readonly id="industria" type="text" placeholder="Indústria">

      <div class="form-actions" style="display:flex; gap:12px; margin-top:12px;">
        <button type="button" class="btn edit-button" id="btnEditar">Editar</button>
        <button type="submit" class="btn save-button" id="btnSalvar">Salvar</button>
      </div>
    </form>

    <!-- ===== Serviços em Andamento ===== -->
    <section class="servicos-andamento">
      <div class="section-header">
        <h2>Serviços em Andamento</h2>
      </div>
      <div class="servicos-grid">
        <?php
        $sql = "SELECT sa.id, sa.status, sa.data_inicio, 
                       s.nome, s.descricao, s.preco,
                       a.nome as area_nome, a.imagem_url as area_img
                FROM servicos_andamento sa
                INNER JOIN servicos s ON sa.servico_id = s.id
                LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
                WHERE sa.usuario_id = :usuario_id
                ORDER BY sa.data_inicio DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
        $stmt->execute();
        $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($servicos)):
          foreach ($servicos as $servico):
            $status_class = match ($servico['status']) {
              'pendente' => 'status-pendente',
              'em andamento' => 'status-andamento',
              'concluido' => 'status-concluido',
              'cancelado' => 'status-cancelado',
              default => ''
            };
        ?>
            <div class="servico-card">
              <div class="servico-header">
                <?php if ($servico['area_img']): ?>
                  <img src="<?= htmlspecialchars($servico['area_img']) ?>" alt="<?= htmlspecialchars($servico['area_nome']) ?>" class="area-icon">
                <?php endif; ?>
                <span class="status-badge <?= $status_class ?>"><?= ucfirst($servico['status']) ?></span>
              </div>
              <div class="servico-body">
                <h3><?= htmlspecialchars($servico['nome']) ?></h3>
                <p class="area-nome"><?= htmlspecialchars($servico['area_nome']) ?></p>
                <p class="descricao"><?= htmlspecialchars($servico['descricao']) ?></p>
              </div>
              <div class="servico-footer">
                <span class="data">Adquirido: <?= date('d/m/Y', strtotime($servico['data_inicio'])) ?></span>
                <span class="preco">R$ <?= number_format($servico['preco'], 2, ',', '.') ?></span>
              </div>
            </div>
          <?php endforeach;
        else: ?>
          <p class="no-services">Você ainda não tem serviços em andamento. Visite nosso <a href="<?= BASE_URL ?>/src/pages/servicos/marketplace.php">marketplace</a> para começar!</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- ===== Pontuações ===== -->
    <section class="pontuacoes">
      <div class="pontuacoes-header">
        <img src="<?= BASE_URL; ?>/public/imgs/pontuação_verde.png" alt="Pontuações Verdes" class="pontuacoes-img">
        <h2 class="pontuacoes-title">Pontuações Verdes</h2>
      </div>
      <p class="pontuacoes-desc">
        Ganhe mais pontos através da <span class="cor_verde">compra de serviços</span> e
        <span class="cor_verde">melhoras sustentáveis</span> na sua empresa
      </p>
      <div class="niveis">
        <div class="nivel-card">
          <div class="nivel-left"><span class="nivel">Nível 7</span><span class="faltam">Faltam 1435 pontos</span></div>
          <div class="nivel-desc">Infraestrutura Eficiente</div>
          <div class="progress-bar">
            <div class="progress" style="width:60%;"></div>
          </div>
        </div>
        <div class="nivel-card">
          <div class="nivel-left"><span class="nivel">Nível 7</span><span class="faltam">Faltam 1435 pontos</span></div>
          <div class="nivel-desc">Energia Renovável</div>
          <div class="progress-bar">
            <div class="progress" style="width:60%;"></div>
          </div>
        </div>
        <div class="nivel-card">
          <div class="nivel-left"><span class="nivel">Nível 7</span><span class="faltam">Faltam 1435 pontos</span></div>
          <div class="nivel-desc">Computação em Nuvem</div>
          <div class="progress-bar">
            <div class="progress" style="width:60%;"></div>
          </div>
        </div>
        <div class="nivel-card">
          <div class="nivel-left"><span class="nivel">Nível 7</span><span class="faltam">Faltam 1435 pontos</span></div>
          <div class="nivel-desc">Políticas Sustentáveis</div>
          <div class="progress-bar">
            <div class="progress" style="width:60%;"></div>
          </div>
        </div>
      </div>
      <p class="pontuacao-total">Pontuação Total: <strong>4769</strong></p>
    </section>
  </main>

  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>

  <!-- Upload logo -->
  <script>
    const UPLOAD_LOGO_URL = '<?= rtrim(BASE_URL, '/') ?>/src/actions/upload_logo.php';

    (async function() {
      const btn = document.getElementById('btnLogo');
      const img = document.getElementById('imgLogo');
      const inp = document.getElementById('inpLogo');
      const MAX = 3 * 1024 * 1024;
      const ok = ['image/jpeg', 'image/png', 'image/webp'];

      // Carrega dados da empresa
      try {
        const res = await fetch('<?= rtrim(BASE_URL, '/') ?>/src/controllers/read_empresa_controller.php', {
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
          const data = await r.json();
          if (!r.ok || !data?.url) throw new Error(data?.error || ('HTTP ' + r.status));
          img.src = data.url + '?t=' + Date.now();
        } catch (e) {
          alert('Falha no upload: ' + e.message);
          inp.value = '';
        }
      });
    })();
  </script>

  <!-- Edição + Salvar -->
  <script>
    (async function() {
      const API = '<?= rtrim(BASE_URL, '/') ?>';
      const form = document.getElementById('formEmpresa');
      const btnEdit = document.getElementById('btnEditar');
      const btnSave = document.getElementById('btnSalvar');
      const btnLogo = document.getElementById('btnLogo');

      const f = {
        empresa: document.getElementById('empresa'),
        cnpj: document.getElementById('cnpj'),
        perfil: document.getElementById('perfil'),
        industria: document.getElementById('industria')
      };

      function setEditing(on) {
        Object.values(f).forEach(i => i.readOnly = !on);
        if (btnLogo) btnLogo.disabled = !on;
        btnSave.disabled = false; // Sempre habilitado
        btnEdit.disabled = on;
        if (on) f.empresa.focus();
      }

      // READ inicial
      try {
        const res = await fetch(`${API}/src/controllers/read_empresa_controller.php`, {
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
          setor_atuacao: f.industria.value.trim()
        };
        if (!payload.nome) {
          alert('Informe o nome da empresa.');
          f.empresa.focus();
          return;
        }

        try {
          btnSave.disabled = true;
          const r = await fetch(`${API}/src/controllers/update_empresa_controller.php`, {
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
  </script>
</body>

</html>