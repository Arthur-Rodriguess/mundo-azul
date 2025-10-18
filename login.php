<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require "conexao.php";

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => "Método não permitido. Use POST."], JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if(!isset($dados['email'], $dados['senha'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Campos obrigatórios: email e senha'], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = trim($dados['email']);
$senha = $dados['senha'];

$sql = "SELECT * FROM usuarios WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":email", $email);
$stmt->execute();

if($stmt->rowCount() === 0) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if(!password_verify($senha, $usuario['senha'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Senha incorreta'], JSON_UNESCAPED_UNICODE);
    exit;
}

$token = bin2hex(random_bytes(16));

$sql = "UPDATE usuarios SET token = :token WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":token", $token);
$stmt->bindValue(":id", $usuario['id']);
$stmt->execute();

http_response_code(200);
echo json_encode([
    'mensagem' => 'Login bem-sucedido',
    'usuario' => [
        'id' => $usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email']
    ],
    'token' => $token
], JSON_UNESCAPED_UNICODE);