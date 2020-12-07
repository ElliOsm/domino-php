<?php 
    testingGrounds();

    function testingGrounds(){
        $state = dominoState([0,1]);

        printCurrentPlayerHand($state);

        $state = takeFromPile($state);
        printCurrentPlayerHand($state);


        $state = playDomino($state, 3);

        printBoard($state);
        //$state = playDomino($state, 5);
    }

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

    //state initialiser function 
    function dominoState($playerIds) {
        $deck = deck();
        $startingDeck = shuffleDeck($deck);

        $state = ["deck" => $startingDeck,
            "players" => 
            [   0 =>[ "id" => $playerIds[0],
                        "hand" => take(7, $startingDeck)],
                1 =>[ "id" => $playerIds[1],
                        "hand" => take(7, drop(7, $startingDeck))]
            ],
            "board" => take(1, drop(14, $startingDeck)),
            "pile" => drop(15, $startingDeck),
            "current-player" => 0 // only 0 or 1. i use this for also indexing the players
        ];
        return $state;
    }

    function getCurrentPlayerHand($state) {
        $playerIndex = $state["current-player"];
        return $state["players"][$playerIndex]["hand"];
    }

    function setCurrentPlayerHand($state, $newHand) {
        $playerIndex = $state["current-player"];
        $state["players"][$playerIndex]["hand"] = $newHand;
        return $state;
    }

    function addDominoToPlayer($state, $domino) {
        $playerIndex = $state["current-player"];
        array_push($state["players"][$playerIndex]["hand"] , $domino);
        return $state;
    }

    // TODO: FIX THIS 
    function removeDominoFromPlayer($state, $domino) {
        $oldHand = getCurrentPlayerHand($state);
        $newHand = array_filter($oldHand, isDominoThere($oldHand, $domino));
        
        return setCurrentPlayerHand($state,$newHand); //keep all domino that are not the same as the given domino
    }

    function isDominoThere($arr, $domino){
        return $arr["front"] === $domino["front"] && $arr["back"] === $domino["back"]; 
    }

    //remove the top domino from the pile, update the pile and return it via state
    function takeFromPile($state) {
        $oldPile = $state["pile"];
        $state["pile"] = drop(1, $oldPile);
        
        $state = addDominoToPlayer($state, take(1, $oldPile));
        return $state;
    }   
    
    function printBoard($state){
        print_r($state["board"]);
    }

    function printCurrentPlayerHand($state){
        $playerIndex = $state["current-player"];
        print_r($state["players"][$playerIndex]["hand"]);
    }


    function nextTurn($state) {
        $state["current-player"] ^= 1; //basically it functions as an NAND gate 0-0=1 // 1-1=0
        return $state;
    }
    
    function addDominoToBoard($state, $domino) {
        $board = $state["board"];
        $lastElement =  array_pop($board);
        $firstElemet = array_shift($board);
        if($domino["front"] == $lastElement["back"]){
            array_push($state["board"],$domino);
            $state = removeDominoFromPlayer($state, $domino);
        }elseif($domino["back"] == $lastElement["front"]){
            array_unshift($state["board"],$domino);
            $state = removeDominoFromPlayer($state, $domino);
        }else{
            echo ("invalid play");
        }
        return $state;
    }

    function playDomino($state, $pos){
        $playerIndex = $state["current-player"];
        $domino = $state["players"][$playerIndex]["hand"][$pos];
        
        $state  = addDominoToBoard($state,$domino);
        isItOver($state);
        return nextTurn($state);
    }

    function isItOver($state){
        $hand = getCurrentPlayerHand($state);
        if($hand == null){
            echo "The Game is over! ". $state["current-player"] . " won!";
            //maybe freeze all html elements so he cant make any more moves?
        }
    }


?>
