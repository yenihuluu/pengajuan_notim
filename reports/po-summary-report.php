<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$whereProperty = '';
$whereProperty1 = '';
$whereProperty2 = '';
$whereProperty3 = '';
$vendorId = '';
$vendorIds = '';
$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$paymentFrom = '';
$paymentTo = '';
$inputFrom = '';
$inputTo = '';
$adjustmentTo = '';
$status = '';
$rejectStatus = '';


/*if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
$vendorId = $_POST['vendorId'];
$whereProperty .= " AND c.vendor_id = {$vendorId} ";
}*/

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'";
                        }
                    }
			
    $whereProperty .= " AND c.vendor_id IN ({$vendorIds}) ";
    
}

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= "AND (SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) = {$stockpileId} ";	
}

if(isset($_POST['adjustmentTo']) && $_POST['adjustmentTo'] != '') {
    $adjustmentTo = $_POST['adjustmentTo'];
    $whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$adjustmentTo}','%d/%m/%Y') ";	
}

if(isset($_POST['rejectStatus']) && $_POST['rejectStatus'] != '') {
    $rejectStatus = $_POST['rejectStatus'];
    $whereProperty .= " AND contract_status = {$rejectStatus}";	
}

if(isset($_POST['status']) && $_POST['status'] != '') {
$status = $_POST['status'];
$whereProperty .= " AND (CASE WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +  
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) = 0 THEN 'CLOSED' 
WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) > 0 THEN 'OPEN' ELSE 'OUTSTANDING' END) = UPPER('{$status}') ";
}


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty1 .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

if(isset($_POST['inputFrom']) && $_POST['inputFrom'] != '' && isset($_POST['inputTo']) && $_POST['inputTo'] != '') {
    $inputFrom = $_POST['inputFrom'];
    $inputTo = $_POST['inputTo'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	
	$whereProperty3 .= " AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') <= b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
} else if(isset($_POST['inputFrom']) && $_POST['inputFrom'] != '' && isset($_POST['inputTo']) && $_POST['inputTo'] == '') {
    $inputFrom = $_POST['inputFrom'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
	
	//$whereProperty3 .= " AND DATE_FORMAT(loading_date, '%Y-%m-%d') >= STR_TO_DATE('{$inputFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['inputFrom']) && $_POST['inputFrom'] == '' && isset($_POST['inputTo']) && $_POST['inputTo'] != '') {
    $inputTo = $_POST['inputTo'];
    $whereProperty .= " AND DATE_FORMAT(c.entry_date, '%Y-%m-%d') <= STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
	
	$whereProperty3 .= " AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') <= b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y') ";
}

if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {
    $paymentFrom = $_POST['paymentFrom'];
    $paymentTo = $_POST['paymentTo'];
    $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) BETWEEN STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] != '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] == '') {
    $paymentFrom = $_POST['paymentFrom'];
    $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) >= STR_TO_DATE('{$paymentFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['paymentFrom']) && $_POST['paymentFrom'] == '' && isset($_POST['paymentTo']) && $_POST['paymentTo'] != '') {
    $paymentTo = $_POST['paymentTo'];
	 $whereProperty .= " AND (SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) <= STR_TO_DATE('{$paymentTo}', '%d/%m/%Y') ";
}

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/po-summary-report.php', {
                   stockpileId: $('input[id="stockpileId"]').val(), 
                //contractId: $('select[id="searchContractId"]').val(), 
				vendorId: $('input[id="vendorIds"]').val(), 
                periodFrom: $('input[id="periodFrom"]').val(),
                periodTo: $('input[id="periodTo"]').val(),
				paymentFrom: $('input[id="paymentFrom"]').val(),
                paymentTo: $('input[id="paymentTo"]').val(),
				inputFrom: $('input[id="inputFrom"]').val(),
                inputTo: $('input[id="inputTo"]').val(),
				adjustmentTo: $('input[id="adjustmentTo"]').val(),
                status: $('input[id="status"]').val(),
				rejectStatus: $('input[id="rejectStatus"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/po-summary-report-xls.php">
    
	<input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $vendorIds; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <input type="hidden" id="paymentFrom" name="paymentFrom" value="<?php echo $paymentFrom; ?>" />
    <input type="hidden" id="paymentTo" name="paymentTo" value="<?php echo $paymentTo; ?>" />
	<input type="hidden" id="inputFrom" name="inputFrom" value="<?php echo $inputFrom; ?>" />
    <input type="hidden" id="inputTo" name="inputTo" value="<?php echo $inputTo; ?>" />
	<input type="hidden" id="adjustmentTo" name="adjustmentTo" value="<?php echo $adjustmentTo; ?>" />
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
	<input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
	<input type="hidden" id="rejectStatus" name="rejectStatus" value="<?php echo $rejectStatus; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
			<th rowspan="2">No</th>
            <th rowspan="2">No PO</th>
			<th rowspan="2">Contract No</th>
            <th rowspan="2">Vendor</th>
            <th rowspan="2">Address</th>
            <th rowspan="2">Original Stockpile</th>
            <th colspan="2">ORDER</th>
            <th colspan="20">QTY RECEIVED in STOCKPILE</th>
            <th rowspan="2">Balance Qty Order</th>
			<th rowspan="2">Price / Kg</th>
            <th rowspan="2">Balance Amount Order</th>
			<th rowspan="2">Adjustment Notes</th>
			<th rowspan="2">Payment Voucher</th>
            <th rowspan="2">Payment Date</th>
            <th colspan="2">FIRST RECEIVED</th>
            <th colspan="2">LAST RECEIVED</th>
            <th rowspan="2">STATUS</th>
			<th colspan="4">aging</th>
           
        </tr>
        <tr>
        	<th>Qty Order</th>
            
        	<th>Amount Order</th>
            
            <th>JAMBI</th>
           
            <th>MAREDAN</th>
           
            <th>DUMAI</th>
           
            <th>PADANG</th>
            
            <th>RENGAT</th>
            
            <th>BENGKULU</th>
            
            <th>SAMPIT</th>
           
            <th>TANJUNG BUTON</th>
            
            <th>TAYAN</th>
           
            <th>JAKARTA</th>
			
			<th>PALEMBANG</th>
			
			<th>PANGKALAN BUN</th>
			
			<th>PONTIANAK</th>
			
			<th>SAMARINDA</th>
			
			<th>BATU LICIN</th>
			
			<th>BANGKA BELITUNG</th>
			
			<th>MALOY</th>
			
			<th>ADJUSTMENT</th>
			
			<th>IN TRANSIT</th>
			
			<th>TOTAL RECEIVED</th>
            
            
            <th>Slip No</th>
            
            <th>Date</th>
            
            <th>Slip No</th>
            
            <th>Date</th>
			
			<th>0 - 90</th>
            
            <th>91 - 180</th>
          	 
            <th>181 - 270</th>
           	
            <th>>270</th>
			<th>Date Diff Payment</th>
			<th>Date Diff Receive</th>
            
             </tr>
       
       
    </thead>
    <tbody>
<?php
     

$sql = "SELECT 
(SELECT stockpile_contract_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_contract, 
(SELECT stockpile_id FROM stockpile_contract WHERE quantity > 0 AND contract_id = c.contract_id ORDER BY stockpile_contract_id ASC LIMIT 1) AS stockpile_id, 
c.vendor_id, c.contract_id, DATE_FORMAT(c.entry_date, '%Y-%m-%d') AS entry_date, c.`po_no`, c.`contract_no`,
CASE WHEN c.po_no = 'P-MIL BD Jaya 002' THEN '2015/2/206'
WHEN c.po_no = 'P-KSM/CPM 004' THEN '2015/3/541'
WHEN c.po_no = 'P-KSM/CPM 005' THEN '2015/6/203'
WHEN c.po_no = 'P-KSM/Penyangga 002' THEN '2015/3/84'
WHEN c.po_no = 'P-KSM/SAREH 002' THEN '2015/4/299'
WHEN c.po_no = 'P-PBI' THEN '2015/2/28'
WHEN c.po_no = 'P-PBI2' THEN '2015/3/78'
WHEN c.po_no = 'P-PBI3' THEN '2015/5/392'
WHEN c.po_no = 'P-PBI4' THEN '2015/5/543'
WHEN c.po_no = 'P-PBI(add1)' THEN '2015/1/117'
WHEN c.po_no = 'P-PBI(add2)' THEN '2015/2/44'
WHEN c.po_no = 'P-PBI (AHZ)' THEN '2015/5/328'
WHEN c.po_no = 'P-PBI2 (AHZ) ' THEN '2015/3/417'
WHEN c.po_no = 'P-PBI3 (AHZ)' THEN '2015/3/578' 
ELSE(SELECT GROUP_CONCAT(p.payment_no) FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  AND p.payment_status = 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) END AS payment_no,
(SELECT p.payment_type FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_type,  
(SELECT CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS payment_location, 
(SELECT b.bank_code FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_code,
(SELECT b.bank_type FROM bank b WHERE b.bank_id = (SELECT p.bank_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id  ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS bank_type,
(SELECT cur.currency_code FROM currency cur WHERE cur.currency_id = (SELECT p.currency_id FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id ORDER BY sc.stockpile_contract_id ASC LIMIT 1)) AS currency_code,
CASE WHEN c.po_no = 'P-BSS/JPJ 001' THEN '2015-05-18'
WHEN c.po_no = 'P-BSS/JPJ 001' THEN '2015-05-18'
WHEN c.po_no = 'P-BSS/JPJ 002' THEN '2015-05-25'
WHEN c.po_no = 'P-BSS/JPJ 003' THEN '2015-05-28'
WHEN c.po_no = 'P-BSS/JPJ 004' THEN '2015-06-15'
WHEN c.po_no = 'P-BSS/JPJ 005' THEN '2015-06-19'
WHEN c.po_no = 'P-BSS/JPJ 006' THEN '2015-06-22'
WHEN c.po_no = 'P-BSS/JPJ 007' THEN '2015-06-27'
WHEN c.po_no = 'P-MIL BD Jaya 002' THEN '2015-02-16'
WHEN c.po_no = 'P-KSM/CPM 004' THEN '2015-03-27'
WHEN c.po_no = 'P-KSM/CPM 005' THEN '2015-06-09'
WHEN c.po_no = 'P-KSM/Penyangga 002' THEN '2015-03-05'
WHEN c.po_no = 'P-KSM/SAREH 002' THEN '2015-04-17'
WHEN c.po_no = 'P-PBI' THEN '2015-02-03'
WHEN c.po_no = 'P-PBI2' THEN '2015-03-05'
WHEN c.po_no = 'P-PBI3' THEN '2015-05-20'
WHEN c.po_no = 'P-PBI4' THEN '2015-05-27'
WHEN c.po_no = 'P-PBI(add1)' THEN '2015-01-22'
WHEN c.po_no = 'P-PBI(add2)' THEN '2015-02-03'
WHEN c.po_no = 'P-PBI (AHZ)' THEN '2015-02-26'
WHEN c.po_no = 'P-PBI2 (AHZ) ' THEN '2015-03-23'
WHEN c.po_no = 'P-PBI3 (AHZ)' THEN '2015-05-31' 
ELSE (SELECT GROUP_CONCAT(p.payment_date) FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND p.payment_status = 0 AND payment_type = 2 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) END AS payment_date,
(SELECT vendor_name FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_name, (SELECT vendor_address FROM vendor WHERE vendor_id = c.vendor_id) AS vendor_address,
(SELECT s.stockpile_name FROM stockpile s LEFT JOIN stockpile_contract sc ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1) AS original_stockpile, 
ROUND(c.`price_converted`,2) AS price_converted, ROUND(c.`quantity`,2) AS quantity, ROUND(c.`price_converted` * c.`quantity`,2) AS amount_order, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) AS bengkulu, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) AS buton, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) AS maredan, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) AS padang, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) AS jambi, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) AS dumai, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) AS rengat, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) AS sampit, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) AS tayan, 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) AS jakarta,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) AS palembang,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) AS pangkalan_bun,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) AS pontianak,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) AS samarinda,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) AS batu_licin,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) AS bangka_belitung,
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) AS maloy,  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +    
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0) AS total_received, 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) AS adjustment,
(SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id) AS inTransit, 
(SELECT adjustment_notes FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2} ORDER BY adj_id DESC LIMIT 1) AS adjustment_notes,
(ROUND(c.quantity,0) -
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) + 
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0))) AS balance_qty_order, 
(ROUND((ROUND(c.quantity,0) - 
(IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0))) * c.price_converted, 2)) AS balance_amount_order, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id ASC LIMIT 1) AS first_date, 
(SELECT t.slip_no FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_slip, 
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` {$whereProperty1} AND notim_status = 0 AND slip_retur IS NULL ORDER BY t.transaction_id DESC LIMIT 1) AS last_date, 
CASE WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +  
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) + 
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) = 0 THEN 'CLOSED' 
WHEN ROUND(c.quantity,0) - (IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BEN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUT%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAR%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAD%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'JAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'DUM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'REN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SAM%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'TYN%' {$whereProperty1} ),0) + 
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'HO%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PAL%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BUN%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'PON%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'SMR%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAT%' {$whereProperty1} ),0) +
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'BAN%' {$whereProperty1} ),0) +   
IFNULL((SELECT ROUND(SUM(t.send_weight),2) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` AND t.slip_no LIKE 'MAL%' {$whereProperty1} ),0) +
(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id {$whereProperty2}) +
IFNULL((SELECT 
 SUM(CASE WHEN b.transaction_date IS NULL AND a.status = 0 THEN 0 
 	  WHEN  b.transaction_date >= a.loading_date AND a.status = 1 AND STR_TO_DATE('{$inputTo}', '%d/%m/%Y') < b.transaction_date AND a.loading_date < STR_TO_DATE('{$inputTo}', '%d/%m/%Y')
 	  THEN (a.send_weight) ELSE 0 END) AS transit
 FROM 
 	stock_transit AS a LEFT JOIN
 	TRANSACTION AS b
 ON
 	a.`mutasi_header_id` = b.`mutasi_id`
 	AND a.`stockpile_contract_id` = b.`stockpile_contract_id`
 	AND b.notim_status = 0 AND b.slip_retur IS NULL
 	LEFT JOIN stockpile_contract AS con ON  a.`stockpile_contract_id` = con.`stockpile_contract_id`
 WHERE con.contract_id = c.contract_id),0)) > 0 THEN 'OPEN' 
WHEN c.contract_status = 2 THEN 'REJECTED' ELSE 'OUTSTANDING' END AS `status`, 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) <= 90 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) <= 90 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
(	SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'a', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 91 AND 180 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 91 AND 180 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'b', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 181 AND 270 THEN 
	(SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL 
	AND (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) BETWEEN 181 AND 270 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'c', 
CASE WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1) IS NULL 
	AND (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) >=271 
	THEN (SELECT DATEDIFF(NOW(),(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) 
	WHEN (SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) IS NOT NULL AND 
	(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) >=271 
	THEN (SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1) ,
	(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) ELSE '' END AS 'd',
(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
(SELECT p.payment_date FROM payment p LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE sc.`contract_id` = c.contract_id AND sc.quantity > 0 ORDER BY sc.stockpile_contract_id ASC LIMIT 1))) AS diff_payment,
(SELECT DATEDIFF((SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id DESC LIMIT 1),
(SELECT t.transaction_date FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.`contract_id` ORDER BY t.transaction_id ASC LIMIT 1))) AS diff_receive 
FROM contract c WHERE c.contract_type = 'P' {$whereProperty}
AND c.contract_status != 2 AND c.langsir = 0
GROUP BY c.po_no
ORDER BY c.contract_id ASC ";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

        <?php
		 $no = 1;
        if($result->num_rows > 0) {
           
            while($row = $result->fetch_object()) {
				
			 $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->currency_code;
                
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

			
                ?>
        <tr>
			<td><?php echo $no; ?></td>
            <td><?php echo $row->po_no; ?></td>
			<td><?php echo $row->contract_no; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->vendor_address; ?></td>
            <td><?php echo $row->original_stockpile; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->amount_order, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->jambi, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->maredan, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->dumai, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->padang, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->rengat, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->bengkulu, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->sampit, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->buton, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->tayan, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->jakarta, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->palembang, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->pangkalan_bun, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->pontianak, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->samarinda, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->batu_licin, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->bangka_belitung, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->maloy, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->adjustment, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->inTransit, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->total_received, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->balance_qty_order, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price_converted, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->balance_amount_order, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo $row->adjustment_notes; ?></td>
            <td><?php echo $voucherCode; ?>#<?php echo $row->payment_no; ?></td>
            <td><?php echo $row->payment_date; ?></td>
            <td><?php echo $row->first_slip; ?></td>
            <td><?php echo $row->first_date; ?></td>
            <td><?php echo $row->last_slip; ?></td>
            <td><?php echo $row->last_date; ?></td>
            <td><?php echo $row->status; ?></td>
			<td><?php echo $row->a; ?></td>
			<td><?php echo $row->b; ?></td>
			<td><?php echo $row->c; ?></td>
			<td><?php echo $row->d; ?></td>
			<td><?php echo $row->diff_payment; ?></td>
			<td><?php echo $row->diff_receive; ?></td>
			
			 
			 
        </tr>
                <?php
                $no++;
				//echo $sql;
            }
		} else{
			//echo $sql;
		}
        
        ?>
    </tbody>
</table>

