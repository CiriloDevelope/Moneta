<!DOCTYPE html>
    <html lang="PT_BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/grafico.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <title>analise financeira</title>
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
      
            <div class="content">
                <div class="info-box">
                    <div class="icon-container">
                        <img src="../img/grafico.png" alt="Gráfico">
                    </div>
                    <p>Gráfico Analitico</p>
                </div>
            </div>

            <div class="grafico">
                <div id="tradingview_chart" style="height:500px;"></div>

                    <script>
                        new TradingView.widget({
                            "container_id": "tradingview_chart",
                            "autosize": true,
                            "height": 700,
                            "symbol": "FX:BTCUSD",  // EUR/USD - Pode mudar para BTC/USD, etc.
                            "interval": "1",
                            "timezone": "Etc/UTC",
                            "theme": "light",
                            "style": "1",
                            "locale": "br",
                            "enable_publishing": false,
                            "hide_side_toolbar": false,
                            "allow_symbol_change": true,
                            "studies": ["RSI@tv-basicstudies"], // Indicadores
                        });
                    </script>
            </div>
        </div>
    </body>
</html>