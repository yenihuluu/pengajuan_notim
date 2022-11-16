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
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
//$tipePengajuan = $myDatabase->real_escape_string($_POST['tipePengajuan']);
$tipePengajuan = $_POST['tipePengajuan'];
$period = $periodFrom . '-' . $periodTo;
//$paymentSchedule = $myDatabase->real_escape_string($_POST['paymentSchedule']);

//$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty1 .= " AND a.request_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty2 .= " AND a.urgent_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty3 .= " AND a.request_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty4 .= " AND a.plan_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    
	//$whereProperty2 .= " AND ru.gl_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty1 .= " AND a.request_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $whereProperty2 .= " AND a.urgent_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $whereProperty3 .= " AND a.request_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $whereProperty4 .= " AND a.plan_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    
	//$whereProperty2 .= " AND ru.gl_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty1 .= " AND a.request_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty2 .= " AND a.urgent_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty3 .= " AND a.request_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty4 .= " AND a.plan_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
		
	//$whereProperty2 .= " AND ru.gl_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	
    $periodFull = "To " . $periodTo . " ";
}

if($tipePengajuan != '') {
     $whereProperty5 .= " AND a.type_pengajuan IN ($tipePengajuan)";
	 //$whereProperty2 .= " AND ru.gl_module IN ({$module})";
	 
    
}
//echo $db->error ;
//</editor-fold>

$fileName = "Logbook Report" . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "P";

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
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Periode = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Logbook Report");



$sqla = "SELECT * FROM (SELECT a.pengajuan_no, CASE WHEN a.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 'General' AS type_pengajuan, a.request_date AS pengajuan_system, a.pengajuan_email_date AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpileId) AS stockpile, (SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT vendor_name FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) AS vendor,
(SELECT gvb.bank_name FROM general_vendor_bank gvb LEFT JOIN pengajuan_general_detail pgd ON gvb.gv_bank_id = pgd.gv_bank_id WHERE pgd.pg_id = a.pengajuan_general_id LIMIT 1) AS bank, 
a.remarks AS keterangan, (SELECT SUM(amount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS dpp,
(SELECT SUM(ppn_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS ppn,
(SELECT SUM(pph_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS pph,
(SELECT SUM(tamount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS totalAmount
FROM pengajuan_general a WHERE a.status_pengajuan != 2 AND a.status_pengajuan != 5 AND (SELECT currency_id FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) != 1 {$whereProperty1}
UNION ALL
SELECT CONCAT('PP/',a.idPP) AS pengajuan_no, CASE WHEN a.urgent_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, (CASE WHEN a.payment_for = 1 THEN 'Curah' WHEN a.payment_for = 2 THEN 'Freight Cost/OA' WHEN a.payment_for = 3 THEN 'Unloading/OB' WHEN a.payment_for = 9 THEN 'Handling Cost' ELSE '' END) AS type_pengajuan ,
 a.entry_date AS pengajuan_system, a.email_date AS pengajuan_email,
a.urgent_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile_id) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.user) AS pic_pengajuan,
(CASE WHEN a.vendor_id IS NOT NULL THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
WHEN a.freight_id IS NOT NULL THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freight_id)
WHEN a.vendor_handling_id IS NOT NULL THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendor_handling_id)
WHEN a.labor_id IS NOT NULL THEN (SELECT labor_name FROM labor WHERE labor_id = a.labor_id) ELSE '' END) AS vendor, a.bank,a.remarks AS keterangan, CASE WHEN a.vendor_id IS NOT NULL THEN (a.qty * a.price) 
WHEN a.labor_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount)
WHEN a.vendor_handling_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount) ELSE a.dpp END AS dpp,
 a.ppn_amount, a.pph_amount, a.amount AS totalAmount
FROM pengajuan_payment a WHERE a.currency_id != 1 AND a.dp_status != 2 AND a.dp_status != 5 {$whereProperty2}
UNION ALL
SELECT CONCAT('PI/',a.pengajuan_interalTF_id) AS pengajuan_no, CASE WHEN a.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END `status`, 'Internal Transfer' AS type_pengajuan, a.entry_date AS pengajuan_system, '' AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS vendor, (SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS bank,a.remarks AS keterangan, a.amount AS dpp, '0' AS ppn, '0' AS pph, a.amount AS totalAmount 
FROM pengajuan_internaltf a WHERE a.amount = 0 AND a.status != 2 {$whereProperty3}) a
WHERE 1=1 {$whereProperty5}
ORDER BY a.type_pengajuan ASC, a.pengajuan_no ASC";
$resulta = $myDatabase->query($sqla, MYSQLI_STORE_RESULT);

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Tipe Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", "Input Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", "Email Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", "Request Payment", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", "Stockpile", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", "PIC Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", "Vendor", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", "Bank Vendor", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", "Keterangan Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", "DPP(USD)", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", "PPN(USD)", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", "PPh(USD)", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", "Total Pembayaran(USD)", PHPExcel_Cell_DataType::TYPE_STRING);



$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while ($rowa = $resulta->fetch_object()) {
	
				$dppTotal1 = $dppTotal1 + $rowa->dpp;
				$ppnTotal1 = $ppnTotal1 + $rowa->ppn;
				$pphTotal1 = $pphTotal1 + $rowa->pph;
				$totalAmount1 = $totalAmount1 + $rowa->totalAmount;

    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $rowa->pengajuan_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $rowa->status, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowa->type_pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowa->pengajuan_system);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowa->pengajuan_email);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowa->request_payment);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowa->stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowa->pic_pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $rowa->vendor);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowa->bank);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $rowa->keterangan);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $rowa->dpp);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $rowa->ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $rowa->pph);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $rowa->totalAmount);
    

    $no++;
	
}

$bodyRowEnd = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Grand Total");
			$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $dppTotal1);
			$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $ppnTotal1);
			$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $pphTotal1);
			$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalAmount1);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:P{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }
		
$rowActive++;

$sql = "SELECT * FROM (SELECT a.pengajuan_no, CASE WHEN a.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 'General' AS type_pengajuan, a.request_date AS pengajuan_system, a.pengajuan_email_date AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpileId) AS stockpile, (SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT vendor_name FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) AS vendor,
(SELECT gvb.bank_name FROM general_vendor_bank gvb LEFT JOIN pengajuan_general_detail pgd ON gvb.gv_bank_id = pgd.gv_bank_id WHERE pgd.pg_id = a.pengajuan_general_id LIMIT 1) AS bank, 
a.remarks AS keterangan, (SELECT SUM(amount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS dpp,
(SELECT SUM(ppn_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS ppn,
(SELECT SUM(pph_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS pph,
(SELECT SUM(tamount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS totalAmount
FROM pengajuan_general a WHERE a.status_pengajuan != 2 AND a.status_pengajuan != 5 AND (SELECT currency_id FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) = 1 {$whereProperty1}
UNION ALL
SELECT CONCAT('PP/',a.idPP) AS pengajuan_no, CASE WHEN a.urgent_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 
(CASE WHEN a.payment_for = 1 THEN 'Curah' WHEN a.payment_for = 2 THEN 'Freight Cost/OA' WHEN a.payment_for = 3 THEN 'Unloading/OB' WHEN a.payment_for = 9 THEN 'Handling Cost' ELSE '' END) AS type_pengajuan ,
 a.entry_date AS pengajuan_system, a.email_date AS pengajuan_email,
a.urgent_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile_id) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.user) AS pic_pengajuan,
(CASE WHEN a.vendor_id IS NOT NULL THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
WHEN a.freight_id IS NOT NULL THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freight_id)
WHEN a.vendor_handling_id IS NOT NULL THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendor_handling_id)
WHEN a.labor_id IS NOT NULL THEN (SELECT labor_name FROM labor WHERE labor_id = a.labor_id) ELSE '' END) AS vendor, a.bank,a.remarks AS keterangan, 
CASE WHEN a.vendor_id IS NOT NULL THEN (a.qty * a.price) 
WHEN a.labor_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount)
WHEN a.vendor_handling_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount) ELSE a.dpp END AS dpp,
 a.ppn_amount, a.pph_amount, a.amount AS totalAmount
FROM pengajuan_payment a WHERE a.currency_id = 1 AND a.dp_status != 2 AND a.dp_status != 5 {$whereProperty2}
UNION ALL
SELECT CONCAT('PI/',a.pengajuan_interalTF_id) AS pengajuan_no, CASE WHEN a.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END `status`, 'Internal Transfer' AS type_pengajuan, a.entry_date AS pengajuan_system, '' AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS vendor, (SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS bank,a.remarks AS keterangan, a.amount AS dpp, '0' AS ppn, '0' AS pph, a.amount AS totalAmount 
FROM pengajuan_internaltf a WHERE a.status != 2 {$whereProperty3}
UNION ALL
SELECT CONCAT('PK/',a.purchasing_id) AS pengajuan_no, CASE WHEN a.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END `status`, 'PKS Kontrak' AS type_pengajuan , a.entry_date AS pengajuan_system, '' AS pengajuan_email,
a.plan_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile_id) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(CASE WHEN a.vendor_id = 0 THEN a.tempVendor ELSE (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id) END) AS vendor,
(SELECT vb.bank_name FROM vendor_bank vb WHERE vendor_id = a.vendor_id LIMIT 1) AS bank, 
(SELECT CASE WHEN ppn = 0 THEN 'NON PKP' ELSE '' END FROM vendor WHERE vendor_id = a.vendor_id) AS keterangan, 
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN ((a.quantity * a.price)/1.11) 
 WHEN a.ppn = 2 THEN (a.quantity * a.price) ELSE ((a.quantity * a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id))END ) AS dpp,
(CASE WHEN a.vendor_id = 0 AND a.ppn = 1 THEN ((a.quantity * a.price) - ((a.quantity * a.price)/1.11))
WHEN a.vendor_id = 0 AND a.ppn = 2 THEN (((a.quantity * a.price)*1.11) - (a.quantity * a.price)) 
WHEN a.ppn = 2 THEN COALESCE((a.quantity * a.price) * (SELECT ppn/100 FROM vendor WHERE vendor_id = a.vendor_id),0) ELSE COALESCE((a.quantity * a.price) - 
((a.quantity * a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id)),0) END) AS ppn, '0' AS ppn,
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN (a.quantity * a.price)
WHEN a.ppn = 2 AND a.vendor_id = 0 THEN ((a.quantity * a.price) + (((a.quantity * a.price)*1.11) - (a.quantity * a.price)) )
WHEN a.ppn = 2 THEN (a.quantity * a.price) + ((a.quantity * a.price) * COALESCE((SELECT ppn/100 FROM vendor WHERE vendor_id = a.vendor_id),0)) ELSE (a.quantity * a.price) END) AS totalAmount
FROM purchasing a WHERE a.`type` = 1 AND a.price > 0 AND a.status != 1 AND a.logbook_status = 0 {$whereProperty4}) a
WHERE 1=1 {$whereProperty5}
ORDER BY a.type_pengajuan ASC, a.pengajuan_no ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Tipe Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", "Input Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", "Email Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", "Request Payment", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", "Stockpile", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", "PIC Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", "Vendor", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", "Bank Vendor", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", "Keterangan Pengajuan", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", "DPP(IDR)", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", "PPN(IDR)", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", "PPh(IDR)", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", "Total Pembayaran(IDR)", PHPExcel_Cell_DataType::TYPE_STRING);



$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while ($row = $result->fetch_object()) {
	
				$dppTotal = $dppTotal + $row->dpp;
				$ppnTotal = $ppnTotal + $row->ppn;
				$pphTotal = $pphTotal + $row->pph;
				$totalAmount = $totalAmount + $row->totalAmount;

    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->pengajuan_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->status, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->type_pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->pengajuan_system);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->pengajuan_email);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->request_payment);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->pic_pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->vendor);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->bank);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->keterangan);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->dpp);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->pph);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->totalAmount);
    

    $no++;

}
$bodyRowEnd = $rowActive;
if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:L{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Grand Total");
			$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $dppTotal);
			$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $ppnTotal);
			$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $pphTotal);
			$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalAmount);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("M{$rowActive}:P{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    

}
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AG)")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    // $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for QTY PKS & INVOICE VALUE
$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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