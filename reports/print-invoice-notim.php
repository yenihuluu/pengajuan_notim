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

$fileName = "Pengajuan  Notim " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Q";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Pengajuan Notim");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
// $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Status Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Request Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Payment Method");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Oringinal Invoice No");
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty (MT)");
//$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Invoice No");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Payment For");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Remarks PO");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "PPH");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Entry Date");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Entry By");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Reject Remarks");

$sql = "SELECT CASE WHEN pp.payment_method = '1' THEN 'Payment'
        WHEN pp.payment_method = '2' THEN 'Down Payment'  ELSE NULL END AS Payment, 
        CASE WHEN pp.payment_type = 2 THEN 'OUT' ELSE NULL END AS tipe, 
        CASE WHEN pp.vendor_id IS NOT NULL THEN ven.vendor_name 
        WHEN pp.freight_id IS NOT NULL THEN fr.freight_supplier 
        WHEN pp.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name 
        WHEN pp.labor_id IS NOT NULL THEN l.labor_name ELSE NULL END AS vendorName, 
        CASE WHEN pp.vendor_bank_id IS NOT NULL THEN vb.bank_name 
        WHEN pp.vendor_bank_id IS NOT NULL THEN fb.bank_name 
        WHEN pp.vendor_bank_id IS NOT NULL THEN lb.bank_name 
        WHEN pp.vendor_bank_id IS NOT NULL THEN vhb.bank_name ELSE NULL END AS bankName, 
        ino.inv_notim_no,
        CASE WHEN pp.payment_for = 0 THEN 'PKS Kontrak' 
        WHEN pp.payment_for = 1 THEN 'PKS Curah' 
        WHEN pp.payment_for = 2 THEN 'Freight Cost' 
        WHEN pp.payment_for = 9 THEN 'Handling Cost' 
        WHEN pp.payment_for = 3 THEN 'Unloading Cost' ELSE NULL END AS payment_For, 
        sp.stockpile_name AS stockpileName, us.user_name AS entryby, pp.* ,
        CASE WHEN pp.dp_status = 0 THEN 'PENGAJUAN'
        WHEN pp.dp_status = 3 THEN 'PAID' 
        WHEN pp.dp_status = 2 THEN 'CANCELED' 
        WHEN pp.dp_status = 5 THEN 'REJECTED' ELSE 'APPROVED' END AS statuspengajuan,
        rp.remarks as remark1, 
        sp.stockpile_name,
        CASE WHEN pp.urgent_payment_type = 1 then 'URGENT' else 'NORMAL' END AS urgentType,
        DATE_FORMAT(urgent_payment_date, '%d-%m-%Y') AS urgentDate
        FROM pengajuan_payment pp LEFT JOIN vendor ven ON ven.vendor_id = pp.vendor_id 
        LEFT JOIN vendor_bank vb ON vb.v_bank_id = pp.vendor_bank_id 
        LEFT JOIN freight fr ON fr.freight_id = pp.freight_id 
        LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id 
        LEFT JOIN labor l ON l.labor_id = pp.labor_id 
        LEFT JOIN freight_bank fb ON fb.f_bank_id = pp.vendor_bank_id 
        LEFT JOIN labor_bank lb ON lb.l_bank_id = pp.vendor_bank_id 
        LEFT JOIN vendor_handling_bank vhb ON vhb.vh_bank_id = pp.vendor_bank_id 
        LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
        LEFT JOIN USER us ON us.user_id = pp.user 
        LEFT JOIN invoice_notim ino ON ino.inv_notim_id = pp.inv_notim_id
        LEFT JOIN reject_ppayment rp ON rp.idPP = pp.idPP
        where pp.email_date is not null and pp.dp_status != 5 AND pp.idPP in ($selectedCheck)
        ORDER BY pp.idPP DESC";

// echo $sql ;
// die();
$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    if ($rowContent->payment_type == 1) {
        $paymentType = 'Urgent';
    } else {
        $paymentType = 'Normal';
    }

    // if ($rowContent->dp_status == 0) {
    //     $statusPengajuan = 'PENGAJUAN';
    // } else if($rowContent->status_pengajuan == 3){
    //     $paymentType = 'PAYMENT';
    // }else if($rowContent->status_pengajuan == 4){
    //     $paymentType = 'REJECTED';
    // }else if($rowContent->status_pengajuan == 5){
    //     $paymentType = 'CANCEL';
    // }else{
    //     $paymentType = 'PENGAJUAN';
    // }

    $reqPaymentDate = $rowContent->request_payment_date;

    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->idPP);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowContent->statuspengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->urgentType);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $rowContent->urgentDate, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowContent->Payment);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowContent->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowContent->invoice_no);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowContent->inv_notim_no);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowContent->vendorName);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowContent->payment_For);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowContent->ppn_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowContent->pph_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $rowContent->amount);

   // $objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}",  number_format($rowContent->ppn_amount, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}",  number_format($rowContent->pph_amount, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}",  number_format($rowContent->amount, 2, ".", ",") , PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", $rowContent->entry_date, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $rowContent->entryby);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $rowContent->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $rowContent->remark1);
}
$bodyRowEnd = $rowActive;


// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Q"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
