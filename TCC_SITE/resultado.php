<?php
include "conexao.php"; // Inclui a conexão com o banco de dados

session_start(); // Iniciar a sessão

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado'] : null;
$idUsuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null; // Certifique-se de usar 'idusuario' consistentemente

// Inicializa a variável de resultado
$resultado = "";

// Verificar se há um termo de pesquisa
$termoPesquisa = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';
$termoPesquisa = '%' . $conn->real_escape_string($termoPesquisa) . '%'; // Adiciona os curingas e faz escaping para prevenir SQL Injection

// Pesquisa e exibição de faculdades
$sql = "SELECT * FROM faculdade WHERE nome LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $termoPesquisa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $idFaculdade = htmlspecialchars($row['idfaculdade']);
        $nomeFaculdade = htmlspecialchars($row["nome"]);
        $resultado .= '<div class="faculdade-card" onclick="openModal(\'' . $idFaculdade . '\', \'' . $nomeFaculdade . '\')">';
        $resultado .= '<div class="faculdade-name">' . $nomeFaculdade . '</div>';
        $resultado .= '<div class="faculdade-details">';
        $resultado .= 'Localização: ' . htmlspecialchars($row["logradouro"]) . '<br>';
        $resultado .= 'Telefone: ' . htmlspecialchars($row["telefone"]) . '<br>';
        $resultado .= '</div>';

        if ($idUsuario) {
            // Removendo o ícone de estrela pois será adicionado no modal
            // $resultado .= '<span class="star" onclick="addToCart(1, ' . $idFaculdade . ')">&#9733;</span>'; // ID do curso temporário
        }

        $resultado .= '</div>';
    }
} else {
    $resultado = "Nenhuma faculdade encontrada com o nome fornecido.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localizar - Faculdades</title>
    <link rel="stylesheet" href="css/pesquisa.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <main class="corpo_main">
        <div class="pesquisa-info">
            <a class="a_home" href="index.php"><img class="home" src="imagens/desfazer.png" alt="Voltar"></a>
            <?php if (!empty($termoPesquisa)): ?>
                <p>Você pesquisou por: <strong><?php echo htmlspecialchars(str_replace('%', '', $termoPesquisa)); ?></strong></p>
            <?php endif; ?>
        </div>
        <div class="container">
            <?php echo $resultado; ?>
        </div>
    </main>

    <!-- Modal -->
    <div id="courseModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Cursos da Faculdade</h2>
            <div id="modal-body">
                <!-- Cursos serão carregados aqui via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // Captura o ID do cliente da sessão PHP
        var clientId = <?php echo json_encode($idUsuario); ?>;

        function openModal(faculdadeId, faculdadeName) {
            // Atualiza o título do modal com o nome da faculdade
            document.getElementById('modalTitle').innerText = `Cursos da ${faculdadeName}`;

            // Faz uma requisição AJAX para buscar os cursos
            loadCourses(faculdadeId);

            // Mostra o modal
            document.getElementById('courseModal').style.display = 'block';
        }

        function closeModal() {
            // Fecha o modal
            document.getElementById('courseModal').style.display = 'none';
        }

        function loadCourses(faculdadeId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `http://localhost/TCC_SITE/get_courses.php?id=${faculdadeId}`, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    document.getElementById('modal-body').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        function addToCart(courseId, faculdadeId) {
        if (!clientId) {
            Swal.fire({
                icon: 'warning',
                title: 'Ops...',
                text: 'Por favor, faça login para adicionar cursos ao carrinho.',
                confirmButtonColor: '#f2513f',
                background: '#ffecc7',
                iconColor: '#ff914d'
            });
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://localhost/TCC_SITE/add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                console.log(xhr.responseText);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Curso adicionado ao carrinho com sucesso!',
                        confirmButtonColor: '#4ebc6e',
                        background: '#ffecc7',
                        iconColor: '#4ebc6e'
                    });
                } else if (response.status === 'exists') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Já Adicionado!',
                        text: 'Este curso já foi adicionado ao carrinho.',
                        confirmButtonColor: '#ff914d',
                        background: '#ffecc7',
                        iconColor: '#ff914d'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao adicionar curso ao carrinho.',
                        confirmButtonColor: '#f2513f',
                        background: '#ffecc7',
                        iconColor: '#f2513f'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao adicionar curso ao carrinho.',
                    confirmButtonColor: '#f2513f',
                    background: '#ffecc7',
                    iconColor: '#f2513f'
                });
            }
        };

        xhr.send("idusuario=" + encodeURIComponent(clientId) +
                "&idfaculdade=" + encodeURIComponent(faculdadeId) +
                "&idcurso=" + encodeURIComponent(courseId));
    }

    </script>
</body>
</html>

