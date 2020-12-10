<?php
include 'domino-function-library.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$state = dominoState(["Jack","Jill"]);
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
    //input
    

}

?>