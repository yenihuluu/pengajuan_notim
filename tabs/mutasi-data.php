<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';


// <editor-fold defaultstate="collapsed" desc="Variable for Mutasi Data">

$mutasiId = '';
$kodeMutasi = '';
$stockpileIFrom = '';
$stockpileTo = '';
$tanggalMutasi = '';
$keterangan = '';
$total = '';

// </editor-fold>

// If ID is in the parameter
if (isset($_POST['mutasiId']) && $_POST['mutasiId'] != '') {
    $actionType = 'UPDATE';
    $mutasiId = $_POST['mutasiId'];
    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            //echo $row->user_id;
            if ($row->user_id != 46 && $row->user_id != 22) {
                $readonlyProperty = 'readonly';
            } else {
                $readonlyProperty = '';
            }
        }
    }
    $sql = "SELECT mh.* FROM mutasi_header mh WHERE mh.mutasi_header_id = {$mutasiId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();

        $mutasiId = $rowData->mutasi_header_id;
        $kodeMutasi = $rowData->kode_mutasi;
        $stockpileIFrom = $rowData->stockpile_from;
        $stockpileTo = $rowData->stockpile_to;
        $keterangan = $rowData->keterangan;
        $tanggalMutasi = $rowData->tanggal_mutasi;
        $total = $rowData->total;
    }
} else {
    $actionType = 'INSERT';
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Category --</option>";
    } else if ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        echo "<option value='NONE'>NONE</option>";
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
    $(document).ajaxStop($.unblockUI);

</script>

<script type="text/javascript">

    //SUBMIT FORM
    $("#mutasiForm").validate({
        // rules: {
        //     requestDateHo: "required",
        //     mvName: "required",
        // },
        // messages: {
        //     requestDateHo: "Request Date Ho is a required field.",
        //     mvName: "Mv Name is a required field.",
        //
        // },
        submitHandler: function (form) {
            $('#submitButton').attr("disabled", true);
			 $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: $("#mutasiForm").serialize(),
                success: function (data) {
                    var returnVal = data.split('|');

                    console.log(returnVal[2]);
                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/mutasi.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton').attr("disabled", false);
                    }
                }
            });
        }
    });
</script>

<form method="post" id="mutasiForm">
    <input type="hidden" name="action" id="action" value="mutasi_data"/>
    <input type="hidden" name="actionType" id="actionType" value="<?php echo $actionType ?>"/>
    <input type="hidden" name="mutasiId" id="mutasiId" value="<?php echo $mutasiId; ?>"/>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Stockpile Awal<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileIFrom, "", "stockpileFrom", "stockpile_id", "stockpile_full",
                "", 1, "span12");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Stockpile Tujuan<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileTo, "", "stockpileTo", "stockpile_id", "stockpile_full",
                "", 1, "span12");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Tanggal Mutasi<span style="color: red;">*</span></label>
            <input type="date" id="tanggalMutasi" name="tanggalMutasi" class="span12"
                   value="<?php echo $tanggalMutasi ?>">
        </div>
        <?php if ($kodeMutasi != '') { ?>
            <div class="span3 lightblue">
                <label>Kode Mutasi<span style="color: red;">*</span></label>
                <input type="text" id="kodeMutasi" name="kodeMutasi" value="<?php echo $kodeMutasi ?>" disabled>
            </div>
        <?php } ?>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Keterangan<span style="color: red;">*</span></label>
            <textarea name="keterangan" id="keterangan"><?php echo $keterangan ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
