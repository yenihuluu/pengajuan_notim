<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'contract';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

$contractId = '';
$contractType = '';
$contract_no = '';
$vendorId = '';
$currencyId = '';
$price = '';
$qty = '';
$exchangeRate = '';
$contractSeq = '';
$notes = '';
$qty_rule = '';
$quantity_rule = '';
// </editor-fold>

// If ID is in the parameter
if (isset($_POST['contractId']) && $_POST['contractId'] != '') {

    $contractId = $_POST['contractId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT con.*, pop.po_pks_id, pop.contract_no AS contractNo
            FROM contract con
            LEFT JOIN po_contract poc ON poc.`contract_id` = con.`contract_id`
            LEFT JOIN po_pks pop ON pop.`po_pks_id` = poc.`po_pks_id`
            WHERE con.contract_id = {$contractId}
            ORDER BY con.contract_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $contractType = $rowData->contract_type;
        $contract_no = $rowData->contractNo;
        $vendorId = $rowData->vendor_id;
        $currencyId = $rowData->currency_id;
        $price = $rowData->price;
        $qty = $rowData->quantity;
        $exchangeRate = $rowData->exchange_rate;
        $generatedPoNo = $rowData->po_no;
        $notes = $rowData->notes;
        $po_pks_id = $rowData->po_pks_id;
        $mutasiType = $rowData->mutasi_type;
    }

    // </editor-fold>

} else {
    $generatedPoNo = "";
    if (isset($_SESSION['contract'])) {
        $contractType = $_SESSION['contract']['contractType'];
        $contract_no = $_SESSION['contract']['contract_no'];
        $vendorId = $_SESSION['contract']['vendorId'];
        $currencyId = $_SESSION['contract']['currencyId'];
        $price = $_SESSION['contract']['price'];
        $qty = $_SESSION['contract']['quantity'];
        $exchangeRate = $_SESSION['contract']['exchangeRate'];
        $stockpileId = $_SESSION['contract']['stockpileId'];
        $contractSeq = $_SESSION['contract']['contractSeq'];
        $notes = $_SESSION['contract']['notes'];
        $po_pks_id = $_SESSION['contract']['po_pks_id'];
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if ($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
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
        //$("#quantityRule").prop("disabled", true);

        $('#quantity_rule').attr("disabled", true);
        
        $(".select2combobox100").select2({
            width: "100%",
            disabled: true
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $('#qty').number(true, 2);

        if (document.getElementById('po_pks_id').value != '') {
            resetPo('po_pks_id', ' ');
            <?php
            if($_SESSION['contract']['po_pks_id'] != '') {
            ?>
            refreshPo(<?php echo $po_pks_id; ?>);
            <?php
            } else {
            ?>
            //setPo(0, $('select[id="po_pks_id"]').val(), 0);
            <?php
            }
            ?>
        }

        if (document.getElementById('contractType').value == 'P' || document.getElementById('contractType').value == '') {
            $('#returnShipmentLabel').hide();
            $('#ShipmentLabel').hide();
            $('#SalesLabel').hide();
        } else {
            $('#returnShipmentLabel').show();
            $('#ShipmentLabel').hide();
            $('#SalesLabel').hide();
        }

        <?php
        if($generatedPoNo == "") {
        ?>
        $('#contractSeq').change(function () {
            if (document.getElementById('contractType').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value + "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function (data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });

        $('#contractType').change(function () {
            if (document.getElementById('contractType').value == 'P' || document.getElementById('contractType').value == '') {
                $('#returnShipmentLabel').hide();
                $('#ShipmentLabel').hide();
                $('#SalesLabel').hide();
            } else {
                $('#returnShipmentLabel').show();
                $('#ShipmentLabel').hide();
                $('#SalesLabel').hide();
            }
        });
        <?php
        }
        ?>
        $('#returnShipment').change(function () {

            if (document.getElementById('returnShipment').value == 1) {
                $('#ShipmentLabel').show();
                $('#SalesLabel').show();
                setShipment();
            } else {
                $('#ShipmentLabel').hide();
                $('#SalesLabel').hide();
            }
        });

        $('#shipmentId').change(function () {

            if (document.getElementById('shipmentId').value != '') {

                setSalesPrice($('select[id="shipmentId"]').val());
            } else {

            }
        });


        $('#po_pks_id').change(function () {
            $('#summaryContract').hide();
            refreshPo($('select[id="po_pks_id"]').val());


        });

        $("#contractDataForm").validate({
            rules: {
                contractType: "required",
                qty_rule: "required",
                contractDate: "required",
                //quantity_rule: "required"
                //currencyId: "required",
                //exchangeRate: "required",
                //price: "required",
                //quantity: "required",
                //stockpileId: "required"
            },
            messages: {
                contractType: "Contract Type is a required field.",
                qty_rule: "Send Weight Rule is a required field.",
                contractDate: "Contract Date is a required field.",
               // quantity_rule: "Quantity Rule is a required field."
                //currencyId: "Currency is a required field.",
                //exchangeRate: "Exchange Rate is a required field.",
                //price: "Price is a required field.",
                //quantity: "Quantity is a required field.",
                //stockpileId: "Stockpile is a required field."
            },
            submitHandler: function (form) {
                $('#submitButton').attr("disabled", true);
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#contractDataForm").serialize(),
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
                                document.getElementById('generalContractId').value = returnVal[3];

                                $('#dataContent').load('forms/contract2.php', {contractId: returnVal[3]}, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            }
                            $('#submitButton').attr("disabled", false);
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
            startView: 0
        });
    });

    function refreshPo(po_pks_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'refreshPo',
                po_pks_id: po_pks_id
                // paymentMethod: paymentMethod,
                // ppn: ppnValue,
                // pph: pphValue
            },
            success: function (data) {
                if (data != '') {
                    $('#summaryContract').show();
                    document.getElementById('summaryContract').innerHTML = data;
                    //refreshPo($('select[id="po_pks_id"]').val());
                    generatedContractNo(document.getElementById('contract_no').value);
                    <?php
                    if($generatedPoNo == "") {
                    ?>
                    if (document.getElementById('contractType').value != "") {

                        $.ajax({
                            url: './get_data.php',
                            method: 'POST',
                            data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value + "&contractSeq=" + document.getElementById('contractSeq').value,
                            success: function (data) {
                                document.getElementById('generatedPoNo').value = data;

                            }
                        });
                    } else {
                        document.getElementById('generatedPoNo').value = "";
                    }
                    <?php
                    }
                    ?>
                }
            }
        });
    }

    function generatedContractNo(contract_no) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGeneratedContract',
                contract_no: contract_no
                // paymentMethod: paymentMethod,
                // ppn: ppnValue,
                // pph: pphValue
            },
            success: function (data) {
                if (data != '') {
                    // $('#summaryContract').show();
                    document.getElementById('generatedContractNo').value = data;

                }
            }
        });
    }

    function setSalesPrice(shipmentId) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getSalesPriceReturn',
                shipmentId: shipmentId
                // paymentMethod: paymentMethod,
                // ppn: ppnValue,
                // pph: pphValue
            },
            success: function (data) {
                if (data != '') {
                    // $('#summaryContract').show();
                    document.getElementById('salesPrice').value = data;

                }
            }
        });
    }

    function setShipment() {

        $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: {
                action: 'getShipmentReturn'
                //contract:contract
                //stockpileContractId: stockpileContractId,
                //paymentMethod: paymentMethod,
                //ppn: ppnValue,
                //pph: pphValue
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
                        document.getElementById('shipmentId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId').options.add(x);

                        $("#shipmentId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId').options.add(x);
                    }


                }
                //setContract(contract);
            }

        });

    }
</script>

<script type="text/javascript">
    $(function () {
        // Session Storage Browser
        Object.keys(sessionStorage).forEach((key) => {
            var newKey = key.split('.');
            if (newKey[0] == "contractData" && newKey[1] != "") {
                document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
                $('#' + newKey[1]).trigger('change');
            }
        });
        $(":input").change(function () {
            sessionStorage.setItem("contractData." + this.id, this.value);
        });
    });
</script>
<form method="post" id="contractDataForm">
    <input type="hidden" name="action" id="action" value="contract_data"/>
    <input type="hidden" name="contractId" id="contractId" value="<?php echo $contractId; ?>"/>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Generated PO No</label>
            <input type="text" class="span12" readonly id="generatedPoNo" name="generatedPoNo"
                   value="<?php echo $generatedPoNo; ?>">
        </div>
        <div class="span2 lightblue">
            <label>Contract Type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT 'P' as id, 'PKS' as info UNION
                    SELECT 'C' as id, 'Curah' as info;", $contractType, "", "contractType", "id", "info",
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span2 lightblue" id="returnShipmentLabel" style="display: none;">
            <label>Return Shipment <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'No' as info UNION
                    SELECT '1' as id, 'Yes' as info;", $returnShipment, "", "returnShipment", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>
        <div class="span2 lightblue" id="ShipmentLabel" style="display: none;">
            <label>Shipment Code<span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "shipmentId", "shipment_id", "shipment_id",
                "", 2, "select2combobox100");
            ?>
        </div>
        <div class="span2 lightblue" id="SalesLabel" style="display: none;">
            <label>Sales Price</label>
            <input type="text" class="span12" readonly id="salesPrice" name="salesPrice">
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Contract No. <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT po.*
                    FROM po_pks po WHERE po.po_status = 0 AND po.quantity > 0 AND reject_status = 0
                    ORDER BY po.po_pks_id DESC", $po_pks_id, "", "po_pks_id", "po_pks_id", "contract_no",
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Generated Contract No</label>
            <input type="text" class="span12" readonly id="generatedContractNo" name="generatedContractNo"
                   value="<?php echo $generatedContractNo; ?>">
        </div>
        <div
    </div>
    <div class="span4 lightblue">
    </div>
    </div>
    </div>
    <div class="row-fluid">
        <div class="row-fluid" id="summaryContract" style="display: none;">
            summary
        </div>

        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Quantity</label>
                <input type="text" class="span12" tabindex="" id="qty" name="qty" value="<?php echo $qty; ?>">
            </div>
            <div class="span4 lightblue">
                <label>Send Weight Rules <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '0' as id, 'Send Weight' as info UNION
                    SELECT '1' as id, 'Netto Weight' as info UNION
                    SELECT '2' as id, 'Lowest Weight' as info;", $qty_rule, '', "qty_rule", "id", "info",
                    "", 12);
                ?>
            </div>
            <div class="span4 lightblue">
            <label >Quantity Rules <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '0' as id, 'Lowest Weight' as info UNION
                    SELECT '1' as id, 'Send Weight' as info UNION
                    SELECT '2' as id, 'Netto Weight' as info;", $quantity_rule, '', "quantity_rule", "id", "info",
                    "", 12, "select2combobox100");
                ?>
            </div>
        </div>
        <?php
        if ($po_pks_id == '') {
        ?>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>PO numbering (leave blank for system numbering)</label>
                <input type="text" class="span3" tabindex="" id="contractSeq" name="contractSeq"
                       value="<?php echo $contractSeq; ?>">
            </div>
            <div class="span2 lightblue">
                <label>Mutasi Barang / Langsir <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'Yes' as info UNION
                    SELECT '0' as id, 'No' as info;", $mutasiType, "", "mutasiType", "id", "info",
                    "", "", "select2combobox100");
                ?>

                <div class="span4 lightblue">
                    <label>Contract Date <span style="color: red;">*</span></label>
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="contractDate" name="contractDate"
                           data-date-format="dd/mm/yyyy" class="datepicker">
                </div>
            </div>
            <?php
            }
            ?>
            <div class="row-fluid">
                <div class="span8 lightblue">
                    <label>Notes <span style="color: red;"></span></label>
                    <textarea class="span12" rows="3" tabindex="" id="notes"
                              name="notes"><?php echo $notes; ?></textarea>
                </div>

            </div>
            <div class="row-fluid">
                <div class="span12 lightblue">
                    <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton">Submit</button>
                    <button class="btn" type="button" onclick="back()">Back</button>
                </div>
            </div>
</form>
