<?php

    function createTableForState($state){
        $sql = sprintf(
            "CREATE TABLE State(
                GameID INT(6) PRIMARY KEY,
                CurrentState VARCHAR(65535)
            )"
        );
        mysql_query($sql); 
    }

    function insertTableFromState($state,$gameID){
        $sql = sprintf(
            'INSERT INTO State (GameID,CurrentState)
             VALUES 
                ($gameID,"$state")'
        );
        mysql_query($sql);        
    }

    function updateTableFromState($state,$gameID){
        $sql = sprintf(
            'UPDATE State
            SET CurrentState = "$state"
            WHERE GameID = "$gameID"
            ');
        mysql_query($sql);        
    }

    //returns in JSON format
    function selectState(){
        $sql = sprintf('SELECT CurrentState FROM State');
        return mysql_query($sql);   
    }

    
?>