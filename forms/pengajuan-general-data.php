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
$_method = '';
$pgdId = '';
$generalVendorId = 0;
$gvBankId = '';
$pgId = '';
$gvEmail = '';
$transaksiMutasi = $_POST['transaksiMutasi'];

if (isset($_POST['generalVendorId']) && $_POST['generalVendorId'] != '') {
    $generalVendorId = $_POST['generalVendorId'];
}
if (isset($_POST['gvBankId']) && $_POST['gvBankId'] != '') {
    $gvBankId = $_POST['gvBankId'];
}

if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
    $pgId = $_POST['pgId'];
}

if (isset($_POST['gvEmail']) && $_POST['gvEmail'] != '') {
    $gvEmail = $_POST['gvEmail'];
}

if (isset($_POST['pgdId']) && $_POST['pgdId'] != '') {

    $pgdId = $_POST['pgdId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    $sql = "SELECT i.*, 
            FROM invoice_detail i
            WHERE inv.invoice_id = {$pgdId}
            ORDER BY i.invoice_detail_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
    }

    // </editor-fold>

    $_method = "UPDATE";
} else {
    $_method = "INSERT";
    $generatedInvoiceNo = "";
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


    });

    <?php if($transaksiMutasi == 2 ) { ?>
    $('#mutasiLabel').show();
    $('#invoiceDetail1').hide();
    $('#invoiceDetail2').hide();
    <?php } else { ?>
    $('#mutasiLabel').hide();
    $('#mutasiDetail').hide();
    $('#invoiceDetail1').show();
    $('#invoiceDetail2').show();
    <?php } ?>

    $('#mutasiId').change(function () {
        //resetExchangeRate();
        $('#mutasiDetail').hide();
        //resetMutasiDetail();

        if (document.getElementById('mutasiId').value != '' && document.getElementById('mutasiId').value != 'INSERT') {
            setMutasiDetail($('select[id="mutasiId"]').val());
        }
    });

    //     $('#generalVendorId').change(function () {
    //         //resetExchangeRate();
    //         $('#IDP').hide();
    //         resetGeneralVendorTax();

    //         if (document.getElementById('generalVendorId').value != 'INSERT') {
    // //                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
    //             getGeneralVendorTax(<?php echo $generalVendorId ?>, $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
    //             setPPh(1, <?php echo $generalVendorId ?>, 0);
    //             if (document.getElementById('invoiceMethod').value == 1) {
    //                 setInvoiceDP(<?php echo $generalVendorId ?>, '', '', 'NONE');

    //                 document.getElementById('invoiceMethodDetail').value = '1';
    //             } else {
    //                 document.getElementById('invoiceMethodDetail').value = '2';
    //             }
    //         }
    //     });

    function resetGeneralVendorTax() {
        document.getElementById('ppn1').value = '';
        //document.getElementById('pph1').value = '';
        document.getElementById('ppnID').value = '';
        //document.getElementById('pphID').value = '';
    }

    function getGeneralVendorTax(generalVendorId, amount, tax_pph1) {

        if (amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getGeneralVendorTax',
                    generalVendorId: generalVendorId,
                    amount: amount,
					tax_pph1 : tax_pph1
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
						document.getElementById('amount1').value = returnVal[5];
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

    <?php  }   ?>

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
            // setInvoiceType(generatedInvoiceNo);
            // if (document.getElementById('invoiceMethod').value == 1) {
            //     $('#method').show();
            // }
        }

    });

    // function setInvoiceType(generatedInvoiceNo) {
    //     document.getElementById('invoiceType').options.length = 0;
    //     var x = document.createElement('option');
    //     x.value = '';
    //     x.text = '-- Please Select --';
    //     document.getElementById('invoiceType').options.add(x);

    //     var x = document.createElement('option');
    //     x.value = '4';
    //     x.text = 'Loading';
    //     document.getElementById('invoiceType').options.add(x);


    //     var x = document.createElement('option');
    //     x.value = '5';
    //     x.text = 'Umum';
    //     document.getElementById('invoiceType').options.add(x);

    //     var x = document.createElement('option');
    //     x.value = '6';
    //     x.text = 'HO';
    //     document.getElementById('invoiceType').options.add(x);

    // }

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

    // $('#stockpileId2').change(function () {
    //     if (document.getElementById('stockpileId2').value != '' && document.getElementById('accountId').value != '') {
    //         resetPoNo(' ');
    //         resetShipmentCode(' Shipment Code ');
    //         setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
    //         setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
    //     } else {
    //         resetPoNo(' PO NO ');
    //         resetShipmentCode(' Shipment Code ');
    //     }
    // });


</script>
<script type="text/javascript">
    $(document).ready(function () {
        //this calculates values automatically
        <?php if($transaksiMutasi == 1 ) { ?>

        $('#ppn1').number(true, 10);
        sum();
        $("#qty, #price, #ppn1, #terminDetail").on("keydown keyup", function () {
            sum();
            $('#amount').number(true, 10);
			 $('#amount1').number(true, 10);
			$('#tax_pph_value').val(0);
            checkPPN();
        });
        resetSetPPh(' ');
        setPPh(1, <?php echo $generalVendorId ?>);

        <?php }else{ ?>

        setMutasiHeader();

        <?php } ?>
        //getGeneralVendorTax(<?php //echo $generalVendorId ?>//, $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
        // resetGeneralVendorTax(' ');
		
		$('#pphTaxId').change(function() {
            if(document.getElementById('pphTaxId').value != '') {
                getTax_PPH($('select[id="pphTaxId"]').val(), $('input[id="amount"]').val());

            } else {
				$("#tax_pph_value").val(0); 
				getTax_PPH($('select[id="pphTaxId"]').val(), $('input[id="amount"]').val());
			}
        });

    });
	
	function getTax_PPH(pphTaxId, amount) {
       
        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getTax_PPH',
                        pphTaxId: pphTaxId,
						amount : amount
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('tax_pph_value').value = returnVal[1];   
						getGeneralVendorTax(<?php echo $generalVendorId ?>, amount, returnVal[1]);
						
						
                    }
                }
            });
        //}
	}


    function resetSetPPh(text) {
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
	
	

    function sum() {
        var num1 = document.getElementById('qty').value;
        var num2 = document.getElementById('price').value;
        var num3 = document.getElementById('ppn1').value;
        //var num4 = document.getElementById('pph1').value;
        var num5 = document.getElementById('terminDetail').value;

        var result = (parseFloat(num1) * parseFloat(num2)) * (parseFloat(num5) / 100);
        if (!isNaN(result)) {
            document.getElementById('amount').value = result;
            //document.getElementById('tamount').value = result1;
        }
    }

    function sum2() {
        var num1 = document.getElementById('qtyInvoiceValue').value;
        var num2 = document.getElementById('terminDetailInvoice').value;
        var result = parseFloat(num1) * (parseFloat(num2) / 100);

        if (!isNaN(result)) {
            document.getElementById('qtyInvoice').value = result;
        }
    }

    function checkPPN() {
        var checkedPPN = document.getElementById('checkedPPN');
        // var generalVendorId = document.getElementById('generalVendorId').value;
        var amount = document.getElementById('amount').value.replace(new RegExp(",", "g"), "");
		var ppn1 = document.getElementById('ppn1').value.replace(new RegExp(",", "g"), "");
		var tax_pph1 = document.getElementById('tax_pph_value').value;
        if (checkedPPN.checked != true) {
            var a = document.getElementById('ppn1').value = 0;
			var total = (amount + a) - tax_pph1;
			document.getElementById('amount1').value = total;
			
        } else {
            getGeneralVendorTax(<?php echo $generalVendorId ?>, amount, tax_pph1);
        }

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


<input type="hidden" id="action" name="action" value="pengajuan_general_detail">
<input type="hidden" id="generalVendorId" name="generalVendorId" value="<?php echo $generalVendorId ?>">
<input type="hidden" id="gvBankId" name="gvBankId" value="<?php echo $gvBankId ?>">
<input type="hidden" id="gvEmail" name="gvEmail" value="<?php echo $gvEmail ?>">
<input type="hidden"  name="transaksiMutasi" value="<?php echo $transaksiMutasi?>">
<input type="hidden"  name="pgId" value="<?php echo $pgId ?>">

<div class="row-fluid">
    <div class="span4 lightblue" id="mutasiLabel" style="display: none;">
        <label>Kode Mutasi<span style="color: red;">*</span></label>
        <?php
        createCombo("", "", "", "mutasiId", "mutasi_header_id", "mutasi_header_id",
            "", 2, "select2combobox100");
        ?>
    </div>
</div>

<div id="invoiceDetail1" style="display: none;">
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Remark (Stockpile)</label>
            <?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", "", "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1); ?>
        </div>
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
                <input type="text" class="span12" tabindex="" id="qty" name="qty">
            </div>
            <div class="span3 lightblue">
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
            <div class="span2 lightblue">
                <label>Termin (%)</label>
                <input type="text" class="span12" tabindex="" id="terminDetail" name="terminDetail" min="0" max="100">
            </div>

        </div>

        <div class="row-fluid">

            <div class="span3 lightblue">
                <label>Amount</label>
				<input type="text" readonly class="span12" tabindex="" id="amount1" name="amount1">
                <input type="hidden" readonly class="span12" tabindex="" id="amount" name="amount">
				
            </div>

            <div class="span3 lightblue">
                <label>PPN</label>
                <input type="text" readonly class="span12" tabindex="" id="ppn1" name="ppn1"
                       value="">
                <input type="hidden" class="span12" id="ppnID" name="ppnID"
                       value="">
                <input type="checkbox" name="checkedPPN" id="checkedPPN" onclick="checkPPN()" checked="checked"/>
            </div>

            <div class="span3 lightblue">
                <label>PPh</label>
                <?php createCombo("", $pphTaxId, "", "pphTaxId", "id", "info", "", "", "select2combobox100", 4); ?>
				<input type="hidden" class="span12" id="tax_pph_value" name="tax_pph_value" value="">
            </div>

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
<div class="row-fluid" id="IDP" style="display: none;">
    Invoice DP
</div>
<div class="row-fluid">

</div>


