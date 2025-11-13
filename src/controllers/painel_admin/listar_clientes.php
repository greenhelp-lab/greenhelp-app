<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

// consulta: busca apenas clientes e inclui nome da empresa
$sql = "
  SELECT u.id, u.nome, u.email, e.nome AS empresa_nome
  FROM usuarios u
  LEFT JOIN empresas e ON u.empresa_id = e.id
  WHERE u.papel = 'cliente'
";

if ($termo !== '') {
  $sql .= " AND (u.nome LIKE :termo OR u.email LIKE :termo OR e.nome LIKE :termo)";
}

$sql .= " ORDER BY u.id DESC";

$stmt = $pdo->prepare($sql);

if ($termo !== '') {
  $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
}

$stmt->execute();
$clientes = $stmt->fetchAll();

if ($clientes) {
  foreach ($clientes as $cliente) {
    $url = BASE_URL . '/src/pages/painel_admin/ver_cliente.php?id=' . urlencode($cliente['id']);
    echo '
      <div class="record" data-user-id="' . htmlspecialchars($cliente['id']) . '">
        <div>
          <div class="record-title">' . htmlspecialchars($cliente['nome']) . '</div>
          <div class="record-email">' . htmlspecialchars($cliente['email']) . '</div>
          <div class="record-company">Empresa: ' . htmlspecialchars($cliente['empresa_nome'] ?? '—') . '</div>
        </div>
        <div>
          <button class="btn-ver" onclick="window.location.href=\'' . $url . '\'">Ver</button>
        </div>
      </div>
    ';
  }
} elseif ($termo !== '') {
  echo '<p style="text-align:center;color:var(--accent);">Nenhum cliente encontrado :(</p>';
} else {
  echo '<p style="text-align:center;color:var(--accent);">Nenhum cliente disponível no momento.</p>';
}
