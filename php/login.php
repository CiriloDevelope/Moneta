
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'rel='stylesheet'>
        <link rel="stylesheet" href="../css/login.css">
        <title>Página de Login </title>
    </head>
<body>


    <main class="container">
        <form action="testeLogin.php" method="POST">
            <h1>Login</h1>
            <div class="input-box">
                <input placeholder="E-mail" type="email" name="email">
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="senha">
                <i class="bx bxs-lock-alt"> </i>
            </div>

            <div class="remember-forgot">
                 <label>
                <input type="checkbox">
                Lembrar senha
                 </label>
                 <a href="esqueceusenha.php">Esqueci senha</a>
            </div>

            <button type="submit" class="login" name="submit"><a>Login</a></button>

            <div class="register-link">
                <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            </div>
        </form>
    </main>
</body>
</html>