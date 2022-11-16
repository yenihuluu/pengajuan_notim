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
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
//$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
//$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$vendorId = $myDatabase->real_escape_string($_POST['vendorId']);
$poNos = $_POST['poNos'];
//$stockpileName = 'All ';
//$periodFull = '';
$vendorName = '';
$lastPoNo = '';

// <editor-fold defaultstate="collapsed" desc="Parameter">
if($poNos != '') {
     $whereProperty .= " AND con.po_no IN ({$poNos})";
	
	 
    
}
/*if($stockpileId != '') {
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}*/

if($vendorId != '') {
    $whereProperty .= " AND con.vendor_id IN ('{$vendorId}') ";
    
    $sql = "SELECT * FROM vendor WHERE vendor_id IN ('{$vendorId}')";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";
}

/*if($periodFrom != '' && $periodTo != '') {
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
}*/


// </editor-fold>

$fileName = "PO Curah Detail Summary " . $vendorName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "X";

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT con.po_no, REPLACE(con.contract_no,'-1','') AS contract_no, con.`entry_date` AS contract_date, v.vendor_name, v.`vendor_address`, s.`stockpile_name`, con.price_converted ,con.`quantity`,
(SELECT SUM(quantity) FROM TRANSACTION WHERE stockpile_contract_id = sc.stockpile_contract_id) AS qty_received, uc.`user_name` AS user_contract, 
con.`entry_date2` AS input_contract, upu.`user_name` AS user_purchasing, pu.`entry_date` AS input_purchasing, SUM(t.`quantity`) AS qty_payment,
MIN(t.`transaction_date`) AS first_date, MIN(t.`slip_no`) AS first_slip,MAX(t.`transaction_date`) AS last_date, MAX(t.`slip_no`) AS last_slip,   
p.payment_no, DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date, p.`amount_journal`, p.`entry_date` AS payment_input, up.`user_name` AS user_payment
FROM stockpile_contract sc
LEFT JOIN stockpile s
    ON s.stockpile_id = sc.stockpile_id
LEFT JOIN contract con
    ON con.contract_id = sc.contract_id
LEFT JOIN vendor v
    ON v.vendor_id = con.vendor_id
LEFT JOIN `transaction` t
    ON t.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN payment p
   ON p.payment_id = t.`payment_id`
LEFT JOIN `user` up 
   ON up.`user_id` = p.`entry_by`
LEFT JOIN `user` uc 
   ON uc.`user_id` = con.`entry_by`
LEFT JOIN po_pks po
   ON po.`contract_no` = REPLACE(con.contract_no,'-1','')
LEFT JOIN purchasing pu 
   ON pu.`purchasing_id` = po.`purchasing_id`
LEFT JOIN `user` upu
   ON upu.`user_id` = pu.`entry_by`
WHERE 1=1
AND con.contract_status <> 2
AND con.contract_type = 'C'
{$whereProperty}
GROUP BY sc.`stockpile_contract_id`,t.payment_id
ORDER BY sc.stockpile_contract_id ASC, t.transaction_id ASC";
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

/*if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}*/

if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Name = {$vendorName}");
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PO CURAH DETAIL LIST");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;

$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PO No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Contract Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Vendor Address");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Price /Kg");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty Received");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "User Contract");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Contract Input");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "User Purchasing");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Purchasing Input");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "First Date");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "First Slip No");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Last Date");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Last Slip No");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Payment No");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Qty Payment");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Amount Payment");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Payment Input");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "User Payment");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
//$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

//$rowActive = $rowActive;
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
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
		$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "");
    } else {
        $sqlCount = "SELECT COUNT(*) AS total_row
        FROM stockpile_contract sc
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v
            ON v.vendor_id = con.vendor_id
        LEFT JOIN `transaction` t
            ON t.stockpile_contract_id = sc.stockpile_contract_id
        LEFT JOIN payment p
           ON p.payment_id = t.`payment_id`
        LEFT JOIN `user` up 
           ON up.`user_id` = p.`entry_by`
        LEFT JOIN `user` uc 
           ON uc.`user_id` = con.`entry_by`
        LEFT JOIN po_pks po
           ON po.`contract_no` = REPLACE(con.contract_no,'-1','')
        LEFT JOIN purchasing pu 
           ON pu.`purchasing_id` = po.`purchasing_id`
        LEFT JOIN `user` upu
           ON upu.`user_id` = pu.`entry_by`
        WHERE 1=1
        AND con.contract_status <> 2
        AND con.contract_type = 'C'
        AND con.po_no = '{$row->po_no}'
        GROUP BY sc.`stockpile_contract_id`,t.payment_id
        ORDER BY sc.stockpile_contract_id ASC, t.transaction_id ASC";
        $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
        $rowCount = $resultCount->fetch_object();
        $totalRow = $rowCount->total_row;
        $counter = 1;

        /*$poNo = $row->po_no;
        $vendorName = $row->vendor_name;
        $contractNo = $row->contract_no;
        $unitPrice = $row->price_converted;
        $quantityOrder = $row->quantity;
        $amountOrder = $row->amount_order;
        $totalQuantityReceived = 0;
        $totalAmountReceived = 0;*/
		

        $no++;
        //$balanceQuantity = $row->quantity;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
        $objPHPExcel->getActiveSheet()->getCell("B{$rowActive}")->setValueExplicit($row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->contract_date);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vendor_address);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->stockpile_name);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->price_converted);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->qty_received);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->user_contract);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->input_contract);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->user_purchasing);
		$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->input_purchasing);
    }
    
   /* $balanceQuantity = $balanceQuantity - $row->quantity_received;
	$balanceQuantityTotal = $balanceQuantity * $row->price_converted;
                
    $totalQuantityReceived = $totalQuantityReceived + $row->quantity_received;
    $totalAmountReceived = $totalAmountReceived + $row->amount_received;*/
    
	
    //$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->first_date);
    $objPHPExcel->getActiveSheet()->getCell("P{$rowActive}")->setValueExplicit($row->first_slip, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->last_date);
    $objPHPExcel->getActiveSheet()->getCell("R{$rowActive}")->setValueExplicit($row->last_slip, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("S{$rowActive}")->setValueExplicit($row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->payment_date);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->qty_payment);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->amount_journal);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->payment_input);
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->user_payment);
    
    
   $lastPoNo = $row->po_no;
                
    
   /* if($counter == $totalRow) {
		
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
    }*/
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
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 $objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
     $objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
     $objPHPExcel->getActiveSheet()->getStyle("Q" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
     $objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":T{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
     $objPHPExcel->getActiveSheet()->getStyle("W" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("U" . ($headerRow + 1) . ":V{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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