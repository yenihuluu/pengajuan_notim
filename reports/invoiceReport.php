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
$searchInvoice = '';


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND DATE_FORMAT(inv.invoice_date,'%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND DATE_FORMAT(inv.invoice_date,'%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND DATE_FORMAT(inv.invoice_date,'%Y-%m-%d') BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}
if(isset($_POST['searchInvoice']) && $_POST['searchInvoice'] != '' && $_POST['searchInvoice'] != 0) {
    $searchInvoice = $_POST['searchInvoice'];
    
	for ($i = 0; $i < sizeof($searchInvoice); $i++) {
                        if($searchInvoices == '') {
                            $searchInvoices .= $searchInvoice[$i];
                        } else {
                            $searchInvoices .= ','. $searchInvoice[$i];
                        }
                    }
	 $whereProperty .= " AND inv.invoice_id IN ({$searchInvoices}) ";
		
}

					

$sql = "SELECT inv.*, id.*, DATE_FORMAT(inv.invoice_date, '%d %b %Y') AS invoice_date, DATE_FORMAT(inv.input_date, '%d %b %Y') AS input_date, DATE_FORMAT(inv.request_date, '%d %b %Y') AS request_date, u.user_name, s.stockpile_name, gv.general_vendor_name,
		CASE WHEN id.type = 4 THEN 'LOADING'
			 WHEN id.type = 5 THEN 'UMUM'
			 WHEN id.type = 6 THEN 'HO'
		ELSE '' END AS invoiceType, ur.user_name AS user_name2,
		p.payment_no, p.payment_type,DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date,
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END AS payment_location,
(SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code,
(SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
(SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,SUM(id.tamount) AS tamount
        FROM invoice inv
        LEFT JOIN invoice_detail id
	    ON id.invoice_id = inv.invoice_id
        LEFT JOIN currency cur
            ON cur.currency_id = id.currency_id
        LEFT JOIN general_vendor gv
            ON gv.general_vendor_id = id.general_vendor_id
		LEFT JOIN USER u
			ON u.user_id = inv.entry_by
		LEFT JOIN stockpile s
			ON inv.stockpileId = s.stockpile_id
		LEFT JOIN USER ur
			ON inv.sync_by = ur.user_id
		LEFT JOIN payment p ON p.invoice_id = inv.invoice_id
        WHERE 1=1 {$whereProperty}
        GROUP BY inv.invoice_id ORDER BY inv.invoice_id DESC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/invoiceReport-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="invoiceId" name="invoiceId" value="<?php echo $searchInvoices; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
			<th>Invoice No</th>
			<th>Invoice Date</th>
            <th>Original Invoice No</th>
            <th>Vendor</th>
            <th>Stockpile</th>
			<th>Amount</th>
            <th>Remarks</th>
			<th>Request Date</th>
			<th>Input Date</th>
			<th>User Input</th>
			<th>Payment Date</th>
			<th>Payment No</th>
			<th>Return Invoice Date</th>
			<th>User Return</th>
			
           
        </tr>
    </thead>
    <tbody>
	<?php
	if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
		
	$voucherCode = '';
			if($row->payment_no != '') {
                    $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;

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
                  }
?> 
	<tr>
	
	<td><?php echo $no; ?></td>
	<td><?php echo $row->invoice_no; ?></td>
	<td><?php echo $row->invoice_date; ?></td>
	<td><?php echo $row->invoice_no2; ?></td>
	<td><?php echo $row->general_vendor_name; ?></td>
	<td><?php echo $row->stockpile_name; ?></td>
	<td><?php echo number_format($row->tamount, 2, ".", ","); ?></td>
	<td><?php echo $row->remarks; ?></td>
	<td><?php echo $row->request_date; ?></td>
	<td><?php echo $row->input_date; ?></td>
	<td><?php echo $row->user_name; ?></td>
	<td><?php echo $row->payment_date; ?></td>
	<td><?php echo $voucherCode; ?> # <?php echo $row->payment_no; ?></td>
	<td><?php echo $row->sync_date; ?></td>
	<td><?php echo $row->user_name2; ?></td>
	
	<?php
	//$total = $total + $row->amount_payment;
	
	
	?>
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