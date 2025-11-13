<?php

/**
 * adicionar_ao_carrinho.php
 * -------------------------
 * Endpoint que recebe requisições POST para adicionar um serviço ao carrinho.
 * 
 * Mapa de funcionamento:
 * 1. Verifica se o usuário está logado
 * 2. Valida o ID do serviço recebido
 * 3. Confirma se o serviço existe e está disponível
 * 4. Verifica se já não está no carrinho
 * 5. Adiciona ao carrinho se tudo estiver ok
 * 
 * Recebe: POST['servico_id'] - ID numérico do serviço a adicionar
 * Retorna: JSON com sucesso/erro e mensagem
 */

// Configura resposta como JSON e inicia sessão se necessário
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

// Inclui arquivo de conexão com o banco
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

// Prepara array de resposta padrão
$resposta = [
  'success' => false,
  'message' => 'Erro desconhecido'
];

// 1: Confirma que usuário está logado
$id_usuario = $_SESSION['user_id'] ?? null;
if (!$id_usuario) {
  $resposta['message'] = 'Você precisa fazer login primeiro!';
  echo json_encode($resposta);
  exit;
}

// 2: Pega e valida ID do serviço
$id_servico = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
if (!$id_servico) {
  $resposta['message'] = 'ID do serviço inválido!';
  echo json_encode($resposta);
  exit;
}

try {
  // 3: Verifica se serviço existe e está disponível
  $sql = "SELECT id FROM servicos WHERE id = ? AND disponivel = 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_servico]);

  if (!$stmt->fetch()) {
    $resposta['message'] = 'Este serviço não está disponível.';
    echo json_encode($resposta);
    exit;
  }

  // 4: Verifica se já está no carrinho
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

  // 5: Adiciona ao carrinho
  $sql = "INSERT INTO carrinho (usuario_id, servico_id, status) 
            VALUES (?, ?, 'pendente')";

  $stmt = $pdo->prepare($sql);
  $ok = $stmt->execute([$id_usuario, $id_servico]);

  // Se deu certo, retorna sucesso
  if ($ok) {
    $resposta['success'] = true;
    $resposta['message'] = 'Serviço adicionado ao carrinho!';
    $resposta['id_inserido'] = $pdo->lastInsertId();
  } else {
    $resposta['message'] = 'Não foi possível adicionar ao carrinho.';
  }
} catch (Exception $erro) {
  // Se der algum erro, loga e retorna mensagem
  error_log('Erro ao adicionar ao carrinho: ' . $erro->getMessage());
  $resposta['message'] = 'Opa! Algo deu errado. Tente novamente.';
}

// Retorna resultado final
echo json_encode($resposta);
