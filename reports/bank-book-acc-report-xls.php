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


$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'DOWNLOAD BANK BOOK REPORT',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
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
// </editor-fold>

$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
$bankId = $myDatabase->real_escape_string($_POST['bankId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$bankName = '';
$bankType = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($bankId != '') {
    $sql = "SELECT b.*, "
            . "CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full "
            . "FROM bank b "
            . "INNER JOIN currency cur "
            . "ON cur.currency_id = b.currency_id "
            . "WHERE b.bank_id = {$bankId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();
    
    //$whereProperty .= " AND IF(b.bank_type = 2, b.bank_id = {$bankId}, (b.bank_id = {$bankId} OR (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id)) ";
	//$whereProperty .= " AND IF(b.bank_type = 1, b.bank_id = {$bankId}, (b.bank_id = {$bankId} OR (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id)) ";
    $whereProperty .= " AND (p.bank_id = {$bankId} OR (SELECT account_id FROM `bank` where bank_id = {$bankId}) = p.account_id) ";
    //$wherePropertyBank .= " AND b.bank_id = {$bankId} ";
    $bank_type = $row->bank_type;
    $bankName = $row->bank_full . " ";
}
if($periodFrom != '' && $periodTo != '') {
    //$periodFrom = $_POST['periodFrom'];
    //$periodTo = $_POST['periodTo'];
   /*$wherePaymentDate .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$sumProperty .= " AND IF(p.payment_type = 1, p.payment_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), p.payment_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
   $boolBalanceBefore = true;*/
   $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$whereProperty3 .= " AND p.payment_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
}/*elseif($periodFrom == '' && $periodTo != '') {
    
    $wherePaymentDate .= " AND p.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} */

$date = explode('/', $periodFrom);
$month = (int)$date[1];
$day   = $date[0];
$year  = (int)$date[2];



  $sqlOB = "SELECT stockpile_id FROM bank WHERE bank_id = {$bankId}";
  			$resultOB = $myDatabase->query($sqlOB, MYSQLI_STORE_RESULT);
                      if($resultOB->num_rows == 1) {
                          $rowOB = $resultOB->fetch_object();
                          $stockpileID = $rowOB->stockpile_id;
                      }
  if  ($year>=2019){
    $sqlOB = "SELECT cutoff_bal FROM bank WHERE bank_id = {$bankId}";
    			$resultOB = $myDatabase->query($sqlOB, MYSQLI_STORE_RESULT);
                        if($resultOB->num_rows == 1) {
                            $rowOB = $resultOB->fetch_object();
                            $openingBalance = $rowOB->cutoff_bal;
                        }

  }else {
    $sqlOB = "SELECT opening_balance FROM bank WHERE bank_id = {$bankId}";
    			$resultOB = $myDatabase->query($sqlOB, MYSQLI_STORE_RESULT);
                        if($resultOB->num_rows == 1) {
                            $rowOB = $resultOB->fetch_object();
                            $openingBalance = $rowOB->opening_balance;
                        }
  }

/*update Alan
$sqlOB = "SELECT opening_balance FROM bank WHERE bank_id = {$bankId}";
			$resultOB = $myDatabase->query($sqlOB, MYSQLI_STORE_RESULT);
                    if($resultOB->num_rows == 1) {
                        $rowOB = $resultOB->fetch_object();
                        $openingBalance = $rowOB->opening_balance;
                    }
					
					
//$no = 1;
$sqlBalance = "SELECT 
ROUND(SUM(CASE 
WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1) / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2018-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2018-01-01' THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2018-01-01'  THEN p.amount_journal
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate
WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01'  THEN (p.amount_journal * -1)/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2018-01-01'  THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01'  THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
ELSE 0 END),2) AS balance
FROM payment p WHERE p.payment_status = 0 {$whereProperty} {$whereProperty3} 
ORDER BY p.payment_date ASC, p.entry_date ASC ";
			$resultBalance = $myDatabase->query($sqlBalance, MYSQLI_STORE_RESULT);
			if($resultBalance->num_rows > 0) {
			while($rowBalance = $resultBalance->fetch_object()) {
				//$mutasi = $rowBalance->balance;
				$mutasi = $rowBalance->balance;
				//echo $balanceBefore ;
			}
				//$no++;
			}
			
			*/
			
			//Update Surya ---------------------------------------------
    if  ($stockpileID==10 && $year>=2019){
      $sqlBalance = "SELECT
      SUM(CASE
      WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2019-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-01-01' THEN (p.amount_journal * -1) / p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2019-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-01-01' THEN p.amount_journal / p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2019-01-01'  THEN p.amount_journal
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2019-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2019-01-01'  THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_method = 2 AND p.payment_date >= '2019-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal))/ p.exchange_rate
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-01-01' THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2019-01-01'  THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date >= '2019-01-01'  THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
      ELSE 0 END) AS balance
      FROM payment p WHERE p.payment_status = 0 {$whereProperty} {$whereProperty3}
      ORDER BY p.payment_date ASC, p.entry_date ASC ";
      			$resultBalance = $myDatabase->query($sqlBalance, MYSQLI_STORE_RESULT);
      			if($resultBalance->num_rows > 0) {
      			while($rowBalance = $resultBalance->fetch_object()) {
      				//$mutasi = $rowBalance->balance;
      				$mutasi = $rowBalance->balance;
      				//echo $balanceBefore ;
      			}
      				//$no++;
      			}
    }elseif ($stockpileID<>10 && $year >=2019) {
      $sqlBalance = "SELECT
      SUM(CASE
      WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2019-04-01' THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1),2)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-04-01' THEN ROUND((p.amount_journal * -1),2) / p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2019-04-01'  THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1),2)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-04-01' THEN ROUND(p.amount_journal,2) / p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2019-04-01'  THEN ROUND(p.amount_journal,2)
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2019-04-01' THEN ROUND((p.amount_journal * -1),2)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2019-04-01'  THEN ROUND((p.amount_journal * -1),2)/ p.exchange_rate
      WHEN p.payment_method = 2 AND p.payment_date >= '2019-04-01'  THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1),2)/ p.exchange_rate
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-04-01' THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate

      WHEN p.payment_type = 1 AND p.payment_date > '2019-04-01' AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1),2)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2019-04-01'  THEN ROUND((p.amount_journal - p.pph_journal),2)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date >= '2019-04-01'  THEN ROUND(((p.amount_journal + p.ppn_journal) - p.pph_journal),2)/ p.exchange_rate
      ELSE 0 END) AS balance
      FROM payment p WHERE p.payment_status = 0 {$whereProperty} {$whereProperty3}
      ORDER BY p.payment_date ASC, p.entry_date ASC";
      			$resultBalance = $myDatabase->query($sqlBalance, MYSQLI_STORE_RESULT);
      			if($resultBalance->num_rows > 0) {
      			while($rowBalance = $resultBalance->fetch_object()) {
      				//$mutasi = $rowBalance->balance;
      				$mutasi = $rowBalance->balance;
      				//echo $balanceBefore ;
      			}
      				//$no++;
      			}
    }else {
      $sqlBalance = "SELECT
      SUM(CASE
      WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1) / p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2018-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2018-01-01' THEN p.amount_journal / p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2018-01-01'  THEN p.amount_journal
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01'  THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal))/ p.exchange_rate
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2018-01-01'  THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01'  THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
      ELSE 0 END) AS balance
      FROM payment p WHERE p.payment_status = 0 {$whereProperty} {$whereProperty3}
      ORDER BY p.payment_date ASC, p.entry_date ASC";
      			$resultBalance = $myDatabase->query($sqlBalance, MYSQLI_STORE_RESULT);
      			if($resultBalance->num_rows > 0) {
      			while($rowBalance = $resultBalance->fetch_object()) {
      				//$mutasi = $rowBalance->balance;
      				$mutasi = $rowBalance->balance;
      				//echo $balanceBefore ;
      			}
      				//$no++;
      			}
    }
//----------------------------------
				$balanceBefore = $openingBalance + $mutasi;
				$balance = $balanceBefore;

				
$sql = "SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = p.payment_location) END AS payment_location, 
(SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code,
(SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
(SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code, 
p.payment_date, p.payment_no, p.payment_type,
CASE WHEN p.stockpile_contract_id IS NOT NULL 
THEN (SELECT v1.vendor_name FROM vendor v1 LEFT JOIN contract c1 ON c1.vendor_id = v1.vendor_id LEFT JOIN stockpile_contract sc1 ON sc1.contract_id = c1.contract_id WHERE sc1.stockpile_contract_id = p.stockpile_contract_id)
WHEN p.invoice_id IS NOT NULL
THEN (SELECT gv1.general_vendor_name FROM general_vendor gv1 LEFT JOIN invoice_detail id1 ON gv1.general_vendor_id = id1.general_vendor_id WHERE id1.invoice_id = p.invoice_id LIMIT 1)
WHEN p.payment_cash_id IS NOT NULL
THEN (SELECT gv2.general_vendor_name FROM general_vendor gv2 LEFT JOIN payment_cash pc1 ON pc1.general_vendor_id = gv2.general_vendor_id WHERE pc1.payment_id = p.payment_id LIMIT 1)
WHEN p.vendor_id IS NOT NULL
THEN (SELECT v2.vendor_name FROM vendor v2 WHERE v2.vendor_id = p.vendor_id)
WHEN p.sales_id IS NOT NULL
THEN (SELECT cs.customer_name FROM customer cs LEFT JOIN sales sl ON sl.customer_id = cs.customer_id WHERE sl.sales_id = p.sales_id)
WHEN p.freight_id IS NOT NULL
THEN (SELECT freight_supplier FROM freight WHERE freight_id = p.freight_id)
WHEN p.vendor_handling_id IS NOT NULL
THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = p.vendor_handling_id)
WHEN p.labor_id IS NOT NULL
THEN (SELECT labor_name FROM labor WHERE labor_id = p.labor_id)
WHEN p.general_vendor_id IS NOT NULL
THEN (SELECT general_vendor_name FROM general_vendor WHERE general_vendor_id = p.general_vendor_id)  
ELSE (SELECT vpc.vendor_name FROM vendor_pettycash vpc LEFT JOIN account a ON a.account_no = vpc.account_no WHERE a.account_id = p.account_id limit 1) 
END AS vendor_name,
CASE WHEN p.invoice_id IS NOT NULL 
THEN (SELECT GROUP_CONCAT(invoice_no, ' - ', invoice_no2) FROM invoice WHERE invoice_id = p.invoice_id)
ELSE p.invoice_no END AS invoice_no,
CASE WHEN p.invoice_id IS NOT NULL 
THEN (SELECT invoice_tax FROM invoice WHERE invoice_id = p.invoice_id)
ELSE p.tax_invoice END AS tax_invoice,
CASE WHEN p.invoice_id IS NOT NULL 
THEN (SELECT invoice_date FROM invoice WHERE invoice_id = p.invoice_id)
WHEN p.invoice_date = '0000-00-00' THEN '-'
ELSE p.invoice_date END AS invoice_date,
CASE WHEN p.stockpile_contract_id IS NOT NULL
THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN p.invoice_id IS NOT NULL
THEN (SELECT c.po_no FROM contract c LEFT JOIN invoice_detail id ON id.poId = c.contract_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
ELSE (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id_2)
END AS po_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL
THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN p.invoice_id IS NOT NULL
THEN (SELECT c.contract_no FROM contract c LEFT JOIN invoice_detail id ON id.poId = c.contract_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
ELSE (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id_2)
END AS contract_no,
CASE WHEN p.invoice_id IS NOT NULL 
THEN (SELECT DISTINCT(sh.shipment_no) FROM shipment sh LEFT JOIN invoice_detail id ON sh.shipment_id = id.shipment_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
ELSE (SELECT sh.shipment_no FROM shipment sh WHERE sh.shipment_id = p.shipment_id)
END AS shipment_code,
CASE WHEN p.stockpile_contract_id IS NOT NULL
THEN (SELECT c.quantity FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN p.freight_id IS NOT NULL
THEN (SELECT SUM(t.freight_quantity) FROM `transaction` t WHERE t.fc_payment_id = p.payment_id)
WHEN p.sales_id IS NOT NULL
THEN (SELECT quantity FROM sales WHERE sales_id = p.sales_id)
WHEN p.general_vendor_id IS NOT NULL THEN p.qty
ELSE '' END AS qty,
CASE WHEN p.stockpile_contract_id IS NOT NULL
THEN (SELECT c.price_converted FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN p.freight_id IS NOT NULL
THEN (SELECT t.freight_price FROM `transaction` t WHERE t.fc_payment_id = p.payment_id LIMIT 1)
WHEN p.sales_id IS NOT NULL
THEN (SELECT price FROM sales WHERE sales_id = p.sales_id)
WHEN p.general_vendor_id IS NOT NULL THEN p.price
ELSE '' END AS price,
CASE WHEN p.stockpile_contract_id IS NOT NULL
THEN (SELECT s.stockpile_name FROM stockpile s LEFT JOIN stockpile_contract sc ON s.stockpile_id = sc.stockpile_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id)
WHEN p.labor_id IS NOT NULL
THEN (SELECT s.stockpile_name FROM stockpile s LEFT JOIN unloading_cost uc ON uc.stockpile_id = s.stockpile_id LEFT JOIN `transaction` t ON t.unloading_cost_id = uc.unloading_cost_id WHERE t.uc_payment_id = p.payment_id LIMIT 1)
ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.stockpile_location) END AS stockpile_name2,
p.remarks, p.cheque_no,
(SELECT account_no FROM account WHERE account_id = p.account_id) AS account_no,
(SELECT account_name FROM account WHERE account_id = p.account_id) AS account_name,
p.exchange_rate AS kurs,
ROUND(CASE 
WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 2 AND p.amount_journal < 0 THEN 0
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_method = 2 AND p.sales_id IS NOT NULL THEN 0
WHEN p.payment_method = 2 THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
ELSE 0 END,2) AS debit_amount,
ROUND(CASE 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN p.amount_journal 
WHEN p.payment_type = 2 AND p.amount_journal < 0 THEN (p.amount_journal * -1)/ p.exchange_rate
ELSE 0 END,2) AS credit_amount,
ROUND(CASE 
WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1) / p.exchange_rate 
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate 
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_date >= '2018-01-01' THEN p.amount_journal
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate 
WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1 )/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' AND p.sales_id IS NOT NULL THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * 1)/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) = 520100 AND p.payment_date >= '2018-01-01' THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate 
WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01' THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate 
ELSE 0 END,2) AS mutasi,
ROUND(CASE
WHEN p.invoice_id IS NOT NULL AND 
(SELECT invoice_method_detail FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 2 AND
(SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id) < 0 THEN
(SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
WHEN p.invoice_id IS NOT NULL THEN (SELECT REPLACE(SUM(amount_converted),'-','') FROM invoice_detail WHERE invoice_id = p.invoice_id)
WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_method = 2 THEN REPLACE((p.original_amount - p.ppn_amount_converted),'-','')
WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_method = 1 THEN REPLACE((p.amount_converted - p.ppn_amount_converted),'-','')
WHEN p.payment_cash_id IS NOT NULL AND 
(SELECT payment_cash_method FROM payment_cash WHERE payment_id = p.payment_id LIMIT 1) = 2 AND
(SELECT SUM(amount_converted) FROM payment_cash WHERE payment_id = p.payment_id) < 0 THEN
(SELECT SUM(amount_converted) FROM payment_cash WHERE payment_id = p.payment_id)
WHEN p.payment_cash_id IS NOT NULL THEN (SELECT REPLACE(SUM(amount_converted),'-','') FROM payment_cash WHERE payment_id = p.payment_id)
WHEN p.vendor_id IS NOT NULL THEN REPLACE((p.amount_journal - p.ppn_journal),'-','')
WHEN p.sales_id IS NOT NULL THEN REPLACE((p.amount_journal - p.ppn_journal),'-','')
WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN REPLACE((p.amount_journal),'-','')
WHEN p.freight_id IS NOT NULL THEN REPLACE((p.amount_journal - p.ppn_journal + p.pph_journal),'-','')
WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN REPLACE((p.amount_journal),'-','')
WHEN p.vendor_handling_id IS NOT NULL THEN REPLACE((p.amount_journal - p.ppn_journal + p.pph_journal),'-','')
WHEN p.labor_id IS NOT NULL THEN REPLACE((p.amount_journal + p.pph_journal),'-','')
WHEN p.general_vendor_id IS NOT NULL THEN REPLACE((p.amount_journal),'-','')
ELSE REPLACE(p.amount_journal,'-','') END,2) AS dpp,
ROUND(CASE
WHEN p.invoice_id IS NOT NULL THEN (SELECT REPLACE(SUM(ppn_converted),'-','') FROM invoice_detail WHERE invoice_id = p.invoice_id)
WHEN p.stockpile_contract_id IS NOT NULL THEN REPLACE((p.ppn_amount_converted),'-','')
WHEN p.payment_cash_id IS NOT NULL THEN (SELECT REPLACE(SUM(ppn_converted),'-','') FROM payment_cash WHERE payment_id = p.payment_id)
WHEN p.vendor_id IS NOT NULL THEN REPLACE((p.ppn_amount_converted),'-','')
WHEN p.sales_id IS NOT NULL THEN REPLACE((p.ppn_journal),'-','')
WHEN p.freight_id IS NOT NULL THEN REPLACE((p.ppn_journal),'-','')
WHEN p.vendor_handling_id IS NOT NULL THEN REPLACE((p.ppn_journal),'-','')
WHEN p.labor_id IS NOT NULL THEN 0
WHEN p.general_vendor_id IS NOT NULL THEN REPLACE((p.ppn_journal),'-','')
ELSE REPLACE(p.ppn_journal,'-','') END,2) AS ppn,
ROUND(CASE
WHEN p.invoice_id IS NOT NULL THEN (SELECT REPLACE(SUM(pph_converted),'-','') FROM invoice_detail WHERE invoice_id = p.invoice_id)
WHEN p.stockpile_contract_id IS NOT NULL THEN 0
WHEN p.payment_cash_id IS NOT NULL THEN (SELECT REPLACE(SUM(pph_converted),'-','') FROM payment_cash WHERE payment_id = p.payment_id)
WHEN p.vendor_id IS NOT NULL THEN 0
WHEN p.sales_id IS NOT NULL THEN 0
WHEN p.freight_id IS NOT NULL THEN REPLACE((p.pph_journal),'-','')
WHEN p.vendor_handling_id IS NOT NULL THEN REPLACE((p.pph_journal),'-','')
WHEN p.labor_id IS NOT NULL THEN REPLACE((p.pph_journal),'-','')
WHEN p.general_vendor_id IS NOT NULL THEN REPLACE((p.pph_journal),'-','')
ELSE REPLACE(p.pph_journal,'-','') END,2) AS pph,
ROUND(CASE
WHEN p.invoice_id IS NOT NULL THEN (SELECT REPLACE(SUM(amount_converted + ppn_converted - pph_converted),'-','') FROM invoice_detail WHERE invoice_id = p.invoice_id) - REPLACE(p.amount_journal,'-','')
WHEN p.payment_cash_id IS NOT NULL THEN (SELECT REPLACE(SUM(amount_converted + ppn_converted - pph_converted),'-','') FROM payment_cash WHERE payment_id = p.payment_id) - REPLACE(p.amount_journal,'-','')
WHEN p.stockpile_contract_id AND p.payment_method = 1 THEN (p.amount_converted - p.amount_journal)
WHEN p.vendor_id THEN (p.amount_converted - p.amount_journal)
ELSE 0 END,2) AS dp, p.payment_id
FROM payment p
WHERE p.payment_status = 0 {$whereProperty} {$whereProperty2}
ORDER BY p.payment_date ASC, p.entry_date ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//</editor-fold>

$fileName = "ListPaymentTransaction" . $bankName . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "X";

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

if ($bankName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Bank = {$bankName}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
if($bank_type == 1){
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "List Payment Transaction Report");
}else{
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "List Payment Transaction Report");	
}
$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No. Voucher");
//$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Nama Supplier");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Payment To/Receive From");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Tax Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Invoice Date");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Contract No.");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Shipment Code");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Harga /Kg");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Keterangan");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "No. Cek");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "No. Akun");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Nama Akun");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Kurs");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "DP");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Penerimaan");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Balance");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$rowActive++;
//$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "");
//$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Nama Supplier");
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
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $balanceBefore);

if($result->num_rows > 0) {		
while($row = $result->fetch_object()) {
		
$balance = $balance + $row->mutasi;		


    $voucherCode = '';
    if($row->payment_no != '') {
        $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;

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

        $voucherCode .= ' # '. $row->payment_no; 
    }    

$rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $row->payment_date);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $voucherCode);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("C{$rowActive}", $row->vendor_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->invoice_no);
	$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->tax_invoice);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->invoice_date);
	$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->po_no);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->contract_no);
	$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->shipment_code);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->qty);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->unit_price);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->stockpile_name2);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->cheque_no);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("O{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
     $objPHPExcel->getActiveSheet()->setCellValueExplicit("P{$rowActive}", $row->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->kurs);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->dpp);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->pph);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->dp);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->debit_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->credit_amount);
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $balance);
//	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $amount3);

    $no++;
	}
}
$bodyRowEnd = $rowActive;

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
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow + 1) . ":A{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow + 1) . ":F{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("J" . ($headerRow + 1) . ":K{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle("Q" . ($headerRow + 1) . ":X{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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