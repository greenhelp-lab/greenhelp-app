<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';
require_once BASE_PATH . '/src/controllers/painel_admin/require_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $area_id = $_POST['area_id'] ?? '';
  $preco = $_POST['preco'] ?? '';
  $prazo = $_POST['prazo_dias'] ?? '';
  $pontos = $_POST['pontos'] ?? '';
  $categoria = trim($_POST['categoria'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');
  $descricao_longa = trim($_POST['descricao_longa'] ?? '');
  $itens_incluidos = trim($_POST['itens_incluidos'] ?? '');
  $garantia = trim($_POST['garantia'] ?? '');
  $contato = trim($_POST['contato'] ?? '');
  if (!$nome || !$area_id || !$preco || !$prazo || !$pontos) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_servico.php?error=campos_obrigatorios");
    exit;
  }
  if ($contato && !filter_var($contato, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_servico.php?error=email_invalido");
    exit;
  }

  try {

    $stmt = $pdo->prepare("
      INSERT INTO servicos 
      (nome, area_id, preco, prazo, pontos, categoria, descricao, descricao_longa, itens_incluidos, garantia, contato, disponivel)
      VALUES 
      (:nome, :area_id, :preco, :prazo, :pontos, :categoria, :descricao, :descricao_longa, :itens_incluidos, :garantia, :contato, 1)
    ");
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
      ':contato' => $contato
    ]);
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_servico.php?status=success");
    exit;

  } catch (PDOException $e) {
    error_log("Erro ao criar servico: " . $e->getMessage());

    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_servico.php?error=erro_banco");
    exit;
  }

} else {
  header("Location: " . BASE_URL . "/src/pages/painel_admin/painel_admin.php");
  exit;
}
