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

// <editor-fold defaultstate="collapsed" desc="Variable for Vehicle Data">

$item_id = '';
$item = '';
$category = '';
$status = '';


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['item_id']) && $_POST['item_id'] != '') {
    
    $item_id = $_POST['item_id'];
    
    $readonlyProperty = 'ReadOnly';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vehicle Data">
    
    $sql = "SELECT i.*
		FROM perdin_item i
		WHERE i.item_id = {$item_id}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $item = $rowData->item;
		$category = $rowData->category;
		$status = $rowData->status;
		//$groupitemId = $rowData->idmaster_groupitem;
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
		
		$(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
		
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#itemDataForm").validate({
            rules: {
                item: "required",
				category: "required",
				status: "required"
            },
            messages: {
                item: "This is a required field.",
				category: "This is a required field.",
				status: "This is a required field."				
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#itemDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('item_id').value = returnVal[3];
                                
                                $('#dataContent').load('contents/perdin-item.php', { item_id: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="itemDataForm">
    <input type="hidden" name="action" id="action" value="perdin_item_data" />
    <input type="hidden" name="item_id" id="item_id" value="<?php echo $item_id; ?>" />
    
	</br>
	 <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Item Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="item" name="item" value="<?php echo $item; ?>" <?php echo $readonlyProperty?>>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	 <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Category<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'HR Departement' as info UNION
                    SELECT '1' as id, 'General' as info;", $category, '', "category", "id", "info", 
                "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Status<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $status, '', "status", "id", "info", 
                "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	</br>
	<div class="row-fluid">
       <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
