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
if (isset($_POST['checks'])) {     
    $checks = $_POST['checks'];

    for ($i = 0; $i < sizeof($checks); $i++) {
        $checked = $checks[$i];
        if ($selectedCheck == '') {
            $selectedCheck .= $checked;
        } else {
            $selectedCheck .= ', ' . $checked;
        }
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
$lastColumn = "P";

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
// $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Payment Type");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Request Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Pengajuan No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Invoice No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Invoice Date");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Original Invoice No");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Vendor");
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty (MT)");
//$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Request Date");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Amount");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Remarks PO");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Remarks Reject/Cancel");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Input By");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Input Date");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Reject/Cancel");

$sql = "SELECT pg.*, pgd.*,i.invoice_no, DATE_FORMAT(pg.invoice_date, '%d %b %Y') AS invoice_date, DATE_FORMAT(pg.input_date, '%d %b %Y') AS input_date, DATE_FORMAT(pg.request_date, '%d %b %Y') AS request_date, u.user_name, s.stockpile_name, gv.general_vendor_name,
        CASE WHEN pgd.type = 4 THEN 'LOADING'
            WHEN pgd.type = 5 THEN 'UMUM'
            WHEN pgd.type = 6 THEN 'HO'
        ELSE '' END AS invoiceType, ur.user_name AS user_name2,
            sum((pgd.amount + pgd.ppn)-pgd.pph) as amount_total
        FROM pengajuan_general pg
        LEFT JOIN pengajuan_general_detail pgd
        ON pgd.pg_id = pg.pengajuan_general_id
        LEFT JOIN invoice i
            ON i.invoice_id = pg.invoice_id
        LEFT JOIN currency cur
            ON cur.currency_id = pgd.currency_id
        LEFT JOIN general_vendor gv
            ON gv.general_vendor_id = pgd.general_vendor_id
        LEFT JOIN USER u
            ON u.user_id = pg.entry_by
        LEFT JOIN stockpile s
            ON pg.stockpileId = s.stockpile_id
        LEFT JOIN USER ur
            ON pg.sync_by = ur.user_id
        WHERE pg.pengajuan_general_id in ($selectedCheck)  
        GROUP BY pg.pengajuan_general_id ORDER BY pg.pengajuan_general_id DESC LIMIT 5000
        ";

// echo $sql ;
// die();
$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    if ($rowContent->payment_type == 1) {
        $paymentType = 'Urgent';
    } else {
        $paymentType = 'Normal';
    }

    if ($rowContent->status_pengajuan == 2) {
        $statusPengajuan = 'INVOICE';
    } else if($rowContent->status_pengajuan == 3){
        $paymentType = 'PAYMENT';
    }else if($rowContent->status_pengajuan == 4){
        $paymentType = 'REJECTED';
    }else if($rowContent->status_pengajuan == 5){
        $paymentType = 'CANCEL';
    }else{
        $paymentType = 'PENGAJUAN';
    }

    $reqPaymentDate = $rowContent->request_payment_date;

    $rowActive++;
    // $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    // $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $paymentType);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $reqPaymentDate, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->pengajuan_no);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowContent->invoice_no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $rowContent->invoice_date, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $rowContent->invoice_no2 , PHPExcel_Cell_DataType::TYPE_STRING);

   // $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowContent->invoice_no2);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowContent->general_vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $rowContent->request_date, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowContent->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowContent->amount_total);
   // $objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}",  number_format($rowContent->amount_total, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowContent->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowContent->reject_remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $paymentType);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $rowContent->user_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", $rowContent->entry_date, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", $rowContent->reject_date, PHPExcel_Cell_DataType::TYPE_STRING);
}
$bodyRowEnd = $rowActive;


// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("P"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("O" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
