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
$period = $myDatabase->real_escape_string($_POST['period']);
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND a.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND a.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND a.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}

if ($period != '' && $period != '') {
    $period = $_POST['period'];
 
	$whereProperty .= " AND a.period = REPLACE(STR_TO_DATE('{$period}', '%m/%Y'),'-00','') ";
	//echo $whereProperty;
}

$sql = "SELECT a.* FROM (SELECT
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
     WHEN p.vendor_id IS NOT NULL THEN 'CURAH'
     WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
     WHEN p.sales_id IS NOT NULL THEN 'SALES'
     WHEN p.general_vendor_id IS NOT NULL THEN 'LOADING/UMUM/HO'
     ELSE 'INTERNAL TRANSFER' END AS data_source,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%Y-%m')
     WHEN p.sales_id IS NOT NULL THEN DATE_FORMAT(sl.sales_date, '%Y-%m')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.payment_date, '%Y-%m')
     ELSE DATE_FORMAT(p.payment_date, '%Y-%m') END AS period,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.vendor_id IS NOT NULL THEN DATE_FORMAT(c.entry_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%Y-%m-%d')
     WHEN p.sales_id IS NOT NULL THEN sl.sales_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.payment_date
     ELSE p.payment_date END AS transaction_date,
p.payment_id, p.payment_no, p.payment_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.contract_no
     WHEN p.invoice_id IS NOT NULL THEN ic.contract_no
     ELSE c1.contract_no END AS contract_no,
     s.stockpile_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(tamount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
     WHEN p.general_vendor_id IS NOT NULL AND p.payment_type = 1 THEN ((p.amount_converted + p.ppn_amount_converted) - p.pph_amount_converted) * -1
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted * 1.1
     ELSE '' END AS original_amount_converted,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
     ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v.npwp_name
     WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
     ELSE '' END AS npwp_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.tax_invoice
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_tax
     WHEN p.general_vendor_id IS NOT NULL THEN p.tax_invoice
     ELSE '' END AS tax_invoice,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.tax_date, '%Y-%m-%d')
     WHEN p.general_vendor_id IS NOT NULL THEN DATE_FORMAT(p.invoice_date, '%Y-%m-%d')
     ELSE '' END AS tax_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted - p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL AND SUBSTRING(invoice_tax, 1, 3) = 040 THEN (SELECT ROUND((SUM(amount_converted)*0.1),0) FROM invoice_detail WHERE invoice_id = p.invoice_id AND ppn > 0)
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.amount_converted) - (SELECT COALESCE(SUM(amount_converted),0) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id) FROM invoice_detail id WHERE id.invoice_id = p.invoice_id AND id.ppn > 0)
     WHEN p.general_vendor_id IS NOT NULL AND p.payment_type = 1 THEN p.amount_converted * -1
     WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted - p.ppn_amount_converted
     ELSE '' END AS dpp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount_converted
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(id.ppn_converted) - (SELECT COALESCE(SUM(ppn_converted),0) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id) FROM invoice_detail id WHERE id.invoice_id = p.invoice_id AND id.ppn > 0)
     WHEN p.general_vendor_id IS NOT NULL AND p.payment_type = 1 THEN p.ppn_amount_converted * -1
     WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount_converted
     ELSE '' END AS ppn,
CASE WHEN p.payment_location = 0 THEN 'HOF'
     ELSE s.stockpile_code END AS payment_location2,
	 b.bank_code, cur.currency_code, b.bank_type, p.payment_method, p.payment_status,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN sc.quantity
     WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(qty) FROM invoice_detail WHERE invoice_id = p.invoice_id)
     WHEN p.general_vendor_id IS NOT NULL THEN p.qty
     ELSE '' END AS qty,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.invoice_no
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_no
     WHEN p.general_vendor_id IS NOT NULL THEN p.invoice_no
     ELSE p.invoice_no END AS invoice_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.invoice_date
     WHEN p.invoice_id IS NOT NULL THEN i.invoice_date
     WHEN p.general_vendor_id IS NOT NULL THEN p.invoice_date
     ELSE '' END AS invoice_date
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON v.vendor_id = c.vendor_id
LEFT JOIN vendor vc ON vc.vendor_id = p.vendor_id
LEFT JOIN sales sl ON sl.sales_id = p.sales_id
LEFT JOIN customer cs ON cs.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN stockpile_contract isc ON isc.stockpile_contract_id = i.po_id
LEFT JOIN contract ic ON isc.contract_id = ic.contract_id
LEFT JOIN stockpile_contract sc1 ON sc1.stockpile_contract_id = p.stockpile_contract_id_2
LEFT JOIN contract c1 ON sc1.contract_id = c1.contract_id
LEFT JOIN bank b ON p.bank_id = b.bank_id
LEFT JOIN currency cur ON cur.currency_id = p.currency_id
WHERE 1=1) a WHERE a.payment_method = 1 AND a.payment_status = 0 AND a.ppn <> 0  {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "Arus_Uang" . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "Y";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Arus Uang");

$rowActive++;
$rowMerge = $rowActive + 1;
$headerRow = $rowActive;

$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "NO");
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PKP");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "MASA");
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "NPWP");
$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "NO SERI FAKTUR PAJAK");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Tanggal");
$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "SOURCE MODUL");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "QUANTITY ORDER");
$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "NO. INVOICE / KWITANSI");
$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "TGL INVOICE");
$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:N{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "NO. SURAT JALAN");
$objPHPExcel->getActiveSheet()->mergeCells("O{$rowActive}:O{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "TUJUAN PENGIRIMAN");
$objPHPExcel->getActiveSheet()->mergeCells("P{$rowActive}:S{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "PEMBAYARAN");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowMerge}", "KAS");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowMerge}", "TANGGAL REKENING KORAN");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowMerge}", "NILAI");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowMerge}", "SELISIH");
$objPHPExcel->getActiveSheet()->mergeCells("T{$rowActive}:Y{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "STATUS");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowMerge}", "KETERANGAN SELISIH BAYAR");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowMerge}", "SPT LAWAN TRANSAKSI");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowMerge}", "BUKTI TRANSFER");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowMerge}", "INVOICE");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowMerge}", "FAKTUR PAJAK");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowMerge}", "KONTRAK");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);
// </editor-fold>
$rowActive = $rowMerge;
// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while($row = $result->fetch_object()) {
  
  $rowActive++;

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
	
	$dpp_total = $row->dpp + $row->ppn;
	$total = $dpp_total - $row->original_amount_converted;
	
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->npwp_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->period, PHPExcel_Cell_DataType::TYPE_STRING);
	//$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->period);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $row->tax_invoice, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->tax_date);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->data_source);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->qty);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->dpp);
	$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->ppn);
	$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $dpp_total);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->invoice_no);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->invoice_date);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", $voucherNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->payment_date);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->original_amount_converted);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $total);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", '');
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", '');

    $no++;
    $ppn = $ppn + $row->ppn;
	$dpp = $dpp + $row->dpp;
	$dppTotal = $dppTotal + $dpp_total;
	$originalAmount = $originalAmount + $row->original_amount_converted;
	$grandTotal = $grandTotal + $total;
	 
}
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:H{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:Y{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $dpp);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $ppn);
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $dppTotal);
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $originalAmount);
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $grandTotal);

//$bodyRowEnd = $rowActive;

//$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:Z{$rowActive}")->applyFromArray($styleArray2);


$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Y"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
//$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
	//$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("YYYY MMM");
    $objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":M{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("Q" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
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