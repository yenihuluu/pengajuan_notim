<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$boolPrivilege = false;
$boolStockpile = false;
$userId = '';

if(isset($_POST['userId']) && $_POST['userId'] != '') {
    $userId = $_POST['userId'];
    $boolShow = true;
}

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 46) {
           $boolPrivilege = true;
        }
	}
}

$sql = "SELECT * FROM user_stockpile WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->stockpile_id == 10) {
           $boolStockpile = true;
        }
	}
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {userId: $('input[id="generalUserId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/user.php', {}, iAmACallbackFunction);
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#user-data" data-toggle="tab">User Details</a></li>
    <?php
    if($boolShow) {
		if($boolPrivilege) {
	?>
    <li><a href="#user-module-data" data-toggle="tab">Privilege(s)</a></li>
	<?php } 
	if($boolStockpile) {?>
	
    <li><a href="#user-stockpile-data" data-toggle="tab">Stockpile(s)</a></li>
	 <?php
    }
    ?>
    <li><a href="#user-signature-data" data-toggle="tab">Signature</a></li>
    <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalUserId" value="<?php echo $userId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="user-data">
        
    </div>
    
    <?php
    if($boolShow) {
		if($boolPrivilege) {
    ?>
    <div class="tab-pane" id="user-module-data">
        
    </div>
    <?php
    }
	if($boolStockpile) {
    ?>
    <div class="tab-pane" id="user-stockpile-data">
        
    </div>
	<?php
    }
    ?>
    <div class="tab-pane" id="user-signature-data">
        
    </div>
    <?php
    }
    ?>
</div>