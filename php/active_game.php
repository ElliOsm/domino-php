<?php
	if(!isset($connected)||$connected == false){
	require "dbconnect.php";
	}		
	
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	$_SESSION['active_G'] = false;
	
	//brings the active users from the DB to find a second player
	$query = "SELECT player2, player1 FROM state";
	$activeG_check = $dbcon->query($query);
	
	if ($activeG_check == true) {
		$activeG_numrows = $activeG_check->num_rows;
	}
	else {
		echo $dbcon->error();
	}
	if ($activeG_numrows > 1) {
		$found = false;
		while(($activeG_row = $activeG_check->fetch_assoc())&& $found != true ) {
		
			if($activeG_row['player2'] == $_SESSION['user']){
				
				$_SESSION['player2'] = $_SESSION['user'];
				$_SESSION['player1'] = $activeP_row['player1'];

				$player1 = $_SESSION['player1'];
				$player2 = $_SESSION['player2'];
	
				$query = "SELECT gameID FROM state WHERE player1 = '$player1' AND player2 = '$player2'";
				$game_check = $dbcon->query($query);
				if ($game_check == true) {
					$game_numrows = $game_check->num_rows;
				}
				else {
					echo $dbcon->error();
				}
				if ($game_numrows == 1) {
					$game_row = $game_check->fetch_assoc();
					//id should not be empty
					if (!empty($game_row)) {
							$_SESSION['gameID'] = $game_row['gameID'];
					}
				}
				
				$JSONstate = selectState($_SESSION['gameID']);
				$state = jsonToState($JSONstate);
				
				$found = true;
				$_SESSION['active_G'] = true;
			}
		}
	}
	elseif ($activeG_numrows <= 1) {
		$_SESSION['loginMessage'] = 'The game has not been set.';
	}
	else {
		$_SESSION['loginMessage'] = 'Connection error.';
	}
	
	session_write_close();

	exit;
?>