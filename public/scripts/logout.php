<?php
session_start(); // iniciando sessão atual
session_unset(); // limpando sessão atual
session_destroy(); // saindo da sessão
header("Location: ../../views/login.php"); // redirecionando usuário para login
?>