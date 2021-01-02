<?php
require "domino-function-library.php";

if(!isset($connected)||$connected == false){
	require "dbconnect.php";
}
if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
}
	/*
	$JSONstate = selectState($_SESSION['gameID']);
	$state = jsonToState($JSONstate);
*/
if (isset($_GET['button'])) {
	
	$json_output = array();
	if ($_GET['button'] == "start"){
		$json_output['hand'] = json_encode("firstName": "Rack",
											"lastName": "jackson",
											"gender": "man");
	}
	else if ($_GET['button'] == "play"){
		//playDomino($state, $_GET['front'] , $_GET['back']);
		$json_output['board'] = json_encode("firstName": "mary","lastName": "poppins","gender": "yes");
		$json_output['hand'] = json_encode("firstName": "maw",
											"lastName": "maw",
											"gender": "maw");
	}
	else if ($_GET['button'] == "flip"){
		//flipDominoInMyHand($state, $_GET['front'] , $_GET['back'])
		$json_output['hand'] = json_encode("firstName": "no",
											"lastName": "no",
												"gender": "no");
	}
	else if ($_GET['button'] == "draw"){
		//takeFromPile($state)
		$json_output['hand'] = json_encode("firstName": "gfer",
											"lastName": "rega",
											"gender": "magrfefn");
	}/*
	$JSONstate = stateToJSON($state);
	updateTableFromState($JSONstate,$_SESSION['gameID']);
	*/
	session_write_close();

/*
	if($state["end"]==True){
		header('Location:');
	}
	*/
	return $json_output;
}
die;
?>