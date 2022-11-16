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


$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'DOWNLOAD TIMBANGAN',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


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
$styleArray9 = array(
    'font' => array(
        'bold' => true,
		'color' => array('rgb' => 'FF0000')
    )
);
// </editor-fold>

$whereProperty = '';
$sumProperty = '';
$periodFrom = '';
$periodTo = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$stockpileId = '';
$stockpileIds = '';


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodFrom'];
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $periodFrom = $_POST['periodTo'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
}
//STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')
//STR_TO_DATE('{$periodTo}', '%d/%m/%Y')

$sql = "SELECT s.stockpile_id,s.stockpile_name,COALESCE(stscan.scanSuratTugas,0) AS scanSuratTugas ,
COALESCE(stNoScan.suratTugasTanpaScan,0) AS suratTugasTanpaScan,
COALESCE(stSystem.SuratTugastoSystem,0)AS SuratTugastoSystem,COALESCE(timbangan.tiketTimbang,0) AS tiketTimbang,
 COALESCE(timbanganSys.timbanganToSystem,0) AS timbanganToSystem,COALESCE(timbangMan.timbanganmanual,0) AS timbanganmanual,
 COALESCE(timbangan.tiketTimbang,0)-COALESCE(timbanganSys.timbanganToSystem,0) as selisih,
COALESCE(rejected.rejectst,0) AS rejectST
 FROM stockpile s
 LEFT JOIN(
 SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS scanSuratTugas
 FROM surattugas st INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
 WHERE st.terima_entry_date BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))*1000 AND UNIX_TIMESTAMP(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))*1000+86400000
 AND st.status_srtgs <> 2 GROUP BY s.`stockpile_id`,s.`stockpile_name` )AS stscan ON stscan.stockpile_id=s.`stockpile_id`
 LEFT JOIN(
 SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS suratTugasTanpaScan
 FROM surattugas st INNER JOIN transaction_timbangan tt ON tt.`counter`= st.counter
 INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
 WHERE st.terima_entry_date =0 AND st.kirim_entry_date = 0 AND st.status_srtgs <> 2
 AND tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
 GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS stNoScan ON stNoScan.stockpile_id=s.`stockpile_id`
 LEFT JOIN
 ( SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS SuratTugastoSystem
 FROM surattugas st INNER JOIN transaction_timbangan tt ON tt.`counter`= st.counter
 INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
 WHERE st.terima_entry_date BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))*1000 AND UNIX_TIMESTAMP(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))*1000+86400000
 AND tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
 AND st.status_srtgs <> 2 GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS stSystem ON stSystem.stockpile_id=s.`stockpile_id`
 LEFT JOIN(
 SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(tt.`transaction_id`) AS tiketTimbang
 FROM transaction_timbangan tt INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=tt.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
  WHERE tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
  GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS timbangan ON timbangan.stockpile_id=s.`stockpile_id`
  LEFT JOIN(
  SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(tt.`transaction_id`) AS timbanganToSystem
  FROM transaction_timbangan tt INNER JOIN `transaction` t ON t.`t_timbangan`=tt.`transaction_id`
  INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=tt.`stockpile_contract_id`
  INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
  WHERE tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
  AND (t.`t_timbangan` IS NOT NULL OR t.`t_timbangan`<>0)
  AND t.`slip_retur` IS NULL GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS timbanganSys ON timbanganSys.stockpile_id=s.`stockpile_id`
  LEFT JOIN(
  SELECT s.`stockpile_id`,s.`stockpile_name`,COALESCE(COUNT(t.`transaction_id`),0) AS timbanganmanual
  FROM `transaction` t INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=t.`stockpile_contract_id`
  INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
  WHERE t.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
  AND t.`t_timbangan` =0 AND t.`slip_retur` IS NULL GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS timbangMan ON timbangMan.stockpile_id=s.`stockpile_id`
  LEFT JOIN( 
	SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS rejectST 
	FROM surattugas st INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id` 
	INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id` 
	WHERE st.terima_entry_date BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))*1000 AND UNIX_TIMESTAMP(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))*1000+86400000 AND st.status_srtgs = 2 
	GROUP BY s.`stockpile_id`,s.`stockpile_name` )AS rejected ON rejected.stockpile_id=s.`stockpile_id`
  WHERE timbangan.tiketTimbang IS NOT NULL
  GROUP BY s.`stockpile_id`,s.`stockpile_name`";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


//</editor-fold>

$fileName = "Timbangan " .str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "H";

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
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Timbangan");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Tiket Timbang");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Notim Timbang");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Notim Manual");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Scan");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "W/O Scan");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "ST to System");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Reject ST");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

        while($row = $result->fetch_object()) {

    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);

//    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit(PHPExcel_Shared_Date::stringToExcel($rowPolicy->unloading_date2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->tiketTimbang);
//    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vehicle_no);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->timbanganToSystem);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->timbanganmanual);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->scanSuratTugas);
  //$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->slip_no);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->suratTugasTanpaScan);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->SuratTugastoSystem);	
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->rejectST);
}
$bodyRowEnd = $rowActive;

//        if ($bodyRowEnd > $headerRow + 1) {
//            $rowActive++;
//
//            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
//            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "T O T A L");
//            $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "=SUM(L" . ($headerRow + 1) . ":L{$bodyRowEnd})");
//            $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "=SUM(M" . ($headerRow + 1) . ":M{$bodyRowEnd})");
//
//            // Set number format for Amount
//            $objPHPExcel->getActiveSheet()->getStyle("L{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//
//
//            // Set border for table
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//            // Set row TOTAL to bold
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
//        }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("G"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

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
