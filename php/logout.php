<?php
if (session_status() !== PHP_SESSION_ACTIVE) 
{
	session_start();
}
unset($_SESSION['id']);
unset($_SESSION['pass']);
unset($_SESSION['user']);
unset($_SESSION['player1']);
unset($_SESSION['player2']);
unset($_SESSION['gameID']);

session_write_close();
header("Location:/ADISE2020_Dominoes/login.html");
exit;
?>