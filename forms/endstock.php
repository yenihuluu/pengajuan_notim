<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$periodTo = '';

if(isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    //$boolShow = true;
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {periodTo: $('input[id="generalPeriodTo"]').val()});
    }
    
    /*function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/stockpile.php', {}, iAmACallbackFunction);
    }*/
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#endstock_all" data-toggle="tab">All</a></li>
    <?php
   // if($boolShow) {
    ?>
    <li><a href="#endstock_rsb" data-toggle="tab">RSB</a></li>
    <li><a href="#endstock_ggl" data-toggle="tab">GGL</a></li>
	<li><a href="#endstock_rsb_ggl" data-toggle="tab">RSB + GGL</a></li>
	<li><a href="#endstock_uncertified" data-toggle="tab">Uncertified</a></li>
    <?php
   // }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalPeriodTo" value="<?php echo $periodTo; ?>" />
    
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="endstock_all">
        
    </div>
    
    <?php
    //if($boolShow) {
    ?>
    <div class="tab-pane" id="endstock_rsb">
        
    </div>
    
    <div class="tab-pane" id="endstock_ggl">
        
    </div>
	<div class="tab-pane" id="endstock_rsb_ggl">
        
    </div>
	<div class="tab-pane" id="endstock_uncertified">
        
    </div>
    <?php
  //  }
    ?>
</div>