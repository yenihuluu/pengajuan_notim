<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$whereProperty2 = '';
$outstanding = '';
$dateFrom = '';
$paymentFrom = '';
$paymentTo = '';
$dateTo = '';

if(isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$dateTo}', '%d/%m/%Y') ";
} else if(isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] == '') {
    $dateFrom = $_POST['dateFrom'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') >= STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['dateFrom']) && $_POST['dateFrom'] == '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN '2019-01-01' AND STR_TO_DATE('{$dateTo}', '%d/%m/%Y') ";
}
if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p1.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty2 .= " AND p1.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p1.payment_date BETWEEN '2019-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}


$sql = "SELECT c.po_no, c.`contract_no`, v.`vendor_name`,  s.`stockpile_name`, c.price, c.quantity, c.notes, (c.price * c.quantity) AS amount, DATE_FORMAT(c.entry_date, '%d %b %Y') AS entry_date, u.user_name,
CONCAT((DATEDIFF(CURRENT_DATE, c.entry_date)), ' Days') AS aging,
CASE WHEN poc.contract_id IS NOT NULL THEN pop.quantity 
ELSE c.quantity END AS qty_total,
CASE WHEN poc.contract_id IS NOT NULL THEN v.ppn
ELSE 0 END AS ppn,
IFNULL((SELECT con.quantity FROM contract con LEFT JOIN stockpile_contract sc ON sc.contract_id = con.contract_id LEFT JOIN payment p1 ON p1.stockpile_contract_id = sc.stockpile_contract_id WHERE con.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2} GROUP BY con.contract_id),0) AS qty_paid,
IFNULL((SELECT p1.original_amount FROM payment p1 LEFT JOIN stockpile_contract sc2 ON sc2.stockpile_contract_id = p1.stockpile_contract_id WHERE sc2.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY sc2.contract_id),0) AS paid,
c.entry_date,
 (SELECT con.contract_id FROM contract con LEFT JOIN stockpile_contract sc ON sc.contract_id = con.contract_id LEFT JOIN payment p1 ON p1.stockpile_contract_id = sc.stockpile_contract_id WHERE con.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY con.contract_id) AS cId,
 (SELECT p1.payment_date FROM payment p1 LEFT JOIN stockpile_contract sc2 ON sc2.stockpile_contract_id = p1.stockpile_contract_id WHERE sc2.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY sc2.contract_id) AS payment_date
FROM contract c 	
				LEFT JOIN po_contract poc ON poc.contract_id = c.contract_id
				LEFT JOIN po_pks pop ON pop.po_pks_id = poc.po_pks_id
				LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id
				LEFT JOIN stockpile s ON s.stockpile_id = sc.stockpile_id
				LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
				LEFT JOIN USER u ON u.user_id = c.entry_by
				WHERE 1=1  AND c.`contract_type` = 'P'  AND c.quantity > 0 AND c.price > 0 AND c.contract_status <> 2
				{$whereProperty}
				AND (SELECT con.contract_id FROM contract con LEFT JOIN stockpile_contract sc ON sc.contract_id = con.contract_id LEFT JOIN payment p1 ON p1.stockpile_contract_id = sc.stockpile_contract_id WHERE con.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY con.contract_id) IS NULL
				AND (SELECT p1.payment_date FROM payment p1 LEFT JOIN stockpile_contract sc2 ON sc2.stockpile_contract_id = p1.stockpile_contract_id WHERE sc2.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 AND c.contract_status <> 2 {$whereProperty2} GROUP BY sc2.contract_id) IS NULL
				ORDER BY c.entry_date DESC
				";
		$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


?>
<br />
<form method="post" action="reports/unpaid-contract-report-xls.php">
    <input type="hidden" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom; ?>" />
    <input type="hidden" id="dateTo" name="dateTo" value="<?php echo $dateTo; ?>" />
	<input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr><th rowspan="2">No</th>
        	<th rowspan="2">Stockpile</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">Contract No.</th>
            <th rowspan="2">Vendor Name</th>
			<th rowspan="2">Qty</th>
			<th rowspan="2">Price</th>
            <th rowspan="2">Amount</th>
            <th rowspan="2">PPN</th>
            <th rowspan="2">Total</th>
            
            <th rowspan="2">Amount Total (inc. PPN)</th>
            <th rowspan="2">Qty Paid</th>
			<th rowspan="2">Amount Paid (inc. PPN)</th>
			<th rowspan="2">Balance Qty</th>
            <th rowspan="2">Balance Amount (inc. PPN)</th>
            <th rowspan="2">Input Date</th>
            <th rowspan="2">Input By</th>
			<th rowspan="2">Aging</th>
			<th rowspan="2">Notes</th>
			
        </tr>
        
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			//echo $sql;
            $no = 1;
			
            while($row = $result->fetch_object()) {
			
			
             $stockpile_name = $row->stockpile_name; 
             $po_no = $row->po_no; 
             $contract_no = $row->contract_no; 
             $vendor_name = $row->vendor_name;
             $input_date = $row->entry_date; 
             $input_by = $row->user_name; 
			 $qty = $row->quantity;
			 $price = $row->price;
			 $amount = $row->amount ;
			 $aging = $row->aging;
			 $qty_total = $row->qty_total;
			 $ppn = ($row->ppn/100) * $amount;
			 $amount_total = ($row->price * $qty) + $ppn;
			 $qty_paid = $row->qty_paid;
			 $ppn_paid = ($row->ppn/100) * $row->paid;
			 $paid = $row->paid + $ppn_paid;
			 $total = $amount + $ppn;
			 $qty_balance = $qty - $qty_paid;
			 $amount_balance = $amount_total - $paid;
			 
                ?>
        <tr>
        	<td><?php echo $no?></td>
            <td><?php echo $stockpile_name; ?></td>
            <td><?php echo $po_no; ?></td>
            <td><?php echo $contract_no; ?></td>
            <td><?php echo $vendor_name; ?></td>
			<td style="text-align: right;"><?php echo number_format($qty, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($price, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($amount, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($ppn, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($total, 2, ".", ","); ?></td>
            
            <td style="text-align: right;"><?php echo number_format($amount_total, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($qty_paid, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($paid, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($qty_balance, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($amount_balance, 2, ".", ","); ?></td>
            <td><?php echo $input_date; ?></td>
            <td><?php echo $input_by; ?></td>
			<td><?php echo $aging; ?></td>
			<td><?php echo $row->notes; ?></td>
			
			
            
            
        </tr>
                <?php
                $no++;
            
			}
        }
        ?>
    </tbody>
</table>
