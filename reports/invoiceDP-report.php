<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';

$periodFrom = '';

$periodTo = '';

/*$paymentFrom = '';

$paymentTo = '';

$amount = '';*/

$generalVendorId = '';

$stockpileId = '';


/*
if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}*/
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND i.invoice_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND i.invoice_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND i.invoice_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
if(isset($_POST['generalVendorId']) && $_POST['generalVendorId'] != '' && $_POST['generalVendorId'] != 0) {
    $generalVendorId = $_POST['generalVendorId'];
    
	for ($i = 0; $i < sizeof($generalVendorId); $i++) {
                        if($generalVendorIds == '') {
                            $generalVendorIds .= $generalVendorId[$i];
                        } else {
                            $generalVendorIds .= ','. $generalVendorId[$i];
                        }
                    }
	$whereProperty .= " AND id.general_vendor_id IN ({$generalVendorIds}) ";
		
}

/*					
if(isset($_POST['vendorFreightId']) && $_POST['vendorFreightId'] != '' && $_POST['vendorFreightId'] != 0) {
    $vendorFreightId = $_POST['vendorFreightId'];
	
	for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
                        if($vendorFreightIds == '') {
                            $vendorFreightIds .= $vendorFreightId[$i];
                        } else {
                            $vendorFreightIds .= ','. $vendorFreightId[$i];
                        }
                    }
					
    $whereProperty .= " AND fc.`vendor_id` IN ({$vendorFreightIds}) ";
		
}*/
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '' && $_POST['stockpileId'] != 0) {
    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }
					
	$whereProperty .= " AND i.stockpileId IN ({$stockpileIds}) ";
}

$sql = "SELECT i.*, id.amount_converted, s.stockpile_name, u.user_name,gv.general_vendor_name,
(id.`amount` - IFNULL((SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0),0)) AS total_dp
FROM invoice i
LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id`
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
LEFT JOIN stockpile s ON i.`stockpileId` = s.`stockpile_id`
LEFT JOIN user u ON i.entry_by = u.user_id
WHERE id.invoice_method_detail = 2 AND id.invoice_detail_status = 0 AND i.invoice_status = 0
 AND (id.`amount` - IFNULL((SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0),0)) > 0 {$whereProperty}
ORDER BY i.invoice_id ASC, id.invoice_detail_id ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/invoiceDP-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
		 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="generalVendorId" name="generalVendorId" value="<?php echo $generalVendorIds; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileIds; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
			<th>Invoice No</th>
			<th>Invoice Date</th>
            <th>Stockpile</th>
            <th>Original Invoice</th>
			<th>Vendor</th>
			<th>Remarks</th>
            <th>Amount</th>
			<th>Entry By</th>
			<th>Entry date</th>
            
			
           
        </tr>
    </thead>
    <tbody>
	<?php
	if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	
	?> 
	<tr>
	
	<td><?php echo $no; ?></td>
	<td><?php echo $row->invoice_no; ?></td>
	<td><?php echo $row->invoice_date; ?></td>
	<td><?php echo $row->stockpile_name; ?></td>
	<td><?php echo $row->invoice_no2; ?></td>
	<td><?php echo $row->general_vendor_name; ?></td>
	<td><?php echo $row->remarks; ?></td>
	<td><?php echo number_format($row->total_dp, 2, ".", ","); ?></td>
	<td><?php echo $row->user_name; ?></td>
	<td><?php echo $row->entry_date; ?></td>
	
	
	</tr>
	<?php
                $no++;
            }
        }else{
			//echo $sql;
		}
        ?>
	</tbody>
	
	</table>
