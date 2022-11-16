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

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'DOWNLOAD DAILY JURNAL REPORT',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
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
$module = $_POST['module'];
$module_name = $_POST['module_name'];
$periodFull = '';

$sql = "SELECT stockpile_id stockpile_id
            FROM user_stockpile   
            WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	$sp = array();
	while($row = mysqli_fetch_array($result)){
	$sp[] = $row['stockpile_id'];		 	
	$stockpile_id =  implode(', ', $sp);	
		
	}



// <editor-fold defaultstate="collapsed" desc="Query">

if ($periodFrom != '' && $periodTo != '') {
    $whereProperty .= " AND (CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN DATE_FORMAT((SELECT adjustment_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN DATE_FORMAT((SELECT entry_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9) THEN (SELECT unloading_date FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT payment_date FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT DATE_FORMAT(jm.gl_add_date, '%Y-%m-%d') FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END) BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $boolBalanceBefore = true;
    $periodFull = $periodFrom . " - " . $periodTo . " ";
} else if ($periodFrom != '' && $periodTo == '') {
    $whereProperty .= " AND (CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN DATE_FORMAT((SELECT adjustment_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN DATE_FORMAT((SELECT entry_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9) THEN (SELECT unloading_date FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT payment_date FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT DATE_FORMAT(jm.gl_add_date, '%Y-%m-%d') FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END) >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $boolBalanceBefore = true;
    $periodFull = "From " . $periodFrom . " ";
} else if ($periodFrom == '' && $periodTo != '') {
    $whereProperty .= " AND (CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN DATE_FORMAT((SELECT adjustment_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN DATE_FORMAT((SELECT entry_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9) THEN (SELECT unloading_date FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT payment_date FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT DATE_FORMAT(jm.gl_add_date, '%Y-%m-%d') FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END) <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $periodFull = "To " . $periodTo . " ";
}

if($module != '') {
     $whereProperty .= " AND gl.general_ledger_module IN ('{$module_name}')"; 
}else{
	 $whereProperty .= " AND gl.general_ledger_module IN ('NOTA TIMBANG','PETTY CASH','PAYMENT ADMIN')";
}


$sql = "SELECT *, 
(SELECT `pph_journal` FROM  payment p WHERE p.payment_id = gl.payment_id  ) AS pph_journal ,
(SELECT `ppn_journal` FROM  payment p WHERE p.payment_id = gl.payment_id  ) AS ppn_journal,
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name,
	
	CASE WHEN gl.general_ledger_transaction_type = 1 THEN 'IN'
		WHEN gl.general_ledger_transaction_type = 2 THEN 'OUT'
		ELSE '' END AS general_ledger_transaction_type2,
                
	CASE WHEN gl.general_ledger_for = 1 THEN 'PKS Kontrak'
		WHEN gl.general_ledger_for = 2 THEN 'PKS Curah'
		WHEN gl.general_ledger_for = 3 THEN 'Freight Cost'
		WHEN gl.general_ledger_for = 4 THEN 'Unloading Cost'
		WHEN gl.general_ledger_for = 5 THEN 'Other'
		WHEN gl.general_ledger_for = 6 THEN 'Internal Transfer'
		WHEN gl.general_ledger_for = 7 THEN 'Retur'
		WHEN gl.general_ledger_for = 8 THEN 'Umum, HO'
		WHEN gl.general_ledger_for = 9 THEN 'Sales'
		WHEN gl.general_ledger_for = 10 THEN 'Invoice'
		WHEN gl.general_ledger_for = 11 THEN 'Jurnal Memorial'
		ELSE '' END AS general_ledger_for2,
        
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT GROUP_CONCAT(s.stockpile_name) FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = gl.contract_id AND sc.quantity > 0 GROUP BY sc.contract_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT s.stockpile_name FROM stockpile s LEFT JOIN invoice i ON i.stockpileId = s.stockpile_id LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id` WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT s.stockpile_name FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT s.stockpile_name FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT s.stockpile_name FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT s.stockpile_name FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT s.stockpile_name FROM stockpile s INNER JOIN sales sl ON s.stockpile_id = sl.stockpile_id INNER JOIN shipment sh ON sh.sales_id = sl.sales_id INNER JOIN `transaction` t ON t.shipment_id = sh.shipment_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT s.stockpile_name FROM 					stockpile s INNER JOIN payment p ON p.stockpile_location = s.stockpile_id WHERE gl.payment_id = p.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT s.stockpile_name FROM stockpile s LEFT JOIN gl_detail jd ON jd.stockpile_id = s.stockpile_id WHERE jd.gl_detail_id = gl.jurnal_id) 
		ELSE '' END AS stockpile_name2,
	
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT GROUP_CONCAT(s.stockpile_id) FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = gl.contract_id AND sc.quantity > 0 GROUP BY sc.contract_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT s.stockpile_id FROM stockpile s LEFT JOIN invoice i ON i.stockpileId = s.stockpile_id LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id` WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 3 OR gl.general_ledger_for = 13) THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT s.stockpile_id FROM stockpile s INNER JOIN sales sl ON s.stockpile_id = sl.stockpile_id INNER JOIN shipment sh ON sh.sales_id = sl.sales_id INNER JOIN `transaction` t ON t.shipment_id = sh.shipment_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13) THEN (SELECT s.stockpile_id FROM stockpile s INNER JOIN payment p ON (CASE WHEN p.payment_location = 0 THEN 10 ELSE p.payment_location END) = s.stockpile_id WHERE gl.payment_id = p.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT s.stockpile_id FROM stockpile s LEFT JOIN gl_detail jd ON jd.stockpile_id = s.stockpile_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS stockpile_id2,
			
	CASE WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.t_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.t_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT t.slip_no FROM `transaction` t LEFT JOIN gl_add jm ON jm.transaction_id = t.transaction_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)        
		ELSE '' END AS slip_no,
			  
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT vendor_id FROM contract WHERE contract_id = gl.contract_id))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE t.transaction_id = gl.transaction_id))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id` LEFT JOIN payment p ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE p.`payment_id` = gl.payment_id))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT vendor_id FROM contract WHERE contract_id = gl.contract_id))  
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id` LEFT JOIN payment p ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE p.`payment_id` = gl.payment_id)) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT `freight_supplier` FROM freight f WHERE f.freight_id = (SELECT freight_id FROM freight_cost WHERE freight_cost_id = (SELECT freight_cost_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT `freight_supplier` FROM freight f WHERE f.freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.`payment_id`))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT labor_name FROM labor WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT labor_name FROM labor WHERE labor_id = (SELECT labor_id FROM `payment` WHERE payment_id = gl.`payment_id`))   
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 5 THEN (SELECT gv.general_vendor_name FROM general_vendor gv WHERE gv.general_vendor_id = (SELECT p.general_vendor_id FROM payment p WHERE p.payment_id = gl.payment_id))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 6 THEN (SELECT ap.account_name FROM account ap WHERE ap.account_id = (SELECT p.account_id FROM payment p WHERE p.payment_id = gl.payment_id ))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 8 THEN (SELECT gv.general_vendor_name FROM general_vendor gv WHERE gv.general_vendor_id = (SELECT p.general_vendor_id FROM payment p WHERE p.payment_id = gl.payment_id))                
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT cust.customer_name FROM customer cust WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM payment  WHERE payment_id = gl.payment_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT cust.customer_name FROM customer cust WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM payment  WHERE payment_id = gl.payment_id)))				
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DISTINCT(gv.general_vendor_name) FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.invoice_id = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT DISTINCT(gv.general_vendor_name) FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT general_vendor_name FROM general_vendor  WHERE general_vendor_id = (SELECT general_vendor_id FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id))
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 AND (SELECT general_vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id = gl.jurnal_id)) IS NOT NULL THEN (SELECT general_vendor_name FROM general_vendor  WHERE general_vendor_id = (SELECT general_vendor_id FROM payment  WHERE payment_id = gl.payment_id))
		ELSE '' END AS supplier_name,
				 
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT contract_no FROM contract WHERE contract_id = gl.contract_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT sales_no FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.transaction_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT sales_no FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.transaction_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN invoice_detail id ON i.`invoice_id` = id.invoice_id WHERE id.`invoice_detail_id` = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT c.contract_no FROM contract c LEFT JOIN gl_add jm ON jm.contract_id = c.contract_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS contract_no,

	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT po_no FROM contract WHERE contract_id = gl.contract_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN  (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id)))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id)))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN invoice_detail id ON i.`invoice_id` = id.invoice_id WHERE id.`invoice_detail_id` = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT c.po_no FROM contract c LEFT JOIN gl_add jm ON jm.contract_id = c.contract_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS po_no,
			
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 12) THEN (SELECT invoice_no FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN gl_add jm ON jm.invoice_id = i.invoice_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS invoice_no,
			
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT tax_invoice FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_tax FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_tax FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id ORDER BY id.invoice_detail_id DESC LIMIT 1)
		ELSE '' END AS tax_invoice,
                
	CASE WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no2 FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id ORDER BY id.invoice_detail_id DESC LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no2 FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT i.invoice_no2 FROM invoice i LEFT JOIN gl_add jm ON jm.invoice_id = i.invoice_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS invoice_no_2,
				
	CASE WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.payment_status FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id)
		ELSE '' END AS invoice_payment,
               
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT cheque_no FROM payment WHERE payment_id = gl.payment_id)
		ELSE '' END AS cheque_no,
                
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 8) THEN  (SELECT shipment_no FROM shipment WHERE shipment_id = (SELECT shipment_id FROM payment WHERE payment_id = gl.payment_id)) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT shipment_no FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.transaction_id ))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT GROUP_CONCAT(sh2.shipment_no) FROM payment_detail pd2 LEFT JOIN shipment sh2 ON sh2.shipment_id = pd2.shipment_id WHERE pd2.payment_id = gl.payment_id GROUP BY pd2.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DISTINCT(sh.shipment_no) FROM shipment sh LEFT JOIN invoice_detail id ON sh.shipment_id = id.shipment_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.invoice_id = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT DISTINCT(sh.shipment_no) FROM shipment sh LEFT JOIN payment_cash pc ON sh.shipment_id = pc.shipment_id WHERE pc.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT shipment_no FROM shipment WHERE shipment_id = (SELECT shipment_id FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id))
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT sh.shipment_no FROM shipment sh LEFT JOIN gl_add jm ON jm.shipment_id = sh.shipment_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS shipment_code,
				
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN (SELECT adjustment FROM contract WHERE contract_id = gl.contract_id)
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT quantity FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.account_id = 8 THEN (SELECT quantity FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.account_id = 52 THEN (SELECT shrink FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.account_id = 147 THEN (SELECT send_weight FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT quantity FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id ))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT quantity FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT COALESCE(SUM(t2.quantity), 0) FROM `transaction` t2 WHERE t2.payment_id = gl.payment_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT freight_quantity FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT COALESCE(SUM(t2.quantity), 0) FROM `transaction` t2 WHERE t2.fc_payment_id = gl.payment_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT quantity FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT COALESCE(SUM(t2.quantity), 0) FROM `transaction` t2 WHERE t2.uc_payment_id = gl.payment_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT quantity FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT COALESCE(SUM(sh2.quantity), 0) FROM payment_detail pd2 LEFT JOIN shipment sh2 ON sh2.shipment_id = pd2.shipment_id WHERE pd2.payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8) THEN (SELECT qty FROM payment WHERE payment_id = gl.payment_id ) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.quantity FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT qty FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT pc.qty FROM payment_cash pc WHERE pc.`payment_cash_id` = gl.cash_id)
		ELSE '' END AS quantity,
				
	 CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT price_converted FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT unit_price FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT price_converted FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id ))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT price_converted FROM contract WHERE contract_id = gl.contract_id)  
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT GROUP_CONCAT(t2.unit_price) FROM `transaction` t2 WHERE t2.payment_id = gl.payment_id GROUP BY t2.payment_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT freight_price FROM `transaction` WHERE transaction_id = gl.transaction_id)  
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT GROUP_CONCAT(t2.freight_price) FROM `transaction` t2 WHERE t2.payment_id = gl.payment_id GROUP BY t2.payment_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT unloading_price FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT unit_price FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT GROUP_CONCAT(t2.unit_price) FROM `transaction` t2 WHERE t2.payment_id = gl.payment_id GROUP BY t2.payment_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8) THEN (SELECT price FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT price FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT pc.price FROM payment_cash pc WHERE pc.`payment_cash_id` = gl.cash_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.price FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS price,
				
	 CASE WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT notes FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.remarks FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN (SELECT adjustment_notes FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.contract_id IS NOT NULL THEN (SELECT notes FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.transaction_id IS NOT NULL THEN (SELECT notes FROM `transaction` WHERE transaction_id = gl.transaction_id)  
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jd.notes FROM gl_detail jd WHERE jd.gl_detail_id = gl.jurnal_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT pc.notes FROM payment_cash pc WHERE pc.`payment_cash_id` = gl.cash_id)
		ELSE (SELECT remarks FROM payment WHERE payment_id = gl.payment_id )  END AS remarks,
									
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN DATE_FORMAT((SELECT adjustment_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN DATE_FORMAT((SELECT entry_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9) THEN (SELECT unloading_date FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT payment_date FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT DATE_FORMAT(jm.gl_add_date, '%Y-%m-%d') FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS gl_date,
				
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT exchange_rate FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT exchange_rate FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jd.exchange_rate FROM gl_detail jd WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS exchange_rate,
               
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT bank_code FROM bank WHERE bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id ))
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id LIMIT 1)
		ELSE '' END AS bank_code,
				
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT bank_type FROM bank WHERE bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id )) 
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id LIMIT 1)
		ELSE '' END AS bank_type,
				
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT currency_code FROM currency WHERE currency_id = (SELECT currency_id FROM payment WHERE payment_id = gl.payment_id))
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id LIMIT 1)
		ELSE '' END AS pcur_currency_code,
				
(SELECT freight_id FROM payment WHERE payment_id = gl.payment_id) AS freight_id, 
(SELECT labor_id FROM payment WHERE payment_id = gl.payment_id) AS labor_id, 
(SELECT payment_status FROM payment WHERE payment_id = gl.payment_id) AS payment_status, 
(SELECT payment_date FROM payment WHERE payment_id = gl.payment_id) AS payment_date,
(SELECT tax_category FROM tax WHERE tax_id = (SELECT pph_tax_id FROM freight WHERE freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.payment_id ))) AS fc_tax_category, 
(SELECT tax_value FROM tax WHERE tax_id = (SELECT pph_tax_id FROM freight WHERE freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.payment_id ))) AS fc_tax, 
(SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id )  AS gv_id,
				
	CASE WHEN (SELECT payment_location FROM payment WHERE payment_id = gl.payment_id ) = 0 THEN 'HOF'
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id LIMIT 1)
		ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = (SELECT payment_location FROM payment WHERE payment_id = gl.payment_id )) END AS payment_location2,
                
	CASE WHEN gl.contract_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id LEFT JOIN contract c ON c.contract_id = sc.contract_id WHERE c.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1 )
		WHEN gl.payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = gl.payment_id ) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.gl_add_no FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS payment_no,
				
(SELECT t.fc_tax_id FROM `transaction` t WHERE t.transaction_id = gl.transaction_id) AS fc_tax_id , 
(SELECT tax_category FROM tax WHERE tax_id = (SELECT fc_tax_id FROM `transaction` WHERE transaction_id = gl.transaction_id))  AS tf_tax_category,
(SELECT pph_tax_id FROM general_vendor WHERE general_vendor_id = (SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id )) AS gv_pph_id, 
(SELECT ppn_tax_id FROM general_vendor WHERE general_vendor_id = (SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id ))  AS gv_ppn_id, 
(SELECT tax_category FROM tax WHERE tax_id = (SELECT pph_tax_id FROM general_vendor WHERE general_vendor_id = (SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id )))  AS gv_pph_category, 
(SELECT tax_category FROM tax WHERE tax_id = (SELECT ppn_tax_id FROM freight WHERE freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.payment_id ))) AS gv_ppn_category, 
(SELECT account_type FROM account WHERE account_id = gl.account_id) AS  account_type, 
(SELECT tax_value FROM tax WHERE tax_id = (SELECT fc_tax_id FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)) AS fc_tax_value,
(SELECT general_vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id= gl.jurnal_id )) AS general_vendor_id, 
(SELECT vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id= gl.jurnal_id )) AS vendor_id, 
(SELECT payment_method FROM payment WHERE payment_id = gl.payment_id ) AS payment_method
   
 FROM general_ledger gl 
 WHERE  gl.amount > 0
    {$whereProperty}  
	
AND (CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT GROUP_CONCAT(s.stockpile_id) FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = gl.contract_id AND sc.quantity > 0 GROUP BY sc.contract_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT s.stockpile_id FROM stockpile s LEFT JOIN invoice i ON i.stockpileId = s.stockpile_id LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id` WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 3 OR gl.general_ledger_for = 13) THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT s.stockpile_id FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT s.stockpile_id FROM stockpile s INNER JOIN sales sl ON s.stockpile_id = sl.stockpile_id INNER JOIN shipment sh ON sh.sales_id = sl.sales_id INNER JOIN `transaction` t ON t.shipment_id = sh.shipment_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13) THEN (SELECT s.stockpile_id FROM stockpile s INNER JOIN payment p ON (CASE WHEN p.payment_location = 0 THEN 10 ELSE p.payment_location END) = s.stockpile_id WHERE gl.payment_id = p.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT s.stockpile_id FROM stockpile s LEFT JOIN gl_detail jd ON jd.stockpile_id = s.stockpile_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END) IN ({$stockpile_id})
ORDER BY  	
	
	(CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN DATE_FORMAT((SELECT adjustment_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN DATE_FORMAT((SELECT entry_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9) THEN (SELECT unloading_date FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12) THEN (SELECT payment_date FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT DATE_FORMAT(jm.gl_add_date, '%Y-%m-%d') FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END)  ASC, 
				
gl.contract_id ASC, gl.payment_id ASC, gl.transaction_id ASC, gl.general_ledger_id ASC,  	

	(CASE WHEN gl.contract_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id LEFT JOIN contract c ON c.contract_id = sc.contract_id WHERE c.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1 )
		WHEN gl.payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = gl.payment_id ) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.gl_add_no FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END)  ASC, 		 
	
	(CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 12) THEN (SELECT invoice_no FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN gl_add jm ON jm.invoice_id = i.invoice_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END) ASC, gl.general_ledger_type ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $db->error ;
//</editor-fold>

$fileName = "Daily Jurnal " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "N";

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
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Transaction Journal");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Source Module");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Vendor Name");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Transaction Date");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Payment No.");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Slip No.");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Invoice No.");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "PO No.");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Contract No.");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Description");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Account No");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Account Name");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Debit");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Credit");

$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray4);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Body">
$boolColor = true;
$no = 1;
while($row = $result->fetch_object()) {
  
if($row->general_ledger_module == 'NOTA TIMBANG' || $row->general_ledger_module == 'CONTRACT' || $row->general_ledger_module == 'INVOICE DETAIL' || $row->general_ledger_module == 'JURNAL MEMORIAL'){
$debit_amount = $row->amount;					
$credit_amount = $row->amount;
}
//==CREDIT NOTIM==//
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && $row->general_ledger_for == 3 && $row->account_no == 230204){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && $row->general_ledger_for == 3 && $row->account_no == 210103){
$credit_amount = $row->amount;
}
//==END CREDIT NOTIM==//

//==DEBIT NOTIM==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && $row->general_ledger_for == 3 && $row->account_no == 140000){
$debit_amount = $row->amount * ((100 - $row->tf_tax_value) / 100);
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && $row->general_ledger_for == 3 && $row->account_no == 230204){
$debit_amount = 0;
}
//==END DEBIT NOTIM==//				
			
//==PAYMENT CREDIT===//
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PETTY CASH' && $row->general_ledger_for == 12){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT ADMIN'){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 12){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->labor_id != 0 && $row->pph_journal != 0 && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201) ){
$credit_amount = $row->pph_journal;
}
//==FREIGHT==//

if($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0&& $row->payment_status != 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3 && $row->payment_method == 2){
$credit_amount = $row->amount;
}
elseif($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0&& $row->payment_status != 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3){
$credit_amount = $row->amount - $row->pph_journal;
}
elseif($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->payment_status != 1 && $row->fc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 3){
$credit_amount = $row->amount;
}
elseif($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->payment_status != 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3){
$credit_amount = $row->amount ;
}
elseif($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->payment_status != 1 && $row->account_id == 5 && $row->general_ledger_for == 3){
$credit_amount = $row->amount ;
}
elseif($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->payment_method == 2 && $row->freight_id != 0 && $row->payment_status != 1 && $row->pph_journal != 0 && $row->general_ledger_for == 3){
$credit_amount = $row->amount ;
}
//==END FREIGHT==//

//==GENERAL VENDOR==//
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0  && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)){
$credit_amount = $row->amount;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)){
$credit_amount = $row->pph_journal;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->account_no == 150410 || $row->account_no == 230100) && $row->payment_status != 1){
$credit_amount = $row->ppn_journal;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_transaction_type == 1 &&$row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0 && $row->payment_status != 1){
$credit_amount = $row->amount;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->account_type == 7 && $row->gv_pph_id == 21 && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0 && $row->payment_status != 1){
$credit_amount = $row->amount + $row->ppn_journal + $row->pph_journal;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0 && $row->payment_status != 1){
$credit_amount = ($row->amount + $row->ppn_journal) - $row->pph_journal;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && $row->payment_status != 1){
$credit_amount = $row->amount + $row->ppn_journal;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->payment_status != 1 && $row->account_type == 7){
$credit_amount = $row->amount - $row->pph_journal ;
}
elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->payment_status != 1){
$credit_amount = $row->amount;
}
//==END GENERAL VENDOR==//
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_type == 7 && ($row->general_ledger_for == 1 || $row->general_ledger_for == 2)){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_no == 150410 && $row->general_ledger_for == 1){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 4 && $row->account_type == 7 && $row->payment_status != 1){
$credit_amount = $row->amount ;				
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 2 && $row->account_type == 7 && $row->payment_status != 1){
$credit_amount = $row->amount ;				
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->general_ledger_for == 6 && $row->account_type == 7){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_no == 130001 && $row->general_ledger_for == 1){
$credit_amount = $row->amount;
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->account_type == 7 && $row->pph_journal != 0 && $row->ppn_journal != 0 && $row->payment_status != 1 && $row->general_ledger_method == 'Down Payment'){
$credit_amount = $row->amount + $row->ppn_journal - $row->pph_journal;
}
//==END PAYMENT CREDIT===//					  

//==PAYMENT DEBIT==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PETTY CASH' && $row->general_ledger_for == 12){
$debit_amount = $row->amount;
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT ADMIN'){
$debit_amount = $row->amount;
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 12){
$debit_amount = $row->amount;
}	
 //==FREIGHT==//

if($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->general_ledger_method == 'DP' && $row->payment_status != 1 && ($row->fc_tax_category == 0 || $row->fc_tax_category == 1) && 	($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201) ){
$debit_amount = $row->amount;
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 0 && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201) ){
$debit_amount = 0;
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 1 && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201) ){
$debit_amount = 0;
}

elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->general_ledger_method == 'Down Payment' && $row->freight_id != 0 && $row->fc_tax_category == 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status != 1){
$debit_amount = $row->amount;				
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->payment_status != 1 && $row->fc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 3){
$debit_amount = $row->amount;
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->payment_status != 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3){
$debit_amount = $row->amount ;
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status != 1){
$debit_amount = $row->amount * ((100 - $row->fc_tax)/100);				
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status != 1){
$debit_amount = $row->amount;				
}
//==END FREIGHT==//
//==UC==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for2 == 'Unloading Cost' && $row->account_type == 7 && $row->payment_status != 1){
$debit_amount = $row->amount ;				
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT'  && $row->account_no == 210104 && $row->payment_status != 1){
$debit_amount = $row->amount ;				
}
//==END UC==//

//==PKS KONTRAK//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->account_no == 210102 && $row->payment_status != 1){
$debit_amount = $row->amount;
}
//==END PKS KONTRAK//
//==GENERAL VENDOR==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8) && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201 || $row->account_no == 150440) && $row->pph_journal != 0){
$debit_amount = $row->pph_journal;					
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8) && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)){
$debit_amount = $row->amount;					
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->gv_pph_id == 21 && $row->payment_status != 1 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)){
$debit_amount = ($row->amount + $row->ppn_journal + $row->ppn_journal ) + $row->pph_journal;
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->payment_status != 1 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)){
$debit_amount = ($row->amount + $row->ppn_journal + $row->ppn_journal )- $row->pph_journal;
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->payment_status != 1 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)){
$debit_amount = $row->amount + $row->ppn_journal;
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->gv_pph_id == 21 && $row->pph_journal != 0 && $row->payment_status != 1 && $row->account_type == 7){
$debit_amount = $row->amount + $row->pph_journal;
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->payment_status != 1 && $row->account_type == 7){
$debit_amount = $row->amount - $row->pph_journal;
}
elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->payment_status != 1){
$debit_amount = $row->amount;
}
//==END GENERAL VENDOR==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 2 && $row->payment_status != 1){
$debit_amount = $row->amount ;				
}				 						   
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && ($row->account_no == 150410 || $row->account_no == 230100)){
$debit_amount = $row->amount;
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_type == 7 && $row->general_ledger_for == 6){
$debit_amount = $row->amount;
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_no == 130001 && $row->general_ledger_for == 1){
$debit_amount = $row->amount;
}
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->supplier_name == 'Kas Negara' && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)){
$debit_amount = $row->amount;					
}
//==SALES==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 9 && $row->payment_status != 1){
$debit_amount = $row->amount ;			
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 9 && $row->payment_status != 1){
$credit_amount = $row->amount ;				
}	
//==END SALES==//
if($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 10 && $row->payment_status != 1){
$debit_amount = $row->amount ;			
}
if($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT'  && $row->general_ledger_for == 10 && $row->payment_status != 1){
$credit_amount = $row->amount ;				
}	
if($row->payment_status == 1 && $row->gl_date < '2018-04-01'){
$debit_amount = 'RETURN';
$credit_amount = 'RETURN';
}
if($row->invoice_payment == 2 && $row->gl_date < '2018-04-01'){
$debit_amount = 'RETURN';
$credit_amount = 'RETURN';
}				

    if($oldModule != '' && $oldModule == 'PAYMENT') {
        if($oldPaymentId != $row->payment_id) {
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

    } elseif($oldModule != '' && $oldModule == 'NOTA TIMBANG') {
        if($oldTransactionId != $row->transaction_id) {
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
    } elseif($oldModule != '' && $oldModule == 'JURNAL MEMORIAL') {
        if($oldJurnalId != $row->jurnal_id) {
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
    }
	$rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no);
	$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $row->general_ledger_module);
	$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->supplier_name);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->gl_date);
    $voucherNo = "";
    if($row->payment_id != '') {
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
    }else{
		$voucherNo =  $row->payment_no; 
	}
	

	$objPHPExcel->getActiveSheet()->setCellValueExplicit("E{$rowActive}", $voucherNo, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("F{$rowActive}", $row->slip_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G{$rowActive}", $row->invoice_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("H{$rowActive}", $row->po_no, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("I{$rowActive}", $row->contract_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->remarks);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("K{$rowActive}", $row->account_no, PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("L{$rowActive}", $row->account_name, PHPExcel_Cell_DataType::TYPE_STRING);
	$debitAmount = 0;
    if($row->general_ledger_type == 1) {
        $debitAmount = $debit_amount; 
    }
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $debitAmount);
    $creditAmount = 0;
    if($row->general_ledger_type == 2) {
        $creditAmount = $credit_amount;
    }
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $creditAmount);

    $no++;
    
    if($boolColor) {
        $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:L{$rowActive}")->applyFromArray($styleArray4b);
    }
    
    $oldModule = $row->general_ledger_module;
    $oldPaymentId = $row->payment_id;
    $oldTransactionId = $row->transaction_id;
    $oldContractId = $row->contract_id;
	$oldInvoiceId = $row->invoice_id;
	$oldJurnalId = $row->jurnal_id;
	$oldCashId = $row->cash_id;
}
$bodyRowEnd = $rowActive;

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
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");
}

// Set number format for Amount 
$objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow + 1) . ":N{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


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