<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$stockpileId = $_POST['stockpileId'];
$vendorId = $_POST['vendorId'];
$allowVendor = true;
$contractType = 'C';

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
        $('#contractType').change(function() {
            if(document.getElementById('contractType').value == 'P') {
                $('#quantityContract').show();
            } else {
                $('#quantityContract').hide();
            }
        });
        
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateContract').hide();
            } else {
                $('#exchangeRateContract').show();
            }
        });
        
        $('#vendorId').change(function() {
            if(document.getElementById('vendorId').value == 'INSERT') {
                $('#vendorDetail1').show();
                $('#vendorDetail2').show();
            } else {
                $('#vendorDetail1').hide();
                $('#vendorDetail2').hide();
            }
        });
    });
</script>

<input type="hidden" id="action" name="action" value="transaction_contract_data">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">
<input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Contract Type <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT 'P' as id, 'PKS' as info UNION
                SELECT 'C' as id, 'Curah' as info;", $contractType, "", "contractType", "id", "info", 
            "", 1, "span6");
        ?>
    </div>
</div>

<div class="row-fluid">   
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", "", "", "currencyId", "currency_id", "currency_code", 
                "", 2, "span12");
        ?>
    </div>
    <div class="span8 lightblue" id="exchangeRateContract" style="display: none;">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span8" tabindex="3" id="exchangeRate" name="exchangeRate">
    </div>
</div>

<div class="row-fluid">   
    <div class="span6 lightblue">
        <label>Price/KG <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="4" id="price" name="price">
    </div>
    <div class="span6 lightblue">
    </div>
</div>

<div class="row-fluid">   
    <div class="span6 lightblue">
        <label>Contract No.</label>
        <input type="text" class="span12" tabindex="5" id="contractNo" name="contractNo">
    </div>
    <div class="span6 lightblue" id="quantityContract" style="display: none;">
        <label>Quantity <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="6" id="quantity" name="quantity">
    </div>
</div>

<!--<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Vendor <span style="color: red;">*</span></label>
        <?php
//        createCombo("SELECT v.vendor_id, CONCAT(v.vendor_name, ' (', v.vendor_code, ')') AS vendor_full
//                    FROM vendor v ORDER BY v.vendor_name", "", "", "vendorId", "vendor_id", "vendor_full", 
//            "", 6, "span12", 1, "", $allowVendor);
        ?>
    </div>
</div>-->

<div class="row-fluid" id="vendorDetail1" style="display: none;">
    <div class="span4 lightblue">
        <label>Vendor Code <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="10" id="vendorCode" name="vendorCode">
    </div>
    <div class="span8 lightblue">
        <label>Vendor Name <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="11" id="vendorName" name="vendorName">
    </div>
</div>
<div class="row-fluid" id="vendorDetail2" style="display: none;">
    <div class="span12 lightblue">
        <label>Vendor Address</label>
        <textarea class="span12" rows="3" tabindex="12" id="vendorAddress" name="vendorAddress"></textarea>
    </div>
</div>


