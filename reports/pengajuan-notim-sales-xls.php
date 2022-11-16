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
        'color' => array('rgb' => 'D3D3D3')
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


// $Amount = $myDatabase->real_escape_string($_POST['Amount']); 
// $periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
// $periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
// $SupTypeId = $_POST['SupTypeId'];
// $stockpileId = $_POST['stockpileIds'];
// $stockpile_names = $_POST['stockpile_names'];
// $spName = $_POST['spName'];

// $whereProperty = '';
// $whereProperty1 = '';

// $tempSupTypeId = '';
// if($SupTypeId == 'CONTRACT'){
//     $tempSupTypeId .= "'P'";
// }else if($SupTypeId == 'CURAH'){
//     $tempSupTypeId .= "'C'";
// }else if($SupTypeId == 'ALL'){
//     $tempSupTypeId .= "'P,C'";
// }
date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$return_value = '';
$selectedCheck = '';

if (isset($_POST['checks'])) {                                            
    $checks = $_POST['checks'];
    for ($i = 0; $i < sizeof($checks); $i++) {
        if($selectedCheck == '') {
            $selectedCheck .= $checks[$i];
        } else {
            $selectedCheck .= ','. $checks[$i];
        }
    }
}


$sql = "SELECT cb.`account_no`,cu.customer_name, pp.`remarks`, cb.`bank_name`, cb.`branch`, cb.`beneficiary`,pp.`total_qty`,pp.`price`,pp.`total_dpp`,
pp.`total_ppn_amount`,pp.`total_pph_amount`, pp.`grand_total`,pp.idPP  
   FROM pengajuan_payment_sales pp
	LEFT JOIN customer cu ON cu.customer_id = pp.customer_id
	LEFT JOIN customer_bank cb ON cb.customer_id = cu.customer_id
        WHERE pp.`user` = {$_SESSION['userId']} AND pp.status = 0 AND pp.idPP IN ({$selectedCheck}) ORDER BY pp.idPP DESC";  
//echo $sql;
 $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


$fileName = "Pengajuan Payment Local Sales"  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "O";

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
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "DATA PENGAJUAN PAYMENT");


$rowActive++;
$rowMerge = $rowActive+1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "No Rek/Cek");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Keterangan");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Bank");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Cabang Bank");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Nama Akun Bank");
 $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Qty(M3)");
 $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Harga/Qty");
 $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "DPP");
 $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "PPN");
 $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Tax Remark");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
$no = 1;

if($result->num_rows > 0){
    while($row = $result->fetch_object()){
        $dpp = 0;
        $sqlUpdate = "UPDATE `pengajuan_payment_sales` 
                    SET email_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') 
                    WHERE idPP in ($row->idPP)";
       $result1 = $myDatabase->query($sqlUpdate, MYSQLI_STORE_RESULT);

       $rowActive++;
       $dpp = $row->qty * $row->price;
       $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
     //  $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->vendorName, PHPExcel_Cell_DataType::TYPE_STRING);
       $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "'".$row->account_no);
       $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->customer_name);
       $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->remarks);
       $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->bank_name); 
       $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->branch);
       $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->beneficiary);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->total_qty); //tidak tau 
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->price);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->total_dpp);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->total_ppn_amount);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->total_pph_amount);
       $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->grand_total);
       $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", '');
   
       //$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray11);
       $no++;
    }

 }


$bodyRowEnd = $rowActive;
for ($temp = ord("B"); $temp <= ord("O"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":{$lastColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();
