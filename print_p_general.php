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
$checks = $_POST['checks'];

//if (!isset($checked)){
//    echo '|FAIL|Ceklis Yang Mau Di Export';
//    die();
//}

for ($i = 0; $i < sizeof($checks); $i++) {
    $checked = $checks[$i];

    if ($selectedCheck == '') {
        $selectedCheck .= $checked;
    } else {
        $selectedCheck .= ', ' . $checked;
    }
}
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$whereAvailableProperty = '';
$sql = "UPDATE pengajuan_general SET pengajuan_email_date = {STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s')} WHERE pengajuan_general_id IN ($selectedCheck) AND pengajuan_email_date is NULL ";
$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// </editor-fold>

$fileName = "Pengajuan  General " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Pengajuan General");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Payment Type");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Urgent Date");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Bank");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Cabang Bank");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Nama Akun Bank");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Termin");
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty (MT)");
//$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "PPH");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Total");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Remarks PO");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Remarks Pengajuan");

$sql = "SELECT pgd.pgd_id,pgd.vendor_name,gv.*, pg.payment_type, pg.request_payment_date,pg.remarks, pgd.termin,sum(pgd.amount) as amount, sum(pgd.pph) as pph,sum(pgd.ppn) as ppn  FROM pengajuan_general_detail pgd
LEFT JOIN general_vendor_bank gv ON gv.gv_bank_id = pgd.gv_bank_id
LEFT JOIN pengajuan_general pg ON pg.pengajuan_general_id = pgd.pg_id
WHERE pgd.pg_id in ($selectedCheck) GROUP BY pgd.pg_id";
echo $sql;

$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    $branch = $rowContent->branch;
    $bank = $rowContent->bank_name;
    $nama_akun_bank = $rowContent->beneficiary;
    $termin = $rowContent->termin;

    $qty = $rowContent->qty;
    $dpp = $rowContent->amount * $termin / 100;
    $ppn = $rowContent->ppn * $termin / 100;
    $pph = $rowContent->pph * $termin / 100;
//    $remarksPO = $rowContent->notes;
//    $hargaQty = $rowContent->price;
    $total = $dpp + $ppn - $pph;

    if ($rowContent->payment_type == 1) {
        $paymentType = 'Urgent';
    } else {
        $paymentType = 'Normal';
    }

    $reqPaymentDate = $rowContent->request_payment_date;

    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $paymentType);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $reqPaymentDate, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $bank);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $branch);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $nama_akun_bank);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}",  number_format($termin, 2, ".", ",") .'%', PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $qty);
//    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $hargaQty);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $dpp);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $pph);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $total);
//    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $remarksPO);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowContent->remarks);

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
