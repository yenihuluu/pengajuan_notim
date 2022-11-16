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

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'";
                        }
                    }

    $whereProperty2 .= " AND t.freight_cost_id IN (select freight_cost_id from freight_cost where freight_id IN ({$vendorIds})) ";

}

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

$sql = "SELECT t.*,
            DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
			DATE_FORMAT(t.modify_date, '%d %b %Y') AS modify_date,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,
            CASE WHEN t.transaction_type = 1 THEN con.po_no ELSE sh.shipment_code END AS po_no,
            CASE WHEN t.transaction_type = 1 THEN con.contract_no ELSE sl.sales_no END AS contract_no,
            CASE WHEN t.transaction_type = 1 THEN vh.vehicle_name ELSE t.vehicle_no END AS vehicle_name,
            CASE WHEN t.transaction_type = 1 THEN t.vehicle_no ELSE '' END AS vehicle_no,
            CASE WHEN t.transaction_type = 1 THEN DATE_FORMAT(t.unloading_date, '%d %b %Y') ELSE DATE_FORMAT(t.transaction_date, '%d %b %Y') END AS unloading_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code, f.freight_id, f.freight_rule,
            v1.vendor_name, hv.vendor_handling_id, hv.vendor_handling_name, hv.vendor_handling_rule, hv.pph_tax_id AS hc_pph_id, hv.pph AS hc_pph, hvtx.tax_category AS hc_pph_category,
            CASE WHEN t.transaction_type = 1 THEN v3.vendor_name ELSE cust.customer_name END AS supplier,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2,
			CASE WHEN t.mutasi_id IS NOT NULL THEN t.unit_price ELSE con.price_converted END AS pks_price,fc.price AS fc_price, ftx.tax_id AS fc_pph_id, ftx.tax_value AS fc_pph, ftx.tax_category AS fc_pph_category,
			CASE WHEN t.slip_retur LIKE '%-R' THEN t.unloading_price * -1 ELSE t.unloading_price END AS uc_price,
			utx.tax_value AS uc_pph, utx.tax_category AS uc_pph_category, u.user_name,
			CASE WHEN t.transaction_type = 1 THEN (SELECT shi.shipment_no FROM shipment shi LEFT JOIN delivery d ON d.shipment_id = shi.shipment_id WHERE d.transaction_id = t.transaction_id LIMIT 1 )
		 ELSE sh.shipment_no END AS shipment_no2,
		 fp.payment_no AS fPayment, up.payment_no AS uPayment, hp.payment_no AS hPayment,ts.`trx_shrink_claim` AS shrink_claim, 
               
			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
				
				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
				
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
                fc.contract_pkhoa, l.labor_name, cpd.contract_pks_detail_id, vc.vendor_curah_name as vendor_curah

        FROM TRANSACTION t
		LEFT JOIN transaction_shrink_weight ts
				ON t.transaction_id = ts.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
		LEFT JOIN contract_pks_detail cpd
            ON t.contract_pks_detail_id = cpd.contract_pks_detail_id
        LEFT JOIN vendor_curah vc
            ON vc.vendor_curah_id = cpd.vendor_curah_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
		LEFT JOIN tax ftx
	    	ON ftx.tax_id = t.fc_tax_id
		LEFT JOIN tax utx
	    	ON utx.tax_id = t.uc_tax_id
		LEFT JOIN USER u
			ON u.user_id = t.modify_by
		LEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling hv
			ON hv.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax hvtx ON hv.pph_tax_id = hvtx.tax_id
	LEFT JOIN payment fp ON fp.payment_id = t.fc_payment_id
	LEFT JOIN payment up ON up.payment_id = t.uc_payment_id
	LEFT JOIN payment hp ON hp.payment_id = t.hc_payment_id
	LEFT JOIN labor l ON l.labor_id = t.labor_id
        WHERE 1=1
        AND t.company_id = {$_SESSION['companyId']}
        {$whereProperty} {$whereProperty2} GROUP BY t.transaction_id ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//echo $sql;
?>
<!--
<form method="post" action="reports/complete-report-admin-xls.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    <input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $whereProperty2; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<form method="post" action="reports/print_notim_admin.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    <input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $whereProperty2; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download report</button>
</form>-->
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            <th>No. Slip</th>
            <th>Stockpile</th>
            <th>Transaction Date</th>
            <th>No. Pol</th>
            <th>Kendaraan</th>
            <th>Tanggal Muat</th>
            <th>Supplier Freight</th>
            <th>No. Surat Jalan</th>
            <th>No. PO</th>
            <th>Nama PKS</th>
            <th>Supplier/Customer</th>
            <th>No. Kontrak</th>
            <th>Type</th>
            <th>Berat Kirim</th>
            <th>Berat Bruto</th>
            <th>Berat Tarra</th>
            <th>Berat Netto</th>
			<th>Susut</th>
            <th>Total /kg</th>
            <th>Total</th>
            <th>Catatan</th>
            <th>Supir</th>
			<th>Biaya Angkut (/Kg)</th>
            <th>Biaya Angkut</th>
			<th>Klaim Susut</th>
			<th>Total Biaya Angkut</th>
            <th>Biaya Bongkar</th>
			<th>Vendor Handling</th>
			<th>Biaya Handling</th>
			<th>Biaya Handling (/kg)</th>
            <th>R</th>
			<th>R (Date)</th>
			<th>Slip Reference</th>
            <th>Type</th>
            <th>Inventory</th>

            <th>Balance (Q)</th>
			<th>Shipment Code</th>
			<th>Freight Payment</th>
			<th>Unloading Payment</th>
			<th>Handling Payment</th>
			<th>Vendor Bongkar</th>
			<th>Sumber PKS</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {


            if($boolBalanceBefore) {
                $sql2 = "SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2
                        FROM transaction t
                        LEFT JOIN stockpile_contract sc
                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                        WHERE 1=1 {$sumProperty}";
                $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

                if($result2->num_rows > 0) {
                    while($row2 = $result2->fetch_object()) {
                        $balanceBefore = $balanceBefore + $row2->quantity2;
                    }
                ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
			<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
			<td></td>
            <td></td>
			<td></td>
			<td></td>
            <td></td>
            <td></td>
			<td></td>
            <td style="text-align: right;"><?php echo number_format($balanceBefore, 0, ".", ","); ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
        </tr>
                <?php
                }
            }

            $balanceQuantity = $balanceBefore;
            $no = 1;
            while($row = $result->fetch_object()) {
                $balanceQuantity = $balanceQuantity + $row->quantity2;
				$pks_price = $row->pks_price;

				if($row->transaction_type == 2){
					if($row->quantity < 0){
						$quantity = $row->quantity * -1;
					}else{
						$quantity = '-' .$row->quantity;
					}
				}else{
					$quantity = $row->quantity;
				}

				if($row->contract_type2 == 'Curah' && $row->transaction_type == 1){
					$shrink = 0;
				}else{
					$shrink = $row->shrink;
				}

				if($row->freight_rule == 1){
					$fp = $row->freight_quantity * $row->freight_price;
				}else{
					$fp = $row->freight_quantity * $row->freight_price;
				}

				if($row->vendor_handling_rule == 1){
					$hp = $row->send_weight * $row->handling_price;
				}else{
					$hp = $row->quantity * $row->handling_price;
				}

				if($row->freight_cost_id != 0 && $row->fc_pph_id != 0 && $row->fc_pph_category == 1){
					$fc = $fp;
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim) / ((100 - $row->fc_pph) / 100);
					$fcTotal = $fc / ((100 - $row->fc_pph) / 100);
					$fc_total = $fcTotal - $fc_shrink;
				}elseif($row->freight_cost_id != 0){
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
					$fcTotal = $fp;
					$fc_total = $fp - $fc_shrink;
				}else{
					$fc_shrink = 0;
					$fcTotal = 0;
					$fc_total = 0;
				}

				if($row->handling_cost_id != 0 && $row->hc_pph_id != 0 && $row->hc_pph_category == 1){
					$hc = $hp;
					$hc_total = $hc / ((100 - $row->hc_pph) / 100);
				}elseif($row->handling_cost_id != 0){
					$hc_total = $hp;
				}else{
					$hc_total = 0;
				}

				if($row->unloading_cost_id != 0){
					$uc_total = $row->uc_price;
				}else{
					$uc_total = 0;
				}

			if($row->slip_no == 'DUM-000000092A'){
				$pks_price = 575;
			}


                ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->unloading_date2; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td><?php echo $row->vehicle_name; ?></td>
            <td><?php echo $row->loading_date2; ?></td>
            <td><?php echo $row->freight_code; ?></td>
            <td><?php echo $row->permit_no; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->supplier; ?></td>
            <td><?php echo $row->contract_no; ?></td>
            <td><?php echo $row->transaction_type2; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->bruto_weight, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->tarra_weight, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->netto_weight, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($shrink, 2, ".", ","); ?></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;"></td>
            <td><?php echo $row->notes; ?></td>
            <td><?php echo $row->driver; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->freight_price, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($fcTotal, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($fc_shrink, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($fc_total, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($uc_total, 2, ".", ","); ?></td>
			<td><?php echo $row->vendor_handling_name; ?></td>
            <td style="text-align: right;"><?php echo number_format($hc_total, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->handling_price, 2, ".", ","); ?></td>
            <td><?php echo $row->user_name; ?></td>
			<td><?php echo $row->modify_date; ?></td>
			<td><?php echo $row->slip_retur; ?></td>
            <td><?php echo $row->contract_type2; ?></td>
            <td style="text-align: right;"><?php echo number_format($quantity, 2, ".", ","); ?></td>

            <td style="text-align: right;"><?php echo number_format($balanceQuantity, 2, ".", ","); ?></td>
			<td><?php echo $row->shipment_no2; ?></td>
			<td> <?php echo $row->fPayment; ?></td>
			<td> <?php echo $row->uPayment; ?></td>
			<td> <?php echo $row->hPayment; ?></td>
			<td> <?php echo $row->labor_name; ?></td>
			<?php if($row->contract_pks_detail_id != ''){?>
                    <td> <?php echo $row->vendor_curah; ?></td>
            <?php } else { ?>
                    <td><?php echo $row->vendor_name; ?></td>
            <?php } ?>
        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>
