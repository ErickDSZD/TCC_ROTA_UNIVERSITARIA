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
    <title>PROUNI</title>
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
        <h2>Como funciona o PROUNI?</h2>
            <p>O Programa Universidade para Todos (Prouni) é uma iniciativa do governo brasileiro que concede bolsas de estudo integrais e
                 parciais em instituições privadas de ensino superior. As bolsas são destinadas a estudantes brasileiros de baixa renda, com 
                 base nas notas obtidas no Exame Nacional do Ensino Médio (ENEM) e no cumprimento de requisitos socioeconômicos. O Prouni
                  oferece oportunidades para que os alunos ingressem em cursos de graduação de instituições privadas de todo o país, por meio 
                  de uma seleção que considera o desempenho no ENEM e a renda familiar per capita.</p>
        <h2>Objetivo</h2>
            <p>O principal objetivo do Prouni é democratizar o acesso ao ensino superior no Brasil, oferecendo bolsas de estudo para 
                estudantes de baixa renda que desejam cursar uma graduação em instituições privadas. O programa visa ampliar as oportunidades 
                educacionais e contribuir para a inclusão social, possibilitando que mais brasileiros possam realizar seus estudos em cursos de
                 graduação, mesmo sem condições financeiras para arcar com os custos das mensalidades.</p>
        <h2>Informações do PROUNI</h2>
            <p>Para participar do Prouni e concorrer a uma bolsa de estudo, é necessário atender aos seguintes requisitos:</p>
        <ul>
            <li>Ter participado do Exame Nacional do Ensino Médio (ENEM) na edição mais recente e obtido, no mínimo, 450 pontos na média das 
                notas e nota maior que zero na redação.</li>
            <li>Não ter diploma de curso superior.</li>
            <li>Ter cursado o ensino médio completo em escola da rede pública ou em instituição privada na condição de bolsista integral, ou 
                ainda ter cursado o ensino médio parcialmente em escola pública e parcialmente em instituição privada na condição de bolsista 
                integral.</li>
            <li>Comprovar renda familiar bruta mensal de até um salário mínimo e meio per capita para bolsas integrais e de até três salários 
                mínimos per capita para bolsas parciais de 50%.</li>
        </ul>
            <p>As inscrições são realizadas online, por meio do sistema do Prouni, dentro do período estipulado pelo Ministério da Educação (MEC).
                 Os candidatos devem escolher até duas opções de curso, especificando a ordem de preferência. Após o período de inscrição, o 
                 sistema realiza a seleção com base nas notas do ENEM e nos critérios socioeconômicos. Os estudantes selecionados devem 
                 comprovar as informações fornecidas e, caso aprovados, realizar a matrícula na instituição de ensino escolhida.</p>
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

