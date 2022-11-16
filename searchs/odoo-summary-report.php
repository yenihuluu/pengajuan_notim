<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">


// </editor-fold>

?>

<script type="text/javascript">
    $(document).ready(function(){
		$(".select2combobox100").select2({
            width: "250%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
		
        $('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#dataContent').load('reports/odoo-summary-report.php', {
                periodFrom: $('input[id="searchPeriodFrom"]').val(),
                periodTo: $('input[id="searchPeriodTo"]').val()
            }, iAmACallbackFunction2);
        });
    });
    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" id="searchForm" method="post">
        
            <div class="control-group">
                <label class="control-label" for="searchPeriodFrom">Period From</label>
                <div class="controls">
                    <input type="text" placeholder="YYYY-MM-DD" tabindex="2" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="yyyy-mm-dd" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Period To</label>
                <div class="controls">
                    <input type="text" placeholder="YYYY-MM-DD" tabindex="3" id="searchPeriodTo" name="searchPeriodTo" data-date-format="yyyy-mm-dd" class="datepicker" >
                </div>
            </div>
        
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>