<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Unloading Cost Data">

$ls_id = '';
$laborId = '';
$stockpileId = $_POST['stockpileId'];
$modifyBy = '';
$modifyDate = '';
$status = '';

// </editor-fold>

if(isset($_POST['ls_id']) && $_POST['ls_id'] != '') {
    $ls_id = $_POST['ls_id'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Unloading Cost Data">
    
    $sql = "SELECT * FROM labor_stockpile WHERE ls_id = {$ls_id}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $laborId = $rowData->labor_id;
        $modifyBy = $rowData->update_by;
        $modifyDate = $rowData->update_date;
		$status = $rowData->status;
    }
    
    // </editor-fold>
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select if Applicable --</option>";
    }
    
    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";
        
        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }
    
    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>



<input type="hidden" id="ls_id" name="ls_id" value="<?php echo $ls_id; ?>">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Labor Name <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT labor_id, labor_name
				FROM labor
                ORDER BY labor_name ASC", $laborId, "", "laborId", "labor_id", "labor_name", 
                "", 1, "span6");
        ?>
    </div>
</div>


<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'ACTIVE' as info UNION
                    SELECT '1' as id, 'INACTIVE' as info;", $status, '', "status", "id", "info", 
                "", 6);
            ?>
    </div>
</div>

<?php
if($modifyBy != '') {
    ?>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Modified on <?php echo $modifyDate; ?> by <?php echo $modifyBy; ?></label>
    </div>
</div>
    <?php
}

