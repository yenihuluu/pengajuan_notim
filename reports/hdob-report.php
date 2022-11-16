<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';

$periodFrom = '';

$periodTo = '';

$paymentFrom = '';

$paymentTo = '';

$amount = '';

$stockpileId = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";

}
if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentFrom = $_POST['paymentFrom'];

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {

    $paymentFrom = $_POST['paymentFrom'];

    $whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {

    $paymentTo = $_POST['paymentTo'];

    $whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";

}
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {

    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
	
	$stockpile_name = array();
	$sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
		
	/*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                        if($stockpile_names == '') {
                            $stockpile_names .= "'". $stockpile_name[$i] ."'";
                        } else {
                            $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                        }
                    }*/
				
	$stockpile_names =  "'" . implode("','", $stockpile_name) . "'";
	$whereProperty .= " AND SUBSTR(t.slip_no,1,3) IN ({$stockpile_names}) ";	
	}
	}

}
if(isset($_POST['laborId']) && $_POST['laborId'] != '' && $_POST['laborId'] != 0) {
    $laborId = $_POST['laborId'];
    
	for ($i = 0; $i < sizeof($laborId); $i++) {
                        if($laborIds == '') {
                            $laborIds .= $laborId[$i];
                        } else {
                            $laborIds .= ','. $laborId[$i];
                        }
                    }
	$whereProperty .= " AND t.`labor_id` IN ({$laborIds}) ";
		
}

$sql = "SELECT t.*, DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2, l.`labor_name`, s.stockpile_name, c.po_no, c.contract_no, vendor_name,
(SELECT t2.`uc_payment_id` FROM `transaction` t2 LEFT JOIN payment p ON p.`payment_id` = t2.`uc_payment_id` WHERE t2.transaction_id = t.transaction_id {$whereProperty2}) AS ucPaymentId,
(SELECT payment_date FROM payment WHERE payment_id = t.uc_payment_id) AS payment_date
FROM `transaction` t
LEFT JOIN labor l ON l.labor_id = t.labor_id
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id
LEFT JOIN contract c ON c.contract_id = sc.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN stockpile s ON s.stockpile_id = sc.stockpile_id
WHERE t.`unloading_price` != 0 AND t.labor_id IS NOT NULL
AND (t.adj_ob IS NULL OR t.adj_ob = 0)
{$whereProperty}
AND (t.`uc_payment_id` != 0 OR t.`uc_payment_id` IS NULL)
AND (SELECT t2.`uc_payment_id` FROM `transaction` t2 LEFT JOIN payment p ON p.`payment_id` = t2.`uc_payment_id` WHERE t2.transaction_id = t.transaction_id {$whereProperty2}) IS NULL
";
//echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/hdob-report.php', {
                 periodFrom: $('input[id="periodFrom"]').val(),
				periodTo: $('input[id="periodTo"]').val(),
				paymentFrom: $('input[id="paymentFrom"]').val(),
				paymentTo: $('input[id="paymentTo"]').val(),
				stockpileId: $('input[id="stockpileId"]').val(),
				laborId: $('input[id="laborId"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

         <form class="form-horizontal" id="downloadxls" method="post" action="reports/hdob-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    	 <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
		 <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpile_names; ?>" />
		 <input type="hidden" id="laborId" name="laborId" value="<?php echo $laborIds; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            <th>No. Slip</th>
            <th>Stockpile</th>
            <th>Transaction Date</th>
            <th>Nama PKS</th>
			<th>No. PO</th>
            <th>No. Kontrak</th>
			<th>Labor</th>
            <th>Biaya Bongkar</th>
           
        </tr>
    </thead>
    <tbody>
	<?php
	//echo $sql;
	if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	$noSlip = $row->slip_no;
	$stockpile = $row->stockpile_name;
	$transactionDate = $row->transaction_date2;
	$laborName = $row->labor_name;
	$poNo = $row->po_no;
	$namaPKS = $row->vendor_name;
	$contractNo = $row->contract_no;
	$uc = $row->unloading_price;
	
?> 
	<tr>
	
	<td><?php echo $no; ?></td>
	<td><?php echo $noSlip; ?></td>
	<td><?php echo $stockpile; ?></td>
	<td><?php echo $transactionDate; ?></td>
	<td><?php echo $namaPKS; ?></td>
	<td><?php echo $poNo; ?></td>
	<td><?php echo $contractNo; ?></td>
	<td><?php echo $laborName; ?></td>
	<td style="text-align: right;"><?php echo number_format($uc, 2, ".", ","); ?></td>
	
	</tr>
	<?php
	$grandTotal = $grandTotal + $uc;
                $no++;
            }
        }
        ?>
	</tbody>
	<tfoot>
	
	<tr>
	<td colspan="8" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($grandTotal, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
	</table>