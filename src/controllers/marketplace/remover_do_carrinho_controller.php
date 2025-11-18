<?php

/**
 * remover_do_carrinho.php
 * ----------------------
 * Endpoint para remover um item do carrinho do usuário.
 * 
 * Mapa de funcionamento:
 * 1. Verifica se usuário está logado
 * 2. Valida o ID do item do carrinho
 * 3. Remove o item (apenas se pertencer ao usuário e estiver pendente)
 * 
 * Recebe: POST['cart_id'] - ID do item no carrinho
 * Retorna: JSON com sucesso/erro e mensagem
 */

// Configura resposta como JSON e inicia sessão
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

// Conecta com o banco
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

// Prepara resposta padrão
$resposta = [
  'success' => false,
  'message' => 'Erro desconhecido'
];

// Passo 1: Verifica login do usuário (aceita user_id ou usuario_id da sessão)
$id_usuario = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$id_usuario) {
  $resposta['message'] = 'Você precisa estar logado!';
  echo json_encode($resposta);
  exit;
}

// Passo 2: Pega e valida ID do item
$id_item = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
if (!$id_item) {
  $resposta['message'] = 'ID do item inválido!';
  echo json_encode($resposta);
  exit;
}

try {
  // Passo 3: Remove o item (apenas se for do usuário e estiver pendente)
  $sql = "DELETE FROM carrinho 
            WHERE id = ? 
            AND usuario_id = ? 
            AND status = 'pendente'";

  $stmt = $pdo->prepare($sql);
  $ok = $stmt->execute([$id_item, $id_usuario]);

  // Verifica se algo foi removido
  if ($ok && $stmt->rowCount() > 0) {
    $resposta['success'] = true;
    $resposta['message'] = 'Item removido do carrinho!';
  } else {
    $resposta['message'] = 'Item não encontrado ou já processado.';
  }
} catch (Exception $erro) {
  // Se der erro, loga e retorna mensagem amigável
  error_log('Erro ao remover do carrinho: ' . $erro->getMessage());
  $resposta['message'] = 'Ops! Algo deu errado. Tente novamente.';
}

// Retorna resultado
echo json_encode($resposta);
