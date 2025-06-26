from fastapi import FastAPI, Query
from fastapi.middleware.cors import CORSMiddleware
from datetime import datetime
import httpx
import time

# Para executar esse codigo rode no terminal "uvicorn api:app --reload"

app = FastAPI()

# Permitir requisições de qualquer origem
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Nome correto das moedas para a CoinGecko
mapa_moedas = {
    "bitcoin": "bitcoin",
    "usd": "tether",
    "eur": "euro-coin"
}

cache = {}

@app.get("/graficos")
async def graficos(moeda: str = Query(...), dias: int = Query(...)):

    if moeda not in mapa_moedas or dias not in [2,7,30,365]:
        return {"erro": "Moeda invalida ou periodo invalido."}
    
    #Evitando problema de travar a api xeba da coingecko

    chave = f"{moeda}_{dias}"
    agora = time.time()

    if chave in cache:
        resultado, timestamp = cache[chave]
        if agora - timestamp < 180:
            return {"valores": resultado}


    
    url = f"https://api.coingecko.com/api/v3/coins/{mapa_moedas[moeda]}/market_chart?vs_currency=brl&days={dias}"

    async with httpx.AsyncClient() as client:
        response = await client.get(url)
        dados = response.json()
        lista_formatada = []

        for item in dados["prices"]:
            timestamp = datetime.fromtimestamp(item[0] / 1000).strftime('%Y-%m-%d')
            preco = round(item[1], 2)
            lista_formatada.append({"timestamp": timestamp, "preco": preco})

        # Armazena valores de moedas sem fazer requisição (cache)
        cache[chave] = (lista_formatada, agora)

        return {"valores": lista_formatada}
    

# Para executar esse codigo rode no terminal "uvicorn main:app --reload"