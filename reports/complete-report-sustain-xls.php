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
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$stockpileName = 'All ';
$periodFull = '';
$tempTest = 0;
$tempTest2 = 0;

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

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
   	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND  t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
   //	$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
   	//$whereProperty .= " AND t.notim_status = 0 AND t.transaction_type = 1 AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
   $periodFull = "To " . $periodTo . " ";
}

$sql = "SELECT t.*,
        DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
        CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
        CASE WHEN t.transaction_type = 1 THEN con.po_no ELSE sh.shipment_code END AS po_no, 
        CASE WHEN t.transaction_type = 1 THEN con.contract_no ELSE sl.sales_no END AS contract_no, 
        CASE WHEN t.transaction_type = 1 THEN vh.vehicle_name ELSE t.vehicle_no END AS vehicle_name,
        CASE WHEN t.transaction_type = 1 THEN t.vehicle_no ELSE '' END AS vehicle_no,
        CASE WHEN t.transaction_type = 1 THEN DATE_FORMAT(t.unloading_date, '%d %b %Y') ELSE DATE_FORMAT(t.transaction_date, '%d %b %Y') END AS unloading_date2,
        DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
        CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
        CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code, f.freight_id, 
        v1.vendor_name, hv.vendor_handling_id, hv.vendor_handling_name, 
        CASE WHEN t.transaction_type = 1 THEN v3.vendor_name ELSE cust.customer_name END AS supplier,
        CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
        CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2, u.user_name,
        CASE WHEN t.transaction_type = 1 THEN 
        (SELECT shi.shipment_no FROM shipment shi LEFT JOIN delivery d ON d.shipment_id = shi.shipment_id WHERE d.transaction_id = t.transaction_id LIMIT 1)
        ELSE sh.shipment_no END AS shipment_no2, l.labor_name, cpd.contract_pks_detail_id, vc.vendor_curah_name AS vendor_curah
        FROM TRANSACTION t
        LEFT JOIN transaction_additional_shrink tas ON t.transaction_id = tas.transaction_id
        LEFT JOIN transaction_shrink_weight ts
            ON t.transaction_id = ts.transaction_id
        LEFT JOIN stockpile_contract sc
        ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
        ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
        ON con.contract_id = sc.contract_id
        LEFT JOIN contract_pks_detail cpd
        ON con.contract_id = cpd.contract_id
        LEFT JOIN vendor_curah vc
        ON vc.vendor_curah_id = cpd.vendor_curah_id
        LEFT JOIN vendor v1
        ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
        ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
        ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
        ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
        ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
        ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
        ON v3.vendor_id = t.vendor_id
        LEFT JOIN shipment sh
        ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
        ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
        ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
        ON cust.customer_id = sl.customer_id
        LEFT JOIN USER u
        ON u.user_id = t.modify_by
        LEFT JOIN vendor_handling_cost vhc
        ON vhc.handling_cost_id = t.handling_cost_id
        LEFT JOIN vendor_handling hv
        ON hv.vendor_handling_id = vhc.vendor_handling_id
        LEFT JOIN labor l ON l.labor_id = t.labor_id
        WHERE 1=1
        AND t.company_id = {$_SESSION['companyId']}
        {$whereProperty}
        AND t.slip_no NOT IN (SELECT LEFT(slip_retur, 17) FROM TRANSACTION WHERE slip_retur IS NOT NULL)
	    AND t.slip_retur IS NULL -- AND t.slip_retur_id IS NOT NULL
		GROUP BY t.transaction_id ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;
// die();

//</editor-fold>

$fileName = "Nota Timbang " . $stockpileCodes . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "T";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "NOTA TIMBANG");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No. Slip");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "No. Pol");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Kendaraan");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Tanggal Muat");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Supplier Freight");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "No. Surat Jalan");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "No. PO/Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Nama PKS");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Supplier/Customer");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "No. Kontrak");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Vendor Handling");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Inventory");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Balance (Q)");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Sumber PKS");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
if($boolBalanceBefore) {
    $sql2 = "SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2
                        FROM transaction t
                        LEFT JOIN stockpile_contract sc
                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                        WHERE 1=1 {$sumProperty}";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    if($result2->num_rows > 0) {
        while($row2 = $result2->fetch_object()) {
            $balanceBefore = $balanceBefore + $row2->quantity2;
        }
        
        $rowActive++;
        $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
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
        $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "");
        $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "");
       
    }
}

    $balanceQuantity = $balanceBefore;
            $no = 1;
            while($row = $result->fetch_object()) {
                $balanceQuantity = $balanceQuantity + $row->quantity2;
				
				if($row->transaction_type == 2){
					if($row->quantity < 0){
						$quantity = $row->quantity * -1;
					}else{
						$quantity = '-' .$row->quantity;
					}
				}else{
					$quantity = $row->quantity;
				}

           
    $rowActive++;
    
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->unloading_date2);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($row->vehicle_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vehicle_name);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->loading_date2);
	$objPHPExcel->getActiveSheet()->getCell("H{$rowActive}")->setValueExplicit($row->freight_code, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->permit_no);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->supplier);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", $row->vendor_handling_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->transaction_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $balanceQuantity);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->contract_type2);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("S{$rowActive}", $row->shipment_no2, PHPExcel_Cell_DataType::TYPE_STRING);
	if($row->contract_pks_detail_id != '') {
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("T{$rowActive}", $row->vendor_curah, PHPExcel_Cell_DataType::TYPE_STRING);
    } else {
	    $objPHPExcel->getActiveSheet()->setCellValueExplicit("T{$rowActive}", $row->vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
    }

	//$objPHPExcel->getActiveSheet()->setCellValue("AQ{$rowActive}", $row->entry_date);

    $no++;
   
}
$bodyRowEnd = $rowActive;


// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("T"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

//$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//  $objPHPExcel->getActiveSheet()->getStyle("AR" . ($headerRow + 1) . ":AT{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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