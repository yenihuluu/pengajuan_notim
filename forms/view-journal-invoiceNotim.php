<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$invId = $_POST['invId'];
$status = $_POST['status'];
$glModule = "";

if($status == 2){
    $glModule = " general_ledger_module = 'RETURN INVOICE NOTIM'";
}else {
    $glModule = " gl.amount > 0 AND general_ledger_module = 'INVOICE NOTIM'";
}

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT gl.*, 
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name

FROM general_ledger gl 
WHERE {$glModule} AND gl.invoice_notim_id = {$invId}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  $sql = "SELECT * FROM invoice_notim WHERE inv_notim_id = {$invId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $invoiceNo = $row->inv_notim_no;
		
    }
// </editor-fold>

?>
<h5>Invoice No: <?php echo $invoiceNo; ?></h5>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            
            <th style="width: 60%;">Account Name</th>
			<th style="width: 20%;">Account No</th>
            <th>Debit</th>
            <th>Credit</th>
            
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
			
			if($rowData->general_ledger_module == 'INVOICE NOTIM'){
				$debit_amount = $rowData->amount;					
				$credit_amount = $rowData->amount;
			}else if($rowData->general_ledger_module == 'RETURN INVOICE NOTIM'){
				$debit_amount = $rowData->amount;					
				$credit_amount = $rowData->amount;
			}

                
        ?>
        <tr>
         
            
            <td style="width: 60%;"><?php echo $rowData->account_name; ?></td>
			<td style="width: 20%;"><?php echo $rowData->account_no; ?></td>
    
			<?php $debitAmount = 0;
			if($rowData->general_ledger_type == 1) {
			$debitAmount = $debit_amount; 
			}?>
			<td style="text-align: right;"><?php echo number_format($debitAmount, 2, ".", ","); ?></td>
    
			<?php $creditAmount = 0;
			if($rowData->general_ledger_type == 2) {
			$creditAmount = $credit_amount;
				}?>
			<td style="text-align: right;"><?php echo number_format($creditAmount, 2, ".", ","); ?></td>
        </tr>
        <?php
            
            }
        } else {
        ?>
        <tr>
            <td colspan="7">
                No data to be shown.
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
    </div>
    <div class="span6 lightblue">
    </div>
</div>
