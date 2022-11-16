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

// <editor-fold defaultstate="collapsed" desc="Variable for Vendor Data">

$vendorGroupId = '';
$groupName = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['vendorGroupId']) && $_POST['vendorGroupId'] != '') {
    
    $vendorGroupId = $_POST['vendorGroupId'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    
    $sql = "SELECT v.*
    FROM vendor_group v
    WHERE v.vendor_group_id = {$vendorGroupId}
    ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vendorGroupId = $rowData->vendor_group_id;
        $groupName = $rowData->group_name;
    }
    
    // </editor-fold>
    
}

// <editor-fold defaultstate="collapsed" desc="Functions">


// </editor-fold>

?>

<script type="text/javascript">
$(document).ajaxStop($.unblockUI);

$(document).ready(function(){
    $("#vendorDataForm").validate({
        rules: {
            groupName: "required",
        },
        messages: {
            groupName: "Group Name is a required field.",
        },
        submitHandler: function(form) {
            
            $.ajax({
                url: './irvan.php',
                method: 'POST',
                data: $("#vendorDataForm").serialize(),
                success: function(data) {
                    var returnVal = data.split('|');
                    
                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);
                        
                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/vendor-group.php', iAmACallbackFunction2);
                        } 
                    }
                }
            });
        }
    });
});
</script>

<form method="post" id="vendorDataForm">
<input type="hidden" name="action" id="action" value="vendor_group_data" />
<input type="hidden" name="vendorGroupId" id="vendorGroupId" value="<?php echo $vendorGroupId; ?>" />

<div class="row-fluid">  
<div class="span4 lightblue">
<label>Group Name <span style="color: red;">*</span></label>
<input type="text" class="span12" tabindex="13" id="groupName" name="groupName" value="<?php echo $groupName; ?>">
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
