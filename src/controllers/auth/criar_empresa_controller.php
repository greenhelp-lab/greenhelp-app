
<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['business_name'] ?? '';
    $cnpj = $_POST['business_cnpj'] ?? '';
    $setor = $_POST['business_industry'] ?? '';
    $porte = $_POST['business_size'] ?? '';

    try {
        $stmt = $pdo->prepare("
            INSERT INTO empresas (nome, cnpj, setor_atuacao, porte)
            VALUES (:nome, :cnpj, :setor_atuacao, :porte)
        ");
        $stmt->execute([
            ':nome' => $nome,
            ':cnpj' => $cnpj,
            ':setor_atuacao' => $setor,
            ':porte' => $porte
        ]);
        $_SESSION['empresa_id'] = $pdo->lastInsertId();
        header('Location: ' . BASE_URL . '/src/pages/login/criar_conta.php');
        exit;

    } catch (PDOException $e) {
        die('Erro ao criar empresa: ' . $e->getMessage());
    }
}
