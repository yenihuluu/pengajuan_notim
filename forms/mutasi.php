<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$boolShow = false;
$mutasiId = '';

if (isset($_POST['mutasiId']) && $_POST['mutasiId'] != '') {
    $mutasiId = $_POST['mutasiId'];
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
        var menu = url.split('#');
        $.blockUI({message: '<h4>Please wait...</h4>'});
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {mutasiId: $('input[id="mutasiId"]').val()});
    }

    function back() {
        $.blockUI({message: '<h4>Please wait...</h4>'});
        $('#pageContent').load('views/mutasi.php', {}, iAmACallbackFunction);
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#mutasi-data" data-toggle="tab">Mutasi Header</a></li>
    <?php if ($boolShow) { ?>
        <li><a href="#mutasi-detail" data-toggle="tab">Mutasi Details</a></li>
        <li><a href="#mutasi-contract" data-toggle="tab">Mutasi Contract</a></li>

    <?php } ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="mutasiId" value="<?php echo $mutasiId; ?>"/>
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="mutasi-data">

    </div>
    <?php if ($boolShow) { ?>
        <div class="tab-pane active" id="mutasi-detail">

        </div>

         <div class="tab-pane active" id="mutasi-contract">

        </div>

    <?php } ?>

</div>