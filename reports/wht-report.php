
<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';
$whereProperty1 = '';

$periodFrom = '';
$periodTo = '';
$periodFrom1 = '';
$periodTo1 = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];
	

   

    $whereProperty .= " AND (a.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];
	

    $whereProperty .= " AND (a.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];
	

    $whereProperty .= " AND (a.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";

}
if( isset($_POST['periodFrom1']) && $_POST['periodFrom1'] != '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] != '') {

    
	$periodFrom1 = $_POST['periodFrom1'];

    $periodTo1 = $_POST['periodTo1'];

    $whereProperty1 .= " AND (a.payment_date BETWEEN STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo1}', '%d/%m/%Y')) ";

} else if(isset($_POST['periodFrom1']) && $_POST['periodFrom1'] != '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] == '') {

    
	$periodFrom1 = $_POST['periodFrom1'];

    $whereProperty1 .= " AND (a.payment_date >= STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y')) ";

} else if(isset($_POST['periodFrom1']) && $_POST['periodFrom1'] == '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] != '') {

	$periodTo1 = $_POST['periodTo1'];

    $whereProperty1 .= " AND (a.payment_date <= STR_TO_DATE('{$periodTo1}', '%d/%m/%Y')) ";

}



$sql = "SELECT a.* FROM (SELECT
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
     WHEN p.vendor_id IS NOT NULL THEN 'CURAH'
     WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
     WHEN p.sales_id IS NOT NULL THEN 'SALES'
     WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
     WHEN p.labor_id IS NOT NULL THEN 'UNLOADING COST'
     WHEN p.general_vendor_id IS NOT NULL THEN 'LOADING/UMUM/HO'
     WHEN p.payment_cash_id IS NOT NULL THEN 'PAYMENT CASH'
     WHEN p.vendor_handling_id IS NOT NULL THEN 'HANDLING COST'
     ELSE 'INTERNAL TRANSFER' END AS data_source,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.sales_date
     WHEN p.freight_id IS NOT NULL THEN tfc.transaction_date
     WHEN p.labor_id IS NOT NULL THEN tuc.transaction_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.payment_date
     WHEN p.vendor_handling_id IS NOT NULL THEN p.payment_date
     ELSE p.payment_date END AS transaction_date,
CASE WHEN p.payment_type = 1 THEN 'IN'	
     WHEN p.payment_type = 2 THEN 'OUT'
     ELSE '' END AS payment_type,
p.payment_id, p.invoice_id, p.payment_no, p.payment_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_name
     WHEN p.vendor_id IS NOT NULL THEN vc.vendor_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.customer_name
     WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
     WHEN p.labor_id IS NOT NULL THEN l.labor_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
     ELSE a.account_name END AS supplier_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp_name
     WHEN p.vendor_id IS NOT NULL THEN vc.npwp_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.npwp_name
     WHEN p.freight_id IS NOT NULL THEN f.npwp_name
     WHEN p.labor_id IS NOT NULL THEN l.npwp_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.npwp_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE '' END AS npwp_name,
CASE WHEN p.freight_id IS NOT NULL THEN tfc.slip_no
     WHEN p.labor_id IS NOT NULL THEN tuc.slip_no 
     ELSE '' END AS slip_no,
     s.stockpile_name, p.remarks, 
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id AND pph_converted > 0)
     WHEN tfc.transaction_id IS NOT NULL THEN (SELECT (freight_quantity * freight_price) FROM `transaction` WHERE transaction_id = tfc.transaction_id)
     WHEN tuc.transaction_id IS NOT NULL THEN (SELECT unloading_price FROM `transaction` WHERE transaction_id = tuc.transaction_id)
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM payment_cash pc WHERE pc.payment_id = p.payment_id LIMIT 1)	 
     ELSE p.original_amount_converted END AS original_amount_converted,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 147)
     WHEN p.vendor_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 147)
     WHEN p.invoice_id IS NOT NULL THEN (SELECT a.account_no FROM account a LEFT JOIN invoice_detail id ON id.account_id = a.account_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN a.account_no
     WHEN p.freight_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 29)
     WHEN p.labor_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 10)
     WHEN p.general_vendor_id IS NOT NULL THEN a.account_no
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT a.account_no FROM account a LEFT JOIN payment_cash pc ON a.account_id = pc.account_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE a.account_no END AS account_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 147)
     WHEN p.vendor_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 147)
     WHEN p.invoice_id IS NOT NULL THEN (SELECT a.account_name FROM account a LEFT JOIN invoice_detail id ON id.account_id = a.account_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN a.account_name
     WHEN p.freight_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 29)
     WHEN p.labor_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 10)
     WHEN p.general_vendor_id IS NOT NULL THEN a.account_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT a.account_name FROM account a LEFT JOIN payment_cash pc ON a.account_id = pc.account_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE a.account_name END AS account_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp
     WHEN p.vendor_id IS NOT NULL THEN vc.npwp
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.npwp
     WHEN p.freight_id IS NOT NULL THEN f.npwp
     WHEN p.labor_id IS NOT NULL THEN l.npwp
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.npwp
     ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vpph.tax_name
     WHEN p.vendor_id IS NOT NULL THEN vcpph.tax_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT pph.tax_name FROM tax pph LEFT JOIN invoice_detail id ON id.pphID = pph.tax_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cspph.tax_name
     WHEN p.freight_id IS NOT NULL THEN fpph.tax_name 
     WHEN p.labor_id IS NOT NULL THEN lpph.tax_name
     WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT pph.tax_name FROM tax pph LEFT JOIN payment_cash pc ON pc.pphID = pph.tax_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_name
     ELSE '' END AS tax_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vpph.tax_category
     WHEN p.vendor_id IS NOT NULL THEN vcpph.tax_category
     WHEN p.invoice_id IS NOT NULL AND (SELECT COUNT(*) FROM tax WHERE account_id = (SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1)) >= 1 THEN '99'
     WHEN p.invoice_id IS NOT NULL THEN (SELECT pph.tax_category FROM tax pph LEFT JOIN invoice_detail id ON id.pphID = pph.tax_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cspph.tax_category
     WHEN p.freight_id IS NOT NULL THEN fpph.tax_category
     WHEN p.labor_id IS NOT NULL THEN lpph.tax_category
     WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_category
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT pph.tax_category FROM tax pph LEFT JOIN payment_cash pc ON pc.pphID = pph.tax_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_category
     ELSE '' END AS tax_category,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vpph.tax_value
     WHEN p.vendor_id IS NOT NULL THEN vcpph.tax_value
     WHEN p.invoice_id IS NOT NULL THEN (SELECT pph.tax_value FROM tax pph LEFT JOIN invoice_detail id ON id.pphID = pph.tax_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cspph.tax_value
     WHEN p.freight_id IS NOT NULL THEN fpph.tax_value
     WHEN p.labor_id IS NOT NULL THEN lpph.tax_value
     WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_value
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT pph.tax_value FROM tax pph LEFT JOIN payment_cash pc ON pc.pphID = pph.tax_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_value
     ELSE '' END AS tax_value,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.vendor_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT CASE WHEN (SELECT COUNT(*) FROM tax WHERE account_id = id.account_id) >= 1 THEN COALESCE(SUM(id.amount_converted),0) 
     ELSE COALESCE(SUM(id.pph_converted),0) END
     FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.freight_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.labor_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.general_vendor_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(pc.pph_converted) FROM payment_cash pc WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN p.pph_amount_converted
     ELSE p.pph_amount_converted END AS tax_payable,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_address
     WHEN p.vendor_id IS NOT NULL THEN vc.vendor_address
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.customer_address
     WHEN p.freight_id IS NOT NULL THEN f.freight_address
     WHEN p.labor_id IS NOT NULL THEN l.labor_address
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_address
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_address
     ELSE '' END AS address,
CASE WHEN p.payment_location = 0 THEN 'HOF'
     ELSE s.stockpile_code END AS payment_location2,
	 b.bank_code, cur.currency_code, b.bank_type, p.payment_method, p.payment_status, p.freight_id
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN vendor vc ON vc.vendor_id = p.vendor_id
LEFT JOIN sales sl ON sl.sales_id = p.sales_id
LEFT JOIN customer cs ON cs.customer_id = sl.customer_id
LEFT JOIN freight f ON f.freight_id = p.freight_id
LEFT JOIN labor l ON l.labor_id = p.labor_id
LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
LEFT JOIN account a ON a.account_id = p.account_id
LEFT JOIN `transaction` tfc ON tfc.fc_payment_id = p.payment_id
LEFT JOIN `transaction` tuc ON tuc.uc_payment_id = p.payment_id
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN tax vpph ON vpph.tax_id = v.pph_tax_id
LEFT JOIN tax vcpph ON vcpph.tax_id = vc.pph_tax_id
LEFT JOIN tax cspph ON cspph.tax_id = cs.pph_tax_id
LEFT JOIN tax fpph ON fpph.tax_id = f.pph_tax_id
LEFT JOIN tax lpph ON lpph.tax_id = l.pph_tax_id
LEFT JOIN tax gvpph ON gvpph.tax_id = gv.pph_tax_id
LEFT JOIN invoice i ON p.invoice_id = i.invoice_id
LEFT JOIN bank b ON p.bank_id = b.bank_id
LEFT JOIN currency cur ON cur.currency_id = p.currency_id
LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = p.vendor_handling_id
LEFT JOIN tax vhpph ON vhpph.tax_id = vh.pph_tax_id
WHERE 1=1) a WHERE a.payment_status = 0 AND a.tax_payable <> 0 {$whereProperty} {$whereProperty1} ORDER BY a.payment_no ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/wht-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
         <input type="hidden" id="periodFrom1" name="periodFrom1" value="<?php echo $periodFrom1; ?>" />
    	 <input type="hidden" id="periodTo1" name="periodTo1" value="<?php echo $periodTo1; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            <th>Data Source</th>
            <th>Voucher No.</th>
            <th>Transaction Date</th>
            <th>Supplier Name</th>
			<th>Slip No.</th>
            <th>Stockpile</th>
			<th>Description</th>
            <th>Account No</th>
            <th>Account Name</th>
            <th>Transaction Amount</th>
            <th>Payment No.</th>
            <th>Payment Date</th>
            <th>Name (Tax ID)</th>
            <th>Tax ID</th>
           	<th>Address</th>
            <th>Tax Type</th>
            <th>Total Amount</th>
            <th>Tax Payable</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
	<?php
	if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
		
	$voucherNo = "";
    if($row->payment_id != '') {
        $voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->currency_code;

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

        $voucherNo = $voucherCode .' # '. $row->payment_no; 
    }else{
		$voucherNo =  $row->payment_no; 
	}
	
	if($row->payment_type == 'IN'){
		$originalAmountConverted = $row->original_amount_converted * -1;
	}else{
		$originalAmountConverted = $row->original_amount_converted;
	}

$downPayment = 0;
if($row->invoice_id != 0 && $row->invoice_id != ''){
/*$sql1 = "SELECT GROUP_CONCAT(invoice_detail_id) AS invoice_detail_id FROM invoice_detail WHERE invoice_id = {$row->invoice_id}";
$result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
if($result1 !== false && $result1->num_rows > 0) {
while($row1 = $result1->fetch_object()) {
$invoiceDetailId = $row1->invoice_detail_id;

$sqlDP = "SELECT SUM(amount_converted) AS down_payment FROM invoice_dp WHERE invoice_detail_dp IN ({$invoiceDetailId}) AND pph_converted > 0";
    		$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
			if($resultDP !== false && $resultDP->num_rows == 1) {
				 $rowDP = $resultDP->fetch_object();
			if($rowDP->down_payment != 0 && $rowDP->down_payment != ''){
				 $downPayment = $rowDP->down_payment;
			}
			//echo $downPayment;
			}
			
}
}*/
$sqlDP = "SELECT SUM(amount_payment) AS down_payment FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.`invoice_detail_id`
WHERE id.invoice_id = {$row->invoice_id}";
    		$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
			if($resultDP !== false && $resultDP->num_rows == 1) {
				 $rowDP = $resultDP->fetch_object();
			if($rowDP->down_payment != 0 && $rowDP->down_payment != ''){
				 $downPayment = $rowDP->down_payment;
			}
			//echo $downPayment;
			}
}
$originalAmount = $originalAmountConverted - $downPayment;
			
			if($row->data_source == 'PAYMENT CASH'){
				$originalAmount = ($originalAmountConverted - $downPayment);
				$taxPayable = $row->tax_payable;
				
			}else{
			if($row->tax_category == 0){
				//if($row->invoice_id != 0 && $row->invoice_id != '' && $originalAmount == 0){
				//$originalAmount = $originalAmountConverted;
				//}else{
				$originalAmount = ($originalAmountConverted - $downPayment);
				//}
				$taxPayable = $originalAmount * ($row->tax_value/100);
			}elseif($row->tax_category == 1 && $row->freight_id != '' && $row->payment_method == 2){
				$originalAmount = ($originalAmountConverted - $downPayment);
				$taxPayable = $originalAmount * ($row->tax_value/100);
			}elseif($row->tax_category == 1 && $row->freight_id != ''){
				$originalAmount = ($originalAmountConverted - $downPayment) / ((100-$row->tax_value)/100);
				$taxPayable = $originalAmount * ($row->tax_value/100);
			}elseif($row->tax_category == 1){
				$originalAmount1 = ($originalAmountConverted - $downPayment);
				$originalAmount = $originalAmountConverted - $downPayment;
				$taxPayable = $originalAmount1 * ($row->tax_value/100);
			}elseif($row->tax_category == '99'){
				$originalAmount = $row->tax_payable;
				$taxPayable = $row->tax_payable;
			}
			}
			
?> 

	<tr>
	<td><?php echo $no; ?></td>
	<td><?php echo $row->data_source; ?></td>
	<td><?php echo $voucherNo; ?></td>
	<td><?php echo $row->transaction_date; ?></td>
	<td><?php echo $row->supplier_name; ?></td>
	<td><?php echo $row->slip_no; ?></td>
	<td><?php echo $row->stockpile_name;?></td>
	<td><?php echo $row->remarks; ?></td>
	<td><?php echo $row->account_no; ?></td>
    <td><?php echo $row->account_name;?></td>
    <td><?php echo number_format($originalAmountConverted, 2, ".", ","); ?></td>
    <td><?php echo $voucherNo; ?></td>
	<td><?php echo $row->payment_date; ?></td>
    <td><?php echo $row->npwp_name; ?></td>
    <td><?php echo $row->npwp; ?></td>
    <td><?php echo $row->address; ?></td>
    <td><?php echo $row->tax_name; ?></td>
    <td><?php echo number_format($originalAmount, 2, ".", ","); ?></td>
    <td><?php echo number_format($taxPayable, 2, ".", ","); ?></td>
    <td></td>
	
	
	</tr>
	<?php
                $no++;
            }
        }
        ?>
	</tbody>
	</table>