<?php
// parâmetros para conexão com banco de dados
$user = 'root';
$dbpassword = '5972';
$database = 'files_upload';
$host = 'localhost';

// conectando banco de dados com objeto mysqli e impondo os parâmetros
$mysqli = new mysqli($host, $user, $dbpassword, $database);

// teste de erro de conexão com banco de dados
if($mysqli->connect_error){ 
    die('Falha na conexão ao banco de dados: ' . $mysqli->error);
}
?>