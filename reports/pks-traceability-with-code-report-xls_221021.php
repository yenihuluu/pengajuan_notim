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
$shipmentId = $myDatabase->real_escape_string($_POST['shipmentId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$dateField = '';
$shipmentCode = 'All ';
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
// <editor-fold defaultstate="collapsed" desc="Query">


if($shipmentId != '') {
    $whereProperty = " AND sh.sales_id = {$shipmentId} ";

      $sql = "SELECT sh.*,s.stockpile_code, s.stockpile_name, sh.shipment_date FROM shipment sh INNER JOIN sales sl ON sh.sales_id = sl.sales_id INNER JOIN stockpile s ON sl.stockpile_id = s.stockpile_id  WHERE sh.sales_id = {$shipmentId}";
    $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowShipment = $resultShipment->fetch_object();
    $shipmentCode = $rowShipment->shipment_no . " ";
	$stockpileCode = $rowShipment->stockpile_code . " - ";
	$stockpileName = $rowShipment->stockpile_name;
	$shipmentDate = $rowShipment->shipment_date;
}
/*if($stockpileId != '') {
    $whereProperty = " AND s.stockpile_id = {$stockpileId} ";
  */
   // $sql = "SELECT * FROM shipment WHERE shipment_id = {$shipmentId}";
   // $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   // $rowShipment = $resultShipment->fetch_object();
   // $shipmentCode = $rowShipment->shipment_code . " ";
//}
if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
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
            t.send_weight, t.netto_weight, d.quantity,
			CASE WHEN t.mutasi_id IS NOT NULL THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
            CASE WHEN t.mutasi_id IS NOT NULL THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, 
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.freight_quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
            t.unloading_price, (d.percent_taken / 100) * t.unloading_price AS unloading_total,
			vhc.price AS vh_price, t.handling_quantity,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END AS handling_total,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
            f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
            t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			l.labor_id,t.freight_cost_id, f.freight_id,
			(SELECT slip_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOut,
			(SELECT transaction_date FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS transactionDate,
			(SELECT SUBSTRING(slip_no,1,3) FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS slipOutCode,
			(SELECT vehicle_no FROM TRANSACTION WHERE notim_status != 1 AND slip_retur IS NULL AND shipment_id = (SELECT shipment_id FROM shipment WHERE sales_id = sl.sales_id ORDER BY shipment_id ASC LIMIT 1)LIMIT 1) AS vessel_name, sh.shipment_no
                     FROM delivery d
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
		lEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        {$whereProperty} ORDER BY d.`transaction_id` ASC";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);





// </editor-fold>

$fileName = $stockpileCode . "Code PKS Traceability Report " . $shipmentCode . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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

if ($shipmentCode != "") {

	$rowActive++;
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
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
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
    //$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
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

            while($row2 = $result2->fetch_object()) {
			$value='';
			$no1 =1;

			$slipOut = $row2->slipOut;
			$slipOutCode = $row2->slipOutCode;
			$shipmentNo = $row2->shipment_no;
			$transactionDate = $row2->transactionDate;

				if($row2->slip_no >= 'SAM-0000000001' && $row2->slip_no <= 'SAM-0000001925'){
				$fc_pph2 = 4 ;
			}else{
				$fc_pph2	 = $row2->fc_pph;
			}

			if($row2->slip_no >= 'MAR-0000000001' && $row2->slip_no <= 'MAR-0000007138'){
				$fc_pph2 = 4 ;
			}else{
				$fc_pph2	 = $row2->fc_pph;
			}

				if($row2->vh_pph_tax_category == 1 && $row2->vh_pph_tax_id != ''){
			         $pphvh2 = ($row2->handling_total / ((100 - $row2->vh_pph) / 100)) - $row2->handling_total;

				 }elseif($row2->vh_pph_tax_category == 0 && $row2->vh_pph_tax_id != ''){
					  $pphvh2 =  0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphvh2 = 0;
				 }

				 $handlingTotal2 = $row2->handling_total - $pphvh2;

		         if($row2->fc_pph_tax_category == 1 && $row2->fc_pph_tax_id != ''){
			         $pphfc2 = ($row2->freight_total / ((100 - $fc_pph2) / 100)) - $row2->freight_total;
					 $pphfcShrink2 = ($row2->freight_shrink / ((100 - $fc_pph2) / 100)) - $row2->freight_shrink;

				 }elseif($row2->fc_pph_tax_category == 0 && $row2->fc_pph_tax_id != ''){
					  $pphfc2 =  0;
						$pphfcShrink2 = 0;
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc2 = 0;
					$pphfcShrink2 = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/

				 $freightTotal2 = ($row2->freight_total + $ppnfc2 + $pphfc2) - ($row2->freight_shrink + $pphfcShrink2);


				 if($row2->uc_pph_tax_category == 1 && $row2->uc_pph_tax_id != ''){
			         $pphuc2 = ($row2->unloading_total / ((100 - $row2->uc_pph) / 100)) - $row2->unloading_total;

				 }elseif($row2->uc_pph_tax_category == 0 && $row2->uc_pph_tax_id != ''){
					 $pphuc2 =  0;
					 //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
				 }else{
				 	$pphuc2 = 0;
				 }


				$unloadingTotal2 = $row2->unloading_total + $ppnuc2 + $pphuc2;

     $totalCogs2 = $row2->cogs_amount + $freightTotal2 + $unloadingTotal2 + $handlingTotal2;

	 $quantity_total= $row2->quantity;
	 $total_quantity = $quantity_total+$total_quantity;

	 $pks_total = $row2->cogs_amount;
	 $total_pks = $pks_total+$total_pks;

	 $fc_total = $freightTotal2;
	 $total_fc = $fc_total+$total_fc;

	 $vh_total = $handlingTotal2;
	 $total_vh = $vh_total+$total_vh;

	 $uc_total = $unloadingTotal2;
	 $total_uc = $uc_total+$total_uc;

	 $cogs_total = $totalCogs2;
	 $total_cogs = $cogs_total+$total_cogs;

	  $vessel_name = $row2->vessel_name;

	  $no1++;
}

$sql = "SELECT
ROUND(SUM(CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE 0 END) -
	SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2) AS qty_available
FROM `transaction` t 
WHERE 1=1 AND t.slip_no < '{$slipOut}' AND t.transaction_date <= '{$transactionDate}' AND SUBSTRING(t.slip_no,1,3) = '{$slipOutCode}'";
    		$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
			if($result !== false && $result->num_rows == 1) {
				 $row = $result->fetch_object();

			    $begining1 = $row->qty_available;

			}
$sql3 = "SELECT * FROM (
SELECT SUM(t2.quantity) AS qty, t2.slip_no, t2.`transaction_date` FROM `transaction` t2 INNER JOIN shipment sh ON sh.shipment_id = t2.shipment_id WHERE 1=1
   AND SUBSTRING(t2.slip_no,1,3) = '{$slipOutCode}' AND t2.transaction_type = 2 AND sh.`shipment_no` NOT LIKE '%{$shipmentNo}%' GROUP BY sh.sales_id ORDER BY t2.slip_no ASC) a
 WHERE a.slip_no < '{$slipOut}' AND a.transaction_date <= '{$transactionDate}'";
$result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
if($result3->num_rows > 0) {
while($row3 = $result3->fetch_object()) {

		$begining2 = $begining2 + $row3->qty;

		}

	}
$begining = $begining1 - $begining2;
$ending = $begining - $total_quantity;


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
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}","0");
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
         //GGL
    $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}","0");
	
	//RSB + GGL
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}","0");
	
	//Uncertified
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$begining);
		
	//TOTAL
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$begining);

$rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Quantity (Kg)");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}","0");
   // $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
      	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}","0");
	
	//RSB + GGL
   	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}","0");
	
	//Uncertified
		$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$total_quantity);

	//TOTAL   	
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$total_quantity);

$rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Ending Balance (Kg)");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}","0");
  //  $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
      //GGL
   $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("C{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}","0");
   
   //RSB + GGL
      $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("D{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}","0");
   
   //Uncertified
   	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("E{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}",$ending);
   
   //TOTAL
          $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
   $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}")->applyFromArray($styleArray6);
   $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}",$ending);

 /*$rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total COGS PKS Amount");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_pks);
    $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Freight Cost");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_fc);
    $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Unloading Cost");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_uc);
    $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total Handling Cost");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_vh);
    $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");
 $rowActive++;
 	$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray4);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}","Total COGS");
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}")->applyFromArray($styleArray6);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}",$total_cogs);
    $objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:{$lastColumn}{$rowActive}");*/


$sql = "SELECT d.*,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,  
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS transaction_date2,
			DATE_FORMAT(t.loading_date, '%d %b %Y') AS tglMuat,
			CONCAT(DATE_FORMAT(t.loading_date, '%d%m%Y'), ' - ', t.vehicle_no) AS kodeMasukMobil,
			CONCAT(DATE_FORMAT(t.unloading_date, '%d%m%Y'), ' - ', t.vehicle_no, ' - ', SUBSTRING(tt.transaction_in, 11, 9)) AS KodeTerima,
            tt.transaction_in, tt.slip as tiketTimbang, con.contract_no,
            t.slip_no, t.permit_no,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            con.po_no, 
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v3.vendor_name AS supplier,
            v1.vendor_name,v1.vendor_code,
			(SELECT GROUP_CONCAT(vc.vendor_curah_code,' - ',vc.vendor_curah_name) FROM vendor_curah vc LEFT JOIN contract_pks_detail cpd ON vc.vendor_curah_id = cpd.vendor_curah_id WHERE cpd.contract_id = con.contract_id) AS vendor_curah_name,				
            sh.shipment_no,
            t.send_weight, t.netto_weight, d.quantity, 
			CASE WHEN t.mutasi_id IS NOT NULL THEN t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN t.unit_price
			ELSE con.price_converted END AS price_converted,
            CASE WHEN t.mutasi_id IS NOT NULL THEN d.quantity * t.unit_cost
			WHEN t.adjustmentAudit_id IS NOT NULL THEN d.quantity * t.unit_price
			ELSE d.quantity * con.price_converted END AS cogs_amount,
            t.freight_quantity, t.freight_price, 
            CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.freight_quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
            t.unloading_price,
            (d.percent_taken / 100) * t.unloading_price AS unloading_total,
			vhc.price AS vh_price, t.handling_quantity,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.handling_quantity * vhc.price)
			ELSE (d.percent_taken / 100) * (t.handling_quantity * vhc.price) END AS handling_total,
			vh1.pph_tax_id AS vh_pph_tax_id, vh1.pph AS vh_pph, vhtx.tax_category AS vh_pph_tax_category,
            f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category,
            t.fc_tax_id AS fc_pph_tax_id, fctxpph.tax_value AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			l.labor_id,t.freight_cost_id, f.freight_id,
            CASE WHEN t.ggl = 1 AND t.rsb = 0 THEN 'GGL'
				WHEN t.ggl = 0 AND t.rsb = 1 THEN 'RSB'
				WHEN t.ggl = 1 AND t.rsb = 1 THEN 'RSB + GGL'
			ELSE 'Uncertified' END AS sertifikat, v1d.distance, v1d.ghg
                     FROM delivery d
        INNER JOIN `transaction` t
        	ON t.transaction_id = d.transaction_id
		LEFT JOIN transaction_timbangan tt 
			ON t.t_timbangan = tt.transaction_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
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
		lEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling vh1
			ON vh1.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax vhtx
			ON vh1.pph_tax_id = vhtx.tax_id
        WHERE 1=1 
        AND sl.company_id = {$_SESSION['companyId']}
        {$whereProperty} ORDER BY t.slip_no ASC, d.`transaction_id` ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

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
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "DO No.");
/*$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Purchase Type");*/
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "PO No.");
/*$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "SUPPLIER NO");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "SUPPLIER NAME");*/
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
$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "TANGGAL MASUK");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:N{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "TANGGAL MUAT");
$objPHPExcel->getActiveSheet()->mergeCells("O{$rowActive}:O{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "KODE MASUK MOBIL");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:P{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "KODE TERIMA");
$objPHPExcel->getActiveSheet()->mergeCells("Q{$rowActive}:Q{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "TIKET TIMBANG");
$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:R{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "CONTRACT NO");


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
					 //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
				 }else{
				 	$pphfc = 0;
					$pphfcShrink = 0;
				 }
				 /*
				 if($row->fc_ppn_tax_id != ''){
					 $ppnfc = ($row->freight_total * ((100 + $row->fc_ppn) / 100)) - $row->freight_total;
				 }else{
				     $ppnfc = 0;
			     }*/



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
	 //$ghgAmount = ($row->quantity / 1000) * $row->ghg;
	 if($row->distance == "" || $row->distance == 0){
		 $distance = 0;
	 }
	  if($row->ghg == "" || $row->ghg == 0){
		 $ghg = 0;
	 } else {
		 $ghg = $row->ghg;
	 }

     $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->transaction_date2);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->permit_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->slip_no);
    //$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->contract_type2);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->po_no);
   /* $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->freight_code);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->supplier);*/
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->vendor_code);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->sertifikat);

    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $distance);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $ghg);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->shipment_no, PHPExcel_Cell_DataType::TYPE_STRING);
	/*$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->freight_supplier);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->freight_price);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->labor_name);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->unloading_price);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->send_weight);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->netto_weight);*/
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->quantity);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->vendor_curah_name);
	
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->transaction_date2);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->tglMuat);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->kodeMasukMobil);
	
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->KodeTerima);
	 $objPHPExcel->getActiveSheet()->setCellValueExplicit("Q{$rowActive}", $row->tiketTimbang, PHPExcel_Cell_DataType::TYPE_STRING);
     $objPHPExcel->getActiveSheet()->setCellValueExplicit("R{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);

    /*$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "");
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->price_converted);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->cogs_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $freightTotal);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $unloadingTotal);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $handlingTotal);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $totalCogs);*/
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
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":I{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

/*$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode("#,##0.0000");*/

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