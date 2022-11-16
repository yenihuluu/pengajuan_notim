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
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
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


$whereAvailableProperty = '';

$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$periodFull = '';


if ($periodTo != '') {
    $periodFull = "To " . $periodTo . " ";
}

// </editor-fold>

$fileName = "Stock Transit Report " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "O";

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

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stock Transit Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Kode Mutasi");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile From");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile To");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Register Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Amount Register");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Netto Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Posting Date");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Amount PKS DRAFT");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Amount PKS SP");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Amount Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Inventory Value");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Aging");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Status");

$sqlContent = "CALL SUMMutasi('{$periodTo}')";
$resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
echo $sqlContent;
//GrandTotal
$grandTotalAmountPksDraft = 0;
$grandTotalAmountPksSp = 0;
$grandTotalAmountRegister = 0;
$grandTotalAmountInvoice = 0;
$grandTotalInventoryValue = 0;
while ($rowContent = $resultContent->fetch_object()) {

    $amountPksInvoice = $rowContent->AmtPKSDraft + $rowContent->AmtInvoice;
    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:K{$rowActive}")->applyFromArray($styleArray4);
//    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->kode_mutasi);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowContent->StockpileAsal);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->StockpileTujuan);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowContent->TglRegister);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowContent->AmtRegister);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowContent->sendWeightTransit);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowContent->nettoStockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowContent->TglPost);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowContent->amountPksSp);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowContent->AmtPKSDraft);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowContent->AmtInvoice);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $amountPksInvoice);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $rowContent->InventoryValue);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $rowContent->Aging);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $rowContent->MutasiStatus);


    $grandTotalAmountPksDraft += $rowContent->AmtPKSDraft;
	$grandTotalAmountPksSp += $rowContent->amountPksSp;
    $grandTotalAmountRegister += $rowContent->AmtRegister;
    $grandTotalAmountInvoice += $rowContent->AmtInvoice;
    $grandTotalInventoryValue += $rowContent->InventoryValue;
}

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:O{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:D{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $grandTotalAmountRegister);
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $grandTotalAmountPksSp);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $grandTotalAmountPksDraft);
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $grandTotalAmountInvoice);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $grandTotalInventoryValue);


$bodyRowEnd = $rowActive;


// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("N"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}

//set date format
$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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