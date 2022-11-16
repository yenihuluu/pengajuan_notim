<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
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



$whereProperty = '';
$whereProperty1 = '';
$whereProperty2 = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$paymentFrom = $myDatabase->real_escape_string($_POST['paymentFrom']);
$paymentTo = $myDatabase->real_escape_string($_POST['paymentTo']);
$inputFrom = $myDatabase->real_escape_string($_POST['inputFrom']);
$inputTo = $myDatabase->real_escape_string($_POST['inputTo']);
$adjustmentTo = $myDatabase->real_escape_string($_POST['adjustmentTo']);
$vendorId = $myDatabase->real_escape_string($_POST['vendorId']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$status = $myDatabase->real_escape_string($_POST['status']);
$stockpileName = 'All';
$paymentDate = '';
$receiveDate = '';
$inputDate = '';
$vendorName = '';
$statusName = 'All';

// <editor-fold defaultstate="collapsed" desc="Parameter">
if($adjustmentTo != '') {
    $whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$adjustmentTo}','%d/%m/%Y') ";
	$adjustmentDate = "To " . $adjustmentTo . " ";
   
}

if($stockpileId != '') {
    $whereProperty .= "AND (SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) = {$stockpileId} ";
    
   $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}
if($vendorId != '') {
    $whereProperty .= " AND v.vendor_name = '{$vendorId}'";
    
    //$sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
   // $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$rowVendor = $resultVendor->fetch_object();
    $vendorName = $vendorId;
}

if($periodFrom != '' && $periodTo != '') {
    $whereProperty1 .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $receiveDate = $periodFrom . " - " . $periodTo . " ";
} else if($periodFrom != '' && $periodTo == '') {
    $whereProperty1 .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $receiveDate = "From " . $periodFrom . " ";
} else if($periodFrom == '' && $periodTo != '') {
    $whereProperty1 .= " AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $receiveDate = "To " . $periodTo . " ";
}

if($paymentFrom != '' && $paymentTo != '') {
    $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
    $paymentDate = $paymentFrom . " - " . $paymentTo . " ";
} else if($paymentFrom != '' && $paymentTo == '') {
     $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
    $paymentDate = "From " . $periodFrom . " ";
} else if($paymentFrom == '' && $paymentTo != '') {
   $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) BETWEEN '2016-08-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
    $paymentDate = "To " . $paymentTo . " ";
}

if($inputFrom != '' && $inputTo != '') {
     $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
    $inputDate = $inputFrom . " - " . $inputTo . " ";
} else if($inputFrom != '' && $inputTo == '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
    $inputDate = "From " . $inputFrom . " ";
} else if($inputFrom == '' && $inputTo != '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') <= STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
    $inputDate = "To " . $inputTo . " ";
}


if($status != '') {
	$statusName = $status;
   $whereProperty .= " AND (CASE WHEN ((ROUND(sc.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0)))) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) = 0 THEN 'CLOSED' 
WHEN ((ROUND(sc.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0)))) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) > 0 THEN 'OPEN' ELSE 'OUTSTANDING' END) = UPPER('{$status}') ";
}
// </editor-fold>

$fileName = "PO Outstanding Summary " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumnSummary = "H";
$lastColumn = "AI";


// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header Summary">
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

if ($paymentDate != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Payment Date = {$paymentDate}");
}

if ($receiveDate != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Received Date = {$receiveDate}");
}

if ($inputDate != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Entry Date = {$inputDate}");
}
if ($adjustmentDate != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Adjustment Date = {$adjustmentDate}");
}
if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}
if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Name = {$vendorName}");
}
if ($statusName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Status = {$statusName}");
}
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumnSummary}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumnSummary}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SUMMARY PO LIST");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;

$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PO No");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Original Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:H{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "ORDER");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:T{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Received In Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("U{$rowActive}:U{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Balance Qty Order");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:V{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Price / Kg");
$objPHPExcel->getActiveSheet()->mergeCells("W{$rowActive}:W{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "");
$objPHPExcel->getActiveSheet()->mergeCells("X{$rowActive}:X{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Adjustment Notes");
$objPHPExcel->getActiveSheet()->mergeCells("Y{$rowActive}:Y{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Payment Voucher");
$objPHPExcel->getActiveSheet()->mergeCells("Z{$rowActive}:Z{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->mergeCells("AA{$rowActive}:AB{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "First Received");
$objPHPExcel->getActiveSheet()->mergeCells("AC{$rowActive}:AD{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Last Received");
$objPHPExcel->getActiveSheet()->mergeCells("AE{$rowActive}:AE{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", "STATUS");
$objPHPExcel->getActiveSheet()->mergeCells("AF{$rowActive}:AI{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", "AGING");

$objPHPExcel->getActiveSheet()->setCellValue("G{$rowMerge}", "Qty Order");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowMerge}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowMerge}", "Jambi");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "Maredan");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "Dumai");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Padang");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Rengat");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "Bengkulu");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowMerge}", "Sampit");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowMerge}", "Tanjung Buton");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowMerge}", "Tayan");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowMerge}", "Jakarta");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "Adjustment");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowMerge}", "Total Received");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowMerge}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowMerge}", "Location");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowMerge}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowMerge}", "Location");
$objPHPExcel->getActiveSheet()->getCell("AF{$rowMerge}")->setValueExplicit("0-90", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowMerge}", "91 - 180");
$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowMerge}", "181 - 270");
$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowMerge}", ">270");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;

// </editor-fold>
$sql = "SELECT 
(SELECT stockpile_contract_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_contract, 
(SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_id, 
c.vendor_id, c.contract_id, DATE_FORMAT(c.entry_date, '%Y-%m-%d') AS entry_date, c.`po_no`, c.`contract_no`, 
(SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 AND p.payment_status = 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_no,
(SELECT p.payment_type FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_type,  
(SELECT CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_location, 
(SELECT b.bank_code FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_code,
(SELECT b.bank_type FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_type,
(SELECT cur.currency_code FROM currency cur WHERE cur.currency_id = (SELECT p.currency_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS currency_code,
(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_date,
(SELECT vendor_name FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_name, (SELECT vendor_address FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_address,
(SELECT s.stockpile_name FROM stockpile s LEFT JOIN stockpile_contract sc ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS original_stockpile, 
ROUND(c.`price_converted`,2) AS price_converted, ROUND(sc.`quantity`,2) AS quantity, ROUND(c.`price_converted` * c.`quantity`,2) AS amount_order, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) AS bengkulu, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) AS buton, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) AS maredan, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) AS padang, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) AS jambi, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) AS dumai, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) AS rengat, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) AS sampit, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) AS tayan, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) AS jakarta, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) AS total_received, 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) AS adjustment, (SELECT adjustment_notes FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) AS adjustment_notes,
((ROUND(sc.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0)))) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) AS balance_qty_order, 
ROUND((((ROUND(sc.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0)))) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2})) * c.price_converted,2) AS balance_amount_order, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_date, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_date, 
CASE WHEN ((ROUND(sc.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0)))) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) = 0 THEN 'CLOSED' 
WHEN ((ROUND(sc.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0)))) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract WHERE contract_id = c.contract_id {$whereProperty2}) > 0 THEN 'OPEN' ELSE 'OUTSTANDING' END AS `status`, 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) <= 90 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) <= 90 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
(	SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'a', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 91 AND 180 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 91 AND 180 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'b', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 181 AND 270 THEN 
	(SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 181 AND 270 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'c', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) >=271 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL AND 
	(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) >=271 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'd' 
FROM contract c 
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id 
WHERE c.contract_type = 'P' AND c.contract_status != 2 {$whereProperty}
ORDER BY c.contract_id ASC ";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
$no = 1;
if($result->num_rows > 0) {
while($row = $result->fetch_object()) {
      $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->currency_code;
                
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


    $rowActive++;
   
   $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->getCell("B{$rowActive}")->setValueExplicit($row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vendor_address);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->original_stockpile);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->jambi);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->maredan);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->dumai);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->padang);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->rengat);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->bengkulu);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->sampit);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->buton);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->tayan);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->jakarta);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->adjustment);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->total_received);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->balance_qty_order);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "");
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->adjustment_notes);
	$objPHPExcel->getActiveSheet()->getCell("Y{$rowActive}")->setValueExplicit($voucherCode .'#'. $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $row->first_date);
	$objPHPExcel->getActiveSheet()->getCell("AB{$rowActive}")->setValueExplicit($row->first_slip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $row->last_date);
	$objPHPExcel->getActiveSheet()->getCell("AD{$rowActive}")->setValueExplicit($row->last_slip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $row->status);
	$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $row->a);
	$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", $row->b);
	$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", $row->c);
	$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", $row->d);
			
	
	
	$qorder = $qorder + $row->quantity;
	$aorder = $aorder + $row->amount_order;
	$qreceived = $qreceived + $row->total_received;
	$qbalance = $qbalance + $row->balance_qty_order;
	$amount = $amount + $row->balance_amount_order;
	
	$no++;

}
	
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:F{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:Y{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $qorder);
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", '');
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $qreceived);
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $qbalance);
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", '');

$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);



$objPHPExcel->getActiveSheet()->getStyle("Z" . ($headerRow + 1) . ":AA{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("AC" . ($headerRow + 1) . ":AC{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":T{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("U" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("AF" . ($headerRow + 1) . ":AI{$bodyRowEnd}")->getNumberFormat()->setFormatCode("#,##0");

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