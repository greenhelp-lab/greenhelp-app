<?php
// src/controllers/update_usuario_controller.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/conexao.php';

try {
  if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'unauthenticated']); exit;
  }

  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'invalid_json']); exit;
  }

  $userId   = (int)$_SESSION['user_id'];
  $nome     = trim($data['nome'] ?? '');
  $email    = trim($data['email'] ?? '');
  $telefone = trim($data['telefone'] ?? '');

  if ($nome === '' || $email === '') {
    http_response_code(422);
    echo json_encode(['ok'=>false,'error'=>'nome_email_required']); exit;
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok'=>false,'error'=>'invalid_email']); exit;
  }

  // (Opcional) checar e-mail duplicado em outro usuário
  $st = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id LIMIT 1');
  $st->execute([':email'=>$email, ':id'=>$userId]);
  if ($st->fetchColumn()) {
    http_response_code(409);
    echo json_encode(['ok'=>false,'error'=>'email_in_use']); exit;
  }

  $sql = 'UPDATE usuarios
          SET nome = :nome, email = :email, telefone = :telefone
          WHERE id = :id';
  $st = $pdo->prepare($sql);
  $st->execute([
    ':nome'     => $nome,
    ':email'    => $email,
    ':telefone' => $telefone,
    ':id'       => $userId,
  ]);

  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  // Se houver UNIQUE KEY no email, cai aqui com SQLSTATE 23000
  $code = (int)($e->getCode() ?: 0);
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'server_error','msg'=>$e->getMessage(),'code'=>$code]);
}
