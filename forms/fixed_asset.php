<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$fixedAssetId = '';

if(isset($_POST['fixedAssetId']) && $_POST['fixedAssetId'] != '') {
    $fixedAssetId = $_POST['fixedAssetId'];
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {fixedAssetId: $('input[id="fixedAssetId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/fixed_asset.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
	<?php
    if($boolShow) {
    ?>
    <li class="active"><a href="#fixed-asset-history"" data-toggle="tab">Fixed Asset History</a></li>
     <li><a href="#fixed-asset-data" data-toggle="tab">Asset Details</a></li>
   <?php
    }else	{
		?>		
     <li class="active"><a href="#fixed-asset-data" data-toggle="tab">Asset Details</a></li>
	<?php
	}
    ?>
   
	
</ul>

<div class="tab-content">
    <input type="hidden" id="fixedAssetId" value="<?php echo $fixedAssetId; ?>" />    
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>  
    
    <?php
    if($boolShow) {
    ?>
    <div class="tab-pane active" id="fixed-asset-history">        
    </div>        
	<div class="tab-pane" id="fixed-asset-data">        
    </div>
    <?php
    }else	{
		?>
		<div class="tab-pane active" id="fixed-asset-data">        
    </div>
	<?php
	}
    ?>
</div>