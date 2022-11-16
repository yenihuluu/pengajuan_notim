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

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

$adj_id = '';
$adjustment = '';
$adjustmentAcc = '';
$adjustmentNotes = '';
$adjustmentDate = '';
$ppn = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['adj_id']) && $_POST['adj_id'] != '') {
    
    $adj_id = $_POST['adj_id'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for User Data">
    
    $sql = "SELECT a.adj_id, a.contract_id, a.adjustment, a.adjustment_acc, a.adjustment_notes, DATE_FORMAT(a.adjustment_date, '%d/%m/%Y') AS adjustment_date, b.contract_no, b.quantity, a.adjustment_ppn,
FORMAT((SELECT COALESCE(SUM(sc.quantity),0) FROM stockpile_contract sc WHERE sc.contract_id = b.contract_id) - 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = b.`contract_id`) -
                (SELECT CASE WHEN c.contract_type = 'C' THEN COALESCE(SUM(t.quantity), 0)
                ELSE COALESCE(SUM(t.send_weight), 0) END
                FROM TRANSACTION t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.`stockpile_contract_id`
                LEFT JOIN contract c ON c.contract_id = sc.contract_id
                WHERE sc.`contract_id` = b.contract_id), 2) AS quantity_available 	
FROM contract_adjustment a
LEFT JOIN contract b ON a.`contract_id` = b.`contract_id`
WHERE a.`adj_id` = {$adj_id}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $adj_id = $rowData->adj_id;
		$contractId = $rowData->contract_id;
        $adjustment = $rowData->adjustment;
        $adjustmentAcc = $rowData->adjustment_acc;
		$adjustmentNotes = $rowData->adjustment_notes;
		$adjustmentDate = $rowData->adjustment_date;
		$contractNo = $rowData->contract_no;
		$quantity = $rowData->quantity;
		$ppn = $rowData->adjustment_ppn;
		$availableQuantity = $rowData->quantity_available;
        
    }else {
    
    if(isset($_SESSION['adjustment'])) {
		$adj_id = $_SESSION['adjustment']['adj_id'];
        $contractId = $_SESSION['adjustment']['contractId'];
        $adjustment = $_SESSION['adjustment']['adjustment'];
        $adjustmentAcc = $_SESSION['adjustment']['adjustmentAcc'];
		$adjustmentNotes = $_SESSION['adjustment']['adjustmentNotes'];
		$adjustmentDate = $_SESSION['adjustment']['adjustmentDate'];
		$contractNo = $_SESSION['adjustment']['contractNo'];
		$quantity = $_SESSION['adjustment']['quantity'];
		$ppn = $_SESSION['adjustment']['ppn'];
     
    }
}
    // </editor-fold>
    
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
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
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $('#adjustment').number(true, 10);
		
		 
		
		$('#contractId').change(function() {
            resetContractAdjustmentDetail(' ');
            
            if(document.getElementById('contractId').value != '') {
				getContractAdjustmentDetail($('select[id="contractId"]').val());
            } 
        });
        
		
		function resetContractAdjustmentDetail() {
        document.getElementById('contractNo').value = '';
        document.getElementById('quantity').value = '';
		//document.getElementById('availableQuantity').value = '';
		
    }
		function getContractAdjustmentDetail(contractAdjustmentDetail) {
        
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getContractAdjustmentDetail',
                        contractAdjustmentDetail: contractAdjustmentDetail
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('contractNo').value = returnVal[1];
                        document.getElementById('quantity').value = returnVal[2];
						//document.getElementById('availableQuantity').value = returnVal[3];
						
                    }
                }
            });
        
	}
       $("#adjustmentDataForm").validate({
            rules: {
                contractId: "required",
                adjustment: "required",
                adjustmentAcc: "required",
				adjustmentDate: "required",
				ppn: "required"
			},
            messages: {
                contractId: "PO Number is a required field.",
                adjustment: "Adjustment is a required field.",
                adjustmentAcc: "Account is a required field.",
				adjustmentDarte: "Date is a required field.",
				ppn: "PPN is a required field."
                
            },
            submitHandler: function(form) {
                $('#submitButton').attr("disabled", true);
				$.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#adjustmentDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalAdjId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/adjustment.php', { adj_id: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#submitButton').attr("disabled", false);
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

<form method="post" id="adjustmentDataForm">
    <input type="hidden" name="action" id="action" value="adjustment_data" />
	<input type="hidden" id="adj_id" name="adj_id" value="<?php echo $adj_id;?>">
    <div class="row-fluid"> 
        <div class="span3 lightblue">
            <label>PO No. <span style="color: red;">*</span></label>
             <?php
            createCombo("SELECT c.*,
IFNULL((SELECT `status` FROM stock_transit WHERE stockpile_contract_id = sc.`stockpile_contract_id` limit 1),0) AS `status`
FROM contract c
LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id`
WHERE DATE_FORMAT(c.`entry_date`,'%Y-%m-%d') >= DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -6 YEAR), '%Y-%m-%d')
AND IFNULL((SELECT `status` FROM stock_transit WHERE stockpile_contract_id = sc.`stockpile_contract_id` limit 1),0) = 0
ORDER BY c.contract_id DESC", $contractId, "", "contractId", "contract_id", "po_no", 
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Contract No</label>
            <input readonly type="text" class="span12" tabindex="" id="contractNo" name="contractNo" value="<?php echo $contractNo; ?>">
        </div>
        <div class="span3 lightblue">
        	<label>Quantity</label>
            <input readonly type="text" class="span12" tabindex="" id="quantity" name="quantity" value="<?php echo number_format($quantity, 0, ".", ",");?>">
        </div>
		<!--<div class="span3 lightblue">
        	<label>Available Quantity</label>
            <input readonly type="text" class="span12" tabindex="" id="availableQuantity" name="availableQuantity" value="<?php //echo number_format($availableQuantity, 0, ".", ",");?>">
        </div>-->
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Adjustment <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="adjustment" name="adjustment" value="<?php echo $adjustment; ?>" >
        </div>
        <div class="span4 lightblue">
            <label>Adjustment Date <span style="color: red;">*</span></label>
			<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="adjustmentDate" name="adjustmentDate" value="<?php echo $adjustmentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
		<label>PPN <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'PPN' as info UNION
                    SELECT '0' as id, 'NON PPN' as info;", $ppn, '', "ppn", "id", "info", 
                "", 6);
            ?>
        </div>
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Account <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT account_id, CONCAT(account_no, ' - ', account_name) AS account_full FROM account WHERE account_type = 1 AND account_no IN (210102, 520100,150120,510101)", $adjustmentAcc, '', "adjustmentAcc", "account_id", "account_full", 
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
        
        </div>
        <div class="span4 lightblue">
        
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Notes<span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="" id="adjustmentNotes" name="adjustmentNotes"><?php echo $adjustmentNotes; ?></textarea>
        </div>
        <div class="span4 lightblue">
            
        </div>
        <div class="span4 lightblue">
            
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
