<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty1 = '';
$whereProperty2 = '';
$labor = '';
$status = '';
//$stockpileId = '';



if(isset($_POST['labor']) && $_POST['labor'] != '') {
    $labor = $_POST['labor'];
	
	for ($i = 0; $i < sizeof($labor); $i++) {
                        if($labors == '') {
                            $labors .= "'". $labor[$i] ."'";
                        } else {
                            $labors .= ','. "'". $labor[$i] ."'";
                        }
                    }
					
    $whereProperty2 .= " AND a.labor_id IN ({$labors}) ";
		
}

if(isset($_POST['status']) && $_POST['status'] != '') {
$status = $_POST['status'];
 
  $whereProperty2 .= " AND a.active = {$status} ";
}

?>
         <form class="form-horizontal" method="post" action="reports/dataVendorOB-report-xls.php" >
         <input type="hidden" id="labors" name="labors" value="<?php echo $labors; ?>" />
		 <input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
        	<th>Stockpile</th>
            <th>Labor Name</th>
            <th>Address</th>
            <th>NPWP</th>
			<th>NPWP Name</th>
            <th>Bank Name</th>
			<th>Branch</th>
            <th>Account No</th>
			<th>Beneficiary</th>
			<th>PPh</th>
            <th>PPN</th>
			<th>Vehicle</th>
			<th>Price</th>
			
            
           
        </tr>
    </thead>
    <tbody>
	<?php
$sql1 = "SELECT stockpile_id FROM stockpile";
$result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
if($result1->num_rows > 0) {
while($row1 = $result1->fetch_object()) {

$stockpileId = $row1->stockpile_id;	
	

$sql = "SELECT * FROM 
(SELECT DISTINCT(v.vehicle_id) AS vehicle_id, v.vehicle_name, 
(SELECT labor_name FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS labor,
(SELECT stockpile_name FROM `stockpile` WHERE stockpile_id = (SELECT stockpile_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1) AS stockpile,
(SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                     AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) AS unloading_cost_id,
(SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1) AS labor_id,
(SELECT active FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS active,
(SELECT labor_address FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS labor_address,
(SELECT npwp FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS npwp,
(SELECT npwp_name FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS npwp_name,
(SELECT bank_name FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS bank_name,
(SELECT account_no FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS account_no,
(SELECT branch FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS branch,
(SELECT beneficiary FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1)) AS beneficiary,
(SELECT tax_name FROM tax WHERE tax_id = (SELECT pph_tax_id FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1))) AS pph,
(SELECT tax_name FROM tax WHERE tax_id = (SELECT ppn_tax_id FROM labor  WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE unloading_cost_id = (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) LIMIT 1))) AS ppn,
(SELECT FORMAT(price_converted,2) FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                     AND stockpile_id = {$stockpileId}
                    AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= NOW()
                    ORDER BY entry_date DESC LIMIT 1
                ) AS price_converted				
            FROM vehicle v
            LEFT JOIN unloading_cost uc
                ON uc.vehicle_id = v.vehicle_id
            WHERE 1=1 
		 AND uc.stockpile_id = {$stockpileId}
            ) a WHERE a. price_converted > 0
            {$whereProperty2}
            GROUP BY a.unloading_cost_id 
			ORDER BY a.labor ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
while($row = $result->fetch_object()) {
	
	$labor = $row->labor;
	$labor_address = $row->labor_address;
	$npwp = $row->npwp;
	$npwp_name = $row->npwp_name;
	$bank_name = $row->bank_name;
	$branch = $row->branch;
	$account_no = $row->account_no;
	$beneficiary = $row->beneficiary;
	$pph = $row->pph;
	$ppn = $row->ppn;
	$vehicle_name = $row->vehicle_name;
	$price_converted = $row->price_converted;
	$stockpile = $row->stockpile;
	
	
?> 
	<tr>
	
	<td><?php echo $stockpile; ?></td>
	<td><?php echo $labor; ?></td>
	<td><?php echo $labor_address; ?></td>
	<td><?php echo $npwp; ?></td>
	<td><?php echo $npwp_name; ?></td>
	<td><?php echo $bank_name; ?></td>
	<td><?php echo $branch; ?></td>
	<td><?php echo $account_no; ?></td>
    <td><?php echo $beneficiary; ?></td>
	<td><?php echo $tax_pph; ?></td>
	<td><?php echo $tax_ppn; ?></td>
	<td><?php echo $vehicle_name; ?></td>
	<td style="text-align:right"><?php echo $price_converted;?></td>
	</tr>
	<?php
     }
} 
}
}        
        ?>
	</tbody>
	</table>