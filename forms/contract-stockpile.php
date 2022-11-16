<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$disabledProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Stockpile Contract Data">

$stockpileContractId = '';
$stockpileId = '';
$contractId = $_POST['contractId'];
$quantity = '';

// </editor-fold>

if(isset($_POST['stockpileContractId']) && $_POST['stockpileContractId'] != '') {
    $stockpileContractId = $_POST['stockpileContractId'];
    
    $disabledProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Stockpile Contract Data">
    
    $sql = "SELECT sc.*
            FROM stockpile_contract sc
            WHERE sc.stockpile_contract_id = {$stockpileContractId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $stockpileId = $rowData->stockpile_id;
        $quantity = $rowData->quantity;
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

<input type="hidden" id="stockpileContractId" name="stockpileContractId" value="<?php echo $stockpileContractId; ?>">
<input type="hidden" id="contractId" name="contractId" value="<?php echo $contractId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Stockpile <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                FROM stockpile s
                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                "", 1, "span10");
        ?>
    </div>
</div>

<div class="row-fluid">   
    <div class="span4 lightblue">
        <label>Quantity (KG)<span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="3" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
    </div>
    <div class="span8 lightblue">
        
    </div>
</div>

