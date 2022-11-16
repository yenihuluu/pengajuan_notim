<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$disabledProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Stockpile Contract Data">

$vendorId = '';
$contractId = $_POST['contractId'];
$contractPksDetailId = '';
// </editor-fold>

if (isset($_POST['contractPksDetailId']) && $_POST['contractPksDetailId'] != '') {
    $contractPksDetailId = $_POST['contractPksDetailId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Condition Data">

    $sql = "SELECT cpd.*, c.contract_no, v.vendor_id,  CONCAT(v.vendor_code, ' - ', v.vendor_name) AS vendor, v.vendor_address
            FROM contract_pks_detail cpd
            left join contract c ON cpd.contract_id = c.contract_id
            left join vendor v on cpd.vendor_id = v.vendor_id
            WHERE cpd.contract_pks_detail_id = {$contractPksDetailId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $contractPksDetailId = $rowData->contract_pks_detail_id;
        $vendorId = $rowData->vendor_id;
        $vendor = $rowData->vendor;
    }

    // </editor-fold>
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select Vendor--</option>";
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
    });
</script>
<input type="hidden" id="contractPksDetailId" name="contractPksDetailId" value="<?php echo $contractPksDetailId; ?>">
<input type="hidden" id="contractId" name="contractId" value="<?php echo $contractId; ?>">

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Vendor <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT v.vendor_id, CONCAT(v.vendor_code, ' - ', v.vendor_name) AS vendor
                FROM vendor v
                ORDER BY v.vendor_code ASC, v.vendor_name ASC", $vendorId, "", "vendorId", "vendor_id", "vendor",
            "", 1, "select2combobox100");
        ?>
    </div>
</div>

