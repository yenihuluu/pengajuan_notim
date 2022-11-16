<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$paymentId = '';

if(isset($_POST['paymentId']) && $_POST['paymentId'] != '') {
    $paymentId = $_POST['paymentId'];
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {paymentId: $('input[id="generalPaymentId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/updateCashPayment.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#updateCashPayment-data" data-toggle="tab">Update Cash Payment</a></li>
	<li><a href="#updateCashPayment-detail" data-toggle="tab">Update Cash Payment Detail</a></li>
    
</ul>

<div class="tab-content">
    <input type="hidden" id="generalPaymentId" value="<?php echo $paymentId; ?>" />
    <div class="alert">
        
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="updateCashPayment-data">
        
    </div>
	<div class="tab-pane" id="updateCashPayment-detail">
        
    </div>
    
    
</div>