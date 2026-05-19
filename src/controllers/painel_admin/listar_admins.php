<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once BASE_PATH . '/src/controllers/painel_admin/require_admin.php';

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "
  SELECT u.id, u.nome, u.email
  FROM usuarios u
  WHERE u.papel = 'admin'
";

if ($termo !== '') {
  $sql .= " AND (u.nome LIKE :termo OR u.email LIKE :termo)";
}

$sql .= " ORDER BY u.id DESC";

$stmt = $pdo->prepare($sql);

if ($termo !== '') {
  $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
}

$stmt->execute();
$admins = $stmt->fetchAll();

if ($admins) {
  foreach ($admins as $admin) {
    $url = BASE_URL . '/src/pages/painel_admin/ver_admin.php?id=' . urlencode($admin['id']);
    echo '
      <div class="record" data-user-id="' . htmlspecialchars($admin['id']) . '">
        <div>
          <div class="record-title">' . htmlspecialchars($admin['nome']) . '</div>
          <div class="record-email">' . htmlspecialchars($admin['email']) . '</div>
          <div class="record-role">Administrador</div>
        </div>
        <div>
          <a class="btn-ver" href="' . $url . '">Ver</a>
        </div>
      </div>
    ';
  }
} elseif ($termo !== '') {
  echo '<p style="text-align:center;color:var(--accent);">Nenhum administrador encontrado :(</p>';
} else {
  echo '<p style="text-align:center;color:var(--accent);">Nenhum administrador disponível no momento.</p>';
}
