<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$codeCosting_id = '';
$costingName = '';

if(isset($_POST['codeCosting_id']) && $_POST['codeCosting_id'] != '') {
    $codeCosting_id = $_POST['codeCosting_id'];
    $boolShow = true;
   $costingName = "Data Costing Code";
}else{
    $costingName = "Add Costing";
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {codeCosting_id: $('input[id="generalCodeCostingId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/job-costing-views.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#code-costing-tabs" data-toggle="tab"><?php echo $costingName ?></a></li>
     
    <?php if($boolShow) { ?>
    <li><a href="#detail_costing_tabs" data-toggle="tab">Add Detail Costing</a></li>
    <?php } ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalCodeCostingId" value="<?php echo $codeCosting_id; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    
    <div class="tab-pane active" id="code-costing-tabs">
        
    </div>
    
    <?php
    if($boolShow) {
    ?>
    
    <div class="tab-pane" id="detail_costing_tabs">
    </div>

    <?php
    }
    ?>
</div>