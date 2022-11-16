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

$prediksiIds = $_POST['prediksiIds'];
$temp = $_POST['temp'];

if($temp != ''){
    $sqlA = "SELECT prediction_code FROM accrue_prediction WHERE prediction_id IN ({$prediksiIds})";
    $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT); 
    $count = $resultA->num_rows;
    if($resultA !== false && $resultA->num_rows > 0) {
        while($rowA = $resultA->fetch_object()) {
            if($prediksiCode == '') {
                $prediksiCode .= "'".$rowA->prediction_code ."'";
            } else {
                $prediksiCode .= ','. "'". $rowA->prediction_code ."'";
            }
        }
    }
}else{
    $prediksiCode = "ALL";
}

$sql = "CALL sp_prediksi('{$prediksiIds}')";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT); 

//</editor-fold>

$fileName = "Data prediksi". str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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

// if ($vendorName != "") {
//     $rowActive++;
//     $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
//     $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor = {$vendorName}");
// }

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PREDICTION DATA");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Status.");
if($count > 1 || $prediksiCode == 'ALL'){
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Prediction code");
}
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Prediction detail code");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Cost");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "General Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Max Charge");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Min Charge");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty Type");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Qty Value");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Price type");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "UOM");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Currency");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Excange Rate");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Rupiah");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Stockpile Remarks");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);

$no = 1;
while($row = $result->fetch_object()) {    
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->journalText);
    if($count > 1 || $prediksiCode == 'ALL'){
        $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->prediction_code, PHPExcel_Cell_DataType::TYPE_STRING);
    }
    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($row->generate_code_detail, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($row->tipe_biaya, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("F{$rowActive}")->setValueExplicit($row->general_vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("G{$rowActive}")->setValueExplicit($row->accountName, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->maxused);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->minused);
    $objPHPExcel->getActiveSheet()->getCell("J{$rowActive}")->setValueExplicit($row->coaType, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->qty);
    $objPHPExcel->getActiveSheet()->getCell("L{$rowActive}")->setValueExplicit($row->priceType, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("M{$rowActive}")->setValueExplicit($row->coaType, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->curr);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->priceMT);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->total_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->exchange_rate);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->in_rupiah);
    $objPHPExcel->getActiveSheet()->getCell("S{$rowActive}")->setValueExplicit($row->spRemarks, PHPExcel_Cell_DataType::TYPE_STRING);

    $no++;
}
$bodyRowEnd = $rowActive;

for ($temp = ord("A"); $temp <= ord("S"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
	
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("O" . ($headerRow + 1) . ":R{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();