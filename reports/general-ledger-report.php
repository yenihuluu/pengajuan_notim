<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';

$periodFrom = '';

$periodTo = '';

$amount = '';

$module = '';

$stockpileId = '';

$coaId = '';

$jurnalNo = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND gl.gl_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND gl.gl_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND gl.gl_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if(isset($_POST['module']) && $_POST['module'] != '') {
    $module = $_POST['module'];
for ($i = 0; $i < sizeof($module); $i++) {
                        if($module_name == '') {
                            $module_name .= "'". $module[$i] ."'";
                        } else {
                            $module_name .= ','. "'". $module[$i] ."'";
                        }
                    }
			}
			
	$whereProperty .= " AND gl.general_ledger_module IN ({$module_name})";

/*    
    if($module == 0) {
        $whereProperty .= " AND a.general_ledger_module = 'CONTRACT' ";
		$module_name = 'CONTRACT';
    } elseif($module == 1) {
        $whereProperty .= " AND a.general_ledger_module = 'NOTA TIMBANG' ";
		$module_name = 'NOTA TIMBANG';
    } elseif($module == 2) {
        $whereProperty .= " AND a.general_ledger_module = 'PAYMENT' ";
		$module_name = 'PAYMENT';
    } elseif($module == 3) {
        $whereProperty .= " AND a.general_ledger_module = 'INVOICE' ";
		$module_name = 'INVOICE';
    } elseif($module == 4) {
        $whereProperty .= " AND a.general_ledger_module = 'JURNAL MEMORIAL' ";
		$module_name = 'JURNAL MEMORIAL';
    } elseif($module == 5) {
        $whereProperty .= " AND a.general_ledger_module = 'PETTY CASH' ";
		$module_name = 'PETTY CASH';
    } elseif($module == 6) {
        $whereProperty .= " AND a.general_ledger_module = 'PAYMENT ADMIN' ";
		$module_name = 'PAYMENT ADMIN';
    }
}*/

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {

    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
	
	$stockpile_name = array();
	$sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
		
	/*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                        if($stockpile_names == '') {
                            $stockpile_names .= "'". $stockpile_name[$i] ."'";
                        } else {
                            $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                        }
                    }*/
				
	$stockpile_names =  "'" . implode("','", $stockpile_name) . "'";				
	}
	}
	
	$whereProperty .= " AND gl.stockpile IN ({$stockpile_names}) ";

}

if(isset($_POST['coaId']) && $_POST['coaId'] != '') {

    $coaId = $_POST['coaId'];
	
	for ($i = 0; $i < sizeof($coaId); $i++) {
                        if($coaNos == '') {
                            $coaNos .= $coaId[$i];
                        } else {
                            $coaNos .= ','. $coaId[$i];
                        }
                    }
		
		 $whereProperty .= " AND gl.account_no IN ({$coaNos})";
	
	/*$coaNo = array();
	$sql = "SELECT GROUP_CONCAT(account_no) AS account_no FROM account WHERE account_no IN ({$coaIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$coaNo[] = $row['account_no'];
		
				
	$coaNos =  "'" . implode("','", $coaNo) . "'";				
	}
	}*/
	

}
if(isset($_POST['jurnalNo']) && $_POST['jurnalNo'] != '') {

    $jurnalNo = $_POST['jurnalNo'];
	
	$whereProperty .= " AND gl.jurnal_no = '{$jurnalNo}' ";
		
	//$whereProperty2 .= " AND ru.stockpile_name IN ({$stockpile_names})";
}

$sql = "SELECT gl.*,
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
FROM gl_report gl WHERE 1=1 {$whereProperty}
ORDER BY gl.gl_date ASC, gl.jurnal_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;


?>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
       
        <form class="form-horizontal" method="post" action="reports/general-ledger-report-xls-new.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="module" name="module" value="<?php echo $module_name; ?>" />
         <input type="hidden" id="stockpile_names" name="stockpile_names" value="<?php echo $stockpile_names; ?>" />
         <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileIds; ?>" />
            <div class="control-group">
               <label class="control-label" for="module_name">Module</label>
                <div class="controls">
                 <input type="text" readonly id="module_name" name="module_name" value="<?php echo $module_name; ?>" />
                </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="stockpile_name">Stockpile</label>
                <div class="controls">
                 <input type="text" readonly id="stockpile_name" name="stockpile_name" value="<?php echo $stockpile_names; ?>" />
                </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="coaNo">COA</label>
                <div class="controls">
                 <input type="text" readonly id="coaNos" name="coaNos" value="<?php echo $coaNos; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="module_name2">Period</label>
                <div class="controls">
                  <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $periodFrom .' - '. $periodTo; ?>" />
                </div>
            </div>
			<div class="control-group">
               <label class="control-label" for="coaNo">Jurnal No</label>
                <div class="controls">
                 <input type="text" readonly id="jurnalNo" name="jurnalNo" value="<?php echo $jurnalNo; ?>" />
                </div>
            </div>
            <div class="control-group">
               
                <div class="controls">
                    <button class="btn btn-success">Download XLS</button>
                   
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    
                </div>
            </div>
        </form>
    </div>
</div>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
			<th>No</th>
            <th>Stockpile</th>
            <th>Date</th>
            <th>Jurnal No.</th>
            <th>Source Module</th>
            <th>Supplier</th>
            <th>Remarks</th>
            <th>Account No</th>
            <th>Account Name</th>
            <th>Debit</th>
            <th>Credit</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			$no = 1;
			while($row = $result->fetch_object()) {
		?>	
			
		<tr>
			<td><?php echo $no; ?></td>
            <td><?php echo $row->stockpile; ?></td>
            <td><?php echo $row->gl_date; ?></td>
            <td><?php echo $row->jurnal_no; ?></td>
            <td><?php echo $row->general_ledger_module; ?></td>
            <td><?php echo $row->supplier_name; ?></td>
			<td><?php echo $row->remarks; ?></td>
			<td><?php echo $row->account_no; ?></td>
			<td><?php echo $row->account_name; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->debitAmount, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->creditAmount, 2, ".", ","); ?></td>
			
		 </tr>
                <?php
                $no++;
            }
		}
        
        ?>
    </tbody>
</table>
