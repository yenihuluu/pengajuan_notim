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
$generalVendorId = $myDatabase->real_escape_string($_POST['generalVendorId']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$paymentFrom = $myDatabase->real_escape_string($_POST['paymentFrom']);
$paymentTo = $myDatabase->real_escape_string($_POST['paymentTo']);
/*$vendorFreightId = $myDatabase->real_escape_string($_POST['vendorFreightId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);

//$stockpileName = 'All ';
$periodFull = '';*/

// <editor-fold defaultstate="collapsed" desc="Query">

if ($generalVendorId != '') {
    $generalVendorId = $_POST['generalVendorId'];
    //$sql = "SELECT GROUP_CONCAT(freight_supplier) AS freight_supplier FROM freight WHERE freight_id IN ({$freightId})";
   // $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$row = $result->fetch_object();
    
    $whereProperty .= " AND (CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
WHEN stockpile_contract_id IS NOT NULL THEN (SELECT v.vendor_name FROM vendor v LEFT JOIN contract c ON c.vendor_id = v.vendor_id LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN vendor_id IS NOT NULL THEN (SELECT vendor_name FROM vendor WHERE vendor_id = p.vendor_id)
WHEN freight_id IS NOT NULL THEN (SELECT freight_supplier FROM freight WHERE freight_id = p.freight_id)
WHEN vendor_handling_id IS NOT NULL THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = p.vendor_handling_id)
WHEN labor_id IS NOT NULL THEN (SELECT labor_name FROM labor WHERE labor_id = p.labor_id)
ELSE '' END) IN ({$generalVendorId})";

    //$freightSupplier = $row->freight_supplier;
}

if ($stockpileId != '') {
    $stockpileId = $_POST['stockpileId'];
    //$sql = "SELECT vendor_name FROM vendor WHERE vendor_id = {$vendorFreightId}";
    //$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$row = $result->fetch_object();
    
    $whereProperty .= " AND (CASE WHEN p.payment_location = 0 THEN 10 ELSE p.payment_location END)  IN ({$stockpileId}) ";

    //$vendorFreight = $row->freight_supplier . " ";
}
/*if ($vendorFreightId != '') {
    $vendorFreightId = $_POST['vendorFreightId'];
    //$sql = "SELECT vendor_name FROM vendor WHERE vendor_id = {$vendorFreightId}";
    //$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$row = $result->fetch_object();
    
    $whereProperty .= " AND fc.`vendor_id` IN ({$vendorFreightId})";

    //$vendorFreight = $row->freight_supplier . " ";
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
    $periodFull = "To " . $periodTo . " ";
}
*/
if ($paymentFrom != '' && $paymentTo != '') {
    $whereProperty .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
	//$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = $paymentFrom . " - " . $paymentTo . " ";
} else if ($paymentFrom != '' && $paymentTo == '') {
    $whereProperty .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y')";
	//$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = "From " . $paymentFrom . " ";
} else if ($paymentFrom == '' && $paymentTo != '') {
    $whereProperty .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
	//$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
    //$periodFull = "To " . $paymentTo . " ";
}

$sql = "SELECT p.payment_no, p.`payment_date`,
 CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
			WHEN p.vendor_id IS NOT NULL THEN 'Curah'
            WHEN p.sales_id IS NOT NULL THEN 'Sales'
            WHEN p.freight_id IS NOT NULL THEN 'Freight Cost'
            WHEN p.labor_id IS NOT NULL THEN 'Unloading Cost'
			WHEN p.vendor_handling_id IS NOT NULL THEN 'Handling Cost'
			WHEN p.invoice_id IS NOT NULL THEN 'Invoice'
            ELSE 'Internal Transfer' END AS payment_type, 
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
WHEN stockpile_contract_id IS NOT NULL THEN (SELECT v.vendor_name FROM vendor v LEFT JOIN contract c ON c.vendor_id = v.vendor_id LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN vendor_id IS NOT NULL THEN (SELECT vendor_name FROM vendor WHERE vendor_id = p.vendor_id)
WHEN freight_id IS NOT NULL THEN (SELECT freight_supplier FROM freight WHERE freight_id = p.freight_id)
WHEN vendor_handling_id IS NOT NULL THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = p.vendor_handling_id)
WHEN labor_id IS NOT NULL THEN (SELECT labor_name FROM labor WHERE labor_id = p.labor_id)
ELSE '' END AS vendor_name, p.amount_converted,
CASE WHEN p.payment_location = 0 THEN 'Jakarta' ELSE s.`stockpile_name` END AS stockpile_name,
CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE s.`stockpile_code` END AS stockpile_code,
p.remarks, p.`payment_location`, u.user_name, p.entry_date, b.`bank_code`, cur.`currency_code`, b.`bank_type`
FROM payment p
LEFT JOIN `user` u ON u.`user_id` = p.`entry_by`
LEFT JOIN stockpile s ON s.`stockpile_id` = p.`payment_location`
LEFT JOIN bank b ON b.`bank_id` = p.`bank_id`
LEFT JOIN currency cur ON cur.`currency_id` = p.`currency_id`
WHERE p.`payment_method` = 2 AND p.`payment_status` = 0 AND p.`amount_converted` > 0 AND p.`payment_date` > '2017-12-31' AND p.`payment_cash_id` IS NULL {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Payment_DP " . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "J";

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
/*
if ($freightSupplier != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Supplier = {$freightSupplier}");
}

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}*/


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Payment DP Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Payment_type");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Payment No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Entry By");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Entry Date");



$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	
	if($row->payment_no != '') {
                    $voucherCode = $row->stockpile_code .'/'. $row->bank_code .'/'. $row->currency_code;

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
                    
                    $paymentNo =  $voucherCode .' # '. $row->payment_no; 
                } else {
                    $paymentNo = '';
                }
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->payment_type);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $paymentNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->payment_date);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->stockpile_name, PHPExcel_Cell_DataType::TYPE_STRING);
	//$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->invoice_no2);
	//$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vehicle_no);
	//$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->freight_supplier);
	//$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->remarks, PHPExcel_Cell_DataType::TYPE_STRING);
	//$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->amount_converted);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->user_name);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->entry_date);
	
	
	
	//$dpp = $dpp + $total;
	//$dppTotal = $dppTotal + $TotalDPP;

    $no++;
	}
}
$bodyRowEnd = $rowActive;


/*		$sqlPph = "SELECT tx.tax_category, f.pph FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id WHERE freight_id = {$freightId}";
                $resultPph = $myDatabase->query($sqlPph, MYSQLI_STORE_RESULT);   
                if($resultPph !== false && $resultPph->num_rows > 0) {
                    while($rowPph = $resultPph->fetch_object()) {
							if(tax_category == 1){
								$pph = $dpp * ($rowPph->pph/100);
							}elseif(tax_category == 0){
								$pph = $dpp * ($rowPph->pph/100);
							}
					}
				}*/
	
		//$grandTotal = $dpp - $pph;
		/*
        if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:N{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Sub Total");
			$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $dpp);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("O{$rowActive}:O{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }
		if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:N{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PPh");
			$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $pph);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("O{$rowActive}:O{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }*/
		/*if ($bodyRowEnd > $headerRow + 1) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:O{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Grand Total");
			$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $dppTotal);
			$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $dpp);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("Q{$rowActive}:R{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }*/
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
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
   
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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