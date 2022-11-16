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
$shrinkToleranceKG = '';
$shrinkTolerancePersen = '';
$shrinkClaim = '';
$shrinkToleranceFreightId = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    
    $stockpileId = $_POST['stockpileId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Stockpile Data">
    
    $sql = "SELECT s.*
            FROM shrink_tolerance_freight s
            WHERE s.stockpile_id = {$stockpileId}
            ORDER BY s.stockpile_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $shrinkToleranceKG = $rowData->shrink_tolerance_kg;
        $shrinkTolerancePersen = $rowData->shrink_tolerance_persen;
        $shrinkClaim = $rowData->shrink_claim;
        $shrinkToleranceFreightId = $rowData->shrink_tolerance_freight_id;
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
        
        $("#shrinkToleranceDataForm").validate({
            rules: {
                shrinkToleranceKG: "required",
                shrinkTolerancePersen: "required",
                shrinkClaim: "required",
            },
            messages: {
                shrinkToleranceKG: "Shrink Tolerance(KG) is a required field.",
                shrinkTolerancePersen: "Shrink Tolerance(%) is a required field.",
                shrinkClaim: "Shrink Claim is a required field.",
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './irvan.php',
                    method: 'POST',
                    data: $("#shrinkToleranceDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                $('#stockpile-shrink-tolerance-freight').load('tabs/stockpile-shrink-tolerance-freight.php', {stockpileId: document.getElementById('stockpileId').value});
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

<form method="post" id="shrinkToleranceDataForm">
    <input type="hidden" name="action" id="action" value="shrink_tolerance_freight_data" />
    <input type="hidden" name="stockpileId" id="stockpileId" value="<?php echo $stockpileId; ?>" />
    <input type="hidden" name="shrinkToleranceFreightId" id="shrinkToleranceFreightId" value="<?php echo $shrinkToleranceFreightId; ?>" />
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Shrink Tolerance(KG)<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="shrinkToleranceKG" name="shrinkToleranceKG" value="<?php echo $shrinkToleranceKG; ?>" maxlength="3">
        </div>
        <div class="span3 lightblue">
            <label>Shrink Tolerance(%)<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="shrinkTolerancePersen" name="shrinkTolerancePersen" value="<?php echo $shrinkTolerancePersen; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Shrink Claim <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="shrinkClaim" name="shrinkClaim" value="<?php echo $shrinkClaim; ?>">
        </div>
    </div>
    <div class="row-fluid">  
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
