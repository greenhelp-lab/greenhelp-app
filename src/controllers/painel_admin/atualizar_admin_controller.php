<?php

declare(strict_types=1);

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
    echo json_encode(['ok' => false, 'error' => 'ID do admin obrigatório']);
    exit;
  }

  $id = (int)$data['id'];
  $nome = trim($data['nome'] ?? '');
  $email = trim($data['email'] ?? '');
  $telefone = trim($data['telefone'] ?? '');
  $ativoBody = $data['ativo'] ?? null;

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
  $st = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id LIMIT 1');
  $st->execute([':email' => $email, ':id' => $id]);
  if ($st->fetchColumn()) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'error' => 'Email já está em uso']);
    exit;
  }

  $st = $pdo->prepare('SELECT ativo FROM usuarios WHERE id = :id LIMIT 1');
  $st->execute([':id' => $id]);
  $ativoAtual = $st->fetchColumn();

  if ($ativoAtual === false) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Admin não encontrado']);
    exit;
  }

  $ativo = $ativoBody === null || $ativoBody === '' ? (int)$ativoAtual : (int)$ativoBody;
  $ativo = $ativo === 1 ? 1 : 0;

  $sql = 'UPDATE usuarios SET
    nome = :nome,
    email = :email,
    telefone = :telefone,
    ativo = :ativo
    WHERE id = :id';

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':telefone' => $telefone,
    ':ativo' => $ativo,
    ':id' => $id
  ]);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar admin']);
}
