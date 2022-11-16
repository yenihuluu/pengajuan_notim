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
            t.send_weight, t.netto_weight, d.quantity, d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified,
			CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
           CASE WHEN d.qty_rsb <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.qty_rsb * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN d.qty_rsb * t.unit_price
			ELSE d.qty_rsb * con.price_converted END )ELSE 0 END AS cogs_amountR,
		   CASE WHEN d.qty_ggl <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.qty_ggl * t.unit_cost
				WHEN t.adjustmentAudit_id IS NOT NULL THEN d.qty_ggl * t.unit_price
				ELSE d.qty_ggl * con.price_converted END )ELSE 0 END AS cogs_amountG,
		   CASE WHEN d.qty_rsb_ggl <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.qty_rsb_ggl * t.unit_cost
				WHEN t.adjustmentAudit_id IS NOT NULL THEN d.qty_rsb_ggl * t.unit_price
				ELSE d.qty_rsb_ggl * con.price_converted END )ELSE 0 END AS cogs_amountRG,
		   CASE WHEN d.qty_uncertified <> 0 THEN(CASE WHEN t.mutasi_id IS NOT NULL AND t.mutasi_id <> 0 THEN d.quantity * t.unit_cost
				WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
				ELSE d.quantity * con.price_converted END )ELSE 0 END AS cogs_amountUN,
				t.freight_quantity, t.freight_price, 
			CASE WHEN d.qty_rsb <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalR,
			CASE WHEN d.qty_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalG,
			CASE WHEN d.qty_rsb_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalRG,
			CASE WHEN d.qty_uncertified <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
				ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
			END ) ELSE 0 END AS freight_totalUN,
			CASE WHEN d.qty_rsb <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.qty_rsb/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkR,
			CASE WHEN d.qty_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.qty_ggl/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkG,
			CASE WHEN d.qty_rsb_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.qty_rsb_ggl/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkRG,
			CASE WHEN d.qty_uncertified <> 0 THEN (CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
			 WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END 
			) ELSE 0 END AS freight_shrinkRN,
				t.unloading_price, 
				CASE WHEN d.qty_rsb <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalR,
				CASE WHEN d.qty_ggl <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalG,
				CASE WHEN d.qty_rsb_ggl <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalRG,
				CASE WHEN d.qty_uncertified <> 0 THEN ((d.percent_taken / 100) * t.unloading_price) ELSE 0 END AS unloading_totalUN,
				vhc.price AS vh_price, t.handling_quantity,
			CASE WHEN d.qty_rsb <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END 
			 ) ELSE 0 END AS handling_totalR,
			 CASE WHEN d.qty_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END 
			 ) ELSE 0 END AS handling_totalG,
			CASE WHEN d.qty_rsb_ggl <> 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END 
			 ) ELSE 0 END AS handling_totalRG,
			  CASE WHEN d.qty_uncertified > 0 THEN (CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			 ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END 
			 ) ELSE 0 END AS handling_totalUN,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
				f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
				t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
				l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
				l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
				l.labor_id,t.freight_cost_id, f.freight_id,
		(SELECT slip_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
		(SELECT transaction_date FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS transactionDate,
		(SELECT SUBSTRING(slip_no,1,3) FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
		(SELECT vehicle_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name, sh.shipment_no
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
		LEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']} 
        {$whereProperty} ORDER BY d.`transaction_id` ASC";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
//echo $sql2;

?>
<div class="row-fluid">
	<div class="span2 lightblue">
		<form method="post" action="reports/cogs-report-xls.php">
			<input type="hidden" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId; ?>" />
			<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
			<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
			<input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
			<button class="btn btn-success">Download XLS</button>
		</form>
	</div>
	<div class="span2 lightblue">
		<form method="post" action="reports/new-cogs-report-xls.php">
			<input type="hidden" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId; ?>" />
			<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
			<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
			<input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
			<button class="btn btn-success">Download XLS2</button>
		</form>
	</div>
</div>
 
<table class="table table-bordered table-striped" style="font-size: 8pt;">
<thead><tr>
<th>Certification</th>
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
$AmountTotalQty = 0; $AmountTotalPKS = 0; $AmountTotalFC = 0; $AmountTotalVH = 0; $AmountTotalUC = 0; $AmountTotalCOGS = 0;

            while($row2 = $result2->fetch_object()) {
			$value='';
			$no1=1;
			
			$slipOut = $row2->slipOut;
			$shipmentNo = $row2->shipment_no;
			$slipOutCode = $row2->slipOutCode;
			$transactionDate = $row2->transactionDate;
				
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
			        // $pphvh2 = ($row2->handling_total / ((100 - $row2->vh_pph) / 100)) - $row2->handling_total;
					 $pphvh2R = ($row2->handling_totalR / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalR;
					 $pphvh2G = ($row2->handling_totalG / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalG;
					 $pphvh2RG = ($row2->handling_totalRG / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalRG;
					 $pphvh2UN = ($row2->handling_totalUN / ((100 - $row2->vh_pph) / 100)) - $row2->handling_totalUN;
				 
				 }elseif($row2->vh_pph_tax_category == 0 && $row2->vh_pph_tax_id != ''){
					 // $pphvh2 =  0;  
					  $pphvh2R =  0;
					  $pphvh2G =  0;
					  $pphvh2RG =  0;
					  $pphvh2UN =  0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	 $pphvh2R =  0;
					 $pphvh2G =  0;
					 $pphvh2RG =  0;
					 $pphvh2UN =  0;
				 }
				 
				 //$handlingTotal2 = $row2->handling_total - $pphvh2;	
				 $handlingTotal2R = $row2->handling_totalR - $pphvh2R;	
				 $handlingTotal2G = $row2->handling_totalG - $pphvh2G;	
				 $handlingTotal2RG = $row2->handling_totalRG - $pphvh2RG;	
				 $handlingTotal2UN = $row2->handling_totalUN - $pphvh2UN;	
				
               
		         if($row2->fc_pph_tax_category == 1 && $row2->fc_pph_tax_id != ''){
			        // $pphfc2 = ($row2->freight_total / ((100 - $fc_pph2) / 100)) - $row2->freight_total;
					 $pphfc2R = ($row2->freight_totalR / ((100 - $fc_pph2) / 100)) - $row2->freight_totalR;
					 $pphfc2G = ($row2->freight_totalG / ((100 - $fc_pph2) / 100)) - $row2->freight_totalG;
					 $pphfc2RG = ($row2->freight_totalRG / ((100 - $fc_pph2) / 100)) - $row2->freight_totalRG;
					 $pphfc2UN = ($row2->freight_totalUN / ((100 - $fc_pph2) / 100)) - $row2->freight_totalUN;
					 
					 //$pphfcShrink2 = ($row2->freight_shrink / ((100 - $fc_pph2) / 100)) - $row2->freight_shrink;
					 $pphfcShrink2R = ($row2->freight_shrinkR / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkR;
					 $pphfcShrink2G = ($row2->freight_shrinkG / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkG;
					 $pphfcShrink2RG = ($row2->freight_shrinkRG / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkRG;
					 $pphfcShrink2UN = ($row2->freight_shrinkUN / ((100 - $fc_pph2) / 100)) - $row2->freight_shrinkUN;
				 
				 }elseif($row2->fc_pph_tax_category == 0 && $row2->fc_pph_tax_id != ''){
					  $pphfc2R =  0;
						$pphfcShrink2R = 0;
						 $pphfc2G =  0;
						$pphfcShrink2G = 0;
						$pphfc2RG =  0;
						$pphfcShrink2RG = 0;
						$pphfc2UN =  0;
						$pphfcShrink2UN = 0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc2R = 0;
					$pphfcShrink2R = 0;
					$pphfc2G = 0;
					$pphfcShrink2G = 0;
					$pphfc2RG = 0;
					$pphfcShrink2RG = 0;
					$pphfc2UN = 0;
					$pphfcShrink2UN = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/
				 
				// $freightTotal2 = ($row2->freight_total + $ppnfc2 + $pphfc2) - ($row2->freight_shrink + $pphfcShrink2);	
				 $freightTotal2R = ($row2->freight_totalR + $ppnfc2R + $pphfc2R) - ($row2->freight_shrinkR + $pphfcShrink2R);
				$freightTotal2G = ($row2->freight_totalG + $ppnfc2G + $pphfc2G) - ($row2->freight_shrinkG + $pphfcShrink2G);
				$freightTotal2RG = ($row2->freight_totalRG + $ppnfc2RG + $pphfc2RG) - ($row2->freight_shrinkRG + $pphfcShrink2RG);	
				$freightTotal2UN = ($row2->freight_totalUN + $ppnfc2UN + $pphfc2UN) - ($row2->freight_shrinkUN + $pphfcShrink2UN);					
				 
				 
				 if($row2->uc_pph_tax_category == 1 &&$row2->uc_pph_tax_id != ''){
			         //$pphuc2 = ($row2->unloading_total / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_total;
					 $pphuc2R = ($row2->unloading_totalR / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalR;
					 $pphuc2G = ($row2->unloading_totalG / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalG;
					 $pphuc2RG = ($row2->unloading_totalRG / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalRG;
					 $pphuc2UN = ($row2->unloading_totalUN / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_totalUN;
					 
				 }elseif($row2->uc_pph_tax_category == 0 && $row2->uc_pph_tax_id != ''){
					// $pphuc2 =  0;
					 $pphuc2R =  0;
					 $pphuc2G =  0;
					 $pphuc2RG =  0;
					 $pphuc2RG =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc2R =  0;
					$pphuc2G =  0;
					$pphuc2RG =  0;
					$pphuc2RG =  0;
				 }
				 
				// $unloadingTotal2 = $row2->unloading_total + $ppnuc2 + $pphuc2;
				$unloadingTotal2R = $row2->unloading_totalR + $ppnuc2R + $pphuc2R;
				$unloadingTotal2G = $row2->unloading_totalG + $ppnuc2G + $pphuc2G;
				$unloadingTotal2RG = $row2->unloading_totalRG + $ppnuc2RG + $pphuc2RG;
				$unloadingTotal2UN = $row2->unloading_totalUN + $ppnuc2UN + $pphuc2UN;					
    
    // $totalCogs2 = $row2->cogs_amount + $freightTotal2 + $unloadingTotal2 + $handlingTotal2;
	 $totalCogs2R = $row2->cogs_amountR + $freightTotal2R + $unloadingTotal2R + $handlingTotal2R;
	 $totalCogs2G = $row2->cogs_amountG + $freightTotal2G + $unloadingTotal2G + $handlingTotal2G;
	 $totalCogs2RG = $row2->cogs_amountRG + $freightTotal2RG + $unloadingTotal2RG + $handlingTotal2RG;
	 $totalCogs2UN = $row2->cogs_amountUN + $freightTotal2UN + $unloadingTotal2UN + $handlingTotal2UN;
	 
	 //$quantity_total= $row2->quantity;
	 $quantity_totalR= $row2->qty_rsb;
	 $quantity_totalG= $row2->qty_ggl;
	 $quantity_totalRG= $row2->qty_rsb_ggl;
	 $quantity_totalUN= $row2->qty_uncertified;
	 
	 //$total_quantity = $quantity_total+$total_quantity;
	  $total_quantityR = $total_quantityR + $quantity_totalR;
	  $total_quantityG = $total_quantityG + $quantity_totalG;
	  $total_quantityRG = $total_quantityRG + $quantity_totalRG;
	  $total_quantityUN = $total_quantityUN + $quantity_totalUN;
	 
	// $pks_total = $row2->cogs_amount;
	// $total_pks = $pks_total+$total_pks;
	$pks_totalR = $row2->cogs_amountR;
	$total_pksR = $total_pksR + $pks_totalR;
	$pks_totalG = $row2->cogs_amountG;
	$total_pksG = $total_pksG + $pks_totalG;
	$pks_totalRG = $row2->cogs_amountRG;
	$total_pksRG = $total_pksRG + $pks_totalRG;
	$pks_totalUN = $row2->cogs_amountUN;
	$total_pksUN = $total_pksUN + $pks_totalUN;
	 
	// $fc_total = $freightTotal2;
	// $total_fc = $fc_total+$total_fc;
	 $fc_totalR = $freightTotal2R;
	 $total_fcR = $fc_totalR+$total_fcR;
	 $fc_totalG = $freightTotal2G;
	 $total_fcG = $fc_totalG + $total_fcG;
	 $fc_totalRG = $freightTotal2RG;
	 $total_fcRG = $fc_totalRG + $total_fcRG;
	 $fc_totalUN = $freightTotal2UN;
	 $total_fcUN = $fc_totalUN + $total_fcUN;
	 
	// $vh_total = $handlingTotal2;
	// $total_vh = $vh_total+$total_vh;
	$vh_totalR = $handlingTotal2R;
	 $total_vhR = $vh_totalR + $total_vhR;
	 $vh_totalG = $handlingTotal2G;
	 $total_vhG = $vh_totalG + $total_vhG;
	 $vh_totalRG = $handlingTotal2RG;
	 $total_vhRG = $vh_totalRG + $total_vhRG;
	 $vh_totalUN = $handlingTotal2UN;
	 $total_vhUN = $vh_totalUN + $total_vhUN;
	 
	 //$uc_total = $unloadingTotal2;
	 //$total_uc = $uc_total+$total_uc;
	  $uc_totalR = $unloadingTotal2R;
	 $total_ucR = $uc_totalR + $total_ucR;
	 $uc_totalG = $unloadingTotal2G;
	 $total_ucG = $uc_totalG + $total_ucG;
	 $uc_totalRG = $unloadingTotal2RG;
	 $total_ucRG = $uc_totalRG + $total_ucRG;
	 $uc_totalUN = $unloadingTotal2UN;
	 $total_ucUN = $uc_totalUN + $total_ucUN;
	 
	// $cogs_total = $totalCogs2;
	// $total_cogs = $cogs_total+$total_cogs;
	$cogs_totalR = $totalCogs2R;
	 $total_cogsR = $cogs_totalR + $total_cogsR;
	 $cogs_totalG = $totalCogs2G;
	 $total_cogsG = $cogs_totalG + $total_cogsG;
	 $cogs_totalRG = $totalCogs2RG;
	 $total_cogsRG = $cogs_totalRG + $total_cogsRG;
	 $cogs_totalUN = $totalCogs2UN;
	 $total_cogsUN = $cogs_totalUN + $total_cogsUN;
	 
	 $vessel_name = $row2->vessel_name;
	  
	  $no1++;
}

$AmountTotalQty = $total_quantityR + $total_quantityG + $total_quantityRG + $total_quantityUN;
$AmountTotalPKS = $total_pksR + $total_pksG + $total_pksRG + $total_pksUN ;
$AmountTotalFC = $total_fcR + $total_fcG + $total_fcRG + $total_fcUN;
$AmountTotalVH = $total_vhR + $total_vhG + $total_vhRG + $total_vhUN;
$AmountTotalUC = $total_ucR + $total_ucG + $total_ucRG + $total_ucUN;
$AmountTotalCOGS = $total_cogsR + $total_cogsG + $total_cogsRG + $total_cogsUN;

}

/*$sql = "SELECT
ROUND(SUM(CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE 0 END) -
	SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2) AS qty_available
FROM `transaction` t 
WHERE 1=1 AND t.slip_no < '{$slipOut}' AND t.transaction_date <= '{$transactionDate}' 
AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}' ORDER by t.transaction_date ASC";*/

/*$sql = " SELECT ROUND(
		SUM(
		       CASE WHEN t.transaction_type = 1 AND t.rsb = 1 AND t.ggl = 0
				THEN t.quantity ELSE 0 END)
				- SUM(CASE WHEN t.transaction_type = 2 AND t.rsb = 1 THEN t.shrink ELSE 0 END),2
			) AS qty_availableR,
			ROUND(
				SUM(
					   CASE WHEN t.transaction_type = 1 AND t.rsb = 0 AND t.ggl = 1
						THEN t.quantity ELSE 0 END)- SUM(CASE WHEN t.transaction_type = 2 AND t.ggl = 1 THEN t.shrink ELSE 0 END),2
			) AS qty_availableG,
			ROUND(
				SUM(
					   CASE WHEN t.transaction_type = 1 AND t.rsb = 1 AND t.ggl = 1
						THEN t.quantity ELSE 0 END)- SUM(CASE WHEN t.transaction_type = 2 AND t.rsb_ggl =1 THEN t.shrink ELSE 0 END),2
			) AS qty_availableRG,
			ROUND(
				SUM(
					   CASE WHEN t.transaction_type = 1 AND t.rsb = 0 AND t.ggl = 0
						THEN t.quantity ELSE 0 END)- SUM(CASE WHEN t.transaction_type = 2 AND (t.rsb = 0 AND t.ggl = 0 AND t.`rsb_ggl` = 0) THEN t.shrink ELSE 0 END),2
			) AS qty_availableUN
		FROM `transaction` t WHERE 1=1 AND t.slip_no < '{$slipOut}' 
		AND t.transaction_date <= '{$transactionDate}' 
		AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}' 
		ORDER BY t.transaction_date ASC ";*/
$sql ="SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb = 1 THEN t.quantity ELSE 0 END)) AS qty_availableR, 
		ROUND(SUM( CASE WHEN t.transaction_type = 1 AND  t.ggl = 1 THEN t.quantity ELSE 0 END)) AS qty_availableG, 
		ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb_ggl = 1  THEN t.quantity ELSE 0 END)) AS qty_availableRG, 
		ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.uncertified = 1 THEN t.quantity ELSE 0 END)) AS qty_availableUN,
		ROUND(SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2 ) AS shrink
		FROM `transaction` t 
		WHERE 1=1 AND t.slip_no < '{$slipOut}' 
	AND t.transaction_date <=  '{$transactionDate}' 
	AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}'  ORDER BY t.transaction_date ASC ";
	//echo ' Query 1 - ' .$sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
			if($result !== false && $result->num_rows == 1) {
				 $row = $result->fetch_object();
		
			   // $begining1 = $row->qty_available;
			   $begining1_R = $row->qty_availableR;
			   $begining1_G = $row->qty_availableG;
			   $begining1_RG = $row->qty_availableRG;
			   $begining1_UN = $row->qty_availableUN;
			   $susut = $row->shrink;
				
			}
//echo ' TESTING1 '. $sql;
			
/*$sql3 = "SELECT sum(a.qty) as begining2  FROM (
SELECT SUM(t2.quantity) AS qty, t2.slip_no, t2.`transaction_date` FROM `transaction` t2 INNER JOIN shipment sh ON sh.shipment_id = t2.shipment_id WHERE 1=1
   AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}' AND t2.transaction_type = 2 AND sh.`shipment_no` NOT LIKE '%{$shipmentNo}%' GROUP BY sh.sales_id ORDER BY t2.slip_no ASC) a
 WHERE a.slip_no < '{$slipOut}' AND a.transaction_date <= '{$transactionDate}' "; */
 
 /*$sql3 = "SELECT *
			FROM ( SELECT CASE WHEN d.qty_rsb > 0 THEN (d.qty_rsb) ELSE 0 END AS qtyRSB,
				CASE WHEN d.qty_ggl > 0 THEN d.qty_ggl ELSE 0 END AS qtyGGL,
				CASE WHEN d.qty_rsb_ggl > 0 THEN d.qty_rsb_ggl ELSE 0 END AS qtyRG,
				CASE WHEN d.qty_uncertified > 0 THEN d.quantity ELSE 0 END AS qtyUN, 
				t2.slip_no, t2.`transaction_date`
				FROM `delivery` d
				INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id`
				LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id`
				WHERE 1=1 AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}'
				AND t2.transaction_type = 2 
				AND sh.`shipment_no` NOT LIKE '%{$shipmentNo}%'
				GROUP BY sh.sales_id ORDER BY t2.slip_no ASC
			) a WHERE a.slip_no < '{$slipOut}' AND a.transaction_date <= '{$transactionDate}' "; */
$sql3 = "SELECT SUM(qtyRSB) AS RS, SUM(qtyGGL) AS GG, SUM(qtyRG) AS RG, SUM(qtyUN) AS UN FROM ( 
		SELECT CASE WHEN d.qty_rsb <> 0 THEN SUM(d.qty_rsb) ELSE 0 END AS qtyRSB, 
			CASE WHEN d.qty_ggl <> 0 THEN SUM(d.qty_ggl) ELSE 0 END AS qtyGGL, 
			CASE WHEN d.qty_rsb_ggl <> 0 THEN SUM(d.qty_rsb_ggl) ELSE 0 END AS qtyRG, 
			CASE WHEN d.qty_uncertified <> 0 THEN SUM(d.qty_uncertified) ELSE 0 END AS qtyUN, 
			t2.slip_no, t2.`transaction_date` 
		FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
		LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
		AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}' AND t2.transaction_type = 2 AND sh.`shipment_no` NOT LIKE '%{$shipmentNo}%'
		GROUP BY d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified
) a 
WHERE a.slip_no < '{$slipOut}' AND a.transaction_date <= '{$transactionDate}' ";
		//	echo ' QUERY - 2 '. $sql3;
$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
if($result3->num_rows == 1) {
	while($row3 = $result3->fetch_object()) {
		
		//	$begining2 = $begining2 + $row3->qty;
		//	$begining2 = $begining2 + $row3->begining2;
		$begining2_R =  $row3->RS;
		$begining2_G = $row3->GG;
		$begining2_RG = $row3->RG;
		$begining2_UN = $row3->UN;
	}	
}
//echo ' TESTING2 '. $sql3;

$AmountTotalB = 0; $AmountTotalE = 0;
//$begining = $begining1 - $begining2;		
$beginingR = $begining1_R - $begining2_R;
$beginingG = $begining1_G - $begining2_G;
$beginingRG = $begining1_RG - $begining2_RG;
$beginingUN = $begining1_UN - $begining2_UN;

//$AmountTotalB = ($beginingR + $beginingG + $beginingRG + $beginingUN) - $susut;
$AmountTotalB = ($beginingR + $beginingG + $beginingRG + $beginingUN);

// echo "SUSUT" . $susut;
$endingR = $beginingR - $total_quantityR;	
$endingG = $beginingG - $total_quantityG;	
$endingRG = $beginingRG - $total_quantityRG;	
$endingUN = $beginingUN - $total_quantityUN;

$AmountTotalE = ($endingR + $endingG + $endingRG + $endingUN);
	
?>
			

<tr>
	<td><?php echo "RSB" ?></td>
	<td><?php echo $vessel_name; ?></td>
	<td><?php echo number_format($beginingR, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_quantityR, 2, ".", ","); ?></td>
	<td><?php echo number_format($endingR, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_pksR, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_fcR, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_ucR, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_vhR, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_cogsR, 2, ".", ","); ?></td>
</tr>
<tr>
	<td><?php echo "GGL"; ?></td>
	<td><?php echo $vessel_name; ?></td>
	<td><?php echo number_format($beginingG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_quantityG, 2, ".", ","); ?></td>
	<td><?php echo number_format($endingG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_pksG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_fcG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_ucG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_vhG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_cogsG, 2, ".", ","); ?></td>
</tr>
<tr>
	<td><?php echo "RSB + GGL"; ?></td>
	<td><?php echo $vessel_name; ?></td>
	<td><?php echo number_format($beginingRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_quantityRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($endingRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_pksRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_fcRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_ucRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_vhRG, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_cogsRG, 2, ".", ","); ?></td>
</tr>
<tr>
	<td><?php echo "Uncertified"; ?></td>
	<td><?php echo $vessel_name; ?></td>
	<td><?php echo number_format($beginingUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_quantityUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($endingUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_pksUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_fcUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_ucUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_vhUN, 2, ".", ","); ?></td>
	<td><?php echo number_format($total_cogsUN, 2, ".", ","); ?></td>
</tr>

<tr>
	<td><?php echo "Total"; ?></td>
	<td></td>
	<td><?php echo number_format($AmountTotalB, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalQty, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalE, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalPKS, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalFC, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalUC, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalVH, 2, ".", ","); ?></td>
	<td><?php echo number_format($AmountTotalCOGS, 2, ".", ","); ?></td>
</tr>

</tbody>
</table>

<!-- --------------------------------------------------------------------------------------------------------------------------------------------- -->

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
			t.permit_no as doNo,
            sh.shipment_no,
            t.send_weight, t.netto_weight, d.quantity, con.price_converted,
            d.quantity * con.price_converted AS cogs_amount,
            t.freight_quantity, t.freight_price, v1d.distance, v1d.ghg,
            CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
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
			l.labor_id,t.freight_cost_id, f.freight_id,
			CASE WHEN d.qty_ggl <> 0 THEN 'GGL'
				 WHEN d.qty_rsb <> 0  THEN 'RSB'
				 WHEN d.qty_rsb_ggl <> 0 THEN 'RSB + GGL'
				 WHEN d.qty_uncertified <> 0 THEN 'Uncertified'
			ELSE 'Uncertified' END AS sertifikat
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
		LEFT JOIN vendor_detail v1d
			ON v1d.vendor_id = v1.vendor_id
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
//echo $sql;

?>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">Area</th>
            <th rowspan="2">Transaction Date</th>
            <th rowspan="2">Slip No</th>
            <th rowspan="2">Purchase Type</th>
            <th rowspan="2">PO No.</th>
			 <th rowspan="2">DO No.</th>
            <th rowspan="2">SUPPLIER NO</th>
            <th rowspan="2">SUPPLIER NAME</th>
            <th rowspan="2">PKS SOURCE</th>
			<th rowspan="2">CERTIFICATION</th>
			<th rowspan="2">DESTINATION (Km)</th>
			<th rowspan="2">GHG AMOUNT</th>
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
				 
				 }elseif($row->fc_pph_tax_category == 0 && $row->fc_pph_tax_id != ''){
					  $pphfc =  0;  
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/
				 
				 $freightTotal = ($row->freight_total + $ppnfc + $pphfc) - $row->freight_shrink;
				 
				 
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
			<td><?php echo $row->doNo; ?></td>

            <td><?php echo $row->freight_code; ?></td>
            <td><?php echo $row->supplier; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
			<td><?php echo $row->sertifikat; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->distance, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->ghg, 0, ".", ","); ?></td>
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