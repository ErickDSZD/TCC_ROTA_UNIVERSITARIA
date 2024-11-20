<?php
include "C:/xampp/htdocs/TCC_SITE/conexao.php";

session_start();

$idUsuario = isset($_POST['idusuario']) ? $_POST['idusuario'] : null;
$idCurso = isset($_POST['idcurso']) ? $_POST['idcurso'] : null;
$idFaculdade = isset($_POST['idfaculdade']) ? $_POST['idfaculdade'] : null;

header('Content-Type: application/json'); // Adiciona cabeçalho para JSON

if ($idUsuario && $idCurso && $idFaculdade) {
    // Verifica se o curso já está no carrinho
    $sqlCheck = "SELECT * FROM carrinho WHERE idusuario = ? AND idcurso = ? AND idfaculdade = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("iii", $idUsuario, $idCurso, $idFaculdade);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Curso já está no carrinho
        echo json_encode(array('status' => 'exists'));
    } else {
        // Adiciona o curso ao carrinho
        $sqlInsert = "INSERT INTO carrinho (idusuario, idcurso, idfaculdade, data_adicao) VALUES (?, ?, ?, NOW())";
        $stmtInsert = $conn->prepare($sqlInsert);
        if ($stmtInsert) {
            $stmtInsert->bind_param("iii", $idUsuario, $idCurso, $idFaculdade);
            if ($stmtInsert->execute()) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Erro ao adicionar ao carrinho.'));
            }
            $stmtInsert->close(); // Fecha o statement de inserção
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Erro na preparação da inserção.'));
        }
    }
    
    $stmtCheck->close(); // Fecha o statement de verificação
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Dados inválidos.'));
}

$conn->close();
?>

