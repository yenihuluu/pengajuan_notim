<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$whereProperty = '';
$outstanding = '';
$dateFrom = '';
$dateTo = '';

if (isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND pr.entry_date BETWEEN STR_TO_DATE('{$dateFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$dateTo}', '%d/%m/%Y')";
} else if (isset($_POST['dateFrom']) && $_POST['dateFrom'] != '' && isset($_POST['dateTo']) && $_POST['dateTo'] == '') {
    $dateFrom = $_POST['dateFrom'];
    $whereProperty .= " AND pr.entry_date >= STR_TO_DATE('{$dateFrom}', '%d/%m/%Y')";
} else if (isset($_POST['dateFrom']) && $_POST['dateFrom'] == '' && isset($_POST['dateTo']) && $_POST['dateTo'] != '') {
    $dateTo = $_POST['dateTo'];
    $whereProperty .= " AND pr.entry_date <= STR_TO_DATE('{$dateFrom}', '%d/%m/%Y')";
}

if (isset($_POST['stockpileId']) && $_POST['stockpileId']) {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND pr.stockpile_id = {$stockpileId}  ";
}

$sql = "SELECT pr.*,s.stockpile_name,u.user_name,pr.entry_date as tanggal_input, slipLama.slip_no as slip_lama, slipBaru.slip_no as slip_baru FROM pengajuan_retur pr
        LEFT JOIN USER u ON u.user_id = pr.entry_by
        LEFT JOIN stockpile s ON s.stockpile_id = pr.stockpile_id
        LEFT JOIN transaction slipLama ON slipLama.transaction_id = pr.slip_lama
        LEFT JOIN transaction slipBaru ON slipBaru.transaction_id = pr.slip_baru
        WHERE pr.entry_date IS NOT NULL {$whereProperty}
        ";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/pengajuan-retur-report.php', {
                    dateFrom: document.getElementById('dateFrom').value,
                    dateTo: document.getElementById('dateTo').value,
                    stockpileId: document.getElementById('stockpileId').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<br/>
<form method="post" id="downloadxls" action="reports/pengajuan-retur-xls.php">
    <input type="hidden" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom; ?>"/>
    <input type="hidden" id="dateTo" name="dateTo" value="<?php echo $dateTo; ?>"/>
	<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>"/>
    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th>Status</th>
        <th>Slip Lama</th>
        <th>Stockpile</th>
        <th>Tanggal Notim</th>
        <th>Tanggal Return</th>
        <th>Slip Baru</th>
        <th>Alasan</th>
        <th>Status Notim</th>
        <th>Requester</th>
        <th>Request Date</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($resultData !== false && $resultData->num_rows > 0) {
        while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <?php
                if ($rowData->status == 1) {
                    echo "<td style='font_weight: bold; color: yellowgreen;'>";
                    echo "APPROVED";
                    echo "</td>";
                } elseif ($rowData->status == 2) {
                    echo "<td style='font_weight: bold; color: green;'>";
                    echo "FINISH";
                    echo "</td>";
                } else {
                    echo "<td style='font_weight: bold; color: blue;'>";
                    echo "PENGAJUAN";
                    echo "</td>";
                }
                ?>
                <td><?php echo $rowData->slip_lama; ?></td>
                <td><?php echo $rowData->stockpile_name; ?></td>
                <td><?php echo $rowData->tanggal_notim; ?></td>
                <td><?php echo $rowData->tanggal_return; ?></td>
                <td><?php echo $rowData->slip_baru; ?></td>
                <td><?php echo $rowData->remarks; ?></td>
                <td><?php echo "LEVEL " . $rowData->status_notim; ?></td>
                <td><?php echo $rowData->user_name; ?></td>
                <td><?php echo $rowData->tanggal_input; ?></td>
            </tr>
        <?php }
    } else { ?>
        <tr>
            <td colspan="15">
                No data to be shown.
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
