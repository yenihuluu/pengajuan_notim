<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$boolShow = false;
$logbookId = '';
$pLogbookId = '';

if (isset($_POST['logbookId']) && $_POST['logbookId'] != '') {
    $logbookId = $_POST['logbookId'];
    $boolShow = true;
}
if (isset($_POST['pLogbookId']) && $_POST['pLogbookId'] != '') {
    $pLogbookId = $_POST['pLogbookId'];
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
            logbookId: $('input[id="generalLogbookId"]').val(),
            pLogbookId: $('input[id="generalPLogbookId"]').val()
        });
    }

    function back() {
        $.blockUI({message: '<h4>Please wait...</h4>'});
        $('#pageContent').load('views/logbook.php', {}, iAmACallbackFunction);
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#logbook-data" data-toggle="tab">Logbook Details</a></li>
    <?php
    if ($boolShow) {
        ?>

        <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalLogbookId" value="<?php echo $logbookId; ?>"/>
    <input type="hidden" id="generalPLogbookId" value="<?php echo $pLogbookId; ?>"/>

    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="logbook-data">

    </div>
    <?php
    if ($boolShow) {
        ?>

        <?php
    }
    ?>

</div>