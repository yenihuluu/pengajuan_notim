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
$statusProperty = '';
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$customerId = $myDatabase->real_escape_string($_POST['customerId']);
$status = $myDatabase->real_escape_string($_POST['status']);
$stockpileName = 'All ';
$customerName = 'All ';
$statusName = 'All ';

// <editor-fold defaultstate="collapsed" desc="Parameter">

if($stockpileId != '') {
    $whereProperty .= " AND sl.stockpile_id = {$stockpileId} ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}

if($customerId != '') {
    $whereProperty .= " AND sl.customer_id = {$customerId} ";
    
    $sql = "SELECT * FROM customer WHERE customer_id = {$customerId}";
    $resultCust = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowCust = $resultCust->fetch_object();
    $customerName = $rowCust->customer_name . " ";
}

if($status != '') {
    $whereProperty .= " AND sl.sales_status = {$status} ";
    
    if($status == 0) {
        $statusName = 'Open ';
    } elseif($status == 1) {
        $statusName = 'Closed ';
    } elseif($status == 2) {
        $statusName = 'Outstanding ';
    }
} 


// </editor-fold>

$fileName = "Sales Report " . $stockpileName . $customerName . $statusName . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "U";

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT s.stockpile_name, sl.sales_no, DATE_FORMAT(sl.sales_date, '%d %b %Y') AS sales_date2, sl.sales_date, cust.customer_name, 
                sl.quantity AS sales_quantity, sl.price, sl.price * sl.quantity AS sales_amount, sl.destination, t.transaction_date,
                DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
                t.slip_no, sh.shipment_code, t.send_weight, t.quantity, sl.price * t.quantity AS shipment_amount, t.shrink, 
                (t.shrink/t.send_weight) * 100 AS shrink_percent, t.shrink * sl.price AS shrink_amount,
                sl.destination
        FROM `transaction` t
        INNER JOIN shipment sh
                ON sh.shipment_id = t.shipment_id
        INNER JOIN sales sl
                ON sl.sales_id = sh.sales_id
        INNER JOIN stockpile s
                ON s.stockpile_id = sl.stockpile_id
        INNER JOIN customer cust
                ON cust.customer_id = sl.customer_id
        WHERE t.transaction_type = 2 
        AND t.company_id = {$_SESSION['companyId']} {$whereProperty} 
        ORDER BY s.stockpile_name, sl.sales_no";
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

if ($customerName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Customer = {$customerName}");
}

if ($statusName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Status = {$statusName}");
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SALES REPORT");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;

$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SALES CONTRACT");

$objPHPExcel->getActiveSheet()->setCellValue("A{$rowMerge}", "Area");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowMerge}", "SALES AGREEMENT NO");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowMerge}", "SALES AGREEMENT DATE");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowMerge}", "BUYER NAME");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowMerge}", "LAYCAN");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowMerge}", "SALES AGREEMENT QTY");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowMerge}", "PRICE / KG");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowMerge}", "SALES AMOUNT");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowMerge}", "TERM OF PAYMENT");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "PORT OF LOADING");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "PORT OF DISCHARGE (DESTINATION)");

$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:U{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "REALIZATION");

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Slip No");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "SHIPMENT CODE");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowMerge}", "QTY LOADING");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowMerge}", "QTY B/L");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowMerge}", "PRICE / KG");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowMerge}", "SALES AMOUNT");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "SHORT IN QTY");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowMerge}", "SHORT (%)");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowMerge}", "LOSS ON SHORTAGE");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$no = 1;
while($row = $result->fetch_object()) {
    
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->sales_no);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->sales_date2);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->customer_name);
//    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit(PHPExcel_Shared_Date::stringToExcel($rowPolicy->unloading_date2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", '');
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->sales_quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->price);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->sales_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", '');
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->destination);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->transaction_date2);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->shipment_code);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->send_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->price);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->shipment_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->shrink);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->shrink_percent);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->shrink_amount);

    $no++;
}
$bodyRowEnd = $rowActive;


// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("O" . ($headerRow + 1) . ":U{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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