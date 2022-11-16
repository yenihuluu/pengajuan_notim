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
$lastColumn = "EG";

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

$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:I{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "BANGKA BELITUNG");

$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:Q{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "BATU LICIN");
$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:Y{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "BENGKULU");
$objPHPExcel->getActiveSheet()->mergeCells("Z{$rowActive}:AG{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "PANGKALAN BUN");
$objPHPExcel->getActiveSheet()->mergeCells("AH{$rowActive}:AO{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "TANJUNG BUTTON");
$objPHPExcel->getActiveSheet()->mergeCells("AP{$rowActive}:AW{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", "DUMAI");
$objPHPExcel->getActiveSheet()->mergeCells("AX{$rowActive}:BE{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("AX{$rowActive}", "JAKARTA");
$objPHPExcel->getActiveSheet()->mergeCells("BF{$rowActive}:BM{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BF{$rowActive}", "JAMBI");
$objPHPExcel->getActiveSheet()->mergeCells("BN{$rowActive}:BU{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BN{$rowActive}", "MAREDAN");
$objPHPExcel->getActiveSheet()->mergeCells("BV{$rowActive}:CC{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("BV{$rowActive}", "PADANG");
$objPHPExcel->getActiveSheet()->mergeCells("CD{$rowActive}:CK{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("CD{$rowActive}", "PALEMBANG");
$objPHPExcel->getActiveSheet()->mergeCells("CL{$rowActive}:CS{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("CL{$rowActive}", "PEKANBARU");
$objPHPExcel->getActiveSheet()->mergeCells("CT{$rowActive}:DA{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("CT{$rowActive}", "PONTIANAK");
$objPHPExcel->getActiveSheet()->mergeCells("DB{$rowActive}:DI{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("DB{$rowActive}", "RENGAT");
$objPHPExcel->getActiveSheet()->mergeCells("DJ{$rowActive}:DQ{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("DJ{$rowActive}", "SAMPIT");
$objPHPExcel->getActiveSheet()->mergeCells("DR{$rowActive}:DY{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("DR{$rowActive}", "SAMARINDA");
$objPHPExcel->getActiveSheet()->mergeCells("DZ{$rowActive}:EG{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("DZ{$rowActive}", "TAYAN");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray3);

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", "Jumlah Notim Out");


$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("AM{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("AU{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("AV{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("AW{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("AX{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("AY{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("AZ{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("BA{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("BB{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("BC{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("BD{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("BE{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("BF{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BG{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BH{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("BI{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("BJ{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("BK{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("BL{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("BM{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("BN{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BO{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BP{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("BQ{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("BR{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("BS{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("BT{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("BU{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("BV{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("BW{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("BX{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("BY{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("BZ{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CA{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CB{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("CC{$rowActive}", "Jumlah Notim Out");


$objPHPExcel->getActiveSheet()->setCellValue("CD{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("CE{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("CF{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("CG{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("CH{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CI{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CJ{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("CK{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("CL{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("CM{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("CN{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("CO{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("CP{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CQ{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CR{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("CS{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("CT{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("CU{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("CV{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("CW{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("CX{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CY{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("CZ{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("DA{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("DB{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("DC{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("DD{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("DE{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("DF{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("DG{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("DH{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("DI{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("DJ{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("DK{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("DL{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("DM{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("DN{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("DO{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("DP{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("DQ{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("DR{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("DS{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("DT{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("DU{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("DV{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("DW{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("DX{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("DY{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->setCellValue("EZ{$rowActive}", "Qty (MT)");
$objPHPExcel->getActiveSheet()->setCellValue("EA{$rowActive}", "Jumlah Mobil (Transaction)");
$objPHPExcel->getActiveSheet()->setCellValue("EB{$rowActive}", "Jumlah Mobil (Timbangan)");
$objPHPExcel->getActiveSheet()->setCellValue("EC{$rowActive}", "Jumlah ISCC");
$objPHPExcel->getActiveSheet()->setCellValue("ED{$rowActive}", "Jumlah GGL");
$objPHPExcel->getActiveSheet()->setCellValue("EE{$rowActive}", "Jumlah ISCC_GGL");
$objPHPExcel->getActiveSheet()->setCellValue("EF{$rowActive}", "Jumlah Uncertified");
$objPHPExcel->getActiveSheet()->setCellValue("EG{$rowActive}", "Jumlah Notim Out");

$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray3);
//$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period");

$tempMonth = '';
if($resultHead->num_rows > 0){

    while($rowHead = $resultHead->fetch_object()) {
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowHead->Periode); 
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowHead->qtyBan); 
        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowHead->countVehicleBANNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowHead->countVehicleBANTim);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowHead->ISCC_BAN);
        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowHead->GGL_BAN);
        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowHead->ISCC_GGL_BAN);
        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowHead->uncertified_BAN);
        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowHead->notimOut_BAN);

        $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowHead->qtyBat); 
        $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowHead->countVehicleBATNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowHead->countVehicleBATTim);
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $rowHead->ISCC_BAT);
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $rowHead->GGL_BAT);
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $rowHead->ISCC_GGL_BAT);
        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $rowHead->uncertified_BAT);
        $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $rowHead->notimOut_BAT);

        $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $rowHead->qtyBen); 
        $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $rowHead->countVehicleBENotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $rowHead->countVehicleBENTim);
        $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $rowHead->ISCC_BEN);
        $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $rowHead->GGL_BEN);
        $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $rowHead->ISCC_GGL_BEN);
        $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $rowHead->uncertified_BEN);
        $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $rowHead->notimOut_BEN);
        
        $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $rowHead->qtyBUN); 
        $objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $rowHead->countVehicleBUNNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $rowHead->countVehicleBUNTim);
        $objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $rowHead->ISCC_BUN);
        $objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", $rowHead->GGL_BUN);
        $objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $rowHead->ISCC_GGL_BUN);
        $objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $rowHead->uncertified_BUN);
        $objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", $rowHead->notimOut_BUN);

        $objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", $rowHead->qtyBUT); 
        $objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", $rowHead->countVehicleBUTNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", $rowHead->countVehicleBUTTim);
        $objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", $rowHead->ISCC_BUT);
        $objPHPExcel->getActiveSheet()->setCellValue("AL{$rowActive}", $rowHead->GGL_BUT);
        $objPHPExcel->getActiveSheet()->setCellValue("AM{$rowActive}", $rowHead->ISCC_GGL_BUT);
        $objPHPExcel->getActiveSheet()->setCellValue("AN{$rowActive}", $rowHead->uncertified_BUT);
        $objPHPExcel->getActiveSheet()->setCellValue("AO{$rowActive}", $rowHead->notimOut_BUT);

        $objPHPExcel->getActiveSheet()->setCellValue("AP{$rowActive}", $rowHead->qtyDUM); 
        $objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", $rowHead->countVehicleDUMNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AR{$rowActive}", $rowHead->countVehicleDUMTim);
        $objPHPExcel->getActiveSheet()->setCellValue("AS{$rowActive}", $rowHead->ISCC_DUM);
        $objPHPExcel->getActiveSheet()->setCellValue("AT{$rowActive}", $rowHead->GGL_DUM);
        $objPHPExcel->getActiveSheet()->setCellValue("AU{$rowActive}", $rowHead->ISCC_GGL_DUM);
        $objPHPExcel->getActiveSheet()->setCellValue("AV{$rowActive}", $rowHead->uncertified_DUM);
        $objPHPExcel->getActiveSheet()->setCellValue("AW{$rowActive}", $rowHead->notimOut_DUM);

        $objPHPExcel->getActiveSheet()->setCellValue("AX{$rowActive}", $rowHead->qtyHO); 
        $objPHPExcel->getActiveSheet()->setCellValue("AY{$rowActive}", $rowHead->countVehicleHONotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("AZ{$rowActive}", $rowHead->countVehicleHOTim);
        $objPHPExcel->getActiveSheet()->setCellValue("BA{$rowActive}", $rowHead->ISCC_HO);
        $objPHPExcel->getActiveSheet()->setCellValue("BB{$rowActive}", $rowHead->GGL_HO);
        $objPHPExcel->getActiveSheet()->setCellValue("BC{$rowActive}", $rowHead->ISCC_GGL_HO);
        $objPHPExcel->getActiveSheet()->setCellValue("BD{$rowActive}", $rowHead->uncertified_HO);
        $objPHPExcel->getActiveSheet()->setCellValue("BE{$rowActive}", $rowHead->notimOut_HO);

        $objPHPExcel->getActiveSheet()->setCellValue("BF{$rowActive}", $rowHead->qtyJAM); 
        $objPHPExcel->getActiveSheet()->setCellValue("BG{$rowActive}", $rowHead->countVehicleJAMNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BH{$rowActive}", $rowHead->countVehicleJAMTim);
        $objPHPExcel->getActiveSheet()->setCellValue("BI{$rowActive}", $rowHead->ISCC_JAM);
        $objPHPExcel->getActiveSheet()->setCellValue("BJ{$rowActive}", $rowHead->GGL_JAM);
        $objPHPExcel->getActiveSheet()->setCellValue("BK{$rowActive}", $rowHead->ISCC_GGL_JAM);
        $objPHPExcel->getActiveSheet()->setCellValue("BL{$rowActive}", $rowHead->uncertified_JAM);
        $objPHPExcel->getActiveSheet()->setCellValue("BM{$rowActive}", $rowHead->notimOut_JAM);

        $objPHPExcel->getActiveSheet()->setCellValue("BN{$rowActive}", $rowHead->qtyMAR); 
        $objPHPExcel->getActiveSheet()->setCellValue("BO{$rowActive}", $rowHead->countVehicleMARNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BP{$rowActive}", $rowHead->countVehicleMARTim);
        $objPHPExcel->getActiveSheet()->setCellValue("BQ{$rowActive}", $rowHead->ISCC_MAR);
        $objPHPExcel->getActiveSheet()->setCellValue("BR{$rowActive}", $rowHead->GGL_MAR);
        $objPHPExcel->getActiveSheet()->setCellValue("BS{$rowActive}", $rowHead->ISCC_GGL_MAR);
        $objPHPExcel->getActiveSheet()->setCellValue("BT{$rowActive}", $rowHead->uncertified_MAR);
        $objPHPExcel->getActiveSheet()->setCellValue("BU{$rowActive}", $rowHead->notimOut_MAR);

        $objPHPExcel->getActiveSheet()->setCellValue("BV{$rowActive}", $rowHead->qtyPAD); 
        $objPHPExcel->getActiveSheet()->setCellValue("BW{$rowActive}", $rowHead->countVehiclePADNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("BX{$rowActive}", $rowHead->countVehiclePADTim);
        $objPHPExcel->getActiveSheet()->setCellValue("BY{$rowActive}", $rowHead->ISCC_PAD);
        $objPHPExcel->getActiveSheet()->setCellValue("BZ{$rowActive}", $rowHead->GGL_PAD);
        $objPHPExcel->getActiveSheet()->setCellValue("CA{$rowActive}", $rowHead->ISCC_GGL_PAD);
        $objPHPExcel->getActiveSheet()->setCellValue("CB{$rowActive}", $rowHead->uncertified_PAD);
        $objPHPExcel->getActiveSheet()->setCellValue("CC{$rowActive}", $rowHead->notimOut_PAD);

        $objPHPExcel->getActiveSheet()->setCellValue("CD{$rowActive}", $rowHead->qtyPAL); 
        $objPHPExcel->getActiveSheet()->setCellValue("CE{$rowActive}", $rowHead->countVehiclePALNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("CF{$rowActive}", $rowHead->countVehiclePALTim);
        $objPHPExcel->getActiveSheet()->setCellValue("CG{$rowActive}", $rowHead->ISCC_PAL);
        $objPHPExcel->getActiveSheet()->setCellValue("CH{$rowActive}", $rowHead->GGL_PAL);
        $objPHPExcel->getActiveSheet()->setCellValue("CI{$rowActive}", $rowHead->ISCC_GGL_PAL);
        $objPHPExcel->getActiveSheet()->setCellValue("CJ{$rowActive}", $rowHead->uncertified_PAL);
        $objPHPExcel->getActiveSheet()->setCellValue("CK{$rowActive}", $rowHead->notimOut_PAL);

        $objPHPExcel->getActiveSheet()->setCellValue("CL{$rowActive}", $rowHead->qtyPEK); 
        $objPHPExcel->getActiveSheet()->setCellValue("CM{$rowActive}", $rowHead->countVehiclePEKNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("CN{$rowActive}", $rowHead->countVehiclePEKTim);
        $objPHPExcel->getActiveSheet()->setCellValue("CO{$rowActive}", $rowHead->ISCC_PEK);
        $objPHPExcel->getActiveSheet()->setCellValue("CP{$rowActive}", $rowHead->GGL_PEK);
        $objPHPExcel->getActiveSheet()->setCellValue("CQ{$rowActive}", $rowHead->ISCC_GGL_PEK);
        $objPHPExcel->getActiveSheet()->setCellValue("CR{$rowActive}", $rowHead->uncertified_PEK);
        $objPHPExcel->getActiveSheet()->setCellValue("CS{$rowActive}", $rowHead->notimOut_PEK);

        $objPHPExcel->getActiveSheet()->setCellValue("CT{$rowActive}", $rowHead->qtyPON); 
        $objPHPExcel->getActiveSheet()->setCellValue("CU{$rowActive}", $rowHead->countVehiclePONNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("CV{$rowActive}", $rowHead->countVehiclePONTim);
        $objPHPExcel->getActiveSheet()->setCellValue("CW{$rowActive}", $rowHead->ISCC_PON);
        $objPHPExcel->getActiveSheet()->setCellValue("CX{$rowActive}", $rowHead->GGL_PON);
        $objPHPExcel->getActiveSheet()->setCellValue("CY{$rowActive}", $rowHead->ISCC_GGL_PON);
        $objPHPExcel->getActiveSheet()->setCellValue("CZ{$rowActive}", $rowHead->uncertified_PON);
        $objPHPExcel->getActiveSheet()->setCellValue("DA{$rowActive}", $rowHead->notimOut_PON);

        $objPHPExcel->getActiveSheet()->setCellValue("DB{$rowActive}", $rowHead->qtyREN); 
        $objPHPExcel->getActiveSheet()->setCellValue("DC{$rowActive}", $rowHead->countVehicleRENNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("DD{$rowActive}", $rowHead->countVehicleRENTim);
        $objPHPExcel->getActiveSheet()->setCellValue("DE{$rowActive}", $rowHead->ISCC_REN);
        $objPHPExcel->getActiveSheet()->setCellValue("DF{$rowActive}", $rowHead->GGL_REN);
        $objPHPExcel->getActiveSheet()->setCellValue("DG{$rowActive}", $rowHead->ISCC_GGL_REN);
        $objPHPExcel->getActiveSheet()->setCellValue("DH{$rowActive}", $rowHead->uncertCified_REN);
        $objPHPExcel->getActiveSheet()->setCellValue("DI{$rowActive}", $rowHead->notimOut_REN);

        $objPHPExcel->getActiveSheet()->setCellValue("DJ{$rowActive}", $rowHead->qtySAM); 
        $objPHPExcel->getActiveSheet()->setCellValue("DK{$rowActive}", $rowHead->countVehicleSAMNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("DL{$rowActive}", $rowHead->countVehicleSAMTim);
        $objPHPExcel->getActiveSheet()->setCellValue("DM{$rowActive}", $rowHead->ISCC_SAM);
        $objPHPExcel->getActiveSheet()->setCellValue("DN{$rowActive}", $rowHead->GGL_SAM);
        $objPHPExcel->getActiveSheet()->setCellValue("DO{$rowActive}", $rowHead->ISCC_GGL_SAM);
        $objPHPExcel->getActiveSheet()->setCellValue("DP{$rowActive}", $rowHead->uncertified_SAM);
        $objPHPExcel->getActiveSheet()->setCellValue("DQ{$rowActive}", $rowHead->notimOut_SAM);

        $objPHPExcel->getActiveSheet()->setCellValue("DR{$rowActive}", $rowHead->qtySMR); 
        $objPHPExcel->getActiveSheet()->setCellValue("DS{$rowActive}", $rowHead->countVehicleSMRNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("DT{$rowActive}", $rowHead->countVehicleSMRTim);
        $objPHPExcel->getActiveSheet()->setCellValue("DU{$rowActive}", $rowHead->ISCC_SMR);
        $objPHPExcel->getActiveSheet()->setCellValue("DV{$rowActive}", $rowHead->GGL_SMR);
        $objPHPExcel->getActiveSheet()->setCellValue("DW{$rowActive}", $rowHead->ISCC_GGL_SMR);
        $objPHPExcel->getActiveSheet()->setCellValue("DX{$rowActive}", $rowHead->uncertified_SMR);
        $objPHPExcel->getActiveSheet()->setCellValue("DY{$rowActive}", $rowHead->notimOut_SMR);

        $objPHPExcel->getActiveSheet()->setCellValue("DZ{$rowActive}", $rowHead->qtyTYN); 
        $objPHPExcel->getActiveSheet()->setCellValue("EA{$rowActive}", $rowHead->countVehicleTYNNotim); 
        $objPHPExcel->getActiveSheet()->setCellValue("EB{$rowActive}", $rowHead->countVehicleTYNTim);
        $objPHPExcel->getActiveSheet()->setCellValue("EC{$rowActive}", $rowHead->ISCC_TYN);
        $objPHPExcel->getActiveSheet()->setCellValue("ED{$rowActive}", $rowHead->GGL_TYN);
        $objPHPExcel->getActiveSheet()->setCellValue("EE{$rowActive}", $rowHead->ISCC_GGL_TYN);
        $objPHPExcel->getActiveSheet()->setCellValue("EF{$rowActive}", $rowHead->uncertified_TYN);
        $objPHPExcel->getActiveSheet()->setCellValue("EG{$rowActive}", $rowHead->notimOut_TYN);
    }
}

$bodyRowEnd = $rowActive; 

for ($temp = ord("A"); $temp <= ord("EG"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("EG")->setAutoSize(true);
if($baseOn == 'Monthly'){
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":A{$bodyRowEnd}")->getNumberFormat()->setFormatCode("MMMM");
}else if($baseOn == 'Daily'){
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":A{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
}


// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":EG{$bodyRowEnd}")->getNumberFormat()->setFormatCode('_(""* #,##0_);_(\(#,##0\);_(""* ""??_);_(@_)'); 
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>