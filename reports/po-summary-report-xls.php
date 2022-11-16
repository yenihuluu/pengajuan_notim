<?php
ini_set('memory_limit', '5120M');
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
$vendorId = $_POST['vendorIds'];
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$status = $myDatabase->real_escape_string($_POST['status']);
$rejectStatus = $myDatabase->real_escape_string($_POST['rejectStatus']);
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

if($rejectStatus != '') {
    $whereProperty .= " AND c.contract_status = {$rejectStatus} ";
	
   
}

if($stockpileId != '') {
    $whereProperty .= "AND (SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) = {$stockpileId} ";
    
   $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}
if($vendorId != '') {
    $whereProperty .= " AND c.vendor_id IN ({$vendorId})";
    
    $sql = "SELECT GROUP_CONCAT(vendor_name) AS vendor_name FROM vendor WHERE vendor_id IN ({$vendorId})";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";
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
   $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) <= STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
    $paymentDate = "To " . $paymentTo . " ";
}

if($inputFrom != '' && $inputTo != '') {
     $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	 
	 $whereProperty3 .= " AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') <= b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
    $inputDate = $inputFrom . " - " . $inputTo . " ";
} else if($inputFrom != '' && $inputTo == '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
	
	$whereProperty3 .= " AND DATE_FORMAT(loading_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
    $inputDate = "From " . $inputFrom . " ";
} else if($inputFrom == '' && $inputTo != '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') <= STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	
	$whereProperty3 .= " AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') <= b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
    $inputDate = "To " . $inputTo . " ";
}


if($status != '') {
	$statusName = $status;
   $whereProperty .= "  AND (CASE WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +    
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) = 0 THEN 'CLOSED' 
WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +  
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) > 0 THEN 'OPEN' ELSE 'OUTSTANDING' END) = UPPER('{$status}')  ";
}
// </editor-fold>

$fileName = "PO Outstanding Summary " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumnSummary = "H";
$lastColumn = "AT";


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
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:AC{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Received In Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("AD{$rowActive}:AD{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", "Balance Qty Order");
$objPHPExcel->getActiveSheet()->mergeCells("AE{$rowActive}:AE{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", "Price / Kg");
$objPHPExcel->getActiveSheet()->mergeCells("AF{$rowActive}:AF{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", "Balance Amount Order");
$objPHPExcel->getActiveSheet()->mergeCells("AG{$rowActive}:AG{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", "Adjustment Notes");
$objPHPExcel->getActiveSheet()->mergeCells("AH{$rowActive}:AH{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "Payment Voucher");
$objPHPExcel->getActiveSheet()->mergeCells("AI{$rowActive}:AI{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->mergeCells("AJ{$rowActive}:AK{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", "First Received");
$objPHPExcel->getActiveSheet()->mergeCells("AL{$rowActive}:AM{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", "Last Received");
$objPHPExcel->getActiveSheet()->mergeCells("AN{$rowActive}:AN{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", "STATUS");
$objPHPExcel->getActiveSheet()->mergeCells("AO{$rowActive}:AT{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", "AGING");

$objPHPExcel->getActiveSheet()->setCellValue("G{$rowMerge}", "Qty Order");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowMerge}", "Amount Order");
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
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "Palembang");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowMerge}", "Pangkalan Bun");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowMerge}", "Pontianak");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowMerge}", "Samarinda");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowMerge}", "Batu Licin");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowMerge}", "Bangka Belitung");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowMerge}", "Maloy");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowMerge}", "In Transit");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowMerge}", "Total Received");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowMerge}", "Adjustment");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowMerge}", "Total Qty After Adjustment");
$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowMerge}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("AK{$rowMerge}", "Slip No");
$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowMerge}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("AM{$rowMerge}", "Slip No");
$objPHPExcel->getActiveSheet()->getCell("AO{$rowMerge}")->setValueExplicit("0 - 90", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowMerge}", "91 - 180");
$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowMerge}", "181 - 270");
$objPHPExcel->getActiveSheet()->setCellValue("AR{$rowMerge}", ">270");
$objPHPExcel->getActiveSheet()->setCellValue("AS{$rowMerge}", "Date Diff Payment");
$objPHPExcel->getActiveSheet()->setCellValue("AT{$rowMerge}", "Date Diff Receive");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;

// </editor-fold>
$sql = "SELECT 
(SELECT stockpile_contract_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_contract, 
(SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_id, 
c.vendor_id, c.contract_id, DATE_FORMAT(c.entry_date, '%Y-%m-%d') AS entry_date, c.`po_no`, c.`contract_no`,
CASE WHEN c.po_no = 'P-MIL BD Jaya 002' THEN '2015/2/206'
WHEN c.po_no = 'P-KSM/CPM 004' THEN '2015/3/541'
WHEN c.po_no = 'P-KSM/CPM 005' THEN '2015/6/203'
WHEN c.po_no = 'P-KSM/Penyangga 002' THEN '2015/3/84'
WHEN c.po_no = 'P-KSM/SAREH 002' THEN '2015/4/299'
WHEN c.po_no = 'P-PBI' THEN '2015/2/28'
WHEN c.po_no = 'P-PBI2' THEN '2015/3/78'
WHEN c.po_no = 'P-PBI3' THEN '2015/5/392'
WHEN c.po_no = 'P-PBI4' THEN '2015/5/543'
WHEN c.po_no = 'P-PBI(add1)' THEN '2015/1/117'
WHEN c.po_no = 'P-PBI(add2)' THEN '2015/2/44'
WHEN c.po_no = 'P-PBI (AHZ)' THEN '2015/5/328'
WHEN c.po_no = 'P-PBI2 (AHZ) ' THEN '2015/3/417'
WHEN c.po_no = 'P-PBI3 (AHZ)' THEN '2015/3/578' 
ELSE(SELECT GROUP_CONCAT(p.payment_no) FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  AND p.payment_status = 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) END AS payment_no,
(SELECT p.payment_type FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_type,  
(SELECT CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_location, 
(SELECT b.bank_code FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_code,
(SELECT b.bank_type FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_type,
(SELECT cur.currency_code FROM currency cur WHERE cur.currency_id = (SELECT p.currency_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS currency_code,
CASE WHEN c.po_no = 'P-BSS/JPJ 001' THEN '2015-05-18'
WHEN c.po_no = 'P-BSS/JPJ 001' THEN '2015-05-18'
WHEN c.po_no = 'P-BSS/JPJ 002' THEN '2015-05-25'
WHEN c.po_no = 'P-BSS/JPJ 003' THEN '2015-05-28'
WHEN c.po_no = 'P-BSS/JPJ 004' THEN '2015-06-15'
WHEN c.po_no = 'P-BSS/JPJ 005' THEN '2015-06-19'
WHEN c.po_no = 'P-BSS/JPJ 006' THEN '2015-06-22'
WHEN c.po_no = 'P-BSS/JPJ 007' THEN '2015-06-27'
WHEN c.po_no = 'P-MIL BD Jaya 002' THEN '2015-02-16'
WHEN c.po_no = 'P-KSM/CPM 004' THEN '2015-03-27'
WHEN c.po_no = 'P-KSM/CPM 005' THEN '2015-06-09'
WHEN c.po_no = 'P-KSM/Penyangga 002' THEN '2015-03-05'
WHEN c.po_no = 'P-KSM/SAREH 002' THEN '2015-04-17'
WHEN c.po_no = 'P-PBI' THEN '2015-02-03'
WHEN c.po_no = 'P-PBI2' THEN '2015-03-05'
WHEN c.po_no = 'P-PBI3' THEN '2015-05-20'
WHEN c.po_no = 'P-PBI4' THEN '2015-05-27'
WHEN c.po_no = 'P-PBI(add1)' THEN '2015-01-22'
WHEN c.po_no = 'P-PBI(add2)' THEN '2015-02-03'
WHEN c.po_no = 'P-PBI (AHZ)' THEN '2015-02-26'
WHEN c.po_no = 'P-PBI2 (AHZ) ' THEN '2015-03-23'
WHEN c.po_no = 'P-PBI3 (AHZ)' THEN '2015-05-31' 
ELSE (SELECT GROUP_CONCAT(p.payment_date) FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND p.payment_status = 0 AND payment_type = 2 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) END AS payment_date,
(SELECT vendor_name FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_name, (SELECT vendor_address FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_address,
(SELECT s.stockpile_name FROM stockpile s LEFT JOIN stockpile_contract sc ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS original_stockpile, 
ROUND(c.`price_converted`,2) AS price_converted, ROUND(c.`quantity`,2) AS quantity, ROUND(c.`price_converted` * c.`quantity`,2) AS amount_order, 
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
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) AS palembang,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) AS pangkalan_bun,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) AS pontianak,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) AS samarinda,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) AS batu_licin,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) AS bangka_belitung,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) AS maloy,  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0) AS total_received, 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) AS adjustment,
(SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id) AS inTransit, 
(SELECT adjustment_notes FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2} ORDER BY adj_id DESC LIMIT 1) AS adjustment_notes,
(ROUND(c.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0))) AS balance_qty_order, 
(ROUND((ROUND(c.quantity,0) - 
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0))) * c.price_converted, 2)) AS balance_amount_order, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_date, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_date, 
CASE WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +  
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) = 0 THEN 'CLOSED' 
WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +   
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) > 0 THEN 'OPEN' 
WHEN c.contract_status = 2 THEN 'REJECTED' ELSE 'OUTSTANDING' END AS `status`, 
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
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'd',
(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) AS diff_payment,
(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1))) AS diff_receive 
FROM contract c WHERE c.contract_type = 'P' {$whereProperty}
AND c.contract_status != 2 AND c.langsir = 0
GROUP BY c.po_no
ORDER BY c.contract_id ASC";

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
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->amount_order);
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
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->palembang);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->pangkalan_bun);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->pontianak);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->samarinda);
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->batu_licin);
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->bangka_belitung);
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $row->maloy);
	$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row->inTransit);
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $row->total_received);
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $row->adjustment);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $row->after_adjustment);
	$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", $row->balance_qty_order);
	$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $row->price_converted);
	$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $row->balance_amount_order);
	$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", $row->adjustment_notes);
	$objPHPExcel->getActiveSheet()->getCell("AH{$rowActive}")->setValueExplicit($voucherCode .'#'. $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", $row->first_date);
	$objPHPExcel->getActiveSheet()->getCell("AK{$rowActive}")->setValueExplicit($row->first_slip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", $row->last_date);
	$objPHPExcel->getActiveSheet()->getCell("AM{$rowActive}")->setValueExplicit($row->last_slip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", $row->status);
	$objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", $row->a);
	$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", $row->b);
	$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", $row->c);
	$objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", $row->d);
	$objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", $row->diff_payment);
	$objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", $row->diff_receive);
			
	
	
	$qorder = $qorder + $row->quantity;
	$aorder = $aorder + $row->amount_order;
	$qreceived = $qreceived + $row->total_received;
	$qbalance = $qbalance + $row->balance_qty_order;
	$amount = $amount + $row->balance_amount_order;
	$afterAdjust = $afterAdjust + $row->after_adjustment;
	$sumInTransit = $sumInTransit + $row->inTransit;
	
	$no++;

}
	
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:F{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:Y{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $qorder);
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $aorder);
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $sumInTransit);
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $qreceived);
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $afterAdjust);
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", $qbalance);
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $amount);

$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setAutoSize(true);



$objPHPExcel->getActiveSheet()->getStyle("AI" . ($headerRow + 1) . ":AJ{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("AL" . ($headerRow + 1) . ":AL{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":AF{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("U" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("AJ" . ($headerRow + 1) . ":AM{$bodyRowEnd}")->getNumberFormat()->setFormatCode("#,##0");

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