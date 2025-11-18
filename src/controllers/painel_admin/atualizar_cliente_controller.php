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
    echo json_encode(['ok' => false, 'error' => 'ID do cliente obrigatório']);
    exit;
  }

  $id = (int)$data['id'];
  $nome = trim($data['nome'] ?? '');
  $email = trim($data['email'] ?? '');
  $telefone = trim($data['telefone'] ?? '');
  $ativo = (int)($data['ativo'] ?? 0);
  // empresa-related data (optional)
  $empresa_select = $data['empresa_select'] ?? null; // '', 'new' or id
  $empresa_obj = $data['empresa'] ?? null; // array with nome, cnpj, setor_atuacao, porte
  $empresa_update = !empty($data['empresa_update']);

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

  // Start transaction if company operations may happen
  $pdo->beginTransaction();

  try {
    $empresa_id_to_set = null;

    if ($empresa_select === 'new') {
      // create new empresa if nome provided
      if ($empresa_obj && !empty(trim($empresa_obj['nome'] ?? ''))) {
        $ins = $pdo->prepare("INSERT INTO empresas (nome, cnpj, setor_atuacao, porte) VALUES (:nome, :cnpj, :setor, :porte)");
        $ins->execute([
          ':nome' => trim($empresa_obj['nome']),
          ':cnpj' => trim($empresa_obj['cnpj'] ?? ''),
          ':setor' => trim($empresa_obj['setor_atuacao'] ?? ''),
          ':porte' => trim($empresa_obj['porte'] ?? '')
        ]);
        $empresa_id_to_set = $pdo->lastInsertId();
      }
    } elseif ($empresa_select === '' || $empresa_select === null) {
      $empresa_id_to_set = null;
    } else {
      // numeric id selected
      $empresa_id_to_set = (int)$empresa_select;
      // if requested, update empresa fields
      if ($empresa_update && $empresa_obj) {
        $up = $pdo->prepare("UPDATE empresas SET nome = :nome, cnpj = :cnpj, setor_atuacao = :setor, porte = :porte WHERE id = :id");
        $up->execute([
          ':nome' => trim($empresa_obj['nome'] ?? ''),
          ':cnpj' => trim($empresa_obj['cnpj'] ?? ''),
          ':setor' => trim($empresa_obj['setor_atuacao'] ?? ''),
          ':porte' => trim($empresa_obj['porte'] ?? ''),
          ':id' => $empresa_id_to_set
        ]);
      }
    }

    // Update usuario including empresa_id
    $sql = 'UPDATE usuarios SET
      nome = :nome,
      email = :email,
      telefone = :telefone,
      empresa_id = :empresa_id,
      ativo = :ativo
      WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':nome' => $nome,
      ':email' => $email,
      ':telefone' => $telefone,
      ':empresa_id' => $empresa_id_to_set,
      ':ativo' => $ativo,
      ':id' => $id
    ]);

    $pdo->commit();
    echo json_encode(['ok' => true]);
    exit;
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
