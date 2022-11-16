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

/*$periodFrom = '';

$periodTo = '';



$amount = '';*/
$paymentFrom = '';

$paymentTo = '';

$generalVendorId = '';

$stockpileId = '';



if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	//$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
	//$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
	//$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}
/*
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}*/
if(isset($_POST['generalVendorId']) && $_POST['generalVendorId'] != '' && $_POST['generalVendorId'] != 0) {
    $generalVendorId = $_POST['generalVendorId'];
    
	for ($i = 0; $i < sizeof($generalVendorId); $i++) {
                        if($generalVendorIds == '') {
                            $generalVendorIds .= $generalVendorId[$i];
                        } else {
                            $generalVendorIds .= ','. $generalVendorId[$i];
                        }
                    }
	$whereProperty .= " AND pc.general_vendor_id IN ({$generalVendorIds}) ";
		
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
					
	$whereProperty .= "AND (CASE WHEN p.payment_location = 0 THEN 10 ELSE p.payment_location END) IN ({$stockpileIds}) ";
}

$sql = "SELECT p.payment_no, p.payment_date, gv.general_vendor_name, pc.amount_converted,pc.notes, p.`payment_location`, CASE WHEN p.payment_location = 0 THEN 'Jakarta' ELSE s.`stockpile_name` END AS stockpile_name, u.user_name, pc.entry_date, CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE s.`stockpile_code` END AS stockpile_code, b.`bank_code`, cur.`currency_code`, b.`bank_type`,
(pc.`amount` - IFNULL((SELECT COALESCE(SUM(amount_payment),0) FROM payment_cash_dp WHERE payment_cash_dp = pc.payment_cash_id AND `status` = 0),0)) AS total_dp
	FROM payment_cash pc
LEFT JOIN payment p ON p.`payment_id` = pc.`payment_id`
LEFT JOIN general_vendor gv ON gv.general_vendor_id = pc.`general_vendor_id`
LEFT JOIN `user` u ON u.`user_id` = pc.`entry_by`
LEFT JOIN stockpile s ON s.`stockpile_id` = p.`payment_location`
LEFT JOIN bank b ON b.`bank_id` = p.`bank_id`
LEFT JOIN currency cur ON cur.`currency_id` = p.`currency_id`
WHERE pc.payment_cash_method = 2 AND (pc.`amount` - IFNULL((SELECT COALESCE(SUM(amount_payment),0) FROM payment_cash_dp WHERE payment_cash_dp = pc.payment_cash_id AND `status` = 0),0)) > 0 AND p.payment_status = 0
{$whereProperty}
ORDER BY pc.payment_cash_id ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/pettyCashDP-report-xls.php" >
		 <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
		 <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
		 <input type="hidden" id="generalVendorId" name="generalVendorId" value="<?php echo $generalVendorIds; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileIds; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
			<th>Payment No</th>
			<th>Payment Date</th>
            <th>Stockpile</th>
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
	<td> <?php 
                if($row->payment_no != '') {
                    $voucherCode = $row->stockpile_code .'/'. $row->bank_code .'/'. $row->currency_code;

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
                    
                    echo $voucherCode .' # '. $row->payment_no; 
                } else {
                    echo '';
                }
                ?>
	</td>
	<td><?php echo $row->payment_date; ?></td>
	<td><?php echo $row->stockpile_name; ?></td>
	<td><?php echo $row->general_vendor_name; ?></td>
	<td><?php echo $row->notes; ?></td>
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
