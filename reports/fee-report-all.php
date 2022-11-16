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
/*
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty3 .= " AND a.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty3 .= " AND a.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty3 .= " AND a.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
*/
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    //$whereProperty1 .= " AND a.stockpile_name = '$stockpileId' ";
		
}
/*
if(isset($_POST['searchAccount']) && $_POST['searchAccount'] != '') {
    $searchAccount = $_POST['searchAccount'];
    $whereProperty2 .= " AND a.account_no =  {$searchAccount}";
		
}else{
	 $whereProperty2 .= " AND a.account_no IN (521000, 520900)";
}*/

$sql = "SELECT id.poId, v.vendor_id, v.vendor_name, c.po_no, c.contract_no, ROUND(c.`quantity`,2) AS qty, ROUND(c.`quantity` * c.`price_converted`,2) AS contract_price, ROUND(c.`price_converted`,2) AS price_kg,
gv.`general_vendor_name`, ROUND(id.`qty`,2) AS qty_fee, ROUND(id.`price`,2) AS fee_kg, ROUND(id.`qty` * id.`price`,2) AS fee, p.payment_no,
(ROUND(c.`quantity` * c.`price_converted`,2) + (SELECT ROUND(SUM(qty * price),2) FROM invoice_detail WHERE poId = id.poId)) AS totalAmount
FROM invoice_detail id
LEFT JOIN contract c ON c.`contract_id` = id.`poId`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
LEFT JOIN payment p ON p.`invoice_id` = id.`invoice_id`
LEFT JOIN account a ON a.`account_id` = id.`account_id` 
WHERE id.`poId` IS NOT NULL AND p.payment_status = 0 AND a.`account_no` IN (521000, 520900) ORDER BY c.po_no ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/fee-report-all.php', {
                  	vendorId: $('input[id="vendorId"]').val()
                    

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
            
            <th>Vendor PKS</th>
            <th>PO No</th>
            <th>Contract No</th>
            <th>Quantity Order</th>
			<th>Price /kg</th>
			<th>Amount</th>
			<th>Payment_no</th>
			<th>vendor</th>
			<th>Qty</th>
			<th>Price /Kg</th>	
			<th>Amount</th>
			<th>Total Amount</th>			
        </tr>
    </thead>
    <tbody>
	<?php
	if($result->num_rows > 0) {
	//$no = 1;
	while($row = $result->fetch_object()) {
	$price = $row->contract_price;
	$priceKg = $row->price_kg;
	$vendor = $row->vendor_name;
	$poNo = $row->po_no;
	$contractNo = $row->contract_no;
	$quantity = $row->qty;
	$vendorId = $row->vendor_id;
	
	
?> 
	<tr>
	<?php
                if($row->poId == $lastPoId) {
                    $counter++;
                ?>
			
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			
			
	<?php
                } else {
                    $sqlCount = "SELECT COUNT(1) AS total_row
FROM invoice_detail id
LEFT JOIN contract c ON c.`contract_id` = id.`poId`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
LEFT JOIN payment p ON p.`invoice_id` = id.`invoice_id`
LEFT JOIN account a ON a.`account_id` = id.`account_id` 
WHERE id.`poId` = {$row->poId} AND p.payment_status = 0 AND a.`account_no` IN (521000, 520900) ORDER BY c.po_no ASC";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
                    $counter = 1;
                    //echo 'tesst';
                    //$poNo = $row->po_no;
                    //$vendorName = $row->vendor_name;
                    //$contractNo = $row->contract_no;
                    //$unitPrice = $row->price_converted;
                   // $quantityOrder = $row->quantity;
                    //$amountOrder = $row->amount_order;
                    //$totalQuantityReceived = 0;
                    //$totalAmountReceived = 0;
                    
                    $no++;
                    //$balanceQuantity = $row->quantity;
                ?>
	<td><?php echo $vendor; ?></td>
	<td><?php echo $poNo; ?></td>
	<td><?php echo $contractNo; ?></td>
	<td><?php echo number_format($quantity, 2, ".", ","); ?></td>
	<td><?php echo number_format($priceKg, 2, ".", ",");?></td>
	<td><?php echo number_format($price, 2, ".", ",");?></td>

	
	<?php } ?>
	<td><?php echo $row->payment_no; ?></td>
	<td><?php echo $row->general_vendor_name; ?></td>
	<td><?php echo number_format($row->qty_fee, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->fee_kg, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->fee, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->totalAmount, 2, ".", ","); ?></td>
	</tr>
	<?php
                $lastPoId = $row->poId;
            }
        }
        ?>
	</tbody>
	</table>