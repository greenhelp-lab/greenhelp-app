<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = intval($_POST['id']);

  try {
    $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: " . BASE_URL . "/src/pages/admin/painel_admin.php?status=deleted");
    exit;
  } catch (PDOException $e) {
    error_log("Erro ao excluir serviço: " . $e->getMessage());
    header("Location: " . BASE_URL . "/src/pages/admin/painel_admin.php?status=error");
    exit;
  }
} else {
  header("Location: " . BASE_URL . "/src/pages/admin/painel_admin.php");
  exit;
}
