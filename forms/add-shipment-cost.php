<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$shipmentCostID = '';

 if(isset($_POST['shipmentCostID']) && $_POST['shipmentCostID'] != '') {
     $shipmentCostID = $_POST['shipmentCostID'];
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
        //alert(menu[1]);
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {shipmentCostID: $('input[id="generalshipmentCostID"]').val()}); //load ke TABS/shipment-cost-tabs.php
    }

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/shipment-cost.php', {}, iAmACallbackFunction); //jika file php didalam folder FORMS kosong balik ke views
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#shipment-cost-tabs" data-toggle="tab">Shipment Cost Details</a> <!-- call shipment-cost-tabs.php dlm folder TABS -->
    </li>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalshipmentCostID" value="<?php echo $shipmentCostID; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="shipment-cost-tabs">

    </div>

</div>