<?php

require "domino-function-library.php";
require "state-sql.php";
if (!isset($connected) || $connected == false) {
    require "dbconnect.php";
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['gameID'])) {
    $player1 = $_SESSION['player1'];
    $player2 = $_SESSION['player2'];
    $query = "SELECT gameID FROM state WHERE player1 = '$player1' AND player2 = '$player2'";
    $game_check = $dbcon->query($query);
    if ($game_check == true) {
        $game_numrows = $game_check->num_rows;
        if ($game_numrows == 1) {
            $game_row = $game_check->fetch_assoc();
            //id should not be empty
            if (!empty($game_row)) {
                $_SESSION['gameID'] = $game_row['gameID'];
            }
        }
    } else {
        echo $dbcon->error;
    }
}

$JSONstate = selectState($_SESSION['gameID']);
$state = jsonToState($JSONstate);

//$_SESSION['current_P'] = $state['current-player'];
$_SESSION['current_P'] = getActivePlayer($_SESSION['gameID']);
$player = getCurrentPlayerId($state);

$button = $_GET['button'];
if ($_SESSION['current_P'] != $player){
	$button = "start";
}

$json_output = array();
 
if (isItOver($state)) {
		//status 3 for end game.
		$json_output['goto_url'] = 'php/end.php';
}
$json_output['end'] = $state["end"];

if (isset($button)) {
   
    if ($button== "start") {
        $json_output['board'] = getBoardToJSON($state);
        //$json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['player1']);
        $json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['current_P']);
		$json_output['cp'] = $player ;
    } else if ($button == "play") {
        $state = playDomino($state, $_GET['front'], $_GET['back']);
        $json_output['board'] = getBoardToJSON($state);
        $json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['current_P']);
		$json_output['cp'] = $player ;
        //$json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['player1']);
    } else if ($button == "flip") {
        $state = flipDominoInMyHand($state, $_GET['front'], $_GET['back']);
        $json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['current_P']);
		$json_output['cp'] = $player ;
        //$json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['player1']);
    } else if ($button == "draw") {
        $state = takeFromPile($state);
        //$json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['player1']);
        $json_output['hand'] = getPlayerHandToJSON($state, $_SESSION['current_P']);
		$json_output['cp'] = $player ;
    }
    $JSONstate = stateToJSON($state);
    updateTableFromState($JSONstate, $_SESSION['gameID']);
	
	echo json_encode($json_output);
}
session_write_close();
exit;
?>