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


// <editor-fold defaultstate="collapsed" desc="Query">

$glDateFrom = '';
$glDateTo = '';
$periodFrom = '';
$periodTo = '';
$module = '';
$selectedCheck = '';

$sql = "SELECT * FROM gl_report WHERE status = 0";

if (isset($_POST['glDateFrom']) && $_POST['glDateFrom'] != '' && isset($_POST['glDateTo']) && $_POST['glDateTo'] != '') {
    $glDateFrom = $_POST['glDateFrom'];
    $glDateTo = $_POST['glDateTo'];

    $sql .= " AND gl_date BETWEEN '{$glDateFrom}' AND '{$glDateTo}'";
}

if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];

    $sql .= " AND DATE_FORMAT(entry_date,'%Y-%m-%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
}

if (isset($_POST['module']) && $_POST['module'] != '') {
    $module = $_POST['module'];

    $sql .= " AND general_ledger_module = '{$module}'";
    if ($module == 'Jurnal Memorial') {
        $sql .= " AND entry_by = '{$_SESSION['userId']}'";
    }
}

if (isset($_POST['checks']) && $_POST['checks'] != '') {
    $checks = $_POST['checks'];
    for ($i = 0; $i < sizeof($checks); $i++) {
        $glId = $checks[$i];

        if ($selectedCheck == '') {
            $selectedCheck .= $glId;
        } else {
            $selectedCheck .= ', ' . $glId;
        }
    }

    $sql .= " AND gl_id IN ($selectedCheck)";
}

$sql .= " ORDER BY jurnal_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "Journal Report - " . $module." " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Y";

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


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Journal   Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "GL Date");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Journal No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Journal Code");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", "Account No", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", "Supplier Code", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", "Supplier Name", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", "Remarks", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", "Stockpile", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", "Shipment Code", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", "PO NO", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", "Contract No", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", "Slip No", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", "Invoice No", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", "Quantity", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", "Price", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", "Debit Amount", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("Q{$rowActive}", "Credit Amount", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("R{$rowActive}", "Method", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("S{$rowActive}", "Transaction Type 2", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("T{$rowActive}", "Original Invoice", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("U{$rowActive}", "Tax Invoice", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("V{$rowActive}", "Cheque No", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("W{$rowActive}", "Original Amount", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("X{$rowActive}", "Currency", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("Y{$rowActive}", "Kurs", PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while ($row = $result->fetch_object()) {

    //ORIGINAL AMOUNT
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
    //JURNAL CODE

    if ($row->general_ledger_module == 'NOTA TIMBANG') {
        $jurnal_code = 'NOTIM';
    } elseif ($row->general_ledger_module == 'PAYMENT') {
        $jurnal_code = 'PAY';
    } elseif ($row->general_ledger_module == 'PAYMENT ADMIN') {
        $jurnal_code = 'PAYA';
    } elseif ($row->general_ledger_module == 'PETTY CASH') {
        $jurnal_code = 'PCH';
    } elseif ($row->general_ledger_module == 'RETURN INVOICE') {
        $jurnal_code = 'RINV';
    } elseif ($row->general_ledger_module == 'RETURN PAYMENT') {
        $jurnal_code = 'RPAY';
    } elseif ($row->general_ledger_module == 'STOCK TRANSIT') {
        $jurnal_code = 'STR';
    } elseif ($row->general_ledger_module == 'CONTRACT') {
        $jurnal_code = 'CTR';
    } elseif ($row->general_ledger_module == 'CONTRACT ADJUSTMENT') {
        $jurnal_code = 'CTRA';
    } elseif ($row->general_ledger_module == 'INVOICE DETAIL') {
        $jurnal_code = 'INV';
    } elseif ($row->general_ledger_module == 'Jurnal Memorial') {
        $jurnal_code = 'JM';
    } else {
        $jurnal_code = 'NULL';
    }

    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->gl_date);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->jurnal_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $jurnal_code);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->supplier_code, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->supplier_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->remarks, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->shipment_code);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->po_no);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->contract_no);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->invoice_no);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", number_format($row->quantity, 10, ".", ","));
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", number_format($row->price, 10, ".", ","));
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", number_format($row->debitAmount, 10, ".", ","));
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", number_format($row->creditAmount, 10, ".", ","));
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->general_ledger_method);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->general_ledger_transaction_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->invoice_no_2);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->tax_invoice);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->cheque_no);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", number_format($original_amount, 10, ".", ","));
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $customCurrency);
    $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", number_format($row->exchange_rate, 10, ".", ","));
    $no++;

}
$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("V" . ($headerRow + 1) . ":V{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

}
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("V)")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    // $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for QTY PKS & INVOICE VALUE
$objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":U{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set number format for OUTSTANDING - PAID
$objPHPExcel->getActiveSheet()->getStyle("AC" . ($headerRow + 1) . ":AE{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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