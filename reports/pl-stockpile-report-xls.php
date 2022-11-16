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

$styleArray4b = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '99CCFF')
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

//$whereProperty = '';
$module1 = $myDatabase->real_escape_string($_POST['module1']);
$module2 = $module1 - 1;
//$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

$sql = "SELECT b.*, (bengkulu + dumai + jakarta + jambi + maredan + padang + rengat + sampit + tanjung_buton + tayan) AS grand_total 
FROM (SELECT a.NoAkun, a.NamaAkun, 
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Bengkulu') ELSE '0' END AS bengkulu,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Dumai') ELSE '0' END AS dumai,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Jakarta') ELSE '0' END AS jakarta,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Jambi') ELSE '0' END AS jambi,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Maredan') ELSE '0' END AS maredan,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Padang' ) ELSE '0' END AS padang,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Rengat') ELSE '0' END AS rengat,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Sampit') ELSE '0' END AS sampit,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Tanjung Buton') ELSE '0' END AS tanjung_buton,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Tayan') ELSE '0' END AS tayan
FROM gl a WHERE 
a.NoAkun LIKE '40%' OR a.NoAkun LIKE '51%' OR a.NoAkun LIKE '52%' OR a.NoAkun LIKE '53%' OR a.NoAkun LIKE '60%' 
OR a.NoAkun LIKE '61%' OR a.NoAkun LIKE '62%' OR a.NoAkun LIKE '70%'
GROUP BY a.NoAkun) b WHERE 1=1 ORDER BY b.NoAkun ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "PL Stockpile Report " . $module1 . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "N";

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

if ($module1 != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Periode = {$module1}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PL Stockpile Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No Akun");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Nama Akun");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", "Bengkulu", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", "Dumai", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", "Jakarta", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", "Jambi", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", "Maredan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", "Padang", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", "Rengat", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", "Sampit", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", "Tanjung Buton", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", "Tayan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Grand Total");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while($row = $result->fetch_object()) {
  

  $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
   //$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->NoAkun);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->NamaAkun, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->bengkulu);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->dumai);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->jakarta);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->jambi);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->maredan);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->padang);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->rengat);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->sampit);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->tanjung_buton);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->tayan);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->grand_total);
	

    $no++;
    
}
$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
   // $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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