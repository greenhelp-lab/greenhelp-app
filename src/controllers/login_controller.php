<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT id, nome, senha, papel FROM usuarios WHERE email = :email AND ativo = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

$user = $stmt->fetch();

if ($user && password_verify($senha, $user['senha'])) {
  $_SESSION['user_id'] = $user['id'];
  print("user id: " . $user['id']);
  $_SESSION['papel'] = $user['papel'];

  $sql = "SELECT id FROM empresas WHERE usuario_id = :user_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':user_id', $user['id']);
  $stmt->execute();

  $empresa = $stmt->fetch();
  $_SESSION['empresa_id'] = $empresa['id'];

  if ($user['papel'] === 'admin') {
    header('Location: ' . BASE_URL . '/src/pages/painel_admin/painel_admin.php');
  } else {
    header('Location: ' . BASE_URL . '/src/pages/home/home_cliente.php');
  }
  exit;
} else {
  $_SESSION['mensagem_erro'] = "Credenciais inválidas.";
  // mensagem de erro visivel no arquivo de login
  header('Location: ' . BASE_URL . '/src/pages/login/login.php');
  exit;
}
