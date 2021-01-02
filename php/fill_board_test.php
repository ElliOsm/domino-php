<?php //if this is displayed it works
//echo 'PHP installation is successful!<br/>';
//$dominoes_numbers = array('','1','2','3','4','5','6'); //7 possible values for the tiles
//$dominoes_numbers_len = count($dominoes_numbers); //dynamically calculate max values
//$tiles = array(); //28 domino tiles array for default double six game
//$max_tiles = 28;

//for ($i = 0; $i < $dominoes_numbers_len; $i++)
//{
//	for ($j = $i;$j < $dominoes_numbers_len; $j++)
//	{
//		$tiles[] = $dominoes_numbers[$i].','.$dominoes_numbers[$j];
//	}
//}
//shuffle($tiles);

//$random_tiles = array_rand($tiles, 6);
//$player1 = [];
//$player2 = [];
//foreach ($random_tiles as $rt)
//{
//	$player1[] = $tiles[$rt];
//	unset($tiles[$rt]);
//}

//$random_tiles = array_rand($tiles, 6);
//foreach ($random_tiles as $rt)
//{
//	$player2[] = $tiles[$rt];
//}


class domino_game {
	private $possible_tiles;
	private $tiles;
	
	protected $turn;
	protected $whoPlays;
	
	public $players;
	
	/**
	* <p>A domino game class. It can generate tiles based on what game type is given.</p>
	* The game stores the players and gives them each a hand or loads their hand from database.
	* On new game, conditions are checked to ensure the game follows the original rules.
	* For instance: The game is played by 1 vs CPU or 2 to 4 players.
	*/
	public function __construct($dominotype)
	{
		$this->turn = 0;
		$this->tiles = array();
		$this->players = array();
		$tempgame = str_replace(' ','',$dominotype);
		if ($tempgame == 'new_game')
		{
			//$this->possible_tiles = array('0','1','2','3','4','5','6');
			//establice a connection to the database
			if(!isset($connected)||$connected == false){
				require "dbconnect.php";
			}
			//request the number series 0-6
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
				$this->possible_tiles = $temp;
			}
			else
			{
				echo 'error';
			}
		}
		elseif ($tempgame == 'load_game')
		{
			//will be implemented later
		}
	}
	
	/**
	* <p>Starts a new game. The tiles are generated based on game type, given at <script>__construct()</script>.
	* Tiles are shuffled and each player takes 6 or more, using the <script>draw()</script> method.
	* Then turns are assigned and the first player(human or CPU) can start playing.</p>
	* @return bool True if the game started or false accompanied with an error message if failed.
	*/
	public function newGame()
	{
		if (empty($this->players))
		{
			trigger_error('domino_game::No players are set.',E_USER_ERROR);
			return false;
		}
		elseif (count($this->players) > 4)
		{
			trigger_error('domino_game::Too many players are present. Cannot start the game.', E_ERROR);
			return false;
		}
		
		$tilesRange = count($this->possible_tiles);
		for ($i = 0; $i < $tilesRange; $i++)
		{
			for ($j = $i;$j < $tilesRange; $j++)
			{
				//$this->tiles[] = $this->possible_tiles[$i].','.$this->possible_tiles[$j];
				$this->tiles[] = array("left"=>$this->possible_tiles[$i],"right"=>$this->possible_tiles[$j]);
			}
		}
		shuffle($this->tiles);
		
		foreach ($this->players as $p)
		{
			$temp_tiles = $this->draw(6);
			$p->storeInHand($temp_tiles);
		}
		$this->whoPlays = 0;
		return true;
	}
	
	public function loadGame()
	{
	}
	
	/**
	* Draw one or more tiles from the board randomly.
	* When a tile is drawn, it's destroyed so it can't be reselected.
	* @return array Returns an array of values or a value.
	*/
	public function draw($howMany=1)
	{
		$random_tiles = array_rand($this->tiles, $howMany);
		$tiles_out = array();
		foreach ($random_tiles as $rt)
		{
			$tiles_out[] = $this->tiles[$rt];
			unset($this->tiles[$rt]);
		}
		return $tiles_out;
	}
	
	/**
	* Registers a player to the game only if he isn't already there.
	* @Return void
	*/
	public function registerPlayer($p)
	{
		if (!in_array($p,$this->players))
		{
			$this->players[] = $p;
		}
	}
	
	public function showPossibleTiles()
	{
		return $this->possible_tiles;
	}
	public function showBoard()
	{
		return $this->tiles;
	}
}
class player {
	private $uid;
	private $hand;
	private $game;
	public $name;
	
	/**
	* <p>Creates a player with an empty hand and a random unique id.
	* If it's a new game, his hand and id is stored in db.
	* If the game is loaded, his hand and id will be filled with the previous values.
	* A game object must be created first to be passed to the player.</p>
	* 
	* @param domino_game $gameToPlay Game is given dynamically when the player is created.
	*/
	public function __construct($name,$gameToPlay)
	{
		$this->uid = 1;
		$this->player_name = $name;
		$this->hand = array();
		$this->shouldPlay = false;
		if (get_class($gameToPlay) == 'domino_game')
		{
			$this->game = $gameToPlay;
		}
		else
		{
			trigger_error('player::This game is not supported.',E_USER_ERROR);
		}
	}
	
	public function storeInHand($items)
	{
		if (is_array($items))
		{
			foreach ($items as $item)
			{
				$this->hand[] = $item;
			}
		}
		else
		{
			$this->hand[] = $items;
		}
	}
	
	public function showHand()
	{
		return $this->hand;
	}
	
	public function showName()
	{
		return $this->player_name;
	}
}
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
//Class testing
$dominoes = new domino_game('new_game');

$player1 = new player($_SESSION['user'],$dominoes);
$player2 = new player('CPU',$dominoes);

$dominoes->registerPlayer($player1);
$dominoes->registerPlayer($player2);
$dominoes->newGame();
//End class testing

session_write_close();

//Class debug output
echo '<pre>
Debug New domino game
***************
Possible tiles: ';
print_r($dominoes->showPossibleTiles());
'Full set of tiles: '.count($dominoes->showBoard()).
'Tiles generated:<br/>';
print_r($dominoes->showBoard());
echo '<br/>Player <span style="color:blue">'.$player1->showName().'</span> has in hand: ';
print_r($player1->showHand());
//echo '<br/>Player <span style="color:blue">'.$player2->showName().'</span> has in hand: ';
//print_r($player2->showHand());
echo '</pre>';
//End class debug output

die;
?>