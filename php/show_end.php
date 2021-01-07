<?php
	
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	if (isset($_SESSION['status'])) {
		
		include "reactivate.php"; 
		
		if ($_SESSION['status'] == 3) {
		echo $_SESSION['EndMessage'];
		
		?>
			<form action="../waiting.html" method="POST">					
				<label for="newgame"><button class="button2">Start again</button></label><br/>
				<input type="submit" name="newgame" style="display:none">
			</form>
		<?php
		}
		else{ 
			echo "Error.";
		}
		exit;
	}
	else {
		http_response_code(403);
		exit;
	}
?>