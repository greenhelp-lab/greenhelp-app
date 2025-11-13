<?php
declare(strict_types=1);

include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
  if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Não autenticado']);
    exit;
  }
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método não permitido']);
    exit;
  }

  $userId = (int) $_SESSION['user_id'];
  $redirectUrl = rtrim(BASE_URL, '/') . '/src/pages/login/login.php';

  $pdo->beginTransaction();

  // 1) Pega avatar (para tentar excluir arquivo depois)
  $st = $pdo->prepare('SELECT avatar_path FROM usuarios WHERE id = :id FOR UPDATE');
  $st->execute([':id' => $userId]);
  $avatarPath = $st->fetchColumn();

  // 2) QUEBRA A REFERÊNCIA CIRCULAR: zera empresa_id do usuário
  $pdo->prepare('UPDATE usuarios SET empresa_id = NULL WHERE id = :id')
      ->execute([':id' => $userId]);

  // 3) Apaga registros do usuário em servicos_andamento
  $pdo->prepare('DELETE FROM servicos_andamento WHERE usuario_id = :id')
      ->execute([':id' => $userId]);

  // 4) Descobre empresas do usuário
  $stmtEmp = $pdo->prepare('SELECT id FROM empresas WHERE usuario_id = :id');
  $stmtEmp->execute([':id' => $userId]);
  $empIds = $stmtEmp->fetchAll(PDO::FETCH_COLUMN);

  if ($empIds) {
    $in = implode(',', array_fill(0, count($empIds), '?'));

    // 4.1) Apaga dependentes dessas empresas
    $pdo->prepare("DELETE FROM pedidos WHERE empresa_id IN ($in)")->execute($empIds);
    $pdo->prepare("DELETE FROM pontuacoes_sustentaveis WHERE empresa_id IN ($in)")->execute($empIds);

    // 4.2) Agora pode apagar as empresas
    $pdo->prepare("DELETE FROM empresas WHERE id IN ($in)")->execute($empIds);
  }

  // 5) Carrinho tem FK com CASCADE; não precisa, mas se quiser garantir:
  // $pdo->prepare('DELETE FROM carrinho WHERE usuario_id = :id')->execute([':id' => $userId]);

  // 6) Por último: apaga o usuário
  $del = $pdo->prepare('DELETE FROM usuarios WHERE id = :id LIMIT 1');
  $ok  = $del->execute([':id' => $userId]);

  if (!$ok || $del->rowCount() === 0) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Falha ao excluir usuário (0 linhas)']);
    exit;
  }

  $pdo->commit();

  // Finaliza sessão
  session_unset();
  session_destroy();

  // Remove avatar local (se não for URL http)
  if (!empty($avatarPath) && !preg_match('~^https?://~i', $avatarPath)) {
    $abs = $avatarPath[0] === '/' ? $_SERVER['DOCUMENT_ROOT'] . $avatarPath : $avatarPath;
    @unlink($abs);
  }

  echo json_encode(['ok' => true, 'redirect' => $redirectUrl]);
} catch (PDOException $e) {
  if ($pdo?->inTransaction()) $pdo->rollBack();
  $msg = $e->getCode() === '23000'
    ? 'Não foi possível excluir: há registros vinculados por chave estrangeira.'
    : ('Exceção PDO: ' . $e->getMessage());
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $msg]);
} catch (Throwable $e) {
  if ($pdo?->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Exceção: ' . $e->getMessage()]);
}
