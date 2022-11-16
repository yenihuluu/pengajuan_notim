<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$paymentId = $_POST['paymentId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT gl.account_no, gl.account_name, gl.debitAmount, gl.creditAmount , gl.`remarks`, gl.`general_ledger_module`
FROM gl_report gl
LEFT JOIN payment p ON p.payment_id = gl.payment_id 
WHERE 1=1 AND gl.`payment_id` = {$paymentId} and regenerate = 0";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  $sql = "SELECT jurnal_no FROM gl_report WHERE payment_id = {$paymentId} LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $jurnal_no = $row->jurnal_no;
		//$po_no = $rowData->po_no;
    }
// </editor-fold>

?>
 <div id="invoiceAccount">

<h5>Payment No: <?php echo $jurnal_no; ?></h5>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            <th style="width: 40%;">Notes</th>
            <th style="width: 40%;">Account Name</th>
			<th style="width: 20%;">Account No</th>
            <th>Debit</th>
            <th>Credit</th>
            
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
				
				$debit_amount = $rowData->debitAmount;					
				$credit_amount = $rowData->creditAmount;
				if($rowData->general_ledger_module == 'RETURN PAYMENT'){
					$status = '(RETURN)';
				}else{
					$status = '';
				}
			
/*if($rowData->general_ledger_type == 2 && $rowData->general_ledger_module == 'INVOICE DETAIL' && $rowData->general_ledger_for == 10 && $rowData->invoice_id != 0){
$credit_amount = $rowData->amount;
$status = '';
}
if($rowData->general_ledger_type == 2 && $rowData->general_ledger_module == 'RETURN INVOICE' && $rowData->general_ledger_for == 10 && $rowData->invoice_id != 0){
$credit_amount = $rowData->amount;
$status = '(RETURN)';
}
if($rowData->general_ledger_type == 1 && $rowData->general_ledger_module == 'INVOICE DETAIL' && $rowData->general_ledger_for == 10 && $rowData->invoice_id != 0){
$debit_amount = $rowData->amount;
$status = '';
}
if($rowData->general_ledger_type == 1 && $rowData->general_ledger_module == 'RETURN INVOICE' && $rowData->general_ledger_for == 10 && $rowData->invoice_id != 0){
$debit_amount = $rowData->amount;
$status = '(RETURN)';
}*/

                
        ?>
        <tr>
         
            <td style="width: 40%;"><?php echo $rowData->remarks; ?></td>
            <td style="width: 40%;"><?php echo $rowData->account_name; ?></td>
			<td style="width: 20%;"><?php echo $rowData->account_no ." ". $status;  ?></td>
			
    
			<?php $debitAmount = 0;
			//if($rowData->general_ledger_type == 1) {
			$debitAmount = $debit_amount; 
			//}
			?>
			<td style="text-align: right;"><?php echo number_format($debitAmount, 2, ".", ","); ?></td>
    
			<?php $creditAmount = 0;
			//if($rowData->general_ledger_type == 2) {
			$creditAmount = $credit_amount;
				//}
				?>
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
</div>
</div>
