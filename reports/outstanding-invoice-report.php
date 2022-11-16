<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$outstanding = '';
$dateFrom = '';
$dateTo = '';

if(isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND i.entry_date BETWEEN STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$dateTo}', '%d/%m/%Y') ";
} else if(isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] == '') {
    $dateFrom = $_POST['dateFrom'];
    $whereProperty .= " AND i.entry_date >= STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['dateFrom']) && $_POST['dateFrom'] == '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND i.entry_date <= STR_TO_DATE('{$dateTo}', '%d/%m/%Y') ";
}

$sql = "SELECT i.invoice_id, i.invoice_no, i.invoice_no2, i.remarks, DATE_FORMAT(i.invoice_date, '%d %b %Y') AS invoice_date, gv.general_vendor_name, a.account_name, sh.shipment_no, c.po_no, 			
s.stockpile_name, id.qty, id.price, DATE_FORMAT(i.entry_date, '%d %b %Y') AS entry_date, u.user_name, id.ppn_converted, id.pph_converted, id.amount_converted,
CASE WHEN id.invoice_detail_id IS NOT NULL THEN (SELECT GROUP_CONCAT(invoice_id) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id ) ELSE 0 END AS iddp, 
CASE WHEN i.invoice_id IS NOT NULL THEN (SELECT COALESCE(SUM(p.original_amount_converted), 0) FROM payment p WHERE p.invoice_id = i.invoice_id AND p.payment_method = 2)
ELSE 0 END AS dp
				FROM invoice i 
				LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id`
				LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = i.po_id
				LEFT JOIN contract c ON c.contract_id = sc.contract_id
				LEFT JOIN stockpile s ON s.stockpile_id = i.stockpileId
				LEFT JOIN account a ON a.account_id = id.account_id
				LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
				LEFT JOIN shipment sh ON sh.shipment_id = id.shipment_id
				LEFT JOIN USER u ON u.user_id = i.entry_by
				WHERE 1=1 AND i.`payment_status` = 0 AND i.invoice_status = 0 {$whereProperty} ORDER BY i.invoice_no DESC";
		$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/outstanding-invoice-report.php', {
                   dateFrom: $('input[id="dateFrom"]').val(),
                dateTo: $('input[id="dateTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<br />
<form method="post" id="downloadxls" action="reports/outstanding-invoice-report-xls.php">
    <input type="hidden" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom; ?>" />
    <input type="hidden" id="dateTo" name="dateTo" value="<?php echo $dateTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr><th rowspan="2">No</th>
        	<th rowspan="2">Invoice No.</th>
            <th rowspan="2">Original Invoice No.</th>
            <th rowspan="2">Invoice Date</th>
            <th rowspan="2">Stockpile</th>
            <th rowspan="2">vendor</th>
            <th rowspan="2">Account</th>
            <th rowspan="2">Shipment Code</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Price</th>
            <th rowspan="2">Amount</th>
			<th rowspan="2">Paid</th>
			<th rowspan="2">Balance</th>
            <th rowspan="2">Input Date</th>
            <th rowspan="2">Input By</th>
			<th rowspan="2">Remarks</th>
        </tr>
        
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			
            $no = 1;
			
            while($row = $result->fetch_object()) {
			
			if($row->ppn_converted != 0 || $row->ppn_converted != 'NULL'){
				$ppn = $row->ppn_converted;
			}else{
				$ppn = 0;
			}
			
			if($row->pph_converted != 0 || $row->pph_converted != 'NULL'){
				$pph = $row->pph_converted;
			}else{
				$pph = 0;
			}
			
			$invoice_detail_id = $row->iddp;
			$dp = $row->dp;
			$total = ($row->amount_converted + $ppn) - $pph;
			
			$sql1 = "SELECT COALESCE(SUM(id.tamount), 0) AS down_payment FROM invoice_detail id
LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id` 
WHERE id.invoice_id IN ({$invoice_detail_id}) AND id.invoice_method_detail = 2 AND id.invoice_detail_status = 1";
            $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
            
            $downPayment = 0;
            if($result1 !== false && $result1->num_rows == 1) {
                $row1 = $result1->fetch_object();
                if($row1->down_payment != 0){
					$downPayment1 = $row1->down_payment;
				}else{
					$downPayment1 = 0;
				}
				
				if($dp != 0){
					$dp2 = $dp;
				}else{
					$dp2 = 0;
				}
				
				$downPayment = $downPayment1 + $dp2;
			}
				$balance = $total - $downPayment;
                ?>
        <tr>
        	<td><?php echo $no?></td>
            <td><?php echo $row->invoice_no; ?></td>
            <td><?php echo $row->invoice_no2;  ?></td>
            <td><?php echo $row->invoice_date;  ?></td>
            <td><?php echo $row->stockpile_name;  ?></td>
            <td><?php echo $row->general_vendor_name;  ?></td>
            <td><?php echo $row->account_name;  ?></td>
            <td><?php echo $row->shipment_no;  ?></td>
            <td><?php echo $row->po_no;  ?></td>
            <td><div style="text-align: right;"><?php echo number_format($row->qty, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($row->price, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($total, 2, ".", ","); ?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($downPayment, 2, ".", ","); ?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($balance, 2, ".", ","); ?></div></td>
            <td><?php echo $row->entry_date;  ?></td>
            <td><?php echo $row->user_name;  ?></td>
			<td><?php echo $row->remarks;  ?></td>
            
            
            
        </tr>
                <?php
                $no++;
            
			}
        }
        ?>
    </tbody>
</table>
