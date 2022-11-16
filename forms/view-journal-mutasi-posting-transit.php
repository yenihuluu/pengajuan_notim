<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$invoiceId = $_POST['invoiceId'];
$paymentId = $_POST['paymentId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT gl.*,  id.notes,
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name
FROM general_ledger gl
LEFT JOIN invoice_detail  id ON id.invoice_detail_id = gl.`invoice_id`
LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id`
WHERE  gl.amount > 0 AND (general_ledger_module = 'INVOICE DETAIL' OR general_ledger_module = 'RETURN INVOICE') AND id.invoice_id = {$invoiceId}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$sql1 = "SELECT *, 
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name
FROM general_ledger gl 
WHERE  gl.amount > 0 AND general_ledger_module = 'PAYMENT' AND gl.payment_id = {$paymentId}";
$result = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);

// </editor-fold>

?>
<!--<h5>PO No: --><?php //echo $po_no; ?><!-- | Contract No: --><?php //echo $contractNo; ?><!--</h5>-->
<div class="row-fluid">
    <div class="span12 lightblue">
        <h5><stong>INVOICE</stong></h5>
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
            if ($resultData !== false && $resultData->num_rows > 0) {
                while ($rowData = $resultData->fetch_object()) {

                    if ($rowData->general_ledger_module == 'INVOICE DETAIL') {
                        $debit_amount = $rowData->amount;
                        $credit_amount = $rowData->amount;
                    }

                    ?>
                    <tr>


                        <td style="width: 60%;"><?php echo $rowData->account_name; ?></td>
                        <td style="width: 20%;"><?php echo $rowData->account_no; ?></td>

                        <?php $debitAmount = 0;
                        if ($rowData->general_ledger_type == 1) {
                            $debitAmount = $debit_amount;
                        } ?>
                        <td style="text-align: right;"><?php echo number_format($debitAmount, 2, ".", ","); ?></td>

                        <?php $creditAmount = 0;
                        if ($rowData->general_ledger_type == 2) {
                            $creditAmount = $credit_amount;
                        } ?>
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
</div>
<div class="row-fluid">
    <div class="span12 lightblue">
        <h5><stong>PAYMENT</stong></h5>
        <table class="table table-bordered table-striped" id="contentTableA" style="font-size: 9pt;">
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
            if ($result !== false && $result->num_rows > 0) {
                while ($rowData = $result->fetch_object()) {

                    if ($rowData->general_ledger_module == 'PAYMENT') {
                        $debit_amount = $rowData->amount;
                        $credit_amount = $rowData->amount;
                    }

                    ?>
                    <tr>


                        <td style="width: 60%;"><?php echo $rowData->account_name; ?></td>
                        <td style="width: 20%;"><?php echo $rowData->account_no; ?></td>

                        <?php $debitAmount = 0;
                        if ($rowData->general_ledger_type == 1) {
                            $debitAmount = $debit_amount;
                        } ?>
                        <td style="text-align: right;"><?php echo number_format($debitAmount, 2, ".", ","); ?></td>

                        <?php $creditAmount = 0;
                        if ($rowData->general_ledger_type == 2) {
                            $creditAmount = $credit_amount;
                        } ?>
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
</div>