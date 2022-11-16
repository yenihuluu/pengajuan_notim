<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';
$vendorId = '';
$vendorIds = '';
$status = '';
//$stockpileId = '';


if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {

    $vendorId = $_POST['vendorId'];
	
	for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= $vendorId[$i];
                        } else {
                            $vendorIds .= ','. $vendorId[$i];
                        }
                    }
				
				$whereProperty .= " AND gv.general_vendor_id IN ({$vendorIds}) ";
}

if(isset($_POST['status']) && $_POST['status'] != '') {
$status = $_POST['status'];
 
  $whereProperty .= " AND gv.active = {$status} ";
}

$sql = "SELECT gv.`general_vendor_id`, gv.`general_vendor_name`, gv.`general_vendor_address`,gv.`npwp`, gv.`npwp_name`,
gvb.`bank_name`,gvb.`branch`,gvb.`account_no`, gvb.`beneficiary`,gvb.`swift_code`, txppn.tax_name AS tax_ppn, txpph.tax_name AS tax_pph
        FROM general_vendor gv
		LEFT JOIN general_vendor_bank gvb 
			ON gvb.`general_vendor_id` = gv.`general_vendor_id`
		LEFT JOIN tax txppn
			ON txppn.tax_id = gv.ppn_tax_id
		LEFT JOIN general_vendor_pph gvpph
			ON gvpph.`general_vendor_id` = gv.`general_vendor_id`
		LEFT JOIN tax txpph
			ON txpph.tax_id = gvpph.pph_tax_id
		WHERE 1=1 {$whereProperty}
        ORDER BY gv.general_vendor_id DESC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/dwm-report.php', {
					vendorId: $('input[id="vendorIds"]').val(),
					status: $('input[id="status"]').val()
				}, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<form class="form-horizontal" method="post" id="downloadxls" action="reports/dataVendor-report-xls.php" >
         <input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $vendorIds; ?>" />
		 <input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
		 <button class="btn btn-success">Download XLS</button>           
        </form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
        	<th>No</th>
            <th>Vendor Name</th>
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
                if($row->general_vendor_id == $general_vendor_id) {
                    $counter++;
                ?>
				
				<?php
				$sqlCount = "SELECT COUNT(1) AS total_row FROM general_vendor_pph d WHERE d.general_vendor_id = '{$row->general_vendor_id}'";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    if($rowCount->total_row > 1){
						
						$totalRow = $rowCount->total_row;
						?>

						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					<?php
					}else{
						//$totalRow = $rowCount->total_row;
					}?>
			
            
           
                <?php
                } else {
      $sqlCount = "SELECT COUNT(1) AS total_row FROM general_vendor_bank d WHERE d.general_vendor_id = '{$row->general_vendor_id}'";
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
	
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->general_vendor_name; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->general_vendor_address; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->npwp; ?></td>
	<td rowspan="<?php echo $totalRow;?>"><?php echo $row->npwp_name; ?></td>
	<?php } ?>
	<td><?php echo $row->bank_name; ?></td>
	<td><?php echo $row->branch; ?></td>
    <td><?php echo $row->account_no; ?></td>
	<td><?php echo $row->beneficiary; ?></td>
	<td><?php echo $row->swift_code; ?></td>
	<td><?php echo $row->tax_pph; ?></td>
	<td><?php echo $row->tax_ppn; ?></td>
	
	</tr>
	<?php
                $general_vendor_id = $row->general_vendor_id;
            }
        }
        ?>
	</tbody>
	</table>