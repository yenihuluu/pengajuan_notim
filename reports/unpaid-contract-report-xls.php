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

$whereProperty = '';
$whereProperty2 = '';
$dateFrom = $myDatabase->real_escape_string($_POST['dateFrom']);
$dateTo = $myDatabase->real_escape_string($_POST['dateTo']);
$paymentFrom = $myDatabase->real_escape_string($_POST['paymentFrom']);
$paymentTo = $myDatabase->real_escape_string($_POST['paymentTo']);

$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($dateFrom != '' && $dateTo != '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$dateTo}', '%d/%m/%Y')  ";
    $periodFull = $dateFrom . " - " . $dateTo . " ";
} else if ($dateFrom != '' && $dateTo == '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') >= STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if ($dateFrom == '' && $dateTo != '') {
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN '2017-01-01' AND  STR_TO_DATE('{$dateTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $dateTo . " ";
}


if ($paymentFrom != '' && $paymentTo != '') {
    $whereProperty2 .= " AND p1.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
    //$periodFull = $paymentFrom . " - " . $paymentTo . " ";
} else if ($paymentFrom != '' && $paymentTo == '') {
    $whereProperty2 .= " AND p1.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
   // $periodFull = "From " . $paymentFrom . " ";
} else if ($paymentFrom == '' && $paymentTo != '') {
    $whereProperty2 .= " AND p1.payment_date BETWEEN '2017-01-01' AND  STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
   // $periodFull = "To " . $paymentTo . " ";
}

$sql = "SELECT c.po_no, c.`contract_no`, v.`vendor_name`,  s.`stockpile_name`, c.price, c.quantity,c.notes,  (c.price * c.quantity) AS amount, DATE_FORMAT(c.entry_date, '%d %b %Y') AS entry_date, u.user_name,
CONCAT((DATEDIFF(CURRENT_DATE, c.entry_date)), ' Days') AS aging,
CASE WHEN poc.contract_id IS NOT NULL THEN pop.quantity 
ELSE c.quantity END AS qty_total,
CASE WHEN poc.contract_id IS NOT NULL THEN v.ppn
ELSE 0 END AS ppn,
IFNULL((SELECT con.quantity FROM contract con LEFT JOIN stockpile_contract sc ON sc.contract_id = con.contract_id LEFT JOIN payment p1 ON p1.stockpile_contract_id = sc.stockpile_contract_id WHERE con.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2} GROUP BY con.contract_id),0) AS qty_paid,
IFNULL((SELECT p1.original_amount FROM payment p1 LEFT JOIN stockpile_contract sc2 ON sc2.stockpile_contract_id = p1.stockpile_contract_id WHERE sc2.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY sc2.contract_id),0) AS paid,
c.entry_date,
 (SELECT con.contract_id FROM contract con LEFT JOIN stockpile_contract sc ON sc.contract_id = con.contract_id LEFT JOIN payment p1 ON p1.stockpile_contract_id = sc.stockpile_contract_id WHERE con.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY con.contract_id) AS cId,
 (SELECT p1.payment_date FROM payment p1 LEFT JOIN stockpile_contract sc2 ON sc2.stockpile_contract_id = p1.stockpile_contract_id WHERE sc2.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY sc2.contract_id) AS payment_date
FROM contract c 	
				LEFT JOIN po_contract poc ON poc.contract_id = c.contract_id
				LEFT JOIN po_pks pop ON pop.po_pks_id = poc.po_pks_id
				LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id
				LEFT JOIN stockpile s ON s.stockpile_id = sc.stockpile_id
				LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
				LEFT JOIN USER u ON u.user_id = c.entry_by
				WHERE 1=1  AND c.`contract_type` = 'P'  AND c.quantity > 0 AND c.price > 0
				{$whereProperty}
				AND (SELECT con.contract_id FROM contract con LEFT JOIN stockpile_contract sc ON sc.contract_id = con.contract_id LEFT JOIN payment p1 ON p1.stockpile_contract_id = sc.stockpile_contract_id WHERE con.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY con.contract_id) IS NULL
				AND (SELECT p1.payment_date FROM payment p1 LEFT JOIN stockpile_contract sc2 ON sc2.stockpile_contract_id = p1.stockpile_contract_id WHERE sc2.contract_id = c.contract_id AND p1.payment_status = 0 AND p1.payment_method = 1 {$whereProperty2}GROUP BY sc2.contract_id) IS NULL
				ORDER BY c.entry_date DESC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Unpaid Contract " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "S";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Unpaid Contract");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Contract No.");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", "PPN", PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Total");
//$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Qty Total");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Amount Total (inc. PPN)");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Qty Paid");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Amount Paid (inc. PPN)");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Balance Qty");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Balance Amount (inc. PPN)");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Input Date");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Input By");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Aging");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Notes");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$no = 1;
while($row = $result->fetch_object()) {
   			
			 $stockpile_name = $row->stockpile_name; 
             $po_no = $row->po_no; 
             $contract_no = $row->contract_no; 
             $vendor_name = $row->vendor_name;
             $input_date = $row->entry_date; 
             $input_by = $row->user_name; 
			 $qty = $row->quantity;
			 $price = $row->price;
			 $amount = $row->amount ;
			 $aging = $row->aging;
			 $qty_total = $row->qty_total;
			 $ppn = ($row->ppn/100) * $amount;
			 $amount_total = ($row->price * $qty) + $ppn;
			 $qty_paid = $row->qty_paid;
			 $ppn_paid = ($row->ppn/100) * $row->paid;
			 $paid = $row->paid + $ppn_paid;
			 $total = $amount + $ppn;
			 $qty_balance = $qty - $qty_paid;
			 $amount_balance = $amount_total - $paid;
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $qty);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $price);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $amount);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $ppn);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $total);
	//$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $qty_total);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $amount_total);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $qty_paid);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $paid);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $qty_balance);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $amount_balance);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $input_date);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $input_by);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("R{$rowActive}", $aging, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("S{$rowActive}", $row->notes, PHPExcel_Cell_DataType::TYPE_STRING);
   
    $no++;
}
$bodyRowEnd = $rowActive;

//        
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":P{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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