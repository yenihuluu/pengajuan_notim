<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$stockpileId = $_POST['stockpileId'];
$allowVehicle = true;

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
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateUnloading').hide();
            } else {
                $('#exchangeRateUnloading').show();
            }
        });
        
        $('#vehicleId').change(function() {
            if(document.getElementById('vehicleId').value == 'INSERT') {
                $('#vehicleDetail1').show();
            } else {
                $('#vehicleDetail1').hide();
            }
        });
    });
</script>

<input type="hidden" id="action" name="action" value="transaction_unloading_data">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Vehicle <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT v.vehicle_id, v.vehicle_name
                FROM vehicle v
                ORDER BY v.vehicle_name ASC", "", "", "vehicleId", "vehicle_id", "vehicle_name", 
                "", 1, "span6", 1, "", $allowVehicle);
        ?>
    </div>
</div>

<div class="row-fluid" id="vehicleDetail1" style="display: none;">
    <div class="span12 lightblue">
        <label>Vehicle Name <span style="color: red;">*</span></label>
        <input type="text" class="span10" tabindex="2" id="vehicleName" name="vehicleName">
    </div>
</div>

<div class="row-fluid">   
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", "", "", "currencyId", "currency_id", "currency_code", 
                "", 3, "span12");
        ?>
    </div>
    <div class="span8 lightblue" id="exchangeRateUnloading" style="display: none;">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="4" id="exchangeRate" name="exchangeRate">
    </div>
</div>

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Price/Unloading <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="5" id="price" name="price">
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

