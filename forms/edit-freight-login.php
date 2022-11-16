<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$freightLoginId = $_POST['freightLoginId'];
$freightId = $_POST['freightId'];
$freightUsername = '';
$freightPassword = '';

if (isset($_POST['freightId']) && $_POST['freightId'] != '') {

    $sql = "SELECT CONCAT(f.freight_code, ' - ' , f.freight_supplier) as freight_name
    FROM freight f
    WHERE f.freight_id = {$freightId}
    ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowData = $resultData->fetch_object();
    $freightName = $rowData->freight_name;
}


if (isset($_POST['freightLoginId']) && $_POST['freightLoginId'] != '') {
    $freightLoginId = $_POST['freightLoginId'];

}

if (isset($_POST['freightLoginId']) && $_POST['freightLoginId'] != '') {
    $sql = "SELECT fl.group_id, mg.group_name as group_name, fl.freight_username, fl.password from freight_login fl LEFT JOIN master_group mg
on fl.group_id = mg.master_group_id where fl.freight_login_id = {$freightLoginId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowData = $resultData->fetch_object();
    $freightUsername = $rowData->freight_username;
    $freightGroup = $rowData->group_id;
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
            echo "<option value=0>NONE</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if ($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function () {

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $('#EditFreightLoginForm').on('submit', function (e) {
            e.preventDefault();
            $('#submitButton').attr("disabled", true);
            $('#closeButton').attr("disabled", true);
            $(this).ajaxSubmit({
                success: showResponse //call function after success
            });
        });
    });
    function showResponse(responseText, statusText, xhr, $form) {
        var returnVal = responseText.split('|');
        if (parseInt(returnVal[3]) != 0)	//if no errors
        {
            alertify.set({
                labels: {
                    ok: "OK"
                }
            });
            alertify.alert(returnVal[2]);
            if (returnVal[1] == 'OK') {
                $('#EditFreightLoginModal').modal('hide');
                // $('#freight-login-data').load('forms/vendor-contracts.php', {po_pks_id: $('input[id="po_pks_id"]').val()}, iAmACallbackFunction2);
                $('#freight-login-data').load('tabs/freight-login-data.php', {freightId: $('input[id="generalFreightId"]').val()});
            } else {
                $('#submitButton').attr("disabled", false);
                $('#closeButton').attr("disabled", false);
            }
        }
    }
</script>

<input type="hidden" name="action" id="action" value="edit_freight_login"/>
<input type="hidden" id="freightId" name="freightId" value="<?php echo $freightId; ?>">
<input type="hidden" id="freightId" name="freightLoginId" value="<?php echo $freightLoginId; ?>">
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Freight Name</label>
        <input type="text" class="span12" tabindex="1" id="freightName" name="freightName"
               value="<?php echo $freightName; ?>" disabled>
    </div>
</div>

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Group Name</label>
        <!--        <input type="text" class="span12" tabindex="1" id="freightGroup" name="freightGroup" value="-->
        <?php //echo $freightGroup; ?><!--" disabled>-->
        <?php
        createCombo("SELECT mg.* FROM master_group mg
                ORDER BY mg.group_name ASC", $freightGroup, "", "freightGroup", "master_group_id", "group_name",
            "", 2, "span12");
        ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Username</label>
        <input type="text" class="span12" tabindex="3" id="freightUsername" name="freightUsername"
               value="<?php echo $freightUsername; ?>">
    </div>
</div>

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Password</label>
        <input type="text" class="span12" tabindex="4" id="freightPassword" name="freightPassword">
    </div>
</div>