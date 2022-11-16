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
$vendorNames = $_POST['vendorName'];
$stockpileNames = $_POST['stockpileName'];


// <editor-fold defaultstate="collapsed" desc="Query">

if($stockpileNames != '') {

   
	$whereProperty .= " AND (SELECT s.stockpile_name FROM stockpile s WHERE stockpile_id = sc.stockpile_id) IN ({$stockpileNames}) ";
	
	$stockpileName = $stockpileNames;
}

if($vendorNames  != '') {
			
    $whereProperty .= " AND (SELECT v.vendor_name FROM vendor v WHERE v.vendor_id = c.`vendor_id`) IN ({$vendorNames}) ";	
	$vendorName = $vendorNames;
}

$sql = "SELECT sc.stockpile_contract_id,
(SELECT s.stockpile_name FROM stockpile s WHERE stockpile_id = sc.stockpile_id) AS stockpile_name,
(SELECT v.vendor_name FROM vendor v WHERE v.vendor_id = c.`vendor_id`) AS vendor_name,
c.po_no, c.contract_no,c.`price_converted` AS pks_price, sc.`quantity` AS qty_order,
(SELECT IFNULL(SUM(t.send_weight),0) FROM `transaction` t WHERE t.stockpile_contract_id = sc.stockpile_contract_id) AS qty_received,
(SELECT  GROUP_CONCAT(DISTINCT f.freight_supplier) FROM freight f 
LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id
LEFT JOIN TRANSACTION t ON t.`freight_cost_id` = fc.`freight_cost_id`
WHERE t.`stockpile_contract_id` = sc.stockpile_contract_id) AS freight_supplier,
(SELECT IFNULL(AVG(t.freight_price),0) FROM TRANSACTION t WHERE t.`stockpile_contract_id` = sc.stockpile_contract_id) AS freight_price,
(SELECT GROUP_CONCAT(DISTINCT l.labor_name) FROM labor l
LEFT JOIN TRANSACTION t ON t.`labor_id` = l.labor_id
WHERE t.`stockpile_contract_id` = sc.stockpile_contract_id) AS labor,
(SELECT IFNULL(SUM(t.unloading_price)/SUM(t.send_weight),0) FROM `transaction` t WHERE t.stockpile_contract_id = sc.stockpile_contract_id) AS price_unloading,
(SELECT  GROUP_CONCAT(DISTINCT vh.vendor_handling_name) FROM vendor_handling vh 
LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id
LEFT JOIN TRANSACTION t ON t.`handling_cost_id` = vhc.`handling_cost_id`
WHERE t.`stockpile_contract_id` = sc.stockpile_contract_id) AS vendor_handling,
(SELECT IFNULL(AVG(t.handling_price),0) FROM TRANSACTION t WHERE t.`stockpile_contract_id` = sc.stockpile_contract_id) AS handling_price,
(SELECT GROUP_CONCAT(gv.general_vendor_name) FROM general_vendor gv WHERE gv.general_vendor_id = id.general_vendor_id AND a.account_no IN (520900,521000)) AS vendor_fee,
(IFNULL((SELECT SUM(id.amount_converted) FROM invoice_detail id WHERE id.poId = sc.contract_id AND a.account_no IN (520900,521000)) / sc.`quantity`,0)) AS fee_price,
(SELECT t.transaction_date FROM `transaction` t WHERE t.stockpile_contract_id = sc.stockpile_contract_id AND t.notim_status = 0 AND t.slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_received,
(SELECT t.transaction_date FROM `transaction` t WHERE t.stockpile_contract_id = sc.stockpile_contract_id AND t.notim_status = 0 AND t.slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_received 
FROM stockpile_contract sc
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN invoice_detail id ON id.`poId` = sc.`contract_id`
LEFT JOIN invoice i on i.invoice_id = id.invoice_id
LEFT JOIN account a ON a.`account_id` = id.`account_id`
WHERE 1=1 AND i.invoice_status = 0 {$whereProperty}
ORDER BY  c.`po_no` ASC, sc.stockpile_contract_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Purchasing Summary " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "S";

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
if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Name = {$vendorName}");
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SUMMARY PURCHASING LIST");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Vendor PKS");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Contract No.");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "PKS Price");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Qty Order");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty Received");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "First Received");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Last Received");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Vendor Freight");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Freight Price");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Vendor Unloading");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Unloading Price");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Vendor Handling");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Handling Price");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Vendor Fee");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Fee Price");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Total");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$no = 1;
while($row = $result->fetch_object()) {
   			
			$total = $row->pks_price + $row->freight_price + $row->price_unloading + $row->handling_price + $row->fee_price;
			
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->pks_price);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->qty_order);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->qty_received);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->first_received);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->last_received);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->freight_supplier);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->freight_price);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->labor);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->price_unloading);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->vendor_handling);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->handling_price);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->vendor_fee);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->fee_price);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $total);
   
    $no++;
}
$bodyRowEnd = $rowActive;

//        
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("R" . ($headerRow + 1) . ":S{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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