<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowFilter = false;


if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 26) {
            $allowFilter = true;
        } 
    }
}

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } else if($empty == 7) {
        echo "<option value= ''>-- All --</option>";
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">

    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });
	
    $(document).ready(function(){
           
        
        $(".select2combobox100").select2({
            width: "250px"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        $('#amount').number(true, 2);
		
		/*$('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
			if($('select[id="searchVendorId"]').val() != '') {
            $('#dataContent').load('reports/po-summary-report-curah.php', {
               
				vendorId: $('select[id="searchVendorId"]').val() ,
				searchStatus: $('select[id="searchStatus"]').val()
               
            }, iAmACallbackFunction2);
			} else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Vendor & Status is required field.");
            }
        });*/
		
		 $('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
			if($('select[id="searchStatus"]').val() != '') {
				                $.blockUI({ message: '<h4>Please wait...</h4>' }); 

            $('#dataContent').load('reports/po-summary-report-curah.php', {
                vendorId: $('select[id="searchVendorId"]').val() ,
				searchStatus: $('select[id="searchStatus"]').val()
            }, iAmACallbackFunction2);
			} else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Please fill the required field.");
            }
        });
		
		
	});
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" id="searchForm" method="post">
        	<div class="control-group">
                <label class="control-label" for="searchVendorId">Vendor</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT DISTINCT v.vendor_id, v.vendor_name                 
								FROM vendor v
                                LEFT JOIN contract con
                                    ON con.vendor_id = v.vendor_id
                                
                                WHERE con.contract_type = 'C' AND DATE_FORMAT(con.entry_date, '%Y-%m-%d') > '2019-08-01'", "", "", "searchVendorId", "vendor_id", "vendor_name", 
                                "", 3, "select2combobox100", 1, "multiple");
                    ?>
                </div>
				
            </div>
            <div class="control-group">
                <label class="control-label" for="searchStatus">Status<span style="color: red;">*</span></label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT '0' AS id, 'Curah' AS info UNION
                                SELECT '1' AS id, 'Return Shipment' AS info", "", "", "searchStatus", "id", "info", 
                                "", 7, "", 1);
                    ?>
                </div>
				</div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>