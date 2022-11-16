<?php

require_once PATH_ASSETS.DS.'/include/global_variable.php';

date_default_timezone_set('Asia/Jakarta');
    
$errorMessage = "";
$serverDB = "192.168.1.138";
$userDB = "root";
$pwdDB = "root";
$database = "provident_inventory";

$myDatabaseServer = new mysqli($serverDB, $userDB, $pwdDB, $database, 8889);
if ($myDatabaseServer->errno) {
    $isDatabaseError = TRUE;
    $errorMessage = "Unable to connect to the database : " . $myDatabaseServer->error . "<br />";
} else {
    $isDatabaseError = FALSE;
}

if (!$isDatabaseError) {
    $myDatabaseServer->select_db($database);
    if ($myDatabaseServer->errno) {
        $isDatabaseError = TRUE;
        $errorMessage = "Unable to connect to the database : " . $myDatabaseServer->error . "<br />";
    }
}

if ($isDatabaseError) {
    $isDBConnect = false;
    echo $errorMessage;
    exit();
} else {
    unset ($isDatabaseError);
    unset ($errorMessage);
}
?>
