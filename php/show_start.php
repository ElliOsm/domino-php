<?php
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	if (isset($_SESSION['status'])) {
		if ($_SESSION['status'] == 1) {
		echo $_SESSION['loginMessage'];
		
		?>
			<form action="active_players.php" method="POST">					
				<label for="newgame"><button class="button2">new game</button></label><br/>
				<input type="submit" name="newgame" style="display:none">
			</form>
		<?php
		}
		elseif ($_SESSION['status'] == 0) { 
			echo $_SESSION['loginMessage'];
			?>
				<a href="/ADISE2020_Dominoes/register.html">Register to dominoes.</a>
			<?php 
		}
		exit;
	}
	else {
		http_response_code(403);
		exit;
	}
?>