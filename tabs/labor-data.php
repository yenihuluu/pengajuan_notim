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

// <editor-fold defaultstate="collapsed" desc="Variable for Labor Data">

$laborId = '';
$laborCode = '';
$laborName = '';
$laborAddress = '';
$npwp = '';
$npwp_name = '';
$bankName = '';
$branch = '';
$accountNo = '';
$beneficiary = '';
$swiftCode = '';
$taxable = '';
$ppn = '';
$pph = '';
$active = '';
$nik = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['laborId']) && $_POST['laborId'] != '') {
    
    $laborId = $_POST['laborId'];
    
    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		//echo $row->user_id;
        if($row->user_id != 46 && $row->user_id != 22) {
            $readonlyProperty = 'readonly';
        }else{
			$readonlyProperty = '';
		}
    }
}
    
    // <editor-fold defaultstate="collapsed" desc="Query for Labor Data">
    
    $sql = "SELECT l.*
            FROM labor l
            WHERE l.labor_id = {$laborId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$laborCode = $rowData->labor_code;
        $laborName = $rowData->labor_name;
        $laborAddress = $rowData->labor_address;
        $npwp = $rowData->npwp;
		$npwp_name = $rowData->npwp_name;
        $bankName = $rowData->bank_name;
        $branch = $rowData->branch;
		$accountNo = $rowData->account_no;
        $beneficiary = $rowData->beneficiary;
        $swiftCode = $rowData->swift_code;
        $taxable = $rowData->taxable;
        $ppn = $rowData->ppn_tax_id;
        $pph = $rowData->pph_tax_id;
		$active = $rowData->active;
		$nik = $rowData->nik;
		$laborRules = $rowData->laborRules;
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
        if($setvalue == 0) {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
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
        echo "<option value='NONE'>Others</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        $("#laborDataForm").validate({
            rules: {
                laborName: "required",
                laborAddress: "required",
                npwp: "required",
				npwp_name: "required",
                ppn: "required",
                pph: "required",
				active: "required"
            },
            messages: {
                laborName: "Labor name is a required field.",
                laborAddress: "Labor address is a required field.",
                npwp: "Tax ID is a required field.",
				npwp_name: "Tax Name is a required field.",
                ppn: "PPN is a required field.",
                pph: "PPh is a required field.",
				active: "Status is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#laborDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalLaborId').value = returnVal[3];
                                
                                $('#dataContent').load('contents/labor.php', { laborId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="laborDataForm">
    <input type="hidden" name="action" id="action" value="labor_data" />
    <input type="hidden" name="laborId" id="laborId" value="<?php echo $laborId; ?>" />
    <div class="row-fluid">
     <div class="span4 lightblue">
            <label>Labor Code</label>
            <input type="text" class="span12" tabindex="12" id="laborCode" name="laborCode" value="<?php echo $laborCode; ?>" disabled>
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Labor Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="laborName" name="laborName" value="<?php echo $laborName; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Bank Name</label>
            <input type="text" class="span12" tabindex="10" id="bankName" name="bankName" value="<?php //echo $bankName; ?>">
        </div>-->
        <!--<div class="span4 lightblue">
            <label>Swift Code</label>
            <input type="text" class="span12" tabindex="21" id="swiftCode" name="swiftCode" value="<?php //echo $swiftCode; ?>">
        </div>-->
		<div class="span4 lightblue">
            <label>PPN <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 1", $ppn, '', "ppn", "tax_id", "tax_name", 
                "", 22);
            ?>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Tax ID <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="npwp" name="npwp" value="<?php echo $npwp; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Bank Account No</label>
            <input type="text" class="span12" tabindex="11" id="accountNo" name="accountNo" <?php //echo $readonlyProperty ?> value="<?php //echo $accountNo; ?>">
        </div>-->
        <div class="span4 lightblue">
            <label>PPh <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 2", $pph, '', "pph", "tax_id", "tax_name", 
                "", 23);
            ?>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
           <label>Tax Name <span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="13" id="npwp_name" name="npwp_name" value="<?php echo $npwp_name; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Beneficiary</label>
            <input type="text" class="span12" tabindex="12" id="beneficiary" name="beneficiary" value="<?php echo $beneficiary; ?>">
        </div>-->
         <div class="span4 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Active' as info UNION
                    SELECT '0' as id, 'Inactive' as info;", $active, '', "active", "id", "info", 
                "", 6);
            ?>
        </div>
    </div>
    <div class="row-fluid">
		<div class="span4 lightblue">
            <label>NIK</label>
            <input type="text" class="span12" tabindex="13" id="nik" name="nik" value="<?php echo $nik; ?>">
        </div>
        <div class="span4 lightblue">
          <label>Labor Rules <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Vehicle' as info UNION
					SELECT '2' as id, 'Lowest' as info UNION
					SELECT '3' as id, 'Netto Weight' as info UNION
                    SELECT '4' as id, 'Send Weight' as info;", $laborRules, '', "laborRules", "id", "info", 
                "", 6);
            ?>
        </div>
        <!--<div class="span4 lightblue">
             <label>Branch</label>
            <input type="text" class="span12" tabindex="12" id="branch" name="branch" value="<?php //echo $branch; ?>">
        </div>-->
       
    </div>
	<div class="row-fluid">
		<div class="span4 lightblue">
            <label>Labor Address <span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="4" id="laborAddress" name="laborAddress"><?php echo $laborAddress; ?></textarea>
        </div>
        <div class="span4 lightblue">
          
        </div>
        <!--<div class="span4 lightblue">
             <label>Branch</label>
            <input type="text" class="span12" tabindex="12" id="branch" name="branch" value="<?php //echo $branch; ?>">
        </div>-->
       
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
