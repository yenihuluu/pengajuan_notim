<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$accruePrediksiId = '';

 if(isset($_POST['prediksiId']) && $_POST['prediksiId'] != '') {
     $accruePrediksiId = $_POST['prediksiId'];
 }

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function(){	//executed after the page has loaded
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

        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {prediksiId: $('input[id="generalprediksiId"]').val()}); //load ke TABS/pengajuan-payment-tabs.php
    }

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/accrue-prediksi-views.php', {}, iAmACallbackFunction); //jika file php didalam folder FORMS kosong balik ke views
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#accrue-prediksi-tabs" data-toggle="tab">Costing</a> <!-- call shipment-cost-tabs.php dlm folder TABS -->
    </li>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalprediksiId" value="<?php echo $accruePrediksiId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="accrue-prediksi-tabs">

    </div>

</div>