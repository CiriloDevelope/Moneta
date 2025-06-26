<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    echo "<script>alert('Erro de sessão. Redirecionando para o login.'); window.location.href='sair.php';</script>";
    exit();
}

$email = $_SESSION['email'];
$senha = $_SESSION['senha'];

// Consulta para buscar o ID do usuário
$sqlUsuario = "SELECT id_usuario FROM usuarios WHERE email = ? AND senha = ?";
$stmtUsuario = $conexao->prepare($sqlUsuario);
$stmtUsuario->bind_param("ss", $email, $senha);
$stmtUsuario->execute();
$resultado = $stmtUsuario->get_result();


if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $id_usuario = $row['id_usuario'];

    // SE A REQUISIÇÃO FOR POST → processa cadastro
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['perfilEscolhido'])) {
        $perfilEscolhido = (int) $_POST['perfilEscolhido'];
  

            $sqlUsuario = "SELECT id_usuario FROM usuarios WHERE email = ? AND senha = ?";
            $stmtUsuario = $conexao->prepare($sqlUsuario);
            $stmtUsuario->bind_param("ss", $email, $senha);
            $stmtUsuario->execute();
            $resultado = $stmtUsuario->get_result();

        $id_moeda = $perfilEscolhido;

        // Verifica novamente só por segurança
        $verifica = "SELECT * FROM usuario_perfil WHERE id_usuario = ?";
        $stmtVerifica = $conexao->prepare($verifica);
        $stmtVerifica->bind_param("i", $id_usuario);
        $stmtVerifica->execute();
        $resVerifica = $stmtVerifica->get_result();

        if ($resVerifica->num_rows > 0) {
            header("Location: PaginaPrincipal.php");
            exit();
        }

        // Se não tiver, cadastra
        $sql = "INSERT INTO usuario_perfil (id_usuario, id_tipo_perfil, id_moeda) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iii", $id_usuario, $perfilEscolhido, $id_moeda);

        if ($stmt->execute()) {
            echo "<script>alert('Perfil cadastrado com sucesso!'); window.location.href='PaginaPrincipal.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao salvar perfil no banco: " . $stmt->error . "');</script>";
        }
    }
    // SE FOR GET → só verifica se já tem perfil
    else {
        $verifica = "SELECT * FROM usuario_perfil WHERE id_usuario = ?";
        $stmtVerifica = $conexao->prepare($verifica);
        $stmtVerifica->bind_param("i", $id_usuario);
        $stmtVerifica->execute();
        $resVerifica = $stmtVerifica->get_result();

        if ($resVerifica->num_rows > 0) {
            header("Location: PaginaPrincipal.php");
            exit();
        }
    }
} else {
    echo "<script>alert('Usuário não encontrado.'); window.location.href='sair.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../css/perfil.css">
    <title>Escolha de Perfil</title>
    <script src="../js/perfil.js" defer></script>
  </head>

  <body>
    <nav class="navbar">
      <a href="sobre.php">Sobre</a>
      <a href="login.php">Login</a>
      <br>
      <h1>Selecione seu perfil</h1>
      <br>
      <a href="index1.php" class="logo">
        <img src="../img/logo.png" alt="Logo" style="height: 40px;" />
      </a>
      <br>
    </nav>

    <form method="POST" id="formPerfil">
      <input type="hidden" name="perfilEscolhido" id="perfilEscolhido" />

      <div class="planos">
        <div class="perfil" onclick="expandir(this, 1)">
          <div class="texto">
            <h2>Perfil Conservador</h2>
            <p>Pra você que prefere segurança e quer evitar riscos.</p>
          </div>
          <img src="../img/seguro.png" alt="Imagem Perfil Conservador" />
          <div class="detalhes">
            <p>O perfil conservador prioriza investimentos de baixo risco, garantindo a segurança do capital investido. O <u>Dólar</u> é uma ótima opção para acompanhar.</p>
            <div class="icone_moeda">
              <h1 class="texto-moeda" id="valorDolar">Carregando...</h1>
              <img src="../img/dolar.png" alt="">
            </div>
          </div>
        </div>

        <div class="perfil" onclick="expandir(this, 2)">
          <div class="texto">
            <h2>Perfil Moderado</h2>
            <p>Equilibra segurança e rentabilidade.</p>
          </div>
          <img src="../img/moderado.png" alt="Imagem Perfil Moderado" />
          <div class="detalhes">
            <p>O perfil moderado busca equilíbrio entre risco e retorno.</p>
            <div class="icone_moeda">
              <h1 class="texto-moeda" id="valorEuro">Carregando...</h1>
              <img src="../img/euro.png" alt="">
            </div>
          </div>
        </div>

        <div class="perfil" onclick="expandir(this, 3)">
          <div class="texto">
            <h2>Perfil Arrojado</h2>
            <p>Busca o maior retorno, aceitando riscos.</p>
          </div>
          <img src="../img/arrojado.png" alt="Imagem Perfil Arrojado" />
          <div class="detalhes">
            <p>O perfil arrojado busca maiores rentabilidades, aceitando riscos elevados.</p>
            <div class="icone_moeda">
              <h1 class="texto-moeda" id="valorBitcoin">Carregando...</h1>
              <img src="../img/btc.png" alt="">
            </div>
          </div>
        </div>
      </div>

      <div id="botoesContainer" style="text-align: center; display: none; margin-top: 20px; position: absolute; bottom: 10px; left: 0; right: 0;">
        <input type="submit" id="botaoEscolha" class="botao" value="Confirmar Escolha" />
        <button type="button" class="botao" onclick="voltar()">Voltar</button>
      </div>
    </form>


    <script>
      async function atualizarValores() {
        try {
          const response = await fetch('https://economia.awesomeapi.com.br/json/last/USD-BRL,EUR-BRL,BTC-BRL');
          const data = await response.json();
          document.getElementById('valorDolar').textContent = 'Valor: $' + parseFloat(data.USDBRL.bid).toFixed(2);
          document.getElementById('valorEuro').textContent = 'Valor: €' + parseFloat(data.EURBRL.bid).toFixed(2);
          document.getElementById('valorBitcoin').textContent = 'Valor: ₿' + parseFloat(data.BTCBRL.bid).toFixed(2);
        } catch (error) {
          console.error('Erro ao buscar valores: ', error);
        }
      }

      atualizarValores();
    </script>
  </body>
</html>
