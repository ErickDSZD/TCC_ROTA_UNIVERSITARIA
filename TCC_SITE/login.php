<?php
include "conexao.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    // Prevenir SQL Injection
    $email = $conn->real_escape_string($email);

    // Consultar o banco de dados para verificar o usuário
    $sql = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Verificar se a senha está correta
        $usuario = $result->fetch_assoc();
        if (password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido, definir sessão
            $_SESSION['usuario_logado'] = $usuario['email'];
            $_SESSION['idusuario'] = $usuario['idusuario']; // Definir ID do usuario na sessão
            $_SESSION['nome'] = $usuario['nome'];
            header("Location: index.php"); // Redirecionar para a página inicial
            exit();
        } else {
            $mensagem_erro = "Senha incorreta.";
        }
    } else {
        $mensagem_erro = "Usuário não encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rota Universitária</title>
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-topo">
                <h1>Login</h1>
                <a class="a_home" href="index.php"><img class="home" src="imagens/home_preto.png" alt="voltar para a página inicial"></a>
            </div>
            <form action="login.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
                
                <p>Não possui cadastro ? <a href="cadastro.php">Cadastre aqui!</a></p>

                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <script>
        // Exibir mensagem de erro com SweetAlert
        <?php if (!empty($mensagem_erro)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Erro de Login',
                text: '<?php echo $mensagem_erro; ?>',
                confirmButtonColor: '#ff914d',
                focusConfirm: false // Desativa o foco automático no botão
            });
        <?php endif; ?>
    </script>

</body>
</html>

