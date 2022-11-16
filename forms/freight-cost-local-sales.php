<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$freightCostId = '';
$freightId = $_POST['freightId'];
$vendorId = '';
$stockpileId = '';
$currencyId = '';
$price = '';
$paymentNotes = '';
$remarks = '';
$modifyBy = '';
$modifyDate = '';
$exchangeRate = '';
$contractPKHOA = '';
$active_from = '';


// </editor-fold>

if(isset($_POST['freightCostId']) && $_POST['freightCostId'] != '') {
    $freightCostId = $_POST['freightCostId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

    $sql = "SELECT fc.*, DATE_FORMAT(fc.modify_date, '%d %b %Y %H:%i:%s') AS modify_date2, u.user_name
            FROM freight_cost_local_sales fc
            LEFT JOIN user u
                ON u.user_id = fc.modify_by
            WHERE fc.freight_cost_id = {$freightCostId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $stockpileId = $rowData->stockpile_id;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $vendorId = $rowData->vendor_id;
        $price = $rowData->price;
        $paymentNotes = $rowData->payment_notes;
        $contractPKHOA = $rowData->contract_pkhoa;
        $remarks = $rowData->remarks;
        $modifyBy = $rowData->user_name;
        $modifyDate = $rowData->modify_date2;
		//$shrink_tolerance_kg = $rowData->shrink_tolerance_kg;
		//$shrink_tolerance_persen = $rowData->shrink_tolerance_persen;
        //$shrink_claim = $rowData->shrink_claim;
        $active_from = $rowData->active_from;

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
            $('#exchangeRateFreight').hide();
        } else {
            $('#exchangeRateFreight').show();
        }

        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateFreight').hide();
            } else {
                $('#exchangeRateFreight').show();
            }
        });

    });
</script>

<input type="hidden" id="freightCostId" name="freightCostId" value="<?php echo $freightCostId; ?>">
<input type="hidden" id="freightId" name="freightId" value="<?php echo $freightId; ?>">

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Stockpile <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT f.stockpile_id, f.stockpile_name
                FROM stockpile f ORDER BY f.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name",
                "", 1, "select2combobox100");
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Customer <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT v.customer_id, v.customer_name
                    FROM customer v ORDER BY v.customer_name ASC", $vendorId, '', "vendorId", "customer_id", "customer_name",
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
        <label>Active From <span style="color: red;">*</span></label>
        <input type="date" class="span6" tabindex="6" id="active_from" name="active_from" value="<?php echo $active_from; ?>">
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
        <label>No Kontrak</label>
        <input type="text" class="span10" tabindex="6" id="contractPKHOA" name="contractPKHOA" value="<?php echo $contractPKHOA; ?>">
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
