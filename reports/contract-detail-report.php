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
$whereProperty = '';
$statusProperty = '';
$stockpileId = '';
$vendorId = '';
//$periodFrom = '';
//$periodTo = '';
//$status = '';
//$lastPoNo = '';
$contract_no = '';

if(isset($_POST['contract_no']) && $_POST['contract_no'] != '') {
    $contract_no = $_POST['contract_no'];
for ($i = 0; $i < sizeof($contract_no); $i++) {
                        if($contract_nos == '') {
                            $contract_nos .= "'". $contract_no[$i] ."'";
                        } else {
                            $contract_nos .= ','. "'". $contract_no[$i] ."'";
                        }
                    }
			$whereProperty .= " AND b.po_pks_id IN ({$contract_nos}) ";		
					
			}

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'";
                        }
                    }
			$whereProperty .= " AND b.vendor_id IN ({$vendorIds}) ";		
					
}
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
			$whereProperty .= " AND f.stockpile_id IN ({$stockpileIds}) ";		
					
}

/*if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
}

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    $whereProperty1 .= " AND con.vendor_id = {$vendorId} ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$periodTo}','%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	$whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$periodTo}','%d/%m/%Y') ";
}

if(isset($_POST['status']) && $_POST['status'] != '') {
    $status = $_POST['status'];
    
    if($status == 0) {
        $statusProperty .= " AND a.quantity_received = 0 ";
    } elseif($status == 1) {
        $statusProperty .= " AND a.quantity <= a.quantity_received ";
    } elseif($status == 2) {
        $statusProperty .= " AND a.quantity > a.quantity_received ";
    }
}*/

$sql = "SELECT a.po_pks_id,d.`vendor_name`, e.`stockpile_name`, b.`contract_no`, b.`quantity` AS qty,  c.`po_no`, c.`entry_date` AS tgl_po, f.`quantity`, f.stockpile_id,
(SELECT SUM(send_weight) FROM `transaction` WHERE stockpile_contract_id = f.stockpile_contract_id) AS total_receive, 
(SELECT GROUP_CONCAT(payment_date) FROM payment WHERE stockpile_contract_id = f.stockpile_contract_id) AS payment_date,
(SELECT GROUP_CONCAT(payment_no) FROM payment WHERE stockpile_contract_id = f.stockpile_contract_id) AS payment_no
FROM po_contract a
LEFT JOIN po_pks b ON b.`po_pks_id` = a.`po_pks_id`
LEFT JOIN contract c ON a.`contract_id` = c.`contract_id`
LEFT JOIN vendor d ON d.`vendor_id` = b.`vendor_id`
LEFT JOIN stockpile_contract f ON f.`contract_id` = c.`contract_id`
LEFT JOIN stockpile e ON e.`stockpile_id` = f.`stockpile_id`
WHERE d.`vendor_name` IS NOT NULL {$whereProperty}
ORDER BY c.`po_no` ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<script type="text/javascript">
 $(document).ready(function () {
	 
	 $('#printPoDetail').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#poDetail").printThis();
//            $("#transactionContainer").hide();
        });
		
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/contract-detail-report.php', {
                
                stockpileId: $('input[id="stockpileIds"]').val(), 
                vendorId: $('input[id="vendorIds"]').val(),
				contract_no: $('input[id="contract_nos"]').val()				
                //periodFrom: $('input[id="periodFrom"]').val(),
               // periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/contract-detail-report-xls.php">
   
    <input type="hidden" id="vendorIds" name="vendorIds" value="<?php echo $vendorIds; ?>" />
	<input type="hidden" id="contract_nos" name="contract_nos" value="<?php echo $contract_nos; ?>" />
	<input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    <button class="btn btn-success">Download XLS</button>
	<button class="btn btn-info" id="printPoDetail">Print</button>
</form>

<div id = "poDetail">
<li class="active">PO Detail Summary Report</li>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th >No</th>
            <th >Vendor name</th>
            <th >Contract No</th>
            <th >Quantity</th>
            <th >Stockpile Name</th>
            <th >PO No</th>
            <th >PO Date</th>
            <th >PO Qty</th>
            <th >Total Received</th>
			<th >Outstanding Balance</th>
            <th >Payment Date</th>
            <th >Payment No</th>
            
        </tr>
        
    </thead>
    <tbody>
        <?php
        if($result->num_rows > 0) {
            $no = 0;
            while($row = $result->fetch_object()) {
				
				$outstanding = $row->quantity - $row->total_receive;
                
                ?>
        <tr>
                <?php
                if($row->po_pks_id == $lastPoNo) {
                    $counter++;
                ?>
            <td></td>
			<td></td>
            <td></td>
            <td></td>
            
                <?php
                } else {
                    $sqlCount = "SELECT count(1) AS total_row
                                FROM po_contract a
LEFT JOIN po_pks b ON b.`po_pks_id` = a.`po_pks_id`
LEFT JOIN contract c ON a.`contract_id` = c.`contract_id`
LEFT JOIN vendor d ON d.`vendor_id` = b.`vendor_id`
LEFT JOIN stockpile_contract f ON f.`contract_id` = c.`contract_id`
LEFT JOIN stockpile e ON e.`stockpile_id` = f.`stockpile_id`
WHERE d.`vendor_name` IS NOT NULL AND b.po_pks_id = {$row->po_pks_id}
ORDER BY c.`po_no` ASC";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
                    $counter = 1;
                    
                    
                    $vendor_name = $row->vendor_name;
                    $contract_no = $row->contract_no;
                    $qty = $row->qty;
                
                    
                    $no++;
                ?>
            <td><?php echo $no; ?></td>
            <td><?php echo $vendor_name; ?></td>
            
			 <td><?php echo $contract_no; ?></td>
            <td style="text-align: right;"><?php echo number_format($qty, 2, ".", ","); ?></td>
            
                <?php
                }
               
                ?>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->tgl_po; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->total_receive, 2, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($outstanding, 2, ".", ","); ?></td>
            <td><?php echo $row->payment_date; ?></td>
            <td><?php echo $row->payment_no; ?></td>
            
        </tr>              
                
                
                    <?php
                    $lastPoNo = $row->po_pks_id;
            }
        }
        ?>
    </tbody>
</table>
</div>
