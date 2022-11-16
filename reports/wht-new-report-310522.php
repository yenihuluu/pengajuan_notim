<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$joinPaymentProperty = '';
$stockpileId = '';
$paymentType = '';
//$purchaseType = '';
$vendorId = '';
$vendorIds = '';
$periodFrom = '';
$periodTo = '';
$dateField = '';
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND p.stockpile_location = {$stockpileId} ";
}

if(isset($_POST['paymentType']) && $_POST['paymentType'] != '') {
    $paymentType = $_POST['paymentType'];
    
    if($paymentType == 1) {
        $paymentName = 'PKS';
    } elseif($paymentType == 2) {
        $paymentName = 'Freight Cost';
    } elseif($paymentType == 3) {
        $paymentName = 'Unloading Cost';
    } elseif($paymentType == 4) {
        $paymentName = 'Other';
    }
}
*/
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'";
                        }
                    }
			
    $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.vendor_name
	WHEN p.sales_id IS NOT NULL THEN cust.customer_name
	WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
	WHEN p.labor_id IS NOT NULL THEN l.labor_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
ELSE (SELECT vendor_name FROM vendor_pettycash WHERE account_no = a.account_no) END) IN ({$vendorIds}) ";
    
}
/*
if(isset($_POST['purchaseType']) && $_POST['purchaseType'] != '') {
    $purchaseType = $_POST['purchaseType'];
    $whereProperty .= " AND con.contract_type = '{$purchaseType}' ";
}
*/
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND p.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

$sql = "SELECT p.payment_id,
CASE WHEN p.payment_type = 1 THEN 'IN'
ELSE 'OUT' END AS paymentType,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS'
	WHEN p.invoice_id IS NOT NULL THEN 'INVOICE'
	WHEN p.payment_cash_id IS NOT NULL THEN 'CASH PAYMENT'
	WHEN p.freight_id IS NOT NULL THEN 'FREIGHT COST'
	WHEN p.vendor_handling_id IS NOT NULL THEN 'HANDLING COST'
	WHEN p.labor_id IS NOT NULL THEN 'UNLOADING COST'
	WHEN p.vendor_id IS NOT NULL THEN 'PKS CURAH'
	WHEN p.sales_id IS NOT NULL THEN ''
	WHEN p.general_vendor_id IS NOT NULL THEN ''
ELSE 'INTERNAL TRANSFER' END AS kode3, p.entry_date,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT invoice_date FROM invoice WHERE invoice_id = p.invoice_id)
ELSE p.invoice_date END AS invoice_date_2,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.vendor_name
	WHEN p.sales_id IS NOT NULL THEN cust.customer_name
	WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
	WHEN p.labor_id IS NOT NULL THEN l.labor_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
ELSE (SELECT vendor_name FROM vendor_pettycash WHERE account_no = a.account_no) END AS vendor_name, s.stockpile_name,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT invoice_no FROM invoice WHERE invoice_id = p.invoice_id)
ELSE '' END AS invoice_no_2,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT invoice_no2 FROM invoice WHERE invoice_id = p.invoice_id)
ELSE p.invoice_no END AS original_invoice_no,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT a.invoice_tax FROM invoice a WHERE a.invoice_id = p.invoice_id)
ELSE p.tax_invoice END AS taxInvoice,
CASE WHEN p.invoice_id IS NOT NULL AND p.payment_date >= '2018-10-01' THEN (SELECT GROUP_CONCAT(c.po_no) FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.invoice_id = p.invoice_id) 
WHEN p.invoice_id IS NOT NULL THEN (SELECT GROUP_CONCAT(c.po_no) FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.invoice_id = p.invoice_id)
WHEN p.vendor_id IS NOT NULL THEN (SELECT c.po_no FROM contract c 
LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id`
LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN payment p2 ON p2.`payment_id` = t.`payment_id`
WHERE p2.`payment_id` = p.payment_id GROUP BY p.`payment_id`)
WHEN p.freight_id IS NOT NULL THEN (SELECT a.po_no FROM contract a LEFT JOIN stockpile_contract b ON a.contract_id = b.contract_id WHERE b.stockpile_contract_id = fc.stockpile_contract_id)
WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT a.po_no FROM contract a LEFT JOIN stockpile_contract b ON a.contract_id = b.contract_id WHERE b.stockpile_contract_id = hc.stockpile_contract_id)
WHEN p.labor_id IS NOT NULL THEN (SELECT a.po_no FROM contract a LEFT JOIN stockpile_contract b ON a.contract_id = b.contract_id WHERE b.stockpile_contract_id = uc.stockpile_contract_id)
ELSE c.po_no END AS po_no, 
CASE WHEN p.freight_id IS NOT NULL THEN fc.slip_no
WHEN p.vendor_handling_id IS NOT NULL THEN hc.slip_no
WHEN p.labor_id IS NOT NULL THEN uc.slip_no
ELSE '' END AS slip_no,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = id.account_id)
WHEN p.payment_cash_id IS NOT NULL THEN (SELECT account_no FROM account WHERE account_id = pc.account_id)
ELSE (SELECT account_no FROM account WHERE account_no = 140000 LIMIT 1) END AS account_no,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = id.account_id)
WHEN p.payment_cash_id IS NOT NULL THEN (SELECT account_name FROM account WHERE account_id = pc.account_id)
ELSE (SELECT account_name FROM account WHERE account_no = 140000 LIMIT 1) END AS account_name,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.remarks
	WHEN p.invoice_id IS NOT NULL THEN i.remarks
	WHEN p.payment_cash_id IS NOT NULL THEN p.remarks
	WHEN p.vendor_id IS NOT NULL THEN p.remarks
	WHEN p.sales_id IS NOT NULL THEN p.remarks
	WHEN p.freight_id IS NOT NULL THEN p.remarks
	WHEN p.vendor_handling_id IS NOT NULL THEN p.remarks
	WHEN p.labor_id IS NOT NULL THEN p.remarks
	WHEN p.general_vendor_id IS NOT NULL THEN p.remarks
ELSE p.remarks END AS keterangan,
CASE WHEN p.currency_id = 2 THEN p.amount
	WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.ppn_amount)
	WHEN p.invoice_id IS NOT NULL THEN id.amount_converted
	WHEN p.payment_cash_id IS NOT NULL THEN pc.amount_converted
	WHEN p.vendor_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN p.amount_converted - p.ppn_amount_converted
	WHEN p.vendor_id IS NOT NULL THEN p.amount_converted
	WHEN p.sales_id IS NOT NULL THEN p.amount_converted
	WHEN p.freight_id IS NOT NULL THEN (fc.freight_quantity * fc.freight_price)
	WHEN p.vendor_handling_id IS NOT NULL THEN (hc.handling_quantity * hc.handling_price)
	WHEN p.labor_id IS NOT NULL THEN uc.unloading_price
	WHEN p.general_vendor_id IS NOT NULL THEN p.amount_converted
ELSE p.amount_converted END AS dpp_pelunasan,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.ppn_amount
	WHEN p.invoice_id IS NOT NULL THEN id.ppn_converted
	WHEN p.payment_cash_id IS NOT NULL THEN pc.ppn_converted
	WHEN p.vendor_id IS NOT NULL THEN p.ppn_amount
	WHEN p.sales_id IS NOT NULL THEN p.ppn_amount
	WHEN p.freight_id IS NOT NULL THEN (fc.freight_quantity * fc.freight_price) * (SELECT (a.ppn/100) FROM freight a WHERE a.freight_id = p.freight_id)
	WHEN p.vendor_handling_id IS NOT NULL THEN (hc.handling_quantity * hc.handling_price) * (SELECT (a.ppn/100) FROM vendor_handling a WHERE a.vendor_handling_id = p.vendor_handling_id)
	WHEN p.labor_id IS NOT NULL THEN uc.unloading_price * (SELECT a.ppn/100 FROM labor a WHERE a.labor_id = uc.labor_id)
	WHEN p.general_vendor_id IS NOT NULL THEN p.ppn_amount
ELSE p.ppn_amount END AS ppn_pelunasan,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN p.pph_amount
	WHEN p.invoice_id IS NOT NULL THEN id.pph_converted
	WHEN p.payment_cash_id IS NOT NULL THEN pc.pph_converted
	WHEN p.vendor_id IS NOT NULL THEN p.pph_amount
	WHEN p.sales_id IS NOT NULL THEN p.pph_amount
	WHEN p.freight_id IS NOT NULL THEN 
		CASE WHEN fpph.tax_category = 1 THEN (((fc.freight_quantity * fc.freight_price) / (SELECT ((100 - a.pph)/100) FROM freight a WHERE a.freight_id = p.freight_id)) * (SELECT (a.pph/100) FROM freight a WHERE a.freight_id = p.freight_id))
			ELSE (fc.freight_quantity * fc.freight_price) * (SELECT (a.pph/100) FROM freight a WHERE a.freight_id = p.freight_id) END
	WHEN p.vendor_handling_id IS NOT NULL THEN 
		CASE WHEN vhpph.tax_category = 1 THEN (((hc.handling_quantity * hc.handling_price)/(SELECT ((100-a.pph)/100) FROM vendor_handling a WHERE a.vendor_handling_id = p.vendor_handling_id))* (SELECT (a.pph/100) FROM vendor_handling a WHERE a.vendor_handling_id = p.vendor_handling_id))
			ELSE (hc.handling_quantity * hc.handling_price) * (SELECT (a.pph/100) FROM vendor_handling a WHERE a.vendor_handling_id = p.vendor_handling_id) END
	WHEN p.labor_id IS NOT NULL THEN 
		CASE WHEN lpph.tax_category = 1 THEN (uc.unloading_price /(SELECT (100 - a.pph)/100 FROM labor a WHERE a.labor_id = uc.labor_id)) * (SELECT a.pph/100 FROM labor a WHERE a.labor_id = uc.labor_id)
			ELSE uc.unloading_price * (SELECT a.pph/100 FROM labor a WHERE a.labor_id = uc.labor_id) END
	WHEN p.general_vendor_id IS NOT NULL THEN p.pph_amount
ELSE p.pph_amount END AS pph_pelunasan,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
	WHEN p.invoice_id IS NOT NULL THEN IFNULL((SELECT SUM(amount_payment) FROM invoice_dp WHERE invoice_detail_id = id.invoice_detail_id AND `status` = 0),0) 
	WHEN p.payment_cash_id IS NOT NULL THEN IFNULL((SELECT SUM(amount_payment) FROM payment_cash_dp WHERE payment_cash_id = pc.payment_cash_id AND `status` = 0),0)
	WHEN p.freight_id IS NOT NULL AND (SELECT MAX(transaction_id) FROM `transaction` WHERE fc_payment_id = p.payment_id) = fc.transaction_id THEN p.freightDP
	WHEN p.vendor_handling_id IS NOT NULL AND (SELECT MAX(transaction_id) FROM `transaction` WHERE hc_payment_id = p.payment_id) = hc.transaction_id THEN p.handlingDP
	WHEN p.vendor_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
ELSE 0 END AS dp_dpp,
CASE WHEN p.invoice_id IS NOT NULL THEN IFNULL((SELECT SUM(a.amount_payment) * (c.tax_value/100) FROM invoice_dp a 
	LEFT JOIN invoice_detail b ON b.invoice_detail_id = a.invoice_detail_dp
	LEFT JOIN tax c ON c.tax_id = b.ppnID 
	WHERE a.invoice_detail_id = id.invoice_detail_id),0)
	WHEN p.payment_cash_id IS NOT NULL THEN IFNULL((SELECT SUM(a.amount_payment) * (c.tax_value/100) FROM payment_cash_dp a 
	LEFT JOIN payment_cash b ON b.payment_cash_id = a.payment_cash_dp
	LEFT JOIN tax c ON c.tax_id = b.ppnID 
	WHERE a.payment_cash_id = pc.payment_cash_id),0)
	WHEN p.freight_id IS NOT NULL AND (SELECT MAX(transaction_id) FROM `transaction` WHERE fc_payment_id = p.payment_id) = fc.transaction_id THEN p.freightDP * (SELECT (a.ppn/100) FROM freight a WHERE a.freight_id = p.freight_id)
	WHEN p.vendor_handling_id IS NOT NULL AND (SELECT MAX(transaction_id) FROM `transaction` WHERE hc_payment_id = p.payment_id) = hc.transaction_id THEN p.handlingDP * (SELECT (a.ppn/100) FROM vendor_handling a WHERE a.vendor_handling_id = p.vendor_handling_id)
ELSE 0 END AS dp_ppn,
CASE WHEN p.invoice_id IS NOT NULL THEN IFNULL((SELECT SUM(a.amount_payment) * (c.tax_value/100) FROM invoice_dp a 
	LEFT JOIN invoice_detail b ON b.invoice_detail_id = a.invoice_detail_dp
	LEFT JOIN tax c ON c.tax_id = b.pphID 
	WHERE a.invoice_detail_id = id.invoice_detail_id),0)
	WHEN p.payment_cash_id IS NOT NULL THEN IFNULL((SELECT SUM(a.amount_payment) * (c.tax_value/100) FROM payment_cash_dp a 
	LEFT JOIN payment_cash b ON b.payment_cash_id = a.payment_cash_dp
	LEFT JOIN tax c ON c.tax_id = b.pphID 
	WHERE a.payment_cash_id = pc.payment_cash_id),0)
	WHEN p.freight_id IS NOT NULL AND (SELECT MAX(transaction_id) FROM `transaction` WHERE fc_payment_id = p.payment_id) = fc.transaction_id THEN p.freightDP * (SELECT (a.pph/100) FROM freight a WHERE a.freight_id = p.freight_id)
	WHEN p.vendor_handling_id IS NOT NULL AND (SELECT MAX(transaction_id) FROM `transaction` WHERE hc_payment_id = p.payment_id) = hc.transaction_id THEN p.handlingDP * (SELECT (a.pph/100) FROM vendor_handling a WHERE a.vendor_handling_id = p.vendor_handling_id)
ELSE 0 END AS dp_pph,
'' AS dpp_net,
'' AS ppn_net,
'' AS pph_net,
'' AS total_amount,
p.payment_type,
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE ps.stockpile_name END AS payment_location,
CASE WHEN p.payment_location = 0 THEN 'HO'
ELSE 'Stockpile' END AS payment_location2,
b.bank_code, b.bank_type, pcur.currency_code AS pcur_currency_code, p.payment_type2,
p.payment_no, p.payment_date,
CASE WHEN p.payment_status = 1 THEN 'RETURN'
ELSE 'PAID' END AS p_status,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN CONCAT(cvpph.tax_value,'%')
	WHEN p.invoice_id IS NOT NULL THEN (SELECT CONCAT(tx.tax_value,'%') FROM tax tx LEFT JOIN general_vendor_pph a ON a.`pph_tax_id` = tx.`tax_id` LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = a.`general_vendor_id` LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE a.`status` = 0 AND id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT CONCAT(tx.tax_value,'%') FROM tax tx LEFT JOIN general_vendor_pph a ON a.`pph_tax_id` = tx.`tax_id` LEFT JOIN general_vendor gv ON gv.general_vendor_id = a.`general_vendor_id` LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE a.`status` = 0 AND pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN CONCAT(vpph.tax_value,'%')
	WHEN p.sales_id IS NOT NULL THEN CONCAT(custpph.tax_value,'%')
	WHEN p.freight_id IS NOT NULL THEN CONCAT(fpph.tax_value,'%')
	WHEN p.vendor_handling_id IS NOT NULL THEN CONCAT(vhpph.tax_value,'%')
	WHEN p.labor_id IS NOT NULL THEN CONCAT(lpph.tax_value,'%')
	WHEN p.general_vendor_id IS NOT NULL THEN CONCAT(gvpph.tax_value,'%')
ELSE '' END AS tarif,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.npwp_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.npwp_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.npwp_name
	WHEN p.sales_id IS NOT NULL THEN cust.npwp_name
	WHEN p.freight_id IS NOT NULL THEN f.npwp_name
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.npwp_name
	WHEN p.labor_id IS NOT NULL THEN l.npwp_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp_name
ELSE '' END AS nama,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.npwp
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.npwp FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.npwp
	WHEN p.sales_id IS NOT NULL THEN cust.npwp
	WHEN p.freight_id IS NOT NULL THEN f.npwp
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.npwp
	WHEN p.labor_id IS NOT NULL THEN l.npwp
	WHEN p.general_vendor_id IS NOT NULL THEN gv.npwp
ELSE '' END AS npwp,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.nik
	WHEN p.invoice_id IS NOT NULL THEN (SELECT COALESCE(gv.nik,'-') AS nik FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT COALESCE(gv.nik,'-') AS nik FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.nik
	WHEN p.sales_id IS NOT NULL THEN cust.npwp
	WHEN p.freight_id IS NOT NULL THEN f.nik
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.nik
	WHEN p.labor_id IS NOT NULL THEN l.nik
	WHEN p.general_vendor_id IS NOT NULL THEN gv.nik
ELSE '' END AS nik,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_address
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_address FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.vendor_address
	WHEN p.sales_id IS NOT NULL THEN cust.customer_address
	WHEN p.freight_id IS NOT NULL THEN f.freight_address
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_address
	WHEN p.labor_id IS NOT NULL THEN l.labor_address
	WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_address
ELSE '' END AS alamat,
CASE WHEN p.freight_id IS NOT NULL THEN (SELECT contract_pkhoa FROM freight_cost  WHERE freight_cost_id = fc.freight_cost_id LIMIT 1)
ELSE '' END AS doc_pkhoa,
ts.amt_claim AS dppSusut,
(ts.amt_claim * (SELECT (a.ppn/100) FROM freight a WHERE a.freight_id = p.freight_id)) AS ppnSusut,
(ts.amt_claim * (SELECT (a.pph/100) FROM freight a WHERE a.freight_id = p.freight_id)) AS pphSusut
FROM payment p
LEFT JOIN payment_cash pc ON pc.payment_id = p.payment_id
LEFT JOIN `transaction` fc ON fc.fc_payment_id = p.payment_id
LEFT JOIN `transaction` uc ON uc.uc_payment_id = p.payment_id
LEFT JOIN `transaction` hc ON hc.hc_payment_id = p.payment_id
LEFT JOIN transaction_shrink_weight ts ON ts.transaction_id = fc.transaction_id
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
LEFT JOIN vendor cv ON cv.`vendor_id` = c.`vendor_id`
LEFT JOIN invoice i ON i.invoice_id = p.invoice_id
LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id
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
{$whereProperty} ORDER BY p.payment_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// echo $sql;
?>
<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $('#printVendorActivity').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#vendorActivity").printThis();
//            $("#transactionContainer").hide();
        });
		


});


</script>
<form method="post" action="reports/wht-new-report-xls.php">
    
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $vendorIds; ?>" />
    
    <button class="btn btn-success">Download XLS</button>
	<button class="btn btn-info" id="printVendorActivity">Print</button>
</form>

<div id = "vendorActivity">
<li class="active">WHT Report</li>
<li class="active">Periode : <?php echo $periodFrom; ?> - <?php echo $periodTo; ?></li>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No</th>
			<th>Type</th>
			<th>Source Data</th>
            <th>Input Date</th>
			<th>Invoice Date</th>
            <th>Vendor Name</th>
			<th>Stockpile</th>
            <th>Invoice No</th>
			<th>Original Invoice No</th>
			<th>Tax Invoice No</th>
            <th>PO No</th>
            <th>Slip No</th>
			<th>Account No</th>
			<th>Account Name</th>
            <th>Remark</th>
			<th>DPP (Pelunasan)</th>
			<th>PPN (Pelunasan)</th>
			<th>PPh (Pelunasan)</th>
			<th>DP (DPP)</th>
			<th>DP (PPN)</th>
			<th>DP (PPh)</th>
			<th>DPP (Net)</th>
			<th>PPN (Net)</th>
			<th>PPh (Net)</th>
			<th>DPP (Susut)</th>
			<th>PPN (Susut)</th>
			<th>PPh (Susut)</th>
            <th>Total Amount</th>
			<th>Payment No</th>
            <th>Payment Date</th>
			<th>Payment Status</th>
			<th>Tarif</th>
			<th>Nama</th>
			<th>NPWP</th>
			<th>NIK</th>
			<th>Alamat</th>
			<th>PKHOA</th>
			
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo $sql;
        } else {
			$no=1;
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

				   $dppNet = $row['dpp_pelunasan'] - $row['dp_dpp'] - $row['dppSusut'];
				   $pphNet = $row['pph_pelunasan'] - $row['dp_pph'] - $row['pphSusut'];
				   $ppnNet = $row['ppn_pelunasan'] - $row['dp_ppn'] - $row['ppnSusut'];

				//   if($row['tax_category_pph'] == 1){
				// 	// $dppOriginal = ($row['dpp_pelunasan'] / ((100 - $row['tax_value_pph']) / 100));
				// 	 $dppOriginalNett = ($dppNet / ((100 - $row['tax_value_pph']) / 100));
				// 	 $pph_pelunasan=  ($dppOriginalNett * ($row['tax_value_pph']/100));
				// 	 $pphNet =  $pph_pelunasan  - $row['dp_pph'];

				//   }else{
				//   	$pphNet = $row['pph_pelunasan'] - $row['dp_pph'];
				//   }

				//   if($row['tax_category_ppn'] == 1){
				// 	$dppOriginalPPh = ($row['dpp_pelunasan'] / ((100 - $row['tax_value_ppn']) / 100));
					
				// 	$ppnNet =  ($dppOriginalPPh * ($row['tax_value_ppn']/100));
				//    }else{
				// 	$ppnNet = $row['ppn_pelunasan'] - $row['dp_ppn'];
				//    }

				  $totalAmount = $dppNet + $ppnNet - $pphNet;



					if($row['payment_type'] == 1) {

						$grand_total = $totalAmount * -1;
					}else{
						$grand_total = $totalAmount;
					}
                ?>
        <tr>
            <td><?php echo $no; ?></td>
			<td><?php echo $row['paymentType']; ?></td>
			<td><?php echo $row['kode3']; ?></td>
            <td><?php echo $row['entry_date']; ?></td>
            <td><?php echo $row['invoice_date_2']; ?></td>
            <td><?php echo $row['vendor_name'];?></td>
			<td><?php echo $row['stockpile_name'];?></td>
            <td><?php echo $row['invoice_no_2']; ?></td>
			<td><?php echo $row['original_invoice_no']; ?></td>
			<td><?php echo $row['taxInvoice']; ?></td>
            <td><?php echo $row['po_no']; ?></td>
            <td><?php echo $row['slip_no']; ?></td>
			<td><?php echo $row['account_no']; ?></td>
			<td><?php echo $row['account_name']; ?></td>
            <td><?php echo $row['keterangan']; ?></td>
            <td><?php echo number_format($row['dpp_pelunasan'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['ppn_pelunasan'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['pph_pelunasan'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['dp_dpp'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['dp_ppn'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['dp_pph'], 2, ".", ","); ?></td>
			<td><?php echo number_format($dppNet, 2, ".", ","); ?></td>
			<td><?php echo number_format($ppnNet, 2, ".", ","); ?></td>
			<td><?php echo number_format($pphNet, 2, ".", ","); ?></td>
			<td><?php echo number_format($row['dppSusut'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['ppnSusut'], 2, ".", ","); ?></td>
			<td><?php echo number_format($row['pphSusut'], 2, ".", ","); ?></td>
			<td><?php echo number_format($grand_total, 2, ".", ","); ?></td>
			<td><?php echo $voucherCode; ?> # <?php echo $row['payment_no']; ?></td>
			<td><?php echo $row['payment_date']; ?></td>
			<td><?php echo $row['p_status']; ?></td>
			<td><?php echo $row['tarif']; ?></td>
			<td><?php echo $row['nama']; ?></td>
			<td><?php echo $row['npwp']; ?></td>
			<td><?php echo $row['nik']; ?></td>
			<td><?php echo $row['alamat']; ?></td>
			<td><?php echo $row['doc_pkhoa']; ?></td>
			
        </tr>
                <?php
				$no++;
            }
        }
        ?>
    </tbody>
</table>
</div>
