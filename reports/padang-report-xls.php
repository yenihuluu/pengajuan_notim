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

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'DOWLNOAD NOTA TIMBANG XLS',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


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
$styleArray9 = array(
    'font' => array(
        'bold' => true,
		'color' => array('rgb' => 'FF0000')
    )
);
// </editor-fold>

$whereProperty = '';
$sumProperty = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$stockpileIds = $_POST['stockpileIds'];
//$vendorIds = $_POST['vendorIds'];
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($stockpileIds !== '') {
   // $stockpileId = $_POST['stockpileId'];
    $stockpile_name = array();
	$stockpile_code = array();
	$stockpileNames = '';
	$stockpileCodes = '';
    $sql = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
		$stockpile_code[] = $row['stockpile_code'];

	/*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                        if($stockpile_names == '') {
                            $stockpile_names .= "'". $stockpile_name[$i] ."'";
                        } else {
                            $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                        }
                    }*/

	$stockpileNames =  "'" . implode("','", $stockpile_name) . "'";
	$stockpileCodes =  "'" . implode("','", $stockpile_code) . "'";

	}
}

    $whereProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpileCodes}) ";
    $sumProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpileCodes}) ";

//    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
//    $sumProperty .= " AND t.slip_no like '{$stockpileId}%' ";

//    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
//    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    $rowStockpile = $resultStockpile->fetch_object();
    //$stockpileName = $row->stockpile_name . " ";
}
if($vendorIds !== '') {

   // $whereProperty2 .= $_POST['vendorIds'];

}

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $periodFull = "To " . $periodTo . " ";
}

$sql = "SELECT t.`transaction_date`, t.`vehicle_no`, v.`vendor_name`, vh.`vehicle_name`, t.`quantity`, t.`freight_price`, t.freight_quantity, f.freight_rule, t.freight_cost_id,
ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
	WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
	WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
	WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
	ELSE 0 END,10) AS qtyClaim,
t.`unloading_price`, ts.`trx_shrink_claim` AS shrink_claim, ftx.tax_id AS fc_pph_id, ftx.tax_value AS fc_pph, ftx.tax_category AS fc_pph_category, fppn.tax_value AS fc_ppn, fppn.tax_id AS fc_ppn_id, uc.ob_padang
FROM TRANSACTION t
LEFT JOIN transaction_shrink_weight ts ON t.transaction_id = ts.transaction_id
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor v ON v.`vendor_id` = c.`vendor_id`
LEFT JOIN unloading_cost uc ON uc.`unloading_cost_id` = t.`unloading_cost_id`
LEFT JOIN vehicle vh ON vh.`vehicle_id` = uc.`vehicle_id`
LEFT JOIN freight_cost fc ON fc.freight_cost_id = t.freight_cost_id
LEFT JOIN freight f ON f.freight_id = fc.freight_id
LEFT JOIN tax ftx ON ftx.tax_id = f.pph_tax_id
LEFT JOIN tax fppn ON fppn.tax_id = f.ppn_tax_id
WHERE t.`transaction_type` = 1 AND t.fc_payment_id IS NULL AND t.freight_price > 0
        {$whereProperty} ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "RekapOA" . $stockpileCodes . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "N";

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

if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileNames}");
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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "REKAP ONGKOS ANGKUT");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "No. Pol");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Kendaraan");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Nama PKS");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Biaya Angkut (/Kg)");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Biaya Angkut");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Biaya Bongkar");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Klaim Susut");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Total Biaya Angkut");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "TOTAL");


$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">


            $no = 1;
            while($row = $result->fetch_object()) {
                
				//$pks_price = $row->pks_price;

				
					$quantity = $row->quantity;
				


				/*if($row->freight_rule == 1){
					$fp = $row->freight_quantity * $row->freight_price;
				}else{
					$fp = $row->freight_quantity * $row->freight_price;
				}*/

				

				if($row->freight_cost_id != 0 && $row->fc_pph_id != 0 && $row->fc_pph_category == 1){
					$fc = $row->freight_price / ((100 - $row->fc_pph) / 100);
					//$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim) / ((100 - $row->fc_pph) / 100);
					$fcTotal = ($fc * $row->freight_quantity);
					$uc = $row->ob_padang;
					$fc_total = ($fcTotal - $uc - $fc_shrink);
					$pph = $fc_total * ($row->fc_pph / 100);
					$ppn = $fc_total * ($row->fc_ppn / 100);
					$total = $fc_total + $ppn - $pph;
				}elseif($row->freight_cost_id != 0){
					$fc = $row->freight_price;
					$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
					$fcTotal = ($fc * $row->freight_quantity);
					$uc = $row->ob_padang;
					$fc_total = $fcTotal - $uc - $fc_shrink;
					$pph = $fc_total * ($row->fc_pph / 100);
					$ppn = $fc_total * ($row->fc_ppn / 100);
					$total = $fc_total + $ppn - $pph;
				}else{
					$fc = 0;
					$fc_shrink = 0;
					$fcTotal = 0;
					$ppn = 0;
					$uc = 0;
					$fc_total = 0;
					$pph = 0;
					$ppn = 0;
					$total = 0;
				}
    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date);
    $objPHPExcel->getActiveSheet()->getCell("C{$rowActive}")->setValueExplicit($row->vehicle_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->vehicle_name);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $fc);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $fcTotal);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $uc);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $fc_shrink);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $fc_total);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $pph);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $total);
	
				$tquantity = $tquantity + $quantity;
				$tfp = $tfp + $fcTotal;
				$tuc = $tuc + $uc;
				$tfc_shrink = $tfc_shrink + $fc_shrink;
				$tfc_total = $tfc_total + $fc_total;
				$tppn = $tppn + $ppn;
				$tpph = $tpph + $pph;
				$ttotal = $ttotal + $total;
    $no++;
}
$bodyRowEnd = $rowActive;

if ($bodyRowEnd > $headerRow) {
            $rowActive++;

            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:E{$rowActive}");
            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "GRAND TOTAL");
			$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $tquantity);
			$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "");
			$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $tfp);
			$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $tuc);
			$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $tfc_shrink);
			$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $tfc_total);
			$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $tppn);
			$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $tpph);
			$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $ttotal);

            // Set number format for Amount 
            $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:N{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // Set border for table
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Set row TOTAL to bold
            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
        }

//        if ($bodyRowEnd > $headerRow + 1) {
//            $rowActive++;
//
//            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:K{$rowActive}");
//            $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "T O T A L");
//            $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "=SUM(L" . ($headerRow + 1) . ":L{$bodyRowEnd})");
//            $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "=SUM(M" . ($headerRow + 1) . ":M{$bodyRowEnd})");
//
//            // Set number format for Amount
//            $objPHPExcel->getActiveSheet()->getStyle("L{$rowActive}:M{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//
//
//            // Set border for table
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//            // Set row TOTAL to bold
//            $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->getFont()->setBold(true);
//        }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("AF"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
   
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
