<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$transactionId = $_POST['transactionId'];

// <editor-fold defaultstate="collapsed" desc="Functions">

$sql = "SELECT photo_document FROM transaction WHERE transaction_id = {$transactionId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    $photo = $row->photo_document;
}
// </editor-fold>

?>

<div id="results" class="text-center">
<img id="base64image" src="data:image/jpeg;base64,<?php echo base64_encode($photo)?>"/>
</div>




