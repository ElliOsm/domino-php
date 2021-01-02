<?php /**
* In this page the user gets authenticated by the system.
*/
if(!isset($connected)||$connected == false){
	require "dbconnect.php";
}
//$connected = true;
if (true && $connected) {
	if (!isset($post_username)) {
		$post_username = htmlspecialchars($_POST['uname']);
	}
	if (!isset($post_password)) {
		$post_password = htmlspecialchars($_POST['pass']);
	}
	
	$query = "SELECT username FROM players WHERE BINARY(username) = '$post_username' AND BINARY(password) = '$post_password'";

	$login_check = $dbcon->query($query);
	if ($login_check == true) {
		$login_numrows = $login_check->num_rows;
	}
	else {
		echo $dbcon->error();
	}
	
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	if ($login_numrows == 1) {
		$login_row = $login_check->fetch_assoc();
		
		//id should not be empty
		if (!empty($login_row)) {
			$_SESSION['status'] = 1;
			$_SESSION['user'] = $login_row['username'];
			$_SESSION['player1'] = $login_row['username'];
			$_SESSION['loginMessage'] = 'Hello '.$post_username.'! Welcome to dominoes game.';
			//adds user to the list of active players
			$_SESSION['player1'] = $login_row['username'];
			$username1 = $_SESSION['player1'];
			$query = "INSERT INTO Active_players (username) VALUES ('$username1')";
        	$dbcon->query($query);	
        }
	}
	elseif ($login_numrows == 0) {
		$_SESSION['status'] = 0;
		$_SESSION['loginMessage'] = 'User not found.';
		$_SESSION['user'] = -1;
	}
	else {
		$_SESSION['status'] = 2;
		$_SESSION['loginMessage'] = 'Connection error.';
		$_SESSION['user'] = -1;
	}
	session_write_close();
	
	header('Location:start.php');
	//print_r($login_row);
	
}
exit;