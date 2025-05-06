<?php
// Inicie a sessão
session_start();

// Destrua todas as variáveis de sessão
$_SESSION = array();

// Destrua a sessão
session_destroy();

// Redirecione para a página de login (ou qualquer outra página que desejar)
header("Location: login.php");
exit;
?>
