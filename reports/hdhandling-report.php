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

$amount = '';

$vendorHandlingId = '';

//$vendorFreightId = '';



if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    //$whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    //$whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    //$whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	 //$whereProperty4 .= " AND date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	//$whereProperty4 .= " AND date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	//$whereProperty4 .= " AND date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
if(isset($_POST['vendorHandlingId']) && $_POST['vendorHandlingId'] != '' && $_POST['vendorHandlingId'] != 0) {
    $vendorHandlingId = $_POST['vendorHandlingId'];
    
	/*for ($i = 0; $i < sizeof($freightId); $i++) {
                        if($freightIds == '') {
                            $freightIds .= $freightId[$i];
                        } else {
                            $freightIds .= ','. $freightId[$i];
                        }
                    }*/
	$whereProperty .= " AND vh.`vendor_handling_id` IN ({$vendorHandlingId}) ";
		
}

					
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
	
	/*for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
					
	$whereProperty .= " AND sc.stockpile_id IN ('{$stockpileId}') ";
	//$whereProperty4 .= " AND analytic_account IN ('{$stockpileId}') ";
}



$sql = "SELECT t.`transaction_date`, t.`slip_no`, t.`vehicle_no`, c.`po_no`, c.`contract_no`, vh.`vendor_handling_name`, t.`handling_quantity`, t.`handling_price`, s.stockpile_name,
ROUND((t.`handling_quantity` * t.`handling_price`),10) AS dpp, 
ROUND(((t.`handling_quantity` * t.`handling_price`) * (vh.ppn / 100)),10) AS ppn,
ROUND(((t.`handling_quantity` * t.`handling_price`) * (vh.pph / 100)),10) AS pph,
((ROUND((t.`handling_quantity` * t.`handling_price`),10) + ROUND(((t.`handling_quantity` * t.`handling_price`) * (vh.ppn / 100)),10)) - ROUND(((t.`handling_quantity` * t.`handling_price`) * (vh.pph / 100)),10)) AS total,
(SELECT payment_date FROM payment WHERE payment_id = t.hc_payment_id AND payment_status = 0 {$whereProperty3}) AS payment_date
FROM `transaction` t
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor_handling_cost vhc ON vhc.`handling_cost_id` = t.`handling_cost_id`
LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = vhc.`vendor_handling_id`
LEFT JOIN tax tx ON tx.`tax_id` = vh.`pph_tax_id`
LEFT JOIN stockpile s ON s.stockpile_id = sc.stockpile_id
WHERE t.`handling_cost_id` IS NOT NULL
AND (SELECT payment_date FROM payment WHERE payment_id = t.hc_payment_id AND payment_status = 0 {$whereProperty3}) IS NULL
{$whereProperty} ORDER BY t.transaction_date ASC
";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/hdhandling-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		  <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    	 <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
		 <input type="hidden" id="vendorHandlingId" name="vendorHandlingId" value="<?php echo $vendorHandlingId; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
			<th>Transaction Date</th>
            <th>Slip No</th>
            <th>Vehicle No</th>
			<th>PO No</th>
            <th>Contract No</th>
			<th>Stockpile</th>
			<th>Vendor Handling</th>
			<th>Handling Quantity</th>
			<th>Handling Price</th>
			<th>DPP</th>
			<th>PPN</th>
			<th>PPh</th>
			<th>Total</th>
			
			<!--<th>Total Susut</th>
			<th>Susut (-) Toleransi (300)</th>
			<th>Harga Klaim (600/kg)</th>-->
			
           
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
	<td><?php echo $row->slip_no; ?></td>
	<td><?php echo $row->vehicle_no; ?></td>
	<td><?php echo $row->po_no; ?></td>
	<td><?php echo $row->contract_no; ?></td>
	<td><?php echo $row->stockpile_name; ?></td>
	<td><?php echo $row->vendor_handling_name; ?></td>
	<td><?php echo number_format($row->handling_quantity, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->handling_price, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->dpp, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->ppn, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->pph, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->total, 2, ".", ","); ?></td>
	
	
	
	<?php
	$qtyTotal = $qtyTotal + $row->handling_quantity;
	$dppTotal = $dppTotal + $row->dpp;
	$ppnTotal = $ppnTotal + $row->ppn;
	$pphTotal = $pphTotal + $row->pph;
	$grandTotal = $grandTotal + $row->total;
	
	
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
	<td colspan="8" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($qtyTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"></td>
	<td style="text-align: right;"><?php echo number_format($dppTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($ppnTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($pphTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($grandTotal, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
	</table>