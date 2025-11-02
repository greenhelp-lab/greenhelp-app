<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';

$empresaId = $_SESSION['empresa_id'] ?? null;

$sql = "SELECT * FROM empresas WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $empresaId, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch();

echo json_encode($user);