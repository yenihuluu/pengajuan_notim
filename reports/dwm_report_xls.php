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
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FF0000')
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


$value = $myDatabase->real_escape_string($_POST['value']); 
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
//$stockpileId = $_POST['stockpileIds'];
$stockpileId = $_POST['stockpileIds'];
$stockpile_names = $_POST['stockpile_names'];
$baseOn = $myDatabase->real_escape_string($_POST['baseOn']);
$spName = $_POST['spName'];

$whereProperty = '';
$whereProperty1 = '';
$whereProperty2 = '';
$whereProperty3 = '';

//$stockpileId = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15';

if($stockpileId != ''){
   $stockpileId1 = "WHERE stockpile_id IN ({$stockpileId})";
}else{
    $stockpileId1 = ""; ;
}


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    if($baseOn == 'monthly'){
        $whereProperty1 .= "AND DATE_FORMAT(transaction_date, '%Y-%m') BETWEEN DATE_FORMAT(STR_TO_DATE('{$periodFrom}', '%m/%Y'),'%Y-%m') 
                             AND DATE_FORMAT(STR_TO_DATE('{$periodTo}', '%m/%Y'),'%Y-%m')";
       $whereProperty .= "MONTHNAME(transaction_date) AS tanggal, DATE_FORMAT(transaction_date, '%Y-%m') as tanggal1";
        $whereProperty2 .= "DATE_FORMAT(transaction_date, '%Y-%m')";
        $whereProperty3 .= "a.tanggal1 ASC";
    }else if($baseOn == 'weekly'){
        $whereProperty1 .= "AND tr.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') 
                             AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
        $whereProperty2 = "tanggal, YEAR(tr.transaction_date), MONTH(tr.transaction_date),sp.`stockpile_name`, lab.`laborRules`, Unloading,  typePKS";
        $whereProperty .= "YEAR(tr.transaction_date) AS tahun,MONTH(tr.transaction_date) AS bulan, FLOOR((DAYOFMONTH(tr.`transaction_date`))/7)+1 AS tanggal";
        $whereProperty3 .= "a.bulan ASC, a.tanggal ASC";
    }else if($baseOn == 'daily'){
        $whereProperty .= " tr.transaction_date AS tanggal";
        $whereProperty1 .= "AND tr.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
        $whereProperty2 .= "tr.transaction_date";
        $whereProperty3 .= "a.tanggal";
    }else if($baseOn == 'year'){
        $whereProperty1 .= "AND YEAR(transaction_date) BETWEEN YEAR(STR_TO_DATE('{$periodFrom}', '%Y')) 
                            AND YEAR(STR_TO_DATE('{$periodTo}', '%Y'))";
        $whereProperty .= " YEAR(transaction_date) AS tanggal";
        $whereProperty2 .= "YEAR(transaction_date)";
        $whereProperty3 .= "a.tanggal ASC";
    }
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
        if($baseOn == 'monthly'){
            $whereProperty1 .= "AND DATE_FORMAT(transaction_date, '%Y-%m') = DATE_FORMAT(STR_TO_DATE('{$periodFrom}', '%m/%Y'),'%Y-%m')";
            $whereProperty .= "MONTHNAME(transaction_date) AS tanggal, DATE_FORMAT(transaction_date, '%Y-%m') as tanggal1";
            $whereProperty2 .= "DATE_FORMAT(transaction_date, '%Y-%m')";
            $whereProperty3 .= "a.tanggal1 ASC";
    }else if($baseOn == 'year'){
        $whereProperty1 .= "AND YEAR(transaction_date) = YEAR(STR_TO_DATE('{$periodFrom}', '%Y'))"; 
        $whereProperty .= " YEAR(transaction_date) AS tanggal";
        $whereProperty2 .= "YEAR(transaction_date)";
        $whereProperty3 .= "a.tanggal ASC";
    }  
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    if($baseOn == 'monthly'){
        $whereProperty1 .= "AND DATE_FORMAT(transaction_date, '%Y-%m') = DATE_FORMAT(STR_TO_DATE('{$periodTo}', '%m/%Y'),'%Y-%m')";
        $whereProperty .= "MONTHNAME(transaction_date) AS tanggal, DATE_FORMAT(transaction_date, '%Y-%m') as tanggal1";
        $whereProperty2 .= "DATE_FORMAT(transaction_date, '%Y-%m')";
        $whereProperty3 .= "a.tanggal1 ASC";
    }else if($baseOn == 'year'){
        $whereProperty1 .= "AND YEAR(transaction_date) = YEAR(STR_TO_DATE('{$periodTo}', '%Y'))"; 
        $whereProperty .= " YEAR(transaction_date) AS tanggal";
        $whereProperty2 .= "YEAR(transaction_date)";
        $whereProperty3 .= "a.tanggal ASC";
    }
}
  
/*if($baseOn == 'daily'){
    $sql = "CALL Sp_Daily_Report('{$stockpileId}', '{$periodFrom}')";
}else if($baseOn == 'weekly' || $baseOn == 'UP_TO'){
    $sql = "CALL DWM_Report('{$stockpileId}', '{$periodFrom}', '{$periodTo}')";
    //$sql= "CALL DWM_Report ('6,8,1,2,3','01/01/2019', '27/02/2020')";
}else if($baseOn == 'monthly'){
    $sql = "CALL Sp_Monthly_Report('{$stockpileId}', '{$periodFrom}')";
   // $sql= "CALL Sp_Monthly_Report('6,8,1,2,3','08/2019')";
}
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);*/


$fileName = "D.W.M "  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Q";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1; //row pertama -> selanjutnya akan increment ke bawah
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));


    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile = {$stockpile_names}");
    $rowActive++;
     if($baseOn == 'weekly' || $baseOn == 'daily'){
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Start Date  = {$periodFrom}");
    }else if($baseOn == 'monthly'){
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Start Month  = {$periodFrom}");
    }else if($baseOn == 'year'){
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Start Year  = {$periodFrom}");
    }

    if($baseOn == 'weekly' || $baseOn == 'daily'){
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Finish Date= {$periodTo}");
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Value = {$value}");
    }else if($baseOn == 'monthly'){
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Finish Month= {$periodTo}");
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Value = {$value}");
    }else if($baseOn == 'year'){
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Finish Year= {$periodTo}");
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Value = {$value}");
    }
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Jenis Report = {$baseOn}");
    $rowActive++;
    $rowActive++;

$rowActive++;
$rowActive++;
$rowMerge = $rowActive + 1;
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:Q{$rowActive}");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:Q{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Total Collection");

$rowMerge = $rowActive + 2;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");

$rowMerge = $rowActive + 2;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
if($baseOn == 'monthly'){
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Month");
} else if($baseOn == 'year'){
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Year");
}else if($baseOn == 'daily'){
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date");
}else if($baseOn == 'weekly'){
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Weekly");
}

$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:I{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Incoming from PKS Contract");

$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:O{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Incoming from PKS Curah");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:E{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:G{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Susut");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:I{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Inventory Weight");

$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:M{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Susut");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:O{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Inventory Weight");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Amount");

$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Amount");

$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
//$rowActive++;
$stockpileSQL = "SELECT * FROM stockpile {$stockpileId1} ORDER BY stockpile_code ASC";
$resultSP = $myDatabase->query($stockpileSQL, MYSQLI_STORE_RESULT);
while($rowSP = $resultSP->fetch_object()) {

    $sql = " SELECT * FROM (SELECT {$whereProperty}, 
                         sp.`stockpile_name` AS stockpileName,
                        lab.`laborRules` AS LaborRules,
                        SUM(tr.send_weight * tr.`unit_price`) AS AmountSendW,
                        SUM(tr.shrink * tr.`unit_price`) AS AmountSusut,
                        SUM(tr.quantity * tr.`unit_price`) AS AmountInventory,
                        SUM(tr.send_weight) AS sendweight,
                        SUM(tr.shrink) AS susut,
                        SUM(tr.quantity) AS inventory,
                        SUM(tr.netto_weight) AS netto,
                        SUM(tr.freight_price * tr.freight_quantity) AS amountFreight,
                        SUM(tr.handling_price * tr.quantity) AS amountHandling,
                        CASE WHEN (lab.laborRules = '1') THEN tr.unloading_price
                            WHEN (lab.laborRules = '2') THEN 
                                CASE WHEN (tr.send_weight < tr.netto_weight)
                                    THEN (tr.send_weight * tr.unloading_price) 
                                ELSE (tr.netto_weight * tr.unloading_price) 
                                END
                            WHEN (lab.laborRules = '3') THEN (tr.netto_weight * tr.unloading_price)
                            WHEN (lab.laborRules = '4') THEN (tr.send_weight * tr.unloading_price)
                        ELSE NULL 
                        END AS Unloading,
                        tr.quantity AS qty,
                        con.contract_type AS typePKS
                        FROM TRANSACTION tr
                        LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = tr.stockpile_contract_id
                        LEFT JOIN stockpile sp ON sp.stockpile_id = sc.stockpile_id 
                        LEFT JOIN contract con ON con.contract_id = sc.`contract_id`
                        LEFT JOIN labor lab ON lab.labor_id = tr.labor_id
                        WHERE tr.stockpile_contract_id 
                        IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$rowSP->stockpile_id})  
                        {$whereProperty1}
                        #AND tr.labor_id IS NOT NULL
                        GROUP BY {$whereProperty2}, con.contract_type
) AS a
ORDER BY {$whereProperty3}, a.typePKS DESC";
    //$sql ="CALL DWM_Report('{$rowSP->stockpile_id}','01/09/2019','30/09/2019')";

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$no = 1;
$totalQtyCollection = 0;
$totalAmountCollection = 0;
//Deklarasi var Collection
$tempCollectionQTY = 0;
$tempCollectionAmount = 0;
$tempCollectionQTY_Curah = 0;
$tempCollectionAmount_Curah = 0;
//Deklarasi var TOTAL
$TempSumQtySW = 0;          $TempSumQtySW_curah = 0;
$TempSumAmountSW = 0;       $TempSumAmountSW_curah = 0;
$TempSumQtySusut = 0;       $TempSumQtySusut_curah = 0;
$TempSumAmountSusut = 0;    $TempSumAmountSusut_curah = 0;
$TempSumQtyInv = 0;         $TempSumQtyInv_curah = 0;
$TempSumAmountInv = 0;      $TempSumAmountInv_curah = 0;
$GrandtotalQTY = 0;         $GrandtotalAmount = 0;
$TempGrandtotalQTY = 0;     $TempGrandtotalQTY_curah = 0;
$TempGrandtotalAmount = 0;  $TempGrandtotalAmount_curah = 0;

if($result->num_rows > 0){
while($row = $result->fetch_object()) {
    $tipePKS = '';
    $tempAmountSW = 0;
    $tempAmountSusut = 0;
    $tempAmountInventory = 0;
    $tipePKS = $row->typePKS;

    //Proses Perhitungan
    $tempPriceSW = 0;
    $tempPriceSusut = 0;
    $tempPriceInventory = 0;

    $tempPriceSW = $row->AmountSendW;
    $tempPriceSusut = $row->AmountSusut;
    $tempPriceInventory = $row->AmountInventory;
    if($value == 'PKS'){
        $tempAmountSW = $tempPriceSW;
        $tempAmountSusut = $tempPriceSusut;
        $tempAmountInventory = $tempPriceInventory;
    }else if($value == 'ALL'){
        $tempAmountSW = ($row->amountFreight) + ($row->amountHandling) + ($row->Unloading) + $tempPriceSW;
        $tempAmountSusut = ($row->amountFreight) + ($row->amountHandling) + ($row->Unloading) + $tempPriceSusut;
        $tempAmountInventory = ($row->amountFreight) + ($row->amountHandling) + ($row->Unloading) + $tempPriceInventory;
    }
   
    if($tipePKS == 'P'){
        $rowActive++;
        $array[] = $row;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
        if($baseOn == 'weekly'){
            $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Minggu ".$row->tanggal);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->tanggal);
        }
        //$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->tanggal);
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpileName);
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->sendweight);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $tempAmountSW);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->susut);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $tempAmountSusut); 
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->inventory);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $tempAmountInventory);
        //SUM Collection Contract
        //$tempCollectionQTY = ($row->sendweight) + ($row->susut) + ($row->inventory);
        //$tempCollectionAmount = $tempAmountSW + $tempAmountSusut + $tempAmountInventory;
		$tempCollectionQTY = $row->inventory;
        $tempCollectionAmount = $tempAmountInventory;
        

        //TOTAL
        $TempSumQtySW = $TempSumQtySW + ($row->sendweight);
        $TempSumAmountSW = $TempSumAmountSW + $tempAmountSW;
        $TempSumQtySusut = $TempSumQtySusut + ($row->susut);
        $TempSumAmountSusut = $TempSumAmountSusut + $tempAmountSusut;
        $TempSumQtyInv = $TempSumQtyInv + ($row->inventory);
        $TempSumAmountInv = $TempSumAmountInv + $tempAmountInventory;
        //tampungTempGrandTotal_Collection
        $TempGrandtotalQTY = $TempGrandtotalQTY + $tempCollectionQTY;
        $TempGrandtotalAmount = $TempGrandtotalAmount + $tempCollectionAmount;
    }else if($tipePKS == 'C'){
        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->sendweight);
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $tempAmountSW);
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->susut);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $tempAmountSusut); 
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->inventory);
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $tempAmountInventory);
        //Sum Collection CURAH
        //$tempCollectionQTY_Curah = ($row->sendweight) + ($row->susut) + ($row->inventory);
        //$tempCollectionAmount_Curah = $tempAmountSW + $tempAmountSusut + $tempAmountInventory;
		$tempCollectionQTY_Curah = $row->inventory;
        $tempCollectionAmount_Curah = $tempAmountInventory;

        //Total
        $TempSumQtySW_curah = $TempSumQtySW_curah + ($row->sendweight);
        $TempSumAmountSW_curah = $TempSumAmountSW_curah + $tempAmountSW;
        $TempSumQtySusut_curah = $TempSumQtySusut_curah + ($row->susut);
        $TempSumAmountSusut_curah = $TempSumAmountSusut_curah + $tempAmountSusut;
        $TempSumQtyInv_curah = $TempSumQtyInv_curah + ($row->inventory);
        $TempSumAmountInv_curah = $TempSumAmountInv_curah + $tempAmountInventory;
        //tampungTempGrandTotal_Collection
        $TempGrandtotalQTY_curah = $TempGrandtotalQTY_curah + $tempCollectionQTY_Curah;
        $TempGrandtotalAmount_curah = $TempGrandtotalAmount_curah + $tempCollectionAmount_Curah;
    }else{
        $rowActive++;
    }
        //GrandTotal Collection
        $totalQtyCollection = ($tempCollectionQTY_Curah + $tempCollectionQTY);
        $totalAmountCollection = $tempCollectionAmount + $tempCollectionAmount_Curah;
		
        //TOTAL COLLECTION 
        $GrandtotalQTY = $TempGrandtotalQTY_curah + $TempGrandtotalQTY;
        $GrandtotalAmount = $TempGrandtotalAmount_curah + $TempGrandtotalAmount;

        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalQtyCollection);
        $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $totalAmountCollection); 
    if($tipePKS == 'P'){
        $no++;
    }   
    
}
}else{
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:Q{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Data Kosong");
    $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
}

 $rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Total : ");
 $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $TempSumQtySW);
 $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $TempSumAmountSW);
 $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $TempSumQtySusut);
 $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $TempSumAmountSusut); 
 $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $TempSumQtyInv);
 $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $TempSumAmountInv);

 $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $TempSumQtySW_curah);
 $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $TempSumAmountSW_curah);
 $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $TempSumQtySusut_curah);
 $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $TempSumAmountSusut_curah); 
 $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $TempSumQtyInv_curah);
 $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $TempSumAmountInv_curah);

 $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $GrandtotalQTY);
 $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $GrandtotalAmount); 
 $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
 $rowActive++;


$bodyRowEnd = $rowActive;
for ($temp = ord("A"); $temp <= ord("Q"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
 if($baseOn == 'monthly'){
     $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("MMMM");
 }else if($baseOn == 'daily'){
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
 }

    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    //$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":K{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table
}
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
