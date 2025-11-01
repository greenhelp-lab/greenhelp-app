<?php

// impede acesso direto via navegador
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  http_response_code(403);
  exit('Acesso direto negado.');
}

$host = 'localhost';
$dbname = 'greenhelp_db';
$usuario = 'root';
$senha = '';

try {
  // cria objeto PDO e define modo de erro para exceções
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}
