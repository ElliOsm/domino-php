<?php
require "dbconnect.php";
require "domino-function-library.php";
require "state-sql.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$json_output = array();
if (check_active_game() == true) {
    $json_output['goto_url'] = 'api.html';
}
echo json_encode($json_output);

session_write_close();
exit;