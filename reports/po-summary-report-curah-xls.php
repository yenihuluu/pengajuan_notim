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
$vendorId = $_POST['vendorIds'];
$searchStatus = $_POST['searchStatus'];


// <editor-fold defaultstate="collapsed" desc="Parameter">


if($searchStatus == 1) {
    $whereProperty .= " AND c.return_shipment = 1 ";  
}else{
	$whereProperty .= " AND (c.return_shipment != 1 OR c.return_shipment IS NULL) ";
}
if($vendorId != '') {
    $whereProperty .= " AND c.vendor_id IN ({$vendorId})";

    $sql = "SELECT GROUP_CONCAT(vendor_name) AS vendor_name FROM vendor WHERE vendor_id IN ({$vendorId})";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";
}
// </editor-fold>

$fileName = "PO Summary Curah " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumnSummary = "O";
$lastColumn = "U";


// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header Summary">
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
/*
if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}
if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Name = {$vendorName}");
}*/

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumnSummary}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumnSummary}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SUMMARY PO LIST (CURAH)");

$rowActive++;
//$rowMerge = $rowActive + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", "Price /Kg");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", "Qty Received");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", "First Slip");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", "First Date");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", "Last Slip");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", "Last Date");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", "Payment No.");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", "Amount Payment");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("Q{$rowActive}", "Debt");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("R{$rowActive}", "Entry By");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("S{$rowActive}", "Entry Date");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("T{$rowActive}", "Purchasing Input");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("U{$rowActive}", "Purchasing Date");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);

//$rowActive = $rowMerge;

// </editor-fold>
$sql = "SELECT c.`po_no`, c.contract_no, v.`vendor_name`, v.`vendor_address`, s.`stockpile_name`, c.`price_converted`,
SUM(t.send_weight) AS qtyRecieved, MIN(slip_no) AS firstSlip, MIN(transaction_date) AS firstDate, MAX(slip_no) AS lastSlip, MAX(transaction_date) AS lastDate,
 p.`payment_date`, p.`payment_no`,
(SELECT SUM(DISTINCT(`amount_converted`))
FROM payment pp LEFT JOIN TRANSACTION tt ON tt.payment_id=pp.payment_id
LEFT JOIN stockpile_contract scc ON scc.stockpile_contract_id=tt.stockpile_contract_id
LEFT JOIN contract cc ON cc.`contract_id`=scc.`contract_id`
WHERE cc.po_no=c.po_no GROUP BY cc.po_no) AS amount_converted,
CASE WHEN p.payment_location = 0 THEN 'HO' ELSE ps.stockpile_code END AS payment_location, b.`bank_code`, cur.`currency_code`, b.`bank_type`, p.`payment_type`,
SUM(CASE WHEN t.payment_id IS NULL AND t.notim_status = 0 AND t.slip_retur IS NULL THEN t.inventory_value ELSE 0 END) AS hutang,c.quantity, u.user_name, c.entry_date,
(SELECT u.user_name FROM `user` u LEFT JOIN purchasing pu ON pu.entry_by = u.user_id LEFT JOIN po_pks po ON po.purchasing_id = pu.purchasing_id WHERE po.contract_no = REPLACE(c.contract_no,'-1','') LIMIT 1) AS purchasingInput,
(SELECT pu.entry_date FROM purchasing pu LEFT JOIN po_pks po ON po.purchasing_id = pu.purchasing_id WHERE po.contract_no = REPLACE(c.contract_no,'-1','') LIMIT 1) AS purchasingDate
FROM contract c
LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN `transaction` t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN payment p ON p.`payment_id` = t.`payment_id`
LEFT JOIN bank b ON b.`bank_id` = p.`bank_id`
LEFT JOIN currency cur ON cur.`currency_id` = p.`currency_id`
LEFT JOIN stockpile ps ON ps.stockpile_id = p.payment_location
LEFT JOIN user u ON u.user_id = c.entry_by
WHERE c.`contract_type` = 'C' AND c.`entry_date` > '2019-08-01' {$whereProperty}
GROUP BY c.`po_no`
ORDER BY c.`contract_id` ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

/*$sql = "SELECT  c.`po_no`, c.contract_no, v.`vendor_name`, v.`vendor_address`, s.`stockpile_name`, c.`price_converted`, SUM(t.`quantity`) AS qtyRecieved,
MIN(t.`slip_no`) AS firstSlip, MAX(t.`slip_no`) AS lastSlip, p.`payment_date`, p.`payment_no`, c.return_shipment,
(SELECT SUM(DISTINCT(`amount_converted`))
FROM payment pp LEFT JOIN TRANSACTION tt ON tt.payment_id=pp.payment_id
LEFT JOIN stockpile_contract scc ON scc.stockpile_contract_id=tt.stockpile_contract_id
LEFT JOIN contract cc ON cc.`contract_id`=scc.`contract_id`
WHERE cc.po_no=c.po_no GROUP BY cc.po_no) AS amount_converted,
CASE WHEN p.payment_location = 0 THEN 'HO' ELSE ps.stockpile_code END AS payment_location, b.`bank_code`, cur.`currency_code`, b.`bank_type`, p.`payment_type`,
SUM(CASE WHEN t.payment_id IS NULL AND t.notim_status = 0 AND t.slip_retur IS NULL THEN t.inventory_value ELSE 0 END) AS hutang,c.quantity
FROM `transaction` t
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN payment p ON p.`payment_id` = t.`payment_id`
LEFT JOIN bank b ON b.`bank_id` = p.`bank_id`
LEFT JOIN currency cur ON cur.`currency_id` = p.`currency_id`
LEFT JOIN stockpile ps ON ps.stockpile_id = p.payment_location
WHERE c.`contract_type` = 'C' AND t.`transaction_date` > '2019-08-01' {$whereProperty}
GROUP BY  c.`po_no`
ORDER BY t.`transaction_date` ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);*/
$no = 1;
if($result->num_rows > 0) {
while($row = $result->fetch_object()) {
      $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->currency_code;

                if($row->bank_type == 1) {
                    $voucherCode .= ' - B';
                } elseif($row->bank_type == 2) {
                    $voucherCode .= ' - P';
                } elseif($row->bank_type == 3) {
                    $voucherCode .= ' - CAS';
                }

                if($row->bank_type != 3) {
                    if($row->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                    }
                }


    $rowActive++;

   $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->getCell("B{$rowActive}")->setValueExplicit($row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vendor_address);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->price_converted);
  $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->qtyRecieved);
	$objPHPExcel->getActiveSheet()->getCell("J{$rowActive}")->setValueExplicit($row->firstSlip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->firstDate);
	$objPHPExcel->getActiveSheet()->getCell("L{$rowActive}")->setValueExplicit($row->lastSlip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->lastDate);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->getCell("O{$rowActive}")->setValueExplicit($voucherCode .'#'. $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->amount_converted);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->hutang);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->user_name);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->entry_date);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->purchasingInput);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->purchasingDate);

	$totalQty = $totalQty + $row->qtyRecieved;
	$totalPayment = $totalPayment + $row->amount_converted;

	$no++;

}

}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:G{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:H{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Total Qty Received");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $totalQty);
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:N{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("I{$rowActive}:P{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Total Amount Payment");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalPayment);

$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);



$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");


// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("Q" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


//$objPHPExcel->getActiveSheet()->getStyle("U" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("AG" . ($headerRow + 1) . ":AJ{$bodyRowEnd}")->getNumberFormat()->setFormatCode("#,##0");

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
