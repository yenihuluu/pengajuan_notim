<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';

$periodFrom = '';

$periodTo = '';

$amount = '';

$module = '';

$stockpileId = '';

$coaId = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $sql = "SELECT '$periodFrom' AS 'From', '$periodTo' ,general_ledger_module, sum(debitAmount) as debitAmount, sum(creditAmount) as creditAmount, STATUS, COUNT(1) as total_data FROM gl_report
    WHERE  gl_date BETWEEN '$periodFrom' AND '$periodTo' and regenerate = 0
    GROUP BY general_ledger_module,STATUS
    ";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
}


?>
<script>
      $('#contentTable a').click(function (e) {
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'detail') {
                console.log("triggred");
                e.preventDefault();
                $("#modalErrorMsg").hide();
                $('#addJurnalModal').modal('show');
                //            alert($('#addNew').attr('href'));
                $('#addJurnalModalForm').load('forms/view-odoo-summary-report.php', {status: menu[1],module: menu[2],periodFrom: menu[3],periodTo: menu[4]});

            }
        });
</script>


<div class="row-fluid">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
            <thead>
            <tr>
                <th>Journal</th>
                <th>General Ledger Module</th>
                <th>Debit Amount</th>
                <th>Credit Amount</th>
                <th>Status</th>
                <th>Total Data</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($result !== false && $result->num_rows > 0) {
                while ($rowData = $result->fetch_object()) {

                    if($rowData->STATUS == 1){
                        $status = '<div class="badge badge-success">IN Odoo</div>';
                    }else{
                        $status = '<div class="badge badge-warning">NOT IN Odoo</div>';
                    }
                    ?>
                    <tr>
                        <td>
                                <a href="#" id="detail|<?php echo $rowData->STATUS; ?>|<?php echo $rowData->general_ledger_module; ?>|<?php echo $periodFrom; ?>|<?php echo $periodTo; ?>" role="button"><img
                                            src="assets/ico/gnome-print.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/> View Journal</a>
                        </td>
                        <td><?php echo $rowData->general_ledger_module; ?></td>
                        <td><?php echo number_format($rowData->debitAmount, 0, ".", ","); ?></td>
                        <td><?php echo number_format($rowData->creditAmount, 0, ".", ","); ?></td>
                        <td><?php echo $status; ?></td>
                        <td><?php echo $rowData->total_data; ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                <tr>
                    <td colspan="17" style="text-align: center">
                        No data to be shown.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</div>

<div id="addJurnalModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="jurnalModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form  method="post" style="margin: 0px;" action="reports/odoo-summary-report-xls.php">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">×</button>
            <h3 id="addJurnalModalLabel">Detail</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
            Error Message
        </div>
        <input type="hidden" name="modalContractId" id="modalContractId"/>
        <div class="modal-body" id="addJurnalModalForm">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">Close</button>
            <button class="btn btn-success">Download Excel</button>
        </div>
    </form>
</div>