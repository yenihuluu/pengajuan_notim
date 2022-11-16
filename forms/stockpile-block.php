<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$stockpileBlockId = '';
$stockpileId = $_POST['stockpileId'];
$ggl = '';
$rsb = '';



// </editor-fold>

if(isset($_POST['stockpileBlockId']) && $_POST['stockpileBlockId'] != '') {
    $stockpileBlockId = $_POST['stockpileBlockId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

    $sql = "SELECT *
            FROM MST_SP_Block 
            WHERE sp_block_id = {$stockpileBlockId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $ggl = $rowData->GGL;
        $rsb = $rowData->RSB;
        $spBlock = $rowData->sp_block;

    }

    // </editor-fold>
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if($empty == 1) {
        echo "<option value='0'>-- NONE --</option>";
    } else {
        echo "<option value='0'>-- NONE --</option>";
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
    $(document).ready(function(){

        // $('#rsb').change(function() {
        //     if(document.getElementById('rsb').value == 1) {
        //         setGGL(1, $('select[id="ggl"]').val());
        //         $('#ggl').attr("readonly", true); 
        //     }else if(document.getElementById('rsb').value == 0){
        //         resetggl(0,$('select[id="ggl"]').val());
        //         $('#ggl').attr("readonly", false); 
        //     }
        // });

		$("select.select2combobox100").select2({
            width: "100%"
        });

        $("select.select2combobox50").select2({
            width: "50%"
        });

        $("select.select2combobox75").select2({
            width: "75%"
        });

    });

        //when rsb = 1 maka ggl = 1
    function resetggl(type, paymentType) {
        document.getElementById('ggl').value = 0;
    }

    function setGGL(type, paymentType) {
        document.getElementById('ggl').value = 1;
    }
</script>

<input type="hidden" id="stockpileBlockId" name="stockpileBlockId" value="<?php echo $stockpileBlockId; ?>">
<input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">

<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Block <span style="color: red;">*</span></label>
        <input type="text" id="spBlock" name="spBlock" value="<?php echo $spBlock ?>">
    </div>
    <div class="span12 lightblue">
        <label>RSB <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'Yes' as info;", $rsb, '', "rsb", "id", "info", 
                    "", "");
                ?>
    </div>
    <div class="span12 lightblue">
        <label>GGL <span style="color: red;">*</span></label>
                 <?php
                createCombo("SELECT '1' as id, 'Yes' as info;", $ggl, '', "ggl", "id", "info", 
                    "", "");
                ?>
        </div>
  
</div>
