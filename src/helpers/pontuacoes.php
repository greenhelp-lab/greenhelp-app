<?php

function calcularNivelPontuacao(int $pontos): int
{
  return max(1, intdiv(max(0, $pontos), 100) + 1);
}

function buscarEmpresaDoUsuario(PDO $pdo, int $usuarioId): ?int
{
  $stmt = $pdo->prepare("SELECT empresa_id FROM usuarios WHERE id = ? LIMIT 1");
  $stmt->execute([$usuarioId]);
  $empresaId = $stmt->fetchColumn();

  if ($empresaId) {
    return (int)$empresaId;
  }

  $stmt = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id = ? ORDER BY id DESC LIMIT 1");
  $stmt->execute([$usuarioId]);
  $empresaId = $stmt->fetchColumn();

  return $empresaId ? (int)$empresaId : null;
}

function adicionarPontosArea(PDO $pdo, int $empresaId, int $areaId, int $pontos): void
{
  if ($empresaId <= 0 || $areaId <= 0 || $pontos <= 0) {
    return;
  }

  $nivel = calcularNivelPontuacao($pontos);

  $stmt = $pdo->prepare("
    INSERT INTO pontuacoes_areas (empresa_id, area_id, pontos, nivel)
    VALUES (:empresa_id, :area_id, :pontos, :nivel)
    ON DUPLICATE KEY UPDATE
      pontos = pontos + VALUES(pontos),
      nivel = GREATEST(1, FLOOR((pontos + VALUES(pontos)) / 100) + 1)
  ");

  $stmt->execute([
    ':empresa_id' => $empresaId,
    ':area_id' => $areaId,
    ':pontos' => $pontos,
    ':nivel' => $nivel
  ]);
}
