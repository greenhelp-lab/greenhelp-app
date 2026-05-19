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

  if (!$data || !isset($data['id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'ID e status obrigatórios']);
    exit;
  }

  $id = (int)$data['id'];
  $status = trim($data['status']);

  $statusValidos = ['pendente', 'em andamento', 'concluido', 'cancelado'];
  if (!in_array($status, $statusValidos, true)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Status inválido']);
    exit;
  }

  $sql = 'UPDATE servicos_andamento SET status = :status WHERE id = :id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':status' => $status,
    ':id' => $id
  ]);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar status']);
}
