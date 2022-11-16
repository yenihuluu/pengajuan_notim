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

// <editor-fold defaultstate="collapsed" desc="Variable for Module Data">

$tipeBiayaId = '';
$tipeBiaya = '';
$tipeBiayaDescription = '';
$active = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['tipeBiayaId']) && $_POST['tipeBiayaId'] != '') {
    
    $tipeBiayaId = $_POST['tipeBiayaId'];
        
    $sql = "SELECT *
            FROM mst_tipe_biaya 
            WHERE id = {$tipeBiayaId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $tipeBiayaId = $rowData->id;
        $tipeBiaya = $rowData->tipe_biaya;
        $tipeBiayaDescription = $rowData->deskripsi;
        $active = $rowData->active;
        $accountId = $rowData->account_id;
        $method = 'UPDATE';
        $submit = 'Update';
    }
    
    // </editor-fold>
    
}else{
    $method = 'INSERT';
    $submit = 'Submit';
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
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
        echo "<option value='OTHER'>Others</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });

        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $("#tipeBiayaForm").validate({
            rules: {
                tipeBiaya: "required",
                active: "required"
            },
            messages: {
                tipeBiaya: "Tipe Biaya is a required field.",
                active: "Status is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    _method: 'INSERT',
                    data: $("#tipeBiayaForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('tipeBiayaId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/tipe-biaya-forms.php', { moduleId: returnVal[3] }, iAmACallbackFunction2);

                            } 
                        }
                    }
                });
            }
        });
    });
</script>


<form method="post" id="tipeBiayaForm">
    <input type="hidden" name="action" id="action" value="tipe_biaya_data" />
    <input type="hidden" name="_method" id="_method" value="<?php echo $method; ?>" />
    <input type="hidden" name="tipeBiayaId" id="tipeBiayaId" value="<?php echo $tipeBiayaId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Tipe Biaya <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="tipeBiaya" name="tipeBiaya" value="<?php echo $tipeBiaya; ?>">
        </div>
        <div class="span4 lightblue">
        <label>Account Name  <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT DISTINCT(a.account_id), CONCAT(a.account_no, ' - ', a.account_name) as fullName, a.* FROM account a 
                            WHERE a.account_type = 4 order by a.account_id ASC", $accountId, '', "accountId", "account_id", "fullName", 
                "", 21, "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Description</label>
            <textarea class="span12" rows="3" tabindex="2" id="tipebiayaDesc" name="tipebiayaDesc"><?php echo $tipeBiayaDescription; ?></textarea>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Active' as info UNION
                    SELECT '0' as id, 'Inactive' as info;", $active, '', "active", "id", "info", 
                "", 6);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>><?php echo $submit ?></button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
