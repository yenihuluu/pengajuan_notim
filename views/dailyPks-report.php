<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Daily PKS Report';

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#dataSearch').load('searchs/dailyPks-report.php');
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

<!--SELECT COALESCE(SUM(t.quantity), 0)
FROM `transaction` t
INNER JOIN stockpile_contract sc
	ON sc.stockpile_contract_id = t.stockpile_contract_id
WHERE t.unloading_date < STR_TO_DATE('24/07/2013', '%d/%m/%Y') - 2
AND t.transaction_type = 1
AND sc.stockpile_id = 1;


SELECT COALESCE(SUM(t.quantity), 0)
FROM `transaction` t
INNER JOIN stockpile_contract sc
	ON sc.stockpile_contract_id = t.stockpile_contract_id
WHERE t.unloading_date < STR_TO_DATE('24/07/2013', '%d/%m/%Y') - 1
AND t.transaction_type = 1
AND sc.stockpile_id = 1;


SELECT COALESCE(SUM(d.quantity), 0 )
FROM delivery d
INNER JOIN `transaction` t
	ON t.transaction_id = d.transaction_id
INNER JOIN stockpile_contract sc
	ON sc.stockpile_contract_id = t.stockpile_contract_id
WHERE d.delivery_date < STR_TO_DATE('24/07/2013', '%d/%m/%Y') -1
AND sc.stockpile_id = 1;

SELECT sl.quantity - COALESCE(SUM(sh.quantity), 0)
FROM sales sl
LEFT JOIN shipment sh
	ON sh.sales_id = sl.sales_id
	AND sh.shipment_date < STR_TO_DATE('30/08/2014', '%d/%m/%Y') -1
WHERE 1=1
AND sl.stockpile_id = 1;-->
