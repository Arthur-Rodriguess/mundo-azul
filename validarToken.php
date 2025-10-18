<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require 'conexao.php';

$headers = getallheaders();

if(!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token não enviado'], JSON_UNESCAPED_UNICODE);
    exit;
}

if(!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de token inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$token = $matches[1];

$sql = "SELECT id, nome, email FROM usuarios WHERE token = :token";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":token", $token);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
http_response_code(200);
echo json_encode([
    'autenticado' => true,
    'usuario' => $usuario
], JSON_UNESCAPED_UNICODE);