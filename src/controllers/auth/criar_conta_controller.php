<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome           = trim($_POST['client_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $senha          = $_POST['password'] ?? '';
    $senha_confirm  = $_POST['password_confirm'] ?? '';

    if ($nome === '' || $email === '' || $senha === '' || $senha_confirm === '') {
        die('Por favor, preencha todos os campos obrigatórios.');
    }

    if ($senha !== $senha_confirm) {
        die('As senhas não coincidem.');
    }
    if (empty($_SESSION['empresa_id'])) {
        die('Nenhuma empresa vinculada. Crie uma empresa antes de criar a conta.');
    }

    $empresa_id = (int)$_SESSION['empresa_id'];

    try {
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (empresa_id, nome, email, senha)
            VALUES (:empresa_id, :nome, :email, :senha)
        ");

        $stmt->execute([
            ':empresa_id' => $empresa_id,
            ':nome'       => $nome,
            ':email'      => $email,
            ':senha'      => $senha_hash
        ]);
        $usuario_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("
            UPDATE empresas
            SET usuario_id = :usuario_id
            WHERE id = :empresa_id
        ");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':empresa_id' => $empresa_id
        ]);
        $_SESSION['user_id'] = $usuario_id;
        $_SESSION['empresa_id_logada'] = $empresa_id;
        unset($_SESSION['empresa_id']);
        header('Location: ' . BASE_URL . '/src/pages/login/login.php');
        exit;

    } catch (Exception $e) {
        die('Erro ao criar conta: ' . $e->getMessage());
    }
}
