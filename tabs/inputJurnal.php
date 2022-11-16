<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'JurnalMemorial';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Vendor Data">

$gl_add_id = '';
$generatedJurnalNo = '';
$stockpile_id = '';
$generalVendorId = '';
$contract_id = '';
$transaction_id = '';
$shipment_id = '';
$invoice_id = '';
$quantity = '';
$price = '';


// </editor-fold>
$date = new DateTime();
$gl_add_date = $date->format('d/m/Y');
// If ID is in the parameter
if(isset($_POST['gl_add_id']) && $_POST['gl_add_id'] != '') {
    
    $gl_add_id = $_POST['gl_add_id'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    
   /* $sql = "SELECT gv.*
            FROM general_vendor gv
            WHERE gv.general_vendor_id = {$vendorId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $vendorName = $rowData->general_vendor_name;
        $vendorAddress = $rowData->general_vendor_address;
        $npwp = $rowData->npwp;
        $bankName = $rowData->bank_name;
        $accountNo = $rowData->account_no;
        $beneficiary = $rowData->beneficiary;
        $swiftCode = $rowData->swift_code;
        $taxable = $rowData->taxable;
        $ppn = $rowData->ppn_tax_id;
        $pph = $rowData->pph_tax_id;
		$active = $rowData->active;
    }*/
    
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

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
        
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
	   $('#price').number(true, 2);
	   
    
    $(document).ready(function(){
		
		 $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getJurnalNo'
                    //stockpilecontract_id: stockpilecontract_id,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                if(data != '') {
                    document.getElementById('generatedJurnalNo').value = data;
					//$('#addInvoice').hide();
					
                }
			//	setInvoiceType(generatedInvoiceNo);
            }
			
        });
		
		$('#stockpile_id').change(function() {
			if(document.getElementById('stockpile_id').value != '') {
			resetSlipNo(' ');
			resetPoNo(' ');
			resetShipmentCode(' ');
			resetInvoice(' ');
            setSlipNo(0, $('select[id="stockpile_id"]').val(), 0);
			setPoNo(0, $('select[id="stockpile_id"]').val(), 0);
			setShipmentCode(0, $('select[id="stockpile_id"]').val(), 0);
			setInvoice(0, $('select[id="stockpile_id"]').val(), 0);
			} else {
                resetSlipNo(' Slip No ');
				resetPoNo(' PO No ');
				resetShipmentCode(' Shipment Code ');
				resetInvoice(' Invoice No ');
            }
        });
	
	<?php
        if(isset($_SESSION['jurnalMemorial'])) {
        ?>         
            if(document.getElementById('stockpile_id').value != '') {
                resetSlipNo(' ');
				resetPoNo(' ');
				resetShipmentCode(' ');
				resetInvoice(' ');
                <?php
                if($_SESSION['jurnalMemorial']['contract_id'] != '' ||  $_SESSION['jurnalMemorial']['transaction_id'] != '' ||  $_SESSION['jurnalMemorial']['shipment_id'] != '' ||  $_SESSION['jurnalMemorial']['invoice_id'] != '') {
                ?>
                setSlipNo(1, $('select[id="stockpile_id"]').val(), <?php echo $_SESSION['jurnalMemorial']['transaction_id']; ?>);
				setPoNo(1, $('select[id="stockpile_id"]').val(), <?php echo $_SESSION['jurnalMemorial']['contract_id']; ?>);
				setShipmentCode(1, $('select[id="stockpile_id"]').val(), <?php echo $_SESSION['jurnalMemorial']['shipment_id']; ?>);
				setInvoice(1, $('select[id="stockpile_id"]').val(), <?php echo $_SESSION['jurnalMemorial']['invoice_id']; ?>);
                <?php
                } else {
                ?>
                setSlipNo(0, $('select[id="stockpile_id"]').val(), 0);
				setPoNo(0, $('select[id="stockpile_id"]').val(), 0);
				setShipmentCode(0, $('select[id="stockpile_id"]').val(), 0);
				setInvoice(0, $('select[id="stockpile_id"]').val(), 0);
                <?php
                }
                ?>
                
            } else {
                 resetSlipNo(' Slip No ');
				 resetPoNo(' PO No ');
				 resetShipmentCode(' Shipment Code ');
				 resetInvoice(' Invoice No ');
            }
            
        <?php
        
		}
        ?>
	
	function resetSlipNo(text) {
        document.getElementById('transaction_id').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('transaction_id').options.add(x);
    }
    
    function setSlipNo(type, stockpile_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getJurnalSlip',
                    stockpile_id: stockpile_id
					
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
                        document.getElementById('transaction_id').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('transaction_id').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('transaction_id').options.add(x);
                    }
				
                if(type == 1) {
                        $('#transaction_id').find('transaction_id').each(function(i,e){
                            if($(e).val() == transaction_id){
                                $('#transaction_id').prop('selectedIndex',i);
                            }
                        });
				}
                }
            }
        });
    }
	
	
		function resetPoNo(text) {
        document.getElementById('contract_id').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contract_id').options.add(x);
    }
    
    function setPoNo(type, stockpile_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getJurnalPo',
                    stockpile_id: stockpile_id
					
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
                        document.getElementById('contract_id').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('contract_id').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contract_id').options.add(x);
                    }
				
                if(type == 1) {
                        $('#contract_id').find('contract_id').each(function(i,e){
                            if($(e).val() == contract_id){
                                $('#contract_id').prop('selectedIndex',i);
                            }
                        });
				}
                }
            }
        });
    }
	
	function resetShipmentCode(text) {
        document.getElementById('shipment_id').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipment_id').options.add(x);
    }
    
    function setShipmentCode(type, stockpile_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getJurnalShipment',
                    stockpile_id: stockpile_id
					
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
                        document.getElementById('shipment_id').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipment_id').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipment_id').options.add(x);
                    }
				
                if(type == 1) {
                        $('#shipment_id').find('shipment_id').each(function(i,e){
                            if($(e).val() == shipment_id){
                                $('#shipment_id').prop('selectedIndex',i);
                            }
                        });
				}
                }
            }
        });
    }
	
	function resetInvoice(text) {
        document.getElementById('invoice_id').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoice_id').options.add(x);
    }
    
    function setInvoice(type, stockpile_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getJurnalInvoice',
                    stockpile_id: stockpile_id
					
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
                        document.getElementById('invoice_id').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoice_id').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoice_id').options.add(x);
                    }
				
                if(type == 1) {
                        $('#invoice_id').find('invoice_id').each(function(i,e){
                            if($(e).val() == invoice_id){
                                $('#invoice_id').prop('selectedIndex',i);
                            }
                        });
				}
                }
            }
        });
    }
	
		
        $("#jurnalDataForm").validate({
            rules: {
              /*  vendorName: "required",
                vendorAddress: "required",
                npwp: "required",
                ppn: "required",
                pph: "required",
				active: "required"*/
            },
            messages: {
               /* vendorName: "Vendor Name is a required field.",
                vendorAddress: "Vendor Address is a required field.",
                npwp: "Tax ID is a required field.",
                ppn: "PPN is a required field.",
                pph: "PPh is a required field.",
				active: "Status is a required field."*/
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#jurnalDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('gl_add_id').value = returnVal[3];
                                
                                $('#dataContent').load('forms/inputJurnal.php', { gl_add_id: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
    $("#insertForm").validate({
			 
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                var resultData = returnVal[3].split('~');
                                 setJurnalDetail();
                               /* if (resultData[0] == 'INVOICE_DETAIL') {
                                    //setContract(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
//                                    resetFreight(' ');
//                                    setFreight(0, resultData[1], 0);
                                   
                                } */
                                
                                $('#insertModal').modal('hide');
                            } else {
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
                        }
                    }
                });
            }
        });
		
		
        
    });
</script>
<script type="text/javascript">
$(document).ready(function(){
 $('#showTransaction').click(function(e){
            e.preventDefault();  
        $('#insertModal').modal('show');
        $('#insertModalForm').load('forms/jurnal-data.php', {});
        });	
});

</script>
<script type="text/javascript">
                    
	
	function deleteJurnalDetail(gl_detail_id) {
		
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: { action: 'delete_jurnal_detail',
					jurnalDetailId: gl_detail_id
				/*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo */
            },
            success: function(data){
                if(data != '') {
                     setJurnalDetail();
                }
            }
        });
    }
	
	function setJurnalDetail() {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setJurnalDetail',
				/*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo */
            },
            success: function(data){
                if(data != '') {
                    $('#jurnalDetail').show();
                    document.getElementById('jurnalDetail').innerHTML = data;
                } else {
					$('#jurnalDetail').hide();
				}
            }
        });
    }
	



</script>
<script type="text/javascript">
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>

<form method="post" id="jurnalDataForm">
    <input type="hidden" name="action" id="action" value="jurnal_data" />
    <input type="hidden" name="gl_add_id" id="gl_add_id" value="<?php echo $gl_add_id; ?>" />
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="inputDate" name="gl_add_date"  value="<?php echo $gl_add_date; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            <input type="hidden" readonly tabindex="" id="generatedJurnalNo" name="generatedJurnalNo"  value="<?php echo $generatedJurnalNo; ?>" >
        </div>
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpile_id, "", "stockpile_id", "stockpile_id", "stockpile_full", 
                    "", "", "select2combobox100");
            ?>
     	
        </div>
        <div class="span3 lightblue">
            <label>Invoice No.</label>
            <?php createCombo("", $invoice_id, "", "invoice_id", "id", "info", "", "", "select2combobox100", 2);	?>
        </div>
		<div class="span3 lightblue">
            <label>PO No.</label>
            <?php createCombo("", $contract_id, "", "contract_id", "id", "info", "", "", "select2combobox100", 2);	?>
        </div>
        
    </div>
    <div class="row-fluid">  
        <div class="span3 lightblue">
             <label>Slip No.</label>
            <?php createCombo("", $transaction_id, "", "transaction_id", "id", "info", "", "", "select2combobox100", 2);	?>
        </div>
        <div class="span3 lightblue">
           <label>Shipment Code</label>
           <?php createCombo("", $shipment_id, "", "shipment_id", "id", "info", "", "", "select2combobox100", 2);	?>
        </div>
        <div class="span3 lightblue">
            <label>General Vendor<span style="color: red;"></span></label>
            <?php
           createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name ASC", $general_vendor_id, "", "general_vendor_id", "general_vendor_id", "general_vendor_name", 
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Vendor PKS<span style="color: red;"></span></label>
            <?php
           createCombo("SELECT v.vendor_id, v.vendor_name FROM vendor v WHERE v.active = 1 ORDER BY v.vendor_name ASC", $vendor_id, "", "vendor_id", "vendor_id", "vendor_name", 
                "", "", "select2combobox100");
            ?>
        </div>
    </div>
     <div class="row-fluid">
		 <div class="span3 lightblue">
             <label>Quantity</label>
            <input type="text" class="span12" tabindex="" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
        </div>
        <div class="span3 lightblue">
           <label>Price</label>
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div class="span3 lightblue">
             
        </div>
       
        <div class="span3 lightblue">
            
        </div>
    </div>
    <div id="addJurnal" class="row-fluid" style="margin-bottom: 7px;">  
     	<div class="span3 lightblue">
        <button class="btn btn-warning" id="showTransaction">Add Data</button>
       
        </div>
        <div class="span1 lightblue">
       		
        </div>
        <div class="span3 lightblue">
         </div>
        <div class="span1 lightblue">
        </div>
         
    </div>
    <div class="row-fluid" id="jurnalDetail" style="display: none;">
        jurnal detail
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
          <label>Description</label>
            <textarea class="span12" rows="3" tabindex="3" id="notes" name="notes"><?php echo $notes; ?></textarea>   
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
<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" style="width:750px; height:500px; margin-left:-400px;" >
    <form id="insertForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="insertModalLabel">Insert New</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="insertModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>