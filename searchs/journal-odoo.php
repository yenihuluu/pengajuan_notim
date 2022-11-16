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
        $('#btn-preview').click(function (e) {
            e.preventDefault();
            $('#dataContent').load('reports/journal-odoo.php', {
                periodFrom: $('select[id="periodFrom"]').val(),
                periodTo: $('select[id="periodTo"]').val(),
                module: $('select[id="module"]').val(),
                glDateFrom: document.getElementById('glDateFrom').value,
                glDateTo: document.getElementById('glDateTo').value,

            }, iAmACallbackFunction2);
        });

        $('#btn-preview-2').click(function (e) {
            e.preventDefault();
            $('#dataContent').load('reports/journal-odoo-2.php', {
                periodFrom: $('select[id="periodFrom"]').val(),
                periodTo: $('select[id="periodTo"]').val(),
                module: $('select[id="module"]').val(),
                glDateFrom: document.getElementById('glDateFrom').value,
                glDateTo: document.getElementById('glDateTo').value,

            }, iAmACallbackFunction2);
        });
    });

    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });
        $(".select2").select2({
            width: "100%"
        });
    });
</script>

<div class="row" style="background-color: #f5f5f5;
            margin-bottom: 5px; padding-top: 15px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span6">
        <form class="form-horizontal" id="searchForm" method="post">
            <div class="control-group">
                <label class="control-label" for="glModule">GL Date From</label>
                <!--                <div class="controls">-->
                <!--                    --><?php
                //                    createCombo("SELECT gl_date FROM gl_report where status = 0 GROUP BY gl_date ORDER BY gl_date asc", "", "", "glDateFrom", "gl_date", "gl_date", "", "", "select2", 1)
                //                    ?>
                <!--                </div>-->
                <div class="controls">
                    <input type="text" placeholder="YYYY-MM-DD" tabindex="2" id="glDateFrom" name="glDateFrom"
                           data-date-format="yyyy-mm-dd" class="datepicker">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="glModule">GL Date To</label>
                <!--                <div class="controls">-->
                <!--                    --><?php
                //                    createCombo("SELECT gl_date FROM gl_report where status = 0 GROUP BY gl_date ORDER BY gl_date asc", "", "", "glDateTo", "gl_date", "gl_date", "", "", "select2", 1)
                //                    ?>
                <!--                </div>-->
                <div class="controls">
                    <input type="text" placeholder="YYYY-MM-DD" tabindex="2" id="glDateTo" name="glDateTo"
                           data-date-format="yyyy-mm-dd" class="datepicker">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="glModule">Entry Date From</label>
                <div class="controls">
                    <?php
                    createCombo("SELECT DATE_FORMAT(entry_date,'%Y-%m-%d') as ymd FROM gl_report where status = 0 GROUP BY DATE_FORMAT(entry_date,'%Y-%m-%d') ORDER BY entry_date asc", "", "", "periodFrom", "ymd", "ymd", "", "", "select2", 1)
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="glModule">Entry Date To</label>
                <div class="controls">
                    <?php
                    createCombo("SELECT DATE_FORMAT(entry_date,'%Y-%m-%d') as ymd FROM gl_report where status = 0 GROUP BY DATE_FORMAT(entry_date,'%Y-%m-%d') ORDER BY entry_date asc", "", "", "periodTo", "ymd", "ymd", "", "", "select2", 1)
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="glModule">Module</label>
                <div class="controls">
                    <?php
                    createCombo("SELECT general_ledger_module AS modul FROM gl_report where status = 0 GROUP BY general_ledger_module ", "", "", "module", "modul", "modul", "", "", "select2", 1)
                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" id="btn-preview" class="btn">Preview</button>
                    <button type="submit" id="btn-preview-2" class="btn">Edit Status</button>
                </div>
                <div class="controls">
                </div>
            </div>
        </form>
    </div>
</div>