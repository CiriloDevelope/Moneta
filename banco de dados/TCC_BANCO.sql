CREATE DATABASE IF NOT EXISTS BANCO_TCC;
USE BANCO_TCC;

-- TABELA DE USUÁRIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(45) NOT NULL,
    email VARCHAR(45) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_nasc DATE,
    telefone CHAR(11) NOT NULL,
    Ativado_notificacao VARCHAR(15) DEFAULT 'false',
    novo_usuario boolean default false, 
    frequencia_notificacao int default 7       
);


-- TABELA DE MOEDAS
CREATE TABLE IF NOT EXISTS moedas (
    id_moeda INT PRIMARY KEY AUTO_INCREMENT,
    moeda VARCHAR(45) NOT NULL UNIQUE,
    risco_atual VARCHAR(30),
    caminho_imagem VARCHAR(30)
);

-- Inserindo dados fixos das moedas
INSERT IGNORE INTO moedas (moeda, risco_atual, caminho_imagem) VALUES
('Dólar', 'Baixo', '../img/dolar.png'),
('Euro', 'Médio', '../img/euro.png'),
('Bitcoin', 'Alto', '../img/btc.png');

-- TABELA DE COTAÇÕES
CREATE TABLE IF NOT EXISTS cotacoes (
    id_cotacao INT PRIMARY KEY AUTO_INCREMENT,
    id_moeda INT NOT NULL,
    valor_compra DECIMAL(15, 5),
    valor_venda DECIMAL(15, 5),
    minima_mensal DECIMAL(15, 5),
    maxima_mensal DECIMAL(15, 5),
    data_cotacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);

-- TABELA DE TIPOS DE PERFIL
CREATE TABLE IF NOT EXISTS tipo_perfil (
    id_tipo_perfil INT PRIMARY KEY AUTO_INCREMENT,
    classificacao INT NOT NULL,
    descricao_perfil VARCHAR(200),
    caminho_imagem VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS iframes( 
id_iframe INT PRIMARY KEY AUTO_INCREMENT, 
id_moeda INT, 
iframe_texto TEXT,
 FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
 );


INSERT INTO iframes (id_moeda, iframe_texto) VALUES
(1, '<iframe class="deshboard" title="modelo" width="1450" height="650" src="https://app.powerbi.com/reportEmbed?reportId=c3df0760-3d3c-4345-8058-af982325fa68&autoAuth=true&ctid=ed38466c-b641-437d-9ae9-d801b829fa94" frameborder="0" allowFullScreen="true"></iframe>'),
(3, '<iframe title="modelo2" width="1450" height="650" src="https://app.powerbi.com/reportEmbed?reportId=25f81d20-cf45-4ba8-8776-d6ae52eb3d66&autoAuth=true&ctid=ed38466c-b641-437d-9ae9-d801b829fa94" frameborder="0" allowFullScreen="true"></iframe>'),
(2, '<iframe title="modelo3" width="1450" height="650" src="https://app.powerbi.com/reportEmbed?reportId=d285137e-96f1-4482-9c53-db69e0bec47b&autoAuth=true&ctid=ed38466c-b641-437d-9ae9-d801b829fa94" frameborder="0" allowFullScreen="true"></iframe>');

-- Inserindo perfis de usuário
INSERT IGNORE INTO tipo_perfil (classificacao, descricao_perfil, caminho_imagem) VALUES
(1, 'Perfil conservador', '../img/seguro.png'),
(2, 'Perfil moderado', '../img/moderado.png'),
(3, 'Perfil arrojado', '../img/arrojado.png');

-- TABELA DE ASSOCIAÇÃO USUÁRIO/PERFIL/MOEDA
CREATE TABLE IF NOT EXISTS usuario_perfil (
    id_usuario INT,
    id_tipo_perfil INT,
    id_moeda INT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_tipo_perfil, id_moeda),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_tipo_perfil) REFERENCES tipo_perfil(id_tipo_perfil),
    FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);

-- TABELA DE NOTIFICAÇÕES
CREATE TABLE IF NOT EXISTS notificacao (
    id_notificacao INT PRIMARY KEY AUTO_INCREMENT,
    perfil_de_risco VARCHAR(50),
    id_moeda INT,
	data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    texto_da_notificacao TEXT NOT NULL,
    porcentagem DECIMAL(5, 2),
    FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);



-- TABELA DE RELAÇÃO ENTRE NOTIFICAÇÃO E USUÁRIO/PERFIL/MOEDA
CREATE TABLE IF NOT EXISTS notificacao_perfil (
    id_notificacao INT,
    id_usuario INT,
    id_tipo_perfil INT,
    id_moeda INT,
    PRIMARY KEY (id_notificacao, id_usuario, id_tipo_perfil, id_moeda),
    FOREIGN KEY (id_notificacao) REFERENCES notificacao(id_notificacao),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_tipo_perfil) REFERENCES tipo_perfil(id_tipo_perfil),
    FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);



-- FUNÇÕES DO BANCO DE DADOS

DELIMITER //
CREATE FUNCTION verificaId(p_email VARCHAR(255), p_senha VARCHAR(255)) RETURNS INT
BEGIN 
    DECLARE id_usuario INT; 
    SELECT u.id_usuario INTO id_usuario 
    FROM usuarios u 
    WHERE u.email = p_email AND u.senha = p_senha; 
    RETURN id_usuario; 
END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE obterDadosMoedaUsuario (
    IN p_id_usuario INT, -- IN são valores de entrada na função
    OUT p_moeda VARCHAR(100), OUT p_risco_atual VARCHAR(100), OUT p_caminho_imagem VARCHAR(255), OUT p_valor_compra DECIMAL(10,2), -- OUT SAO VALORES DE SAIDA DA FUNCAO
    OUT p_valor_venda DECIMAL(10,2),OUT p_minima_mensal DECIMAL(10,2), OUT p_maxima_mensal DECIMAL(10,2), OUT p_data_cotacao DATE
)
BEGIN
    SELECT 
    m.moeda, m.risco_atual, m.caminho_imagem, -- TABELA MOEDA
    c.valor_compra,c.valor_venda,c.minima_mensal,c.maxima_mensal,c.data_cotacao -- TABELA COTAÇÃO
    INTO 
    p_moeda,p_risco_atual,p_caminho_imagem,p_valor_compra,
	p_valor_venda,p_minima_mensal,p_maxima_mensal,
	p_data_cotacao
    
    FROM usuario_perfil up
    JOIN moedas m ON m.id_moeda = up.id_moeda
    JOIN (
        SELECT * FROM cotacoes
        WHERE data_cotacao = (
            SELECT MAX(data_cotacao)
            FROM cotacoes c2
            WHERE c2.id_moeda = cotacoes.id_moeda
        )
    ) c ON c.id_moeda = m.id_moeda
    WHERE up.id_usuario = p_id_usuario
    LIMIT 1;
END //
DELIMITER ;

-- FUNCAO QUE BUSCA NO BANCO O ID MOEDA BASEADO NO NOME DA MOEDA
DELIMITER //
CREATE FUNCTION obterIdMoeda(p_nome_moeda VARCHAR(255)) RETURNS INT 
BEGIN
	DECLARE v_id_moeda INT;
    SELECT id_moeda INTO v_id_moeda
    FROM moedas
    WHERE moeda = p_nome_moeda;
    RETURN v_id_moeda;
END//
DELIMITER ;








-- CONSULTAS ÚTEIS PARA VERIFICAÇÃO:
SELECT * FROM usuarios;
SELECT * FROM moedas;
SELECT * FROM cotacoes;
SELECT * FROM tipo_perfil;
SELECT * FROM usuario_perfil;
SELECT * FROM notificacao;
SELECT * FROM notificacao_perfil;







