<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$cBankId = '';
$bankName = '';
$branch = '';
$accountNo = '';
$beneficiary = '';
$swiftCode = '';
$masterBankId = '';
$active = '' ;

if(isset($_POST['cBankId']) && $_POST['cBankId'] != '') {
    $cBankId = $_POST['cBankId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

    $sql = "SELECT * FROM customer_bank WHERE cust_bank_id = {$cBankId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$cBankId = $rowData->cust_bank_id;
        $bankName = $rowData->bank_name;
        $branch = $rowData->branch;
        $accountNo = $rowData->account_no;
        $beneficiary = $rowData->beneficiary;
        $swiftCode = $rowData->swift_code;
        $masterBankId = $rowData->master_bank_id;
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
        echo "<option value=''>-- Please Select if Applicable --</option>";
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


		$('#masterBank').change(function() {

            if(document.getElementById('masterBank').value != '') {
                setBankName($('select[id="masterBank"]').val());
			}
        });

		function resetSPB() {

        //document.getElementById('vendorName').value = '';
		//document.getElementById('vendorId').value = '';
		//document.getElementById('stockpileName').value = '';

		//document.getElementById('stockpileId').value = '';
		document.getElementById('quantity').value = 0;
		document.getElementById('price').value = 0;
		document.getElementById('docSPB').href = '';
		}

		function setBankName(masterBankId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getBankName',
                    masterBankId: masterBankId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');


					document.getElementById('bankName').value = returnVal[1];

                }
            }
        });
    }


    });

</script>
<input type="hidden" id="cBankId" name="cBankId" value="<?php echo $cBankId; ?>">
<div class="row-fluid">
  <div class="span4 lightblue">
      <label>Master Bank<span style="color: red;">*</span></label>
      <?php
      createCombo("SELECT master_bank_id,bank_name FROM master_bank
        ORDER BY bank_name ASC", $masterBankId, "", "masterBank", "master_bank_id", "bank_name",
          "", "", "select2combobox100");
      ?>
      </div>
    <div class="span6 lightblue">

    </div>
</div>
<div class="row-fluid">


    <div class="span6 lightblue">
        <label>Bank Name</label>
        <input type="text" class="span12" tabindex="10" id="bankName" name="bankName" value="<?php echo $bankName; ?>">
    </div>
    <div class="span6 lightblue">

    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Branch</label>
        <input type="text" class="span12" tabindex="10" id="branch" name="branch" value="<?php echo $branch; ?>">
    </div>
    <div class="span6 lightblue">

    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Account No</label>
        <input type="text" class="span12" tabindex="10" id="accountNo" name="accountNo" value="<?php echo $accountNo; ?>">
    </div>
    <div class="span6 lightblue">

    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Beneficiary</label>
        <input type="text" class="span12" tabindex="10" id="beneficiary" name="beneficiary" value="<?php echo $beneficiary; ?>">
    </div>
    <div class="span6 lightblue">

    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Swift Code</label>
        <input type="text" class="span12" tabindex="10" id="swiftCode" name="swiftCode" value="<?php echo $swiftCode; ?>">
    </div>
    <div class="span6 lightblue">

    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
           <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $active, '', "active", "id", "info", 
                "", 6);
            ?>
        </div>
    <div class="span6 lightblue">

    </div>
</div>