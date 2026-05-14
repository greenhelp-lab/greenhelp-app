<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  http_response_code(403);
  exit('Acesso direto negado.');
}

$env = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/.env');
$dbhost = $env["DB_HOST"];
$dbuser = $env["DB_USER"];
$dbpass = $env["DB_PASS"];
$dbname = $env["DB_NAME"];
$dbport = $env["PORT"];

try {
  $pdo = new PDO(
    "mysql:host=" . $dbhost . ";port=" . $dbport . ";dbname=" . $dbname,
    $dbuser,
    $dbpass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}
