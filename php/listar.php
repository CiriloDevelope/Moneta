<?php
include_once('config.php');

$consulta = "SELECT * FROM usuarios ORDER BY id_usuario ";

$resultado = $conexao->query($consulta);

print_r($resultado);

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
<body>
<div>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">Telefone</th>
            <th scope="col">Email</th>
            <th scope="col">Data de Nacimento</th>
            <th scope="col">Senha</th>
            <th scope="col">...</th>

            </tr>
        </thead>
        <tbody>
            <?php
                while ($user_data = mysqli_fetch_assoc($resultado)){
                    print "<td>";
                    print "<tr>" . $user_data['id_usuario'] . "</td>";
                }            
            ?>
        </tbody>
        </table>
    </div>
</body>
</html>