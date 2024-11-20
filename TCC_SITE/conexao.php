<?php
// Configurações do banco de dados
$host = 'localhost'; // ou o IP do servidor
$user = 'root';
$password = '';
$database = 'tcc_rota';

// Cria a conexão
$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>

