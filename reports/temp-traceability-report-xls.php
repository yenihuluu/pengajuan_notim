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
    'font' => array(
        'bold' => true
    ),
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
$shipmentId = $myDatabase->real_escape_string($_POST['shipmentId1']);
$salesId = $myDatabase->real_escape_string($_POST['salesId1']);
$shipmentDate =  $myDatabase->real_escape_string($_POST['shipmentDate']);
// $shipmentCode = $myDatabase->real_escape_string($_POST['shipmentCode']);
$stockpileCode = $myDatabase->real_escape_string($_POST['stockpileCode']);
$stockpileName = $myDatabase->real_escape_string($_POST['stockpileName']);
$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);

$rsb = $myDatabase->real_escape_string($_POST['rsb']);
$ggl = $myDatabase->real_escape_string($_POST['ggl']);
$rsb_ggl = $myDatabase->real_escape_string($_POST['rsb_ggl']);
$transactionDate2 = $myDatabase->real_escape_string($_POST['transactionDate2']);
$newTransactionDate2 = implode("-", array_reverse(explode("/", $transactionDate2)));
if($shipmentId != '') {
    
    $sql = "SELECT sh.*,s.stockpile_code, s.stockpile_name, sh.shipment_date FROM shipment sh 
            INNER JOIN sales sl ON sh.sales_id = sl.sales_id 
            INNER JOIN stockpile s ON sl.stockpile_id = s.stockpile_id  
            WHERE sh.sales_id = {$salesId}";
    $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowShipment = $resultShipment->fetch_object();
    $shipmentCode = $rowShipment->shipment_no . " ";
   // echo $sql;
}


$sql2 = "SELECT d.*,
        CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
        DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
        t.slip_no, 
        CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
        con.po_no, 
        CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
        v3.vendor_name AS supplier,
        v1.vendor_name, 
        sh.shipment_code,
        t.send_weight, t.netto_weight, d.quantity, d.qty_rsb , d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified,
        CASE WHEN t.mutasi_id IS NOT NULL THEN t.unit_cost
            WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
        ELSE con.price_converted END AS price_converted,
        CASE WHEN t.mutasi_id IS NOT NULL THEN d.quantity * t.unit_cost
            WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
        ELSE d.quantity * con.price_converted END AS cogs_amount,
        t.freight_quantity, t.freight_price, 
        CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
            ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) 
        END AS freight_total,
        CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
            WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) 
        ELSE 0 END AS freight_shrink,
        t.unloading_price, (d.percent_taken / 100) * t.unloading_price AS unloading_total,
        vhc.price AS vh_price, t.handling_quantity,
        CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
            ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) 
        END AS handling_total,
        vh1.pph_tax_id AS vh_pph_tax_id, 
        vh1.pph AS vh_pph, 
        vhtx.tax_category AS vh_pph_tax_category,
        f.ppn_tax_id AS fc_ppn_tax_id, 
        f.ppn AS fc_ppn, 
        fctxppn.tax_category AS fc_ppn_tax_category,
        t.fc_tax_id AS fc_pph_tax_id, 
        fctxpph.tax_value AS fc_pph, 
        fctxpph.tax_category AS fc_pph_tax_category,
        l.ppn_tax_id AS uc_ppn_tax_id, 
        l.ppn AS uc_ppn, 
        uctxppn.tax_category AS uc_ppn_tax_category,
        l.pph_tax_id AS uc_pph_tax_id, 
        l.pph AS uc_pph, 
        uctxpph.tax_category AS uc_pph_tax_category,
        l.labor_id,
        t.freight_cost_id, 
        f.freight_id,
        (SELECT slip_no FROM temp_TRANSACTION WHERE delivery_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
        (SELECT transaction_date FROM temp_TRANSACTION WHERE delivery_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS transactionDate,
        (SELECT SUBSTRING(slip_no,1,3) FROM temp_TRANSACTION WHERE delivery_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
        (SELECT vehicle_no FROM temp_TRANSACTION WHERE delivery_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name
        FROM temp_delivery d
        LEFT JOIN `transaction` t
            ON t.transaction_id = d.transaction_id
        LEFT JOIN stockpile_contract sc
        ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
        ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
        ON con.contract_id = sc.contract_id
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
        ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
        ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
        ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
        ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
        ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
        ON fctxppn.tax_id = f.ppn_tax_id
        LEFT JOIN labor l
        ON l.labor_id = t.labor_id
        LEFT JOIN tax uctxpph
        ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
        ON uctxppn.tax_id = l.ppn_tax_id	
        LEFT JOIN vendor_handling_cost vhc
        ON vhc.handling_cost_id = t.handling_cost_id
        LEFT JOIN vendor_handling vh1
        ON vh1.vendor_handling_id = vhc.vendor_handling_id
        LEFT JOIN tax vhtx
        ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']} AND sh.shipment_id =  {$shipmentId}
        AND d.status = 0 ORDER BY d.`temp_transaction_id` ASC";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
//echo $sql2;

// </editor-fold>

$fileName = $stockpileCode . " - PKS Traceability Report -" . $shipmentCode . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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

if ($shipmentCode != "") {
   
	$rowActive++;
    //RSB
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Stockpile");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$stockpileName);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
  //GGL
   $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
   $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$stockpileName);

   //RSB&GGL
   $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
   $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$stockpileName);

   //UNCERTIFIED
   $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
   $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$stockpileName);

   //TOTAL
   $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
   $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$stockpileName);

$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Certification");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}","RSB");
    //$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}","GGL");
	
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}","RSB + GGL");
	
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}","Uncertified");
	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}","TOTAL");

$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Shipment Code");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$shipmentCode);
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
    //GGL
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$shipmentCode);
    
    //RSB + GGL 	
    $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$shipmentCode);
    
    //Uncertified
    $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$shipmentCode);
    
    //TOTAL 	
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$shipmentCode);

$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Shipment Date");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$shipmentDate);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");

    //GGL
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$shipmentDate);

    //RSB + GGL
    $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$shipmentDate);

    //Uncertified
    $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$shipmentDate);

    //TOTAL
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$shipmentDate);

}

$AmountTotalQty = 0;
$total_quantityR = 0;
$total_quantityG = 0;
$total_quantityRG = 0;
$total_quantityUN = 0;
$AmountTotalB = 0; 
$AmountTotalE = 0;
     while($row2 = $result2->fetch_object()) {
			$value='';
			$no1 =1;
			
			$slipOut = $row2->slipOut;
			$slipOutCode = $row2->slipOutCode;
			$shipmentNo = $row2->shipment_no;
			$transactionDate = $row2->transactionDate;
                        
            $quantity_totalR= $row2->qty_rsb;
            $quantity_totalG= $row2->qty_ggl;
            $quantity_totalRG= $row2->qty_rsb_ggl;
            $quantity_totalUN= $row2->qty_uncertified;

            $total_quantityR = $total_quantityR + $quantity_totalR;
            $total_quantityG = $total_quantityG + $quantity_totalG;
            $total_quantityRG = $total_quantityRG + $quantity_totalRG;
            $total_quantityUN = $total_quantityUN + $quantity_totalUN;

            $vessel_name = $row2->vessel_name;
	  
	  $no1++;
}
$AmountTotalQty = $total_quantityR + $total_quantityG + $total_quantityRG + $total_quantityUN;

//SELECT DATA TRANSACTION (IN) BIASANYA IN ITU SERTIFIKAT YG MASUK HANYA 1
$sql ="SELECT ROUND(SUM(CASE WHEN t.transaction_type = 1 AND t.rsb = 1 THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2 AND t.rsb = 1 THEN t.shrink ELSE 0 END)) AS qty_availableR, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND  t.ggl = 1 THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2 AND t.ggl = 1 THEN t.shrink ELSE 0 END)) AS qty_availableG, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb_ggl = 1 THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2 AND t.`rsb_ggl` = 1 THEN t.shrink ELSE 0 END)) AS qty_availableRG,
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN t.quantity ELSE 0 END)
            - SUM(CASE WHEN t.transaction_type = 2  AND (t.uncertified = 1 OR t.uncertified = 0 OR t.uncertified IS NULL) THEN t.shrink ELSE 0 END))
            AS qty_availableUN,
            ROUND(SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2 ) AS shrink
        FROM `transaction` t
		WHERE 1=1 AND SUBSTRING(t.slip_no,9,17) < '{$slipOut}' 
		AND t.transaction_date <=  '{$transactionDate}' 
		AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}'  ORDER BY t.transaction_date ASC";
//echo ' Query 1 - ' .$sql;
//die();
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();	
        // $begining1 = $row->qty_available;
        $begining1_R = $row->qty_availableR;
        $begining1_G = $row->qty_availableG;
        $begining1_RG = $row->qty_availableRG;
        $begining1_UN = $row->qty_availableUN;
        $susut = $row->shrink;			
}

/*$sql3 = "SELECT SUM(qtyRSB) AS RS, SUM(qtyGGL) AS GG, SUM(qtyRG) AS RG, SUM(qtyUN) AS UN FROM ( 
    SELECT CASE WHEN d.qty_rsb <> 0 THEN SUM(d.qty_rsb) ELSE 0 END AS qtyRSB, 
        CASE WHEN d.qty_ggl <> 0 THEN SUM(d.qty_ggl) ELSE 0 END AS qtyGGL, 
        CASE WHEN d.qty_rsb_ggl <> 0 THEN SUM(d.qty_rsb_ggl) ELSE 0 END AS qtyRG, 
        CASE WHEN d.qty_uncertified <> 0 THEN SUM(d.qty_uncertified) ELSE 0 END AS qtyUN, 
        t2.slip_no, t2.`transaction_date` 
    FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
    LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
    AND SUBSTRING(t2.slip_no,1,3) = '{$stockpileCode}' AND t2.transaction_type = 2
    GROUP BY d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified
) a ";

$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
if($result3->num_rows == 1) {
    while($row3 = $result3->fetch_object()) {
        
        $begining2_R =  $row3->RS;
        $begining2_G = $row3->GG;
        $begining2_RG = $row3->RG;
        $begining2_UN = $row3->UN;
    }	
}*/

//Update By Idris ngambil notim Out
		$sql3 = "SELECT * FROM (
SELECT case when (t2.rsb = 1) then SUM(t2.quantity) else 0 end AS qtyRsb,
case when (t2.ggl = 1) then SUM(t2.quantity) ELSE 0 END AS qtyGgl,
case WHEN (t2.rsb_ggl = 1) THEN SUM(t2.quantity) ELSE 0 END  AS qtyRsbGgl,
case WHEN (t2.uncertified = 1 OR t2.uncertified = 0 OR t2.uncertified IS NULL) THEN SUM(t2.quantity)ELSE 0 END AS qtyUncertifeid, 
t2.slip_no, t2.`transaction_date` 
FROM `transaction` t2 INNER JOIN shipment sh ON sh.shipment_id = t2.shipment_id WHERE 1=1
AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}' AND t2.transaction_type = 2) a";
$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
		
if($result3->num_rows > 0) {
	while($row3 = $result3->fetch_object()) {
		
		//	$begining2 = $begining2 + $row3->qty;
		//	$begining2 = $begining2 + $row3->begining2;
		$begining2_R = $begining2_R + $row3->qtyRsb;
		$begining2_G = $begining2_G + $row3->qtyGgl;
		$begining2_RG = $begining2_RG + $row3->qtyRsbGgl;
		$begining2_UN = $begining2_UN + $row3->qtyUncertifeid;
	}	
}




// $begining = $begining1 - $begining2;		
// $ending = $begining - $total_quantity;	
$AmountTotalB = 0; $AmountTotalE = 0;

$beginingR = $begining1_R - $begining2_R;
$beginingG = $begining1_G - $begining2_G;
$beginingRG = $begining1_RG - $begining2_RG;
$beginingUN = $begining1_UN - $begining2_UN;

$AmountTotalB = ($beginingR + $beginingG + $beginingRG + $beginingUN);
//$AmountTotalB = ($beginingR + $beginingG + $beginingRG + $beginingUN) - $susut;

$endingR = $beginingR - $total_quantityR;	
$endingG = $beginingG - $total_quantityG;	
$endingRG = $beginingRG - $total_quantityRG;	
$endingUN = $beginingUN - $total_quantityUN;

$AmountTotalE = ($endingR + $endingG + $endingRG + $endingUN);
//$AmountTotalE = ($endingR + $endingG + $endingRG + $endingUN) - $susut;

$rowActive++;
	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Vessel Name");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray4);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$vessel_name);
    //$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}"); 
	
      //GGL
  	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$vessel_name);
      
  //RSB + GGL
    $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$vessel_name);
      
  //Uncertified
    $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$vessel_name);
      
      //TOTAL
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$vessel_name);
  
 $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Begining Balance (Kg)");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$beginingR);
    // echo "TEST :" .$beginingR;
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");

    //GGL
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$beginingG);
	
	//RSB + GGL
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$beginingRG);
	
	//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$beginingUN);
	
	//TOTAL
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalB);

  $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Quantity (Kg)");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_quantityR);
       //GGL
   	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$total_quantityG);
	
	//RSB + GGL
   	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$total_quantityRG);
	
	//Uncertified
   	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_quantityUN);
	
	//TOTAL   	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalQty);

   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
$rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Ending Balance (Kg)");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$endingR);
  
   //GGL
   $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}",$endingG);
   
   //RSB + GGL
      $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}",$endingRG);
   
   //Uncertified
      $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$endingUN);
   
   //TOTAL
          $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$AmountTotalE);
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");		 
	
$sql = "SELECT d.*,
    CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
        DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
        t.slip_no, 
        CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
        con.po_no, 
        CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
        v3.vendor_name AS supplier,
        v1.vendor_name, 
        sh.shipment_code,
        t.send_weight, 
        t.netto_weight, 
        d.quantity, d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl,
    CASE WHEN t.mutasi_id IS NOT NULL THEN t.unit_cost
        WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
    ELSE con.price_converted END AS price_converted,
        CASE WHEN t.mutasi_id IS NOT NULL THEN d.quantity * t.unit_cost
        WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
    ELSE d.quantity * con.price_converted END AS cogs_amount,
        t.freight_quantity, 
        t.freight_price, 
    CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
        ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
    CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
        WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) 
    ELSE 0 END AS freight_shrink,
        t.unloading_price, 
        (d.percent_taken / 100) * t.unloading_price AS unloading_total,
    vhc.price AS vh_price, 
    t.handling_quantity,
    CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
            ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END AS handling_total,
    vh1.pph_tax_id AS vh_pph_tax_id, 
    vh1.pph AS vh_pph, 
    vhtx.tax_category AS vh_pph_tax_category,
        f.ppn_tax_id AS fc_ppn_tax_id, 
        f.ppn AS fc_ppn, 
        fctxppn.tax_category AS fc_ppn_tax_category,
        t.fc_tax_id AS fc_pph_tax_id, 
        fctxpph.tax_value AS fc_pph, 
        fctxpph.tax_category AS fc_pph_tax_category,
        l.ppn_tax_id AS uc_ppn_tax_id, 
        l.ppn AS uc_ppn, 
        uctxppn.tax_category AS uc_ppn_tax_category,
        l.pph_tax_id AS uc_pph_tax_id, 
        l.pph AS uc_pph, 
        uctxpph.tax_category AS uc_pph_tax_category,
    l.labor_id,
    t.freight_cost_id, 
    f.freight_id,
    t.permit_no,
    (SELECT slip_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
    (SELECT transaction_date FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS transactionDate,
    (SELECT SUBSTRING(slip_no,1,3) FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
    (SELECT vehicle_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name, 
    sh.shipment_no,
    CASE WHEN d.qty_rsb <> 0 THEN 'RSB'
        WHEN d.qty_ggl <> 0 THEN 'GGL'
        WHEN d.qty_rsb_ggl <> 0 THEN 'RSB + GGL'
    ELSE 'Uncertified' END AS sertifikat,
    v1d.distance, v1d.ghg
        FROM temp_delivery d
        LEFT JOIN `temp_transaction` t
            ON t.temp_transaction_id = d.temp_transaction_id
        LEFT JOIN `transaction` tt
            ON d.transaction_id = tt.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = tt.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN vendor_detail v1d
			ON v1d.vendor_id = v1.vendor_id
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
            ON sh.shipment_id = d.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN tax fctxpph
            ON fctxpph.tax_id = t.fc_tax_id
        LEFT JOIN tax fctxppn
            ON fctxppn.tax_id = f.ppn_tax_id
        LEFT JOIN labor l
            ON l.labor_id = t.labor_id
        LEFT JOIN tax uctxpph
            ON uctxpph.tax_id = l.pph_tax_id
        LEFT JOIN tax uctxppn
            ON uctxppn.tax_id = l.ppn_tax_id	
        LEFT JOIN vendor_handling_cost vhc
            ON vhc.handling_cost_id = t.handling_cost_id
        LEFT JOIN vendor_handling vh1
            ON vh1.vendor_handling_id = vhc.vendor_handling_id
        LEFT JOIN tax vhtx
            ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']} AND sh.shipment_id =  {$shipmentId}
        AND sh.sales_id =  {$salesId}  AND d.status = 0  ORDER BY t.slip_no ASC, d.`temp_transaction_id` ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);	

$rowActive++;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "COGS REPORT");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Area");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Slip No.");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "PO No.");

$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "DO No.");


$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "PKS SOURCE");

$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "CERTIFICATION");

$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "DESTINATION (Km)");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "GHG AMOUNT");


$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "SHIPMENT CODE");
$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "INVENTORY (KG)");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "PKS SOURCE DETAIL");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">

while($row = $result->fetch_object()) {
    $rowActive++;
	
		if($row->slip_no >= 'SAM-0000000001' && $row->slip_no <= 'SAM-0000001925'){
				$fc_pph = 4 ;
			}else{
				$fc_pph	 = $row->fc_pph;
			}
			
			if($row->slip_no >= 'MAR-0000000001' && $row->slip_no <= 'MAR-0000007138'){
				$fc_pph = 4 ;
			}else{
				$fc_pph	 = $row->fc_pph;
			}
				
				if($row->vh_pph_tax_category == 1 && $row->vh_pph_tax_id != ''){
			         $pphvh = ($row2->handling_total / ((100 - $row->vh_pph) / 100)) - $row->handling_total;
				 
				 }elseif($row->vh_pph_tax_category == 0 && $row->vh_pph_tax_id != ''){
					  $pphvh =  0;  
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphvh = 0;
				 }
				 
				 $handlingTotal = $row->handling_total - $pphvh;
               
		         if($row->fc_pph_tax_category == 1 && $row->fc_pph_tax_id != ''){
			         $pphfc = ($row->freight_total / ((100 - $fc_pph) / 100)) - $row->freight_total;
					 $pphfcShrink = ($row->freight_shrink / ((100 - $fc_pph) / 100)) - $row->freight_shrink;
				 
				 }elseif($row->fc_pph_tax_category == 0 && $row->fc_pph_tax_id != ''){
					  $pphfc =  0;
						$pphfcShrink = 0;
				 }else{
				 	$pphfc = 0;
					$pphfcShrink = 0;
				 }
				 $freightTotal = ($row->freight_total + $ppnfc + $pphfc) - ($row->freight_shrink + $pphfcShrink);
				 
				 
				 if($row->uc_pph_tax_category == 1 && $row->uc_pph_tax_id != ''){
			         $pphuc = ($row->unloading_total / ((100 - $row->uc_pph) / 100)) - $row->unloading_total;
					 
				 }elseif($row->uc_pph_tax_category == 0 && $row->uc_pph_tax_id != ''){
					 $pphuc =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc = 0;
				 }
				 
				 
				 $unloadingTotal = $row->unloading_total + $ppnuc + $pphuc;	
    
     $totalCogs = $row->cogs_amount + $freightTotal + $unloadingTotal + $handlingTotal;
    
     $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date2);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->permit_no);

    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vendor_name); 
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->sertifikat);

    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->distance);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->ghg);



    $objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->shipment_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->vendor_curah_name);
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

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":G{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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