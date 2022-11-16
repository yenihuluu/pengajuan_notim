<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

//$wherePropertyBank = '';
$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
$bankId = '';
$periodFrom = '';
$periodTo = '';
//$sumProperty = '';
//$balanceBefore = 0;
//$boolBalanceBefore = false;
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'VIEW BANK BOOK REPORT',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if(isset($_POST['bankId']) && $_POST['bankId'] != '') {
    $bankId = $_POST['bankId'];
	
    //$whereProperty .= " AND IF(b.bank_type = 2, b.bank_id = {$bankId}, (b.bank_id = {$bankId} OR (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id)) ";
	//$whereProperty .= " AND IF(b.bank_type = 1, b.bank_id = {$bankId}, (b.bank_id = {$bankId} OR (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id)) ";
    $whereProperty .= " AND (p.bank_id = {$bankId} OR (SELECT account_id FROM `bank` where bank_id = {$bankId}) = p.account_id) ";
    //$wherePropertyBank .= " AND b.bank_id = {$bankId} ";
}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty2 .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$whereProperty21 .= " AND p.edit_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$whereProperty3 .= " AND p.payment_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	
	$whereProperty4 .= " AND p.edit_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    //$boolBalanceBefore = true;
}/*else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $wherePaymentDate .= " AND p.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	
}else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $wherePaymentDate .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
	
}*/


$date = explode('/', $periodFrom);
$month = (int)$date[1];
$day   = $date[0];
$year  = (int)$date[2];


/*update Alan
$sqlOB = "SELECT opening_balance FROM bank WHERE bank_id = {$bankId}";
			$resultOB = $myDatabase->query($sqlOB, MYSQLI_STORE_RESULT);
                    if($resultOB->num_rows == 1) {
                        $rowOB = $resultOB->fetch_object();
                        $openingBalance = $rowOB->opening_balance;
                    }
//$no = 1;
$sqlBalance = "SELECT 
SUM(CASE 
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
			*/
			//update Surya ----------------------------------------------
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


  if  ($stockpileID==10 && $year>=2019){
    $sqlBalance = "SELECT
      SUM(CASE
  
   WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2019-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	 WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2019-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	  
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2019-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_method = 2 AND p.payment_date >= '2019-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	  
	  WHEN p.payment_type = 1 AND p.payment_date >= '2019-01-01' AND p.sales_id IS NOT NULL AND p.payment_status = 0 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
	  
	  
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2019-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND p.payment_date >= '2019-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	  
	  
	  
	  WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2019-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-01-01' THEN (p.amount_journal * -1) / p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2019-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-01-01' THEN p.amount_journal / p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2019-01-01'  THEN p.amount_journal
	  WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2021-07-21' THEN (p.original_amount_converted * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2021-05-31' THEN (p.original_amount_converted * -1)/ p.exchange_rate
	  WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2019-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2019-01-01'  THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_method = 2 AND p.payment_date >= '2019-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal))/ p.exchange_rate
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-01-01' THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_date >= '2019-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2019-01-01'  THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date >= '2019-01-01'  THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
      ELSE 0 END) AS balance
      FROM payment p WHERE 1=1 {$whereProperty} {$whereProperty3}
      ORDER BY p.payment_date ASC, p.entry_date ASC";
    			$resultBalance = $myDatabase->query($sqlBalance, MYSQLI_STORE_RESULT);
    			if($resultBalance->num_rows > 0) {
    			while($rowBalance = $resultBalance->fetch_object()) {
    				//$mutasi = $rowBalance->balance;
    				$mutasi = $rowBalance->balance;
    				//echo $mutasi ;
    			}
    				//$no++;
    			}
  }elseif ($stockpileID<>10 && $year >=2019) {
    $sqlBalance = "SELECT
      SUM(CASE
	  
	  WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2019-04-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-04-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2019-04-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	   WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-04-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2019-04-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2019-04-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2019-04-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_method = 2 AND p.payment_date >= '2019-04-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-04-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND p.payment_date > '2019-04-01' AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2019-04-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND p.payment_date >= '2019-04-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	  
	  
      WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2019-04-01' THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1),2)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-04-01' THEN ROUND((p.amount_journal * -1),2) / p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2019-04-01'  THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1),2)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2019-04-01' THEN ROUND(p.amount_journal,2) / p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2019-04-01'  THEN ROUND(p.amount_journal,2)
	  WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2021-07-21' THEN (p.original_amount_converted * -1)/ p.exchange_rate
	  WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2021-05-31' THEN (p.original_amount_converted * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2019-04-01' THEN ROUND((p.amount_journal * -1),2)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2019-04-01'  THEN ROUND((p.amount_journal * -1),2)/ p.exchange_rate
      WHEN p.payment_method = 2 AND p.payment_date >= '2019-04-01'  THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1),2)/ p.exchange_rate
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2019-04-01' THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date > '2019-04-01' AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN ROUND((((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1),2)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2019-04-01'  THEN ROUND((p.amount_journal - p.pph_journal),2)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date >= '2019-04-01'  THEN ROUND(((p.amount_journal + p.ppn_journal) - p.pph_journal),2)/ p.exchange_rate
      ELSE 0 END) AS balance
      FROM payment p WHERE 1=1 {$whereProperty} {$whereProperty3}
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
	  WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2018-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	   
	  WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2018-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2018-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
      WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01'  AND p.payment_status = 1 {$whereProperty4} THEN 0
	  
      WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1) / p.exchange_rate
      WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2018-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id AND p.payment_date >= '2018-01-01' THEN p.amount_journal / p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id  AND p.payment_date >= '2018-01-01'  THEN p.amount_journal
	  WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2021-07-21' THEN (p.original_amount_converted * -1)/ p.exchange_rate
	  WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2021-05-31' THEN (p.original_amount_converted * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id  AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01'  THEN (p.amount_journal * -1)/ p.exchange_rate
      WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01'  THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal))/ p.exchange_rate
	  WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate
      WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 AND p.payment_date >= '2018-01-01'  THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
      WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01'  THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
      ELSE 0 END) AS balance
      FROM payment p WHERE 1=1 {$whereProperty} {$whereProperty3}
      ORDER BY p.payment_date ASC, p.entry_date ASC  ";
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

			//---------------------------------------------------------------
				$balanceBefore = $openingBalance + $mutasi;
				$balance = $balanceBefore;
				
				//echo $mutasi;
				echo '<br>';
				//echo $sqlBalance;
			
$sql = "SELECT * FROM (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = p.payment_location) END AS payment_location, 
(SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code,
(SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
(SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code, 
p.payment_date, p.payment_no, p.payment_type, p.payment_id,
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
CASE WHEN p.account_id = 1 AND (SELECT bank_type FROM bank WHERE bank_id = {$bankId}) = 1 THEN (SELECT a.account_no FROM account a LEFT JOIN bank b ON a.account_id = b.account_id WHERE b.bank_id = {$bankId})
ELSE (SELECT account_no FROM account WHERE account_id = p.account_id) END AS account_no,
CASE WHEN p.account_id = 1 AND (SELECT bank_type FROM bank WHERE bank_id = {$bankId}) = 1 THEN (SELECT a.account_name FROM account a LEFT JOIN bank b ON a.account_id = b.account_id WHERE b.bank_id = {$bankId})
ELSE (SELECT account_name FROM account WHERE account_id = p.account_id) END AS account_name,
p.exchange_rate AS kurs,
ROUND(CASE 
WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 2 AND p.amount_journal < 0 THEN 0
WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.original_amount_converted / p.exchange_rate
WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.original_amount_converted / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_method = 2 AND p.sales_id IS NOT NULL THEN 0
WHEN p.payment_method = 2 THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
ELSE 0 END,2) AS debit_amount,
ROUND(CASE 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.sales_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
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
WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.original_amount_converted * -1)/ p.exchange_rate
WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.original_amount_converted * -1)/ p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1)/ p.exchange_rate 
WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * -1 )/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' AND p.sales_id IS NOT NULL THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * 1)/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.sales_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*-1)/ p.exchange_rate 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) = 520100 AND p.payment_date >= '2018-01-01' THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate 
WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01' THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate 
ELSE 0 END,2) AS mutasi
FROM payment p
WHERE 1=1 {$whereProperty} {$whereProperty2}
UNION ALL
SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = p.payment_location) END AS payment_location, 
(SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code,
(SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
(SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code, 
p.edit_date AS payment_date, CONCAT(p.payment_no,'-R') AS payment_no, p.payment_type,p.payment_id,
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
CASE WHEN p.invoice_id IS NOT NULL AND (SELECT invoice_status FROM invoice WHERE invoice_id = p.invoice_id) = 1
THEN (SELECT GROUP_CONCAT(invoice_no, '-R - ', invoice_no2) FROM invoice WHERE invoice_id = p.invoice_id)
WHEN p.invoice_id IS NOT NULL 
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
CASE WHEN p.account_id = 1 AND (SELECT bank_type FROM bank WHERE bank_id = {$bankId}) = 1 THEN (SELECT a.account_no FROM account a LEFT JOIN bank b ON a.account_id = b.account_id WHERE b.bank_id = {$bankId})
ELSE (SELECT account_no FROM account WHERE account_id = p.account_id) END AS account_no,
CASE WHEN p.account_id = 1 AND (SELECT bank_type FROM bank WHERE bank_id = {$bankId}) = 1 THEN (SELECT a.account_name FROM account a LEFT JOIN bank b ON a.account_id = b.account_id WHERE b.bank_id = {$bankId})
ELSE (SELECT account_name FROM account WHERE account_id = p.account_id) END AS account_name,
p.exchange_rate AS kurs,
ROUND(CASE 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) =  520100 THEN (p.amount_journal - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.sales_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN p.amount_journal 
WHEN p.payment_type = 2 AND p.amount_journal < 0 THEN (p.amount_journal * -1)/ p.exchange_rate
ELSE 0 END,2) AS debit_amount,
ROUND(CASE 
WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL THEN p.amount_journal / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 2 AND p.amount_journal < 0 THEN 0
WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.original_amount_converted / p.exchange_rate
WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.original_amount_converted / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN p.amount_journal / p.exchange_rate
WHEN p.payment_method = 2 AND p.sales_id IS NOT NULL THEN 0
WHEN p.payment_method = 2 THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal) / p.exchange_rate
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN ((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate
ELSE 0 END,2) AS credit_amount,
ROUND(CASE 
WHEN p.payment_type = 2 AND p.general_vendor_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * 1)/ p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.stockpile_contract_id IS NOT NULL AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * 1) / p.exchange_rate 
WHEN p.payment_type = 2 AND p.payment_method = 2 AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * 1)/ p.exchange_rate 
WHEN p.payment_type = 2 AND p.payment_method = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.original_amount_converted * 1) / p.exchange_rate
WHEN p.payment_type = 2 AND p.payment_method = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.original_amount_converted * 1) / p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = {$bankId}) = p.currency_id THEN ((p.amount_journal / p.exchange_rate)* -1)
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = 37) = p.account_id AND (SELECT currency_id FROM `bank` WHERE bank_id = 37) != p.currency_id THEN ((p.amount_journal)* -1)

WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id AND p.payment_date >= '2018-01-01' THEN p.amount_journal * 1
WHEN p.payment_type = 2 AND p.vendor_handling_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.original_amount_converted * 1)/ p.exchange_rate
WHEN p.payment_type = 2 AND p.freight_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.original_amount_converted * 1)/ p.exchange_rate
WHEN p.payment_type = 2 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * 1)/ p.exchange_rate 
WHEN p.payment_type = 2 AND p.amount_journal < 0 AND p.payment_date >= '2018-01-01' THEN (p.amount_journal * 1 )/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' AND p.sales_id IS NOT NULL THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * -1)/ p.exchange_rate
WHEN p.payment_method = 2 AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal) * 1)/ p.exchange_rate
WHEN p.payment_type = 1 AND p.payment_method = 1 AND p.stockpile_contract_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN ((((p.amount_journal) - p.pph_journal)/ p.exchange_rate)* 1)
WHEN p.payment_type = 1 AND p.sales_id IS NOT NULL AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) != p.account_id THEN (p.amount_journal / p.exchange_rate)*-1 
WHEN p.payment_type = 1 AND (SELECT account_id FROM `bank` WHERE bank_id = {$bankId}) = p.account_id THEN (((p.amount_journal + p.ppn_journal) - p.pph_journal)*1)/ p.exchange_rate 
WHEN p.payment_type = 1 AND (SELECT account_no FROM account WHERE account_id = p.account_id) = 520100 AND p.payment_date >= '2018-01-01' THEN (((p.amount_journal - p.pph_journal)/ p.exchange_rate) *-1)
WHEN p.payment_type = 1 AND p.payment_date >= '2018-01-01' THEN ((((p.amount_journal + p.ppn_journal) - p.pph_journal)/ p.exchange_rate)*-1) 
ELSE 0 END,2) AS mutasi
FROM payment p
WHERE p.payment_status = 1 {$whereProperty} {$whereProperty21}
) a
ORDER BY a.payment_date ASC, a.payment_id ASC, a.payment_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/bank-book-report-new.php', {
                    bankId: $('input[id="bankId"]').val(), 
                    periodFrom: $('input[id="periodFrom"]').val(),
                    periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<form method="post" id="downloadxls" action="reports/bank-book-report-new-xls.php">
    <input type="hidden" id="bankId" name="bankId" value="<?php echo $bankId; ?>" />
	<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>Date</th>
            <th>No. Voucher</th>
            <!--<th>Nama Supplier</th>-->
            <th>Payment To/Receive From</th>
            <th>Invoice No</th>
            <th>Tax Invoice</th>
			<th>Invoice Date</th>
			<th>PO No</th>
			<th>Contract No</th>
			<th>Shipment Code</th>
            <th>Qty</th>
            <th>Harga / Kg</th>
            <th>Stockpile</th>
            <th>Keterangan</th>
            <th>No. Cek</th>
            <th>No. Akun</th>
            <th>Nama Akun</th>
			<th>Kurs</th>
            <th>Pembayaran</th>
            <th>Penerimaan</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
		<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><?php //echo number_format($openingBalance, 2, ".", ","); ?></td>
		<td><?php ///echo number_format($mutasi, 2, ".", ","); ?></td>
		<td style="text-align: right;"><?php echo number_format($balanceBefore, 2, ".", ","); ?></td>
		</tr>
        <?php
        if($result === false) {
            echo 'wrong query';
			
			//echo $sql;
        } else {
			 while($row = $result->fetch_object()) {
				
				$balance = $balance + $row->mutasi;
                ?>
		
        <tr>
            <td><?php echo $row->payment_date; ?></td>
            <td>
                <?php 
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
                    
                    echo $voucherCode .' # '. $row->payment_no; 
                } else {
                    echo '';
                }
                ?>
            </td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->invoice_no; ?></td>
			<td><?php echo $row->tax_invoice; ?></td>
            <td><?php echo $row->invoice_date; ?></td>
			<td><?php echo $row->po_no; ?></td>
			<td><?php echo $row->contract_no; ?></td>
			<td><?php echo $row->shipment_code; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->qty, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->unit_price, 2, ".", ","); ?></td>
            <td><?php echo $row->stockpile_name2; ?></td>
            <td><?php echo $row->remarks; ?></td>
            <td><?php echo $row->cheque_no; ?></td>
            <td><?php echo $row->account_no; ?></td>
            <td><?php echo $row->account_name; ?></td>
			<td><?php echo number_format($row->kurs, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->debit_amount, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->credit_amount, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($balance, 2, ".", ","); ?></td>
        </tr>
                <?php
				$no++;
            }
        }
		
        ?>
    </tbody>
</table>
