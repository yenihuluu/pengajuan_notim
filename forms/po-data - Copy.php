<?php
error_rePOrting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'PODetail';

$POMethod = 2;
$accountId = '';
$POId = '';
$POType = '';
$generatedPONo = '';
$generalVendorId = '';
$pph1 = 0;
$ppnPO1 = ''; 
$ppnPOID = '';
$pphID = '';
$pphstatus = 0;
$itemId='';

if(isset ($_POST['generalVendorId']) && $_POST['generalVendorId'] != ''){
	
	$generalVendorId = $_POST['generalVendorId'];
}
if(isset ($_POST['generatedPONo']) && $_POST['generatedPONo'] != ''){
	
	$generatedPONo = $_POST['generatedPONo'];
}
if(isset($_POST['POId']) && $_POST['POId'] != '') {
    
    $POId = $_POST['POId'];
    
    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    
    $sql = "SELECT * 
            FROM PO_detail
            WHERE no_PO = {$generatedPONo}
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
       
    }
    
    // </editor-fold>
    
} 
/*
else {
    $generatedPONo = "";
    if(isset($_SESSION['PO'])) {
        
    }
}
*/

if(isset($_SESSION['PODetail'])) {
    $POType = $_SESSION['PODetail']['POType'];
	$generatedPONo = $_SESSION['PODetail']['generatedPONo'];
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
$(document).ready(function() {
		$("select.select2combobox100").select2({
            width: "100%"
        });
        
        $("select.select2combobox50").select2({
            width: "50%"
        });
        
        $("select.select2combobox75").select2({
            width: "75%"
        });
		
		
	
	
});

$('#generalVendorId').change(function() {
            //resetExchangeRate();
			$('#IDP').hide();
            //resetGeneralVendorTax();
            
            if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
				/*
				if(document.getElementById('invoiceMethod').value == 1){
				setInvoiceDP($('select[id="generalVendorId"]').val(), '', '', 'NONE','NONE');
				document.getElementById('invoiceMethodDetail').value = '1';
				}else{
					document.getElementById('invoiceMethodDetail').value = '2';
				}
				*/
            } 
        });

function resetGeneralVendorTax() {
        document.getElementById('ppnPO1').value = 0;
        document.getElementById('pph1').value = 0;
		document.getElementById('ppnPOID').value = 0;
		document.getElementById('pphID').value = 0;
    }
  
    function getGeneralVendorTax(generalVendorId, amount) {
		
        if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getGeneralVendorTax',
                        generalVendorId: generalVendorId,
                        amount: amount
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('ppnPO1').value = returnVal[1];
                        document.getElementById('pph1').value = returnVal[2];
						document.getElementById('ppnPOID').value = returnVal[3];
						document.getElementById('pphID').value = returnVal[4];
                    }
                }
            });
        } else {
            document.getElementById('ppnPO1').value = '0';
            document.getElementById('pph1').value = '0';
			document.getElementById('ppnPOID').value = '0';
			document.getElementById('pphID').value = '0';
        }
    }
</script>
<script type="text/javascript">
 if(document.getElementById('generalVendorId').value != '') {
                    //resetGeneralVendorTax(' ');
					//resetInvoiceDP(' ');
                    <?php
                    if($_SESSION['PO']['generalVendorId'] != '') {
                    ?>
                    getGeneralVendorTax(1, $('select[id="generalVendorId"]').val(), <?php echo $_SESSION['PO']['generalVendorId']; ?>);
					
					if(document.getElementById('invoiceMethod').value == 1){
                    //setInvoiceDP(1, $('select[id="generalVendorId"]').val(),<?php echo $_SESSION['invoice']['generalVendorId']; ?>, '', '', 'NONE', 'NONE');
					}else{
						resetInvoiceDP(' ');
					}
                    <?php
                    } else {
                    ?>
                    //setInvoiceDP(0, $('select[id="generalVendorId"]').val(), 0);
                    <?php
                    }
                    ?>
                }
/*	$('#pph1').change(function() {
             $('#IDP').hide();
            resetGeneralVendorTax();
            
            if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
				
			if(document.getElementById('invoiceMethod').value == 2) {
                        //refreshSummaryFreight($('select[id="freightId"]').val());
               } else if(document.getElementById('invoiceMethod').value == 1) {
                        setInvoiceDP($('select[id="generalVendorId"]').val(), '', 'NONE', 'NONE', $('select[id="invoiceMethod"]').val());
                    }
            } 
        });*/
<?php
        //if(isset($_SESSION['PODetail'])) {
        ?>         
            if(document.getElementById('POType').value != '') {
                resetAccount(' ');
                <?php
                if($_SESSION['PODetail']['accountId'] != '') {
                ?>
                setAccount(1, $('select[id="POType"]').val(), <?php echo $_SESSION['PODetail']['accountId']; ?>);
                <?php
                } else {
                ?>
                setAccount(0, $('select[id="POType"]').val(), 0);
                <?php
                }
                ?>
                
            } else {
                resetAccount(' PO Type ');
            }
            
        <?php
        
		//}
        ?>
		
		<?php
        if(isset($_SESSION['PODetail'])) {
        ?>         
            if(document.getElementById('accountId').value != '') {
                //resetPONo(' ');
                <?php
                if($_SESSION['PODetail']['POId'] != '') {
                ?>
                setPONo(1, $('select[id="accountId"]').val(), <?php echo $_SESSION['PODetail']['POId']; ?>);
                <?php
                } else {
                ?>
                setPONo(0, $('select[id="accountId"]').val(), 0);
                <?php
                }
                ?>
                
            } else {
                resetPONo(' PO NO ');
            }
            
        <?php
        
		}
        ?>

			$.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getPONo'
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                if(data != '') {
                    document.getElementById('generatedPONo').value = data;
					//alert(data);
                }
				
				setPOType(generatedPONo);
				//if(document.getElementById('POMethod').value == 1){
				$('#method').show();
				//}
				
            }
			
        });
function setPOType(generatedPONo) {
        document.getElementById('POType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('POType').options.add(x);
        
       /*
            var x = document.createElement('option');
            x.value = '0';
            x.text = 'PKS Kontrak';
            document.getElementById('invoiceType').options.add(x);
            
            var x = document.createElement('option');
            x.value = '1';
            x.text = 'PKS Curah/Sales';
            document.getElementById('invoiceType').options.add(x);
            
            var x = document.createElement('option');
            x.value = '2';
            x.text = 'Freight Cost';
            document.getElementById('invoiceType').options.add(x);

            var x = document.createElement('option');
            x.value = '3';
            x.text = 'Unloading Cost';
            document.getElementById('invoiceType').options.add(x);
            */
            var x = document.createElement('option');
            x.value = '4';
            x.text = 'Loading';
            document.getElementById('POType').options.add(x);
            /*
            var x = document.createElement('option');
            x.value = '7';
            x.text = 'Internal Transfer';
            document.getElementById('POType').options.add(x);
        */
        
        var x = document.createElement('option');
        x.value = '5';
        x.text = 'Umum';
        document.getElementById('POType').options.add(x);
        
        var x = document.createElement('option');
        x.value = '6';
        x.text = 'HO';
        document.getElementById('POType').options.add(x);
        
        <?php
        if(isset($_SESSION['PODetail']) && $_SESSION['PODetail']['POType'] != '') {
        ?>
        document.getElementById('POType').value = <?php echo  $POType; ?>;     
		
        <?php
        }
        ?>
    }
	
function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }
    
    function setAccount(type, POType) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountInvoice',
                    invoiceType: POType
					
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
                        $('#accountId').find('accountId').each(function(i,e){
                            if($(e).val() == accountId){
                                $('#accountId').prop('selectedIndex',i);
                            }
                        });
				}
                
                }
            }
        });
    }
	/*
	function resetPONo(text) {
        document.getElementById('POId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('POId').options.add(x);
    }
    
    function setPONo(type, accountId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getaccountInvoice',
                    accountId: accountId
					
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
                        document.getElementById('POId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('POId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('POId').options.add(x);
                    }
				
				if(type == 1) {
                        $('#POId').find('POId').each(function(i,e){
                            if($(e).val() == POId){
                                $('#POId').prop('selectedIndex',i);
                            }
                        });
				}
                
                }
            }
        });
    }
	
	if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
        
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });
		*/
		$('#POType').change(function() {
			if(document.getElementById('POType').value != '') {
			resetAccount(' ');
            setAccount(0, $('select[id="POType"]').val(), 0);
			} else {
                resetAccount(' PO Type ');
            }
        });
	/*
		$('#accountId').change(function() {
			if(document.getElementById('accountId').value != '') {
			//resetPONo(' ');
            setPONo(0, $('select[id="accountId"]').val(), 0);
			} else {
                resetPONo(' PO NO ');
            }
        });
		*/
</script>
<script type="text/javascript">
$(document).ready(function() {
    //this calculates values automatically 
	$('#ppnPO1').number(true, 2);
	$('#pph1').number(true, 2);
    sum();
   $("#qty, #price").on("keydown keyup", function() {
        sum();
		
	$('#amount').number(true, 2);
	   
	    if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
		};
	//$('#amount').number(true, 2);
	
	
    });
});
	
function checkpphstatus()
{
  var checkbox = document.getElementById('pphstatus1');
  if (checkbox.checked != true)
  {
  	document.getElementById('pphstatus').value=0;
   
  }
	else{
		document.getElementById('pphstatus').value=1;
    
	}
		
}

	
	
function sum() {
            var num1 = document.getElementById('qty').value;
            var num2 = document.getElementById('price').value;
			var num3 = document.getElementById('ppnPO1').value;
			var num4 = document.getElementById('pph1').value;
			//var num5 = document.getElementById('termin').value;
			var result = (parseFloat(num1) * parseFloat(num2));
			//var result = (parseFloat(num1) * parseFloat(num2)) * (parseFloat(num5)/100);
			//var result1 = parseInt(num1) * parseInt(num2) + parseInt(num3) - parseInt(num4);
            //if (!isNaN(result)) {
                document.getElementById('amount').value = result;
				//document.getElementById('tamount').value = result1;
            //}
        }
function sum2(a) {
	
	//alert ('BISA');
	
			var dp = document.getElementsByName('checkedSlips2[]');

			//alert (dp);
			var total1 = 0;
			for (var i = 0; i < dp.length; i++) {
			
				if(parseFloat(dp[i].value))
					total1 += parseFloat(dp[i].value);
					
			}
				
				
                document.getElementById('dp_total').value = total1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");;
				
            
        } 
	$(document).ready(function()
{
 
});
function checkAllInv(a) {
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
	 checkSlipInvoice(generalVendorId, ppnPO1, pph1);
 }
function test1(chk){
    
	//alert ('test');
	var text = document.getElementsByName('checkedSlips2[]');
	var checkBox = document.getElementsByName('checkedSlips[]');
	for (var i = 0; i < checkBox.length; i++) {
		if (checkBox[i].checked) {
		 for (var j = 0; j < text.length; j++) {
			 if (text[i].type == 'text') {
                 text[i].readOnly = false;
			}
		 }
		
		}else{
			 for (var j = 0; j < text.length; j++) {
			 if (text[i].type == 'text') {
                 text[i].readOnly = true;
				 text[i].value = "";
				 
				
			}
		 }
		}
	}
		 sum2('');
}		
function checkSlipInvoice(generalVendorId, ppnPO1, pph1) {
//        var checkedSlips = document.forms[0].checkedSlips;

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
		
		var checkedSlips2 = document.getElementsByName('checkedSlips2[]');
        var selected2 = "";
        for (var i = 0; i < checkedSlips2.length; i++) {
            if (checkedSlips2[i].value != '') {
                if(selected2 == "") {
                    selected2 = checkedSlips2[i].value;
                } else {
                    selected2 = selected2 + "," + checkedSlips2[i].value;
                }
				//alert(selected2);
            }
        }
        
        var ppnPOValue = 'NONE';
        var pphPOValue = 'NONE';
        
        if (typeof(ppnPO1) != 'undefined' && ppnPO1 != null && typeof(pph1) != 'undefined' && pph1 != null)
        {
            if(ppnPO1 != 'NONE') {
                if(ppnPO1.value != '') {
                    ppnPOValue = ppnPO1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph1 != 'NONE') {
                if(pph1.value != '') {
                    pphPOValue = pph1.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
       

        
		setInvoiceDP(generalVendorId, selected, selected2, ppnPOValue, pphPOValue);
				
 //alert(generalVendorId);
    }
	


function setInvoiceDP(generalVendorId, checkedSlips, checkedSlips2, ppnPO1, pph1) {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setInvoiceDP',
					generalVendorId: generalVendorId,
                    checkedSlips: checkedSlips,
					checkedSlips2: checkedSlips2,
                    ppnPO1: ppnPO1,
                    pph1: pph1,
					//invoiceMethod: invoiceMethod
					
            },
            success: function(data){
                if(data != '') {
                    $('#IDP').show();
                    document.getElementById('IDP').innerHTML = data;
                }
            }
        });
    }
</script>


  <input type="hidden" id="action" name="action" value="PO_detail">
  <input type="hidden" class="span12" readonly id="generalVendorId" name="generalVendorId" value="<?php echo $generalVendorId; ?>">
  
<div class="row-fluid">
	<div class="span4 lightblue">
  <input type="text" class="span12" readonly id="generatedPONo" name="generatedPONo" value="<?php echo $generatedPONo; ?>">
	</div>
	
	  </div>
 <div class="row-fluid" style="margin-bottom: 7px;">  
     <div class="span3 lightblue">
        <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
       <div class="span1 lightblue">
        </div>
       
        <div class="span3 lightblue"></div>
    </div>

   
<div class="row-fluid">
	<div class="span4 lightblue">
	  <label>Item </label>
	  <span class="span3 lightblue">
	  <?php
            createCombo("select idmaster_item, item_name from master_item", $itemId, "", "itemId", "idmaster_item", "item_name", "", "", "select2combobox100", 1);
            ?>
	  </span></div>
   <div class="span1 lightblue">
        <label>Qty</label>
        <input type="text" class="span12"  tabindex="" id="qty" name="qty"  >
  </div>
    <div class="span3 lightblue">
        <label>Unit Price</label>
        <input type="text" class="span12"  tabindex="" id="price" name="price"   >
    </div>
    <div class="span2 lightblue">
       	<label>Amount</label>
        <input type="text" readonly class="span12" tabindex="" id="amount" name="amount"  >
                    
    </div>
</div>
<div class="row-fluid">
	<div class="span2 lightblue">
       				<label>PPN</label>
                    <input type="text"  class="span12" tabindex="" readonly id="ppnPO1" name="ppnPO1" value="<?php echo $_SESSION['PODetail']['ppnPO1']; ?>">
                    <input type="hidden" class="span12"  id="ppnPOID" name="ppnPOID" value="<?php echo $_SESSION['PODetail']['ppnPOID']; ?>">
    </div>
    <div class="span2 lightblue">
      				<label>PPh</label>
                    <input type="text" class="span12" tabindex="" readonly id="pph1" name="pph1" value="<?php echo $_SESSION['PODetail']['pph1']; ?>">
      
      <input type="checkbox" name="pphstatus1" onclick="checkpphstatus()" id="pphstatus1" >
                    <label for="checkbox">Yes </label>
      <input type="hidden" class="span12"  id="pphID" name="pphID" value="<?php echo $_SESSION['PODetail']['pphID']; ?>">
      <span class="span4 lightblue">
      <input type="hidden" class="span12" readonly id="pphstatus" name="pphstatus" value="<?php echo $pphstatus; ?>">
    </span></div>
    
</div>
<div class="row-fluid">
	
</div>


