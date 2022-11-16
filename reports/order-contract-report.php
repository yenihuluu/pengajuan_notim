<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$summaryProperty = '';
$whereProperty = '';
$whereProperty1 = '';
$statusProperty = '';
$stockpileId = '';
$contractId = '';
$periodFrom = '';
$periodTo = '';
$paymentFrom = '';
$paymentTo = '';
$status = '';


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    //$whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $summaryProperty .= " AND a.input_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    //$whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $summaryProperty .= " AND a.input_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    //$whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $summaryProperty .= " AND a.input_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {
    $paymentFrom = $_POST['paymentFrom'];
    $paymentTo = $_POST['paymentTo'];
    //$whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $summaryProperty1 .= " AND a.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {
    $paymentFrom = $_POST['paymentFrom'];
    //$whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $summaryProperty1 .= " AND a.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {
    $paymentTo = $_POST['paymentTo'];
    //$whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    //$summaryProperty1 .= " AND p.payment_date <= STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	 $summaryProperty1 .= " AND a.payment_date BETWEEN '2016-08-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
}
/*
if(isset($_POST['status']) && $_POST['status'] != '') {
    $status = $_POST['status'];
    
    if($status == 0) {
        $statusProperty .= " AND a.quantity_received = 0 ";
    } elseif($status == 1) {
        $statusProperty .= " AND a.quantity <= a.quantity_received ";
    } elseif($status == 2) {
        $statusProperty .= " AND a.quantity > a.quantity_received ";
    }
}*/
?>
<form method="post" action="reports/order-contract-report-xls.php">
   
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
           
            <th rowspan="2">No.</th>
            <th rowspan="2">Vendor Name</th>
            <th rowspan="2">Contract Date</th>
            <th rowspan="2">Contract No.</th>
            <th rowspan="2">QTY Contract</th>
            <th rowspan="2">QTY Paid</th>
            <th rowspan="2">Balance Qty</th>
            <th rowspan="2">Status</th>
            <th rowspan="2">Payment No</th>
            <th rowspan="2">Payment Date</th>
            <th rowspan="2">DAYS O/S</th>
            
        </tr>
        
       
       
    </thead>
    <tbody>
<?php

$sql = "SELECT * FROM 
(SELECT v.vendor_name, STR_TO_DATE(pop.`entry_date`, '%Y-%m-%d') AS input_date, con.contract_no, con.payment_status, pop.`quantity` AS qtyTotal, con.`quantity`,
STR_TO_DATE(p.payment_date, '%Y-%m-%d') AS payment_date, p.payment_no,
CASE WHEN pop.po_status = 1 THEN 'CLOSED'
ELSE 'OPEN' END AS po_status,
(pop.quantity - (SELECT COALESCE(SUM(quantity),0) FROM po_contract WHERE po_pks_id = pop.`po_pks_id` AND contract_id <= con.contract_id)) AS balance,
CASE WHEN con.payment_status = 1 THEN (DATE_FORMAT(p.payment_date, '%d %b %Y') -  DATE_FORMAT(pop.`entry_date`, '%d %b %Y'))
WHEN con.payment_status = 0 THEN (DATE_FORMAT(CURRENT_DATE, '%d %b %Y') -  DATE_FORMAT(pop.`entry_date`, '%d %b %Y'))
ELSE 1 END AS aging
FROM contract con 
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = con.`contract_id`
LEFT JOIN payment p ON p.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN po_contract poc ON con.contract_id = poc.`contract_id`
LEFT JOIN po_pks pop ON pop.`po_pks_id` = poc.`po_pks_id`
LEFT JOIN vendor v ON v.`vendor_id` = con.`vendor_id`
)a WHERE 1=1 {$summaryProperty} {$summaryProperty1}";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

        <?php
		
        if($result->num_rows > 0) {
           	 $no = 1;
            while($row = $result->fetch_object()) {
			
                ?>
        <tr>
           	<td><?php echo $no ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->input_date; ?></td>
            <td><?php echo $row->contract_no; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->qtyTotal, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->balance, 2, ".", ","); ?></td>
            <td><?php echo $row->po_status; ?></td>
            <td><?php echo $row->payment_no; ?></td>
            <td><?php echo $row->payment_date; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->aging, 0, ".", ","); ?></td>
            
        </tr>
                <?php
                $no++;
            }
			} 
        
        ?>
    </tbody>
</table>

