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

$whereProperty = '';
$sumProperty = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$laborId = $myDatabase->real_escape_string($_POST['laborId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$paymentFrom = $myDatabase->real_escape_string($_POST['paymentFrom']);
$paymentTo = $myDatabase->real_escape_string($_POST['paymentTo']);
$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">
if ($laborId != '') {
	$whereProperty .= " AND t.`labor_id` IN ({$laborId}) ";
}
if ($stockpileId != '') {
    $stockpileId = $_POST['stockpileId'];
$sql = "SELECT GROUP_CONCAT(stockpile_code) as stockpileCode, stockpile_name FROM stockpile WHERE stockpile_code IN ({$stockpileId})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty .= " AND SUBSTR(t.slip_no,1,3) IN ({$stockpileId}) ";
//    $sumProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    
//    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
//    $sumProperty .= " AND t.slip_no like '{$stockpileId}%' ";

//    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
//    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $row->stockpileCode . " ";
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
    $periodFull = "To " . $periodTo . " ";
}
if ($paymentFrom != '' && $paymentTo != '') {
    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = $paymentFrom . " - " . $paymentTo . " ";
} else if ($paymentFrom != '' && $paymentTo == '') {
    $whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y')";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = "From " . $paymentFrom . " ";
} else if ($paymentFrom == '' && $paymentTo != '') {
    $whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
    $periodFull = "To " . $paymentTo . " ";
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
AND (SELECT t2.`uc_payment_id` FROM `transaction` t2 LEFT JOIN payment p ON p.`payment_id` = t2.`uc_payment_id` WHERE t2.transaction_id = t.transaction_id {$whereProperty2}) IS NULL
AND (t.`uc_payment_id` != 0 OR t.`uc_payment_id` IS NULL)";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Hutang_Dagang_OB " . $stockpileName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "I";

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


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Hutang Dagang OB");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No. Slip");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Nama PKS");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "No PO");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "No Kontrak");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Labor");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Biaya Bongkar");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

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
	
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $noSlip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $transactionDate);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $namaPKS);
	$objPHPExcel->getActiveSheet()->getCell("F{$rowActive}")->setValueExplicit($poNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getCell("G{$rowActive}")->setValueExplicit($contractNo, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $laborName);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $uc);
	$grandTotal = $grandTotal + $uc;

    $no++;
}
	}
$bodyRowEnd = $rowActive;
if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:H{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Grand Total");
			$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $grandTotal);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("I{$rowActive}:I{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }
//        if ($bodyRowEnd > $headerRow + 1) {
//            $rowActive++;
//
//            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
//            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "T O T A L");
//            $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "=SUM(L" . ($headerRow + 1) . ":L{$bodyRowEnd})");
//            $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "=SUM(M" . ($headerRow + 1) . ":M{$bodyRowEnd})");
//
//            // Set number format for Amount 
//            $objPHPExcel->getActiveSheet()->getStyle("L{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//            
//
//            // Set border for table
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//            // Set row TOTAL to bold
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
//        }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
   
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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
exit();