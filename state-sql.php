<?php

/*
include 'domino-function-library.php';
include '../dbconnect.php';

$state = dominoState(['jack','jill']);
printCurrentPlayerHand($state);

$JSONstate = stateToJSON($state);
insertTableFromState($JSONstate,'4');

$hand = getCurrentPlayerHand($state);
$domino = array_pop($hand);
$front = $domino["front"];
$back = $domino["back"];
$state = playDomino($state,$front,$back);

$JSONstate = stateToJSON($state);
updateTableFromState($JSONstate,'4');
$state = dominoState(['n','m']);
echo "<br>";
echo "<br>";
echo "<br>";
print($state["current-player"]);
echo "<br>";
echo "<br>";
printCurrentPlayerHand($state);



$newjsonstate = selectState('4');
echo "<br>";
echo "<br>";

echo "<br>";
$state = jsonToState($newjsonstate);
var_dump($state);
*/

    function insertTableFromState($JSONstate,$gameID){
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $database = 'dominoes';
        $port = '3306';
        $dbcon = new mysqli($host,$user,$pass,$database,$port);
                
        $query = "INSERT INTO state (gameID,currentState) VALUES ('$gameID','$JSONstate')";
        mysqli_query ($dbcon, $query);        
    }

    function updateTableFromState($JSONstate,$gameID){
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $database = 'dominoes';
        $port = '3306';
        $dbcon = new mysqli($host,$user,$pass,$database,$port);

        $query = "UPDATE state SET currentState = '$JSONstate' WHERE gameID = '$gameID' ";
        mysqli_query ($dbcon, $query);       
    }

    //returns in JSON format
    function selectState($gameID){
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $database = 'dominoes';
        $port = '3306';
        $dbcon = new mysqli($host,$user,$pass,$database,$port);

        $query = "SELECT currentState FROM state WHERE (gameID = '$gameID') ";
        $result =  mysqli_query ($dbcon, $query);;
        $array = ($result->fetch_assoc());
        return $array["currentState"];
    }

    
?>