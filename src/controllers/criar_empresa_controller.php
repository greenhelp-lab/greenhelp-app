<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['empresa'] = [
    'nome_empresa' => $_POST['nome_empresa'],
    'cnpj' => $_POST['cnpj'],
    'setor' => $_POST['setor'],
    'tamanho_empresa' => $_POST['tamanho_empresa']
    // outros campos da empresa
  ];

  header('Location: ' . BASE_URL . '/src/pages/login/criar_conta.php');
  exit;
}
