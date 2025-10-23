<?php
// conexao.php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "greenhelp";

// Criar conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
