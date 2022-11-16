<?php
ini_set('memory_limit', '1024M');
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

$styleArray4b = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '99CCFF')
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
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

// <editor-fold defaultstate="collapsed" desc="Query">
$module = $_POST['module'];
$From = $_POST['periodFrom'];
$End = $_POST['periodTo'];
$status = $_POST['status'];
$periodFull = $From . ' To ' . $End.' ';

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT  *, CASE WHEN quantity = '' THEN 0 ELSE quantity END as quantity,
CASE WHEN price = '' THEN 0 ELSE price END as price,
CASE WHEN creditAmount = '' THEN 0 ELSE creditAmount END as creditAmount,
CASE WHEN debitAmount = '' THEN 0 ELSE debitAmount END as debitAmount
FROM `gl_report` WHERE `gl_date` >= '$From' AND `gl_date` <= '$End' AND general_ledger_module = '$module' AND `status` = '$status' and `regenerate` = 0;";

echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "Odoo Summary Report Jatim - " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") .' - '. $module . ".xls";
$onSheet = 0;
$lastColumn = "Z";

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
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "GL Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Module");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Journal No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Account");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Partner");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Partner Name");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "PO No");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Slip No");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Invoice No");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Method");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Original Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Tax Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Cheque No");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Original Amount");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Transaction Currency");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Kurs");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while ($row = $result->fetch_object()) {
    $glId = $row->general_ledger_id;
    if ($row->debitAmount != 0) {
        $original_amount = $row->debitAmount;
    } else {
        $original_amount = $row->creditAmount;
    }
    //CUSTOM CURRENCY
    if ($row->exchange_rate > 10000) {
        $customCurrency = 'USD';
    } else {
        $customCurrency = 'IDR';
    }

    if ($row->exchange_rate == 0) {
        $rate = 1;
    } else {
        $rate = $row->exchange_rate;
    }
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no++);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->gl_date);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->general_ledger_module);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->jurnal_no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->supplier_code);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->supplier_name);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->shipment_code);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->contract_no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", $row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->price);

    $debitAmount = 0;
    if ($row->general_ledger_type == 1) {
        $debitAmount = round($row->debitAmount, 2);
    } elseif ($row->general_ledger_type == '') {
        $debitAmount = round($row->debitAmount, 2);
    }
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if ($row->general_ledger_type == 2) {
        $creditAmount = round($row->creditAmount, 2);
    } elseif ($row->general_ledger_type == '') {
        $creditAmount = round($row->creditAmount, 2);
    }

    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $creditAmount);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->general_ledger_method);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->general_ledger_transaction_type2);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("U{$rowActive}", $row->invoice_no_2, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->tax_invoice);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->cheque_no);
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $debitAmount);
    $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $customCurrency);
    $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $rate);

    if ($boolColor) {
        $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:Z{$rowActive}")->applyFromArray($styleArray4b);
    }
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
}

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("Q" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("R" . ($headerRow + 1) . ":R{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("X" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
/*$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
$cacheSettings = array( ' memoryCacheSize ' => '8MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);*/

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->setPreCalculateFormulas(false);
$objWriter->save('php://output');
// </editor-fold>
exit();