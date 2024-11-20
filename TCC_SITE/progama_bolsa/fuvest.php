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
    <title>FUVEST</title>
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
        <h2>Como funciona a FUVEST ?</h2>

        <p>A Fundação Universitária para o Vestibular (Fuvest) é responsável pelo processo seletivo de ingresso na Universidade de São Paulo (USP). 
            A prova da Fuvest é uma das mais concorridas do país e avalia o conhecimento adquirido pelos candidatos ao longo do ensino médio. 
            Além da prova, a Fuvest também utiliza a nota do Exame Nacional do Ensino Médio (ENEM) como parte do processo de seleção em algumas etapas de seu vestibular.</p>

        <h2>Objetivo</h2>

        <p>O principal objetivo da Fuvest é selecionar os estudantes mais capacitados para ingressar em cursos de graduação oferecidos pela USP. 
            O processo seletivo busca avaliar não apenas o conhecimento acadêmico, mas também habilidades e competências necessárias para o desempenho acadêmico e profissional 
            dos candidatos.</p>

        <h2>Informações da FUVEST</h2>

        <p>Para participar do vestibular da Fuvest e concorrer a uma vaga em cursos da USP, é necessário atender aos seguintes requisitos e seguir as etapas do processo seletivo:</p>

        <ul>
            <li>Realizar a inscrição dentro do período estipulado pela Fuvest, geralmente entre os meses de agosto e setembro.</li>
            <li>Participar das provas aplicadas pela Fuvest, que incluem questões de múltipla escolha e redação, abrangendo disciplinas do currículo do ensino médio.</li>
            <li>Obter um desempenho satisfatório nas provas aplicadas pela Fuvest, alcançando uma pontuação que atenda às notas de corte estabelecidas para cada curso.</li>
            <li>Utilizar a nota do ENEM em algumas etapas do processo seletivo, conforme definido pela Fuvest em cada edição do vestibular.</li>
        </ul>

        <p>Após o processo de avaliação, os candidatos são classificados de acordo com seus desempenhos nas provas da Fuvest e, em alguns casos, com base na combinação de suas notas do ENEM. Os aprovados podem efetuar a matrícula na instituição para a qual foram selecionados, seguindo as orientações e prazos determinados pela Fuvest.</p>

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

