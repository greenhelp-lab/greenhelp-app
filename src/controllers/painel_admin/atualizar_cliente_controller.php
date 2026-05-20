<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once BASE_PATH . '/src/controllers/painel_admin/require_admin.php';

header('Content-Type: application/json');

$body = json_decode(file_get_contents("php://input"), true);
if (!$body) {
  echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
  exit;
}

$id           = (int)($body['id'] ?? 0);
$nome         = trim($body['nome'] ?? '');
$telefone     = trim($body['telefone'] ?? '');
$email        = trim($body['email'] ?? '');
$ativoBody    = $body['ativo'] ?? null;
$empresa_sel  = $body['empresa_id'] ?? '';
$empresa_data = $body['empresa_data'] ?? [];

if ($id <= 0) {
  echo json_encode(['ok' => false, 'error' => 'ID inválido']);
  exit;
}

try {
  $pdo->beginTransaction();
  $st = $pdo->prepare("SELECT empresa_id, ativo FROM usuarios WHERE id = :id LIMIT 1");
  $st->execute([':id' => $id]);
  $usuario = $st->fetch();

  if (!$usuario) {
    $pdo->rollBack();
    echo json_encode(['ok' => false, 'error' => 'Usuário não encontrado']);
    exit;
  }

  $ativo = $ativoBody === null || $ativoBody === '' ? (int)$usuario['ativo'] : (int)$ativoBody;
  $ativo = $ativo === 1 ? 1 : 0;

  $empresaAtualId = $usuario['empresa_id'] ? (int)$usuario['empresa_id'] : null;
  $newEmpresaId = null;

  if ($empresa_sel === "new") {
    $ins = $pdo->prepare("
    INSERT INTO empresas (nome, cnpj, setor_atuacao, porte)
    VALUES (:n, :c, :s, :p)
  ");
    $ins->execute([
      ':n' => trim($empresa_data['nome'] ?? ''),
      ':c' => trim($empresa_data['cnpj'] ?? ''),
      ':s' => trim($empresa_data['setor'] ?? ''),
      ':p' => trim($empresa_data['porte'] ?? '')
    ]);

    $newEmpresaId = (int)$pdo->lastInsertId();
  } elseif ($empresa_sel === "" || $empresa_sel === null) {
    $newEmpresaId = null;
  } else {

    $empresa_sel_id = (int)$empresa_sel;
    $newEmpresaId = $empresa_sel_id;
    $upd = $pdo->prepare("
    UPDATE empresas
       SET nome = :n,
           cnpj = :c,
           setor_atuacao = :s,
           porte = :p
     WHERE id = :id
    LIMIT 1
  ");
    $upd->execute([
      ':n'  => trim($empresa_data['nome'] ?? ''),
      ':c'  => trim($empresa_data['cnpj'] ?? ''),
      ':s'  => trim($empresa_data['setor'] ?? ''),
      ':p'  => trim($empresa_data['porte'] ?? ''),
      ':id' => $empresa_sel_id
    ]);
  }
  $up = $pdo->prepare("
  UPDATE usuarios
     SET nome = :n,
         telefone = :t,
         email = :e,
         ativo = :a,
         empresa_id = :emp
   WHERE id = :id
   LIMIT 1
");

  $up->execute([
    ':n'   => $nome,
    ':t'   => $telefone,
    ':e'   => $email,
    ':a'   => $ativo,
    ':emp' => $newEmpresaId,
    ':id'  => $id
  ]);
  $pdo->commit();
  echo json_encode(['ok' => true]);
} catch (Exception $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar cliente']);
}
