<?php
include "conexao.php";
session_start();

if (isset($_SESSION['idusuario'])) {
    $idUsuario = $_SESSION['idusuario'];

    // Excluir registros do carrinho antes de deletar o usuário
    $sql_delete_carrinho = "DELETE FROM carrinho WHERE idusuario = ?";
    $stmt_carrinho = $conn->prepare($sql_delete_carrinho);
    $stmt_carrinho->bind_param("i", $idUsuario);
    $stmt_carrinho->execute();
    $stmt_carrinho->close();

    // Excluir o usuário do banco de dados
    $sql_delete_usuario = "DELETE FROM usuario WHERE idusuario = ?";
    $stmt_usuario = $conn->prepare($sql_delete_usuario);
    $stmt_usuario->bind_param("i", $idUsuario);

    if ($stmt_usuario->execute()) {
        session_destroy(); // Encerra a sessão
        header("Location: index.php?msg=conta_deletada"); // Redireciona para a página inicial
        exit();
    } else {
        echo "Erro ao deletar a conta.";
    }

    $stmt_usuario->close();
} else {
    echo "Usuário não autenticado.";
}

$conn->close();
?>
