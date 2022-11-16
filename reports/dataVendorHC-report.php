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
$vendorHandling = '';
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
					
    $whereProperty1 .= " AND vhc.stockpile_id IN ({$stockpileIds}) ";
		
}

if(isset($_POST['vendorHandling']) && $_POST['vendorHandling'] != '') {
    $vendorHandling = $_POST['vendorHandling'];
	
	for ($i = 0; $i < sizeof($vendorHandling); $i++) {
                        if($vendorHandlings == '') {
                            $vendorHandlings .= "'". $vendorHandling[$i] ."'";
                        } else {
                            $vendorHandlings .= ','. "'". $vendorHandling[$i] ."'";
                        }
                    }
					
    $whereProperty2 .= " AND vhc.vendor_handling_id IN ({$vendorHandlings}) ";
		
}

if(isset($_POST['status']) && $_POST['status'] != '') {
$status = $_POST['status'];
 
  $whereProperty2 .= " AND vh.active = {$status} ";
}

$sql = "SELECT DISTINCT(vhc.`price_converted`), vhc.`handling_cost_id`, vhc.`vendor_handling_id`, vhc.`vendor_id`, vhc.`stockpile_id`, v.vendor_name, vh.vendor_handling_code, vh.vendor_handling_name, vh.`vendor_handling_address`,
vh.`npwp`,vh.`beneficiary`, vh.`bank_name`, vh.`account_no`, vh.`swift_code`, txpph.`tax_name` AS pph, txppn.`tax_name` AS ppn, s.`stockpile_name`, vh.npwp_name, vh.branch
FROM vendor_handling_cost vhc 
LEFT JOIN vendor_handling vh ON vhc.`vendor_handling_id` = vh.`vendor_handling_id`
LEFT JOIN vendor_handling_bank vhb ON vhb.vendor_handling_id = vh.vendor_handling_id
LEFT JOIN vendor v ON v.`vendor_id` = vhc.`vendor_id`
LEFT JOIN tax txpph ON txpph.`tax_id` = vh.`pph_tax_id`
LEFT JOIN tax txppn ON txppn.`tax_id` = vh.`ppn_tax_id`
LEFT JOIN stockpile s ON s.`stockpile_id` = vhc.`stockpile_id`
WHERE 1=1 {$whereProperty1} {$whereProperty2} ORDER BY vhc.`handling_cost_id` DESC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
         <form class="form-horizontal" method="post" action="reports/dataVendorHC-report-xls.php" >
         <input type="hidden" id="vendorHandlings" name="vendorHandlings" value="<?php echo $vendorHandlings; ?>" />
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
            <th>Vendor Handling Code</th>
            <th>Vendor Handling Name</th>
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
	$no = 1;
	while($row = $result->fetch_object()) {

	
?> 
	<tr>
	
	<?php
                if($row->handling_cost_id == $handling_cost_id) {
                    $counter++;
                ?>
            
            
           
                <?php
                } else {
      $sqlCount = "SELECT COUNT(1) AS total_row FROM vendor_handling_bank d WHERE d.vendor_handling_id = '{$row->vendor_handling_id}'";
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
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->vendor_handling_code; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->vendor_handling_name; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->vendor_handling_address; ?></td>
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
                $handling_cost_id = $row->handling_cost_id;
            }
        }
        ?>
	</tbody>
	</table>