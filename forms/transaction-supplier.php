<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ready(function(){
        
    });
</script>

<input type="hidden" id="action" name="action" value="transaction_supplier_data">

<div class="row-fluid">
    <div class="span4 lightblue">
        <label>Vendor Code <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="10" id="vendorCode" name="vendorCode">
    </div>
    <div class="span8 lightblue">
        <label>Vendor Name <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="11" id="vendorName" name="vendorName">
    </div>
</div>
<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Vendor Address</label>
        <textarea class="span12" rows="3" tabindex="12" id="vendorAddress" name="vendorAddress"></textarea>
    </div>
</div>


