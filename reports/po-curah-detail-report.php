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
$periodFrom = '';
$periodTo = '';
$status = '';
$lastPoNo = '';
$poNo = '';

if(isset($_POST['poNo']) && $_POST['poNo'] != '') {
    $poNo = $_POST['poNo'];
for ($i = 0; $i < sizeof($poNo); $i++) {
                        if($poNos == '') {
                            $poNos .= "'". $poNo[$i] ."'";
                        } else {
                            $poNos .= ','. "'". $poNo[$i] ."'";
                        }
                    }
			$whereProperty .= " AND con.po_no IN ({$poNos}) ";		
					
			}

/*if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
}*/

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    $whereProperty .= " AND con.vendor_id IN ('{$vendorId}') ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    //$whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	//$whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$periodTo}','%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    //$whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    //$whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
	//$whereProperty2 .= " AND adjustment_date <= STR_TO_DATE('{$periodTo}','%d/%m/%Y') ";
}

/*if(isset($_POST['status']) && $_POST['status'] != '') {
    $status = $_POST['status'];
    
    if($status == 0) {
        $statusProperty .= " AND a.quantity_received = 0 ";
    } elseif($status == 1) {
        $statusProperty .= " AND a.quantity <= a.quantity_received ";
    } elseif($status == 2) {
        $statusProperty .= " AND a.quantity > a.quantity_received ";
    }
}*/

$sql = "SELECT con.po_no, REPLACE(con.contract_no,'-1','') AS contract_no, con.`entry_date` AS contract_date, v.vendor_name, v.`vendor_address`, s.`stockpile_name`, con.price_converted ,con.`quantity`,
(SELECT SUM(quantity) FROM TRANSACTION WHERE stockpile_contract_id = sc.stockpile_contract_id) AS qty_received, uc.`user_name` AS user_contract, 
con.`entry_date2` AS input_contract, upu.`user_name` AS user_purchasing, pu.`entry_date` AS input_purchasing, SUM(t.`quantity`) AS qty_payment,
MIN(t.`transaction_date`) AS first_date, MIN(t.`slip_no`) AS first_slip,MAX(t.`transaction_date`) AS last_date, MAX(t.`slip_no`) AS last_slip,   
p.payment_no, DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date, p.`amount_journal`, p.`entry_date` AS payment_input, up.`user_name` AS user_payment
FROM stockpile_contract sc
LEFT JOIN stockpile s
    ON s.stockpile_id = sc.stockpile_id
LEFT JOIN contract con
    ON con.contract_id = sc.contract_id
LEFT JOIN vendor v
    ON v.vendor_id = con.vendor_id
LEFT JOIN `transaction` t
    ON t.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN payment p
   ON p.payment_id = t.`payment_id`
LEFT JOIN `user` up 
   ON up.`user_id` = p.`entry_by`
LEFT JOIN `user` uc 
   ON uc.`user_id` = con.`entry_by`
LEFT JOIN po_pks po
   ON po.`contract_no` = REPLACE(con.contract_no,'-1','')
LEFT JOIN purchasing pu 
   ON pu.`purchasing_id` = po.`purchasing_id`
LEFT JOIN `user` upu
   ON upu.`user_id` = pu.`entry_by`
WHERE 1=1
AND con.contract_status <> 2
AND con.contract_type = 'C'
{$whereProperty}
GROUP BY sc.`stockpile_contract_id`,t.payment_id
ORDER BY sc.stockpile_contract_id ASC, t.transaction_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/po-curah-detail-report.php', {
                   vendorId: $('input[id="vendorId"]').val() ,
                   poNos: $('input[id="poNos"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id = "downloadxls" action="reports/po-curah-detail-report-xls.php">
    <!--<input type="hidden" id="stockpileId" name="stockpileId" value="<?php //echo $stockpileId; ?>" />-->
    <input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>" />
     <!--<input type="hidden" id="periodFrom" name="periodFrom" value="<?php //echo $periodFrom; ?>" />
     <input type="hidden" id="periodTo" name="periodTo" value="<?php //echo $periodTo; ?>" />-->
	<input type="hidden" id="poNos" name="poNos" value="<?php echo $poNos; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No</th>
            <th>PO No</th>
            <th>Contract No</th>
            <th>Contract Date</th>
			<th>Vendor Name</th>
            <th>Vendor Address</th>
            <th>Stockpile</th>
            <th>Price /Kg</th>
            <th>Qty</th>
            <th>Qty Received</th>
            <th>User Contract</th>
            <th>Contract Input</th>
            <th>User Purchasing</th>
            <th>Purchasing Input</th>
            <th>First Date</th>
            <th>First Slip No</th>
            <th>Last Date</th>
            <th>Last Slip No</th>
            <th>Payment No</th>
            <th>Payment Date</th>
            <th>Qty Payment</th>
            <th>Amount Payment</th>
            <th>Payment Input</th>
            <th>User Payment</th>
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
                if($row->po_no == $lastPoNo) {
                    $counter++;
                ?>
            <td></td>
			<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
                <?php
                } else {
                    $sqlCount = "SELECT COUNT(*) AS total_row
                    FROM stockpile_contract sc
                    LEFT JOIN stockpile s
                        ON s.stockpile_id = sc.stockpile_id
                    LEFT JOIN contract con
                        ON con.contract_id = sc.contract_id
                    LEFT JOIN vendor v
                        ON v.vendor_id = con.vendor_id
                    LEFT JOIN `transaction` t
                        ON t.stockpile_contract_id = sc.stockpile_contract_id
                    LEFT JOIN payment p
                       ON p.payment_id = t.`payment_id`
                    LEFT JOIN `user` up 
                       ON up.`user_id` = p.`entry_by`
                    LEFT JOIN `user` uc 
                       ON uc.`user_id` = con.`entry_by`
                    LEFT JOIN po_pks po
                       ON po.`contract_no` = REPLACE(con.contract_no,'-1','')
                    LEFT JOIN purchasing pu 
                       ON pu.`purchasing_id` = po.`purchasing_id`
                    LEFT JOIN `user` upu
                       ON upu.`user_id` = pu.`entry_by`
                    WHERE 1=1
                    AND con.contract_status <> 2
                    AND con.contract_type = 'C'
                    AND con.po_no = '{$row->po_no}'
                    GROUP BY sc.`stockpile_contract_id`,t.payment_id
                    ORDER BY sc.stockpile_contract_id ASC, t.transaction_id ASC";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
                    $counter = 1;
                    
                   /* $poNo = $row->po_no;
                    $vendorName = $row->vendor_name;
                    $contractNo = $row->contract_no;
                    $unitPrice = $row->price_converted;
                    $quantityOrder = $row->quantity;
                    $amountOrder = $row->amount_order;
                    $totalQuantityReceived = 0;
                    $totalAmountReceived = 0;*/
                    
                    $no++;
                   // $balanceQuantity = $row->quantity;
                ?>
            <td><?php echo $no; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->contract_no; ?></td>
			<td><?php echo $row->contract_date; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->vendor_address; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->price_converted, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->qty_received, 0, ".", ","); ?></td>
            <td><?php echo $row->user_contract; ?></td>
            <td><?php echo $row->input_contract; ?></td>
            <td><?php echo $row->user_purchasing; ?></td>
            <td><?php echo $row->input_purchasing; ?></td>
                <?php
                }
                
                //$balanceQuantity = $balanceQuantity - $row->quantity_received;
                
               // $totalQuantityReceived = $totalQuantityReceived + $row->quantity_received;
                //$totalAmountReceived = $totalAmountReceived + $row->amount_received;
                ?>
            <td><?php echo $row->first_date; ?></td>
            <td><?php echo $row->first_slip; ?></td>
            <td><?php echo $row->last_date; ?></td>
            <td><?php echo $row->last_slip; ?></td>
            <td><?php echo $row->payment_no; ?></td>
            <td><?php echo $row->payment_date; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->qty_payment, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->amount_journal, 2, ".", ","); ?></td>
            <td><?php echo $row->payment_input; ?></td>
            <td><?php echo $row->user_payment; ?></td>
        </tr>
                <?php
                $lastPoNo = $row->po_no;
                
          ?>
                    <?php
                }
            }
        //}
        ?>
    </tbody>
</table>

