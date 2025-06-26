<?php
include_once('config.php');


// FUNÇÃO QUE BUSCA O ID DO USUARIO NO BANCO DE DADOS
function verificaId($conexao, $email, $senha) {

    $id_usuario = "";

    $query = "SELECT verificaId(?, ?) AS id_usuario";
    $stmt = $conexao->prepare($query);  
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $stmt->bind_result($id_usuario); 
    if ($stmt->fetch()) {
        return $id_usuario;
    } else {
        return null;
    }
}



function obterIdMoeda($conexao, $nomeMoeda){

    $id_moeda = "";
    //      777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777777
}


//FUNÇÃO DE PESQUISAR OS DADOS ATUAIS DA MOEDA QUE O USUARIO ESCOLHEU
 function obterDadosMoedaUsuario($conexao, $id_usuario) {

    $chamada = $conexao->prepare("CALL obterDadosMoedaUsuario(?, 
        @p_moeda, @p_risco_atual, @p_caminho_imagem, 
        @p_valor_compra, @p_valor_venda, @p_minima_mensal, 
        @p_maxima_mensal, @p_data_cotacao)");
    $chamada->bind_param("i", $id_usuario);
    $chamada->execute();
    $chamada->close();

    // Recupera os valores das variáveis OUT com um SELECT
    $result = $conexao->query("SELECT 
        @p_moeda AS moeda,
        @p_risco_atual AS risco_atual,
        @p_caminho_imagem AS caminho_imagem,
        @p_valor_compra AS valor_compra,
        @p_valor_venda AS valor_venda,
        @p_minima_mensal AS minima_mensal,
        @p_maxima_mensal AS maxima_mensal,
        @p_data_cotacao AS data_cotacao");

    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    } else {
        return [
            'moeda' => 'Moeda não associada',
            'risco_atual' => '-',
            'caminho_imagem' => '../img/logo.png',
            'valor_compra' => '-',
            'valor_venda' => '-',
            'minima_mensal' => '-',
            'maxima_mensal' => '-',
            'data_cotacao' => '-'
        ];
    }
}


//FUNÇÃO DE TEMPORIZADOR
function tempoDecorrido($data) {

    $agora = new DateTime();
    $dataNotificacao = new DateTime($data);
    $diferenca = $agora->diff($dataNotificacao);

    if ($diferenca->y > 0) {
        return $diferenca->y . ' ano' . ($diferenca->y > 1 ? 's' : '') . ' atrás';
    } 
    elseif ($diferenca->m > 0) {
        return $diferenca->m . ' mês' . ($diferenca->m > 1 ? 'es' : '') . ' atrás';
    } 
    elseif ($diferenca->d > 0) {
        return $diferenca->d . ' dia' . ($diferenca->d > 1 ? 's' : '') . ' atrás';
    } 
    elseif ($diferenca->h > 0) {
        return $diferenca->h . ' hora' . ($diferenca->h > 1 ? 's' : '') . ' atrás';
    } 
    elseif ($diferenca->i > 0) {
        return $diferenca->i . ' minuto' . ($diferenca->i > 1 ? 's' : '') . ' atrás';
    }
     else {
        return 'Agora mesmo';
    }
}
?>