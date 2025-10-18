<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Tenta fazer a conexão
require 'conexao.php';
// Caso dê certo vai aparecer essa mensagem
echo "Conexão bem-sucedida!";