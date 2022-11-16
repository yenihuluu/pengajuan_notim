<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty= '';
$stockpileId = '';
$periodFrom = '';
$periodTo = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$stockpileId = '';
$stockpileIds = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }

    $whereProperty .= " AND tt.stockpile_id IN ({$stockpileIds}) ";
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
}

$sql = "SELECT t.*, tt.transaction_in, tt.transaction_out, v.vendor_name, t.slip_no, s.stockpile_name, vh.vehicle_name, c.contract_no
FROM transaction_timbangan tt
LEFT JOIN `transaction` t ON tt.transaction_id = t.t_timbangan
LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id
LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN vendor v ON tt.vendor_id = v.vendor_id
LEFT JOIN stockpile s on tt.stockpile_id = s.stockpile_id
LEFT JOIN unloading_cost uc on tt.unloading_cost_id = uc.unloading_cost_id
LEFT JOIN vehicle vh on uc.vehicle_id = vh.vehicle_id
WHERE tt.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
AND tt.netto_weight >= '300' AND tt.tarra_weight != '0' AND tt.send_weight != '0' and t.slip_retur is null AND t.notim_status = 0 {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/vehicle-report.php', {
                     stockpileId: $('select[id="stockpileId"]').val(),
					periodFrom: $('input[id="periodFrom"]').val(),
					periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/vehicle-report-xls.php">
    <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success" id="download">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>No.</th>
            <th>No. Slip</th>
            <th>Stockpile</th>
            <th>Transaction Date</th>
            <th>Transaction IN</th>
            <th>Transaction OUT</th>
            <th>Loading Date</th>
            <th>No Vehicle</th>
            <th>Vehicle</th>
            <th>Driver</th>
            <th>Vendor Name</th>
            <th>Send Weight</th>
            <th>Bruto Weight</th>
            <th>Tarra Weight</th>
            <th>Netto Weight</th>
            <th>Handling Quantity</th>
            <th>Shrink</th>
            <th>Quantity</th>
			<th>Slip</th>
			<th>Contract No</th>
        </tr>
    </thead>
    <tbody>
    <?php

    if ($result === false) {

        echo 'wrong query';
        echo $sql;
    } else {
            $no=1;
        while ($row = $result->fetch_object()) {

            ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->transaction_date; ?></td>
            <td><?php echo $row->transaction_in; ?></td>
            <td><?php echo $row->transaction_out; ?></td>
            <td><?php echo $row->loading_date; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td><?php echo $row->vehicle_name; ?></td>
            <td><?php echo $row->driver; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->bruto_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->tarra_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->handling_quantity, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?></td>
			<td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
			<td><?php echo $row->slip; ?></td>
			<td><?php echo $row->contract_no; ?></td>
        </tr>
                <?php
                $no++;
            }
        }
        ?>
    </tbody>
</table>
<?php
