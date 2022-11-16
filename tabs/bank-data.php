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

// <editor-fold defaultstate="collapsed" desc="Variable for Bank Data">

$bankId = '';
$bankName = '';
$bankAccountNo = '';
$bankAccountName = '';
$currencyId = '';
$openingBalance = '';
$accountId = '';
$bankType = '';
$bankCode = '';
$stockpileId = '';
$masterBank = '';
$branch = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['bankId']) && $_POST['bankId'] != '') {
    
    $bankId = $_POST['bankId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Bank Data">
    
    $sql = "SELECT b.*
            FROM bank b
            WHERE b.bank_id = {$bankId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $bankName = $rowData->bank_name;
        $bankAccountNo = $rowData->bank_account_no;
        $bankAccountName = $rowData->bank_account_name;
        $currencyId = $rowData->currency_id;
        $openingBalance = $rowData->opening_balance;
        $accountId = $rowData->account_id;
        $bankType = $rowData->bank_type;
        $bankCode = $rowData->bank_code;
		$stockpileId = $rowData->stockpile_id;
		$masterBank = $rowData->master_bank;
		$branch = $rowData->branch;
		
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
        $('#openingBalance').number(true, 2);
        
        $("#bankDataForm").validate({
            rules: {
                bankName: "required",
                bankAccountNo: "required",
                bankAccountName: "required",
                currencyId: "required",
                accountId: "required",
                openingBalance: "required",
                bankType: "required",
                bankCode: "required",
				masterBank: "required",
				branch: "required",
				stockpileId: "required"
            },
            messages: {
                bankName: "Bank name is a required field.",
                bankAccountNo: "Account no is a required field.",
                bankAccountName: "Account name is a required field.",
                currencyId: "Currency is a required field.",
                accountId: "COA is a required field.",
                openingBalance: "Opening Balance is a required field.",
                bankType: "Bank type is a required field.",
                bankCode: "Bank code is a required field.",
				 masterBank: "Master Bank code is a required field.",
				  branch: "Branch code is a required field.",
				stockpileId: "Stockpile code is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#bankDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalBankId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/bank.php', { bankId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="bankDataForm">
    <input type="hidden" name="action" id="action" value="bank_data" />
    <input type="hidden" name="bankId" id="bankId" value="<?php echo $bankId; ?>" />
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM currency ORDER BY currency_code", $currencyId, '', "currencyId", "currency_id", "currency_code", 
                "", 1);
            ?>
        </div>
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
		<?php
            createCombo("SELECT * FROM stockpile ORDER BY stockpile_name ASC", $stockpileId, '', "stockpileId", "stockpile_id", "stockpile_name", 
                "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Bank code <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="11" id="bankCode" name="bankCode" value="<?php echo $bankCode; ?>">
        </div>
		
        
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Bank name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="bankName" name="bankName" value="<?php echo $bankName; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Bank type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Bank' as info UNION
                    SELECT '2' as id, 'Petty Cash' as info UNION
                    SELECT '3' as id, 'Cash Advance' as info;", $bankType, '', "bankType", "id", "info", 
                "", 12);
            ?>
        </div>
         <div class="span4 lightblue">
            <label>Bank Account No <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="bankAccountNo" name="bankAccountNo" value="<?php echo $bankAccountNo; ?>">
        </div>
    </div>
    <div class="row-fluid">   
       
        <div class="span4 lightblue">
            <label>Opening Balance <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="13" id="openingBalance" name="openingBalance" value="<?php echo number_format($openingBalance, 0, ".", ","); ?>">
        </div>
         <div class="span4 lightblue">
            <label>Bank Account Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="4" id="bankAccountName" name="bankAccountName" value="<?php echo $bankAccountName; ?>">
        </div>
        <div class="span4 lightblue">
            <label>COA <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT account_id, CONCAT(account_no, ' - ', account_name) AS account_full "
                    . "FROM account WHERE account_type = 7 ORDER BY account_name", $accountId, '', "accountId", "account_id", "account_full", 
                "", 14);
            ?>
        </div>
    </div>
	<div class="row-fluid">   
       
        
         <div class="span4 lightblue">
            <label>Branch <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="4" id="branch" name="branch" value="<?php echo $branch; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Master Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT master_bank_id, bank_name "
                    . "FROM master_bank ORDER BY bank_name ASC", $masterBank, '', "masterBank", "master_bank_id", "bank_name", 
                "", 14);
            ?>
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
