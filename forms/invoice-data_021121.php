<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'invoiceDetail';

$invMethod = '';
$invoiceType = '';
$accountId = '';
$poId = '';
$invoiceType = '';
$generatedInvoiceNo = '';
$generalVendorId = '';
$pph1 = '';
$ppn1 = '';
$ppnID = '';
$pphID = '';
if (isset($_POST['invoiceId']) && $_POST['invoiceId'] != '') {

    $invoiceId = $_POST['invoiceId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT i.*, 
            FROM invoice_detail i
            WHERE inv.invoice_id = {$invoiceId}
            ORDER BY i.invoice_detail_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();

    }

    // </editor-fold>

} else {
    $generatedInvoiceNo = "";
    if (isset($_SESSION['invoice'])) {

    }
}

if (isset($_SESSION['invoiceDetail'])) {
    $invoiceType = $_SESSION['invoiceDetail']['invoiceType'];
    $generatedInvoiceNo = $_SESSION['invoiceDetail']['generatedInvoiceNo'];
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
        echo "<option value=''>-- Please Select --</option>";
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
    $(document).ready(function () {
        $("select.select2combobox100").select2({
            width: "100%"
        });

        $("select.select2combobox50").select2({
            width: "50%"
        });

        $("select.select2combobox75").select2({
            width: "75%"
        });

        // $('#qty').number(true, 2);
        // $('#price').number(true, 2);
        // $('#amount').number(true, 2);
        

    });

    $('#transaksiMutasi').change(function () {

        if (document.getElementById('transaksiMutasi').value == 1) {
            $('#mutasiLabel').show();

            $('#invoiceDetail1').hide();
            $('#invoiceDetail2').hide();
            $('#divtipebiaya').hide();
            //$('#invoiceDetail3').hide();
            $('#divCodePrediksi').hide();
            $('#divAccount').show();
            $('#divShipment').show();
            $('#divShipmentPrediksi').hide();
            $('#divAccountPrediksi').hide();
            $('#divVendor').show();
            $('#divVendorPrediksi').hide();
            setMutasiHeader();
        } else {
            $('#mutasiLabel').hide();
            $('#mutasiDetail').hide();
            $('#invoiceDetail1').show();
            $('#invoiceDetail2').show();
            $('#divtipebiaya').hide();
            $('#divCodePrediksi').hide();
            $('#divAccount').show();
            $('#divShipment').show();
            $('#divShipmentPrediksi').hide();
            $('#divAccountPrediksi').hide();
            $('#divVendor').show();
            $('#divVendorPrediksi').hide();


          
            //$('#invoiceDetail').show();

        }
    });

    $('#mutasiId').change(function () {
        //resetExchangeRate();
        $('#mutasiDetail').hide();
        //resetMutasiDetail();

        if (document.getElementById('mutasiId').value != '' && document.getElementById('mutasiId').value != 'INSERT') {

            if (document.getElementById('invoiceMethod').value == 1) {
                setMutasiDetail($('select[id="mutasiId"]').val());

                document.getElementById('invoiceMethodDetail').value = '1';
            } else {
                document.getElementById('invoiceMethodDetail').value = '2';
            }
        }
    });

    $('#generalVendorId').change(function () {
        //resetExchangeRate();
        $('#divCodePrediksi').hide();
        $('#IDP').hide();
        $('#divprediksidetail').hide();
        
        resetGeneralVendorTax();

        if (document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
            getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
            setPPh(1, $('select[id="generalVendorId"]').val(), 0);
            if (document.getElementById('invoiceMethod').value == 1) {
                setInvoiceDP($('select[id="generalVendorId"]').val(), '', '', 'NONE');
                document.getElementById('invoiceMethodDetail').value = '1';
            } else {
                document.getElementById('invoiceMethodDetail').value = '2';
            }
        }
    });

    $('#generalVendorId1').change(function () {
        $('#divCodePrediksi').hide();
        $('#IDP').hide();
        $('#divprediksidetail').hide();
        
        resetGeneralVendorTax();
        resetPrediksiId(' ');
        if (this.value != '' && document.getElementById('invoiceType').value == 4) {
            getGeneralVendorTax($('select[id="generalVendorId1"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
            setPPh(1, $('select[id="generalVendorId1"]').val(), 0);
            if (document.getElementById('invoiceMethod').value == 1) {
                setInvoiceDP($('select[id="generalVendorId1"]').val(), '', '', 'NONE');
                document.getElementById('invoiceMethodDetail').value = '1';
            } else {
                document.getElementById('invoiceMethodDetail').value = '2';
            }
            getPrediksiId(0, $('select[id="shipmentIdPrediksi"]').val(), $('select[id="tipeBiayaId"]').val(), $('select[id="generalVendorId1"]').val(), 0);

        }else{
            resetPrediksiId('Account Id ');
        }
       
    });

    $('#prediksiId').change(function () {
        $('#divprediksidetail').hide();
        if (document.getElementById('prediksiId').value != '') {
            setPrediksiDetail($('select[id="prediksiId"]').val());
        }
    });


    function resetGeneralVendorTax() {
        document.getElementById('ppn1').value = '';
        //document.getElementById('pph1').value = '';
        document.getElementById('ppnID').value = '';
        //document.getElementById('pphID').value = '';
    }

    function getGeneralVendorTax(generalVendorId, amount) {

        if (amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getGeneralVendorTax',
                    generalVendorId: generalVendorId,
                    amount: amount
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {
                        document.getElementById('ppn1').value = returnVal[1];
                        //document.getElementById('pph1').value = returnVal[2];
                        document.getElementById('ppnID').value = returnVal[3];
                        //document.getElementById('pphID').value = returnVal[4];
                        document.getElementById('checkedPPN').checked = true;
                        //document.getElementById('checkedPPh').checked = true;
                    }
                }
            });
        } else {
            document.getElementById('ppn1').value = '0';
            //document.getElementById('pph1').value = '0';
            document.getElementById('ppnID').value = '0';
            //document.getElementById('pphID').value = '0';
        }
    }
</script>
<script type="text/javascript">
    if (document.getElementById('generalVendorId').value != '') {
        resetGeneralVendorTax(' ');
        resetSetPPh(' ');
        resetInvoiceDP(' ');
        <?php
        if($_SESSION['invoice']['generalVendorId'] != '') {
        ?>
        getGeneralVendorTax(1, $('select[id="generalVendorId"]').val(), <?php echo $_SESSION['invoice']['generalVendorId']; ?>);
        setPPh(1, $('select[id="generalVendorId"]').val(),<?php echo $_SESSION['invoice']['generalVendorId']; ?>);
        if (document.getElementById('invoiceMethod').value == 1) {
            setInvoiceDP(1, $('select[id="generalVendorId"]').val(),<?php echo $_SESSION['invoice']['generalVendorId']; ?>, 'NONE');

        } else {
            resetInvoiceDP(' ');
        }
        <?php
        } else {
        ?>
        setInvoiceDP(0, $('select[id="generalVendorId"]').val(), 0);
        <?php
        }
        ?>
    }

    <?php
    if(isset($_SESSION['invoiceDetail'])) {
    ?>
    if (document.getElementById('invoiceType').value != '') {
        resetAccount(' ');
        <?php
        if($_SESSION['invoiceDetail']['accountId'] != '') {
        ?>
        setAccount(1, $('select[id="invoiceType"]').val(), <?php echo $_SESSION['invoiceDetail']['accountId']; ?>);
        <?php
        } else {
        ?>
        setAccount(0, $('select[id="invoiceType"]').val(), 0);
        <?php
        }
        ?>

    } else {
        resetAccount(' Invoice Type ');
    }

    <?php

    }
    ?>

    <?php
    if(isset($_SESSION['invoiceDetail'])) {
    ?>
    if (document.getElementById('stockpileId2').value != '') {
        resetPoNo(' ');
        resetShipmentCode('  ');
        <?php
        if($_SESSION['invoiceDetail']['poId'] != '' || $_SESSION['invoiceDetail']['shipmentId1'] != '') {
        ?>
        setPoNo(1, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['invoiceDetail']['poId']; ?>);
        setShipmentCode(1, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['invoiceDetail']['shipmentId1']; ?>);
        <?php
        } else {
        ?>
        setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
        setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        <?php
        }
        ?>

    } else {
        resetPoNo(' PO NO ');
        resetShipmentCode(' Shipment Code ');
    }

    <?php    }    ?>

    $.ajax({
        url: './get_data.php',
        method: 'POST',
        data: {
            action: 'getInvoiceNo'
            //stockpileContractId: stockpileContractId,
            //paymentMethod: paymentMethod,
            //ppn: ppnValue,
            //pph: pphValue
        },
        success: function (data) {
            if (data != '') {
                document.getElementById('generatedInvoiceNo').value = data;

            }
            setInvoiceType(generatedInvoiceNo);
            if (document.getElementById('invoiceMethod').value == 1) {
                $('#method').show();
            }
        }

    });

    function setInvoiceType(generatedInvoiceNo) {
        document.getElementById('invoiceType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('invoiceType').options.add(x);

        /*
             var x = document.createElement('option');
             x.value = '0';
             x.text = 'PKS Kontrak';
             document.getElementById('invoiceType').options.add(x);

             var x = document.createElement('option');
             x.value = '1';
             x.text = 'PKS Curah/Sales';
             document.getElementById('invoiceType').options.add(x);

             var x = document.createElement('option');
             x.value = '2';
             x.text = 'Freight Cost';
             document.getElementById('invoiceType').options.add(x);

             var x = document.createElement('option');
             x.value = '3';
             x.text = 'Unloading Cost';
             document.getElementById('invoiceType').options.add(x);
             */
        var x = document.createElement('option');
        x.value = '4';
        x.text = 'Loading';
        document.getElementById('invoiceType').options.add(x);
        /*
        var x = document.createElement('option');
        x.value = '7';
        x.text = 'Internal Transfer';
        document.getElementById('invoiceType').options.add(x);
    */

        var x = document.createElement('option');
        x.value = '5';
        x.text = 'Umum';
        document.getElementById('invoiceType').options.add(x);

        var x = document.createElement('option');
        x.value = '6';
        x.text = 'HO';
        document.getElementById('invoiceType').options.add(x);

        <?php
        if(isset($_SESSION['invoiceDetail']) && $_SESSION['invoiceDetail']['invoiceType'] != '') {
        ?>
        document.getElementById('invoiceType').value = <?php echo $invoiceType; ?>;

        <?php  } ?>
    }

    function resetsetPPh(text) {
        document.getElementById('pphTaxId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('pphTaxId').options.add(x);
    }

    function setPPh(type, generalVendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGeneralVendorPPh',
                generalVendorId: generalVendorId

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('pphTaxId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('pphTaxId').options.add(x);
                    }

                    var x = document.createElement('option');
                    x.value = 0;
                    x.text = 'NONE';
                    document.getElementById('pphTaxId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('pphTaxId').options.add(x);
                    }

                    if (type == 1) {
                        $('#pphTaxId').find('pphTaxId').each(function (i, e) {
                            if ($(e).val() == pphTaxId) {
                                $('#pphTaxId').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

    function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }

    function setAccount(type, invoiceType) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getAccountInvoice',
                invoiceType: invoiceType

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('accountId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('accountId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('accountId').options.add(x);
                    }

                    if (type == 1) {
                        $('#accountId').find('accountId').each(function (i, e) {
                            if ($(e).val() == accountId) {
                                $('#accountId').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

    
    function resetAccountPrediksi(text) {
        document.getElementById('accountIdPrediksi').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountIdPrediksi').options.add(x);
    }

    function setAccountPrediksi(type, shipmentId1) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getAccountInvoicePrediksi',
                shipmentId1: shipmentId1

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('accountIdPrediksi').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('accountIdPrediksi').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('accountIdPrediksi').options.add(x);
                    }

                    if (type == 1) {
                        $('#accountIdPrediksi').find('accountIdPrediksi').each(function (i, e) {
                            if ($(e).val() == accountIdPrediksi) {
                                $('#accountIdPrediksi').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }


    function resetGVprediksi(text) {
        document.getElementById('generalVendorId1').options.length = 0;
        document.getElementById('tipeBiayaId').options.length = 0;
        var x = document.createElement('option');
        var x1 = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        x1.value = '';
        x1.text = '-- Please Select' + text + '--';
        document.getElementById('generalVendorId1').options.add(x);
        document.getElementById('tipeBiayaId').options.add(x1);
    }

    function setGVprediksi(type, shipmentId, accountId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGVprediksi',
                shipmentId: shipmentId,
                accountId: accountId

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('generalVendorId1').options.length = 0;
                        document.getElementById('tipeBiayaId').options.length = 0;
                        var x = document.createElement('option');
                        var x1 = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        x1.value = '';
                        x1.text = '-- Please Select Account--';
                        document.getElementById('generalVendorId1').options.add(x);
                        document.getElementById('tipeBiayaId').options.add(x1);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        var x1 = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        x1.value = resultOption[2];
                        x1.text = resultOption[3];
                        document.getElementById('generalVendorId1').options.add(x);
                        document.getElementById('tipeBiayaId').options.add(x1);
                    }

                    if (type == 1) {
                        $('#generalVendorId1').find('generalVendorId1').each(function (i, e) {
                            if ($(e).val() == generalVendorId1) {
                                $('#generalVendorId1').prop('selectedIndex', i);
                            }
                        });
                        $('#tipeBiayaId').find('tipeBiayaId').each(function (i, e) {
                            if ($(e).val() == tipeBiayaId) {
                                $('#tipeBiayaId').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }


    function resetPoNo(text) {
        document.getElementById('poId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('poId').options.add(x);
    }

    function setPoNo(type, accountId, stockpileId2) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPoInvoice',
                accountId: accountId,
                stockpileId2: stockpileId2

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('poId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('poId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('poId').options.add(x);
                    }

                    if (type == 1) {
                        $('#poId').find('poId').each(function (i, e) {
                            if ($(e).val() == poId) {
                                $('#poId').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

    function resetShipmentCode(text) {
        document.getElementById('shipmentId1').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId1').options.add(x);
    }

    function setShipmentCode(type, stockpileId2) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getShipmentInvoice',
                stockpileId2: stockpileId2

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('shipmentId1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId1').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId1').options.add(x);
                    }

                    if (type == 1) {
                        $('#shipmentId1').find('shipmentId1').each(function (i, e) {
                            if ($(e).val() == shipmentId1) {
                                $('#shipmentId1').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

    function resetShipmentCodePrediksi(text) {
        document.getElementById('shipmentIdPrediksi').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentIdPrediksi').options.add(x);
    }

    function setShipmentCodePrediksi(type, invoiceType) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getShipmentInvoicePrediksi',
                invoiceType: invoiceType

            },
            success: function (data) {
                //alert(data);
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
                        document.getElementById('shipmentIdPrediksi').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentIdPrediksi').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentIdPrediksi').options.add(x);
                    }

                    if (type == 1) {
                        $('#shipmentIdPrediksi').find('shipmentIdPrediksi').each(function (i, e) {
                            if ($(e).val() == shipmentIdPrediksi) {
                                $('#shipmentIdPrediksi').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

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

    $('#invoiceType').change(function () {
        if (document.getElementById('invoiceType').value != '') {
            if (this.value == 4 && document.getElementById('transaksiMutasi').value ==  0 ) {
                $('#divAccount').hide();
                $('#divShipmentPrediksi').show();
                $('#divAccountPrediksi').show();
                $('#divShipment').hide();
                $('#divVendor').hide();
                $('#divVendorPrediksi').show();
                $('#divtipebiaya').show();
                resetShipmentCodePrediksi(' Shipment Code ');
                setShipmentCodePrediksi(0, $('select[id="invoiceType"]').val(), 0);
            }else{
                $('#divAccount').show();
                $('#divShipmentPrediksi').hide();
                $('#divAccountPrediksi').hide();
                $('#divShipment').show();
                $('#divVendor').show();
                $('#divVendorPrediksi').hide();
                $('#divtipebiaya').hide();
                resetAccount(' ');
                setAccount(0, $('select[id="invoiceType"]').val(), 0);
            }
        } else {
            resetAccount(' Invoice Type ');
        }
    });

    $('#shipmentIdPrediksi').change(function () {
        if (this.value != '' && document.getElementById('invoiceType').value == 4) {
            resetAccountPrediksi(' ');
            setAccountPrediksi(0, $('select[id="shipmentIdPrediksi"]').val(), 0);
        } else {
            resetAccountPrediksi(' Shipment Id ');
          
        }
    });

    $('#accountIdPrediksi').change(function () {
        if (this.value != '' && document.getElementById('invoiceType').value == 4) {
            resetGVprediksi(' ');
            setGVprediksi(0, $('select[id="shipmentIdPrediksi"]').val(), $('select[id="accountIdPrediksi"]').val(), 0);

        } else {
            resetGVprediksi(' Account Id ');
        }
    });

    $('#stockpileId2').change(function () {
        if (document.getElementById('stockpileId2').value != '' && document.getElementById('accountId').value != '') {
            resetPoNo(' ');
            resetShipmentCode(' Shipment Code ');
            setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
            setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        } else {
            resetPoNo(' PO NO ');
            resetShipmentCode(' Shipment Code ');
        }
    });


</script>
<script type="text/javascript">
    $(document).ready(function () {
        //this calculates values automatically
        $('#ppn1').number(true, 10);
        //$('#pph1').number(true, 10);
        sum();
        $("#qty, #price, #ppn1, #termin").on("keydown keyup", function () {
            sum();

            $('#amount').number(true, 10);

        });
    });

    function sum() {
        var num1 = document.getElementById('qty').value;
        var num2 = document.getElementById('price').value;
        var num3 = document.getElementById('ppn1').value;
        //var num4 = document.getElementById('pph1').value;
        var num5 = document.getElementById('termin').value;
        var result = (parseFloat(num1) * parseFloat(num2)) * (parseFloat(num5) / 100);
        //var result1 = parseInt(num1) * parseInt(num2) + parseInt(num3) - parseInt(num4);
        if (!isNaN(result)) {
            document.getElementById('amount').value = result;
            //document.getElementById('tamount').value = result1;
        }
    }

    function sum2() {


        //alert('TEST');
        var num1 = document.getElementById('qtyInvoiceValue').value;
        var num2 = document.getElementById('terminInvoice').value;
        //var num3 = document.getElementById('ppn1').value;
        //var num4 = document.getElementById('pph1').value;
        //var num5 = document.getElementById('termin').value;
        var result = parseFloat(num1) * (parseFloat(num2) / 100);
        //var result1 = parseInt(num1) * parseInt(num2) + parseInt(num3) - parseInt(num4);
        if (!isNaN(result)) {
            document.getElementById('qtyInvoice').value = result;
            //document.getElementById('tamount').value = result1;
        }
    }

    function checkPPN() {
        var checkedPPN = document.getElementById('checkedPPN');
        var generalVendorId = document.getElementById('generalVendorId').value;
        var amount = document.getElementById('amount').value.replace(new RegExp(",", "g"), "");
        if (checkedPPN.checked != true) {
            document.getElementById('ppn1').value = 0;
        } else {
            getGeneralVendorTax(generalVendorId, amount);
        }

    }

    function resetPrediksiId(text) {
        document.getElementById('prediksiId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('prediksiId').options.add(x);
    }

    function getPrediksiId(type, shipmentId, tipeBiayaId, generalVendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPrediksiId',
                shipmentId: shipmentId,
                tipeBiayaId: tipeBiayaId,
                generalVendorId: generalVendorId

            },
            success: function (data) {
                //alert(data);
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

                  //  alert(returnValLength);


                    if (returnValLength > 1) {
                        $('#divCodePrediksi').show();
                        $('#divInputPrediksi').hide();
                        $('#divComboPrediksi').show();
                        document.getElementById('prediksiId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('prediksiId').options.add(x);
                    

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('prediksiId').options.add(x);
                        }

                        if (type == 1) {
                            $('#prediksiId').find('prediksiId').each(function (i, e) {
                                if ($(e).val() == prediksiId) {
                                    $('#prediksiId').prop('selectedIndex', i);
                                }
                            });
                        }
                    }else if (returnValLength == 1) {
                        $('#divCodePrediksi').show();
                        $('#divComboPrediksi').hide();
                        $('#divInputPrediksi').show();
                        document.getElementById('prediksiId1').value = returnVal[1];
                        document.getElementById('prediksiText').value = returnVal[2];
                        document.getElementById('prediksiHeader').value = returnVal[3];
                        setPrediksiDetail(returnVal[1]);
                        

                    }

                }
            }
        });
    }

    function setInvoiceDP(generalVendorId, ppn1) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setInvoiceDP',
                generalVendorId: generalVendorId,
                ppn1: ppn1,

            },
            success: function (data) {
                if (data != '') {
                    $('#IDP').show();
                    document.getElementById('IDP').innerHTML = data;
                }
            }
        });
    }

    function setPrediksiDetail(prediksiId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPrediksiDetail',
                prediksiId: prediksiId,

            },
            success: function (data) {
                if (data != '') {
                    $('#divprediksidetail').show();
                    document.getElementById('prediksiDetail').innerHTML = data;
                }
            }
        });
    }

    function setMutasiDetail(mutasiId) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setMutasiDetail',
                mutasiId: mutasiId,
                //checkedSlips: checkedSlips,
                //checkedSlips2: checkedSlips2,
                //ppn1: ppn1,

                //invoiceMethod: invoiceMethod

            },
            success: function (data) {
                if (data != '') {
                    $('#mutasiDetail').show();
                    document.getElementById('mutasiDetail').innerHTML = data;
                }
            }
        });
    }

    function setMutasiHeader() {

        $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: {
                action: 'getMutasiHeader'
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
                        document.getElementById('mutasiId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('mutasiId').options.add(x);

                        $("#mutasiId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('mutasiId').options.add(x);
                    }


                }
                //setContract(contract);
            }

        });

    }
</script>

<input type="hidden" id="action" name="action" value="invoice_detail">
<input type="hidden" id="prediksiHeader" name="prediksiHeader">

<div class="row-fluid">

    <div class="span4 lightblue" id="transaksiMutasiLabel">
        <label>Transaksi Mutasi ? <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT '0' as id, 'No' as info UNION
                    SELECT '1' as id, 'Yes' as info;", $transaksiMutasi, "", "transaksiMutasi", "id", "info",
            "", 11, "select2combobox100");
        ?>
    </div>
    <div class="span4 lightblue" id="mutasiLabel" style="display: none;">
        <label>Kode Mutasi<span style="color: red;">*</span></label>
        <?php
        createCombo("", "", "", "mutasiId", "mutasi_header_id", "mutasi_header_id",
            "", 2, "select2combobox100");
        ?>
    </div>
    <div>

    </div>
</div>
<div id="invoiceDetail1" style="display: none;">
    <div class="row-fluid">

        <div class="span4 lightblue">
            <label>Type</label>
            <?php createCombo("", $invoiceType, "", "invoiceType", "id", "info", "", "", "select2combobox100", 1); ?>
            <input type="hidden" class="span12" tabindex="" id="invoiceMethodDetail" name="invoiceMethodDetail"
                   value="<?php echo $invoiceMethodDetail; ?>">
        </div>

        <div class="span4 lightblue" id = "divAccount" style="display: none;">
            <label>Account</label>
            <?php createCombo("", $accountId, "", "accountId", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id = "divShipmentPrediksi" style="display: none;">
            <label>Shipment Code</label>

            <?php createCombo("", $shipmentIdPrediksi, "", "shipmentIdPrediksi", "shipment_id", "shipment_no", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id="method" style="display: none;">
            <label>Method</label>
            <?php
            createCombo("SELECT '1' as id, 'IN' as info UNION
                    SELECT '2' as id, 'OUT' as info;", $invMethod, "", "invMethod", "id", "info",
                "", "", "select2combobox100", 1);
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Remark (Stockpile)</label>
            <?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", "", "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1); ?>
        </div>
        <div class="span4 lightblue">
            <label>PO No.</label>
            <?php createCombo("", $poId, "", "poId", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id = "divAccountPrediksi" style="display: none;">
            <label>Account</label>
            <?php createCombo("", $accountId, "", "accountIdPrediksi", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id = "divShipment" style="display: none;">
            <label>Shipment Code</label>

            <?php createCombo("", $shipmentId1, "", "shipmentId1", "shipment_id", "shipment_no", "", "", "select2combobox100", 4); ?>
        </div>
    </div>
</div>
<div class="row-fluid">

    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code",
            "", "", "select2combobox100");
        ?>
    </div>
    <div class="span4 lightblue" id="exchangeRate" style="display: none;">
        <label>Exchange Rate <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate"
               value="<?php echo $exchangeRate; ?>">
    </div>
    <div class="span4 lightblue" id="divtipebiaya" style="display: none;">
        <label>Tipe Biaya <span style="color: red;">*</span></label>
        <?php
            createCombo("", $tipeBiayaId, "", "tipeBiayaId", "costId", "tipe_biaya", "", "", "select2combobox100", 4);
        ?>
    </div>
</div>
<div id="invoiceDetail2" style="display: none;">
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Qty</label>
            <input type="text" class="span12" tabindex="" id="qty" name="qty">
        </div>
        <div class="span2 lightblue">
            <label>UOM <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT *
                    FROM uom 
                    ORDER BY uom_type ASC", $uom, "", "uom", "idUOM", "uom_type",
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Unit Price</label>
            <input type="text" class="span12" tabindex="" id="price" name="price">
        </div>
        <div class="span1 lightblue">
            <label>Termin (%)</label>
            <input type="text" class="span12" tabindex="" id="termin" name="termin" min="0" max="100">
        </div>
        <div class="span3 lightblue">
            <label>Amount</label>
            <input type="text" readonly class="span12" tabindex="" id="amount" name="amount">

        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Vendor <span style="color: red;">*</span></label>
            <div id="divVendor" style="display: none;">
                <?php
                
                createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                            FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, $readonlyProperty, "generalVendorId", "general_vendor_id", "general_vendor_name",
                    "", "", "select2combobox100", 1, "", true);
                ?>
            </div>
            <div id="divVendorPrediksi" style="display: none;">
                <?php createCombo("", $generalVendorId, "", "generalVendorId1", "general_vendor_id", "general_vendor_name", "", "", "select2combobox100", 4); ?>
            </div>
        </div>
        <div class="span4 lightblue" id="divCodePrediksi" style="display: none;">
            <label>Code Prediksi <span style="color: red;">*</span></label>
            <div id="divComboPrediksi" style="display: none;" >
                <?php
                    createCombo("", $prediksiId, "", "prediksiId", "prediction_detail_id", "generate_code_detai", "", "", "select2combobox100", 4); 
                ?>
            </div>
            <div id="divInputPrediksi" style="display: none;" >
                <input type="hidden" readonly class="span12" tabindex="" id="prediksiId1">
                <input type="text" readonly class="span12" tabindex="" id="prediksiText">
            </div>
        </div>
        <div class="span2 lightblue">
            <label>PPN</label>
            <input type="text" readonly class="span12" tabindex="" id="ppn1" name="ppn1"
                   value="<?php echo $_SESSION['invoiceDetail']['ppn1']; ?>">
            <input type="hidden" class="span12" id="ppnID" name="ppnID"
                   value="<?php echo $_SESSION['invoiceDetail']['ppnID']; ?>">
            <input type="checkbox" name="checkedPPN" id="checkedPPN" onclick="checkPPN()" checked="checked"/>
        </div>
        <div class="span2 lightblue">
            <label>PPh</label>
            <?php createCombo("", $pphTaxId, "", "pphTaxId", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>

    </div>

</div>

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span12" rows="1" tabindex="" id="notes" name="notes"></textarea>
    </div>

</div>
<div class="row-fluid" id="mutasiDetail" style="display: none;">

    Mutasi Detail
</div>
<div class="row-fluid" id = "divprediksidetail" style="display: none;">  
        <span>Data Prediksi</span>
        <div class="row-fluid" id="prediksiDetail">
            detail prediksi
        </div>
    </div>


<div class="row-fluid" id="IDP" style="display: none;">
    Invoice DP
</div>
<div class="row-fluid">

</div>


