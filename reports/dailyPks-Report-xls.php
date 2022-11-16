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

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('Y-m');

$whereBalanceProperty = '';
$whereDeliveriesProperty = '';
$whereShipmentProperty = '';
$whereLessProperty = '';

$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$periodFull = '';



if($periodTo != '') {
    
    $whereBalanceProperty .= " AND a.unloading_date < STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereDeliveriesProperty .= " AND t.unloading_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$whereShipmentProperty .=  " AND d.delivery_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereLessProperty .= " AND t.unloading_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
      
    $periodFull = "To " . $periodTo . " ";
}

// </editor-fold>

$fileName = "Inventory & Committed Shipments" . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "PKS Report (Inventory & Committed Shipments)");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Description");

$col = 'B';
$sqlHead = "SELECT s.stockpile_name
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
            if($resultHead->num_rows > 0) {
                while($rowHead = $resultHead->fetch_object()) {
					$stockpile_name = strtoupper($rowHead->stockpile_name);
                     
					foreach($rowHead as $cell){ 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$headerRow,$cell); 
					$col++; 
					} 
                }
            }
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Total");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Remarks");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Balance Previous Report");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(a.quantity), 0) AS quantity, COALESCE(SUM(a.shrink), 0) AS shrink
                                    FROM (
                                        SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.quantity END AS quantity,
											CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END AS shrink,
                                            CASE WHEN t.transaction_type = 1 THEN t.unloading_date ELSE t.transaction_date END AS unloading_date
                                        FROM `transaction` t
                                        LEFT JOIN stockpile_contract sc
                                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                                        LEFT JOIN shipment sh
                                            ON sh.shipment_id = t.shipment_id
                                        LEFT JOIN sales sl
                                            ON sl.sales_id = sh.sales_id    
                                        WHERE 1=1 
                                        AND (sc.stockpile_id = {$rowBody->stockpile_id} OR sl.stockpile_id = {$rowBody->stockpile_id})
                                        AND t.company_id = {$_SESSION['companyId']}
                                    ) a
                                    WHERE 1=1 {$whereBalanceProperty}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty = $rowContent->quantity - $rowContent->shrink;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,$qty);
					$total = $total + $qty; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Plus : Deliveries");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(t.quantity), 0) AS quantity
                                    FROM `transaction` t
                                    INNER JOIN stockpile_contract sc
                                        ON sc.stockpile_contract_id = t.stockpile_contract_id
                                    WHERE 1=1 {$whereDeliveriesProperty}
                                    AND t.transaction_type = 1
                                    AND t.company_id = {$_SESSION['companyId']}
                                    AND sc.stockpile_id = {$rowBody->stockpile_id}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_plus = $rowContent->quantity;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,$qty_plus);
					$total = $total + $qty_plus; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Less : Shipments");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(less_shipment), 0) AS less_shipment 
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     	$less_shipment = $rowContent->less_shipment;
					
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "-". $less_shipment);
					$total = $total + $less_shipment; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Less : Local Sales");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(less_local), 0) AS less_local
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "-". $rowContent->less_local);
					$total = $total + $rowContent->less_local; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Less : Susut (Moisture Loss)");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(less_susut), 0) AS less_susut 
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "-". $rowContent->less_susut);
					$total = $total + $rowContent->less_susut; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "+/- Other Adjustment");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Total");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(SUM(a.quantity), 0) AS quantity, COALESCE(SUM(a.shrink), 0) AS shrink
                                    FROM (
                                        SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.quantity END AS quantity,
											CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END AS shrink,
                                            CASE WHEN t.transaction_type = 1 THEN t.unloading_date ELSE t.transaction_date END AS unloading_date
                                        FROM `transaction` t
                                        LEFT JOIN stockpile_contract sc
                                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                                        LEFT JOIN shipment sh
                                            ON sh.shipment_id = t.shipment_id
                                        LEFT JOIN sales sl
                                            ON sl.sales_id = sh.sales_id    
                                        WHERE 1=1 
                                        AND (sc.stockpile_id = {$rowBody->stockpile_id} OR sl.stockpile_id = {$rowBody->stockpile_id})
                                        AND t.company_id = {$_SESSION['companyId']}
                                    ) a
                                    WHERE 1=1 {$whereBalanceProperty}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty = $rowContent->quantity - $rowContent->shrink;
					}else{
						$qty = 0;
					}
					
					 $sqlContent = "SELECT COALESCE(SUM(t.quantity), 0) AS quantity
                                    FROM `transaction` t
                                    INNER JOIN stockpile_contract sc
                                        ON sc.stockpile_contract_id = t.stockpile_contract_id
                                    WHERE 1=1 {$whereDeliveriesProperty}
                                    AND t.transaction_type = 1
                                    AND t.company_id = {$_SESSION['companyId']}
                                    AND sc.stockpile_id = {$rowBody->stockpile_id}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_plus = $rowContent->quantity;
					}else{
						$qty_plus = 0;
					}
					
					 $sqlContent = "SELECT COALESCE(SUM(less_shipment + less_local + less_susut), 0) AS stock_less
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_less = $rowContent->stock_less;
					}else{
						$stock_less = 0;
					}
					
					$qty_total = ($qty + $qty_plus) - $stock_less;
                     
					if($qty_total < 0){
					$qty_total1 = $qty_total * -1;
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "-". $qty_total1);
					}else{
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $qty_total);	
					}
					$total = $total + $qty_total; 
					$col++; 
					
                
            }
		}
if($total < 0){
$total1 = $total * -1;
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total1);
}else{
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
}
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");
		
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Screened Stock");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(screened_stock), 0) AS screened_stock 
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $rowContent->screened_stock);
					$total = $total + $rowContent->screened_stock; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Screened + Sprayed Stock");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(sprayed_stock), 0) AS sprayed_stock  
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $rowContent->sprayed_stock);
					$total = $total + $rowContent->sprayed_stock; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Unscreened Stock");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(unscreened_stock), 0) AS unscreened_stock 
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $rowContent->unscreened_stock);
					$total = $total + $rowContent->unscreened_stock; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Total Inventory Available");
$col = 'B';
$total = 0;
$stock_total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(screened_stock + sprayed_stock + unscreened_stock),0) AS stock_total
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                     	$stock_total = $rowContent->stock_total;
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $stock_total);
					$total = $total + $stock_total; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for January {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-01' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for January {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-02' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for February {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-02' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for March {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-03' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for April {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-04' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for May {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-05' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for June {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-06' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for July {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-07' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for August {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-08' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for September {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-09' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for October {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-10' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for November {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-11' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Committed Shipment for December {$currentYear}");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(quantity), 0) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-12' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty_sales);
					$total = $total + $qty_sales; 
					$col++; 
					
                }
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Available Inventory");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT (screened_stock + sprayed_stock + unscreened_stock) AS stock_total
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_total = $rowContent->stock_total;
					}else{
						$stock_total = 0;
					}
					
					$sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y') = '{$currentYear}' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
					}else{
						$qty_sales = 0;
					}
					
					$stock_available = $stock_total - $qty_sales;
					  
					if($stock_available < 0){
					$stock_available1 = $stock_available * -1;
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "-". $stock_available1);
					}else{
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $stock_available);	
					}
					$total = $total + $stock_available; 
					$col++; 
					
                
            }
		}
if($total < 0){
$total1 = $total * -1;
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total1);
}else{
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
}
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "COMMITTED SHIPMENTS");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("January {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-01'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes1 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
$qty = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-01'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes1);
	}
}

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("February {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-02' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes2 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-02'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes2);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("March {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-03'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes3 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-03'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes3);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("April {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-04' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes4 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-04'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes4);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("May {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-05' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes5 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-05'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes5);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("June {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-06' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes6 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-06'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes6);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("July {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-07' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes7 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-07'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes7);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("August {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-08' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes8 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-08'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes8);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("September {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-09' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes9 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-09'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes9);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("October {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-10' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes10 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-10'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes10);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("November {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-11' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes11 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-11'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes11);
	}
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getCell("A{$rowActive}")->setValueExplicit("December {$currentYear}", PHPExcel_Cell_DataType::TYPE_STRING);
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   
                     
					 
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "");
					//$total = $total + $rowContent->less_susut; 
					$col++; 
					
                
            }
		}

$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "");


$sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-12' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes12 = $row->notes;
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $destination);
	

$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-12'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty1 = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
                   		
						if($qty1 != 0 && $sales_status == 0){
							
							$qty = $qty1;
							 
						}elseif($qty1 != 0 && $sales_status == 1){
							$qty = "DONE";	
						}
						}
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive,"-". $qty);
						$total = $total + $qty;
					$col++;
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, 0);
						$col++;
					}
            }
		}

	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $notes12);
	}
}

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Balance");
$col = 'B';
$total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT (screened_stock + sprayed_stock + unscreened_stock) AS stock_total
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_total = $rowContent->stock_total;
					}else{
						$stock_total = 0;
					}
					
					$sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y') = '{$currentYear}' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
					}else{
						$qty_sales = 0;
					}
					
					$stock_available = $stock_total - $qty_sales;
					  
					if($stock_available < 0){
					$stock_available1 = $stock_available * -1;
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, "-". $stock_available1);
					}else{
					$objPHPExcel->getActiveSheet()->setCellValue($col.$rowActive, $stock_available);	
					}
					$total = $total + $stock_available; 
					$col++; 
					
                
            }
		}
if($total < 0){
$total1 = $total * -1;
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "-". $total1);
}else{
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $total);
}
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
$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":L{$bodyRowEnd}")->getNumberFormat()->setFormatCode('_(""* #,##0.00_);_(\(#,##0.00\);_(""* ""??_);_(@_)'); 

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