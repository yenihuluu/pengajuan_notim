<?php
error_rePOrting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connectionre
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'PO';

$readonlyProperty = '';
$disableProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">
$inputDate = $date->format('d/m/Y');
$POId = '';
$PONo = '';
$inputnopenawaran = '';
$generalVendorId = '';
$generatedPONo = '';
$currencyId = '';
$POMethod = '';
$price = '';
$quantity = '';
$amount = '';
$amountDP = 0;
$pphPO1 = '';
$ppnPO1 = '';
$ppnPOID = '';
$pphPOID = '';
$pph2 = 0;
$ppn2 = 0;
$exchangeRate = '';
$shipmentId = '';
$toc = '';
$totalpph = '';
$totalppn = '';
$totalall = '';
$signId = '';
$requestDate = '';
$idPOHDR = '';
$gvEmail = '';
$gvBankId = '';
// </editor-fold>

// If ID is in the parameter
if (isset($_POST['POId']) && $_POST['POId'] != '') {

    $POId = $_POST['POId'];

    // $readonlyProperty = ' readonly ';
    // $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">


    $sql = "SELECT
			ph.*,gv.*,DATE_FORMAT(ph.tanggal, '%d/%m/%Y') as tanggal,pg.status_pengajuan, ph.status AS po_status
			FROM po_hdr ph
			LEFT JOIN general_vendor gv ON gv.general_vendor_id = ph.general_vendor_id
			LEFT JOIN master_sign si ON si.idmaster_sign = ph.sign_id
            LEFT JOIN pengajuan_general pg ON pg.po_id = ph.idpo_hdr
            WHERE ph.no_po = '{$POId}'
            ORDER BY ph.idpo_hdr ASC
            ";
    // echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $inputnopenawaran = $rowData->no_penawaran;
        $requestDate = $rowData->tanggal;
        $generalVendorId = $rowData->general_vendor_id;
        $signId = $rowData->sign_id;
        $currencyId = $rowData->currency_id;
        $remarks = $rowData->memo;
        $toc = $rowData->toc;
        $idPOHDR = $rowData->idpo_hdr;
        $gvBankId = $rowData->bank_id;
        $gvEmail = $rowData->gv_email;
        $POMethod = $rowData->po_method;

        if($rowData->po_status == 5 || $rowData->po_status == 1) {
            $disableProperty = 'disabled';
            $readonlyProperty = 'readonly';
        }
    }

    // </editor-fold>

}

// <editor-fold defaultstate="collapsed" desc="Functions">
/*
function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {

        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
	} else if($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
    }

    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if($boolAllow) {
        if(strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }

    echo "</SELECT>";
}
*/

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
    $(document).ajaxStop($.unblockUI);

    function setPODetail(generatedPONo, idPOHDR, gvid) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPODetail',
                generatedPONo: generatedPONo,
                idPOHDR: idPOHDR,
                gvid: gvid
            },
            success: function (data) {
                if (data != '') {
                    $('#PODetail').show();
                    document.getElementById('PODetail').innerHTML = data;
                } else {
                    $('#PODetail').hide();
                }
            }
        });
    }

    <?php if($idPOHDR != ''){?>
        getVendorBank(1, <?php echo $generalVendorId ?>, <?php echo $gvBankId ?>);
    <?php } ?>

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

        if (document.getElementById('POId').value != "") {
            document.getElementById('generatedPONo').value = document.getElementById('POId').value;
            setPODetail($('input[id="POId"]').val(), $('input[id="idPOHDR"]').val(), $('select[id="generalVendorId"]').val());
        } else {
            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: {
                    action: 'getPONo'
                },
                success: function (data) {
                    if (data != '') {
                        document.getElementById('generatedPONo').value = data;
                        $('#addPO').hide();

                    }
                }

            });
        }

        $('#generalVendorId').change(function () {
            if (document.getElementById('generalVendorId').value != '') {
                setPODetail($('input[id="POId"]').val(), $('input[id="idPOHDR"]').val(), $('select[id="generalVendorId"]').val());
                getVendorBank(0, $('select[id="generalVendorId"]').val());
                getVendorEmail($('select[id="generalVendorId"]').val());

                $('#addPO').show();
            } else {
                $('#addPO').hide();
            }
        });

        // $('#POMethod').change(function () {
        //     if (document.getElementById('POMethod').value != '') {
        //         $('#addPO').show();
        //     } else {
        //         $('#addPO').hide();
        //     }
        // });


        $("#PODataForm").validate({
            rules: {
                //contractType: "required",
                generalVendorId: "required",
                currencyId: "required",
                //exchangeRate: "required",
                //accountId: "required",
                //invoiceType: "required",
                amount: "required",
                stockpileId: "required",
                gvBankId: "required",
                signId: "required",
                requestDate: "required"
            },
            messages: {
                // contractType: "Contract Type is a required field.",
                generalVendorId: "Vendor is a required field.",
                currencyId: "Currency is a required field.",
                //exchangeRate: "Exchange Rate is a required field.",
                //accountId: "Account is a required field.",
                //invoiceType: "Invoice Type is a required field.",
                amount: "Amount is a required field.",
                stockpileId: "Stockpile is a required field.",
                gvBankId: "Bank is a required field.",
                signId: "Check By is a required field.",
                requestDate: "Date is a required field"
            },
            submitHandler: function (form) {

                $.ajax({
                    url: './irvan.php',
                    method: 'POST',
                    data: $("#PODataForm").serialize(),
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
                                $('#pageContent').load('forms/print-po.php', {POId: returnVal[4]}, iAmACallbackFunction);
                            }
                        }
                    }
                });
            }
        });

        $("#insertForm").validate({
            rules: {
                itemId: "required",
                qty: "required",
                price: "required",
                amount: "required",
                stockpileId: "required",
                pphTaxId: "required",
                groupitemId: "required"
            },
            messages: {
                itemId: "Item is a required field.",
                qty: "Quantity Type is a required field.",
                price: "Price Type is a required field.",
                amount: "Amount is a required field.",
                stockpileId: "Stockpile is a required field.",
                pphTaxId: "PPh is a required field.",
                groupitemId: "Group Item is a required field."
            },
            submitHandler: function (form) {

                $.ajax({
                    url: './irvan.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');
                        //setPODetail(document.getElementById('generatedPONo').value);
                        //$('#insertModal').modal('hide');

                        if (returnVal[1] == 'OK') {
                            setPODetail($('input[id="POId"]').val(), $('input[id="idPOHDR"]').val(), $('select[id="generalVendorId"]').val());

                            $('#insertModal').modal('hide');
                        } else {
                            document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                            $("#modalErrorMsgInsert").show();
                        }

                    }
                });
            }
        });

        $('#showTransaction').click(function (e) {
            e.preventDefault();
            $('#insertModal').modal('show');
            $('#insertModalForm').load('forms/PO-data.php', {
                generalVendorId: $('select[id="generalVendorId"]').val(),
                gvEmail: document.getElementById('gvEmail').value,
                generatedPONo: document.getElementById('generatedPONo').value,
                requestDate: document.getElementById('requestDate').value,
                POId: document.getElementById('POId').value,
                POMethod: $('select[id="POMethod"]').val()
            });
        });

        if (document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }

        $('#currencyId').change(function () {
            if (document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });

    });

    function deletePODetail(idpo_detail) {
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'delete_po_detail',
                poDetailId: idpo_detail

            },
            success: function (data) {
                if (data != '') {
                    setPODetail($('input[id="POId"]').val(), $('input[id="idPOHDR"]').val(), $('select[id="generalVendorId"]').val());
                }
            }
        });
    }

    function EditPODetail(idpo_detail) {
        $("#modalErrorMsg").hide();
        $('#insertModal').modal('show');
        $('#insertModalForm').load('forms/po-data.php', {
            detail_poId: idpo_detail,
            generalVendorId: $('select[id="generalVendorId"]').val(), POMethod: $('select[id="POMethod"]').val()
        }, iAmACallbackFunction2);	//and hide the rotating gif
    }

    function getVendorEmail(generalVendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorEmail',
                    generalVendorId: generalVendorId
            },
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    document.getElementById('gvEmail').value = returnVal[1];
                }
            }
        });
    }
    
    function getVendorBank(type, vendorId, gvBankId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getVendorBankPO',
                vendorId: vendorId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }


                    if (returnValLength > 0) {
                        document.getElementById('gvBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('gvBankId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('gvBankId').options.add(x);
                    }

                    if (type == 1) {
                        $('#gvBankId').find('option').each(function(i,e){
                            if($(e).val() == gvBankId){
                                $('#gvBankId').prop('selectedIndex',i);        
                                    $("#gvBankId").select2({
                                        width: "100%",
                                        placeholder: gvBankId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function checkSlipInvoice(generalVendorId, ppnPO1, pphPO1, invoiceMethod) {
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }

        var checkedSlips2 = document.getElementsByName('checkedSlips2[]');
        var selected2 = "";
        for (var i = 0; i < checkedSlips2.length; i++) {
            if (checkedSlips2[i].checked) {
                if (selected2 == "") {
                    selected2 = checkedSlips2[i].value;
                } else {
                    selected2 = selected2 + "," + checkedSlips2[i].value;
                }
            }
        }

        var ppnPOValue = 'NONE';
        var pphPOValue = 'NONE';

        if (typeof (ppnPO1) != 'undefined' && ppnPO1 != null && typeof (pphPO1) != 'undefined' && pphPO1 != null) {
            if (ppnPO1 != 'NONE') {
                if (ppnPO1.value != '') {
                    ppnPOValue = ppnPO1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pphPO1 != 'NONE') {
                if (pphPO1.value != '') {
                    pphPOValue = pphPO1.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        setInvoiceDP(generalVendorId, selected2, selected, ppnPOValue, pphPOValue, invoiceMethod);
        }

        function checkAllInv(a) {
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        if (a.checked) {
            for (var i = 0; i < checkedSlips.length; i++) {
                if (checkedSlips[i].type == 'checkbox') {
                    checkedSlips[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkedSlips.length; i++) {
                console.log(i)
                if (checkedSlips[i].type == 'checkbox') {
                    checkedSlips[i].checked = false;
                }
            }
        }
        checkSlipInvoice(generalVendorId, ppnPO1, pphPO1, invoiceMethod);
    }

    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });

    function cancelPO() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'return_PO_data',
                idPOHDR: document.getElementById('idPOHDR').value,
                NoPOId: document.getElementById('POId').value,
                rejectRemarks: document.getElementById('reject_remarks').value,
            },
            success: function (data) {
                console.log(data);
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
                        $('#pageContent').load('views/po.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }
</script>

<form method="POst" id="PODataForm">
    <input type="hidden" name="action" id="action" value="PO_data"/>
    <input type="hidden" name="POId" id="POId" value="<?php echo $POId; ?>"/>
    <input type="hidden" name="idPOHDR" id="idPOHDR" value="<?php echo $idPOHDR; ?>"/>

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Generated PO No.</label>
            <input type="text" readonly id="generatedPONo" name="generatedPONo" value="<?php echo $generatedPONo; ?>">
        </div>

        <div class="span4 lightblue">
            <label>No Penawaran</label>
            <input type="text" tabindex="" id="inputnopenawaran" name="inputnopenawaran"
                   value="<?php echo $inputnopenawaran; ?>">
        </div>


    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Tanggal PO</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>

        <div class="span4 lightblue">
            <label>Check by<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT idmaster_sign, name
                        FROM master_sign", $signId, $readonlyProperty, "signId", "idmaster_sign", "name",
                "", "", "select2combobox75", 1, "", false);
            ?>
        </div>
        <div class="span4 lightblue">

        </div>

    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
            <label>Invoice Method <span style="color: red;">*</span></label>
            <?php if($POId == '') {
            createCombo("SELECT '1' as id, 'Full Payment' as info UNION
              		   SELECT '2' as id, 'Down Payment' as info;", $POMethod, "", "POMethod", "id", "info", "", "", "select2combobox75", 1);
            } else {
            createCombo("SELECT '1' as id, 'Full Payment' as info UNION
                            SELECT '2' as id, 'Down Payment' as info;", $POMethod, "disabled", "POMethod", "id", "info", "", "", "select2combobox75", 1); } ?>
            <!-- <input type="text" value="Full Payment" readonly>
            <input type="hidden" value="1" id="POMethod"> -->
        </div>
        <div class="span4 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code",
                "", "", "select2combobox75");
            ?>
        </div>
        <div class="span4 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate"
                   value="<?php echo $exchangeRate; ?>">
        </div>
    </div>

    <div class="row-fluid" style="margin-bottom: 25px;">
        <div class="span4 lightblue">
            <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, $readonlyProperty, "generalVendorId", "general_vendor_id", "general_vendor_name",
                "", "", "select2combobox75", 1, "", true);
            ?>
        </div>

        <div class="span3 lightblue">
            <label>Vendor Email<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="gvEmail" name="gvEmail" value= '<?php echo $gvEmail ?>'>
        </div>

        <div class="span4 lightblue">
            <label>Vendor Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "gvBankId", "gv_bank_id", "bank_name",
                "", 10, "select2combobox75", 2);
            ?>
        </div>
    </div>

    <div id="addPO" class="row-fluid" style="margin-bottom: 25px;">
        <div class="span4 lightblue">
            <button class="btn btn-warning" id="showTransaction">Add Data</button>

        </div>
    </div>

    <div class="row-fluid" id="PODetail" style="display: none;">
        PO detail
    </div>


    <div class="row-fluid" id="IDP1" style="display: none;">
        PO DP
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks" <?php echo $readonlyProperty; ?>><?php echo $remarks; ?></textarea>
        </div>

    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Terms of Condition</label>
            <textarea class="span12" rows="3" tabindex="" id="toc" name="toc" <?php echo $readonlyProperty; ?>><?php echo $toc; ?></textarea>
        </div>

    </div>
    <div class="row-fluid" style="margin-bottom: 15px;">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>

    <?php if($POId != '') { ?>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Reject Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                    name="reject_remarks" <?php echo $readonlyProperty; ?>><?php echo $rejectRemarks; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger" type="button" <?php echo $disableProperty; ?> onclick="cancelPO()">CANCEL</button>
        </div>
    </div>
    <?php } ?>
</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form id="insertForm" method="POst" style="margin: 0px;">
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
            <button class="btn btn-primary">Add</button>
        </div>
    </form>
</div>
