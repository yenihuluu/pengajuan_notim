<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once './assets/include/path_variable.php';

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
// </editor-fold>
$checks = $_POST['checks'];

//if (!isset($checked)){
//    echo '|FAIL|Ceklis Yang Mau Di Export';
//    die();
//}

/*for ($i = 0; $i < sizeof($checks); $i++) {
    $checked = $checks[$i];

    if ($selectedCheck == '') {
        $selectedCheck .= $checked;
    } else {
        $selectedCheck .= ', ' . $checked;
    }
}
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$whereAvailableProperty = '';
$sql = "UPDATE pengajuan_general SET pengajuan_email_date = {STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s')} WHERE pengajuan_general_id IN ($selectedCheck) AND pengajuan_email_date is NULL ";
$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
*/
// </editor-fold>

$fileName = "purchasing_incomplete" . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "K";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Incomplete Purchasing Data");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Number");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract Type");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "PPN");
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty (MT)");
//$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Harga / Qty");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Freight");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Entry Date");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Payment Type");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Request payment date");
//$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Remarks PO");


$sql = "SELECT CONCAT(v.`vendor_code`, ' - ', v.vendor_name) AS vendor_name,
		case when p.contract_type = 1 then 'PKS-Contract'
		when p.contract_type = 2 then 'PKS-SPB'
		when p.contract_type = 3 then 'PKHOA' end as contract_type2,
		s.`stockpile_name`,p.*,DATE_FORMAT(p.entry_date, '%d %b %Y %H:%i:%s') AS entry_date,
		CASE WHEN p.ppn = 1 THEN 'INCLUDE' ELSE 'EXCLUDE' END AS ppn,
		CASE WHEN p.freight = 1 THEN 'INCLUDE' ELSE 'EXCLUDE' END freight,
		DATE_FORMAT(p.admin_input, '%d %b %Y %H:%i:%s') AS admin_input,pp.contract_no,
		coalesce(pp.final_status,1) as final_status,
		case when import2 is not null and pp.final_status <> 1 then 'Need Approve'
		when import2 is null and pp.final_status <> 1 then 'Follow UP'
		else 'OK' end as approve,
		        DATE_FORMAT(p.plan_payment_date, '%d %b %Y') AS reqPaymentDate
		FROM purchasing p
		LEFT JOIN stockpile s ON s.`stockpile_id`=p.`stockpile_id`
		LEFT JOIN vendor v ON v.`vendor_id`=p.`vendor_id`
		LEFT JOIN po_pks pp on pp.purchasing_id=p.purchasing_id
		WHERE pp.final_status <> 1 AND p.import2 IS NULL";
echo $sql;

$resultContent = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {

    /*$branch = $rowContent->branch;
    $bank = $rowContent->bank_name;
    $nama_akun_bank = $rowContent->beneficiary;
    $termin = $rowContent->termin;

    $qty = $rowContent->qty;
    $dpp = $rowContent->amount * $termin / 100;
    $ppn = $rowContent->ppn * $termin / 100;
    $pph = $rowContent->pph * $termin / 100;
//    $remarksPO = $rowContent->notes;
//    $hargaQty = $rowContent->price;
    $total = $dpp + $ppn - $pph;

    if ($rowContent->payment_type == 1) {
        $paymentType = 'Urgent';
    } else {
        $paymentType = 'Normal';
    }
	

    $reqPaymentDate = $rowContent->request_payment_date;*/
	if($rowContent->payment_type == 0){
		$type = 'Normal';
	}else if($rowContent->payment_type == 1){
		$type = 'Urgent';
	}

    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->purchasing_id);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowContent->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->contract_type2);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowContent->vendor_name);
	 $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}",  number_format($rowContent->price, 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}",  number_format($rowContent->quantity, 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
    
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $rowContent->ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowContent->freight);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowContent->entry_date);
   
//    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $qty);
//    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $hargaQty);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $type);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $rowContent->reqPaymentDate);
   

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

$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Stock Transit Report">

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
