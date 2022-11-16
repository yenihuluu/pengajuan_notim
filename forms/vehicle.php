<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$vehicleId = '';

if(isset($_POST['vehicleId']) && $_POST['vehicleId'] != '') {
    $vehicleId = $_POST['vehicleId'];
    $boolShow = true;
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {vehicleId: $('input[id="generalVehicleId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/vehicle.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#vehicle-data" data-toggle="tab">Vehicle Details</a></li>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalVehicleId" value="<?php echo $vehicleId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="vehicle-data">
        
    </div>
    
</div>