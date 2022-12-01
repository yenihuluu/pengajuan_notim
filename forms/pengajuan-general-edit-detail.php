<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$typeOKS = '';

$pgdId = $_POST['pgdId'];
$invoiceMethod = $_POST['method'];
$typeOKS = $_POST['typeOKS'];

// echo "INV Method" .$invoiceMethod;

$type = '';
$accountId = '';
$spRemark = '';
$generalVendorId = '';
$notes = '';
$qty = '';
$price = '';
$termin = '';
$amount = '';
$shipmentId = '';
$poId = '';
$joinProperty = '';

// <editor-fold defaultstate="collapsed" desc="Functions">


$sql = "SELECT id.*, gv.general_vendor_name, pd.idpo_detail  FROM pengajuan_general_detail id 
        LEFT JOIN general_vendor gv ON gv.general_vendor_id = id.general_vendor_id 
        LEFT JOIN invoice_dp idp ON idp.pengajuan_detail_id = id.pgd_id
        LEFT JOIN po_detail pd ON pd.idpo_detail = idp.po_detail_id_dp
        WHERE pgd_id = {$pgdId} ORDER BY pgd_id ASC LIMIT 1";
// echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if ($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    $type = $row->type;
    $accountId = $row->account_id;
    $spRemark = $row->stockpile_remark;
    $generalVendorId = $row->general_vendor_id;
    $notes = $row->notes;
    $pphTaxId = $row->pphID;
    $ppnTaxId = $row->ppnID;
    $qty = $row->qty;
    $price = $row->price;
    $termin = $row->termin;
    $amount = $row->amount;
    $shipmentId = $row->shipment_id;
    $poId = $row->poId;
    $podID = $row->idpo_detail;
    $invoiceType = $row->type;
    $tamount = $row->tamount;
    $tamountConverted = $row->tamount_pengajuan;
    $notes = $row->notes;
    $pphValue = $row->pph;
    $ppnValue = $row->ppn;
    $ppnConverted = $row->ppn_converted;
    $pphConverted = $row->pph_converted;
    $excRate = $row->exchange_rate;
    $currencyId = $row->currency_id;
    $uomId = $row->uom_id;
    $gvId = $row->general_vendor_id;
    $vendorName = $row->general_vendor_name;
    $tipeBiayaId = $row->biaya_id;
    $prediksiDetailId = $row->prediction_detail_id;
    $gvEmail = $row->vendor_email;
    $contractId = $row->contract_id;
  //  $invoiceMethod = $row->invoice_method_detail;
    // echo "ALA - " .$accountId . " - " . $tipeBiayaId;
    if($typeOKS == 2){
        $accountId = 539;
        $invoiceType = 1;
       
    }else if($typeOKS == 3){
        $accountId = 540;
        $invoiceType = 1;
    }
}

if ($invoiceType == 7) {
    $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
}
// </editor-fold>

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    if ($sql != '') {
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    } else {
        $result = false;
    }
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

?>
<script type="application/javascript">
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
        $('#divAccount').show();
        $('#divShipment').show();

        <?php if(isset($type) && $type != '') { ?>
            // document.getElementById("divAccount").classList.remove("hidden");
        //setAccount(1, <?php //echo $type ?>//, <?php //echo $accountId ?>//);
        <?php } ?>
        <?php if($spRemark != '' && $accountId !='' && $typeOKS == 1){ ?>
            resetPoNo(' ');
            setPoNo(0, <?php echo $accountId ?>, <?php echo $spRemark ?>, 0);
        <?php } else if($typeOKS == 2){ ?>
          // var contractId = document.getElementById('contractId').value;
         //SET LOADING
            $('#invoiceType').find('option').each(function(i,e){
                if($(e).val() == 4){
                    $('#invoiceType').prop('selectedIndex',i);
                                
                        $("#invoiceType").select2({
                            width: "100%",
                            placeholder: 4
                        });
                }
            });

            //SET ACCOUNT
           // setAccount(1, $('select[id="invoiceType"]').val(), <?php echo $accountId ?>);
            setPoNo(1, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), <?php echo $contractId ?>);

            //set PO No otomatis
           
        <?php }else if($typeOKS == 3){ ?>
            $('#accountId').find('option').each(function(i,e){
                if($(e).val() == <?php echo $accountId ?>){
                    $('#accountId').prop('selectedIndex',i);
                                
                        $("#accountId").select2({
                            width: "100%",
                            placeholder: <?php echo $accountId ?>
                        });
                }
            });       
        <?php } ?>

        //kalau job costing sudah dipake ini diaktifkan
        // <?php if($type == 4 && $prediksiDetailId > 0 && $typeOKS == 1){?>
        //         resetShipmentCodePrediksi(' Shipment Code ');
        //         $('#divAccount').hide();
        //         $('#divShipment').hide();
        //         $('#divShipmentPrediksi').show();
        //         $('#divAccountPrediksi').show();
        //         $('#divtipebiaya').show();
        //         setShipmentCodePrediksi(1, <?php echo $type ?>, <?php echo $shipmentId ?>, <?php echo $prediksiDetailId ?>);
        //         setAccountPrediksi(1, <?php echo $shipmentId ?>, <?php echo $accountId ?>, <?php echo $prediksiDetailId ?>);
        //         setTipeBiayaPrediksi(1, <?php echo $shipmentId ?>, <?php echo $accountId ?>, <?php echo $tipeBiayaId ?>, <?php echo $prediksiDetailId ?>);
        //         getPrediksiId(1, <?php echo $shipmentId ?>,<?php echo $tipeBiayaId ?>, <?php echo $gvId ?>, <?php echo $prediksiDetailId ?>);
        //         setPrediksiDetail(<?php echo $prediksiDetailId ?>, 1);
        // <?php } elseif($type == 4 && $prediksiDetailId == 0 && $typeOKS == 1) {?>
        //         $('#divAccount').hide();
        //         $('#divShipment').hide();
        //         $('#divShipmentPrediksi').show();
        //         $('#divAccountPrediksi').show();
        //         $('#divtipebiaya').show();
        //         setShipmentCodePrediksi(1, <?php echo $type ?>, <?php echo $shipmentId ?>, 0);
        //         setAccountPrediksi(1, <?php echo $shipmentId ?>, <?php echo $accountId ?>, 0);
        //         setTipeBiayaPrediksi(1, <?php echo $shipmentId ?>, <?php echo $accountId ?>, 0, 0);
        // <?php } else {?>
        //         // $('#divAccount').show();
        //         // $('#divShipment').show();
        //         $('#divShipmentPrediksi').hide();
        //         $('#divAccountPrediksi').hide();
        //         $('#divtipebiaya').hide();
        // <?php } ?>

        <?php if($invoiceMethod == 1){ ?>
            setInvoiceDP(<?php echo  $podID ?>, <?php echo  $generalVendorId ?>, '', '', 'NONE');
        <?php } ?>

    });

    $('#invoiceType').change(function () {
        if (document.getElementById('invoiceType').value != '') {
            $('#divprediksidetail').hide();
           /* if (this.value == 4 && document.getElementById('typeOKS').value == 1) {
                resetAccountPrediksi(' ');
			   resetTipeBiaya(' ');
                $('#divAccount').hide();
                $('#divShipment').hide();
                $('#divShipmentPrediksi').show();
                $('#divAccountPrediksi').show();
                // $('#divVendor').hide();
                // $('#divVendorPrediksi').show();
                $('#divtipebiaya').show();
                resetShipmentCodePrediksi(' Shipment Code ');
                setShipmentCodePrediksi(0, $('select[id="invoiceType"]').val(), 0, 0);
            }else{
                $('#divAccount').show();
                $('#divShipment').show();
                $('#divShipmentPrediksi').hide();
                $('#divAccountPrediksi').hide();
                resetTipeBiayaPrediksi(' ');
                resetAccountPrediksi(' ');
                resetShipmentCodePrediksi(' Shipment Code ');
                // $('#divVendor').show();
                // $('#divVendorPrediksi').hide();
                $('#divtipebiaya').hide();
                document.getElementById("divAccount").classList.remove("hidden");
                resetAccount(' ');
                setAccount(0, $('select[id="invoiceType"]').val(), 0);
            }   */
            // resetAccount(' ');
            // setAccount(0, $('select[id="invoiceType"]').val(), 0);
            $('#divAccount').show();
                $('#divShipment').show();
                $('#divShipmentPrediksi').hide();
                $('#divAccountPrediksi').hide();
                resetTipeBiayaPrediksi(' ');
                resetAccountPrediksi(' ');
                resetShipmentCodePrediksi(' Shipment Code ');
                // $('#divVendor').show();
                // $('#divVendorPrediksi').hide();
                $('#divtipebiaya').hide();
                document.getElementById("divAccount").classList.remove("hidden");
                resetAccount(' ');
                setAccount(0, $('select[id="invoiceType"]').val(), 0);
        } else {
            document.getElementById("divAccount").classList.add("hidden");
            resetAccount(' Invoice Type ');
        }
    });

     
   $('#tipeBiayaId').change(function () {
           $('#divprediksidetail').hide(); 
           $('#divCodePrediksi').hide();
           resetPrediksiId(' ');
        if (this.value != '' && document.getElementById('invoiceType').value == 4) {
            getPrediksiId(0, $('select[id="shipmentIdPrediksi"]').val(), $('select[id="tipeBiayaId"]').val(), $('input[id="gvId"]').val(), 0);
        }

		
   });

    $('#stockpileId2').change(function () {
        if (document.getElementById('stockpileId2').value != '') {
            setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        } else {
            resetShipmentCode(' Shipment Code ');
        }
    });

    $('#shipmentId1').change(function () {
        console.log(document.getElementById('shipmentId1').value);
    });

    $('#accountId').change(function () {
        if (document.getElementById('accountId').value != '') {
            resetPoNo(' ');
            resetShipmentCode(' Shipment Code ');
            if(document.getElementById('typeOKS').value == 1){
                setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
            }
            setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        } else {
            resetPoNo(' PO NO ');
            resetShipmentCode(' Shipment Code ');
        }
    });

    $('#prediksiId').change(function () {
        $('#divprediksidetail').hide();
        if (document.getElementById('prediksiId').value != '') {
            setPrediksiDetail($('select[id="prediksiId"]').val(), 0);
        }
    });

    $('#shipmentIdPrediksi').change(function () {
        if (this.value != '' && document.getElementById('invoiceType').value == 4) {
            resetAccountPrediksi(' ');
            setAccountPrediksi(0, $('select[id="shipmentIdPrediksi"]').val(), 0, 0);
        } else {
            resetAccountPrediksi(' Shipment Id ');
          
        }
    });

    $('#accountIdPrediksi').change(function () {
        if (this.value != '' && document.getElementById('invoiceType').value == 4) {
            resetTipeBiayaPrediksi(' ');
            setTipeBiayaPrediksi(0, $('select[id="shipmentIdPrediksi"]').val(), $('select[id="accountIdPrediksi"]').val(), 0, 0);

        } else {
            resetTipeBiayaPrediksi(' Account Id ');
        }
    });




    function resetShipmentCode(text) {
        document.getElementById('shipmentId1').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId1').options.add(x);
    }

    function setShipmentCode(type, stockpileId2, shipmentId1) {
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

    function resetTipeBiayaPrediksi(text) {
        document.getElementById('tipeBiayaId').options.length = 0;
        var x1 = document.createElement('option');
        x1.value = '';
        x1.text = '-- Please Select' + text + '--';
        document.getElementById('tipeBiayaId').options.add(x1);
    }

    function setTipeBiayaPrediksi(type, shipmentId, accountId, tipeBiayaId, prediksiDetailId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGVprediksi',
                shipmentId: shipmentId,
                accountId: accountId,
                prediksiDetailId: prediksiDetailId

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
                        document.getElementById('tipeBiayaId').options.length = 0;
                        var x1 = document.createElement('option');
                        x1.value = '';
                        x1.text = '-- Please Select Account--';
                        document.getElementById('tipeBiayaId').options.add(x1);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x1 = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x1.value = resultOption[2];
                        x1.text = resultOption[3];
                        document.getElementById('tipeBiayaId').options.add(x1);
                    }

                    if(type == 1) {
                            $('#tipeBiayaId').find('option').each(function(i,e){
                                if($(e).val() == tipeBiayaId){
                                    $('#tipeBiayaId').prop('selectedIndex',i);
                                    $("#tipeBiayaId").select2({
                                    width: "100%",
                                    placeholder: tipeBiayaId
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
                invoiceType: invoiceType,
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
                    console.log(type);

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

    function resetAccountPrediksi(text) {
        document.getElementById('accountIdPrediksi').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountIdPrediksi').options.add(x);
    }
	
	function resetTipeBiaya(text) {
        document.getElementById('tipeBiayaId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('tipeBiayaId').options.add(x);
    }

    function setAccountPrediksi(type, shipmentId1, accountIdPrediksi, prediksiDetailId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getAccountInvoicePrediksi',
                shipmentId1: shipmentId1,
                accountIdPrediksi: accountIdPrediksi,
                prediksiDetailId: prediksiDetailId

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
                    if(type == 1) {
                            $('#accountIdPrediksi').find('option').each(function(i,e){
                                if($(e).val() == accountIdPrediksi){
                                    $('#accountIdPrediksi').prop('selectedIndex',i);
                                    $("#accountIdPrediksi").select2({
                                    width: "100%",
                                    placeholder: accountIdPrediksi
                                });
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

    function setShipmentCodePrediksi(type, invoiceType, shipmentIdPrediksi, prediksiDetailId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getShipmentInvoicePrediksi',
                invoiceType: invoiceType,
                shipmentIdPrediksi: shipmentIdPrediksi,
                prediksiDetailId: prediksiDetailId

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

                    if(type == 1) {
                            $('#shipmentIdPrediksi').find('option').each(function(i,e){
                                if($(e).val() == shipmentIdPrediksi){
                                    $('#shipmentIdPrediksi').prop('selectedIndex',i);
                                    $("#shipmentIdPrediksi").select2({
                                    width: "100%",
                                    placeholder: shipmentIdPrediksi
                                });
                                }
                        });
                    }

                }
            }
        });
    }
    function resetPrediksiId(text) {
        document.getElementById('prediksiId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('prediksiId').options.add(x);
    }

    function getPrediksiId(type, shipmentId, tipeBiayaId, generalVendorId, prediksiId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPrediksiId',
                shipmentId: shipmentId,
                tipeBiayaId: tipeBiayaId,
                generalVendorId: generalVendorId,
                prediksiId: prediksiId

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

                        if(type == 1) {
                            $('#prediksiId').find('option').each(function(i,e){
                                if($(e).val() == prediksiId){
                                    $('#prediksiId').prop('selectedIndex',i);
                                    $("#prediksiId").select2({
                                    width: "100%",
                                    placeholder: prediksiId
                                });
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
                        setPrediksiDetail(returnVal[1], 0);
                    }

                   

                }
            }
        });
    }
    
    function setPrediksiDetail(prediksiId, status) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPrediksiDetail',
                prediksiId: prediksiId,
                status: status

            },
            success: function (data) {
                if (data != '') {
                    $('#divprediksidetail').show();
                    document.getElementById('prediksiDetail').innerHTML = data;
                }
            }
        });
    }
    

    function setInvoiceDP(podID, generalVendorId, ppn1) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setInvoiceDP',
                podID: podID,
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

</script>

<div id="addAccountForms">
    <input type="hidden" name="action" id="action" value="pengajuan_general_detail">
    <input type="hidden" name="pgdId" value="<?php echo $pgdId ?>">
    <input type="hidden" name="_method" value="UPDATE">
    <input type="hidden" name="amount1" id="amount1" value="<?php echo $tamount ?>">
    <input type="hidden" name="tamount_converted" id="tamount_converted" value="<?php echo $tamountConverted ?>">
    <input type="hidden" name="tamount_pengajuan" id="tamount_pengajuan" value="<?php echo $tamountConverted ?>">
    <input type="hidden" name="pphTaxId" id="pphTaxId" value="<?php echo $pphTaxId ?>">
    <input type="hidden" name="tax_pph_value" id="tax_pph_value" value="<?php echo $pphValue ?>">
    <input type="hidden" name="ppn1" id="ppn1" value="<?php echo $ppnValue ?>">
    <input type="hidden" name="exchangeRate" id="exchangeRate" value="<?php echo $excRate ?>">
    <input type="hidden" name="currencyId" id="currencyId" value="<?php echo $currencyId ?>">
    <input type="hidden" name="uom" id="uom" value="<?php echo $uomId ?>">
    <input type="hidden" name="gvEmail" id="gvEmail" value="<?php echo $gvEmail ?>">
    <input type="hidden" id="prediksiHeader" name="prediksiHeader">
    <input type="hidden" id="invoiceMethod" name="invoiceMethod" value="<?php echo $invoiceMethod ?>"> 
    <input type="hidden" name="typeOKS" id="typeOKS" value="<?php echo $typeOKS ?>">
    <input type="hidden" name="contractId" id="contractId" value="<?php echo $contractId ?>">


    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Type</label>
            <?php createCombo("SELECT '4' as id, 'Loading' as info UNION
                    SELECT '5' as id, 'Umum' as info UNION
                    SELECT '6' as id, 'HO' as info;", $type, "", "invoiceType", "id", "info", "", "", "select2combobox100", 1); ?>
        </div>

        <div class="span4 lightblue" id= "divAccount" style="display: none;">
            <label>Account</label>
            <?php createCombo("SELECT acc.account_id, CONCAT(acc.account_name) AS account_full
                     FROM account acc {$joinProperty}
                        WHERE acc.status = 0 AND acc.account_type = {$invoiceType} AND acc.account_no != 210105", $accountId, "", "accountId", "account_id", "account_full", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id = "divShipmentPrediksi" style="display: none;">
            <label>Shipment Code</label>

            <?php createCombo("", "", "", "shipmentIdPrediksi", "shipment_id", "shipment_no", "", "", "select2combobox100", 4); ?>
        </div>          
        <div class="span4 lightblue">
            <label>Method</label>
            <!--            --><?php
            //            createCombo("SELECT '1' as id, 'IN' as info UNION
            //                    SELECT '2' as id, 'OUT' as info;", $invMethod, "", "invMethod", "id", "info",
            //                "", "", "select2combobox100", 1);
            //            ?>
            <input type="text" value="OUT" readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Remark (Stockpile)</label>
            <?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $spRemark, "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1); ?>
        </div>
        <div class="span4 lightblue">
            <label>PO No.</label>
            <?php createCombo("", $poId, "", "poId", "contract_id", "po_no", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id = "divAccountPrediksi" style="display: none;">
            <label>Account</label>
            <?php createCombo("", $accountId, "", "accountIdPrediksi", "id", "info", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue" id="divShipment" style="display: none;">
            <label>Shipment Code</label>
            <!-- <?php createCombo("SELECT a.shipment_id, CONCAT(a.shipment_no, ' - ', SUBSTR(a.`shipment_code`,-1)) AS shipment_no, b.`stockpile_id`  
                    FROM shipment a LEFT JOIN sales b ON a.`sales_id` = b.`sales_id` WHERE b.`sales_status` != 4 AND b.stockpile_id = {$spRemark} ORDER BY shipment_id DESC", $shipmentId, "", "shipmentId1", "shipment_id", "shipment_no", "", "", "select2combobox100", 4); 
            ?> -->
            <?php
            createCombo("SELECT shipment_id, shipment_no
                            FROM shipment", $shipmentId, "", "shipmentId1", "shipment_id", "shipment_no",
                "", "", "select2combobox100", 4, "", true);
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Qty</label>
            <input type="text" class="span12" tabindex="" id="qty" name="qty" value="<?php echo $qty ?>" readonly>
        </div>
        <div class="span3 lightblue">
            <label>Unit Price</label>
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $price ?>" readonly>
        </div>
        <div class="span2 lightblue">
            <label>Termin (%)</label>
            <input type="text" class="span12" tabindex="" id="terminDetail" name="terminDetail" min="0" max="100"
                   value="<?php echo $termin ?>" readonly>
        </div>
        <div class="span4 lightblue">
            <label>Amount</label>
            <input type="text" readonly class="span12" tabindex="" id="amount" name="amount"
                   value="<?php echo $amount ?>">

        </div>
    </div>
    <div class="row-fluid">
        <div class="span3 lightblue" >
                <label>Vendor Name <span style="color: red;">*</span></label>
                <input type="hidden"  class="span12" tabindex="" readonly id="gvId" name="gvId" value=<?php echo $gvId ?>>
                <input type="text" name="vendorName" id="vendorName" readonly value="<?php echo $vendorName ?>">
        </div>
        <div class="span3 lightblue" id="divtipebiaya" style="display: none;"  >
                <label>Tipe Biaya <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "tipeBiayaId", "costId", "tipe_biaya", "", "", "select2combobox100", 4);
                ?>
        </div>
        <div class="span3 lightblue" id="divCodePrediksi" style="display: none;">
            <label>Code Prediksi <span style="color: red;">*</span></label>
            <div id="divComboPrediksi" style="display: none;" >
                <?php
                    createCombo("", "", "", "prediksiId", "prediction_detail_id", "generate_code_detai", "", "", "select2combobox100", 4); 
                ?>
            </div>
            <div id="divInputPrediksi" style="display: none;" >
                <input type="hidden" readonly class="span12" tabindex="" id="prediksiId1">
                <input type="text" readonly class="span12" tabindex="" id="prediksiText">
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="1" tabindex="" id="notes" name="notes"> <?php echo $notes ?></textarea>
        </div>
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

</div>
