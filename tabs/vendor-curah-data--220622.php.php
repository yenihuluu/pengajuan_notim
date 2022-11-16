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

// <editor-fold defaultstate="collapsed" desc="Variable for Vendor Data">

$vendorCurahId = '';
$vendorCurahCode = '';
$vendorCurahName = '';
$vendorCurahAddress = '';
$email = '';


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['vendorCurahId']) && $_POST['vendorCurahId'] != '') {

    $vendorCurahId = $_POST['vendorCurahId'];

    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">

    $sql = "SELECT vc.*
            FROM vendor_curah vc
            WHERE vc.vendor_curah_id = {$vendorCurahId}
            ORDER BY vc.vendor_curah_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$vendorCurahCode = $rowData->vendor_curah_code;
        $vendorCurahName = $rowData->vendor_curah_name;
        $vendorCurahAddress = $rowData->vendor_curah_address;
        $email = $rowData->vendor_curah_email;

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
        $("#vendorCurahDataForm").validate({
            rules: {
				vendorCurahCode: "required",
                vendorCurahName: "required",
                email: "required"

                
            },
            messages: {
				vendorCurahCode: "Vendor Curah Code is a required field.",
                vendorCurahName: "Vendor Curah Name is a required field.",
                email: "Email is a required field."

                
            },
            submitHandler: function(form) {

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#vendorCurahDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');
                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);

                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalVendorCurahId').value = returnVal[3];

                                $('#dataContent').load('contents/vendor-curah.php', { vendorCurahId: returnVal[3] }, iAmACallbackFunction);
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

<form method="post" id="vendorCurahDataForm">
    <input type="hidden" name="action" id="action" value="vendor_curah_data" />
    <input type="hidden" name="vendorCurahId" id="vendorCurahId" value="<?php echo $vendorCurahId; ?>" />
	<div class="row-fluid">
        <div class="span4 lightblue">
            <label>Vendor Curah Code <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="vendorCurahCode" name="vendorCurahCode" value="<?php echo $vendorCurahCode; ?>">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Vendor Curah Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="vendorCurahName" name="vendorCurahName" value="<?php echo $vendorCurahName; ?>">
        </div>

        <div class="span4 lightblue">
            <label>Email <span style="color: red;">*</span></label>
            <input type="email" class="span12" tabindex="13" id="email" name="email" value="<?php echo $email; ?>">
        </div>
    </div>
	<div class="row-fluid">
        <div class="span4 lightblue">
            <label>Vendor Curah Address <span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="2" id="vendorCurahAddress" name="vendorCurahAddress"><?php echo $vendorCurahAddress; ?></textarea>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
