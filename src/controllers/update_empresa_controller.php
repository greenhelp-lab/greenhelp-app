<?php
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

  $nome     = trim($data['nome'] ?? '');
  $cnpj     = trim($data['cnpj'] ?? '');
  $porte    = trim($data['porte'] ?? '');
  $setor    = trim($data['setor_atuacao'] ?? '');
  $endereco = trim($data['endereco'] ?? ''); // NOVO

  if ($nome === '') {
    http_response_code(422);
    echo json_encode(['ok'=>false,'error'=>'nome_required']); exit;
  }
  if (strlen($endereco) > 200) {
    http_response_code(422);
    echo json_encode(['ok'=>false,'error'=>'endereco_too_long']); exit;
  }

  $userId = (int)$_SESSION['user_id'];
  $empresaId = $_SESSION['empresa_id'] ?? null;
  if (!$empresaId) {
    $st = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id=:uid ORDER BY id DESC LIMIT 1");
    $st->execute([':uid'=>$userId]);
    $empresaId = (int)$st->fetchColumn();
    if ($empresaId) $_SESSION['empresa_id'] = $empresaId;
  }
  if (!$empresaId) { echo json_encode(['ok'=>false,'error'=>'empresa_not_found']); exit; }

  $st = $pdo->prepare("SELECT 1 FROM empresas WHERE id=:id AND usuario_id=:uid");
  $st->execute([':id'=>$empresaId, ':uid'=>$userId]);
  if (!$st->fetchColumn()) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>'forbidden']); exit;
  }

  $sql = "UPDATE empresas
          SET nome=:nome, cnpj=:cnpj, porte=:porte, setor_atuacao=:setor, endereco=:endereco
          WHERE id=:id AND usuario_id=:uid";
  $st = $pdo->prepare($sql);
  $st->execute([
    ':nome'=>$nome,
    ':cnpj'=>$cnpj,
    ':porte'=>$porte,
    ':setor'=>$setor,
    ':endereco'=>$endereco,
    ':id'=>$empresaId,
    ':uid'=>$userId
  ]);

  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'server_error','msg'=>$e->getMessage()]);
}
