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
//$whereProperty2 = '';
//$vendorIds = '';
//$stockpileId = '';
//$stockpileName = 'All ';
//$vendorName = 'All ';
$status = $myDatabase->real_escape_string($_POST['status']);
$vendorIds = $_POST['vendorIds'];

// <editor-fold defaultstate="collapsed" desc="Query">

/*if ($stockpileId != '') {
    $stockpileId = $_POST['stockpileId'];
    $sql = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty1 .= " AND fc.stockpile_id = {$stockpileId} ";
    $stockpileName = $row->stockpile_name . " ";
}

if ($freightSupplier != '') {
	$freightSupplier = $_POST['freightSupplier'];
	$sql = "SELECT freight_supplier FROM freight WHERE freight_id = {$freightSupplier}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty2 .= " AND fc.freight_id = {$freightSupplier} ";
    $freightName = $row->freight_supplier . " ";
}*/
if($vendorIds != ''){
	$whereProperty .= " AND gv.general_vendor_id IN ({$vendorIds}) ";
}

if($status != ''){
	$whereProperty .= " AND gv.active = {$status} ";
}

$sql = "SELECT gv.`general_vendor_id`, gv.`general_vendor_name`, gv.`general_vendor_address`,gv.`npwp`, gv.`npwp_name`,
gvb.`bank_name`,gvb.`branch`,gvb.`account_no`, gvb.`beneficiary`,gvb.`swift_code`, txppn.tax_name AS tax_ppn, txpph.tax_name AS tax_pph
        FROM general_vendor gv
		LEFT JOIN general_vendor_bank gvb 
			ON gvb.`general_vendor_id` = gv.`general_vendor_id`
		LEFT JOIN tax txppn
			ON txppn.tax_id = gv.ppn_tax_id
		LEFT JOIN general_vendor_pph gvpph
			ON gvpph.`general_vendor_id` = gv.`general_vendor_id`
		LEFT JOIN tax txpph
			ON txpph.tax_id = gvpph.pph_tax_id
		WHERE 1=1 {$whereProperty}
        ORDER BY gv.general_vendor_id DESC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "DataGeneralVendor " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "L";

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

if ($freightName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor OA = {$freightName}");
}*/


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Data General Vendor");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
//$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Vendor Code");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Adress");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "NPWP");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "NPWP Name");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Bank Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Branch");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Beneficiary");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Swift Code");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "PPN");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 0;
	while($row = $result->fetch_object()) {
	$rowActive++;
	
	 if($row->general_vendor_id == $general_vendor_id) {
                    $counter++;
		$sqlCount = "SELECT COUNT(1) AS total_row FROM general_vendor_pph d WHERE d.general_vendor_id = '{$row->general_vendor_id}'";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    if($rowCount->total_row > 1){
						$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
						$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
						$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
						$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
						$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
						
						$rowActive2 = ($rowActive + $totalRow) - 1;
					}else{
						$rowActive2 = $rowActive;
					}
        
       
    } else {
        $sqlCount = "SELECT COUNT(1) AS total_row FROM general_vendor_bank d WHERE d.general_vendor_id = '{$row->general_vendor_id}'";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                     
						$totalRow = $rowCount->total_row;
					
					if($totalRow > 1){
					$rowActive2 = ($rowActive + $totalRow) - 1;
					}else{
					$rowActive2 = $rowActive;	
					}
                    $counter = 1;
                    
                    
                    $no++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowActive2}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->general_vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->general_vendor_address, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowActive2}");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->npwp_name, PHPExcel_Cell_DataType::TYPE_STRING);
	}
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->bank_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->branch, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->beneficiary, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->swift_code, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->tax_pph, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $row->tax_ppn, PHPExcel_Cell_DataType::TYPE_STRING);
	
	$general_vendor_id = $row->general_vendor_id;
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
//$objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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