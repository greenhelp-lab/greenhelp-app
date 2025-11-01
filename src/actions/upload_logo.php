<?php
declare(strict_types=1);
ini_set('display_errors', 1); error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/src/config/config.php'; // expõe $pdo e BASE_URL

// 1) empresa logada (ajuste a chave da sessão se for diferente)
$empresaId = $_SESSION['empresa_id'] ?? null;
if (!$empresaId) { http_response_code(401); exit('nao autenticado'); }

// 2) valida arquivo
if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
  http_response_code(400); exit('arquivo invalido');
}
$f = $_FILES['logo'];
if ($f['size'] > 3*1024*1024) { http_response_code(413); exit('ate 3MB'); }

$fi = new finfo(FILEINFO_MIME_TYPE);
$mime = $fi->file($f['tmp_name']);
$exts = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if (!isset($exts[$mime])) { http_response_code(415); exit('use jpg/png/webp'); }
$ext = $exts[$mime];
if (!getimagesize($f['tmp_name'])) { http_response_code(415); exit('nao e imagem'); }

// 3) pasta
$dir = $_SERVER['DOCUMENT_ROOT'].'/greenhelp-app/public/uploads/logos';
if (!is_dir($dir)) mkdir($dir, 0755, true);

// 4) apaga logo antigo e salva novo (pode trocar sempre)
foreach (glob($dir."/e{$empresaId}.*") as $old) { @unlink($old); }
$dest   = "{$dir}/e{$empresaId}.{$ext}";
$public = rtrim(BASE_URL,'/')."/public/uploads/logos/e{$empresaId}.{$ext}";

if (!move_uploaded_file($f['tmp_name'], $dest)) { http_response_code(500); exit('falha ao salvar'); }

// 5) (opcional) redimensiona para 1080px (maior lado)
[$w,$h] = getimagesize($dest);
$scale = min(1, 1080 / max($w,$h));
if ($scale < 1) {
  $nw = (int)round($w*$scale); $nh = (int)round($h*$scale);
  $src = $mime==='image/jpeg' ? imagecreatefromjpeg($dest)
       : ($mime==='image/png' ? imagecreatefrompng($dest) : imagecreatefromwebp($dest));
  $dst = imagecreatetruecolor($nw,$nh);
  imagealphablending($dst,false); imagesavealpha($dst,true);
  imagecopyresampled($dst,$src,0,0,0,0,$nw,$nh,$w,$h);
  if ($mime==='image/jpeg') imagejpeg($dst,$dest,88);
  elseif ($mime==='image/png') imagepng($dst,$dest,6);
  else imagewebp($dst,$dest,88);
  imagedestroy($src); imagedestroy($dst);
}

// 6) atualiza BD
$st = $pdo->prepare("UPDATE empresas SET logo_path = :p WHERE id = :id");
$st->execute([':p'=>$public, ':id'=>$empresaId]);

header('Content-Type: application/json');
echo json_encode(['ok'=>true, 'url'=>$public]);
