<?php
include "C:/xampp/htdocs/TCC_SITE/conexao.php";

session_start(); // Iniciar a sessão

// Verificar se o usuário está logado
$idUsuario = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$idUsuario) {
    echo 'Você precisa estar logado para adicionar ao carrinho.';
    exit;
}

// Adicionar ao carrinho
if (isset($_POST['idfaculdade']) && isset($_POST['idcurso'])) {
    $idFaculdade = $_POST['idfaculdade'];
    $idCurso = $_POST['idcurso'];

    $stmt = $conn->prepare("INSERT INTO carrinho (idusuario, idcurso, idfaculdade, data_adicao) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $idUsuario, $idCurso, $idFaculdade);
    $stmt->execute();
    $stmt->close();

    echo 'Faculdade adicionada ao carrinho com sucesso.';
} else {
    echo 'Dados insuficientes para adicionar ao carrinho.';
}
?>

