<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$po_pks_id = '';

if(isset($_POST['po_pks_id']) && $_POST['po_pks_id'] != '') {
    $po_pks_id = $_POST['po_pks_id'];
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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {po_pks_id: $('input[id="po_pks_id"]').val()});
    }
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/po-pks.php', {}, iAmACallbackFunction);
        
        saveForm(4);
    }
    
    function saveForm(id) {
        if(id == 4) {
            $.ajax({
                url: 'session_processing.php',
                method: 'POST',
                data: $("#PoDataForm").serialize(),
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
    <li class="active"><a href="#po-pks" data-toggle="tab">Contract</a></li>
</ul>

<div class="tab-content">
    <input type="hidden" id="po_pks_id" value="<?php echo $po_pks_id; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>
    
    <div class="tab-pane active" id="po-pks">
        
    </div>
    
</div>