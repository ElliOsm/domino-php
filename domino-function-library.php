<?php 

    //creating an assosiative array 
    function deck(){
        if(!isset($connected)||$connected == false){
            require "dbconnect.php";
        }
        //request the number series 0-6 from the databace
        
        $query = "SELECT numTiles FROM tiles";
        $result = $dbcon->query($query);
        //create temporary array to store them
        $temp = array();
        
        if ($result->num_rows > 0) 
        {	
            // takes the results row by row and places them to a temporary array. 
            while($row = $result->fetch_assoc()) 
            {
                $temp[] = $row["numTiles"];
            }
            $possible_tiles = $temp;
        }
        else
        {
            //echo 'error';
        }
        //dhmiourgei to deck
        $tilesRange = count($possible_tiles);
        for ($i = 0; $i < $tilesRange; $i++)
        {
            for ($j = $i;$j < $tilesRange; $j++)
            {
                $cards[] = ["front"=>($possible_tiles[$i]),"back"=>($possible_tiles[$j])];
            }
        }
    
        return $cards;
    }

    //take the n first elements of an array
    function take($n , $array) {
        return array_slice($array, "0", $n);
    }

    //take the n+ elements of an array
    function drop($n, $array) {
        return array_slice($array,$n);
    }

    //function for shuffling an assosiative array as shuffle() doesnt function properly with the key-value pairs
    function shuffleDeck($deck){
        $keys = array_keys($deck);
        shuffle($keys);
        foreach($keys as $key) {
            $new[$key] = $deck[$key];
        }
        $deck = $new;
        return $deck;
    }

    //state initializer function 
    function dominoState($playerIds) {
        $deck = deck();
        $startingDeck = shuffleDeck($deck);
        $state = ["deck" => $startingDeck,
            "players" => 
            [   0 => [ "id" => $playerIds[0],
                        "hand" => take(6, $startingDeck)],
                1 => [ "id" => $playerIds[1],
                        "hand" => take(6, drop(6, $startingDeck))]
            ],
            "board" => array(),
            "pile" => drop(12, $startingDeck),
            "current-player" => 0, // only 0 or 1. i use this for also indexing the players
            "end" => FALSE
        ];
        $state = chooseWhoPlaysFirst($state);
        if($state["current-player"] == -1){
            do {
                $state = redraw($state);
            }while($state["current-player"] == -1);
        }
        return $state;
    }

    //call at the start of the game if we need a redraw
    function redraw($state){
        $deck = deck();
        $startingDeck = shuffleDeck($deck);
        $state["players"][0]["hand"] = take(6, $startingDeck);
        $state["players"][1]["hand"] = take(6, drop(6, $startingDeck));
        $state["pile"] = drop(12, $startingDeck);
        $state = chooseWhoPlaysFirst($state);
        return $state;
    }

    //random function that chooses who plays first
    function chooseWhoPlaysFirst($state){
        /*
        $randIndex = rand(0,1);
        $state["current-player"] = $randIndex;
        */

        $player2Value = -1;
        $player1Value = -1;

        $player1Hand =  $state["players"][0]["hand"];
        $player2Hand =  $state["players"][1]["hand"];


        for($i=0; $i < 7 ; $i++){
            foreach($player1Hand as $num1 => $value1){
                if ($value1["front"] == $i && $value1["back"] == $i){
                    $player1Value = $i;
                }
            }
            foreach($player2Hand as $num2 => $value2){
                if ($value2["front"] == $i && $value2["back"] == $i){
                    $player2Value = $i;
                }
            }
        }
        if (($player2Value == $player1Value)){
            $state["current-player"] = -1;    
        }elseif($player1Value > $player2Value){
            $state["current-player"] = 0;
        }elseif($player2Value > $player1Value){
            $state["current-player"] = 1;
        }

        return $state;
    }

    function getCurrentPlayerHand($state) {
        $playerIndex = $state["current-player"];
        return $state["players"][$playerIndex]["hand"];
    }


    function getCurrentPlayerId($state) {
        $playerIndex = $state["current-player"];
        return $state["players"][$playerIndex]["id"];
    }

    function setCurrentPlayerHand($state, $newHand) {
        $playerIndex = $state["current-player"];
        $state["players"][$playerIndex]["hand"] = $newHand;
        return $state;
    }

    function addDominoToPlayer($state, $domino) {
        $playerIndex = $state["current-player"];
        array_push($state["players"][$playerIndex]["hand"] , ...$domino);
        return $state;
    }

    /*remove the given domino from player
    function removeDominoFromPlayer($state, $domino) {
        $oldHand = getCurrentPlayerHand($state);
        $newHand = array_filter($oldHand,  function($oldHand) use($domino){
            $tempFront = $oldHand["front"];
            $tempBack = $oldHand["back"];
            return $oldHand["front"] != $domino["front"] && $oldHand["back"] != $domino["back"];
        });
        return setCurrentPlayerHand($state,$newHand); 
    }*/
	
	function unsetDominoFromHand($state,$domino){
        $oldHand = getCurrentPlayerHand($state);
        $index = findDominoInHand($state,$domino);
		$oldHand[$index] = ["front" => " ", "back" => " "];
        return setCurrentPlayerHand($state,$oldHand);
    }
        

    //remove the top domino from the pile, update the pile and return it via state
    function takeFromPile($state) {
        $oldPile = $state["pile"];
        $state["pile"] = drop(1, $oldPile);
        $state = addDominoToPlayer($state, take(1, $oldPile));
        isItOver($state);
        return nextTurn($state);
    }   
   /* 
    function printBoard($state){
        var_dump($state["board"]);
    }

    function printCurrentPlayerHand($state){
        $playerIndex = $state["current-player"];
        var_dump($state["players"][$playerIndex]["hand"]);
    }
    */

    function nextTurn($state) {
        $state["current-player"] ^= 1; //basically it functions as an XNOR gate 0-0=1 // 1-1=0
        return $state;
    }
    
    function addDominoToBoard($state, $domino) {
        $board = $state["board"];
        $lenght = count($board);
        if (empty($board)){
            $state = unsetDominoFromHand($state,$domino);
            array(array_push($state["board"], $domino));
        }elseif($lenght==1){
            $onlyElement =  array_pop($board);
            if($domino["front"] == $onlyElement["back"]){
                $state = unsetDominoFromHand($state,$domino);
                array_push($state["board"] , $domino);
            }elseif($domino["back"] == $onlyElement["front"]){
                $state = unsetDominoFromHand($state,$domino);
                array_unshift($state["board"], $domino);
            }else{
                return $state;
            }
        }else{
            $firstElement = array_shift($board);
            $lastElement =  array_pop($board);

            if($domino["front"] == $lastElement["back"]){
                $state = unsetDominoFromHand($state,$domino);
                array_push($state["board"] , $domino);
            }elseif($domino["back"] == $firstElement["front"]){
                $state = unsetDominoFromHand($state,$domino);
                array_unshift($state["board"], $domino);
            }else{
                return $state;
            }
        }
        return $state;
    }

    //function that plays a domino and checks if it is over
    function playDomino($state, $front , $back){
        $playerIndex = $state["current-player"];
        $domino = ["front" => $front, "back" => $back];
        $state  = addDominoToBoard($state,$domino);
        isItOver($state);
        return nextTurn($state); //to change player turns
    }

    function flipDominoInMyHand($state, $front , $back){
        $domino = ["front" => $front, "back" => $back];
        $newDomino = flipDomino($domino);
        $dominoIndex = findDominoInHand($state,$domino);
        if($dominoIndex == -1){
            return $state;
        }
        $playerIndex = $state["current-player"];
        $state["players"][$playerIndex]["hand"][$dominoIndex] = $newDomino;
        return $state;
    }

    function findDominoInHand($state, $domino) {
        $hand = getCurrentPlayerHand($state);
        foreach($hand as $num1 => $value1){
            if($value1 == $domino){
                return $num1;
            }
        }
        //echo"there was an error try again";
        return -1;
    }

    //function that flips any given domino
    function flipDomino($domino){
        $temp = $domino["front"];
        $domino["front"] = $domino["back"];
        $domino["back"] = $temp;
        return $domino;
    }

    //function that checks when the game is over
    function isItOver($state){
        $hand = getCurrentPlayerHand($state);
        $pile = $state["pile"];
		if (session_status() !== PHP_SESSION_ACTIVE) {
				session_start();
		}
		// places the end message in session.
        if(isHandEmpty($hand)){
			$_SESSION['EndMessage'] = "The Game is over! ". $state["current-player"] . " won!";
            //maybe freeze all html elements so he cant make any more moves?
            $state["end"] = TRUE;
        }elseif(empty($pile)){
            $winPlayerIndex = countPoints($state);
            if($winPlayerIndex != -1){
               $state["end"] = TRUE;
			   $_SESSION['EndMessage'] = "The Game is over! ". $state["players"][$winPlayerIndex]["id"] . " won!";
            }else{
               $_SESSION['EndMessage'] = "its a draw.";
            }
        }
    }

    function isHandEmpty($hand){
        foreach($hand as $num1 => $value1){
            if(($value1["front"] != " ") || ($value1["back"] != " ")){
                return FALSE;
            }
        }
        return TRUE;
    }
    
    //function to cound all points to both player hands
    function countPoints($state){
        $player1Hand =  $state["players"][0]["hand"];
        $player2Hand =  $state["players"][1]["hand"];

        $adderPlayer1 = 0;
        $adderPlayer2 = 0;

        $numMax = [0,0];
        foreach($player1Hand as $num1 => $value1){
            if(($value1["front"] != " ") || ($value1["back"] != " ")){
                $adderPlayer1 = $adderPlayer1 + $value1["front"] + $value1["back"];
            }
        }
        foreach($player2Hand as $num2 => $value2){
            if(($value2["front"] != " " || $value2["back"] != " ")){
                $adderPlayer2 = $adderPlayer2 + $value2["front"] + $value2["back"];
            }
        }

        if($adderPlayer2 == $adderPlayer1){
            return -1;
        }elseif($adderPlayer2 < $adderPlayer1){
            return 0;
        }elseif($adderPlayer2 > $adderPlayer1){
            return 1;
        }else{
            //echo "there was an error";
        }

    }
    
    function stateToJSON($state){
        $jsonState = json_encode($state);
        return $jsonState;
    }


    function jsonToState($jsonState){
        $newState = json_decode($jsonState, true);
        return $newState;
    }

    function getPlayerHandToJSON($state,$name) {
        if($state["players"][0]["id"]==$name){
            $index=0;
        }else{
            $index=1;
        }
        $hand = $state["players"][$index]["hand"];
        //$jsonHand = json_encode($hand);
        return $hand;
    }

    function getBoardToJSON($state) {
        $board = $state["board"];
        //$jsonBoard = json_encode($board);
        return $board;
    }
    
    //helper function to check if a game is active under conditions.
    //the function takes our current logged in player and matches him with another active player,
    //then checks if the two have an active game and returns true on line 361,
    //in all other instances it returns false as stated on line 364;
    function check_active_game() {
        global $dbcon;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['active_G'] = false;
        $player1 = $_SESSION['player1'];
        
        //$rows_active_players = $active_players->fetch_all(MYSQLI_ASSOC);
        //$inline_active_players = implode(',',$rows_active_players[0]);

        $query = "SELECT gameID,player1,player2 FROM state 
            WHERE (player1 = '$player1' OR player2 = '$player1') 
            AND (player1 IN (SELECT username FROM active_players WHERE username <> '$player1') 
                OR player2 IN (SELECT username FROM active_players WHERE username <> '$player1'))";
        $active_game = $dbcon->query($query);

        if ($active_game != false) {
            if ($active_game->num_rows == 1) {
                $active_game_row = $active_game->fetch_assoc();
                $_SESSION['gameID'] = $active_game_row['gameID'];
                $_SESSION['player1'] = $active_game_row['player1'];
                $_SESSION['player2'] = $active_game_row['player2'];
                $_SESSION['active_G'] = true;
                return true;
            }
        }
        return false;
    }
    
    function getActivePlayer($gameID) {
        global $dbcon;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $query = "SELECT player1, player2 FROM state WHERE gameID = $gameID";
        $game = $dbcon->query($query);
        if ($game !== false) {
            $game_row = $game->fetch_assoc();
            if ($_SESSION['user'] == $game_row['player1']) {
                return $game_row['player1'];
            }
            else {
                return $game_row['player2'];
            }
        }
        else {
            return false;
        }
        //if ($_SESSION[''])
    }


/*
    function pileToJSON($state){
        $pile = $state["pile"];
        $jsonPile = json_encode($pile);
        return $jsonPile;
    }
    
    function jsonToPile($jsonPile){
        $newPile = json_decode($jsonPile, true);
        $state["pile"] = $newPile;
        return $state;
    }
    function handToJSON($state, $playerOffset){
        $hand = $state["players"][$playerOffset]["hand"];
        $jsonHand = json_encode($hand);
        return $jsonHand;
    }
    
    function jsonTohand($jsonHand, $playerOffset){
        $newHand = json_decode($jsonHand, true);
        $state["players"][$playerOffset]["hand"] = $newHand;
        return $state;
    }
 */


?>
