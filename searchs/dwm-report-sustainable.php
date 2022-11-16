<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowFilter = false;


if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 26) {
            $allowFilter = true;
        } 
    }
}

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
			 $.blockUI({ message: '<h4>Please wait...</h4>' }); 
//            alert('tes');
            // if($('select[id="searchStockpileId"]').val() != '') { --HAPUS SEMENTARA
                // if($('input[id="diff"]').val() < 30 && $('select[id="diff"]').val() != 0) {
                //     alertify.set({ labels: {
                //         ok     : "OK"
                //     } });
                //     alertify.alert("periode yang dipilih minimal 1 Bulan!");
                //  }else{
                //     $('#dataContent').load('reports/dwm-report.php', {
                //     stockpileId: $('select[id="searchStockpileId"]').val(), 
                //     periodFrom: $('input[id="searchPeriodFrom"]').val(),
                //     periodTo: $('input[id="searchPeriodTo"]').val(),
                //     value: $('select[id="searchValue"]').val(),
                //     baseOn: $('select[id="searchBaseOn"]').val()
                //     }, iAmACallbackFunction2);
                //  }
                 $('#dataContent').load('reports/dwm-report-sustainable.php', {
                    stockpileId: $('select[id="searchStockpileId"]').val(), 
                    periodFrom: $('input[id="searchPeriodFrom"]').val(),
                    periodTo: $('input[id="searchPeriodTo"]').val(),
                    value: $('select[id="searchValue"]').val(),
                    baseOn: $('select[id="searchBaseOn"]').val()
                    }, iAmACallbackFunction2);
            // }
             
            
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

    //validation currentDate
    function checkDate() {
        var startDate = new Date(document.getElementById('searchPeriodFrom').value || document.getElementById('searchPeriodTo').value);
        var today = new Date();
        if (startDate.getTime() > today.getTime()) {
            alertify.set({ labels: {
                        ok     : "OK"
                    } });
                    alertify.alert("tanggal yang dipilih tidak boleh melebihi tanggal Hari ini !");
        }  
    }

    $("#searchPeriodFrom").change(function() {
        checkDate(); 
    });

    $("#searchPeriodTo").change(function() {
        checkDate(); 
    });

  //change Tanggal BaseOn Daily, Monthly, Weekly
    $("#startDate1").hide();
    $("#startDate2").hide();
    $("#finishDate1").hide();
    $("#finishDate2").hide();
    $("#searchBaseOn").change(function() {
        if(document.getElementById('searchBaseOn').value == 'daily' || document.getElementById('searchBaseOn').value == 'weekly'){
                $("#finishDate").show();
                $("#startDate").show();

                $("#startDate1").hide();
                $("#finishDate1").hide();
                $("#startDate2").hide();
                $("#finishDate2").hide();
                $(function() {
                    $("#searchPeriodFrom").val(null);
                    $("#searchPeriodFrom").datepicker("destroy");
                    $("#searchPeriodFrom").datepicker({
                        minViewMode: 0,
                        todayHighlight: true,
                        //autoclose: true,
                        orientation: "bottom auto",
                        startView: 0
                    });

                    $("#searchPeriodTo").val(null);
                    $("#searchPeriodTo").datepicker("destroy");
                    $("#searchPeriodTo").datepicker({
                        minViewMode: 0,
                        todayHighlight: true,
                        //autoclose: true,
                        orientation: "bottom auto",
                        startView: 0
                    });

                    $('#searchPeriodTo').change(function () {
                    var diff = $('#searchPeriodFrom').datepicker("getDate") - $('#searchPeriodTo').datepicker("getDate");
                    $('#diff').val(diff / (1000 * 60 * 60 * 24) * -1 + 1);
                    });

                });
            }else if(document.getElementById('searchBaseOn').value == 'monthly'){
                $("#searchPeriodTo").val(null);
                $("#searchPeriodFrom").val(null);

                $("#finishDate").hide();
                $("#startDate").hide();
                $("#startDate1").show();
                $("#finishDate1").show();
                $("#startDate2").hide();
                $("#finishDate2").hide();
                $(function() {
                    $("#searchPeriodFrom").datepicker("destroy");
                    $('#searchPeriodFrom').datepicker( {
                        format: "mm/yyyy",
                        startView: "months", 
                        minViewMode: "months"
                        });
                    
                    $("#searchPeriodTo").datepicker("destroy");
                    $('#searchPeriodTo').datepicker( {
                        format: "mm/yyyy",
                        startView: "months", 
                        minViewMode: "months"
                        });
                });
            }else if(document.getElementById('searchBaseOn').value == 'year'){
                $("#searchPeriodTo").val(null);
                $("#searchPeriodFrom").val(null);

                $("#finishDate").hide();
                $("#startDate").hide();
                $("#startDate1").hide();
                $("#finishDate1").hide();
                $("#startDate2").show();
                $("#finishDate2").show();

                $(function() {
                    $("#searchPeriodFrom").datepicker("destroy");
                    $('#searchPeriodFrom').datepicker( {
                        format: "yyyy",
                        startView: "years", 
                        minViewMode: "years"
                        });

                    $("#searchPeriodTo").datepicker("destroy");
                    $('#searchPeriodTo').datepicker( {
                        format: "yyyy",
                        startView: "years", 
                        minViewMode: "years"
                        });
                });
            }
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
                <label class="control-label" for="searchStockpileId">Stockpile <span style="color: red;">*</span></label>
                <div class="controls">
                    <?php
						createCombo("SELECT s.stockpile_id, s.stockpile_name, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                        FROM user_stockpile us
                        INNER JOIN stockpile s
                            ON s.stockpile_id = us.stockpile_id
                        WHERE us.user_id = {$_SESSION['userId']}
                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "searchStockpileId", "stockpile_id", "stockpile_full",
                        "", 1, "select2combobox100", 7, "multiple");
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="baseOn">Base On</label>
                <div class="controls">
                        <?php
                            createCombo("SELECT 'daily' as id, 'Daily' as info UNION
                                        SELECT 'weekly' as id, 'Weekly' as info UNION
                                        SELECT 'monthly' as id, 'Monthly' as info UNION
                            SELECT 'year' as id, 'Year' as info;", "", "", "searchBaseOn", "id", "info",
                                    "", 21, 'select2combobox100');
                        ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodFrom" id="startDate">Start Date</label>
                <label class="control-label" for="searchPeriodFrom" id="startDate1">Start Month</label>
                <label class="control-label" for="searchPeriodFrom" id="startDate2">Start Year </label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="5" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo" id="finishDate">Finish Date</label>
                <label class="control-label" for="searchPeriodTo" id="finishDate1">Finish Month</label>
                <label class="control-label" for="searchPeriodTo" id="finishDate2">Finish Year</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker2" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchValue">Value <span style="color: red;">*</span></label>
                <div class="controls">
                <?php
                    createCombo("SELECT 'PKS' as id, 'PKS' as info UNION
                    SELECT 'ALL' as id, 'ALL' as info;", "", "", "searchValue", "id", "info",
            "", 21, 'select2combobox100');
            ?>
                </div>
            </div>
            <div class="control-group" style="display: none;">
                <div class="controls">
                <input type="text" tabindex="6" id='diff' name="diff" >
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