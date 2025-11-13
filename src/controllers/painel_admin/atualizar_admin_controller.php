<?php

declare(strict_types=1);

include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

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
    echo json_encode(['ok' => false, 'error' => 'ID do admin obrigatório']);
    exit;
  }

  $id = (int)$data['id'];
  $nome = trim($data['nome'] ?? '');
  $email = trim($data['email'] ?? '');
  $telefone = trim($data['telefone'] ?? '');
  $ativado = (int)($data['ativado'] ?? 0);

  if (!$nome) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Nome obrigatório']);
    exit;
  }

  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Email inválido']);
    exit;
  }

  // Verificar duplicação de email em outro usuário
  $st = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id LIMIT 1');
  $st->execute([':email' => $email, ':id' => $id]);
  if ($st->fetchColumn()) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'error' => 'Email já está em uso']);
    exit;
  }

  $sql = 'UPDATE usuarios SET
    nome = :nome,
    email = :email,
    telefone = :telefone,
    ativado = :ativado
    WHERE id = :id';

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':telefone' => $telefone,
    ':ativado' => $ativado,
    ':id' => $id
  ]);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
