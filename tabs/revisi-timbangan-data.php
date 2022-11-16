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

// <editor-fold defaultstate="collapsed" desc="Variable for revisi timbangan Data">

$revisiTimbanganId = '';
$vendorId = '';
$stockpileContractId = '';
$slip = '';
$vendor = '';
$contractNo = '';
$send_weight = '';
$bruto_weight = '';
$tarra_weight = '';
$netto_weight = '';
$vehicleNo = '';
$driver = '';
$note = '';
$pecahSlip = '';

// </editor-fold>

// If ID is in the parameter
if (isset($_POST['revisiTimbanganId']) && $_POST['revisiTimbanganId'] != '') {

    $revisiTimbanganId = $_POST['revisiTimbanganId'];

    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">

    $sql = "SELECT ttr.* , v.vendor_id, v.vendor_name, sc.stockpile_contract_id, c.contract_no
            FROM transaction_timbangan_rev ttr
            LEFT JOIN vendor v on ttr.vendor_id = v.vendor_id
            LEFT JOIN stockpile_contract sc on ttr.stockpile_contract_id = sc.stockpile_contract_id
            LEFT JOIN contract c on sc.contract_id = c.contract_id
            WHERE ttr.transaction_rev_id = {$revisiTimbanganId}
            ORDER BY ttr.entry_date ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vendorId = $rowData->vendor_id;
        $stockpileContractId = $rowData->stockpile_contract_id;
        $slip = $rowData->slip;
        $vendor = $rowData->vendor_name;
        $contractNo = $rowData->contract_no;
        $send_weight = $rowData->send_weight;
        $bruto_weight = $rowData->bruto_weight;
        $tarra_weight = $rowData->tara_weight;
        $netto_weight = $rowData->netto_weight;
		$vehicleNo = $rowData->vehicle_no;
        $driver = $rowData->driver;
        $note = $rowData->note;
        $pecahSlip = $rowData->pecah_slip;


    }

    // </editor-fold>

}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
        if ($setvalue == 0) {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
    } else if ($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if ($empty == 2) {
        echo "<option value='NONE'>Others</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function () {
        $("#revisiTimbanganDataForm").validate({
            rules: {},
            messages: {},
            submitHandler: function (form) {

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#revisiTimbanganDataForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');
                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({
                                labels: {
                                    ok: "OK"
                                }
                            });
                            alertify.alert(returnVal[2]);

                            if (returnVal[1] == 'OK') {
                                document.getElementById('revisiTimbanganId').value = returnVal[3];

                                $('#dataContent').load('contents/revisi-timbangan.php', {revisiTimbanganId: returnVal[3]}, iAmACallbackFunction);
                            }
                        }
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">

    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 2
        });
    });
</script>

<form method="post" id="revisiTimbanganDataForm">
    <input type="hidden" name="action" id="action" value="revisi_timbangan_data"/>
    <input type="hidden" name="revisiTimbanganId" id="revisiTimbanganId" value="<?php echo $revisiTimbanganId; ?>"/>
    <input type="hidden" name="vendorId" id="vendorId" value="<?php echo $vendorId; ?>"/>
        <input type="hidden" name="pecahSlip" id="pecahSlip" value="<?php echo $pecahSlip; ?>"/>
    <input type="hidden" name="stockpileContractId" id="stockpileContractId" value="<?php echo $stockpileContractId; ?>"/>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Slip <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="slip" name="slip" value="<?php echo $slip; ?>" readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Vehicle No <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="vehicleNo" name="vehicleNo" value="<?php echo $vehicleNo; ?>" readonly>
        </div>
        <div class="span4 lightblue">
            <label>Driver <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="driver" name="driver" value="<?php echo $driver; ?>"
                   readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Vendor Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="vendorName" name="vendorName" value="<?php echo $vendor; ?>"
                   readonly>
        </div>
        <div class="span4 lightblue">
            <label>Contract No <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="4" id="contractNo" name="contractNo" value="<?php echo $contractNo; ?>"
                   readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Send Weight <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="5" id="sendWeight" name="sendWeight"
                   value="<?php echo $send_weight; ?>" readonly>
        </div>
        <div class="span4 lightblue">
            <label>Bruto Weight<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="6" id="brutoWeight" name="brutoWeight"
                   value="<?php echo $bruto_weight; ?>" readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Tarra Weight<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="7" id="tarraWeight" name="tarraWeight"
                   value="<?php echo $tarra_weight; ?>" readonly>
        </div>
        <div class="span4 lightblue">
            <label>Netto Weight<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="8" id="nettoWeight" name="nettoWeight"
                   value="<?php echo $netto_weight; ?>" readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Note <span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="9" id="note" name="note"
                      readonly><?php echo $note; ?></textarea>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-success" <?php echo $disableProperty; ?>>Approve</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
