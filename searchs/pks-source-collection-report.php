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
//            alert('tes');
            if($('select[id="searchSupType"]').val() != '') {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/pksSource-Collection-Report.php', {
                    stockpileId: $('select[id="searchStockpileId"]').val(), 
                 //   pksSourceId: $('select[id="searchContractId"]').val(), 
                    periodFrom: $('input[id="searchPeriodFrom"]').val(),
                    periodTo: $('input[id="searchPeriodTo"]').val(),
                 //   Amount: $('select[id="searchAmount"]').val()
                }, iAmACallbackFunction2);
            } else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Supplier Type is required field.");
            }
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
	});
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">

        <form class="form-horizontal" id="searchForm"  method="post">
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
                <label class="control-label" for="searchPeriodFrom">Start Periode</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="5" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Finish Periode</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <!--  <div class="control-group">
                <label class="control-label" for="searchContractId">Pks Source Type <span style="color: red;">*</span></label>
                <div class="controls">
                <?php
                   // createCombo("SELECT 'ALL' as id, 'ALL' as info UNION
                   //              SELECT 'CONTRACT' as id, 'CONTRACT' as info UNION
                    //             SELECT 'CURAH' as id, 'CURAH' as info;", "", "", "searchContractId", "id", "info",
                   //             "", 21, 'select2combobox100');
                ?>
                </div>
            </div> -->

            <!--  <div class="control-group">
                <label class="control-label" for="searchValue">Amount Type<span style="color: red;">*</span></label>
                <div class="controls">
                <?php
                    //createCombo("SELECT 'PKS' as id, 'PKS' as info UNION
                    //SELECT 'ALL' as id, 'ALL' as info;", "", "", "searchAmount", "id", "info",
           // "", 21, 'select2combobox100');
            ?>
                </div>
            </div> -->
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>