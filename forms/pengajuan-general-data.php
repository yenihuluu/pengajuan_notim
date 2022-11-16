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
$gvEmail = '';
$pgId = '';
$transaksiMutasi = $_POST['transaksiMutasi'];

if (isset($_POST['generalVendorId']) && $_POST['generalVendorId'] != '') {
    $generalVendorId = $_POST['generalVendorId'];
}
if (isset($_POST['gvEmail']) && $_POST['gvEmail'] != '') {
    $gvEmail = $_POST['gvEmail'];
}
if (isset($_POST['gvBankId']) && $_POST['gvBankId'] != '') {
    $gvBankId = $_POST['gvBankId'];
}

if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
    $pgId = $_POST['pgId'];
}

if (isset($_POST['oksAktId']) && $_POST['oksAktId'] != '') {
    $oksAKT = $_POST['oksAktId'];
}

if (isset($_POST['transaksiPO']) && $_POST['transaksiPO'] != '') {
    $transaksiPO = $_POST['transaksiPO'];
}

if (isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
}

if (isset($_POST['pgdId']) && $_POST['pgdId'] != '') {

    $pgdId = $_POST['pgdId'];
    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    $sql = "SELECT * FROM pengajuan_general_detail WHERE pgd_id = {$pgdId} ORDER BY pgd_id ASC";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

 //  echo $sql;

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $stockpileId2 = $rowData->stockpile_remark;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $qty = $rowData->qty;
        $uom = $rowData->uom_id;
        $price = $rowData->price;
        $termin = $rowData->termin;
        $amount1 = ($rowData->amount + $rowData->ppn)-$rowData->pph;
        $amount = $rowData->amount;
        $pphId = $rowData->pphID;
        $pphVal = $rowData->pph;
        $pgdId = $rowData->pgd_id;
        $remarks = $rowData->notes;
        $contractId = $rowData->contract_id;
        $generalVendorId = $rowData->general_vendor_id;
        
        // $pgId = $rowData->pg_id;
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

        $('#qty').number(true, 10);
        $('#price').number(true, 10);
        $('#terminDetail').number(true, 0);
        $('#amount1').number(true, 10);
        $('#msgError2').hide();

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

    <?php if($oksAKT == 2 && $transaksiPO == 2) { ?>
        $('#poLabel').show();
        // document.getElementById('terminDetail').value = 100;
        // $("#terminDetail").prop("readonly",true);
        <?php if($pgdId != ''){ ?>
          setPoNo(1,  <?php echo $generalVendorId ?>, <?php echo $contractId ?>);
        <?php }else{ ?>
            setPoNo(1,  $('input[id="generalVendorId"]').val(), 0);
        <?php } ?>

    <?php }else if($oksAKT == 1){ ?>
        $('#poLabel').hide();
        $("#terminDetail").prop("readonly",false);
        document.getElementById('terminDetail').value = 0;
    <?php } ?>

    $('#mutasiId').change(function () {
        //resetExchangeRate();
        $('#mutasiDetail').hide();
        //resetMutasiDetail();

        if (document.getElementById('mutasiId').value != '' && document.getElementById('mutasiId').value != 'INSERT') {
            setMutasiDetail($('select[id="mutasiId"]').val());
        }
    });

    $('#poId').change(function () {
        resetQtyPo(' ');
        if (document.getElementById('poId').value != '') {
            getQtyPo($('select[id="poId"]').val(), $('input[id="generalVendorId"]').val() );
        } 
    });

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

    function setPoNo(type, generalVendorId, poId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPO_OKS_AKT',
                gvId: generalVendorId
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

    function resetQtyPo(text) {
        document.getElementById('tempQtyPo').value = 0;   
        document.getElementById('qty').value = 0;  
        document.getElementById('typeOKS').value = 0; 
        document.getElementById('price').value = 0; 
        // $("#qty").attr("disabled", false);
        // $("#price").attr("disabled", false);
    }

    function getQtyPo(poId, gvId) {
       //if(amount != '') {
           $.ajax({
               url: 'get_data.php',
               method: 'POST',
               data: { action: 'getQtyPo',
                    poId: poId,
                    gvId: gvId
               },
               success: function(data){
                   var returnVal = data.split('|');
                   if(parseInt(returnVal[0])!=0)	//if no errors
                   {
                       document.getElementById('tempQtyPo').value = returnVal[1];   
                       document.getElementById('qty').value = returnVal[1];  
                       document.getElementById('typeOKS').value = returnVal[2]; 
                       document.getElementById('price').value = returnVal[3]; 
                       document.getElementById('oksAKT_id').value = returnVal[4];
                       document.getElementById('pks_id').value = returnVal[5];

                       
                       if(returnVal[2] == 1){
                            // $("#qty").attr("disabled", true);
                            //  $("#price").attr("disabled", true);
                            $("#qty").prop("readonly",true);
                            $("#price").prop("readonly",true);
                             $('#msgError2').hide();

                             $('#uom').find('option').each(function(i,e){
                                if($(e).val() == 2){
                                    $('#uom').prop('selectedIndex',i);        
                                        $("#uom").select2({
                                            width: "100%",
                                            placeholder: 2
                                        });
                                }
                            });
                            $('#uom option:not(:selected)').attr('disabled', true);

                       }else{
                        $("#qty").prop("readonly",false);
                            $("#price").prop("readonly",false);
                             $('#msgError2').hide();
                       }
                    //    else if(returnVal[2] == 0){
                    //         $("#qty").attr("disabled", true);
                    //          $("#price").attr("disabled", true);
                    //         $('#errorText').show();
                    //         document.getElementById('errorText').innerHTML = 'OKS AKT sudah kedaluwarsa'; 
                    //    }
                      
                   }
               }
           });
       //}
   }
</script>


<script type="text/javascript">
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
        }

    });

    function resetPoNo(text) {
        document.getElementById('poId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('poId').options.add(x);
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
</script>

<script type="text/javascript">
    $(document).ready(function () {
        //this calculates values automatically
        <?php if($transaksiMutasi == 1 ) { ?>
            $('#ppn1').number(true, 10);
            sum();
            <?php if($pgdId != ''){ ?>
                checkPPN();
           <?php } ?>
     
        $("#qty, #price, #ppn1, #terminDetail").on("keydown keyup", function () {
            sum();
            $('#amount').number(true, 10);
			 $('#amount1').number(true, 10);
			$('#tax_pph_value').val(0);
            checkPPN();
            // getTax_PPH($('select[id="pphTaxId"]').val(), $('input[id="amount"]').val());
        });
        resetSetPPh(' ');
        setPPh(1, <?php echo $generalVendorId ?>, <?php echo $pphId ?>);

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

        <?php if($oksAKT == 2 ) { ?>
            $('#qty').change(function() {
                QtyValidation($('input[id="qty"]').val(), $('select[id="poId"]').val());
            });

            $('#price').change(function() {
                searchPrice($('input[id="price"]').val(), $('input[id="generalVendorId"]').val(), $('select[id="poId"]').val());
            });
        <?php } ?>

    });

    function searchPrice(tempPrice, gvId, contractId) {
       //if(amount != '') {
           $.ajax({
               url: 'get_data.php',
               method: 'POST',
               data: { action: 'searchPrice',
                    tempPrice: tempPrice,
                    gvId: gvId,
                    contractId: contractId
               },
               success: function(data){
                   var returnVal = data.split('|');
                   if(parseInt(returnVal[0])!=0)	//if no errors
                   {
                        if(returnVal[1] == 1){
                            $('#msgError2').show();
                            document.getElementById('msgError2').innerHTML = 'Fee belum terdaftar'; 
                            document.getElementById('price').value = 0;  
                        }else{
                            $('#msgError2').hide();   
                            document.getElementById('oksAKT_id').value = returnVal[2];
                            document.getElementById('pks_id').value = returnVal[3];

                        }            
                   }
               }
           });
       //}
   }
		
	function QtyValidation(quantity, contractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'QtyValidation',
                    quantity: quantity,
                    contractId: contractId
                },
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                { 
                    if(returnVal[1] == 1){
                        $('#msgError').show();
                        document.getElementById('msgError').innerHTML = 'Qty harus kurang dari Qty PO-PKS' + ' | ' + returnVal[2];							
                        document.getElementById('qty').value = 0;  
                        $('#uom').find('option').each(function(i,e){
                            if($(e).val() == 0){
                                $('#uom').prop('selectedIndex',i);        
                                    $("#uom").select2({
                                        width: "100%",
                                        placeholder: 0
                                    });
                            }
                        }); 
                        $('#uom option:not(:selected)').attr('disabled', false);
                    }else{
                        $('#msgError').hide();  
                        $('#uom').find('option').each(function(i,e){
                            if($(e).val() == 2){
                                $('#uom').prop('selectedIndex',i);        
                                    $("#uom").select2({
                                        width: "100%",
                                        placeholder: 2
                                    });
                            }
                        }); 
                        $('#uom option:not(:selected)').attr('disabled', true);
                    }
                }
            }
        });
	}

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

    function setPPh(type, generalVendorId, pphTaxId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGeneralVendorPPh',
                generalVendorId: generalVendorId,
                pphTaxId: pphTaxId

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

                    if(type == 1) {
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
	
	

    function sum() {
        var num1 = document.getElementById('qty').value.replace(new RegExp(",", "g"), "");
        var num2 = document.getElementById('price').value.replace(new RegExp(",", "g"), "");
        var num3 = document.getElementById('ppn1').value;
        //var num4 = document.getElementById('pph1').value;
        var num5 = document.getElementById('terminDetail').value.replace(new RegExp(",", "g"), "");
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
		var tax_pph1 = document.getElementById('tax_pph_value').value.replace(new RegExp(",", "g"), "");
      //  alert(tax_pph1);
        if (checkedPPN.checked != true) {
            var a = document.getElementById('ppn1').value = 0;
			var total = amount  - tax_pph1;
			document.getElementById('amount1').value = total;
			
        } else {
            getGeneralVendorTax(<?php echo $generalVendorId ?>, amount, tax_pph1);
        }
        if(checkedPPN.checked == true){
            document.getElementById('checkValue').value = 1;
        }else{
            document.getElementById('checkValue').value = 0;
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
            }
        });
    }
</script>

<input type="hidden" id="action" name="action" value="pengajuan_general_detail">
<input type="hidden" id="_method" name="_method" value="<?php echo $_method ?>">
<input type="hidden" id="generalVendorId" name="generalVendorId" value="<?php echo $generalVendorId ?>">
<input type="hidden" id="gvEmail" name="gvEmail" value="<?php echo $gvEmail ?>">
<input type="hidden" id="gvBankId" name="gvBankId" value="<?php echo $gvBankId ?>"> 
<input type="hidden"  name="transaksiMutasi" value="<?php echo $transaksiMutasi?>">
<input type="hidden"   id="pgId" name="pgId" value="<?php echo $pgId ?>">
<input type="hidden"   id="pgdId" name="pgdId" value="<?php echo $pgdId ?>">
<input type="hidden"   id="tempQtyPo" name="tempQtyPo">
<input type="hidden"   id="typeOKS" name="typeOKS">
<input type="hidden"   id="oksAKT" name="oksAKT" value="<?php echo $oksAKT ?>">
<input type="hidden"   id="oksAKT_id" name="oksAKT_id">
<input type="hidden"   id="pks_id" name="pks_id">
<input type="hidden"   id="stockpileId" name="stockpileId" value="<?php echo $stockpileId ?>">
<input type="hidden"  id="checkValue" name="checkValue">

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
        <div class="span3 lightblue">
            <label>Remark (Stockpile)</label>
            <?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", "$stockpileId2", "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1); ?>
        </div>

        <div class="span3 lightblue" id="poLabel" style="display: none;"> 
            <label>PO PKS</label>
            <?php createCombo("", $poId, "", "poId", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>

        <div class="span3 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code",
                "", "", "select2combobox100");
            ?>
        </div>

        <div class="span3 lightblue" id="exchangeRate" style="display: none;">
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
                <input type="text" class="span12" tabindex="" id="qty" name="qty" value=<?php echo $qty?>>
                <span class="help-block" id="msgError" style="display: none; color: red; font-size: 11px"></span>
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
                <input type="text" class="span12" tabindex="" id="price" name="price" value=<?php echo $price ?>>
                <span class="help-block" id="msgError2" style="display: none; color: red; font-size: 11px"></span>
            </div>

            <div class="span2 lightblue">
                <label>Termin (%)</label>
                <input type="text" class="span12" tabindex="" id="terminDetail" name="terminDetail" min="0" max="100" value=<?php echo $termin ?>>
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
                <?php createCombo("", "", "", "pphTaxId", "id", "info", "", "", "select2combobox100", 4); ?>
				<input type="hidden" class="span12" id="tax_pph_value" name="tax_pph_value" value="<?php echo $pphVal ?>">
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span12" rows="1" tabindex="" id="notes" name="notes"><?php echo $remarks ?></textarea>
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


