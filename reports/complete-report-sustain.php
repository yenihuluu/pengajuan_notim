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
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileIds = $_POST['stockpileId'];
	
	
					
	$stockpile_code = array();				
    $sql = "SELECT stockpile_code FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_code[] = $row['stockpile_code'];
		
		$stockpile_codes =  "'" . implode("','", $stockpile_code) . "'";
		}
	}
    //echo $sql;
    $whereProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpile_codes}) ";
    $sumProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpile_codes}) ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
	// $whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND  t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
}

$sql = "SELECT t.*,
        DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
        CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
        CASE WHEN t.transaction_type = 1 THEN con.po_no ELSE sh.shipment_code END AS po_no, 
        CASE WHEN t.transaction_type = 1 THEN con.contract_no ELSE sl.sales_no END AS contract_no, 
        CASE WHEN t.transaction_type = 1 THEN vh.vehicle_name ELSE t.vehicle_no END AS vehicle_name,
        CASE WHEN t.transaction_type = 1 THEN t.vehicle_no ELSE '' END AS vehicle_no,
        CASE WHEN t.transaction_type = 1 THEN DATE_FORMAT(t.unloading_date, '%d %b %Y') ELSE DATE_FORMAT(t.transaction_date, '%d %b %Y') END AS unloading_date2,
        DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
        CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
        CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code, f.freight_id, 
        v1.vendor_name, hv.vendor_handling_id, hv.vendor_handling_name, 
        CASE WHEN t.transaction_type = 1 THEN v3.vendor_name ELSE cust.customer_name END AS supplier,
        CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
        CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2, u.user_name,
        CASE WHEN t.transaction_type = 1 THEN 
        (SELECT shi.shipment_no FROM shipment shi LEFT JOIN delivery d ON d.shipment_id = shi.shipment_id WHERE d.transaction_id = t.transaction_id LIMIT 1)
        ELSE sh.shipment_no END AS shipment_no2, l.labor_name, cpd.contract_pks_detail_id, vc.vendor_curah_name AS vendor_curah
        FROM TRANSACTION t
        LEFT JOIN transaction_additional_shrink tas ON t.transaction_id = tas.transaction_id
        LEFT JOIN transaction_shrink_weight ts
            ON t.transaction_id = ts.transaction_id
        LEFT JOIN stockpile_contract sc
        ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
        ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
        ON con.contract_id = sc.contract_id
        LEFT JOIN contract_pks_detail cpd
        ON con.contract_id = cpd.contract_id
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
        LEFT JOIN USER u
        ON u.user_id = t.modify_by
        LEFT JOIN vendor_handling_cost vhc
        ON vhc.handling_cost_id = t.handling_cost_id
        LEFT JOIN vendor_handling hv
        ON hv.vendor_handling_id = vhc.vendor_handling_id
        LEFT JOIN labor l ON l.labor_id = t.labor_id
        WHERE 1=1
        AND t.company_id = {$_SESSION['companyId']}
        {$whereProperty}
        AND t.slip_no NOT IN (SELECT LEFT(slip_retur, 17) FROM TRANSACTION WHERE slip_retur IS NOT NULL)
	    AND t.slip_retur IS NULL -- AND t.slip_retur_id IS NOT NULL
		GROUP BY t.transaction_id ORDER BY t.slip_no ASC";
	//echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/complete-report-sustain.php', {
                    // stockpileId: document.getElementById('periodFrom').value, 
                    stockpileId: document.getElementById('stockpileIds').value,
					periodFrom: document.getElementById('periodFrom').value,
					periodTo: document.getElementById('periodTo').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<form method="post" id="downloadxls" action="reports/complete-report-sustain-xls.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
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
            <th>Vendor Handling</th>
            <th>Type</th>
            <th>Inventory</th>
            <th>Balance (Q)</th>
            <th>Type</th>
			<th>Shipment Code</th>
            <th>Sumber PKS</th>

        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			//echo $sql;
			
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
        </tr>
                <?php
                }
            }
            
            $balanceQuantity = $balanceBefore;
            $no = 1;
            while($row = $result->fetch_object()) {
                $balanceQuantity = $balanceQuantity + $row->quantity2;
				
				if($row->transaction_type == 2){
					if($row->quantity < 0){
						$quantity = $row->quantity * -1;
					}else{
						$quantity = '-' .$row->quantity;
					}
				}else{
					$quantity = $row->quantity;
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
            <td><?php echo $row->vendor_handling_name; ?></td>
            <td><?php echo $row->transaction_type2; ?></td>
            <td style="text-align: right;"><?php echo number_format($quantity, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($balanceQuantity, 2, ".", ","); ?></td>
            <td><?php echo $row->contract_type2; ?></td>
           <td><?php echo $row->shipment_no2; ?></td>
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
