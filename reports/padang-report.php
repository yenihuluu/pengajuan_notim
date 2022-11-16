<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$sumProperty = '';
$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$stockpileId = '';
$stockpileIds = '';
$whereProperty2='';
$vendorId = '';
$vendorIds = '';
$tquantity = 0;
$tfp = 0;
$tuc = 0;
$tfc_shrink = 0;
$tfc_total = 0;
$tppn = 0;
$tpph = 0;
$ttotal = 0;

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'VIEW NOTA TIMBANG REPORT',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileIds = $_POST['stockpileId'];

	/*for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }*/

	$stockpile_code = array();
    $sql = "SELECT stockpile_code FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_code[] = $row['stockpile_code'];

		$stockpile_codes =  "'" . implode("','", $stockpile_code) . "'";
		}
	}

    $whereProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpile_codes}) ";
    $sumProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpile_codes}) ";
}

/*if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'";
                        }
                    }

    $whereProperty2 .= " AND t.freight_cost_id IN (select freight_cost_id from freight_cost where freight_id IN ({$vendorIds})) ";

}*/

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
}

$sql = "SELECT t.`transaction_date`, t.`vehicle_no`, v.`vendor_name`, vh.`vehicle_name`, t.`quantity`, t.`freight_price`, t.freight_quantity, f.freight_rule, t.freight_cost_id,
ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
	WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
	WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
	WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
	ELSE 0 END,10) AS qtyClaim,
t.`unloading_price`, ts.`trx_shrink_claim` AS shrink_claim, ftx.tax_id AS fc_pph_id, ftx.tax_value AS fc_pph, ftx.tax_category AS fc_pph_category, fppn.tax_value AS fc_ppn, fppn.tax_id AS fc_ppn_id, uc.ob_padang
FROM TRANSACTION t
LEFT JOIN transaction_shrink_weight ts ON t.transaction_id = ts.transaction_id
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN unloading_cost uc ON uc.`unloading_cost_id` = t.`unloading_cost_id`
LEFT JOIN vehicle vh ON vh.`vehicle_id` = uc.`vehicle_id`
LEFT JOIN freight_cost fc ON fc.freight_cost_id = t.freight_cost_id
LEFT JOIN freight f ON f.freight_id = fc.freight_id
LEFT JOIN tax ftx ON ftx.tax_id = f.pph_tax_id
LEFT JOIN tax fppn ON fppn.tax_id = f.ppn_tax_id
WHERE t.`transaction_type` = 1 AND t.fc_payment_id IS NULL AND t.freight_price > 0
        {$whereProperty} ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//echo $sql;
?>

<form method="post" action="reports/padang-report-xls.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            
            <th>Transaction Date</th>
            <th>No. Pol</th>
            <th>Kendaraan</th>
			<th>Nama PKS</th>
            <th>Quantity</th>
			<th>Biaya Angkut (/Kg)</th>
            <th>Biaya Angkut</th>
			<th>Biaya Bongkar</th>
			<th>Klaim Susut</th>
			<th>Total Biaya Angkut</th>
			<th>PPN</th>
			<th>PPh</th>
			<th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {


            $no = 1;
            while($row = $result->fetch_object()) {
                
				//$pks_price = $row->pks_price;

				
					$quantity = $row->quantity;
				


				/*if($row->freight_rule == 1){
					$fp = $row->freight_quantity * $row->freight_price;
				}else{
					$fp = $row->freight_quantity * $row->freight_price;
				}*/

				

				if($row->freight_cost_id != 0 && $row->fc_pph_id != 0 && $row->fc_pph_category == 1){
					$fc = $row->freight_price / ((100 - $row->fc_pph) / 100);
					//$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim) / ((100 - $row->fc_pph) / 100);
					$fcTotal = ($fc * $row->freight_quantity);
					$uc = $row->ob_padang;
					$fc_total = ($fcTotal - $uc - $fc_shrink);
					$pph = $fc_total * ($row->fc_pph / 100);
					$ppn = $fc_total * ($row->fc_ppn / 100);
					$total = $fc_total + $ppn - $pph;
				}elseif($row->freight_cost_id != 0){
					$fc = $row->freight_price;
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
					$fcTotal = ($fc * $row->freight_quantity);
					$uc = $row->ob_padang;
					$fc_total = $fcTotal - $uc - $fc_shrink;
					$pph = $fc_total * ($row->fc_pph / 100);
					$ppn = $fc_total * ($row->fc_ppn / 100);
					$total = $fc_total + $ppn - $pph;
				}else{
					$fc = 0;
					$fc_shrink = 0;
					$fcTotal = 0;
					$ppn = 0;
					$uc = 0;
					$fc_total = 0;
					$pph = 0;
					$ppn = 0;
					$total = 0;
				}

				


                ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row->transaction_date; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td><?php echo $row->vehicle_name; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td style="text-align: right;"><?php echo number_format($quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($fc, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($fcTotal, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($uc, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($fc_shrink, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($fc_total, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($ppn, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($pph, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($total, 2, ".", ","); ?></td>
            
        </tr>
                <?php
				
				$tquantity = $tquantity + $quantity;
				$tfp = $tfp + $fcTotal;
				$tuc = $tuc + $uc;
				$tfc_shrink = $tfc_shrink + $fc_shrink;
				$tfc_total = $tfc_total + $fc_total;
				$tppn = $tppn + $ppn;
				$tpph = $tpph + $pph;
				$ttotal = $ttotal + $total;
                $no++;
            }
        }
        ?>
    </tbody>
	<tfoot>
	<tr>
	<td colspan = "5" style="text-align: right;">GRAND TOTAL</td>
	<td style="text-align: right;"><?php  echo number_format($tquantity, 2, ".", ","); ?></td>
	<td></td>
	<td style="text-align: right;"><?php  echo number_format($tfp, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($tuc, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($tfc_shrink, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($tfc_total, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($tppn, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($tpph, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($ttotal, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
</table>
