<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';
require_once BASE_PATH . '/src/controllers/painel_admin/require_admin.php';

header('Content-Type: application/json; charset=utf-8');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método não permitido']);
    exit;
  }

  $data = json_decode(file_get_contents('php://input'), true);

  if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'ID do cliente obrigatório']);
    exit;
  }

  $id = (int)$data['id'];

  $pdo->beginTransaction();
  $pdo->prepare('UPDATE usuarios SET empresa_id = NULL WHERE id = :id')
    ->execute([':id' => $id]);
  $pdo->prepare('DELETE FROM servicos_andamento WHERE usuario_id = :id')
    ->execute([':id' => $id]);
  $stmtEmp = $pdo->prepare('SELECT id FROM empresas WHERE usuario_id = :id');
  $stmtEmp->execute([':id' => $id]);
  $empId = $stmtEmp->fetch();
  $empresaId = (int)$empId['id'];
  

  if ($empId) {
    $pdo->prepare("DELETE FROM pontuacoes_areas WHERE empresa_id = :id")->execute([':id' => $empresaId]);
    $pdo->prepare("DELETE FROM empresas WHERE id = :id")->execute([':id' => $empresaId]);
  }
  $del = $pdo->prepare('DELETE FROM usuarios WHERE id = :id LIMIT 1');
  $ok  = $del->execute([':id' => $id]);

  if (!$ok || $del->rowCount() === 0) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Falha ao excluir cliente (0 linhas)']);
    exit;
  }

  $pdo->commit();

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao excluir cliente']);
}
