<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'List Payment Transaction Report';

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#dataSearch').load('searchs/bank-book-acc-report.php');
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


<!--SELECT p.payment_date, p.payment_no, 
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN v1.vendor_name
WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
WHEN p.labor_id IS NOT NULL THEN l.labor_name
WHEN p.sales_id IS NOT NULL THEN cust.customer_name
ELSE '' END AS supplier, p.bank_id, 
p.payment_notes, p.remarks, a.account_no, a.account_name, 
CASE WHEN a.account_type = 4 THEN 'Non Cangkang' ELSE 'Cangkang' END AS account_type2,
CASE WHEN p.payment_type = 2 THEN p.original_amount ELSE 0 END AS debit_amount,
CASE WHEN p.payment_type = 1 THEN p.original_amount ELSE 0 END AS credit_amount,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN GROUP_CONCAT(t1.slip_no)
ELSE '' END AS slip_no,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN SUM(t1.quantity)
ELSE '' END AS qty,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN t1.unit_price
ELSE '' END AS unit_price,
CASE WHEN p.stockpile_contract_id IS NOT NULL THEN s.stockpile_name
ELSE '' END AS stockpile_name2
FROM payment p
LEFT JOIN stockpile_contract sc
ON sc.stockpile_contract_id = p.stockpile_contract_id
LEFT JOIN stockpile s
ON s.stockpile_id = sc.stockpile_id
LEFT JOIN contract con
ON con.contract_id = sc.contract_id
LEFT JOIN vendor v1
ON v1.vendor_id = con.vendor_id
LEFT JOIN freight f
ON f.freight_id = p.freight_id
LEFT JOIN labor l
ON l.labor_id = p.labor_id
LEFT JOIN sales sl
ON sl.sales_id = p.sales_id
LEFT JOIN customer cust
ON cust.customer_id = sl.customer_id
LEFT JOIN account a
ON a.account_id = p.account_id
LEFT JOIN `transaction` t1
ON t1.payment_id = p.payment_id
AND t1.transaction_type = 1
GROUP BY p.payment_id-->