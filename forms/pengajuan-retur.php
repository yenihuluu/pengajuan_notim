<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$boolShow = false;
$invoiceId = '';
$prId = '';
$slipLama = '';
$stockpileId = '';
$tanggalNotim = '';
$_method = '';
$tanggalNotimBaru = '';
$slipBaru = '';

if (isset($_POST['prId']) && $_POST['prId'] != '') {
    $prId = $_POST['prId'];
    $boolShow = true;
}

if (isset($_POST['_method']) && $_POST['_method'] != '') {
    $_method = $_POST['_method'];
}

if (isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $tanggalNotim = $_POST['tanggalNotim'];
    $slipLama = $_POST['slipLama'];
}

if (isset($_POST['slipBaru']) && $_POST['slipBaru'] != '' && isset($_POST['tanggalNotimBaru']) && $_POST['tanggalNotimBaru'] != '') {
    $tanggalNotimBaru = $_POST['tanggalNotimBaru'];
    $slipBaru = $_POST['slipBaru'];
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
            prId: $('input[id="generalPRId"]').val(),
            stockpileId: document.getElementById('generalStockpileId').value,
            tanggalNotim: document.getElementById('generalTanggalNotim').value,
            slipLama: document.getElementById('generalSlipLama').value,
            tanggalNotimBaru: document.getElementById('generaltanggalNotimBaru').value,
            slipBaru: document.getElementById('generalSlipBaru').value,
            _method: $('input[id="generalMethod"]').val()
        });
    }

    function back() {
        $.blockUI({message: '<h4>Please wait...</h4>'});
        $('#pageContent').load('views/pengajuan-retur.php', {}, iAmACallbackFunction);

    }


</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#pengajuan-retur-data" data-toggle="tab">Pengajuan Retur Details</a></li>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalPRId" value="<?php echo $prId; ?>"/>
    <input type="hidden" id="generalStockpileId" value="<?php echo $stockpileId; ?>"/>
    <input type="hidden" id="generalTanggalNotim" value="<?php echo $tanggalNotim; ?>"/>
    <input type="hidden" id="generalSlipLama" value="<?php echo $slipLama; ?>"/>
    <input type="hidden" id="generaltanggalNotimBaru" value="<?php echo $tanggalNotimBaru; ?>"/>
    <input type="hidden" id="generalSlipBaru" value="<?php echo $slipBaru; ?>"/>
    <input type="hidden" id="generalMethod" value="<?php echo $_method; ?>"/>

    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="pengajuan-retur-data">

    </div>

</div>
