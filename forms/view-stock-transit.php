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


// <editor-fold defaultstate="collapsed" desc="Variable for stock transit Data">

$stockTransitId = '';
$kodeMutasi = '';
$stockpileContractId = '';
$contractNo = '';
$sendWeight = '';
$nettoWeight = '';
$nettoStockpile = '';
$stockTransiCol = '';
$loadingDate = '';
$mutasiHeaderId = '';
// </editor-fold>

// If ID is in the parameter
if (isset($_POST['mutasiHeaderId']) && $_POST['mutasiHeaderId'] != '') {
    $mutasiHeaderId = $_POST['mutasiHeaderId'];
    $sql = "SELECT mh.kode_mutasi, st.*, c.* FROM stock_transit st 
    LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = st.stockpile_contract_id 
    LEFT JOIN contract c ON c.contract_id = sc.contract_id 
    LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = st.mutasi_header_id
    WHERE st.mutasi_header_id = {$mutasiHeaderId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    If ($resultData !== false && $resultData->num_rows > 0) {
        $row = $resultData->fetch_object();
        $actionType = 'UPDATE';
        $kodeMutasi = $row->kode_mutasi;
    } else {
        $actionType = 'INSERT';
        $sql = "SELECT mc.*, CONCAT('SIT-', c.contract_no) as kode_stock_transit, c.contract_no,sc.contract_id, mh.kode_mutasi
            FROM mutasi_contract AS mc 
            LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = mc.stockpile_contract_id
            LEFT JOIN contract c ON c.contract_id = sc.contract_id
            LEFT JOIN mutasi_header mh on mh.mutasi_header_id = mc.mutasi_header_id 
            WHERE  mutasi_header_id = {$mutasiHeaderId}";
        $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions Create Combo">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
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

    $(document).ready(function () {	//executed after the page has loaded
        $('.combobox').combobox();

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });
    });
</script>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/stock-transit.php', {}, iAmACallbackFunction);
    }

    $(document).ready(function () {
        // const kodeMutasiId = document.getElementById('kodeMutasiId').value;
        // if (kodeMutasiId != '') {
        //     setContractNo(0, kodeMutasiId, 0);
        // }
        $("#stockTransitDataForm").validate({
            submitHandler: function (form) {
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#stockTransitDataForm").serialize(),
                    success: function (data) {
                        // console.log(data);
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
                                back();
                            }
                        }
                    }
                });
            }
        });
    });
</script>

<form method="post" id="stockTransitDataForm">
    <input type="hidden" name="action" id="action" value="stock_transit_data"/>
    <input type="hidden" name="actionType" id="actionType" value="<?php echo $actionType ?>"/>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Mutasi Code<span style="color: red;">*</span></label>
            <td><input type="text" name="stockTransits[kode_mutasi][]"
                                   value="<?php echo $kodeMutasi ?>" readonly></td>
        </div>
    </div>
    <br>
    <br>
    <div class="row-fluid">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
            <thead>
            <tr>
                <th>Contract No</th>
                <th>Code Stock Transit</th>
                <th>Send Weight Pabric</th>
                <th>Netto Weight Draft Survey</th>
                <th>Loading Date</th>
            </thead>
            <tbody>
            <?php
            if ($resultData !== false && $resultData->num_rows > 0) {
                if ($actionType === 'INSERT') {
                    while ($rowData = $resultData->fetch_object()) {
                        ?>
                        <tr>
                            <input type="hidden" name="stockTransits[stockpile_contract_id][]"
                                   value="<?php echo $rowData->stockpile_contract_id ?>">
                            <td><?php echo $rowData->contract_no; ?></td>
                            <td><input type="text" name="stockTransits[kode_stock_transit][]"
                                   value="<?php echo $rowData->kode_stock_transit ?>" readonly></td>
                            <td><input type="number" name="stockTransits[send_weight][]" readonly></td>
                            <td><input type="number" name="stockTransits[netto_weight][]" readonly></td>
                            <td><input type="date" name="stockTransits[loading_date][]" readonly></td>
                        </tr>
                    <?php }
                } else {
                    while ($rowData = $resultData->fetch_object()) {
                        ?>

                        <tr>
                            <input type="hidden" name="stockTransits[id][]"
                                   value="<?php echo $rowData->stock_transit_id ?>">
                            <td><?php echo $rowData->contract_no; ?></td>
                            <td><input type="text" name="stockTransits[kode_stock_transit][]"
                                   value="<?php echo $rowData->kode_stock_transit ?>" readonly></td>
                            <td><input type="text" name="stockTransits[send_weight][]"
                                       value="<?php echo $rowData->send_weight ?>" readonly></td>
                            <td><input type="text" name="stockTransits[netto_weight][]"
                                       value="<?php echo $rowData->netto_weight ?>" readonly></td>
                            <td><input type="date" name="stockTransits[loading_date][]"
                                       value="<?php echo $rowData->loading_date ?>" readonly></td>
                        </tr>
                    <?php }
                }
            } else {
                ?>
                <tr>
                    <td colspan="9" style="text-align: center">
                        No data to be shown.
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>