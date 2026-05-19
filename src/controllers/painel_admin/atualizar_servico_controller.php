<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';
require_once BASE_PATH . '/src/controllers/painel_admin/require_admin.php';

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
    echo json_encode(['ok' => false, 'error' => 'ID do serviço obrigatório']);
    exit;
  }

  $id = (int)$data['id'];
  $nome = trim($data['nome'] ?? '');
  $area_id = (int)($data['area_id'] ?? 0);
  $preco = (float)($data['preco'] ?? 0);
  $prazo = (int)($data['prazo'] ?? 0);
  $pontos = (int)($data['pontos'] ?? 0);
  $categoria = trim($data['categoria'] ?? '');
  $descricao = trim($data['descricao'] ?? '');
  $descricao_longa = trim($data['descricao_longa'] ?? '');
  $itens_incluidos = trim($data['itens_incluidos'] ?? '');
  $garantia = trim($data['garantia'] ?? '');
  $contato = trim($data['contato'] ?? '');
  $disponivel = (int)($data['disponivel'] ?? 0);

  if (!$nome) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Nome do serviço obrigatório']);
    exit;
  }

  $sql = 'UPDATE servicos SET
    nome = :nome,
    area_id = :area_id,
    preco = :preco,
    prazo = :prazo,
    pontos = :pontos,
    categoria = :categoria,
    descricao = :descricao,
    descricao_longa = :descricao_longa,
    itens_incluidos = :itens_incluidos,
    garantia = :garantia,
    contato = :contato,
    disponivel = :disponivel
    WHERE id = :id';

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':nome' => $nome,
    ':area_id' => $area_id,
    ':preco' => $preco,
    ':prazo' => $prazo,
    ':pontos' => $pontos,
    ':categoria' => $categoria,
    ':descricao' => $descricao,
    ':descricao_longa' => $descricao_longa,
    ':itens_incluidos' => $itens_incluidos,
    ':garantia' => $garantia,
    ':contato' => $contato,
    ':disponivel' => $disponivel,
    ':id' => $id
  ]);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar serviço']);
}
