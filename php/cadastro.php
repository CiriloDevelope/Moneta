<?php
include_once('config.php');
session_start();

$erro_senha = "";
$fezlogin = "";

if (isset($_POST['submit'])) {

    $nome = $_POST['nome'];
    $data_nasc = $_POST['data_nasc'];
    $email =$_POST['email'];
    $telefone = $_POST['telefone'];
    $senha = md5($_POST['senha']);
    $confirma = md5($_POST['confirmasenha']);

    //print_r($email = md5($_POST['email']));
    //print_r($telefone = md5($_POST['telefone']));
    //print_r($senha = md5($_POST['senha']));
    //print_r($confirma = md5($_POST['confirmasenha']));


    if ($senha !== $confirma) {
        $erro_senha = "As senhas não conferem.";
    } else {
        $resultado = mysqli_query($conexao, "INSERT INTO usuarios(nome, telefone, email, data_nasc, senha) VALUES('$nome', '$telefone', '$email', '$data_nasc', '$senha')");

        if ($resultado) {
            // Mensagem de sucesso
            $fezlogin = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/cadastro.css">
    <title>Página de Cadastro</title>
</head>

<body>
<?php if ($fezlogin == "success"): ?>
    <main class="cadastrado">
        <div class="input-box">
            <h1 class="textocadastrado">CADASTRADO COM SUCESSO!</h1>
            <p>Você será redirecionado para a página de login em 3 segundos.</p>
        </div>

        <script>
            setTimeout(function() {
               window.location.href = "login.php"; 
            }, 4000); 
        </script>
    </main>
<?php elseif ($erro_senha != ""): ?>
    <div style="text-align: center; color: red;">
        <h2><?php echo $erro_senha; ?></h2>
        <form method="post" action="cadastro.php">
            <button type="submit">Voltar</button>
        </form>
    </div>
<?php else: ?>
    <div class="container">
        <form action="cadastro.php" method="POST">
            <h1>Cadastre-se</h1>
            <div class="input-box">
                <input placeholder="Nome Completo" type="text" name="nome" required>
            </div>

            <div class="input-box">
                <input placeholder="Data de Nascimento" type="date" name="data_nasc" required>
            </div>

            <div class="input-box">
                <input placeholder="Telefone" type="number" name="telefone" required>
            </div>

            <div class="input-box">
                <input placeholder="E-mail" type="email" name="email" required>
            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="senha" required>
            </div>

            <div class="input-box">
                <input placeholder="Confirmar senha" type="password" name="confirmasenha" required>
            </div>

            <button type="submit" class="login" name="submit">Cadastrar</button>
        </form>

        <form action="index1.php">
            <button type="submit" class="login" name="voltar">Pagina Inicial</button>
        </form>
    </div>
<?php endif; ?> 

</body>

</html>
