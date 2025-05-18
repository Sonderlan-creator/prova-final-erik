<?php
$servidor = "localhost";
$usuario = "root";         
$senha = "";               
$banco = "lottus";      

$conn = new mysqli($servidor, $usuario, $senha, $banco);

if ($conn->connect_error) {

    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Falha na conexão com o banco de dados']);
    exit;
}


$conn->set_charset("utf8");
