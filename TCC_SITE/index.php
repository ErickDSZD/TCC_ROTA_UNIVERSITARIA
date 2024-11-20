<?php
include "conexao.php";
session_start(); // Iniciar a sessão

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado'] : null;
$nome_usuario = isset($_SESSION['nome']) ? $_SESSION['nome'] : null;

$idUsuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null; // Obtém o ID do usuário

$favoritos = "";

// Verificar se o usuário está logado
if ($idUsuario) {
    // Consultar faculdades favoritas do usuário
    $sql = "SELECT f.nome AS faculdade_nome, cr.nome AS curso_nome, c.data_adicao, c.idfaculdade, c.idcurso
            FROM carrinho c
            JOIN faculdade f ON c.idfaculdade = f.idfaculdade
            JOIN curso cr ON c.idcurso = cr.idcurso
            WHERE c.idusuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $faculdadeNome = htmlspecialchars($row['faculdade_nome']);
            $cursoNome = htmlspecialchars($row['curso_nome']);

            // Formatar a data de adição
            $dataAdicao = new DateTime($row['data_adicao']);
            $dataAdicao = $dataAdicao->format('d/m/Y H:i'); // Formato Dia/Mês/Ano Hora:Minuto

            $idFaculdade = htmlspecialchars($row['idfaculdade']);
            $idCurso = htmlspecialchars($row['idcurso']);

            $favoritos .= '<div class="favorito-item">';
            $favoritos .= '<h3>' . $faculdadeNome . '</h3>';
            $favoritos .= '<p>Curso: ' . $cursoNome . '</p>';
            $favoritos .= '<p>Data de Adição: ' . $dataAdicao . '</p>';
            $favoritos .= '<button onclick="removeFavorite(' . $idFaculdade . ', ' . $idCurso . ')">Remover</button>';
            $favoritos .= '</div>';
        }
    } else {
        $favoritos = "<p>Você ainda não tem faculdades favoritas.</p>";
    }

    $stmt->close();
} else {
    $favoritos = "<p>Você precisa estar logado para ver seus favoritos.</p>";
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rota Universitária</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <header class="header">
        <aside class="aside_fixed">
            <img class="logo" src="imagens/logo_creme.png" alt="Logo do site">
            <form action="resultado.php" method="GET">
                <input type="text" name="pesquisar" id="pesquisar" placeholder="Pesquise por uma faculdade">
                <button class="button_pesquisar" type="submit">Pesquisar</button>
            </form>

            <?php if ($usuario_logado): ?>
                <a class="a_user" href="perfil.php"><img class="user" src="imagens/user.png" alt="Ver perfil"></a>
                <p>Bem-vindo(a), <?php echo htmlspecialchars($nome_usuario); ?>!</p>
            <?php else: ?>
                <a class="a_user" href="login.php"><img class="user" src="imagens/user.png" alt="Fazer login"></a>
                <p>Você não está logado.</p>
            <?php endif; ?>

            <img class="carrinho" src="imagens/carrinho.png" alt="carrinho de favorito">
            <a class="a_mapa" href="map.php"><img src="imagens/map.png" alt="Mapa para localização das faculdades" class="mapa"></a>
        </aside>

        <div id="sidebar" class="sidebar">
            <div class="sidebar-content">
                <h2>Seus Favoritos</h2>
                <p>Aqui você verá as faculdades que marcou como favorita.</p>
                <?php echo $favoritos; ?>
            </div>
            <button id="closeSidebar" class="close-sidebar">Fechar</button>
        </div>



        <aside class="aside_href">
            <p><a href="./progama_bolsa/cursos.php">Cursos</a></p>
            <p onmouseover="infPrograma()"><a onmouseover="infPrograma()" id="programabolsa" href="">Programa de bolsas</a></p>
            <p onmouseover="infFaculdade()"><a onmouseover="infFaculdade()" id="faculdade" href="">Faculdades</a></p>
            <p><a class="logo" href="index.php">Sobre</a></p>
        </aside>

        <!-- Aba de texto sobre programa de bolsas -->

        <div onmouseleave="disabledProg()" id="prog_none" class="prog_none">
            <div>
                <h1><a href="./progama_bolsa/prouni.php">PROUNI</a></h1>
                <h1><a href="./progama_bolsa/sisu.php">SISU</a></h1>
                <h1><a href="./progama_bolsa/fies.php">FIES</a></h1>
                <h1><a href="./progama_bolsa/enem.php">ENEM</a></h1>
                <h1><a href="./progama_bolsa/fuvest.php">FUVEST</a></h1>
                <h1><a href="./progama_bolsa/vunesp.php">VUNESP</a></h1>
                <h1><a href="./progama_bolsa/unicamp.php">UNICAMP</a></h1>
                <h1><a href="./progama_bolsa/ufmt.php">UFMT</a></h1>
                <h1><a href="./progama_bolsa/ufscar.php">UFSCAR</a></h1>
            </div>
        </div>

        <!-- Aba de texto sobre faculdades -->

        <div onmouseleave="disabledFacul()" id="facul_none" class="facul_none">
            <div>
                <h1><a href="./faculdades/publica.php">Faculdades Públicas</a></h1>
                <h1><a href="./faculdades/privada.php">Faculdades Privadas</a></h1>
            </div>
        </div>

        <main class="corpo_main">
            <h2>Bem-vindo ao ROTA UNIVERSITÁRIA!</h2>

            <p>Estamos aqui para ajudar você a encontrar a universidade ideal e entender todos os aspectos do ingresso no ensino superior. Nosso site oferece uma visão abrangente das faculdades, incluindo localização e cursos oferecidos, facilitando sua decisão.</p>

            <h2>Por que usar nosso site?</h2>
            <p>Escolher a universidade certa pode ser desafiador, especialmente quando se trata de entender os programas de bolsa e os requisitos de admissão. Nosso objetivo é simplificar esse processo, oferecendo informações claras e acessíveis sobre instituições educacionais e suas ofertas.</p>

            <h2>O que oferecemos:</h2>
            <ul>
                <li><strong>Informações Detalhadas:</strong> Descubra tudo o que você precisa saber sobre cada faculdade.</li>
                <li><strong>Facilidade de Navegação:</strong> Encontre rapidamente o que procura sem perder tempo.</li>
                <li><strong>Aba de Favoritos:</strong> Marque suas instituições favoritas para acesso rápido e fácil.</li>
            </ul>

            <h2>Como podemos ajudar:</h2>
            <p>Nossa plataforma é projetada para fornecer informações diretas e úteis. Se você tiver dúvidas ou precisar de mais assistência, entre em contato conosco por e-mail. Estamos aqui para ajudar você a dar o próximo passo em sua jornada acadêmica.</p>
        </main>

    </header>

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

    <script src="./script.js"></script>
</body>

</html>

