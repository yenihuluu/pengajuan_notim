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
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$periodFrom1 = $myDatabase->real_escape_string($_POST['periodFrom1']);
$periodTo1 = $myDatabase->real_escape_string($_POST['periodTo1']);

$periodFull = '';
$periodFull1 = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END) BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END) >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END) <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}


if ($periodFrom1 != '' && $periodTo1 != '') {
    $whereProperty1 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo1}', '%d/%m/%Y')";
   // $boolBalanceBefore = true;
    
	$periodFull1 = $periodFrom1 . " - " . $periodTo1 . " ";
} else if ($periodFrom1 != '' && $periodTo1 == '') {
    $whereProperty1 .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom1}', '%d/%m/%Y')";
    //$boolBalanceBefore = true;
    
	$periodFull1 = "From " . $periodFrom1 . " ";
} else if ($periodFrom == '' && $periodTo != '' && $periodFrom1 == '' && $periodTo1 != '') {
    $whereProperty1 .= " AND p.payment_date <= STR_TO_DATE('{$periodTo1}', '%d/%m/%Y')";
   
	$periodFull1 = "To " . $periodTo1 . " ";
}

$sql = "SELECT
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
     WHEN p.vendor_id IS NOT NULL THEN 'CURAH'
     WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
     WHEN p.sales_id IS NOT NULL THEN 'SALES'
     WHEN p.general_vendor_id IS NOT NULL THEN 'LOADING/UMUM/HO'
     WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
     ELSE 'INTERNAL TRANSFER' END AS data_source,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m')
     WHEN p.sales_id IS NOT NULL THEN DATE_FORMAT(sl.entry_date, '%Y-%m')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.entry_date, '%Y-%m')
     WHEN p.freight_id IS NOT NULL THEN DATE_FORMAT(p.entry_date, '%Y-%m')
     ELSE DATE_FORMAT(p.entry_date, '%Y-%m') END AS period,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.input_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.entry_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.entry_date
     WHEN p.freight_id IS NOT NULL THEN p.entry_date
     ELSE p.entry_date END AS transaction_date,
p.payment_id, p.payment_no, p.payment_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_name
     WHEN p.vendor_id IS NOT NULL THEN vc.vendor_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.customer_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
     WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
     ELSE '' END AS supplier_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp_name
     WHEN p.vendor_id IS NOT NULL THEN vc.npwp_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cs.npwp_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
     WHEN p.freight_id IS NOT NULL THEN f.npwp_name
     ELSE '' END AS npwp_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.po_no
     WHEN p.invoice_id IS NOT NULL THEN ic.po_no
     ELSE c1.po_no END AS po_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.contract_no
     WHEN p.invoice_id IS NOT NULL THEN ic.contract_no
     ELSE c1.contract_no END AS contract_no,
     s.stockpile_name, p.remarks,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted
     WHEN p.invoice_id IS NOT NULL THEN p.amount_converted
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted * 1.1
     WHEN p.freight_id IS NOT NULL THEN p.amount_converted
     ELSE '' END AS original_amount_converted,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
     WHEN p.freight_id IS NOT NULL THEN f.npwp
     ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.vendor_address
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_address
     WHEN p.freight_id IS NOT NULL THEN f.freight_address
     ELSE '' END AS address,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.tax_invoice
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_tax
     WHEN p.general_vendor_id IS NOT NULL THEN p.tax_invoice
     WHEN p.freight_id IS NOT NULL THEN p.tax_invoice
     ELSE '' END AS tax_invoice,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.tax_date, '%Y-%m-%d')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     WHEN p.freight_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     ELSE '' END AS tax_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted - p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.amount_converted) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE id.invoice_id = p.invoice_id AND id.ppn > 0 AND i.invoice_status = 0 )
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted
     WHEN p.freight_id IS NOT NULL THEN ((p.amount_converted - p.ppn_amount_converted) + p.pph_amount_converted)
     ELSE '' END AS dpp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.ppn_converted) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE id.invoice_id = p.invoice_id AND i.invoice_status = 0)
     WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.freight_id IS NOT NULL THEN p.ppn_amount_converted
     ELSE '' END AS ppn,
CASE WHEN p.invoice_id IS NOT NULL 
	 THEN (SELECT SUM(CASE WHEN id.invoice_detail_id IS NOT NULL THEN 
(SELECT SUM(amount_converted) FROM invoice_detail a LEFT JOIN invoice_dp b ON a.invoice_detail_id = b.invoice_detail_dp 
WHERE b.invoice_detail_id = id.invoice_detail_id)ELSE 0 END) AS dp 
FROM invoice_detail id WHERE id.invoice_id = p.invoice_id)
     ELSE '' END AS dp,
CASE WHEN p.invoice_id IS NOT NULL 
	 THEN (SELECT SUM(CASE WHEN id.invoice_detail_id IS NOT NULL THEN 
(SELECT SUM(ppn_converted) FROM invoice_detail a LEFT JOIN invoice_dp b ON a.invoice_detail_id = b.invoice_detail_dp 
WHERE b.invoice_detail_id = id.invoice_detail_id)ELSE 0 END) AS dp 
FROM invoice_detail id WHERE id.invoice_id = p.invoice_id)
     ELSE '' END AS ppn_dp,
CASE WHEN p.payment_location = 0 THEN 'HOF'
     ELSE s.stockpile_code END AS payment_location2,
	 b.bank_code, cur.currency_code, b.bank_type, p.payment_method, p.payment_status, p.payment_type  
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN vendor vc ON vc.vendor_id = p.vendor_id
LEFT JOIN sales sl ON sl.sales_id = p.sales_id
LEFT JOIN customer cs ON cs.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
LEFT JOIN freight f ON f.freight_id = p.freight_id
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN stockpile_contract isc ON isc.stockpile_contract_id = i.po_id
LEFT JOIN contract ic ON isc.contract_id = ic.contract_id
LEFT JOIN stockpile_contract sc1 ON sc1.stockpile_contract_id = p.stockpile_contract_id_2
LEFT JOIN contract c1 ON sc1.contract_id = c1.contract_id
LEFT JOIN bank b ON p.bank_id = b.bank_id
LEFT JOIN currency cur ON cur.currency_id = p.currency_id
WHERE 1=1 AND p.payment_method = 1 AND p.payment_status = 0
AND ((CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.ppn_converted) FROM invoice_detail id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE id.invoice_id = p.invoice_id AND i.invoice_status = 0)
     WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.freight_id IS NOT NULL THEN p.ppn_amount_converted
     ELSE '' END) > 0 
     OR (CASE WHEN p.invoice_id IS NOT NULL 
	 THEN (SELECT SUM(CASE WHEN id.invoice_detail_id IS NOT NULL THEN 
(SELECT SUM(ppn_converted) FROM invoice_detail a LEFT JOIN invoice_dp b ON a.invoice_detail_id = b.invoice_detail_dp 
WHERE b.invoice_detail_id = id.invoice_detail_id)ELSE 0 END) AS dp 
FROM invoice_detail id WHERE id.invoice_id = p.invoice_id)
     ELSE '' END) > 0) {$whereProperty} {$whereProperty1}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "VAT" . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Z";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "VAT Report");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;

$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Period");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Data Source");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Voucher No.");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Supplier Name");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Contract No.");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Description");
$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Transaction Amount");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Payment No.");
$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:N{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Name (Tax ID)");
$objPHPExcel->getActiveSheet()->mergeCells("O{$rowActive}:O{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Tax ID");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:P{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Address");
$objPHPExcel->getActiveSheet()->mergeCells("Q{$rowActive}:Q{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Invoice Tax No.");
$objPHPExcel->getActiveSheet()->mergeCells("R{$rowActive}:R{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Invoice Tax Date");
$objPHPExcel->getActiveSheet()->mergeCells("S{$rowActive}:S{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->mergeCells("T{$rowActive}:T{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "VAT");
$objPHPExcel->getActiveSheet()->mergeCells("U{$rowActive}:U{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "SPM");
$objPHPExcel->getActiveSheet()->mergeCells("V{$rowActive}:Y{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Vendor Documents Status");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowMerge}", "Contract");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowMerge}", "Inv/Kwi/DO");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowMerge}", "FP Doc");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowMerge}", "SPT PPN");
$objPHPExcel->getActiveSheet()->mergeCells("Z{$rowActive}:Z{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Remark");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>
$rowActive = $rowMerge;
// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while($row = $result->fetch_object()) {
  
  $rowActive++;


    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->period);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->data_source);
    
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
	
	$dppPPN = ($row->ppn/$row->dpp)*100;
	if($dppPPN == 10){
		$dpp_value = $row->dpp;
	}else{
		$dpp_value = $row->ppn * 10;
	}
	
	if($row->dp != 0 && $row->ppn_dp == 0){
		$dp = 0;
	}else{
		$dp = $row->dp;
	}
	
	$dppTotal = $dpp_value - $dp;
	$ppnTotal = $row->ppn - $row->ppn_dp;
	if($row->payment_type == 1){
		$ppn = $ppnTotal * -1;
		$dpp = $dppTotal * -1;
	}else{
		$ppn = $ppnTotal;
		$dpp = $dppTotal;
	}
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $voucherNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->transaction_date);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->supplier_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->stockpile_name);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->original_amount_converted);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $voucherNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->npwp_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", $row->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->address);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("Q{$rowActive}", $row->tax_invoice, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->tax_date);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $dpp);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $ppn);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", '');


    $no++;
    
	
	
	$tamount = $tamount + $row->dpp;
	$totalTax = $totalTax + $row->ppn;
	
   
}

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:Z{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $tamount);
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $totalTax);

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
	$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("MMM-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("K" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("S" . ($headerRow + 1) . ":T{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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