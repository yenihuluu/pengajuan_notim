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
    )
);

$styleArray10 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK
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

$styleArray13 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$styleArray14 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'DCDCDC')
    )
);


$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$stockpileId = $_POST['stockpileId'];
$stockpile_names = $_POST['stockpile_names'];

$whereProperty = '';
$whereProperty1 = '';

$newDateFrom = date("Y-m-d", strtotime($periodFrom));
$newDateTo = date("Y-m-d", strtotime($periodTo));

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty1 .= " AND tr.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty1 .= " AND tr.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty1 .= " AND tr.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}
if ($_POST['stockpileId'] == 'all') {
    $stockpileId = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,17';
    $sql = "CALL `sp_StockMutationReport` ('{$stockpileId}', '{$periodFrom}', '{$periodTo}')";
} else {
    $sql = "CALL `sp_StockMutationReport` ('{$stockpileId}', '{$periodFrom}', '{$periodTo}')";
}
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($stockpileId !== '') {
    // $stockpileId = $_POST['stockpileId'];
    $stockpile_name = array();
    $stockpile_code = array();
    $stockpileNames = '';
    $stockpileCodes = '';
    $sql1 = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileId})";
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

$fileName = "Stock-Mutation-Report " . $stockpileCodes . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "w";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpile_names}");
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Periode Awal = {$periodFrom}");
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Periode Akhir = {$periodTo}");
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "STOCK MUTATION REPORT");


$rowActive++;
$rowMergeStockpile = $rowActive + 4;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMergeStockpile}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMergeStockpile}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:E{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "{$periodFrom}");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:U{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Mutation Stock");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:W{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "{$periodTo}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray14);


$rowActive++;
$rowMergeStockBalance = $rowActive + 2;
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:E{$rowMergeStockBalance}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Beginning Stock Balance");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:O{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "(IN)");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:U{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "(OUT)");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:W{$rowMergeStockBalance}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Ending Stock Balance");
$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray14);


$rowActive++;
$rowMerge = $rowActive + 1;
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Purchasing");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Transfer Stock");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:O{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Total Incoming Stock");

$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:Q{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Shipment");
$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:S{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Transfer Stock");
$objPHPExcel->getActiveSheet()->mergeCells("T{$rowActive}:U{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Total Outgoing Stock");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:W{$rowMerge}");
$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray14);


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:H{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "PKS Kontrak");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PKS Curah");
$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray14);

$rowActive++;
//Beginning Stock Balance
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Amount");

//(IN)
//PKS Base Smount
//$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:G{$rowActive}");
//$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Amount");
//PKS Kontrak
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Amount Price");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Amount All");
//PKS Curah
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Amount Price");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Amount All");
//Transfer Stock
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Amount");
//Total Incoming Stock
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Amount");

//(OUT)
//Shipment
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Amount");
//Transfer Stock
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Amount");
//Total Outgoing Stock
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Amount");

//End Stock Balance
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray14);


$no = 1;
//Deklarasi Var Total
$grandTotalQtyBeginStock = 0;
$grandTotalAmountBeginStock = 0;
$grandTotalPksBaseAmount = 0;
$grandTotalQtyPksKontrak = 0;
$grandTotalAmountPksKontrak = 0;
$grandTotalQtyPksCurah = 0;
$grandTotalAmountPksCurah = 0;
$grandTotalQtyTransferStockIn = 0;
$grandTotalAmountTransferStockIn = 0;
$grandTotalQtyIncomingStock = 0;
$grandTotalAmountIncomingStock = 0;
$grandTotalQtyShipment = 0;
$grandTotalAmountShipment = 0;
$grandTotalQtyTransferStockOut = 0;
$grandTotalAmountTransferStockOut = 0;
$grandTotalQtyOutgoingStock = 0;
$grandTotalAmountOutgoingStock = 0;
$grandTotalQtyEndingStock = 0;
$grandTotalAmountEndingStock = 0;

while ($row = $result->fetch_object()) {

    //Incoming & Outgoing & Ending Stock Balance
    $qtyTotalIncoming = round(($row->quantity_available + $row->quantity_kontrak + $row->quantity_curah + $row->quantity_transferIn), 2);
    $amountTotalIncoming = round(($row->amount_pks + $row->amount_kontrak + $row->amount_curah + $row->transferStock_in), 2);
    $qtyTotalOutgoing = round(($row->quantity_shipment + $row->quantity_transferOut), 2);
    $amountTotalOutgoing = round(($row->amount_shipment + $row->transferStock_out), 2);
    $qtyEndingStockBalance = round(($qtyTotalIncoming + $qtyTotalOutgoing), 2);
    $amountEndingStockBalance = round(($amountTotalIncoming + $amountTotalOutgoing), 2);

    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpile_name);
    //Beginning Balance
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->quantity_available);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->amount_pks);
    //PKS Kontrak
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->quantity_kontrak);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->base_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->amount_kontrak);
    //PKS Curah
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->quantity_curah);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->base_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->amount_curah);
    //Transfer Stock In
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->quantity_transferIn);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->transferStock_in);
    //Total Incoming
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $qtyTotalIncoming);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $amountTotalIncoming);
    //Shipment
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->quantity_shipment);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->amount_shipment);
    //Transfer Stock Out
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->quantity_transferOut);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->transferStock_out);
    //Total Outgoing
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $qtyTotalOutgoing);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $amountTotalOutgoing);
    //Ending Balance
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $qtyEndingStockBalance);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $amountEndingStockBalance);
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}" . ":{$lastColumn}{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    //GrandTotal
    $grandTotalQtyBeginStock += $row->quantity_available;
    $grandTotalAmountBeginStock += $row->amount_pks;
    $grandTotalPksBaseAmount += $row->base_amount;
    $grandTotalQtyPksKontrak += $row->quantity_kontrak;
    $grandTotalAmountPksKontrak += $row->amount_kontrak;
    $grandTotalQtyPksCurah += $row->quantity_curah;
    $grandTotalAmountPksCurah += $row->amount_curah;
    $grandTotalQtyTransferStockIn += $row->quantity_transferIn;
    $grandTotalAmountTransferStockIn += $row->transferStock_in;
    $grandTotalQtyIncomingStock += $qtyTotalIncoming;
    $grandTotalAmountIncomingStock += $amountTotalIncoming;
    $grandTotalQtyShipment += $row->quantity_shipment;
    $grandTotalAmountShipment += $row->amount_shipment;
    $grandTotalQtyTransferStockOut += $row->quantity_transferOut;
    $grandTotalAmountTransferStockOut += $row->transferStock_out;
    $grandTotalQtyOutgoingStock += $qtyTotalOutgoing;
    $grandTotalAmountOutgoingStock += $amountTotalOutgoing;
    $grandTotalQtyEndingStock += $qtyEndingStockBalance;
    $grandTotalAmountEndingStock += $amountEndingStockBalance;

    $no++;
}

$rowActive++;
$rowMerge = $rowActive + 1;

$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:C{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "GRAND TOTAL");
//Beginning Balance
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $grandTotalQtyBeginStock);
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $grandTotalAmountBeginStock);
//PKS Kontrak
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $grandTotalQtyPksKontrak);
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $grandTotalPksBaseAmount);
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $grandTotalAmountPksKontrak);
//PKS Curah
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $grandTotalQtyPksCurah);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $grandTotalPksBaseAmount);
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $grandTotalAmountPksCurah);
//Transfer Stock In
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $grandTotalQtyTransferStockIn);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $grandTotalAmountTransferStockIn);
//Total Incoming
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $grandTotalQtyIncomingStock);
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $grandTotalAmountIncomingStock);
//Shipment
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $grandTotalQtyShipment);
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $grandTotalAmountShipment);
//Transfer Stock Out
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $grandTotalQtyTransferStockOut);
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $grandTotalAmountTransferStockOut);
//Total Outgoing
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $grandTotalQtyOutgoingStock);
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $grandTotalAmountOutgoingStock);
//Ending Balance
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $grandTotalQtyEndingStock);
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $grandTotalAmountEndingStock);
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray13);


$bodyRowEnd = $rowActive;
// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("B"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setAutoSize(true);

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>