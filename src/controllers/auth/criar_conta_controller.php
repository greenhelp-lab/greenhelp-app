<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e validação básica
    $nome = trim($_POST['client_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';
    $senha_confirm = $_POST['password_confirm'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($senha_confirm)) {
        die('Por favor, preencha todos os campos obrigatórios.');
    }

    if ($senha !== $senha_confirm) {
        die('As senhas não coincidem.');
    }

    // Chama o controller para criar o usuário
    try {
        CriarContaController::criarUsuario($pdo, $nome, $email, $senha);
        header('Location: ' . BASE_URL . '/src/pages/login/login.php');
        exit;
    } catch (Exception $e) {
        die('Erro ao criar conta: ' . $e->getMessage());
    }
}
