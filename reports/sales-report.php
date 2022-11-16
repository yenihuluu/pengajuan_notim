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
$status = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND sl.stockpile_id = {$stockpileId} ";
}

if(isset($_POST['customerId']) && $_POST['customerId'] != '') {
    $customerId = $_POST['customerId'];
    $whereProperty .= " AND sl.customer_id = {$customerId} ";
}

if(isset($_POST['status']) && $_POST['status'] != '') {
    $status = $_POST['status'];
    $whereProperty .= " AND sl.sales_status = {$status} ";
}

$sql = "SELECT s.stockpile_name, sl.sales_no, DATE_FORMAT(sl.sales_date, '%d %b %Y') AS sales_date2, sl.sales_date, cust.customer_name, 
                sl.quantity AS sales_quantity, sl.price, sl.price * sl.quantity AS sales_amount, sl.destination, t.transaction_date,
                DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
                t.slip_no, sh.shipment_code, t.send_weight, t.quantity, sl.price * t.quantity AS shipment_amount, t.shrink, 
                (t.shrink/t.send_weight) * 100 AS shrink_percent, t.shrink * sl.price AS shrink_amount,
                sl.destination
        FROM `transaction` t
        INNER JOIN shipment sh
                ON sh.shipment_id = t.shipment_id
        INNER JOIN sales sl
                ON sl.sales_id = sh.sales_id
        INNER JOIN stockpile s
                ON s.stockpile_id = sl.stockpile_id
        INNER JOIN customer cust
                ON cust.customer_id = sl.customer_id
        WHERE t.transaction_type = 2 
        AND t.company_id = {$_SESSION['companyId']} {$whereProperty} 
        ORDER BY s.stockpile_name, sl.sales_no";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<form method="post" action="reports/sales-report-xls.php">
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
    <input type="hidden" id="customerId" name="customerId" value="<?php echo $customerId; ?>" />
    <input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th colspan="11">SALES CONTRACT</th>
            <th colspan="10">REALIZATION</th>
        </tr>
        <tr>
            <th>Area</th>
            <th>SALES AGREEMENT NO</th>
            <th>SALES AGREEMENT DATE</th>
            <th>BUYER NAME</th>
            <th>LAYCAN</th>
            <th>SALES AGREEMENT QTY</th>
            <th>PRICE / KG</th>
            <th>SALES AMOUNT</th>
            <th>TERM OF PAYMENT</th>
            <th>PORT OF LOADING</th>
            <th>PORT OF DISCHARGE (DESTINATION)</th>
            <th>Transaction Date</th>
            <th>Slip No</th>
            <th>SHIPMENT CODE</th>
            <th>QTY LOADING</th>
            <th>QTY B/L</th>
            <th>PRICE / KG</th>
            <th>SALES AMOUNT</th>
            <th>SHORT IN QTY</th>
            <th>SHORT (%)</th>
            <th>LOSS ON SHORTAGE</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
                ?>
        <tr>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->sales_no; ?></td>
            <td><?php echo $row->sales_date2; ?></td>
            <td><?php echo $row->customer_name; ?></td>
            <td></td>
            <td style="text-align: right;"><?php echo number_format($row->sales_quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->sales_amount, 2, ".", ","); ?></td>
            <td></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->destination; ?></td>
            <td><?php echo $row->transaction_date2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->shipment_code; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->shipment_amount, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->shrink_percent, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->shrink_amount, 2, ".", ","); ?></td>
        </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
