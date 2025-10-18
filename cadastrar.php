<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
require  "conexao.php";

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido. Use POST."], JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if(!isset($dados['nome'], $dados['email'], $dados['senha'])) {
    http_response_code(400);
    echo json_encode(["erro" => "Campos obrigatórios: nome, email, senha."], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome = htmlspecialchars(trim($dados['nome']));
$email = htmlspecialchars(trim($dados['email']));
$senha = htmlspecialchars($dados['senha']);

$sql = "SELECT id from usuarios WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":email", $email);
$stmt->execute();

if($stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["erro" => "E-mail já cadastrado."], JSON_UNESCAPED_UNICODE);
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":nome", $nome);
$stmt->bindValue(":email", $email);
$stmt->bindValue(":senha", $hash);

if($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["mensagem" => "Usuário cadastrado com sucesso."], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao cadastrar"], JSON_UNESCAPED_UNICODE);
}