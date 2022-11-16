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

$freightId = '';

$vendorFreightId = '';



if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty5 .= " AND edit_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	$whereProperty5 .= " AND edit_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty5 .= " AND edit_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	 $whereProperty4 .= " AND date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	$whereProperty4 .= " AND date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$whereProperty4 .= " AND date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
if(isset($_POST['freightId']) && $_POST['freightId'] != '' && $_POST['freightId'] != 0) {
    $freightId = $_POST['freightId'];
    
	/*for ($i = 0; $i < sizeof($freightId); $i++) {
                        if($freightIds == '') {
                            $freightIds .= $freightId[$i];
                        } else {
                            $freightIds .= ','. $freightId[$i];
                        }
                    }*/
	$whereProperty .= " AND fc.`freight_id` IN ({$freightId}) ";
		
}

					
if(isset($_POST['vendorFreightId']) && $_POST['vendorFreightId'] != '' && $_POST['vendorFreightId'] != 0) {
    $vendorFreightId = $_POST['vendorFreightId'];
	
	/*for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
                        if($vendorFreightIds == '') {
                            $vendorFreightIds .= $vendorFreightId[$i];
                        } else {
                            $vendorFreightIds .= ','. $vendorFreightId[$i];
                        }
                    }*/
					
    $whereProperty .= " AND fc.`vendor_id` IN ({$vendorFreightId}) ";
		
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
					
	$whereProperty .= " AND s.stockpile_name IN ('{$stockpileId}') ";
	$whereProperty4 .= " AND analytic_account IN ('{$stockpileId}') ";
}



$sql = "SELECT t.transaction_date, s.stockpile_name, t.`slip_no`, t.`vehicle_no`, f.freight_supplier, v.vendor_name, fc.contract_pkhoa,
c.po_no, c.contract_no, t.`send_weight`, t.`netto_weight`, t.`quantity`, t.`freight_price`, ftx.tax_category, f.pph,
CASE WHEN f.freight_rule = 1 THEN ROUND((t.freight_price * t.send_weight),5) ELSE ROUND((t.freight_price * t.quantity),5) END AS total_oa, 
(SELECT t2.fc_payment_id FROM jatim_inventory.`transaction` t2 LEFT JOIN jatim_inventory.payment p ON p.payment_id = t2.fc_payment_id WHERE t2.transaction_id = t.`transaction_id` AND p.payment_status = 0 AND p.payment_method = 1 {$whereProperty2}) AS fc_payment_id,
(SELECT payment_date FROM jatim_inventory.payment WHERE payment_id = t.fc_payment_id AND payment_status = 0 AND payment_method = 1 {$whereProperty3}) AS payment_date, ts.amt_claim
FROM jatim_inventory.`transaction` t
LEFT JOIN jatim_inventory.freight_cost fc ON fc.`freight_cost_id` = t.`freight_cost_id`
LEFT JOIN jatim_inventory.freight f ON f.`freight_id` = fc.`freight_id`
LEFT JOIN jatim_inventory.vendor v ON v.`vendor_id` = fc.`vendor_id`
LEFT JOIN jatim_inventory.stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN jatim_inventory.contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN jatim_inventory.stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN jatim_inventory.tax ftx ON ftx.tax_id = f.pph_tax_id
LEFT JOIN jatim_inventory.`transaction_shrink_weight` ts ON ts.transaction_id = t.transaction_id
WHERE 1=1 {$whereProperty}
AND (SELECT t2.fc_payment_id FROM jatim_inventory.`transaction` t2 LEFT JOIN jatim_inventory.payment p ON p.payment_id = t2.fc_payment_id WHERE t2.transaction_id = t.`transaction_id` {$whereProperty2}) IS NULL
AND (SELECT payment_date FROM jatim_inventory.payment WHERE payment_id = t.fc_payment_id AND payment_status = 0 AND payment_method = 1 {$whereProperty3}) IS NULL
AND t.freight_price > 0

AND t.freight_cost_id IS NOT NULL
AND t.sync_status != 11
AND t.adj_oa IS NULL
UNION ALL
SELECT DATE AS transaction_date, analytic_account AS stockpile_name, slip_number AS slip_no, '' AS vehicle_no, partner AS freight_supplier, '' AS vendor_name, '' AS contract_pkhoa,
'' AS po_no, '' AS contract_no, '' AS send_weight, '' AS netto_weight, '' AS quantity, '' AS freight_price, '' AS tax_category, '' AS pph, SUM(credit-debit) AS total_oa, '' AS fc_payment_id, '' AS payment_date, '' AS amt_claim
FROM jatim_gl.`hdoadetail`
WHERE journal_entry  IN
(
'BEN-18-0000002736',
'PAD-18-0000007375',
'PAD-18-0000007661',
'PAD-18-0000007806',
'PAD-18-0000007910',
'PAD-18-0000008434',
'PAD-18-0000008563',
'PAD-18-0000008760',
'PAD-18-0000009659',
'PAD-18-0000009660',
'PAD-18-0000009673',
'PAD-18-0000010529',
'PAD-18-0000013624',
'BEN-18-0000002722',
'172',
'173',
'360',
'BUT-18-0000007406',
'BUT-18-0000007452',
'BUT-18-0000007989',
'BUT-18-0000008172',
'BUT-18-0000009118'
) {$whereProperty4} GROUP BY journal_entry
ORDER BY transaction_date ASC 
";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/hdoa-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		  <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    	 <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
		 <input type="hidden" id="freightId" name="freightId" value="<?php echo $freightId; ?>" />
		 <input type="hidden" id="vendorFreightId" name="vendorFreightId" value="<?php echo $vendorFreightId; ?>" />
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
			<th>Harga OA</th>
			<th>DPP</th>
			<th>Total Susut DPP</th>
			<th>Total DPP</th>
			<th>Total Biaya Angkut</th>
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
	<td><?php echo number_format($row->freight_price, 2, ".", ","); ?></td>
	
	<?php 
	if($row->tax_category == 1){
		$total_oa = $row->total_oa / ((100 - $row->pph)/100);
		$total_susut = $row->amt_claim / ((100 - $row->pph)/100);
		$TotalDPP = $total_oa - $total_susut;
	}else{
		$total_oa = $row->total_oa;
		$total_susut = $row->amt_claim;
		$TotalDPP = $total_oa - $total_susut;
	}
		
		$totalOA = $total_oa - ($total_oa * ($row->pph/100));
		$totalSusut = $total_susut - ($total_susut * ($row->pph/100));
		$total = $totalOA - $totalSusut;
	?>
	<td><?php echo number_format($total_oa, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_susut, 2, ".", ","); ?></td>
	<td><?php echo number_format($TotalDPP, 2, ".", ","); ?></td>
	<td><?php echo number_format($total, 2, ".", ","); ?></td>
	
	<?php
	$dpp = $dpp + $total;
	$dppTotal = $dppTotal + $TotalDPP;
	
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
	<td colspan="16" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($dppTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($dpp, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
	</table>