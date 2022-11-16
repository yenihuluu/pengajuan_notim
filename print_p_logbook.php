<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once './assets/include/path_variable.php';

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


// </editor-fold>


$whereAvailableProperty = '';

// </editor-fold>

$fileName = "Pengajuan Logbook " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "J";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Pengajuan Logbook");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Bank");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Cabang Bank");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Nama Akun Bank");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "PPH");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Tax Remark");

$sql = "SELECT  pl.*,s.stockpile_name,b.bank_name,user.user_name FROM pengajuan_logbook pl
LEFT JOIN stockpile s ON s.stockpile_id = pl.stockpile_id
LEFT JOIN master_bank b on b.master_bank_id = pl.master_bank_id
LEFT JOIN user on user.user_id = pl.entry_by
WHERE pl.entry_by = {$_SESSION['userId']}
AND pl.status = 0
ORDER BY pl.id DESC";

$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    $cabangBank = $rowContent->cabang_bank;
    $bank = $rowContent->bank_name;
    $nama_akun_bank = $rowContent->nama_akun_bank;
    $qty = $rowContent->qty;
    $dpp = $rowContent->dpp;
    $ppn = $rowContent->ppn;
    $pph = $rowContent->pph;
    $taxRemark = $rowContent->tax_remark;
    $hargaQty = $rowContent->harga_qty;
    $total = $rowContent->total;

    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $bank);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $cabangBank);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $nama_akun_bank);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $qty);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $hargaQty);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $dpp);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $pph);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $total);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $taxRemark);
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

$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Stock Transit Report">

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
