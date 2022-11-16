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

// <editor-fold defaultstate="collapsed" desc="Variable for Account Data">

$accountId = '';
$accountType = '';
$accountNo = '';
$accountName = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['accountId']) && $_POST['accountId'] != '') {
    
    $accountId = $_POST['accountId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Account Data">
    
    $sql = "SELECT a.*
            FROM account a
            WHERE a.account_id = {$accountId}
            ORDER BY a.account_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $accountType = $rowData->account_type;
        $accountNo = $rowData->account_no;
        $accountName = $rowData->account_name;
		$description = $rowData->description;
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
        
        $("#accountDataForm").validate({
            rules: {
                accountType: "required",
                accountNo: "required",
                accountName: "required"
            },
            messages: {
                accountType: "Account type is a required field.",
                accountNo: "Account no is a required field.",
                accountName: "Account name is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#accountDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalAccountId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/account.php', { accountId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="accountDataForm">
    <input type="hidden" name="action" id="action" value="account_data" />
    <input type="hidden" name="accountId" id="accountId" value="<?php echo $accountId; ?>" />
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Account Type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, '' as info UNION
                    SELECT '1' as id, 'PKS/Sales' as info UNION
                    SELECT '2' as id, 'Freight Cost' as info UNION
                    SELECT '3' as id, 'Unloading Cost' as info UNION
                    SELECT '4' as id, 'Loading' as info UNION
                    SELECT '5' as id, 'Umum' as info UNION
                    SELECT '6' as id, 'HO' as info UNION
					SELECT '9' as id, 'Handling Cost' as info UNION
                    SELECT '7' as id, 'Bank' as info;", $accountType, '', "accountType", "id", "info", 
                "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Account No <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="accountNo" name="accountNo" value="<?php echo $accountNo; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Account Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="accountName" name="accountName" value="<?php echo $accountName; ?>">
        </div>
        <div class="span4 lightblue">
            
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Description <span style="color: red;"></span></label>
            <textarea class="span12" rows="3" tabindex="4" id="description" name="description"><?php echo $description; ?></textarea>
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
