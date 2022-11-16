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
$dateFrom = $myDatabase->real_escape_string($_POST['dateFrom']);
$dateTo = $myDatabase->real_escape_string($_POST['dateTo']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if (isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND DATE_FORMAT(pr.entry_date,'%d/%m/%Y') BETWEEN '{$dateFrom}' AND '{$dateTo}'";
} else if (isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] == '') {
    $dateFrom = $_POST['dateFrom'];
    $whereProperty .= " AND DATE_FORMAT(pr.entry_date,'%d/%m/%Y') >= '{$dateFrom}'";
} else if (isset($_POST['dateFrom']) && $_POST['dateFrom'] == '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND DATE_FORMAT(pr.entry_date,'%d/%m/%Y') <= '{$dateTo}'";
}
if (isset($_POST['stockpileId']) && $_POST['stockpileId']) {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND pr.stockpile_id = {$stockpileId}  ";
}

$sql = "SELECT pr.*,s.stockpile_name,u.user_name,pr.entry_date as tanggal_input, slipLama.slip_no as slip_lama, slipBaru.slip_no as slip_baru, a.user_name as approved_by FROM pengajuan_retur pr
        LEFT JOIN USER u ON u.user_id = pr.entry_by
        LEFT JOIN USER a ON a.user_id = pr.approved_by    
        LEFT JOIN stockpile s ON s.stockpile_id = pr.stockpile_id
        LEFT JOIN transaction slipLama ON slipLama.transaction_id = pr.slip_lama
        LEFT JOIN transaction slipBaru ON slipBaru.transaction_id = pr.slip_baru
        WHERE pr.entry_date IS NOT NULL {$whereProperty}
        ";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Pengajuan Retur " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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

$rowActive = 1;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));


if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Pengajuan Retur");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Slip Lama.");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Tanggal Notim");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Tanggal Return");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Slip Baru");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Alasan Retur");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Status Notim.");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Requester");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Request Date");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Approved By");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Approved Date");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Balance");
//$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Input Date");
//$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Input By");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$no = 1;

while ($row = $resultData->fetch_object()) {

    if ($row->status == 1) {
        $status = 'APPROVED';
    } elseif ($row->status == 2) {
        $status = 'FINISH';
    } else {
        $status = 'PENGAJUAN';
    }

    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $status, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->slip_lama, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->tanggal_notim);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->tanggal_return);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->slip_baru, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->remarks, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", 'LEVEL ' . $row->status_notim, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->user_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->entry_date, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $row->approved_by, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $row->approved_date, PHPExcel_Cell_DataType::TYPE_STRING);

//    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $balance);
//    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->entry_date);
//    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->user_name);

    $no++;
}
$bodyRowEnd = $rowActive;

//
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("O" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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