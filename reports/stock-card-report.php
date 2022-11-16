<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$shipmentId = '';
$stockpileId = '';
$periodFrom = '';
$periodTo = '';
//$paymentFrom = '';
//$paymentTo = '';


if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
}
if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != '') {
    $shipmentId = $_POST['shipmentId'];
    $whereProperty .= " AND sl.sales_id = {$shipmentId} ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}


$sql = "SELECT DISTINCT t.*,
            DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
            s.stockpile_name, con.po_no, con.contract_no, vh.vehicle_name, con.contract_id,
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,v1.npwp,
            v1.vendor_name, v3.vendor_name AS supplier, f.freight_supplier,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.quantity END AS quantity2,
            ship.shipment_no AS group_shipment_code, 
            DATE_FORMAT(d.delivery_date, '%d %b %Y') AS group_delivery_date, 
            d.quantity AS group_quantity,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS payment_no,
            CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS payment_date,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.original_amount_converted FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS amount_paid,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.tax_invoice FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS tax_invoice,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.invoice_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS tax_date,
			CASE WHEN t.fc_payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = t.fc_payment_id)
            ELSE '' END AS fc_payment_no,
            CASE WHEN t.fc_payment_id IS NOT NULL THEN (SELECT payment_date FROM payment WHERE payment_id = t.fc_payment_id)
            ELSE '' END AS fc_payment_date
        FROM TRANSACTION t
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
        LEFT JOIN delivery d
            ON d.transaction_id = t.transaction_id
        LEFT JOIN shipment ship
            ON ship.shipment_id = d.shipment_id
		LEFT JOIN sales sl 
			ON ship.sales_id = sl.sales_id
        WHERE 1=1  
        AND t.company_id = {$_SESSION['companyId']}
        AND t.transaction_type = 1 {$whereProperty}
        ";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<form method="post" action="reports/stock-card-report-xls.php">
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
	<input type="hidden" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">Area</th>
            <th rowspan="2">Transaction Date</th>
			<th rowspan="2">Loading Date</th>
            <th rowspan="2">Slip No.</th>
            <th rowspan="2">Purchase Type</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">SUPPLIER CODE</th>
            <th rowspan="2">PKS SOURCE</th>
			<th rowspan="2">NPWP</th>
			<th rowspan="2">Tax Invoice No</th>
			<th rowspan="2">Tax Invoice Date</th>
            <th colspan="3">Product (PKS)</th>
            <th rowspan="2">FREIGHT COST</th>
            <th rowspan="2">UNLOADING COST</th>
            <th rowspan="2">TOTAL</th>
            <th colspan="3">SHIPMENT</th>
            <th rowspan="2">QTY ENDING BALANCE</th>
			<th rowspan="2">Payment No</th>
			<th rowspan="2">Payment Date</th>
			<th rowspan="2">Amount Paid</th>
			<th rowspan="2">FC Payment No</th>
			<th rowspan="2">FC Payment Date</th>
        </tr>
        <tr>
            
            <th>Inventory</th>
            <th>Price /kg</th>
            <th>Amount</th>
            <th>CODE</th>
            <th>DATE</th>
            <th>QTY (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
            $no = 1;
            while($row = $result->fetch_object()) {
                ?>
        <tr>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->unloading_date2; ?></td>
			<td><?php echo $row->loading_date2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->contract_type2; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->freight_code; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
			<td><?php echo $row->npwp; ?></td>
			<td><?php echo $row->tax_invoice; ?></td>
			<td><?php echo $row->tax_date; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->unit_price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity * $row->unit_price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->freight_quantity * $row->freight_price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->unloading_price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format(($row->quantity * $row->unit_price) + ($row->freight_quantity * $row->freight_price) + ($row->unloading_price), 2, ".", ","); ?></td>
            <td><?php echo $row->group_shipment_code; ?></td>
            <td><?php echo $row->group_delivery_date; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->group_quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity - $row->group_quantity, 0, ".", ","); ?></td>
			<td><?php echo $row->payment_no; ?></td>
			<td><?php echo $row->payment_date; ?></td>
			<td><?php echo number_format($row->amount_paid, 2, ".", ","); ?></td>
			<td><?php echo $row->fc_payment_no; ?></td>
			<td><?php echo $row->fc_payment_date; ?></td>
        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>

