<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';
// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';
// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

if (isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $date = str_replace('/', '-', $periodTo);;
    $period = date("Y-m-d", strtotime($date));

    $sql = "CALL SUMMutasi('{$period}')";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   // echo $sql;
}

?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#contentTable a').click(function (e) {
            e.preventDefault();
            //alert(this.id);
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'detail') {
                e.preventDefault();
                $("#dataContent").fadeOut();
                 $.blockUI({ message: '<h4>Please wait...</h4>' });

                $('#loading').css('visibility','visible');

                $('#dataContent').load('views/report-posting-transit.php', {kodeMutasi: menu[1]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
            }
        });
		
		var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/stock-transit-report.php', {
                    periodTo: document.getElementById('periodTo').value
                   

                }, iAmACallbackFunction2);
            }, 1000);
        });


    });
</script>
<form method="post" id="downloadxls" action="reports/stock-transit-report-xls.php">
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $period; ?>"/>
    <button class="btn btn-success">Download XLS</button>

</form>


<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
<!--    --><?php //echo $sql; ?>
    <thead>
    <tr>
        <th style="text-align: center;">Periode</th>
        <th style="text-align: center;">Mutasi Code</th>

        <th style="text-align: center;">Stockpile From</th>
        <th style="text-align: center;">Stockpile To</th>

        <th style="text-align: center;">Register Date</th>
        <th style="text-align: center;">Amount Register</th>
		<th style="text-align: center;">Send Weight</th>
        <th style="text-align: center;">Netto Stockpile</th>

        <th style="text-align: center;">Posting Date</th>
        
		<th style="text-align: center;">Amount PKS SP</th>
		<th style="text-align: center;">Amount PKS DRAFT</th>
		
        <th style="text-align: center;">Amount Invoice</th>

        <th style="text-align: center;">Total</th>
        <th style="text-align: center;">Inventory Value</th>

        <th style="text-align: center;">Aging</th>

        <th style="text-align: center;">Status</th>

    </tr>

    </thead>

    <tbody>

    <?php

    if ($result->num_rows > 0) {
        $grandTotalAmountPks = 0;
        $grandTotalAmountRegister = 0;
        $grandTotalAmountInvoice = 0;
        $grandTotalInventoryValue = 0;
        while ($row = $result->fetch_object()) {
            $stockpile_from = $row->StockpileAsal;
            $stockpile_to = $row->StockpileTujuan;
            $mutasiHeaderId = $row->mutasi_header_id;
            $amountPksDraft = $row->AmtPKSDraft;
			$amountPksSp = $row->amountPksSp;
            $amountRegister = $row->AmtRegister;
			$sendWeight = $row->sendWeightTransit;
            $nettoStockpile = $row->nettoStockpile;
            $amountInvoice = $row->AmtInvoice;
            $tglRegister = $row->TglRegister;
            $tglPost = $row->TglPost;
            $aging = $row->Aging;
            $inventoryValue = $row->InventoryValue;
            $statusMutasi = $row->MutasiStatus;
            $amountPksInvoice = $amountPksDraft + $amountInvoice;
            $grandTotalAmountPks += $amountPksDraft;
			$grandTotalAmountPksSp += $amountPksSp;
            $grandTotalAmountRegister += $amountRegister;
            $grandTotalAmountInvoice += $amountInvoice;
            $grandTotalInventoryValue += $inventoryValue;

            ?>
            <tr>
                <td style="text-align: center;"><?php echo $period; ?></td>
                <td style="text-align: center;font-size: 12px;font-weight: bold;"><a href="#" id="detail|<?php echo $mutasiHeaderId; ?>"
                                                   role="button"><?php echo $row->kode_mutasi; ?></a></td>

                <td style="text-align: center;"><?php echo $stockpile_from; ?></td>
                <td style="text-align: center;"><?php echo $stockpile_to; ?></td>

                <td style="text-align: center;"><?php echo $tglRegister; ?></td>
                <td style="text-align: center;"><?php echo number_format($amountRegister, 2, ".", ",") ?></td>
                <td style="text-align: center;"><?php echo number_format($sendWeight, 2, ".", ",") ?></td>
                <td style="text-align: center;"><?php echo number_format($nettoStockpile, 2, ".", ",") ?></td>

                <td style="text-align: center;"><?php echo $tglPost; ?></td>
				
				<td style="text-align: center;"><?php echo number_format($amountPksSp, 2, ".", ",") ?></td>
                <td style="text-align: center;"><?php echo number_format($amountPksDraft, 2, ".", ",") ?></td>
                <td style="text-align: center;"><?php echo number_format($amountInvoice, 2, ".", ",") ?></td>

                <td style="text-align: center;"><?php echo number_format($amountPksInvoice, 2, ".", ",") ?></td>
                <td style="text-align: center;"><?php echo number_format($inventoryValue, 2, ".", ",") ?></td>

                <td style="text-align: center;"><?php echo $aging; ?></td>

                <td style="text-align: center;"><?php echo $statusMutasi; ?></td>

            </tr>


            <?php

        }

    } else {
        echo 'wrong query';
    }

    ?>
    <!--            Grand Total-->
    <tr>
        <td style="text-align: center" colspan="5"><strong>Total</strong></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalAmountRegister, 2, ".", ","); ?></td>
        <td></td>
		<td></td>
        <td></td>
		<td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalAmountPksSp, 2, ".", ","); ?></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalAmountPks, 2, ".", ","); ?></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalAmountInvoice, 2, ".", ","); ?></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalInventoryValue, 2, ".", ","); ?></td>
        <td></td>
        <td></td>
		<td></td>
    </tr>
    </tbody>
</table>
<div id="addDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form id="detailForm" method="post" style="margin: 0px;" action="reports/detail-stocktransit-xls.php">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>
            <h3 id="addDetailModalLabel">Detail Stock Transit</h3>
        </div>


        <div class="modal-body" id="addDetailModalForm">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>
            <button class="btn btn-success">Download XLS</button>
            <!--<button class="btn btn-primary">Submit</button>-->
        </div>
    </form>
</div>