import requests

def analisar_variacao(moeda, dias):
    url = f"http://localhost:8000/graficos?moeda={moeda}&dias={dias}"

    resposta = requests.get(url)
    dados = resposta.json()
    valores = dados["valores"]

    preco_inicial = valores[0]["preco"]
    preco_final = valores[-1]["preco"]

    if moeda == "bitcoin":

        variacao = ((preco_final - preco_inicial) / preco_inicial) * 100

        if variacao <= -10:
            msg =  f"""📉 Alerta de Queda no Ativo!
O ativo BTC (Bitcoin) sofreu uma queda de {variacao:.2f}% nos últimos {dias} dias.
💰 Valor atual: R$ {preco_inicial:,.2f}

Fique atento! Pode ser um bom momento para reavaliar suas estratégias.
"""
            

        elif variacao >= 10:
            msg = msg =  f"""📈 Alerta de Alta no Ativo!
O ativo BTC (Bitcoin) sofreu uma alta de {variacao:.2f}% nos últimos {dias} dias..
💰 Valor atual: R$ {preco_inicial:,.2f}

Fique atento! Pode ser um bom momento para reavaliar suas estratégias.
"""
        else:
            print(f"Bitcoin nao bateu nenhuma das condições de queda ou baixa alta, variação atual: {variacao}")
            return None, None

    elif moeda == "usd":

        variacao = ((preco_final - preco_inicial) / preco_inicial) * 100

        if variacao <= -1:
            msg =  msg =  f"""📉 Alerta de Queda no Ativo!
O ativo USD (Dolar) sofreu uma queda de {variacao:.2f}% nos últimos {dias} dias.
💰 Valor atual: R$ {preco_inicial:,.2f}

Fique atento! Pode ser um bom momento para reavaliar suas estratégias.
"""

        elif variacao >= 1:
            msg =  msg =  f"""📈 Alerta de Alta no Ativo!
O ativo USD (Dolar) sofreu uma alta de {variacao:.2f}% nos últimos {dias} dias.
💰 Valor atual: R$ {preco_inicial:,.2f}

Fique atento! Pode ser um bom momento para reavaliar suas estratégias.
"""

        else:
            print(f"Dolar nao bateu nenhuma das condições de queda ou baixa alta, variação atual: {variacao}")
            return None, None
    
    elif moeda == "eur":

        variacao = ((preco_final - preco_inicial) / preco_inicial) * 100

        if variacao <= -1:
            msg =   f"""📉 Alerta de Queda no Ativo!
O ativo Eur (Euro) sofreu uma queda de {variacao:.2f}% nos últimos {dias} dias.
💰 Valor atual: R$ {preco_inicial:,.2f}

Fique atento! Pode ser um bom momento para reavaliar suas estratégias.
"""

        elif variacao >= 1:
            msg = f"""📈 Alerta de Alta no Ativo!
O ativo Eur (Euro) sofreu uma alta de {variacao:.2f}% nos últimos {dias} dias.
💰 Valor atual: R$ {preco_inicial:,.2f}

Fique atento! Pode ser um bom momento para reavaliar suas estratégias.
"""

        else:
            print(f"Euro nao bateu nenhuma das condições de queda ou baixa alta, variação atual: {variacao}")
            return None, None

   
    return (msg, variacao)



    



