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

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('Y/m');
$todayDate = $date->format('d/m/Y');
$currentYear = $date->format('Y');
$periodFrom1 = '';
$periodTo1 = '';
$periodFrom = '';
$periodTo = '';
$whereProperty = '';
$selectedCheck = '';

#1
$supplierCode = '';
$stockpile = '';
$supplierName = '';
$contractType = '';
#2
$whereSupName = '';
$whereSupCode = '';
$whereType = '';
$whereStockpile = '';

// $Amount = $myDatabase->real_escape_string($_POST['Amount']); 
$periodFrom1 = $myDatabase->real_escape_string($_POST['periodFrom2']);
$periodTo1 = $myDatabase->real_escape_string($_POST['periodTo2']);

$supplierName = $myDatabase->real_escape_string($_POST['supName']);
$supplierCode =  $myDatabase->real_escape_string($_POST['supCode']);
$contractType =  $myDatabase->real_escape_string($_POST['contractType']);
$stockpile = $myDatabase->real_escape_string($_POST['stockpile']);

#1
if($supplierName != ''){
    $whereSupName = " AND  ven.vendor_name like ('%{$supplierName}%')";
}
#2
if($supplierCode != ''){
    $whereSupCode = " AND  ven.vendor_code like ('%{$supplierCode}%')";
}
#3
if($contractType != ''){
    $whereType = " AND  con.contract_type = '{$contractType}'";
}
#4
if ($stockpile != ''){
    $whereStockpile = " AND st.stockpile_name LIKE ('{$stockpile}%')";
}

if($periodFrom1 != '' && $periodTo1 != '') {
        $whereProperty = "DATE_FORMAT(tr.transaction_date, '%Y/%m/%d') BETWEEN '{$periodFrom1}' AND '{$periodTo1}'";
}else{
   $periodFrom = $currentMonthYear;
   $whereProperty = "DATE_FORMAT(tr.transaction_date, '%Y/%m') = '{$currentMonthYear}'";
//    $whereProperty = "DATE_FORMAT(tr.transaction_date, '%Y/%m') = '2020/10'";
}

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

//  $sql = "SELECT ven.vendor_name AS vendorName,
//         ven.vendor_code AS vendorCode,
//         tr.transaction_date AS tanggal,
//             lab.`labor_rules` AS labor_rules,
//             con.contract_type AS contractType,
//             st.stockpile_name AS stockpileName,
//             SUM(tr.send_weight) AS sendweight,
//             SUM(tr.shrink) AS susut,
//             SUM(tr.quantity) AS inventory,
//             SUM(tr.netto_weight) AS netto,
//             SUM(tr.send_weight * tr.`unit_price`) AS AmountSendW,
//             SUM(tr.shrink * tr.`unit_price`) AS AmountSusut,
//             SUM(tr.quantity * tr.`unit_price`) AS AmountInventory,
//             SUM(tr.freight_price * tr.freight_quantity) AS amountFreight,
//             SUM(tr.handling_price * tr.quantity) AS amountHandling,
//             CASE WHEN (lab.labor_rules = '1') THEN tr.unloading_price
//                     WHEN (lab.labor_rules = '2') THEN 
//                         CASE WHEN (tr.send_weight < tr.netto_weight)
//                             THEN (tr.send_weight * tr.unloading_price) 
//                         ELSE (tr.netto_weight * tr.unloading_price) 
//                         END
//                     WHEN (lab.labor_rules = '3') THEN (tr.netto_weight * tr.unloading_price)
//                     WHEN (lab.labor_rules = '4') THEN (tr.send_weight * tr.unloading_price)
//             ELSE NULL 
//             END AS Unloading,
//             tr.quantity AS qty
//         FROM TRANSACTION tr
//         LEFT JOIN stockpile_contract stc ON stc.stockpile_contract_id = tr.stockpile_contract_id
//         LEFT JOIN contract con ON con.contract_id = stc.contract_id
//         LEFT JOIN vendor ven ON ven.vendor_id = con.vendor_id
//         LEFT JOIN stockpile st ON st.stockpile_id = stc.stockpile_id
//         LEFT JOIN labor lab ON lab.labor_id = tr.labor_id
//         WHERE {$whereProperty} {$whereSupName} {$whereSupCode} {$whereType} {$whereStockpile}
//         GROUP BY con.contract_type, ven.vendor_name, stockpileName
//         ORDER BY tr.transaction_id DESC";

$sql = "SELECT  SUM(a.sendweight1) AS sendweight,
        SUM(a.susut1) AS susut,
        SUM(a.inventory1) AS inventory,
        SUM(a.netto1) AS netto,
        SUM(a.AmountSendW1) AS AmountSendW,
        SUM(a.AmountSusut1) AS AmountSusut,
        SUM(a.AmountInventory1) AS AmountInventory,
        SUM(a.amountFreight1) AS amountFreight,
        SUM(a.amountHandling1) AS amountHandling,
        SUM(COALESCE(a.Unloading_retur,0)) + SUM(COALESCE(a.Unloading,0)) AS newUnloading, 
        a.* FROM 
            (
            SELECT tr.transaction_id, ven.vendor_name AS vendorName,
            ven.vendor_code AS vendorCode,
            tr.transaction_date AS tanggal,
            lab.`laborRules` AS laborRules,
            con.contract_type AS contractType,
            st.stockpile_name AS stockpileName,
            (tr.send_weight) AS sendweight1,
            CASE WHEN con.contract_type = 'C' AND tr.transaction_type = 1
            THEN 0 ELSE (tr.shrink) END AS susut1,
            (tr.quantity) AS inventory1,
            (tr.netto_weight) AS netto1,
            (tr.send_weight * tr.`unit_price`) AS AmountSendW1,
            CASE WHEN con.contract_type = 'C' AND tr.transaction_type = 1
		        THEN 0 ELSE (tr.shrink * tr.`unit_price`) END AS  AmountSusut1,
            (tr.quantity * tr.`unit_price`) AS AmountInventory1,
            (tr.freight_price * tr.freight_quantity) AS amountFreight1,
            (tr.handling_price * tr.quantity) AS amountHandling1,

            CASE WHEN (lab.laborRules = '1') THEN (tr.unloading_price)
            WHEN (lab.laborRules = '2') THEN 
            CASE WHEN tr.slip_retur IS NULL  AND (tr.send_weight <= tr.netto_weight)
                THEN (tr.send_weight * tr.unloading_price) 
                            WHEN tr.slip_retur IS NULL THEN (tr.netto_weight * tr.unloading_price) 
                        ELSE 0 END
                                WHEN (lab.laborRules = '3') AND tr.slip_retur IS NULL THEN (tr.netto_weight * tr.unloading_price)
                                WHEN (lab.laborRules = '4') AND tr.slip_retur IS NULL THEN (tr.send_weight * tr.unloading_price)
                ELSE NULL END AS Unloading,
                        
                CASE  WHEN (lab.laborRules = '2') THEN 
            CASE WHEN (tr.slip_retur IS NOT NULL ) AND (tr.send_weight*-1 <= tr.netto_weight*-1)
                THEN (tr.send_weight*-1 * tr.unloading_price) 
                            WHEN (tr.slip_retur IS NOT NULL )
                THEN (tr.netto_weight * tr.unloading_price*-1) 
                        ELSE 0 END
            WHEN (lab.laborRules = '3') AND tr.slip_retur IS NOT NULL 
                THEN (tr.netto_weight * tr.unloading_price) * -1
                    WHEN (lab.laborRules = '4') AND tr.slip_retur IS NOT NULL
                        THEN (tr.send_weight * tr.unloading_price) * -1
                ELSE NULL END AS Unloading_retur,

            tr.quantity AS qty
            FROM TRANSACTION tr
            LEFT JOIN stockpile_contract stc ON stc.stockpile_contract_id = tr.stockpile_contract_id
            LEFT JOIN contract con ON con.contract_id = stc.contract_id
            LEFT JOIN vendor ven ON ven.vendor_id = con.vendor_id
            LEFT JOIN stockpile st ON st.stockpile_id = stc.stockpile_id
            LEFT JOIN labor lab ON lab.labor_id = tr.labor_id

            WHERE {$whereProperty} {$whereSupName} {$whereSupCode} {$whereType} {$whereStockpile}
            GROUP BY tr.slip_no
        ) AS a
        GROUP BY a.contractType, a.vendorName, a.stockpileName
        ORDER BY a.transaction_id DESC, a.vendorName ASC";
 $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//  echo $sql;
//  die();

$fileName = "Suppliers Collection "  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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

$rowActive = 1; //row pertama -> selanjutnya akan increment ke bawah->
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

if ($periodTo1 != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Periode Awal = {$periodFrom1}");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Periode Akhir = {$periodTo1}");
    $rowActive++;
    $rowActive++;
}else{
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Periode = {$periodFrom}");
    $rowActive++;
    $rowActive++;
}
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Supplier Delivery Report");


$rowActive++;
$rowMerge = $rowActive+1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Suplier");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Kode Suplier");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Contract Type");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:H{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:J{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Susut");
$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:L{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Inventory Weight");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);

$jumlalhKolom = 0;
$jumlahKolom = $result->num_rows; 
if($jumlahKolom == 0){
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:L{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Data Kosong");
    $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray12);
}

$no = 1;
$totalSWQty = 0;            $totalSWAmount = 0;
$totalSusutQty = 0;         $totalSusutAmount = 0;
$totalInvQty = 0;          $totalInvAmount = 0;
// if($result->num_rows > 0){
    while($row = $result->fetch_object()) {
        $AmountSW = 0;              $tempAmountSw = 0;
        $AmountSusut = 0;           $tempAmountSusut = 0;
        $AmountInventory = 0;       $tempAmountInventory = 0;

        // $tempAmountSw = $row->AmountSendW;
        // $tempAmountSusut = $row->AmountSusut;
        // $tempAmountInventory = $row->AmountInventory;
        // if($Amount == 'PKS'){
        //     $AmountSW = $tempAmountSw;
        //     $AmountSusut = $tempAmountSusut;
        //     $AmountInventory = $tempAmountInventory;

        // }else if($Amount == 'ALL'){
        //     $AmountSW = ($row->amountFreight) + ($row->amountHandling) + ($row->Unloading) + ($tempAmountSw);
        //     $AmountSusut = ($row->amountFreight) + ($row->amountHandling) + ($row->Unloading) + ($tempAmountSusut);
        //     $AmountInventory = ($row->amountFreight) + ($row->amountHandling) + ($row->Unloading) + ($tempAmountInventory);

        // }

        $AmountSW = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountSendW);
        // echo " F -> " . $row->amountFreight . "<br> -> H " . $row->amountHandling . "<br> U -> " . $row->Unloading . "<br> SW -> " . $row->AmountSendW;
        // die();
        $AmountSusut = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountSusut);
        $AmountInventory = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountInventory);

        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
        $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->vendorName, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($row->vendorCode, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->contractType);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->stockpileName);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->sendweight);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $AmountSW); 
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->susut);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $AmountSusut);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->inventory);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $AmountInventory);
        //$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray11);
        $totalSWAmount = $totalSWAmount + $AmountSW;
        $totalSusutAmount = $totalSusutAmount + $AmountSusut;
        $totalInvAmount = $totalInvAmount + $AmountInventory;

        $totalSWQty = $totalSWQty + ($row->sendweight);
        $totalSusutQty = $totalSusutQty + ($row->susut);
        $totalInvQty = $totalInvQty + ($row->inventory);
        
        $no++;
    }
// }else{
//     echo "PLEASE CHECKLIST / ERROR";
// }

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Total : ");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $totalSWQty);
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $totalSWAmount); 
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $totalSusutQty);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $totalSusutAmount);
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $totalInvQty);
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $totalInvAmount);
$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);


$bodyRowEnd = $rowActive;
for ($temp = ord("B"); $temp <= ord("L"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":{$lastColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();