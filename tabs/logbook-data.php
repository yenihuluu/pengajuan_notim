<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';


// <editor-fold defaultstate="collapsed" desc="Variable for Logbook Data">

$logbookId = '';
$pLogbookId = '';
//$company = '';
$stockpile = '';
$requesterId = '';
$picFinance = '';
//$category = '';
//$advanceNumber = '';
$invCategoryId = '';
$vendorName = '';
//$mvName = '';
//$qtyPks = '';
$invoiceValue = '';
$paymentDate = '';
$status = '';
$paidTime = '';
$pvNumber = '';
$outstanding = '';
$toBePaid = '';
$paid = '';
$masterBankId = '';
$paidRemarks = '';
$paymentSchedule = '';
$statusDay = '';
$shipmentCode = '';
$vendorType = '';
$requestWeek = '';
$incomplete;
$incompleteRemarks = '';
// </editor-fold>

//PICFINANCE
$sqlUser = "SELECT * FROM `master_pic_finance` WHERE entry_by = {$_SESSION['userId']}";
$resultUser = $myDatabase->query($sqlUser, MYSQLI_STORE_RESULT);
$rowUser = $resultUser->fetch_object();
$picFinance = $rowUser->id;

if (isset($_POST['pLogbookId']) && $_POST['pLogbookId'] != '' && $logbookId === '') {
    $pLogbookId = $_POST['pLogbookId'];
    $sql = "SELECT DATE_FORMAT(pl.entry_date, '%Y-%m-%d') AS tgl, pl.*, mstReq.id AS idreq FROM pengajuan_logbook pl
            LEFT JOIN master_requester mstReq ON mstReq.entry_by = pl.entry_by  where pl.id = {$pLogbookId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $rowData = $result->fetch_object();
        $stockpile = $rowData->stockpile_id;
        $vendorName = $rowData->vendor;
        $remark = $rowData->remark;
        //$qtyPks = $rowData->qty;
        $masterBankId = $rowData->master_bank_id;
        $vendorType = $rowData->vendor_type;
        $invoiceValue = $rowData->total;
        $remarks = $rowData->keterangan;
        $requesterId = $rowData->idreq;
        $requestDateHo = $rowData->tgl;
        $requestDate = $rowData->tgl;
        $invReceive = $rowData->tgl;
//        echo json_encode($result->fetch_object());
    }
}
// If ID is in the parameter
if (isset($_POST['logbookId']) && $_POST['logbookId'] != '') {

    $logbookId = $_POST['logbookId'];

    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            //echo $row->user_id;
            if ($row->user_id != 46 && $row->user_id != 22) {
                $readonlyProperty = 'readonly';
            } else {
                $readonlyProperty = '';
            }
        }
    }

    // <editor-fold defaultstate="collapsed" desc="Query for Logbook Data">

    $sql = "SELECT l.*
            FROM logbook l
            WHERE l.logbook_id = {$logbookId}
            ORDER BY l.logbook_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $requestDateHo = $rowData->request_date_ho;
        $requestDate = $rowData->request_date;
        $requestMonth = $rowData->request_month;
        $requestWeek = $rowData->request_week;
        $emailTime = $rowData->email_time;
        $emailTimeApp = $rowData->email_time_app;
        $invReceive = $rowData->inv_receive;
        $paymentSchedule = $rowData->payment_schedule;
        //$company = $rowData->company_id;
        $stockpile = $rowData->stockpile_id;
        $requesterId = $rowData->master_requester_id;
        $picFinance = $rowData->master_pic_finance_id;
        $category = $rowData->logbook_category_id;
        //$advanceNumber = $rowData->advance_number;
        $invCategoryId = $rowData->logbook_inv_category_id;
        $vendorName = $rowData->vendor_name;
        $remarks = $rowData->remarks;
        //$mvName = $rowData->mv_name;
        //$qtyPks = $rowData->qty_pks;
        $invoiceValue = $rowData->invoice_value;
        $paymentDate = $rowData->payment_date;
        $status = $rowData->status;
        $paidTime = $rowData->paid_time;
        $statusTime = $rowData->status_time;
        $statusDay = $rowData->status_day;
        $masterBankId = $rowData->master_bank_id;
        $pvNumber = $rowData->pv_number;
        $outstanding = $rowData->outstanding;
        $toBePaid = $rowData->to_be_paid;
        $paid = $rowData->paid;
        $paidRemarks = $rowData->paid_remarks;
        $shipmentCode = $rowData->shipment_code;
        $vendorType = $rowData->vendor_type;
        $incomplete = $rowData->incomplete;
        $incompleteRemarks = $rowData->keterangan_incomplete;
    }

    // </editor-fold>

}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Category --</option>";
    } else if ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        echo "<option value='NONE'>NONE</option>";
    }

    if ($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if ($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
</script>

<script type="text/javascript">
    // SET REQUEST MONTH & WEEK
    $('#requestDate').change(function () {
        resetRequestMonth();
        resetRequestWeek();
        if (document.getElementById('requestDate').value != '' && document.getElementById('requestDate').value != 'INSERT') {
            resetRequestMonth();
            resetRequestWeek();
            setRequestMonth(document.getElementById('requestDate').value);
            setRequestWeek(document.getElementById('requestDate').value);
            document.getElementById('requestMonth').disabled = false;
            document.getElementById('requestWeek').disabled = false;

        } else if (document.getElementById('requestDate').value != '' && document.getElementById('requestDate').value == 'INSERT') {
            $("#modalErrorMsgInsert").hide();
            $('#insertModal').modal('show');
            $('#insertModalForm').load('forms/transaction-vendor.php', {});
        }
    });

    function resetRequestMonth() {
        document.getElementById('requestMonth').value = '';
    }

    function resetRequestWeek() {
        document.getElementById('requestWeek').value = '';
    }

    function setRequestMonth(date) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getMonth',
                date: date,
            },
            success: function (data) {
                document.getElementById('requestMonth').value = data;
            }
        });
    }


    function setRequestWeek(date) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getWeek',
                date: date,
            },
            success: function (data) {
                document.getElementById('requestWeek').value = data;

            }
        });
    }

    // SET INV CATEGORY
    /*$('#categoryId').change(function () {
        resetInvCategory(' Category ');
        if (document.getElementById('categoryId').value != '' && document.getElementById('categoryId').value != 'INSERT') {
            resetInvCategory(' Category ');
            setInvCategory(0, $('select[id="categoryId"]').val(), 0);
        } else if (document.getElementById('categoryId').value != '' && document.getElementById('categoryId').value == 'INSERT') {
            $("#modalErrorMsgInsert").hide();
            $('#insertModal').modal('show');
            $('#insertModalForm').load('forms/transaction-vendor.php', {});
        }
    });

    function resetInvCategory(text) {
        document.getElementById('invCategoryId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invCategoryId').options.add(x);

        $("#invCategoryId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setInvCategory(type, categoryId, invCategoryId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getInvCategory',
                categoryId: categoryId,
                newInvCategoryId: invCategoryId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('invCategoryId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invCategoryId').options.add(x);

                        $("#invCategory").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invCategoryId').options.add(x);
                    }

                    <?php
                    if($allowContract) {
                    ?>
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('invCategoryId').options.add(x);
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#invCategory').find('option').each(function (i, e) {
                            if ($(e).val() == invCategoryId) {
                                $('#invCategory').prop('selectedIndex', i);
                            }
                        });
                    }
                }
            }
        });
    } */

    //SET STATUS TIME
    $('#paidTime').change(function () {
        resetStatusTime();
        if (document.getElementById('paidTime').value != '' && document.getElementById('paidTime').value != 'INSERT') {
            resetStatusTime();
            setStatusTime(document.getElementById('paidTime').value);
            document.getElementById('statusTime').disabled = false;
        }
    });

    function resetStatusTime() {
        document.getElementById('statusTime').value = '';
    }

    function setStatusTime(time) {
        let a = time.split(':'); // split it at the colons
        let minutes = (+a[0]) * 60 + (+a[1]);

        if (minutes > 719) {
            document.getElementById('statusTime').value = 'NOK';
        } else if (minutes <= 719) {
            document.getElementById('statusTime').value = 'OK';
        }
    }

    //SET STATUS DAY
    $('#paymentSchedule').change(function () {
        if (document.getElementById('paymentSchedule').value != '' && document.getElementById('paymentSchedule').value != 'INSERT') {
            document.getElementById('paymentDate').disabled = false;
        } else {
            document.getElementById('paymentDate').disabled = true;
        }
    });
    $('#paymentDate').change(function () {
        if (document.getElementById('paymentSchedule').value != '' && document.getElementById('paymentDate').value != '') {
            let schedule = new Date(document.getElementById('paymentSchedule').value);
            let payment = new Date(document.getElementById('paymentDate').value);
            document.getElementById('statusDay').disabled = false;
            if (payment.getTime() > schedule.getTime()) {
                document.getElementById('statusDay').value = 'NOK';
            } else if (payment.getTime() <= schedule.getTime()) {
                document.getElementById('statusDay').value = 'OK';
            }
        } else {
            resetStatusDay()
        }
    });

    function resetStatusDay() {
        document.getElementById('statusDay').value = '';
    }

    $('#status').change(function () {
        let status = document.getElementById('status').value;
        if (status == 'PAID') {
            document.getElementById('paid').value = document.getElementById('invoiceValue').value;
            document.getElementById('outstanding').value = 0;
            document.getElementById('toBePaid').value = 0;
        } else if (status == 'PV') {
            document.getElementById('toBePaid').value = document.getElementById('invoiceValue').value;
            document.getElementById('outstanding').value = 0;
            document.getElementById('paid').value = 0;
        } else if (status == 'CANCEL') {
            document.getElementById('toBePaid').value = 0;
            document.getElementById('outstanding').value = 0;
            document.getElementById('paid').value = 0;
        } else {
            document.getElementById('paid').value = 0;
            document.getElementById('toBePaid').value = 0;
            document.getElementById('outstanding').value = document.getElementById('invoiceValue').value;
        }
        console.log(document.getElementById('status').value)

    });

    $('#invoiceValue').change(function () {
        let val = document.getElementById('invoiceValue').value;
        if (val != '') {
            document.getElementById('status').disabled = false;
        } else {
            document.getElementById('status').disabled = true;
        }
    });

    $('#vendorType').change(function () {
        let vendorType = document.getElementById("vendorType").value;
        if (vendorType == 'Pks') {
            document.getElementById("vendorPks").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType == 'Freight') {
            document.getElementById("vendorFreight").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'General') {
            document.getElementById("vendorGeneral").classList.remove("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'Labor') {
            document.getElementById("vendorLabor").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'Handling') {
            document.getElementById("vendorHandling").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'PettyCash') {
            document.getElementById("vendorPettyCash").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
        }
    });


    $('#pLogbookId').change(function () {
        console.log(document.getElementById('pLogbookId').value);
        $('#dataContent').load('forms/add-logbook.php', {pLogbookId: document.getElementById('pLogbookId').value}, iAmACallbackFunction2);
    });
   

    $(function () {
        <?php if ($vendorType != ''){?>
        let vendorType = document.getElementById("vendorType").value;
        if (vendorType == 'Pks') {
            document.getElementById("vendorPks").classList.remove("hidden");
        } else if (vendorType == 'Freight') {
            document.getElementById("vendorFreight").classList.remove("hidden");
        } else if (vendorType === 'General') {
            document.getElementById("vendorGeneral").classList.remove("hidden");
        } else if (vendorType === 'Labor') {
            document.getElementById("vendorLabor").classList.remove("hidden");
        } else if (vendorType === 'Handling') {
            document.getElementById("vendorHandling").classList.remove("hidden");
        } else if (vendorType === 'PettyCash') {
            document.getElementById("vendorPettyCash").classList.remove("hidden");
        }
        <?php } ?>

        // <?php if ($requestWeek == ''){?>
        // let d = new Date().toISOString().slice(0, 10);
        //     document.getElementById('requestDate').value = d;
        // <?php } ?>

        let invoiceValue = document.getElementById('invoiceValue').value;
        if (invoiceValue != '') {
            document.getElementById('status').disabled = false;
        } else {
            document.getElementById('status').disabled = true;
        }
        if (document.getElementById('requestDate').value != '') {
            setRequestMonth(document.getElementById('requestDate').value);
            setRequestWeek(document.getElementById('requestDate').value);
            document.getElementById('requestMonth').disabled = false;
            document.getElementById('requestWeek').disabled = false;
        }

        $("#vendorName").select2({
            width: "100%"
        });
        $("#vendorNameGeneral").select2({
            width: "100%"
        });
        $("#vendorNameFreight").select2({
            width: "100%"
        });
        $("#masterBankId").select2({
            width: "100%"
        });
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 2
        });

    });
    //SUBMIT FORM
    $("#logbookDataForm").validate({
        rules: {
            stockpileId: "required",
           // companyId: "required",
            requesterId: "required",
            remarks: "required",
            masterBankId: "required",
            invoiceValue: "required",
            status: "required",
        },
        messages: {
            stockpileId: "Stockpile is a required field.",
           // companyId: "Company is a required field.",
            requesterId: "Requester is a required field.",
            remarks: "Remarks is a required field.",
            masterBankId: "Bank is a required field.",
            invoiceValue: "Invoice Value is a required field.",
            status: "Status is a required field.",

        },
        submitHandler: function (form) {
            $('#submitButton').attr("disabled", true);
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: $("#logbookDataForm").serialize(),
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
                            $('#pageContent').load('views/logbook.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton').attr("disabled", false);
                    }
                }
            });
        }
    });

    function statusIncomplete() {
        var status = document.getElementById('incomplete');
        // var generalVendorId = document.getElementById('vendorNameGeneral').value;
        if (status.checked != true) {
            document.getElementById("keterangan").classList.add("hidden");
        } else {
            document.getElementById("keterangan").classList.remove("hidden");
        }
    }

    $(".select2combobox100").select2({
            width: "100%"
    });
    $(".select2combobox150").select2({
            width: "150%"
    });
</script>
<?php if ($_POST['logbookId'] === '') { ?>
    <div class="row-fluid">
        <div class="span8 ghtblue">
            <label>Pengajuan Logbook<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT id, CONCAT(pl.id,'- ',user.user_name,'- ',pl.entry_date) as info FROM pengajuan_logbook pl LEFT JOIN user ON user.user_id = pl.entry_by WHERE status = 0 ORDER BY id desc", $pLogbookId, "", "pLogbookId", "id", "info",
                "", "", "select2combobox150");
            ?>
        </div>
    </div>
<?php } ?>
<?php if (isset($_POST['pLogbookId']) && $_POST['pLogbookId'] != '') {
    $sql = "SELECT  pl.*,s.stockpile_name,b.bank_name,user.user_name FROM pengajuan_logbook pl
LEFT JOIN stockpile s ON s.stockpile_id = pl.stockpile_id
LEFT JOIN master_bank b on b.master_bank_id = pl.master_bank_id
LEFT JOIN user on user.user_id = pl.entry_by
WHERE id = {$pLogbookId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    ?>
    <div class="row-fluid">
        <table class="table table-bordered table-striped" style="font-size: 9pt;">
            <thead>
            <tr>
                <th>Entry By</th>
                <th>Vendor</th>
                <th>Stockpile</th>
                <th>No Invoice</th>
                <th>No Rek</th>
                <th>Bank Name</th>
                <th>Cabang Bank</th>
                <th>Nama Akun Bank</th>
                <th>Qty</th>
                <th>Harga Qty</th>
                <th>DPP</th>
                <th>PPN</th>
                <th>PPH</th>
                <th>Total</th>
                <th>Tax Remark</th>
                <th>Remark</th>
                <th>File</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($resultData !== false && $resultData->num_rows > 0) {
                while ($rowData = $resultData->fetch_object()) {
                    ?>
                    <tr>
                        <td><?php echo $rowData->user_name; ?></td>
                        <td><?php echo $rowData->vendor; ?></td>
                        <td><?php echo $rowData->stockpile_name; ?></td>
                        <td><?php echo $rowData->no_invoice; ?></td>
                        <td><?php echo $rowData->no_rek; ?></td>
                        <td><?php echo $rowData->bank_name; ?></td>
                        <td><?php echo $rowData->cabang_bank; ?></td>
                        <td><?php echo $rowData->nama_akun_bank; ?></td>
                        <td><?php echo number_format($rowData->qty, 0, ".", ","); ?></td>
                        <td><?php echo number_format($rowData->harga_qty, 0, ".", ","); ?></td>
                        <td><?php echo number_format($rowData->dpp, 0, ".", ","); ?></td>
                        <td><?php echo number_format($rowData->ppn, 0, ".", ","); ?></td>
                        <td><?php echo number_format($rowData->pph, 0, ".", ","); ?></td>
                        <td><?php echo number_format($rowData->total, 0, ".", ","); ?></td>
                        <td><?php echo $rowData->tax_remark; ?></td>
                        <td><?php echo $rowData->remark; ?></td>
                        <td><?php echo "<a href='" . $rowData->file . "' target='_blank'>File</a>"; ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="16" style="text-align: center">
                        No data to be shown.
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
<?php } ?>
<form method="post" id="logbookDataForm">
    <input type="hidden" name="action" id="action" value="logbook_data"/>
    <input type="hidden" name="logbookId" id="logbookId" value="<?php echo $logbookId; ?>"/>
    <input type="hidden" name="pLogbookId" id="pLogbookId" value="<?php echo $pLogbookId; ?>"/>
        <!--    Vendor Type-->
        <!--    Vendor Name-->
        <div class="row-fluid">
            <div class="span4 lightblue">
                <label>Vendor Type <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'Pks' as info UNION
                    SELECT '2' as id, 'General' as info UNION
                    SELECT '3' as id, 'Freight' as info  UNION
                    SELECT '4' as id, 'Labor' as info UNION
                    SELECT '5' as id, 'Handling' as info UNION
                    SELECT '6' as id, 'PettyCash' as info;", $vendorType, '', "vendorType", "info", "info",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="span4 lightblue">
                <div class="hidden" id="vendorPks">
                    <label>Vendor Pks</label>
                    <?php
                    createCombo("SELECT * FROM vendor ORDER BY vendor_name ASC", $vendorName, '', "vendorName", "vendor_name", "vendor_name",
                        "", 21, 'span12');
                    ?>
                </div>
                <div class="hidden" id="vendorGeneral">
                    <label>Vendor General</label>
                    <?php
                    createCombo("SELECT * FROM general_vendor ORDER BY general_vendor_name ASC", $vendorName, '', "vendorNameGeneral", "general_vendor_name", "general_vendor_name",
                        "", 21, 'span12');
                    ?>
                </div>
                <div class="hidden" id="vendorFreight">
                    <label>Vendor Freight</label>
                    <?php
                    createCombo("SELECT * FROM freight ORDER BY freight_supplier ASC", $vendorName, '', "vendorNameFreight", "freight_supplier", "freight_supplier",
                        "", 21, 'span12');
                    ?>
                </div>
                <div class="hidden" id="vendorLabor">
                    <label>Vendor Labor</label>
                    <?php
                    createCombo("SELECT * FROM labor ORDER BY labor_name ASC", $vendorName, '', "vendorLabor", "labor_name", "labor_name",
                        "", 21, 'span12');
                    ?>
                </div>
                <div class="hidden" id="vendorHandling">
                    <label>Vendor Handling</label>
                    <?php
                    createCombo("SELECT * FROM vendor_handling ORDER BY vendor_handling_name ASC", $vendorName, '', "vendorHandling", "vendor_handling_name", "vendor_handling_name",
                        "", 21, 'span12');
                    ?>
                </div>
                <div class="hidden" id="vendorPettyCash">
                    <label>Vendor PettyCash</label>
                    <?php
                    createCombo("SELECT * FROM vendor_pettycash ORDER BY vendor_name ASC", $vendorName, '', "vendorPettyCash", "vendor_name", "vendor_name",
                        "", 21, 'span12');
                    ?>
                </div>
            </div>
        </div>

        <!--    Stockpile-->
        <!--    Master Bank-->
        <div class="row-fluid">
            <div class="span4 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT * FROM stockpile", $stockpile, '', "stockpileId", "stockpile_id", "stockpile_name",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Bank<span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT master_bank_id,bank_name FROM master_bank ORDER BY bank_name ASC", $masterBankId, '', "masterBankId", "master_bank_id", "bank_name",
                    "", "", "span12");
                ?>
            </div>
        </div>
    <!--    Request Date HO-->
    <!--    MV Name-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Request Date HO</label>
            <input type="date" placeholder="MM/DD/YYYY" tabindex="" id="requestDateHo" name="requestDateHo"
                   value="<?php echo $requestDateHo; ?>" class="span12">
        </div>
         <div class="span4 lightblue">
            <label>Request Date</label>
            <input type="date" placeholder="MM/DD/YYYY" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" class="span12">
        </div>
        <!-- <div class="span4 lightblue">
            <label>Mv Name</label>
            <input type="text" placeholder="Input Mv Name" tabindex="" id="mvName" name="mvName" class="span12"
                   value="<?php echo $mvName; ?>">
        </div> -->
    </div>
    <!--    Request Date-->
    <!--    QTY PKS-->
    <div class="row-fluid">
        <!-- <div class="span4 lightblue">
            <label>Request Date</label>
            <input type="date" placeholder="MM/DD/YYYY" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" class="span12">
        </div> -->
        <!-- <div class="span4 lightblue">
            <label>QTY PKS</label>
            <input type="number" placeholder="Input QTY PKS" tabindex="" id="qtyPKS" name="qtyPKS" class="span12"
                   value="<?php echo $qtyPks; ?>"> -->
        </div>
    </div>
    <!--    Request Month-->
    <!--    Invoice Value-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Request Month</label>
            <input type="number" placeholder="MM" tabindex="" id="requestMonth" name="requestMonth" class="span12"
                   value="<?php echo $requestMonth; ?>" disabled>
        </div>
        <div class="span4 lightblue">
            <label>Invoice Value</label>
            <input type="number" placeholder="Input Invoice Value" tabindex="" id="invoiceValue" name="invoiceValue"
                   class="span12"
                   value="<?php echo $invoiceValue; ?>">
        </div>
    </div>
    <!--    Request Week-->
    <!--    Payment Date-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Request Week</label>
            <input type="number" placeholder="DD" tabindex="" id="requestWeek" name="requestWeek" class="span12"
                   value="<?php echo $requestWeek; ?>" disabled>
        </div>
        <!-- <div class="span4 lightblue">
            <label>Payment Date</label>
            <input type="date" placeholder="MM/DD/YYYY" tabindex="" id="paymentDate" name="paymentDate"
                   value="<?php echo $paymentDate; ?>" class="span12" disabled>
        </div> -->
        <div class="span4 lightblue">
            <label>Status Time</label>
            <input type="text" placeholder="Status Time" tabindex="" id="statusTime" name="statusTime"
                   class="span12"
                   value="<?php echo $statusTime; ?>" disabled>
        </div>
    </div>
    <!--    Email Time-->
    <!--    Status-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Email Time</label>
            <input type="time" placeholder="Select Time" tabindex="" id="emailTime" name="emailTime" class="span12"
                   value="<?php echo $emailTime; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'PAID' as info UNION
                    SELECT '0' as id, 'CANCEL' as info UNION SELECT '2' as id, 'HOLD' as info UNION SELECT '3' as id, 'PV' as info UNION SELECT '4' as id, 'Oncheck' as info;", $status, '', "status", "info", "info",
                "", 6, 'select2combobox100');
            ?>
        </div>
    </div>
    <!--    Inv Receive-->
    <!--    Paid Time-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Inv Receive</label>
            <input type="date" placeholder="MM/DD/YYYY" tabindex="" id="invReceive" name="invReceive"
                   value="<?php echo $invReceive; ?>" class="span12">
        </div>
        <div class="span4 lightblue">
            <label>Paid Time</label>
            <input type="time" placeholder="Select Time" tabindex="" id="paidTime" name="paidTime" class="span12"
                   value="<?php echo $paidTime; ?>">
        </div>
    </div>
    <!--    Payment Schedule-->
    <!--Status Time-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Payment Schedule</label>
            <input type="date" placeholder="MM/DD/YYYY" tabindex="" id="paymentSchedule" name="paymentSchedule"
                   value="<?php echo $paymentSchedule; ?>" class="span12">
        </div>
        <div class="span4 lightblue">
            <label>Payment Date</label>
            <input type="date" placeholder="MM/DD/YYYY" tabindex="" id="paymentDate" name="paymentDate"
                   value="<?php echo $paymentDate; ?>" class="span12" disabled>
        </div>
    </div>
    <!--    Company-->
    <!--    Status Day-->
    <div class="row-fluid">
        <!-- <div class="span4 lightblue">
            <label>Company <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM company", $company, '', "companyId", "company_id", "company_name",
                "", 21);
            ?>
        </div> -->
        <div class="span4 lightblue">
            <label>Status Day</label>
            <input type="text" placeholder="Status Day" tabindex="" id="statusDay" name="statusDay" class="span12"
                   value="<?php echo $statusDay; ?>" disabled>
        </div>
        <div class="span4 lightblue">
            <label>PV Number</label>
            <input type="text" placeholder="Input PV Number" tabindex="" id="pvNumber" name="pvNumber"
                   class="span12"
                   value="<?php echo $pvNumber; ?>">
        </div>
    </div>
    <!--    Requester-->
    <!--    Pv Number-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Requester <span style="color: red;">*</span></label>
            <!-- <input type="text" tabindex="" id="requesterId" name="requesterId" class="span12" value="<?php echo $requester; ?>" disabled> -->
            <?php
            createCombo("SELECT * FROM master_requester", $requesterId, '', "requesterId", "id", "requester",
                "", 21, 'select2combobox100');
            ?>
        </div>

        <div class="span4 lightblue">
            <label>PIC Finance <span style="color: red;">*</span></label>
           <!--<input type="text" tabindex="" id="picFinanceId" name="picFinanceId" class="span12" value="<?php echo $picFinance; ?>" disabled>-->
            <?php
            createCombo("SELECT * FROM master_pic_finance", $picFinance, '', "picFinanceId", "id", "pic_name",
                "", 21, 'select2combobox100');
            ?>
        </div>
        <!-- <div class="span4 lightblue">
            <label>PV Number</label>
            <input type="text" placeholder="Input PV Number" tabindex="" id="pvNumber" name="pvNumber"
                   class="span12"
                   value="<?php echo $pvNumber; ?>">
        </div> -->
    </div>
    <!--    PIC FINANCE-->
    <!--    Outstanding-->
    <div class="row-fluid">
        <!-- <div class="span4 lightblue">
            <label>PIC Finance <span style="color: red;">*</span></label>
            <input type="text" tabindex="" id="picFinanceId" name="picFinanceId" class="span12" value="<?php echo $picFinance; ?>" disabled>
            <?php
            createCombo("SELECT * FROM master_pic_finance", $picFinance, '', "picFinanceId", "id", "pic_name",
                "", 21);
            ?> 
        </div> -->
        <div class="span4 lightblue">
            <label>Outstanding</label>
            <input type="number" placeholder="Input Outstanding" tabindex="" id="outstanding" name="outstanding"
                   class="span12"
                   value="<?php echo $outstanding; ?>">
        </div>

        <div class="span4 lightblue">
            <label>To Be Paid</label>
            <input type="number" placeholder="Input To Be Paid" tabindex="" id="toBePaid" name="toBePaid"
                   class="span12"
                   value="<?php echo $toBePaid; ?>">
        </div>
    </div>
    <!--    Category-->
    <!--    To Be Paid-->
    <!-- <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Category <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM logbook_category", $category, '', "categoryId", "id", "name",
                "", 21);
            ?>
        </div>
        <div class="span4 lightblue">
            <label>To Be Paid</label>
            <input type="number" placeholder="Input To Be Paid" tabindex="" id="toBePaid" name="toBePaid"
                   class="span12"
                   value="<?php echo $toBePaid; ?>">
        </div>
    </div> -->
    <!--    Inv Category-->
    <!--    Paid-->
    <!--    Belom FIX-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Inv Category <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM logbook_inv_category ", $invCategoryId, '', "invCategoryId", "id", "name",
                "", 21, 'select2combobox100', '2');
            ?>
        </div> 
        <div class="span4 lightblue">
            <label>Paid</label>
            <input type="number" placeholder="Input Paid" tabindex="" id="paid" name="paid"
                   class="span12"
                   value="<?php echo $paid; ?>">
        </div>

        <!-- <div class="span4 lightblue">
            <label>Shipment Code <span style="color: red;">*</span></label>
            <?php
                createCombo("SELECT a.shipment_id as shipmentId, a.shipment_no as shipmentCode FROM shipment a
                            INNER JOIN sales b ON b.sales_id = a.sales_id
                            INNER JOIN stockpile c  ON  c.stockpile_id = b.stockpile_id where c.stockpile_id = b.stockpile_id
                            order by b.sales_date Desc limit 50", "$shipmentCode", "", "shipmentCode", "shipmentCode", "shipmentCode",
                "", 21, 'select2combobox100');
            ?>
        </div> -->
    </div>
    <!--    Advance Number-->
    <!--    Shipment Code-->
     <div class="row-fluid">
        <!--<div class="span4 lightblue">
            <label>Advance Number <span style="color: red;">*</span></label>
            <input type="text" placeholder="Input Advance Number" tabindex="" id="advanceNumber"
                   name="advanceNumber"
                   class="span12"
                   value="<?php echo $advanceNumber; ?>">
        </div>-->
        <div class="span4 lightblue">
            <label>Shipment Code <span style="color: red;">*</span></label>
            <?php
                createCombo("SELECT a.shipment_id as shipmentId, a.shipment_no as shipmentCode FROM shipment a
                            INNER JOIN sales b ON b.sales_id = a.sales_id
                            INNER JOIN stockpile c  ON  c.stockpile_id = b.stockpile_id where c.stockpile_id = b.stockpile_id
                            order by b.sales_date Desc limit 50", "$shipmentCode", "", "shipmentCode", "shipmentCode", "shipmentCode",
                "", 21, 'select2combobox100');
            ?>
        </div>
    </div> 
    <!--    Remarks-->
    <!--    Paid Remarks-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Remarks <span style="color: red;">*</span></label>
            <textarea class="span12" name="remarks" id="remarks"><?php echo $remarks; ?></textarea>
        </div>
        <div class="span4 lightblue">
            <label>Paid Remarks</label>
            <textarea class="span12" name="paidRemarks"><?php echo $paidRemarks; ?></textarea>
        </div>
    </div>
    <?php if ($allowEditStatus && isset($_POST['logbookId']) && $_POST['logbookId'] != '') { ?>
        <div class="row-fluid">
            <div class="span4 lightblue">
                <label>Checklist If Incomplete</label>
                <input type="checkbox" id="incomplete" name="incomplete" class="span2" value="2"
                       onclick="statusIncomplete()" <?php if ($incomplete == 2) {
                    echo 'checked';
                } ?>>
            </div>
            <div class="span4 lightblue hidden" id="keterangan">
                <label>Keterangan Incomplete</label>
                <textarea class="span12" name="keteranganIncomplete"><?php echo $incompleteRemarks; ?></textarea>
            </div>
        </div>
    <?php } else { ?>
        <?php if ($logbookId == '') { ?>
            <div class="row-fluid">
                <div class="span4 lightblue">
                    <label>Checklist If Incomplete</label>
                    <input type="checkbox" id="incomplete" name="incomplete" class="span2" value="2"
                           onclick="statusIncomplete()" <?php if ($incomplete == 2) {
                        echo 'checked';
                    } ?>>
                </div>
                <div class="span4 lightblue hidden" id="keterangan">
                    <label>Keterangan Incomplete</label>
                    <textarea class="span12" name="keteranganIncomplete"><?php echo $incompleteRemarks; ?></textarea>
                </div>
            </div>
        <?php } else { ?>
            <input type="hidden" name="incomplete" value="<?php echo $incomplete; ?>">
            <input type="hidden" name="keteranganIncomplete" value="<?php echo $incompleteRemarks; ?>">
        <?php } ?>
    <?php } ?>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
