<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Data">

$picFinanceId = '';
$picFinanceName = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['picFinanceId']) && $_POST['picFinanceId'] != '') {

    $picFinanceId = $_POST['picFinanceId'];

    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		//echo $row->user_id;
        if($row->user_id != 46 && $row->user_id != 22) {
            $readonlyProperty = 'readonly';
        }else{
			$readonlyProperty = '';
		}
    }
}

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Data">

    $sql = "SELECT mpf.*
            FROM master_pic_finance mpf
            WHERE mpf.id = {$picFinanceId}
            ORDER BY mpf.id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $picFinanceName = $rowData->pic_name;
    }

    // </editor-fold>

}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == 0) {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
    } else if($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if($empty == 2) {
        echo "<option value='NONE'>Others</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function(){
        $("#picFinanceDataForm").validate({
            rules: {
                picFinanceName: "required",
            },
            messages: {
                picFinanceName: "PIC Name is a required field.",
            },
            submitHandler: function(form) {

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#picFinanceDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);

                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalPicFinanceId').value = returnVal[3];

                                $('#dataContent').load('contents/pic-finance.php', { picFinanceId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            }
                        }
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">

    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 2
        });
    });
</script>

<form method="post" id="picFinanceDataForm">
    <input type="hidden" name="action" id="action" value="pic_finance_data" />
    <input type="hidden" name="picFinanceId" id="picFinanceId" value="<?php echo $picFinanceId; ?>" />
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>PIC Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="picFinanceName" name="picFinanceName" value="<?php echo $picFinanceName; ?>">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
