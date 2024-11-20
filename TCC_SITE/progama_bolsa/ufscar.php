<?php
include "C:/xampp/htdocs/TCC_SITE/conexao.php";
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
            $dataAdicao = htmlspecialchars($row['data_adicao']);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFSCAR</title>
    <link rel="stylesheet" href="style.css">
    <script src="../script.js" defer></script>
</head>
<body>
    <header class="header">
    <aside class="aside_fixed">
            <img class="logo" src="logo.png" alt="Logo do site">
            <form action="http://localhost/TCC_SITE/resultado.php" method="GET">
                <input type="text" name="pesquisar" id="pesquisar" placeholder="Pesquise por uma faculdade">
                <button class="button_pesquisar" type="submit">Pesquisar</button>
            </form>
            <img class="carrinho" src="../imagens/carrinho.png" alt="carrinho de favorito">
            <a class="a_mapa" href="../map.php"><img src="../imagens/map.png" alt="Mapa para localização das faculdades" class="mapa"></a>
        </aside>

        <div id="sidebar" class="sidebar">
            <div class="sidebar-content">
                <h2>Seus Favoritos</h2>
                <p>Aqui você verá as faculdades favoritas que você marcou.</p>
                <?php echo $favoritos; ?>
            </div>
            <button id="closeSidebar" class="close-sidebar">Fechar</button>
        </div>

    <aside class="aside_href">
            <p><a href="./cursos.php">Cursos</a></p>
            <p onmouseover="infPrograma()"><a onmouseover="infPrograma()" id="programabolsa" href="">Programa de bolsas</a></p>
            <p onmouseover="infFaculdade()"><a onmouseover="infFaculdade()" id="faculdade" href="">Faculdades</a></p>
            <p><a class="logo" href="../index.php">Sobre</a></p>
        </aside>

        <!-- Aba de texto sobre programa de bolsas -->

        <div onmouseleave="disabledProg()" id="prog_none" class="prog_none">
            <div>
                <h1><a href="./prouni.php">PROUNI</a></h1>
                <h1><a href="./sisu.php">SISU</a></h1>
                <h1><a href="./fies.php">FIES</a></h1>
                <h1><a href="./enem.php">ENEM</a></h1>
                <h1><a href="./fuvest.php">FUVEST</a></h1>
                <h1><a href="./vunesp.php">VUNESP</a></h1>
                <h1><a href="./unicamp.php">UNICAMP</a></h1>
                <h1><a href="./ufmt.php">UFMT</a></h1>
                <h1><a href="./ufscar.php">UFSCAR</a></h1>
            </div>
        </div>

        <!-- Aba de texto sobre faculdades -->

        <div onmouseleave="disabledFacul()" id="facul_none" class="facul_none">
            <div>
                <h1><a href="../faculdades/publica.php">Faculdades Públicas</a></h1>
                <h1><a href="../faculdades/privada.php">Faculdades Privadas</a></h1>
            </div>
        </div>

    <main class="corpo_main">
    <h2>Como funcina a UFSCAR ?</h2>

    <p>A Universidade Federal de São Carlos (UFSCAR) é uma instituição pública de ensino superior localizada no estado de São Paulo, reconhecida pela qualidade acadêmica e 
        pela oferta de cursos nas áreas de ciências humanas, exatas, tecnológicas e da saúde. O processo seletivo da UFSCAR é realizado através de diferentes modalidades, 
        incluindo o Sistema de Seleção Unificada (SiSU) e o vestibular específico organizado pela Comissão Permanente para os Vestibulares da UFSCAR (COVEST).</p>

    <h2>Objetivo</h2>

    <p>O principal objetivo da UFSCAR é promover o ensino, a pesquisa e a extensão universitária de qualidade, contribuindo para o desenvolvimento regional e nacional. 
        A universidade busca selecionar estudantes com base no mérito acadêmico e no potencial de contribuição para a sociedade, valorizando a diversidade e a inclusão social.</p>

    <h2>Informações da UFSCAR</h2>

    <p>Para participar do processo seletivo da UFSCAR e concorrer a uma vaga em seus cursos de graduação, os candidatos devem atender aos seguintes requisitos e seguir as
         etapas estabelecidas:</p>

    <ul>
        <li>Para participar do SiSU:
            <ul>
                <li>Ter participado do Exame Nacional do Ensino Médio (ENEM) na edição mais recente e obtido nota acima de zero na redação.</li>
                <li>Realizar a inscrição dentro do período estipulado pelo Ministério da Educação (MEC), por meio do sistema online do SiSU.</li>
                <li>Escolher até duas opções de curso, especificando a ordem de preferência e a modalidade de concorrência (ampla concorrência ou cotas).</li>
                <li>Obter um desempenho satisfatório nas provas do ENEM e alcançar uma pontuação que atenda às notas de corte estabelecidas para cada curso na UFSCAR.</li>
            </ul>
        </li>
        <li>Para participar do vestibular da COVEST:
            <ul>
                <li>Realizar a inscrição dentro do período estipulado pela UFSCAR, geralmente entre os meses de setembro e outubro.</li>
                <li>Participar das provas aplicadas pela COVEST, que incluem questões de múltipla escolha e redação, abrangendo disciplinas do ensino médio.</li>
                <li>Obter um desempenho satisfatório nas provas e alcançar uma pontuação que atenda às notas de corte estabelecidas para cada curso.</li>
            </ul>
        </li>
    </ul>

    <p>Após a conclusão do processo seletivo, os candidatos são selecionados de acordo com suas notas no ENEM ou desempenho no vestibular da COVEST, conforme a modalidade 
        escolhida. Os aprovados podem efetuar a matrícula na UFSCAR para o curso para o qual foram selecionados, seguindo as orientações e prazos estabelecidos pela universidade.</p>

    </main>

    <footer class="footer">
        <div class="footer_content">
            <h2>Contato</h2>
            <div class="footer_section">
                <div class="p">
                    <p>Email: contato@rotouniversitaria.com</p>
                </div>
                <div class="p">
                    <p>Telefone: (11) 1234-5678</p>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <p>&copy; 2024 Rota Universitária. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
