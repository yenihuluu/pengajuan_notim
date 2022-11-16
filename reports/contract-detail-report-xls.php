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
//$vendorIds = $myDatabase->real_escape_string($_POST['vendorIds']);
$vendorIds = $_POST['vendorIds'];
$stockpileIds = $_POST['stockpileIds'];
$contract_nos = $_POST['contract_nos'];
//$stockpileName = 'All ';
//$periodFull = '';
//$vendorName = '';
$lastPoNo = '';

// <editor-fold defaultstate="collapsed" desc="Parameter">
if($contract_nos != '') {
     $whereProperty .= " AND b.po_pks_id IN ({$contract_nos})";
	
}
if($vendorIds != '') {
    $whereProperty .= "AND b.vendor_id IN ({$vendorIds})";
   
}
if($stockpileIds != '') {
    $whereProperty .= "AND f.stockpile_id IN ({$stockpileIds})";
   
}



// </editor-fold>

$fileName = "Contract Detail Summary " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "L";

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT a.po_pks_id,d.`vendor_name`, e.`stockpile_name`, b.`contract_no`, b.`quantity` AS qty,  c.`po_no`, c.`entry_date` AS tgl_po, f.`quantity`, f.stockpile_id,
(SELECT SUM(send_weight) FROM `transaction` WHERE stockpile_contract_id = f.stockpile_contract_id) AS total_receive, 
(SELECT GROUP_CONCAT(payment_date) FROM payment WHERE stockpile_contract_id = f.stockpile_contract_id) AS payment_date,
(SELECT GROUP_CONCAT(payment_no) FROM payment WHERE stockpile_contract_id = f.stockpile_contract_id) AS payment_no
FROM po_contract a
LEFT JOIN po_pks b ON b.`po_pks_id` = a.`po_pks_id`
LEFT JOIN contract c ON a.`contract_id` = c.`contract_id`
LEFT JOIN vendor d ON d.`vendor_id` = b.`vendor_id`
LEFT JOIN stockpile_contract f ON f.`contract_id` = c.`contract_id`
LEFT JOIN stockpile e ON e.`stockpile_id` = f.`stockpile_id`
WHERE d.`vendor_name` IS NOT NULL {$whereProperty}
ORDER BY c.`po_no` ASC";
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
}

if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Name = {$vendorName}");
}
*/
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "CONTRACT DETAIL LIST");

$rowActive++;
$rowMerge = $rowActive;
$headerRow = $rowActive;

$objPHPExcel->getActiveSheet()->setCellValue("A{$rowMerge}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowMerge}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowMerge}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowMerge}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowMerge}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowMerge}", "PO No");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowMerge}", "PO Date");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowMerge}", "PO Qty");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowMerge}", "Total Received");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowMerge}", "Outstanding Balance");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowMerge}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowMerge}", "Payment No");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
     if($result->num_rows > 0) {
            $no = 0;

while($row = $result->fetch_object()) {
	
	$outstanding = $row->quantity - $row->total_receive;
	
    $rowActive++;
    
    if($row->po_pks_id == $lastPoNo) {
        $counter++;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
       
    } else {
        $sqlCount = "SELECT count(1) AS total_row
        FROM po_contract a
LEFT JOIN po_pks b ON b.`po_pks_id` = a.`po_pks_id`
LEFT JOIN contract c ON a.`contract_id` = c.`contract_id`
LEFT JOIN vendor d ON d.`vendor_id` = b.`vendor_id`
LEFT JOIN stockpile_contract f ON f.`contract_id` = c.`contract_id`
LEFT JOIN stockpile e ON e.`stockpile_id` = f.`stockpile_id`
WHERE d.`vendor_name` IS NOT NULL AND b.po_pks_id = {$row->po_pks_id}
ORDER BY c.`po_no` ASC";
        $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
        $rowCount = $resultCount->fetch_object();
        $totalRow = $rowCount->total_row;
        $counter = 1;

                    $vendor_name = $row->vendor_name;
                    $contract_no = $row->contract_no;
                    $qty = $row->qty;
                
		

        $no++;
       // $balanceQuantity = $row->quantity;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $vendor_name);
        $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
       // $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $contract_no);
		$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $qty);
       
    }
    
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->getCell("F{$rowActive}")->setValueExplicit($row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->tgl_po);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->total_receive);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $outstanding);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->payment_date);
    //$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->payment_no);
    $objPHPExcel->getActiveSheet()->getCell("L{$rowActive}")->setValueExplicit($row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
    
    
    
   $lastPoNo = $row->po_pks_id;
                
    
    
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
    $objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 //$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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