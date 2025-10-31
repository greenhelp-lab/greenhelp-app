<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['empresa'] = [
    'nome_empresa' => $_POST['nome_empresa'],
    'cnpj' => $_POST['cnpj'],
    'endereco' => $_POST['endereco']
    // outros campos da empresa
  ];

  header('Location: ' . BASE_URL . '/src/pages/login/criar_conta.php');
  exit;
}
