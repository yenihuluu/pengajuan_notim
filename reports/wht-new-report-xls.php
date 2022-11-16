<?php

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
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
//$paymentType = $myDatabase->real_escape_string($_POST['paymentType']);
//$purchaseType = $myDatabase->real_escape_string($_POST['purchaseType']);
$vendorId = $_POST['vendorIds'];
$dateField = '';
$stockpileName = 'All ';
$periodFull = ' ';
$transactionName = ' ';
$purchaseName = '';
$vendorName = '';
$pphSusut = 0;

// <editor-fold defaultstate="collapsed" desc="Query">
/*
if($stockpileId != '') {
    $whereProperty .= " AND p.stockpile_location = {$stockpileId} ";
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowStockpile = $resultStockpile->fetch_object();
    $stockpileName = $rowStockpile->stockpile_name . " ";
}*/
/*
if($purchaseType != '') {
    $whereProperty .= " AND con.contract_type = '{$purchaseType}' ";
    
    if($purchaseType == 'P') {
        $purchaseName = 'PKS';
    } else {
        $purchaseName = 'Curah';
    }
}*/

if($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND p.payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if($periodFrom != '' && $periodTo == '') { 
    $whereProperty .= " AND p.payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $periodFull = "From " . $periodFrom . " ";
} else if($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND p.payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}
/*
if($paymentType != '') {
    
    if($paymentType == 1) {
        $paymentName = 'PKS ';
    } elseif($paymentType == 2) {
        $paymentName = 'Freight Cost ';
    } elseif($paymentType == 3) {
        $paymentName = 'Unloading Cost ';
    } elseif($paymentType == 4) {
        $paymentName = 'Other ';
    }
}*/

if($vendorId != '') {
        $whereProperty .= " AND (CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cv.vendor_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN v.vendor_name
	WHEN p.sales_id IS NOT NULL THEN cust.customer_name
	WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
	WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
	WHEN p.labor_id IS NOT NULL THEN l.labor_name
	WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
ELSE (SELECT vendor_name FROM vendor_pettycash WHERE account_no = a.account_no) END) IN ({$vendorId}) ";
    
    /*$sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowVendor = $resultVendor->fetch_object();
    $vendorName = $rowVendor->vendor_name . " ";*/
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
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvpph.tax_category
	WHEN p.invoice_id IS NOT NULL THEN (SELECT tx.tax_category FROM tax tx LEFT JOIN general_vendor_pph a ON a.`pph_tax_id` = tx.`tax_id` LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = a.`general_vendor_id` LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE a.`status` = 0 AND id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT tx.tax_category FROM tax tx LEFT JOIN general_vendor_pph a ON a.`pph_tax_id` = tx.`tax_id` LEFT JOIN general_vendor gv ON gv.general_vendor_id = a.`general_vendor_id` LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE a.`status` = 0 AND pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN vpph.tax_category
	WHEN p.sales_id IS NOT NULL THEN custpph.tax_value
	WHEN p.freight_id IS NOT NULL THEN fpph.tax_category
	WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_category
	WHEN p.labor_id IS NOT NULL THEN lpph.tax_category
	WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_category
ELSE '' END AS tax_category,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN cvpph.tax_value
	WHEN p.invoice_id IS NOT NULL THEN (SELECT tx.tax_value FROM tax tx LEFT JOIN general_vendor_pph a ON a.`pph_tax_id` = tx.`tax_id` LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = a.`general_vendor_id` LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id WHERE a.`status` = 0 AND id.invoice_id = p.invoice_id LIMIT 1)
	WHEN p.payment_cash_id IS NOT NULL THEN (SELECT tx.tax_value FROM tax tx LEFT JOIN general_vendor_pph a ON a.`pph_tax_id` = tx.`tax_id` LEFT JOIN general_vendor gv ON gv.general_vendor_id = a.`general_vendor_id` LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE a.`status` = 0 AND pc.payment_id = p.payment_id LIMIT 1)
	WHEN p.vendor_id IS NOT NULL THEN vpph.tax_value
	WHEN p.sales_id IS NOT NULL THEN custpph.tax_value
	WHEN p.freight_id IS NOT NULL THEN fpph.tax_value
	WHEN p.vendor_handling_id IS NOT NULL THEN vhpph.tax_value
	WHEN p.labor_id IS NOT NULL THEN lpph.tax_value
	WHEN p.general_vendor_id IS NOT NULL THEN gvpph.tax_value
ELSE '' END AS tax_value,
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

// </editor-fold>

$fileName = "WHT Report " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "AK";

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
/*
if ($stockpileName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
}	*/

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period = {$periodFull}");
}
/*
if ($paymentName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Payment Type = {$paymentName}");
}*/


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "WHT Report");

$rowActive++;
$rowMerge = $rowActive;
$headerRow = $rowActive;
//$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
//$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Type");
//$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Source Data");
//$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Input Date");
//$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Invoice Date");
//$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Vendor Name");
//$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Stockpile");
//$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Original Invoice No.");
//$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Tax Invoice No");
//$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "PO No");
//$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Slip No");
//$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Remark");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "DPP (Pelunasan)");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "PPN (Pelunasan)");
//$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "PPh (Pelunasan)");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "DP (DPP)");
//$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:N{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "DP (PPN)");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "DP (PPh)");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "DPP (Net)");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "PPN (Net)");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "PPh (Net)");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "DPP (Susut)");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "PPN (Susut)");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "PPh (Susut)");

$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Payment No");
$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", "Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", "Payment Status");
$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", "Tarif");
$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", "Nama");
$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", "NPWP");
$objPHPExcel->getActiveSheet()->setCellValue("AI{$rowActive}", "NIK");
$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", "Alamat");
$objPHPExcel->getActiveSheet()->setCellValue("AK{$rowActive}", "PKHOA");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
$objPHPExcel->getActiveSheet()->getStyle("A{$rowMerge}:{$lastColumn}{$rowMerge}")->applyFromArray($styleArray4);

$rowActive = $rowMerge;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
//$boolColor = false;
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
				  
                  if($row['tax_category'] == 1){
                    $dpp_pelunasan = ($row['dpp_pelunasan'] / ((100 - $row['tax_value']) / 100));
                    $dp_dpp = ($row['dp_dpp'] / ((100 - $row['tax_value']) / 100));
                    $dppSusut = ($row['dppSusut'] / ((100 - $row['tax_value']) / 100));
                 }else{
                    $dpp_pelunasan = $row['dpp_pelunasan'];
                    $dp_dpp = $row['dp_dpp'];
                    $dppSusut = $row['dppSusut'];
                }

                  $dppNet = $dpp_pelunasan - $dp_dpp - $dppSusut;
                //$dppNet = $row['dpp_pelunasan'] - $row['dp_dpp'] - $row['dppSusut'];
				  $pphNet = $row['pph_pelunasan'] - $row['dp_pph'] - $row['pphSusut'];
				  $ppnNet = $row['ppn_pelunasan'] - $row['dp_ppn'] - $row['ppnSusut'];
				  $totalAmount = $dppNet + $ppnNet - $pphNet;
                  //$dppSusut = $row['dppSusut'];
                  $ppnSusut = $row['ppnSusut'];
                  $pphSusut = $row['pphSusut'];



					if($row['payment_type'] == 1) {

						$grand_total = $totalAmount * -1;
					}else{
						$grand_total = $totalAmount;
					}
    $rowActive++;
	
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row['paymentType']);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row['kode3']);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row['entry_date']);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row['invoice_date_2']);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row['vendor_name']);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row['stockpile_name']);
	//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row['invoice_no_2']);
    //$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row['original_invoice_no']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row['invoice_no_2'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row['original_invoice_no'], PHPExcel_Cell_DataType::TYPE_STRING);
    //$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row['taxInvoice']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("J{$rowActive}", $row['taxInvoice'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row['po_no']);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row['slip_no']);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row['account_no']);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row['account_name']);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row['keterangan']);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $dpp_pelunasan);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row['ppn_pelunasan']);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row['pph_pelunasan']);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $dp_dpp);
	$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row['dp_ppn']);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row['dp_pph']);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $dppNet);
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $ppnNet);
	$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $pphNet);
    $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $dppSusut);
    $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $ppnSusut);
    $objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", $pphSusut);
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $grand_total);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", $voucherCode .' # '. $row['payment_no']);
	$objPHPExcel->getActiveSheet()->setCellValue("AD{$rowActive}", $row['payment_date']);
	$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $row['p_status']);
	$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $row['tarif']);
	$objPHPExcel->getActiveSheet()->setCellValue("AG{$rowActive}", $row['nama']);
	//$objPHPExcel->getActiveSheet()->setCellValue("AE{$rowActive}", $row['npwp']);
	//$objPHPExcel->getActiveSheet()->setCellValue("AF{$rowActive}", $row['nik']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AH{$rowActive}", $row['npwp'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AI{$rowActive}", $row['nik'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AJ{$rowActive}", $row['alamat']);
	//$objPHPExcel->getActiveSheet()->setCellValue("AH{$rowActive}", $row['doc_pkhoa']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AK{$rowActive}", $row['doc_pkhoa'], PHPExcel_Cell_DataType::TYPE_STRING);
	

    
	
    $no++;
	
	if($row['p_status'] == 'RETURN') {
        $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:AH{$rowActive}")->applyFromArray($styleArray4b);
    }
	
}
$bodyRowEnd = $rowActive;


	
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Footer">
/*
if ($bodyRowEnd > $headerRow + 1) {
    $rowActive++;

    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
    
    
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "=SUM(K" . ($headerRow + 1) . ":K{$bodyRowEnd})");

    // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("K{$rowActive}:K{$rowActive}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


    // Set border for table
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:K{$rowActive}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set row TOTAL to bold
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:K{$rowActive}")->getFont()->setBold(true);
}*/
 // </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("AK"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":E{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	// $objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	  $objPHPExcel->getActiveSheet()->getStyle("AD" . ($headerRow + 1) . ":AD{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("P" . ($headerRow + 1) . ":AB{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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