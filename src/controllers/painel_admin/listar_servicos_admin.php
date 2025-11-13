<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "
  SELECT id, nome, categoria, preco, descricao
  FROM servicos
  WHERE disponivel = 1
";

if ($termo !== '') {
  $sql .= " AND (nome LIKE :termo OR categoria LIKE :termo OR descricao LIKE :termo)";
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);

if ($termo !== '') {
  $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
}

$stmt->execute();
$servicos = $stmt->fetchAll();

if ($servicos) {
  foreach ($servicos as $servico) {
    $url = BASE_URL . '/src/pages/painel_admin/ver_servico.php?id=' . urlencode($servico['id']);
    echo '
      <div class="record" data-service-id="' . htmlspecialchars($servico['id']) . '">
        <div>
          <div class="record-title">' . htmlspecialchars($servico['nome']) . '</div>
          <div class="record-email">Categoria: ' . htmlspecialchars($servico['categoria'] ?? '—') . '</div>
          <div class="record-company">Preço: R$ ' . number_format($servico['preco'], 2, ',', '.') . '</div>
        </div>
        <div>
          <button class="btn-ver" onclick="window.location.href=\'' . $url . '\'">Ver</button>
        </div>
      </div>
    ';
  }
} elseif ($termo !== '') {
  echo '<p style="text-align:center;color:var(--accent);">Nenhum serviço encontrado.</p>';
} else {
  echo '<p style="text-align:center;color:var(--accent);">Nenhum serviço cadastrado.</p>';
}
