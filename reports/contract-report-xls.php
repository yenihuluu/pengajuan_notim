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

//$whereProperty = '';
//$sumProperty = '';
//$balanceBefore = 0;
//$boolBalanceBefore = false;
$vendorId = $myDatabase->real_escape_string($_POST['vendorId']);
//$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
//$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
//$stockpileName = 'All ';
//$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($vendorId != '') {
    $vendorId = $_POST['vendorId'];
    $sql = "SELECT vendor_code, vendor_name FROM vendor WHERE vendor_id = {$vendorId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    //$whereProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    //$sumProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    
//    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
//    $sumProperty .= " AND t.slip_no like '{$stockpileId}%' ";

//    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
//    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    $rowStockpile = $resultStockpile->fetch_object();
    $vendorName = $row->vendor_name . " ";
}
/*
if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $periodFull = "To " . $periodTo . " ";
}*/

$sql = "SELECT con.*, cur.currency_code,v.vendor_name, v.vendor_code,
            CASE when con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2, a.stockpile_name
        FROM contract con
        INNER JOIN currency cur
            ON cur.currency_id = con.currency_id
        INNER JOIN vendor v
            ON v.vendor_id = con.vendor_id
		INNER JOIN stockpile_contract c
			ON con.contract_id = c.contract_id
		INNER JOIN stockpile a
			ON a.stockpile_id = c.stockpile_id
		
        WHERE 1=1 AND 
		con.company_id = {$_SESSION['companyId']}
		AND v.vendor_id = '{$vendorId}'
        ORDER BY a.stockpile_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Data Contract " . $vendorName  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "H";

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

if ($vendorName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor = {$vendorName}");
}
/*
if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}*/


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "DATA CONTRACT");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No. PO");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Kode Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Nama Kontrak");
//$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "PPN");
//$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "PPH");
//$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "PKS Asal");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "No. Kontrak");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Price/KG");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Stockpile");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
/*if($boolBalanceBefore) {
    $sql2 = "SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2
            FROM transaction t
            LEFT JOIN stockpile_contract sc
                ON sc.stockpile_contract_id = t.stockpile_contract_id
            WHERE 1=1 {$sumProperty}";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    if($result2->num_rows > 0) {
        while($row2 = $result2->fetch_object()) {
            $balanceBefore = $balanceBefore + $row2->quantity2;
        }
     */   
       /* $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", ""); */
        //$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
        //$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
        //$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
        
//    }
//}

//$balanceQuantity = $balanceBefore;
$no = 1;
//$pph = 'NONE ';
//	$pks = 'NONE ';
while($row = $result->fetch_object()) {
    //$balanceQuantity = $balanceQuantity + $row->quantity2;
    
    $rowActive++;
            
	
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    //$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->po_no);
	$objPHPExcel->getActiveSheet()->getCell("B{$rowActive}")->setValueExplicit($row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->vendor_code);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->vendor_name);
    //$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->tax_name);
    //$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $pph);
    //$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $pks);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->contract_no);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->price_converted);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->stockpile_name);
   
    
    $no++;
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
for ($temp = ord("A"); $temp <= ord("K"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
	
}
//$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
/*if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}*/

//$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Cell_DataType::FORMAT_GENERAL);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("U" . ($headerRow + 1) . ":V{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("Y" . ($headerRow + 1) . ":AA{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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