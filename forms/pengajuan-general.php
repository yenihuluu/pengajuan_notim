<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$boolShow = false;
$invoiceId = '';
$pgId = '';
$idPOHDR = '';
$termin = '';
$transaksiMutasi = '';
if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
    $pgId = $_POST['pgId'];
    $boolShow = true;
}

if (isset($_POST['idPOHDR']) && $_POST['idPOHDR'] != '') {
    $idPOHDR = $_POST['idPOHDR'];
}
if (isset($_POST['termin']) && $_POST['termin'] != '') {
    $termin = $_POST['termin'];
}
if (isset($_POST['transaksiPO']) && $_POST['transaksiPO'] != '') {
    $transaksiPO = $_POST['transaksiPO'];
}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function () {	//executed after the page has loaded
        loadConfig();

        $('#myTab a').click(function (e) {
            e.preventDefault();
            //alert(this);
            $("#successMsg").hide();
            $("#errorMsg").hide();

            $(this).tab('show');

            loadConfig();
        });
    });

    function loadConfig() {
        var url = $('#myTab .active a').attr('href');
        //alert(url)
        var menu = url.split('#');

        $.blockUI({message: '<h4>Please wait...</h4>'});
        //alert(menu[1]);
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {
            pgId: $('input[id="generalPgId"]').val(),
            idPOHDR: $('input[id="generalIdPOHDR"]').val(),
            transaksiPO: $('input[id="generalTransaksiPO"]').val(),
            termin: $('input[id="generalTermin"]').val()

        });
    }

    function back() {
        $.blockUI({message: '<h4>Please wait...</h4>'});
        $('#pageContent').load('views/pengajuan-general.php', {}, iAmACallbackFunction);

        saveForm(3);
    }

    function saveForm(id) {
        if (id == 3) {
            $.ajax({
                url: 'session_processing.php',
                method: 'POST',
                data: $("#invoiceDataForm").serialize(),
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {

                    }
                }
            });
        }
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#pengajuan-general-data" data-toggle="tab">Pengajuan General Details</a></li>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalPgId" value="<?php echo $pgId; ?>"/>
    <input type="hidden" id="generalIdPOHDR" value="<?php echo $idPOHDR; ?>"/>
    <input type="hidden" id="generalTransaksiPO" value="<?php echo $transaksiPO; ?>"/>
    <input type="hidden" id="generalTermin" value="<?php echo $termin; ?>"/>

    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="pengajuan-general-data">

    </div>

</div>
