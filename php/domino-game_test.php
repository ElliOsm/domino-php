<?php
include 'domino-function-library.php';
include 'state-sql.php';


//connect to sql
if(!isset($connected)||$connected == false){
    require "dbconnect.php";
}

//create session
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

//get session user 1 and 2
$_SESSION['id'] = uniqid(); //generates a unique id
$player1 = $_SESSION['user'];
$player2 = $_SESSION['user2'];
$_SESSION['id'];



$state = dominoState($player1,$player2);

//preparation
echo '<br />';
while($state["end"] == FALSE){
    echo 'The player who plays first is ' . getCurrentPlayerId($state); " .";
    echo '<br />';
    echo 'His/Her hand is : ';
    echo '<br />';
    printCurrentPlayerHand($state);
    echo '<br />';
    echo 'The board is : ' ;
    echo '<br />';
    printBoard($state);
    echo '<br />';
    //USER INPUT
    $hand = getCurrentPlayerHand($state);
    $domino = array_pop($hand);
    $front = $domino["front"];
    $back = $domino["back"];
    $state = playDomino($state,$front,$back);
    updateTableFromState($state);

}




?>