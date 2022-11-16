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
	WHEN p.freight_id IS NOT NULL AND p.ppn_amount_converted > 0 THEN (p.amount_converted + p.pph_amount_converted) - p.ppn_amount_converted
	WHEN p.freight_id IS NOT NULL THEN (p.amount_converted + p.pph_amount_converted)
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN p.amount_converted
	WHEN p.vendor_handling_id IS NOT NULL THEN (p.amount_converted + p.pph_amount_converted)
	WHEN p.labor_id IS NOT NULL THEN (p.amount_converted + p.pph_amount_converted)
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
	WHEN p.freight_id IS NOT NULL THEN p.amount_converted
	WHEN p.vendor_handling_id IS NOT NULL AND p.payment_method = 2 THEN ((p.amount_converted + p.ppn_amount) - p.pph_amount)
	WHEN p.vendor_handling_id IS NOT NULL THEN p.amount_converted
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
	WHEN p.freight_id IS NOT NULL THEN (p.amount_converted - p.amount_journal)
	WHEN p.vendor_handling_id IS NOT NULL THEN (p.amount_converted - p.original_amount)
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
	WHEN p.freight_id IS NOT NULL THEN (p.amount_converted - (p.amount_converted - p.amount_journal))
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
ELSE '' END AS pph,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT invoice_date FROM invoice WHERE invoice_id = p.invoice_id)
ELSE p.invoice_date END AS invoice_date_2,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT invoice_no FROM invoice WHERE invoice_id = p.invoice_id)
ELSE '' END AS invoice_no_2,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT invoice_no2 FROM invoice WHERE invoice_id = p.invoice_id)
ELSE p.invoice_no END AS original_invoice_no,
CASE WHEN p.invoice_id IS NOT NULL AND p.payment_date >= '2018-10-01' THEN (SELECT GROUP_CONCAT(c.po_no) FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.invoice_id = p.invoice_id) 
WHEN p.invoice_id IS NOT NULL THEN (SELECT GROUP_CONCAT(c.po_no) FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.invoice_id = p.invoice_id)
WHEN p.vendor_id IS NOT NULL THEN (SELECT c.po_no FROM contract c 
LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id`
LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN payment p2 ON p2.`payment_id` = t.`payment_id`
WHERE p2.`payment_id` = p.payment_id GROUP BY p.`payment_id`)
ELSE c.po_no END AS po_no, 
CASE WHEN p.invoice_id IS NOT NULL AND p.payment_date >= '2018-10-01' THEN (SELECT GROUP_CONCAT(c.contract_no) FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.invoice_id = p.invoice_id)
WHEN p.invoice_id IS NOT NULL THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.po_id = sc.stockpile_contract_id WHERE i.invoice_id = p.invoice_id)
WHEN p.vendor_id IS NOT NULL THEN (SELECT c.contract_no FROM contract c 
LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id`
LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN payment p2 ON p2.`payment_id` = t.`payment_id`
WHERE p2.`payment_id` = p.payment_id GROUP BY p.`payment_id`)
ELSE c.contract_no END AS contract_no,
CASE WHEN p.payment_status = 1 THEN 'RETURN'
ELSE 'PAID' END AS p_status
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
{$whereProperty} ORDER BY p.payment_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// </editor-fold>

$fileName = "Vendor Activity Report " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "S";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Vendor Activity Report");

$rowActive++;
$rowMerge = $rowActive;
$headerRow = $rowActive;
//$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:A{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
//$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Source");
//$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Input Date");
//$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:D{$rowMerge}");

//$objPHPExcel->getActiveSheet()->mergeCells("E{$rowActive}:E{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Invoice Date");
//$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:F{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Vendor Name");
//$objPHPExcel->getActiveSheet()->mergeCells("G{$rowActive}:G{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Stockpile");
//$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:H{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Original Invoice No.");
//$objPHPExcel->getActiveSheet()->mergeCells("I{$rowActive}:I{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "PO No.");
//$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:J{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Contract No.");
//$objPHPExcel->getActiveSheet()->mergeCells("K{$rowActive}:K{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Remark");
//$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:L{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "DPP");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "PPN");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "Total Amount");
//$objPHPExcel->getActiveSheet()->mergeCells("M{$rowActive}:M{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "Payment No.");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Payment Date");
//$objPHPExcel->getActiveSheet()->mergeCells("N{$rowActive}:N{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Days (Payment)");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Payment Status");
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
				  
				  //$paymentNo = ($voucherCode,'#',


					if($row['payment_type'] == 1) {

						$grand_total = $row['grand_total'] * -1;
					}else{
						$grand_total = $row['grand_total'];
					}
    $rowActive++;
	
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row['paymentType']);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row['entry_date']);
	$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row['invoice_date_2']);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row['vendor_name']);
	$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row['stockpile_name']);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row['invoice_no_2']);
	$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row['original_invoice_no']);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row['po_no']);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row['contract_no']);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row['keterangan']);
	$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row['dpp']);
	$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row['ppn_amount']);
	$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row['pph_amount']);
	$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row['total']);
	$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $voucherCode .' # '. $row['payment_no']);
	$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row['payment_date']);
	$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row['entry_date'] - $row['payment_date']);
	$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row['p_status']);
    
	
    $no++;
	
	if($row['p_status'] == 'RETURN') {
        $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:P{$rowActive}")->applyFromArray($styleArray4b);
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
for ($temp = ord("A"); $temp <= ord("Z"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);

// Set format date in cell
if ($bodyRowEnd > $headerRow) {
    $objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	// $objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
	  $objPHPExcel->getActiveSheet()->getStyle("Q" . ($headerRow + 1) . ":Q{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("L" . ($headerRow + 1) . ":O{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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