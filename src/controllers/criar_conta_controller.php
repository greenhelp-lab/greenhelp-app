<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // validação dos dados do usuário
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);

  // pega os dados da empresa da sessão
  if (isset($_SESSION['empresa'])) {
    $empresa = $_SESSION['empresa'];

    // exemplo de inserção usando PDO
    $stmt = $pdo->prepare("
            INSERT INTO empresas (nome_empresa, cnpj, setor, tamanho_empresa)
            VALUES (:nome_empresa, :cnpj, :setor, :tamanho_empresa)
        ");
    $stmt->execute([
      ':nome_empresa' => $empresa['nome_empresa'],
      ':cnpj' => $empresa['cnpj'],
      ':setor' => $empresa['setor'],
      ':tamanho_empresa' => $empresa['tamanho_empresa']
    ]);

    $empresa_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("
            INSERT INTO usuarios (empresa_id, nome, email, senha)
            VALUES (:empresa_id, :nome, :email, :senha)
        ");
    $stmt->execute([
      ':empresa_id' => $empresa_id,
      ':nome' => $nome,
      ':email' => $email,
      ':senha' => $senha
    ]);

    // limpa sessão
    unset($_SESSION['empresa']);

    // redireciona para página home
    header('Location: ' . BASE_URL . '/src/pages/home/home.php');
    exit;
  } else {
    // se não houver dados da empresa na sessão, volta para a página de criar empresa para que o usuário forneça os dados
    header('Location: ' . BASE_URL . '/src/pages/login/criar_empresa.php');
    exit;
  }
}
