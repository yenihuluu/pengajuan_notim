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
		
		
        $('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
			if($('input[id="searchPeriodFrom"]').val() != '' && $('input[id="searchPeriodTo"]').val() != '') {
				 $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#dataContent').load('reports/shipment-cost-report.php', {
                
                periodFrom: $('input[id="searchPeriodFrom"]').val(),
                periodTo: $('input[id="searchPeriodTo"]').val()
            }, iAmACallbackFunction2);
			} else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Date is required field.");
            }
        });
    });
    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
			//orientation: "bottom auto",
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
                <label class="control-label" for="searchPeriodFrom">Date From</label>
                <div class="controls">
                    <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="2" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Date To</label>
                <div class="controls">
                    <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="3" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn" id="preview">Preview</button>
                    <!--<button class="btn btn-success" id="generate">Generate XLS</button>-->
                </div>
            </div>
        </form>
    </div>
</div>