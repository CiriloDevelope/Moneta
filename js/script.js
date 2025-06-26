// Função para fazer a requisição à API e calcular a conversão
function calcularConversao() {
    const moedaOrigem = document.getElementById('moedaOrigem').value;
    const moedaDestino = document.getElementById('moedaDestino').value;
    const valor = parseFloat(document.getElementById('valor').value);

    if (isNaN(valor) || valor <= 0) {
        document.getElementById('conversao').value = "Informe um valor válido";
        return;
    }

    const url = 'https://economia.awesomeapi.com.br/json/last/USD-BRL,EUR-BRL,BTC-BRL';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const taxas = {
                brl: 1.0,
                usd: parseFloat(data.USDBRL.bid),
                eur: parseFloat(data.EURBRL.bid),
                btc: parseFloat(data.BTCBRL.bid)
            };

            const valorEmBRL = valor * taxas[moedaOrigem];
            const valorConvertido = valorEmBRL / taxas[moedaDestino];

            document.getElementById('conversao').value = valorConvertido.toFixed(2);
        })
        .catch(error => {
            console.error('Erro ao obter dados da API:', error);
            document.getElementById('conversao').value = 'Erro ao calcular';
        });
}




let carregando = false;  // Flag para controlar o estado de requisição

function updateChart(dias = 7) {
    if (carregando) {
        return; // Se já estiver carregando, não faz outra requisição
    }

    carregando = true; // Marca que está carregando

    const moeda = document.getElementById("moeda").value;
    const url = `http://localhost:8000/graficos?moeda=${moeda}&dias=${dias}`;


    // Desabilita os botões enquanto está carregando
    document.querySelectorAll("button").forEach(btn => btn.disabled = true);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const valores = data.valores;

            if (!valores || !Array.isArray(valores)) {
                console.error("Erro: 'valores' não encontrado ou não é um array válido.");
                return;
            }

            const labels = valores.map(p => p.timestamp);
            const precos = valores.map(p => p.preco);

            // Se já existir um gráfico, destrua o anterior
            if (window.grafico && typeof window.grafico.destroy === 'function') {
                window.grafico.destroy();
            }

            // Criação do novo gráfico
            const ctx = document.getElementById("grafico").getContext("2d");
            window.grafico = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: `Preço do ${moeda.toUpperCase()}`,
                        data: precos,
                        borderColor: "#4CAF50",
                        backgroundColor: "rgba(5, 252, 13, 0.2)",
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Data' } },
                        y: {
                            title: { display: true, text: 'Preço (BRL)' },
                            ticks: { beginAtZero: false }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error("Erro ao obter os dados da API: ", err);
        })
        .finally(() => {
            // Reativa os botões e libera o estado de carregamento
            document.querySelectorAll("button").forEach(btn => btn.disabled = false);
            carregando = false; // Libera o carregamento
        });
}


window.onload = function () {
    updateChart();
};




