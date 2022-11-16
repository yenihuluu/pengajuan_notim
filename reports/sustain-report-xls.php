<?php
ini_set('memory_limit', '1024M');
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


//vembri
$whereProperty = '';

$dateProperty = '';

$periodFrom = '';

$periodTo = '';

$amount = '';

$gudang = '';

$stockpile_id = $_POST['stockpile_id'];
//echo $stockpile_id;

$periodFull = '';

if($stockpile_id != ''){
    $stockpile_id1 = " AND s.stockpile_id IN ({$stockpile_id})";
    //echo $stockpile_id1;
}else{
    $stockpile_id1 = "";
    //echo $stockpile_id1; 
}

$whereProperty .= $stockpile_id1;
//echo $whereProperty;

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    
    $dateProperty = " AND transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

    $periodFull = $periodFrom . " - " . $periodTo . " ";
    
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

    $dateProperty = " AND transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

    $periodFull = "From " . $periodFrom . " ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

    $dateProperty = " AND transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

    $periodFull = "To " . $periodTo . " ";

}


$sql = "SELECT v.vendor_name AS pks,
        SUM(t.send_weight) AS tonase_pengiriman_mt,
        COUNT(DISTINCT(CASE WHEN t.t_timbangan > 0 THEN t.t_timbangan  END)) AS ritase_pengiriman,
       -- COUNT(t.transaction_id) AS ritase_pengiriman,
        s.stockpile_name AS gudang,
        CASE
            WHEN v.rsb = 1 AND v.ggl = 0 THEN 'ISCC'
            WHEN v.ggl = 1 AND v.rsb = 0 THEN 'GGL'
            WHEN v.rsb = 1 AND v.ggl = 1 THEN 'ISCC $ GGL'
            ELSE 'Uncertified'
        END AS claim
        FROM TRANSACTION t 
        LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id
        LEFT JOIN contract c ON sc.contract_id = c.contract_id
        LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
        LEFT JOIN stockpile s ON sc.stockpile_id = s.stockpile_id
        WHERE t.slip_no NOT IN (
        SELECT LEFT(slip_retur,17) AS retur FROM TRANSACTION
        WHERE slip_retur IS NOT NULL {$dateProperty}
        ) AND t.slip_retur IS NULL
        AND (t.mutasi_id IS NULL OR t.mutasi_id = '0')
        AND c.langsir = 0
{$whereProperty}
GROUP BY v.vendor_id, s.stockpile_id
ORDER BY v.vendor_name, s.stockpile_name";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;

//</editor-fold>

$fileName = "Sustain Report" . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "F";

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

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "SUSTAIN REPORT");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PKS");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Tonase Pengiriman(MT)");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Ritase Pengiriman");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Jenis Klaim");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while($row = $result->fetch_object()) {

$rowActive++;
	
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->pks);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->tonase_pengiriman_mt);	
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->ritase_pengiriman);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->gudang);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->claim);
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

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode('_(""* #,##0_);_(\(#,##0\);_(""* ""??_);_(@_)'); 

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->setPreCalculateFormulas(false);
$objWriter->save('php://output');
// </editor-fold>
exit();