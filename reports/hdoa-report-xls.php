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
$freightId = $myDatabase->real_escape_string($_POST['freightId']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$vendorFreightId = $myDatabase->real_escape_string($_POST['vendorFreightId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$paymentFrom = $myDatabase->real_escape_string($_POST['paymentFrom']);
$paymentTo = $myDatabase->real_escape_string($_POST['paymentTo']);
//$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($freightId != '') {
    $freightId = $_POST['freightId'];
    $sql = "SELECT GROUP_CONCAT(freight_supplier) AS freight_supplier FROM freight WHERE freight_id IN ({$freightId})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    $whereProperty .= " AND fc.`freight_id` IN ({$freightId})  ";

    $freightSupplier = $row->freight_supplier;
}

if ($stockpileId != '') {
    $stockpileId = $_POST['stockpileId'];
    //$sql = "SELECT vendor_name FROM vendor WHERE vendor_id = {$vendorFreightId}";
    //$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$row = $result->fetch_object();
    
    $whereProperty .= " AND s.stockpile_name IN ('{$stockpileId}') ";
	$whereProperty4 .= " AND analytic_account IN ('{$stockpileId}') ";

    //$vendorFreight = $row->freight_supplier . " ";
}
if ($vendorFreightId != '') {
    $vendorFreightId = $_POST['vendorFreightId'];
    //$sql = "SELECT vendor_name FROM vendor WHERE vendor_id = {$vendorFreightId}";
    //$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$row = $result->fetch_object();
    
    $whereProperty .= " AND fc.`vendor_id` IN ({$vendorFreightId})";

    //$vendorFreight = $row->freight_supplier . " ";
}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
	 $whereProperty4 .= " AND date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
	$whereProperty4 .= " AND date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND t.transaction_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')  ";
	$whereProperty4 .= " AND date BETWEEN '2017-01-01' AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}

if ($paymentFrom != '' && $paymentTo != '') {
    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
	$whereProperty3 .= " AND payment_date BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = $paymentFrom . " - " . $paymentTo . " ";
} else if ($paymentFrom != '' && $paymentTo == '') {
    $whereProperty2 .= " AND p.payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y')";
	$whereProperty3 .= " AND payment_date >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
//    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
    //$periodFull = "From " . $paymentFrom . " ";
} else if ($paymentFrom == '' && $paymentTo != '') {
    $whereProperty2 .= " AND p.payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y')  ";
	$whereProperty3 .= " AND payment_date BETWEEN '2017-01-01' AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $paymentTo . " ";
}

$sql = "SELECT t.transaction_date, s.stockpile_name, t.`slip_no`, t.`vehicle_no`, f.freight_supplier, v.vendor_name, fc.contract_pkhoa,
c.po_no, c.contract_no, t.`send_weight`, t.`netto_weight`, t.`quantity`, t.`freight_price`, ftx.tax_category, f.pph,
CASE WHEN f.freight_rule = 1 THEN ROUND((t.freight_price * t.send_weight),5) ELSE ROUND((t.freight_price * t.quantity),5) END AS total_oa, 
(SELECT t2.fc_payment_id FROM jatim_inventory.`transaction` t2 LEFT JOIN jatim_inventory.payment p ON p.payment_id = t2.fc_payment_id WHERE t2.transaction_id = t.`transaction_id` AND p.payment_status = 0 AND p.payment_method = 1 {$whereProperty2}) AS fc_payment_id,
(SELECT payment_date FROM jatim_inventory.payment WHERE payment_id = t.fc_payment_id AND payment_status = 0 AND payment_method = 1 {$whereProperty3}) AS payment_date, ts.amt_claim
FROM jatim_inventory.`transaction` t
LEFT JOIN jatim_inventory.freight_cost fc ON fc.`freight_cost_id` = t.`freight_cost_id`
LEFT JOIN jatim_inventory.freight f ON f.`freight_id` = fc.`freight_id`
LEFT JOIN jatim_inventory.vendor v ON v.`vendor_id` = fc.`vendor_id`
LEFT JOIN jatim_inventory.stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN jatim_inventory.contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN jatim_inventory.stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
LEFT JOIN jatim_inventory.tax ftx ON ftx.tax_id = f.pph_tax_id
LEFT JOIN jatim_inventory.`transaction_shrink_weight` ts ON ts.transaction_id = t.transaction_id
WHERE 1=1 {$whereProperty}
AND (SELECT t2.fc_payment_id FROM jatim_inventory.`transaction` t2 LEFT JOIN jatim_inventory.payment p ON p.payment_id = t2.fc_payment_id WHERE t2.transaction_id = t.`transaction_id` {$whereProperty2}) IS NULL
AND (SELECT payment_date FROM jatim_inventory.payment WHERE payment_id = t.fc_payment_id AND payment_status = 0 AND payment_method = 1 {$whereProperty3}) IS NULL
AND t.freight_price > 0

AND t.freight_cost_id IS NOT NULL
AND t.sync_status != 11
AND t.adj_oa IS NULL
UNION ALL
SELECT DATE AS transaction_date, analytic_account AS stockpile_name, slip_number AS slip_no, '' AS vehicle_no, partner AS freight_supplier, '' AS vendor_name, '' AS contract_pkhoa,
'' AS po_no, '' AS contract_no, '' AS send_weight, '' AS netto_weight, '' AS quantity, '' AS freight_price, '' AS tax_category, '' AS pph, SUM(credit-debit) AS total_oa, '' AS fc_payment_id, '' AS payment_date, '' AS amt_claim
FROM jatim_gl.`hdoadetail`
WHERE journal_entry  IN
(
'BEN-18-0000002736',
'PAD-18-0000007375',
'PAD-18-0000007661',
'PAD-18-0000007806',
'PAD-18-0000007910',
'PAD-18-0000008434',
'PAD-18-0000008563',
'PAD-18-0000008760',
'PAD-18-0000009659',
'PAD-18-0000009660',
'PAD-18-0000009673',
'PAD-18-0000010529',
'PAD-18-0000013624',
'BEN-18-0000002722',
'172',
'173',
'360',
'BUT-18-0000007406',
'BUT-18-0000007452',
'BUT-18-0000007989',
'BUT-18-0000008172',
'BUT-18-0000009118'
) {$whereProperty4} GROUP BY journal_entry
ORDER BY transaction_date ASC ";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "Summary_OA " . $freightSupplier . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "R";

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

if ($freightSupplier != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Supplier = {$freightSupplier}");
}

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Summary OA");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "No. Slip");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "No. Mobil");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Nama Supplier");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Nama PKS");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "No. Kontrak PKHOA");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "No. PO");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "No. Kontrak");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Berat Kirim");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Berat Netto");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Inventory");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Harga OA");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Total Susut");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Total DPP");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Total Biaya Angkut");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

    if($result->num_rows > 0) {
	$no = 1;
	while($row = $result->fetch_object()) {
	
	if($row->tax_category == 1){
		$total_oa = $row->total_oa / ((100 - $row->pph)/100);
		$total_susut = $row->amt_claim / ((100 - $row->pph)/100);
		$TotalDPP = $total_oa - $total_susut;
	}else{
		$total_oa = $row->total_oa;
		$total_susut = $row->amt_claim;
		$TotalDPP = $total_oa - $total_susut;
	}
		
		$totalOA = $total_oa - ($total_oa * ($row->pph/100));
		$totalSusut = $total_susut - ($total_susut * ($row->pph/100));
		$total = $totalOA - $totalSusut;
	
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vehicle_no);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->freight_supplier);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->vendor_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->contract_pkhoa, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->send_weight);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->netto_weight);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->freight_price);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $total_oa);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalSusut);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $TotalDPP);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $total);
	
	
	$dpp = $dpp + $total;
	$dppTotal = $dppTotal + $TotalDPP;

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
		if ($bodyRowEnd > $headerRow + 1) {
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
        }
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
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":R{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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