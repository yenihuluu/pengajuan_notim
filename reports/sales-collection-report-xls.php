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
$customerId = $myDatabase->real_escape_string($_POST['customerId']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);

$customerName = 'All ';
$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($stockpileId != '') {
    $whereProperty .= " AND sl.stockpile_id = {$stockpileId} ";

    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}

if ($customerId != '') {
    $whereProperty .= " AND sl.customer_id = {$customerId} ";

    $sql = "SELECT * FROM customer WHERE customer_id = {$customerId}";
    $resultCustomer = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowCustomer = $resultCustomer->fetch_object();
    $customerName = $rowCustomer->customer_name . " ";
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}

$sql = "SELECT sh.shipment_id, sh.shipment_date, s.stockpile_name, cust.customer_name, sh.shipment_code, sl.destination, 
        t.vehicle_no, t.quantity, sl.price, sl.price * t.quantity AS total_amount,
        p.payment_date, CONCAT(b.bank_name, ' ', cur.currency_code, ' - ', b.bank_account_no) AS bank_full, 
        CONCAT(cur2.currency_code, ' ', FORMAT(pd.amount_converted, 2)) AS amount,
        DATE_FORMAT(sh.shipment_date, '%d %b %Y') AS shipment_date2,
        DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date2
        FROM shipment sh
        INNER JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sl.stockpile_id	
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN `transaction` t
            ON t.shipment_id = sh.shipment_id
        LEFT JOIN payment_detail pd
            ON pd.shipment_id = sh.shipment_id
        LEFT JOIN payment p
            ON p.payment_id = pd.payment_id
            AND p.payment_status = 0
        LEFT JOIN currency cur2
            ON cur2.currency_id = p.currency_id
        LEFT JOIN bank b
            ON b.bank_id = p.bank_id
        LEFT JOIN currency cur
            ON cur.currency_id = b.currency_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        AND sh.shipment_date IS NOT NULL {$whereProperty}
        ORDER BY sh.shipment_code, p.entry_date";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Sales Collection " . $stockpileName . $customerName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "L";

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

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SALES COLLECTION REPORT");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PERIOD");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "STOCKPILE");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "PEMBELI");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "KODE");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "TUJUAN");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "KAPAL");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "QTY (KG)");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "HARGA / KG");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "TGL BYR");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "BANK");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "NILAI BAYAR");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

while($row = $result->fetch_object()) {
    $rowActive++;
    
    if($row->shipment_code == $lastShipmentCode) {
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
    } else {
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->shipment_date2);
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->stockpile_name);
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->customer_name);
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->shipment_code);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->destination);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vehicle_no);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->price);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->total_amount);
    }
    
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->payment_date2);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->bank_full);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->amount);
    
    $lastShipmentCode = $row->shipment_code;
}
$bodyRowEnd = $rowActive;

//</editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":A{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
//    $objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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