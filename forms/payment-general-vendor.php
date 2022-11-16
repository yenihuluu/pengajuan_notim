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
    } else if($empty == 3) {
        echo "<option value='0'>NONE</option>";
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

<input type="hidden" id="action" name="action" value="payment_general_vendor_data">

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Vendor Name <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="1" id="vendorName" name="vendorName">
    </div>
    <div class="span6 lightblue">
        <label>Tax ID <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="2" id="npwp" name="npwp">
    </div>
</div>
<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Vendor Address <span style="color: red;">*</span></label>
        <textarea class="span12" rows="3" tabindex="3" id="vendorAddress" name="vendorAddress"></textarea>
    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>PPN <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT * FROM tax WHERE tax_type = 1", "", "", "ppn", "tax_id", "tax_name", 
                "", 4, "span12", 3);
        ?>
    </div>
    <div class="span6 lightblue">
        <label>PPh <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT * FROM tax WHERE tax_type = 2", "", "", "pph", "tax_id", "tax_name", 
                "", 5, "span12", 3);
        ?>
    </div>
</div>


