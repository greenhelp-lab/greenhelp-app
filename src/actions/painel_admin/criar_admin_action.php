<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefone = trim($_POST['telefone'] ?? '');
  $senha = $_POST['senha'] ?? '';

  if (!$nome || !$email || !$senha) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_admin.php?error=campos_obrigatorios");
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_admin.php?error=email_invalido");
    exit;
  }

  $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
  $stmt->execute([':email' => $email]);
  if ($stmt->fetchColumn()) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_admin.php?error=email_existente");
    exit;
  }

  try {
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha, papel, ativado) VALUES (:nome, :email, :telefone, :senha, :papel, :ativado)");
    $stmt->execute([
      ':nome' => $nome,
      ':email' => $email,
      ':telefone' => $telefone,
      ':senha' => $senha_hash,
      ':papel' => 'admin',
      ':ativado' => 1
    ]);

    header("Location: " . BASE_URL . "/src/pages/painel_admin/painel_admin.php?status=admin_criado");
    exit;
  } catch (PDOException $e) {
    error_log("Erro ao criar admin: " . $e->getMessage());
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_admin.php?error=erro_banco");
    exit;
  }
} else {
  header("Location: " . BASE_URL . "/src/pages/painel_admin/painel_admin.php");
  exit;
}
