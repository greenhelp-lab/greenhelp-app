<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/actions/serviços.php';

$servicoModel = new Servico($pdo);

$action = $_GET['action'] ?? null;

// --- CREATE ---
if ($action === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $dados = [
    'nome' => $_POST['nome'],
    'descricao' => $_POST['descricao'],
    'preco' => $_POST['preco'],
    'pontos' => $_POST['pontos'],
    'categoria' => $_POST['categoria'] ?? 'Sem categoria',
    'disponivel' => 1,
    'descricao_longa' => $_POST['descricao_longa'] ?? '',
    'itens_incluidos' => $_POST['itens_incluidos'] ?? '',
    'garantia' => $_POST['garantia'] ?? '12 meses',
    'prazo' => $_POST['prazo'] ?? '',
    'contato' => $_POST['contato'] ?? 'contato@greenhelp.tech',
    'area_id' => $_POST['area_id'],
  ];

  $servicoModel->criar($dados);
  header('Location: /greenhelp-app/src/pages/db_views/db_criar_servico.php?msg=criado');
  exit;
}

// --- UPDATE ---
if ($action === 'atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $dados = [
    'nome' => $_POST['nome'],
    'descricao' => $_POST['descricao'],
    'preco' => $_POST['preco'],
    'pontos' => $_POST['pontos'],
    'categoria' => $_POST['categoria'],
    'disponivel' => $_POST['disponivel'],
    'descricao_longa' => $_POST['descricao_longa'] ?? '',
    'itens_incluidos' => $_POST['itens_incluidos'] ?? '',
    'garantia' => $_POST['garantia'],
    'prazo' => $_POST['prazo'],
    'contato' => $_POST['contato'],
    'area_id' => $_POST['area_id'],
  ];

  $servicoModel->atualizar($id, $dados);
  header('Location: /greenhelp-app/src/pages/db_views/db_criar_servico.php?msg=atualizado');
  exit;
}

// --- DELETE ---
if ($action === 'deletar' && isset($_GET['id'])) {
  $servicoModel->deletar($_GET['id']);
  header('Location: /greenhelp-app/src/pages/db_views/db_criar_servico.php?msg=deletado');
  exit;
}
