<?php
include "conexao.php";
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header("Location: login.php");
    exit();
}

$usuario_logado = $_SESSION['usuario_logado'];

// Busca as informações do usuário
$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario_logado);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$estado_id = $usuario['idestado'];
$cidade_id = $usuario['idcidade'];

// Busca o nome do estado
$sql_estado = "SELECT nome FROM estado WHERE idestado = ?";
$stmt_estado = $conn->prepare($sql_estado);
$stmt_estado->bind_param("i", $estado_id);
$stmt_estado->execute();
$result_estado = $stmt_estado->get_result();
$estado = $result_estado->fetch_assoc()['nome'];

// Busca o nome da cidade
$sql_cidade = "SELECT nome FROM cidade WHERE idcidade = ?";
$stmt_cidade = $conn->prepare($sql_cidade);
$stmt_cidade->bind_param("i", $cidade_id);
$stmt_cidade->execute();
$result_cidade = $stmt_cidade->get_result();
$cidade = $result_cidade->fetch_assoc()['nome'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Atualiza as informações do usuário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $senha = $_POST['senha'];
    $estado = $_POST['estado'];
    $cidade = $_POST['cidade'];

    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    $sql_update = "UPDATE usuario SET nome = ?, cep = ?, logradouro = ?, numero = ?, bairro = ?, senha = ?, idestado = (SELECT idestado FROM estado WHERE nome = ?), idcidade = (SELECT idcidade FROM cidade WHERE nome = ?) WHERE email = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssssss", $nome, $cep, $logradouro, $numero, $bairro, $senha_hash, $estado, $cidade, $email);
    $stmt_update->execute();

    echo "<script>alert('Perfil atualizado com sucesso!');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Rota Universitária</title>
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <header class="header">
        <h1>Perfil
            <a href="index.php" class="button button-home">Início</a>
            <a href="logout.php" class="button button-logout">Logout</a>
            <button type="button" class="button button-delete" onclick="confirmDelete()">Deletar Conta</button>
        </h1>
    </header>

    <main>
        <form action="perfil.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($usuario['cep']); ?>" required>

            <label for="logradouro">Logradouro:</label>
            <input type="text" id="logradouro" name="logradouro" value="<?php echo htmlspecialchars($usuario['logradouro']); ?>" required>

            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($usuario['numero']); ?>" required>

            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($usuario['bairro']); ?>" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <!-- Adicionar opções dinâmicas de estados aqui -->
                <option value="<?php echo htmlspecialchars($estado); ?>" selected><?php echo htmlspecialchars($estado); ?></option>
            </select>

            <label for="cidade">Cidade:</label>
            <select name="cidade" id="cidade" required>
                <!-- Adicionar opções dinâmicas de cidades aqui -->
                <option value="<?php echo htmlspecialchars($cidade); ?>" selected><?php echo htmlspecialchars($cidade); ?></option>
            </select>

            <button type="submit" class="button">Salvar Alterações</button>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Você tem certeza?',
                text: "Esta ação não pode ser desfeita.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Deletar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "deletar_conta.php";
                }
            });
        }
    </script>

</body>

</html>