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

    $sql = "SELECT mh.`mutasi_header_id`, mh.`kode_mutasi`,
    c.pks, SUM(CASE WHEN gl.`general_ledger_type`=1 THEN gl.amount ELSE -gl.amount END) AS total_gl,COALESCE(f.`inventory_value`,0) AS posting_value
    FROM general_ledger gl
    LEFT JOIN invoice_detail id ON id.`invoice_detail_id`=gl.`invoice_id`
    LEFT JOIN invoice i ON i.`invoice_id`=id.`invoice_id`
    LEFT JOIN mutasi_detail md ON md.`mutasi_detail_id`=id.`mutasi_detail_id`
    LEFT JOIN mutasi_header mh ON mh.`mutasi_header_id`=md.`mutasi_header_id`
    LEFT JOIN
    (
    SELECT mutasi_id,transaction_date, SUM(send_weight),SUM(netto_weight), SUM(inventory_value) AS inventory_value FROM TRANSACTION
    WHERE (mutasi_id IS NOT NULL OR mutasi_id) <>0 AND notim_status <>1 AND slip_retur IS NULL
    GROUP BY mutasi_id
    )f ON f. mutasi_id=mh.mutasi_header_id
    LEFT JOIN(
    SELECT SUM(st.`send_weight`*c.`price_converted`) AS PKS, mh.`mutasi_header_id`
    FROM mutasi_header mh
    LEFT JOIN stock_transit st ON mh.`mutasi_header_id` = st.`mutasi_header_id`
    LEFT JOIN stockpile_contract sc ON st.`stockpile_contract_id`=sc.`stockpile_contract_id`
    LEFT JOIN contract c ON c.`contract_id`=sc.`contract_id`
    LEFT JOIN stockpile s ON mh.`stockpile_to` = s.stockpile_id
    WHERE  st.`loading_date`<= '{$period}' 
    GROUP BY mh.`mutasi_header_id`
    )c ON c.mutasi_header_id=mh.`mutasi_header_id`
    WHERE gl.`account_id` IN (455,458)
    AND ((i.`payment_status` = 0 AND i.invoice_status = 0) OR (i.payment_status = 1 AND i.`invoice_status` = 0))
    AND i.`invoice_no`<>'INV/JPJ/2008/306'
    GROUP BY mh.`mutasi_header_id`";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
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

    });
</script>
<form method="post" action="reports/stock-transit-report-xls.php">
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $period; ?>"/>
    <button class="btn btn-success">Download XLS</button>

</form>


<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
<!--    --><?php //echo $sql; ?>
    <thead>
    <tr>
        <th style="text-align: center;">Kode Mutasi</th>

        <th style="text-align: center;">Amount PKS</th>

        <th style="text-align: center;">Amount GL</th>

        <th style="text-align: center;">Posting Value</th>

    </tr>

    </thead>

    <tbody>

    <?php

    if ($result !== false && $result->num_rows > 0) {
        $grandTotalAmountPks = 0;
        $grandTotalAmountGl = 0;
        $grandTotalPostingValue = 0;
        while ($row = $result->fetch_object()) {
            $stockpile_to = $row->stockpile_to;
            $mutasiHeaderId = $row->mutasi_header_id;
            $grandTotalAmountPks += $row->pks;
            $grandTotalAmountGl += $row->total_gl;
            $grandTotalPostingValue += $row->posting_value;

            ?>
            <tr>
                <td style="text-align: center;"><a href="#" id="detail|<?php echo $mutasiHeaderId; ?>"
                                                   role="button"><?php echo $row->kode_mutasi; ?></a></td>
                <td style="text-align: center;"><?php echo number_format($row->pks, 2, ".", ",") ?></td>

                <td style="text-align: center;"><?php echo number_format($row->total_gl, 2, ".", ",") ?></td>

                <td style="text-align: center;"><?php echo number_format($row->posting_value, 2, ".", ",") ?></td>

            </tr>


            <?php

        }

    } else {
        echo 'wrong query';
    }

    ?>
    <!--            Grand Total-->
    <tr>
        <td style="text-align: center"><strong>Total</strong></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalAmountPks, 2, ".", ","); ?></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalAmountGl, 2, ".", ","); ?></td>
        <td style="text-align: center; font-weight: bold"><?php echo number_format($grandTotalPostingValue, 2, ".", ","); ?></td>
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