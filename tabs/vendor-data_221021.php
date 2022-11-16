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

$vendorId = '';
$vendorCode = '';
$vendorName = '';
$vendorAddress = '';
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
$vendorGroupId = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    
    $vendorId = $_POST['vendorId'];
    
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
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    
    $sql = "SELECT v.*
            FROM vendor v
            WHERE v.vendor_id = {$vendorId}
            ORDER BY v.vendor_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vendorCode = $rowData->vendor_code;
        $vendorName = $rowData->vendor_name;
        $vendorAddress = $rowData->vendor_address;
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
        $vendorGroupId = $rowData->vendor_group_id;
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
        $("#vendorDataForm").validate({
            rules: {
                vendorCode: "required",
                vendorName: "required",
                vendorAddress: "required",
                npwp: "required",
				npwp_name: "required",
                ppn: "required",
                pph: "required",
				active: "required"
            },
            messages: {
                vendorCode: "Vendor Code is a required field.",
                vendorName: "Vendor Name is a required field.",
                vendorAddress: "Vendor Address is a required field.",
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
                                document.getElementById('generalVendorId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/vendor.php', { vendorId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="vendorDataForm">
    <input type="hidden" name="action" id="action" value="vendor_data" />
    <input type="hidden" name="vendorId" id="vendorId" value="<?php echo $vendorId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Code <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="vendorCode" name="vendorCode" value="<?php echo $vendorCode; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Bank Name</label>
            <input type="text" class="span12" tabindex="10" id="bankName" name="bankName" value="<?php //echo $bankName; ?>">
        </div>-->
        <div class="span4 lightblue">
            <label>PPN <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 1", $ppn, '', "ppn", "tax_id", "tax_name", 
                "", 21);
            ?>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Vendor Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="vendorName" name="vendorName" value="<?php echo $vendorName; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Vendor Group</label>
            <?php
            createCombo("SELECT * FROM vendor_group ", $vendorGroupId, '', "vendorGroupId", "vendor_group_id", "group_name", 
                "", 21);
            ?>
        </div>
        <div class="span4 lightblue">
            <label>PPh <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 2", $pph, '', "pph", "tax_id", "tax_name", 
                "", 21);
            ?>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Tax ID <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="npwp" name="npwp" value="<?php echo $npwp; ?>">
        </div>
       <!-- <div class="span4 lightblue">
            <label>Beneficiary</label>
            <input type="text" class="span12" tabindex="12" id="beneficiary" name="beneficiary" value="<?php //echo $beneficiary; ?>">
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
            <label>Tax Name <span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="13" id="npwp_name" name="npwp_name" value="<?php echo $npwp_name; ?>">
        </div>
		<div class="span4 lightblue">
            <label>NIK</label>
            <input type="text" class="span12" tabindex="13" id="nik" name="nik" value="<?php echo $nik; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Swift Code</label>
            <input type="text" class="span12" tabindex="13" id="swiftCode" name="swiftCode" value="<?php //echo $swiftCode; ?>">
        </div>-->
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Vendor Address <span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="3" id="vendorAddress" name="vendorAddress"><?php echo $vendorAddress; ?></textarea>
        </div>
        <!--<div class="span4 lightblue">
            <label>Branch</label>
            <input type="text" class="span12" tabindex="13" id="branch" name="branch" value="<?php //echo $branch; ?>">
        </div>-->
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
