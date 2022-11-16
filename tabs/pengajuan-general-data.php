<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Pengajuan General';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Pengajuan General Data">
$inputDate = $date->format('d/m/Y');
$pgId = '';
$invoiceNo = '';
$generalVendorId = '';
$generatedInvoiceNo = '';
$currencyId = '';
$invoiceMethod = '';
$price = '';
$quantity = '';
$amount = '';
$amountDP = 0;
$pph2 = 0;
$ppn2 = 0;
$exchangeRate = '';
$remarks = '';
$file = '';
$transaksiPO = '';
$paymentType = '';
$requestPaymentDate = '';
$sisaTermin = '';
$transaksiMutasi = 1;
$remarks_reject = '';
$gvBankId = '';
$gvEmail = '';
$userEmail = '';
// </editor-fold>

// If ID is in the parameter
if (isset($_POST['pgId']) && $_POST['pgId'] != '') {

    $pgId = $_POST['pgId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Pengajuan General">

    $sql = "SELECT pg.*, DATE_FORMAT(pg.invoice_date, '%d/%m/%Y') AS invoice_date2, 
            DATE_FORMAT(pg.input_date, '%d/%m/%Y') AS input_date, DATE_FORMAT(pg.request_date, '%d/%m/%Y') AS request_date, DATE_FORMAT(pg.tax_date, '%d/%m/%Y') AS tax_date,
            DATE_FORMAT(pg.request_payment_date, '%d/%m/%Y') AS request_payment_date
            FROM pengajuan_general pg
            WHERE pg.pengajuan_general_id = {$pgId}
            ORDER BY pg.pengajuan_general_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $invoiceTax = $rowData->invoice_tax;
        $invoiceDate = $rowData->invoice_date2;
        $inputDate = $rowData->input_date;
        $requestDate = $rowData->request_date;
        $taxDate = $rowData->tax_date;
        $generatedInvoiceNo = $rowData->invoice_no;
        $generatedInvoiceNo2 = $rowData->invoice_no2;
        $stockpileId = $rowData->stockpileId;
        $stockpileContractId3 = $rowData->po_id;
        $remarks = $rowData->remarks;
        $remarks_reject = $rowData->reject_remarks;

        $generalVendorId2 = $rowData->generalVendorId;
        $file = $rowData->file;
        $transaksiPO = $rowData->transaction_po;
        $transaksiMutasi = $rowData->transaction_type;
        $paymentType = $rowData->payment_type;
        $requestPaymentDate = $rowData->request_payment_date;
		$picEmail = $rowData->pic_email;
		//if($picEmail == ''){
		//	$picEmail = 0;
		//}else{
		//	$picEmail = $rowData->pic_email;
		//}
        

    }

    $sqlGetPGD = "SELECT * FROM pengajuan_general_detail WHERE pg_id = {$pgId} LIMIT 1";
    $resultData2 = $myDatabase->query($sqlGetPGD, MYSQLI_STORE_RESULT);
    if ($resultData2 !== false && $resultData2->num_rows > 0) {
        $rowData2 = $resultData2->fetch_object();
        $generalVendorId = $rowData2->general_vendor_id;
        $gvBankId = $rowData2->gv_bank_id;
        $gvEmail = $rowData2->vendor_email;
    }

    // </editor-fold>
    $method = 'UPDATE';

} else {
    $method = 'INSERT';
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
        if ($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        }
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
    } else if ($empty == 5) {
        echo "<option value=''>-- Please Select Vendor --</option>";
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
    $('.datepicker').datepicker({
        minViewMode: 0,
        todayHighlight: true,
        //autoclose: true,
        orientation: "bottom auto",
        startView: 0
    });
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

        <?php
        if($generatedInvoiceNo == "") {
        ?>
        // if(document.getElementById('generalVendorId').value != "") {
        $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: {
                action: 'getPengajuanNo'

            },
            success: function (data) {
                if (data != '') {
                    document.getElementById('generatedInvoiceNo').value = data;
                }
            }
        });
        <?php }else{ ?>
        //setInvoiceType(generatedInvoiceNo);
        <?php } ?>
        <?php if(isset($idPOHDR) && $idPOHDR != ''){ ?>
        document.getElementById('row1').classList.remove("hidden");
        document.getElementById('row2').classList.remove("hidden");
        document.getElementById('row3').classList.remove("hidden");
        $('#poDetail').show();
        <?php } ?>
        //ON CHANGE NO PO
        $("#idPOHDR").change(function (e) {
            if (document.getElementById('idPOHDR').value != 0) {
                document.getElementById('row2').classList.remove("hidden");
                document.getElementById('row3').classList.remove("hidden");
                $('#poDetail').show();

                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#poDetail').load('tabs/pengajuan-general-po-detail.php', {
                    idPOHDR: document.getElementById('idPOHDR').value,
                    transaksiPO: document.getElementById('transaksiPO').value,
                    termin: document.getElementById('termin').value
                }, iAmACallbackFunction2);

                getSisaTerminPO(document.getElementById('idPOHDR').value);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif
            } else {
                document.getElementById('row2').classList.add("hidden");
                document.getElementById('row3').classList.add("hidden");
                document.getElementById('row4').classList.add("hidden");
                $('#poDetail').hide();
            }
        });
        //SUBMIT FORM
        $("#PengajuanDataForm").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $.ajax({
                url: './irvan.php',
                type: 'POST',
                data: formData,
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
                            $('#pageContent').load('views/pengajuan-general.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
		
		      $("#PengajuanDataForm").validate({
                rules: {
                    transaksiPO: "required",
                    paymentType: "required",
                    requestPaymentDateValue: "required",
                    requestPaymentDate: "required"
                    // file: {required: true, filesize: 1048576}
                },
                messages: {
                    transaksiPO: "Transaction PO is a required field.",
                    paymentType: "Type is a required field.",
                    requestPaymentDateValue: "Request Payment date  is a required field.",
                    requestPaymentDate: "Request payment date is a required field.",
                    // file: "invoice file   is a required field."
                },
            submitHandler: function (form) {
                // $('#submitButton2').attr("disabled", true);
            }
        });

        $("#insertForm").validate({
            rules: {
                //contractType: "required",
                currencyId: "required",
                exchangeRate: "required",
                invoiceType: "required",
                qty: "required",
                price: "required",
                pphTaxId: "required",
                generalVendorId: "required",
                amount: "required",
				gvEmail: "required",
				requestPaymentDate:"required"
                //stockpileId: "required"
            },
            messages: {
                // contractType: "Contract Type is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                invoiceType: "Invoice Type is a required field.",
                qty: "Quantity Type is a required field.",
                price: "Price Type is a required field.",
                pphTaxId: "PPh Type is a required field.",
                generalVendorId: "Vendor Type is a required field.",
                amount: "Amount is a required field.",
				gvEmail: "Email Vendor is a required field.",
				requestPaymentDate:"This is a required field."
                //stockpileId: "Stockpile is a required field."
            },
            submitHandler: function (form) {
               
                $.ajax({
                    url: './irvan.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                var resultData = returnVal[3].split('~');
                                <?php if(isset($_POST['pgId']) && $_POST['pgId'] != ''){ ?>
                                getPengajuanDetail();
                                <?php }else{ ?>
                                setPengajuanGeneralDetail();
                                <?php } ?>

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


</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#showTransaction').click(function (e) {
            if (document.getElementById('generalVendorId').value != '' && document.getElementById('gvBankId').value != '') {
                e.preventDefault();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/pengajuan-general-data.php', {
                    pgId: document.getElementById('pgId').value,
                    generalVendorId: document.getElementById('generalVendorId').value,
                    gvBankId: document.getElementById('gvBankId').value,
                    gvEmail: document.getElementById('gvEmail').value,
                    transaksiMutasi: document.getElementById('transaksiMutasi').value

                });
            } else {
                alertify.set({
                    labels: {
                        ok: "OK"
                    }
                });
                alertify.alert("Pilih Vendor & Vendor Bank Dahulu!");
            }
        });

        $('#showMutasiDetail').click(function (e) {
            e.preventDefault();
            $('#insertModal').modal('show');
            $('#insertModalForm').load('forms/pengajuan-general-data.php', {
                pgId: document.getElementById('pgId').value,
                generalVendorId: document.getElementById('generalVendorId').value,
                gvBankId: document.getElementById('gvBankId').value,
                gvEmail: document.getElementById('gvEmail').value,
                transaksiMutasi: document.getElementById('transaksiMutasi').value

            });

        });

        <?php if(isset($_POST['pgId']) && $_POST['pgId'] != ''){ ?>
        getPengajuanDetail();
        <?php } ?>
    });

    function getPengajuanDetail() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPengajuanGeneralDetail',
                pgId: document.getElementById('pgId').value
            },
            success: function (data) {
                if (data != '') {
                    $('#pengajuanDetail').show();
                    document.getElementById('pengajuanDetail').innerHTML = data;
                } else {
                    $('#pengajuanDetail').hide();
                }
            }
        });
    }

    function deleteInvoiceDetail(pgdId) {
        alertify.set({
            labels: {
                ok: "Yes",
                cancel: "No"
            }
        });
        alertify.confirm("Are you sure want to delete this record?", function (e) {
            if (e) {
                $.ajax({
                    url: './irvan.php',
                    method: 'POST',
                    data: {
                        action: 'pengajuan_general_detail',
                        _method: 'DELETE',
                        pgdId: pgdId
                    },
                    success: function (data) {
                        if (data != '') {
                            <?php if(isset($_POST['pgId'])){ ?>
                            getPengajuanDetail();
                            <?php }else{ ?>
                            setPengajuanGeneralDetail();
                            <?php } ?>
                        }
                    }
                });
            }
            return false;
        });
    }

    function setPengajuanGeneralDetail() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPengajuanGeneralDetail',
            },
            success: function (data) {
                if (data != '') {
                    $('#pengajuanDetail').show();
                    document.getElementById('pengajuanDetail').innerHTML = data;
                } else {
                    $('#pengajuanDetail').hide();
                }
            }
        });
    }

    function checkSlipInvoice(generalVendorId, ppn1, pph1, invoiceMethod) {

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

        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn1) != 'undefined' && ppn1 != null && typeof (pph1) != 'undefined' && pph1 != null) {
            if (ppn1 != 'NONE') {
                if (ppn1.value != '') {
                    ppnValue = ppn1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph1 != 'NONE') {
                if (pph1.value != '') {
                    pphValue = pph1.value.replace(new RegExp(",", "g"), "");
                }
            }
        }


        setInvoiceDP(generalVendorId, selected2, selected, ppnValue, pphValue, 1);

        //alert(generalVendorId);
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
                if (checkedSlips[i].type == 'checkbox') {
                    checkedSlips[i].checked = false;
                }
            }
        }
        checkSlipInvoice(generalVendorId, ppn1, pph1, 1);
    }

</script>

<script type="text/javascript">
    $(function () {

        //https://github.com/eternicode/bootstrap-datepicker
        <?php if(isset($transaksiMutasi) && $transaksiMutasi == 1){ ?>
        $('#generalVendorIdLabel').show();
        $('#generalVendorEmail').show();
        $('#generalUserEmail').show();
        $('#vendorBankIdLabel').show();
        $('#AddData').show();
        // document.getElementById('AddData').classList.remove("hidden");
        // document.getElementById('AddMutasi').classList.add("hidden");

        <?php }elseif(isset($transaksiMutasi) && $transaksiMutasi == 2){ ?>
        $('#generalVendorIdLabel').hide();
        $('#vendorBankIdLabel').hide();
        $('#generalVendorEmail').show();
        $('#generalUserEmail').show();

        // document.getElementById('AddMutasi').classList.remove("hidden");
        // document.getElementById('AddData').classList.add("hidden");
        <?php } ?>

        <?php if(isset($transaksiPO) && $transaksiPO == 1) { ?>
        document.getElementById('row2').classList.remove("hidden");
        document.getElementById('row3').classList.remove("hidden");
        document.getElementById('AddData').classList.remove("hidden");
        $('#notPoTransaction').show();
        $('#poHDRLabel').hide();
        $('#poDetail').hide();
        <?php } elseif(isset($transaksiPO) && $transaksiPO == 2) { ?>
        $('#poHDRLabel').show();
        $('#poDetail').show();
        $('#notPoTransaction').hide();
        document.getElementById('AddData').classList.add("hidden");
        document.getElementById('row2').classList.remove("hidden");
        document.getElementById('row3').classList.remove("hidden");
        <?php } ?>

        <?php if(isset($paymentType) && ($paymentType == 1 || $paymentType == 0)) { ?>
        $('#requestPaymentDate').show();
        <?php } ?>

        $('#paymentType').change(function () {
            console.log(this.value)
            if (this.value == 1) {
                $('#requestPaymentDate').show();
                $("#requestPaymentDateValue").prop("disabled", false).val("");
                $("#requestPaymentDateValue2").prop("disabled", true);

            } else if (this.value == 0) {
                $('#requestPaymentDate').show();
                $("#requestPaymentDateValue").prop("disabled", true);
                $("#requestPaymentDateValue2").prop("disabled", false);
                $.ajax({
                    url: 'get_data.php',
                    method: 'POST',
                    data: {
                        action: 'setPlanPayDate',
                    },
                    success: function (data) {
                        $('#requestPaymentDateValue').val(data)
                        $('#requestPaymentDateValue2').val(data)
                    }
                });
            } else if (this.value == "") {
                $('#requestPaymentDate').hide();
                $("#requestPaymentDateValue").prop("disabled", true).val("");
            }

        });

        var wto;
        $('#termin').change(function () {
            clearTimeout(wto);
            wto = setTimeout(function () {
                var termin = parseInt(document.getElementById('termin').value);
                var sisaTermin = parseInt(document.getElementById('sisaTermin').value)
                if (termin > sisaTermin) {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert("Termin tidak boleh lebih dari sisa termin!");
                } else {
                    $("#dataSearch").fadeOut();
                    $("#dataContent").fadeOut();

                    $.blockUI({message: '<h4>Please wait...</h4>'});

                    $('#loading').css('visibility', 'visible');

                    $('#poDetail').load('tabs/pengajuan-general-po-detail.php', {
                        idPOHDR: document.getElementById('idPOHDR').value,
                        transaksiPO: document.getElementById('transaksiPO').value,
                        termin: document.getElementById('termin').value
                    }, iAmACallbackFunction2);

                    $('#loading').css('visibility', 'hidden');	//and hide the rotating gif
                }

            }, 1000);

        });

        $('#transaksiMutasi').change(function () {
            if (document.getElementById('transaksiMutasi').value == 2) {
                $('#generalVendorIdLabel').hide();
                $('#generalVendorEmail').hide();
                $('#generalUserEmail').hide();
                $('#vendorBankIdLabel').hide();
                // $('#AddData').hide();
                // $('#AddMutasi').show();

                document.getElementById('AddMutasi').classList.remove("hidden");
                document.getElementById('AddData').classList.add("hidden");
            } else {
                $('#generalVendorIdLabel').show();
                $('#generalVendorEmail').show();
                $('#generalUserEmail').show();
                $('#vendorBankIdLabel').show();
                // $('#AddData').show();
                // $('#AddMutasi').hide()

                document.getElementById('AddData').classList.remove("hidden");
                document.getElementById('AddMutasi').classList.add("hidden");
            }
        });

        $('#transaksiPO').change(function () {
            // document.getElementById('AddMutasi').classList.add("hidden");
            // document.getElementById('transaksiMutasi').value = 0;
            if (document.getElementById('transaksiPO').value == 2) {
                document.getElementById('AddData').classList.add("hidden");
                document.getElementById('row1').classList.remove("hidden");
                $('#poHDRLabel').show();
                $('#poDetail').show();
                $('#notPoTransaction').hide();
            } else {
                document.getElementById('AddData').classList.remove("hidden");
                document.getElementById('row1').classList.add("hidden");
                document.getElementById('row2').classList.remove("hidden");
                document.getElementById('row3').classList.remove("hidden");
                $('#poHDRLabel').hide();
                $('#poDetail').hide();
                $('#notPoTransaction').show();

            }
        });

        // // Session Storage Browser
        // Object.keys(sessionStorage).forEach((key) => {
        //     var newKey = key.split('.');
        //     if (newKey[0] == "pengajuanGeneral" && newKey[1] != "") {
        //         document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
        //         $('#' + newKey[1]).trigger('change');
        //     }
        // });
        // $(":input").change(function () {
        //     sessionStorage.setItem("pengajuanGeneral." + this.id, this.value);
        // });
    });
    // if(sessionStorage.getItem("pengajuanGeneral.idPOHDR") != null){
    //     $('#idPOHDR').val(sessionStorage.getItem("pengajuanGeneral.idPOHDR")).trigger('change');
    // }
    $('#generalVendorId').change(function () {
        getVendorBank(0, $('select[id="generalVendorId"]').val());
        getVendorEmail($('select[id="generalVendorId"]').val());
        console.log("TEST");
    });

    function getVendorBank(type, vendorId) {
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

                        document.getElementById('gvBankId').value = vendorId;

                    }
                }
            }
        });
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

    function getSisaTerminPO(IdPOHDR) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getSisaTerminPO',
                idPOHDR: IdPOHDR
            },
            success: function (data) {
                var returnVal = data.split('|');
                $('#sisaTermin').val(returnVal[0]);
                $('#requestDate').val(returnVal[1]);
                $('#stockpileId').val(returnVal[2]).trigger('change');

            }
        });
    }

    // window.onload = function () {

    // }
</script>

<form method="post" id="PengajuanDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_general_data">
    <input type="hidden" name="pgId" id="pgId" value="<?php echo $pgId; ?>">
    <input type="hidden" name="_method" id="_method" value="<?php echo $method; ?>">

    <div class="row-fluid">
        <?php if (isset($pgId) && $pgId != '') { ?>
            <div class="span3 lightblue" id="transaksiPOLabel">
                <label>Transaksi PO ? <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", $transaksiPO, "disabled", "transaksiPO", "id", "info",
                    "", 11, "select2combobox100");
                ?>
            </div>
        <?php } else { ?>
            <div class="span3 lightblue" id="transaksiPOLabel">
                <label>Transaksi PO ? <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", "", "", "transaksiPO", "id", "info",
                    "", 11, "select2combobox100");
                ?>
            </div>
        <?php } ?>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Payment Type</label>
            <?php
            createCombo("SELECT '0' as id, 'Normal' as info UNION SELECT '1' as id, 'Urgent' as info;", $paymentType, "", "paymentType", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue" id="requestPaymentDate" style="display: none">
            <label>Request Payment Date</label>
            <input type="hidden" name="requestPaymentDate" id="requestPaymentDateValue2" disabled>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" name="requestPaymentDate"
                   id="requestPaymentDateValue"
                   value="<?php echo $requestPaymentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" disabled>
        </div>

    </div>

    <div id="row1" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue" id="poHDRLabel" style="display: none">
            <label>PO No <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT idpo_hdr,no_po FROM po_hdr where status = 0 AND entry_by = {$_SESSION['userId']} ORDER BY entry_date desc", $idPOHDR, "", "idPOHDR", "idpo_hdr", "no_po", "", "", "select2combobox100", 1);
            ?>
        </div>

        <div class="span1 lightblue">
        </div>

        <div class="span3 lightblue">
            <label>Termin</label>
            <input type="text" placeholder="termin" tabindex="" id="termin" name="termin"
                   value="<?php echo $termin ?>">
        </div>

        <div class="span1 lightblue">
            <!--            <button class="btn-warning">check</button>-->
        </div>

        <div class="span3 lightblue">
            <label>Sisa Termin</label>
            <input type="number" placeholder="SisaTermin" tabindex="" id="sisaTermin" name="sisaTermin"
                   value="<?php echo $sisaTermin ?>" readonly>
        </div>


    </div>

    <div id="row2" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Request Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate"
                   value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Original Invoice No.</label>
            <input type="text" class="span12" tabindex="" id="generatedInvoiceNo2" name="generatedInvoiceNo2"
                   value="<?php echo $generatedInvoiceNo2; ?>">

        </div>
    </div>

    <div id="row3" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
            <!--            <input type="text" value="--><?php //echo $stockpile ?><!--" readonly>-->
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <!--<label>Invoice Method <span style="color: red;">*</span></label>-->
            <?php
            //createCombo("SELECT '1' as id, 'Full Payment' as info UNION
            //      SELECT '2' as id, 'Down Payment' as info;", $invoiceMethod, "", "invoiceMethod", "id", "info", "", "", "select2combobox100",1);
            ?>
            <label>Tax Invoice No.</label>
            <input type="text" class="span12" tabindex="" id="invoiceTax" name="invoiceTax"
                   value="<?php echo $invoiceTax; ?>">
        </div>
        <div class="span1 lightblue">

        </div>
        <div class="span3 lightblue">
            <label>Tax Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="taxDate" name="taxDate"
                   value="<?php echo $taxDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
    </div>
    <div class="row-fluid">

        <div class="span3 lightblue">
            <label>Generated Pengajuan No.</label>
            <input type="text" class="span12" readonly id="generatedInvoiceNo" name="generatedInvoiceNo"
                   value="<?php echo $generatedInvoiceNo; ?>">
        </div>

        <div class="span1 lightblue">
        </div>

        <div class="span3 lightblue">
            <label>File.</label>
            <input type="file" class="span12" readonly id="file" name="file">
        </div>

        <div class="span1 lightblue">
        </div>

        <?php if (isset($file) && $file != '') { ?>
            <div class="span3 lightblue">
                <label>Show File.</label>
                <a href="<?php echo $file ?>" target="_blank">Open File </a>
            </div>
        <?php } ?>


    </div>
    <!--
        <div class="row-fluid hidden" style="margin-bottom: 7px;">
            <div class="span3 lightblue">
                <label>Invoice Method <span style="color: red;">*</span></label>
                <input type="text" value="Full Payment" readonly>
                <input type="hidden" value="1" id="invoiceMethod">

            </div>
        </div> -->

    <div id="notPoTransaction" class="row-fluid" style="margin-bottom: 25px; display: none">
    <div class="row-fluid" style="margin-bottom: 1px;">
        <div class="span4 lightblue" id="transaksiMutasiLabel">
            <label>Transaksi Mutasi ? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", $transaksiMutasi, "disabled", "transaksiMutasi", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>

        <div class="span4 lightblue" id="generalVendorIdLabel" style="display: none">
            <label>Vendor<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, "", "generalVendorId", "general_vendor_id", "general_vendor_name",
                "", "", "select2combobox100", 1, "", true);
            ?>
        </div>

        <?php if ($gvBankId != '') { ?>
            <div class="span4 lightblue" id="vendorBankIdLabel" style="display: none">
                <label>Vendor Bank<span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT gv_bank_id, CONCAT(bank_name,' - ',account_no) AS bank_name FROM general_vendor_bank WHERE general_vendor_id = {$generalVendorId}", "$gvBankId", "", "gvBankId", "gv_bank_id", "bank_name",
                    "", "", "select2combobox100", 1, "", true);
                ?>
            </div>
        <?php } else { ?>
            <div class="span4 lightblue" id="vendorBankIdLabel" style="display: none">
                <label>Vendor Bank <span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "gvBankId", "gv_bank_id", "bank_name",
                    "", 10, "select2combobox100", 5);
                ?>
            </div>
        <?php } ?>
        </div>
        <div id="row3" class="row-fluid" style="margin-bottom: 7px;">
            <?php if($gvEmail != ''){ ?>
                <div class="span4 lightblue" id="generalVendorEmail" style="display: none" >
                    <label>Vendor Email<span style="color: red;">*</span></label>
                    <input type="text" class="span12" tabindex="" id="gvEmail" name="gvEmail" value = "<?php echo $gvEmail ?>" readonly>
                </div>
            <?php }else{ ?>
                <div class="span4 lightblue" id="generalVendorEmail" style="display: none" >
                    <label>Vendor Email<span style="color: red;">*</span></label>
                    <input type="text" class="span12" tabindex="" id="gvEmail" name="gvEmail" readonly>
                </div>
            <?php } ?>

            <div class="span4 lightblue" id="generalUserEmail" style="display: none" >
                <label>PIC Email<span style="color: red;"></span></label>
                <?php
                createCombo("SELECT user_id, user_email FROM user WHERE active = 1", "$picEmail", "", "picEmail", "user_email", "user_email",
                    "", "", "select2combobox100", 1, "", true);
                ?>
            </div>
        </div>
    </div>

    <div id="AddData" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <button type="button" class="btn btn-warning" id="showTransaction">Add Data</button>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
        </div>
        <div class="span1 lightblue">
        </div>
    </div>

    <div id="AddMutasi" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <button type="button" class="btn btn-warning" id="showMutasiDetail">Add Mutasi</button>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
        </div>
        <div class="span1 lightblue">
        </div>
    </div>

    <div class="row-fluid" id="poDetail" style="display: none;">

    </div>

    <div class="row-fluid" id="pengajuanDetail" style="display: none;">
        Pengajuan Detail
    </div>

    <div class="row-fluid" id="IDP1" style="display: none;">
        Invoice DP
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>
    </div>
    <?php if ($remarks_reject != '') { ?>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span8 lightblue">
                <label>Remarks Reject</label>
                <textarea class="span12" rows="3" tabindex="" id="remarks_reject" name="remarks_reject"
                          readonly><?php echo $remarks_reject; ?></textarea>
            </div>
        </div>
    <?php } ?>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" type="submit" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
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