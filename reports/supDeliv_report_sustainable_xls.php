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

$sql = "SELECT  SUM(COALESCE(a.sendweight1,0)) AS sendweight,
        SUM(COALESCE(a.susut1,0)) AS susut,
        SUM(COALESCE(a.inventory1,0)) AS inventory,
        SUM(COALESCE(a.netto1,0)) AS netto,
        SUM(COALESCE(a.AmountSendW1,0)) AS AmountSendW,
        SUM(COALESCE(a.AmountSusut1,0)) AS AmountSusut,
        SUM(COALESCE(a.AmountInventory1,0)) AS AmountInventory,
        SUM(COALESCE(a.amountFreight1,0)) AS amountFreight,
        SUM(COALESCE(a.amountHandling1,0)) AS amountHandling,
        -- SUM(COALESCE(a.Unloading_retur,0)) + SUM(COALESCE(a.Unloading,0)) AS newUnloading, 
        SUM(COALESCE(a.Unloading,0)) AS newUnloading,
        SUM(COALESCE(a.fc_shrink1,0)) AS fc_shrink,
        a.* FROM 
            (
            SELECT tr.transaction_id, ven.vendor_name AS vendorName,
            ven.vendor_code AS vendorCode,
            tr.transaction_date AS tanggal,
            lab.`laborRules` AS laborRules,
            con.contract_type AS contractType,
            st.stockpile_name AS stockpileName,
            COALESCE(tr.send_weight,0) AS sendweight1,
            CASE WHEN con.contract_type = 'C' AND tr.transaction_type = 1
                THEN 0 
            ELSE COALESCE(tr.shrink,0) 
            END AS susut1,
            COALESCE(tr.quantity,0) AS inventory1,
            COALESCE(tr.netto_weight,0) AS netto1,
            CASE WHEN ven.rsb = 1 AND ven.ggl = 0 THEN 'ISCC'
        WHEN ven.rsb = 0 AND ven.ggl = 1 THEN 'GGL'
        WHEN ven.rsb = 0 AND ven.ggl = 0 THEN 'UNCERTIFIED'
        ELSE NULL END AS productClaim,

            CASE WHEN tr.mutasi_id > 0 AND con.langsir = 0
                    THEN (COALESCE(tr.send_weight,0) * COALESCE(tr.`unit_cost`,0))
            ELSE (COALESCE(tr.send_weight,0) * COALESCE(tr.`unit_price`,0))
            END AS AmountSendW1,

            CASE WHEN con.contract_type = 'C' AND tr.transaction_type = 1
                THEN 0 
                ELSE
                    CASE WHEN tr.mutasi_id > 0 AND con.langsir = 0
                        THEN (COALESCE(tr.shrink,0) * COALESCE(tr.`unit_cost`,0)) 
                        ELSE (COALESCE(tr.shrink,0) * COALESCE(tr.`unit_price`,0)) 
                    END
            END AS  AmountSusut1,

            CASE WHEN tr.mutasi_id > 0 AND con.langsir = 0
                    THEN (COALESCE(tr.quantity,0) * COALESCE(tr.`unit_cost`,0)) 
                        ELSE (COALESCE(tr.quantity,0) * COALESCE(tr.`unit_price`,0))   
            END AS AmountInventory1,

            (COALESCE(tr.freight_price,0) * COALESCE(tr.freight_quantity,0)) AS amountFreight1,
            (COALESCE(tr.handling_price,0) * COALESCE(tr.quantity,0)) AS amountHandling1,
            COALESCE(tr.unloading_price,0) AS Unloading,
            CASE WHEN fc.freight_cost_id != 0 AND ftx.tax_category = 1 AND ftx.tax_id != 0 
                THEN (COALESCE(ts.amt_claim,0) / ((100-COALESCE(ftx.tax_value,0))/100))
            WHEN fc.freight_cost_id != 0 
                THEN COALESCE(ts.amt_claim,0)
            ELSE 0 END AS fc_shrink1,
            COALESCE(tr.quantity,0) AS qty
            FROM TRANSACTION tr
            LEFT JOIN stockpile_contract stc ON stc.stockpile_contract_id = tr.stockpile_contract_id
            LEFT JOIN contract con ON con.contract_id = stc.contract_id
            LEFT JOIN vendor ven ON ven.vendor_id = con.vendor_id
            LEFT JOIN stockpile st ON st.stockpile_id = stc.stockpile_id
            LEFT JOIN labor lab ON lab.labor_id = tr.labor_id
            LEFT JOIN freight_cost fc ON fc.freight_cost_id = tr.freight_cost_id
            LEFT JOIN transaction_shrink_weight ts ON tr.transaction_id = ts.transaction_id
            LEFT JOIN tax ftx ON ftx.tax_id = tr.fc_tax_id
            WHERE {$whereProperty} {$whereSupName} {$whereSupCode} {$whereType} {$whereStockpile} AND con.langsir = 0 AND tr.transaction_type = 1
            GROUP BY tr.slip_no
        ) AS a
        GROUP BY a.contractType, a.vendorName, a.stockpileName
        ORDER BY a.transaction_id DESC, a.vendorName ASC";
 $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//  echo $sql;
//  die();

$fileName = "Suppliers Collection "  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "J";

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
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Susut");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Inventory Weight");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Product Claim");
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

        // $AmountSW = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountSendW);
        $AmountSW = ($row->AmountSendW);

        // echo " F -> " . $row->amountFreight . "<br> -> H " . $row->amountHandling . "<br> U -> " . $row->Unloading . "<br> SW -> " . $row->AmountSendW;
        // die();
        // $AmountSusut = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountSusut);
        // $AmountSusut = ($row->fc_shrink) + ($row->AmountSusut);
        $AmountSusut = ($row->AmountSusut);
        $AmountInventory = ($row->amountFreight - $row->fc_shrink) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountInventory);
        $AmountInventory =  ($row->AmountInventory);


        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
        $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->vendorName, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($row->vendorCode, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->contractType);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->stockpileName);

        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->sendweight);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->susut);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->inventory);
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->productClaim);


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
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $totalSusutQty);
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $totalInvQty);
$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);


$bodyRowEnd = $rowActive;
for ($temp = ord("B"); $temp <= ord("J"); $temp++) {
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
