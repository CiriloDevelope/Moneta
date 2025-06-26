<?php 
session_start();
include_once('funcao.php'); 
include_once('config.php');

if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    echo "<script>alert('Erro de sessão. Redirecionando para o login.'); window.location.href='sair.php';</script>";
    exit();
} 
else{
  //verifica dados usuario
    $email = $_SESSION['email']; 
    $senha = $_SESSION['senha'];
    $id_usuario = verificaId($conexao,$email,$senha);//função de buscar o id do usuario 
  
  

    $url = "https://economia.awesomeapi.com.br/last/USD-BRL,EUR-BRL,BTC-BRL";//URL DA API EM TEMPO REAL
    $resposta = file_get_contents($url);
    $dadosAPI = json_decode($resposta, true);
    
    // coloquei na array para facilitar a busca 
    $dadosMoedas = [  
        'USD-BRL' => $dadosAPI['USDBRL'],   
        'EUR-BRL' => $dadosAPI['EURBRL'],
        'BTC-BRL' => $dadosAPI['BTCBRL']
    ];


//PUXANDO OS DADOS DA CHAMADA E COLOCANDO EM VARIAVEIS
$dadosMoeda = obterDadosMoedaUsuario($conexao, $id_usuario);// FUNÇÃO QUE FAZ CHAMADA EM UM PROCEDURE DENTRO DO BANCO DE DADOS

if ($dadosMoeda && $dadosMoeda['moeda'] !== 'Moeda não associada') {
    $moeda_nome_bd = $dadosMoeda['moeda'];
    $moeda_risco_bd = $dadosMoeda['risco_atual'];
    $moeda_imagem_bd = $dadosMoeda['caminho_imagem'];
    $valor_compra_bd = $dadosMoeda['valor_compra'];
    $valor_venda_bd = $dadosMoeda['valor_venda'];
    $minima_mensal_bd = $dadosMoeda['minima_mensal'];
    $maxima_mensal_bd = $dadosMoeda['maxima_mensal'];
    $data_cotacao_bd = $dadosMoeda['data_cotacao'];
} else {
    $moeda_nome_bd = "Moeda não associada";
    $moeda_risco_bd = "-";
    $moeda_imagem_bd = "../img/logo.png";
    $valor_compra_bd = "-";
    $valor_venda_bd = "-";
    $minima_mensal_bd = "-";
    $maxima_mensal_bd = "-";
    $data_cotacao_bd = "-";
}

    foreach ($dadosMoedas as $codigo => $moeda) {
        // Convertendo o nome da moeda pra caber no banco
        $nomeMoeda = match ($codigo) {
            'USD-BRL' => 'Dólar',
            'EUR-BRL' => 'Euro',
            'BTC-BRL' => 'Bitcoin',
            default => null
        };
    
        if (!$nomeMoeda) continue;
    
        // busca o id da moeda
        $stmtMoeda = $conexao->prepare("SELECT id_moeda FROM moedas WHERE moeda = ?");
        $stmtMoeda->bind_param("s", $nomeMoeda);
        $stmtMoeda->execute();
        $stmtMoeda->bind_result($id_moeda);
        $stmtMoeda->fetch();
        $stmtMoeda->close();
    
        if (!$id_moeda) continue;
    
        // FINALMENTE ENTENDI O QUE TINHA DE ERRADO NESSA BOMBA DE CODE, ERA O TIMESTAMP QUE NAO CABIA NO BANCO :3
        $timestamp = (int)$moeda['timestamp'];
        $data_cotacao = date("Y-m-d H:i:s", $timestamp);
    
        // valores para serem inseridos no banco, convertidos para nao dar bosta (php mentiroso)
        $valor_compra = (float)$moeda['bid'];
        $valor_venda  = (float)$moeda['ask'];
        $minima_mensal = (float)$moeda['low'];
        $maxima_mensal = (float)$moeda['high'];



// Buscar a última cotação registrada no banco para esta moeda
$stmtUltima = $conexao->prepare("
    SELECT valor_compra, valor_venda 
    FROM cotacoes 
    WHERE id_moeda = ? 
    ORDER BY data_cotacao DESC 
    LIMIT 1
");
$stmtUltima->bind_param("d", $id_moeda);
$stmtUltima->execute();
$stmtUltima->store_result();

$temRegistro = $stmtUltima->num_rows > 0;

$valor_compra_ultimo = null;
$valor_venda_ultimo = null;

if ($temRegistro) {
    $stmtUltima->bind_result($valor_compra_ultimo, $valor_venda_ultimo);
    $stmtUltima->fetch();
}
$stmtUltima->close();


 // PDO de inserir 
// Verifica se houve mudança no valor
if (!$temRegistro || $valor_compra != $valor_compra_ultimo || $valor_venda != $valor_venda_ultimo) {

  $stmtInsert = $conexao->prepare("
      INSERT INTO cotacoes (id_moeda, valor_compra, valor_venda, minima_mensal, maxima_mensal, data_cotacao)
      VALUES (?, ?, ?, ?, ?, ?)
  ");
  $stmtInsert->bind_param("ddddds", $id_moeda, $valor_compra, $valor_venda, $minima_mensal, $maxima_mensal, $data_cotacao);
  $stmtInsert->execute();
  $stmtInsert->close();
}
}
}
    ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
<body>

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

  
  <div class="container">
    <div class="banner">
      <a href="notificacao.php">
      <video autoplay muted loop>
        <source src="../img/Banner.mp4" type="video/mp4">
      </video></a>
    </div>
  <div class="linha-superior">
      <div class="Texto-do-conversor">
        <h1><img src="../img/logo.png" style="width: 90px; height:90px;">MONETA </h1>
        
        <h1>Transforme valores em segundos.<br>Economia clara e sem complicações.</h1>
      </div>
      <form id="conversorForm">
        <div class="conversor">                                                             
          <h1 class="titulo_conversor">Conversor de Moedas</h1>
          <div class="select-container">
          
            <select class="select_moedas1" name="moeda_origem" id="moedaOrigem" onchange="calcularConversao()">
              <option value="usd">USD</option>
              <option value="eur">EUR</option>
              <option value="brl">BRL</option>
              <option value="btc">BTC</option>
            </select>
          
            <select class="select_moedas2" name="moeda_destino" id="moedaDestino" onchange="calcularConversao()">
              <option value="usd">USD</option>
              <option value="eur">EUR</option>
              <option value="brl">BRL</option>
              <option value="btc">BTC</option>
            </select>
          </div>

          <div class="input_conversor">
            <input type="number" id="valor" class="input" placeholder="Quantia" min="0" oninput="calcularConversao()" />
            <input type="number" id="conversao" class="input" placeholder="Convertido" min="0" readonly />
          </div>
          <p class="texto-abaixo-conversor">R$1,00 Equivale á $0,18 Dolár<?php $valor_compra?></p>
        </div>
      </form>
    </div>
     <div class="section-spacing">
      <h1>Análise o Histórico das cotações</h1>
     </div>

    <div class="coluna-inferior">
      <div class="grafico">
        <h1>Gráfico em tempo real</h1>
        <div class="grafico-controls">
          <select id="moeda">
            <option value="bitcoin">Bitcoin</option>
            <option value="usd">Dólar</option>
            <option value="eur">Euro</option>
          </select>
          <button onclick="updateChart(7)">7 dias</button>
          <button onclick="updateChart(30)">30 dias</button>
          <button onclick="updateChart(365)">1 ano</button>
        </div>
        <canvas id="grafico" height="200"></canvas>
      </div>

       <div class="conteudo">
          <div class="titulo">
              <h1><?php echo "Dados do ".$moeda_nome_bd; ?></h1>
          </div>
          <div class="conteudo-moeda">
              <div class="icone-moeda">
                  <img src="<?php echo $moeda_imagem_bd; ?>" class="png-moeda">
              </div>

              <div class="dados-moeda">
                  <div class="linha">
                      <span class="coluna-nome">Valor de Compra</span>
                      <span class="coluna-valor">R$ <?php echo number_format($valor_compra_bd, 2, ',', '.' ); ?></span>
                  </div>
                  <div class="linha">
                      <span class="coluna-nome">Valor de Venda</span>
                      <span class="coluna-valor">R$ <?php echo number_format($valor_venda_bd, 2, ',', '.'); ?></span>
                  </div>
                  <div class="linha">
                      <span class="coluna-nome">Mínima Mensal</span>
                      <span class="coluna-valor">R$ <?php echo number_format($minima_mensal_bd, 2, ',', '.'); ?></span>
                  </div>
                  <div class="linha">
                      <span class="coluna-nome">Máxima Mensal</span>
                      <span class="coluna-valor">R$ <?php echo number_format($maxima_mensal_bd, 2, ',', '.'); ?></span>
                  </div>
                  <div class="linha">
                      <span class="coluna-nome">Risco</span>
                      <span class="coluna-valor"><?php echo $moeda_risco_bd ; ?></span>
                  </div>
            </div>
          </div>
        </div>
    
      <div class="widget_trading">
        <fieldset><legend>Navegue pelas Notícias:</legend></fieldset>
        <div class="tradingview-widget-container">
          <div class="tradingview-widget-container__widget"></div>
          <div class="tradingview-widget-copyright">
            <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank">
              <span class="blue-text"></span>
            </a>
          </div>
          <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-timeline.js" async>
          {
            "feedMode": "all_symbols",
            "isTransparent": false,
            "displayMode": "regular",
            "width": "100%",
            "height": "550",
            "colorTheme": "dark", 
            "locale": "PT-BR"
          }
          </script>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/script.js"></script>
</body>

</html>