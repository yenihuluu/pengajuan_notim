<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

//$whereProperty = '';
$vendorId = '';

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    
    //$whereProperty = " AND sh.shipment_id = {$shipmentId} ";
}

$sql = "SELECT con.*, cur.currency_code,v.vendor_name, v.vendor_code,
            CASE when con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2, a.stockpile_name
        FROM contract con
        INNER JOIN currency cur
            ON cur.currency_id = con.currency_id
        INNER JOIN vendor v
            ON v.vendor_id = con.vendor_id
		INNER JOIN stockpile_contract c
			ON con.contract_id = c.contract_id
		INNER JOIN stockpile a
			ON a.stockpile_id = c.stockpile_id
        WHERE con.company_id = {$_SESSION['companyId']}
		AND v.vendor_id = '{$vendorId}'
        ORDER BY a.stockpile_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<br />
<form method="post" action="reports/contract-report-xls.php">
    <input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr><th rowspan="2">No</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">Vendor Code</th>
            <th rowspan="2">Contract Name</th>
            
            
            <th rowspan="2">Contract No.</th>
            <th rowspan="2">Price/KG</th>
            <th rowspan="2">Quantity Order</th>
            <th colspan="6">Stockpile</th>
            
        </tr>
        
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
            $no = 1;
			
            while($row = $result->fetch_object()) {
               // $totalCogs = $row->cogs_amount + $row->freight_total + $row->unloading_total;
                ?>
        <tr>
        	<td><?php echo $no?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->vendor_code; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
           
            
            
            <td><?php echo $row->contract_no; ?></td>
            <td><div style="text-align: right;"><?php echo number_format($row->price_converted, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ","); ?></div></td>
            <td><?php echo $row->stockpile_name; ?></td>
            
        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>
