from analise import analisar_variacao
from mensagem import mensagem_wpp
import mysql.connector
from time import sleep

def listar_bancos(conexao):
    cursor = conexao.cursor()
    cursor.execute("SHOW DATABASES;")
    bancos = cursor.fetchall()
    print("Bancos disponíveis:", bancos)
    cursor.close()

def conectar_banco():
    #Faz a conexão com o banco de dados.
    try:
        conexao = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="BANCO_TCC"
        )
        return conexao
    except mysql.connector.Error as err:
        
        print(f"Erro ao conectar ao banco: {err}")
        return None
    


def get_usuarios(conexao):
    cursor = conexao.cursor()
    #Pega todo mundo que está registrado no banco de dados
    query = """
    SELECT 
        u.id_usuario,
        u.telefone,
        m.moeda,
        u.novo_usuario,
        u.Ativado_notificacao
    FROM usuarios u
    JOIN usuario_perfil up ON u.id_usuario = up.id_usuario
    JOIN moedas m ON m.id_moeda = up.id_moeda
    WHERE u.Ativado_notificacao = 'true';
    """

    cursor.execute(query)
    usuarios = cursor.fetchall()
    print (usuarios)
    return usuarios


def new_user(cursor, conexao, telefone, novo_usuario):
    # Verifica se o usuário é novo e envia mensagem de boas-vindas
    if novo_usuario == 0:
        mensagem_wpp(f"+55{telefone}", "👋 Olá! Seja bem-vindo ao Moneta.\n\n"
    "Nosso sistema monitora ativos como Bitcoin, Dólar e Euro em tempo real.\n"
    "Você receberá alertas automáticos por aqui sempre que houver uma queda significativa "
    "em algum ativo do seu interesse.\n\n"
    "Fique tranquilo: só enviamos notificações quando for realmente relevante.\n"
    "Conte com a gente para tomar decisões mais inteligentes. 🚀")
        
        query = """
            UPDATE usuarios 
            SET novo_usuario = 1
            WHERE telefone = %s;
        """
        cursor.execute(query, (telefone,))
        conexao.commit()


def gerar_notificacao(cursor, conexao, moeda_api, id_moeda, telefone):

    mensagem, porcentagem_nova = analisar_variacao(moeda_api, 30)
    if not mensagem:
        return None, None

    ultima_notificacao = get_ultima_notificacao(conexao, id_moeda)
    ultima_porcentagem = get_ultima_porcentagem(conexao, id_moeda)

    if id_moeda == 3:
        limite_diferenca = 2
    elif id_moeda in (1, 2):
        limite_diferenca = 1
    else:
        limite_diferenca = 1

    if ultima_porcentagem is None or abs(float(porcentagem_nova) - float(ultima_porcentagem)) >= limite_diferenca:
        if ultima_notificacao is None or ultima_notificacao != mensagem:
            mensagem_wpp(f"+55{telefone}", mensagem)

            query = """
                INSERT INTO notificacao (id_moeda, texto_da_notificacao, porcentagem)
                VALUES (%s, %s, %s);
            """
            cursor.execute(query, (id_moeda, mensagem, porcentagem_nova))
            conexao.commit()
            return mensagem, porcentagem_nova
        else:
            print("Mensagem repetida.")
            return None, None
    else:
        print("Nao bateu uma porcentagem relevante.")
        return None, None

    
def get_ultima_notificacao(conexao, id_moeda):

    cursor = conexao.cursor(dictionary=True)
    query = f"""
        SELECT *
        FROM notificacao
        WHERE id_moeda = %s
        ORDER BY data_criacao DESC
        LIMIT 1;
    """
    cursor.execute(query, (id_moeda,))
    ultima_notificacao = cursor.fetchone()
    
    if ultima_notificacao:
        return ultima_notificacao["texto_da_notificacao"]
    
    else:
        return None
    
def get_ultima_porcentagem(conexao, id_moeda):
    
    cursor = conexao.cursor(dictionary=True)
    query = f"""
        SELECT *
        FROM notificacao
        WHERE id_moeda = %s
        ORDER BY data_criacao DESC
        LIMIT 1;
    """
    cursor.execute(query, (id_moeda,))
    ultima_porcentagem = cursor.fetchone()
    
    if ultima_porcentagem:
        return ultima_porcentagem["porcentagem"]
    
    else:
        return None
    
#Função principal basicamente.
def processar_notificacoes(conexao):
    cursor = conexao.cursor()
    try:
        usuarios = get_usuarios(conexao)
        
        for id_usuario, telefone, moeda, novo_usuario, ativado_notificacao in usuarios:

            if not telefone or not moeda:
                continue

            moeda_api = moeda.lower()
            if moeda_api == "dólar":
                moeda_api = "usd"
                id_moeda = 1
            elif moeda_api == "euro":
                moeda_api = "eur"
                id_moeda = 2
            elif moeda_api == "bitcoin":
                moeda_api = "bitcoin"
                id_moeda = 3
            else:
                continue

            if novo_usuario == 0:
                new_user(cursor, conexao, telefone, novo_usuario)
            else:
                gerar_notificacao(cursor, conexao, moeda_api, id_moeda, telefone)

    finally:
        cursor.close()


#Logica para puxar tudo e testar ESSA MERDA!!!

while True:
    conexao = conectar_banco()
    try:
        processar_notificacoes(conexao)
    except Exception as e:
        print(f"[ERRO] Ocorreu um problema: {e}")
    finally:
        conexao.close()
    sleep(15)

