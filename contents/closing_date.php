<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$date = new DateTime();
//$paymentDate = $date->format('d/m/Y');

$stockpileId = '';

if (isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty = " WHERE cd.stockpile_id = {$stockpileId}";
} else {
    $whereProperty = " WHERE cd.stockpile_id IS NULL";
}

$sqlCheck = "SELECT * FROM closing_date WHERE stockpile_id IS NOT NULL limit 1";
$resultData = $myDatabase->query($sqlCheck, MYSQLI_STORE_RESULT);
$rowCheck = $resultData->fetch_object();
$active = $rowCheck->active;
if($active){
$allStockpile = 0;
}else{
$allStockpile = 1;
}

$sql = "SELECT *
FROM `closing_date` cd
LEFT JOIN closing_date_label cdl ON cdl.closing_date_label_id = cd.closing_date_label_id {$whereProperty}";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


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
    $(function () {
          $('#closingDate').prop('disabled', true);
            $('#startPeriod').prop('disabled', true);
            $('#endPeriod').prop('disabled', true);
        $("select.select2combobox100").select2({
            width: "100%"
        });

        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });
    });

    function back() {
        $('#pageContent').load('views/closing-date.php', {}, iAmACallbackFunction);
    }

    $(document).ready(function () {
        $('#submitClosingDate').hide();
        // $('#closingAll').hide();


        $("#submitClosingDate").click(function () {
            $.ajax({
                url: './irvan.php',
                method: 'POST',
                data: $("#closingDateForm").serialize(),
                success: function (data) {
                    // console.log(data);
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/closing_date.php',
                                {
                                    stockpileId: document.getElementById('stockpileId').value
                                }, iAmACallbackFunction2
                            );
                        }
                    }
                }
            });
        });

        $('#edit').click(function () {
            $("input:text").prop('disabled', false);
            $("input:checkbox").prop('disabled', false);
            $('#submitClosingDate').show();

            $('#closingDate').prop('disabled', true);
            $('#startPeriod').prop('disabled', true);
            $('#endPeriod').prop('disabled', true);
        });

        // $('#check').click(function () {
        //     $('#closingAll').show();
        // });

        $('#stockpileId').change(function () {
            $('#dataContent').load('contents/closing_date.php',
                {
                    stockpileId: document.getElementById('stockpileId').value
                }, iAmACallbackFunction2
            );
        });

        $('.cdId').change(function (e) {
            document.getElementById('cdId' + this.value).disabled = this.checked;
        });

        $('.statusCD').change(function (e) {
            document.getElementById('status' + this.value).disabled = this.checked;
        });
        $('#check1').click(function () {
            // $('#closingAll').show();
            // $('#closingDate').enabled();
            // $('#startPeriod').enabled();
            // $('#endPeriod').enabled();

              $('#closingDate').prop('disabled', false);
            $('#startPeriod').prop('disabled', false);
            $('#endPeriod').prop('disabled', false);

        });

        $('#allStockpile').change(function () {

            $.ajax({
                url: './irvan.php',
                method: 'POST',
                data: {
                    _method: "AllStockpile",
                    action:"closing_date_data",
                    status: document.getElementById("allStockpile").value
                },
                success: function (data) {
                    // console.log(data);
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/closing_date.php',
                                {
                                    stockpileId: document.getElementById('stockpileId').value
                                }, iAmACallbackFunction2
                            );
                        }
                    }
                }
            });
        });
    });

    function toggle(source) {
        checkboxes = document.getElementsByName('closings[id][]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    // function toggleStatus(source) {
    //     checkboxes = document.getElementsByName('closings[status][]');
    //     for (var i = 0, n = checkboxes.length; i < n; i++) {
    //         checkboxes[i].checked = source.checked;
    //     }
    // }

</script>

<div class="row-fluid">
        <button id="edit" class="btn btn-warning">Edit</button>
        <button id="submitClosingDate" class="btn btn-primary">Submit</button>
</div>
<br>

<form method="post" id="closingDateForm">
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Stockpile<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
        <div class="span9 lightblue"  style="text-align: right;">
        <input type="checkbox" name="checkbox" id="allStockpile" value="<?php echo $allStockpile ?>" <?php if($allStockpile) {echo 'checked'; } ?>>
        <label for="allStockpile"><b>All Stockpile</b></label>
        </div>
    </div>
    <input type="hidden" name="action" id="action" value="closing_date_data"/>
    

    <div id="closingAll" class="row-fluid">
        <div class="span4 lightblue">
            <label>Start Period <span style="color: red;">*</span></label>
            <input type="text" placeholder="YYYY-MM-DD" id="startPeriod" name="startPeriod" value="-"
                  data-date-format="yyyy-mm-dd" class="datepicker">
        </div>
        <div class="span4 lightblue">
            <label>End Period <span style="color: red;">*</span></label>
            <input type="text" placeholder="YYYY-MM-DD" id="endPeriod" name="endPeriod" value="-"
                  data-date-format="yyyy-mm-dd" class="datepicker">
        </div>
        <div class="span4 lightblue">
            <label>Closing Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="YYYY-MM-DD" id="closingDate" name="closingDate" value="-"
                  data-date-format="yyyy-mm-dd" class="datepicker">
        </div>
    </div>

    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
        <tr>
            <td width="50px">
                <div style="text-align: center">
                    <input id="check1" type="checkbox" onClick="toggle(this)" disabled/>
                </div>
            </td>
            <th>Label</th>
            <th>Start Period</th>
            <th>End Period</th>
            <th>Closing Date</th>
        </thead>
        <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            $i = 0;
            while ($rowData = $resultData->fetch_object()) {
                $closingDate = $rowData->closing_date;
                $startPeriod = $rowData->start_period;
                $endPeriod = $rowData->end_period;
                ?>
                <tr>
                    <td>
                        <div style="text-align: center">
                            <input type="checkbox" class="cdId" name="closings[id][]"
                                   value="<?php echo $rowData->closing_date_id ?>" disabled/>
                                   
                            <input type="hidden" id="cdId<?php echo $rowData->closing_date_id ?>" name="closings[id][]"
                                   value="">
                        </div>
                    </td>
                    <!-- <td>
                        <div style="text-align: center">
                            <input type="checkbox" class="statusCD" name="closings[status][]"
                                   value="<?php echo $rowData->closing_date_id ?>" <?php if ($rowData->status == 1) {
                                echo 'checked';
                            } ?> disabled/>

                            <input type="hidden" id="status<?php echo $rowData->closing_date_id ?>"
                                   name="closings[status][]" value="0" <?php if ($rowData->status == 1) {
                                echo 'disabled';
                            } ?>>

                            <?php if ($rowData->status == 0) {
                                echo 'Closing Date';
                            } else {
                                echo 'Period';
                            }
                            ?>
                        </div>
                    </td> -->
                    <td><?php echo $rowData->label; ?></td>
                    <td>
                        <input type="text" placeholder="YYYY-MM-DD" name="closings[startPeriod][]"
                               value="<?php echo $startPeriod; ?>" data-date-format="yyyy-mm-dd" class="datepicker"
                               disabled>
                    </td>
                    <td>
                        <input type="text" placeholder="YYYY-MM-DD" name="closings[endPeriod][]"
                               value="<?php echo $endPeriod; ?>" data-date-format="yyyy-mm-dd" class="datepicker"
                               disabled>
                    </td>
                    <td>
                        <input type="text" placeholder="YYYY-MM-DD" name="closings[closingDate][]"
                               value="<?php echo $closingDate; ?>" data-date-format="yyyy-mm-dd" class="datepicker"
                               disabled>
                    </td>

                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td colspan="9" style="text-align: center">
                    No data to be shown.
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</form>
<!--<div class="row-fluid">-->
<!--    <div class="span2 lightblue">-->
<!--        <label>Nota Timbang - Input<span style="color: red;">*</span></label>-->
<!--    </div>-->
<!--    <div class="span4 lightblue">-->
<!--        <input type="text" placeholder="YYYY-MM-DD" id="tglPosting" name="tglPosting"-->
<!--               value="--><?php //echo $closingDate; ?><!--" data-date-format="dd/mm/yy" class="datepicker">-->
<!--    </div>-->
<!--    <div class="span2 lightblue">-->
<!--        <label>Nota Timbang - Revisi<span style="color: red;">*</span></label>-->
<!--    </div>-->
<!--    <div class="span4 lightblue">-->
<!--        <input type="text" placeholder="YYYY-MM-DD" id="tglPosting" name="tglPosting"-->
<!--               value="--><?php //echo $closingDate1; ?><!--" data-date-format="YYYY-MM-DD" class="datepicker">-->
<!--    </div>-->
<!--</div>-->

