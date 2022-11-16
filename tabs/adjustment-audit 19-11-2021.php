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

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

$auditId = '';
$stockpileId = '';
$shipmentId = '';
$quantity = '';
$cogsHandling = '';
$cogsPKS = '';
$cogsOA = '';
$cogsOB = '';
$adjustmentDate = '';
$notes = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['auditId']) && $_POST['auditId'] != '') {
    
    $auditId = $_POST['auditId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for User Data">
    
    $sql = "SELECT a.audit_id,a.shipment_id,a.stockpile_id,a.quantity,a.cogs_pks,a.cogs_oa,a.cogs_ob,a.cogs_handling,DATE_FORMAT(a.adjustment_date, '%d/%m/%Y') AS adjustment_date,a.notes FROM adjustment_audit a 
			LEFT JOIN shipment b ON a.shipment_id = b.shipment_id
			LEFT JOIN stockpile c ON c.stockpile_id = a.stockpile_id WHERE audit_id = {$auditId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $auditId = $rowData->audit_id;
        $stockpileId = $rowData->stockpile_id;
        $shipmentId = $rowData->shipment_id;
		$quantity = $rowData->quantity;
		$cogsPKS = $rowData->cogs_pks;
		$cogsOA = $rowData->cogs_oa;
		$cogsOB = $rowData->cogs_ob;
		$cogsHandling = $rowData->cogs_handling;
		$adjustmentDate = $rowData->adjustment_date;
		$notes = $rowData->notes;
		
        
    }else {
    
    if(isset($_SESSION['adjustment'])) {
        $auditId = $_SESSION['adjustment']['auditId'];
        $stockpileId = $_SESSION['adjustment']['stockpileId'];
        $shipmentId = $_SESSION['adjustment']['shipmentId'];
		$quantity = $_SESSION['adjustment']['quantity'];
		$cogsPKS = $_SESSION['adjustment']['cogsPKS'];
		$cogsOA = $_SESSION['adjustment']['cogsOA'];
		$cogsOB = $_SESSION['adjustment']['cogsOB'];
		$cogsHandling = $_SESSION['adjustment']['cogsHandling'];
		$adjustmentDate = $_SESSION['adjustment']['adjustmentDate'];
		
     
    }
}
    // </editor-fold>
    
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
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
        if(strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
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
        
        $('#quantity').number(true, 2);
		
		 
		
		$('#stockpileId').change(function() {
            //resetShipment(' ');
            
            if(document.getElementById('stockpileId').value != '') {
				setShipment($('select[id="stockpileId"]').val());
            } 
        });
        
		
		function setShipment(stockpileId) {

			$.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getShipmentAudit',
					stockpileId:stockpileId
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('shipmentId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId').options.add(x);

                        $("#shipmentId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId').options.add(x);
                    }



                    
                }
				//setContract(contract);
            }

        });

		}
		
		$('#generateUnitCost').click(function(e){
            
                e.preventDefault();

                if(document.getElementById('shipmentId').value != '' && document.getElementById('quantity').value != '') {
					setUnitCost($('select[id="shipmentId"]').val(),$('input[id="quantity"]').val());
					
				}else{
				
					alert('Please Choose Shipment Code & Input Quantity');
				}
            });
			
			
		function setUnitCost(shipmentId,quantity) {
		
			var shipment_id = $('select[id="shipmentId"]').val();
			var qty = $('input[id="quantity"]').val();
			
			
		
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: { action: 'set_unit_cost',
                    shipmentId: shipmentId,
					quantity: quantity
                   // paymentMethod: paymentMethod,
                   // ppn: ppnValue,
                   // pph: pphValue
            },
            success: function(data){
                if(data != '') {
                   // $('#summaryContract').show();
                   // document.getElementById('unitCost').value = data; 
				   getUnitCost(shipmentId);
               
                }
            }
        });
    }	
			
		function getUnitCost(shipmentId) {
      
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getUnitCost',
                    shipmentId: shipmentId
                   // paymentMethod: paymentMethod,
                   // ppn: ppnValue,
                   // pph: pphValue
            },
            success: function(data){
                if(data != '') {
					var returnVal = data.split('||');
                   // $('#summaryContract').show();
                    document.getElementById('cogsPKS').value = returnVal[0]; 
					document.getElementById('cogsOA').value = returnVal[1]; 
					document.getElementById('cogsOB').value = returnVal[2]; 
					document.getElementById('cogsHandling').value = returnVal[3]; 
               
                }
            }
        });
    }
	
	
			
			
       $("#adjustmentAuditDataForm").validate({
            rules: {
                stockpileId: "required",
                shipmentId: "required",
                quantity: "required",
				//unitCost: "required",
				adjustmentDate: "required"
			},
            messages: {
                stockpileId: "Stockpile is a required field.",
                shipmentId: "Shipment Code is a required field.",
                quantity: "Quantity is a required field.",
				//unitCost: "Unit Cost is a required field.",
				adjustmentDate: "Adjustment Date is a required field."
                
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#adjustmentAuditDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalAuditId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/adjustment-audit.php', { auditId: returnVal[3] }, iAmACallbackFunction2);

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
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });
</script>

<form method="post" id="adjustmentAuditDataForm">
    <input type="hidden" name="action" id="action" value="adjustment_audit" />
<input type="hidden" class="span12" tabindex="" id="auditId" name="auditId" value="<?php echo $auditId; ?>" >	
    <div class="row-fluid"> 
        <div class="span4 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
             <?php
             createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                    "", 4, "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Shipment Code<span style="color: red;">*</span></label>
            <?php
            createCombo("", "", $shipmentId, "shipmentId", "shipment_id", "shipment_id",
                "", 2, "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
        	<label>Adjustment Date <span style="color: red;">*</span></label>
			<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="adjustmentDate" name="adjustmentDate" value="<?php echo $adjustmentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Quantity <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="quantity" name="quantity" value="<?php echo $quantity; ?>" >
        </div>
        
        
    </div>
    <div class="row-fluid">  
        <div class="span2 lightblue">
			<label></label>
			<br>
            <button class="btn btn-warning" id="generateUnitCost">Generate Unit Cost</button>
			
        </div>
        <div class="span2 lightblue">
		<label>COGS PKS<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="cogsPKS" name="cogsPKS" value="<?php echo $cogsPKS;?>">
        </div>
		<div class="span2 lightblue">
		<label>COGS OA<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="cogsOA" name="cogsOA" value="<?php echo $cogsOA;?>">
        </div>
		<div class="span2 lightblue">
		<label>COGS OB<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="cogsOB" name="cogsOB" value="<?php echo $cogsOB;?>">
        </div>
		<div class="span2 lightblue">
		<label>COGS Handling<span style="color: red;">*</span></label>
            <input readonly type="text" class="span12" tabindex="" id="cogsHandling" name="cogsHandling" value="<?php echo $cogsHandling;?>">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>
        <div class="span4 lightblue">
            
        </div>
        <div class="span4 lightblue">
            
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
