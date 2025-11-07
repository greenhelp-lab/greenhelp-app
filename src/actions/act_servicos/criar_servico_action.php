<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome']);
  $area_id = $_POST['area_id'];
  $preco = $_POST['preco'];
  $prazo_dias = $_POST['prazo_dias'];
  $pontos = $_POST['pontos'];
  $categoria = trim($_POST['categoria'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');
  $descricao_longa = trim($_POST['descricao_longa'] ?? '');
  $itens_incluidos = trim($_POST['itens_incluidos'] ?? '');
  $garantia = trim($_POST['garantia'] ?? '');
  $contato = trim($_POST['contato'] ?? '');

  try {
    $stmt = $pdo->prepare("
      INSERT INTO servicos 
        (nome, area_id, preco, prazo_dias, pontos, categoria, descricao, descricao_longa, itens_incluidos, garantia, contato)
      VALUES 
        (:nome, :area_id, :preco, :prazo_dias, :pontos, :categoria, :descricao, :descricao_longa, :itens_incluidos, :garantia, :contato)
    ");

    $stmt->execute([
      ':nome' => $nome,
      ':area_id' => $area_id,
      ':preco' => $preco,
      ':prazo_dias' => $prazo_dias,
      ':pontos' => $pontos,
      ':categoria' => $categoria,
      ':descricao' => $descricao,
      ':descricao_longa' => $descricao_longa,
      ':itens_incluidos' => $itens_incluidos,
      ':garantia' => $garantia,
      ':contato' => $contato
    ]);

    header("Location: " . BASE_URL . "/src/pages/admin/db_criar_servico.php?status=success");
    exit;
  } catch (PDOException $e) {
    error_log("Erro ao criar serviço: " . $e->getMessage());
    header("Location: " . BASE_URL . "/src/pages/admin/db_criar_servico.php?status=error");
    exit;
  }
} else {
  header("Location: " . BASE_URL . "/src/pages/admin/painel_admin.php");
  exit;
}
