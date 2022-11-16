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

$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);

$periodFull = '';



if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	$periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$periodFull = "To " . $periodTo . " ";
}

$sql = "SELECT * FROM (
SELECT sh.`shipment_id`, sh.shipment_date, sh.shipment_no, (t.`quantity` - IFNULL((SELECT SUM(quantity) FROM contract WHERE return_shipment_id = sh.shipment_id),0)) AS quantity,
t.vehicle_no,
(SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTR(t.slip_no,1,3)) AS stockpile,

CASE WHEN sl.localSales = 1 THEN (IFNULL(sh.quantity * sl.price_converted,0))
ELSE 
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 120000 AND gl.transaction_id = t.`transaction_id`),0) - 
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 120000 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0)) END AS salesPrice,

IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 230100 AND gl.transaction_id = t.`transaction_id`),0) - 
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 230100 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0) AS ppn,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 160100 AND gl.transaction_id = t.`transaction_id` ),0))
ELSE 
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id` LIMIT 1 ),0) - 
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0))END AS cogsPrice,

CASE WHEN SUBSTR(shipment_no,-2,2) = '-S' THEN IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510201 AND gl.transaction_id = t.`transaction_id`),0)
WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510101 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510101 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0) END AS cogsPKS,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510102 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510102 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0))END  AS cogsOA,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510103 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510103 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0)) END AS cogsOB,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510104 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510104 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0)) END AS cogsHandling
FROM shipment sh
LEFT JOIN `transaction` t ON t.`shipment_id` = sh.`shipment_id`
LEFT JOIN adjustment_audit adj ON sh.shipment_id = adj.shipment_id 
LEFT JOIN sales sl ON sl.sales_id = sh.sales_id
WHERE t.transaction_type = 2 {$whereProperty} AND t.notim_status = 0 AND t.slip_retur IS NULL 
AND sh.shipment_no NOT LIKE '%LANGSIR%'
UNION ALL
SELECT sh.shipment_id, aa.adjustment_date AS shipment_date, 'Adjustment Audit' AS shipment_no, aa.quantity, aa.notes AS vehicle_no,
(SELECT stockpile_name FROM stockpile WHERE stockpile_id = aa.stockpile_id) AS stockpile, '0' AS salesPrice,
'0' AS ppn, ((aa.quantity * aa.cogs_pks * -1) +  (aa.quantity * aa.cogs_oa * -1) + (aa.quantity * aa.cogs_ob * -1) + (aa.quantity * aa.cogs_handling * -1)) AS cogsPrice,
(aa.quantity * aa.cogs_pks * -1) AS cogsPKS,(aa.quantity * aa.cogs_oa * -1) AS cogsOA,(aa.quantity * aa.cogs_ob * -1) AS cogsOB,(aa.quantity * aa.cogs_handling * -1) AS cogsHandling
FROM adjustment_audit aa
LEFT JOIN shipment sh ON sh.shipment_id = aa.shipment_id
WHERE aa.adjustment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
) a
ORDER BY a.shipment_date ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "COGS Report Period " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "L";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "COGS Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Shipment Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Sales Price");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "COGS Price (Total)");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "COGS (PKS)");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "COGS (OA)");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "COGS (OB)");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "COGS (Handling)");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Vessel Name");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

$no = 1;
while($row = $result->fetch_object()) {
   			
	$salesPrice = $row->salesPrice - $row->ppn; 		
			 
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->shipment_date);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->shipment_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->stockpile);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $salesPrice);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->cogsPrice);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->cogsPKS);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->cogsOA);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->cogsOB);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->cogsHandling);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->vehicle_no);
	
   
    $no++;
	
				$qty = $qty + $row->quantity;
				$sp = $sp + $salesPrice;
				$cogsPrice = $cogsPrice + $row->cogsPrice;
				$cogsPKS = $cogsPKS + $row->cogsPKS;
				$cogsOA = $cogsOA + $row->cogsOA;
				$cogsOB = $cogsOB + $row->cogsOB;
				$cogsHandling = $cogsHandling + $row->cogsHandling;
}
$bodyRowEnd = $rowActive;

if ($bodyRowEnd > $headerRow) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:D{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "GRAND TOTAL");
			$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $qty);
			$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $sp);
			$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $cogsPrice);
			$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $cogsPKS);
			$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $cogsOA);
			$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $cogsOB);
			$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $cogsHandling);
			$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
			

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}:L{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }

//        
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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