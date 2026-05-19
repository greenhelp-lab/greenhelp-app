<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/helpers/pontuacoes.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

$id_usuario = $_SESSION['user_id'] ?? null;
if (!$id_usuario) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Você precisa estar logado!']);
  exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$ids_carrinho = isset($dados['items']) && is_array($dados['items'])
  ? array_map('intval', $dados['items'])
  : [];

if (!$ids_carrinho) {
  echo json_encode(['success' => false, 'message' => 'Dados inválidos!']);
  exit;
}

try {
  $pdo->beginTransaction();

  $empresaId = buscarEmpresaDoUsuario($pdo, (int)$id_usuario);
  if (!$empresaId) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Empresa não encontrada para este usuário.']);
    exit;
  }

  $ph = implode(',', array_fill(0, count($ids_carrinho), '?'));
  $sql = "SELECT c.id, c.servico_id, s.preco, s.area_id, s.pontos
          FROM carrinho c
          JOIN servicos s ON c.servico_id = s.id
          WHERE c.id IN ($ph)
          AND c.usuario_id = ?
          AND c.status = 'pendente'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([...$ids_carrinho, $id_usuario]);
  $itens = $stmt->fetchAll();

  if (!$itens) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Nenhum item válido encontrado!']);
    exit;
  }

  $stmt_inserir = $pdo->prepare(
    "INSERT INTO servicos_andamento (usuario_id, servico_id, valor_total, status)
     VALUES (?, ?, ?, 'pendente')"
  );

  $stmt_atualizar = $pdo->prepare(
    "UPDATE carrinho SET status = 'comprado' WHERE id = ? AND usuario_id = ?"
  );

  $ids_processados = [];
  foreach ($itens as $item) {
    $stmt_inserir->execute([$id_usuario, $item['servico_id'], $item['preco']]);
    adicionarPontosArea($pdo, (int)$empresaId, (int)$item['area_id'], (int)$item['pontos']);
    $stmt_atualizar->execute([$item['id'], $id_usuario]);
    $ids_processados[] = $item['id'];
  }

  $pdo->commit();
  echo json_encode([
    'success' => true,
    'message' => 'Compra finalizada com sucesso!',
    'ids_processados' => $ids_processados
  ]);
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  error_log('Erro ao finalizar compra: ' . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Erro ao finalizar a compra.']);
}
