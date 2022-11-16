<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';

$periodFrom = '';
$periodTo = '';
$paymentFrom = '';
$paymentTo = '';
$vendorId = '';
$stockpileId = '';

if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	//$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty2 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	//$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND payment_date BETWEEN '2019-08-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	//$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN '2019-08-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '' && $_POST['vendorId'] != 0) {
    $vendorId = $_POST['vendorId'];
    
	for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= $vendorId[$i];
                        } else {
                            $vendorIds .= ','. $vendorId[$i];
                        }
                    }
	 $whereProperty .= " AND c.vendor_id IN ({$vendorIds}) ";
		
}

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '' && $_POST['stockpileId'] != 0) {
    $stockpileId = $_POST['stockpileId'];
    
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
	 $whereProperty .= " AND SUBSTR(t.slip_no,1,3) IN ({$stockpileIds}) ";
		
}

					

$sql = "SELECT t.transaction_date, s.stockpile_name, t.`slip_no`, t.`vehicle_no`, v.`vendor_name`, c.po_no, c.contract_no,
t.`send_weight`, t.`netto_weight`, t.`quantity`, t.`unit_price`, 
(CASE WHEN  t.notim_status = 0 AND t.slip_retur IS NULL THEN t.`quantity` * t.`unit_price` ELSE 0 END)  AS amount_payment,
(SELECT payment_date FROM payment WHERE payment_id = t.payment_id {$whereProperty2}) AS payment_date,
(SELECT payment_no FROM payment WHERE payment_id = t.payment_id {$whereProperty2}) AS payment_no
FROM `transaction` t
LEFT JOIN vendor v ON v.`vendor_id` = t.`vendor_id`
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
WHERE 1=1 AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') > '2019-08-01' AND c.`contract_type` = 'C'
AND (CASE WHEN t.notim_status = 0 AND t.slip_retur IS NULL THEN t.`quantity` * t.`unit_price` ELSE 0 END) > 0
AND (c.return_shipment IS NULL OR c.return_shipment = 0) {$whereProperty}
AND (SELECT payment_date FROM payment WHERE payment_id = t.payment_id {$whereProperty2}) IS NULL
AND t.payment_id IS NULL
ORDER BY t.transaction_id ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/hdcurah-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    	 <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
		 <input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorIds; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileIds; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
			<th>Transaction Date</th>
			<th>Stockpile</th>
            <th>No. Slip</th>
            <th>No. Mobil</th>
            <th>Nama PKS</th>
			<th>No. PO</th>
            <th>No. Kontrak</th>
			<th>Berat Kirim</th>
			<th>Berat Netto</th>
			<th>Inventory</th>
			<th>Unit Price</th>
			<th>Amount Payment</th>
			<th>Payment Date</th>
			<th>Payment No</th>
			
           
        </tr>
    </thead>
    <tbody>
	<?php
	//echo $sql;
	if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	
?> 
	<tr>
	
	<td><?php echo $no; ?></td>
	<td><?php echo $row->transaction_date; ?></td>
	<td><?php echo $row->stockpile_name; ?></td>
	<td><?php echo $row->slip_no; ?></td>
	<td><?php echo $row->vehicle_no; ?></td>
	<td><?php echo $row->vendor_name; ?></td>
	<td><?php echo $row->po_no; ?></td>
	<td><?php echo $row->contract_no; ?></td>
	<td><?php echo number_format($row->send_weight, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->netto_weight, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->unit_price, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->amount_payment, 2, ".", ","); ?></td>
	<td><?php echo $row->payment_date; ?></td>
	<td><?php echo $row->payment_no; ?></td>
	
	<?php
	$total = $total + $row->amount_payment;
	
	
	?>
	</tr>
	<?php
                $no++;
            }
        }else{
			//echo $sql;
		}
        ?>
	</tbody>
	<tfoot>
	<?php
/*
	$sqlPph = "SELECT tx.tax_category, f.pph FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id WHERE freight_id = {$freightIds}";
                $resultPph = $myDatabase->query($sqlPph, MYSQLI_STORE_RESULT);   
                if($resultPph !== false && $resultPph->num_rows > 0) {
                    while($rowPph = $resultPph->fetch_object()) {
							
								$pph = $dpp * ($rowPph->pph/100);
							
					}
				}
	
	$grandTotal = $dpp - $pph;*/
	?>
	<!--<tr>
	<td colspan="14" style="text-align: right;">Sub Total</td>
	<td style="text-align: right;"><?php // echo number_format($dpp, 2, ".", ","); ?></td>
	</tr>
	<tr>
	<td colspan="14" style="text-align: right;">PPh</td>
	<td style="text-align: right;"><?php //echo number_format($pph, 2, ".", ","); ?></td>
	</tr>-->
	<tr>
	<td colspan="12" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($total, 2, ".", ","); ?></td>
	<td></td>
	<td></td>
	</tr>
	</tfoot>
	</table>