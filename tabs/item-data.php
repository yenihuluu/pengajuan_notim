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

$vehicleId = '';
$itemId = '';
$itemCode = '';
$vehicleName = '';
$groupitemId='';
$uomId = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['itemId']) && $_POST['itemId'] != '') {
    
    $itemId = $_POST['itemId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vehicle Data">
    
    $sql = "SELECT i.*, g.group_name, g.idmaster_groupitem, u.*
		FROM master_item i
		left join master_groupitem g on g.idmaster_groupitem = i.group_itemid
		left join UOM u on u.idUOM = i.uom_id
		where i.idmaster_item = {$itemId}
        ORDER BY i.idmaster_item ASC";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $itemName = $rowData->item_name;
		$itemCode = $rowData->item_code;
		$uomId = $rowData->uom_id;
		$groupitemId = $rowData->idmaster_groupitem;
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
                itemName: "required",
				itemCode: "required",
				uom: "required",
				groupitemId: "required"
            },
            messages: {
                itemName: "Item name is a required field.",
				itemCode: "Item Code is a required field.",
				uom: "UOM is a required field.",
				groupitemId: "Group is a required field."				
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
                                document.getElementById('itemId').value = returnVal[3];
                                
                                $('#dataContent').load('contents/item.php', { itemId: returnVal[3] }, iAmACallbackFunction2);

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
    $('#groupitemId').change(function() {
            if(document.getElementById('groupitemId').value != '') {
				//window.alert($(this).find('option:selected').val());
				var itemID = $(this).find('option:selected').val();
             $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getItemNo',
				   groupitemId: itemID
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
				//window.alert(data);
                if(data != '') {
					
                    document.getElementById('itemCode').value = data;
					//$('#addInvoice').hide();
					
               }
				//setInvoiceType(generatedInvoiceNo);
            }
			
        });
				
        }
		
		});                
	
	
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
    <input type="hidden" name="action" id="action" value="item_data" />
    <input type="hidden" name="itemId" id="itemId" value="<?php echo $itemId; ?>" />
    <div class="row-fluid">
		<div class="span4 lightblue">
        <label>Group Item<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT idmaster_groupitem,group_name FROM master_groupitem", $groupitemId, "", "groupitemId", "idmaster_groupitem", "group_name", "", "", "select2combobox100");
            ?>
      </div>
        
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	</br>
	 <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Item Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="itemName" name="itemName" value="<?php echo $itemName; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	 <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Item Code (Unique)<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" readonly id="itemCode" name="itemCode" value="<?php echo $itemCode; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>UOM<span style="color: red;">*</span></label> <?php
            createCombo("SELECT idUOM,uom_type FROM uom", $uomId, "", "uomId", "idUOM", "uom_type", "", "", "select2combobox100");
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
