<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$shipmentId = '';
//$stockpileId = '';

if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != '') {
    $shipmentId = $_POST['shipmentId'];
    
    $whereProperty = " AND sh.sales_id = {$shipmentId} ";
}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";    
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    
    $whereProperty = " AND s.stockpile_id = {$stockpileId} ";
}*/
/*
$sql3="SELECT c.contract_id, c.po_no, c.contract_no FROM contract c 
LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id`
LEFT JOIN `transaction` t ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN delivery d ON t.`transaction_id` = d.`transaction_id`
LEFT JOIN shipment sh ON d.`shipment_id` = sh.`shipment_id`
WHERE sh.`sales_id` = {$shipmentId} AND c.`contract_status` = 0";
$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
if($result3 != false && $result3->num_rows == 0) {
	*/

$sql2 = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
            t.slip_no, 
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name, 
            sh.shipment_code,
            t.send_weight, t.netto_weight, d.quantity,
			CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT AND t.adjustmentAudit_id <> 0 NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
            CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id <> 0 THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, 
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.freight_quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
            t.unloading_price, (d.percent_taken / 100) * t.unloading_price AS unloading_total,
			vhc.price AS vh_price, t.handling_quantity,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END AS handling_total,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
            f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
            t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			l.labor_id,t.freight_cost_id, f.freight_id,
			(SELECT slip_no FROM TRANSACTION WHERE shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
			(SELECT SUBSTRING(slip_no,1,3) FROM TRANSACTION WHERE shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
			(SELECT vehicle_no FROM transaction WHERE shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name
                     FROM delivery d
        LEFT JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
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
            ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
	        ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
	        ON fctxppn.tax_id = f.ppn_tax_id
	    LEFT JOIN labor l
            ON l.labor_id = t.labor_id
	    LEFT JOIN tax uctxpph
	        ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
	        ON uctxppn.tax_id = l.ppn_tax_id	
		lEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        {$whereProperty} ORDER BY d.`transaction_id` ASC";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

?>
<form method="post" action="reports/cogs-report-xls.php">
    <input type="hidden" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId; ?>" />
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
	<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
 
<table class="table table-bordered table-striped" style="font-size: 8pt;">
<thead><tr>
<th>Vessel Name</th>
<th>Begining Balance</th>
<th>Total Quantity</th>
<th>Ending Balance</th>
<th>Total COGS PKS Amount</th>
<th>Total Freight Cost</th>
<th>Total Unloading Cost</th>
<th>Total Handling Cost</th>
<th>Total COGS</th>
</tr></thead>
<tbody>
<?php
         if($result2->num_rows > 0) {
            
            while($row2 = $result2->fetch_object()) {
			$value='';
			$no1=1;
			
			$slipOut = $row2->slipOut;
			$slipOutCode = $row2->slipOutCode;
				
				if($row2->slip_no >= 'SAM-0000000001' && $row2->slip_no <= 'SAM-0000001925'){
				$fc_pph2 = 4 ;
			}else{
				$fc_pph2	 = $row2->fc_pph;
			}
			
			if($row2->slip_no >= 'MAR-0000000001' && $row2->slip_no <= 'MAR-0000007138'){
				$fc_pph2 = 4 ;
			}else{
				$fc_pph2	 = $row2->fc_pph;
			}
			
			if($row2->vh_pph_tax_category == 1 && $row2->vh_pph_tax_id != ''){
			         $pphvh2 = ($row2->handling_total / ((100 - $row2->vh_pph) / 100)) - $row2->handling_total;
				 
				 }elseif($row2->vh_pph_tax_category == 0 && $row2->vh_pph_tax_id != ''){
					  $pphvh2 =  0;  
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphvh2 = 0;
				 }
				 
				 $handlingTotal2 = $row2->handling_total - $pphvh2;	
				
               
		         if($row2->fc_pph_tax_category == 1 && $row2->fc_pph_tax_id != ''){
			         $pphfc2 = ($row2->freight_total / ((100 - $fc_pph2) / 100)) - $row2->freight_total;
					 $pphfcShrink2 = ($row2->freight_shrink / ((100 - $fc_pph2) / 100)) - $row2->freight_shrink;
				 
				 }elseif($row2->fc_pph_tax_category == 0 && $row2->fc_pph_tax_id != ''){
					  $pphfc2 =  0;
						$pphfcShrink2 = 0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc2 = 0;
					$pphfcShrink2 = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/
				 
				 $freightTotal2 = ($row2->freight_total + $ppnfc2 + $pphfc2) - ($row2->freight_shrink + $pphfcShrink2);	
				 
				 
				 if($row2->uc_pph_tax_category == 1 &&$row2->uc_pph_tax_id != ''){
			         $pphuc2 = ($row2->unloading_total / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_total;
					 
				 }elseif($row2->uc_pph_tax_category == 0 && $row2->uc_pph_tax_id != ''){
					 $pphuc2 =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc2 = 0;
				 }
				 
				 
				 $unloadingTotal2 = $row2->unloading_total + $ppnuc2 + $pphuc2;	
    
     $totalCogs2 = $row2->cogs_amount + $freightTotal2 + $unloadingTotal2 + $handlingTotal2;
	 
	 $quantity_total= $row2->quantity;
	 $total_quantity = $quantity_total+$total_quantity;
	 
	 $pks_total = $row2->cogs_amount;
	 $total_pks = $pks_total+$total_pks;
	 
	 $fc_total = $freightTotal2;
	 $total_fc = $fc_total+$total_fc;
	 
	 $vh_total = $handlingTotal2;
	 $total_vh = $vh_total+$total_vh;
	 
	 $uc_total = $unloadingTotal2;
	 $total_uc = $uc_total+$total_uc;
	 
	 $cogs_total = $totalCogs2;
	 $total_cogs = $cogs_total+$total_cogs;
	  $vessel_name = $row2->vessel_name;
	  
	  $no1++;
}}

$sql = "SELECT
ROUND(SUM(CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.`quantity` END) -
	SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2) AS qty_available
FROM `transaction` t 
WHERE 1=1 AND t.slip_no < '{$slipOut}' AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}'";
    		$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
			if($result !== false && $result->num_rows == 1) {
				 $row = $result->fetch_object();
		
			    $begining = $row->qty_available;
				$ending = $begining - $total_quantity;
			}
    ?>
			

<tr>
<td><?php echo $vessel_name; ?></td>
<td><?php echo number_format($begining, 2, ".", ","); ?></td>
<td><?php echo number_format($total_quantity, 2, ".", ","); ?></td>
<td><?php echo number_format($ending, 2, ".", ","); ?></td>
<td><?php echo number_format($total_pks, 2, ".", ","); ?></td>
<td><?php echo number_format($total_fc, 2, ".", ","); ?></td>
<td><?php echo number_format($total_uc, 2, ".", ","); ?></td>
<td><?php echo number_format($total_vh, 2, ".", ","); ?></td>
<td><?php echo number_format($total_cogs, 2, ".", ","); ?></td>

</tr>

</tbody>
</table>
<?php
$sql = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
            t.slip_no, 
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name, 
            sh.shipment_no,
            t.send_weight, t.netto_weight, d.quantity, 
			CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id <> 0 THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
            CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id <> 0 THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, 
            CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.freight_quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
            t.unloading_price,
            (d.percent_taken / 100) * t.unloading_price AS unloading_total,
			vhc.price AS vh_price, t.handling_quantity,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END AS handling_total,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
            f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
            t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			l.labor_id,t.freight_cost_id, f.freight_id
                     FROM delivery d
        INNER JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
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
            ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
	        ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
	        ON fctxppn.tax_id = f.ppn_tax_id
	    LEFT JOIN labor l
            ON l.labor_id = t.labor_id
	    LEFT JOIN tax uctxpph
	        ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
	        ON uctxppn.tax_id = l.ppn_tax_id	
		lEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        {$whereProperty} ORDER BY t.slip_no ASC, d.`transaction_id` ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">Area</th>
            <th rowspan="2">Transaction Date</th>
            <th rowspan="2">Slip No</th>
            <th rowspan="2">Purchase Type</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">SUPPLIER NO</th>
            <th rowspan="2">SUPPLIER NAME</th>
            <th rowspan="2">PKS SOURCE</th>
            <th rowspan="2">SHIPMENT CODE</th>
            <th colspan="6">Product (PKS)</th>
            <th rowspan="2">FREIGHT COST</th>
            <th rowspan="2">UNLOADING COST</th>
			<th rowspan="2">HANDLING COST</th>
            <th rowspan="2">TOTAL COGS</th>
        </tr>
        <tr>
            <th>Berat Kirim (kg)</th>
            <th>Berat Netto (kg)</th>
            <th>Inventory (kg)</th>
            <th>Berat Susut (kg)</th>
            <th>Price / kg</th>
            <th>COGS PKS Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
            
            while($row = $result->fetch_object()) {
				
				if($row->slip_no >= 'SAM-0000000001' && $row->slip_no <= 'SAM-0000001925'){
				$fc_pph = 4 ;
			}else{
				$fc_pph	 = $row->fc_pph;
			}
			
			if($row->slip_no >= 'MAR-0000000001' && $row->slip_no <= 'MAR-0000007138'){
				$fc_pph = 4 ;
			}else{
				$fc_pph	 = $row->fc_pph;
			}
				
		 if($row->vh_pph_tax_category == 1 && $row->vh_pph_tax_id != ''){
			         $pphvh = ($row2->handling_total / ((100 - $row->vh_pph) / 100)) - $row->handling_total;
				 
				 }elseif($row->vh_pph_tax_category == 0 && $row->vh_pph_tax_id != ''){
					  $pphvh =  0;  
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphvh = 0;
				 }
				 
				 $handlingTotal = $row->handling_total - $pphvh;
               
		        if($row->fc_pph_tax_category == 1 && $row->fc_pph_tax_id != ''){
			         $pphfc = ($row->freight_total / ((100 - $fc_pph) / 100)) - $row->freight_total;
					 $pphfcShrink = ($row->freight_shrink / ((100 - $fc_pph) / 100)) - $row->freight_shrink;
				 
				 }elseif($row->fc_pph_tax_category == 0 && $row->fc_pph_tax_id != ''){
					  $pphfc =  0;
						$pphfcShrink = 0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc = 0;
					$pphfcShrink = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/
				 
				 
				 
				 $freightTotal = ($row->freight_total + $ppnfc + $pphfc) - ($row->freight_shrink + $pphfcShrink);
				 
				 
				 if($row->uc_pph_tax_category == 1 && $row->uc_pph_tax_id != ''){
			         $pphuc = ($row->unloading_total / ((100 - $row->uc_pph) / 100)) - $row->unloading_total;
					 
				 }elseif($row->uc_pph_tax_category == 0 && $row->uc_pph_tax_id != ''){
					 $pphuc =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc = 0;
				 }
				 
				 
				 $unloadingTotal = $row->unloading_total + $ppnuc + $pphuc;	
    
     $totalCogs = $row->cogs_amount + $freightTotal + $unloadingTotal + $handlingTotal;
    
                ?>
        <tr>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->transaction_date2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->contract_type2; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->freight_code; ?></td>
            <td><?php echo $row->supplier; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->shipment_no; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td></td>
            <td style="text-align: right;"><?php echo number_format($row->price_converted, 4, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->cogs_amount, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($freightTotal, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($unloadingTotal, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($handlingTotal, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($totalCogs, 2, ".", ","); ?></td>
        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>
<?php
//}else{
	?>
<!--<table class="table table-bordered table-striped" style="font-size: 8pt;">
<thead><tr>
<th>No PO</th>
<th>No Kontrak</th>
<th>Status</th>
</tr></thead>
<?php
//while($row3 = $result3->fetch_object()) {
	?>
<tbody>
<tr>

            <td><?php //echo $row3->po_no; ?></td>
            <td><?php //echo $row3->contract_no; ?></td>
            <td><?php //echo "PLEASE LOCK CONTRACT"; ?></td>
           
        </tr>
                <?php
               // $no++;
       //    }
//}
        ?>
    </tbody>
</table> --!>           