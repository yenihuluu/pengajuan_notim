<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$disabledProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Stockpile Contract Data">

$vendorCurahId = '';
$contractId = $_POST['contractId'];
$contractPksDetailId = '';
// </editor-fold>

if (isset($_POST['contractPksDetailId']) && $_POST['contractPksDetailId'] != '') {
    $contractPksDetailId = $_POST['contractPksDetailId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Condition Data">

    $sql = "SELECT cpd.*, c.contract_no, vc.vendor_curah_id,  vc.vendor_curah_name, vc.vendor_curah_address
            FROM contract_pks_detail cpd
            left join contract c ON cpd.contract_id = c.contract_id
            left join vendor_curah vc on cpd.vendor_curah_id = vc.vendor_curah_id
            WHERE cpd.contract_pks_detail_id = {$contractPksDetailId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $contractPksDetailId = $rowData->contract_pks_detail_id;
        $vendorCurahId = $rowData->vendor_curah_id;
        $vendorCurahName = $rowData->vendor_curah_name;
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
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }

    echo "</SELECT>";
}


// </editor-fold>

?>
<script type="text/javascript">

    $(document).ready(function () {

        $(".vendorCurahId").select2({
            width: "100%"
        });

    });
</script>
<input type="hidden" id="contractPksDetailId" name="contractPksDetailId" value="<?php echo $contractPksDetailId; ?>">
<input type="hidden" id="contractId" name="contractId" value="<?php echo $contractId; ?>">

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Vendor Curah<span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT vc.vendor_curah_id, vc.vendor_curah_name
                FROM vendor_curah vc
                ORDER BY vc.vendor_curah_name ASC", $vendorCurahId, "", "vendorCurahId", "vendor_curah_id", "vendor_curah_name",
            "", "", "vendorCurahId");
        ?>
    </div>
</div>


