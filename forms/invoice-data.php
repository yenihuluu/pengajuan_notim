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
//$transaksiMutasi = '';
$invoiceId = '';
if (isset($_POST['invoiceId']) && $_POST['invoiceId'] != '') {

    $invoiceId = $_POST['invoiceId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT i.* 
            FROM invoice_detail i
            WHERE i.invoice_detail_id = {$invoiceId}
            ORDER BY i.invoice_detail_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();


        $transaksiMutasi = 0;
        $invoiceType = $rowData->type;
        $accountId = $rowData->account_id;
        $stockpileId2 = $rowData->stockpile_remark;
        $shipmentId1 = $rowData->shipment_id;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $qty = $rowData->qty;
        $price = $rowData->price;
        $termin = $rowData->termin;
        $uom = $rowData->idUOM;
        $amount = $rowData->amount_converted;
        $amountDPP = $rowData->amount;
        $generalVendorId = $rowData->general_vendor_id;
        $ppn1 = $rowData->ppn_converted;
        $ppnID = $rowData->ppnID;
        $pphTaxId = $rowData->pphID;
        $notes = $rowData->notes;
        $invoice_method_detail = $rowData->invoice_method_detail;
        $poId = $rowData->poId;
        //$invMethod = $rowData->invoice_method_detail;
       

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

        if(document.getElementById('transaksiMutasi').value == ''){
            $('#mutasiLabel').hide();
            $('#mutasiDetail').hide();
            $('#invoiceDetail1').hide();
            $('#invoiceDetail2').hide();
        }else if(document.getElementById('transaksiMutasi').value == 0) {
            $('#mutasiLabel').hide();
            $('#mutasiDetail').hide();
            $('#invoiceDetail1').show();
            $('#invoiceDetail2').show();

        }

    });

   
             

    $('#transaksiMutasi').change(function () {

        if (document.getElementById('transaksiMutasi').value == 1) {
            $('#mutasiLabel').show();

            $('#invoiceDetail1').hide();
            $('#invoiceDetail2').hide();
            //$('#invoiceDetail3').hide();
            setMutasiHeader();
        } else {
            $('#mutasiLabel').hide();
            $('#mutasiDetail').hide();
            $('#invoiceDetail1').show();
            $('#invoiceDetail2').show();
            //$('#invoiceDetail').show();

        }
    });

    

    
</script>
<script type="text/javascript">
    
    /*	$('#pph1').change(function() {
                 $('#IDP').hide();
                resetGeneralVendorTax();

                if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
    //                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                    getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));

                if(document.getElementById('invoiceMethod').value == 2) {
                            //refreshSummaryFreight($('select[id="freightId"]').val());
                   } else if(document.getElementById('invoiceMethod').value == 1) {
                            setInvoiceDP($('select[id="generalVendorId"]').val(), '', 'NONE', 'NONE', $('select[id="invoiceMethod"]').val());
                        }
                }
            });*/
    <?php
    if($invoiceId != '') {
                   
    ?>
        <?php 
        if($invoiceType != '') {
            ?>
        resetAccount(' ');
        
        <?php
        if($accountId != '') {
        ?>
        
        setAccount(1, <?php echo $invoiceType; ?>, <?php echo $accountId; ?>);
        <?php
        } else {
        ?>
        setAccount(0, $('select[id="invoiceType"]').val(), 0);
        <?php
        }
        ?>
        <?php 
        }
        ?>

        <?php 
        if($stockpileId2 != '') {
            ?>
        setPoNo(1, <?php echo $accountId; ?>, <?php echo $stockpileId2; ?>, <?php echo $poId; ?>);
        setShipmentCode(1, <?php echo $stockpileId2; ?>, <?php echo $shipmentId1; ?>);
        <?php
        } else {
        ?>
        setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
        setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        <?php
        }
        ?>

        <?php 
        if($generalVendorId != '') {
            ?>
        resetGeneralVendorTax(' ');
        resetSetPPh(' ');
        //resetInvoiceDP(' ');

        getGeneralVendorTax(<?php echo $generalVendorId; ?>, <?php echo $amountDPP; ?>, <?php echo $ppn1; ?>);
        setPPh(1, <?php echo $generalVendorId; ?>, <?php echo $pphTaxId; ?>);

        <?php 
        if($invoice_method_detail == 1) {
            ?>
        setInvoiceDP(<?php echo $generalVendorId; ?>, 'NONE',<?php echo $invoiceId; ?> );

        <?php
        }else{
        ?>
            //setInvoiceDP(0, $('select[id="generalVendorId"]').val(), 'NONE');

            <?php
        }
        ?>

        <?php
        } else {
        ?>
            //getGeneralVendorTax(0, $('select[id="generalVendorId"]').val());
            //setPPh(0, $('select[id="generalVendorId"]').val());
        <?php
        }
        ?>

        <?php    
        
    }
    ?>

    

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

    function resetGeneralVendorTax() {
        document.getElementById('ppn1').value = '';
        //document.getElementById('pph1').value = '';
        document.getElementById('ppnID').value = '';
        //document.getElementById('pphID').value = '';
    }

    function getGeneralVendorTax(generalVendorId,amount,ppn1) {

        if (amount != '' && ppn1 != 0) {
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
        } else if (ppn1 == 0) {
            document.getElementById('ppn1').value = '0';  
            document.getElementById('ppnID').value = '0';
            document.getElementById('checkedPPN').checked = false;
            
        }else {
            document.getElementById('ppn1').value = '0';
            //document.getElementById('pph1').value = '0';
            document.getElementById('ppnID').value = '0';
            //document.getElementById('pphID').value = '0';
        }
    } 
    
    function setInvoiceType(generatedInvoiceNo) {

        //
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
        if($invoiceType != '') {
        ?>
            
                $('#invoiceType').find('option').each(function(i,e){
                            if($(e).val() == <?php echo $invoiceType; ?>){
                                $('#invoiceType').prop('selectedIndex',i);
								
								  $("#invoiceType").select2({
                                    width: "100%",
                                    placeholder: invoiceType
                                });
                            }
                        });

        <?php  } ?>
    }

    function resetSetPPh(text) {
        document.getElementById('pphTaxId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('pphTaxId').options.add(x);
    }

    function setPPh(type, generalVendorId,pphTaxId) {
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
                        $('#pphTaxId').find('option').each(function(i,e){
                            if($(e).val() == pphTaxId){
                                $('#pphTaxId').prop('selectedIndex',i);
								
								  $("#pphTaxId").select2({
                                    width: "100%",
                                    placeholder: pphTaxId
                                });
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

    function setAccount(type, invoiceType, accountId) {
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

                        $('#accountId').find('option').each(function(i,e){
                            if($(e).val() == accountId){
                                $('#accountId').prop('selectedIndex',i);
								
								  $("#accountId").select2({
                                    width: "100%",
                                    placeholder: accountId
                                });
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

    function setPoNo(type, accountId, stockpileId2, poId) {
       
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
                        $('#poId').find('option').each(function(i,e){
                            if($(e).val() == poId){
                                $('#poId').prop('selectedIndex',i);
								
								  $("#poId").select2({
                                    width: "100%",
                                    placeholder: poId
                                });
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

    function setShipmentCode(type, stockpileId2, shipmentId1) {
        //alert (type)
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
                        $('#shipmentId1').find('option').each(function(i,e){
                            if($(e).val() == shipmentId1){
                                $('#shipmentId1').prop('selectedIndex',i);
								
								  $("#shipmentId1").select2({
                                    width: "100%",
                                    placeholder: shipmentId1
                                });
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
            resetAccount(' ');
            setAccount(0, $('select[id="invoiceType"]').val(), 0);
        } else {
            resetAccount(' Invoice Type ');
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
        $('#IDP').hide();
        resetGeneralVendorTax();

        if (document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
            getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
            setPPh(1, $('select[id="generalVendorId"]').val(), 0);
            if (document.getElementById('invoiceMethod').value == 1) {
                setInvoiceDP($('select[id="generalVendorId"]').val(), '', '', 'NONE',0);

                document.getElementById('invoiceMethodDetail').value = '1';
            } else {
                document.getElementById('invoiceMethodDetail').value = '2';
            }
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

            $('#amount').number(true, 2);

        });
    });

    function sum() {
        var num1 = document.getElementById('qty').value;
        var num2 = document.getElementById('price').value;
        var num3 = document.getElementById('ppn1').value;
        //var num4 = document.getElementById('pph1').value;
        var num5 = document.getElementById('termin').value;
        var result = ((parseFloat(num5) / 100) * (parseFloat(num1) * parseFloat(num2)));
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

    function setInvoiceDP(generalVendorId, ppn1, invoiceId) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setInvoiceDP',
                generalVendorId: generalVendorId,
                //checkedSlips: checkedSlips,
                //checkedSlips2: checkedSlips2,
                ppn1: ppn1,
                invoiceId: invoiceId

            },
            success: function (data) {
                if (data != '') {
                    $('#IDP').show();
                    document.getElementById('IDP').innerHTML = data;
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
<input type="hidden" name="invoiceDetailId" id="invoiceDetailId" value="<?php echo $invoiceId; ?>"/>

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
                   value="<?php echo $invoice_method_detail; ?>">
        </div>

        <div class="span4 lightblue">
            <label>Account</label>
            <?php createCombo("", $accountId, "", "accountId", "id", "info", "", "", "select2combobox100", 4); ?>
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
            <?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId2, "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1); ?>
        </div>
        <div class="span4 lightblue">
            <label>PO No.</label>
            <?php createCombo("", $poId, "", "poId", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue">
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
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate"
               value="<?php echo $exchangeRate; ?>">
    </div>
    <div>

    </div>
</div>
<div id="invoiceDetail2" style="display: none;">
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Qty</label>
            <input type="text" class="span12" tabindex="" id="qty" name="qty" value="<?php echo $qty; ?>">
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
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Termin (%)</label>
            <input type="text" class="span12" tabindex="" id="termin" name="termin" min="0" max="100" value="<?php echo $termin; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Amount</label>
            <input type="text" readonly class="span12" tabindex="" id="amount" name="amount" value="<?php echo $amount; ?>">

        </div>
    </div>
    <div class="row-fluid">
        <div class="span6 lightblue">
            <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, $readonlyProperty, "generalVendorId", "general_vendor_id", "general_vendor_name",
                "", "", "select2combobox100", 1, "", true);
            ?>
        </div>
        <div class="span3 lightblue">
            <label>PPN</label>
            <input type="text" readonly class="span12" tabindex="" id="ppn1" name="ppn1"
                   value="<?php echo $ppn1; ?>">
            <input type="hidden" class="span12" id="ppnID" name="ppnID" value="<?php echo $ppnID; ?>">
            <input type="checkbox" name="checkedPPN" id="checkedPPN" onclick="checkPPN()" checked="checked"/>
        </div>
        <div class="span3 lightblue">
            <label>PPh</label>
            <?php createCombo("", $pphTaxId, "", "pphTaxId", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>

    </div>
</div>
<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span12" rows="1" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
    </div>

</div>
<div class="row-fluid" id="mutasiDetail" style="display: none;">

    Mutasi Detail
</div>
<div class="row-fluid" id="IDP" style="display: none;">
    Invoice DP
</div>
<div class="row-fluid">

</div>


