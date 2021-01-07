<?php
	$host = 'localhost';
	$user = 'root';
        //$user = 'user';
	$pass = '';
	$database = 'dominoes';
	$port = '3306';
	
	$dbcon = new mysqli($host,$user,$pass,$database,$port);
	$connected = false;
	if ($dbcon !== false) {
		//echo "DB connected.";
		$connected = true;
	}
	elseif (!empty($dbcon->error)) {
		echo $dbcon->errno.' '.$dbcon->error;
	}
	else {
		echo "Database obj is empty.";
		exit;
	}