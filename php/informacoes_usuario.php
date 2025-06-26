<?php
 
 session_start();
 include_once('config.php');
 
 if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
     echo "<script>alert('Faça o login novamente!.'); window.location.href='sair.php';</script>";
     exit();
 }
 
 $email = $_SESSION['email'];
 $senha = $_SESSION['senha'];

 
 
 $sqlUsuario = "SELECT id_usuario FROM usuarios WHERE email = ? AND senha = ?";
 $stmtUsuario = $conexao->prepare($sqlUsuario);
 $stmtUsuario->bind_param("ss", $email, $senha);
 $stmtUsuario->execute();
 $resultado = $stmtUsuario->get_result();
 
 if ($resultado->num_rows > 0) {
     $row = $resultado->fetch_assoc();
     $id_usuario = $row['id_usuario'];
 
     

    
     $sqlDetalhes = 
"SELECT u.id_usuario, u.nome, u.email, u.telefone,u.data_nasc, tp.id_tipo_perfil, tp.classificacao, tp.descricao_perfil,tp.caminho_imagem, m.moeda
 FROM  usuarios u
 LEFT JOIN  usuario_perfil up 
 ON u.id_usuario = up.id_usuario
 LEFT JOIN tipo_perfil tp 
 ON tp.id_tipo_perfil = up.id_tipo_perfil
 LEFT JOIN moedas m 
 ON m.id_moeda = up.id_moeda
 WHERE  u.id_usuario = ?";
 
 $stmtDetalhes = $conexao->prepare($sqlDetalhes);
 $stmtDetalhes->bind_param("i", $id_usuario);
 $stmtDetalhes->execute();
 $resultadoDetalhes = $stmtDetalhes->get_result();
 
 // Verifica se achou dados
 if ($resultadoDetalhes->num_rows > 0) {
     while ($row = $resultadoDetalhes->fetch_assoc()) {
         // Aqui você pega os valores e pode exibir ou guardar em variáveis
         $nome = $row['nome'];
         $email = $row['email'];
         $classificacao = $row['classificacao'];
         $descricao_perfil = $row['descricao_perfil'];
         $moeda = $row['moeda'];
         $telefone = $row['telefone'];
         $data_nasc = $row['data_nasc'];
         $caminho_imagem = $row['caminho_imagem'];
       
 
         //TESTA TODAS AS INFORMAÇOES TRAZIDAS DO BANCO
        // echo "<p>Nome: $nome</p>";
        // echo "<p>Email: $email</p>";
        // echo "<p>Classificação: $classificacao</p>";
        // echo "<p>Descrição do Perfil: $descricao_perfil</p>";
        // echo "<p>Moeda: $moeda</p>";
        //echo "<p>telefone: $telefone</p>";
       // echo "<p>data: $data_nasc</p>";
       //print_r($caminho_imagem);
     }
     
 } else {
     echo "<p>Nenhum dado encontrado para esse usuário.</p>";
 }
 } else {
     echo "<script>alert('Usuário não encontrado.'); window.location.href='sair.php';</script>";
     exit();
 }

 if (isset($_POST['mudar_informacao'])) {
  $acao = "mudar_informacao";
 }
 if (isset($_POST['mudar'])) {


  $acao = "mudar"; 

  $novo_nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
  $novo_email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
  $novo_telefone =htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8');
  $novo_data_nasc =htmlspecialchars($_POST['data_nasc'], ENT_QUOTES, 'UTF-8');

  // Atualiza no banco de dados
  $sqlAlteracao = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, data_nasc = ? WHERE id_usuario = ?";
  $stmtAlteracao = $conexao->prepare($sqlAlteracao);
  $stmtAlteracao->bind_param("ssssi", $novo_nome, $novo_email,$novo_telefone,$novo_data_nasc, $id_usuario);

  if ($stmtAlteracao->execute()) {
      echo "<script>alert('Dados atualizados com sucesso.'); window.location.href='informacoes_usuario.php';</script>";
  } else {
      echo "<script>alert('Erro ao atualizar os dados.');</script>";
  }
}
 

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil do Usuário</title>
  <link rel="stylesheet" href="../css/informacoes_usuario.css">
</head>
<body>
<body>
  <?php if(isset($acao) && $acao == "mudar"):?>
 <div class="sidebar" id="sidebar">
    <h1>Menu</h1>
    <ul class="menus">
      <li><a href="informacoes_usuario.php"><img src="../img/usuario.png" alt="Usuario"><span>Usuario</span></a></li>
      <li><a href="PaginaPrincipal.php"><img src="../img/home.png" alt="Conversor"><span>Home</span></a></li>
      <li><a href="notificacao.php"><img src="../img/notificacao.png" alt="Notificação"><span>Notificação</span></a></li>
      <li><a href="relatorios.php"><img src="../img/relatorio.png" alt="Relatorio"><span>Relatorio</span></a></li>
      <li><a href="grafico.php"><img src="../img/grafico.png" alt="Gráfico"><span>Gráficos</span></a></li>
      <li><a href="sair.php"><img src="../img/sair.png" alt="Sair"><span>Sair</span></a></li>
    </ul>
  </div>
  <?php elseif(isset($acao) && $acao == "mudar_informacao"): ?>
      <?php 
        $sqlUsuario = "SELECT id_usuario, nome, telefone, email, data_nasc FROM usuarios WHERE email = ? AND senha = ?";
        $stmtUsuario = $conexao->prepare($sqlUsuario);
        $stmtUsuario->bind_param("ss", $email, $senha);
        $stmtUsuario->execute();
        $resultado = $stmtUsuario->get_result();
        ?>
 <div class="sidebar" id="sidebar">
    <h1>Menu</h1>
    <ul class="menus">
      <li><a href="informacoes_usuario.php"><img src="../img/usuario.png" alt="Usuario"><span>Usuario</span></a></li>
      <li><a href="PaginaPrincipal.php"><img src="../img/home.png" alt="Conversor"><span>Home</span></a></li>
      <li><a href="notificacao.php"><img src="../img/notificacao.png" alt="Notificação"><span>Notificação</span></a></li>
      <li><a href="relatorios.php"><img src="../img/relatorio.png" alt="Relatorio"><span>Relatorio</span></a></li>
      <li><a href="grafico.php"><img src="../img/grafico.png" alt="Gráfico"><span>Gráficos</span></a></li>
      <li><a href="sair.php"><img src="../img/sair.png" alt="Sair"><span>Sair</span></a></li>
    </ul>
  </div>
    <div class="caixa_tabela">
  <fieldset class="titulo">Seus Dados</fieldset>
  <form action="" method="POST">
    <table class="dados_usuario">
      <tr>
        <td>Nome:</td>
        <td>
          <input type="text" name="nome" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>" class="input-campo">
        </td>
       
      </tr>
      <tr>
        <td>E-mail:</td>
        <td>
          <input type="text" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" class="input-campo">
        </td>
      </tr>
      <tr>
        <td>Telefone:</td>
        <td>
          <input type="text" name="telefone" value="<?= htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') ?>" class="input-campo">
        </td>
      </tr>
      <tr>
        <td>Nacimento:</td>
        <td>
          <input type="date" name="data_nasc" value="<?= htmlspecialchars($data_nasc, ENT_QUOTES, 'UTF-8') ?>" class="input-campo">
        </td>
      </tr>
    </table>
      <button class="content-lapis" type="submit" name="mudar" value="1" class="icone-botao">
            <img src="../img/editar.png" alt="Editar" class="lapis">
        </button>
  </form>
</div>
  <?php else: ?>

 <div class="sidebar" id="sidebar">
    <h1>Menu</h1>
    <ul class="menus">
      <li><a href="informacoes_usuario.php"><img src="../img/usuario.png" alt="Usuario"><span>Usuario</span></a></li>
      <li><a href="PaginaPrincipal.php"><img src="../img/home.png" alt="Conversor"><span>Home</span></a></li>
      <li><a href="notificacao.php"><img src="../img/notificacao.png" alt="Notificação"><span>Notificação</span></a></li>
      <li><a href="relatorios.php"><img src="../img/relatorio.png" alt="Relatorio"><span>Relatorio</span></a></li>
      <li><a href="grafico.php"><img src="../img/grafico.png" alt="Gráfico"><span>Gráficos</span></a></li>
      <li><a href="sair.php"><img src="../img/sair.png" alt="Sair"><span>Sair</span></a></li>
    </ul>
  </div>
<?php   


?>
  <form action="" method="POST">
    <div class="perfil-container">
        <div class="perfil-caixa">
        <img src="<?= htmlspecialchars($caminho_imagem) ?>" alt="Foto do Usuário" class="perfil-imagem"> 


          <h2><?php echo $descricao_perfil ?></h2>
          <p class="email"><u>Nome:</u> <?php echo $nome; ?></p>
          <p class="email"><u>E-mail:</u> <?php echo $email; ?></p>
          <p class="email"><u>Telefone:</u> <?php echo $telefone; ?></p>
          <p class="email"><u>Data de Nacimento:</u> <?php echo $data_nasc; ?></p>
          <p class="email"><u>Moeda associada ao perfil:</u> <?php echo $moeda; ?></p>
          <div class="button">
            <input class="mudar" type="submit" name="mudar_informacao" value="Alterar Dados">
            <button class="sair"><a href="sair.php">Sair</a></button>
          </div>
        </div>
      </div>
  </form>
  <?php endif; ?>
</body>
</body>
</html>
