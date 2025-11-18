<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefone = trim($_POST['telefone'] ?? '');
  $senha = $_POST['senha'] ?? '';
  // empresa association: either existing id, or create new when empresa_id === 'new'
  $empresa_id = $_POST['empresa_id'] ?? '';
  $empresa_nome = trim($_POST['empresa_nome'] ?? '');
  $empresa_cnpj = trim($_POST['empresa_cnpj'] ?? '');
  $empresa_setor = trim($_POST['empresa_setor'] ?? '');
  $empresa_porte = trim($_POST['empresa_porte'] ?? '');

  if (!$nome || !$email || !$senha) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_cliente.php?error=campos_obrigatorios");
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_cliente.php?error=email_invalido");
    exit;
  }

  $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
  $stmt->execute([':email' => $email]);
  if ($stmt->fetchColumn()) {
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_cliente.php?error=email_existente");
    exit;
  }

  // Criar/associar empresa dentro de transação quando necessário
  try {
    $pdo->beginTransaction();

    if ($empresa_id === 'new' || ($empresa_id === '' && $empresa_nome !== '')) {
      $ins = $pdo->prepare("INSERT INTO empresas (nome, cnpj, setor_atuacao, porte) VALUES (:nome, :cnpj, :setor, :porte)");
      $ins->execute([
        ':nome' => $empresa_nome,
        ':cnpj' => $empresa_cnpj,
        ':setor' => $empresa_setor,
        ':porte' => $empresa_porte
      ]);
      $empresa_id = $pdo->lastInsertId();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir usuário com possível empresa_id (ou null)
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha, papel, ativo, empresa_id) VALUES (:nome, :email, :telefone, :senha, :papel, :ativo, :empresa_id)");
    $stmt->execute([
      ':nome' => $nome,
      ':email' => $email,
      ':telefone' => $telefone,
      ':senha' => $senha_hash,
      ':papel' => 'cliente',
      ':ativo' => 1,
      ':empresa_id' => $empresa_id !== '' ? $empresa_id : null
    ]);

    $pdo->commit();
    header("Location: " . BASE_URL . "/src/pages/painel_admin/painel_admin.php?status=cliente_criado");
    exit;
  } catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao criar cliente/empresa: " . $e->getMessage());
    header("Location: " . BASE_URL . "/src/pages/painel_admin/criar_cliente.php?error=erro_banco");
    exit;
  }
} else {
  header("Location: " . BASE_URL . "/src/pages/painel_admin/painel_admin.php");
  exit;
}
