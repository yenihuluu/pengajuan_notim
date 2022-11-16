<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Closing Date';
$date = new DateTime();
//$paymentDate = $date->format('d/m/Y');

$closingDate = '';
$closingDate1 = '';
$closingDate2 = '';
$closingDate3 = '';
$closingDate4 = '';
$closingDate5 = '';
$closingDat6 = '';
$closingDate7 = '';
$closingDat8 = '';
$closingDate9 = '';
$closingDate10 = '';
$closingDate11 = '';
$closingDate12 = '';
$closingDate13 = '';
$closingDate14 = '';
$closingDate15 = '';
$closingDate16 = '';
$closingDate17 = '';
$closingDate18 = '';
$closingDate19 = '';
$closingDate20 = '';
$closingDate21 = '';
$closingDate22 = '';
$closingDate23 = '';
$sql = "select cd.closing_date_id, cd.label, DATE_FORMAT(cd.closing_date, '%d %b %Y') as closing_date from closing_date cd order by label";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//if ($resultData !== false && $resultData->num_rows > 0){
//    $row = $resultData->fetch_object();
//    $closingDate = $row->closing_date;
//}


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
        $('#closingAll').hide();


        $("#submitClosingDate").click(function () {
            $.ajax({
                url: './data_processing.php',
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
                            back();
                        }
                    }
                }
            });
        });

        $('#edit').click(function () {
            $("input:text").prop('disabled', false);
            $("input:checkbox").prop('disabled', false);
            $('#submitClosingDate').show();
        });

        $('#check').click(function () {
            $('#closingAll').show();
        });
        // $('#check1').click(function () {
        //     $('#closingAll').show();
        // });
    });

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<div class="row-fluid">
    <div class="span2 lightblue">
        <button id="edit" class="btn btn-warning">Edit</button>
        <button id="submitClosingDate" class="btn btn-primary">Submit</button>
    </div>
</div>

<br>
<form method="post" id="closingDateForm">
    <input type="hidden" name="action" id="action" value="closing_date_data"/>
    <div id="closingAll" class="row-fluid">
    <div class="span4 lightblue">
        <label>Closing Date <span style="color: red;">*</span></label>
        <input type="text" placeholder="DD/MM/YYYY" name="closingDate"
               value="<?php echo $closingDate; ?>" data-date-format="dd MM yyyy" class="datepicker" >
    </div>
</div>
    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
        <tr>
            <td width="50px">
                <div style="text-align: center">
                    <input id="check" type="checkbox" onClick="toggle(this)" disabled/>
                </div>
            </td>
            <th>Label</th>
            <th>Closing Date</th>
        </thead>
        <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                $closingDate = $rowData->closing_date;
                ?>
                <tr>
                    <td>
                        <div style="text-align: center">
                            <input type="checkbox" name="checks[]"
                                   value="<?php echo $rowData->closing_date_id ?>" disabled/>
                        </div>
                    </td>
                    <td><?php echo $rowData->label; ?></td>
                    <td>
                        <input type="hidden" placeholder="DD/MM/YYYY" name="closings[id][]"
                               value="<?php echo $rowData->closing_date_id; ?>">
                        <input type="text" placeholder="DD/MM/YYYY" name="closings[date][]"
                               value="<?php echo $closingDate; ?>" data-date-format="dd MM yyyy" class="datepicker"
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
<!--        <input type="text" placeholder="DD/MM/YYYY" id="tglPosting" name="tglPosting"-->
<!--               value="--><?php //echo $closingDate; ?><!--" data-date-format="dd/mm/yy" class="datepicker">-->
<!--    </div>-->
<!--    <div class="span2 lightblue">-->
<!--        <label>Nota Timbang - Revisi<span style="color: red;">*</span></label>-->
<!--    </div>-->
<!--    <div class="span4 lightblue">-->
<!--        <input type="text" placeholder="DD/MM/YYYY" id="tglPosting" name="tglPosting"-->
<!--               value="--><?php //echo $closingDate1; ?><!--" data-date-format="dd/mm/yyyy" class="datepicker">-->
<!--    </div>-->
<!--</div>-->

