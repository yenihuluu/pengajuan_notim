<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';


// Session

require_once PATH_INCLUDE . DS . 'session_variable.php';


// Initiate DB connection

require_once PATH_INCLUDE . DS . 'db_init.php';


$whereProperty1 = '';
$periodFrom = '';
$periodTo = '';
$stockpileId = '';

if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND tr.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty1 .= " AND tr.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if (isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND tr.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}


if (isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    for ($i = 0; $i < sizeof($stockpileId); $i++) {
        if ($stockpileIds == '') {
            $stockpileIds .= $stockpileId[$i];
        } else {
            $stockpileIds .= ',' . $stockpileId[$i];
        }
    }

    $stockpile_name = array();
    $sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result !== false && $result->num_rows > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $stockpile_name[] = $row['stockpile_name'];

            $stockpile_names = "'" . implode("','", $stockpile_name) . "'";
        }
    }

}

if ($stockpileIds == 'all') {
    $stockpileIds = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,17';
    $sql = "CALL `sp_StockMutationReport` ('{$stockpileIds}', '{$periodFrom}', '{$periodTo}')";
} else {
    $sql = "CALL `sp_StockMutationReport` ('{$stockpileIds}', '{$periodFrom}', '{$periodTo}')";
}
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/stock-mutation-report.php', {
                    periodFrom: document.getElementById('periodFrom').value,
                    periodTo: document.getElementById('periodTo').value,
					stockpileId: document.getElementById('stockpileId').value,
                    

                }, iAmACallbackFunction2);
            }, 500);
        });

    });
</script>
<?php echo $sql ?>
    <form class="form-horizontal" id="downloadxls" method="post" action="reports/stock-mutation-report-xls.php">
        <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>"/>
        <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>"/>
        <input type="hidden" id="stockpile_names" name="stockpile_names" value="<?php echo $stockpile_names; ?>"/>
        <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileIds; ?>"/>
        <button class="btn btn-success" id="download">Download XLS</button>

    </form>
    <table class="table table-bordered table-striped" style="font-size: 8pt; text-align:center">
        <thead>
        <tr>
            <th rowspan="5" style="text-align: center; vertical-align: middle; ">No.</th>
            <th rowspan="5" style="text-align: center; vertical-align: middle;">Stockpile</th>
            <th colspan="2" style="text-align: center; vertical-align: middle;"><?php echo $periodFrom ?></th>
            <th colspan="17" style="text-align: center; vertical-align: middle;">Mutation Stock</th>
            <th colspan="4" style="text-align: center; vertical-align: middle;"><?php echo $periodTo ?></th>
        </tr>
        <tr>
            <td colspan="2" rowspan="3" style="text-align: center; vertical-align: middle;"><strong>Beginning Stock
                    Balance</strong></td>
            <td colspan="10" style="text-align: center; vertical-align: middle;"><strong>(IN)</strong></td>
            <td colspan="7" style="text-align: center; vertical-align: middle;"><strong>(OUT)</strong></td>
            <td colspan="2" rowspan="3" style="text-align: center; vertical-align: middle;"><strong>Ending Stock
                    Balance</strong></td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; vertical-align: middle;"><strong>Purchasing</strong></td>
            <td colspan="2" rowspan="2" style="text-align: center; vertical-align: middle;"><strong>Transfer
                    Stock</strong></td>
            <td colspan="2" rowspan="2" style="text-align: center; vertical-align: middle;"><strong>Total Incoming
                    Stock</strong></td>
            <td colspan="3" rowspan="2" style="text-align: center; vertical-align: middle;"><strong>Shipment</strong>
            </td>
            <td colspan="2" rowspan="2" style="text-align: center; vertical-align: middle;"><strong>Transfer
                    Stock</strong></td>
            <td colspan="2" rowspan="2" style="text-align: center; vertical-align: middle;"><strong>Total Outgoing
                    Stock</strong></td>

        </tr>
        <tr>
            <td colspan="3" style="text-align: center; vertical-align: middle;"><strong>PKS Kontrak</strong></td>
            <td colspan="3" style="text-align: center; vertical-align: middle;"><strong>PKS Curah</strong></td>
        </tr>
        <tr>
            <!--            Begin Stock Balance-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>
            <!--            PKS kontrak-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount Price</th>
            <th style="text-align: center; vertical-align: middle;">Amount All</th>
            <!--            PKS Curah-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount Price</th>
            <th style="text-align: center; vertical-align: middle;">Amount All</th>
            <!--            Transfer Stock In-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>
            <!--            Total Incoming Stock-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>
            <!--            Shipment-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th colspan="2" style="text-align: center; vertical-align: middle;">Amount</th>
            <!--            Transfer Stock Out-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>
            <!--            Total Outgoing Stock-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>
            <!--            Ending Balance Stock-->
            <th style="text-align: center; vertical-align: middle;">Qty</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>

        </tr>
        </thead>
        <tbody>
        <?php

        if ($result === false) {

            echo 'wrong query';
            echo $sql;
        } else {
            $no = 1;
            //Deklarasi Var Total
            $grandTotalQtyBeginStock = 0;
            $grandTotalAmountBeginStock = 0;
            $grandTotalPksBaseAmount = 0;
            $grandTotalQtyPksKontrak = 0;
            $grandTotalAmountPksKontrak = 0;
            $grandTotalQtyPksCurah = 0;
            $grandTotalAmountPksCurah = 0;
            $grandTotalQtyTransferStockIn = 0;
            $grandTotalAmountTransferStockIn = 0;
            $grandTotalQtyIncomingStock = 0;
            $grandTotalAmountIncomingStock = 0;
            $grandTotalQtyShipment = 0;
            $grandTotalAmountShipment = 0;
            $grandTotalQtyTransferStockOut = 0;
            $grandTotalAmountTransferStockOut = 0;
            $grandTotalQtyOutgoingStock = 0;
            $grandTotalAmountOutgoingStock = 0;
            $grandTotalQtyEndingStock = 0;
            $grandTotalAmountEndingStock = 0;

            while ($row = $result->fetch_object()) {

                //Incoming & Outgoing & Ending Stock Balance
                $qtyTotalIncoming = round(($row->quantity_available + $row->quantity_pks + $row->quantity_curah + $row->quantity_transferIn), 2);
                $amountTotalIncoming = round(($row->amount_kontrak + $row->amount_curah + $row->transferStock_in), 2);
                $qtyTotalOutgoing = round(($row->quantity_shipment + $row->quantity_transferOut), 2);
                $amountTotalOutgoing = round(($row->amount_shipment + $row->transferStock_out), 2);
                $qtyEndingStockBalance = round(($qtyTotalIncoming + $qtyTotalOutgoing), 2);
                $amountEndingStockBalance = round(($amountTotalIncoming + $amountTotalOutgoing), 2);

                //GrandTotal
                $grandTotalQtyBeginStock = 0;
                $grandTotalAmountBeginStock = 0;
                $grandTotalPksBaseAmount = 0;
                $grandTotalQtyPksKontrak = 0;
                $grandTotalAmountPksKontrak = 0;
                $grandTotalQtyPksCurah = 0;
                $grandTotalAmountPksCurah = 0;
                $grandTotalQtyTransferStockIn = 0;
                $grandTotalAmountTransferStockIn = 0;
                $grandTotalQtyIncomingStock = 0;
                $grandTotalAmountIncomingStock = 0;
                $grandTotalQtyShipment = 0;
                $grandTotalAmountShipment = 0;
                $grandTotalQtyTransferStockOut = 0;
                $grandTotalAmountTransferStockOut = 0;
                $grandTotalQtyOutgoingStock = 0;
                $grandTotalAmountOutgoingStock = 0;
                $grandTotalQtyEndingStock = 0;
                $grandTotalAmountEndingStock = 0;

                $grandTotalQtyBeginStock += $row->quantity_available;
                $grandTotalAmountBeginStock += $row->amount_pks;
                $grandTotalPksBaseAmount += $row->base_amount;
                $grandTotalQtyPksKontrak += $row->quantity_kontrak;
                $grandTotalAmountPksKontrak += $row->amount_kontrak;
                $grandTotalQtyPksCurah += $row->quantity_curah;
                $grandTotalAmountPksCurah += $row->amount_curah;
                $grandTotalQtyTransferStockIn += $row->quantity_transferIn;
                $grandTotalAmountTransferStockIn += $row->transferStock_in;
                $grandTotalQtyIncomingStock += $qtyTotalIncoming;
                $grandTotalAmountIncomingStock += $amountTotalIncoming;
                $grandTotalQtyShipment += $row->quantity_shipment;
                $grandTotalAmountShipment += $row->amount_shipment;
                $grandTotalQtyTransferStockOut += $row->quantity_transferOut;
                $grandTotalAmountTransferStockOut += $row->transferStock_out;
                $grandTotalQtyOutgoingStock += $qtyTotalOutgoing;
                $grandTotalAmountOutgoingStock += $amountTotalOutgoing;
                $grandTotalQtyEndingStock += $qtyEndingStockBalance;
                $grandTotalAmountEndingStock += $amountEndingStockBalance;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $row->stockpile_name; ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->quantity_available, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->amount_pks, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->quantity_kontrak, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->base_amount, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->amount_kontrak, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->quantity_curah, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->base_amount, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->amount_curah, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->quantity_transferIn, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->transferStock_in, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($qtyTotalIncoming, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($amountTotalIncoming, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->quantity_shipment, 0, ".", ","); ?></td>
                    <td colspan="2" style="text-align: center;"><?php echo number_format($row->amount_shipment, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->quantity_transferOut, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($row->transferStock_out, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($qtyTotalOutgoing, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($amountTotalOutgoing, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($qtyEndingStockBalance, 0, ".", ","); ?></td>
                    <td style="text-align: center;"><?php echo number_format($amountEndingStockBalance, 0, ".", ","); ?></td>
                </tr>


                <?php
                $no++;
            }
        }
        ?>
        <tr>
            <td colspan="2" style="text-align: center; vertical-align: middle;"><strong>GRAND TOTAL</strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyBeginStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountBeginStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyPksKontrak, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalPksBaseAmount, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountPksKontrak, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyPksCurah, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalPksBaseAmount, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountPksCurah, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyTransferStockIn, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountTransferStockIn, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyIncomingStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountIncomingStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyShipment, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;" colspan="2"><strong><?php echo number_format($grandTotalAmountShipment, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyTransferStockOut, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountTransferStockOut, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyOutgoingStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountOutgoingStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalQtyEndingStock, 0, ".", ","); ?></strong></td>
            <td style="text-align: center;"><strong><?php echo number_format($grandTotalAmountEndingStock, 0, ".", ","); ?></strong></td>

        </tr>
        </tbody>
    </table>
<?php
