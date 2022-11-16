<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$stockpileIds = '';
$stockpileId = '';
$shipmentIds = '';
$shipmentId = '';
$periodFrom = '';
$periodTo = '';
$status = '';
$lastSalesNo = '';




	




	



if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }
    $whereProperty .= " AND sc.stockpile_id IN ({$stockpileIds}) ";
}

if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != '') {
    $shipmentId = $_POST['shipmentId'];
	for ($i = 0; $i < sizeof($shipmentId); $i++) {
                        if($shipmentIds == '') {
                            $shipmentIds .= $shipmentId[$i];
                        } else {
                            $shipmentIds .= ',' .$shipmentId[$i];
                        }
                    }
    $whereProperty .= " AND sh.shipment_id IN ({$shipmentIds}) ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1) BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1) >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1) <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}



$sql = "SELECT cust.customer_name, cust.npwp, sh.shipment_no ,sh.shipment_code, (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1 ) AS bl_date,
 '' AS bkp_jkp, '' AS barang, sl.`notes`, '' AS serial_no, '' AS serial_date,
(SELECT quantity FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 AND quantity >= 0 ORDER BY transaction_date DESC LIMIT 1) AS sales_quantity,
ROUND(ROUND(sl.`price` * sl.`quantity`,2) * sl.`exchange_rate`,0) AS dpp,
((cust.ppn / 100) * (ROUND(ROUND(sl.`price` * sl.`quantity`,2) * sl.`exchange_rate`,0))) AS ppn,
t.`slip_no`, c.`po_no`, c.`contract_no`, v.`npwp_name`, v.`npwp` AS v_npwp, '' AS jenis_barang, d.`quantity`, c.`price`, 
ROUND((d.quantity * c.price),5) AS dpp_pembelian, ROUND(((v.`ppn`/100) * (d.quantity * c.price)),5) AS ppn_pembelian,
t.`permit_no`, t.`loading_date`, '' AS tax_invoice_no, '' AS tax_invoice_date,
(SELECT GROUP_CONCAT(p.invoice_no) FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id) AS invoice_no,
(SELECT GROUP_CONCAT(DATE_FORMAT(p.invoice_date, '%d %b %y')) FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id) AS invoice_date,
(SELECT GROUP_CONCAT(p.payment_no) FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id) AS payment_no,
(SELECT GROUP_CONCAT(DATE_FORMAT(p.payment_date, '%d %b %y')) FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id) AS payment_date, 
(SELECT SUM(p.amount_converted) FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id) AS payment_amount,
(SELECT CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = p.payment_location) END FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id GROUP BY p.stockpile_contract_id) AS payment_location2,
(SELECT b.bank_code FROM bank b LEFT JOIN payment p ON b.bank_id = p.bank_id LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id GROUP BY p.stockpile_contract_id) AS bank_code,
(SELECT cur.currency_code FROM currency cur LEFT JOIN payment p ON cur.currency_id = p.currency_id LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id GROUP BY p.stockpile_contract_id) AS pcur_currency_code,
(SELECT b.bank_type FROM bank b LEFT JOIN payment p ON b.bank_id = p.bank_id LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id GROUP BY p.stockpile_contract_id) AS bank_type,
(SELECT p.payment_type FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id GROUP BY p.stockpile_contract_id) AS payment_type,
sl.`stockpile_id`, sl.`sales_id`, sl.peb_fp_no, sl.peb_fp_date,
(SELECT product_name FROM product WHERE product_id = sl.bkp_jkp) AS bkp_jkp 
FROM delivery d
LEFT JOIN shipment sh ON sh.`shipment_id` = d.`shipment_id`
LEFT JOIN sales sl ON sl.`sales_id` = sh.`sales_id`
LEFT JOIN customer cust ON cust.`customer_id`= sl.`customer_id`
LEFT JOIN `transaction` t ON t.`transaction_id` = d.`transaction_id`
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
WHERE 1=1  {$whereProperty}
ORDER BY d.delivery_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/arusBarang-report.php', {
                   periodFrom: $('input[id="periodFrom"]').val(),
					periodTo: $('input[id="periodTo"]').val(),
					period: $('input[id="period"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/arusBarang-report-xls.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    <input type="hidden" id="shipmentIds" name="shipmentIds" value="<?php echo $shipmentIds; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="3" style="text-align: center;">No</th>
            <th colspan="12" style="text-align: center;">PENYERAHAN</th>
			<th colspan="19" style="text-align: center;">PEMBELIAN</th>
            
        </tr>
        <tr>
            <th rowspan="2" style="text-align: center;">Nama Pembeli</th>
            <th rowspan="2" style="text-align: center;">NPWP</th>
            <th rowspan="2" style="text-align: center;">Shipment Code</th>
            <th rowspan="2" style="text-align: center;">Tanggal BL</th>
            <th rowspan="2" style="text-align: center;">BKP / JKP</th>
            <th rowspan="2" style="text-align: center;">Rincian Barang</th>
			<th rowspan="2" style="text-align: center;">Quantity</th>
			<th rowspan="2" style="text-align: center;">Notes</th>
			<th colspan="4" style="text-align: center;">PEB / Faktur Pajak</th>
			<th rowspan="2" style="text-align: center;">No Slip</th>
            <th rowspan="2" style="text-align: center;">PO</th>
            <th rowspan="2" style="text-align: center;">Kontrak</th>
            <th rowspan="2" style="text-align: center;">Nama PKP Penjual BKP/JKP</th>
            <th rowspan="2" style="text-align: center;">NPWP</th>
            <th colspan="3" style="text-align: center;">Uraian Barang</th>
			<th rowspan="2" style="text-align: center;">DPP</th>
			<th rowspan="2" style="text-align: center;">PPN</th>
			<th colspan="2" style="text-align: center;">Surat Jalan</th>
			<th colspan="2" style="text-align: center;">Invoice</th>
			<th colspan="2" style="text-align: center;">Faktur Pajak</th>
			<th colspan="3" style="text-align: center;">Bukti Pembayaran</th>
			
		</tr>
			<tr>
			
			<th style="text-align: center;">Nomor Seri</th>
			<th style="text-align: center;">Tanggal</th>
			<th style="text-align: center;">DPP</th>
			<th style="text-align: center;">PPN</th>
			<th style="text-align: center;">Jenis Barang</th>
			<th style="text-align: center;">Quantity</th>
			<th style="text-align: center;">@ Harga Satuan</th>
			<th style="text-align: center;">No</th>
			<th style="text-align: center;">Tanggal</th>
			<th style="text-align: center;">No</th>
			<th style="text-align: center;">Tanggal</th>
			<th style="text-align: center;">No</th>
			<th style="text-align: center;">Tanggal</th>
			<th style="text-align: center;">Payment Voucher</th>
			<th style="text-align: center;">Tanggal</th>
			<th style="text-align: center;">Nilai</th>
			</tr>
		
			
		
    </thead>
    <tbody>
        <?php
        if($result->num_rows > 0) {
			//echo 'test';
            $no = 0;
            while($row = $result->fetch_object()) {
				
                
                ?>
        <tr>
                <?php
                if($row->shipment_code == $lastSalesNo) {
                    $counter++;
                ?>
            <td></td>
			<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
			<td></td>
			<td></td>
            <td></td>
           
                <?php
                } else {
                    $sqlCount = "SELECT COUNT(1) AS total_row
FROM delivery d
LEFT JOIN shipment sh ON sh.`shipment_id` = d.`shipment_id`
LEFT JOIN sales sl ON sl.`sales_id` = sh.`sales_id`
LEFT JOIN customer cust ON cust.`customer_id`= sl.`customer_id`
LEFT JOIN `transaction` t ON t.`transaction_id` = d.`transaction_id`
WHERE 1=1 AND sh.shipment_code = '{$row->shipment_code}'";
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
            <td><?php echo $no; ?></td>
			<td><?php echo $row->customer_name; ?></td>
            <td><?php echo $row->npwp; ?></td>
			<td><?php echo $row->shipment_no; ?></td>
			<td><?php echo $row->bl_date; ?></td>
            <td><?php echo $row->bkp_jkp; ?></td>
            <td><?php echo $row->barang; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->sales_quantity, 2, ".", ","); ?></td>
			<td><?php echo $row->notes; ?></td>
			<td><?php echo $row->peb_fp_no; ?></td>
			<td><?php echo $row->peb_fp_date; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->dpp, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->ppn, 2, ".", ","); ?></td>
			<?php } ?>
			
            <td><?php echo $row->slip_no;; ?></td>
            <td><?php echo $row->po_no; ?></td>
			<td><?php echo $row->contract_no; ?></td>
			<td><?php echo $row->npwp_name; ?></td>
			<td><?php echo $row->v_npwp; ?></td>
			<td><?php echo $row->jenis_barang; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->price, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->dpp_pembelian, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->ppn_pembelian, 2, ".", ","); ?></td>
			<td><?php echo $row->permit_no; ?></td>
			<td><?php echo $row->loading_date; ?></td>
			<td><?php echo $row->invoice_no; ?></td>
			<td><?php echo $row->invoice_date; ?></td>
			<td><?php echo $row->tax_invoice_no; ?></td>
			<td><?php echo $row->tax_invoice_date; ?></td>
			<?php $voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
                
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
                }?>
			<td><?php echo $voucherCode; ?> # <?php echo $row->payment_no; ?></td>
			<td><?php echo $row->payment_date; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->payment_amount, 2, ".", ","); ?></td>
			
			</tr>

                    <?php
                $lastSalesNo = $row->shipment_code;
            }
        }
        ?>
    </tbody>
</table>

