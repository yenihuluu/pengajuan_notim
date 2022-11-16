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


$sql = "SELECT CASE WHEN pp.vendor_id IS NOT NULL THEN ven.vendor_name
        WHEN pp.freight_id IS NOT NULL THEN fr.freight_supplier
        WHEN pp.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
        WHEN pp.labor_id IS NOT NULL THEN l.labor_name
        ELSE NULL END AS vendorName,
        CASE WHEN pp.vendor_bank_id IS NOT NULL THEN vb.bank_name 
        WHEN pp.vendor_bank_id IS NOT NULL THEN fb.bank_name 
        WHEN pp.vendor_bank_id IS NOT NULL THEN lb.bank_name 
        WHEN pp.vendor_bank_id IS NOT NULL THEN vhb.bank_name 
        ELSE NULL END AS bankName,
        pp.*
        FROM pengajuan_payment_sales_oa pp
        LEFT JOIN vendor ven ON ven.vendor_id = pp.vendor_id
        LEFT JOIN vendor_bank vb ON vb.v_bank_id = pp.vendor_bank_id
        LEFT JOIN freight_local_sales fr ON fr.freight_id = pp.freight_id
        LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id
        LEFT JOIN labor l ON l.labor_id = pp.labor_id
        LEFT JOIN freight_Local_sales_bank fb ON fb.f_bank_id = pp.vendor_bank_id
        LEFT JOIN labor_bank lb ON lb.l_bank_id = pp.vendor_bank_id
        LEFT JOIN vendor_handling_bank vhb ON vhb.vh_bank_id = pp.vendor_bank_id
        LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
        
       
        LEFT JOIN USER us ON us.user_id = pp.user 
        INNER JOIN user_stockpile ust ON ust.stockpile_id = sp.stockpile_id
        WHERE ust.user_id = {$_SESSION['userId']} and pp.status = 0 and pp.idPP IN ({$selectedCheck}) ORDER BY idPP DESC";  
//echo $sql;
 $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


$fileName = "Pengajuan Payment "  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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
        $sqlUpdate = "UPDATE `pengajuan_payment_sales_oa` 
                    SET email_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') 
                    WHERE idPP in ($row->idPP)";
       $result1 = $myDatabase->query($sqlUpdate, MYSQLI_STORE_RESULT);

       $rowActive++;
       $dpp = $row->qty * $row->price;
       $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
     //  $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->vendorName, PHPExcel_Cell_DataType::TYPE_STRING);
       $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "'".$row->rek);
       $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->remarks);
       $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vendorName);
       $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->bank); 
       $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->bankName);
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
