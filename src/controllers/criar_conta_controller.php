<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // validação dos dados do usuário
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

  // pega os dados da empresa da sessão
  if (isset($_SESSION['empresa'])) {
    $empresa = $_SESSION['empresa'];

    // exemplo de inserção usando PDO
    $stmt = $pdo->prepare("
            INSERT INTO empresas (nome_empresa, cnpj, endereco)
            VALUES (:nome_empresa, :cnpj, :endereco)
        ");
    $stmt->execute([
      ':nome_empresa' => $empresa['nome_empresa'],
      ':cnpj' => $empresa['cnpj'],
      ':endereco' => $empresa['endereco']
    ]);

    $empresa_id = $pdo->lastInsertId();

    // agora insere o usuário
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

    // redireciona ou mostra sucesso
    header('Location: cadastro_sucesso.php');
    exit;
  } else {
    // se não houver dados da empresa na sessão
    header('Location: criar_empresa.php');
    exit;
  }
}
