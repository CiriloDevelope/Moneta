<?php
include_once('config.php');

if (!$conexao) {
    die("Erro de conexão: " . $conexao->connect_error);
}

$erro = []; // Inicializando o array de erros

if (isset($_POST['ok']) && isset($_POST['email'])) {
    
    $email = $conexao->real_escape_string($_POST['email']);  
    if (!filter_var($email , FILTER_VALIDATE_EMAIL)) {
        $erro[] = "E-mail inválido";
    }

    if (count($erro) == 0) {
        $consulta = "SELECT senha FROM usuarios WHERE email = '$email'";
        $resultado = $conexao->query($consulta) or die($conexao->error);
        $dados = $resultado->fetch_assoc();
        $total = $resultado->num_rows;

        if ($total == 0) {
            $erro[] = "<span class='erro'>O E-mail informado não existe no banco de dados</span>";

        }

        if (count($erro) == 0 && $total > 0) {
            $novasenha = substr(md5(time()), 0, 6);
            $NScriptografada = md5(md5($novasenha));

            if (mail($email, "Sua nova senha", "Sua nova senha: " . $novasenha)) {
                $consulta = "UPDATE usuarios SET senha = '$NScriptografada' WHERE email = '$email'";
                $resultado = $conexao->query($consulta) or die($conexao->error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci Minha Senha</title>
    <link rel="stylesheet" href="../css/esqueceusenha.css">
</head>
<body>


<section class="fundo"></section>


<div class="container">
    <?php
        if (count($erro) > 0) {
            foreach ($erro as $msg) {
                print "<p>$msg</p>";
            }
        }
    ?>
    <h1>Esqueci Minha Senha</h1>
    <p>Informe seu email para recuperar sua senha:</p>
    <form action="" method="POST">
        <div class="input-box">
            <input type="email" placeholder="Digite seu e-mail" name="email" required>
        </div>
        <button type="submit" class="send-link" name="ok" value="ok">Enviar link de recuperação</button>
        <div class="register-link">
            <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
    </form>
</div>
</body>
</html>
