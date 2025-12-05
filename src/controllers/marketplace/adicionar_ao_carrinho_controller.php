<?php

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$resposta = [
  'success' => false,
  'message' => 'Erro desconhecido'
];

$id_usuario = $_SESSION['user_id'] ?? null;
if (!$id_usuario) {
  $resposta['message'] = 'Você precisa fazer login primeiro!';
  echo json_encode($resposta);
  exit;
}

$id_servico = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
if (!$id_servico) {
  $resposta['message'] = 'ID do serviço inválido!';
  echo json_encode($resposta);
  exit;
}

try {
  $sql = "SELECT id FROM servicos WHERE id = ? AND disponivel = 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_servico]);

  if (!$stmt->fetch()) {
    $resposta['message'] = 'Este serviço não está disponível.';
    echo json_encode($resposta);
    exit;
  }

  $sql = "SELECT id FROM carrinho 
            WHERE usuario_id = ? 
            AND servico_id = ? 
            AND status = 'pendente'";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_usuario, $id_servico]);

  if ($stmt->fetch()) {
    $resposta['message'] = 'Serviço já está no seu carrinho!';
    $resposta['success'] = true;
    echo json_encode($resposta);
    exit;
  }

  $sql = "INSERT INTO carrinho (usuario_id, servico_id, status) 
            VALUES (?, ?, 'pendente')";

  $stmt = $pdo->prepare($sql);
  $ok = $stmt->execute([$id_usuario, $id_servico]);

  if ($ok) {
    $resposta['success'] = true;
    $resposta['message'] = 'Serviço adicionado ao carrinho!';
    $resposta['id_inserido'] = $pdo->lastInsertId();
  } else {
    $resposta['message'] = 'Não foi possível adicionar ao carrinho.';
  }
} catch (Exception $erro) {
  error_log('Erro ao adicionar ao carrinho: ' . $erro->getMessage());
  $resposta['message'] = 'Opa! Algo deu errado. Tente novamente.';
}

echo json_encode($resposta);
