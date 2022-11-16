<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$transactionId = $_POST['transactionId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT *, 
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name
FROM general_ledger gl 
WHERE  gl.amount > 0 AND general_ledger_module = 'NOTA TIMBANG' AND gl.transaction_id = {$transactionId}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  $sql = "SELECT * FROM contract c
left join stockpile_contract sc on sc.contract_id = c.contract_id
left join `transaction` t on sc.stockpile_contract_id = t.stockpile_contract_id WHERE transaction_id = {$transactionId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $contractNo = $row->contract_no;
		$po_no = $row->po_no;
    }
// </editor-fold>

?>
<h5>PO No: <?php echo $po_no; ?> | Contract No: <?php echo $contractNo; ?></h5>
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

			if($rowData->general_ledger_module == 'NOTA TIMBANG'){
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
