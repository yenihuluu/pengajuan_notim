<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } 
    //else if($empty == 7) {
    // echo "<option value=''>-- Please Select Customer --</option>";
    // }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ready(function(){

        $('#searchForm').submit(function(e){
            e.preventDefault();
			$.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#dataContent').load('reports/do-truck-photo-report-report.php', {
				
                periodFrom: $('input[id="searchPeriodFrom1"]').val(),
                periodTo: $('input[id="searchPeriodTo1"]').val()
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
	 $(document).ready(function(){
           
        
        $(".select2combobox100").select2({
            width: "225px"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        $('#amount').number(true, 2);
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
                <label class="control-label" for="searchPeriodFrom1">Period Date From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="5" id="searchPeriodFrom1" name="searchPeriodFrom1" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="searchPeriodTo1">Period Date To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodTo1" name="searchPeriodTo1" data-date-format="dd/mm/yyyy" class="datepicker" >
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