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
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFF00')
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )

);

$styleArray4 = array(
    'font' => array(
        'bold' => true
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
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
    'font' => array(
        'bold' => true
    ),
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
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
    )
);

$styleArray9 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
    )
);

$styleArray10 = array(
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
$styleArray11 = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'ffb3ba')
    )
);

$styleArray12 = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FF0000')
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);

$styleArray13 = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'BAE1FF')
    )
);




$shipmentId = $myDatabase->real_escape_string($_POST['shipmentId']);
$whereProperty = '';
$whereProperty2 = '';
$currentDate = '';
$date = '';
$shipmentCode1 = '';
$motherVessel = '';
$pebDate = '';

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql2 = "SELECT sh.shipment_code as shipmentCode, sal.mother_vessel as motherVessel,  DATE_FORMAT(ap.peb_date, '%d/%m/%Y') AS pebDate FROM shipment sh 
            LEFT JOIN accrue_prediction ap ON ap.shipment_id = sh.shipment_id
            LEFT JOIN sales sal on sal.sales_id = ap.sales_id
            WHERE sh.shipment_id = {$shipmentId}";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
$row2 = $result2->fetch_object();
$shipmentCode1 = $row2->shipmentCode;
$motherVessel = $row2->motherVessel;
$pebDate = $row2->pebDate;

$sql = "CALL sp_jobCosting({$shipmentId})";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);





// echo "1 ".$shipmentCode1;


$fileName = "Job Costing Data "  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "AA";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1; //row pertama -> selanjutnya akan increment ke bawah->
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));
    
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Shipment Code = '{$shipmentCode1}'");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Mother Vessel = '{$motherVessel}'");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PEB Date = '{$pebDate}'");
    $rowActive++;

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "JOB COSTING DATA");

//LOGBOOK
$rowActive++;
$rowMerge = $rowActive+1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Currency");

$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Account Name");


$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Type Charge");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Jenis Qty");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Min Charge");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Max Charge");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Biaya");

$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Vendor");

$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Price / MT");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);

$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:Q{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Prediksi");
$objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:Q{$rowActive}")->applyFromArray($styleArray11);

$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:AA{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Aktual");
$objPHPExcel->getActiveSheet()->getStyle("R{$rowActive}:AA{$rowActive}")->applyFromArray($styleArray13);

$rowActive++;

// PREDIKSI
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Price/MT");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Kurs");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "In Rupiah");
$objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:Q{$rowActive}")->applyFromArray($styleArray11);


// AKTUAL
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "No Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Tanggal Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Price/MT");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Kurs");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "In Rupiah");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Tanggal Payment");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "No Payment");
$objPHPExcel->getActiveSheet()->getStyle("R{$rowActive}:AA{$rowActive}")->applyFromArray($styleArray13);




//$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);

$jumlalhKolom = 0;
$jumlahKolom = $result->num_rows; 
if($jumlahKolom == 0){
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:M{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Data Kosong");
    $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray12);
}

$no = 1;
while($row = $result->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->currency_code, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->accountName, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->maxminC, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->priceType, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->qtyType, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->min_charge);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->max_charge); 
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->biaya, PHPExcel_Cell_DataType::TYPE_STRING);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->general_vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->priceMT); 

    // PREDIKSI
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->qty);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->priceMT);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->total_amount); 
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->exchange_rate); 
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->in_rupiah);  
    $objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:Q{$rowActive}")->applyFromArray($styleArray11);
   
    // AKTUAL
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("R{$rowActive}", $row->invNo, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->dateInv);     
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->qtyInv); 
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->priceInv);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->amountInv);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->exchange_rate); 
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->inRP_Inv); 

    $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $row->methodText);
    $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row->paymentDate); 
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("AA{$rowActive}", $row->payment_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getStyle("R{$rowActive}:AA{$rowActive}")->applyFromArray($styleArray13);

    $no++;
}

$rowActive++;
$bodyRowEnd = $rowActive;
for ($temp = ord("B"); $temp <= ord("AA"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
    $objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 

    $objPHPExcel->getActiveSheet()->getStyle("S" . ($headerRow + 1) . ":S{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("Z" . ($headerRow + 1) . ":Z{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();