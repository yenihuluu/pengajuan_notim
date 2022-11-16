<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$slipRetur = $_POST['slipRetur'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT t.*, c.po_no FROM `transaction` t
LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN contract c ON c.contract_id = sc.contract_id WHERE slip_no = '{$slipRetur}'";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  //$sql = "SELECT * FROM transaction WHERE transaction_id = {$transactionId}";
   // $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //if($result !== false && $result->num_rows == 1) {
      //  $row = $result->fetch_object();
      //  $slipNo = $row->slip_no;
		
   // }
// </editor-fold>
if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
?>
<h5>Slip No: <?php echo $rowData->slip_no; ?></h5>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            
            <th>Transaction Date</th>
			<th>PO No</th>
            <th>vehicle No</th>
            <th>Send Weight</th>
			<th>Bruto Weight</th>
			<th>Tarra Weight</th>
			<th>Netto Weight</th>
			<th>Freight Price</th>
			<th>Unloading Price</th>
            
            
        </tr>
    </thead>
    <tbody>
        
        <tr>
         
            
            <td><?php echo $rowData->transaction_date; ?></td>
			<td><?php echo $rowData->po_no; ?></td>
			<td><?php echo $rowData->vehicle_no; ?></td>
			<td style="text-align: right;"><?php echo number_format($rowData->send_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($rowData->bruto_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($rowData->tarra_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($rowData->netto_weight, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($rowData->freight_price, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($rowData->unloading_price, 2, ".", ","); ?></td>
			
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
