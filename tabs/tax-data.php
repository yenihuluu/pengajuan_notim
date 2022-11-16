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

// <editor-fold defaultstate="collapsed" desc="Variable for Module Data">

$taxId = '';
$accountId = '';
$taxType = '';
$taxCategory = '';
$taxName = '';
$taxValue = '';
$active = '';


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['taxId']) && $_POST['taxId'] != '') {
    
    $taxId = $_POST['taxId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Module Data">
    
    $sql = "SELECT m.*
            FROM tax m
            WHERE m.tax_id = {$taxId}
            ORDER BY m.tax_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$accountId = $rowData->account_id;
        $taxType = $rowData->tax_type;
		$taxCategory = $rowData->tax_category;
		$taxName = $rowData->tax_name;
		$taxValue = $rowData->tax_value;
		$active = $rowData->active;
		
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
           
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
	});
    $(document).ready(function(){
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#taxDataForm").validate({
            rules: {
                taxType: "required",
				taxCategory: "required",
				accountId: "required",
				taxName: "required",
				taxValue: "required",
                active: "required"
            },
            messages: {
                taxType: "Type is a required field",
				taxCategory: "Category is a required field",
				accountId: "Account is a required field",
				taxName: "Tax Name is a required field",
				taxValue: "Tax Value is a required field",
                active: "Status is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#taxDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalTaxId').value = returnVal[3];
                                
                                $('#dataContent').load('views/tax.php', { taxId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="taxDataForm">
    <input type="hidden" name="action" id="action" value="tax_data" />
    <input type="hidden" name="taxId" id="taxId" value="<?php echo $taxId; ?>" />
      <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'PPN' as info UNION
                    SELECT '2' as id, 'PPh' as info;", $taxType, '', "taxType", "id", "info", 
                "", 6);
            ?>
        </div>
       <div class="span4 lightblue">
            <label>Category <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Normal' as info UNION
                    SELECT '1' as id, 'Gross Up' as info;", $taxCategory, '', "taxCategory", "id", "info", 
                "", 6);
            ?>
        </div>
        
 </div>
 <div class="row-fluid">
      <div class="span4 lightblue">
            <label>Account <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT account_id, CONCAT(account_no, ' - ', account_name, ' - ',
			(CASE WHEN account_type = 0 THEN 'PKS/Sales'
            WHEN account_type = 1 THEN 'PKS/Sales' 
            WHEN account_type = 2 THEN 'Freight Cost'
            WHEN account_type = 3 THEN 'Unloading Cost'
            WHEN account_type = 4 THEN 'Loading'
            WHEN account_type = 5 THEN 'Umum'
            WHEN account_type = 6 THEN 'HO'
            WHEN account_type = 7 THEN 'Bank'
             ELSE 'Non Inventory' END)) AS account_full
                    FROM account WHERE account_type IN (0,1,2,3,4,5,6) ORDER BY account_name", $accountId, '', "accountId", "account_id", "account_full", 
                "", 14, "select2combobox100");
            ?>
        </div>
         <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
 </div>    
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Tax Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="taxName" name="taxName" value="<?php echo $taxName; ?>" maxlength="50">        </div>
      	<div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
        </div>
        
    <div class="row-fluid">  
        <div class="span4 lightblue">
             <label>Tax Value <span style="color: red;">*</span></label>
            <input type="text" class="span4" tabindex="1" id="taxValue" name="taxValue" value="<?php echo number_format($rowData->tax_value, 2, ".", ","); ?>" maxlength="4" />
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
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
