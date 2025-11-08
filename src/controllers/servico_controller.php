<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
include_once BASE_PATH . '/src/actions/servicos.php';

$model = new Servico($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = $_POST['acao'] ?? '';
  $id = $_POST['id'] ?? null;

  switch ($acao) {
    case 'salvar':
      $dados = [
        'id' => $id,
        'nome' => $_POST['nome'],
        'data_criacao' => $_POST['data_criacao'],
        'area_id' => $_POST['area_id'],
        'preco' => $_POST['preco'],
        'prazo_dias' => $_POST['prazo_dias'],
        'pontos' => $_POST['pontos'],
        'descricao' => $_POST['descricao']
      ];
      $model->atualizar($dados);
      header("Location: " . BASE_URL . "/src/pages/db_views/servico.php?id={$id}&msg=atualizado");
      exit;

    case 'deletar':
      if ($id) {
        $model->deletar($id);
        header("Location: " . BASE_URL . "/src/pages/db_views/lista_servicos.php?msg=deletado");
        exit;
      }
      break;
  }
}
?>
