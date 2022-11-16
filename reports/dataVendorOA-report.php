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
$freightSupplier = '';
$stockpileId = '';
$status = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
				
				
    $whereProperty1 .= " AND fc.stockpile_id IN ({$stockpileIds}) ";
		
}

if(isset($_POST['freightSupplier']) && $_POST['freightSupplier'] != '') {
    $freightSupplier = $_POST['freightSupplier'];
	
	for ($i = 0; $i < sizeof($freightSupplier); $i++) {
                        if($freightSuppliers == '') {
                            $freightSuppliers .= "'". $freightSupplier[$i] ."'";
                        } else {
                            $freightSuppliers .= ','. "'". $freightSupplier[$i] ."'";
                        }
                    }
					
    $whereProperty2 .= " AND fc.freight_id IN ({$freightSuppliers}) ";
		
}

if(isset($_POST['status']) && $_POST['status'] != '') {
$status = $_POST['status'];
 
  $whereProperty2 .= " AND f.active = {$status} ";
}

$sql = "SELECT DISTINCT(fc.`price_converted`), fc.freight_cost_id, fc.freight_id, fc.`vendor_id`, fc.`stockpile_id`, v.vendor_name, f.freight_code, f.`freight_supplier`, f.`freight_address`,
f.`npwp`,fb.`beneficiary`, fb.`bank_name`, fb.`account_no`, fb.`swift_code`, txpph.`tax_name` AS pph, txppn.`tax_name` AS ppn, s.`stockpile_name`, f.npwp_name, fb.branch
FROM freight_cost fc 
LEFT JOIN freight f ON fc.`freight_id` = f.`freight_id`
LEFT JOIN freight_bank fb ON fb.freight_id = f.freight_id
LEFT JOIN vendor v ON v.`vendor_id` = fc.`vendor_id`
LEFT JOIN tax txpph ON txpph.`tax_id` = f.`pph_tax_id`
LEFT JOIN tax txppn ON txppn.`tax_id` = f.`ppn_tax_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = fc.`stockpile_id`
WHERE 1=1 {$whereProperty1} {$whereProperty2} ORDER BY fc.freight_cost_id DESC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/dataVendorOA-report-xls.php" >
         <input type="hidden" id="freightSuppliers" name="freightSuppliers" value="<?php echo $freightSuppliers; ?>" />
		 <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
		 <input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
        	<th>No</th>
			<th>Stockpile</th>	
			<th>Vendor PKS</th>
            <th>Freight Code</th>
            <th>Freight Name</th>
			<th>Address</th>
			<th>NPWP</th>
			<th>NPWP Name</th>
            <th>Bank Name</th>
			<th>Branch</th>
            <th>Account No</th>
			<th>Beneficiary</th>
			<th>Swift Code</th>
			<th>PPh</th>
            <th>PPN</th>
			<th>Price</th>
            
           
        </tr>
    </thead>
    <tbody>
	<?php
	if($result->num_rows > 0) {
	$no = 0;
	while($row = $result->fetch_object()) {
	
	
?> 
		<tr>	
	 <?php
                if($row->freight_cost_id == $freight_cost_id) {
                    $counter++;
                ?>
            
            
           
                <?php
                } else {
      $sqlCount = "SELECT COUNT(1) AS total_row FROM freight_bank d WHERE d.freight_id = '{$row->freight_id}'";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    if($rowCount->total_row == 0){
						$totalRow = $rowCount->total_row + 1;
					}else{
						$totalRow = $rowCount->total_row;
					}
                    $counter = 1;
                    //echo 'tesst';
                    //$poNo = $row->po_no;
                    //$vendorName = $row->vendor_name;
                    //$contractNo = $row->contract_no;
                    //$unitPrice = $row->price_converted;
                   // $quantityOrder = $row->quantity;
                    //$amountOrder = $row->amount_order;
                    //$totalQuantityReceived = 0;
                    //$totalAmountReceived = 0;
                    
                    $no++;
                    //$balanceQuantity = $row->quantity;
                ?>
	
	<td rowspan="<?php echo $totalRow;?>"><?php echo $no; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->stockpile_name; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->vendor_name; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->freight_code; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->freight_supplier; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->freight_address; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->npwp; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->npwp_name; ?></td>
	<?php } ?>
	<td><?php echo $row->bank_name; ?></td>
	<td><?php echo $row->branch; ?></td>
	<td><?php echo $row->account_no; ?></td>
    <td><?php echo $row->beneficiary; ?></td>
	<td><?php echo $row->swift_code; ?></td>
	<td><?php echo $row->pph; ?></td>
	<td><?php echo $row->ppn; ?></td>
	<td><?php echo number_format($row->price_converted, 2, ".", ","); ?></td>
	
	</tr>
	<?php
                $freight_cost_id = $row->freight_cost_id;
            }
        }
        ?>
	</tbody>
	</table>