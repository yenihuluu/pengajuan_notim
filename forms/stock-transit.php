<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$boolShow = false;
$stockTransitId = '';
$mutasiHeaderId = '';
if (isset($_POST['mutasiHeaderId']) && $_POST['mutasiHeaderId'] != '') {
    $mutasiHeaderId = $_POST['mutasiHeaderId'];
}
if (isset($_POST['stockTransitId']) && $_POST['stockTransitId'] != '') {
    $stockTransitId = $_POST['stockTransitId'];
    $boolShow = true;
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
            stockTransitId: $('input[id="generalStockTransitId"]').val(),
            mutasiHeaderId: $('input[id="mutasiId"]').val(),
            stockpileContractId: $('input[id="contractNoId"]').val(),
        });
    }

    function back() {
        $.blockUI({message: '<h4>Please wait...</h4>'});
        $('#pageContent').load('views/stock-transit.php', {}, iAmACallbackFunction);
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#stock-transit-data" data-toggle="tab">Stock Transit Details</a></li>
    <?php if ($boolShow) { ?>
    <?php } ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalStockTransitId" value="<?php echo $stockTransitId; ?>"/>
    <input type="hidden" id="mutasiId" value="<?php echo $mutasiHeaderId; ?>"/>
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="stock-transit-data">

    </div>
    <?php
    if ($boolShow) {
        ?>
        <div class="tab-pane" id="netto-stockpile-data">

        </div>
        <?php
    }
    ?>

</div>