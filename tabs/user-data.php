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

// <editor-fold defaultstate="collapsed" desc="Variable for User Data">

$userId = '';
$userName = '';
$userPassword = '';
$userEmail = '';
$userPhone = '';
$active = '';
$stockpileId = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['userId']) && $_POST['userId'] != '') {
    
    $userId = $_POST['userId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for User Data">
    
    $sql = "SELECT u.*
            FROM user u
            WHERE u.user_id = {$userId}
            ORDER BY u.user_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $userName = $rowData->user_name;
        $userEmail = $rowData->user_email;
        $userPhone = $rowData->user_phone;
        $active = $rowData->active;
		$stockpileId = $rowData->stockpile_id;
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
        
        $("#userDataForm").validate({
            rules: {
                userName: "required",
                userPassword: "required",
                confirmPassword: "required",
                userEmail: {
                    required: true,
                    email: true
                },
                active: "required",
				stockpileId: "required"
            },
            messages: {
                userName: "Name is a required field.",
                userPassword: "Password is a required field.",
                confirmPassword: "Confirm Password is a required field.",
                userEmail: {
                 required: "Email is a required field.",
                    email: "Invalid email."
                },
                active: "Status is a required field.",
				stockpileId: "Stockpile is a required field."
            },
            submitHandler: function(form) {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#userDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalUserId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/user.php', { userId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="userDataForm">
    <input type="hidden" name="action" id="action" value="user_data" />
    <input type="hidden" name="userId" id="userId" value="<?php echo $userId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Email <span style="color: red;">*</span></label>
            <input type="text" class="span12" <?php echo $readonlyProperty; ?> tabindex="1" id="userEmail" name="userEmail" value="<?php echo $userEmail; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="4" id="userName" name="userName" value="<?php echo $userName; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Password <span style="color: red;">*</span></label>
            <input type="password" class="span12" tabindex="2" id="userPassword" name="userPassword">
        </div>
        <div class="span4 lightblue">
            <label>Phone</label>
            <input type="text" class="span12" tabindex="5" id="userPhone" name="userPhone" value="<?php echo $userPhone; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Confirm Password <span style="color: red;">*</span></label>
            <input type="password" class="span12" tabindex="3" id="confirmPassword" name="confirmPassword">
        </div>
        <div class="span4 lightblue">
        <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
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
