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

//$whereProperty1 = '';
$whereProperty2 = '';
$labors = $_POST['labors'];
$status = $myDatabase->real_escape_string($_POST['status']);
//$stockpileIds = $_POST['stockpileIds'];
//$stockpileName = 'All ';
//$freightName = 'All ';
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
//$freightSupplier = $myDatabase->real_escape_string($_POST['freightSupplier']);

// <editor-fold defaultstate="collapsed" desc="Query">

//if ($stockpileIds != '') {
    
    //$whereProperty1 .= " AND fc.stockpile_id IN ({$stockpileIds}) ";
//}

if ($labors != '') {
	
	$whereProperty2 .= " AND a.labor_id IN ({$labors}) ";
}
if($status != ''){
  $whereProperty2 .= " AND a.active = {$status} ";
}



//</editor-fold>

$fileName = "DataVendorOB " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "M";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Data Vendor OB");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Labor Name");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "NPWP");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "NPWP Name");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Bank Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Branch");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Beneficiary");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Vehicle");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Price");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$sql1 = "SELECT stockpile_id FROM stockpile";
$result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
if($result1->num_rows > 0) {
while($row1 = $result1->fetch_object()) {

$stockpileId = $row1->stockpile_id;	
	

$sql = "SELECT * FROM 
(SELECT DISTINCT(v.vehicle_id) AS vehicle_id, v.vehicle_name, 
(SELECT labor_name FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS labor,
(SELECT stockpile_name FROM `stockpile` WHERE stockpile_id = (SELECT stockpile_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1) AS stockpile,
(SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                     AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) AS unloading_cost_id,
(SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1) AS labor_id,
(SELECT active FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS active,
(SELECT labor_address FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS labor_address,
(SELECT npwp FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS npwp,
(SELECT npwp_name FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS npwp_name,
(SELECT bank_name FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS bank_name,
(SELECT account_no FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS account_no,
(SELECT branch FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS branch,
(SELECT beneficiary FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS beneficiary,
(SELECT tax_name FROM tax WHERE tax_id = (SELECT pph_tax_id FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1))) AS pph,
(SELECT tax_name FROM tax WHERE tax_id = (SELECT ppn_tax_id FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1))) AS ppn,
(SELECT FORMAT(price_converted,2) FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                     AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) AS price_converted				
            FROM vehicle v
            LEFT JOIN unloading_cost uc
                ON uc.vehicle_id = v.vehicle_id
            WHERE 1=1 
		 AND uc.stockpile_id = {$stockpileId}
            ) a WHERE a. price_converted > 0
            {$whereProperty2}
            GROUP BY a.unloading_cost_id 
			ORDER BY a.labor ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
while($row = $result->fetch_object()) {
	
	$labor = $row->labor;
	$labor_address = $row->labor_address;
	$npwp = $row->npwp;
	$npwp_name = $row->npwp_name;
	$bank_name = $row->bank_name;
	$branch = $row->branch;
	$account_no = $row->account_no;
	$beneficiary = $row->beneficiary;
	$pph = $row->pph;
	$ppn = $row->ppn;
	$vehicle_name = $row->vehicle_name;
	$price_converted = $row->price_converted;
	$stockpile = $row->stockpile;
	
	
	
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $stockpile);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $labor, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $labor_address, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $npwp, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $npwp_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $bank_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $branch, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $account_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $beneficiary, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $pph, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $ppn, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $vehicle_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $price_converted);
	
	

   // $no++;
}
	}
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
           //$objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//            
//
//            // Set border for table
			  $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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