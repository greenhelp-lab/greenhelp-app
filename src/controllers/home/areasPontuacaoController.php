<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$usuarioId = $_SESSION['usuario_id'] ?? null;
$empresaId = null;

if ($usuarioId) {
    $sqlEmpresa = "SELECT id FROM empresas WHERE usuario_id = ?";
    $stmt = $pdo->prepare($sqlEmpresa);
    $stmt->execute([$usuarioId]);
    $empresaId = (int)($stmt->fetch()['id'] ?? 0);
}

$sqlAreas = "SELECT id, nome, imagem_url FROM areas_sustentaveis";
$resultAreas = $pdo->query($sqlAreas);

$areasPontuacao = [];

while ($area = $resultAreas->fetch()) {

    $areaId = (int)$area['id'];

    $sqlPont = "
        SELECT pontos, nivel
        FROM pontuacoes_areas
        WHERE empresa_id = ? AND area_id = ?
        LIMIT 1
    ";

    $stmtPont = $pdo->prepare($sqlPont);
    $stmtPont->execute([$empresaId, $areaId]);
    $row = $stmtPont->fetch();

    if ($row) {
        $pontos = (int)$row['pontos'];
        $nivel = (int)$row['nivel'];
    } else {
        $pontos = 0;
        $nivel  = 1;
    }

    $areasPontuacao[] = [
        'id'     => $areaId,
        'nome'   => $area['nome'],
        'icone'  => $area['imagem_url'],
        'pontos' => $pontos,
        'nivel'  => $nivel
    ];
}
