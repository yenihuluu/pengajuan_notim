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
$freightSuppliers = $_POST['freightSuppliers'];
$stockpileIds = $_POST['stockpileIds'];
$status = $myDatabase->real_escape_string($_POST['status']);
//$stockpileName = 'All ';
//$freightName = 'All ';
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
//$freightSupplier = $myDatabase->real_escape_string($_POST['freightSupplier']);

// <editor-fold defaultstate="collapsed" desc="Query">

if ($stockpileIds != '') {
    
    $whereProperty1 .= " AND fc.stockpile_id IN ({$stockpileIds}) ";
}

if ($freightSuppliers != '') {
	
	$whereProperty2 .= " AND fc.freight_id IN ({$freightSuppliers}) ";
}
if($status != ''){
  $whereProperty2 .= " AND f.active = {$status} ";
}

$sql = "SELECT DISTINCT(fc.`price_converted`), fc.freight_cost_id, fc.freight_id, fc.`vendor_id`, fc.`stockpile_id`, v.vendor_name, f.freight_code, f.`freight_supplier`, f.`freight_address`,
f.`npwp`,fb.`beneficiary`, fb.`bank_name`, fb.`account_no`, fb.`swift_code`, txpph.`tax_name` AS pph, txppn.`tax_name` AS ppn, s.`stockpile_name`, f.npwp_name, fb.branch
FROM freight_cost fc 
LEFT JOIN freight f ON fc.`freight_id` = f.`freight_id`
LEFT JOIN freight_bank fb ON fb.freight_id = f.freight_id
LEFT JOIN vendor v ON v.`vendor_id` = fc.`vendor_id`
LEFT JOIN tax txpph ON txpph.`tax_id` = f.`pph_tax_id`
LEFT JOIN tax txppn ON txppn.`tax_id` = f.`ppn_tax_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = fc.`stockpile_id`
WHERE 1=1 {$whereProperty1} {$whereProperty2} ORDER BY fc.freight_cost_id DESC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "DataVendorOA " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "P";

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
/*
if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}

if ($freightName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor OA = {$freightName}");
}*/


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Data Vendor OA");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Vendor PKS");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Freight Code");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Freight Name");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "NPWP");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "NPWP Name");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Bank Name");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Branch");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Beneficiary");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Swift Code");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Price");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	$rowActive++;
    
	 if($row->freight_cost_id == $freight_cost_id) {
                    $counter++;
	} else {
      $sqlCount = "SELECT COUNT(1) AS total_row FROM freight_bank d WHERE d.freight_id = '{$row->freight_id}'";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
					
					if($totalRow > 1){
					$rowActive2 = ($rowActive + $totalRow) - 1;
					}else{
					$rowActive2 = $rowActive;	
					}
                    $counter = 1;
                    //echo 'tesst';
                    //$poNo = $row->po_no;
                    //$vendorName = $row->vendor_name;
                    //$contractNo = $row->contract_no;
                    //$unitPrice = $row->price_converted;
                   // $quantityOrder = $row->quantity;
                    //$amountOrder = $row->amount_order;
                    //$totalQuantityReceived = 0;
                    //$totalAmountReceived = 0;
                    
                    $no++;
	
	$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowActive2}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowActive2}");
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->stockpile_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->freight_code, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->freight_supplier, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->freight_address, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->npwp_name, PHPExcel_Cell_DataType::TYPE_STRING);
	}
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->bank_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->branch, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $row->beneficiary, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $row->swift_code, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", $row->pph, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", $row->ppn, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->price_converted);
	

    $freight_cost_id = $row->freight_cost_id;
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
    //$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
   
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
//$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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