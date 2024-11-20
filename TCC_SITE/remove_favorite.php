<?php
include "conexao.php"; // Inclui a conexão com o banco de dados

session_start(); // Iniciar a sessão

$idUsuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null;

if ($idUsuario && isset($_POST['idfaculdade']) && isset($_POST['idcurso'])) {
    $idFaculdade = $_POST['idfaculdade'];
    $idCurso = $_POST['idcurso'];

    // Remover o item do carrinho
    $sql = "DELETE FROM carrinho WHERE idusuario = ? AND idfaculdade = ? AND idcurso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $idUsuario, $idFaculdade, $idCurso);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
} else {
    echo "unauthorized";
}

$conn->close();
?>

