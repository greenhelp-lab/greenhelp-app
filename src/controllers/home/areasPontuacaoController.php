<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/helpers/pontuacoes.php';

$usuarioId = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? null;
$empresaId = $_SESSION['empresa_id'] ?? null;

if (!$empresaId && $usuarioId) {
  $empresaId = buscarEmpresaDoUsuario($pdo, (int)$usuarioId);
  if ($empresaId) {
    $_SESSION['empresa_id'] = $empresaId;
  }
}

$sqlAreas = "SELECT id, nome, imagem_url FROM areas_sustentaveis ORDER BY nome";
$resultAreas = $pdo->query($sqlAreas);

$areasPontuacao = [];

while ($area = $resultAreas->fetch()) {

  $areaId = (int)$area['id'];
  $pontos = 0;
  $nivel = 1;

  if ($empresaId) {
    $stmtPont = $pdo->prepare("
      SELECT pontos, nivel
      FROM pontuacoes_areas
      WHERE empresa_id = ? AND area_id = ?
      LIMIT 1
    ");
    $stmtPont->execute([(int)$empresaId, $areaId]);
    $row = $stmtPont->fetch();

    if ($row) {
      $pontos = (int)$row['pontos'];
      $nivel = (int)$row['nivel'];
    }
  }

  $areasPontuacao[] = [
    'id' => $areaId,
    'nome' => $area['nome'],
    'icone' => $area['imagem_url'],
    'pontos' => $pontos,
    'nivel' => $nivel
  ];
}
