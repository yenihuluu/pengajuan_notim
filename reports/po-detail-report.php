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

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
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
}

$sql = "SELECT con.po_no, p.payment_no, DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date, con.contract_no, v.vendor_name, con.price_converted, con.quantity, 
            con.quantity * con.price_converted AS amount_order,
            t.slip_no, DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2, 
            t.send_weight AS quantity_received, t.send_weight * con.price_converted AS amount_received,
			(SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = con.contract_id {$whereProperty2}) AS adjustment
        FROM stockpile_contract sc
        INNER JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        INNER JOIN contract con
            ON con.contract_id = sc.contract_id
        INNER JOIN vendor v
            ON v.vendor_id = con.vendor_id
        INNER JOIN `transaction` t
            ON t.stockpile_contract_id = sc.stockpile_contract_id
        LEFT JOIN payment p
           ON p.stockpile_contract_id = sc.stockpile_contract_id
        WHERE 1=1
		AND con.contract_status <> 2
        AND con.company_id = {$_SESSION['companyId']}
        AND t.company_id = {$_SESSION['companyId']}
        {$whereProperty} {$whereProperty1}
		GROUP BY t.slip_no
        ORDER BY sc.stockpile_contract_id ASC, t.slip_no ASC";
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
                $('#dataContent').load('reports/po-detail-report.php', {
                    stockpileId: $('input[id="stockpileId"]').val(), 
                vendorId: $('input[id="vendorId"]').val(),
				poNo: $('input[id="poNos"]').val(),				
                periodFrom: $('input[id="periodFrom"]').val(),
                periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/po-detail-report-xls.php">
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
    <input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="poNos" name="poNos" value="<?php echo $poNos; ?>" />
    <button class="btn btn-success">Download XLS</button>
	<button class="btn btn-info" id="printPoDetail">Print</button>
</form>

<div id = "poDetail">
<li class="active">PO Detail Summary Report</li>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">No PO</th>
            <th rowspan="2">PAYMENT VOUCHER</th>
			<th rowspan="2">PAYMENT DATE</th>
            <th rowspan="2">Vendor</th>
            <th rowspan="2">No Kontrak</th>
            <th rowspan="2">Address</th>
            <th rowspan="2">Price / Kg</th>
            <th colspan="2">ORDER</th>
            <th colspan="4">RECEIVED</th>
            <th rowspan="2">Balance Qty Order</th>
            <th rowspan="2">Balance Amount Order</th>
            <th rowspan="2">STATUS</th>
        </tr>
        <tr>
            <th>Quantity Order</th>
            <th>Amount Order</th>
            <th>SLIP NO</th>
            <th>TRANSACTION DATE</th>
            <th>RECEIVED QTY</th>
            <th>RECEIVED AMOUNT</th>
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
                <?php
                } else {
                    $sqlCount = "SELECT count(1) AS total_row
                                FROM stockpile_contract sc
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = sc.stockpile_id
                                INNER JOIN contract con
                                    ON con.contract_id = sc.contract_id
                                INNER JOIN vendor v
                                    ON v.vendor_id = con.vendor_id
                                INNER JOIN `transaction` t
                                    ON t.stockpile_contract_id = sc.stockpile_contract_id
                                
                                WHERE 1=1 AND con.po_no = '{$row->po_no}'
								AND con.contract_status <> 2
                                AND con.company_id = {$_SESSION['companyId']}
                                AND t.company_id = {$_SESSION['companyId']}
								
                                ORDER BY sc.stockpile_contract_id, t.slip_no";
                    $resultCount = $myDatabase->query($sqlCount, MYSQLI_STORE_RESULT);
                    $rowCount = $resultCount->fetch_object();
                    $totalRow = $rowCount->total_row;
                    $counter = 1;
                    
                    $poNo = $row->po_no;
                    $vendorName = $row->vendor_name;
                    $contractNo = $row->contract_no;
                    $unitPrice = $row->price_converted;
                    $quantityOrder = $row->quantity;
                    $amountOrder = $row->amount_order;
                    $totalQuantityReceived = 0;
                    $totalAmountReceived = 0;
                    
                    $no++;
                    $balanceQuantity = $row->quantity;
                ?>
            <td><?php echo $no; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->payment_no; ?></td>
			 <td><?php echo $row->payment_date; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->contract_no; ?></td>
            <td></td>
            <td style="text-align: right;"><?php echo number_format($row->price_converted, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->amount_order, 2, ".", ","); ?></td>
                <?php
                }
                
                $balanceQuantity = $balanceQuantity - $row->quantity_received;
                
                $totalQuantityReceived = $totalQuantityReceived + $row->quantity_received;
                $totalAmountReceived = $totalAmountReceived + $row->amount_received;
                ?>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->unloading_date2; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity_received, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->amount_received, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($balanceQuantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($balanceQuantity * $row->price_converted, 2, ".", ","); ?></td>
            <td></td>
        </tr>
                <?php
                $lastPoNo = $row->po_no;
                
                //echo $counter;
                if($counter == $totalRow) {
					if($row->adjustment != 0 && $row->adjustment != ''){
						$amountAdjustment = $row->adjustment * $row->price_converted;
						$amountBalance = $balanceQuantity * $row->price_converted;
						$totalBalance = $amountBalance - $amountAdjustment;
						
                    ?>
		<tr>
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
            <td>ADJUSTMENT</td>
            <td style="text-align: right;"><?php echo number_format($row->adjustment, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($amountAdjustment, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($balanceQuantity - $row->adjustment, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($totalBalance, 2, ".", ","); ?></td>
            <td></td>
        </tr>
					<?php }?>
        <tr>
            <td>BALANCE</td>
            <td><?php echo $poNo; ?></td>
            <td></td>
			<td></td>
            <td><?php echo $vendorName; ?></td>
            <td><?php echo $contractNo; ?></td>
            <td></td>
            <td><?php echo number_format($unitPrice, 2, ".", ","); ?></td>
            <td><?php echo number_format($quantityOrder, 0, ".", ","); ?></td>
            <td><?php echo number_format($amountOrder, 2, ".", ","); ?></td>
            <td></td>
            <td></td>
            <td style="text-align: right;"><?php echo number_format($totalQuantityReceived + $row->adjustment, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($totalAmountReceived + $amountAdjustment, 2, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($balanceQuantity - $row->adjustment, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($totalBalance, 2, ".", ","); ?></td>
            <td><?php if($balanceQuantity - $row->adjustment > 0) { echo 'ON PROGRESS'; } else { echo 'CLOSED'; }?></td>
        </tr>
                    <?php
                }
            }
        }
        ?>
    </tbody>
</table>
</div>
