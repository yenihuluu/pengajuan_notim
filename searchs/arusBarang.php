<?php

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
    } else if($empty == 7) {
        echo "<option value=''>-- All --</option>";
    }
    
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
//            alert('tes');
$.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#dataContent').load('reports/arusBarang-report.php', {
				shipmentId: $('select[id="shipmentId"]').val(),
				stockpileId: $('select[id="stockpileId"]').val(),
				periodFrom: $('input[id="periodFrom"]').val(),
				periodTo: $('input[id="periodTo"]').val(),
				//periodFrom1: $('input[id="periodFrom1"]').val(),
				//periodTo1: $('input[id="periodTo1"]').val()
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
	 
        $(".select2combobox300").select2({
            width: "500%"
        });
        
        $(".select2combobox100").select2({
            width: "300%"
        });
        
        $(".select2combobox200").select2({
            width: "400%"
        });
        $('#amount').number(true, 2);
		
		
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" id="searchForm" method="post">
          	<div class="control-group">
                <label class="control-label" for="searchPeriodFrom">BL Date From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="periodFrom" name="periodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">BL Date To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="periodTo" name="periodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>            
             <div class="control-group">
                <label class="control-label" for="searchStockpileId">Stockpile</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM stockpile s
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "stockpileId", "stockpile_id", "stockpile_full", 
                                "", 7, "select2combobox100", "", "multiple");
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchStockpileId">Shipment Code</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT sh.shipment_id, sh.shipment_no
                                FROM shipment sh WHERE sh.shipment_status = 1
                                ORDER BY sh.shipment_id DESC", "", "", "shipmentId", "shipment_id", "shipment_no", 
                                "", 7, "select2combobox100", "", "multiple");
                    ?>
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