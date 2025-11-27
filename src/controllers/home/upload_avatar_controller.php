<?php
declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        throw new RuntimeException('nao autenticado');
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('arquivo invalido');
    }

    $f = $_FILES['foto'];

    if ($f['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('ate 3MB');
    }

    $fi   = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($f['tmp_name']);

    $exts = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($exts[$mime])) {
        throw new RuntimeException('use jpg/png/webp');
    }

    $ext = $exts[$mime];

    if (!getimagesize($f['tmp_name'])) {
        throw new RuntimeException('nao e imagem');
    }

    $dir = $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/public/uploads/avatars';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // apaga antiga e salva nova
    foreach (glob($dir . "/u{$userId}.*") as $old) {
        @unlink($old);
    }

    $dest   = "{$dir}/u{$userId}.{$ext}";
    $public = rtrim(BASE_URL, '/') . "/public/uploads/avatars/u{$userId}.{$ext}";

    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new RuntimeException('falha ao salvar arquivo');
    }

    // (opcional) redimensiona
    [$w, $h] = getimagesize($dest);
    $scale   = min(1, 1080 / max($w, $h));

    if ($scale < 1 && function_exists('imagecreatetruecolor')) {
        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);

        if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
            $src = imagecreatefromjpeg($dest);
        } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
            $src = imagecreatefrompng($dest);
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $src = imagecreatefromwebp($dest);
        } else {
            $src = null;
        }

        if ($src) {
            $dst = imagecreatetruecolor($nw, $nh);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            if ($mime === 'image/jpeg') {
                imagejpeg($dst, $dest, 88);
            } elseif ($mime === 'image/png') {
                imagepng($dst, $dest, 6);
            } else {
                imagewebp($dst, $dest, 88);
            }

            imagedestroy($src);
            imagedestroy($dst);
        }
    }

    // atualiza no banco
    $st = $pdo->prepare("UPDATE usuarios SET avatar_path = :p WHERE id = :id");
    $st->execute([':p' => $public, ':id' => $userId]);

    echo json_encode(['ok' => true, 'url' => $public]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => $e->getMessage()
    ]);
}
