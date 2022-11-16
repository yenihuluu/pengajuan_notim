<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

//$transactionId = $_POST['transactionId'];
//$transactionNewId = $_POST['transactionNewId'];
$type = $_POST['type'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$image = false;
if(isset($_POST['transactionId'])){
        $sql = "SELECT ttsp.pic,ttsp.pic_truck FROM jatim_inventory.transaction t
        RIGHT JOIN jatim_inventory.transaction_timbangan tt ON tt.transaction_id = t.t_timbangan
        RIGHT JOIN jatim_inventory.transaction_timbangan_sp ttsp ON ttsp.slip = tt.slip WHERE t.transaction_id = {$_POST['transactionId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $photo = $row->pic;
        if($type == 'truck'){
            $photo = $row->pic_truck;
        }
        $image = true;
    }
} else if(isset($_POST['transactionNewId'])) {
    $sql = "SELECT ttsp.pic,ttsp.pic_truck FROM jatim_inventory.transaction t
    RIGHT JOIN jatim_inventory.transaction_timbangan tt ON tt.transaction_id = t.t_timbangan
    RIGHT JOIN jatim_inventory.transaction_timbangan_sp ttsp ON ttsp.slip = tt.slip WHERE tt.transaction_id = {$_POST['transactionNewId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $photo = $row->pic;
        if($type == 'truck'){
            $photo = $row->pic_truck;
        }
        $image = true;
    }
}

// </editor-fold>
?>

<div id="results" class="text-center">
    <?php if($image) { ?>
        <img id="base64image" src="data:image/jpeg;base64,<?php echo base64_encode($photo)?>"/>
        <?php }else{ ?>
        <h1>Photo Tidak ada!</h1>
    <?php } ?>
</div>




