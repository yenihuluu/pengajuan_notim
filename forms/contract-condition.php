<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Condition Data">

$conditionId = '';
$contractId = $_POST['contractId'];
$categoryId = '';
$rule = '';
$remarks = '';

// </editor-fold>

if(isset($_POST['conditionId']) && $_POST['conditionId'] != '') {
    $conditionId = $_POST['conditionId'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Condition Data">
    
    $sql = "SELECT cond.*
            FROM `condition` cond
            WHERE cond.condition_id = {$conditionId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $categoryId = $rowData->category_id;
        $rule = $rowData->rule;
        $remarks = $rowData->remarks;
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

<input type="hidden" id="conditionId" name="conditionId" value="<?php echo $conditionId; ?>">
<input type="hidden" id="contractId" name="contractId" value="<?php echo $contractId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Category <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cat.category_id, cat.category_name
                FROM category cat
                ORDER BY cat.category_name ASC", $categoryId, "", "categoryId", "category_id", "category_name", 
                "", 1, "span6");
        ?>
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Rule <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="2" id="rule" name="rule" value="<?php echo $rule; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Remarks</label>
        <textarea class="span9" tabindex="3" rows="3" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
    </div>
</div>


