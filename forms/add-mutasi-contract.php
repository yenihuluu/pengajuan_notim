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


// <editor-fold defaultstate="collapsed" desc="Variable for Mutasi Contract">

$mutasiId = $_POST['mutasiId'];
$mutasiContractId = '';
$kodeMutasi = '';
$stockpileContractId = '';
$actionType = '';

// </editor-fold>
if (isset($mutasiId) && $mutasiId != '') {

    $sql = "SELECT mh.* FROM mutasi_header mh  WHERE mh.mutasi_header_id = {$mutasiId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $kodeMutasi = $rowData->kode_mutasi;
    }
}

// If ID is in the parameter
if (isset($_POST['mutasiContractId']) && $_POST['mutasiContractId'] != '') {
    $actionType = 'UPDATE';
    $mutasiContractId = $_POST['mutasiContractId'];

    $sql = "SELECT mc.*, mh.kode_mutasi
            FROM mutasi_contract mc 
            LEFT JOIN mutasi_header mh ON mc.mutasi_header_id = mh.mutasi_header_id            
            WHERE mc.id = {$mutasiContractId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $mutasiContractIdId = $rowData->id;
        $kodeMutasi = $rowData->kode_mutasi;
        $stockpileContractId = $rowData->stockpileContractId;
    }

} else {
    $actionType = 'INSERT';
}

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
        echo "<option value='NULL'>NONE</option>";
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

?>
<script>
    $(function () {

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });
    });

</script>


<input type="hidden" name="action" id="action" value="mutasi_contract"/>
<input type="hidden" name="actionType" id="actionType" value="<?php echo $actionType ?>"/>
<input type="hidden" name="mutasiId" id="mutasiId" value="<?php echo $_POST['mutasiId']; ?>"/>
<input type="hidden" name="mutasiContractId" id="mutasiContractId" value="<?php echo $mutasiContractId ?>"/>

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Kode Mutasi</label>
        <input type="text" placeholder="Input Kode Mutasi" tabindex="" id="kodeMutasi" name="kodeMutasi"
               class="span12"
               value="<?php echo $kodeMutasi; ?>" disabled>
    </div>
<div class="span6 lightblue">
        <label>Contract No<span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT sc.stockpile_contract_id, c.contract_id, CONCAT (c.contract_no, ' | ', v.vendor_code , ' - ' , v.vendor_name) as contract_no FROM mutasi_header mh 
left join stockpile_contract sc on mh.stockpile_from = sc.stockpile_id
left join contract c on sc.contract_id = c.contract_id 
left join vendor v on c.vendor_id = v.vendor_id
left join stock_transit st on st.stockpile_contract_id = sc.stockpile_contract_id
left join mutasi_contract mc on mc.stockpile_contract_id = sc.stockpile_contract_id
WHERE mh.mutasi_header_id = {$mutasiId} and c.mutasi_type = 1 and mc.stockpile_contract_id is null and st.stockpile_contract_id is null
UNION ALL
SELECT sc.stockpile_contract_id, c.contract_id, CONCAT (c.contract_no, ' | ', v.vendor_code , ' - ' , v.vendor_name) as contract_no FROM mutasi_header mh 
left join stockpile_contract sc on mh.stockpile_to = sc.stockpile_id
left join contract c on sc.contract_id = c.contract_id 
left join vendor v on c.vendor_id = v.vendor_id
left join stock_transit st on st.stockpile_contract_id = sc.stockpile_contract_id
left join mutasi_contract mc on mc.stockpile_contract_id = sc.stockpile_contract_id
WHERE mh.mutasi_header_id = {$mutasiId} and c.mutasi_type = 0 and c.langsir = 1 and mc.stockpile_contract_id is null and st.stockpile_contract_id is null", $stockpileContractId, '', "stockpileContractId", "stockpile_contract_id", "contract_no",
            "", 21, 'select2combobox100', 1);
        ?>
    </div>
</div>
