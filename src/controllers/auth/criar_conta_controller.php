<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php'; // fornece $pdo

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

    // Verifica se existe empresa criada ANTES
    if (empty($_SESSION['empresa_id'])) {
        die('Nenhuma empresa vinculada. Crie uma empresa antes de criar a conta.');
    }

    $empresa_id = (int)$_SESSION['empresa_id'];

    try {
        // Hash seguro da senha
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

        // Inserir usuário
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

        // ID do usuário recém-criado
        $usuario_id = $pdo->lastInsertId();

        // 🔥 Vincula AGORA o usuário à empresa no banco
        $stmt = $pdo->prepare("
            UPDATE empresas
            SET usuario_id = :usuario_id
            WHERE id = :empresa_id
        ");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':empresa_id' => $empresa_id
        ]);

        // Salvar sessão do usuário
        $_SESSION['user_id'] = $usuario_id;

        // Agora a empresa está vinculada ao usuário logado
        $_SESSION['empresa_id_logada'] = $empresa_id;

        // Remove empresa temporária
        unset($_SESSION['empresa_id']);

        // Redireciona para login
        header('Location: ' . BASE_URL . '/src/pages/login/login.php');
        exit;

    } catch (Exception $e) {
        die('Erro ao criar conta: ' . $e->getMessage());
    }
}
