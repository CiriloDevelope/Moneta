<?php
session_start();
include_once('config.php');
include_once('funcao.php');



if (empty($_SESSION['email']) || empty($_SESSION['senha'])) {
    echo "<script>alert('Sessão inválida. Você será redirecionado para o login.'); window.location.href='sair.php';</script>";
    exit();
}

$email = $_SESSION['email'];
$senha = $_SESSION['senha'];


$sql = "SELECT id_usuario, Ativado_notificacao FROM usuarios WHERE email = ? AND senha = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
 
    echo "<script>alert('Usuário não encontrado.'); window.location.href='sair.php';</script>";
    exit();
}

$usuario = $result->fetch_assoc();
$idUsuario = $usuario['id_usuario'];

$notificacoesAtivas = ($usuario['Ativado_notificacao'] === 'true');


if (isset($_POST['submit'])) {
    $novoStatus = $notificacoesAtivas ? 'false' : 'true';

    $sqlUpdate = "UPDATE usuarios SET Ativado_notificacao = ? WHERE id_usuario = ?";
    $stmtUpdate = $conexao->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $novoStatus, $idUsuario);
    $stmtUpdate->execute();

    
    $notificacoesAtivas = !$notificacoesAtivas;


    header("Refresh:0");
    exit();
}

// Busca as notificações do usuário
$sqlNotificacoes = "
   SELECT m.id_moeda, n.id_notificacao, n.texto_da_notificacao , n.data_criacao, up.id_usuario
FROM moedas m
INNER JOIN usuario_perfil up ON m.id_moeda = up.id_moeda
INNER JOIN notificacao n ON n.id_moeda = m.id_moeda WHERE up.id_usuario = ?;
";
$stmtNotificacoes = $conexao->prepare($sqlNotificacoes);
$stmtNotificacoes->bind_param("i", $idUsuario);
$stmtNotificacoes->execute();
$resultadoNotificacoes = $stmtNotificacoes->get_result();

    
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Gerenciar Notificações</title>
    <link rel="stylesheet" href="../css/notificacao.css" />
</head>
<body>

<!-- Menu lateral -->
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

<div class="notificacoes">
    <h2 class="titulo">Suas Notificações</h2><br>

    <ul>
        <?php if ($resultadoNotificacoes->num_rows === 0): ?>
            <li>Você não possui notificações no momento.</li>
        <?php else: ?>
            <?php while ($notificacao = $resultadoNotificacoes->fetch_assoc()): ?>
                <li>
                    <div class="notificacao">
                        <span class="icone-notificacao">
                            <img src="../img/olho.png" alt="Ícone Notificação" class="olho">
                        </span>
                        <span class="mensagem-notificacao"><?php echo htmlspecialchars($notificacao['texto_da_notificacao']); ?></span><br>
                        <sma ?> 
                             <?php echo tempoDecorrido($notificacao['data_criacao']); ?>
                        </small>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php endif; ?>
    </ul>

    <br><br><br>
    <form method="POST">
        <input
            type="submit"
            class="ver-todas-notificacoes"
            name="submit"
            value="<?php echo $notificacoesAtivas ? 'Desativar Notificações' : 'Ativar Notificações'; ?>"
        >
    </form>
</div>

</body>
</html>
