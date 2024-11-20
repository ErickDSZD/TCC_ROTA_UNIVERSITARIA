<?php
include "conexao.php";

$nome = $_GET['nome'];
$email = $_GET['email'];
$senha = $_GET['senha'];
$cep = $_GET['cep'];
$logradouro = $_GET['logradouro'];
$numero = $_GET['numero'];
$bairro = $_GET['bairro'];
$estado = $_GET['estado'];
$cidade = $_GET['cidade'];

// Validação básica do lado do servidor
if (empty($nome) || empty($email) || empty($senha) || empty($cep) || empty($numero)  || empty($logradouro) || empty($bairro) || empty($estado) || empty($cidade)) {
    echo "Por favor, preencha todos os campos.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Formato de email inválido.";
    exit;
}

// Verificar se o usuário já existe
$sql_verificar = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql_verificar);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: cadastro.php?erro=usuario_ja_existe");
    exit;
}

// Buscar o idcidade com base no nome da cidade
$sql_busca_cidade = "SELECT idcidade FROM cidade WHERE nome = ?";
$stmt_cidade = $conn->prepare($sql_busca_cidade);
$stmt_cidade->bind_param("s", $cidade);
$stmt_cidade->execute();
$stmt_cidade->bind_result($idcidade);
$stmt_cidade->fetch();
$stmt_cidade->close();

// Verifica se a cidade foi encontrada
if (!$idcidade) {
    echo "Cidade não encontrada.";
    exit;
}

// Criando o hash da senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir no banco usando Prepared Statements
$sql = "INSERT INTO usuario (nome, email, senha, cep, numero, logradouro, bairro, idcidade, idestado)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $nome, $email, $senhaHash, $cep, $numero, $logradouro, $bairro, $idcidade, $estado);

if ($stmt->execute()) {
    // Iniciar a sessão e logar o usuário
    session_start();

    // Obter o ID do último usuário inserido
    $idUsuario = $conn->insert_id;

    // Consultar os dados completos do usuário
    $sql_usuario = "SELECT idusuario, nome, email FROM usuario WHERE idusuario = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $idUsuario);
    $stmt_usuario->execute();
    $stmt_usuario->bind_result($idusuario, $nomeUsuario, $emailUsuario);
    $stmt_usuario->fetch();

    // Definir variáveis de sessão
    $_SESSION['usuario_logado'] = $emailUsuario;
    $_SESSION['idusuario'] = $idusuario;
    $_SESSION['nome'] = $nomeUsuario;

    $stmt_usuario->close();
    header("Location: index.php");
    exit;
}
else {
    error_log("Erro ao cadastrar usuário: " . $stmt->error);
    echo "Ocorreu um erro. Por favor, tente novamente mais tarde.";
}


$stmt->close();
$conn->close();
?>

