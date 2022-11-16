<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$whereProperty = '';
$vendorId = $_POST['vendorId'];
$gv_pph_id = '';
$pph_tax_id = '';
$status = '';


// </editor-fold>

if(isset($_POST['gv_pph_id']) && $_POST['gv_pph_id'] != '') {
    $gv_pph_id = $_POST['gv_pph_id'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">
    
    $sql = "SELECT * FROM general_vendor_pph gvPPh left join tax tx on tx.tax_id = gvPPh.pph_tax_id WHERE gv_pph_id = {$gv_pph_id}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $pph_tax_id = $rowData->pph_tax_id;
        $status = $rowData->status;
		$taxName = $rowData->tax_name;
		$gv_pph_id = $rowData->gv_pph_id;
        
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
    $(document).ready(function(){
		$("select.select2combobox100").select2({
            width: "100%"
        });
        
        $("select.select2combobox50").select2({
            width: "50%"
        });
        
        $("select.select2combobox75").select2({
            width: "75%"
        });
		
        /*if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRateFreight').hide();
        } else {
            $('#exchangeRateFreight').show();
        }
            
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateFreight').hide();
            } else {
                $('#exchangeRateFreight').show();
            }
        });*/
		
    });
</script>

<!--<input type="hidden" id="freightCostId" name="freightCostId" value="<?php //echo $freightCostId; ?>">-->
<input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>">
<input type="hidden" id="gv_pph_id" name="gv_pph_id" value="<?php echo $gv_pph_id; ?>">

<div class="row-fluid">   
    <div class="span6 lightblue">
        <label>PPh <span style="color: red;">*</span></label>
        <?php
		if($gv_pph_id == ''){
        createCombo("SELECT tx.tax_id, tx.tax_name
                FROM tax tx WHERE tx.tax_type = 2 AND tx.tax_id NOT IN (SELECT pph_tax_id FROM general_vendor_pph WHERE general_vendor_id = {$vendorId})
                ORDER BY tx.tax_id DESC", "", $pph_tax_id, "taxId", "tax_id", "tax_name", 
                "", 1, "select2combobox100");
		}else{		
        ?>
		<input type="text" readonly id="taxName" name="taxName" value="<?php echo $taxName; ?>">
		<input type="hidden" id="taxId" name="taxId" value="<?php echo $pph_tax_id; ?>">
		<?php
		}
		?>
    </div>
	<div class="span6 lightblue">
           <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $status, '', "status", "id", "info", 
                "", 6);
            ?>
        </div>
</div>


