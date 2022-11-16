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
$whereProperty = '';
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$vendorId = $myDatabase->real_escape_string($_POST['vendorId']);
$poNos = $_POST['poNos'];
$stockpileName = 'All ';
$periodFull = '';
$vendorName = '';
$lastPoNo = '';

// <editor-fold defaultstate="collapsed" desc="Parameter">
if($poNos != '') {
     $whereProperty .= " AND con.po_no IN ({$poNos})";
	
	 
    
}
if($stockpileId != '') {
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}

if($vendorId != '') {
    $whereProperty1 .= " AND con.vendor_id = {$vendorId} ";
    
    $sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";
}

if($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$periodTo}','%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$periodTo}','%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}


// </editor-fold>

$fileName = "PO Detail Summary " . $stockpileName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Q";

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT con.po_no, p.payment_no, DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date, con.contract_no, v.vendor_name, con.price_converted, con.quantity, 
            con.quantity * con.price_converted AS amount_order,
            t.slip_no, DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2, 
            t.send_weight AS quantity_received, t.send_weight * con.price_converted AS amount_received,
			(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = con.contract_id {$whereProperty2}) AS adjustment
        FROM stockpile_contract sc
        INNER JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        INNER JOIN contract con
            ON con.contract_id = sc.contract_id
        INNER JOIN vendor v
            ON v.vendor_id = con.vendor_id
        INNER JOIN `transaction` t
            ON t.stockpile_contract_id = sc.stockpile_contract_id
        LEFT JOIN payment p
           ON p.stockpile_contract_id = sc.stockpile_contract_id
        WHERE 1=1
		AND con.contract_status <> 2
        AND con.company_id = {$_SESSION['companyId']}
        AND t.company_id = {$_SESSION['companyId']}
        {$whereProperty} {$whereProperty1}
		GROUP BY t.slip_no
        ORDER BY sc.stockpile_contract_id ASC, t.slip_no ASC";
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

if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Name = {$vendorName}");
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PO DETAIL LIST");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No PO");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "PAYMENT VOUCHER");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "PAYMENT DATE");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "VENDOR");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "No. Kontrak");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Price / Kg");

$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:J{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "ORDER");

$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:N{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "RECEIVED");

$objPHPExcel->getActiveSheet()->mergeCells("O{$rowActive}:O{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Balance Qty Order");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:P{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Balance Amount Order");
$objPHPExcel->getActiveSheet()->mergeCells("Q{$rowActive}:Q{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "STATUS");

$objPHPExcel->getActiveSheet()->setCellValue("I{$rowMerge}", "Quantity Order");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "Amount Order");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "SLIP NO");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "TRANSACTION DATE");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "RECEIVED QTY");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "RECEIVED AMOUNT");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
     if($result->num_rows > 0) {
            $no = 0;

while($row = $result->fetch_object()) {
    $rowActive++;
    
    if($row->po_no == $lastPoNo) {
        $counter++;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
    } else {
        $sqlCount = "SELECT count(1) AS total_row
                                FROM stockpile_contract sc
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = sc.stockpile_id
                                INNER JOIN contract con
                                    ON con.contract_id = sc.contract_id
                                INNER JOIN vendor v
                                    ON v.vendor_id = con.vendor_id
                                INNER JOIN `transaction` t
                                    ON t.stockpile_contract_id = sc.stockpile_contract_id
                                
                                WHERE 1=1 AND con.po_no = '{$row->po_no}'
								AND con.contract_status <> 2
                                AND con.company_id = {$_SESSION['companyId']}
                                AND t.company_id = {$_SESSION['companyId']}
								
                                ORDER BY sc.stockpile_contract_id, t.slip_no";
        $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
        $rowCount = $resultCount->fetch_object();
        $totalRow = $rowCount->total_row;
        $counter = 1;

        $poNo = $row->po_no;
        $vendorName = $row->vendor_name;
        $contractNo = $row->contract_no;
        $unitPrice = $row->price_converted;
        $quantityOrder = $row->quantity;
        $amountOrder = $row->amount_order;
        $totalQuantityReceived = 0;
        $totalAmountReceived = 0;
		$amountAdjustment = 0;
		$totalBalance  = 0;
		

        $no++;
        $balanceQuantity = $row->quantity;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->po_no);
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->payment_no);
		$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->payment_date);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->contract_no);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->price_converted);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->amount_order);
    }
    
    $balanceQuantity = $balanceQuantity - $row->quantity_received;
	$balanceQuantityTotal = $balanceQuantity * $row->price_converted;
                
    $totalQuantityReceived = $totalQuantityReceived + $row->quantity_received;
    $totalAmountReceived = $totalAmountReceived + $row->amount_received;
    
	$objPHPExcel->getActiveSheet()->getCell("K{$rowActive}")->setValueExplicit($row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->unloading_date2);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->quantity_received);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->amount_received);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $balanceQuantity);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $balanceQuantityTotal);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
    
    
   $lastPoNo = $row->po_no;
                
    
    if($counter == $totalRow) {
		
		if($row->adjustment != 0 && $row->adjustment != ''){
						$amountAdjustment = $row->adjustment * $row->price_converted;
						$amountBalance = $balanceQuantity * $row->price_converted;
						$totalBalance = $amountBalance - $amountAdjustment;
						
		$rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "ADJUSTMENT");
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->adjustment);
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $amountAdjustment);
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $balanceQuantity - $row->adjustment);
        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalBalance);
        if($balanceQuantity > 0) {
            $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
        }
		}	
		$rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "BALANCE");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $poNo);
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $vendorName);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $contractNo);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $unitPrice);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $quantityOrder);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $amountOrder);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $totalQuantityReceived + $row->adjustment);
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $totalAmountReceived + $amountAdjustment);
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $balanceQuantity - $row->adjustment);
        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalBalance);
        if($balanceQuantity - $row->adjustment > 0) {
            $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "ON PROGRESS");
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "CLOSED");
        }
    }
}
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
    $objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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