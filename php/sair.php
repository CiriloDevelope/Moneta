<?php
session_start();

unset($_SESSION['email']);
unset($_SESSION['senha']);
unset($_SESSION['id_usuario']);

header('Location:index1.php');



?>