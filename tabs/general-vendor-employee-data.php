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
$stockpile_id = '';
$deptId = '';
$divId = '';
$levelId = '';
$allowUpdate = false;

// </editor-fold>
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 62) {
            $allowUpdate = true;
        }
    }
}

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
	//echo $readonlyProperty;
	//echo $sql;
   // $readonlyProperty = 'readonly';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    
    $sql = "SELECT gv.*, b.level, c.level AS pengajuan_level 
            FROM general_vendor gv
			LEFT JOIN perdin_level b ON gv.level_id = b.level_id
			LEFT JOIN perdin_level c ON gv.level_pengajuan = c.level_id
            WHERE gv.general_vendor_id = {$vendorId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
		while ($rowData = $resultData->fetch_object()) {
        //$rowData = $resultData->fetch_object();
		$vendorCode = $rowData->general_vendor_code;
        $vendorName = $rowData->general_vendor_name;
        $vendorAddress = $rowData->general_vendor_address;
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
		$stockpile_id = $rowData->stockpile_id;
		$deptId = $rowData->dept_id;
		$divId = $rowData->div_id;
		$levelId = $rowData->level_pengajuan;
		//$levelPengajuan = $rowData->level_pengajua;
		$level_name = $rowData->level;
		$pengajuan_level = $rowData->pengajuan_level;
    }
	}
    
    // </editor-fold>
	//echo $sql;
	//echo 'AA';
    
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
		
        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
		if($setvalue == 0) {
            echo "<option value='0' selected>NONE</option>";
        } else {
            //echo "<option value='0'>NONE</option>";
        }
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == 0) {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
	} else if($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        if(strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
		
		<?php if($deptId != '') {?>
                  
		setDivisi(1, $('select[id="deptId"]').val(), <?php echo $divId; ?>);
   
		<?php }?>
		
		$('#deptId').change(function() {
            resetDivisi(' ');
            
            if(document.getElementById('deptId').value != '') {
				setDivisi(0,$('select[id="deptId"]').val(),0);
            } 
        });
		
		
		function resetDivisi(text) {
        document.getElementById('divId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('divId').options.add(x);
    }
		
		function setDivisi(type, deptId, divId) {

			$.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getDivisi',
					deptId:deptId
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('divId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('divId').options.add(x);

                        $("#divId").select({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('divId').options.add(x);
                    }

				if(type == 1) {
                        $('#divId').find('option').each(function(i,e){
                            if($(e).val() == divId){
                                $('#divId').prop('selectedIndex',i);
								
								  $("#divId").select({
                                    width: "100%",
                                    placeholder: divId
                                });
                            }
                        });
				}

                    
                }
				//setContract(contract);
            }

        });

		}
		
        $("#generalVendorDataForm").validate({
            rules: {
                vendorName: "required",
                vendorAddress: "required",
                npwp: "required",
				npwp_name: "required",
                ppn: "required",
               // pph: "required",
				active: "required"
            },
            messages: {
                vendorName: "Vendor Name is a required field.",
                vendorAddress: "Vendor Address is a required field.",
                npwp: "Tax ID is a required field.",
				npwp_name: "Tax Name is a required field.",
                ppn: "PPN is a required field.",
               // pph: "PPh is a required field.",
				active: "Status is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#generalVendorDataForm").serialize(),
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
                                
                                $('#dataContent').load('contents/general-vendor-employee.php', { vendorId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
		
		$('#updateLevelButton').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: { action: 'update_level',
								vendorId: document.getElementById('vendorId').value,
								levelId : document.getElementById('levelId').value
                            },
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/general-vendor-employee.php', { vendorId: returnVal[3] }, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
		
		$('#approveLevelButton').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: { action: 'approve_level',
								vendorId: document.getElementById('vendorId').value,
								levelId : document.getElementById('levelId').value
                            },
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                          $('#dataContent').load('forms/general-vendor-employee.php', { vendorId: returnVal[3] }, iAmACallbackFunction2);

                        } 
                    }
                }
            });
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

<form method="post" id="generalVendorDataForm">
    <input type="hidden" name="action" id="action" value="general_vendor_data_employee" />
    <input type="hidden" name="vendorId" id="vendorId" value="<?php echo $vendorId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Vendor Name <span style="color: red;">*</span></label>
            <input disabled type="text" class="span12" tabindex="2" id="vendorName" name="vendorName" value="<?php echo $vendorName; ?>">
        </div>
		<div class="span4 lightblue">
            <label>Vendor Code <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="vendorCode" name="vendorCode" value="<?php echo $vendorCode; ?>" disabled>
        </div>
       <!-- <div class="span4 lightblue">
            <label>Bank Name</label>
            <input type="text" class="span12" tabindex="10" id="bankName" name="bankName" value="<?php //echo $bankName; ?>">
        </div>
		
        <div class="span4 lightblue">
            <label>Swift Code</label>
            <input type="text" class="span12" tabindex="13" id="swiftCode" name="swiftCode" value="<?php //echo $swiftCode; ?>">
        </div>-->
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Tax ID <span style="color: red;">*</span></label>
            <input disabled type="text" class="span12" tabindex="3" id="npwp" name="npwp" value="<?php echo $npwp; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Bank Account No</label>
            <input type="text" class="span12" tabindex="11" id="accountNo" name="accountNo" <?php //echo $readonlyProperty ?> value="<?php //echo $accountNo; ?>">
        </div>-->
        <div class="span4 lightblue">
            <label>PPN <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 1", $ppn, 'disabled', "ppn", "tax_id", "tax_name", 
                "", 21);
            ?>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Tax Name <span style="color: red;">*</span></label>
			<input disabled type="text" class="span12" tabindex="13" id="npwp_name" name="npwp_name" value="<?php echo $npwp_name; ?>">
        </div>
        <!--<div class="span4 lightblue">
            <label>Beneficiary</label>
            <input type="text" class="span12" tabindex="12" id="beneficiary" name="beneficiary" value="<?php //echo $beneficiary; ?>">
        </div>-->
       <div class="span4 lightblue">
            <label>NIK</label>
            <input disabled type="text" class="span12" tabindex="13" id="nik" name="nik" value="<?php echo $nik; ?>">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
             <label>Vendor Address <span style="color: red;">*</span></label>
            <textarea disabled class="span12" rows="3" tabindex="3" id="vendorAddress" name="vendorAddress"><?php echo $vendorAddress; ?></textarea>
        </div>
		<!--<div class="span4 lightblue">
            <label>Branch</label>
            <input type="text" class="span12" tabindex="13" id="branch" name="branch" value="<?php //echo $branch; ?>">
			
        </div>-->
        <div class="span4 lightblue">
           <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Active' as info UNION
                    SELECT '0' as id, 'Inactive' as info;", $active, 'disabled', "active", "id", "info", 
                "", 6);
            ?>
        </div>
        
    </div>
	<div class="alert">
        <b>Info:</b> Wajib diisi jika karyawan
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Departement <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM perdin_departement ORDER by dept ASC", $deptId, '', "deptId", "dept_id", "dept", "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
           <label>Divisi <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", $divId, "divId", "divId", "divisi", "", 2);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Grade <span style="color: red;">*</span></label>
            <input type="text" readonly class="span9" tabindex="13" id="level_name" name="level_name" value="<?php echo $level_name; ?>">
        </div>
        <div class="span4 lightblue">
          <label>Pengajuan Grade <span style="color: red;">*</span></label>
            <input type="text" readonly class="span9" tabindex="13" id="pengajuan_level" name="pengajuan_level" value="<?php echo $pengajuan_level; ?>">
        </div>
		<div class="span4 lightblue">
		<label>Stockpile </label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile ORDER BY stockpile_name ASC", $stockpile_id, "", "stockpile_id", "stockpile_id", "stockpile_name", "", "", "", 1);
            ?>
        </div>
        </div>
        
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
<div class="alert">
        <b>Info:</b> Pengajuan update grade employee
    </div>
<!--<form method="post" id="updateLevel">-->
<!--<input type="hidden" name="action" id="action" value="update_level" />-->

<div class="row-fluid">  
<div class="span4 lightblue">
<label>Grade <span style="color: red;">*</span></label>
<?php
createCombo("SELECT * FROM perdin_level ORDER by level ASC", $levelId, '', "levelId", "level_id", "level", "", 1);
?>
</br>
<button class="btn btn-primary" id="updateLevelButton">Submit</button>
<?php if($allowUpdate && ($levelId != '' && $levelId != 0)){?>
<button class="btn btn-success" id="approveLevelButton">Approve</button>
<?php } ?>
</div>
</div>
<!--</form>-->