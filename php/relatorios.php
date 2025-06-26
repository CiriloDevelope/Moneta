<?php
session_start();
include_once('config.php');

// Verifica a sessão do usuário
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

$iframeHTML = "<p style='color:red;'>Erro ao carregar gráfico.</p>"; // valor padrão

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $id_usuario = $row['id_usuario'];

    // Buscar o id_moeda do perfil do usuário
    $sqlMoeda = "SELECT id_moeda FROM usuario_perfil WHERE id_usuario = ?";
    $stmtMoeda = $conexao->prepare($sqlMoeda);
    $stmtMoeda->bind_param("i", $id_usuario);
    $stmtMoeda->execute();
    $resultadoMoeda = $stmtMoeda->get_result();

    if ($resultadoMoeda->num_rows > 0) {
        $rowMoeda = $resultadoMoeda->fetch_assoc();
        $id_moeda = $rowMoeda['id_moeda'];

        // Buscar o iframe correspondente à moeda
        $sqlIframe = "SELECT iframe_texto FROM iframes WHERE id_moeda = ?";
        $stmtIframe = $conexao->prepare($sqlIframe);
        $stmtIframe->bind_param("i", $id_moeda);
        $stmtIframe->execute();
        $resultadoIframe = $stmtIframe->get_result();

        if ($resultadoIframe->num_rows > 0) {
            $rowIframe = $resultadoIframe->fetch_assoc();
            $iframeHTML = $rowIframe['iframe_texto'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações</title>
    <link rel="stylesheet" href="../css/relatorio.css">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <h1>Menu</h1>
        <ul class="menus">
            <li><a href="informacoes_usuario.php"><img src="../img/usuario.png" alt="Usuario"><span>Usuario</span></a></li>
            <li><a href="PaginaPrincipal.php"><img src="../img/home.png" alt="Home"><span>Home</span></a></li>
            <li><a href="notificacao.php"><img src="../img/notificacao.png" alt="Notificação"><span>Notificação</span></a></li>
            <li><a href="relatorios.php"><img src="../img/relatorio.png" alt="Relatorio"><span>Relatorio</span></a></li>
            <li><a href="grafico.php"><img src="../img/grafico.png" alt="Usuario"><span>Gráficos</span></a></li>
            <li><a href="sair.php"><img src="../img/sair.png" alt="Sair"><span>Sair</span></a></li>
        </ul>
    </div>

    <div class="conteudo">
        <h1>Relatório de Analise</h1>
        <?php echo $iframeHTML; ?>
    </div>
</body>
</html>
