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

<input type="hidden" id="action" name="action" value="payment_bank_data">

<div class="row-fluid">   
    <div class="span5 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", "", "", "currencyId", "currency_id", "currency_code", 
                "", 1, "span12");
        ?>
    </div>
    <div class="span5 lightblue">
        <label>Bank Code <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="2" id="bankCode" name="bankCode">
    </div>
    <div class="span2 lightblue"></div>
</div>
<div class="row-fluid">
    <div class="span5 lightblue">
        <label>Bank Name <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="3" id="bankName" name="bankName">
    </div>
    <div class="span5 lightblue">
        <label>Bank Account No. <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="4" id="bankAccountNo" name="bankAccountNo">
    </div>
    <div class="span2 lightblue"></div>
</div>

<div class="row-fluid">   
    <div class="span5 lightblue">
        <label>Bank Account Name <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="5" id="bankAccountName" name="bankAccountName">
    </div>
    <div class="span5 lightblue">
        <label>Opening Balance <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="6" id="openingBalance" name="openingBalance">
    </div>
    <div class="span2 lightblue"></div>
</div>

<div class="row-fluid">   
    <div class="span5 lightblue">
        <label>COA <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT account_id, CONCAT(account_no, ' - ', account_name) AS account_full "
                    . "FROM account WHERE account_type = 7 ORDER BY account_name", '', '', "accountId", "account_id", "account_full", 
                "", 7);
        ?>
    </div>
    <div class="span5 lightblue">
        <label>Bank type <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT '1' as id, 'Bank' as info UNION
                SELECT '2' as id, 'Petty Cash' as info;", '', '', "bankType", "id", "info", 
            "", 8);
        ?>
    </div>
</div>

