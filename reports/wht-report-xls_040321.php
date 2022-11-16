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

$styleArray4b = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '99CCFF')
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
$whereProperty1 = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
//$module = $myDatabase->real_escape_string($_POST['module']);
//$whereProperty1 = '';
$periodFrom1 = $myDatabase->real_escape_string($_POST['periodFrom1']);
$periodTo1 = $myDatabase->real_escape_string($_POST['periodTo1']);

$periodFull = '';
$periodFull1 = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND (a.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))";
   // $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
	
} else if ($periodFrom != '' && $periodTo == '' ) {
    $whereProperty .= " AND (a.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))";
    //$boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
	
} else if ($periodFrom == '' && $periodTo != '' ) {
    $whereProperty .= " AND (a.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))";
    $periodFull = "To " . $periodTo . " ";
	
}

if ($periodFrom1 != '' && $periodTo1 != '') {
    $whereProperty1 .= " AND (a.payment_date BETWEEN STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo1}', '%d/%m/%Y'))";
   // $boolBalanceBefore = true;
    
	$periodFull1 = $periodFrom1 . " - " . $periodTo1 . " ";
} else if ($periodFrom1 != '' && $periodTo1 == '') {
    $whereProperty1 .= " AND (a.payment_date >= STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y'))";
    //$boolBalanceBefore = true;
    
	$periodFull1 = "From " . $periodFrom1 . " ";
} else if ($periodFrom == '' && $periodTo != '' && $periodFrom1 == '' && $periodTo1 != '') {
    $whereProperty1 .= " AND (a.payment_date <= STR_TO_DATE('{$periodTo1}', '%d/%m/%Y'))";
   
	$periodFull1 = "To " . $periodTo1 . " ";
}


$sql = "SELECT a.* FROM (SELECT
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
     WHEN p.vendor_id IS NOT NULL THEN 'CURAH'
     WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
     WHEN p.sales_id IS NOT NULL THEN 'SALES'
     WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
     WHEN p.labor_id IS NOT NULL THEN 'UNLOADING COST'
     WHEN p.general_vendor_id IS NOT NULL THEN 'LOADING/UMUM/HO'
     WHEN p.payment_cash_id IS NOT NULL THEN 'PAYMENT CASH'
     WHEN p.vendor_handling_id IS NOT NULL THEN 'HANDLING COST'
     ELSE 'INTERNAL TRANSFER' END AS data_source,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.sales_date
     WHEN p.freight_id IS NOT NULL THEN tfc.transaction_date
     WHEN p.labor_id IS NOT NULL THEN tuc.transaction_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.payment_date
     WHEN p.vendor_handling_id IS NOT NULL THEN p.payment_date
     ELSE p.payment_date END AS transaction_date,
CASE WHEN p.payment_type = 1 THEN 'IN'	
     WHEN p.payment_type = 2 THEN 'OUT'
     ELSE '' END AS payment_type,
p.payment_id, p.invoice_id, p.payment_no, p.payment_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_name
     WHEN p.vendor_id IS NOT NULL THEN vc.vendor_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.customer_name
     WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
     WHEN p.labor_id IS NOT NULL THEN l.labor_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
     ELSE a.account_name END AS supplier_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp_name
     WHEN p.vendor_id IS NOT NULL THEN vc.npwp_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.npwp_name
     WHEN p.freight_id IS NOT NULL THEN f.npwp_name
     WHEN p.labor_id IS NOT NULL THEN l.npwp_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.npwp_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE '' END AS npwp_name,
CASE WHEN p.freight_id IS NOT NULL THEN tfc.slip_no
     WHEN p.labor_id IS NOT NULL THEN tuc.slip_no 
     ELSE '' END AS slip_no,
     s.stockpile_name, p.remarks, 
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id AND pph_converted > 0)
     WHEN tfc.transaction_id IS NOT NULL THEN (SELECT (freight_quantity * freight_price) FROM `transaction` WHERE transaction_id = tfc.transaction_id)
     WHEN tuc.transaction_id IS NOT NULL THEN (SELECT unloading_price FROM `transaction` WHERE transaction_id = tuc.transaction_id)
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM payment_cash pc WHERE pc.payment_id = p.payment_id LIMIT 1)	 
     ELSE p.original_amount_converted END AS original_amount_converted,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 147)
     WHEN p.vendor_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 147)
     WHEN p.invoice_id IS NOT NULL THEN (SELECT a.account_no FROM account a LEFT JOIN invoice_detail id ON id.account_id = a.account_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN a.account_no
     WHEN p.freight_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 29)
     WHEN p.labor_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = 10)
     WHEN p.general_vendor_id IS NOT NULL THEN a.account_no
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT a.account_no FROM account a LEFT JOIN payment_cash pc ON a.account_id = pc.account_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE a.account_no END AS account_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 147)
     WHEN p.vendor_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 147)
     WHEN p.invoice_id IS NOT NULL THEN (SELECT a.account_name FROM account a LEFT JOIN invoice_detail id ON id.account_id = a.account_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN a.account_name
     WHEN p.freight_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 29)
     WHEN p.labor_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = 10)
     WHEN p.general_vendor_id IS NOT NULL THEN a.account_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT a.account_name FROM account a LEFT JOIN payment_cash pc ON a.account_id = pc.account_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE a.account_name END AS account_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp
     WHEN p.vendor_id IS NOT NULL THEN vc.npwp
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.npwp
     WHEN p.freight_id IS NOT NULL THEN f.npwp
     WHEN p.labor_id IS NOT NULL THEN l.npwp
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.npwp
     ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vpph.tax_name
     WHEN p.vendor_id IS NOT NULL THEN vcpph.tax_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT pph.tax_name FROM tax pph LEFT JOIN invoice_detail id ON id.pphID = pph.tax_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cspph.tax_name
     WHEN p.freight_id IS NOT NULL THEN fpph.tax_name 
     WHEN p.labor_id IS NOT NULL THEN lpph.tax_name
     WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_name
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT pph.tax_name FROM tax pph LEFT JOIN payment_cash pc ON pc.pphID = pph.tax_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_name
     ELSE '' END AS tax_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vpph.tax_category
     WHEN p.vendor_id IS NOT NULL THEN vcpph.tax_category
     WHEN p.invoice_id IS NOT NULL THEN (SELECT pph.tax_category FROM tax pph LEFT JOIN invoice_detail id ON id.pphID = pph.tax_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cspph.tax_category
     WHEN p.freight_id IS NOT NULL THEN fpph.tax_category
     WHEN p.labor_id IS NOT NULL THEN lpph.tax_category
     WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_category
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT pph.tax_category FROM tax pph LEFT JOIN payment_cash pc ON pc.pphID = pph.tax_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_category
     ELSE '' END AS tax_category,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vpph.tax_value
     WHEN p.vendor_id IS NOT NULL THEN vcpph.tax_value
     WHEN p.invoice_id IS NOT NULL THEN (SELECT pph.tax_value FROM tax pph LEFT JOIN invoice_detail id ON id.pphID = pph.tax_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cspph.tax_value
     WHEN p.freight_id IS NOT NULL THEN fpph.tax_value
     WHEN p.labor_id IS NOT NULL THEN lpph.tax_value
     WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_value
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT pph.tax_value FROM tax pph LEFT JOIN payment_cash pc ON pc.pphID = pph.tax_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_value
     ELSE '' END AS tax_value,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.vendor_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT COALESCE(SUM(id.pph_converted),0) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.freight_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.labor_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.general_vendor_id IS NOT NULL THEN p.pph_amount_converted
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(pc.pph_converted) FROM payment_cash pc WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN p.pph_amount_converted
     ELSE p.pph_amount_converted END AS tax_payable,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_address
     WHEN p.vendor_id IS NOT NULL THEN vc.vendor_address
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.customer_address
     WHEN p.freight_id IS NOT NULL THEN f.freight_address
     WHEN p.labor_id IS NOT NULL THEN l.labor_address
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_address
     WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.payment_cash_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_address
     ELSE '' END AS address,
CASE WHEN p.payment_location = 0 THEN 'HOF'
     ELSE s.stockpile_code END AS payment_location2,
	 b.bank_code, cur.currency_code, b.bank_type, p.payment_method, p.payment_status, p.freight_id,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.invoice_no
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_no
     WHEN p.general_vendor_id IS NOT NULL THEN p.invoice_no
     ELSE p.invoice_no END AS invoice_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.invoice_date
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.invoice_date
     ELSE '' END AS invoice_date,
CASE WHEN p.freight_id IS NOT NULL THEN (SELECT fc.contract_pkhoa FROM freight_cost fc WHERE fc.freight_cost_id = tfc.freight_cost_id)
ELSE '' END AS pkhoa
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN vendor vc ON vc.vendor_id = p.vendor_id
LEFT JOIN sales sl ON sl.sales_id = p.sales_id
LEFT JOIN customer cs ON cs.customer_id = sl.customer_id
LEFT JOIN freight f ON f.freight_id = p.freight_id
LEFT JOIN labor l ON l.labor_id = p.labor_id
LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
LEFT JOIN account a ON a.account_id = p.account_id
LEFT JOIN `transaction` tfc ON tfc.fc_payment_id = p.payment_id
LEFT JOIN `transaction` tuc ON tuc.uc_payment_id = p.payment_id
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN tax vpph ON vpph.tax_id = v.pph_tax_id
LEFT JOIN tax vcpph ON vcpph.tax_id = vc.pph_tax_id
LEFT JOIN tax cspph ON cspph.tax_id = cs.pph_tax_id
LEFT JOIN tax fpph ON fpph.tax_id = f.pph_tax_id
LEFT JOIN tax lpph ON lpph.tax_id = l.pph_tax_id
LEFT JOIN tax gvpph ON gvpph.tax_id = gv.pph_tax_id
LEFT JOIN invoice i ON p.invoice_id = i.invoice_id
LEFT JOIN bank b ON p.bank_id = b.bank_id
LEFT JOIN currency cur ON cur.currency_id = p.currency_id
LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = p.vendor_handling_id
LEFT JOIN tax vhpph ON vhpph.tax_id = vh.pph_tax_id
WHERE 1=1) a WHERE a.payment_status = 0 AND a.tax_payable > 0 {$whereProperty} {$whereProperty1} ORDER BY a.payment_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "Witholding Tax " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "W";

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
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Transaction Date = {$periodFull}");
}
if ($periodFull1 != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Payment Date = {$periodFull1}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Witholding Tax Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
//$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile Contract");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Data Source");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Voucher No.");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Supplier Name.");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Slip No.");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Description");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Account No.");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Transaction Amount");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Payment No.");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Name (Tax ID)");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Tax ID");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Tax Type");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Tax Payable");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Remark");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Original Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Original Invoice Date");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "PKHOA");



$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while($row = $result->fetch_object()) {
  
  $rowActive++;


    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->data_source);
    
	$voucherNo = "";
    if($row->payment_id != '') {
        $voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->currency_code;

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

        $voucherNo = $voucherCode .' # '. $row->payment_no; 
    }else{
		$voucherNo =  $row->payment_no; 
	}
	
	if($row->payment_type == 'IN'){
		$originalAmountConverted = $row->original_amount_converted * -1;
	}else{
		$originalAmountConverted = $row->original_amount_converted;
	}

$downPayment = 0;
if($row->invoice_id != 0 && $row->invoice_id != ''){
/*$sql1 = "SELECT GROUP_CONCAT(invoice_detail_id) AS invoice_detail_id FROM invoice_detail WHERE invoice_id = {$row->invoice_id}";
$result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
if($result1 !== false && $result1->num_rows > 0) {
while($row1 = $result1->fetch_object()) {
$invoiceDetailId = $row1->invoice_detail_id;

$sqlDP = "SELECT SUM(amount_converted) AS down_payment FROM invoice_detail WHERE invoice_detail_dp IN ({$invoiceDetailId}) AND pph_converted > 0";
    		$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
			if($resultDP !== false && $resultDP->num_rows == 1) {
				 $rowDP = $resultDP->fetch_object();
			if($rowDP->down_payment != 0 && $rowDP->down_payment != ''){
				 $downPayment = $rowDP->down_payment;
			}
			//echo $downPayment;
			}
			
}
}*/
$sqlDP = "SELECT SUM(amount_payment) AS down_payment FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.`invoice_detail_id`
WHERE id.invoice_id = {$row->invoice_id}";
    		$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
			if($resultDP !== false && $resultDP->num_rows == 1) {
				 $rowDP = $resultDP->fetch_object();
			if($rowDP->down_payment != 0 && $rowDP->down_payment != ''){
				 $downPayment = $rowDP->down_payment;
			}
			//echo $downPayment;
			}
}
$originalAmount = $originalAmountConverted - $downPayment;
			if($row->data_source == 'PAYMENT CASH'){
				$originalAmount = ($originalAmountConverted - $downPayment);
				$taxPayable = $row->tax_payable;
				
			}else{
			if($row->tax_category == 0){
				//if($row->invoice_id != 0 && $row->invoice_id != '' && $originalAmount == 0){
				//$originalAmount = $originalAmountConverted;
				//}else{
				$originalAmount = ($originalAmountConverted - $downPayment);
				//}
				$taxPayable = $originalAmount * ($row->tax_value/100);
			}elseif($row->tax_category == 1 && $row->freight_id != '' && $row->payment_method == 2){
				$originalAmount = ($originalAmountConverted - $downPayment);
				$taxPayable = $originalAmount * ($row->tax_value/100);
			}elseif($row->tax_category == 1 && $row->freight_id != ''){
				$originalAmount = ($originalAmountConverted - $downPayment) / ((100-$row->tax_value)/100);
				$taxPayable = $originalAmount * ($row->tax_value/100);
			}elseif($row->tax_category == 1){
				$originalAmount1 = ($originalAmountConverted - $downPayment);
				$originalAmount = $originalAmountConverted - $downPayment;
				$taxPayable = $originalAmount1 * ($row->tax_value/100);
			}
			}
	
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $voucherNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->transaction_date);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->supplier_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->remarks);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $originalAmountConverted);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $voucherNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("N{$rowActive}", $row->npwp_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", $row->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->address);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->tax_name);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $originalAmount);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $taxPayable);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("U{$rowActive}", $row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("V{$rowActive}", $row->invoice_date, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("W{$rowActive}", $row->pkhoa, PHPExcel_Cell_DataType::TYPE_STRING);

    $no++;
    
	
	
	$tamount = $tamount + $originalAmount;
	$totalTax = $totalTax + $taxPayable;
	
   
}

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:T{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $tamount);
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $totalTax);

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
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	 $objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("R" . ($headerRow + 1) . ":S{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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