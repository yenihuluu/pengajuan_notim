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
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);
// </editor-fold>

$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);

$coaNos = $_POST['coaNos'];

$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND gl.gl_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND gl_date BETWEEN '2021-01-01' AND DATE_ADD(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), INTERVAL -1 DAY) ";
    
	//$whereProperty2 .= " AND ru.gl_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND gl.gl_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	$whereProperty3 .= " AND gl_date BETWEEN '2021-01-01' AND DATE_ADD(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), INTERVAL -1 DAY) ";
    
	//$whereProperty2 .= " AND ru.gl_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND gl.gl_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
		
	//$whereProperty2 .= " AND ru.gl_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	
    $periodFull = "To " . $periodTo . " ";
}


if($coaNos != '') {
     $whereProperty .= " AND gl.account_no IN ({$coaNos})";
	 $whereProperty2 .= " AND account_no IN ({$coaNos})";
	 $whereProperty3 .= " AND account_no IN ({$coaNos})";
	// $whereProperty2 .= " AND ru.account_no IN ({$coaNos})";
    
}

$str = $_POST['periodFrom'];
$date = DateTime::createFromFormat('d/m/Y', $str);
$jurnalYear = $date->format('Y'); 

if($jurnalYear >= 2021){
$sqlp2 = "SELECT SUM(balance) AS balance FROM gl_report_saldo WHERE 1=1 {$whereProperty2}";
                $resultp2 = $myDatabase->query($sqlp2, MYSQLI_STORE_RESULT);
                if ($resultp2->num_rows == 1) {
                    while ($rowp2 = $resultp2->fetch_object()) {
                        
						$saldoAwal = $rowp2->balance;
						//$creditAmountSaldo = $rowp2->creditAmount;

                    }
                }
			
}else{
	$saldoAwal = 0;
	//$creditAmountSaldo = 0;
}

$sqlDate = "SELECT DATE_FORMAT(DATE_ADD(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), INTERVAL -1 DAY),'%Y') AS tahun";
$resultDate = $myDatabase->query($sqlDate, MYSQLI_STORE_RESULT);
                if ($resultDate->num_rows == 1) {
                    while ($rowDate = $resultDate->fetch_object()) {
                        
						$tahun = $rowDate->tahun;
	
                    }
                }

if($tahun >= 2021){
$sqlp2 = "SELECT SUM(debitAmount - creditAmount) AS balanceDetail FROM gl_report WHERE 1=1 AND regenerate = 0 {$whereProperty3}";
                $resultp2 = $myDatabase->query($sqlp2, MYSQLI_STORE_RESULT);
                if ($resultp2->num_rows == 1) {
                    while ($rowp2 = $resultp2->fetch_object()) {
                        
						$balanceDetail = $rowp2->balanceDetail;
						//$creditAmountTotal = $rowp2->creditAmount;

                    }
                }
			
}else{
	$balanceDetail = 0;
	//$creditAmountTotal = 0;
}

$totalBalance = $saldoAwal + $balanceDetail;
//$saldoAwalCredit = $creditAmountSaldo + $creditAmountTotal;


$sql = "SELECT gl.*,
CASE WHEN gl.debitAmount > 0 THEN gl.debitAmount ELSE -1*creditAmount END AS balance2,
CASE WHEN gl.contract_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id LEFT JOIN contract c ON c.contract_id = sc.contract_id WHERE c.contract_id = gl.contract_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1 )
		WHEN gl.payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = gl.payment_id ) 
		ELSE '' END AS payment_no2,
CASE WHEN (SELECT payment_location FROM payment WHERE payment_id = gl.payment_id ) = 0 THEN 'HOF'
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = (SELECT payment_location FROM payment WHERE payment_id = gl.payment_id )) END AS payment_location2,
CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT bank_code FROM bank WHERE bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id ))
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		ELSE '' END AS bank_code,
CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT currency_code FROM currency WHERE currency_id = (SELECT currency_id FROM payment WHERE payment_id = gl.payment_id))
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)		
		ELSE '' END AS pcur_currency_code,
CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT bank_type FROM bank WHERE bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id )) 
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		ELSE '' END AS bank_type,
(SELECT payment_type FROM payment WHERE payment_id = gl.payment_id ) AS payment_type,
CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code_fc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code_uc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code_hc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type_fc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type_uc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type_hc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code_fc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code_uc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code_hc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_location_fc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_location_uc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_location_hc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no_fc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no_uc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no_hc
FROM gl_report gl WHERE 1=1 AND gl.regenerate = 0 {$whereProperty}
ORDER BY gl.gl_date ASC, gl.jurnal_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "General Ledger Balance" . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "AC";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "GENERAL LEDGER");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
//$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile Contract");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Journal No.");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Source Module");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Method");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Supplier Name");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Contract No.");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Slip No.");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Original Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Tax Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Cheque No.");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Remarks");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Price");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Kurs");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Credit");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Balance");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "Payment Voucher");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "Payment Voucher OA");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Payment Voucher OB");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Payment Voucher Handling");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>
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
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $totalBalance);
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "");

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$balance = $totalBalance;
$no = 1;
while($row = $result->fetch_object()) {

$balance = $balance + $row->balance2;
$rowActive++;
    if($oldModule != '' && $oldModule == 'PAYMENT') {
        if($oldPaymentId != $row->payment_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
    } elseif($oldModule != '' && $oldModule == 'CONTRACT') {
        if($oldContractId != $row->contract_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }

    } elseif($oldModule != '' && $oldModule == 'NOTA TIMBANG') {
        if($oldTransactionId != $row->transaction_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
    } elseif($oldModule != '' && $oldModule == 'INVOICE DETAIL') {
        if($oldInvoiceId != $row->invoice_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
    } elseif($oldModule != '' && $oldModule == 'RETURN INVOICE') {
        if($oldInvoiceId != $row->invoice_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
    } elseif($oldModule != '' && $oldModule == 'JURNAL MEMORIAL') {
        if($oldJurnalId != $row->jurnal_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
    } elseif($oldModule != '' && $oldModule == 'PETTY CASH') {
        if($oldCashId != $row->cash_id) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
		if($newGl == '' && $newPaymentNo != $row->payment_no) {
            if($boolColor) {
                $boolColor = false;
            } else {
                $boolColor = true;
            }
        }
    }
	
	$voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
		

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
		
         //$voucherNo = $row->payment_no;
		 $paymentNo = $voucherCode .' # '. $row->payment_no2;
	
	if($row->transaction_id != '' && $row->payment_no_fc != '' ) {
        
		
		$voucherCode = $row->payment_location_fc .'/'. $row->bank_code_fc .'/'. $row->pcur_currency_code_fc;
		

        if($row->bank_type_fc == 1) {
            $voucherCode .= ' - B';
        } elseif($row->bank_type_fc == 2) {
            $voucherCode .= ' - P';
        } elseif($row->bank_type_fc == 3) {
            $voucherCode .= ' - CAS';
        }

        if($row->bank_type_fc != 3) {
            if($row->payment_type == 1) {
                $voucherCode .= 'RV';
            } else {
                $voucherCode .= 'PV';
            }
        }
		
         //$voucherNo = $row->payment_no;
		 $paymentNoFc = $voucherCode .' # '. $row->payment_no_fc; 
    }
	
	if($row->transaction_id != '' && $row->payment_no_uc != '' ) {
        
		
		$voucherCode = $row->payment_location_uc .'/'. $row->bank_code_uc .'/'. $row->pcur_currency_code_uc;
		

        if($row->bank_type_uc == 1) {
            $voucherCode .= ' - B';
        } elseif($row->bank_type_uc == 2) {
            $voucherCode .= ' - P';
        } elseif($row->bank_type_uc == 3) {
            $voucherCode .= ' - CAS';
        }

        if($row->bank_type_uc != 3) {
            if($row->payment_type == 1) {
                $voucherCode .= 'RV';
            } else {
                $voucherCode .= 'PV';
            }
        }
		
         //$voucherNo = $row->payment_no;
		 $paymentNoUc = $voucherCode .' # '. $row->payment_no_uc; 
    }
	
	if($row->transaction_id != '' && $row->payment_no_hc != '' ) {
        
		
		$voucherCode = $row->payment_location_hc .'/'. $row->bank_code_hc .'/'. $row->pcur_currency_code_hc;
		

        if($row->bank_type_hc == 1) {
            $voucherCode .= ' - B';
        } elseif($row->bank_type_hc == 2) {
            $voucherCode .= ' - P';
        } elseif($row->bank_type_hc == 3) {
            $voucherCode .= ' - CAS';
        }

        if($row->bank_type_hc != 3) {
            if($row->payment_type == 1) {
                $voucherCode .= 'RV';
            } else {
                $voucherCode .= 'PV';
            }
        }
		
         //$voucherNo = $row->payment_no;
		 $paymentNoHc = $voucherCode .' # '. $row->payment_no_hc; 
    }
	
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
   //$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->stockpile_name);
   $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->stockpile);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->gl_date);
    /*$voucherNo =  "";
	$paymentNo =  ""; 
	if($row->contract_id != '' && $row->general_ledger_module == 'CONTRACT ADJUSTMENT'){
		$voucherNo = $row->payment_no;
		$paymentNo = 'JV-PO-' .$row->payment_no2; 
	}elseif($row->contract_id != '' && $row->general_ledger_module != 'CONTRACT ADJUSTMENT'){
		$voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
		

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
		
		
		 $voucherNo = $row->payment_no;
         $paymentNo = $voucherCode .' # '. $row->payment_no2;
	}
	
    elseif($row->payment_id != '') {
        
		
		$voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
		

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
		 $paymentNo = $voucherCode .' # '. $row->payment_no2; 
    
	}elseif($row->invoice_id != '') {
        
		
		$voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
		

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
		
         $voucherNo = $row->payment_no;
		 $paymentNo = $voucherCode .' # '. $row->payment_no2; 
    }else{
		$voucherNo =  $row->payment_no;
		$paymentNo =  $row->payment_no2; 		
	}*/
	
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("D{$rowActive}", $row->jurnal_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->general_ledger_module);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->general_ledger_method);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->general_ledger_transaction_type2);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->supplier_name);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->po_no);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->contract_no);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
	/*
	if($row->payment_id != '' && $row->general_ledger_module == 'PAYMENT' && $row->account_id == 29){
		
$sqlfc="SELECT * FROM (SELECT t.slip_no, t.fc_payment_id, (t.freight_price * t.freight_quantity) AS fc_price FROM 			
					general_ledger gl INNER JOIN `transaction` t ON t.fc_payment_id = gl.payment_id) a 
					WHERE 1=1 AND a.fc_payment_id = {$row->payment_id} and a.fc_price = {$row->amount} GROUP BY a.slip_no";
	$resultfc = $myDatabase->query($sqlfc, MYSQLI_STORE_RESULT);
	while($rowfc = $resultfc->fetch_object()) {	
	*/
	
/*}
}else{
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);		
}*/
//    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->invoice_no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("M{$rowActive}", $row->invoice_no_2, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->tax_invoice);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->cheque_no);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->shipment_code);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->quantity);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->price);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("T{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	//$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->account_no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("U{$rowActive}", $row->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->exchange_rate);
	/*$debitAmount = 0;
    if($row->general_ledger_type == 1) {
        $debitAmount = $debit_amount; 
    }elseif($row->general_ledger_type == ''){
		$debitAmount = $debit_amount;
	}*/
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->debitAmount);
    /*$creditAmount = 0;
    if($row->general_ledger_type == 2) {
        $creditAmount = $credit_amount;
    }elseif($row->general_ledger_type == ''){
		$creditAmount = $credit_amount;
	}*/
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->creditAmount);
	$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $balance);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("Z{$rowActive}", $paymentNo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $paymentNoFc);
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $paymentNoUc);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $paymentNoHc);
	
    $no++;
    
    if($boolColor) {
        $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:AC{$rowActive}")->applyFromArray($styleArray4b);
    }
    
    $oldModule = $row->general_ledger_module;
    $oldPaymentId = $row->payment_id;
    $oldTransactionId = $row->transaction_id;
    $oldContractId = $row->contract_id;
	$oldInvoiceId = $row->invoice_id;
	$oldJurnalId = $row->jurnal_id;
	$oldCashId = $row->cash_id;
	$newGL = $row->general_ledger_id;
	$newPaymentNo = $row->payment_no;
	
}
$bodyRowEnd = $rowActive;

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("V" . ($headerRow + 1) . ":V{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//$objPHPExcel->getActiveSheet()->getStyle("T" . ($headerRow + 1) . ":T{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
$objPHPExcel->getActiveSheet()->getStyle("W" . ($headerRow + 1) . ":W{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("X" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("Y" . ($headerRow + 1) . ":Y{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
/*$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
$cacheSettings = array( ' memoryCacheSize ' => '8MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);*/

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->setPreCalculateFormulas(false);
$objWriter->save('php://output');
// </editor-fold>
exit();