<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$contractId = '';

if(isset($_POST['contractId']) && $_POST['contractId'] != '') {
    $contractId = $_POST['contractId'];
    $boolShow = true;
	$sql = "select contract_type from contract where contract_id = {$contractId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowData = $resultData->fetch_object();
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {contractId: $('input[id="generalContractId"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/contract.php', {}, iAmACallbackFunction);
        
        saveForm(3);
    }
    
    function saveForm(id) {
        if(id == 3) {
            $.ajax({
                url: 'session_processing.php',
                method: 'POST',
                data: $("#contractDataForm").serialize(),
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {

                    }
                }
            });
        }
    }
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#contract-data-2" data-toggle="tab">Contract Details</a></li>
    <?php
    if ($boolShow) {
        ?>
            <li><a href="#contract-stockpile-data" data-toggle="tab">Stockpile(s)</a></li>
            <li><a href="#contract-condition-data" data-toggle="tab">Condition(s)</a></li>
            <li><a href="#contract-pks-detail-data" data-toggle="tab">PKS Details</a></li>
            <?php
        }
        ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalContractId" value="<?php echo $contractId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="contract-data-2">
        
    </div>
    
    <?php
    if($boolShow) {
    ?>
    <div class="tab-pane" id="contract-stockpile-data">
        
    </div>
    
    <div class="tab-pane" id="contract-condition-data">
        
    </div>
	
	<div class="tab-pane" id="contract-pks-detail-data">

    </div>
    <?php
    }
    ?>
</div>