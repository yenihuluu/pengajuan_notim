<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$shipmentId = '';
//$stockpileId = '';

/*if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != '') {
    $shipmentId = $_POST['shipmentId'];
    
    $whereProperty = " AND sh.sales_id = {$shipmentId} ";
}*/
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND sh.shipment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";  	
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND sh.shipment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND sh.shipment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    
    $whereProperty = " AND s.stockpile_id = {$stockpileId} ";
}*/
?>
<?php
$sql = "SELECT * FROM (
SELECT sh.`shipment_id`, sh.shipment_date, sh.shipment_no, (t.`quantity` - IFNULL((SELECT SUM(quantity) FROM contract WHERE return_shipment_id = sh.shipment_id),0)) AS quantity,
t.vehicle_no,
(SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTR(t.slip_no,1,3)) AS stockpile,

CASE WHEN sl.localSales = 1 THEN (IFNULL(sh.quantity * sl.price_converted,0))
ELSE 
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 120000 AND gl.transaction_id = t.`transaction_id`),0) - 
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 120000 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0)) END AS salesPrice,

IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 230100 AND gl.transaction_id = t.`transaction_id`),0) - 
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 230100 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0) AS ppn,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 160100 AND gl.transaction_id = t.`transaction_id` ),0))
ELSE 
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id` LIMIT 1 ),0) - 
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0))END AS cogsPrice,

CASE WHEN SUBSTR(shipment_no,-2,2) = '-S' THEN IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510201 AND gl.transaction_id = t.`transaction_id`),0)
WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510101 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510101 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0) END AS cogsPKS,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510102 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510102 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0))END  AS cogsOA,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510103 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510103 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0)) END AS cogsOB,

CASE WHEN sl.localSales = 1 THEN (IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 140000 AND gl.transaction_id = t.`transaction_id`),0))
ELSE
(IFNULL((SELECT gl.amount FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510104 AND gl.transaction_id = t.`transaction_id`),0) -
IFNULL((SELECT SUM(gl.amount) FROM general_ledger gl LEFT JOIN account a ON a.account_id = gl.account_id WHERE a.account_no = 510104 AND gl.transaction_id IN ( 
(SELECT t.transaction_id FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE t.`notim_status` = 0 AND t.`slip_retur` IS NULL AND c.`return_shipment_id` = sh.`shipment_id`))),0)) END AS cogsHandling
FROM shipment sh
LEFT JOIN `transaction` t ON t.`shipment_id` = sh.`shipment_id`
LEFT JOIN adjustment_audit adj ON sh.shipment_id = adj.shipment_id 
LEFT JOIN sales sl ON sl.sales_id = sh.sales_id
WHERE t.transaction_type = 2 {$whereProperty} AND t.notim_status = 0 AND t.slip_retur IS NULL 
AND sh.shipment_no NOT LIKE '%LANGSIR%'
UNION ALL
SELECT sh.shipment_id, aa.adjustment_date AS shipment_date, 'Adjustment Audit' AS shipment_no, aa.quantity, aa.notes AS vehicle_no,
(SELECT stockpile_name FROM stockpile WHERE stockpile_id = aa.stockpile_id) AS stockpile, '0' AS salesPrice,
'0' AS ppn, ((aa.quantity * aa.cogs_pks * -1) +  (aa.quantity * aa.cogs_oa * -1) + (aa.quantity * aa.cogs_ob * -1) + (aa.quantity * aa.cogs_handling * -1)) AS cogsPrice,
(aa.quantity * aa.cogs_pks * -1) AS cogsPKS,(aa.quantity * aa.cogs_oa * -1) AS cogsOA,(aa.quantity * aa.cogs_ob * -1) AS cogsOB,(aa.quantity * aa.cogs_handling * -1) AS cogsHandling
FROM adjustment_audit aa
LEFT JOIN shipment sh ON sh.shipment_id = aa.shipment_id
WHERE aa.adjustment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
) a
ORDER BY a.shipment_date ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
?>
<br />

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/cogs-report-period.php', {
                    periodFrom: document.getElementById('periodFrom').value,
                    periodTo: document.getElementById('periodTo').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<form method="post" id="downloadxls" action="reports/cogs-report-period-xls.php">
    
	<input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        
        <tr>
            <th>Shipment Date</th>
            <th>Shipment Code</th>
            <th>Stockpile</th>
			<th>Quantity</th>
            <th>Sales Price</th>
            <th>COGS Price (Total)</th>
			<th>COGS (PKS)</th>
			<th>COGS (OA)</th>
			<th>COGS (OB)</th>
			<th>COGS (Handling)</th>
			<th>Vessel Name</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
            
            while($row = $result->fetch_object()) {
				
				$salesPrice = $row->salesPrice - $row->ppn; 
    
                ?>
        <tr>
            <td><?php echo $row->shipment_date; ?></td>
            <td><?php echo $row->shipment_no; ?></td>
            <td><?php echo $row->stockpile; ?></td>
			<td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($salesPrice, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->cogsPrice, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->cogsPKS, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->cogsOA, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->cogsOB, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->cogsHandling, 0, ".", ","); ?></td>
			<td><?php echo $row->vehicle_no; ?></td>
           
        </tr>
                <?php
                $no++;
				
				$qty = $qty + $row->quantity;
				$sp = $sp + $salesPrice;
				$cogsPrice = $cogsPrice + $row->cogsPrice;
				$cogsPKS = $cogsPKS + $row->cogsPKS;
				$cogsOA = $cogsOA + $row->cogsOA;
				$cogsOB = $cogsOB + $row->cogsOB;
				$cogsHandling = $cogsHandling + $row->cogsHandling;
            }
        }
        ?>
    </tbody>
	<tfoot>
	<tr>
	<td colspan = "3" style="text-align: right;">GRAND TOTAL</td>
	<td style="text-align: right;"><?php  echo number_format($qty, 0, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($sp, 0, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($cogsPrice, 0, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($cogsPKS, 0, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($cogsOA, 0, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($cogsOB, 0, ".", ","); ?></td>
	<td style="text-align: right;"><?php  echo number_format($cogsHandling, 0, ".", ","); ?></td>
	<td></td>
	</tr>
	</tfoot>
</table>
<?php
//}else{
	?>
<!--<table class="table table-bordered table-striped" style="font-size: 8pt;">
<thead><tr>
<th>No PO</th>
<th>No Kontrak</th>
<th>Status</th>
</tr></thead>
<?php
//while($row3 = $result3->fetch_object()) {
	?>
<tbody>
<tr>

            <td><?php //echo $row3->po_no; ?></td>
            <td><?php //echo $row3->contract_no; ?></td>
            <td><?php //echo "PLEASE LOCK CONTRACT"; ?></td>
           
        </tr>
                <?php
               // $no++;
       //    }
//}
        ?>
    </tbody>
</table> --!>           