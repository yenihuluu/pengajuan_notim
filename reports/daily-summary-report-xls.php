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
$stockpileProperty = '';
$periodOf = '';
$month = '';
$year = '';
$periodName = '';


if(isset($_POST['periodOf']) && $_POST['periodOf'] != '') {
    $periodOf = $_POST['periodOf'];
    $splitPeriodOf = explode("/", $periodOf);
    $month = $splitPeriodOf[0];
    $year = $splitPeriodOf[1];
    
    $whereProperty .= " AND MONTH(t.unloading_date) = {$month} ";
    $whereProperty .= " AND YEAR(t.unloading_date) = {$year} ";
    
    $sqlPeriod = "SELECT DATE_FORMAT(STR_TO_DATE('{$periodOf}', '%m/%Y'), '%b %Y') AS period_name FROM dual";
    $resultPeriod = $myDatabase->query($sqlPeriod, MYSQLI_STORE_RESULT);
    $rowPeriod = $resultPeriod->fetch_object();
    $periodName = $rowPeriod->period_name . " ";
}

$sql = "SELECT day FROM calendar ORDER BY day ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$sqlCount = "SELECT * FROM stockpile WHERE stockpile_id IN (
                SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']}
            )";
$resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
$totalStockpile = $resultCount->num_rows;

$fileName = "Daily Summary Report " . $periodName . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "B";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$columnIndex = PHPExcel_Cell::columnIndexFromString($lastColumn);
$adjustedColumnIndex = $columnIndex + $totalStockpile;
$lastColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);

$rowActive = 1;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

if ($periodName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodName}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "DAILY SUMMARY");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "DATE");

$sqlHead = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN (
                SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']}
            ) ORDER BY stockpile_name";
$resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
$currentColumn = "A";
if($resultHead->num_rows > 0) {
    while($rowHead = $resultHead->fetch_object()) {
        $columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
        $adjustedColumnIndex = $columnIndex + 1;
        $nextColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);
        
        $objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", strtoupper($rowHead->stockpile_name));
        
        $currentColumn = $nextColumn;
    }
}

$columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
$adjustedColumnIndex = $columnIndex + 1;
$nextColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);
$objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", "TOTAL");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);

// </editor-fold>

//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Hello");

// <editor-fold defaultstate="collapsed" desc="Body">

while($row = $result->fetch_object()) {
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->day);
    $whereRowProperty = '';
    $total = 0;
    $whereRowProperty = " AND DAY(t.unloading_date) = {$row->day} ";
    $whereRowProperty .= $whereProperty;

    $sqlBody = "SELECT stockpile_id, stockpile_name FROM stockpile WHERE stockpile_id IN (
                    SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']}
                ) ORDER BY stockpile_name";
    $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
    $currentColumn = "A";
    if($resultBody->num_rows > 0) {
        while($rowBody = $resultBody->fetch_object()) {
            $columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
            $adjustedColumnIndex = $columnIndex + 1;
            $nextColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);

            $stockpileProperty = " AND t.stockpile_contract_id IN (SELECT sc.stockpile_contract_id FROM stockpile_contract sc WHERE sc.stockpile_id = {$rowBody->stockpile_id}) ";

            $sqlContent = "SELECT COALESCE(SUM(t.quantity), 0) AS quantity
                        FROM `transaction` t
                        WHERE 1=1 AND t.company_id = {$_SESSION['companyId']} {$whereRowProperty} {$stockpileProperty}";
            $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);

            if($resultContent->num_rows == 1) {
                $rowContent = $resultContent->fetch_object();
                $objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", $rowContent->quantity);
                $total = $total + $rowContent->quantity;
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", 0);
            }

            $currentColumn = $nextColumn;
        }
    }
    $columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
    $adjustedColumnIndex = $columnIndex + 1;
    $nextColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);
    $objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", $total);
}
$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Footer">
if ($bodyRowEnd > $headerRow + 1) {
    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
    
    $sqlFoot = "SELECT stockpile_name FROM stockpile ORDER BY stockpile_name";
    $resultFoot = $myDatabase->query($sqlFoot, MYSQLI_STORE_RESULT);
    $currentColumn = "A";
    if($resultFoot->num_rows > 0) {
        while($rowFoot = $resultFoot->fetch_object()) {
            $columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
            $adjustedColumnIndex = $columnIndex + 1;
            $nextColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);

            $objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", "=SUM({$nextColumn}" . ($headerRow + 1) . ":{$nextColumn}{$bodyRowEnd})");

            $currentColumn = $nextColumn;
        }
    }
    
    $columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
    $adjustedColumnIndex = $columnIndex + 1;
    $nextColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);
    
    $objPHPExcel->getActiveSheet()->setCellValue("{$nextColumn}{$rowActive}", "=SUM({$nextColumn}" . ($headerRow + 1) . ":{$nextColumn}{$bodyRowEnd})");

    // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$nextColumn}{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


    // Set border for table
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set row TOTAL to bold
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
}
 // </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);


// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":{$nextColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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