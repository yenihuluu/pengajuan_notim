<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Stockpile Data">

$stockpileId = '';
$stockpileCode = '';
$stockpileName = '';
$stockpileAddress = '';
$active = '';
$freightWeightRule = '';
$curahWeightRule = '';
$vendorPortal = '';
$email = '';    


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    
    $stockpileId = $_POST['stockpileId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Stockpile Data">
    
    $sql = "SELECT s.*
            FROM stockpile s
            WHERE s.stockpile_id = {$stockpileId}
            ORDER BY s.stockpile_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $stockpileCode = $rowData->stockpile_code;
        $stockpileName = $rowData->stockpile_name;
        $stockpileAddress = $rowData->stockpile_address;
        $active = $rowData->active;
        $freightWeightRule = $rowData->freight_weight_rule;
        $curahWeightRule = $rowData->curah_weight_rule;
        $vendorPortal = $rowData->vendor_portal;
        $email = $rowData->stockpile_email;
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
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#stockpileDataForm").validate({
            rules: {
                stockpileCode: "required",
                stockpileName: "required",
                active: "required",
                freightWeightRule: "required",
                curahWeightRule: "required",
                email: "required"
            },
            messages: {
                stockpileCode: "Code is a required field.",
                stockpileName: "Name is a required field.",
                active: "Status is a required field.",
                freightWeightRule: "Freight Weight Rule is a required field.",
                curahWeightRule: "Curah Weight Rule is a required field.",
                email :"Email is required"
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#stockpileDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalStockpileId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/stockpile.php', { stockpileId: returnVal[3] }, iAmACallbackFunction2);

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
            startView: 2
        });
    });
</script>

<form method="post" id="stockpileDataForm">
    <input type="hidden" name="action" id="action" value="stockpile_data" />
    <input type="hidden" name="stockpileId" id="stockpileId" value="<?php echo $stockpileId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Code <span style="color: red;">*</span></label>
            <input type="text" class="span6" tabindex="1" id="stockpileCode" name="stockpileCode" value="<?php echo $stockpileCode; ?>" maxlength="3">
        </div>
        <div class="span4 lightblue">
            <label>Freight Weight Rule <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Lowest' as info UNION
                    SELECT '1' as id, 'Send Weight' as info UNION
                    SELECT '2' as id, 'Netto Weight' as info;", $freightWeightRule, '', "freightWeightRule", "id", "info", 
                "", 7);
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Email <span style="color: red;">*</span><small> Kepala Cabang</small></label>
            <input type="email" class="span12" tabindex="1" id="email" name="email" value="<?php echo $email; ?>">
            </div>

        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="stockpileName" name="stockpileName" value="<?php echo $stockpileName; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Curah Weight Rule <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Lowest' as info UNION
                    SELECT '1' as id, 'Send Weight' as info UNION
                    SELECT '2' as id, 'Netto Weight' as info;", $curahWeightRule, '', "curahWeightRule", "id", "info", 
                "", 8);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Address</label>
            <textarea class="span12" rows="3" tabindex="3" id="stockpileAddress" name="stockpileAddress"><?php echo $stockpileAddress; ?></textarea>
        </div>
        <div class="span4 lightblue">
            <label>Vendor Portal<span style="color: red;">*</span></label>
           <?php
                createCombo("SELECT '0' as id, 'No' as info UNION
                        SELECT '1' as id, 'Yes' as info", $vendorPortal, "", "vendorPortal", "id", "info",
                    "", 11, "select2combobox50");
                ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Active' as info UNION
                    SELECT '0' as id, 'Inactive' as info;", $active, '', "active", "id", "info", 
                "", 6);
            ?>
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
