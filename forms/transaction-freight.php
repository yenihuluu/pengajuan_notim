<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$stockpileId = $_POST['stockpileId'];
$vendorId = $_POST['vendorId'];
$allowFreight = true;

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
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateFreight').hide();
            } else {
                $('#exchangeRateFreight').show();
            }
        });
        
        $('#freightId').change(function() {
            if(document.getElementById('freightId').value == 'INSERT') {
                $('#freightDetail1').show();
                $('#freightDetail2').show();
                $('#freightDetail3').show();
            } else {
                $('#freightDetail1').hide();
                $('#freightDetail2').hide();
                $('#freightDetail3').hide();
            }
        });
    });
</script>

<input type="hidden" id="action" name="action" value="transaction_freight_data">
<input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Freight <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT f.freight_id, CONCAT(f.freight_code, ' - ', f.freight_supplier) AS freight_full
                FROM freight f
                ORDER BY f.freight_code ASC, f.freight_supplier ASC", "", "", "freightId", "freight_id", "freight_full", 
                "", 1, "span10", 1, "", $allowFreight);
        ?>
    </div>
</div>

<div class="row-fluid" id="freightDetail1" style="display: none;">
    <div class="span4 lightblue">
        <label>Freight Code <span style="color: red;">*</span></label>
        <input type="text" class="span10" tabindex="2" id="freightCode" name="freightCode">
    </div>
    <div class="span8 lightblue">
        <label>Freight Supplier <span style="color: red;">*</span></label>
        <input type="text" class="span10" tabindex="3" id="freightSupplier" name="freightSupplier">
    </div>
</div>

<div class="row-fluid" id="freightDetail2" style="display: none;">
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

<div class="row-fluid" id="freightDetail3" style="display: none;">
    <div class="span6 lightblue">
        <label>Tax ID <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="6" id="npwp" name="npwp">
    </div>
    <div class="span6 lightblue">
        <label>Freight Address <span style="color: red;">*</span></label>
        <textarea class="span12" rows="3" tabindex="7" id="freightAddress" name="freightAddress"></textarea>
    </div>
</div>

<div class="row-fluid">   
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", "", "", "currencyId", "currency_id", "currency_code", 
                "", 4, "span12");
        ?>
    </div>
    <div class="span8 lightblue" id="exchangeRateFreight" style="display: none;">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="5" id="exchangeRate" name="exchangeRate">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Price/KG <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="6" id="price" name="price">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Payment Notes</label>
        <input type="text" class="span10" tabindex="7" id="paymentNotes" name="paymentNotes">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Remarks</label>
        <textarea class="span10" rows="3" tabindex="8" id="remarks" name="remarks"></textarea>
    </div>
</div>


