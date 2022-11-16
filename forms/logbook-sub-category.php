<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$logbookCategoryId = $_POST['logbookCategoryId'];
$logbookSubCategoryName = '';

if(isset($_POST['logbookCategoryId']) && $_POST['logbookCategoryId'] != '') {

    $sql = "SELECT lc.name as category_name
    FROM logbook_category lc
    WHERE lc.id = {$logbookCategoryId}
    ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $rowData = $resultData->fetch_object();
    $categoryName = $rowData->category_name;
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == 0) {
            echo "<option value=0>NONE</option>";
        }
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
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function() {

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

<input type="hidden" id="logbookSubCategoryId" name="logbookSubCategoryId" value="<?php echo $logbookSubCategoryId; ?>">
<input type="hidden" id="logbookCategoryId" name="logbookCategoryId" value="<?php echo $logbookCategoryId; ?>">
<div class="row-fluid">
    <div class="span6 lightblue">
    <label>Category</label>
            <input type="text" class="span12" tabindex="1" id="categoryName" name="categoryName" value="<?php echo $categoryName; ?>" disabled>
    </div>
</div>

<div class="row-fluid">
    <div class="span6 lightblue">
    <label>Sub Category</label>
            <input type="text" class="span12" tabindex="3" id="logbookSubCategoryName" name="logbookSubCategoryName">
    </div>
</div>