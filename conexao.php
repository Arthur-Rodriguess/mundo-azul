<?php

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');
$db_charset = 'utf8mb4';

if(!$db_host || !$db_user || $db_pass || $db_name) {
    http_response_code(500);
    die(json_encode(["erro" => "Erro de configuração: Credenciais do banco de dados ausentes no ambiente."]));
}

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    error_log("falha na conexão PDO: " . $e->getMessage());

    http_response_code(500);
    die(json_encode(["erro" => "Erro interno do servidor. Não foi possível conectar ao banco de dados."]));
}