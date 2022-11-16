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

$whereProperty1 = '';
$whereProperty2 = '';
$whereProperty3 = '';
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$searchAccount = $myDatabase->real_escape_string($_POST['searchAccount']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$stockpileName = 'All ';
$accountName = 'ALL ';
$periodFull = '';
// <editor-fold defaultstate="collapsed" desc="Query">

if ($stockpileId != '') {
    $stockpileId = $_POST['stockpileId'];
    $sql = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_name = '{$stockpileId}'";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty1 .= " AND (SELECT stockpile_name FROM stockpile WHERE stockpile_id = id.stockpile_remark) = '{$stockpileId}' ";
    $stockpileName = $row->stockpile_name . " ";
}

if ($searchAccount != '') {
    $searchAccount = $_POST['searchAccount'];
    $sql = "SELECT account_name FROM account WHERE account_no = {$searchAccount}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty2 .= " AND (SELECT account_no FROM account WHERE account_id = id.account_id) IN ({$searchAccount}) ";
    $accountName = $row->account_name . " ";
} else {
	$whereProperty2 .= " AND (SELECT account_no FROM account WHERE account_id = id.account_id) IN (521000, 520900)";
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty3 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty3 .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty3 .= " AND p.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}

$sql = "SELECT p.`payment_no`, p.payment_date, p.`invoice_id`,
CASE WHEN i.po_id IS NOT NULL THEN i.po_id
WHEN id.poId IS NOT NULL THEN id.poId
ELSE '' END AS po,
(SELECT account_no FROM account WHERE account_id = id.account_id) AS account_no,
(SELECT stockpile_name FROM stockpile WHERE stockpile_id = id.stockpile_remark) AS stockpile_name,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT po_no FROM contract WHERE contract_id = id.poId)
ELSE '' END AS po_no,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT contract_no FROM contract WHERE contract_id = id.poId)
ELSE '' END AS contract_no,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT v.vendor_name FROM vendor v LEFT JOIN contract c ON c.vendor_id = v.vendor_id  LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT v.vendor_name FROM vendor v LEFT JOIN contract c ON c.vendor_id = v.vendor_id WHERE contract_id = id.poId)
ELSE '' END AS vendor_name,
(SELECT general_vendor_name FROM general_vendor WHERE general_vendor_id = id.general_vendor_id) AS general_vendor_name,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.quantity FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT quantity FROM contract WHERE contract_id = id.poId)
ELSE '' END AS quantity,
CASE WHEN i.po_id IS NOT NULL THEN (SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = i.po_id)
WHEN id.poId IS NOT NULL THEN (SELECT price_converted FROM contract WHERE contract_id = id.poId)
ELSE '' END AS price_converted,
id.price AS fee_price,
CASE WHEN p.invoice_id IS NOT NULL THEN id.amount_converted
WHEN p.payment_type = 1 THEN -1*p.amount_converted
ELSE p.amount_converted END AS amountConverted,
id.notes
FROM payment p
LEFT JOIN invoice i ON p.`invoice_id` = i.`invoice_id`
LEFT JOIN invoice_detail id ON id.`invoice_id` = i.`invoice_id`
WHERE p.`invoice_id` IS NOT NULL AND p.payment_status = 0 {$whereProperty1} {$whereProperty2} {$whereProperty3}
ORDER BY p.payment_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "FeeReport " . $stockpileName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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

if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Account = {$accountName}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Fee Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "PO No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Price /Kg");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Quantity Order");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Beneficiary");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Fee");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Payment Voucher");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Remarks");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	$price = $row->price_converted;
	$stockpile = $row->stockpile_name;
	$vendor = $row->vendor_name;
	$poNo = $row->po_no;
	$beneficiary = $row->general_vendor_name;
	$contractNo = $row->contract_no;
	$quantity = $row->quantity;
	$amountConverted = $row->amountConverted;
	if($quantity != 0){
		$fee = $amountConverted / $quantity;
	}else{
		$fee = 0;
	}
	$paymentNo = $row->payment_no;
	$paymentDate = $row->payment_date;
	$remarks = $row->notes;
	
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $stockpile);
	$objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($poNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($contractNo, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $vendor);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $price);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $beneficiary);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $fee);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $amountConverted);
	$objPHPExcel->getActiveSheet()->getCell("K{$rowActive}")->setValueExplicit($paymentNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $paymentDate);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $remarks);

    $no++;
}
	}
$bodyRowEnd = $rowActive;

//        if ($bodyRowEnd > $headerRow + 1) {
//            $rowActive++;
//
//            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
//            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "T O T A L");
//            $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "=SUM(L" . ($headerRow + 1) . ":L{$bodyRowEnd})");
//            $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "=SUM(M" . ($headerRow + 1) . ":M{$bodyRowEnd})");
//
//            // Set number format for Amount 
//            $objPHPExcel->getActiveSheet()->getStyle("L{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//            
//
//            // Set border for table
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//            // Set row TOTAL to bold
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
//        }
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
    $objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
   
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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