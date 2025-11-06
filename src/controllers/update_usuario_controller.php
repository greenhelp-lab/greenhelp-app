<?php
// /src/controllers/update_usuario_controller.php
declare(strict_types=1);

include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
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

  // aceita JSON ou form-urlencoded
  $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($contentType, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: [];
  } else {
    $data = $_POST;
  }

  $userId   = (int) $_SESSION['user_id'];
  $nome     = trim($data['nome']     ?? '');
  $telefone = trim($data['telefone'] ?? '');
  $email    = trim($data['email']    ?? '');

  if ($nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Dados inválidos']);
    exit;
  }

  // e-mail já usado por outro usuário
  $st = $pdo->prepare('SELECT id FROM usuarios WHERE email = :e AND id <> :id LIMIT 1');
  $st->execute([':e' => $email, ':id' => $userId]);
  if ($st->fetch()) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'error' => 'E-mail já em uso']);
    exit;
  }

  $up = $pdo->prepare('UPDATE usuarios SET nome = :n, telefone = :t, email = :e WHERE id = :id LIMIT 1');
  $ok = $up->execute([':n' => $nome, ':t' => $telefone, ':e' => $email, ':id' => $userId]);

  if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Falha ao atualizar']);
    exit;
  }

  // (opcional) atualizar sessão se você usar esses campos em outros lugares
  $_SESSION['user_email'] = $email;
  $_SESSION['user_name']  = $nome;

  echo json_encode(['ok' => true, 'user' => [
    'id' => $userId, 'nome' => $nome, 'telefone' => $telefone, 'email' => $email
  ]]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Exceção: ' . $e->getMessage()]);
}
