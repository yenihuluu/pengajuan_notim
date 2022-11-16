<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_EXTENSION . DS . 'PHPExcel.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/IOFactory.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/Cell/AdvancedValueBinder.php';


// <editor-fold defaultstate="collapsed" desc="Define Style for excel">
$styleArray = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startcolor' => array(
            'argb' => 'FFA0A0A0'
        ),
        'endcolor' => array(
            'argb' => 'FFFFFFFF'
        )
    )
);

$styleArray1 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);

$styleArray2 = array(
    'font' => array(
        'bold' => true,
        'size' => 14
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);

$styleArray3 = array(
    'font' => array(
        'bold' => true
    )
);

$styleArray4 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray5 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray6 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray7 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray8 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);
// </editor-fold>
$whereProperty1 = '';
$whereProperty = '';
$stockpileIds = $myDatabase->real_escape_string($_POST['stockpileIds']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$shipmentIds = $myDatabase->real_escape_string($_POST['shipmentIds']);
$stockpileName = 'All ';
$periodFull = '';
//$vendorName = '';
$lastSalesNo = '';

// <editor-fold defaultstate="collapsed" desc="Parameter">

if($stockpileIds != '') {
    $whereProperty .= " AND sl.stockpile_id IN ({$stockpileIds}) ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}

if($shipmentIds != '') {
    $whereProperty .= " AND sh.shipment_id IN ({$shipmentIds}) ";
    
    $sql = "SELECT * FROM shipment WHERE shipment_id IN ({$shipmentIds})";
    $resultSales = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowSales = $resultSales->fetch_object();
    $salesNo = $rowVendor->shipment_no . " ";
}

if($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1 ) BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1 ) >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND (SELECT transaction_date FROM `transaction` WHERE shipment_id = d.shipment_id AND notim_status = 0 ORDER BY transaction_date DESC LIMIT 1 ) <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}


// </editor-fold>

$fileName = "Arus_Barang" . $stockpileName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "AF";

// <editor-fold defaultstate="collapsed" desc="Query">

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

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}

if ($salesNo != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Sales No = {$salesNo}");
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "ARUS BARANG");

$rowActive++;
$rowMerge = $rowActive + 1;
$rowMerge2 = $rowMerge + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:M{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PENYERAHAN");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowMerge}:B{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowMerge}", "Nama Pembeli");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowMerge}:C{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowMerge}", "NPWP");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowMerge}:D{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowMerge}", "Shipment Code");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowMerge}:E{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowMerge}", "Tanggal BL");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowMerge}:F{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowMerge}", "BKP / JKP");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowMerge}:G{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowMerge}", "Rincian Barang");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowMerge}:H{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowMerge}", "Quantity");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowMerge}:I{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowMerge}", "Notes");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowMerge}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "PEB / Faktur Pajak");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge2}", "Nomor Seri");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge2}", "Tanggal");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge2}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge2}", "PPN");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:AF{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "PEMBELIAN");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowMerge}:N{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "No Slip");
$objPHPExcel->getActiveSheet()->mergeCells("O{$rowMerge}:O{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowMerge}", "PO");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowMerge}:P{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowMerge}", "Kontrak");
$objPHPExcel->getActiveSheet()->mergeCells("Q{$rowMerge}:Q{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowMerge}", "Nama PKP Penjual BKP/JKP");
$objPHPExcel->getActiveSheet()->mergeCells("R{$rowMerge}:R{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowMerge}", "NPWP");
$objPHPExcel->getActiveSheet()->mergeCells("S{$rowMerge}:U{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "Uraian Barang");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge2}", "Jenis Barang");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowMerge2}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowMerge2}", "@ Harga Satuan");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowMerge}:V{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowMerge}", "DPP");
$objPHPExcel->getActiveSheet()->mergeCells("W{$rowMerge}:W{$rowMerge2}");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowMerge}", "PPN");
$objPHPExcel->getActiveSheet()->mergeCells("X{$rowMerge}:Y{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowMerge}", "Surat Jalan");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowMerge2}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowMerge2}", "Tanggal");
$objPHPExcel->getActiveSheet()->mergeCells("Z{$rowMerge}:AA{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowMerge}", "Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowMerge2}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowMerge2}", "Tanggal");
$objPHPExcel->getActiveSheet()->mergeCells("AB{$rowMerge}:AC{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowMerge}", "Faktur Pajak");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowMerge2}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowMerge2}", "Tanggal");
$objPHPExcel->getActiveSheet()->mergeCells("AD{$rowMerge}:AF{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowMerge}", "Bukti Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowMerge2}", "Payment Voucher");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowMerge2}", "Tanggal");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowMerge2}", "Nilai");



$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge2}:{$lastColumn}{$rowMerge2}")->applyFromArray($styleArray4);

$rowActive = $rowMerge2;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
     if($result->num_rows > 0) {
            $no = 0;

while($row = $result->fetch_object()) {
    $rowActive++;
    
    if($row->shipment_code == $lastSalesNo) {
        $counter++;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
    } else {
        $sqlCount = "SELECT COUNT(1) AS total_row
FROM delivery d
LEFT JOIN shipment sh ON sh.`shipment_id` = d.`shipment_id`
LEFT JOIN sales sl ON sl.`sales_id` = sh.`sales_id`
LEFT JOIN customer cust ON cust.`customer_id`= sl.`customer_id`
LEFT JOIN `transaction` t ON t.`transaction_id` = d.`transaction_id`
WHERE 1=1 AND sh.`shipment_code` = '{$row->shipment_code}'";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
                    $counter = 1;
                    
                    
                    $no++;
                   
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->customer_name);
        $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($row->shipment_no, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->bl_date);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->bkp_jkp);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->barang);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->sales_quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->notes);
        $objPHPExcel->getActiveSheet()->getCell("J{$rowActive}")->setValueExplicit($row->peb_fp_no, PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->peb_fp_date);
		$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->dpp);
		$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->ppn);
	
	$dpp = $dpp + $row->dpp;
	$ppn = $ppn + $row->ppn;
	$slqty = $slqty + $row->sales_quantity;
    }
  
	$objPHPExcel->getActiveSheet()->getCell("N{$rowActive}")->setValueExplicit($row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("O{$rowActive}")->setValueExplicit($row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("P{$rowActive}")->setValueExplicit($row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->npwp_name);
    $objPHPExcel->getActiveSheet()->getCell("R{$rowActive}")->setValueExplicit($row->v_npwp, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->jenis_barang);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->price);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->dpp_pembelian);
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->ppn_pembelian);
	$objPHPExcel->getActiveSheet()->getCell("X{$rowActive}")->setValueExplicit($row->permit_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $row->loading_date);
	$objPHPExcel->getActiveSheet()->getCell("Z{$rowActive}")->setValueExplicit($row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $row->invoice_date);
	$objPHPExcel->getActiveSheet()->getCell("AB{$rowActive}")->setValueExplicit($row->tax_invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $row->tax_invoice_date);
	
	$voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
                
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
	$objPHPExcel->getActiveSheet()->getCell("AD{$rowActive}")->setValueExplicit($voucherCode ."#". $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $row->payment_amount);
    
    
   $lastSalesNo = $row->shipment_code;
	
	
	$qty = $qty + $row->quantity;
	$dppPembelian = $dppPembelian + $row->dpp_pembelian;
	$ppnPembelian = $ppnPembelian + $row->ppn_pembelian;
	$grandTotal = $grandTotal + $row->payment_amount;
  
	}
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:G{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:AF{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $slqty);
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $dpp);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $ppn);
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $qty);
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $dppPembelian);
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $ppnPembelian);
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $grandTotal);

$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("AF"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    //$objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
	
}
//$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 $objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 $objPHPExcel->getActiveSheet()->getStyle("Y" . ($headerRow + 1) . ":Y{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 //$objPHPExcel->getActiveSheet()->getStyle("AA" . ($headerRow + 1) . ":AA{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 $objPHPExcel->getActiveSheet()->getStyle("AC" . ($headerRow + 1) . ":AC{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 //$objPHPExcel->getActiveSheet()->getStyle("AE" . ($headerRow + 1) . ":AE{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("AF" . ($headerRow + 1) . ":AF{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>