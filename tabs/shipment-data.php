<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$date = new DateTime();

// <editor-fold defaultstate="collapsed" desc="Variable for Shipment Data">

$shipmentId = '';
$shipmentNo = '';
$shipmentCode = '';
$shipmentDate = $date->format('d/m/Y');
$shipmentType = 1;
$customerId = '';
$destination = '';
$notes = '';
$currencyId = '';
$exchangeRate = '';
$price = '';
$quantity = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != '') {
    
    $shipmentId = $_POST['shipmentId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Shipment Data">
    
    $sql = "SELECT sh.*, DATE_FORMAT(sh.shipment_date, '%d/%b/%Y') AS shipment_date2
            FROM shipment sh
            WHERE sh.shipment_id = {$shipmentId}
            ORDER BY sh.shipment_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $shipmentCode = $rowData->shipment_code;
        $shipmentDate = $rowData->shipment_date2;
        $shipmentType = $rowData->shipment_type;
        $customerId = $rowData->customer_id;
        $destination = $rowData->destination;
        $notes = $rowData->notes;
        $currencyId = $rowData->currency_id;
        $price = $rowData->price;
        $quantity = $rowData->quantity;
        $exchangeRate = $rowData->exchange_rate;
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
        echo "<option value=''>-- Select if Applicable --</option>";
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
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
        
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });
        
        $("#shipmentDataForm").validate({
            rules: {
                shipmentCode: "required",
                shipmentDate: "required",
                shipmentType: "required",
                customerId: "required",
                currencyId: "required",
                exchangeRate: "required",
                price: "required",
                quantity: "required"
            },
            messages: {
                shipmentCode: "Sales Agreement Code is a required field.",
                shipmentDate: "Sales Agreement Date is a required field.",
                shipmentType: "Type is a required field.",
                customerId: "Buyer is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                price: "Price/KG is a required field.",
                quantity: "Quantity (KG) is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#shipmentDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalShipmentId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/shipment.php', { shipmentId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>

<form method="post" id="shipmentDataForm">
    <input type="hidden" name="action" id="action" value="shipment_data" />
    <input type="hidden" name="shipmentId" id="shipmentId" value="<?php echo $shipmentId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Sales Agreement Code <span style="color: red;">*</span></label>
            <input type="text" class="span6" <?php echo $readonlyProperty; ?> tabindex="1" id="shipmentCode" name="shipmentCode" value="<?php echo $shipmentCode; ?>" maxlength="10">
        </div>
        <div class="span4 lightblue">
            <label>Buyer <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cust.customer_id, cust.customer_name 
                    FROM customer cust ORDER BY cust.customer_name ASC", $customerId, '', "customerId", "customer_id", "customer_name", 
                    "", 4);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Sales Agreement Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="shipmentDate" name="shipmentDate" value="<?php echo $shipmentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span2 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", 5, "span12");
            ?>
        </div>
        <div class="span2 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="5" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Commit' as info UNION
                    SELECT '2' as id, 'Direct' as info;", $shipmentType, '', "shipmentType", "id", "info", 
                "", 3);
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="6" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Destination</label>
            <input type="text" class="span12" tabindex="8" id="destination" name="destination" value="<?php echo $destination; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Quantity (KG) <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="8" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span8 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="3" tabindex="9" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
