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

// <editor-fold defaultstate="collapsed" desc="Variable for Bank Data">

$salesDetailId = '';
$salesHeaderId = '';
$vessel = '';
$shipmentNo = '';
$quantity = '';
$statsType = '';
$stockpileId = '';
$customerId = '';
$loadingDate='';
$plus = '';
$ncv = '';
$tm = '';
$fm = '';
$mt = '';
$salesNo = '';
if(isset($_POST['salesHeaderId']) && $_POST['salesHeaderId'] != '') {
    
    $salesHeaderId = $_POST['salesHeaderId'];
}
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['salesDetailId']) && $_POST['salesDetailId'] != '') {
    
    $salesDetailId = $_POST['salesDetailId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Bank Data">
    
    $sql = "select sh.*,sd.* 
			from sales_detail sd 
			left join sales_header sh on sd.sales_header_id=sh.sales_header_id
            WHERE sd.sales_detail_id = {$salesDetailId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vessel = $rowData->vessel;
        $shipmentNo = $rowData->shipment_no;
        $quantity = $rowData->qty;
		$customerId = $rowData->customer_id;
        $statsType = $rowData->stats;
		$stockpileId = $rowData->stockpile_id;
		$tolerance = $rowData->tolerance;
		$ncv = $rowData->ncv;
		$tax_base = $rowData->tax_base;
		$fm = $rowData->fm;
		$fob = $rowData->fob;
		$moisture = $rowData->moisture;
		$salesHeaderId = $rowData->sales_header_id;
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
        $('#quantity').number(true, 2);
        $('#ncv').number(true, 2);
        $('#tm').number(true, 2);
        $('#fm').number(true, 2);
        $('#mt').number(true, 2);
		
        
        $("#salesDetailDataForm").validate({
            rules: {
                vessel: "required",
                salesHeaderId: "required",
                salesNo: "required",
                quantity: "required",
                statsType: "required",
				customerId: "required",
				ncv: "required",
				tax_base: "required",
				fm: "required",
				fob: "required",
				stockpileId: "required"
            },
            messages: {
                vessel: "Vessel is a required field.",
                customerId: "Buyer is a required field.",
                salesNo: "Sales No. is a required field.",
                quantity: "Opening Balance is a required field.",
                statsType: "Bank type is a required field.",
				ncv: "NCV is a required field",
				tax_base: "Tax Base is a required field",
				fm: "FM is a required field",
				fob: "MT is a required field",
				stockpileId: "Stockpile code is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#salesDetailDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('salesDetailId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/sales-detail.php', { salesDetailId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="salesDetailDataForm">
    <input type="hidden" name="action" id="action" value="sales_detail_data" />
    <input type="hidden" name="salesDetailId" id="salesDetailId" value="<?php echo $salesDetailId; ?>" />
	<input type="hidden" name="salesDetailId" id="salesDetailId" value="<?php echo $salesHeaderId; ?>" />
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Sales No <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT sales_no,sales_header_id FROM sales_header ORDER BY sales_header_id ASC", $salesHeaderId, '', "salesHeaderId", "sales_header_id", "sales_no", 
                "", 3);
            ?>
        </div>
		     
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>End User<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM customer ORDER BY customer_name", $customerId, '', "customerId", "customer_id", "customer_name", 
                "", 2);
            ?>
        </div>
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
		<?php
            createCombo("SELECT * FROM stockpile ORDER BY stockpile_name ASC", $stockpileId, '', "stockpileId", "stockpile_id", "stockpile_name", 
                "", 3);
            ?>
        </div>       
		
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Vessel<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="5" id="vessel" name="vessel" value="<?php echo $vessel; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Status<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT 'Potential' as id, 'Potential' as info UNION
                    SELECT 'Confirm' as id, 'Confirm' as info ;", $statsType, '', "statsType", "id", "info", 
					"", 6);
            ?>
        </div>
         <div class="span4 lightblue">
            <label>Shipment No </label>
            <input type="text" class="span12" tabindex="7" id="shipmentNo" name="shipmentNo" value="<?php echo $shipmentNo; ?>">
        </div>
    </div>
	
    <div class="row-fluid">          
        <div class="span4 lightblue">
            <label>Quantity <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="8" id="quantity" name="quantity" value="<?php echo number_format($quantity, 0, ".", ","); ?>">
        </div>
         <div class="span4 lightblue">
            <label>Tolerance<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, '0%' as info UNION
                    SELECT '5' as id, '5%' as info UNION
					SELECT '10' as id, '10%' as info;", $tolerance, '', "tolerance", "id", "info", 
					"", 9);
            ?>
        </div>
		<div class="span4 lightblue">
            <label>Moisture <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="8" id="moisture" name="moisture" value="<?php echo number_format($moisture, 0, ".", ","); ?>">
        </div>
		</div>
		<div class="row-fluid"> 
         <div class="span4 lightblue">
            <label>NCV <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="10" id="ncv" name="ncv" value="<?php echo number_format($ncv, 0, ".", ","); ?>">
        </div>
    </div>
	
	<div class="row-fluid">          
        <div class="span4 lightblue">
            <label>Tax Base <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="11" id="tax_base" name="tax_base" value="<?php echo number_format($tax_base, 0, ".", ","); ?>">
        </div>
		<div class="span4 lightblue">
            <label>FM <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="12" id="fm" name="fm" value="<?php echo number_format($fm, 2, ".", ","); ?>">
        </div>
		<div class="span4 lightblue">
            <label>FOB <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="13" id="fob" name="fob" value="<?php echo number_format($fob, 2, ".", ","); ?>">
        </div>        
    </div>
    
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
