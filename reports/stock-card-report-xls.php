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
$shipmentId = $myDatabase->real_escape_string($_POST['shipmentId']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$stockpileName = 'All ';
$shipmentNo = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Parameter">

if($stockpileId != '') {
   $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_code = '{$stockpileId}'";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}
if($shipmentId != '') {
  $whereProperty .= " AND sl.sales_id = {$shipmentId} ";
    
    $sql = "SELECT sh.* FROM shipment sh LEFT JOIN sales sl ON sl.sales_id = sh.sales_id WHERE sl.sales_id = {$shipmentId}";
    $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowShipment = $resultShipment->fetch_object();
    $shipmentNo = $rowShipment->shipment_no . " ";
}

if($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}


// </editor-fold>

$fileName = "Stock Card Report " . $stockpileName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Z";

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT DISTINCT t.*,
            DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
            s.stockpile_name, con.po_no, con.contract_no, vh.vehicle_name, con.contract_id,
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,v1.npwp,
            v1.vendor_name, v3.vendor_name AS supplier, f.freight_supplier,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.quantity END AS quantity2,
            ship.shipment_no AS group_shipment_code, 
            DATE_FORMAT(d.delivery_date, '%d %b %Y') AS group_delivery_date, 
            d.quantity AS group_quantity,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS payment_no,
            CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS payment_date,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.original_amount_converted FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS amount_paid,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.tax_invoice FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS tax_invoice,
			CASE WHEN t.stockpile_contract_id IS NOT NULL THEN (SELECT p.invoice_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id  WHERE sc.contract_id = con.contract_id ORDER BY payment_no DESC LIMIT 1)
            ELSE '' END AS tax_date,
			CASE WHEN t.fc_payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = t.fc_payment_id)
            ELSE '' END AS fc_payment_no,
            CASE WHEN t.fc_payment_id IS NOT NULL THEN (SELECT payment_date FROM payment WHERE payment_id = t.fc_payment_id)
            ELSE '' END AS fc_payment_date
        FROM TRANSACTION t
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN delivery d
            ON d.transaction_id = t.transaction_id
        LEFT JOIN shipment ship
            ON ship.shipment_id = d.shipment_id
		LEFT JOIN sales sl 
			ON ship.sales_id = sl.sales_id
        WHERE 1=1  
        AND t.company_id = {$_SESSION['companyId']}
        AND t.transaction_type = 1 {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// </editor-fold>

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
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}

if ($shipmentNo != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Shipment Code = {$shipmentNo}");
}
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "STOCK CARD");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Loading Date");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Slip No");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Purchase Type");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Supplier Code");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "PKS Source");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Tax ID");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Tax Invoice No");
$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Tax Invoice Date");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:N{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Product (PKS)");

$objPHPExcel->getActiveSheet()->mergeCells("O{$rowActive}:O{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Freight Cost");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:P{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Unloading Cost");
$objPHPExcel->getActiveSheet()->mergeCells("Q{$rowActive}:Q{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Total");

$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:T{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Shipment");

$objPHPExcel->getActiveSheet()->mergeCells("U{$rowActive}:U{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Qty Ending Balance");

$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:V{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Payment No");

$objPHPExcel->getActiveSheet()->mergeCells("W{$rowActive}:W{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Payment Date");

$objPHPExcel->getActiveSheet()->mergeCells("X{$rowActive}:X{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Amount Paid");
$objPHPExcel->getActiveSheet()->mergeCells("Y{$rowActive}:Y{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "FC Payment No");
$objPHPExcel->getActiveSheet()->mergeCells("Z{$rowActive}:Z{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "FC Payment Date");

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Inventory (kg)");
//$objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Berat Susut (kg)");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Price / kg");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "Amount (IDR)");

$objPHPExcel->getActiveSheet()->setCellValue("R{$rowMerge}", "CODE");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "DATE");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowMerge}", "QTY (kg)");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

while($row = $result->fetch_object()) {
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->unloading_date2);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->loading_date2);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->contract_type2);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->freight_code);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->npwp);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->tax_invoice);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->tax_date);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->unit_price);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->quantity * $row->unit_price);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->freight_quantity * $row->freight_price);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->unloading_price);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", ($row->quantity * $row->unit_price) + ($row->freight_quantity * $row->freight_price) + $row->unloading_price);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("R{$rowActive}", $row->group_shipment_code, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->group_delivery_date);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->group_quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->quantity - $row->group_quantity);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("V{$rowActive}", $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->amount_paid);
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $row->fc_payment_no);
	$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row->fc_payment_date);
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

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("S" . ($headerRow + 1) . ":S{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("W" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("Z" . ($headerRow + 1) . ":Z{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":U{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("X" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
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