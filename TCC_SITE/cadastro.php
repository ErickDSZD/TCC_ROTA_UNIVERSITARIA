<?php
include "conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Rota Universitária</title>
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <div class="form-container">
        <div class="login-topo">
            <h1>Cadastrar</h1>
            <a class="a_home" href="index.php"><img class="home" src="imagens/home_preto.png" alt="voltar para a página inicial"></a>
        </div>

        <form action="inserir_banco.php" method="GET">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required autocomplete="off">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="off">

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" required maxlength="8" autocomplete="off">

            <label for="logradouro">Logradouro</label>
            <input type="text" id="logradouro" name="logradouro" required>

            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" required autocomplete="off">

            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" required>

            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="1">SP</option>
                <option value="2">AC</option>
                <option value="3">AL</option>
                <option value="4">AP</option>
                <option value="5">AM</option>
                <option value="6">BA</option>
                <option value="7">CE</option>
                <option value="8">DF</option>
                <option value="9">ES</option>
                <option value="10">GO</option>
                <option value="11">MA</option>
                <option value="12">MT</option>
                <option value="13">MS</option>
                <option value="14">MG</option>
                <option value="15">PA</option>
                <option value="16">PB</option>
                <option value="17">PR</option>
                <option value="18">PE</option>
                <option value="19">PI</option>
                <option value="20">RJ</option>
                <option value="21">RN</option>
                <option value="22">RS</option>
                <option value="23">RO</option>
                <option value="24">RR</option>
                <option value="25">SC</option>
                <option value="26">SE</option>
                <option value="27">TO</option>
            </select>

            <label for="cidade">Cidade</label>
            <input type="text" id="cidade" name="cidade" required>

            <p>Já possui cadastro ? <a href="login.php">Acesse sua conta!</a></p>

            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.10/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('cep').addEventListener('blur', function() {
            var cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'CEP não encontrado!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4ebc6e',
                                showCloseButton: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                timer: 5000, // tempo de exibição em milissegundos (5 segundos)
                                timerProgressBar: true // exibe uma barra de progresso do tempo
                            });
                            return;
                        }
                        document.getElementById('logradouro').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        // document.getElementById('estado').value = '1';
                    })
                    .catch(error => console.error('Erro:', error));
            }
        });

        // Verifica se há parâmetros na URL
        const urlParams = new URLSearchParams(window.location.search);

        // Verifica se o parâmetro "erro" é igual a "usuario_ja_existe"
        if (urlParams.get('erro') === 'usuario_ja_existe') {
            Swal.fire({
                title: 'Erro!',
                text: 'Usuário já existe!',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4ebc6e',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                showCloseButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: 5000, // tempo de exibição em milissegundos (5 segundos)
                timerProgressBar: true // exibe uma barra de progresso do tempo
            });
        }
    </script>
</body>

</html>

