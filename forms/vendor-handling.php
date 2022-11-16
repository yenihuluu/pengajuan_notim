<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$vendorHandlingId = '';

if(isset($_POST['vendorHandlingId']) && $_POST['vendorHandlingId'] != '') {
    $vendorHandlingId = $_POST['vendorHandlingId'];
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {vendorHandlingId: $('input[id="generalVendorHandlingId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/vendor-handling.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#vendor-handling-data" data-toggle="tab">Vendor Handling Details</a></li>
	<?php
    if($boolShow) {
    ?>
    <li><a href="#vh-bank-data" data-toggle="tab">Bank</a></li>
    
    <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalVendorHandlingId" value="<?php echo $vendorHandlingId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="vendor-handling-data">
        
    </div>
	<?php
    if($boolShow) {
    ?>
    <div class="tab-pane" id="vh-bank-data">
        
    </div>
    
    
    <?php
    }
    ?>
    
</div>