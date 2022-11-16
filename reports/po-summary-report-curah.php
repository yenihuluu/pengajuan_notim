<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$whereProperty = '';
$vendorId = '';
$vendorIds = '';
$searchStatus = '';
/*$whereProperty1 = '';
$whereProperty2 = '';

$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$paymentFrom = '';
$paymentTo = '';
$inputFrom = '';
$inputTo = '';
$adjustmentTo = '';
$status = '';*/


if(isset($_POST['searchStatus']) && $_POST['searchStatus'] != '') {
$searchStatus = $_POST['searchStatus'];
if($searchStatus == 1){
$whereProperty .= " AND c.return_shipment = 1 ";
}else{
$whereProperty .= " AND (c.return_shipment != 1 OR c.return_shipment IS NULL)";
}
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

    $whereProperty .= " AND c.vendor_id IN ({$vendorIds}) ";

}
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= "AND (SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) = {$stockpileId} ";
}*/



?>

<script type="text/javascript">
 $(document).ready(function () {
	 
	 
	  $('#printPoCurah').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#poCurah").printThis();
//            $("#transactionContainer").hide();
        });
		
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/po-summary-report-curah.php', {
                   vendorId: $('input[id="vendorIds"]').val() ,
				searchStatus: $('input[id="searchStatus"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id = "downloadxls" "po-summary-report-curah" action="reports/po-summary-report-curah-xls.php">

	<input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $vendorIds; ?>" />
	<input type="hidden" id="searchStatus" name="searchStatus" value="<?php echo $searchStatus; ?>" />

    <button class="btn btn-success">Download XLS</button>
	<button class="btn btn-info" id="printPoCurah">Print</button>
</form>

<div id = "poCurah">
<li class="active">PO Curah Summary Report</li>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
			<th rowspan="2">No</th>
            <th rowspan="2">No PO</th>
			<th rowspan="2">Contract No</th>
            <th rowspan="2">Vendor</th>
            <th rowspan="2">Address</th>
            <th rowspan="2">Stockpile</th>
            <th rowspan="2">Price / Kg</th>
            <th rowspan="2">Quantity</th>
            <th rowspan="2">Qty Received</th>
			<th rowspan="2">First Slip</th>
			<th rowspan="2">First Date</th>
			<th rowspan="2">Last Slip</th>
			<th rowspan="2">Last Date</th>
            <th rowspan="2">Payment Date</th>
			<th rowspan="2">Payment No.</th>
			<th rowspan="2">Amount Payment</th>
			<th rowspan="2">Debt</th>
			<th rowspan="2">Entry By</th>
			<th rowspan="2">Entry Date</th>
			<th rowspan="2">Purchasing Input</th>
			<th rowspan="2">Purchasing Date</th>


        </tr>


    </thead>
    <tbody>
<?php
$sql = "SELECT c.`po_no`, c.contract_no, v.`vendor_name`, v.`vendor_address`, s.`stockpile_name`, c.`price_converted`,
SUM(t.send_weight) AS qtyRecieved, MIN(slip_no) AS firstSlip, MIN(transaction_date) AS firstDate, MAX(slip_no) AS lastSlip, MAX(transaction_date) AS lastDate,
 p.`payment_date`, p.`payment_no`,
(SELECT SUM(DISTINCT(`amount_converted`))
FROM payment pp LEFT JOIN TRANSACTION tt ON tt.payment_id=pp.payment_id
LEFT JOIN stockpile_contract scc ON scc.stockpile_contract_id=tt.stockpile_contract_id
LEFT JOIN contract cc ON cc.`contract_id`=scc.`contract_id`
WHERE cc.po_no=c.po_no GROUP BY cc.po_no) AS amount_converted,
CASE WHEN p.payment_location = 0 THEN 'HO' ELSE ps.stockpile_code END AS payment_location, b.`bank_code`, cur.`currency_code`, b.`bank_type`, p.`payment_type`,
SUM(CASE WHEN t.payment_id IS NULL AND t.notim_status = 0 AND t.slip_retur IS NULL THEN t.inventory_value ELSE 0 END) AS hutang,c.quantity, u.user_name, c.entry_date,
(SELECT u.user_name FROM `user` u LEFT JOIN purchasing pu ON pu.entry_by = u.user_id LEFT JOIN po_pks po ON po.purchasing_id = pu.purchasing_id WHERE po.contract_no = REPLACE(c.contract_no,'-1','') LIMIT 1) AS purchasingInput,
(SELECT pu.entry_date FROM purchasing pu LEFT JOIN po_pks po ON po.purchasing_id = pu.purchasing_id WHERE po.contract_no = REPLACE(c.contract_no,'-1','') LIMIT 1) AS purchasingDate
FROM contract c
LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN `transaction` t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN payment p ON p.`payment_id` = t.`payment_id`
LEFT JOIN bank b ON b.`bank_id` = p.`bank_id`
LEFT JOIN currency cur ON cur.`currency_id` = p.`currency_id`
LEFT JOIN stockpile ps ON ps.stockpile_id = p.payment_location
LEFT JOIN user u ON u.user_id = c.entry_by
WHERE c.`contract_type` = 'C' AND c.`entry_date` > '2019-08-01' {$whereProperty}
GROUP BY c.`po_no`
ORDER BY c.`contract_id` ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

/*$sql = "SELECT  c.`po_no`, c.contract_no, v.`vendor_name`, v.`vendor_address`, s.`stockpile_name`, c.`price_converted`, SUM(t.`quantity`) AS qtyRecieved,
MIN(t.`slip_no`) AS firstSlip, MAX(t.`slip_no`) AS lastSlip, p.`payment_date`, p.`payment_no`, c.return_shipment,
(SELECT SUM(DISTINCT(`amount_converted`))
FROM payment pp LEFT JOIN TRANSACTION tt ON tt.payment_id=pp.payment_id
LEFT JOIN stockpile_contract scc ON scc.stockpile_contract_id=tt.stockpile_contract_id
LEFT JOIN contract cc ON cc.`contract_id`=scc.`contract_id`
WHERE cc.po_no=c.po_no GROUP BY cc.po_no) AS amount_converted,
CASE WHEN p.payment_location = 0 THEN 'HO' ELSE ps.stockpile_code END AS payment_location, b.`bank_code`, cur.`currency_code`, b.`bank_type`, p.`payment_type`,
SUM(CASE WHEN t.payment_id IS NULL AND t.notim_status = 0 AND t.slip_retur IS NULL THEN t.inventory_value ELSE 0 END) AS hutang,c.quantity
FROM `transaction` t
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN payment p ON p.`payment_id` = t.`payment_id`
LEFT JOIN bank b ON b.`bank_id` = p.`bank_id`
LEFT JOIN currency cur ON cur.`currency_id` = p.`currency_id`
LEFT JOIN stockpile ps ON ps.stockpile_id = p.payment_location
WHERE c.`contract_type` = 'C' AND t.`transaction_date` > '2019-08-01' {$whereProperty}
GROUP BY  c.`po_no`
ORDER BY t.`transaction_date` ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);*/

?>

        <?php
		 $no = 1;
        if($result->num_rows > 0) {

            while($row = $result->fetch_object()) {

			 $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->currency_code;

                if($row->bank_type == 1) {
                    $voucherCode .= ' - B';
                } elseif($row->bank_type == 2) {
                    $voucherCode .= ' - P';
                } elseif($row->bank_type == 3) {
                    $voucherCode .= ' - CAS';
                }

                if($row->bank_type != 3) {
                    if($row->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                    }
                }


                ?>
        <tr>
			<td><?php echo $no; ?></td>
            <td><?php echo $row->po_no; ?></td>
			<td><?php echo $row->contract_no; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->vendor_address; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price_converted, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->qtyRecieved, 0, ".", ","); ?></td>
			<td><?php echo $row->firstSlip; ?></td>
			<td><?php echo $row->firstDate; ?></td>
			<td><?php echo $row->lastSlip; ?></td>
			<td><?php echo $row->lastDate; ?></td>
			<td><?php echo $row->payment_date; ?></td>
            <td><?php echo $voucherCode; ?>#<?php echo $row->payment_no; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->amount_converted, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->hutang, 0, ".", ","); ?></td>
			<td><?php echo $row->user_name; ?></td>
			<td><?php echo $row->entry_date; ?></td>
			<td><?php echo $row->purchasingInput; ?></td>
			<td><?php echo $row->purchasingDate; ?></td>

        </tr>
                <?php
                $no++;
				$totalQty = $totalQty + $row->qtyRecieved;
				$totalPayment = $totalPayment + $row->amount_converted;
            }
		} else{
			//echo $sql;
		}

        ?>
		<tr>
		<td colspan='7' style="text-align: right;">Total Qty Received</td>
		<td style="text-align: right;"><?php echo number_format($totalQty, 0, ".", ","); ?></td>
		<td colspan='4' style="text-align: right;">Total Amount Payment</td>
		<td style="text-align: right;"><?php echo number_format($totalPayment, 0, ".", ","); ?></td>
    </tbody>
</table>
</div>