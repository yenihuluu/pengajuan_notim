<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


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
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {});
    }
    
  
    
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#vendor-pks" data-toggle="tab">Vendor PKS</a></li>
    <li><a href="#vendor-oa" data-toggle="tab">Vendor Freight</a></li>
    <li><a href="#vendor-ob" data-toggle="tab">Labor</a></li>
    <li><a href="#vendor-hc" data-toggle="tab">Vendor Handling</a></li>
    <li><a href="#vendor-gv" data-toggle="tab">General Vendor</a></li>
</ul>

<div class="tab-content">
    
    <div class="tab-pane active" id="vendor-pks"></div>
    <div class="tab-pane" id="vendor-oa"></div>
    <div class="tab-pane" id="vendor-ob"></div>
	<div class="tab-pane" id="vendor-hc"></div>
	<div class="tab-pane" id="vendor-gv"></div>
   
</div>