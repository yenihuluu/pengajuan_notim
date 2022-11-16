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
$whereProperty2 = '';
$whereProperty = '';
$sumProperty = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$vendorId = $_POST['vendorId']; 
$stockpileId = $_POST['stockpileId'];
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$paymentFrom = $myDatabase->real_escape_string($_POST['paymentFrom']);
$paymentTo = $myDatabase->real_escape_string($_POST['paymentTo']);

//$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($vendorId != '') {
    
    
    $whereProperty .= " AND c.vendor_id IN ({$vendorId})  ";

    
}

if ($stockpileId != '') {
    
    
    $whereProperty .= " AND SUBSTR(t.slip_no,1,3) IN ({$stockpileId})";

    
}

if ($paymentFrom != '' && $paymentTo != '') {
    $whereProperty2 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
	//$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = $paymentFrom . " - " . $paymentTo . " ";
} else if ($paymentFrom != '' && $paymentTo == '') {
    $whereProperty2 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y')";
	//$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = "From " . $paymentFrom . " ";
} else if ($paymentFrom == '' && $paymentTo != '') {
    $whereProperty2 .= " AND payment_date BETWEEN '2019-08-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
	//$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
   // $periodFull = "To " . $paymentTo . " ";
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN '2019-08-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
    $periodFull = "To " . $periodTo . " ";
}



$sql = "SELECT t.transaction_date, s.stockpile_name, t.`slip_no`, t.`vehicle_no`, v.`vendor_name`, c.po_no, c.contract_no,
t.`send_weight`, t.`netto_weight`, t.`quantity`, t.`unit_price`, 
(CASE WHEN  t.notim_status = 0 AND t.slip_retur IS NULL THEN t.`quantity` * t.`unit_price` ELSE 0 END)  AS amount_payment,
(SELECT payment_date FROM payment WHERE payment_id = t.payment_id {$whereProperty2}) AS payment_date,
(SELECT payment_no FROM payment WHERE payment_id = t.payment_id {$whereProperty2}) AS payment_no
FROM `transaction` t
LEFT JOIN vendor v ON v.`vendor_id` = t.`vendor_id`
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
WHERE 1=1 AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') > '2019-08-01' AND c.`contract_type` = 'C'
AND (CASE WHEN t.notim_status = 0 AND t.slip_retur IS NULL THEN t.`quantity` * t.`unit_price` ELSE 0 END) > 0
AND (c.return_shipment IS NULL OR c.return_shipment = 0) {$whereProperty}
AND (SELECT payment_date FROM payment WHERE payment_id = t.payment_id {$whereProperty2}) IS NULL
AND t.payment_id IS NULL
ORDER BY t.transaction_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Summary_HD_Curah " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Summary HD Curah");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "No. Slip");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "No. Mobil");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Nama PKS");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "No. PO");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "No. Kontrak");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Berat Kirim");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Berat Netto");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Inventory");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Unit Price");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Payment No");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	
	
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->vehicle_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->send_weight);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->netto_weight);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->unit_price);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->amount_payment);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	
	
	
	$total = $total + $row->amount_payment;

    $no++;
	}
}
$bodyRowEnd = $rowActive;

		if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:L{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Grand Total");
			$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $total);
			$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "");
			$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "");

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
   $objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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