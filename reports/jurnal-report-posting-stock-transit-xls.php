<?php
ini_set('memory_limit', '1024M');
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
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);
// </editor-fold>

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$whereProperty = '';
$whereProperty2 = '';
$invoiceDetailId = ($_POST['invoiceDetailId']);
$paymentId = ($_POST['paymentId']);
$transactionId = ($_POST['transactionId']);
$contractId = ($_POST['contractId']);
$kodeMutasi = ($_POST['kodeMutasi']);

// <editor-fold defaultstate="collapsed" desc="Query">

//MUTASI
$sqlMutasi = "SELECT mh.* from mutasi_header mh where mh.mutasi_header_id = {$kodeMutasi}";
$resultMutasi = $myDatabase->query($sqlMutasi, MYSQLI_STORE_RESULT);
if ($resultMutasi !== false && $resultMutasi->num_rows > 0) {
    while ($rowMutasi = $resultMutasi->fetch_object()) {
        $mutasiCode = $rowMutasi->kode_mutasi;
    }
}

//INVOICE
$sql1 = "SELECT gl.*,  DATE_FORMAT(i.input_date, '%Y-%m-%d') AS input_date, i.invoice_no, gv.general_vendor_name, 
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name
FROM general_ledger gl 
LEFT JOIN invoice_detail id ON gl.invoice_id = id.invoice_detail_id
LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id 
LEFT JOIN invoice i ON id.invoice_id = i.invoice_id
WHERE  gl.amount > 0 AND general_ledger_module = 'INVOICE DETAIL' AND gl.invoice_id IN ($invoiceDetailId)";
$resultInvoice = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);

//PAYMENT
$sql2 = "SELECT gl.*, b.bank_type, p.payment_type, p.payment_no, date_format(p.payment_date, '%Y-%m-%d') as payment_date,
(SELECT `account_no` FROM account a WHERE a.account_id = gl.account_id) AS account_no, 
(SELECT `account_name` FROM account a WHERE a.account_id = gl.account_id) AS account_name 
FROM general_ledger gl 
left join payment p on gl.payment_id = p.payment_id
left join bank b on b.bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id)
WHERE gl.amount > 0 AND general_ledger_module = 'PAYMENT' AND gl.payment_id in ($paymentId)";
$resultPayment = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

//TRANSACTION NOTIM
$sql3 = "SELECT gl.*, DATE_FORMAT(t.unloading_date, '%Y-%m-%d') AS unloading_date, t.slip_no,
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name
FROM general_ledger gl LEFT JOIN `transaction` t ON gl.transaction_id = t.transaction_id
WHERE  gl.amount > 0 AND general_ledger_module = 'NOTA TIMBANG' AND t.`notim_status`!= 1 AND t.`slip_retur` IS NULL 
AND gl.transaction_id IN ($transactionId)";
$resultTransaction = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);

//CONTRACT
$sql4 = "select gl.*,
(SELECT `account_no` FROM account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM account a WHERE a.account_id = gl.account_id) AS account_name,
(SELECT `contract_no` FROM contract c WHERE c.contract_id = gl.contract_id) AS contract_no, c.po_no,
DATE_FORMAT((SELECT `entry_date` FROM contract c WHERE c.contract_id = gl.contract_id),'%Y-%m-%d') AS contract_date
from contract c 
left join stockpile_contract sc on c.contract_id = sc.contract_id
left join mutasi_contract mc on mc.stockpile_contract_id = sc.stockpile_contract_id
left join mutasi_header mh on mh.mutasi_header_id = mc.mutasi_header_id
left join general_ledger gl on gl.`contract_id` = c.contract_id
where mh.mutasi_header_id = {$kodeMutasi} and gl.`general_ledger_module` = 'CONTRACT' and c.langsir = 0";
$resultContract = $myDatabase->query($sql4, MYSQLI_STORE_RESULT);



//STOCK TRANSIT
$sql5 = "SELECT gl.*, st.kode_stock_transit, mh.kode_mutasi,
(SELECT `account_no` FROM account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM account a WHERE a.account_id = gl.account_id) AS account_name,
(SELECT `contract_no` FROM contract c WHERE c.contract_id = gl.contract_id) AS contract_no,
DATE_FORMAT((SELECT `entry_date` FROM contract c WHERE c.contract_id = gl.contract_id),'%Y-%m-%d') AS contract_date
FROM contract c 
LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id
LEFT JOIN mutasi_contract mc ON mc.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN stock_transit st ON sc.stockpile_contract_id = st.stockpile_contract_id
LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = mc.mutasi_header_id
LEFT JOIN general_ledger gl ON gl.`contract_id` = c.contract_id
WHERE mh.mutasi_header_id = {$kodeMutasi} AND gl.`general_ledger_module` = 'STOCK TRANSIT' AND c.langsir = 0
UNION ALL
SELECT gl.*, st.kode_stock_transit, mh.kode_mutasi,
(SELECT `account_no` FROM account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM account a WHERE a.account_id = gl.account_id) AS account_name,
(SELECT `contract_no` FROM contract c WHERE sh.shipment_no = TRIM(TRAILING '-1' FROM c.contract_no)) AS contract_no,
DATE_FORMAT((SELECT `entry_date` FROM contract c WHERE sh.shipment_no = TRIM(TRAILING '-1' FROM c.contract_no)),'%Y-%m-%d') AS contract_date
FROM contract c 
LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id
LEFT JOIN mutasi_contract mc ON mc.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN stock_transit st ON sc.stockpile_contract_id = st.stockpile_contract_id
LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = mc.mutasi_header_id
LEFT JOIN shipment sh ON sh.shipment_no = TRIM(TRAILING '-1' FROM c.contract_no)
LEFT JOIN `transaction` t ON t.`shipment_id` = sh.shipment_id
LEFT JOIN general_ledger gl ON gl.`transaction_id` = t.transaction_id
WHERE mh.mutasi_header_id = {$kodeMutasi} AND t.`notim_status`!= 1 AND t.`slip_retur` IS NULL AND c.langsir = 1
";
$resultStockTransit = $myDatabase->query($sql5, MYSQLI_STORE_RESULT);
//</editor-fold>

$fileName = "Jurnal Report Posting Transit " . '- ' . $mutasiCode . ' - ' . str_replace(" ", "-", $_SESSION['userName']) . " " . $currentDate . ".xls";
$onSheet = 0;
$lastColumn = "H";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1; //row pertama -> selanjutnya akan increment ke bawah
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "JURNAL REPORT POSTING STOCK TRANSIT");

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Kode Mutasi = {$mutasiCode}");

//Contract
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "CONTRACT");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "PO No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);

$no = 1;
while ($rowContract = $resultContract->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowContract->contract_date);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContract->contract_no);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowContract->po_no);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($rowContract->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("F{$rowActive}")->setValueExplicit($rowContract->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $debitAmount = 0;
    if ($rowContract->general_ledger_type == 1) {
        $debitAmount = $rowContract->amount;
    } elseif ($rowContract->general_ledger_type == '') {
        $debitAmount = $rowContract->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if ($rowContract->general_ledger_type == 2) {
        $creditAmount = $rowContract->amount;
    } elseif ($rowContract->general_ledger_type == '') {
        $creditAmount = $rowContract->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $creditAmount);
    $no++;
}

//Stock Transit
$rowActive++;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "STOCK TRANSIT");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Contract No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Stock Transit Code");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);

$no = 1;
while ($rowStockTransit = $resultStockTransit->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowStockTransit->contract_date);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowStockTransit->contract_no);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowStockTransit->kode_stock_transit);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($rowStockTransit->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("F{$rowActive}")->setValueExplicit($rowStockTransit->account_name, PHPExcel_Cell_DataType::TYPE_STRING);

    $debitAmount = 0;
    if ($rowStockTransit->general_ledger_type == 1) {
        $debitAmount = $rowStockTransit->amount;
    } elseif ($rowStockTransit->general_ledger_type == '') {
        $debitAmount = $rowStockTransit->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if ($rowStockTransit->general_ledger_type == 2) {
        $creditAmount = $rowStockTransit->amount;
    } elseif ($rowStockTransit->general_ledger_type == '') {
        $creditAmount = $rowStockTransit->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $creditAmount);
    $no++;
}

//Invoice Detail
$rowActive++;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "HNVOICE DETAIL");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Hnvoice No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);

$no = 1;
while ($rowInvoice = $resultInvoice->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowInvoice->input_date);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowInvoice->general_vendor_name);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowInvoice->invoice_no);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($rowInvoice->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("F{$rowActive}")->setValueExplicit($rowInvoice->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $debitAmount = 0;
    if ($rowInvoice->general_ledger_type == 1) {
        $debitAmount = $rowInvoice->amount;
    } elseif ($rowInvoice->general_ledger_type == '') {
        $debitAmount = $rowInvoice->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if ($rowInvoice->general_ledger_type == 2) {
        $creditAmount = $rowInvoice->amount;
    } elseif ($rowInvoice->general_ledger_type == '') {
        $creditAmount = $rowInvoice->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $creditAmount);
    $no++;
}

//Payment
$rowActive++;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "PAYMENT");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Payment No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);

$no = 1;
while ($rowPayment = $resultPayment->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowPayment->payment_date);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowPayment->payment_no);
    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($rowPayment->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($rowPayment->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $debitAmount = 0;
    if ($rowPayment->general_ledger_type == 1) {
        $debitAmount = $rowPayment->amount;
    } elseif ($rowPayment->general_ledger_type == '') {
        $debitAmount = $rowPayment->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if ($rowPayment->general_ledger_type == 2) {
        $creditAmount = $rowPayment->amount;
    } elseif ($rowPayment->general_ledger_type == '') {
        $creditAmount = $rowPayment->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $creditAmount);
    $no++;
}

//Transaction
$rowActive++;
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "TRANSACTION");

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Slip No");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);

$no = 1;
while ($rowTransaction = $resultTransaction->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowTransaction->unloading_date);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowTransaction->slip_no);
    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($rowTransaction->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($rowTransaction->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $debitAmount = 0;
    if ($rowTransaction->general_ledger_type == 1) {
        $debitAmount = $rowTransaction->amount;
    } elseif ($rowTransaction->general_ledger_type == '') {
        $debitAmount = $rowTransaction->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if ($rowTransaction->general_ledger_type == 2) {
        $creditAmount = $rowTransaction->amount;
    } elseif ($rowTransaction->general_ledger_type == '') {
        $creditAmount = $rowTransaction->amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $creditAmount);
    $no++;
}


$bodyRowEnd = $rowActive;
// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("I"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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