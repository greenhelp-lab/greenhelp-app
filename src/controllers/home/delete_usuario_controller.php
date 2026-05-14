<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
  if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Não autenticado']);
    exit;
  }
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método não permitido']);
    exit;
  }

  $userId = (int) $_SESSION['user_id'];
  $redirectUrl = rtrim(BASE_URL, '/') . '/src/pages/login/login.php';

  $pdo->beginTransaction();
  $st = $pdo->prepare('SELECT avatar_path FROM usuarios WHERE id = :id FOR UPDATE');
  $st->execute([':id' => $userId]);
  $avatarPath = $st->fetchColumn();
  $pdo->prepare('UPDATE usuarios SET empresa_id = NULL WHERE id = :id')
      ->execute([':id' => $userId]);
  $pdo->prepare('DELETE FROM servicos_andamento WHERE usuario_id = :id')
      ->execute([':id' => $userId]);
  $stmtEmp = $pdo->prepare('SELECT id FROM empresas WHERE usuario_id = :id');
  $stmtEmp->execute([':id' => $userId]);
  $empIds = $stmtEmp->fetchAll(PDO::FETCH_COLUMN);

  if ($empIds) {
    $in = implode(',', array_fill(0, count($empIds), '?'));
    $pdo->prepare("DELETE FROM empresas WHERE id IN ($in)")->execute($empIds);
  }
  $del = $pdo->prepare('DELETE FROM usuarios WHERE id = :id LIMIT 1');
  $ok  = $del->execute([':id' => $userId]);

  if (!$ok || $del->rowCount() === 0) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Falha ao excluir usuário (0 linhas)']);
    exit;
  }

  $pdo->commit();
  session_unset();
  session_destroy();
  if (!empty($avatarPath) && !preg_match('~^https?://~i', $avatarPath)) {
    $abs = $avatarPath[0] === '/' ? $_SERVER['DOCUMENT_ROOT'] . $avatarPath : $avatarPath;
    @unlink($abs);
  }

  echo json_encode(['ok' => true, 'redirect' => $redirectUrl]);
} catch (PDOException $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  $msg = $e->getCode() === '23000'
    ? 'Não foi possível excluir: há registros vinculados por chave estrangeira.'
    : 'Erro ao excluir usuário.';
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $msg]);
} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao excluir usuário.']);
}
