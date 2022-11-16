<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Unloading Cost Data">

$unloadingCostId = '';
$vehicleId = '';
$stockpileId = $_POST['stockpileId'];
$currencyId = '';
$price = '';
$modifyBy = '';
$modifyDate = '';
$exchangeRate = '';
// </editor-fold>

if(isset($_POST['unloadingCostId']) && $_POST['unloadingCostId'] != '') {
    $unloadingCostId = $_POST['unloadingCostId'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Unloading Cost Data">
    
    $sql = "SELECT uc.*, DATE_FORMAT(uc.modify_date, '%d %b %Y %H:%i:%s') AS modify_date2, u.user_name
            FROM unloading_cost uc
            LEFT JOIN user u
                ON u.user_id = uc.modify_by
            WHERE uc.unloading_cost_id = {$unloadingCostId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vehicleId = $rowData->vehicle_id;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $price = $rowData->price;
        $modifyBy = $rowData->user_name;
        $modifyDate = $rowData->modify_date2;
		$status = $rowData->status;
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
        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRateUnloading').hide();
        } else {
            $('#exchangeRateUnloading').show();
        }
        
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateUnloading').hide();
            } else {
                $('#exchangeRateUnloading').show();
            }
        });
    });
</script>

<input type="hidden" id="unloadingCostId" name="unloadingCostId" value="<?php echo $unloadingCostId; ?>">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Vehicle <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT v.vehicle_id, v.vehicle_name
                FROM vehicle v
				where active = 1
                ORDER BY v.vehicle_name ASC", $vehicleId, "", "vehicleId", "vehicle_id", "vehicle_name", 
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
                ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                "", 2, "span12");
        ?>
    </div>
    <div class="span8 lightblue" id="exchangeRateUnloading" style="display: none;">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="4" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Price/Unloading <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="5" id="price" name="price" value="<?php echo $price; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'ACTIVE' as info UNION
                    SELECT '1' as id, 'INACTIVE' as info;", $status, '', "status", "id", "info", 
                "", 6);
            ?>
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

