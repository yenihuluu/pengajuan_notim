<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';


// <editor-fold defaultstate="collapsed" desc="Variable for Logbook Data">

$terminId = $_POST['terminId'];
$terminDetailId = '';
$terminName = '';
$pecentage = '';


// </editor-fold>
if (isset($_POST['terminId']) && $_POST['terminId'] != '') {

    $sql = "SELECT mt.* FROM master_termin mt  WHERE mt.id = {$terminId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $terminName = $rowData->name;
    }
}

// If ID is in the parameter
if (isset($_POST['terminDetailId']) && $_POST['terminDetailId'] != '') {
    $actionType = 'UPDATE';
    $terminDetailId = $_POST['terminDetailId'];

    $sql = "SELECT td.*, mt.name FROM termin_detail td LEFT JOIN master_termin as mt ON mt.id = td.termin_id WHERE td.id = {$terminDetailId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();

        $terminDetailId = $rowData->id;
        $terminName = $rowData->name;
        $percentage = $rowData->percentage;
    }
} else {
    $actionType = 'INSERT';
}
?>

<input type="hidden" name="action" id="action" value="termin_detail"/>
<input type="hidden" name="actionType" id="actionType" value="<?php echo $actionType ?>"/>
<input type="hidden" name="terminId" id="terminId" value="<?php echo $_POST['terminId']; ?>"/>
<input type="hidden" name="terminDetailId" id="terminDetailId" value="<?php echo $terminDetailId ?>"/>


<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Termin Name</label>
        <input type="text" placeholder="Input Termin Name" tabindex="" id="terminName" name="terminName"
               class="span12"
               value="<?php echo $terminName; ?>" disabled>
    </div>
     <div class="span6 lightblue">
        <label>Percentage</label>
        <input type="number" placeholder="Input Percentage" tabindex="" id="percentage" name="percentage"
               class="span12"
               value="<?php echo $percentage; ?>">
    </div>
</div>
