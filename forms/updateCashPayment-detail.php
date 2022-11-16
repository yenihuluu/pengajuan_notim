<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$paymentCashId = '';
$paymentId = $_POST['paymentId'];
$accountId = ''; 
$notes = '';
$paymentCashType = 0;


// </editor-fold>

if(isset($_POST['paymentCashId']) && $_POST['paymentCashId'] != '') {
    $paymentCashId = $_POST['paymentCashId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

    $sql = "SELECT pc.* 
            FROM payment_cash pc
            WHERE pc.payment_cash_id = {$paymentCashId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $accountId = $rowData->account_id;
        $notes = $rowData->notes;
		$paymentCashType = $rowData->type;
		$stockpileId2 = $rowData->stockpile_remark;
		$shipmentId1 = $rowData->shipment_id;
		$uom = $rowData->idUOM;
        

    }

    // </editor-fold>
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
		
        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
	} else if($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
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
		
		$('#paymentCashType').change(function() {
			if(document.getElementById('paymentCashType').value != '') {
			resetAccount(' ');
            setAccount(0, $('select[id="paymentCashType"]').val(), 0);
			} else {
                resetAccount(' paymentCash Type ');
            }
        });
		
		$('#stockpileId2').change(function() {
			if(document.getElementById('stockpileId2').value != '' && document.getElementById('accountId').value != '') {
			//resetPoNo(' ');
			resetShipmentCode(' Shipment Code ');
            //setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
			setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
			} else {
                //resetPoNo(' PO NO ');
				resetShipmentCode(' Shipment Code ');
            }
        });
		
		<?php if($paymentCashType != '') {?>
                  
		setAccount(1, $('select[id="paymentCashType"]').val(), <?php echo $accountId; ?>);
   
		<?php }?>
		
		<?php if($stockpileId2 != '') {?>
		
                  
		setShipmentCode(1, $('select[id="stockpileId2"]').val(), <?php echo $shipmentId1; ?>);
   
		<?php }?>

       /* if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
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
	
	function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }
    
    function setAccount(type, paymentCashType, accountId) {
		//alert(type);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountUpdatePaymentCash',
                    paymentCashType: paymentCashType
					
            },
            success: function(data){
               //alert(data); 
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
                        document.getElementById('accountId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('accountId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('accountId').options.add(x);
                    }
				
				if(type == 1) {
                        $('#accountId').find('option').each(function(i,e){
                            if($(e).val() == accountId){
                                $('#accountId').prop('selectedIndex',i);
								
								  $("#accountId").select2({
                                    width: "100%",
                                    placeholder: accountId
                                });
                            }
                        });
				}
				
				
                }
            }
        });
    }
	
	function resetShipmentCode(text) {
        document.getElementById('shipmentId1').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId1').options.add(x);
    }
    
    function setShipmentCode(type, stockpileId2, shipmentId1) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipmentInvoice',
                    stockpileId2:stockpileId2
					
            },
            success: function(data){
               //alert(data); 
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
                        document.getElementById('shipmentId1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId1').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId1').options.add(x);
                    }
				
				if(type == 1) {
                        $('#shipmentId1').find('option').each(function(i,e){
                            if($(e).val() == shipmentId1){
                                $('#shipmentId1').prop('selectedIndex',i);
								
								 $("#shipmentId1").select2({
                                    width: "100%",
                                    placeholder: shipmentId1
                                });
                            }
                        });
				}
                
                }
            }
        });
    }
</script>

<input type="hidden" id="paymentCashId" name="paymentCashId" value="<?php echo $paymentCashId; ?>">
<input type="hidden" id="paymentId" name="paymentId" value="<?php echo $paymentId; ?>">
<input type="hidden" id="accountIdOld" name="accountIdOld" value="<?php echo $accountId; ?>">
<input type="hidden" id="stockpileId2Old" name="stockpileId2Old" value="<?php echo $stockpileId2; ?>">
<input type="hidden" id="shipmentId1Old" name="shipmentId1Old" value="<?php echo $shipmentId1; ?>">
<input type="hidden" id="uomOld" name="uomOld" value="<?php echo $uom; ?>">
<input type="hidden" id="notesOld" name="notesOld" value="<?php echo $notes; ?>">
<input type="hidden" id="paymentCashTypeOld" name="paymentCashTypeOld" value="<?php echo $paymentCashType; ?>">

<div class="row-fluid">
<div class="span12 lightblue">
        <label>Type<span style="color: red;">*</span></label>
		<?php 
        createCombo("SELECT '4' as id, 'Loading' as info UNION
                    SELECT '5' as id, 'Umum' as info UNION
					SELECT '6' as id, 'HO' as info;", $paymentCashType, "", "paymentCashType", "id", "info", 
                "", 11, "select2combobox100");?>
    </div>
</div>
<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Account <span style="color: red;">*</span></label>
       
        <?php createCombo("", $accountId, "", "accountId", "account_id", "account_full", "", "", "select2combobox100", 1);	?>
       
    </div>
</div>
<div class="row-fluid">
<div class="span12 lightblue">
        <label>Remark (Stockpile)<span style="color: red;">*</span></label>
		<?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId2, "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);?>	
    </div>
</div> 
<div class="row-fluid">
<div class="span12 lightblue">
        <label>Shipment Code</label>
	
		<?php createCombo("", $shipmentId1, "", "shipmentId1", "shipment_id", "shipment_no", "", "", "select2combobox100", 4);	?>
    </div>
</div> 
<div class="row-fluid">	
<div class="span12 lightblue">
            <label>UOM <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT *
                    FROM uom 
                    ORDER BY uom_type ASC", $uom, "", "uom", "idUOM", "uom_type", 
                    "", "", "select2combobox100");
            ?>
        </div> 
</div> 
<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span10" rows="3" tabindex="7" id="notes" name="notes"><?php echo $notes; ?></textarea>
    </div>
</div>

