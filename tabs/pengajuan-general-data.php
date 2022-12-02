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
$idPOHDR = '';
$transaksiPO = '';
$paymentType = '';
$requestPaymentDate = '';
$termin = '';
$sisaTermin = '';
$transaksiMutasi = 1;
$remarks_reject = '';
$gvBankId = '';
$gvEmail = '';
$style = '';
$periodeFrom = '';
$periodeTo = '';
$downPayment = '';
// $priceOKS = '';

// </editor-fold>
if (isset($_POST['idPOHDR']) && $_POST['idPOHDR'] != '') {
    $termin = $_POST['termin'];
    $idPOHDR = $_POST['idPOHDR'];

    $transaksiPO = $_POST['transaksiPO'];
    $transaksiOKSAKT = $_POST['transaksiOKSAKT'];
    $paymentType = $_POST['paymentType'];
    $requestPaymentDate = $_POST['requestPaymentDateValue'];
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT p.*, pd.termin, DATE_FORMAT(p.tanggal,'%d/%m/%Y') as date ,s.*FROM po_hdr p 
            LEFT JOIN stockpile s ON p.stockpile_id = s.stockpile_id 
            LEFT JOIN po_detail pd ON p.idpo_hdr = pd.po_hdr_id 
            WHERE idpo_hdr = $idPOHDR";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $requestDate = $rowData->date;
        $stockpile = $rowData->stockpile_name;
        $stockpileId = $rowData->stockpile_id;
        $noPO = $rowData->no_po;
        $termin = $rowData->termin;
    }
    // </editor-fold>

    $sqlPODetail = "SELECT CONCAT(pd.qty,' ',u.uom_type) as qty, harga, keterangan, pd.amount, i.item_name,
			(case when pd.pphstatus = 1 then pd.pph else 0 end) as pph,
			(case when pd.ppnstatus = 1 then pd.ppn else 0 end) as ppn,
    		(pd.amount+(case when pd.ppnstatus = 1 then pd.ppn else 0 end)-(case when pd.pphstatus = 1 then pd.pph else 0 end)) as grandtotal,
            u.uom_type,s.`stockpile_name`, sh.`shipment_no`,sum(id.tamount_converted) as paid, sum(pgd.termin) as total_termin, pd.idpo_detail, pd.termin
			from po_detail pd
			left join master_item i on i.idmaster_item = pd.item_id
            left join uom u on u.idUOM = i.uom_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
            LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
            LEFT JOIN pengajuan_general_detail pgd ON pgd.po_detail_id = pd.idpo_detail AND pgd.status != 3 # 3=cancel kalo ada PO nya
            -- INNER JOIN pengajuan_general pg ON pg.`pengajuan_general_id` = pgd.`pg_id` AND pg.`status_pengajuan` != 2
            LEFT JOIN invoice_detail id ON id.pgd_id = pgd.pgd_id
			WHERE no_po = '{$noPO}' GROUP BY i.item_name,pd.notes ORDER BY pd.entry_date ASC";
    $resultPODetail = $myDatabase->query($sqlPODetail, MYSQLI_STORE_RESULT);
    // echo " test <br> <br> " .$sqlPODetail;

    $sqlNew = "SELECT CONCAT(pd.qty,' ',u.uom_type) as qty, harga, keterangan, pd.amount, i.item_name,
			(case when pd.pphstatus = 1 then pd.pph else 0 end) as pph,
			(case when pd.ppnstatus = 1 then pd.ppn else 0 end) as ppn,
    		(pd.amount+(case when pd.ppnstatus = 1 then pd.ppn else 0 end)-(case when pd.pphstatus = 1 then pd.pph else 0 end)) as grandtotal,
            u.uom_type,s.`stockpile_name`, sh.`shipment_no`,sum(id.tamount_converted) as paid, sum(pgd.termin) as total_termin
			from po_detail pd
			left join master_item i on i.idmaster_item = pd.item_id
            left join uom u on u.idUOM = i.uom_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
            LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
            LEFT JOIN pengajuan_general_detail pgd ON pgd.po_detail_id = pd.idpo_detail
			LEFT JOIN pengajuan_general pg ON pg.pengajuan_general_id = pgd.pg_id
            LEFT JOIN invoice_detail id ON id.pgd_id = pgd.pgd_id
			WHERE no_po = '{$noPO}' AND pg.status_pengajuan IN(0,1)  GROUP BY i.item_name,pd.notes LIMIT 1";

    $poDetail = $myDatabase->query($sqlNew, MYSQLI_STORE_RESULT);

    $sisaTermin = 100;
    while ($rowNew = $poDetail->fetch_object()) {
        $sisaTermin = 100 - $rowNew->total_termin;
    }

}
// If ID is in the parameter
if (isset($_POST['pgId']) && $_POST['pgId'] != '') {

    $pgId = $_POST['pgId'];

    $readonlyProperty = ' readonly ';

    // <editor-fold defaultstate="collapsed" desc="Query for Pengajuan General">

    $sql = "SELECT pg.*, DATE_FORMAT(pg.invoice_date, '%d/%m/%Y') AS invoice_date2, 
            DATE_FORMAT(pg.input_date, '%d/%m/%Y') AS input_date, DATE_FORMAT(pg.request_date, '%d/%m/%Y') AS request_date, DATE_FORMAT(pg.tax_date, '%d/%m/%Y') AS tax_date,
            DATE_FORMAT(pg.request_payment_date, '%d/%m/%Y') AS request_payment_date,
            DATE_FORMAT(pg.periode_from, '%d/%m/%Y') AS periodeFrom,
            DATE_FORMAT(pg.periode_to, '%d/%m/%Y') AS periodeTo
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
        $generatedInvoiceNo = $rowData->pengajuan_no;
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
        $transaksiOKSAKT = $rowData->transaksi_oks_akt;
        $vendorId = $rowData->vendor_id;
        $periodeFrom = $rowData->periodeFrom;
        $periodeTo = $rowData->periodeTo;
        // $priceOKS = $rowData->total_price;

        if($rowData->status_pengajuan == 4){
            $disabledProperty = '';
        }else{
            $disabledProperty = ' disabled ';
        }

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
    }else if($empty == 5){
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

        
        // $('#priceOKS').number(true, 2);
        
        <?php
        if($generatedInvoiceNo == "" &&  $pgId == "") {
        ?>
        // if(document.getElementById('generalVendorId').value != "") {
        $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: {
                action: 'getPengajuanNo'
                //stockpileContractId: stockpileContractId,
                //paymentMethod: paymentMethod,
                //ppn: ppnValue,
                //pph: pphValue
            },
            success: function (data) {
                if (data != '') {
                    document.getElementById('generatedInvoiceNo').value = data;
                }
                //setInvoiceType(generatedInvoiceNo);
            }
        });
        <?php } ?>
        //setInvoiceType(generatedInvoiceNo);

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

                $('#dataContent').load('forms/pengajuan-general.php', {
                    idPOHDR: document.getElementById('idPOHDR').value,
                    transaksiPO: document.getElementById('transaksiPO').value,
                    termin: document.getElementById('termin').value,
                    transaksiOKSAKT: document.getElementById('transaksiOKSAKT').value,
                    paymentType: document.getElementById('paymentType').value,
                    requestPaymentDateValue: document.getElementById('requestPaymentDateValue').value
                }, iAmACallbackFunction2);

                if (document.getElementById('paymentType').value == 1) {
                    $('#requestPaymentDate').show();
                    $("#requestPaymentDateValue").prop("disabled", false).val("");
                    $("#requestPaymentDateValue2").prop("disabled", false);

                }else if(document.getElementById('paymentType').value == 0){
                    $('#requestPaymentDate').show();
                    $("#requestPaymentDateValue").prop("disabled", true);
                    $("#requestPaymentDateValue2").prop("disabled", false);
                    $.ajax({
                        url: 'get_data.php',
                        method: 'POST',
                        data: {
                            action: 'setPlanPayDateGeneral',
                        },
                        success: function (data) {
                        $('#requestPaymentDateValue').val(data)
                        $('#requestPaymentDateValue2').val(data)
                        }
                    });
                } else if(document.getElementById('paymentType').value == "") {
                    $('#requestPaymentDate').hide();
                    $("#requestPaymentDateValue").prop("disabled", true).val("");
                }

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif
            } else {
                document.getElementById('row2').classList.add("hidden");
                document.getElementById('row3').classList.add("hidden");
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
                    requestPaymentDate: "required",
                    transaksiOKSAKT: "required"
                    // file: {required: true, filesize: 1048576}
                },
                messages: {
                    transaksiPO: "Transaction PO is a required field.",
                    paymentType: "Type is a required field.",
                    requestPaymentDateValue: "Request Payment date  is a required field.",
                    requestPaymentDate: "Request payment date is a required field.",
                    transaksiOKSAKT: "OKS AKT Transaction  is a required field.",
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
                    transaksiMutasi: document.getElementById('transaksiMutasi').value,
                    oksAktId: document.getElementById('transaksiOKSAKT').value,
                    stockpileId: document.getElementById('stockpileId').value,
                    pksId: document.getElementById('pksId').value,
                    transaksiPO: document.getElementById('transaksiPO').value

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
                pgId: document.getElementById('pgId').value,
                transactionType: document.getElementById('transaksiMutasi').value,
                generalVendorId: document.getElementById('generalVendorId').value

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

    function deleteInvoiceDetail(pgdId, pgId) {
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
                        pgdId: pgdId, 
                        pgId: pgId
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

    function editDetail(pgdId,transaksiMutasi,generalVendorId, pgId) {
        $("#modalErrorMsg").hide();
        $('#insertModal').modal('show');
        $('#insertModalForm').load('forms/pengajuan-general-data.php', {
            pgdId: pgdId,
            pgId: pgId,
            transaksiMutasi: transaksiMutasi,
            generalVendorId: generalVendorId
        }, iAmACallbackFunction2);	//and hide the rotating gif
    }

    function setPengajuanGeneralDetail() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPengajuanGeneralDetail',
                transactionType: document.getElementById('transaksiMutasi').value,
                generalVendorId: document.getElementById('generalVendorId').value

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

    function cancelPG() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'pengajuan_general_data',
                _method: 'CANCEL',
                pgId: document.getElementById('pgId').value,
                rejectRemarks: document.getElementById('reject_remarks').value,
                typeOKS: document.getElementById('transaksiOKSAKT').value
            },
            success: function (data) {
                // console.log(data);
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
            }
        });
    }
</script>

<script type="text/javascript">
    $(function () {
        // $('#transaksiPO').val('1');

        // Session Storage Browser

        // Object.keys(sessionStorage).forEach((key) => {
        // var newKey = key.split('.');
        //  if(newKey[1] !=""){
        //     document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
        //  }
        // });
        // console.log(sessionStorage.getItem("pengajuanGeneral.invoiceTax"));
        // $(":input").change(function(){
        //     sessionStorage.setItem("pengajuanGeneral."+this.id, this.value);
        // });

        //https://github.com/eternicode/bootstrap-datepicker
   

        <?php if(isset($transaksiMutasi) && $transaksiMutasi == 1 ){ ?>
        $('#generalVendorIdLabel').show();
        $('#generalVendorEmail').show();
        $('#generalUserEmail').show();
        
        $('#vendorBankIdLabel').show();
        $('#AddData').show();
        // document.getElementById('AddData').classList.remove("hidden");
        // document.getElementById('AddMutasi').classList.add("hidden");

        <?php }elseif(isset($transaksiMutasi) && $transaksiMutasi == 2){ ?>
        $('#generalVendorIdLabel').hide();
        $('#generalVendorEmail').hide();
        $('#generalUserEmail').hide();
        $('#vendorBankIdLabel').hide();
        // document.getElementById('AddMutasi').classList.remove("hidden");
        // document.getElementById('AddData').classList.add("hidden");
        <?php } ?>

        <?php if(isset($transaksiPO) && ($transaksiPO == 1 && $transaksiOKSAKT == 1) || ($transaksiPO == 2 && $transaksiOKSAKT == 2) ) { ?>
        document.getElementById('row2').classList.remove("hidden");
        document.getElementById('row3').classList.remove("hidden");
        document.getElementById('AddData').classList.remove("hidden");
        $('#notPoTransaction').show();
        $('#notPoTransaction2').show();
        $('#poHDRLabel').hide();
        $('#poHDRLabel2').hide();
        $('#poDetail').hide();
        $('#requestPaymentDate').show();

        getVendor(1, <?php echo $transaksiOKSAKT ?>, 0, <?php echo $generalVendorId ?>);
                
        <?php } elseif(isset($transaksiPO) && $transaksiPO == 2 && $transaksiOKSAKT == 1) { ?>
            $('#poHDRLabel').show();
            $('#poHDRLabel2').show();
            $('#poDetail').show();
            $('#notPoTransaction').hide();
            $('#notPoTransaction2').hide();
            document.getElementById('AddData').classList.add("hidden");
            document.getElementById('row2').classList.remove("hidden");
            document.getElementById('row3').classList.remove("hidden");
            $('#requestPaymentDate').show();

        <?php } else if(isset($transaksiPO) && $transaksiOKSAKT == 3){?>
                $('#AddData').hide();
                $('#slipVehicle').hide();
                $('#OKSothersLabels').show();
                $('#vendorBankIdLabel').hide(); 
                $('#requestPaymentDate').show();

                <?php $style = 'style="margin-right: 25px;"'; ?>
                $('#transaksiOKSAKT').find('option').each(function(i,e){
                    if($(e).val() == 3){
                        $('#transaksiOKSAKT').prop('selectedIndex',i);        
                            $("#transaksiOKSAKT").select2({
                                width: "100%",
                                placeholder: 3
                            });
                    }
                });


                $('#oksOthersLabel').show();
                // $('#priceOKSLabel').show(); 
                $('#poHDRLabel').hide();
                $('#poHDRLabel2').hide();
                $('#poDetail').hide();
                $('#notPoTransaction').show();
                $('#notPoTransaction2').show();
                document.getElementById('row1').classList.add("hidden");
                document.getElementById('row2').classList.remove("hidden");
                document.getElementById('row3').classList.remove("hidden");
                //$('#AddData').hide();
               $('#slipVehicle').hide();
                $('#transaksiMutasiLabel').hide(); 
                $('#pksNameLabel').show(); 
                $('#periodefrom').prop('readonly', true);
                $('#periodeto').prop('readonly', true);
                // $('#priceOKS').prop('readonly', true);
                getVendor(1, <?php echo $transaksiOKSAKT ?>, <?php echo $vendorId ?>, <?php echo $generalVendorId ?>);
                setPKSOks(1,<?php echo $vendorId ?>);
               getVendorBank(1, <?php echo $generalVendorId ?>, <?php echo $gvBankId ?>);
            //    $('#vendorId option:not(:selected)').prop('disabled', true);
        <?php } ?>

        <?php if(isset($paymentType) && $paymentType == 1) { ?>
        $('#requestPaymentDate').show();
        <?php } ?>

        $('#paymentType').change(function () {
            // console.log(this.value)
            if (this.value == 1) {
                $('#requestPaymentDate').show();
                $("#requestPaymentDateValue").prop("disabled", false).val("");
                $("#requestPaymentDateValue2").prop("disabled", true);

            }else if(this.value == 0){
                $('#requestPaymentDate').show();
                $("#requestPaymentDateValue").prop("disabled", true);
                $("#requestPaymentDateValue2").prop("disabled", false);
                $.ajax({
                    url: 'get_data.php',
                    method: 'POST',
                    data: {
                        action: 'setPlanPayDateGeneral',
                    },
                    success: function (data) {
                    $('#requestPaymentDateValue').val(data)
                    $('#requestPaymentDateValue2').val(data)
                    }
                });
            } else if(this.value == "") {
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

                    $('#dataContent').load('forms/pengajuan-general.php', {
                        idPOHDR: document.getElementById('idPOHDR').value,
                        transaksiPO: document.getElementById('transaksiPO').value,
                        termin: document.getElementById('termin').value,
                        transaksiOKSAKT: document.getElementById('transaksiOKSAKT').value,
                        paymentType: document.getElementById('paymentType').value,
                        requestPaymentDateValue: document.getElementById('requestPaymentDateValue').value
                    }, iAmACallbackFunction2);

                    if (document.getElementById('paymentType').value == 1) {
                        $('#requestPaymentDate').show();
                        $("#requestPaymentDateValue").prop("disabled", false).val("");
                        $("#requestPaymentDateValue2").prop("disabled", false);

                    }else if(document.getElementById('paymentType').value == 0){
                        $('#requestPaymentDate').show();
                        $("#requestPaymentDateValue").prop("disabled", true);
                        $("#requestPaymentDateValue2").prop("disabled", false);
                        $.ajax({
                            url: 'get_data.php',
                            method: 'POST',
                            data: {
                                action: 'setPlanPayDateGeneral',
                            },
                            success: function (data) {
                            $('#requestPaymentDateValue').val(data)
                            $('#requestPaymentDateValue2').val(data)
                            }
                        });
                    } else if(document.getElementById('paymentType').value == "") {
                        $('#requestPaymentDate').hide();
                        $("#requestPaymentDateValue").prop("disabled", true).val("");
                    }

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

        $('#transaksiOKSAKT').change(function () {
            if (this.value == 2) {
                $('#AddData').show();
                $('#slipVehicle').hide();
                $('#OKSothersLabels').hide();
                getVendor(0, $('select[id="transaksiOKSAKT"]').val(), 0);

                $('#transaksiPO').find('option').each(function(i,e){
                    if($(e).val() == 2){
                        $('#transaksiPO').prop('selectedIndex',i);        
                            $("#transaksiPO").select2({
                                width: "100%",
                                placeholder: 2
                            });
                    }
                });


                if(document.getElementById('transaksiPO').value == 2 ){ //OKS AKT
                    document.getElementById('AddData').classList.remove("hidden");
                    document.getElementById('row1').classList.add("hidden");
                    document.getElementById('row2').classList.remove("hidden");
                    document.getElementById('row3').classList.remove("hidden");
                    $('#poHDRLabel').hide();
                    $('#poHDRLabel2').hide();
                    $('#poDetail').hide();
                    $('#notPoTransaction').show();
                    $('#notPoTransaction2').show();
                    $('#oksOthersLabel').hide();
                    // $('#priceOKSLabel').hide(); 
                    $('#transaksiMutasiLabel').show(); 
                    $('#pksNameLabel').hide(); 
                    $('#vendorBankIdLabel').show(); 
                }
                $('#transaksiPO option:not(:selected)').attr('disabled', true);
                
            }else if(this.value == 3){ //OKS OTHERS
                $('#transaksiPO option:not(:selected)').attr('disabled', false);
                $('#AddData').hide();
                $('#slipVehicle').hide();
                $('#OKSothersLabels').show();
                $('#vendorBankIdLabel').hide(); 

                <?php $style = 'style="margin-right: 25px;"'; ?>
                $('#transaksiPO').find('option').each(function(i,e){
                    if($(e).val() == 1){
                        $('#transaksiPO').prop('selectedIndex',i);        
                            $("#transaksiPO").select2({
                                width: "100%",
                                placeholder: 1
                            });
                    }
                });

                $('#oksOthersLabel').show();
                // $('#priceOKSLabel').show(); 
                $('#poHDRLabel').hide();
                $('#poHDRLabel2').hide();
                $('#poDetail').hide();
                $('#notPoTransaction').show();
                $('#notPoTransaction2').show();
                document.getElementById('row1').classList.add("hidden");
                document.getElementById('row2').classList.remove("hidden");
                document.getElementById('row3').classList.remove("hidden");
                $('#AddData').hide();
                $('#slipVehicle').hide();
                $('#transaksiMutasiLabel').hide(); 
                $('#pksNameLabel').show(); 
                getVendor(0, $('select[id="transaksiOKSAKT"]').val(), 0);
                setPKSOks(0,0);
                $('#transaksiPO option:not(:selected)').attr('disabled', true);
                
            }else if(this.value == 1){ 
                $('#transaksiPO option:not(:selected)').attr('disabled', false);
                $('#OKSothersLabels').hide(); 
                $('#vendorBankIdLabel').show(); 
                $('#transaksiMutasiLabel').show(); 
                $('#slipVehicle').hide();
                $('#AddData').show();
                $('#oksOthersLabel').hide();
                // $('#priceOKSLabel').hide(); 
                $('#pksNameLabel').hide(); 
                $('#transaksiPO').find('option').each(function(i,e){
                    if($(e).val() == 0){
                        $('#transaksiPO').prop('selectedIndex',i);        
                            $("#transaksiPO").select2({
                                width: "100%",
                                placeholder: 0
                            });
                    }
                });
                getVendor(0, $('select[id="transaksiOKSAKT"]').val(), 0);
              //  $('#transaksiPO option:not(:selected)').attr('disabled', false);
            }
        });

        $('#transaksiPO').change(function () {
            // document.getElementById('AddMutasi').classList.add("hidden");
            // document.getElementById('transaksiMutasi').value = 0;
            if (document.getElementById('transaksiPO').value == 2 && document.getElementById('transaksiOKSAKT').value == 1 ) {
                document.getElementById('AddData').classList.add("hidden");
                document.getElementById('row1').classList.remove("hidden");
                $('#poHDRLabel').show();
                $('#poHDRLabel2').show();
                $('#poDetail').show();
                $('#notPoTransaction').hide();
                $('#notPoTransaction2').hide();
            } else if((document.getElementById('transaksiPO').value == 2 && 
                        document.getElementById('transaksiOKSAKT').value == 2) || 
                      (document.getElementById('transaksiPO').value == 1 && 
                        document.getElementById('transaksiOKSAKT').value == 1))
            {
                document.getElementById('AddData').classList.remove("hidden");
                document.getElementById('row1').classList.add("hidden");
                document.getElementById('row2').classList.remove("hidden");
                document.getElementById('row3').classList.remove("hidden");
                $('#poHDRLabel').hide();
                $('#poHDRLabel2').hide();
                $('#poDetail').hide();
                $('#notPoTransaction').show();
                $('#notPoTransaction2').show();

            }
        });
    });

    $('#vendorId').change(function () {
        if(document.getElementById('vendorId').value != 0){
            getVendor(0, $('select[id="transaksiOKSAKT"]').val(),  $('select[id="vendorId"]').val());
        }
    });

    $('#generalVendorId').change(function () {
        getVendorBank(0, $('select[id="generalVendorId"]').val(), 0);
        getVendorEmail($('select[id="generalVendorId"]').val());
        if(document.getElementById('transaksiOKSAKT').value == 3){
            setSlipVehicle($('select[id="stockpileId"]').val(), '', $('input[id="periodefrom"]').val(), $('input[id="periodeto"]').val(), $('select[id="vendorId"]').val(),  $('select[id="generalVendorId"]').val());
            // validate_price_oks($('select[id="vendorId"]').val(), $('select[id="generalVendorId"]').val(), $('input[id="priceOKS"]').val());
            // if(document.getElementById('priceOKS').value > 0 && document.getElementById('stockpileId').value != ''){
            // }
        }
        
    });

    // $('#priceOKS').change(function () {
    //     if(document.getElementById('vendorId').value != '' || document.getElementById('generalVendorId').value != ''){
    //         validate_price_oks($('select[id="vendorId"]').val(), $('select[id="generalVendorId"]').val(), $('input[id="priceOKS"]').val());
    //     }
    // });

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
                        if(document.getElementById('transaksiOKSAKT').value != 3){
                            document.getElementById('gvBankId').options.length = 0;
                        }else{
                            document.getElementById('gvBankId2').options.length = 0;
                        }
                        
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        if(document.getElementById('transaksiOKSAKT').value != 3){
                            document.getElementById('gvBankId').options.add(x);
                        }else{
                            document.getElementById('gvBankId2').options.add(x);
                        }
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('transaksiOKSAKT').value != 3){
                            document.getElementById('gvBankId').options.add(x);
                        }else{
                            document.getElementById('gvBankId2').options.add(x);
                        }
                    }

                    if (type == 1) {
                        if(document.getElementById('transaksiOKSAKT').value != 3){
                            $('#gvBankId').find('option').each(function(i,e){
                                if($(e).val() == gvBankId){
                                    $('#gvBankId').prop('selectedIndex',i);        
                                        $("#gvBankId").select2({
                                            width: "100%",
                                            placeholder: gvBankId
                                        });
                                }
                            });
                        }else{
                            $('#gvBankId2').find('option').each(function(i,e){
                                if($(e).val() == gvBankId){
                                    $('#gvBankId2').prop('selectedIndex',i);        
                                        $("#gvBankId2").select2({
                                            width: "100%",
                                            placeholder: gvBankId
                                        });
                                }
                            });
                        }
                    }
                }
            }
        });
    }

    function getVendor(type, oksAKT, vendorId, gvId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setVendorOKS',
                oksAKT: oksAKT,
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
                        document.getElementById('generalVendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('generalVendorId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('generalVendorId').options.add(x);
                    }

                    if (type == 1) {
                        $('#generalVendorId').find('option').each(function(i,e){
                            if($(e).val() == gvId){
                                $('#generalVendorId').prop('selectedIndex',i);        
                                    $("#generalVendorId").select2({
                                        width: "100%",
                                        placeholder: gvId
                                    });
                            }
                        });
                    }
                }
            }
        });
    }


    function setPKSOks(type, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPKSOks'
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
                        document.getElementById('vendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorId').options.add(x);
                    }

                    if (type == 1) {
                        $('#vendorId').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#vendorId').prop('selectedIndex',i);        
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

//     function validate_price_oks(vendorId, gvId, priceOKS) {
//        //if(amount != '') {
//            $.ajax({
//                url: 'get_data.php',
//                method: 'POST',
//                data: { action: 'check_price_oks_akt',
//                 vendorId: vendorId,
//                 gvId: gvId,
//                 priceOKS: priceOKS
//                },
//                success: function(data){
//                    var returnVal = data.split('|');
//                    if(parseInt(returnVal[0])!=0)	//if no errors
//                    {
//                     //    document.getElementById('price').value = returnVal[1]; 

//                        if(returnVal[1] == 1){
//                             $('#slipVehicle').hide();
//                             document.getElementById('priceOKS').value = 0;
//                              $('#msgError').show();
//                              document.getElementById('msgError').innerHTML = 'Fee belum terdaftar'; 
//                        }else{
//                         $('#msgError').hide();
//                         document.getElementById('oksAkt_id').value = returnVal[2];
//                         setSlipVehicle($('select[id="stockpileId"]').val(), '', $('input[id="periodefrom"]').val(), $('input[id="periodeto"]').val(), $('select[id="vendorId"]').val(),  $('select[id="generalVendorId"]').val(), 0);
//                        }
                   
                      
//                    }
//                }
//            });
//        //}
//    }

    function setSlipVehicle(stockpileId, checkedSlips, periodeFrom, periodeTo, vendorId, gvId, pg_id) { //checkedSlips awalnya Kosong
		//alert(vendorId +', '+ ppn +', '+ pph);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipVehicle',
                    stockpileId: stockpileId,
                    checkedSlips: checkedSlips,
					periodeFrom: periodeFrom,
					periodeTo: periodeTo,
                    vendorId: vendorId,
                    gvId: gvId,
                    pg_id: pg_id
            },
            success: function(data){
                if(data != '') {
                    $('#slipVehicle').show();
                    document.getElementById('slipVehicle').innerHTML = data;
                    $('#emptydatavehicle').hide();
                }else{
                    $('#emptydatavehicle').show();
                    $('#slipVehicle').hide();
                    document.getElementById('emptydatavehicle').innerHTML = 'Data Kosong';

                }
            }
        });
    }

    function checkAll(a) {
     var checkedSlips = document.getElementsByName('checkedSlips[]');
	if (a.checked) {
         for (var i = 0; i < checkedSlips.length; i++) {
             if (checkedSlips[i].type == 'checkbox') {
                 checkedSlips[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkedSlips.length; i++) {
            //  console.log(i)
             if (checkedSlips[i].type == 'checkbox') {
                 checkedSlips[i].checked = false;
             }
         }
     }
     checkSlip(stockpileId, periodeFrom, periodeTo, vendorId, gvId)
    }

    function checkSlip(stockpileId, periodeFrom, periodeTo, vendorId, gvId) {
//        var checkedSlips = document.forms[0].checkedSlips;
        // console.log("Yeni")
        // console.log(stockpileId, selected, periodeFrom, periodeTo, vendorId, gvId, pg_id);
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if(selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }
    
        setSlipVehicle(stockpileId, selected, periodeFrom, periodeTo, vendorId, gvId);
        //Panggil ulang function setSlipCurah dimana checkedSlips = selected (sudah ada isi)
    }
</script>


<form method="post" id="PengajuanDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_general_data">
    <input type="hidden" name="pgId" id="pgId" value="<?php echo $pgId; ?>">
    <input type="hidden" name="oksAkt_id" id="oksAkt_id">
    <input type="hidden" name="pksId" id="pksId">
    <input type="hidden" name="_method" id="_method" value="<?php echo $method; ?>">

    <div class="row-fluid" style="margin-bottom: 10px;">
        <?php if(isset($pgId) && $pgId != '') { ?>
            <div class="span3 lightblue" id="transaksiOKS_AKTLabel">
                <label>Transaksi OKS AKT? <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                            SELECT '2' as id, 'Yes' as info UNION
                            SELECT '3' as id, 'Others' as info;", $transaksiOKSAKT, "disabled", "transaksiOKSAKT", "id", "info",
                    "", 11, "select2combobox100");
                ?>
            </div>

            <div class="span3 lightblue" id="transaksiPOLabel">
                <label>Transaksi PO ? <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", $transaksiPO, "disabled", "transaksiPO", "id", "info",
                    "", 11, "select2combobox100");
                ?>
            </div>
        <?php } else { ?>
            <div class="span3 lightblue" id="transaksiOKS_AKTLabel">
                <label>Transaksi OKS AKT? <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info UNION
                    SELECT '3' as id, 'Others (cars)' as info;", $transaksiOKSAKT, "", "transaksiOKSAKT", "id", "info",
                    "", 11, "select2combobox100");
                ?>
            </div>

            <div class="span3 lightblue" id="transaksiPOLabel">
                <label>Transaksi PO ? <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", $transaksiPO, "", "transaksiPO", "id", "info",
                    "", 11, "select2combobox100");
                ?>
            </div>
        <?php } ?>
       
        <div class="span3 lightblue">
            <label>Payment Type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Normal' as info UNION SELECT '1' as id, 'Urgent' as info;", $paymentType, "", "paymentType", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue" id="requestPaymentDate" style="display: none">
            <label>Request Payment Date <span style="color: red;">*</span></label>
            <input type="hidden"  name="requestPaymentDate" id="requestPaymentDateValue2">
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" name="requestPaymentDate" id="requestPaymentDateValue"
                   value="<?php echo $requestPaymentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
    </div>

    <div id="row1" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue" id="poHDRLabel" style="display: none">
            <label>PO No <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT idpo_hdr,no_po FROM po_hdr WHERE status = 0 
                        AND entry_by = {$_SESSION['userId']} 
                        ORDER BY entry_date desc", $idPOHDR, "", 
                        "idPOHDR", "idpo_hdr", "no_po", "", "", 
                        "select2combobox100", 1
                    );
            ?>
        </div>

        <div class="span3 lightblue">
            <label>Termin</label>
            <input type="text" placeholder="termin" tabindex="" id="termin" name="termin"
                   value="<?php echo $termin ?>" readonly>
        </div>

        <!-- <div class="span3 lightblue">
            <label>Sisa Termin</label>
            <input type="number" placeholder="SisaTermin" tabindex="" id="sisaTermin" name="sisaTermin"
                   value="<?php echo $sisaTermin ?>" readonly>
        </div> -->
    </div>

    <div id="row2" class="row-fluid hidden" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Request Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" style="width: 95%;">
        </div>
       
        <div class="span3 lightblue">
            <label>Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate"
                   value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" style="width: 95%;">
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
        </div>

        <div class="span3 lightblue">
            <label>Tax Invoice No.</label>
            <input type="text" class="span12" tabindex="" id="invoiceTax" name="invoiceTax"
                   value="<?php echo $invoiceTax; ?>">
        </div>

        <div class="span3 lightblue">
            <label>Tax Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="taxDate" name="taxDate"
                   value="<?php echo $taxDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" style="width: 95%;">
        </div>


    </div>

    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue" <?php echo $style ?>>
            <label>Generated Pengajuan No.</label>
            <input type="text" class="span12" readonly id="generatedInvoiceNo" name="generatedInvoiceNo"
                   value="<?php echo $generatedInvoiceNo; ?>">
        </div>

        <div style="display: none" id="oksOthersLabel" >
            <div class="span3 lightblue">
                <label>Periode From</label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="periodefrom" name="periodefrom"
                     data-date-format="dd/mm/yyyy" class="datepicker" style="width: 95%;" value = "<?php echo $periodeFrom ?>">

            </div>

            <div class="span3 lightblue" >
                <label>Periode To</label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="periodeto" name="periodeto"
                    data-date-format="dd/mm/yyyy" class="datepicker" style="width: 95%;" value = "<?php echo $periodeTo ?>">
            </div>
        </div>
        <div class="span3 lightblue">
            <label>File.</label>
            <input type="file" class="span12" readonly id="file" name="file" style="width: 95%;">
            <?php if (isset($file) && $file != '') { ?>
                <label>Show File.</label>
                <a href="<?php echo $file ?>" target="_blank">Open File </a>
            <?php } ?>
        </div>


        <div class="span3 lightblue" id="poHDRLabel2" style="display: none" >
                <label>PIC Email<span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT user_id, user_email FROM user WHERE active = 1", $picEmail, "", "picEmail", "user_email", "user_email",
                    "", "", "select2combobox100", 1, "", true);
            ?>
        </div>



    </div>

    <div id="notPoTransaction" class="row-fluid" style="margin-bottom: 10px; display: none">

        <div class="span3 lightblue">
            <label>Transaksi Mutasi ? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'No' as info UNION
                    SELECT '2' as id, 'Yes' as info;", $transaksiMutasi, "disabled", "transaksiMutasi", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>

        <!-- <div class="span3 lightblue" id="priceOKSLabel" style="display: none">
            <label>Price</label>
            <input type="text" class="span12" tabindex="" id="priceOKS" name="priceOKS"
                   value="<?php //echo $priceOKS; ?>">
            <span class="help-block" id="msgError" style="display: none; color: red; font-size: 11px"></span>

        </div> -->

        <div class="span3 lightblue" id="pksNameLabel" style="display: none">
            <label>PKS Name<span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorId", "vendor_id", "vendor_name", "", 10, "select2combobox100", 5);
            ?>
        </div>

        <div class="span3 lightblue" id="generalVendorIdLabel" style="display: none">
            <label>Vendor<span style="color: red;">*</span></label>
            <?php
            // createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
            //             FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, "", "generalVendorId", "general_vendor_id", "general_vendor_name",
            //     "", "", "select2combobox100", 1, "", true);
            createCombo("", "", "", "generalVendorId", "general_vendor_id", "general_vendor_name", "", 10, "select2combobox100", 5);
            ?>
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
                            "", 10, "select2combobox100", 5);
                    ?>
            </div>
        <?php } ?>
    </div>

    <div id="notPoTransaction2" class="row-fluid" style="margin-bottom: 25px; display: none">
        <?php if($gvEmail != ''){ ?>
            <div class="span3 lightblue" id="generalVendorEmail" style="display: none" >
                <label>Vendor Email<span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="gvEmail" name="gvEmail" value = "<?php echo $gvEmail ?>">
            </div>
        <?php }else{ ?>
            <div class="span3 lightblue" id="generalVendorEmail" style="display: none" >
                <label>Vendor Email<span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="gvEmail" name="gvEmail">
            </div>
        <?php } ?>
        <div class="span3 lightblue" id="generalUserEmail" style="display: none" >
                <label>PIC Email<span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT user_id, user_email FROM user WHERE active = 1", $picEmail2, "", "picEmail2", "user_email", "user_email",
                    "", "", "select2combobox100", 1, "", true);
            ?>
        </div>

        <div class="span3 lightblue" id="OKSothersLabels" style="display: none">
                <label>Vendor Bank <span style="color: red;"  >*</span></label>
                <?php
                createCombo("", "", "", "gvBankId2", "gv_bank_id", "bank_name",
                    "", 10, "select2combobox100", 5);
                ?>
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
        <?php if (isset($idPOHDR) && $idPOHDR != '') { ?>
            PO detail
            <table width="100%" class="table table-bordered table-striped" style="font-size: 8pt;">
                <thead>
                <tr>
                    <th>Shipment Code</th>
                    <th>Stockpile</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Termin</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>VAT</th>
                    <th>WHT</th>
                    <th>Down Payment</th>
                    <th>Total Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php
                    if ($resultPODetail !== false && $resultPODetail->num_rows > 0) {
                    $totalPaid = 0;
                    $totalPAmount = 0;
                    $totalAvailableAmount = 0;
                    $downPayment = 0;
                    while ($row = $resultPODetail->fetch_object()) {
                        $sqlDP = "SELECT SUM((idp.amount_payment + idp.ppn_value) - idp.pph_value) AS down_payment,
                       idp.ppn_value AS ppn, 
                       idp.pph_value AS pph 
                        FROM invoice_dp idp 
                        WHERE idp.status = 0 AND idp.po_detail_id_dp = {$row->idpo_detail}";
                        $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                        if ($resultDP !== false && $resultDP->num_rows > 0) {

                            $rowDP = $resultDP->fetch_object();

                            if ($rowDP->ppn == 0) {
                                $dp_ppn = 0;
                            } else {
                                //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                                $dp_ppn = $rowDP->ppn;
                            }

                            if ($rowDP->pph == 0) {
                                $dp_pph = 0;
                            } else {
                                //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                                $dp_pph = $rowDP->pph;
                            }


                            if ($rowDP->down_payment != 0) {
                                //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                                //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                                $downPayment = $rowDP->down_payment;
                                // echo " AKA " . $downPayment;
                            } else {
                                $downPayment = 0;
                            }
                        }

                        $amount = $row->amount;
                        $totalPrice = $totalPrice + $amount;
                        $tpph = $row->pph;
                        $tppn = $row->ppn;
                        $paid = $row->paid;
                        $tgtotal = $row->grandtotal;
                        $tamount = ($amount + $tppn - $tpph) - $downPayment;
                        $AvailAmount = $tamount - $paid;
                        $totalpph = $totalpph + $tpph;
                        $totalppn = $totalppn + $tppn;
                        $totalDownPayment = $totalDownPayment + $downPayment;
                        $totalall = $totalall + $tamount;

                        $pAmount = $tamount;
                        $totalPAmount += $pAmount;
                        $totalPaid += $paid;
                        $totalAvailableAmount += $AvailAmount;

                    ?>
                    
                    <td><?php echo $row->shipment_no; ?></td>
                    <td><?php echo $row->stockpile_name; ?></td>
                    <td><?php echo $row->qty; ?></td>
                    <td><?php echo number_format($row->harga, 2, ".", ","); ?></td>
                    <td><?php echo $row->termin; ?>%</td>
                    <td><?php echo $row->item_name; ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->amount, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($tppn, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($tpph, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($downPayment, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($tamount, 2, ".", ","); ?></td>

                    <input type="hidden" name="grandTotal"
                           value="<?php echo number_format($tamount, 2, ".", ","); ?>">
                </tr>
                <?php }
                } ?>
                </tbody>
                <tfoot>

                <tr>
                    <td colspan="6" style="text-align: right;"> Grand Total</td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalPrice, 2, ".", ","); ?></td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalppn, 2, ".", ","); ?></td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalpph, 2, ".", ","); ?></td>
                        <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalDownPayment, 2, ".", ","); ?></td>
                    <td colspan="1" style="text-align: right;"><?php echo number_format($totalall, 2, ".", ","); ?></td>

                </tr>

                <tr>
                </tr>
                </tfoot>
            </table>
        <?php } ?>
    </div>

    <div class="row-fluid" id="slipVehicle" style="display: none;">
        slip
    </div>
    <span class="help-block" id="emptydatavehicle" style="display: none; color: red;"></span>


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
            <button class="btn btn-primary" type="submit" <?php echo $disabledProperty; ?>>Submit</button>
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
<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-danger" <?php echo $disabledProperty; ?> onclick="cancelPG()">CANCEL</button>
    </div>
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