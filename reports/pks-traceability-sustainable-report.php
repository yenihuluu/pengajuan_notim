<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$stockpileId = '';
//$stockpileId = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];

    $whereProperty = " AND s.stockpile_id = {$stockpileId} ";
}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/pks-traceability-sustainable-report.php', {
                    // stockpileId: document.getElementById('periodFrom').value, 
				stockpileId: document.getElementById('stockpileId').value,
				periodFrom: document.getElementById('periodFrom').value,
                periodTo: document.getElementById('periodTo').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<div class="row-fluid">
	<div class="span2 lightblue">
		<form method="post" id="downloadxls" action="reports/pks-traceability-sustainable-report-xls.php">
			<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
			<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
			<input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
			<button class="btn btn-success">Download XLS</button>
		</form>

	</div>
</div>

<?php
$sql = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
			DATE_FORMAT(t.loading_date, '%d %b %Y') AS tglMuat,
			CONCAT(DATE_FORMAT(t.loading_date, '%d%m%Y'), ' - ', t.vehicle_no) AS kodeMasukMobil,
			CONCAT(DATE_FORMAT(t.unloading_date, '%d%m%Y'), ' - ', t.vehicle_no, ' - ', SUBSTRING(tt.transaction_in, 11, 9)) AS KodeTerima,
			tt.transaction_in, tt.slip as tiketTimbang, con.contract_no,
            t.slip_no, t.permit_no,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name,v1.vendor_code,
			(SELECT GROUP_CONCAT(vc.vendor_curah_code,' - ',vc.vendor_curah_name) FROM vendor_curah vc LEFT JOIN contract_pks_detail cpd ON vc.vendor_curah_id = cpd.vendor_curah_id WHERE cpd.contract_id = con.contract_id) AS vendor_curah_name,				
            sh.shipment_no,
            t.send_weight, t.netto_weight, d.quantity, 
			CASE WHEN t.mutasi_id IS NOT NULL THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
            CASE WHEN t.mutasi_id IS NOT NULL THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, 
            CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
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
			l.labor_id,t.freight_cost_id, f.freight_id,
			CASE WHEN d.qty_ggl <> 0 THEN 'GGL'
				 WHEN d.qty_rsb <> 0  THEN 'RSB'
				 WHEN d.qty_rsb_ggl <> 0 THEN 'RSB + GGL'
				 WHEN d.qty_uncertified <> 0 THEN 'Uncertified'
			ELSE 'Uncertified' END AS sertifikat, v1d.distance, v1d.ghg
        FROM delivery d
        INNER JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
		LEFT JOIN transaction_timbangan tt 
			ON t.t_timbangan = tt.transaction_id
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
// echo $sql;
?>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">Area</th>
            <th rowspan="2">Transaction Date</th>
            <th rowspan="2">Slip No</th>
			<th rowspan="2">DO No</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">PKS SOURCE</th>
			<th rowspan="2">Certification</th>

			<th rowspan="2">Destination (Km)</th>
			<th rowspan="2">GHG Amount</th>

            <th rowspan="2">SHIPMENT CODE</th>
            <th rowspan="2">Inventory (kg)</th>
			<th rowspan="2">PKS SOURCE DETAIL</th>
			<th rowspan="2">Tanggal Masuk</th>
			<th rowspan="2">Tanggal Muat</th>
			<th rowspan="2">Kode Masuk Mobil</th>
			<th rowspan="2">Kode Terima</th>
			<th rowspan="2">Tiket Timbang</th>
			<th rowspan="2">Contract No</th>

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
			<td><?php echo $row->permit_no; ?></td>
            <td><?php echo $row->po_no; ?></td>


            <td><?php echo $row->vendor_code; ?></td>
			<td><?php echo $row->sertifikat; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->distance, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->ghg, 0, ".", ","); ?></td>

            <td><?php echo $row->shipment_no; ?></td>

            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td><?php echo $row->vendor_curah_name; ?></td>
			
			<td><?php echo $row->transaction_date2; ?></td>
			<td><?php echo $row->tglMuat; ?></td>
			<td><?php echo $row->kodeMasukMobil; ?></td>
			<td><?php echo $row->KodeTerima; ?></td>
			<td><?php echo $row->tiketTimbang; ?></td>
			<td><?php echo $row->contract_no; ?></td>

        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>