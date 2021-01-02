<?php

//include 'domino-function-library.php';

    function insertTableFromState($JSONstate,$gameID){ 
        if(!isset($connected)||$connected == false){
            require "dbconnect.php";
        }
               
        $query = "INSERT INTO state (gameID,currentState) VALUES ('$gameID','$JSONstate')";
        mysqli_query ($dbcon, $query);  
    
    }

    function insertTableFromStateWithoutGameID($JSONstate,$player1,$player2){ 
        if(!isset($connected)||$connected == false){
            require "dbconnect.php";
        }

        $query = "INSERT INTO state (player1, player2, currentState) VALUES ('$player1', '$player2', '$JSONstate')";
        mysqli_query ($dbcon, $query);
    
    }


    function updateTableFromState($JSONstate,$gameID){
        if(!isset($connected)||$connected == false){
            require "dbconnect.php";
        }
        $query = "UPDATE state SET currentState = '$JSONstate' WHERE gameID = '$gameID' ";
        mysqli_query ($dbcon, $query);  
    }

    //returns in JSON format
    function selectState($gameID){
        if(!isset($connected)||$connected == false){
            require "dbconnect.php";
        }
        $query = "SELECT currentState FROM state WHERE (gameID = '$gameID') ";
        $result =  mysqli_query ($dbcon, $query);;
        $array = ($result->fetch_assoc());
        return $array["currentState"];
    }

    
?>