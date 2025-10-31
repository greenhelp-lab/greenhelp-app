<?php
session_start();
require_once('conexao.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT id, nome, senha, role FROM users WHERE email = :email AND ativo = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['senha'])) {
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['role'] = $user['role'];

  if ($user['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/src/pages/painel_admin/painel_admin.php');
  } else {
    header('Location: ' . BASE_URL . '/src/pages/home/home_cliente.php');
  }
  exit;
} else {
  $_SESSION['mensagem_erro'] = "Credenciais inválidas.";
  header('Location: ' . BASE_URL . '/src/pages/login/login.php');
  exit;
}
