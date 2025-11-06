<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/config/config.php';
session_unset();  // limpa todas as variáveis da sessão
session_destroy(); // encerra a sessão
header("Location: ' . BASE_URL . '/public/sobre_nos.php"); // redireciona para a página sobre_nós, famosa landing page!
exit();
