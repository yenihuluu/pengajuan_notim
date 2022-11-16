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
    $sql = "SELECT * FROM stock_transit st
            LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = st.stockpile_contract_id
            LEFT JOIN contract c ON c.contract_id = sc.contract_id
            WHERE st.mutasi_header_id = {$mutasiHeaderId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    If ($resultData !== false && $resultData->num_rows > 0) {
        $actionType = 'UPDATE';
    } else {
        $actionType = 'INSERT';
        $sql = "SELECT mc.*, CONCAT('SIT-', c.contract_no) as kode_stock_transit, c.contract_no,sc.contract_id FROM mutasi_contract AS mc 
            LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = mc.stockpile_contract_id
            LEFT JOIN contract c ON c.contract_id = sc.contract_id
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

    $('#mutasiHeaderId').change(function () {
        $('#pageContent').load('forms/stock-transit.php', {
            mutasiHeaderId: $('select[id="mutasiHeaderId"]').val(),
        }, iAmACallbackFunction);
    });

    function resetContractNo(text) {
        document.getElementById('stockpileContractId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('stockpileContractId').options.add(x);

        $("#contractNo").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setContractNo(type, kodeMutasiId, newContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getContractNo',
                kodeMutasiId: kodeMutasiId,
                newContractId: newContractId
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
                        document.getElementById('stockpileContractId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileContractId').options.add(x);

                        $("#stockpileContractId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }

                    <?php
                    if($allowContract) {
                    ?>
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('stockpileContractId').options.add(x);
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#newContractId').find('option').each(function (i, e) {
                            if ($(e).val() == newContractId) {
                                $('#stockpileContractId').prop('selectedIndex', i);

                                $("#stockpileContractId").select2({
                                    width: "100%",
                                    placeholder: newContractId
                                });
                            }
                        });
                    }
                }
            }
        });
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
            <?php
            createCombo("SELECT mh.mutasi_header_id, mh.kode_mutasi
                    FROM mutasi_header mh where status = 0
                    ORDER BY mh.tanggal_mutasi DESC, mh.kode_mutasi ASC", $mutasiHeaderId, "", "mutasiHeaderId", "mutasi_header_id", "kode_mutasi",
                "", 1, "select2combobox100");
            ?>
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
                            <td><input type="number" name="stockTransits[send_weight][]"></td>
                            <td><input type="number" name="stockTransits[netto_weight][]"></td>
                            <td><input type="date" name="stockTransits[loading_date][]"></td>
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
                                       value="<?php echo $rowData->send_weight ?>"></td>
                            <td><input type="text" name="stockTransits[netto_weight][]"
                                       value="<?php echo $rowData->netto_weight ?>"></td>
                            <td><input type="date" name="stockTransits[loading_date][]"
                                       value="<?php echo $rowData->loading_date ?>"></td>
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
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>