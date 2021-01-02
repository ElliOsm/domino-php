<?php
require "dbconnect.php";

$post_username = htmlspecialchars($_POST['uname']);
$post_password = htmlspecialchars($_POST['pass']);

$register_query = "INSERT INTO players (username,password) VALUES ('$post_username','$post_password')";
$statement = $dbcon->query($register_query);
//$register_affected_rows = $statement->affected_rows;
if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
}
if ($statement !== false ) {
	$_SESSION['registerMessage'] = 'Success!';
	$_SESSION['status'] = 1;
	
	include 'login.php';
}
else {
	//error
	$_SESSION['status'] = 2;
	$_SESSION['registerMessage'] = 'Connection error.';
	$_SESSION['user'] = -1;
	header('Location:/ADISE2020_Dominoes/register.html');
	
	session_write_close();
	exit;
}