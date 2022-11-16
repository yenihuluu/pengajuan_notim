<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$stockpileId = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {stockpileId: $('input[id="generalStockpileId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/stockpile.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#stockpile-data" data-toggle="tab">Stockpile Details</a></li>
    <?php
    if($boolShow) {
    ?>
    <li><a href="#stockpile-freight-data" data-toggle="tab">Freight Cost(s)</a></li>
    <li><a href="#stockpile-unloading-data" data-toggle="tab">Unloading Cost(s)</a></li>
	<li><a href="#stockpile-handling-data" data-toggle="tab">Handling Cost(s)</a></li>
	<li><a href="#stockpile-shrink-tolerance-freight" data-toggle="tab">Shrink Tolerance Freight</a></li>
	<li><a href="#stockpile-labor-data" data-toggle="tab">Labor Stockpile</a></li>
	<li><a href="#stockpile-block-data" data-toggle="tab">Block</a></li>

    <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalStockpileId" value="<?php echo $stockpileId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="stockpile-data">
        
    </div>
    
    <?php
    if($boolShow) {
    ?>
    <div class="tab-pane" id="stockpile-freight-data">
        
    </div>
    
    <div class="tab-pane" id="stockpile-unloading-data">
        
    </div>
	<div class="tab-pane" id="stockpile-handling-data">
        
    </div>
    <div class="tab-pane" id="stockpile-shrink-tolerance-freight">
    </div>
	<div class="tab-pane" id="stockpile-labor-data">
    </div>
	<div class="tab-pane" id="stockpile-block-data">
    </div>
    <?php
    }
    ?>
</div>