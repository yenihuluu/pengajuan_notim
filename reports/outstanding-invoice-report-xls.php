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
$dateFrom = $myDatabase->real_escape_string($_POST['dateFrom']);
$dateTo = $myDatabase->real_escape_string($_POST['dateTo']);

$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($dateFrom != '' && $dateTo != '') {
    $whereProperty .= " AND i.entry_date BETWEEN STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$dateTo}', '%d/%m/%Y')  ";
    $periodFull = $dateFrom . " - " . $dateTo . " ";
} else if ($dateFrom != '' && $dateTo == '') {
    $whereProperty .= " AND i.entry_date >= STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if ($dateFrom == '' && $dateTo != '') {
    $whereProperty .= " AND i.entry_date <= STR_TO_DATE('{$dateTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $dateTo . " ";
}

$sql = "SELECT i.invoice_id, i.invoice_no, i.invoice_no2, i.remarks, DATE_FORMAT(i.invoice_date, '%d %b %Y') AS invoice_date, gv.general_vendor_name, a.account_name, sh.shipment_no, c.po_no, 			
s.stockpile_name, id.qty, id.price, DATE_FORMAT(i.entry_date, '%d %b %Y') AS entry_date, u.user_name, id.ppn_converted, id.pph_converted, id.amount_converted,
CASE WHEN id.invoice_detail_id IS NOT NULL THEN (SELECT GROUP_CONCAT(invoice_id) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id ) ELSE 0 END AS iddp, 
CASE WHEN i.invoice_id IS NOT NULL THEN (SELECT COALESCE(SUM(p.original_amount_converted), 0) FROM payment p WHERE p.invoice_id = i.invoice_id AND p.payment_method = 2)
ELSE 0 END AS dp
				FROM invoice i  
				LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id`
				LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = i.po_id
				LEFT JOIN contract c ON c.contract_id = sc.contract_id
				LEFT JOIN stockpile s ON s.stockpile_id = i.stockpileId
				LEFT JOIN account a ON a.account_id = id.account_id
				LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
				LEFT JOIN shipment sh ON sh.shipment_id = id.shipment_id
				LEFT JOIN USER u ON u.user_id = i.entry_by
				WHERE 1=1 AND i.`payment_status` = 0 AND i.invoice_status = 0 {$whereProperty} ORDER BY i.invoice_no DESC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Outstanding Invoice " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Q";
	
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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Outstanding Invoice");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Original Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Invoice Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Account");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Paid");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Balance");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Input Date");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Input By");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Remarks");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

 $no = 1;
			
            while($row = $result->fetch_object()) {
			
			if($row->ppn_converted != 0 || $row->ppn_converted != 'NULL'){
				$ppn = $row->ppn_converted;
			}else{
				$ppn = 0;
			}
			
			if($row->pph_converted != 0 || $row->pph_converted != 'NULL'){
				$pph = $row->pph_converted;
			}else{
				$pph = 0;
			}
			
			$invoice_detail_id = $row->iddp;
			$dp = $row->dp;
			$total = ($row->amount_converted + $ppn) - $pph;
			
			$sql1 = "SELECT COALESCE(SUM(id.tamount), 0) AS down_payment FROM invoice_detail id
LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id` 
WHERE id.invoice_id IN ({$invoice_detail_id}) AND id.invoice_method_detail = 2 AND id.invoice_detail_status = 1";
            $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
            
            //$downPayment = 0;
            if($result1 !== false && $result1->num_rows == 1) {
                $row1 = $result1->fetch_object();
                if($row1->down_payment != 0){
					$downPayment1 = $row1->down_payment;
				}else{
					$downPayment1 = 0;
				}
				
				if($dp != 0){
					$dp2 = $dp;
				}else{
					$dp2 = 0;
				}
				
				$downPayment = $downPayment1 + $dp2;
			}
				$balance = $total - $downPayment;
			
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->invoice_no2, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->invoice_date);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->general_vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->shipment_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->qty);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->price);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $downPayment);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $balance);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->entry_date);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->user_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("Q{$rowActive}", $row->remarks, PHPExcel_Cell_DataType::TYPE_STRING);
   
    $no++;
}
$bodyRowEnd = $rowActive;

//        
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("O" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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