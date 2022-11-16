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

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END) BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END) >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END) <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if( isset($_POST['periodFrom1']) && $_POST['periodFrom1'] != '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] != '') {

    
	$periodFrom1 = $_POST['periodFrom1'];

    $periodTo1 = $_POST['periodTo1'];

    $whereProperty1 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo1}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom1']) && $_POST['periodFrom1'] != '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] == '') {

    
	$periodFrom1 = $_POST['periodFrom1'];

    $whereProperty1 .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom1']) && $_POST['periodFrom1'] == '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] != '') {

	$periodTo1 = $_POST['periodTo1'];

    $whereProperty1 .= " AND p.payment_date <= STR_TO_DATE('{$periodTo1}', '%d/%m/%Y') ";

}


$sql = "SELECT
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
     WHEN p.vendor_id IS NOT NULL THEN 'CURAH'
     WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
     WHEN p.sales_id IS NOT NULL THEN 'SALES'
     WHEN p.general_vendor_id IS NOT NULL THEN 'LOADING/UMUM/HO'
     WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
     ELSE 'INTERNAL TRANSFER' END AS data_source,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m')
     WHEN p.sales_id IS NOT NULL THEN DATE_FORMAT(sl.entry_date, '%Y-%m')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.entry_date, '%Y-%m')
     WHEN p.freight_id IS NOT NULL THEN DATE_FORMAT(p.entry_date, '%Y-%m')
     ELSE DATE_FORMAT(p.entry_date, '%Y-%m') END AS period,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END AS transaction_date,
p.payment_id, p.payment_no, p.payment_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_name
     WHEN p.vendor_id IS NOT NULL THEN vc.vendor_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.customer_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
     WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
     ELSE '' END AS supplier_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp_name
     WHEN p.vendor_id IS NOT NULL THEN vc.npwp_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.npwp_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
     WHEN p.freight_id IS NOT NULL THEN f.npwp_name
     ELSE '' END AS npwp_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.po_no
     WHEN p.invoice_id IS NOT NULL THEN ic.po_no
     ELSE c1.po_no END AS po_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.contract_no
     WHEN p.invoice_id IS NOT NULL THEN ic.contract_no
     ELSE c1.contract_no END AS contract_no,
     s.stockpile_name, p.remarks,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted
     WHEN p.invoice_id IS NOT NULL THEN p.amount_converted
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted * 1.1
     WHEN p.freight_id IS NOT NULL THEN p.amount_converted
     ELSE '' END AS original_amount_converted,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
     WHEN p.freight_id IS NOT NULL THEN f.npwp
     ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_address
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_address
     WHEN p.freight_id IS NOT NULL THEN f.freight_address
     ELSE '' END AS address,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.tax_invoice
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_tax
     WHEN p.general_vendor_id IS NOT NULL THEN p.tax_invoice
     WHEN p.freight_id IS NOT NULL THEN p.tax_invoice
     ELSE '' END AS tax_invoice,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.tax_date, '%Y-%m-%d')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     WHEN p.freight_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     ELSE '' END AS tax_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted - p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.amount_converted) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE id.invoice_id = p.invoice_id AND id.ppn > 0 AND i.invoice_status = 0 )
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted
     WHEN p.freight_id IS NOT NULL THEN ((p.amount_converted - p.ppn_amount_converted) + p.pph_amount_converted)
     ELSE '' END AS dpp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.ppn_converted) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE id.invoice_id = p.invoice_id AND i.invoice_status = 0)
     WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.freight_id IS NOT NULL THEN p.ppn_amount_converted
     ELSE '' END AS ppn,
CASE WHEN p.invoice_id IS NOT NULL 
	 THEN (SELECT SUM(CASE WHEN id.invoice_detail_id IS NOT NULL THEN 
(SELECT SUM(amount_converted) FROM invoice_detail a LEFT JOIN invoice_dp b ON a.invoice_detail_id = b.invoice_detail_dp 
WHERE b.invoice_detail_id = id.invoice_detail_id)ELSE 0 END) AS dp 
FROM invoice_detail id WHERE id.invoice_id = p.invoice_id)
     ELSE '' END AS dp,
CASE WHEN p.invoice_id IS NOT NULL 
	 THEN (SELECT SUM(CASE WHEN id.invoice_detail_id IS NOT NULL THEN 
(SELECT SUM(ppn_converted) FROM invoice_detail a LEFT JOIN invoice_dp b ON a.invoice_detail_id = b.invoice_detail_dp 
WHERE b.invoice_detail_id = id.invoice_detail_id)ELSE 0 END) AS dp 
FROM invoice_detail id WHERE id.invoice_id = p.invoice_id)
     ELSE '' END AS ppn_dp,
CASE WHEN p.payment_location = 0 THEN 'HOF'
     ELSE s.stockpile_code END AS payment_location2,
	 b.bank_code, cur.currency_code, b.bank_type, p.payment_method, p.payment_status, p.payment_type  
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN vendor vc ON vc.vendor_id = p.vendor_id
LEFT JOIN sales sl ON sl.sales_id = p.sales_id
LEFT JOIN customer cs ON cs.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
LEFT JOIN freight f ON f.freight_id = p.freight_id
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN stockpile_contract isc ON isc.stockpile_contract_id = i.po_id
LEFT JOIN contract ic ON isc.contract_id = ic.contract_id
LEFT JOIN stockpile_contract sc1 ON sc1.stockpile_contract_id = p.stockpile_contract_id_2
LEFT JOIN contract c1 ON sc1.contract_id = c1.contract_id
LEFT JOIN bank b ON p.bank_id = b.bank_id
LEFT JOIN currency cur ON cur.currency_id = p.currency_id
WHERE 1=1 AND p.payment_method = 1 AND p.payment_status = 0
AND ((CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.ppn_converted) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE id.invoice_id = p.invoice_id AND i.invoice_status = 0)
     WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.freight_id IS NOT NULL THEN p.ppn_amount_converted
     ELSE '' END) > 0 
     OR (CASE WHEN p.invoice_id IS NOT NULL 
	 THEN (SELECT SUM(CASE WHEN id.invoice_detail_id IS NOT NULL THEN 
(SELECT SUM(ppn_converted) FROM invoice_detail a LEFT JOIN invoice_dp b ON a.invoice_detail_id = b.invoice_detail_dp 
WHERE b.invoice_detail_id = id.invoice_detail_id)ELSE 0 END) AS dp 
FROM invoice_detail id WHERE id.invoice_id = p.invoice_id)
     ELSE '' END) > 0) {$whereProperty} {$whereProperty1}";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/vat-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="periodFrom1" name="periodFrom1" value="<?php echo $periodFrom1; ?>" />
    	 <input type="hidden" id="periodTo1" name="periodTo1" value="<?php echo $periodTo1; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Period</th>
            <th rowspan="2">Data Source</th>
            <th rowspan="2">Voucher No.</th>
            <th rowspan="2">Transaction Date</th>
            <th rowspan="2">Supplier Name</th>
			<th rowspan="2">PO No.</th>
            <th rowspan="2">Contract No.</th>
            <th rowspan="2">Stockpile</th>
			<th rowspan="2">Description</th>
           	<th rowspan="2">Transaction Amount</th>
            <th rowspan="2">Payment No.</th>
            <th rowspan="2">Payment Date</th>
            <th rowspan="2">Name (Tax ID)</th>
            <th rowspan="2">Tax ID</th>
           	<th rowspan="2">Address</th>
            <th rowspan="2">Invoice Tax No.</th>
            <th rowspan="2">Invoice Tax Date.</th>
            <th rowspan="2">Total Amount</th>
            <th rowspan="2">VAT</th>
            <th rowspan="2">SPM</th>
            <th colspan="4">Vendor Documents Status</th>
            <th rowspan="2">Remarks</th>
        </tr>
        <tr>
        	<th>Contract</th>
            <th>Inv/Kwi/DO</th>
            <th>FP Doc</th>
            <th>SPT PPN</th>
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
	
	$dppPPN = ($row->ppn/$row->dpp)*100;
	if($dppPPN == 10){
		$dpp_value = $row->dpp;
	}else{
		$dpp_value = $row->ppn * 10;
	}
	
	if($row->dp != 0 && $row->ppn_dp == 0){
		$dp = 0;
	}else{
		$dp = $row->dp;
	}
	
	$dppTotal = $dpp_value - $dp;
	$ppnTotal = $row->ppn - $row->ppn_dp;
	if($row->payment_type == 1){
		$ppn = $ppnTotal * -1;
		$dpp = $dppTotal * -1;
	}else{
		$ppn = $ppnTotal;
		$dpp = $dppTotal;
	}
	
?> 
	<tr>
	<td><?php echo $no; ?></td>
    <td><?php echo $row->period; ?></td>
	<td><?php echo $row->data_source; ?></td>
	<td><?php echo $voucherNo; ?></td>
	<td><?php echo $row->transaction_date; ?></td>
	<td><?php echo $row->supplier_name; ?></td>
	<td><?php echo $row->po_no; ?></td>
    <td><?php echo $row->contract_no; ?></td>
	<td><?php echo $row->stockpile_name;?></td>
	<td><?php echo $row->remarks; ?></td>
    <td><?php echo number_format($row->original_amount_converted, 2, ".", ","); ?></td>
    <td><?php echo $voucherNo; ?></td>
	<td><?php echo $row->payment_date; ?></td>
    <td><?php echo $row->npwp_name; ?></td>
    <td><?php echo $row->npwp; ?></td>
    <td><?php echo $row->address; ?></td>
    <td><?php echo $row->tax_invoice; ?></td>
    <td><?php echo $row->tax_date; ?></td>
    <td><?php echo number_format($dpp, 2, ".", ","); ?></td>
    <td><?php echo number_format($ppn, 2, ".", ","); ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
	</tr>
   
	<?php
                $no++;
            }
        }
        ?>
	</tbody>
	</table>