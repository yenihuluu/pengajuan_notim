<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$statusProperty = '';
$stockpileId = '';
$customerId = '';
$periodFrom = '';
$periodTo = '';
$lastShipmentCode = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND sl.stockpile_id = {$stockpileId} ";
}

if(isset($_POST['customerId']) && $_POST['customerId'] != '') {
    $customerId = $_POST['customerId'];
    $whereProperty .= " AND sl.customer_id = {$customerId} ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

$sql = "SELECT sh.shipment_id, sh.shipment_date, s.stockpile_name, cust.customer_name, sh.shipment_code, sl.destination, 
        t.vehicle_no, t.quantity, sl.price, sl.price * t.quantity AS total_amount,
        p.payment_date, CONCAT(b.bank_name, ' ', cur.currency_code, ' - ', b.bank_account_no) AS bank_full, 
        CONCAT(cur2.currency_code, ' ', FORMAT(pd.amount_converted, 2)) AS amount,
        DATE_FORMAT(sh.shipment_date, '%d %b %Y') AS shipment_date2,
        DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date2
        FROM shipment sh
        INNER JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sl.stockpile_id	
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN `transaction` t
            ON t.shipment_id = sh.shipment_id
        LEFT JOIN payment_detail pd
            ON pd.shipment_id = sh.shipment_id
        LEFT JOIN payment p
            ON p.payment_id = pd.payment_id
            AND p.payment_status = 0
        LEFT JOIN currency cur2
            ON cur2.currency_id = p.currency_id
        LEFT JOIN bank b
            ON b.bank_id = p.bank_id
        LEFT JOIN currency cur
            ON cur.currency_id = b.currency_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        AND sh.shipment_date IS NOT NULL {$whereProperty}
        ORDER BY sh.shipment_code, p.entry_date LIMIT 10";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>PERIOD</th>
            <th>STOCKPILE</th>
            <th>PEMBELI</th>
            <th>KODE</th>
            <th>TUJUAN</th>
            <th>KAPAL</th>
            <th>QTY (KG)</th>
            <th>HARGA / KG</th>
            <th>TOTAL</th>
            <th>TGL BYR</th>
            <th>BANK</th>
            <th>NILAI BAYAR</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
                if($row->shipment_code == $lastShipmentCode) {
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
            <td><?php echo $row->payment_date2; ?></td>
            <td><?php echo $row->bank_full; ?></td>
            <td style="text-align: right;"><?php echo $row->amount; ?></td>
        </tr>
                <?php
                } else {
                ?>
        <tr>
            <td><?php echo $row->shipment_date2; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->customer_name; ?></td>
            <td><?php echo $row->shipment_code; ?></td>
            <td><?php echo $row->destination; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->total_amount, 2, ".", ","); ?></td>
            <td><?php echo $row->payment_date2; ?></td>
            <td><?php echo $row->bank_full; ?></td>
            <td style="text-align: right;"><?php echo $row->amount; ?></td>
        </tr>
                <?php
                }
                $lastShipmentCode = $row->shipment_code;
            }
        }
        ?>
    </tbody>
</table>

<form method="post" action="reports/sales-collection-report-xls.php">
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
    <input type="hidden" id="customerId" name="customerId" value="<?php echo $customerId; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>