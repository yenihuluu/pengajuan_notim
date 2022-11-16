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


$whereAvailableProperty = '';

$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$periodFull = '';


if ($periodTo != '') {

    $whereAvailableProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereAvailableProperty2 .= " AND d.`delivery_date` > STR_TO_DATE('{$periodTo}', '%d/%m/%Y') AND t2.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

    $periodFull = "To " . $periodTo . " ";
}

// </editor-fold>

$fileName = "End Stock Report RSB_GGL " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "M";

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
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "End Stock Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stokpiles");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date Slip No.");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Available Inventory");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "PKS Amount");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "PKS Amount (/kg)");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Freight Cost");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Freight Cost (/kg)");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Unloading Cost");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Unloading Cost (/kg)");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Handling Cost");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Handling Cost (/kg)");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Total Amount (/kg)");


$sqlContent = "SELECT (SELECT DATE_FORMAT(unloading_date, '%d %b %y') FROM `transaction` WHERE delivery_status <> 1 AND  SUBSTRING(slip_no,1,3) = a.stockpile_code AND transaction_type = 1 ORDER BY slip_no ASC LIMIT 1) AS start_date, stockpile_code, stockpile, qty_available, SUM(pks_amount) AS pks_amount, SUM(freight_amount) AS freight_amount, SUM(unloading_amount) AS unloading_amount, SUM(handling_amount) AS handling_amount FROM (
SELECT '' AS start_date, SUBSTRING(t.slip_no,1,3) AS stockpile_code,
(SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTRING(t.slip_no,1,3)) AS stockpile,
ROUND(SUM(CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.`quantity` END) -
	SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2) AS qty_available,
ROUND((SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 THEN t.quantity * t.unit_price
	WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.stock_transit_id IS NOT NULL AND t.stock_transit_id != 0
                THEN t.quantity * t.`unit_cost`
	WHEN t.transaction_type = 1 AND t.delivery_status <> 1
	THEN t.quantity * (SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = t.stockpile_contract_id) ELSE 0 END) -
	SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 
        THEN (SELECT SUM(quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.unit_price 
	WHEN t.transaction_type = 1 AND t.delivery_status = 2  AND t.stock_transit_id IS NOT NULL AND t.stock_transit_id != 0 
                THEN (SELECT SUM(quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) *
                t.`unit_cost`
	WHEN t.transaction_type = 1 AND t.delivery_status = 2
	THEN (SELECT SUM(quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * 
	(SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = t.stockpile_contract_id) ELSE 0 END)),2) AS pks_amount,
ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 THEN t.freight_quantity * t.freight_price
	WHEN t.transaction_type = 1 AND t.delivery_status <> 1 
	THEN (CASE WHEN (fc.freight_id = 296 OR fc.freight_id = 309) THEN t.send_weight ELSE t.freight_quantity END) * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN t.freight_price / ((100 - f.pph)/100)
	ELSE t.freight_price END
	FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
	LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t.`freight_cost_id`)
	ELSE 0 END) -
	SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
                THEN (SELECT  SUM((percent_taken / 100) * t.freight_quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.freight_price 
	WHEN t.transaction_type = 1 AND t.delivery_status = 2
	THEN (SELECT SUM((percent_taken / 100) * t.freight_quantity) * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN t.freight_price / ((100 - f.pph)/100)
	ELSE t.freight_price END
	FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
	LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t.`freight_cost_id`)
	FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) ELSE 0 END) ,2) -
ROUND(SUM(COALESCE(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 
	THEN (SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT SUM(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = t.`transaction_id`) / ((100 - f.pph)/100)
	ELSE (SELECT SUM(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = t.`transaction_id`) END
	FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
	LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t.`freight_cost_id`)
	ELSE 0 END,0)) - 
	SUM(COALESCE((CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2
	THEN (SELECT SUM(percent_taken / 100) * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT SUM(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = t.`transaction_id`) / ((100 - f.pph)/100)
	ELSE (SELECT SUM(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = t.`transaction_id`) END
	FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
	LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id WHERE fc.freight_cost_id = t.`freight_cost_id`)
	FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) ELSE 0 END),0))
	,2) AS freight_amount,
ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 THEN t.unloading_price
	WHEN t.transaction_type = 1 AND t.delivery_status <> 1
	THEN t.unloading_price ELSE 0 END) -
	SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
        THEN (SELECT ((SUM(quantity))/t.quantity) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.unloading_price 
	WHEN t.transaction_type = 1 AND t.delivery_status = 2
	THEN (SELECT ((SUM(quantity))/t.quantity) * t.`unloading_price`
	FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) ELSE 0 END),2) AS unloading_amount,
ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status <> 1 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0 THEN t.handling_quantity * t.handling_price
	WHEN t.transaction_type = 1 AND t.delivery_status <> 1
	THEN t.handling_quantity * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t.`handling_cost_id`) / ((100 - vh.pph)/100)
	ELSE (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t.`handling_cost_id`)  END
	FROM vendor_handling vh LEFT JOIN tax tx ON tx.tax_id = vh.pph_tax_id
	LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id WHERE vhc.handling_cost_id = t.`handling_cost_id`)
	ELSE 0 END) -
	SUM(CASE WHEN t.transaction_type = 1 AND t.delivery_status = 2 AND t.adjustmentAudit_id IS NOT NULL AND t.adjustmentAudit_id > 0
                THEN (SELECT SUM((t.`handling_quantity` * (percent_taken/100))) FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) * t.handling_price 
	WHEN t.transaction_type = 1 AND t.delivery_status = 2
	THEN (SELECT SUM((t.`handling_quantity` * (percent_taken/100))) * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t.`handling_cost_id`) / ((100 - vh.pph)/100)
	ELSE (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t.`handling_cost_id`) END
	FROM vendor_handling vh LEFT JOIN tax tx ON tx.tax_id = vh.pph_tax_id
	LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id WHERE vhc.handling_cost_id = t.`handling_cost_id`)
	FROM delivery WHERE transaction_id = t.transaction_id LIMIT 1) ELSE 0 END),2) AS handling_amount
FROM `transaction` t 
LEFT JOIN freight_cost fc ON fc.`freight_cost_id` = t.`freight_cost_id`
WHERE 1=1 {$whereAvailableProperty} AND t.rsb_ggl = 1
GROUP BY SUBSTRING(t.slip_no,1,3)
UNION ALL
SELECT '' AS start_date, SUBSTRING(t.slip_no,1,3) AS stockpileCode, '' AS stockpile, '' AS qty_available,
SUM(CASE WHEN t2.transaction_type = 1  AND t2.stock_transit_id IS NOT NULL AND t2.stock_transit_id <> 0
               THEN d.quantity * t2.`unit_cost` ELSE ROUND(d.quantity * t2.unit_price,2) END) AS pks_amount,
ROUND(SUM((
	CASE WHEN (fc.freight_id = 296 OR fc.freight_id = 309 ) AND d.`percent_taken` = 100 THEN t2.send_weight
	WHEN (fc.freight_id = 296 OR fc.freight_id = 309 ) AND d.`percent_taken` < 100 THEN (SELECT (t2.freight_quantity*d.`quantity`) / t2.quantity FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`)
	WHEN d.`percent_taken` < 100 THEN (SELECT (t2.freight_quantity*d.`quantity`) / t2.quantity FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`) 
	ELSE t2.freight_quantity END) *  
	CASE WHEN t2.adjustmentAudit_id IS NOT NULL AND t2.adjustmentAudit_id > 0 THEN t2.freight_price
	ELSE(SELECT CASE WHEN tx.tax_category = 1 THEN t2.freight_price / ((100 - f.pph)/100)
	ELSE t2.freight_price END
	FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
	LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
	LEFT JOIN `transaction` t2 ON t2.freight_cost_id = fc.`freight_cost_id` WHERE t2.transaction_id = d.transaction_id)END) -
	COALESCE(SUM((CASE WHEN d.`percent_taken` < 100 THEN (SELECT (d.`quantity`) / t2.freight_quantity FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`) 
	ELSE 1 END) * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT sum(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id) / ((100 - f.pph)/100)
	ELSE (SELECT sum(amt_claim) FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id) END
	FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id
	LEFT JOIN freight_cost fc ON fc.freight_id = f.freight_id 
	LEFT JOIN `transaction` t2 ON t2.freight_cost_id = fc.`freight_cost_id` WHERE t2.transaction_id = d.transaction_id)),0),2) AS freight_amount,
ROUND(SUM(CASE WHEN d.`percent_taken` < 100 THEN (SELECT (d.`quantity`/t2.quantity) * t2.unloading_price FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`)
	ELSE (SELECT unloading_price FROM `transaction` WHERE transaction_id = d.`transaction_id`) END),2) AS unloadingAmount,
ROUND(SUM((CASE WHEN d.`percent_taken` < 100 THEN (SELECT (t2.handling_quantity*d.`quantity`) / t2.quantity FROM `transaction` t2 WHERE t2.transaction_id = d.`transaction_id`) 
ELSE (SELECT `handling_quantity` FROM `transaction` WHERE transaction_id = d.`transaction_id`) END) * 
	(SELECT CASE WHEN tx.tax_category = 1 THEN (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t2.`handling_cost_id`) / ((100 - vh.pph)/100)
	ELSE (SELECT price_converted FROM vendor_handling_cost WHERE handling_cost_id = t2.`handling_cost_id`) END
	FROM vendor_handling vh LEFT JOIN tax tx ON tx.tax_id = vh.pph_tax_id
	LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id
	LEFT JOIN `transaction` t2 ON t2.handling_cost_id = vhc.`handling_cost_id` WHERE t2.transaction_id = d.transaction_id)),2) AS handlingAmount		
FROM delivery d 
LEFT JOIN `transaction` t ON d.`shipment_id` = t.`shipment_id`
LEFT JOIN `transaction` t2 ON t2.`transaction_id` = d.`transaction_id`
LEFT JOIN freight_cost fc ON fc.`freight_cost_id` = t2.`freight_cost_id`  
WHERE 1=1 {$whereAvailableProperty2} AND t.`slip_retur` IS NULL AND t.`notim_status` = 0 AND t.rsb_ggl = 1 GROUP BY  SUBSTRING(t.slip_no,1,3)
) a GROUP BY stockpile_code ORDER BY stockpile ASC";
$resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
while ($rowContent = $resultContent->fetch_object()) {
    $startDate = $rowContent->start_date;
    $stockpile_code = $rowContent->stockpile_code;
    $stockpile = $rowContent->stockpile;
    $qty_available = $rowContent->qty_available;
    $pks_amount = $rowContent->pks_amount;
    $freight_amount = $rowContent->freight_amount;
    $unloading_amount = $rowContent->unloading_amount;
    $handling_amount = $rowContent->handling_amount;
    $totalPksKg = $pks_amount / ($qty_available);
    $totalFreightKg = $freight_amount / ($qty_available);
    $totalUnloadingKg = $unloading_amount / ($qty_available);
    $totalHandlingKg = $handling_amount / ($qty_available);

    $total = $pks_amount + $freight_amount + $unloading_amount + $handling_amount;
    $totalKg = $totalPksKg + $totalFreightKg + $totalUnloadingKg + $totalHandlingKg;

    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $startDate);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $qty_available);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $pks_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $totalPksKg);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $freight_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $totalFreightKg);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $unloading_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $totalUnloadingKg);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $handling_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $totalHandlingKg);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $totalKg);

    $qtyTotal = $qtyTotal + $qty_available;
    $grandTotal = $grandTotal + $total;
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $qtyTotal);
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $grandTotal);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$bodyRowEnd = $rowActive;


// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Stock Transit Report">
/*
$onSheet = 0;
$lastColumn = "L";
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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stock Transit Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Mutasi Code");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile From");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile To");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Send Weight");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Netto Stockpile (KG)");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Shrink Price (Rp/KG)");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Shrink (KG)");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Shrink Amount (Rp)");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Unit Cost (Rp/KG)");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "(Rp/KG) Mutasi Amount (Rp/KG)");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Amount");

$sql = "select st.*, CONCAT (s.stockpile_code, ' - ', s.stockpile_name) as stockpile_from, CONCAT (ss.stockpile_code, ' - ', ss.stockpile_name) as stockpile_to, c.contract_no as contract_no, c.price, mh.kode_mutasi as mutasi_code
from stock_transit st left join mutasi_header mh on st.mutasi_header_id = mh.mutasi_header_id 
left join stockpile s on mh.stockpile_from = s.stockpile_id
left join stockpile ss on mh.stockpile_to = ss.stockpile_id
left join stockpile_contract sc on sc.stockpile_contract_id = st.stockpile_contract_id
left join contract c on sc.contract_id = c.contract_id";

$sql = "select st.*, sum(st.send_weight) as total_send_weight, sum(st.netto_weight) as total_netto_weight, mh.total as total_mutasi_header, CONCAT (s.stockpile_code, ' - ', s.stockpile_name) as stockpile_from, CONCAT (ss.stockpile_code, ' - ', ss.stockpile_name) as stockpile_to, c.price, mh.kode_mutasi as destination_code,
(SELECT  DATE_FORMAT(MAX(tt.transaction_date), '%Y-%m-%d') from transaction_timbangan tt left join mutasi_header mh on tt.mutasi_header_id = mh.mutasi_header_id) as transaction_date,mh.kode_mutasi as mutasi_code
from stock_transit st
left join mutasi_header mh on st.mutasi_header_id = mh.mutasi_header_id
left join stockpile s on mh.stockpile_from = s.stockpile_id
left join stockpile ss on mh.stockpile_to = ss.stockpile_id
left join stockpile_contract sc on sc.stockpile_contract_id = st.stockpile_contract_id
left join contract c on sc.contract_id = c.contract_id
group by st.mutasi_header_id";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$totalMutasi = 0;
while ($rowContent = $result->fetch_object()) {
    $mutasiCode = $rowContent->mutasi_code;
    $stockpileFrom = $rowContent->stockpile_from;
    $stockpileTo = $rowContent->stockpile_to;
    $sendWeight = $rowContent->send_weight;
    $price = $rowContent->price;

    $unitPrice = $rowContent->price;
    $sendWeightTransit = $rowContent->send_weight;
    $nettoTransit = $rowContent->netto_weight;
    $totalNettoWeight = $rowContent->total_netto_weight;
    $totalSendWeight = $rowContent->total_send_weight;
    $totalMutasiHeader = $rowContent->total_mutasi_header;

    $amount = $rowContent->price * $totalSendWeight;
    $totalMutasi += $amount;
    $nettoStockpile = $sendWeightTransit * $totalNettoWeight / $totalSendWeight;
    $mutasiValueTon = $totalMutasiHeader / $totalNettoWeight;

    $shrink = $sendWeightTransit - $nettoStockpile;
    if ($shrink <= 0) {
        $shrink = 0;
    } else {
        $shrink;
    }
    $shrinkAmount = $shrink * bcadd($mutasiValueTon, $unitPrice, 3);
    $shrinkValueTon = bcdiv($shrinkAmount, $nettoStockpile, 3);
    $unitCost = $unitPrice + $mutasiValueTon + $shrinkValueTon;
    $inventoryValue = bcmul($nettoStockpile, $unitCost, 3);

    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $mutasiCode);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $stockpileFrom);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $stockpileTo);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $sendWeight);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $price);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $nettoStockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $shrinkValueTon);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $shrink);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $shrinkAmount);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $unitCost);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $mutasiValueTon);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $amount);
}
$grandTotal = $grandTotal + $totalMutasi;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:L{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL MUTASI");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $totalMutasi);
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:L{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:L{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "");
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:B{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "GRAND TOTAL END STOCK + MUTASI");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $grandTotal);
$bodyRowEnd = $rowActive;
// </editor-fold>
*/

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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