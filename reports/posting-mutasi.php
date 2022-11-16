<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$whereProperty = '';
$sumProperty = '';
$mutasiHeaderId = '';
if (isset($_POST['mutasiHeaderId']) && $_POST['mutasiHeaderId'] != '') {
    $mutasiHeaderId = $_POST['mutasiHeaderId'];


    $sql = "select t.transaction_id, t.slip_no, t.transaction_date, t.send_weight, t.netto_weight, t.quantity, mh.mutasi_header_id, c.contract_no from `transaction` t 
left join mutasi_header mh on t.mutasi_id = mh.mutasi_header_id
left join stockpile_contract sc on t.stockpile_contract_id = sc.stockpile_contract_id
left join contract c on sc.contract_id = c.contract_id
where mh.mutasi_header_id = {$mutasiHeaderId} and t.posting_status = 0 AND t.company_id = {$_SESSION['companyId']}
order by t.slip_no";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function () {	//executed after the page has loaded

        $('#posting').click(function () {
            //alert ('posting');
            $("#postingMutasi").validate({
                submitHandler: function (form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#postingMutasi").serialize(),

                        success: function (data) {
                            var returnVal = data.split('|');

                            if (parseInt(returnVal[3]) != 0)	//if no errors
                            {
                                alertify.set({
                                    labels: {
                                        ok: "OK"
                                    }
                                });
                                alertify.alert(returnVal[2]);

                                if (returnVal[1] == 'OK') {
                                    $('#dataSearch').load('searchs/posting-mutasi.php', {}, iAmACallbackFunction2);
                                    $('#dataContent').load('reports/posting-mutasi.php', {mutasiHeaderId: $('input[id="mutasiHeaderId"]').val()}, iAmACallbackFunction2);
                                }
                            }
                        }
                    });
                }
            });
        });
    });


</script>

<script type="text/javascript">
    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<form method="post" id="postingMutasi">
    <div>

        <button class="btn btn-primary" id="posting">POSTING</button>
        <!--<button class="btn btn-warning" id="return">RETURN</button>-->
    </div>
    <br>
    <table class="table table-bordered table-striped" style="font-size: 8pt;" id="checkTable">
        <thead>
        <tr>
            <td width="50px">
                <div style="text-align: center">
                    <input type="checkbox" onClick="toggle(this)"/>
                </div>
            </td>
            <td>Slip No</td>
            <td>Transaction Date</td>
            <td>Send Weight</td>
            <td>Netto Weight</td>
            <td>Quantity</td>
            <td>Contract No</td>

        </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        if ($result === false) {
            echo 'wrong query';
            echo $sql;
        } else {
            while ($row = $result->fetch_object()) {
                ?>
                <tr>
                    <td>
                        <div style="text-align: center">
                            <input type="hidden" name="mutasiHeaderId" id="mutasiHeaderId"
                                   value="<?php echo $row->mutasi_header_id; ?>"/>
                            <input type="hidden" name="action" id="action" value="posting_mutasi_ok"/>
                            <input type="checkbox" name="checks[]" value="<?php echo $row->transaction_id ?>"/>
                        </div>
                    </td>
                    <td><?php echo $row->slip_no; ?></td>
                    <td><?php echo $row->transaction_date; ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
                    <td><?php echo $row->contract_no; ?></td>
                </tr>
                <?php
                $no++;
            }
        }
        ?>
        </tbody>
    </table>
</form>
