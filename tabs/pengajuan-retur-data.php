<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Pengajuan General';

$readonlyProperty = '';
$disabledProperty = '';
$tanggalReturnProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Pengajuan General Data">
$inputDate = $date->format('d/m/Y');
$prId = '';
$slipLama = '';
$stockpileId = '';
$tanggalNotim = '';
$tanggalNotim2 = '';
$tanggalReturn = '';
$slipBaru = '';
$remarks = '';
$tanggalNotimBaru = '';
$slipBaru = '';
$prId = '';
$nomorSlip = '';
$file = '';
// </editor-fold>


// If ID is in the parameter
if (isset($_POST['prId']) && $_POST['prId'] != '') {

    $prId = $_POST['prId'];


    // <editor-fold defaultstate="collapsed" desc="Query for Pengajuan Retur">

    $sql = "SELECT pr.*, DATE_FORMAT(pr.tanggal_notim,'%d/%m/%Y') as tanggal_notim,DATE_FORMAT(pr.tanggal_notim,'%Y%m%d') as tanggal_notim2
        FROM pengajuan_retur pr
        WHERE pr.id_p_retur = {$prId}";

    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $slipLama = $rowData->slip_lama;
        $stockpileId = $rowData->stockpile_id;
        $tanggalNotim = $rowData->tanggal_notim;
        $tanggalNotim2 = $rowData->tanggal_notim2;
        $tanggalReturn = $rowData->tanggal_return;
        $slipBaru = $rowData->slip_baru;
        $remarks = $rowData->remarks;
        $file = $rowData->file;
    }

    // </editor-fold>
    $method = $_POST['_method'];

    if ($method == 'APPROVE' || $method == 'FINISH') {
        $readonlyProperty = ' readonly';
        $disabledProperty = ' disabled';
        if ($method == 'FINISH') {
            $tanggalReturnProperty = ' readonly';
        }
    }


} else {
    $method = 'INSERT';
    if (isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
        $stockpileId = $_POST['stockpileId'];
        $tanggalNotim = $_POST['tanggalNotim'];
        $slipLama = $_POST['slipLama'];
    }
}

if (isset($_POST['tanggalNotimBaru']) && $_POST['tanggalNotimBaru'] != '' && isset($_POST['slipBaru']) && $_POST['slipBaru'] != '') {
    $tanggalNotimBaru = $_POST['tanggalNotimBaru'];
    $slipBaru = $_POST['slipBaru'];
}

if ($method != 'FINISH') {
    $nomorSlip = $slipLama;
} else {
    $nomorSlip = $slipBaru;
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {

        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if ($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
    } else if ($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
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
        if (strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }

    echo "</SELECT>";
}

// </editor-fold>
?>


<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function () {

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

    });

    //SUBMIT FORM
    $("#PengajuanDataForm").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
		$.blockUI({ message: '<h4>Please wait...</h4>' });
        $.ajax({
            url: './irvan.php',
            type: 'POST',
            data: formData,
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
                        $('#pageContent').load('views/pengajuan-retur.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>

<script type="text/javascript">
    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });

        $('#stockpileId').change(function () {
            if (document.getElementById('tanggalNotim').value != '') {
                resetSlipLama(" Other Stockpile");
                setSlipLama(0, document.getElementById('stockpileId').value, document.getElementById('tanggalNotim').value);
            }
        });
        $('#tanggalNotim').change(function () {
            if (document.getElementById('stockpileId').value != '') {
                resetSlipLama("");
                setSlipLama(0, document.getElementById('stockpileId').value, document.getElementById('tanggalNotim').value);
            }
        });
        $('#slipLama').change(function () {
            $('#dataContent').load('forms/pengajuan-retur.php', {
                stockpileId: document.getElementById('stockpileId').value,
                tanggalNotim: document.getElementById('tanggalNotim').value,
                slipLama: document.getElementById('slipLama').value,
                _method: "INSERT"
            }, iAmACallbackFunction2);
        });

        $('#slipBaru').change(function () {
            $('#dataContent').load('forms/pengajuan-retur.php', {
                tanggalNotimBaru: document.getElementById('tanggalNotimBaru').value,
                slipBaru: document.getElementById('slipBaru').value,
                prId: document.getElementById('prId').value,
                _method: "FINISH"
            }, iAmACallbackFunction2);
        });

        <?php if($method == 'FINISH'){ ?>
        $('#tanggalNotimBaru').change(function () {
            if (document.getElementById('tanggalNotimBaru').value != '') {
                resetSlipBaru("");
                setSlipBaru(0, <?php echo $stockpileId ?>, document.getElementById('tanggalNotimBaru').value);
            }
        });
        <?php } ?>
    });

    function resetSlipLama(text) {
        document.getElementById('slipLama').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('slipLama').options.add(x);

        $("#slipLama").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setSlipLama(type, stockpileId, tanggalNotim, slipLama) {

        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'get_slip_no',
                stockpileId: stockpileId,
                tanggalNotim: tanggalNotim
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
                        document.getElementById('slipLama').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('slipLama').options.add(x);

                        $("#slipLama").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('slipLama').options.add(x);
                    }

                    if (type == 1) {
                        $('#slipLama').find('slipLama').each(function (i, e) {
                            if ($(e).val() == slipLama) {
                                $('#slipLama').prop('selectedIndex', i);
                            }
                        });
                    }


                }
                //setContract(contract);
            }

        });
    }

    function resetSlipBaru(text) {
        document.getElementById('slipBaru').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('slipBaru').options.add(x);

        $("#slipBaru").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setSlipBaru(type, stockpileId, tanggalNotim, slipBaru) {

        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'get_slip_no',
                stockpileId: stockpileId,
                tanggalNotim: tanggalNotim
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
                        document.getElementById('slipBaru').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('slipBaru').options.add(x);

                        $("#slipBaru").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('slipBaru').options.add(x);
                    }

                    if (type == 1) {
                        $('#slipBaru').find('slipBaru').each(function (i, e) {
                            if ($(e).val() == slipBaru) {
                                $('#slipLama').prop('selectedIndex', i);
                            }
                        });
                    }


                }
                //setContract(contract);
            }

        });
    }
</script>

<form method="post" id="PengajuanDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_return_data">
    <input type="hidden" name="prId" id="prId" value="<?php echo $prId; ?>">
    <input type="hidden" name="_method" id="_method" value="<?php echo $method; ?>">

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "$disabledProperty", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
        <div class="span1 lightblue">
        </div>

        <div class="span3 lightblue">
            <label>Tanggal Notim</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="tanggalNotim" name="tanggalNotim"
                   value="<?php echo $tanggalNotim; ?>" data-date-format="dd/mm/yyyy"
                   class="datepicker" <?php echo $readonlyProperty ?>>
        </div>
        <div class="span1 lightblue">
        </div>

        <?php if ($method == 'UPDATE') { ?>
            <div class="span3 lightblue">
                <label>Slip No <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT transaction_id, slip_no FROM transaction t LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id WHERE sc.stockpile_id = {$stockpileId} AND t.notim_status = 0 AND DATE_FORMAT(t.transaction_date,'%d/%m/%Y') = '{$tanggalNotim}'", $slipLama, "", "slipLama", "transaction_id", "slip_no", "", "", "select2combobox100", 1);
                ?>
            </div>
        <?php } elseif ($method == 'APPROVE' || $method == 'FINISH') { ?>
            <div class="span3 lightblue">
                <label>Slip No <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT transaction_id, slip_no FROM transaction t LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id WHERE sc.stockpile_id = {$stockpileId} AND t.notim_status = 0 AND DATE_FORMAT(t.transaction_date,'%d/%m/%Y') = '{$tanggalNotim}'", $slipLama, "$disabledProperty", "slipLama", "transaction_id", "slip_no", "", "", "select2combobox100", 1);
                ?>
            </div>
        <?php } else { ?>
            <?php if ($stockpileId != '' && $tanggalNotim != '') { ?>
                <div class="span3 lightblue">
                    <label>Slip No <span style="color: red;">*</span></label>
                    <?php
                    createCombo("SELECT transaction_id, slip_no FROM transaction t LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id WHERE sc.stockpile_id = {$stockpileId} AND t.notim_status = 0 AND DATE_FORMAT(t.transaction_date,'%d/%m/%Y') = '{$tanggalNotim}'", $slipLama, "", "slipLama", "transaction_id", "slip_no", "", "", "select2combobox100", 1);
                    ?>
                </div>
            <?php } else { ?>
                <div class="span3 lightblue">
                    <label>Slip No <span style="color: red;">*</span></label>
                    <?php
                    createCombo("", $slipLama, "$disabledProperty", "slipLama", "transaction_id", "slip_no", "", "", "select2combobox100", 1);
                    ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="row-fluid">
        <?php if ($method !== 'FINISH') { ?>
            <div class="span3 lightblue">
                <label>File.</label>
                <input type="file" class="span12" readonly id="file" name="file">
            </div>

            <div class="span1 lightblue">
            </div>
        <?php } ?>

        <?php if (isset($file) && $file != '') { ?>
            <div class="span3 lightblue">
                <label>Show File.</label>
                <a href="<?php echo $file ?>" target="_blank">Open File </a>
            </div>
        <?php } ?>
        <div class="span1 lightblue">
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <?php if ($method == 'APPROVE') { ?>
            <div class="span3 lightblue">
                <label>Tanggal Return</label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="tanggalReturn" name="tanggalReturn"
                       value="<?php echo $tanggalReturn; ?>" data-date-format="dd/mm/yyyy"
                       class="datepicker" <?php echo $tanggalReturnProperty ?>>
            </div>
        <?php } ?>

        <?php if ($method == 'FINISH') { ?>
            <div class="span3 lightblue">
                <label>Tanggal Notim Baru</label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="tanggalNotimBaru" name="tanggalNotimBaru"
                       value="<?php echo $tanggalNotimBaru ?>" data-date-format="dd/mm/yyyy"
                       class="datepicker">
            </div>
            <div class="span1 lightblue">
            </div>
            <?php if ($stockpileId != '' && $tanggalNotimBaru != '') { ?>
                <div class="span3 lightblue">
                    <label>Slip Baru <span style="color: red;">*</span></label>
                    <?php
                    createCombo("SELECT transaction_id, slip_no FROM transaction t LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id WHERE sc.stockpile_id = {$stockpileId} AND t.notim_status = 0 AND DATE_FORMAT(t.transaction_date,'%d/%m/%Y') = '{$tanggalNotimBaru}'", $slipBaru, "", "slipBaru", "transaction_id", "slip_no", "", "", "select2combobox100", 1);
                    ?>
                </div>
            <?php } else { ?>
                <div class="span3 lightblue">
                    <label>Slip Baru <span style="color: red;">*</span></label>
                    <?php
                    createCombo("", $slipBaru, "", "slipBaru", "transaction_id", "slip_no", "", "", "select2combobox100", 1);
                    ?>
                </div>
            <?php } ?>

        <?php } ?>

    </div>

    <?php if ($method !== 'FINISH') { ?>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span8 lightblue">
                <label>Remarks</label>
                <textarea class="span12" rows="3" tabindex="" id="remarks"
                          name="remarks"><?php echo $remarks; ?></textarea>
            </div>
        </div>

    <?php } ?>


    <?php if (isset($nomorSlip) && $nomorSlip != '') { ?>
        <?php
        $sql = "SELECT t.*,
DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
DATE_FORMAT(t.entry_date, '%d %b %Y %H:%i:%s') AS entry_date2,
DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2,
CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
(SELECT user_name FROM `user` WHERE user_id = t.entry_by) AS user_name,
(SELECT c.contract_no FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id)) AS contract_no,
(SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id))) AS vendor_name,
CASE WHEN t.transaction_type = 1
THEN (SELECT c.po_no FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id))
ELSE (SELECT shipment_code FROM shipment WHERE shipment_id = t.shipment_id) END AS po_no,
CASE WHEN t.transaction_type = 1
THEN (SELECT stockpile_name FROM stockpile WHERE stockpile_id = (SELECT stockpile_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id))
ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = (SELECT stockpile_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id))) END AS stockpile_name,
(SELECT vehicle_name FROM vehicle WHERE vehicle_id = (SELECT vehicle_id FROM unloading_cost WHERE unloading_cost_id = t.unloading_cost_id)) AS vehicle_name,
(SELECT freight_code FROM freight WHERE freight_id = (SELECT freight_id FROM freight_cost WHERE freight_cost_id = t.freight_cost_id)) AS freight_code,
(SELECT price FROM freight_cost WHERE freight_cost_id = t.freight_cost_id) AS freight_cost,
(SELECT price FROM unloading_cost WHERE unloading_cost_id = t.unloading_cost_id) AS unloading_cost,
(SELECT vendor_handling_code FROM vendor_handling WHERE vendor_handling_id = (SELECT vendor_handling_id FROM vendor_handling_cost WHERE handling_cost_id = t.handling_cost_id)) AS vendor_handling_code,
(SELECT price FROM vendor_handling_cost WHERE handling_cost_id = t.handling_cost_id) AS handling_price,
(SELECT sales_no FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id)) AS sales_no,
(SELECT customer_name FROM customer WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id))) AS customer_name
 FROM `transaction` t
 WHERE 1=1
 AND (
(SELECT stockpile_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id) IN (SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']})
 OR
(SELECT stockpile_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id))IN (SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']})
)
AND t.transaction_id = {$nomorSlip} ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
            ?>
            <div id="transactionContainer">
                <h4>Detail Slip</h4>
                <?php
                if ($row->transaction_type == 1) {
                    ?>
                    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
                        <tr>
                            <td width="24%"><b>Stockpile</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->stockpile_name; ?></td>
                            <td width="24%"><b>Type</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->transaction_type2; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Slip No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->slip_no; ?></td>
                            <td width="24%"><b>PO No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->po_no; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Receive Date</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->unloading_date2; ?></td>
                            <td width="24%"><b>Contract Name</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->vendor_name; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Vehicle No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->vehicle_no; ?></td>
                            <td width="24%"><b>Supplier</b></td>
                            <td width="2%">:</td>
                            <td width="24%"></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Driver</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->driver; ?></td>
                            <td width="24%"><b>Contract No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->contract_no; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Loading Date</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->loading_date2; ?></td>
                            <td width="24%"><b>Delivery Notes No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->permit_no; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Vehicle</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->vehicle_name; ?></td>
                            <td width="24%"><b>Sent Weight</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Supplier Freight</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->freight_code; ?></td>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                        </tr>
                        <tr>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                            <td width="24%"><b>Bruto Weight</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->bruto_weight, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                            <td width="24%"><b>Tarra Weight</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->tarra_weight, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                            <td width="24%"><b>Netto Weight</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                            <td width="24%"><b>Shrink</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <table class="table table-bordered table-striped" style="font-size: 9pt;">
                        <thead>
                        <tr>
                            <th>Expense</th>
                            <th>Quantity (KG)</th>
                            <th>Price/KG</th>
                            <th>Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Freight Cost</td>
                            <td><?php echo number_format($row->quantity, 0, ".", ","); ?> Kg</td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format($row->freight_cost, 0, ".", ","); ?></div>
                            </td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format(($row->freight_price * $row->quantity), 0, ".", ","); ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Unloading Cost</td>
                            <td></td>
                            <td></td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format($row->unloading_price, 0, ".", ","); ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Handling Cost</td>
                            <td><?php echo number_format($row->handling_quantity, 0, ".", ","); ?> Kg</td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format($row->handling_price, 0, ".", ","); ?></div>
                            </td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format(($row->handling_price * $row->handling_quantity), 0, ".", ","); ?></div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format((($row->freight_price * $row->quantity) + $row->unloading_price + ($row->handling_price * $row->handling_quantity)), 0, ".", ","); ?></div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td>
                                <div style="text-align: right;"><?php echo number_format((($row->freight_price * $row->quantity) + $row->unloading_price), 0, ".", ","); ?></div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <?php
                } else {
                    ?>
                    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
                        <tr>
                            <td width="24%"><b>Stockpile</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->stockpile_name; ?></td>
                            <td width="24%"><b>Type</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->transaction_type2; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Slip No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->slip_no; ?></td>
                            <td width="24%"><b>Shipment Code</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->po_no; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Transaction Date</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->transaction_date2; ?></td>
                            <td width="24%"><b>Buyer</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->customer_name; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"><b>Vessel Name</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->vehicle_no; ?></td>
                            <td width="24%"><b>Sales Agreement No.</b></td>
                            <td width="2%">:</td>
                            <td width="24%"><?php echo $row->sales_no; ?></td>
                        </tr>
                        <tr>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                            <td width="24%"><b>Stockpile Weight</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="24%"></td>
                            <td width="2%"></td>
                            <td width="24%"></td>
                            <td width="24%"><b>B/L Weight</b></td>
                            <td width="2%">:</td>
                            <td width="24%">
                                <div style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?>
                                    Kg
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <?php
                }
                ?>
                <!--<br/>-->
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
                    <thead>
                    <tr>
                        <th>Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td <?php if ($row->notes == '') echo 'style="height: 40px;"'; ?>><?php echo $row->notes; ?></td>
                    </tr>
                    </tbody>
                </table>
                <!--<br/>-->
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
                    <thead>
                    <tr>
                        <th>Driver</th>
                        <th>Scaler</th>
                        <th>Acknowledge</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="width: 33%; height: 40px;"></td>
                        <td style="width: 33%; height: 40px;"></td>
                        <td style="width: 33%; height: 40px;"></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <hr>

            <?php
            // </editor-fold>
        }
        ?>

    <?php } ?>


    <div class="row-fluid">
        <div class="span12 lightblue">
            <?php if ($method == 'APPROVE') { ?>
                <div class="row-fluid" style="margin-bottom: 7px;">
                    <div class="span8 lightblue">
                        <label>Remarks Approve</label>
                        <textarea class="span12" rows="3" tabindex="" id="remarksApprove"
                                  name="remarksApprove"></textarea>
                    </div>
                </div>

                <input type="hidden" name="slipLama" id="slipLama" value="<?php echo $slipLama; ?>">
                <button class="btn btn-warning">APPROVE</button>
            <?php } elseif ($method == 'FINISH') { ?>
                <button class="btn btn-success">FINISH</button>
            <?php } else { ?>
                <button class="btn btn-primary">Submit</button>
            <?php } ?>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>

</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form id="insertForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="insertModalLabel">Insert New</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="insertModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>