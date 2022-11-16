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
            if($('input[id="searchPeriodTo"]').val() != '') {
                $('#dataContent').load('reports/summary-stock.php', {
                    periodTo: $('input[id="searchPeriodTo"]').val(),
                    periodFrom: $('input[id="searchPeriodFrom"]').val(),
                    baseOn: $('select[id="searchBaseOn"]').val()
                }, iAmACallbackFunction2);
            } else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Supplier Type is required field.");
            }
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
       });
    
    $("#startMonth").hide();
    $("#finishMonth").hide();
    $("#searchBaseOn").change(function() {
            if(document.getElementById('searchBaseOn').value == 'Daily') {
                $("#searchPeriodFrom").val(null);
                $("#searchPeriodTo").val(null);
                //$("#finishDate1").hide();
                $("#startMonth").hide();
                $("#finishMonth").hide();
                $("#startDate").show();
                $("#finishDate").show();
                $(function() {
                    $("#searchPeriodFrom").datepicker("destroy");
                    $("#searchPeriodTo").datepicker("destroy");
                     $('#searchPeriodFrom').datepicker({
                    minViewMode: 0,
                    todayHighlight: true,
                    //autoclose: true,
                    orientation: "bottom auto",
                    startView: 0
                    });
                    $('#searchPeriodTo').datepicker({
                    minViewMode: 0,
                    todayHighlight: true,
                    //autoclose: true,
                    orientation: "bottom auto",
                    startView: 0
                    });
                });
            }else if(document.getElementById('searchBaseOn').value == 'Monthly'){
                $("#searchPeriodFrom").val(null);
                $("#searchPeriodTo").val(null);
                //$("#finishDate1").show();
                $("#startDate").hide();
                $("#finishDate").hide();
                $("#startMonth").show();
                $("#finishMonth").show();
                $(function() {
                    $("#searchPeriodFrom").datepicker("destroy");
                    $("#searchPeriodTo").datepicker("destroy");
                    $('.datepicker').datepicker( {
                        format: "mm/yyyy",
                        startView: "months", 
                        minViewMode: "months",
                        orientation: "bottom auto",
                        startView: 0
                        });
                });
            }else{
                $("#searchPeriodFrom").val(null);
                $("#searchPeriodTo").val(null);
                $(function() {
                    $("#searchPeriodFrom").datepicker("destroy");
                    $("#searchPeriodTo").datepicker("destroy");
                });
            }

    })
  
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">

        <form class="form-horizontal" id="searchForm"  method="post">
        <div class="control-group">
                <label class="control-label" for="baseOn">Base On</label>
                <div class="controls">
                        <?php
                            createCombo("SELECT 'Daily' as id, 'Daily' as info UNION
                                        SELECT 'Monthly' as id, 'Monthly' as info;", "", "", "searchBaseOn", "id", "info",
                                    "", 21, 'select2combobox100');
                        ?>
                </div>
            </div>
        <div class="control-group">
                <label class="control-label" for="searchPeriodFrom" id="startDate">Date Start</label>
                <label class="control-label" for="searchPeriodFrom" id="startMonth">Month Start</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo" id="finishDate">Date Finish</label>
                <label class="control-label" for="searchPeriodTo" id="finishMonth">Month Finish</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
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