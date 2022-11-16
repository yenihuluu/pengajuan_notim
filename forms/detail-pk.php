<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$pgId = $_POST['pgId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT  a.`quantity` AS qty, 100 AS `termin`, 1 AS `exchange_rate`,
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN ((a.price)/1.11) 
WHEN a.ppn = 2 THEN (a.price) ELSE ((a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id))END ) AS price,
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN ((a.quantity * a.price)/1.11) 
WHEN a.ppn = 2 THEN (a.quantity * a.price) ELSE ((a.quantity * a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id))END ) AS `amount_converted`,
(CASE WHEN a.vendor_id = 0 AND a.ppn = 1 THEN ((a.quantity * a.price) - ((a.quantity * a.price)/1.11))
WHEN a.vendor_id = 0 AND a.ppn = 2 THEN (((a.quantity * a.price)*1.11) - (a.quantity * a.price)) 
WHEN a.ppn = 2 THEN COALESCE((a.quantity * a.price) * (SELECT ppn/100 FROM vendor WHERE vendor_id = a.vendor_id),0) ELSE COALESCE((a.quantity * a.price) - 
((a.quantity * a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id)),0) END) AS `ppn_converted`, 0 AS `pph_converted`,
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN (a.quantity * a.price)
WHEN a.ppn = 2 AND a.vendor_id = 0 THEN ((a.quantity * a.price) + (((a.quantity * a.price)*1.11) - (a.quantity * a.price)) )
WHEN a.ppn = 2 THEN (a.quantity * a.price) + ((a.quantity * a.price) * COALESCE((SELECT ppn/100 FROM vendor WHERE vendor_id = a.vendor_id),0)) ELSE (a.quantity * a.price) END) AS `tamount_converted`, 
'' AS `notes`  
FROM purchasing a 
WHERE a.`purchasing_id` = {$pgId}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  /*$sql = "SELECT * FROM contract WHERE contract_id = {$contractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $contractNo = $row->contract_no;
		$po_no = $row->po_no;*/
    //}
// </editor-fold>

?>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            
            <th>Quantity</th>
			<th>Price</th>
            <th>Termin (%)</th>
            <th>Kurs (IDR)</th>
            <th>DPP</th>
            <th>PPN</th>
            <th>PPh</th>
            <th>Total</th>
            <th>Keterangan</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
			
			//if($rowData->general_ledger_module == 'CONTRACT'){
				//$debit_amount = $rowData->debitAmount;					
				//$credit_amount = $rowData->creditAmount;
			//}
                
        ?>
        <tr>
         
        <td style="text-align: right;"><?php echo number_format($rowData->qty, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->price, 10, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->termin, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->exchange_rate, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->amount_converted, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->ppn_converted, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->pph_converted, 2, ".", ","); ?></td>
        <td style="text-align: right;"><?php echo number_format($rowData->tamount_converted, 2, ".", ","); ?></td>
        <td style="text-align: left;"><?php echo $rowData->notes; ?></td>
			
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
