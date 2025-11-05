<?php
// src/controllers/update_empresa_controller.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/config.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthenticated']); exit; }

// empresa “ativa” (a mesma que você define no get_empresa_controller)
$empresaId = $_SESSION['empresa_id'] ?? null;
if (!$empresaId) {
  // fallback: pega a última do usuário
  $st = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id = :u ORDER BY id DESC LIMIT 1");
  $st->execute([':u'=>(int)$userId]);
  $empresaId = $st->fetchColumn() ?: null;
  if ($empresaId) $_SESSION['empresa_id'] = (int)$empresaId;
}
if (!$empresaId) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'empresa_nao_encontrada']); exit; }

// garante que a empresa pertence ao usuário logado
$own = $pdo->prepare("SELECT 1 FROM empresas WHERE id = :id AND usuario_id = :u");
$own->execute([':id'=>(int)$empresaId, ':u'=>(int)$userId]);
if (!$own->fetchColumn()) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit; }

// lê dados (aceita form-url-encoded, multipart ou JSON)
$body = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$nome          = trim($body['nome']          ?? '');
$cnpj          = trim($body['cnpj']          ?? '');
$setor_atuacao = trim($body['setor_atuacao'] ?? '');
$porte         = trim($body['porte']         ?? '');
$endereco      = trim($body['endereco']      ?? '');

// validações simples
if ($nome === '') { http_response_code(422); echo json_encode(['ok'=>false,'error'=>'nome_obrigatorio']); exit; }
// (opcional) validar formato de CNPJ aqui

$st = $pdo->prepare("
  UPDATE empresas
     SET nome = :n,
         cnpj = :c,
         setor_atuacao = :s,
         porte = :p,
         endereco = :e
   WHERE id = :id
");
$st->execute([
  ':n'=>$nome, ':c'=>$cnpj, ':s'=>$setor_atuacao, ':p'=>$porte, ':e'=>$endereco,
  ':id'=>(int)$empresaId
]);

echo json_encode(['ok'=>true, 'rows'=>$st->rowCount()]);
