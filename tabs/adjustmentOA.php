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

/*$gl_add_id = '';
$generatedJurnalNo = '';
$stockpileId = '';
$generalVendorId = '';
$contract_id = '';
$transaction_id = '';
$shipment_id = '';
$invoice_id = '';
$quantity = '';
$price = '';*/


// </editor-fold>
$date = new DateTime();
$gl_add_date = $date->format('d/m/Y');
// If ID is in the parameter
if(isset($_POST['gl_add_id']) && $_POST['gl_add_id'] != '') {
    
    //$gl_add_id = $_POST['gl_add_id'];
    
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
        
       //$('#quantity').number(true, 2);
	  // $('#price').number(true, 2);
	   
    
    $(document).ready(function(){
		
		/* $.ajax({
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
			
        });*/
		
		$('#stockpileId').change(function() {
			if(document.getElementById('stockpileId').value != '') {
				resetSupplier(' ');
                setSupplier(0, $('select[id="stockpileId"]').val(), 0);
			}
        });
		
		 $('#freightId').change(function() {
            resetVendorFreight(' ');
			//resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

			//resetVendorBank ($('select[id="paymentFor"]').val());

            if(document.getElementById('freightId').value != '') {
                setVendorFreight(0, $('select[id="stockpileId"]').val(), $('select[id="freightId"]').val(), 0);
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
				 //getVendorBank(0,$('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
            }
        });
		
		 $('#vendorFreightId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			 resetSlipNo(' ');


		//bikin alert dsini
		//alert('html from to nya');

            if(document.getElementById('vendorFreightId').value != '' && document.getElementById('freightId').value != '') {
                //if(document.getElementById('paymentMethod').value == 2) {
                    //refreshSummaryFreight($('select[id="freightId"]').val());
                //} else if(document.getElementById('paymentMethod').value == 1) {

					//alert(document.getElementById('vendorFreightId').value);


 					setSlipFreight($('select[id="stockpileId"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val(), $('select[id="transactionId"]').val());
					
					setSlipNo($('select[id="stockpileId"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), /*'', 'NONE', 'NONE',*/$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val());
						//alert(setSlipFreight);
					//getBankDetail($('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipFreight($('select[id="freightId"]').val(), '', 'NONE', 'NONE', '2016-02-01', '2016-02-05');

               // }
            }
        });
		
		$('#transactionId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			 //resetSlipNo(' ');


		//bikin alert dsini
		//alert('html from to nya');

            if(document.getElementById('vendorFreightId').value != '' && document.getElementById('freightId').value != '' && document.getElementById('transactionId').value != '') {
                //if(document.getElementById('paymentMethod').value == 2) {
                    //refreshSummaryFreight($('select[id="freightId"]').val());
                //} else if(document.getElementById('paymentMethod').value == 1) {

					//alert(document.getElementById('vendorFreightId').value);


 					setSlipFreight($('select[id="stockpileId"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val(), $('select[id="transactionId"]').val());
					
					
						//alert(setSlipFreight);
					//getBankDetail($('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipFreight($('select[id="freightId"]').val(), '', 'NONE', 'NONE', '2016-02-01', '2016-02-05');

               // }
            }
        });
	
	/*<?php
        if(isset($_SESSION['jurnalMemorial'])) {
        ?>         
            if(document.getElementById('stockpileId').value != '') {
				resetSupplier('freightId', ' ');
                <?php
                 if($_SESSION['jurnalMemorial']['freightId'] != '') {
                ?>
                setSupplier(1, 'freightId', $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['jurnalMemorial']['freightId']; ?>);
				
				<?php
                    if($_SESSION['jurnalMemorial']['vendorFreightId'] != '') {
                    ?>
					setVendorFreight(1, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['jurnalMemorial']['freightId']; ?>, <?php echo $_SESSION['jurnalMemorial']['vendorFreightId']; ?>);
					
					setSlipFreight(<?php echo $_SESSION['jurnalMemorial']['freightId']; ?>, <?php echo $_SESSION['jurnalMemorial']['vendorFreightId']; ?>, '', 'NONE', 'NONE');
					
					  <?php
                    } else {
                    ?>
                    setVendorFreight(0, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['jurnalMemorial']['freightId']; ?>, 0);
                    <?php
                    }
                
                } else {
                ?>
                setSupplier(0, 'freightId', $('select[id="stockpileId2"]').val(), 0);
                <?php
                }
                ?>
                
            } else {
				resetSupplier(' Freight ');
                resetVendorFreight(' Freight ');
            }
            
        <?php
        
		}
        ?>*/
	});
	
</script>
<script>
function resetSupplier(text) {
        document.getElementById('freightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightId').options.add(x);
    }

    function setSupplier(type, stockpileId, freightId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightPayment',
                    stockpileId: stockpileId
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
                        document.getElementById('freightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('freightId').value = freightId;
                    }
                }
            }
        });
    }
	
	function resetVendorFreight(text) {
        document.getElementById('vendorFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorFreightId').options.add(x);
    }

    function setVendorFreight(type, stockpileId, freightId, vendorFreightId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorFreight',
                    stockpileId: stockpileId,
					freightId: freightId
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
                        document.getElementById('vendorFreightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('vendorFreightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorFreightId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('vendorFreightId').value = vendorFreightId;
                    }
                }
            }
        });
    }
	
	function setSlipFreight(stockpileId, freightId, vendorFreightIds, checkedSlips, ppn, pph, paymentFrom, paymentTo, transactionId) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipFreight2',
					stockpileId: stockpileId,
                    freightId: freightId,
					vendorFreightId: vendorFreightIds,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo,
					transactionId: transactionId
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }
	
	function resetSlipNo(text) {
        document.getElementById('transactionId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('transactionId').options.add(x);
    }

    function setSlipNo(stockpileId, freightId, vendorFreightIds, /*checkedSlips, ppn, pph,*/ paymentFrom, paymentTo) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSlipNo',
                    stockpileId: stockpileId,
                    freightId: freightId,
					vendorFreightId: vendorFreightIds,
                    //checkedSlips: checkedSlips,
                    //ppn: ppn,
                    //pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo
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
                        document.getElementById('transactionId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('transactionId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('transactionId').options.add(x);
                    }

                   /* if(type == 1) {
                        document.getElementById('transactionId').value = transactionId;
                    }*/
                }
            }
        });
    }
	
	
	function checkAll(a) {
     var checkedSlips = document.getElementsByName('checkedSlips[]');
	      if (a.checked) {
         for (var i = 0; i < checkedSlips.length; i++) {
             if (checkedSlips[i].type == 'checkbox') {
                 checkedSlips[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkedSlips.length; i++) {
             console.log(i)
             if (checkedSlips[i].type == 'checkbox') {
                 checkedSlips[i].checked = false;
             }
         }
     }
	 checkSlipFreight(freightId, ppn, pph, paymentFrom, paymentTo, transactionId);
 }



    function checkSlipFreight(stockpileId, freightId, vendorFreightIds, ppn, pph, paymentFrom, paymentTo, transactionId) {
		
		//alert('html from to nya');

        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if(selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }


        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof(ppn) != 'undefined' && ppn != null && typeof(pph) != 'undefined' && pph != null)
        {
            if(ppn != 'NONE') {
                if(ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph != 'NONE') {
                if(pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        //alert(ppnValue + "," + pphValue);

       // alert(vendorFreights);
        setSlipFreight(stockpileId, freightId, vendorFreightIds, selected, ppnValue, pphValue, paymentFrom, paymentTo, transactionId);
    }
	
	
		
        
   
		
		
        
    
</script>
<script type="text/javascript">
$(document).ready(function(){
	
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
                                //document.getElementById('gl_add_id').value = returnVal[3];
                                
                                $('#dataContent').load('views/adjustmentOA.php', { /*gl_add_id: returnVal[3]*/ }, iAmACallbackFunction2);

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

<form method="post" id="jurnalDataForm">
    <input type="hidden" name="action" id="action" value="adjustment_oa_ob_data" />
    <!--<input type="hidden" name="gl_add_id" id="gl_add_id" value="<?php //echo $gl_add_id; ?>" />-->
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Jurnal Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="jurnalDate" name="jurnalDate" data-date-format="dd/mm/yyyy" class="datepicker" >
            
        </div>
		<div class="span3 lightblue">
           <label>Account <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT account_id, CONCAT(account_no ,' - ', account_name) AS account_full FROM account WHERE account_type = 6 ORDER BY account_no ASC", "", "", "accountId", "account_id", "account_full", 
                    "", "", "select2combobox100");
            ?>
     	
        </div>
        <div class="span3 lightblue">
           <label>Transaction Date From</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom" name="paymentFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
     	
        </div>
        <div class="span3 lightblue">
           <label>Transaction Date To</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo" name="paymentTo" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		
        
    </div>
    <div class="row-fluid">  
        <div class="span3 lightblue">
             <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                    "", "", "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
           <label>Vendor Freight <span style="color: red;">*</span></label>
			<?php
            createCombo("", "", "", "freightId", "freight_id", "freight_supplier",
                    "", 15, "select2combobox100", 2);
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Supplier <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorFreightId", "vendor_id", "vendor_freight",
                    "", 15, "select2combobox100", 7, "multiple");
            ?>
        </div>
		<div class="span3 lightblue">
            <label>Slip No</label>
            <?php
            createCombo("", "", "", "transactionId", "transaction_id", "slip_no",
                    "", 15, "select2combobox100", 7, "multiple");
            ?>
        </div>
       
    </div>
    
	</br>
	</br>
    
   <div class="row-fluid" id="slipPayment" style="display: none;">
        slip
    </div>
	
	</br>
	</br>
	
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
