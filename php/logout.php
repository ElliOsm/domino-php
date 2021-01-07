<?php

if (!isset($connected) || $connected == false) {
    require "dbconnect.php";
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$player1 = $_SESSION['user'];
$query = "DELETE FROM Active_players WHERE username = '$player1'";
$dbcon->query($query);

/*unset($_SESSION['id']);
unset($_SESSION['pass']);
unset($_SESSION['user']);
unset($_SESSION['player1']);
unset($_SESSION['player2']);
unset($_SESSION['gameID']);*/
session_destroy();

session_write_close();
header("Location:../login.html");
exit;
?>