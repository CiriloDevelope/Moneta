import http.client
import ssl 
import os

from dotenv import load_dotenv

load_dotenv(override=True)

token = os.getenv("token")
instance = os.getenv("instance")

# NÃO DIVULGAR ESSA FUNÇÃO TEM O MEU TOKEN PESSOAL AQUI KRL
def mensagem_wpp(numero_destino, mensagem):
    conn = http.client.HTTPSConnection("api.ultramsg.com",context = ssl._create_unverified_context())

    payload = f"token={token}&to={numero_destino}&body={mensagem}"
    payload = payload.encode('utf8').decode('iso-8859-1') 

    headers = { 'content-type': "application/x-www-form-urlencoded" }

    conn.request("POST", f"/{instance}/messages/chat", payload, headers)

    res = conn.getresponse()
    data = res.read()

    print(data.decode("utf-8"))


# mensagem_wpp("+5511963553555", "funciona")