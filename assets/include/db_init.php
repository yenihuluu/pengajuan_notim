<?php

require_once PATH_ASSETS.DS.'/include/global_variable.php';

date_default_timezone_set('Asia/Jakarta');

$errorMessage = "";
//  $serverDB = "localhost"; //local
 $serverDB = "10.15.14.55"; //dev
 $userDB = "root";
  $pwdDB = "Jpro@2016"; //prod
// $pwdDB = ""; //local
$database = "jatim_pengajuan"; //prod

 //$serverDB = "10.15.14.54"; //prod
//  $serverDB = "10.15.14.55"; //dev
// $pwdDB = "Jpro@2016";
//$userDB = "root";
// $database = "jatim_inventory"; //prod
// $database = "jatim_inventory3"; //dev
//   $database = "jatim_pengajuan"; //local


$myDatabase = new mysqli($serverDB, $userDB, $pwdDB);
if ($myDatabase->errno) {
    $isDatabaseError = TRUE;
    $errorMessage = "Unable to connect to the database : " . $myDatabase->error . "<br />";
} else {
    $isDatabaseError = FALSE;
}

if (!$isDatabaseError) {
    $myDatabase->select_db($database);
    if ($myDatabase->errno) {
        $isDatabaseError = TRUE;
        $errorMessage = "Unable to connect to the database : " . $myDatabase->error . "<br />";
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
