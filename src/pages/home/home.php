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
    <h1 class="welcome-title">Bem vindo(a), <?= htmlspecialchars($_SESSION['primeiro_nome'] ?? ''); ?>!</h1>

    <div class="user-photo">
      <button type="button" id="btnLogo" aria-label="Alterar logo da empresa" disabled>
        <img id="imgLogo" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES) ?>" alt="Logo da empresa">
      </button>
      <input type="file" id="inpLogo" name="logo" accept="image/*" hidden>
    </div>

    <h2>Sobre Sua Empresa</h2>

    <form id="formEmpresa" class="form-empresa">
      <input readonly id="empresa" type="text" placeholder="Nome da Empresa">
      <input readonly id="cnpj" type="text" placeholder="CNPJ">
      <input readonly id="perfil" type="text" placeholder="Tamanho da Empresa">
      <input readonly id="industria" type="text" placeholder="Indústria">
      <input readonly id="endereco" type="text" placeholder="Endereço" maxlength="200"> <!-- NOVO -->

      <div class="form-actions" style="display:flex; gap:12px; margin-top:12px;">
        <button type="button" class="btn edit-button" id="btnEditar">Editar</button>
        <button type="submit" class="btn save-button" id="btnSalvar">Salvar</button>
      </div>
    </form>

    <!-- ===== Serviços em Andamento ===== -->
    <?php
    $sql = "SELECT sa.id, sa.status, sa.valor_total, sa.data_inicio, 
                   s.nome, s.descricao,
                   a.nome as area_nome, a.imagem_url as area_img
            FROM servicos_andamento sa
            INNER JOIN servicos s ON sa.servico_id = s.id
            LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
            WHERE sa.usuario_id = :usuario_id
            ORDER BY sa.data_inicio DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
    $stmt->execute();
    $servicos = $stmt->fetchAll();
    ?>

    <section class="servicos-andamento">
      <div class="section-header">
        <h2>Serviços Contratados</h2>
      </div>
      <div class="servicos-grid">
        <?php if (!empty($servicos)): ?>
          <?php foreach ($servicos as $servico):
            $status_class = match ($servico['status']) {
              'pendente' => 'status-pendente',
              'em andamento' => 'status-andamento',
              'concluido' => 'status-concluido',
              'cancelado' => 'status-cancelado',
              default => ''
            }; ?>
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
                <span class="preco">R$ <?= number_format($servico['valor_total'], 2, ',', '.') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-services">Você ainda não tem serviços em andamento. Visite nosso <a href="<?= BASE_URL ?>/src/pages/servicos/marketplace.php">marketplace</a> para começar!</p>
        <?php endif; ?>
      </div>
    </section>

    <section class="pontuacoes">
      <div class="pontuacoes-header">
        <h2 class="pontuacoes-title">Pontuações Verdes</h2>
      </div>
      <p class="pontuacoes-desc">
        Ganhe mais pontos através da <span class="cor_verde">compra de serviços</span> e
        <span class="cor_verde">melhoras sustentáveis</span> na sua empresa
      </p>
      <?php
      // Funções de cálculo de nível e progresso (seguem regras do projeto)
      function calcularNivel(int $pontos): int
      {
        if ($pontos >= 1000) return 5;
        if ($pontos >= 500) return 4;
        if ($pontos >= 250) return 3;
        if ($pontos >= 100) return 2;
        return 1;
      }

      function progressoParaProximoNivel(int $pontos): array
      {
        $nivel = calcularNivel($pontos);

        switch ($nivel) {
          case 1:
            $limite = 100;
            break;
          case 2:
            $limite = 250;
            break;
          case 3:
            $limite = 500;
            break;
          case 4:
            $limite = 1000;
            break;
          default:
            return ['nivel' => 5, 'progress' => 100, 'faltam' => 0];
        }

        $progress = min(100, ($pontos / $limite) * 100);
        $faltam = max(0, $limite - $pontos);

        return [
          'nivel' => $nivel,
          'progress' => (int) round($progress),
          'faltam' => (int) $faltam
        ];
      }

      // Busca áreas e pontuações (agora sem usar coluna inexistente p.usuario_id)
      $usuarioId = $_SESSION['user_id'] ?? null;
      $areas = [];
      if ($usuarioId) {
        $sqlAreas = "SELECT a.id AS area_id, a.nome AS area_nome, p.pontos, p.nivel
                     FROM areas_sustentaveis a
                     LEFT JOIN pontuacoes_areas p
                       ON p.area_id = a.id
                     ORDER BY a.nome";
        $stAreas = $pdo->prepare($sqlAreas);
        $stAreas->execute(); // sem parâmetro, pois não há mais :uid
        $areas = $stAreas->fetchAll();
      }

      // Calcula pontuação total (soma de todas as áreas)
      $pontuacaoTotal = 0;
      foreach ($areas as $aRow) {
        $pontuacaoTotal += (int) ($aRow['pontos'] ?? 0);
      }
      ?>

      <div class="niveis" aria-live="polite">
        <?php if (!empty($areas)): ?>
          <?php
          // Gera CSS específico por cartão para definir a largura da barra (sem usar style="...")
          $areaCustomCss = "";
          foreach ($areas as $aTmp) {
            $aidTmp = (int) $aTmp['area_id'];
            $pTmp = (int) ($aTmp['pontos'] ?? 0);
            $prTmp = progressoParaProximoNivel($pTmp);
            $areaCustomCss .= ".nivel-card[data-area-id=\"" . $aidTmp . "\"] .progress{width: " . (int) $prTmp['progress'] . "%;}\n";
          }
          if ($areaCustomCss) {
            echo "<style type=\"text/css\">\n" . $areaCustomCss . "</style>";
          }
          ?>

          <?php foreach ($areas as $a):
            $areaId = (int) $a['area_id'];
            $areaNome = $a['area_nome'] ?? '';
            $pontos = (int) ($a['pontos'] ?? 0);
            // recalcula o nível a partir dos pontos para garantir consistência com regras
            $nivel = calcularNivel($pontos);
            $prog = progressoParaProximoNivel($pontos);

            // Tenta detectar um ícone local seguindo um padrão (se existir)
            $iconUrl = null;
            $possibleFiles = [
              $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/public/imgs/areas/area_' . $areaId . '.svg',
              $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/public/imgs/areas/area_' . $areaId . '.png',
              $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/public/imgs/areas/area_' . $areaId . '.webp',
              $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/public/imgs/areas/area_' . $areaId . '.jpg'
            ];
            foreach ($possibleFiles as $pf) {
              if (file_exists($pf)) {
                $rel = str_replace($_SERVER['DOCUMENT_ROOT'], '', $pf);
                $iconUrl = rtrim(BASE_URL, '/') . $rel;
                break;
              }
            }
          ?>
            <div class="nivel-card" data-area-id="<?= $areaId ?>" data-nivel="<?= $nivel ?>">
              <div class="nivel-card-header">
                <?php if ($iconUrl): ?>
                  <img class="area-icon" src="<?= htmlspecialchars($iconUrl) ?>" alt="<?= htmlspecialchars($areaNome) ?>">
                <?php endif; ?>
                <div class="nivel-titles">
                  <h3 class="nivel-desc"><?= htmlspecialchars($areaNome) ?></h3>
                  <p class="nivel-meta">
                    <span class="nivel">Nível <?= $nivel ?></span>
                    <span class="faltam">• Faltam <?= $prog['faltam'] ?> pts</span>
                  </p>
                </div>
              </div>
              <div class="nivel-card-body">
                <div class="progress-bar" aria-hidden="true">
                  <div class="progress" data-progress="<?= $prog['progress'] ?>"></div>
                </div>
                <div class="nivel-stats">
                  <span class="pontos"><?= $pontos ?> pts</span>
                  <span class="percent"><?= $prog['progress'] ?>%</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-services">Nenhuma área cadastrada no sistema.</p>
        <?php endif; ?>
      </div>

      <p class="pontuacao-total">Pontuação Total: <strong><?= $pontuacaoTotal ?></strong></p>
    </section>

  </main>

  <?php include BASE_PATH . "/src/pages/partials/footer.php"; ?>

  <script>
    const UPLOAD_LOGO_URL = '<?= rtrim(BASE_URL, '/') ?>/src/controllers/home/upload_logo_controller.php';

    (async function() {
      const btn = document.getElementById('btnLogo');
      const img = document.getElementById('imgLogo');
      const inp = document.getElementById('inpLogo');
      const MAX = 3 * 1024 * 1024;
      const ok = ['image/jpeg', 'image/png', 'image/webp'];

      try {
        const res = await fetch('<?= rtrim(BASE_URL, '/') ?>/src/controllers/home/read_empresa_controller.php', {
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
        industria: document.getElementById('industria'),
        endereco: document.getElementById('endereco') // NOVO
      };

      function setEditing(on) {
        Object.values(f).forEach(i => i.readOnly = !on);
        if (btnLogo) btnLogo.disabled = !on;
        btnSave.disabled = false;
        btnEdit.disabled = on;
        if (on) f.empresa.focus();
      }

      try {
        const res = await fetch(`${API}/src/controllers/home/read_empresa_controller.php`, {
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
          const r = await fetch(`${API}/src/controllers/home/update_empresa_controller.php`, {
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
