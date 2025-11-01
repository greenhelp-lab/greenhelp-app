<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app'); // Define o caminho base da plataforma - bom para usar em qualquer caminho de PHP!
define('BASE_URL', '/greenhelp-app'); // Define a URL base da plataforma - bom para usar em links de CSS, JS, imagens, etc.

// CONFIGURAÇÃO DE BANCO DE DADOS
const DB_HOST = '127.0.0.1';
const DB_NAME = 'greenhelp_db'; // nome do seu banco
const DB_USER = 'root';
const DB_PASS = '';

try {
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erro na conexão: ' . $e->getMessage());
}


