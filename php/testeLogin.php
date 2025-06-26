<?php
 session_start();

//print_r($_REQUEST);
if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])){

 include_once('config.php');
 include_once('funcao.php');

 $email = $_POST['email'];
 $senha = md5($_POST['senha']);
 // verificar print_r($email); 
 // verificar print_r($senha);

 $consulta = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";

 $resultado_consulta = $conexao->query($consulta);

 // verifica o que deu na consulta print_r($consulta);
 // verifica o que deu na consulta print_r($resultado_consulta);

 if(mysqli_num_rows($resultado_consulta) < 1 ){
    // não existe no banco de dados
    $erro_login = "Usuario ou Senha incorretos!";
    unset( $_SESSION['email']);
    unset( $_SESSION['senha']);
    unset($_SESSION['id_usuario']); 

 }
 else{   
    //existe no banco de dados.
    $_SESSION['email'] = $email;
    $_SESSION['senha'] = $senha;
    header('Location:perfil.php');
 }
}
else{
    //não acessa
    header('Location:login.php');
}

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="../css/cadastro.css">
    </head>
<body>

<?php if(isset($erro_login)): ?>
    <main >
    <div class= "input-box">

    <h1 style="color: white;"><?php echo $erro_login; ?></h1>
        <form method="post" action="login.php"  style="background-color: black;">
             <button type="submit" class="login">Voltar para o Login</button>
        </form>

    </div>
</main>
<?php endif;?>  