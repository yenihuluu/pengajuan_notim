<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$whereProperty2 = '';
$whereProperty3 = '';
//$sumProperty = '';
//$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$pType = '';
$pTypes = '';
$lastPaymentId = '';
$pLocation = '';
$pLocations = '';
$paymentNo = '';
$paymentNos = '';


//$balanceBefore = 0;
//$boolBalanceBefore = false;
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $sql = "SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();

    $whereProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
    $sumProperty .= " AND t.slip_no like '{$row->stockpile_code}%' ";
}*/

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']} AND module_id = 27";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 27) {

			$whereProperty = "";

			if(isset($_POST['pLocation']) && $_POST['pLocation'] != ''){
			$pLocation = $_POST['pLocation'];
			for ($i = 0; $i < sizeof($pLocation); $i++) {
                        if($pLocations == '') {
                            $pLocations .= "'". $pLocation[$i] ."'";
                        } else {
                            $pLocations .= ','. "'". $pLocation[$i] ."'";
                        }
                    }

			$whereProperty3 .= "AND (CASE WHEN p.payment_location = 0 THEN 'HO' ELSE 'Stockpile' END) IN ({$pLocations})";
			}

        }else{
			$whereProperty = "AND p.entry_by = {$_SESSION['userId']}";
			$whereProperty3 = "";
		}

	}
}

if(isset($_POST['pType']) && $_POST['pType'] != ''){
	$pType = $_POST['pType'];
	for ($i = 0; $i < sizeof($pType); $i++) {
                        if($pTypes == '') {
                            $pTypes .= $pType[$i];
                        } else {
                            $pTypes .= ','. $pType[$i];
                        }
                    }

	$whereProperty2 .= "AND p.payment_type2 IN ({$pTypes})";
}

if(isset($_POST['paymentNo']) && $_POST['paymentNo'] != ''){
	$paymentNo = $_POST['paymentNo'];
	for ($i = 0; $i < sizeof($paymentNo); $i++) {
                        if($paymentNos == '') {
                            $paymentNos .= "'". $paymentNo[$i] ."'";
                        } else {
                            $paymentNos .= ','."'". $paymentNo[$i] ."'";
                        }
                    }

	$whereProperty2 .= "AND p.payment_no IN ({$paymentNos})";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')";
    //$sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    //$boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND DATE_FORMAT(p.payment_date, '%Y-%m-%d') <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
}

$sql = "SELECT p.payment_id, p.payment_no, p.payment_date, p.entry_date, p.payment_type,
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE ps.stockpile_name END AS payment_location,
CASE WHEN p.payment_location = 0 THEN 'HO'
ELSE 'Stockpile' END AS payment_location2,
CASE WHEN p.payment_type = 1 THEN 'IN'
ELSE 'OUT' END AS paymentType,
b.bank_code, b.bank_type, pcur.currency_code AS pcur_currency_code, p.payment_type2,
CASE WHEN p.payment_type2 = 1 THEN 'TT'
				WHEN p.payment_type2 = 2 THEN 'Cek/Giro'
				WHEN p.payment_type2 = 3 THEN 'Tunai'
				WHEN p.payment_type2 = 4 THEN 'Bill Payment'
				WHEN p.payment_type2 = 5 THEN 'Auto Debet'
			ELSE 'TT' END AS p_type,

CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(account_no,'-',''),'.',''),' ','')) FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.general_vendor_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(gv.account_no,'-',''),'.',''),' ',''))
	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.account_no
	WHEN p.invoice_id IS NOT NULL THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(gv.account_no,'-',''),'.',''),' ','')) FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT TRIM(REPLACE(REPLACE(REPLACE(gv.account_no,'-',''),'.',''),' ','')) FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(v.account_no,'-',''),'.',''),' ',''))
	WHEN p.sales_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(cust.account_no,'-',''),'.',''),' ',''))
	WHEN p.freight_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(f.account_no,'-',''),'.',''),' ',''))
	WHEN p.vendor_handling_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(vh.account_no,'-',''),'.',''),' ',''))
	WHEN p.labor_id IS NOT NULL THEN TRIM(REPLACE(REPLACE(REPLACE(l.account_no,'-',''),'.',''),' ',''))
ELSE (SELECT TRIM(REPLACE(REPLACE(REPLACE(no_rek,'-',''),'.',''),' ','')) FROM vendor_pettycash WHERE account_no = a.account_no) END AS no_rek,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.vendor_name
	WHEN p.sales_id IS NOT NULL THEN cust.customer_name
	WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
	WHEN p.labor_id IS NOT NULL THEN l.labor_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
ELSE (SELECT vendor_name FROM vendor_pettycash WHERE account_no = a.account_no) END AS vendor_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN CONCAT('(Contract No: ',c.contract_no,') - ',p.remarks)
	WHEN p.invoice_id IS NOT NULL THEN i.remarks
	WHEN p.payment_cash_id IS NOT NULL THEN p.remarks
	WHEN p.vendor_id IS NOT NULL THEN p.remarks
	WHEN p.sales_id IS NOT NULL THEN p.remarks
	WHEN p.freight_id IS NOT NULL THEN p.remarks
	WHEN p.vendor_handling_id IS NOT NULL THEN p.remarks
	WHEN p.labor_id IS NOT NULL THEN p.remarks
	WHEN p.general_vendor_id IS NOT NULL THEN p.remarks
ELSE p.remarks END AS keterangan,
CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)

	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT bank_name FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)

	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.bank_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.bank_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.bank_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.bank_name
	WHEN p.sales_id IS NOT NULL THEN cust.bank_name
	WHEN p.freight_id IS NOT NULL THEN f.bank_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.bank_name
	WHEN p.labor_id IS NOT NULL THEN l.bank_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.bank_name
ELSE (SELECT bank FROM vendor_pettycash WHERE account_no = a.account_no) END AS bank_name,


CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
  WHEN p.freight_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN freight_bank fb ON fb.master_bank_id=mb.master_bank_id WHERE fb.f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_handling_bank vhb ON vhb.master_bank_id = mb.master_bank_id WHERE vhb.vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN labor_bank lb ON lb.master_bank_id=mb.master_bank_id WHERE lb.l_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL THEN  (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN (SELECT kode1 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
ELSE (SELECT kode1 FROM master_bank mb LEFT JOIN vendor_pettycash vpc ON vpc.master_bank_id=mb.master_bank_id WHERE vpc.account_no = a.account_no) END AS kode1,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
  WHEN p.freight_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN freight_bank fb ON fb.master_bank_id=mb.master_bank_id WHERE fb.f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_handling_bank vhb ON vhb.master_bank_id = mb.master_bank_id WHERE vhb.vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN labor_bank lb ON lb.master_bank_id=mb.master_bank_id WHERE lb.l_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL THEN  (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_bank vb ON mb.master_bank_id = vb.master_bank_id WHERE vb.v_bank_id = p.vendor_bank_id)
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN (SELECT kode2 FROM master_bank mb LEFT JOIN general_vendor_bank gvb ON mb.master_bank_id = gvb.master_bank_id WHERE gvb.gv_bank_id = p.vendor_bank_id)
ELSE (SELECT kode2 FROM master_bank mb LEFT JOIN vendor_pettycash vpc ON vpc.master_bank_id=mb.master_bank_id WHERE vpc.account_no = a.account_no) END AS kode2,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
	WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
	WHEN p.payment_cash_id IS NOT NULL THEN 'PETTY CASH'
	WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
	WHEN p.vendor_handling_id IS NOT NULL THEN 'HANDLING COST'
	WHEN p.labor_id IS NOT NULL THEN 'UNLOADING COST'
	WHEN p.vendor_id IS NOT NULL THEN 'PKS CURAH'
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN ''
ELSE 'INTERNAL TRANSFER' END AS kode3,

CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12'  THEN (SELECT branch FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12'  THEN (SELECT branch FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL  AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)

	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT branch FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)

	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.branch
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.branch FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.branch FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.branch
	WHEN p.sales_id IS NOT NULL THEN cust.branch
	WHEN p.freight_id IS NOT NULL THEN f.branch
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.branch
	WHEN p.labor_id IS NOT NULL THEN l.branch
	WHEN p.general_vendor_id IS NOT NULL THEN gv.branch
ELSE (SELECT branch FROM vendor_pettycash WHERE account_no = a.account_no) END AS branch,
CASE WHEN p.stockpile_contract_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.vendor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
	WHEN p.invoice_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
	WHEN p.payment_cash_id IS NOT NULL AND p.payment_date > '2019-10-12'  THEN (SELECT beneficiary FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)

	WHEN p.freight_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
	WHEN p.labor_id IS NOT NULL AND p.payment_date > '2019-10-12' THEN (SELECT beneficiary FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
	WHEN p.stockpile_contract_id IS NOT NULL THEN cv.beneficiary
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.beneficiary FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.beneficiary FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.beneficiary
	WHEN p.sales_id IS NOT NULL THEN cust.beneficiary
	WHEN p.freight_id IS NOT NULL THEN f.beneficiary
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.beneficiary
	WHEN p.labor_id IS NOT NULL THEN l.beneficiary
	WHEN p.general_vendor_id IS NOT NULL THEN gv.beneficiary
ELSE (SELECT beneficiary FROM vendor_pettycash WHERE account_no = a.account_no) END AS beneficiary,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.quantity
	WHEN p.invoice_id IS NOT NULL AND ((SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 249 OR (SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 167) THEN (SELECT qty FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1)
	WHEN p.invoice_id IS NOT NULL THEN 0
	WHEN p.payment_cash_id IS NOT NULL THEN 0
	WHEN p.vendor_id IS NOT NULL THEN p.qty
	WHEN p.sales_id IS NOT NULL THEN sl.quantity
	WHEN p.freight_id IS NOT NULL THEN p.qty
	WHEN p.vendor_handling_id IS NOT NULL THEN p.qty
	WHEN p.labor_id IS NOT NULL THEN p.qty
	WHEN p.general_vendor_id IS NOT NULL THEN p.qty
ELSE p.qty END AS quantity,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN c.price_converted
	WHEN p.invoice_id IS NOT NULL AND ((SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 249 OR (SELECT account_id FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1) = 167) THEN (SELECT price FROM invoice_detail WHERE invoice_id = p.invoice_id LIMIT 1)
	WHEN p.invoice_id IS NOT NULL THEN 0
	WHEN p.payment_cash_id IS NOT NULL THEN 0
	WHEN p.vendor_id IS NOT NULL THEN p.price
	WHEN p.sales_id IS NOT NULL THEN sl.price_converted
	WHEN p.freight_id IS NOT NULL THEN p.price
	WHEN p.vendor_handling_id IS NOT NULL THEN p.price
	WHEN p.labor_id IS NOT NULL THEN p.price
	WHEN p.general_vendor_id IS NOT NULL THEN p.price
ELSE p.price END AS price_converted,
CASE WHEN p.currency_id = 2 THEN p.amount
	WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.ppn_amount)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(amount_converted) FROM payment_cash WHERE payment_id = p.payment_id)
	WHEN p.vendor_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN p.amount_converted - p.ppn_amount_converted
	WHEN p.vendor_id IS NOT NULL THEN p.amount_converted
	WHEN p.sales_id IS NOT NULL THEN p.amount_converted
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN (p.amount_converted)
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.original_amount_converted + p.pph_amount_converted) - p.ppn_amount_converted
	WHEN p.freight_id IS NOT NULL THEN (p.original_amount_converted + p.pph_amount_converted)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN p.amount_converted
	WHEN p.vendor_handling_id IS NOT NULL THEN (p.amount_converted)
	WHEN p.labor_id IS NOT NULL THEN (p.amount_converted + p.pph_amount_converted) - p.ppn_amount_converted
	WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted
ELSE p.amount_converted END AS dpp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount
	WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(ppn_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(ppn_converted) FROM payment_cash WHERE payment_id = p.payment_id)
	WHEN p.vendor_id IS NOT NULL THEN p.ppn_amount
	WHEN p.sales_id IS NOT NULL THEN p.ppn_amount
	WHEN p.freight_id IS NOT NULL THEN p.ppn_amount
	WHEN p.vendor_handling_id IS NOT NULL THEN p.ppn_amount
	WHEN p.labor_id IS NOT NULL THEN p.ppn_amount
	WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount
ELSE p.ppn_amount END AS ppn_amount,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.pph_amount
	WHEN p.invoice_id IS NOT NULL THEN (SELECT SUM(pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT SUM(pph_converted) FROM payment_cash WHERE payment_id = p.payment_id)
	WHEN p.vendor_id IS NOT NULL THEN p.pph_amount
	WHEN p.sales_id IS NOT NULL THEN p.pph_amount
	WHEN p.freight_id IS NOT NULL THEN p.pph_amount
	WHEN p.vendor_handling_id IS NOT NULL THEN p.pph_amount
	WHEN p.labor_id IS NOT NULL THEN p.pph_amount
	WHEN p.general_vendor_id IS NOT NULL THEN p.pph_amount
ELSE p.pph_amount END AS pph_amount,
CASE WHEN p.currency_id = 2 THEN p.amount
	WHEN p.stockpile_contract_id IS NOT NULL THEN p.amount_converted
	WHEN p.invoice_id IS NOT NULL THEN (((SELECT SUM(amount_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id) + (SELECT SUM(ppn_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id)) - (SELECT SUM(pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id))
	WHEN p.payment_cash_id IS NOT NULL THEN (((SELECT SUM(amount_converted) FROM payment_cash WHERE payment_id = p.payment_id) + (SELECT SUM(ppn_converted) FROM payment_cash WHERE payment_id = p.payment_id)) - (SELECT SUM(pph_converted) FROM payment_cash WHERE payment_id = p.payment_id))
	WHEN p.vendor_id IS NOT NULL THEN p.amount_converted
	WHEN p.sales_id IS NOT NULL THEN (p.amount_converted + p.ppn_amount)
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	
	WHEN p.freight_id IS NOT NULL THEN p.original_amount_converted
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	WHEN p.vendor_handling_id IS NOT NULL THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	WHEN p.labor_id IS NOT NULL THEN p.amount_converted
	WHEN p.general_vendor_id IS NOT NULL THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
ELSE p.amount_converted END AS total,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
	WHEN p.invoice_id IS NOT NULL THEN (SELECT (COALESCE(SUM(idp.amount_payment),0) + COALESCE(SUM(CASE WHEN iddp.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END),0)) -
COALESCE(SUM(CASE WHEN iddp.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END),0) FROM invoice_detail id
	LEFT JOIN invoice_dp idp ON idp.invoice_detail_id = id.invoice_detail_id
	LEFT JOIN invoice_detail iddp ON iddp.invoice_detail_id = idp.invoice_detail_dp
	LEFT JOIN tax ppn ON ppn.`tax_id` = iddp.`ppnID`
	LEFT JOIN tax pph ON pph.`tax_id` = iddp.`pphID`
	WHERE id.invoice_id = p.invoice_id)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT COALESCE(GROUP_CONCAT((SELECT ROUND(SUM(tamount),2) FROM payment_cash WHERE payment_cash_dp = pc.payment_cash_id)),0) FROM payment_cash pc WHERE pc.payment_id = p.payment_id)
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.original_amount_converted - p.amount_journal)
	WHEN p.freight_id IS NOT NULL THEN (p.amount_converted - p.amount_journal)
	WHEN p.vendor_handling_id IS NOT NULL THEN (((p.amount_converted + p.ppn_amount) - p.pph_amount) - p.original_amount)
	WHEN p.vendor_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
ELSE 0 END AS dp,
CASE WHEN p.currency_id = 2 THEN p.amount
    WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - (p.amount_converted - p.original_amount))
	WHEN p.invoice_id IS NOT NULL THEN ((SELECT SUM((amount_converted + ppn_converted) - pph_converted) FROM invoice_detail WHERE invoice_id = p.invoice_id) - (SELECT (COALESCE(SUM(idp.amount_payment),0) + COALESCE(SUM(CASE WHEN iddp.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END),0)) -
COALESCE(SUM(CASE WHEN iddp.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END),0) FROM invoice_detail id
	LEFT JOIN invoice_dp idp ON idp.invoice_detail_id = id.invoice_detail_id
	LEFT JOIN invoice_detail iddp ON iddp.invoice_detail_id = idp.invoice_detail_dp
	LEFT JOIN tax ppn ON ppn.`tax_id` = iddp.`ppnID`
	LEFT JOIN tax pph ON pph.`tax_id` = iddp.`pphID`
	WHERE id.invoice_id = p.invoice_id))
	WHEN p.payment_cash_id IS NOT NULL THEN ((SELECT SUM((amount_converted + ppn_converted) - pph_converted) FROM payment_cash WHERE payment_id = p.payment_id) - (SELECT COALESCE(GROUP_CONCAT((SELECT ROUND(SUM(tamount),2) FROM payment_cash WHERE payment_cash_dp = pc.payment_cash_id)),0) FROM payment_cash pc WHERE pc.payment_id = p.payment_id))
	WHEN p.vendor_id IS NOT NULL THEN p.original_amount
	WHEN p.sales_id IS NOT NULL THEN (p.amount_converted + p.ppn_amount)
	WHEN p.freight_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount) - (p.amount_converted - p.original_amount)
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.amount_converted - (p.amount_converted - p.amount_journal))
	WHEN p.freight_id IS NOT NULL THEN (p.original_amount_converted - (p.amount_converted - p.amount_journal))
	WHEN p.general_vendor_id IS NOT NULL THEN (((p.amount_converted + p.ppn_amount) - p.pph_amount) - (p.amount_converted - p.original_amount))
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN (p.amount_converted - p.pph_amount)
	WHEN p.vendor_handling_id IS NOT NULL THEN p.amount_converted
	WHEN p.labor_id IS NOT NULL THEN p.amount_converted
ELSE p.amount_converted END AS grand_total,
s.stockpile_name, u.user_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvppn.tax_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.ppn_tax_id = tx.tax_id LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.ppn_tax_id = tx.tax_id LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN vppn.tax_name
	WHEN p.sales_id IS NOT NULL THEN custppn.tax_name
	WHEN p.freight_id IS NOT NULL THEN fppn.tax_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vhppn.tax_name
	WHEN p.labor_id IS NOT NULL THEN lppn.tax_name
	WHEN p.general_vendor_id IS NOT NULL THEN gvppn.tax_name
ELSE '' END AS ppn,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvpph.tax_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.pph_tax_id = tx.tax_id LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT tx.tax_name FROM tax tx LEFT JOIN general_vendor gv ON gv.pph_tax_id = tx.tax_id LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN vpph.tax_name
	WHEN p.sales_id IS NOT NULL THEN custpph.tax_name
	WHEN p.freight_id IS NOT NULL THEN fpph.tax_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_name
	WHEN p.labor_id IS NOT NULL THEN lpph.tax_name
	WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_name
ELSE '' END AS pph
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor cv ON cv.`vendor_id` = c.`vendor_id`
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN vendor v ON v.`vendor_id` = p.`vendor_id`
LEFT JOIN freight f ON f.`freight_id` = p.`freight_id`
LEFT JOIN labor l ON l.`labor_id` = p.`labor_id`
LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = p.`vendor_handling_id`
LEFT JOIN sales sl ON sl.`sales_id` = p.`sales_id`
LEFT JOIN customer cust ON cust.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = p.`general_vendor_id`
LEFT JOIN stockpile s ON s.stockpile_id = p.stockpile_location
LEFT JOIN `user` u ON u.user_id = p.entry_by
LEFT JOIN tax cvppn ON cvppn.tax_id = cv.ppn_tax_id
LEFT JOIN tax cvpph ON cvpph.tax_id = cv.pph_tax_id
LEFT JOIN tax vppn ON vppn.tax_id = v.ppn_tax_id
LEFT JOIN tax vpph ON vpph.tax_id = v.pph_tax_id
LEFT JOIN tax custppn ON custppn.tax_id = cust.ppn_tax_id
LEFT JOIN tax custpph ON custpph.tax_id = cust.pph_tax_id
LEFT JOIN tax fppn ON fppn.tax_id = f.ppn_tax_id
LEFT JOIN tax fpph ON fpph.tax_id = f.pph_tax_id
LEFT JOIN tax vhppn ON vhppn.tax_id = vh.ppn_tax_id
LEFT JOIN tax vhpph ON vhpph.tax_id = vh.pph_tax_id
LEFT JOIN tax lppn ON lppn.tax_id = l.ppn_tax_id
LEFT JOIN tax lpph ON lpph.tax_id = l.pph_tax_id
LEFT JOIN tax gvppn ON gvppn.tax_id = gv.ppn_tax_id
LEFT JOIN tax gvpph ON gvpph.tax_id = gv.pph_tax_id
LEFT JOIN stockpile ps ON ps.stockpile_id = p.payment_location
LEFT JOIN bank b ON b.bank_id = p.bank_id
LEFT JOIN currency pcur ON pcur.currency_id = p.currency_id
LEFT JOIN account a ON a.account_id = p.account_id
WHERE 1=1 AND p.payment_status = 0
{$whereProperty}{$whereProperty2}{$whereProperty3} ORDER BY p.payment_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//echo $sql;

?>
<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $('#printApprovalSheet').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#approvalSheet").printThis();
//            $("#transactionContainer").hide();
        });


});


</script>
<form method="post" action="reports/paymentToTax-xls.php">
    <!--<input type="hidden" id="stockpileId" name="stockpileId" value="<?php //echo $stockpileId; ?>" />-->
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="pTypes" name="pTypes" value="<?php echo $pTypes; ?>" />
	<input type="hidden" id="pLocations" name="pLocations" value="<?php echo $pLocations; ?>" />
	<input type="hidden" id="paymentNos" name="paymentNos" value="<?php echo $paymentNos; ?>" />
    <button class="btn btn-success">Download XLS</button>
	<button class="btn btn-info" id="printApprovalSheet">Print</button>

</form>

<form method="post" action="reports/print_batch_upload.php" target="_blank">
    <!--<input type="hidden" id="stockpileId" name="stockpileId" value="<?php //echo $stockpileId; ?>" />-->
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="pTypes" name="pTypes" value="<?php echo $pTypes; ?>" />
	<input type="hidden" id="pLocations" name="pLocations" value="<?php echo $pLocations; ?>" />
	<input type="hidden" id="paymentNos" name="paymentNos" value="<?php echo $paymentNos; ?>" />
    <button class="btn btn-success">Batch Upload</button>

</form>

<div id = "approvalSheet">
<li class="active">Rincian Pembayaran PT Jatim Propertindo Jaya</li>
<li class="active">Periode : <?php echo $periodFrom; ?> - <?php echo $periodTo; ?></li>

<table class="table table-bordered table-striped" style="font-size: 8pt;">

    <thead>
        <tr>
            <th>No.</th>
			
			
            <th>Payment No</th>
			<th>Category</th>
			<th>Payment Type</th>
            <th>Approval</th>
            <th>No Rek/Cek</th>
            <th>Vendor</th>
            <th>Keterangan</th>
            <th>Bank</th>
            <th>Cabang Bank</th>
            <th>Nama Akun Bank</th>
            <th>Qty (MT/KG/HR/DW)</th>
            <th>Harga</th>
            <th>DPP</th>
            <th>PPN</th>
            <th>PPh</th>
            <th>Total Rincian</th>
            <th>DP</th>
			<th>Type</th>
            <th>Grand Total</th>
            <th>Stockpile</th>
            <th>PIC Finance</th>
            <th>PPN</th>
            <th>PPh</th>
            <th>kode1</th>
            <th>kode2</th>
			<th>Entry Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
          if($result->num_rows > 0) {
			//echo 'test';
            $no = 1;
            while($row = mysqli_fetch_array($result)){

				if($row['payment_no'] != '') {
                    $voucherCode = $row['payment_location'] .'/'. $row['bank_code'] .'/'. $row['pcur_currency_code'];

                    if($row['bank_type'] == 1) {
                        $voucherCode .= ' - B';
                    } elseif($row['bank_type'] == 2) {
                        $voucherCode .= ' - P';
                    } elseif($row['bank_type'] == 3) {
                        $voucherCode .= ' - CAS';
                    }

                    if($row['bank_type'] != 3) {
                        if($row['payment_type'] == 1) {
                            $voucherCode .= 'RV';
                        } else {
                            $voucherCode .= 'PV';
                        }
                    }
                  }


					if($row['payment_type'] == 1) {

						$grand_total = $row['grand_total'] * -1;
					}else{
						$grand_total = $row['grand_total'];
					}
					
					$grandTotal = ((($row['dpp'] + $row['ppn_amount'])-$row['pph_amount'])-$row['dp']);

					//$gt[] = $row['total'] ;
				   //$gTotal = implode(', ', $gt);
				    //$grandTotal = array_sum($gt) - $row['dp'];
				    //$sum += $row['total'];
					//echo $sum;
					//echo array_sum($gt);


			//echo $totals;
		?>
        <tr>

			<?php
              // if($row['payment_id'] == $lastPaymentId) {
                   // $counter++;
                ?>


			<?php
			/*	}else{

					$sqlCount = "SELECT COUNT(1) AS total_row
FROM payment p
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor cv ON cv.`vendor_id` = c.`vendor_id`
LEFT JOIN invoice_detail id ON id.`invoice_id` = p.`invoice_id`
LEFT JOIN general_vendor igv ON igv.`general_vendor_id` = id.`general_vendor_id`
LEFT JOIN vendor v ON v.`vendor_id` = p.`vendor_id`
LEFT JOIN freight f ON f.`freight_id` = p.`freight_id`
LEFT JOIN labor l ON l.`labor_id` = p.`labor_id`
LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = p.`vendor_handling_id`
LEFT JOIN sales sl ON sl.`sales_id` = p.`sales_id`
LEFT JOIN customer cust ON cust.customer_id = sl.customer_id
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = p.`general_vendor_id`
LEFT JOIN payment_cash pc ON pc.payment_id = p.payment_id
LEFT JOIN general_vendor pcgv ON pcgv.general_vendor_id = pc.general_vendor_id
WHERE 1=1 AND p.`payment_id` = '{$row['payment_id']}' ORDER BY p.payment_id DESC";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
                    $counter = 1;
					$no++;
					//echo  $totalRow;*/

			?>
            <td ><?php echo $no; ?></td>
			
            <td ><?php echo $voucherCode; ?> # <?php echo $row['payment_no']; ?></td>
			<td ><?php echo $row['kode3']; ?></td>
			<td ><?php echo $row['p_type']; ?></td>
            <td ></td>
            <td ><?php echo $row['no_rek']; ?></td>
            <td ><?php echo $row['vendor_name']; ?></td>
				<?php //}	?>
            <td><?php echo $row['keterangan']; ?></td>

			<?php
                /*if($row['payment_id'] == $lastPaymentId) {
                    $counter++;*/
                ?>


			<?php
				/*}else{
					//$totalRow = $rowCount->total_row;
                    $counter = 1;
					//$no++;*/
			?>
            <td ><?php echo $row['bank_name']; ?></td>
            <td ><?php echo $row['branch']; ?></td>
            <td ><?php echo $row['beneficiary']; ?></td>
			<?php //}	?>
            <td style="text-align: right;"><?php echo number_format($row['quantity'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['price_converted'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['dpp'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['ppn_amount'], 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row['pph_amount'], 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row['total'], 0, ".", ","); ?></td>
			<?php
               /* if($row['payment_id'] == $lastPaymentId) {
                    $counter++;*/
                ?>


			<?php
				/*}else{
					//$totalRow = $rowCount->total_row;
                    $counter = 1;
				//}
					/// $lastPaymentNo = $row['payment_no'];





					//$no++;*/
			?>
			<td  style="text-align: right;"><?php echo number_format($row['dp'], 0, ".", ","); ?></td>
			<td ><?php echo $row['paymentType']; ?></td>
            <td  style="text-align: right;"><?php echo number_format($grandTotal, 0, ".", ","); ?></td>
            <td ><?php echo $row['stockpile_name']; ?></td>
            <td ><?php echo $row['user_name']; ?></td>
            <td ><?php echo $row['ppn']; ?></td>
      			<td ><?php echo $row['pph']; ?></td>
            <td ><?php echo $row['kode1']; ?></td>
            <td ><?php echo $row['kode2']; ?></td>
			
			<td ><?php echo $row['entry_date']; ?></td>
            <?php //}	?>
        </tr>
                <?php
				$no++;
                //$lastPaymentId = $row['payment_id'];
				//$lastPaymentNo2 = $row->payment_no;
				//$dp = $row->dp;
				$grandTotal = $grandTotal + $grand_total;
            }

			//echo $grandTotal;
        }else{
			//echo $sql;
		}
        ?>
		<tr>
		<td colspan="18" style="text-align: right;">TOTAL</td>
		<td ><?php echo number_format($grandTotal, 0, ".", ","); ?></td>
		<td colspan="4"></td>

		</tr>
    </tbody>
</table>
</div>
