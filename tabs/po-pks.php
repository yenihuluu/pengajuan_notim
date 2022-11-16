<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';


$readonlyProperty = '';
$readonlyProperty2 = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">


$po_pks_id = '';
//$contractType = '';
$contractNo = '';
$spbNo = '';
$vendorId = '';
$currencyId = '';
$price = '';
$quantity = '';
$exchangeRate = '';
//$contractSeq = '';
$notes = '';
$notes2 = '';
$contract = '';
$spb = '';
$finalStatus = '';
// </editor-fold>

// If ID is in the parameter
if (isset($_POST['po_pks_id']) && $_POST['po_pks_id'] != '') {

    $po_pks_id = $_POST['po_pks_id'];

    //$readonlyProperty2 = ' readonly ';
    //$disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT po.*,
(SELECT purchasing_id FROM purchasing WHERE contract_type = 1 AND purchasing_id = po.purchasing_id) AS contract,
(SELECT purchasing_id FROM purchasing WHERE contract_type = 2 AND purchasing_id = po.purchasing_id) AS spb
            FROM po_pks po
            WHERE po.po_pks_id = {$po_pks_id}
            ORDER BY po.po_pks_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        //$contractType = $rowData->contract_type;
        $contractNo = $rowData->contract_no;
        $vendorId = $rowData->vendor_id;
        $currencyId = $rowData->currency_id;
        $price = $rowData->price;
        $quantity = $rowData->quantity;
        $exchangeRate = $rowData->exchange_rate;
        $po_pks_id = $rowData->po_pks_id;
        $stockpileId = $rowData->stockpile_id;
        $notes = $rowData->notes;
        $notes2 = $rowData->notes2;
        $lockContract = $rowData->lock_contract;
        $spbNo = $rowData->spb_no;
        $contract = $rowData->contract;
        $spb = $rowData->spb;
        $finalStatus = $rowData->final_status;

        if ($lockContract == 1) {
            $readonlyProperty = ' readonly ';
        }
    }
    $sql2 = "SELECT c.`payment_status`
            FROM po_pks po
            LEFT JOIN po_contract poc ON po.`po_pks_id` = poc.`po_pks_id`
            LEFT JOIN contract c ON c.`contract_id` = poc.`contract_id`
            WHERE po.po_pks_id = {$po_pks_id}
            GROUP BY po.`po_pks_id`
            ORDER BY po.po_pks_id ASC
            ";
//            echo $sql;
    $resultData2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    if ($resultData2 !== false && $resultData2->num_rows > 0) {
        $rowData2 = $resultData2->fetch_object();
        $paymentStatus = $rowData2->paymentStatus;

        if ($paymentStatus == 1) {
            $readonlyProperty2 = ' readonly ';
        }
    }
    // </editor-fold>

} else {
    //$generatedPoNo = "";
    if (isset($_SESSION['po_pks'])) {
        // $contractType = $_SESSION['contract']['contractType'];
        $spbNo = $_SESSION['contract']['spbNo'];
        $contractNo = $_SESSION['contract']['contractNo'];
        $vendorId = $_SESSION['contract']['vendorId'];
        $currencyId = $_SESSION['contract']['currencyId'];
        $price = $_SESSION['contract']['price'];
        //$quantity = $_SESSION['contract']['quantity'];
        $exchangeRate = $_SESSION['contract']['exchangeRate'];
        $stockpileId = $_SESSION['contract']['stockpileId'];
        //$contractSeq = $_SESSION['contract']['contractSeq'];
        $notes = $_SESSION['contract']['notes'];
        $notes2 = $_SESSION['contract']['notes2'];
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

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $('#quantity').number(true, 2);

        $('#price').number(true, 10);

        $('#uploadFile').hide();

        <?php
        if($contract == "") {
        ?>
        setConDoc(0, 0)

        <?php
        }elseif($contract != ""){
        ?>
        setConDoc(1,<?php echo $contract?>)
        <?php
        }elseif($spb == "") {
        ?>
        setSPBDoc(0, 0)
        <?php
        }else{
        ?>
        setSPBDoc(1,<?php echo $spb?>)
        <?php
        }
        ?>

        if (document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }

        jQuery.validator.addMethod("indonesianDate", function (value, element) {
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });

        $('#currencyId').change(function () {
            if (document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });


        $('#contract').change(function () {
            resetContract();
            resetVendorPOPKS();
            resetStockpilePOPKS();
            if (document.getElementById('contract').value != '') {
                setContract($('select[id="contract"]').val());
            }
        });

        $('#spb').change(function () {
            resetSPB();
            resetVendorPOPKS();
            resetStockpilePOPKS();
            if (document.getElementById('spb').value != '') {
                setSPB($('select[id="spb"]').val());
            }
        });

        function setConDoc(type, contract) {

            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: {
                    action: 'getConDoc',
                    contract: contract
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
                            document.getElementById('contract').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('contract').options.add(x);

                            $("#contract").select2({
                                width: "100%",
                                placeholder: "-- Please Select --"
                            });
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('contract').options.add(x);
                        }


                        if (type == 1) {
                            $('#contract').find('option').each(function (i, e) {
                                if ($(e).val() == contract) {
                                    $('#contract').prop('selectedIndex', i);

                                    $("#contract").select2({
                                        width: "100%",
                                        placeholder: contract
                                    });
                                }
                            });
                        }
                    }
                    setContract(contract);
                }

            });

        }

        function setSPBDoc(type, spb) {

            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: {
                    action: 'getSPB',
                    spb: spb
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
                            document.getElementById('spb').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('spb').options.add(x);

                            $("#spb").select2({
                                width: "100%",
                                placeholder: "-- Please Select --"
                            });
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('spb').options.add(x);
                        }


                        if (type == 1) {
                            $('#spb').find('option').each(function (i, e) {
                                if ($(e).val() == spb) {
                                    $('#spb').prop('selectedIndex', i);

                                    $("#spb").select2({
                                        width: "100%",
                                        placeholder: spb
                                    });
                                }
                            });
                        }
                    }
                    setSPB(spb);
                }

            });

        }

        function resetContract() {

            //document.getElementById('vendorName').value = '';
            //document.getElementById('vendorId').value = '';
            //document.getElementById('stockpileName').value = '';

            //document.getElementById('stockpileId').value = '';
            document.getElementById('quantity').value = 0;
            document.getElementById('price').value = 0;
            document.getElementById('docContract').href = '';
        }

        function resetSPB() {

            //document.getElementById('vendorName').value = '';
            //document.getElementById('vendorId').value = '';
            //document.getElementById('stockpileName').value = '';

            //document.getElementById('stockpileId').value = '';
            document.getElementById('quantity').value = 0;
            document.getElementById('price').value = 0;
            document.getElementById('docSPB').href = '';
        }

        function setContract(contract) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getContractDoc',
                    contract: contract
                },
                success: function (data) {
//                        alert(data);
                    if (data != '') {
                        var returnVal = data.split('||');

                        $('#contractDoc').show();
                        <?php
                        if($po_pks_id == "") {
                        ?>
                        setVendorPOPKS(1, returnVal[2]);
                        document.getElementById('price').value = returnVal[7];
                        document.getElementById('quantity').value = returnVal[6];
						document.getElementById('ho').value = returnVal[8];

                        <?php
                        }else{
                        ?>
                        setVendorPOPKS(1, <?php echo $vendorId;?>);

                        <?php
                        }
                        ?>
                        setStockpilePOPKS(1, returnVal[4]);
                        //document.getElementById('vendorName').value = returnVal[1];
                        //document.getElementById('vendorId').value = returnVal[2];
                        //document.getElementById('stockpileName').value = returnVal[3];
                        //document.getElementById('stockpileId').value = returnVal[4];
                        document.getElementById('docContract').href = returnVal[5];

                        if (returnVal[8] == 1) {
                            $('#uploadFile').show();
                        } else {
                            $('#uploadFile').hide();
                        }
                    }
                }
            });
        }

        function setSPB(spb) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getSPBDoc',
                    spb: spb
                },
                success: function (data) {
//                        alert(data);
                    if (data != '') {
                        var returnVal = data.split('||');

                        $('#spbDoc').show();
                        <?php
                        if($po_pks_id == "") {
                        ?>
                        setVendorPOPKS(1, returnVal[2]);
                        <?php
                        }else{
                        ?>
                        setVendorPOPKS(1, <?php echo $vendorId;?>);
                        <?php
                        }
                        ?>
                        setStockpilePOPKS(1, returnVal[4]);
                        //document.getElementById('vendorName').value = returnVal[1];
                        //document.getElementById('vendorId').value = returnVal[2];
                        //document.getElementById('stockpileName').value = returnVal[3];
                        //document.getElementById('stockpileId').value = returnVal[4];
                        document.getElementById('docSPB').href = returnVal[5];
                        document.getElementById('quantity').value = returnVal[6];
                        document.getElementById('price').value = returnVal[7];

                    }
                }
            });
        }

        function resetVendorPOPKS() {
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

        function setVendorPOPKS(type, vendorId) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getVendorPOPKS',
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

        function resetStockpilePOPKS() {
            document.getElementById('stockpileId').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('stockpileId').options.add(x);

            $("#stockpileId").select2({
                width: "100%",
                placeholder: "-- Please Select --"
            });
        }

        function setStockpilePOPKS(type, stockpileId) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getStockpilePOPKS',
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
                            document.getElementById('stockpileId').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('stockpileId').options.add(x);

                            $("#stockpileId").select2({
                                width: "100%",
                                placeholder: "-- Please Select --"
                            });
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('stockpileId').options.add(x);
                        }


                        if (type == 1) {
                            $('#stockpileId').find('option').each(function (i, e) {
                                if ($(e).val() == stockpileId) {
                                    $('#stockpileId').prop('selectedIndex', i);

                                    $("#stockpileId").select2({
                                        width: "100%",
                                        placeholder: stockpileId
                                    });
                                }
                            });
                        }
                    }
                }
            });
        }

        $('#PoDataForm').on('submit', function (e) {
            e.preventDefault();
			$.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#importButton').attr("disabled", true);
            $('#closeImportButton').attr("disabled", true);
            $('#importModal').modal('hide');
            $('#dataContent').load('contents/po-pks.php', {}, iAmACallbackFunction2);

            //$.blockUI({ message: '<h4>Please wait...</h4>' });

            $(this).ajaxSubmit({
                success: showResponse //call function after success
            });
        });
    });

    function showResponse(responseText, statusText, xhr, $form) {
        // for normal html responses, the first argument to the success callback
        // is the XMLHttpRequest object's responseText property

        // if the ajaxSubmit method was passed an Options Object with the dataType
        // property set to 'xml' then the first argument to the success callback
        // is the XMLHttpRequest object's responseXML property

        // if the ajaxSubmit method was passed an Options Object with the dataType
        // property set to 'json' then the first argument to the success callback
        // is the json data object returned by the server

//        alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
//            '\n\nThe output div should have already been updated with the responseText.');

        var returnVal = responseText.split('|');
//        alert(returnVal);
        if (parseInt(returnVal[3]) != 0)	//if no errors
        {
//            alert(responseText);
            alertify.set({
                labels: {
                    ok: "OK"
                }
            });
            alertify.alert(returnVal[2]);
            if (returnVal[1] == 'OK') {
                //show success message
                $('#importModal').modal('hide');
                $('#dataContent').load('contents/po-pks.php', {}, iAmACallbackFunction2);

//                document.getElementById('successMsg').innerHTML = returnVal[2];
//                $("#successMsg").show();

            } else {
                //show error message
//                document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
//                $("#modalErrorMsg").show();
                $('#importButton').attr("disabled", false);
                $('#closeImportButton').attr("disabled", false);
            }
        }

    }
	  

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
        // Session Storage Browser
        Object.keys(sessionStorage).forEach((key) => {
            var newKey = key.split('.');
            if (newKey[0] == "poPKSData" && newKey[1] != "") {
                document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
                $('#' + newKey[1]).trigger('change');
            }
        });
        $(":input").change(function () {
            sessionStorage.setItem("poPKSData." + this.id, this.value);
        });
    });
	
</script>

<form method="post" enctype="multipart/form-data" id="PoDataForm" action="./data_processing.php">
    <input type="hidden" name="action" id="action" value="po_pks_data"/>
    <input type="hidden" name="po_pks_id" id="po_pks_id" value="<?php echo $po_pks_id; ?>"/>
    <input type="hidden" name="ho" id="ho" name = "ho"/>

	

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Contract No. <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="contractNo" name="contractNo"
                   value="<?php echo $contractNo; ?>">
        </div>
        <div class="span2 lightblue">
            <label>Contract Document<span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "contract", "purchasing_id", "purchasing_id",
                "", 2, "select2combobox100");
            ?>
        </div>

        <div class="span2 lightblue" id="contractDoc" style="display: none;">
            <label>View Contract</label>
            <a href="" id="docContract" role="button" title="View" target="_blank"><img src="assets/ico/gnome-print.png"
                                                                                        width="18px" height="18px"
                                                                                        style="margin-bottom: 5px;"/></a>
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>SPB No. <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="spbNo" name="spbNo" value="<?php echo $spbNo; ?>">
        </div>
        <div class="span2 lightblue">
            <label>SPB Document <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT purchasing_id FROM purchasing WHERE status = 0 AND contract_type = 2 AND admin_input IS NULL
			ORDER BY purchasing_id ASC", $spb, "", "spb", "purchasing_id", "purchasing_id",
                "", "", "select2combobox100");
            ?>
        </div>

        <div class="span2 lightblue" id="spbDoc" style="display: none;">
            <label>View SPB</label>
            <a href="" id="docSPB" role="button" title="View" target="_blank"><img src="assets/ico/gnome-print.png"
                                                                                   width="18px" height="18px"
                                                                                   style="margin-bottom: 5px;"/></a>
        </div>
    </div>

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'OK' as info UNION
               		   SELECT '2' as id, 'SPB' as info UNION
                     SELECT '3' as id, 'Incomplete' as info;", $finalStatus, "", "finalStatus", "id", "info", "", "", "select2combobox100", 1);
            ?>

        </div>
    </div>
    <div class="row-fluid">
        <div class="span8 lightblue">
            <label>Notes Status <span style="color: red;"></span></label>
            <textarea class="span12" rows="3" tabindex="" id="notes2" name="notes2"><?php echo $notes2; ?></textarea>
        </div>

    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorId", "vendor_id", "vendor_full",
                "", 2, "select2combobox100");
            ?>
        </div>

        <div class="span4 lightblue">
            <label>Stockpile<span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "stockpileId", "stockpile_id", "stockpile_full",
                "", 2, "select2combobox100");
            ?>
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">


        <div class="span4 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div class="span2 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code",
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span2 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate"
                   value="<?php echo $exchangeRate; ?>">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Quantity (KG) <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="quantity" name="quantity"
                   value="<?php echo $quantity; ?>">
        </div>

        <div class="span4 lightblue">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span8 lightblue">
            <label>Notes <span style="color: red;"></span></label>
            <textarea class="span12" rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>

    </div>
    <div id="uploadFile">
        <label class="control-label" for="imagefile">Upload File (Acceptable format is PDF file)<span
                    style="color: red;">*</span></label>
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="input-append">
                <div class="uneditable-input" style="min-width: 200px;">
                    <i class="icon-file fileupload-exists"></i>
                    <span class="fileupload-preview"></span>
                </div>
                <span class="btn btn-file">
                    <span class="fileupload-new">Select file</span>
                    <span class="fileupload-exists">Change</span>
                    <input type="file" name="imagefile" id="imagefile"/>
                </span>
                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="importButton">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
