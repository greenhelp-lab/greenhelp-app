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
    echo json_encode(['ok' => false, 'error' => 'ID do serviço obrigatório']);
    exit;
  }

  $id = (int)$data['id'];

  $stmt = $pdo->prepare('DELETE FROM servicos WHERE id = :id LIMIT 1');
  $ok = $stmt->execute([':id' => $id]);

  if (!$ok || $stmt->rowCount() === 0) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Falha ao excluir serviço (0 linhas)']);
    exit;
  }

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao excluir serviço']);
}
