<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (($_SESSION['papel'] ?? '') !== 'admin') {
  http_response_code(403);
  if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/src/controllers/') !== false) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Acesso negado']);
    exit;
  }
  exit('Acesso negado');
}
