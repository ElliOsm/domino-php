<?php
    $player1 = $_SESSION["player1"];
    $player2 = $_SESSION["player2"];


    $state = dominoState([$player1,$player2]);
    $JSONstate = stateToJSON($state);
    insertTableFromStateWithoutGameID($JSONstate,$player1,$player2);

    $query = "DELETE FROM Active_players WHERE username = '$player1' AND username = '$player2'";
    $dbcon->query($query);

    //takes the ganeID from the DB and adds it to session.
	
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
	elseif ($game_numrows == 0) {
		$_SESSION['loginMessage'] = 'Game has not been set.';
	}
	else {
		$_SESSION['loginMessage'] = 'Connection error.';
    }
    
    session_write_close();
    

    header('Location:/ADISE2020_Dominoes/api.html');
    
?>
