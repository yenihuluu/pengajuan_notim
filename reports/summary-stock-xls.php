<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
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
        'color' => array('rgb' => 'D3D3D3')
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

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('Y-m');

$whereBalanceProperty = '';
$whereDeliveriesProperty = '';
$whereShipmentProperty = '';
$whereLessProperty = '';
$periodFrom = '';
$periodTo = '';
$baseOn = '';

$baseOn = $myDatabase->real_escape_string($_POST['baseOn']);

$periodFrom = $_POST['periodFrom'];
$periodTo = $_POST['periodTo'];

$resultHead = '';
if($baseOn == 'Monthly'){
    $sqlHead = "CALL sp_summary_stock_Month('{$periodFrom}', '{$periodTo}')";
    $Periode = 'Bulan';
    $Report = 'Monthly';
    $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
    //$sql= "CALL Sp_Monthly_Report('6,8,1,2,3','01/01/2019', '27/02/2020')";
}else if($baseOn == 'Daily'){
    //$sqlHead = "CALL sp_summary_stock_Daily('{$periodFrom}','{$periodTo}')";
    $sqlHead = "CALL sp_summary_stock_Daily('{$periodFrom}','{$periodTo}')";
    $Periode = 'Tanggal';
    $Report = 'Daily';
    $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
}

$fileName = "Summary Stock Collection" . $periodTo  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "BM";

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

    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period Start : {$periodFrom}");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period Finish : {$periodTo}");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Jenis Report : {$Report}");

$tempmonth;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Summary Stock Collection Report");

$rowActive++;
$headerRow = $rowActive;
$rowMerge = $rowActive + 1;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $Periode);

$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:E{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "BANGKA BELITUNG");

$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:I{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "BATU LICIN");

$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:M{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "BENGKULU");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:Q{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "PANGKALAN BUN");
$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:U{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "TANJUNG BUTTON");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:Y{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "DUMAI");
$objPHPExcel->getActiveSheet()->mergeCells("Z{$rowActive}:AC{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "JAKARTA");
$objPHPExcel->getActiveSheet()->mergeCells("AD{$rowActive}:AG{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", "JAMBI");
$objPHPExcel->getActiveSheet()->mergeCells("AH{$rowActive}:AK{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "MAREDAN");
$objPHPExcel->getActiveSheet()->mergeCells("AL{$rowActive}:AO{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", "PADANG");
$objPHPExcel->getActiveSheet()->mergeCells("AP{$rowActive}:AS{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", "PALEMBANG");
$objPHPExcel->getActiveSheet()->mergeCells("AT{$rowActive}:AW{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", "PEKANBARU");
$objPHPExcel->getActiveSheet()->mergeCells("AX{$rowActive}:BA{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AX{$rowActive}", "PONTIANAK");
$objPHPExcel->getActiveSheet()->mergeCells("BB{$rowActive}:BE{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BB{$rowActive}", "RENGAT");
$objPHPExcel->getActiveSheet()->mergeCells("BF{$rowActive}:BI{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BF{$rowActive}", "SAMPIT");
$objPHPExcel->getActiveSheet()->mergeCells("BJ{$rowActive}:BM{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BJ{$rowActive}", "SAMARINDA");
$objPHPExcel->getActiveSheet()->mergeCells("BN{$rowActive}:BQ{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BN{$rowActive}", "TAYAN");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray3);

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AM{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AU{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AV{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AW{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("AX{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AY{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("AZ{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BA{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("BB{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BC{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("BD{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BE{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("BF{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BG{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("BH{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BI{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("BJ{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BK{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("BL{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BM{$rowActive}", "Jumlah Mobil (Timbangan)");

$objPHPExcel->getActiveSheet()->setCellValue("BN{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BO{$rowActive}", "Jumlah Hari");
$objPHPExcel->getActiveSheet()->setCellValue("BP{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BQ{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray3);
//$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period");

$tempMonth = '';
if($resultHead->num_rows > 0){

    while($rowHead = $resultHead->fetch_object()) {
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowHead->Periode); 

        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowHead->qtyBan); 
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowHead->CountDayBan); 
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowHead->countVehicleBANNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowHead->countVehicleBANTim);

        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowHead->qtyBat); 
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowHead->CountDayBat); 
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowHead->countVehicleBATNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowHead->countVehicleBATTim);

        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowHead->qtyBen); 
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowHead->CountDayBen); 
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowHead->countVehicleBENotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $rowHead->countVehicleBENTim);
        
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $rowHead->qtyBUN); 
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $rowHead->CountDayBUN); 
        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $rowHead->countVehicleBUNNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $rowHead->countVehicleBUNTim);

        $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $rowHead->qtyBUT); 
        $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $rowHead->CountDayBUT); 
        $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $rowHead->countVehicleBUTNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $rowHead->countVehicleBUTTim);

        $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $rowHead->qtyDUM); 
        $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $rowHead->CountDayDUM); 
        $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $rowHead->countVehicleDUMNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $rowHead->countVehicleDUMTim);

        $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $rowHead->qtyHO); 
        $objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $rowHead->CountDayHO); 
        $objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $rowHead->countVehicleHONotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $rowHead->countVehicleHOTim);

        $objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", $rowHead->qtyJAM); 
        $objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $rowHead->CountDayJAM); 
        $objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $rowHead->countVehicleJAMNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", $rowHead->countVehicleJAMTim);

        $objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", $rowHead->qtyMAR); 
        $objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", $rowHead->CountDayMAR); 
        $objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", $rowHead->countVehicleMARNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", $rowHead->countVehicleMARTim);

        $objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", $rowHead->qtyPAD); 
        $objPHPExcel->getActiveSheet()->setCellValue("AM{$rowActive}", $rowHead->CountDayPAD); 
        $objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", $rowHead->countVehiclePADNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", $rowHead->countVehiclePADTim);

        $objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", $rowHead->qtyPAL); 
        $objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", $rowHead->CountDayPAL); 
        $objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", $rowHead->countVehiclePALNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", $rowHead->countVehiclePALTim);

        $objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", $rowHead->qtyPEK); 
        $objPHPExcel->getActiveSheet()->setCellValue("AU{$rowActive}", $rowHead->CountDayPEK); 
        $objPHPExcel->getActiveSheet()->setCellValue("AV{$rowActive}", $rowHead->countVehiclePEKNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AW{$rowActive}", $rowHead->countVehiclePEKTim);

        $objPHPExcel->getActiveSheet()->setCellValue("AX{$rowActive}", $rowHead->qtyPON); 
        $objPHPExcel->getActiveSheet()->setCellValue("AY{$rowActive}", $rowHead->CountDayPON); 
        $objPHPExcel->getActiveSheet()->setCellValue("AZ{$rowActive}", $rowHead->countVehiclePONNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BA{$rowActive}", $rowHead->countVehiclePONTim);

        $objPHPExcel->getActiveSheet()->setCellValue("BB{$rowActive}", $rowHead->qtyREN); 
        $objPHPExcel->getActiveSheet()->setCellValue("BC{$rowActive}", $rowHead->CountDayREN); 
        $objPHPExcel->getActiveSheet()->setCellValue("BD{$rowActive}", $rowHead->countVehicleRENNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BE{$rowActive}", $rowHead->countVehicleRENTim);

        $objPHPExcel->getActiveSheet()->setCellValue("BF{$rowActive}", $rowHead->qtySAM); 
        $objPHPExcel->getActiveSheet()->setCellValue("BG{$rowActive}", $rowHead->CountDaySAM); 
        $objPHPExcel->getActiveSheet()->setCellValue("BH{$rowActive}", $rowHead->countVehicleSAMNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BI{$rowActive}", $rowHead->countVehicleSAMTim);

        $objPHPExcel->getActiveSheet()->setCellValue("BJ{$rowActive}", $rowHead->qtySMR); 
        $objPHPExcel->getActiveSheet()->setCellValue("BK{$rowActive}", $rowHead->CountDaySMR); 
        $objPHPExcel->getActiveSheet()->setCellValue("BL{$rowActive}", $rowHead->countVehicleSMRNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BM{$rowActive}", $rowHead->countVehicleSMRTim);

        $objPHPExcel->getActiveSheet()->setCellValue("BN{$rowActive}", $rowHead->qtyTYN); 
        $objPHPExcel->getActiveSheet()->setCellValue("BO{$rowActive}", $rowHead->CountDayTYN); 
        $objPHPExcel->getActiveSheet()->setCellValue("BP{$rowActive}", $rowHead->countVehicleTYNNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BQ{$rowActive}", $rowHead->countVehicleTYNTim);

    }
}

$bodyRowEnd = $rowActive; 

for ($temp = ord("A"); $temp <= ord("BM"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("BM")->setAutoSize(true);
if($baseOn == 'Monthly'){
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":A{$bodyRowEnd}")->getNumberFormat()->setFormatCode("MMMM");
}else if($baseOn == 'Daily'){
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":A{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
}


// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":BM{$bodyRowEnd}")->getNumberFormat()->setFormatCode('_(""* #,##0_);_(\(#,##0\);_(""* ""??_);_(@_)'); 
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>