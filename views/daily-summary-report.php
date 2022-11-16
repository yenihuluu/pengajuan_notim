<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Daily Summary Report';

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#dataSearch').load('searchs/daily-summary-report.php');
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


<!--SELECT cal.day, COALESCE(SUM(t.quantity), 0)
FROM calendar cal
LEFT JOIN `transaction` t
	ON cal.day = DAY(t.unloading_date)
	AND MONTH(t.unloading_date) = 7
	AND YEAR(t.unloading_date) = 2013
	AND t.stockpile_contract_id IN (SELECT sc.stockpile_contract_id FROM stockpile_contract sc WHERE sc.stockpile_id = 1)
WHERE 1=1 
GROUP BY cal.day-->