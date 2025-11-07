<?php

/**
 * finalizar_compra.php
 * -------------------
 * Endpoint para finalizar a compra dos itens selecionados no carrinho.
 * Recebe uma lista de IDs do carrinho via POST em JSON e processa a compra.
 * 
 * Mapa de funcionamento:
 * 1. Verifica se o usuário está logado
 * 2. Recebe e valida os itens do carrinho
 * 3. Dentro de uma transação:
 *    - Confirma que os itens são do usuário e estão pendentes
 *    - Cria registros em servicos_andamento
 *    - Marca itens do carrinho como processados
 * 
 * Recebe: JSON { items: number[] } - Lista de IDs dos itens do carrinho
 * Retorna: JSON com sucesso/erro, mensagem e IDs processados
 */

// Configurações iniciais
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

// Prepara resposta padrão
$resposta = [
  'success' => false,
  'message' => 'Erro desconhecido'
];

// 1: Verifica login
$id_usuario = $_SESSION['user_id'] ?? null;
if (!$id_usuario) {
  http_response_code(401); // Não autorizado
  $resposta['message'] = 'Você precisa estar logado!';
  echo json_encode($resposta);
  exit;
}

// 2: Pega e valida dados do POST
$json = file_get_contents('php://input');
$dados = json_decode($json, true);

// Verifica se recebeu um array de itens
if (!isset($dados['items']) || !is_array($dados['items'])) {
  $resposta['message'] = 'Dados inválidos! Envie um array de IDs.';
  echo json_encode($resposta);
  exit;
}

// Filtra apenas números do array
$ids_carrinho = array_filter($dados['items'], 'is_numeric');
if (empty($ids_carrinho)) {
  $resposta['message'] = 'Selecione pelo menos um item!';
  echo json_encode($resposta);
  exit;
}

try {
  // Inicia transação
  $pdo->beginTransaction();

  // 3.1: Busca itens válidos do usuário
  $placeholders = str_repeat('?,', count($ids_carrinho) - 1) . '?';
  $sql = "SELECT c.id, c.servico_id, s.nome 
            FROM carrinho c
            INNER JOIN servicos s ON c.servico_id = s.id
            WHERE c.id IN ($placeholders) 
            AND c.usuario_id = ? 
            AND c.status = 'pendente'";

  $stmt = $pdo->prepare($sql);
  $params = array_merge($ids_carrinho, [$id_usuario]);
  $stmt->execute($params);
  $itens_validos = $stmt->fetchAll();

  // Se nenhum item válido, cancela
  if (empty($itens_validos)) {
    $pdo->rollBack();
    $resposta['message'] = 'Nenhum item válido encontrado!';
    echo json_encode($resposta);
    exit;
  }

  // Prepara statements para reuso
  $stmt_inserir = $pdo->prepare(
    "INSERT INTO servicos_andamento (usuario_id, servico_id, status) 
         VALUES (?, ?, 'pendente')"
  );

  $stmt_atualizar = $pdo->prepare(
    "UPDATE carrinho SET status = 'processado' 
         WHERE id = ? AND usuario_id = ?"
  );

  // Processa cada item
  $ids_processados = [];
  foreach ($itens_validos as $item) {
    // Cria serviço em andamento
    $stmt_inserir->execute([
      $id_usuario,
      $item['servico_id']
    ]);

    // Marca item do carrinho como processado
    $stmt_atualizar->execute([
      $item['id'],
      $id_usuario
    ]);

    $ids_processados[] = $item['id'];
  }

  // Confirma todas as alterações
  $pdo->commit();

  // Retorna sucesso
  $resposta['success'] = true;
  $resposta['message'] = 'Compra finalizada com sucesso!';
  $resposta['ids_processados'] = $ids_processados;
} catch (Exception $erro) {
  // Se der erro, cancela tudo
  if ($pdo->inTransaction()) $pdo->rollBack();

  // Loga o erro real mas retorna mensagem amigável
  error_log('Erro ao finalizar compra: ' . $erro->getMessage());
  http_response_code(500);
  $resposta['message'] = 'Ops! Algo deu errado ao finalizar a compra. Tente novamente.';
}

// Retorna resultado
echo json_encode($resposta);
