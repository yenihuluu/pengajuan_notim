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
$period = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND a.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND a.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND a.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if(isset($_POST['period']) && $_POST['period'] != '') {
    $period = $_POST['period'];
	
	$whereProperty .= " AND a.period = REPLACE(STR_TO_DATE('{$period}', '%m/%Y'),'-00','') ";
	//echo $whereProperty;
}


$sql = "SELECT a.* FROM (SELECT
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
     WHEN p.vendor_id IS NOT NULL THEN 'CURAH'
     WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
     WHEN p.sales_id IS NOT NULL THEN 'SALES'
     WHEN p.general_vendor_id IS NOT NULL THEN 'LOADING/UMUM/HO'
     ELSE 'INTERNAL TRANSFER' END AS data_source,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%Y-%m')
     WHEN p.sales_id IS NOT NULL THEN DATE_FORMAT(sl.sales_date, '%Y-%m')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.payment_date, '%Y-%m')
     ELSE DATE_FORMAT(p.payment_date, '%Y-%m') END AS period,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.sales_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.payment_date
     ELSE p.payment_date END AS transaction_date,
p.payment_id, p.payment_no, p.payment_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.contract_no
     WHEN p.invoice_id IS NOT NULL THEN ic.contract_no
     ELSE c1.contract_no END AS contract_no,
     s.stockpile_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(tamount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
     WHEN p.general_vendor_id IS NOT NULL AND p.payment_type = 1 THEN ((p.amount_converted + p.ppn_amount_converted) - p.pph_amount_converted) * -1
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted * 1.1
     ELSE '' END AS original_amount_converted,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
     ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
     ELSE '' END AS npwp_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.tax_invoice
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_tax
     WHEN p.general_vendor_id IS NOT NULL THEN p.tax_invoice
     ELSE '' END AS tax_invoice,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.tax_date, '%Y-%m-%d')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     ELSE '' END AS tax_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted - p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL AND SUBSTRING(invoice_tax, 1, 3) = 040 THEN (SELECT ROUND((SUM(amount_converted)*0.1),0) FROM invoice_detail WHERE invoice_id = p.invoice_id AND ppn > 0)
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.amount_converted) - (SELECT COALESCE(SUM(amount_converted),0) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id) FROM invoice_detail id WHERE id.invoice_id = p.invoice_id AND id.ppn > 0)
     WHEN p.general_vendor_id IS NOT NULL AND p.payment_type = 1 THEN p.amount_converted * -1
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted - p.ppn_amount_converted
     ELSE '' END AS dpp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.ppn_converted) - (SELECT COALESCE(SUM(ppn_converted),0) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id) FROM invoice_detail id WHERE id.invoice_id = p.invoice_id AND id.ppn > 0)
     WHEN p.general_vendor_id IS NOT NULL AND p.payment_type = 1 THEN p.ppn_amount_converted * -1
     WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount_converted
     ELSE '' END AS ppn,
CASE WHEN p.payment_location = 0 THEN 'HOF'
     ELSE s.stockpile_code END AS payment_location2,
	 b.bank_code, cur.currency_code, b.bank_type, p.payment_method, p.payment_status,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN sc.quantity
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(qty) FROM invoice_detail WHERE invoice_id = p.invoice_id)
     WHEN p.general_vendor_id IS NOT NULL THEN p.qty
     ELSE '' END AS qty,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.invoice_no
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_no
     WHEN p.general_vendor_id IS NOT NULL THEN p.invoice_no
     ELSE p.invoice_no END AS invoice_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.invoice_date
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.invoice_date
     ELSE '' END AS invoice_date
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN vendor vc ON vc.vendor_id = p.vendor_id
LEFT JOIN sales sl ON sl.sales_id = p.sales_id
LEFT JOIN customer cs ON cs.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN stockpile_contract isc ON isc.stockpile_contract_id = i.po_id
LEFT JOIN contract ic ON isc.contract_id = ic.contract_id
LEFT JOIN stockpile_contract sc1 ON sc1.stockpile_contract_id = p.stockpile_contract_id_2
LEFT JOIN contract c1 ON sc1.contract_id = c1.contract_id
LEFT JOIN bank b ON p.bank_id = b.bank_id
LEFT JOIN currency cur ON cur.currency_id = p.currency_id
WHERE 1=1) a WHERE a.payment_method = 1 AND a.payment_status = 0 AND a.ppn <> 0 {$whereProperty}";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/arusUang-report-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="period" name="period" value="<?php echo $period; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">PKP</th>
            <th rowspan="2">MASA</th>
            <th rowspan="2">NPWP</th>
            <th rowspan="2">NO SERI FAKTUR PAJAK</th>
            <th rowspan="2">Tanggal</th>
			<th rowspan="2">Source Modul</th>
            <th rowspan="2">QUANTITY ORDER</th>
            <th rowspan="2">DPP</th>
			<th rowspan="2">PPN</th>
           	<th rowspan="2">TOTAL</th>
            <th rowspan="2">NO. INVOICE / KWITANSI</th>
            <th rowspan="2">TGL INVOICE</th>
            <th rowspan="2">NO. SURAT JALAN</th>
            <th rowspan="2">TUJUAN PENGIRIMAN</th>
           	<th colspan="4">PEMBAYARAN</th>
            <th colspan="6">STATUS</th>
		</tr>
		<tr>
            <th rowspan="2">KAS</th>
            <th rowspan="2">TANGGAL REKENING KORAN</th>
            <th rowspan="2">NILAI</th>
            <th rowspan="2">SELISIH</th>
            <th rowspan="2">KETERANGAN SELISIH BAYAR</th>
            <th rowspan="2">SPT LAWAN TRANSAKSI</th>
			<th rowspan="2">BUKTI TRANSFER</th>
			<th rowspan="2">INVOICE</th>
			<th rowspan="2">FAKTUR PAJAK</th>
			<th rowspan="2">KONTRAK</th>
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
	$dpp_total = $row->dpp + $row->ppn;
	$total = $dpp_total - $row->original_amount_converted;
	
?> 
	<tr>
	<td><?php echo $no; ?></td>
	<td><?php echo $row->npwp_name; ?></td>
	<td><?php echo $row->period; ?></td>
	<td><?php echo $row->npwp; ?></td>
	<td><?php echo $row->tax_invoice; ?></td>
    <td><?php echo $row->tax_date; ?></td>
	<td><?php echo $row->data_source; ?></td>
	<td><?php echo number_format($row->qty, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->dpp, 2, ".", ","); ?></td>
	<td><?php echo number_format($row->ppn, 2, ".", ","); ?></td>
	<td><?php echo number_format($dpp_total, 2, ".", ","); ?></td>
	<td><?php echo $row->invoice_no; ?></td>
	<td><?php echo $row->invoice_date; ?></td>
	<td></td>
	<td><?php echo $row->stockpile_name;?></td>
	<td><?php echo $voucherNo; ?></td>
	<td><?php echo $row->payment_date; ?></td>
	<td><?php echo number_format($row->original_amount_converted, 2, ".", ","); ?></td>
	<td><?php echo number_format($total , 2, ".", ","); ?></td>
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