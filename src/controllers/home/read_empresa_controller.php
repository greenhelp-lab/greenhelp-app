<?php
// src/controllers/read_empresa_controller.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/conexao.php'; // <<< precisa disso

try {
  $userId = $_SESSION['user_id'] ?? null;
  if (!$userId) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'unauthenticated']);
    exit;
  }

  $sql = "SELECT id, nome, cnpj, setor_atuacao, porte, endereco, logo_path
          FROM empresas
          WHERE usuario_id = :uid
          ORDER BY id DESC
          LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([':uid' => (int)$userId]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    echo json_encode(['ok'=>true,'empresa'=>null]);
    exit;
  }

  $_SESSION['empresa_id'] = (int)$row['id'];

  echo json_encode([
    'ok' => true,
    'empresa' => [
      'id'            => (int)$row['id'],
      'nome'          => (string)($row['nome'] ?? ''),
      'cnpj'          => (string)($row['cnpj'] ?? ''),
      'setor_atuacao' => (string)($row['setor_atuacao'] ?? ''),
      'porte'         => (string)($row['porte'] ?? ''),
      'endereco'      => (string)($row['endereco'] ?? ''),
      'logo_path'     => (string)($row['logo_path'] ?? ''),
    ]
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'server_error','msg'=>$e->getMessage()]);
}
