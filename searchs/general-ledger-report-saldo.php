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
            $('#dataContent').load('reports/general-ledger-report-saldo.php', {
                periodFrom: $('input[id="searchPeriodFrom"]').val(),
                periodTo: $('input[id="searchPeriodTo"]').val(),
				/*module: $('select[id="glModule"]').val(),
				stockpileId: $('select[id="searchStockpileId"]').val(),
				jurnalNo: $('input[id="searchJurnalNo"]').val()*/
				coaId: $('select[id="searchCoaNo"]').val()
				
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
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Period To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <!--<div class="control-group">
                <label class="control-label" for="glModule">Module</label>
                <div class="controls">
                    <?php 
                  /* createCombo("SELECT 'CONTRACT' AS id, 'CONTRACT' AS info UNION
								SELECT 'STOCK TRANSIT' AS id, 'STOCK TRANSIT' AS info UNION
                                SELECT 'NOTA TIMBANG' AS id, 'NOTA TIMBANG' AS info UNION
								SELECT 'PAYMENT' AS id, 'PAYMENT' AS info UNION
								SELECT 'INVOICE DETAIL' AS id, 'INVOICE' AS info UNION
                                SELECT 'JURNAL MEMORIAL' AS id, 'JURNAL MEMORIAL' AS info UNION
								SELECT 'PETTY CASH' AS id, 'PETTY CASH' AS info UNION
								SELECT 'PAYMENT ADMIN' AS id, 'PAYMENT ADMIN' AS info UNION
								SELECT 'CONTRACT ADJUSTMENT' AS id, 'CONTRACT ADJUSTMENT' AS info UNION
								SELECT 'RETURN INVOICE' AS id, 'RETURN INVOICE' AS info UNION
								SELECT 'RETURN PAYMENT' AS id, 'RETURN PAYMENT' AS info", "", "", "glModule", "id", "info",
                                "", 7, "select2combobox100", "", "multiple");*/
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchStockpileId">Stockpile</label>
                <div class="controls">
                    <?php 
                    /*createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "searchStockpileId", "stockpile_id", "stockpile_full", 
                                "", 7, "select2combobox100", "", "multiple");*/
                    ?>
                </div>
            </div>-->
            <div class="control-group">
                <label class="control-label" for="searchCoaNo">COA</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT a.account_no, CONCAT(a.account_no, ' - ', a.account_name) AS account_full
                                FROM account a
                                GROUP BY a.account_no ORDER BY a.account_no ASC", "", "", "searchCoaNo", "account_no", "account_full", 
                                "", 7, "select2combobox100", "", "multiple");
                    ?>
                </div>
            </div>
			<!--<div class="control-group">
                <label class="control-label" for="searchJurnalNo">Jurnal No</label>
                <div class="controls">
                    <input type="text" class="span3" tabindex="" id="searchJurnalNo" name="searchJurnalNo" >
                </div>
            </div>-->
			
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>