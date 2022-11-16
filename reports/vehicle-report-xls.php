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
$styleArray9 = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => 'FF0000')
    )
);
// </editor-fold>

$whereProperty = '';
$stockpileIds = $_POST['stockpileIds'];
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$periodFull = $periodFrom . " - " . $periodTo . " ";
if($stockpileIds != '') {
        $whereProperty .= " AND tt.stockpile_id IN ({$stockpileIds}) ";

    /*$sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";*/
}

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT t.*, tt.transaction_in, tt.transaction_out, tt.slip, v.vendor_name, t.slip_no, s.stockpile_name, vh.vehicle_name, c.contract_no
FROM transaction_timbangan tt
LEFT JOIN `transaction` t ON tt.transaction_id = t.t_timbangan
LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON tt.vendor_id = v.vendor_id
LEFT JOIN stockpile s on tt.stockpile_id = s.stockpile_id
LEFT JOIN unloading_cost uc on tt.unloading_cost_id = uc.unloading_cost_id
LEFT JOIN vehicle vh on uc.vehicle_id = vh.vehicle_id
WHERE tt.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
AND tt.netto_weight >= '300' AND tt.tarra_weight != '0' AND tt.send_weight != '0' and t.slip_retur is null AND t.notim_status = 0 {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($stockpileIds !== '') {
    // $stockpileId = $_POST['stockpileId'];
    $stockpile_name = array();
    $stockpile_code = array();
    $stockpileNames = '';
    $stockpileCodes = '';
    $sql1 = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
    if ($result1 !== false && $result1->num_rows > 0) {
        while ($rowData = mysqli_fetch_array($result1)) {
            $stockpile_name[] = $rowData['stockpile_name'];
            $stockpile_code[] = $rowData['stockpile_code'];

            /*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                                if($stockpile_names == '') {
                                    $stockpile_names .= "'". $stockpile_name[$i] ."'";
                                } else {
                                    $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                                }
                            }*/

            $stockpileNames = "'" . implode("','", $stockpile_name) . "'";
            $stockpileCodes = "'" . implode("','", $stockpile_code) . "'";

        }
    }
}
//</editor-fold>

$fileName = "Vehicle Report" . " " . $stockpileCodes . " " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "T";

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

if ($stockpileNames != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileNames}");
}

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "VEHICLE REPORT");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No. Slip");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Transaction IN");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Transaction OUT");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Loading Date");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "No Vehicle");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Vehicle");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Driver");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Bruto Weight");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Tarra Weight");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Netto Weight");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Handling Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Shrink");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Slip");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Contract No");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$no = 1;
while ($row = $result->fetch_object()) {

    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->transaction_date);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->transaction_in);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->transaction_out);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->loading_date);
    $objPHPExcel->getActiveSheet()->getCell("H{$rowActive}")->setValueExplicit($row->vehicle_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->vehicle_name);
    $objPHPExcel->getActiveSheet()->getCell("J{$rowActive}")->setValueExplicit($row->driver, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->send_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->bruto_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->tarra_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->netto_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->handling_quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->shrink);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->getCell("S{$rowActive}")->setValueExplicit($row->slip, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getCell("T{$rowActive}")->setValueExplicit($row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $no++;
}
$bodyRowEnd = $rowActive;

for ($temp = ord("A"); $temp <= ord("T"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY hh:mm");
    $objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY hh:mm");
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

}


// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("R" . ($headerRow + 1) . ":R{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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