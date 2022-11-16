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
$whereProperty2 = '';
$whereProperty = '';
$sumProperty = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$invoiceId = $myDatabase->real_escape_string($_POST['invoiceId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);

//$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($invoiceId != '') {
    
    
    $whereProperty .= " AND inv.invoice_id IN ({$invoiceId})  ";

    
}


if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND DATE_FORMAT(inv.invoice_date,'%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND DATE_FORMAT(inv.invoice_date,'%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND DATE_FORMAT(inv.invoice_date,'%Y-%m-%d') BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
    $periodFull = "To " . $periodTo . " ";
}



$sql = "SELECT inv.*, id.*, DATE_FORMAT(inv.invoice_date, '%d %b %Y') AS invoice_date, DATE_FORMAT(inv.input_date, '%d %b %Y') AS input_date, DATE_FORMAT(inv.request_date, '%d %b %Y') AS request_date, u.user_name, s.stockpile_name, gv.general_vendor_name,
		CASE WHEN id.type = 4 THEN 'LOADING'
			 WHEN id.type = 5 THEN 'UMUM'
			 WHEN id.type = 6 THEN 'HO'
		ELSE '' END AS invoiceType, ur.user_name AS user_name2,
		p.payment_no, p.payment_type,DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date,
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END AS payment_location,
(SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code,
(SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
(SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,SUM(id.tamount) AS tamount
        FROM invoice inv
        LEFT JOIN invoice_detail id
	    ON id.invoice_id = inv.invoice_id
        LEFT JOIN currency cur
            ON cur.currency_id = id.currency_id
        LEFT JOIN general_vendor gv
            ON gv.general_vendor_id = id.general_vendor_id
		LEFT JOIN USER u
			ON u.user_id = inv.entry_by
		LEFT JOIN stockpile s
			ON inv.stockpileId = s.stockpile_id
		LEFT JOIN USER ur
			ON inv.sync_by = ur.user_id
		LEFT JOIN payment p ON p.invoice_id = inv.invoice_id
        WHERE 1=1 {$whereProperty}
        GROUP BY inv.invoice_id ORDER BY inv.invoice_id DESC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "InvoiceReport " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Invoice Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Invoice No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Invoice Date");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Original Invoice No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Request Date");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Input Date");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "User Input");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Payment No");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Return Invoice Date");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "User Return");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	
	$voucherCode = '';
			if($row->payment_no != '') {
                    $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;

                    if($row->bank_type == 1) {
                        $voucherCode .= ' - B';
                    } elseif($row->bank_type == 2) {
                        $voucherCode .= ' - P';
                    } elseif($row->bank_type == 3) {
                        $voucherCode .= ' - CAS';
                    }

                    if($row->bank_type != 3) {
                        if($row->payment_type == 1) {
                            $voucherCode .= 'RV';
                        } else {
                            $voucherCode .= 'PV';
                        }
                    }
                  }
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->invoice_date);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->invoice_no2, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->general_vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->tamount);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->remarks);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->request_date);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->input_date);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->user_name);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $voucherCode,'#',$row->payment_no);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->sync_date);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->user_name2);
	

	
	
	//$total = $total + $row->amount_payment;

    $no++;
	}
}
$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":J{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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