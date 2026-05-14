<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$resposta = [
  'success' => false,
  'message' => 'Erro desconhecido'
];

$id_usuario = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$id_usuario) {
  $resposta['message'] = 'Você precisa estar logado!';
  echo json_encode($resposta);
  exit;
}

$id_item = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
if (!$id_item) {
  $resposta['message'] = 'ID do item inválido!';
  echo json_encode($resposta);
  exit;
}

try {
  $sql = "DELETE FROM carrinho 
            WHERE id = ? 
            AND usuario_id = ? 
            AND status = 'pendente'";

  $stmt = $pdo->prepare($sql);
  $ok = $stmt->execute([$id_item, $id_usuario]);

  if ($ok && $stmt->rowCount() > 0) {
    $resposta['success'] = true;
    $resposta['message'] = 'Item removido do carrinho!';
  } else {
    $resposta['message'] = 'Item não encontrado ou já processado.';
  }
} catch (Exception $erro) {
  error_log('Erro ao remover do carrinho: ' . $erro->getMessage());
  $resposta['message'] = 'Ops! Algo deu errado. Tente novamente.';
}

echo json_encode($resposta);
