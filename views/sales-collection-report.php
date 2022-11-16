<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Sales Collection Report';

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#dataSearch').load('searchs/sales-collection-report.php');
    });
</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<div class="alert fade in alert-success" id="successMsgAll" style="display:none;">
    Success Message
</div>
<div class="alert fade in alert-error" id="errorMsgAll" style="display:none;">
    Error Message
</div>

<div id="dataSearch">
    
</div>

<div id="dataContent">
    
</div>
<!--SELECT sh.shipment_id, s.stockpile_name, cust.customer_name, sh.shipment_code, sl.destination, t.vehicle_no, t.quantity, sl.price, sl.price * t.quantity,
p.payment_date, CONCAT(b.bank_name, ' ', cur.currency_code, ' - ', b.bank_account_no) AS bank_full, pd.amount
FROM shipment sh
LEFT JOIN sales sl
	ON sl.sales_id = sh.sales_id
LEFT JOIN stockpile s
	ON s.stockpile_id = sl.stockpile_id	
LEFT JOIN customer cust
	ON cust.customer_id = sl.customer_id
LEFT JOIN `transaction` t
	ON t.shipment_id = sh.shipment_id
LEFT JOIN payment_detail pd
	ON pd.shipment_id = sh.shipment_id
LEFT JOIN payment p
	ON p.payment_id = pd.payment_id
LEFT JOIN bank b
	ON b.bank_id = p.bank_id
LEFT JOIN currency cur
	ON cur.currency_id = b.currency_id
WHERE 1=1
ORDER BY sh.shipment_code, p.entry_date-->