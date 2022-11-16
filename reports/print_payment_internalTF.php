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

// </editor-fold>

$fileName = "Internal Transfer " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "M";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Internal Transfer");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
// $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Status Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Periode From");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Periode To");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Payment Date");
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty (MT)");
//$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Request Type");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Entry Date");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Entry By");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Remarks PO");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Keterangan");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "user HO");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Keterangan Batal");

$sql = "SELECT sp.stockpile_name as stockpileName, 
        CASE WHEN tf.status = 0 then 'Pengajuan' 
            WHEN tf.status = 1 then 'In-Process'
            WHEN tf.status = 2 then 'Rejected'
        else NULL END as status1,
        CASE WHEN tf.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS requestType,
        us.user_name as username,
        (select us1.user_name from user us1 where  us1.user_id = tf.user_HO) as userHO,
        DATE_FORMAT(tf.periode_from, '%d/%m/%Y') AS dateFrom, 
        DATE_FORMAT(tf.periode_to, '%d/%m/%Y') AS dateTo,
        DATE_FORMAT(tf.request_payment_date, '%d/%m/%Y') AS PayDate,
        tf.* FROM pengajuan_internalTF tf
        LEFT JOIN stockpile sp ON sp.stockpile_id = tf.stockpile 
        left join user us on us.user_id = tf.entry_by
        where tf.status is not null AND tf.pengajuan_interalTF_id in ($selectedCheck) 
        ORDER BY pengajuan_interalTF_id DESC, tf.status DESC";

// echo $sql ;
// die();
$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    if ($rowContent->status1 == 0) {
        $tfStatus = 'Pengajuan';
    } else if ($rowContent->status1 == 1) {
        $tfStatus = 'In-Process';
    }else if ($rowContent->status1 == 2) {
        $tfStatus = 'Rejected';
    }

    if ($rowContent->request_payment_type == 0) {
        $requestType = 'NORMAL';
    } else{
        $requestType = 'URGENT';
    }

    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->pengajuan_interalTF_id);
    // $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $reqPaymentDate, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $tfStatus);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->stockpileName);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $rowContent->dateFrom, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $rowContent->dateTo, PHPExcel_Cell_DataType::TYPE_STRING);
   // $objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}",  number_format($rowContent->amount, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
       $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowContent->amount);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $rowContent->PayDate, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $requestType);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $rowContent->entry_date, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowContent->username);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowContent->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowContent->userHO);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $rowContent->remaks_reject);
}

$bodyRowEnd = $rowActive;

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("M"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
