<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$adjNo = $_POST['adjNo'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT gl.*
FROM rpt_upload gl
WHERE gl.payment_no = '{$adjNo}' ";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


 /*$sql = "SELECT * FROM adjustment_oa_ob WHERE invoice_id = {$invoiceId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $invoiceNo = $row->invoice_no;
		//$po_no = $rowData->po_no;
    }*/
// </editor-fold>

?>
<h5>Jurnal No: <?php echo $adjNo; ?></h5>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            <th style="width: 40%;">Slip No</th>
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
/*			
if($rowData->general_ledger_type == 2 && $rowData->general_ledger_module == 'INVOICE DETAIL' && $rowData->general_ledger_for == 10 && $rowData->invoice_id != 0){
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
         
            <td style="width: 40%;"><?php echo $rowData->slip_no; ?></td>
            <td style="width: 40%;"><?php echo $rowData->account_name; ?></td>
			<td style="width: 20%;"><?php echo $rowData->account_no /*." ". $status*/;  ?></td>
			
    
			<?php $debitAmount = 0;
			if($rowData->amounts > 0) {
			$debitAmount = $rowData->amounts; 
			}?>
			<td style="text-align: right;"><?php echo number_format($debitAmount, 2, ".", ","); ?></td>
    
			<?php $creditAmount = 0;
			if($rowData->amounts < 0) {
			$creditAmount = $rowData->amounts * -1;
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
