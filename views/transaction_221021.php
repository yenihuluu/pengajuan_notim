<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';


$_SESSION['menu_name'] = 'Transactions';

$date = new DateTime();
$transactionDate = $date->format('d/m/Y');
$transactionDate2 = $date->format('d/m/Y');
$unloadingDate = $date->format('d/m/Y');
$transactionType = 1;
$allowUnloading = false;
$allowContract = false;
$allowFreight = false;
$allowHandling = false;
$allowSales = false;
$allowShipment = false;
$allowVendor = false;
$allowCustomer = false;
$allowLabor = false;
$allowSupplier = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 7) {
            $allowUnloading = true;
        } elseif ($row->module_id == 8) {
            $allowContract = true;
        } elseif ($row->module_id == 9) {
            $allowFreight = true;
            $allowHandling = true;
        } elseif ($row->module_id == 10) {
            $allowSales = true;
        } elseif ($row->module_id == 11) {
            $allowVendor = true;
        } elseif ($row->module_id == 12) {
            $allowCustomer = true;
        } elseif ($row->module_id == 13) {
            $allowLabor = true;
        } elseif ($row->module_id == 16) {
            $allowSupplier = true;
        }
    }
}

if (isset($_SESSION['transaction'])) {
    $stockpileId = $_SESSION['transaction']['stockpileId'];
    $transactionType = $_SESSION['transaction']['transactionType'];
    $unloadingCostId = $_SESSION['transaction']['unloadingCostId'];
    $vendorId = $_SESSION['transaction']['vendorId'];
    $freightCostId = $_SESSION['transaction']['freightCostId'];
    $handlingCostId = $_SESSION['transaction']['handlingCostId'];
    $stockpileContractId = $_SESSION['transaction']['stockpileContractId'];
    $contractPksDetailId = $_SESSION['transaction']['contractPksDetailId'];
    $customerId = $_SESSION['transaction']['customerId'];
    $salesId = $_SESSION['transaction']['salesId'];
    $shipmentId = $_SESSION['transaction']['shipmentId'];
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
//        $('.select2combobox').selectize({
//            allowEmptyOption: true,
//            create: true,
//            sortField: 'text'
//        });

        $('#sendWeight').number(true, 2);

        $('#brutoWeight').number(true, 2);

        $('#tarraWeight').number(true, 2);

        $('#sendWeight2').number(true, 2);

        $('#blWeight').number(true, 2);

        $('#showTransaction').click(function (e) {
            e.preventDefault();

            $("#transactionContainer").show();
            $("#hideTransaction").show();
            $("#printTransaction").show();
            $("#showTransaction").hide();
        });

        $('#hideTransaction').click(function (e) {
            e.preventDefault();

            $("#transactionContainer").hide();
            $("#showTransaction").show();
            $("#printTransaction").hide();
            $("#hideTransaction").hide();
        });

        $('#printTransaction').click(function (e) {
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#transactionContainer").printThis();
//            $("#transactionContainer").hide();
        });

        <?php
        if(isset($_SESSION['transaction']) && $transactionType == 1) {
        ?>
        if (document.getElementById('stockpileId').value != '') {

            <?php
            if($unloadingCostId != '') {
            ?>
            setUnloading(1, $('select[id="stockpileId"]').val(), <?php echo $unloadingCostId; ?>);
            setLabor(1, $('select[id="stockpileId"]').val());
            setUnloadingDetail(<?php echo $unloadingCostId; ?>);
            <?php
            } else {
            ?>
            setUnloading(0, $('select[id="stockpileId"]').val(), 0);
            setLabor(0, $('select[id="stockpileId"]').val());

            resetContract(' Contract ');
            resetFreight(' Contract ');
            resetHandling(' Contract ');

            <?php
            }

            if($vendorId != '') {
            ?>
            setVendor(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>);

            resetHandling(' ');
            resetFreight(' ');
            resetContract(' ');
            resetContractPksDetail(' PKS Detail ');
            <?php
            if($freightCostId != '') {
            ?>
            setFreight(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $freightCostId; ?>, document.getElementById('unloadingDate').value);
            setFreightDetail(<?php echo $freightCostId; ?>);
            <?php
            } else {
            ?>
            setFreight(0, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, 0, document.getElementById('unloadingDate').value);

            <?php
            }

            if($handlingCostId != '') {
            ?>
            setHandling(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $handlingCostId; ?>);
            setHandlingDetail(<?php echo $handlingCostId; ?>);
            <?php
            } else {
            ?>
            setHandling(0, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, 0);
            <?php
            }

            if($stockpileContractId != '') {
            ?>
            setContract(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $stockpileContractId; ?>);
            setContractDetail(<?php echo $stockpileContractId; ?>);
            <?php
            } else {
            ?>
            setContract(0, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, 0);
            <?php
            }
            } else {
            ?>
            setVendor(0, $('select[id="stockpileId"]').val(), 0);
            <?php
            }
            ?>

        }


        <?php
        } if(isset($_SESSION['transaction']) && $transactionType == 2) {
        ?>
        if (document.getElementById('customerId').value != '') {
            resetShipment(' Sales ');
            <?php
            if($salesId != '') {
            ?>
            resetShipment(' ');
            setSales(1, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), <?php echo $salesId; ?>);
            <?php
            if($shipmentId != '') {
            ?>
            setShipment(1, <?php echo $salesId; ?>, <?php echo $shipmentId; ?>);
            setShipmentDetail(<?php echo $shipmentId; ?>);
            <?php
            } else {
            ?>
            setShipment(0, <?php echo $salesId; ?>, 0);
            <?php
            }
            } else {
            ?>
            setSales(0, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), 0);
            <?php
            }
            ?>
        }
        <?php
        }
        ?>

        if (document.getElementById('transactionType').value == 1) {
            $('#inTransaction').show();
            $('#outTransaction').hide();

        } else if (document.getElementById('transactionType').value == 2) {
            $('#inTransaction').hide();
            $('#outTransaction').show();

        }

        $('#insertModal').on('hidden', function () {
            // do something…
            if (document.getElementById('vendorId').value == 'INSERT') {
                setVendor(0, $('select[id="stockpileId"]').val(), 0);
            } else if (document.getElementById('unloadingCostId').value == 'INSERT') {
                setUnloading(0, $('select[id="stockpileId"]').val(), 0);
            } else if (document.getElementById('laborId').value == 'INSERT') {
                setLabor(0, 0);
            } else if (document.getElementById('stockpileContractId').value == 'INSERT') {
                setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if (document.getElementById('freightCostId').value == 'INSERT') {
                setFreight(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, document.getElementById('unloadingDate').value);
            } else if (document.getElementById('handlingCostId').value == 'INSERT') {
                setHandling(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if (document.getElementById('supplierId').value == 'INSERT') {
                setSupplier(0, 0);
            } else if (document.getElementById('customerId').value == 'INSERT') {
                setCustomer(0, 0);
            } else if (document.getElementById('salesId').value == 'INSERT') {
                setSales(0, $('select[id="customerId"]').val(), 0);
            }
        });


        $('#transactionType').change(function () {
            resetVehicle();
            resetVendor();
            resetFreight(' Stockpile ');
            resetFreightDetail();
            resetHandling(' Stockpile ');
            resetHandlingDetail();
            resetUnloadingDetail();
            resetContract(' Stockpile ');
            resetContractDetail();
            resetContractPksDetail(' PKS Detail ');
            resetSales(' Buyer ');
            resetShipment(' Buyer ');
            resetShipmentDetail();

//            alert(document.getElementById('transactionType').value);
            document.getElementById('stockpileId').value = '';
            document.getElementById('shipmentId').value = '';

            $("#stockpileId").select2({
                width: "75%",
                placeholder: document.getElementById('stockpileId').value
            });

            $("#shipmentId").select2({
                width: "100%",
                placeholder: document.getElementById('shipmentId').value
            });

            $('#inTransaction').hide();
            $('#outTransaction').hide();

            if (document.getElementById('transactionType').value == 1) {
                $('#inTransaction').show();
                $('#outTransaction').hide();
            } else if (document.getElementById('transactionType').value == 2) {
                $('#inTransaction').hide();
                $('#outTransaction').show();
            }
        });

        $('#stockpileId').change(function () {
//            $('#clientId').val('');
            resetVehicle();
            resetLabor();
            resetVendor();
            resetFreight(' Stockpile ');
            resetFreightDetail();
            resetHandling(' Stockpile ');
            resetHandlingDetail();
            resetContract(' Stockpile ');
            resetContractDetail();
            resetContractPksDetail(' PKS Detail ');


            if (document.getElementById('stockpileId').value != '') {
                setUnloading(0, $('select[id="stockpileId"]').val(), 0);
                setLabor(0, $('select[id="stockpileId"]').val(), 0);
                setVendor(0, $('select[id="stockpileId"]').val(), 0);
                resetContract(' Contract ');
                resetFreight(' Contract ');
                resetHandling(' Contract ');
            }
        });

        $('#customerId').change(function () {
            resetSales(' Buyer ');
            resetShipment(' Sales ');
            resetShipmentDetail();

            if (document.getElementById('customerId').value != '' && document.getElementById('customerId').value != 'INSERT') {
                resetSales(' ');
                setSales(0, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), 0);
            } else if (document.getElementById('customerId').value != '' && document.getElementById('customerId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-customer.php', {});
            }
        });

        $('#salesId').change(function () {
            resetShipment(' Sales ');
            resetShipmentDetail();

            if (document.getElementById('salesId').value != '' && document.getElementById('salesId').value != 'INSERT') {
                resetShipment(' ');
                setShipment(0, $('select[id="salesId"]').val(), 0);
            } else if (document.getElementById('salesId').value != '' && document.getElementById('salesId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-sales.php', {customerId: $('select[id="customerId"]').val()});
            }
        });

//        $('#shipmentId').change(function() {
//            if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value == 'INSERT') {
//                $("#modalErrorMsgInsert").hide();
//                $('#insertModal').modal('show');
//                $('#insertModalForm').load('forms/transaction-shipment.php', {});
//            }
//        });

        $('#vendorId').change(function () {
            resetFreight(' Contract ');
            resetFreightDetail();
            resetHandling(' Contract ');
            resetHandlingDetail();
            resetContract(' Contract ');
            resetContractDetail();
            resetContractPksDetail(' PKS Detail ');

            // document.getElementById('supplierId').value = '';
//            $('#supplierId').attr("disabled", true);

            if (document.getElementById('vendorId').value != '' && document.getElementById('vendorId').value != 'INSERT') {
                resetFreight(' ');
                resetHandling(' ');
                resetContract(' ');
                setFreight(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, document.getElementById('unloadingDate').value);
                setHandling(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
                setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if (document.getElementById('vendorId').value != '' && document.getElementById('vendorId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-vendor.php', {});
            }
        });

        $('#stockpileContractId').change(function () {
            resetContractDetail();
            resetContractPksDetail(' PKS Detail ');

            if (document.getElementById('stockpileContractId').value != '' && document.getElementById('stockpileContractId').value != 'INSERT') {
                setContractPksDetail(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), $('select[id="stockpileContractId"]').val());
                setContractDetail($('select[id="stockpileContractId"]').val());
            } else if (document.getElementById('stockpileContractId').value != '' && document.getElementById('stockpileContractId').value == 'INSERT') {
//                resetFreight(' Contract ');
//                resetFreightDetail();
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-contract.php', {
                    stockpileId: $('select[id="stockpileId"]').val(),
                    vendorId: $('select[id="vendorId"]').val()
                });
            }
        });

        $('#shipmentId').change(function () {
            if (document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value != 'INSERT') {
                setShipmentDetail($('select[id="shipmentId"]').val());
            } else if (document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value == 'INSERT') {
                resetShipmentDetail();

                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-shipment.php', {salesId: $('select[id="salesId"]').val()});
            } else {
                resetShipmentDetail();
            }
        });

        $('#unloadingCostId').change(function () {
            resetUnloadingDetail();
            if (document.getElementById('unloadingCostId').value != '' && document.getElementById('unloadingCostId').value != 'INSERT') {
                setUnloadingDetail($('select[id="unloadingCostId"]').val());
            } else if (document.getElementById('unloadingCostId').value != '' && document.getElementById('unloadingCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-unloading.php', {stockpileId: $('select[id="stockpileId"]').val()});
            }
        });

        $('#freightCostId').change(function () {
            resetFreightDetail();
            if (document.getElementById('freightCostId').value != '' && document.getElementById('freightCostId').value != 'INSERT') {
                setFreightDetail($('select[id="freightCostId"]').val());
            } else if (document.getElementById('freightCostId').value != '' && document.getElementById('freightCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-freight.php', {
                    stockpileId: $('select[id="stockpileId"]').val(),
                    vendorId: $('select[id="vendorId"]').val()
                });
            }
        });

        $('#handlingCostId').change(function () {
            resetHandlingDetail();
            if (document.getElementById('handlingCostId').value != '' && document.getElementById('handlingCostId').value != 'INSERT') {
                setHandlingDetail($('select[id="handlingCostId"]').val());
            } else if (document.getElementById('handlingCostId').value != '' && document.getElementById('handlingCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-handling.php', {
                    stockpileId: $('select[id="stockpileId"]').val(),
                    vendorId: $('select[id="vendorId"]').val()
                });
            }
        });

        $('#laborId').change(function () {
            if (document.getElementById('laborId').value != '' && document.getElementById('laborId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-labor.php', {});
            }
        });

        /*$('#supplierId').change(function() {
            if(document.getElementById('supplierId').value != '' && document.getElementById('supplierId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-supplier.php', {});
            } 
        });*/

        $("#transactionDataForm").validate({
            rules: {
                stockpileId: "required",
                transactionType: "required",
                loadingDate: "required",
                stockpileContractId: "required",
                vehicleNo: "required",
                driver: "required",
                unloadingDate: "required",
                unloadingCostId: "required",
                permitNo: "required",
                freightCostId: "required",
                //handlingCostId: "required",
                sendWeight: "required",
                brutoWeight: "required",
                tarraWeight: "required",
                block: "required",
                vendorId: "required",
                transactionDate2: "required",
                sendWeight2: "required",
                blWeight: "required",
                vehicleNo2: "required",
                shipmentId: "required"
            },
            messages: {
                stockpileId: "Stockpile is a required field.",
                transactionType: "Type is a required field.",
                loadingDate: "Loading Date is a required field.",
                stockpileContractId: "PO No. is a required field.",
                vehicleNo: "Vehicle No. is a required field.",
                driver: "Driver is a required field.",
                unloadingDate: "Unloading Date is a required field.",
                unloadingCostId: "Vehicle is a required field.",
                permitNo: "Permit No. is a required field.",
                freightCostId: "Supplier Freight is a required field.",
                //handlingCostId: "Handling Cost is a required field.",
                sendWeight: "Sent Weight is a required field.",
                brutoWeight: "Bruto Weight is a required field.",
                tarraWeight: "Tarra Weight is a required field.",
                block: "Block is a required field.",
                vendorId: "Contract Name is a required field.",
                transactionDate2: "Transaction Date is a required field.",
                sendWeight2: "Stockpile Weight is a required field.",
                blWeight: "BL Weight is a required field.",
                vehicleNo2: "Vessel Name is a required field.",
                shipmentId: "Sales Agreement No. is a required field."
            },
            submitHandler: function (form) {
                $('#submitButton').attr("disabled", true);
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#transactionDataForm").serialize(),
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
                                $('#pageContent').load('views/transaction.php', {}, iAmACallbackFunction);
                            }
                            $('#submitButton').attr("disabled", false);
                        }
                    }
                });
            }
        });

        $("#insertForm").validate({
            submitHandler: function (form) {

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                var resultData = returnVal[3].split('~');

                                if (resultData[0] == 'CONTRACT') {
                                    setContract(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
//                                    resetFreight(' ');
//                                    setFreight(0, resultData[1], 0);
                                    setContractDetail(resultData[1]);
                                } else if (resultData[0] == 'UNLOADING') {
                                    setUnloading(1, $('select[id="stockpileId"]').val(), resultData[1]);
                                    setUnloadingDetail(resultData[1]);
                                } else if (resultData[0] == 'FREIGHT') {
                                    setFreight(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
                                    setFreightDetail(resultData[1]);
                                } else if (resultData[0] == 'SALES') {
                                    setSales(1, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), resultData[1]);
                                    resetShipment(' ');
                                    setShipment(0, resultData[1], 0);
                                } else if (resultData[0] == 'SHIPMENT') {
                                    setShipment(1, $('select[id="salesId"]').val(), resultData[1]);
                                    setShipmentDetail(resultData[1]);
                                } else if (resultData[0] == 'VENDOR') {
                                    setVendor(1, $('select[id="stockpileId"]').val(), resultData[1]);
                                    resetFreight(' ');
                                    resetContract(' ');
                                    setFreight(1, $('select[id="stockpileId"]').val(), resultData[1], 0, document.getElementById('unloadingDate').value);
                                    setContract(1, $('select[id="stockpileId"]').val(), resultData[1], 0);
                                } else if (resultData[0] == 'CUSTOMER') {
                                    setCustomer(1, resultData[1]);
                                    setShipment(0, resultData[1], 0);
                                } else if (resultData[0] == 'LABOR') {
                                    setLabor(1, resultData[1]);
                                } else if (resultData[0] == 'SUPPLIER') {
                                    setSupplier(1, resultData[1]);
                                }

                                $('#insertModal').modal('hide');
                            } else {
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
                        }
                    }
                });
            }
        });

    });

    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });
    });

    function resetVehicle() {
        document.getElementById('unloadingCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('unloadingCostId').options.add(x);

        $("#unloadingCostId").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }

    function setUnloading(type, stockpileId, unloadingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getUnloadingCost',
                stockpileId: stockpileId
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
//                    if(returnValLength > 0) {
                    document.getElementById('unloadingCostId').options.length = 0;
                    var x = document.createElement('option');
                    x.value = '';
                    x.text = '-- Please Select --';
                    document.getElementById('unloadingCostId').options.add(x);

                    $("#unloadingCostId").select2({
                        width: "100%",
                        placeholder: "-- Please Select --"
                    });
//                    }

                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('unloadingCostId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('unloadingCostId').options.add(x);
                    }

                    <?php
                    if($allowUnloading) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('unloadingCostId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#unloadingCostId').find('option').each(function (i, e) {
                            if ($(e).val() == unloadingCostId) {
                                $('#unloadingCostId').prop('selectedIndex', i);

                                $("#unloadingCostId").select2({
                                    width: "100%",
                                    placeholder: unloadingCostId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetLabor() {
        document.getElementById('laborId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('laborId').options.add(x);

        $("#laborId").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }

    function setLabor(type, stockpileId, laborId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getLabor',
                stockpileId: stockpileId
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
//                    if(returnValLength > 0) {
                    document.getElementById('laborId').options.length = 0;
                    var x = document.createElement('option');
                    x.value = '';
                    x.text = '-- Please Select --';
                    document.getElementById('laborId').options.add(x);

                    $("#laborId").select2({
                        width: "100%",
                        placeholder: "-- Please Select --"
                    });
//                    }

                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('laborId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }

                    <?php
                    if($allowUnloading) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('laborId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#laborId').find('option').each(function (i, e) {
                            if ($(e).val() == laborId) {
                                $('#laborId').prop('selectedIndex', i);

                                $("#laborId").select2({
                                    width: "100%",
                                    placeholder: laborId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetVendor() {
        document.getElementById('vendorId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('vendorId').options.add(x);

        $("#vendorId").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }

    function setVendor(type, stockpileId, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getVendor',
                stockpileId: stockpileId,
                newVendorId: vendorId
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
                    if (returnValLength >= 0) {
                        document.getElementById('vendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorId').options.add(x);

                        $("#vendorId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorId').options.add(x);
                    }

                    <?php
                    if($allowVendor) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('vendorId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#vendorId').find('option').each(function (i, e) {
                            if ($(e).val() == vendorId) {
                                $('#vendorId').prop('selectedIndex', i);

                                $("#vendorId").select2({
                                    width: "100%",
                                    placeholder: vendorId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function addCommas(nStr) {
        nStr = nStr.replace(',', '');
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function resetFreight(text) {
        document.getElementById('freightCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightCostId').options.add(x);

        $("#freightCostId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setFreight(type, stockpileId, vendorId, freightCostId, trxDate) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightCost',
                stockpileId: stockpileId,
                vendorId: vendorId,
                trxDate: trxDate
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
                        document.getElementById('freightCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightCostId').options.add(x);

                        $("#freightCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('freightCostId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightCostId').options.add(x);
                    }

                    <?php
                    if($allowFreight) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('freightCostId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#freightCostId').find('option').each(function (i, e) {
                            if ($(e).val() == freightCostId) {
                                $('#freightCostId').prop('selectedIndex', i);

                                $("#freightCostId").select2({
                                    width: "100%",
                                    placeholder: freightCostId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetFreightDetail() {
        $('#labelFreightCost').hide();
        document.getElementById('labelFreightCost').innerHTML = '';
    }

    function resetHandling(text) {
        document.getElementById('handlingCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('handlingCostId').options.add(x);

        $("#handlingCostId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setHandling(type, stockpileId, vendorId, handlingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getHandlingCost',
                stockpileId: stockpileId,
                vendorId: vendorId
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
                        document.getElementById('handlingCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('handlingCostId').options.add(x);

                        $("#handlingCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('handlingCostId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('handlingCostId').options.add(x);
                    }

                    <?php
                    // if($allowFreight) {
                    ?>
//                    if(returnValLength > 0) {
                    //   var x = document.createElement('option');
                    //   x.value = 'INSERT';
                    //   x.text = '-- Insert New --';
                    //   document.getElementById('freightCostId').options.add(x);
//                    }                  
                    <?php
                    //  }
                    ?>

                    if (type == 1) {
                        $('#handlingCostId').find('option').each(function (i, e) {
                            if ($(e).val() == handlingCostId) {
                                $('#handlingCostId').prop('selectedIndex', i);

                                $("#handlingCostId").select2({
                                    width: "100%",
                                    placeholder: handlingCostId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetHandlingDetail() {
        $('#labelHandlingCost').hide();
        document.getElementById('labelHandlingCost').innerHTML = '';
    }

    function resetUnloadingDetail() {
        $('#labelUnloadingCost').hide();
        document.getElementById('labelUnloadingCost').innerHTML = '';
    }

    function resetContract(text) {
        document.getElementById('stockpileContractId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('stockpileContractId').options.add(x);

        $("#stockpileContractId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setContract(type, stockpileId, vendorId, stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getStockpileContract',
                stockpileId: stockpileId,
                vendorId: vendorId
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
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('stockpileContractId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#stockpileContractId').find('option').each(function (i, e) {
                            if ($(e).val() == stockpileContractId) {
                                $('#stockpileContractId').prop('selectedIndex', i);

                                $("#stockpileContractId").select2({
                                    width: "100%",
                                    placeholder: stockpileContractId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetContractPksDetail(text) {
        document.getElementById('contractPksDetailId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractPksDetailId').options.add(x);

        $("#contractPksDetailId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setContractPksDetail(type, stockpileId, vendorId, stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPoNoCurah',
                stockpileId: stockpileId,
                vendorId: vendorId,
                stockpileContractId: stockpileContractId,
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
                        document.getElementById('contractPksDetailId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select PKS --';
                        document.getElementById('contractPksDetailId').options.add(x);

                        $("#contractPksDetailId").select2({
                            width: "100%",
                            placeholder: "-- Please Select PKS --"
                        });
                    }

                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('contractPksDetailId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractPksDetailId').options.add(x);
                    }


                    if (type == 1) {
                        $('#contractPksDetailId').find('option').each(function (i, e) {
                            if ($(e).val() == contractPksDetailId) {
                                $('#contractPksDetailId').prop('selectedIndex', i);

                                $("#contractPksDetailId").select2({
                                    width: "100%",
                                    placeholder: contractPksDetailId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetShipment(text) {
        document.getElementById('shipmentId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId').options.add(x);

        $("#shipmentId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function resetSales(text) {
        document.getElementById('salesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('salesId').options.add(x);

        $("#salesId").select2({
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function resetContractDetail() {
        $('#labelQuantityContract').hide();
        document.getElementById('labelQuantityContract').innerHTML = '';
        document.getElementById('contractNo').value = '';
        // document.getElementById('supplierId').value = '';
//        $('#supplierId').attr("disabled", true);

        /*$("#supplierId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });*/
    }

    function setContractDetail(stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getContractDetail',
                stockpileContractId: stockpileContractId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    var returnVal = data.split('||');

                    //if(returnVal[0] == 'C') {
//                        $('#supplierId').attr("disabled", false);
                    // setSupplier(0, 0);
                    //} else if(returnVal[0] == 'P') {
                    $('#labelQuantityContract').show();
                    document.getElementById('labelQuantityContract').innerHTML = 'Quantity balance is ' + returnVal[2] + ' KG';
//                        $('#supplierId').attr("disabled", true);
                    //}

                    document.getElementById('contractNo').value = returnVal[1];
                    document.getElementById('supplierId').value = returnVal[3];
                }
            }
        });
    }

    function setFreightDetail(freightCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightDetail',
                freightCostId: freightCostId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelFreightCost').show();
                    document.getElementById('labelFreightCost').innerHTML = 'Freight cost/KG is ' + data;
                }
            }
        });
    }

    function setHandlingDetail(handlingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getHandlingDetail',
                handlingCostId: handlingCostId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelHandlingCost').show();
                    document.getElementById('labelHandlingCost').innerHTML = 'Handling cost/KG is ' + data;
                }
            }
        });
    }

    function setUnloadingDetail(unloadingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getUnloadingDetail',
                unloadingCostId: unloadingCostId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelUnloadingCost').show();
                    document.getElementById('labelUnloadingCost').innerHTML = 'Unloading cost is ' + data;
                }
            }
        });
    }

    function getNettoWeight(brutoWeight, tarraWeight) {
        if (brutoWeight.value != '' && tarraWeight.value != '') {
            document.getElementById('nettoWeight').value = brutoWeight.value.replace(new RegExp(",", "g"), "") - tarraWeight.value.replace(new RegExp(",", "g"), "");
        } else {
            document.getElementById('nettoWeight').value = '';
        }
    }

    function resetCustomer() {
        document.getElementById('customerId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('customerId').options.add(x);

        $("#customerId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }

    function setCustomer(type, customerId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getCustomer'
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
                        document.getElementById('customerId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('customerId').options.add(x);

                        $("#customerId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('customerId').options.add(x);
                    }

                    <?php
                    if($allowCustomer) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('customerId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#customerId').find('option').each(function (i, e) {
                            if ($(e).val() == customerId) {
                                $('#customerId').prop('selectedIndex', i);

                                $("#customerId").select2({
                                    width: "100%",
                                    placeholder: customerId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    /*function setLabor(type, laborId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getLabor'
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('laborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborId').options.add(x);
                        
                        $("#laborId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('laborId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }

                    <?php
    if($allowLabor) {
    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('laborId').options.add(x);
//                    }                  
                    <?php
    }
    ?>
                                        
                    if(type == 1) {
                        $('#laborId').find('option').each(function(i,e){
                            if($(e).val() == laborId){
                                $('#laborId').prop('selectedIndex',i);
                                
                                $("#laborId").select2({
                                    width: "100%",
                                    placeholder: laborId
                                });
                            }
                        });
                    }
                }
            }
        });
    }*/

    function setSales(type, customerId, stockpileId, salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getSales',
                customerId: customerId,
                stockpileId: stockpileId
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
                        document.getElementById('salesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('salesId').options.add(x);

                        $("#salesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('salesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('salesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#salesId').find('option').each(function (i, e) {
                            if ($(e).val() == salesId) {
                                $('#salesId').prop('selectedIndex', i);

                                $("#salesId").select2({
                                    width: "100%",
                                    placeholder: salesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function setShipment(type, salesId, shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getShipment',
                salesId: salesId,
                shipmentId: shipmentId
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

                    if (type == 1) {
                        $('#shipmentId').find('option').each(function (i, e) {
                            if ($(e).val() == salesId) {
                                $('#shipmentId').prop('selectedIndex', i);

                                $("#shipmentId").select2({
                                    width: "100%",
                                    placeholder: shipmentId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetShipmentDetail() {
//        document.getElementById('customerName').value = '';
        document.getElementById('quantityAvailable').value = '';
    }

    function setShipmentDetail(shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getShipmentDetail',
                shipmentId: shipmentId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    var returnVal = data.split('||');

//                    document.getElementById('customerName').value = returnVal[0];
                    document.getElementById('quantityAvailable').value = returnVal[1];
                }
            }
        });
    }

    /* function setSupplier(type, vendorId) {
         $.ajax({
             url: 'get_data.php',
             method: 'POST',
             data: { action: 'getSupplier',
                     newVendorId: vendorId
             },
             success: function(data){
                 var returnVal = data.split('~');
                 if(parseInt(returnVal[0])!=0)	//if no errors
                 {
                     //alert(returnVal[1].indexOf("{}"));
                     if(returnVal[1] == '') {
                         returnValLength = 0;
                     } else if(returnVal[1].indexOf("{}") == -1) {
                         isResult = returnVal[1].split('{}');
                         returnValLength = 1;
                     } else {
                         isResult = returnVal[1].split('{}');
                         returnValLength = isResult.length;
                     }

                     //alert(isResult);
                     if(returnValLength > 0) {
                         document.getElementById('supplierId').options.length = 0;
                         var x = document.createElement('option');
                         x.value = '';
                         x.text = '-- Please Select --';
                         document.getElementById('supplierId').options.add(x);

                         $("#supplierId").select2({
                             width: "100%",
                             placeholder: "-- Please Select --"
                         });
                     }

                     for (i=0; i < returnValLength; i++) {
                         var x = document.createElement('option');
                         resultOption = isResult[i].split('||');
                         x.value = resultOption[0];
                         x.text = resultOption[1];
                         document.getElementById('supplierId').options.add(x);
                     }

<?php if($allowSupplier) { ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('supplierId').options.add(x);
//                    }                  
                    <?php }    ?>
                                        
                    if(type == 1) {
                        $('#supplierId').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#supplierId').prop('selectedIndex',i);
                                
                                $("#supplierId").select2({
                                    width: "100%",
                                    placeholder: vendorId
                                });
                            }
                        });
                    }
                }
            }
        });
    }*/

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.js"></script>
<!-- <script src="https://code.jquery.com/git/jquery-3.x-git.slim.js"></script> -->
<!-- <script type="text/javascript" src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script> -->
<script>
    
  
   
    /*$('#snapPhoto').click(function () {
        $('#snapPhotoModal').modal('show');
        $('#snapPhotoModal').load('forms/cam-snap.php', {showImage: false});
    });
    $('#showImage').click(function () {
        $('#snapPhotoModal').modal('show');
        // $('#snapPhotoModal').innerHTML = '<img id="base64image" src="'+sessionStorage.getItem("file")+'"/>';
        $('#snapPhotoModal').load('forms/cam-snap.php', {showImage: true});
        $('#photoDocument').val(sessionStorage.getItem("file"));

    });*/

</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

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
 AND t.company_id = {$_SESSION['companyId']}
 ORDER BY t.transaction_id DESC LIMIT 1";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();

    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
    ?>

    <h4>Last Transaction</h4>

    <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
        <tr>
            <th>Type</th>
            <th>Slip No.</th>
            <th>Transaction Date</th>
            <th>Vehicle No./Vessel Name</th>
            <th>PO No./Shipment Code</th>
            <th>Weight (KG)</th>
            <!--<th>Total Price</th>-->
            <th>Entry By</th>
            <th>Entry Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $row->transaction_type2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->unloading_date2; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <!--<td><div style="text-align: right;"><?php echo number_format((($row->freight_price * $row->quantity) + $row->unloading_price), 0, ".", ","); ?></div></td>-->
            <td><?php echo $row->user_name; ?></td>
            <td><?php echo $row->entry_date2; ?></td>
            <td>
                <button class="btn btn-warning" id="showTransaction">Show</button>
                <button class="btn btn-danger" id="hideTransaction" style="display: none;">Hide</button>
                <button class="btn btn-info" id="printTransaction" style="display: none;">Print</button>
            </td>
        </tr>
        </tbody>
    </table>

    <div id="transactionContainer" style="display: none;">
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
                        <div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?>Kg
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
                        <div style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?> Kg</div>
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
                        <div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?>Kg
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
                        <div style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?>Kg
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

<!--<h4>Transaction Form</h4>-->

<form method="post" id="transactionDataForm">
    <input type="hidden" name="action" id="action" value="transaction_data"/>
    <input type="hidden" id="transactionDate" name="transactionDate"
           value="<?php echo $transactionDate; ?>">
    <input type="hidden"  id="photoDocument" name="photoDocument">
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span2 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full",
                "", 1, "select2combobox75");
            ?>
        </div>
        <div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT '1' as id, 'IN' as info UNION
                    SELECT '2' as id, 'OUT' as info;", $transactionType, "", "transactionType", "id", "info",
                "", 11, "select2combobox50");
            ?>
            <br>
            <!-- <a id="snapPhoto" class="btn btn-primary">OPEN CAMERA</a>
            <a id="showImage" class="btn btn-success">SHOW IMAGE</a>
			
            <!-- <a>SHOW DOC</a> -->
        </div>

    </div>

    <div id="inTransaction">

        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span2 lightblue">
                <label>Contract Name <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "vendorId", "vendor_id", "vendor_name",
                    "", 2, "select2combobox100", 2);
                ?>
            </div>
            <div class="span2 lightblue">
                <label>PO No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "stockpileContractId", "stockpile_contract_id", "po_no",
                    "", 13, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelQuantityContract" style="display: none;"></span>
            </div>
        </div>
        <div class="row-fluid" id="contractPks" style="margin-bottom: 7px">
            <div class="span2 lightblue">
                <label></label>
            </div>
            <div class="span4 lightblue">
                <label></label>
            </div>
            <div class="span2 lightblue">
                <label>Contract PKS <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "contractPksDetailId", "contract_pks_detail_id", "contract_pks",
                    "", 2, "select2combobox100", 3);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Receive Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="unloadingDate" name="unloadingDate"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['unloadingDate'];
                       } else {
                           echo $unloadingDate;
                       } ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
            <div class="span2 lightblue">
                <label></label>
            </div>
            <div class="span4 lightblue">
                <input type="hidden" readonly class="span12" tabindex="15" id="supplierId" name="supplierId">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Loading Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="4" id="loadingDate" name="loadingDate"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['loadingDate'];
                       } ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
            <div class="span2 lightblue">
                <label>Contract No.</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="15" id="contractNo" name="contractNo">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Vehicle <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "unloadingCostId", "unloading_cost_id", "vehicle_name",
                    "", 5, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelUnloadingCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Vehicle No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="vehicleNo" name="vehicleNo"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['vehicleNo'];
                       } ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Supplier Freight <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "freightCostId", "freight_cost_id", "freight_full",
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelFreightCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Driver <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="17" id="driver" name="driver"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['driver'];
                       } ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Unloading Org <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                /*createCombo("SELECT * FROM labor WHERE active = 1 ORDER BY labor_name ASC", $_SESSION['transaction']['laborId'], "", "laborId", "labor_id", "labor_name", 
                    "", 7, "select2combobox100", 3, "", $allowLabor);*/
                createCombo("", "", "", "laborId", "labor_id", "labor_name",
                    "", 5, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelFreightCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Delivery Notes No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="18" id="permitNo" name="permitNo"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['permitNo'];
                       } ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Handling Cost <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "handlingCostId", "handling_cost_id", "handling_full",
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelHandlingCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Stockpile Block <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("SELECT 'NONE' id, 'NONE' info UNION 
                            SELECT 'A' id, 'A' info UNION 
                            SELECT 'B' id, 'B' info UNION 
                            SELECT 'C' id, 'C' info UNION 
                            SELECT 'D' id, 'D' info UNION 
                            SELECT 'E' id, 'E' info UNION 
                            SELECT 'F' id, 'F' info UNION 
                            SELECT 'G' id, 'G' info", $_SESSION['transaction']['block'], "", "block", "id", "info",
                    "", 19, "select2combobox50");
                ?>
            </div>
        </div>
        <hr>
        <div class="row-fluid">
            <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>Sent Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="29" id="sendWeight" name="sendWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['sendWeight'];
                       } ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>Bruto Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="30" id="brutoWeight" name="brutoWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['brutoWeight'];
                       } ?>" onblur="getNettoWeight(this, document.getElementById('tarraWeight'));">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <!--<label>Unloading Cost</label>-->
            </div>
            <div class="span4 lightblue">
                <!--<label id="labelUnloadingCost"></label>-->
            </div>
            <div class="span2 lightblue">
                <label>Tarra Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="31" id="tarraWeight" name="tarraWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['tarraWeight'];
                       } ?>" onblur="getNettoWeight(document.getElementById('brutoWeight'), this);">
            </div>
        </div>
        <hr>
        <div class="row-fluid">
            <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div>
            <div class="span2 lightblue">
                <label>Netto Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="32" id="nettoWeight" name="nettoWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['nettoWeight'];
                       } ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes"
                          name="notes"><?php if (isset($_SESSION['transaction'])) {
                        echo $_SESSION['transaction']['notes'];
                    } ?></textarea>
            </div>
        </div>

    </div>

    <div id="outTransaction" style="display: none;">

        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Transaction Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="transactionDate2" name="transactionDate2"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['transactionDate2'];
                       } else {
                           echo $transactionDate2;
                       } ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
            <div class="span2 lightblue">
                <label>Buyer</label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("SELECT cust.customer_id, cust.customer_name
                            FROM customer cust ORDER BY cust.customer_name ASC", $customerId, "", "customerId", "customer_id", "customer_name",
                    "", 11, "select2combobox100", 1, "", $allowCustomer);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Vessel Name <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="3" id="vehicleNo2" name="vehicleNo2"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['vehicleNo2'];
                       } ?>">
            </div>
            <div class="span2 lightblue">
                <label>Sales Agreement No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "salesId", "sales_id", "sales_no",
                    "", 12, "select2combobox100", 1, "", $allowSales);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Stockpile Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="4" id="sendWeight2" name="sendWeight2"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['sendWeight2'];
                       } ?>">
            </div>
            <div class="span2 lightblue">
                <label>Shipment Code <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "shipmentId", "shipment_id", "shipment_no",
                    "", 13, "select2combobox100", 1, "", $allowShipment);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>B/L Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="5" id="blWeight" name="blWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['blWeight'];
                       } ?>">
            </div>
            <div class="span2 lightblue">
                <label>Quantity Agreed (KG)</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="14" id="quantityAvailable"
                       name="quantityAvailable">
                <input type="hidden" readonly class="span12" tabindex="14" id="idSuratTugas" name="idSuratTugas">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes2"
                          name="notes2"><?php if (isset($_SESSION['transaction'])) {
                        echo $_SESSION['transaction']['notes2'];
                    } ?></textarea>
            </div>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton">Submit</button>
        </div>
    </div>
</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel"
     aria-hidden="true">
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


<!-- <div id="snapPhotoModal" class="modal hide fade" role="dialog" aria-labelledby="snapPhotoModalLabel" aria-hidden="true"
     style="width:1000px; height:600px; margin-left:-500px;">
</div> -->
