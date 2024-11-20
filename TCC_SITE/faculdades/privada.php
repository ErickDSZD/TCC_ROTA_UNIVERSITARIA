<?php
include "C:/xampp/htdocs/TCC_SITE/conexao.php";

session_start(); // Iniciar a sessão

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado'] : null;
$idUsuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculdades Privadas - Rota Universitária</title>
    <link rel="stylesheet" href="style.css">
    <script src="../script.js" defer></script>
    <!-- Inclua o SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="header">
        <aside class="aside_fixed">
            <img src="logo.png" alt="Logo do site">
            <form action="http://localhost/TCC_SITE/resultado.php" method="GET">
                <input type="text" name="pesquisar" id="pesquisar" placeholder="Pesquise por uma faculdade">
                <button class="button_pesquisar" type="submit">Pesquisar</button>
            </form>
        </aside>
    </header>

    <aside class="aside_href">
            <p><a href="../progama_bolsa/cursos.php">Cursos</a></p>
            <p onmouseover="infPrograma()"><a onmouseover="infPrograma()" id="programabolsa" href="">Programa de bolsas</a></p>
            <p onmouseover="infFaculdade()"><a onmouseover="infFaculdade()" id="faculdade" href="">Faculdades</a></p>
            <p><a class="logo" href="../index.php">Sobre</a></p>
        </aside>

        <!-- Aba de texto sobre programa de bolsas -->

        <div onmouseleave="disabledProg()" id="prog_none" class="prog_none">
            <div>
                <h1><a href="../progama_bolsa/prouni.php">PROUNI</a></h1>
                <h1><a href="../progama_bolsa/sisu.php">SISU</a></h1>
                <h1><a href="../progama_bolsa/fies.php">FIES</a></h1>
                <h1><a href="../progama_bolsa/enem.php">ENEM</a></h1>
                <h1><a href="../progama_bolsa/fuvest.php">FUVEST</a></h1>
                <h1><a href="../progama_bolsa/vunesp.php">VUNESP</a></h1>
                <h1><a href="../progama_bolsa/unicamp.php">UNICAMP</a></h1>
                <h1><a href="../progama_bolsa/ufmt.php">UFMT</a></h1>
                <h1><a href="../progama_bolsa/ufscar.php">UFSCAR</a></h1>
            </div>
        </div>

        <!-- Aba de texto sobre faculdades -->

        <div onmouseleave="disabledFacul()" id="facul_none" class="facul_none">
            <div>
                <h1><a href="publica.php">Faculdades Públicas</a></h1>
                <h1><a href="privada.php">Faculdades Privadas</a></h1>
            </div>
        </div>

    <main class="corpo_main">
        <h2>AQUI ESTÁ UMA LISTA DE FACULDADES PRIVADAS:</h2>

        <?php
        // Consulta SQL para selecionar as faculdades onde "PUBLICA" é NULL
        $sql = "SELECT * FROM faculdade WHERE PUBLICA IS NULL";
        $result = $conn->query($sql);

        // Verifica se há resultados e os exibe
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $idFaculdade = htmlspecialchars($row['idfaculdade']); // Define o ID da faculdade
                $nomeFaculdade = htmlspecialchars($row["nome"]);
                echo '<div class="faculdade-card" onclick="openModal(\'' . $idFaculdade . '\', \'' . $nomeFaculdade . '\')">';
                echo '<div class="faculdade-name">' . $nomeFaculdade . '</div>';
                echo '<div class="faculdade-details">';
                echo 'Localização: ' . htmlspecialchars($row["logradouro"]) . '<br>';
                echo 'Telefone: ' . htmlspecialchars($row["telefone"]) . '<br>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "Nenhuma faculdade privada encontrada.";
        }

        $conn->close(); // Fecha a conexão com o banco de dados
        ?>
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

    <footer class="footer">
        <div class="footer_content">
            <h2>Contato</h2>
            <div class="footer_section">
                <div class="p">
                    <p>Email: rotauniversitaria04@gmail.com</p>
                </div>
                <div class="p">
                    <p>Telefone: (11) 12345-6789</p>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <p>&copy; 2024 Rota Universitária. Todos os direitos reservados.</p>
        </div>
    </footer>

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
                console.log("Resposta do servidor:", xhr.responseText); // Adicionado para depuração

                try {
                    var response = JSON.parse(xhr.responseText);

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
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Resposta do servidor inválida.',
                        confirmButtonColor: '#f2513f',
                        background: '#ffecc7',
                        iconColor: '#f2513f'
                    });
                    console.error("Erro ao analisar JSON:", e);
                }
            };

            xhr.send("idusuario=" + encodeURIComponent(clientId) +
                     "&idfaculdade=" + encodeURIComponent(faculdadeId) +
                     "&idcurso=" + encodeURIComponent(courseId));
        }

        // Exibe o ID do cliente no console
        console.log("Client ID: ", clientId);
    </script>
</body>
</html>

