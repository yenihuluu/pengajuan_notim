<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty1 = '';
$whereProperty2 = '';
$searchAccount = '';
$stockpileId = '';
$whereProperty3 = '';
$periodFrom = '';
$periodTo = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty3 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty3 .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty3 .= " AND p.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty1 .= " AND (SELECT stockpile_name FROM stockpile WHERE stockpile_id = id.stockpile_remark) = '{$stockpileId}' ";
		
}

if(isset($_POST['searchAccount']) && $_POST['searchAccount'] != '') {
    $searchAccount = $_POST['searchAccount'];
    $whereProperty2 .= " AND (SELECT account_no FROM account WHERE account_id = id.account_id) IN ({$searchAccount})";
		
}else{
	 $whereProperty2 .= " AND (SELECT account_no FROM account WHERE account_id = id.account_id) IN (521000, 520900)";
}

$sql = "SELECT p.`payment_no`, p.payment_date, p.`invoice_id`,
CASE WHEN i.po_id IS NOT NULL THEN i.po_id
WHEN id.poId IS NOT NULL THEN id.poId
ELSE '' END AS po,
(SELECT account_no FROM account WHERE account_id = id.account_id) AS account_no,
(SELECT stockpile_name FROM stockpile WHERE stockpile_id = id.stockpile_remark) AS stockpile_name,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT po_no FROM contract WHERE contract_id = id.poId)
ELSE '' END AS po_no,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT contract_no FROM contract WHERE contract_id = id.poId)
ELSE '' END AS contract_no,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT v.vendor_name FROM vendor v LEFT JOIN contract c ON c.vendor_id = v.vendor_id  LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT v.vendor_name FROM vendor v LEFT JOIN contract c ON c.vendor_id = v.vendor_id WHERE contract_id = id.poId)
ELSE '' END AS vendor_name,
(SELECT general_vendor_name FROM general_vendor WHERE general_vendor_id = id.general_vendor_id) AS general_vendor_name,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.quantity FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT quantity FROM contract WHERE contract_id = id.poId)
ELSE '' END AS quantity,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT price_converted FROM contract WHERE contract_id = id.poId)
ELSE '' END AS price_converted,
id.price AS fee_price,
CASE WHEN p.invoice_id IS NOT NULL THEN id.amount_converted
WHEN p.payment_type = 1 THEN -1*p.amount_converted
ELSE p.amount_converted END AS amountConverted,
id.notes
FROM payment p
LEFT JOIN invoice i ON p.`invoice_id` = i.`invoice_id`
LEFT JOIN invoice_detail id ON id.`invoice_id` = i.`invoice_id`
WHERE p.`invoice_id` IS NOT NULL AND p.payment_status = 0 {$whereProperty1} {$whereProperty2} {$whereProperty3}
ORDER BY p.payment_id ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/dwm-report.php', {
                   searchAccount: $('input[id="searchAccount"]').val(),
				stockpileId: $('input[id="stockpileId"]').val(),
				 periodFrom: $('input[id="periodFrom"]').val(),
				periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

         <form class="form-horizontal" id="downloadxls" method="post" action="reports/fee-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
         <input type="hidden" id="searchAccount" name="searchAccount" value="<?php echo $searchAccount; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            <th>Stockpile</th>
            <th>PO No</th>
            <th>Contract No</th>
            <th>Vendor</th>
			<th>Price /Kg</th>
            <th>Quantity Order</th>
			<th>Beneficiary</th>
            <th>Fee</th>
            <th>Total</th>
            <th>Payment Voucher</th>
            <th>Payment Date</th>
			<th>Remark</th>
           
        </tr>
    </thead>
    <tbody>
	<?php
	if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	$price = $row->price_converted;
	$stockpile = $row->stockpile_name;
	$vendor = $row->vendor_name;
	$poNo = $row->po_no;
	$beneficiary = $row->general_vendor_name;
	$contractNo = $row->contract_no;
	$quantity = $row->quantity;
	$amountConverted = $row->amountConverted;
	$fee = $amountConverted / $quantity;
	$paymentNo = $row->payment_no;
	$paymentDate = $row->payment_date;
	$remarks = $row->notes;
	
?> 
	<tr>
	
	<td><?php echo $no; ?></td>
	<td><?php echo $stockpile; ?></td>
	<td><?php echo $poNo; ?></td>
	<td><?php echo $contractNo; ?></td>
	<td><?php echo $vendor; ?></td>
	<td><?php echo number_format($price, 2, ".", ",");?></td>
	<td><?php echo number_format($quantity, 2, ".", ","); ?></td>
	<td><?php echo $beneficiary; ?></td>
    <td><?php echo number_format($fee, 2, ".", ",");?></td>
    <td><?php echo number_format($amountConverted, 2, ".", ","); ?></td>
    <td><?php echo $paymentNo; ?></td>
    <td><?php echo $paymentDate; ?></td>
	<td><?php echo $remarks; ?></td>
	
	
	</tr>
	<?php
                $no++;
            }
        }
        ?>
	</tbody>
	</table>