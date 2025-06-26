<?php
$bdname = "BANCO_TCC";  
$bdhost = "LocalHost";    
$bdusername = "root";       
$bdpassword = "";        

$conexao = new mysqli($bdhost, $bdusername, $bdpassword, $bdname);

if($conexao->connect_errno) {
    //print("Erro na conexão: ");
}

 else {
  //print("Conexão efetuada com sucesso!");
}
?>
