<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Approve Pengajuan General';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">
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
$pengajuanEmail = '';
$rejectRemarks = '';
$disableProperty1 = '';

// </editor-fold>

// If ID is in the parameter
if (isset($_POST['pgId']) && $_POST['pgId'] != '') {

    $pgId = $_POST['pgId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = '';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT pg.*, DATE_FORMAT(pg.invoice_date, '%d/%m/%Y') AS invoice_date2,
            DATE_FORMAT(pg.input_date, '%d/%m/%Y') AS input_date, DATE_FORMAT(pg.request_date, '%d/%m/%Y') AS request_date, DATE_FORMAT(pg.tax_date, '%d/%m/%Y') AS tax_date,
            CASE WHEN pg.invoice_id IS NOT NULL THEN pg.invoice_id ELSE pg.invoice_old_id END AS invoiceId
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
        $generalVendorId2 = $rowData->generalVendorId;
        $file = $rowData->file;
        $rejectRemarks = $rowData->reject_remarks;
        $transaksiPO = $rowData->transaction_po;
        $transaksiMutasi = $rowData->transaction_type;
        $paymentType = $rowData->payment_type;
        $requestPaymentDate = $rowData->request_payment_date;
        $invId = $rowData->invoiceId;
        $pStatus = $rowData->status_pengajuan;
        $invoiceMethod = $rowData->invoice_method;
        $typeOKS = $rowData->transaksi_oks_akt;
        if($pStatus == 2){
            $disabledProperty = ' disabled ';
        }
    }

    $sqlGetPGD = "SELECT * FROM pengajuan_general_detail WHERE pg_id = {$pgId} LIMIT 1";
    $resultData2 = $myDatabase->query($sqlGetPGD, MYSQLI_STORE_RESULT);
    if ($resultData2 !== false && $resultData2->num_rows > 0) {
        $rowData2 = $resultData2->fetch_object();
        $generalVendorId = $rowData2->general_vendor_id;
        $gvEmail = $rowData2->vendor_email;
        $gvBankId = $rowData2->gv_bank_id;
    }

    if($invId != '' || $invId != 0){
        $sqlInv = "SELECT DATE_FORMAT(input_date, '%d/%m/%Y') AS inputDate, invoice_status, payment_status FROM invoice where invoice_id =  {$invId}";
        $resultInv = $myDatabase->query($sqlInv, MYSQLI_STORE_RESULT);
        $rowInv = $resultInv->fetch_object();
        $inputDate = $rowInv->inputDate;
        $invStatus = $rowInv->invoice_status;
        $paymentStatus = $rowInv->payment_status;

        if(($pStatus == 3 || $pStatus == 1) && ($invStatus == 1 || $invStatus == 4)  && ($paymentStatus == 1 || $paymentStatus == 2 || $paymentStatus == 0)){
            $disableProperty = 'disabled';
        }

        if(($pStatus == 3 && $invStatus == 1 && ($paymentStatus == 1 || $paymentStatus == 2))){
            $disableProperty1 = 'disabled';
        }
    }
    // </editor-fold>

} else {
    $generatedInvoiceNo = "";
    if (isset($_SESSION['invoice'])) {
        $invoiceTax = $_SESSION['invoice']['invoiceTax'];
        $invoiceDate = $_SESSION['invoice']['invoiceDate'];
        $inputDate = $_SESSION['invoice']['inputDate'];
        $requestDate = $_SESSION['invoice']['requestDate'];
        $taxDate = $_SESSION['invoice']['taxDate'];
        $generatedInvoiceNo = $_SESSION['invoice']['generatedInvoiceNo'];
        $generatedInvoiceNo2 = $_SESSION['invoice']['generatedInvoiceNo2'];
        $stockpileId = $_SESSION['invoice']['stockpileId'];
        $stockpileContractId3 = $_SESSION['invoice']['stockpileContractId3'];
        $remark = $_SESSION['invoice']['remark'];
        $generalVendorId2 = $_SESSION['generalVendorId2']['remark'];
    }
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

        $('#printVehicleData').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#viewVehicleModalForm").printThis();
//            $("#transactionContainer").hide();
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
                            $('#pageContent').load('views/register-invoice-general.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $('#invoiceMethod').change(function() {
            // resetVendorBankDetail ($('select[id="paymentFor"]').val());
            if(document.getElementById('invoiceMethod').value == 2 || document.getElementById('invoiceMethod').value == 1) {
                setMethod($('select[id="invoiceMethod"]').val(), $('input[id="pgId"]').val());
                    
            } 
        });
    });

    function setMethod(invoiceMethod, pgId) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'setMethod',
                        invoiceMethod: invoiceMethod,
                        pgId: pgId
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        $('#dataContent').load('forms/approve-pengajuan-general.php', {pgId: pgId}, iAmACallbackFunction2);
                    }
                }
            });
	}
</script>

<script type="text/javascript">
    $(document).ready(function () {
        getPengajuanDetail();
    });
    $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
             if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_vendor_invoice',
                                    InvoiceDetailId: menu[2]
                            },
                            success: function(data){
                                var returnVal = data.split('|');
                                if(parseInt(returnVal[3])!=0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                    if(returnVal[1] == 'OK') {
                                       $('#dataContent').load('forms/approve-pengajuan-general.php', {}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            }
        });	

    <?php if(isset($transaksiMutasi) && $transaksiMutasi == 1  && ($typeOKS == 2 || $typeOKS == 1)){ ?>
        $('#generalVendorIdLabel').show();
        $('#vendorBankIdLabel').show();
        $('#generalVendorEmail').show();
        document.getElementById('AddData').classList.remove("hidden");
        document.getElementById('AddMutasi').classList.add("hidden");
        document.getElementById('view_vehicle').classList.add("hidden");

    <?php }elseif(isset($transaksiMutasi) && $transaksiMutasi == 2){ ?>
    $('#generalVendorIdLabel').hide();
    $('#generalVendorEmail').hide();
    $('#vendorBankIdLabel').hide();
        document.getElementById('AddMutasi').classList.remove("hidden");
        document.getElementById('AddData').classList.add("hidden");
        document.getElementById('view_vehicle').classList.add("hidden");
    <?php } else if(isset($transaksiMutasi) && $transaksiMutasi == 1 && $typeOKS == 3);{?>
        $('#generalVendorIdLabel').show();
        $('#vendorBankIdLabel').show();
        $('#generalVendorEmail').show();
        document.getElementById('AddData').classList.add("hidden");
        document.getElementById('AddMutasi').classList.add("hidden");
        document.getElementById('view_vehicle').classList.remove("hidden");

    <?php } ?>
    $('#transaksiMutasi').change(function () {
        if (document.getElementById('transaksiMutasi').value == 2) {
            $('#generalVendorIdLabel').hide();
            $('#generalVendorEmail').hide();
            $('#vendorBankIdLabel').hide();
            // $('#AddData').hide();
            // $('#AddMutasi').show();

            document.getElementById('AddMutasi').classList.remove("hidden");
            document.getElementById('AddData').classList.add("hidden");

        } else {
            $('#generalVendorIdLabel').show();
            $('#vendorBankIdLabel').show();
            $('#generalVendorEmail').show();
            // $('#AddMutasi').hide()

            document.getElementById('AddData').classList.remove("hidden");
            document.getElementById('AddMutasi').classList.add("hidden");
        }
        insertTemp();
        
    });

    function getPengajuanDetail() {
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'get_pengajuan_general_detail',
                pgId: document.getElementById('pgId').value,
                method: document.getElementById('invoiceMethod').value,
                mutasi:  document.getElementById('transaksiMutasi').value,
                typeOKS:  document.getElementById('typeOKS').value

            },
            success: function (data) {
                if (data != '') {
                    $('#invoiceDetail').show();
                    document.getElementById('invoiceDetail').innerHTML = data;
                } else {
                    $('#invoiceDetail').hide();
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
                            getPengajuanDetail();
                        }
                    }
                });
            }
            return false;
        });
    }

    function deletePGDetail(pgd_id) {
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'delete_pg_detail',
                pgd_id: pgd_id
                /*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
                    paymentFrom: paymentFrom,
                    paymentTo: paymentTo */
            },
            success: function (data) {
                if (data != '') {
                    getPengajuanDetail();
                }
            }
        });
    }

    function editDetail(pgdId, method, typeOKS) {
        $("#modalErrorMsg").hide();
        $('#accountModal').modal('show');
        //            alert($('#addNew').attr('href'));
        $('#accountModalForm').load('forms/pengajuan-general-edit-detail.php', {pgdId: pgdId, method: method, typeOKS: typeOKS}, iAmACallbackFunction2);	//and hide the rotating gif
    }

    $("#accountForm").validate({
        submitHandler: function (form) {
            $.ajax({
                url: './irvan.php',
                method: 'POST',
                data: $("#accountForm").serialize(),
                success: function (data) {
                    console.log(data);
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        if (returnVal[1] == 'OK') {
                            alertify.set({
                                labels: {
                                    ok: "OK"
                                }
                            });
                            alertify.alert(returnVal[2]);
							alert("OKE-2");
                            getPengajuanDetail();
                            $('#accountModal').modal('hide');
                        } else {
                            document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                            $("#modalErrorMsgInsert").show();
                        }
                    }
                }
            });
        }
    });

    function rejectPG() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'pengajuan_general_data',
                _method: 'REJECT',
                pgId: document.getElementById('pgId').value,
                rejectRemarks: document.getElementById('reject_remarks').value,
                typeOKS: document.getElementById('typeOKS').value
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
                        $('#pageContent').load('views/register-invoice-general.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function cancelPG() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'pengajuan_general_data',
                _method: 'RETURNED_INV',
                pgId: document.getElementById('pgId').value,
                invId: document.getElementById('invId').value,
                rejectRemarks: document.getElementById('reject_remarks').value,
                returnDate: document.getElementById('returnDate').value


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
                        $('#pageContent').load('views/register-invoice-general.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function jurnalTest() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: 'action=jurnal_accrue',
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
                        $('#pageContent').load('tabs/approve-pengajuan-general-data.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function jurnalTestInv() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: 'action=jurnal_invoice',
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
                        $('#pageContent').load('views/register-invoice-general.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function insertTemp() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'temp_pengajuan_general_detail',
                pgId: document.getElementById('pgId').value,
                transaksiMutasi: document.getElementById('transaksiMutasi').value

            },
            success: function (data) {
                getPengajuanDetail();
            }
        });
    }

</script>
<script type="text/javascript">
    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });

        $('#showTransaction').click(function (e) {
            if (document.getElementById('generalVendorId').value != '' && document.getElementById('gvBankId').value != '') {
                e.preventDefault();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/pengajuan-general-data.php', {
                    pgId: document.getElementById('pgId').value,
                    generalVendorId: document.getElementById('generalVendorId').value,
                    gvBankId: document.getElementById('gvBankId').value,
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
                transaksiMutasi: document.getElementById('transaksiMutasi').value
            });

        });

        $('#show_view_vehicle').click(function (e) {
            e.preventDefault();
            $('#showPreviewVehicle').modal('show');
            $('#viewVehicleModalForm').load('forms/vehicle_list_oks_akt.php', {
                pgId: document.getElementById('pgId').value,
                generalVendorId: document.getElementById('generalVendorId').value
            });

        });

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
            amount: "required"
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
            amount: "Amount is a required field."
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
							alert("OKE");
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

    $("#PreviewVehicleForm").validate({
        submitHandler: function(form) {
		    $("#modalErrorMsg").hide();
			$('#showPreviewVehicle').modal('hide');
				
			$.blockUI({ message: '<h4>Please wait...</h4>' });
			$('#loading').css('visibility','visible');
                
                $.ajax({
                    url: './irvan.php',
                    method: 'POST',
                    data: $("#PreviewVehicleForm").serialize(),
                    success: function(data) {
                       
					   var returnVal = data.split('|');
							if (parseInt(returnVal[3]) != 0){	//if no errors
                        
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                        }
                    }
                });
		    $('#loading').css('visibility','hidden');
        }
    });
</script>

<form method="post" id="PengajuanDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_general_data">
    <input type="hidden" name="pgId" id="pgId" value="<?php echo $pgId; ?>">
    <input type="hidden" name="invId" id="invId" value="<?php echo $invId; ?>">
    <input type="hidden" name="typeOKS" id="typeOKS" value="<?php echo $typeOKS; ?>">
    
    <input type="hidden" name="_method" id="_method" value="APPROVE">
    <!-- <div class="row-fluid">
        <?php if($invStatus == 2 || $pStatus == 2 ) { ?>
            <div class="span12 lightblue">
                <label style="color: red;"><center><b>RETURNED/CANCELED</center></b></label>
            </div>
            <hr>
        <?php } ?>
    </div> -->

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Generated Invoice No.</label>
            <input type="text" class="span12" readonly id="generatedInvoiceNo" name="generatedInvoiceNo"
                   value="<?php echo $generatedInvoiceNo; ?>">
        </div>

        <div class="span1 lightblue">
        </div>

        <div class="span3 lightblue">
            <label>Input Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="inputDate" name="inputDate"
                   value="<?php echo $inputDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Request Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" readonly>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate"
                   value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" readonly>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Original Invoice No.</label>
            <input type="text" class="span12" tabindex="" id="generatedInvoiceNo2" name="generatedInvoiceNo2"
                   value="<?php echo $generatedInvoiceNo2; ?>" readonly>

        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
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
                   value="<?php echo $invoiceTax; ?>" readonly>
        </div>
        <div class="span1 lightblue">

        </div>
        <div class="span3 lightblue">
            <label>Tax Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="taxDate" name="taxDate"
                   value="<?php echo $taxDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" readonly>
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Invoice Method <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Full Payment' as info UNION
              		   SELECT '2' as id, 'Down Payment' as info;", $invoiceMethod, "", "invoiceMethod", "id", "info", "", "", "select2combobox100", 1);
            ?>
            <!-- <input type="text" value="Full Payment" readonly>
            <input type="hidden" value="1" id="invoiceMethod"> -->
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

    <div id="notPoTransaction" class="row-fluid" style="margin-bottom: 25px;">
        <div class="span3 lightblue" id="transaksiMutasiLabel">
            <label>Transaksi Mutasi ? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", $transaksiMutasi, "", "transaksiMutasi", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>

        <div class="span3 lightblue" id="generalVendorIdLabel" style="display: none">
            <label>Vendor<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, "", "generalVendorId", "general_vendor_id", "general_vendor_name",
                "", "", "select2combobox100", 1, "", true);
            ?>
        </div>
        <div class="span3 lightblue" id="generalVendorEmail" style="display: none" >
            <label>Vendor Email<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="gvEmail" name="gvEmail" value=<?php echo $gvEmail ?>>
        </div>

        <?php if ($gvBankId != '') { ?>
            <div class="span3 lightblue" id="vendorBankIdLabel" style="display: none">
                <label>Vendor Bank<span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT gv_bank_id, CONCAT(bank_name,' - ',account_no) AS bank_name FROM general_vendor_bank WHERE general_vendor_id = {$generalVendorId}", "$gvBankId", "", "gvBankId", "gv_bank_id", "bank_name",
                    "", "", "select2combobox100", 1, "", true);
                ?>
            </div>
        <?php } else { ?>
            <div class="span3 lightblue" id="vendorBankIdLabel" style="display: none">
                <label>Vendor Bank <span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "gvBankId", "gv_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);
                ?>
            </div>
        <?php } ?>
    </div>

    <div id="AddData" class="row-fluid hidden" style="margin-bottom: 7px; display: none;">
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
    
    <div id="view_vehicle" class="row-fluid hidden" style="margin-bottom: 7px; display: none;">
        <div class="span3 lightblue">
            <button type="button" class="btn btn-warning" id="show_view_vehicle">View Vehicle</button>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
        </div>
        <div class="span1 lightblue">
        </div>
    </div>

    <div class="row-fluid" id="invoiceDetail" style="display: none;">
        Pengajuan Detail
    </div>

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject Remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $rejectRemarks; ?></textarea>
    </div>
</div>

<?php if($invStatus == 1 || $invStatus == 4){ ?>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Return Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="returnDate" name="returnDate"
                    value="<?php echo $returnDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
    </div>
<?php } ?>

<div class="row-fluid">
    <div class="span12 lightblue">
    <?php if($invStatus == 1 || $invStatus == 4){ ?>
        <button class="btn btn-danger" <?php echo $disableProperty1; ?> onclick="cancelPG()">RETURN</button>
    <?php }else if($pStatus == 0 || ($pStatus == 3 && $invStatus == 2) || ($pStatus == 1 && $invStatus == 2)){ ?>
        <button class="btn btn-warning" <?php echo $disableProperty1; ?> onclick="rejectPG()">REJECT</button>
    <?php } ?>
    <?php if($_SESSION['userId'] == 200) { ?>
        <button class="btn btn-warning" id="jurnalInvNotim" onclick="jurnalTest()">J-Accrue</button>
        <button class="btn btn-warning" id="jurnalInvoice" onclick="jurnalTestInv()">J-INV</button>
    <?php } ?>
    </div>
</div>

<div id="accountModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form id="accountForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="accountModalLabel">Edit Pengajuan Detail</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="accountModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

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

<!-- PREVIEW VEHICLE -->
<div id="showPreviewVehicle" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="PreviewVehicleLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;" 
    data-keyboard="false" data-backdrop="static">
        <form id="PreviewVehicleForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
                <h3 id="insertModalLabel">Preview Vehicle Data</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
                Error Message
            </div>
			<div class="modal-body" id="viewVehicleModalForm">
                
            </div>
			
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
				<button class="btn btn-primary" id="printVehicleData">Preview</button>
                <!--<button class="btn" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">Close</button>
                <button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
</div>