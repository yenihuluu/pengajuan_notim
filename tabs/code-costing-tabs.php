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
$_method = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Stockpile Data">

$codeCosting_id = '';
$stockpileCode = '';
$stockpileName = '';
$active = '';

// </editor-fold>

// If ID is in the parameter

if(isset($_POST['codeCosting_id']) && $_POST['codeCosting_id'] != '') {
    
    $codeCosting_id = $_POST['codeCosting_id'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Stockpile Data">
    
  $sql = "SELECT *
            FROM header_costing 
            WHERE header_costing_id = {$codeCosting_id}
            ORDER BY header_costing_id ASC ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $stockpileCode = $rowData->code_costing;
        $stockpileId = $rowData->loading_port;
    } 
    
    // </editor-fold>
    $_method = 'UPDATE';
    $readonly1 = 'readonly';
}else{
    $_method = 'INSERT';
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

        
        $("#CostingDataForm").validate({
            rules: {
                stockpileCode: "required",
                stockpileId: "required"
            },
            messages: {
                stockpileCode: "Code is a required field.",
                stockpileId: "Stockpile is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    _method: 'INSERT',
                    data: $("#CostingDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalCodeCostingId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/code-costing-form.php', { codeCosting_id: returnVal[3] }, iAmACallbackFunction2);

//                       
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

<form method="post" id="CostingDataForm">
    <input type="hidden" name="action" id="action" value="master_costing_data" />
    <input type="hidden" name="_method" id="_method" value=<?php echo $_method ?>>
    <input type="hidden" name="codeCosting_id" id="codeCosting_id"  value="<?php echo $codeCosting_id; ?>" />
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Code <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="stockpileCode" name="stockpileCode" value="<?php echo $stockpileCode; ?>" <?php echo $readonly1 ?>>
        </div>

        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
    <div class="span3 lightblue">
		    <label>Stockpile (Loading Port) <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                        FROM user_stockpile us
                        INNER JOIN stockpile s
                            ON s.stockpile_id = us.stockpile_id
                        WHERE us.user_id = {$_SESSION['userId']}
                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC",$stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full",
                "", 9, "");
            ?>
        </div>
    
        <div class="span4 lightblue">

        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
