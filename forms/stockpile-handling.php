<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$handlingCostId = '';
$vendorHandlingId = '';
$vendorId = '';
$stockpileId = $_POST['stockpileId'];
$currencyId = '';
$price = '';
$paymentNotes = '';
$remarks = '';
$modifyBy = '';
$modifyDate = '';
$exchangeRate = '';

// </editor-fold>

if(isset($_POST['handlingCostId']) && $_POST['handlingCostId'] != '') {
    $handlingCostId = $_POST['handlingCostId'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">
    
    $sql = "SELECT vhc.*, DATE_FORMAT(vhc.modify_date, '%d %b %Y %H:%i:%s') AS modify_date2, u.user_name
            FROM vendor_handling_cost vhc
            LEFT JOIN user u
                ON u.user_id = vhc.modify_by
            WHERE vhc.handling_cost_id = {$handlingCostId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vendorHandlingId = $rowData->vendor_handling_id;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $vendorId = $rowData->vendor_id;
        $price = $rowData->price;
        $paymentNotes = $rowData->payment_notes;
        $remarks = $rowData->remarks;
        $modifyBy = $rowData->user_name;
        $modifyDate = $rowData->modify_date2;
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

<script type="text/javascript">
    $(document).ready(function(){
		$("select.select2combobox100").select2({
            width: "100%"
        });
        
        $("select.select2combobox50").select2({
            width: "50%"
        });
        
        $("select.select2combobox75").select2({
            width: "75%"
        });
		
        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRateHandling').hide();
        } else {
            $('#exchangeRateHandling').show();
        }
            
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateHandling').hide();
            } else {
                $('#exchangeRateHandling').show();
            }
        });
		
    });
</script>

<input type="hidden" id="handlingCostId" name="handlingCostId" value="<?php echo $handlingCostId; ?>">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Vendor Handling <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT vh.vendor_handling_id, CONCAT(vh.vendor_handling_code, ' - ', vh.vendor_handling_name) AS vendor_handling_full
                FROM vendor_handling vh WHERE vh.active = 1
                ORDER BY vh.vendor_handling_code ASC, vh.vendor_handling_name ASC", $vendorHandlingId, "", "vendorHandlingId", "vendor_handling_id", "vendor_handling_full", 
                "", 1, "select2combobox100");
        ?>
    </div>
</div>

<div class="row-fluid">  
    <div class="span12 lightblue">
        <label>Vendor <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT v.vendor_id, CONCAT(v.vendor_name, ' (', v.vendor_code, ')') AS vendor_full
                    FROM vendor v WHERE v.active = 1 ORDER BY v.vendor_name", $vendorId, '', "vendorId", "vendor_id", "vendor_full", 
            "", 2, "select2combobox100");
        ?>
    </div>
</div>

<div class="row-fluid">   
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                "", 3, "span12");
        ?>
    </div>
    <div class="span8 lightblue" id="exchangeRateFreight" style="display: none;">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="4" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Price/KG <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="5" id="price" name="price" value="<?php echo $price; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Payment Notes</label>
        <input type="text" class="span10" tabindex="6" id="paymentNotes" name="paymentNotes" value="<?php echo $paymentNotes; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Remarks</label>
        <textarea class="span10" rows="3" tabindex="7" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
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

