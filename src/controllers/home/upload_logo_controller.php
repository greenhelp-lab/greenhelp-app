<?php

declare(strict_types=1);

// SEM HTML DE ERRO
ini_set('display_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/upload_logo_error.log');
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';


try {
  // --- Auth / empresa ---
  $userId = $_SESSION['user_id'] ?? null;
  if (!$userId) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'usuario_nao_autenticado']);
    exit;
  }

  $empresaId = $_SESSION['empresa_id'] ?? null;
  if (!$empresaId) {
    $st = $pdo->prepare("SELECT id FROM empresas WHERE usuario_id = :uid ORDER BY id DESC LIMIT 1");
    $st->execute([':uid' => $userId]);
    $empresaId = $st->fetchColumn() ?: null;
    if ($empresaId) $_SESSION['empresa_id'] = (int)$empresaId;
  }
  if (!$empresaId) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'empresa_nao_autenticada']);
    exit;
  }

  // --- Arquivo ---
  if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'arquivo_invalido']);
    exit;
  }
  $f = $_FILES['logo'];
  if ($f['size'] > 3 * 1024 * 1024) {
    http_response_code(413);
    echo json_encode(['ok' => false, 'error' => 'arquivo_maior_3mb']);
    exit;
  }

  $fi   = new finfo(FILEINFO_MIME_TYPE);
  $mime = $fi->file($f['tmp_name']);
  $exts = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  if (!isset($exts[$mime])) {
    http_response_code(415);
    echo json_encode(['ok' => false, 'error' => 'tipo_nao_suportado']);
    exit;
  }
  $ext = $exts[$mime];

  // valida imagem real
  if (!getimagesize($f['tmp_name'])) {
    http_response_code(415);
    echo json_encode(['ok' => false, 'error' => 'nao_e_imagem']);
    exit;
  }

  // --- Pasta destino ---
  $dir = $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/public/uploads/logos';
  if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'falha_criar_diretorio']);
    exit;
  }

  // apaga antiga e salva nova
  foreach (glob($dir . "/e{$empresaId}.*") as $old) {
    @unlink($old);
  }
  $dest   = "{$dir}/e{$empresaId}.{$ext}";
  $public = rtrim(BASE_URL, '/') . "/public/uploads/logos/e{$empresaId}.{$ext}";

  if (!move_uploaded_file($f['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'falha_ao_salvar']);
    exit;
  }

  // --- Redimensiona (máx 1080px) SE GD ESTIVER DISPONÍVEL ---
  if (function_exists('imagecreatetruecolor')) {
    [$w, $h] = getimagesize($dest);
    $scale = min(1, 1080 / max($w, $h));

    if ($scale < 1) {
      $nw = (int)round($w * $scale);
      $nh = (int)round($h * $scale);

      // loaders com checagem de suporte
      $src = null;
      if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
        $src = imagecreatefromjpeg($dest);
      } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
        $src = imagecreatefrompng($dest);
      } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
        $src = imagecreatefromwebp($dest);
      }

      // se não tiver suporte ao tipo no GD, apenas NÃO redimensiona
      if ($src) {
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $ok = true;
        if ($mime === 'image/jpeg' && function_exists('imagejpeg')) {
          $ok = imagejpeg($dst, $dest, 88);
        } elseif ($mime === 'image/png' && function_exists('imagepng')) {
          $ok = imagepng($dst, $dest, 6);
        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
          $ok = imagewebp($dst, $dest, 88);
        }

        imagedestroy($src);
        imagedestroy($dst);

        if (!$ok) {
          http_response_code(500);
          echo json_encode(['ok' => false, 'error' => 'falha_redimensionar']);
          exit;
        }
      }
      // se $src for null, seguimos com a imagem original sem erro
    }
  }
  // se não tiver GD, simplesmente NÃO redimensiona e segue com o arquivo original

  // --- Atualiza BD ---
  $st = $pdo->prepare("UPDATE empresas SET logo_path = :p WHERE id = :id");
  $st->execute([':p' => $public, ':id' => $empresaId]);

  echo json_encode(['ok' => true, 'url' => $public]);
} catch (Throwable $e) {
  // algo explodiu (ex.: permissão etc.)
  error_log('upload_logo.php: ' . $e->getMessage());
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'excecao', 'msg' => $e->getMessage()]);
}
