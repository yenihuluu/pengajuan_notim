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
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$journalType = $myDatabase->real_escape_string($_POST['journalType']);
$purchaseType = $myDatabase->real_escape_string($_POST['purchaseType']);
$dateField = '';
$stockpileName = 'All ';
$periodFull = '';
$journalName = '';
$purchaseName = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if($stockpileId != '') {
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}

if($purchaseType != '') {
    $whereProperty .= " AND con.contract_type = '{$purchaseType}' ";
    
    if($purchaseType == 'P') {
        $purchaseName = 'PKS';
    } else {
        $purchaseName = 'Curah';
    }
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

if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
    $dateField = 't.transaction_date';
    
    if($journalType == 'PURCHASE') {
        $journalName = 'Journal for PKS Purchase ';
        $lastColumn = "N";
    } elseif($journalType == 'SHRINK') {
        $journalName = 'Journal for PKS Susut ';
        $lastColumn = "N";
    }
} elseif($journalType == 'FREIGHT' || $journalType == 'UNLOADING') {
    $dateField = 't.unloading_date';
    
    if($journalType == 'FREIGHT') {
        $journalName = 'Journal for Freight Cost ';
        $lastColumn = "O";
    } elseif($journalType == 'UNLOADING') {
        $journalName = 'Journal for Unloading Cost ';
        $lastColumn = "N";
    }
}

$sql = "SELECT s.stockpile_name, DATE_FORMAT({$dateField}, '%d %b %Y') AS transaction_date2, l.labor_name,
                t.slip_no, CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
                con.po_no, CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code, f.freight_supplier,
                v1.vendor_name, t.send_weight, t.netto_weight, t.quantity, t.shrink, t.unit_price, t.freight_price, t.unloading_price, vh.vehicle_name
            FROM transaction t
            LEFT JOIN stockpile_contract sc
                ON sc.stockpile_contract_id = t.stockpile_contract_id
            LEFT JOIN stockpile s
                ON s.stockpile_id = sc.stockpile_id
            LEFT JOIN contract con
                ON con.contract_id = sc.contract_id
            LEFT JOIN vendor v1
                ON v1.vendor_id = con.vendor_id
            LEFT JOIN freight_cost fc
                ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            LEFT JOIN vendor v2
                ON v2.vendor_id = fc.vendor_id
            LEFT JOIN unloading_cost uc
                ON uc.unloading_cost_id = t.unloading_cost_id
            LEFT JOIN vehicle vh
                ON vh.vehicle_id = uc.vehicle_id
			LEFT JOIN labor l
				ON t.labor_id = l.labor_id
            WHERE 1=1
            AND t.company_id = {$_SESSION['companyId']}
            AND t.transaction_type = 1 {$whereProperty} 
			ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// </editor-fold>

$fileName = "Purchase Report " . $journalName . $stockpileName . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;

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

if ($purchaseName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Purchase Name = {$purchaseName}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $journalName);

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Area");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Slip No.");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Purchase Type");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "PO No.");
if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
    $objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "LABOR");
    $objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "SUPPLIER FREIGHT");
    $objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "PKS SOURCE");
    $objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Product (PKS)");
    
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowMerge}", "Berat Kirim (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "Berat Netto (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "Inventory (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Berat Susut (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Price /kg");
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "Amount");
} elseif($journalType == 'FREIGHT') { 
    $objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Jenis Kendaraan");
    $objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "LABOR");
    $objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "SUPPLIER NAME");
    $objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PKS SOURCE");
    $objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "FREIGHT COST");
    
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "Berat Kirim (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "Berat Netto (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Inventory (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Berat Susut (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "FREIGHT COST /KG");
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowMerge}", "Total Freight Cost");
} elseif($journalType == 'UNLOADING') {
    $objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Jenis Kendaraan");
    $objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "LABOR");
    $objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "SUPPLIER NAME");
    $objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PKS SOURCE");
    $objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "UNLOADING COST");
    
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "Berat Kirim (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "Berat Netto (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Inventory (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowMerge}", "Berat Susut (kg)");
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowMerge}", "Total Unloading Cost");
}

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

while($row = $result->fetch_object()) {
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date2);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->contract_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->po_no);
    if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->labor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->freight_supplier);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->send_weight);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->netto_weight);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->shrink);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->unit_price);
        if($journalType == 'PURCHASE') {
            $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->quantity * $row->unit_price);
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->shrink * $row->unit_price);
        }
    } elseif($journalType == 'FREIGHT') {
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vehicle_name);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->labor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->freight_supplier);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->send_weight);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->netto_weight);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->shrink);
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->freight_price);
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->freight_quantity * $row->freight_price);
    } elseif($journalType == 'UNLOADING') {
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vehicle_name);
		$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->vendor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->labor_name);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->freight_supplier);
       
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->send_weight);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->netto_weight);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->quantity);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->shrink);
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->unloading_price);
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
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
    $objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
} elseif($journalType == 'FREIGHT') {
    $objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
} elseif($journalType == 'UNLOADING') {
    $objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

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