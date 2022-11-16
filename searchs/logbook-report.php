<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if ($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if ($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if ($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if ($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } else if ($empty == 7) {
        echo "<option value=''>-- All --</option>";
    }

    if ($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if ($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ready(function () {
        $(".select2combobox100").select2({
            width: "250%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        $('#searchForm').submit(function (e) {
            e.preventDefault();
//            alert('tes');
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 

            $('#dataContent').load('reports/logbook-report.php', {
                // periodFrom: $('input[id="searchPeriodFrom"]').val(),
                // periodTo: $('input[id="searchPeriodTo"]').val(),
                periodFrom: $('input[id="searchPeriodFrom"]').val(),
                periodTo: $('input[id="searchPeriodTo"]').val(),
                tipePengajuan: $('select[id="tipePengajuan"]').val(),
				//paymentSchedule: $('select[id="paymentSchedule"]').val(),
            }, iAmACallbackFunction2);
        });
    });

    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            format: "dd/mm/yyyy",
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

           <!-- <div class="control-group">
                <label class="control-label" for="glModule">Periode Request Date HO From</label>
                <div class="controls">
                    <?php
                    //createCombo("SELECT request_date_ho AS period FROM logbook GROUP BY request_date_ho ", "", "", "periodFrom", "period", "period", "", "", "", 1)
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="glModule">Periode Request Date HO To</label>
                <div class="controls">
                    <?php
                    //createCombo("SELECT request_date_ho AS period FROM logbook GROUP BY request_date_ho ", "", "", "periodTo", "period", "period", "", "", "", 1)
                    ?>
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="glModule">Periode Payment Schedule</label>
                <div class="controls">
                    <?php
                    //createCombo("SELECT payment_schedule AS period FROM logbook GROUP BY payment_schedule ", "", "", "paymentSchedule", "period", "period", "", "", "", 1)
                    ?>
                </div>
            </div>-->
			<div class="control-group">
                <label class="control-label" for="searchPeriodFrom">Request Payment From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Request Payment To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="tipePengajuan">Tipe Pengajuan</label>
                <div class="controls">
                    <?php
                   createCombo("SELECT 'General' AS id, 'General' AS info UNION
                   SELECT 'Curah' AS id, 'Curah' AS info UNION
				   SELECT 'Freight Cost/OA' AS id, 'Freight Cost/OA' AS info UNION
				   SELECT 'Unloading/OB' AS id, 'Unloading/OB' AS info UNION
				   SELECT 'Handling Cost' AS id, 'Handling Cost' AS info UNION
                   SELECT 'Internal Transfer' AS id, 'Internal Transfer' AS info UNION
                   SELECT 'PKS Kontrak' AS id, 'PKS Kontrak' AS info", "", "", "tipePengajuan", "id", "info",
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
            
            </div>
        </form>
    </div>
</div>