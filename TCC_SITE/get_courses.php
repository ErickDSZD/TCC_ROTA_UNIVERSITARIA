<?php
include "C:/xampp/htdocs/TCC_SITE/conexao.php";

if (isset($_GET['id'])) {
    $idFaculdade = $_GET['id'];

    // Consulta para buscar os cursos associados à faculdade
    $stmt = $conn->prepare("
        SELECT curso.idcurso, curso.nome, curso.carga_horaria
        FROM faculdade_curso
        JOIN curso ON faculdade_curso.idcurso = curso.idcurso
        WHERE faculdade_curso.idfaculdade = ?
    ");
    $stmt->bind_param("i", $idFaculdade);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Exibe o nome do curso com a estrela na frente
            echo '<div class="course-name" data-course-id="' . htmlspecialchars($row['idcurso']) . '">';
            echo '<span class="heart" onclick="addToCart(' . htmlspecialchars($row['idcurso']) . ', ' . htmlspecialchars($idFaculdade) . ')">&#9829;</span>';
            echo "<h3>" . htmlspecialchars($row['nome']) . "</h3>";
            echo "<p>Carga Horária: " . htmlspecialchars($row['carga_horaria']) . "</p>";
            echo '</div>';
        }
    } else {
        echo "Nenhum curso encontrado para esta faculdade.";
    }

    $stmt->close();
}

$conn->close();
?>

