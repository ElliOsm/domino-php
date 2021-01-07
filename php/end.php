<!DOCTYPE html>
<html dir="ltr">
	<!-- #BeginTemplate "../../dominoes_test_layout.dwt" -->
	<head>
		<!-- #BeginEditable "doctitle" -->
		<title>Untitled 1</title>
		<link rel="stylesheet" href="../domino.css"/>
		<!-- #EndEditable -->
	</head>
	<body>
		<div id="main">
			<div id="head">
				<h1>Classic Dominoes</h1>
			</div>
			<div id="board">
				<!--here will be the board game -->
				<!-- #BeginEditable "body" -->
				<div id="game">
					<form action="logout.php" method="post" style="text-align:left">
						<label for="logout"><button>Logout</button></label><br/>
						<input type="submit" name="logout" style="display:none">
					</form>
					<div id="iner_game">
						<?php
							include 'show_end.php';
						?>
					</div>
				</div>
				<!-- #EndEditable -->
			</div>
			<div id="foot">
				<h4>Copyrights © ADISE20_Dominoes team</h4>
			</div>
		</div>
	</body>
<!-- #EndTemplate -->
</html>