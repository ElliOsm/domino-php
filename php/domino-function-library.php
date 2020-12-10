<?php 
    //creating an assosiative array 
    function deck(){
        for ($i = 0; $i <= 6; $i++) {
            for ($j = $i; $j <= 6; $j++) {
                $cards[] = ["front" => $i, "back" => $j];
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
        $randIndex = rand(0,1);
        $state["current-player"] = $randIndex;
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

    //remove the given domino from player
    function removeDominoFromPlayer($state, $domino) {
        $oldHand = getCurrentPlayerHand($state);
        $newHand = array_filter($oldHand,  function($oldHand) use($domino){
            $tempFront = $oldHand["front"];
            $tempBack = $oldHand["back"];
            return $oldHand["front"] != $domino["front"] || $oldHand["back"] != $domino["back"];
        });
        return setCurrentPlayerHand($state,$newHand); 
    }
        

    //remove the top domino from the pile, update the pile and return it via state
    function takeFromPile($state) {
        $oldPile = $state["pile"];
        $state["pile"] = drop(1, $oldPile);
        $state = addDominoToPlayer($state, take(1, $oldPile));
        isItOver($state);
        return $state;
    }   
    
    function printBoard($state){
        var_dump($state["board"]);
    }

    function printCurrentPlayerHand($state){
        $playerIndex = $state["current-player"];
        var_dump($state["players"][$playerIndex]["hand"]);
    }


    function nextTurn($state) {
        $state["current-player"] ^= 1; //basically it functions as an NAND gate 0-0=1 // 1-1=0
        return $state;
    }
    
    function addDominoToBoard($state, $domino) {
        $board = $state["board"];
        if (empty($board)){
            $state = removeDominoFromPlayer($state, $domino);
            array_push($state["board"],$domino);
        }else{
            $lastElement =  array_pop($board);
            $firstElemet = array_shift($board);
            if($domino["front"] == $lastElement["back"]){
                $state = removeDominoFromPlayer($state, $domino);
                array_push($state["board"],$domino);
            }elseif($domino["back"] == $lastElement["front"]){
                $state = removeDominoFromPlayer($state, $domino);
                array_unshift($state["board"],$domino);
            }else{
                //add maybe a pop up window for illegal move
                echo ("invalid play");
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

    //function that flips any given domino
    function flipDomino($domino){
        $temp = $domino["front"];
        $domino["back"] = $domino["front"];
        $domino["front"] = $temp;
        return $domino;
    }

    //function that checks when the game is over
    function isItOver($state){
        $hand = getCurrentPlayerHand($state);
        $pile = $state["pile"];
        if(empty($hand)){
            echo "The Game is over! ". $state["current-player"] . " won!";
            //maybe freeze all html elements so he cant make any more moves?
            $state["end"] = TRUE;
        }elseif(empty($pile)){
            $winPlayerIndex = countPoints($state);
            if($winPlayerIndex != -1){
               $state["end"] = TRUE;
               echo "The Game is over! ". $state["players"][$winPlayerIndex]["id"] . " won!";
               echo '<br />';
            }else{
               echo "its a draw.";
               echo '<br />';
            }
        }
    }
    
    //function to cound all points to both player hands
    function countPoints($state){
        $player1Hand =  $state["players"][0]["hand"];
        $player2Hand =  $state["players"][1]["hand"];

        $adderPlayer1 = 0;
        $adderPlayer2 = 0;

        $numMax = [0,0];
        foreach($player1Hand as $num1 => $value1){
            $adderPlayer1 = $adderPlayer1 + $value1["front"] + $value1["back"];
        }
        foreach($player2Hand as $num2 => $value2){
            $adderPlayer2 = $adderPlayer2 + $value2["front"] + $value2["back"];
        }

        if($adderPlayer2 == $adderPlayer1){
            return -1;
        }elseif($adderPlayer2 < $adderPlayer1){
            return 0;
        }elseif($adderPlayer2 > $adderPlayer1){
            return 1;
        }else{
            echo "there was an error";
        }

    }


?>
