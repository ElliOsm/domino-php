<?php
	if(!isset($connected)||$connected == false){
            require "dbconnect.php";
    }

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	$player1 = $_SESSION['player1'];
	$player2 = $_SESSION['player2'];	
	$query = "UPDATE state SET player1 = 'ended' WHERE player1 = '$player1'";
	$dbcon->query($query);
	$query = "UPDATE state SET player2 = 'ended' WHERE player2 = '$player2'";
	$dbcon->query($query);
?>