<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'VIEW OA SUMMARY REPORT',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$whereProperty = '';

$periodFrom = '';

$periodTo = '';

//$paymentFrom = '';

//$paymentTo = '';

$amount = '';

$freightId = '';

$vendorFreightId = '';


/*
if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}*/
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
if(isset($_POST['freightId']) && $_POST['freightId'] != '' && $_POST['freightId'] != 0) {
    $freightIds = $_POST['freightId'];
    
	/*for ($i = 0; $i < sizeof($freightId); $i++) {
                        if($freightIds == '') {
                            $freightIds .= $freightId[$i];
                        } else {
                            $freightIds .= ','. $freightId[$i];
                        }
                    }*/
	$whereProperty .= " AND fc.`freight_id` IN ({$freightIds}) ";
		
}

					
if(isset($_POST['vendorFreightId']) && $_POST['vendorFreightId'] != '' && $_POST['vendorFreightId'] != 0) {
    $vendorFreightId = $_POST['vendorFreightId'];
	
	for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
                        if($vendorFreightIds == '') {
                            $vendorFreightIds .= $vendorFreightId[$i];
                        } else {
                            $vendorFreightIds .= ','. $vendorFreightId[$i];
                        }
                    }
					
    $whereProperty .= " AND fc.`vendor_id` IN ({$vendorFreightIds}) ";
		
}
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '' && $_POST['stockpileId'] != 0) {
    $stockpileId = $_POST['stockpileId'];
	$whereProperty .= " AND s.stockpile_id = {$stockpileId} ";
}

$sql = "SELECT t.transaction_date, s.stockpile_name, t.`slip_no`, t.`vehicle_no`, f.freight_supplier, v.vendor_name, fc.contract_pkhoa,
c.po_no, c.contract_no, t.`send_weight`, t.`netto_weight`, t.`quantity`, t.shrink, t.`freight_price`, ftx.tax_category, f.pph,
CASE WHEN f.freight_rule = 1 THEN ROUND((t.freight_price * t.send_weight),5) ELSE ROUND((t.freight_price * t.quantity),5) END AS total_oa
FROM `transaction` t
LEFT JOIN freight_cost fc ON fc.`freight_cost_id` = t.`freight_cost_id`
LEFT JOIN freight f ON f.`freight_id` = fc.`freight_id`
LEFT JOIN vendor v ON v.`vendor_id` = fc.`vendor_id`
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN tax ftx ON ftx.tax_id = f.pph_tax_id
WHERE 1=1 {$whereProperty}
AND t.freight_price > 0
ORDER BY t.transaction_id ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/oa-summary-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 
		 <input type="hidden" id="freightId" name="freightId" value="<?php echo $freightIds; ?>" />
		 <input type="hidden" id="vendorFreightId" name="vendorFreightId" value="<?php echo $vendorFreightIds; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
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
			<th>Nama Supplier</th>
            <th>Nama PKS</th>
			<th>No. Kontrak PKHOA</th>
			<th>No. PO</th>
            <th>No. Kontrak</th>
			<th>Berat Kirim</th>
			<th>Berat Netto</th>
			<th>Inventory</th>
			<th>Susut</th>
			<th>Harga OA</th>
			<th>DPP</th>
			
			
           
        </tr>
    </thead>
    <tbody>
	<?php
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
	<td><?php echo $row->freight_supplier; ?></td>
	<td><?php echo $row->vendor_name; ?></td>
	<td><?php echo $row->contract_pkhoa; ?></td>
	<td><?php echo $row->po_no; ?></td>
	<td><?php echo $row->contract_no; ?></td>
	<td><?php echo number_format($row->send_weight, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->netto_weight, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->shrink, 2, ".", ","); ?></td>
	
	
	<?php 
	if($row->tax_category == 1){
		$total_oa = $row->total_oa / ((100 - $row->pph)/100);
		$fp = $row->freight_price / ((100 - $row->pph)/100);
	}else{
		$total_oa = $row->total_oa;
		$fp = $row->freight_price;
	}
		
		$total = $total_oa - ($total_oa * ($row->pph/100));
	?>
	<td><?php echo number_format($fp, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_oa, 2, ".", ","); ?></td>
	
	
	<?php
	$dpp = $dpp + $total_oa;
	
	
	?>
	</tr>
	<?php
                $no++;
            }
        }
        ?>
	</tbody>
	<tfoot>
	<?php

	$sqlPph = "SELECT tx.tax_category, f.pph FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id WHERE freight_id = {$freightIds}";
                $resultPph = $myDatabase->query($sqlPph, MYSQLI_STORE_RESULT);   
                if($resultPph !== false && $resultPph->num_rows > 0) {
                    while($rowPph = $resultPph->fetch_object()) {
							
								$pph = $dpp * ($rowPph->pph/100);
							
					}
				}
	
	$grandTotal = $dpp - $pph;
	?>
	<tr>
	<td colspan="15" style="text-align: right;">Sub Total</td>
	<td style="text-align: right;"><?php  echo number_format($dpp, 2, ".", ","); ?></td>
	</tr>
	<tr>
	<td colspan="15" style="text-align: right;">PPh</td>
	<td style="text-align: right;"><?php echo number_format($pph, 2, ".", ","); ?></td>
	</tr>
	<tr>
	<td colspan="15" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($grandTotal, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
	</table>