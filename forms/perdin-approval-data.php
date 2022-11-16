<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'settlePerdin';

/*$pcMethod = '';
$accountId11 = '';
$paymentCashType = '';
//$generatedInvoiceNo = '';
$generalVendorId11 = '';
$pph11 = '';
$ppn11 = ''; 
$ppnID11 = '';
$pphID11 = '';*/
//$settleId = $_POST['settleId'];
$id_user = $_POST['id_user'];
$stockpile_id = $_POST['stockpile_id'];
$qty = '';
$price = '';
$amount = '';
$uom = '';
$remarks = '';
$settlementType = '';
if(isset($_POST['settleId']) && $_POST['settleId'] != '') {
    
    $settleId = $_POST['settleId'];
    
    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    
    $sql = "SELECT a.*,DATE_FORMAT(a.tanggal,'%d/%m/%Y') AS tanggal2, b.`origin` 
    FROM perdin_settle_detail a
    LEFT JOIN perdin_adv_settle b ON a.`sa_id` = b.`sa_id`
            WHERE a.settle_id = {$settleId}";
           // echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
       
	   
	   $settlementType = $rowData->settlementType;
	   $accountId = $rowData->accountId;
	   $userTo = $rowData->user_to;
	   $items = $rowData->items;
	   $item_id = $rowData->item_id;
	   $uom = $rowData->uom;
	   $qty = $rowData->qty;
	   $price = $rowData->price;
	   $amount = $rowData->amount;
	   $notes = $rowData->notes;
	   $tanggal = $rowData->tanggal2;
       $origin = $rowData->origin;
	   
    }
    
    // </editor-fold>
    
} /*else {
   
}*/

if(isset($_SESSION['settlePerdin'])) {
    $id_user = $_SESSION['settlePerdin']['id_user'];
	//$generatedInvoiceNo = $_SESSION['invoiceDetail']['generatedInvoiceNo'];
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

		

/*$('#generalVendorId11').change(function() {
            //resetExchangeRate();
			$('#CDP').hide();
            resetGeneralVendorTax2();
            
            if(document.getElementById('generalVendorId11').value != '' && document.getElementById('generalVendorId11').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                getGeneralVendorTax2($('select[id="generalVendorId11"]').val(), $('input[id="amount11"]').val().replace(new RegExp(",", "g"), ""));
				setPPh(1, $('select[id="generalVendorId11"]').val(), 0);
				if(document.getElementById('paymentMethod').value == 1){
				setCashDP($('select[id="generalVendorId11"]').val(), '', '','NONE');
				document.getElementById('paymentCashMethod').value = '1';
				}else{
					document.getElementById('paymentCashMethod').value = '2';
				}
            } 
        });
$('#stockpileId11').change(function() {
			if(document.getElementById('stockpileId11').value != '') {
			resetSlipNo(' ');
            setSlipNo(0, $('select[id="stockpileId11"]').val(), 0);
			
			} else {
                resetSlipNo(' Slip No ');
				
            }
        });

function resetGeneralVendorTax2() {
        document.getElementById('ppn11').value = '';
       // document.getElementById('pph11').value = '';
		document.getElementById('ppnID11').value = '';
		//document.getElementById('pphID11').value = '';
    }
    
    function getGeneralVendorTax2(generalVendorId11, amount11) {
		
        if(amount11 != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getGeneralVendorTax2',
                        generalVendorId11: generalVendorId11,
                        amount11: amount11
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('ppn11').value = returnVal[1];
                        //document.getElementById('pph11').value = returnVal[2];
						document.getElementById('ppnID11').value = returnVal[3];
						//document.getElementById('pphID11').value = returnVal[4];
						document.getElementById('checkedPPN').checked = true;
						//document.getElementById('checkedPPh').checked = true;
                    }
                }
            });
        } else {
            document.getElementById('ppn11').value = '0';
           // document.getElementById('pph11').value = '0';
			document.getElementById('ppnID11').value = '0';
			//document.getElementById('pphID11').value = '0';
        }
    }*/
</script>
<script type="text/javascript">
 /*if(document.getElementById('generalVendorId11').value != '') {
                    resetGeneralVendorTax2(' ');
					//resetInvoiceDP(' ');
					resetSetPPh(' ');
                    <?php
                    if($_SESSION['paymentCash']['generalVendorId11'] != '') {
                    ?>
                    getGeneralVendorTax2(1, $('select[id="generalVendorId11"]').val(), <?php echo $_SESSION['paymentCash']['generalVendorId11']; ?>);
					setPPh(1, $('select[id="generalVendorId11"]').val(),<?php echo $_SESSION['paymentCash']['generalVendorId11']; ?>);
					if(document.getElementById('paymentMethod').value == 1){
                    setCashDP(1, $('select[id="generalVendorId11"]').val(),<?php echo $_SESSION['paymentCash']['generalVendorId11']; ?>, 'NONE');
					}else{
						resetCashDP(' ');
					}
                    <?php
                    } else {
                    ?>
                    setCashDP(0, $('select[id="generalVendorId11"]').val(), 0);
                    <?php
                    }
                    ?>
				}
 <?php
        if(isset($_SESSION['paymentCash'])) {
        ?>         
            if(document.getElementById('stockpile_id').value != '') {
                resetSlipNo(' ');
				
                <?php
                if($_SESSION['paymentCash']['transaction_id'] != '') {
                ?>
                setSlipNo(1, $('select[id="stockpile_id"]').val(), <?php echo $_SESSION['jurnalMemorial']['transaction_id']; ?>);
				
                <?php
                } else {
                ?>
                setSlipNo(0, $('select[id="stockpile_id"]').val(), 0);
				
                <?php
                }
                ?>
                
            } else {
                 resetSlipNo(' Slip No ');
				 
            }
            
        <?php
        
		}
        ?>
<?php
        if(isset($_SESSION['paymentCash'])) {
        ?>         
			
            if(document.getElementById('paymentCashType').value != '') {
                resetAccount(' ');
                <?php
                if($_SESSION['paymentCash']['accountId11'] != '') {
                ?>
                setAccount(1, $('select[id="paymentCashType"]').val(), <?php echo $_SESSION['paymentCash']['accountId11']; ?>);
                <?php
                } else {
                ?>
                setAccount(0, $('select[id="paymentCashType"]').val(), 0);
                <?php
                }
                ?>
                
            } else {
                resetAccount(' Invoice Type ');
				
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

function resetsetPPh(text) {
        document.getElementById('pphTaxId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('pphTaxId').options.add(x);
    }
	 function setPPh(type, generalVendorId11) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getGeneralVendorPPh',
                    generalVendorId: generalVendorId11
					
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
                        document.getElementById('pphTaxId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('pphTaxId').options.add(x);
                    }
					
					var x = document.createElement('option');
                    x.value = 0;
                    x.text = 'NONE';
                    document.getElementById('pphTaxId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('pphTaxId').options.add(x);
                    }
				
				if(type == 1) {
                        $('#pphTaxId').find('pphTaxId').each(function(i,e){
                            if($(e).val() == pphTaxId){
                                $('#pphTaxId').prop('selectedIndex',i);
                            }
                        });
				}
                
                }
            }
        });
    }
	

	
	if(document.getElementById('currencyId11').value == 1 || document.getElementById('currencyId11').value == '') {
            $('#exchangeRate11').hide();
        } else {
            $('#exchangeRate11').show();
        }
        
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $('#currencyId11').change(function() {
            if(document.getElementById('currencyId11').value == 1 || document.getElementById('currencyId11').value == '') {
                $('#exchangeRate11').hide();
            } else {
                $('#exchangeRate11').show();
            }
        });
		
		$('#paymentCashType').change(function() {
			if(document.getElementById('paymentCashType').value != '') {
			resetAccount(' ');
            setAccount(0, $('select[id="paymentCashType"]').val(), 0);
			} else {
                resetAccount(' Invoice Type ');
            }
			if(document.getElementById('paymentMethod').value == 1){
				$('#method').show();
			}
        });*/
		
	<?php
        if($accountId != "") {
        ?>
		//alert('bb');
		setAccount(1,<?php echo $settlementType; ?>,<?php echo $accountId; ?>);
		<?php
		}
        ?>	
		<?php
		 if($userTo != "") {
        ?>
		//alert('bb');
		//setUser(1,$('input[id="origin"]').val(),<?php //echo $userTo; ?>);
		<?php
		}	
        ?>

	
	$('#settlementType').change(function() {
			resetAccount(' account ');
			if(document.getElementById('settlementType').value != '') {
			resetAccount(' account ');
            setAccount(0, $('select[id="settlementType"]').val(), 0);
			} else {
                resetAccount(' Settlement Type ');
            }
			/*if(document.getElementById('paymentMethod').value == 1){
				$('#method').show();
			}*/
        });
		
	function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }
    
    function setAccount(type, settlementType, accountId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountSettlement',
                    settlementType: settlementType
					
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
</script>
<script type="text/javascript">
$(document).ready(function() {
    //this calculates values automatically 
	//$('#ppn11').number(true, 2);
	//$('#pph11').number(true, 2);
	//$('#price11').number(true, 2);
	//$('#qty11').number(true, 2);
    sum();
   $("#qty, #price").on("keydown keyup", function() {
        sum();
		
	$('#amount').number(true, 10);
	
    });
});

function sum() {
            var num1 = document.getElementById('qty').value;
            var num2 = document.getElementById('price').value;
			//var num3 = document.getElementById('ppn11').value;
			//var num4 = document.getElementById('pph11').value;
			//var num5 = document.getElementById('termin11').value;
			var result = (parseFloat(num1) * parseFloat(num2));
			//var result1 = parseInt(num1) * parseInt(num2) + parseInt(num3) - parseInt(num4);
            if (!isNaN(result)) {
                document.getElementById('amount').value = result;
				//document.getElementById('amount_benefit').value = result;
            }
        }
		
/*function checkPPN() {
     var checkedPPN = document.getElementById('checkedPPN');
	 var generalVendorId = document.getElementById('generalVendorId11').value;
     var amount = document.getElementById('amount11').value.replace(new RegExp(",", "g"), "");
	      if (checkedPPN.checked != true ) {
             document.getElementById('ppn11').value = 0;
     } else {
         getGeneralVendorTax2(generalVendorId, amount);
     }
	 
 }*/
 /*
 function checkPPh() {
     var checkedPPh = document.getElementById('checkedPPh');
	 var generalVendorId = document.getElementById('generalVendorId11').value;
     var amount = document.getElementById('amount11').value.replace(new RegExp(",", "g"), "");
	      if (checkedPPh.checked != true ) {
             document.getElementById('pph11').value = 0;
     } else {
         getGeneralVendorTax2(generalVendorId, amount);
     }
	 
 }*/
/*function sum2(a) {
	
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

function test1(chk){
    
	//alert ('test');
	var text = document.getElementsByName('checkedSlips2[]');
	var checkBox = document.getElementsByName('checkedSlips[]');
	for (var i = 0; i < checkBox.length; i++) {
		if (checkBox[i].checked) {
			
		 for (var j = 0; j < text.length; j++) {
			 if (text[i].type == 'text') {
				// alert ('test');
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

function checkAllPC(a) {
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
	 checkSlipPC(generalVendorId11, ppn11, pph11);
 }
		
function checkSlipPC(generalVendorId11, ppn11, pph11) {
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
        
        var ppnValue = 'NONE';
        var pphValue = 'NONE';
        
        if (typeof(ppn11) != 'undefined' && ppn11 != null && typeof(pph11) != 'undefined' && pph11 != null)
        {
            if(ppn11 != 'NONE') {
                if(ppn11.value != '') {
                    ppnValue = ppn11.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph11 != 'NONE') {
                if(pph11.value != '') {
                    pphValue = pph11.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
       

        
		setCashDP(generalVendorId11, selected, selected2, ppnValue, pphValue);
				
 //alert(generalVendorId);
    }
	*/
	
	
	
	
 
 
	//function checkSlipAdvance(){
    
	//var x = 0;
	//var y = 0;
	//var amount_benefit = Array();
	//var advance = Array();
	//var dpAmount= document.getElementsByName('checkedSlips2[]');
	//var dpCheck = document.getElementsByName('checkedSlips[]');
	//var checkedSlips = document.getElementsByName('checkedSlips[]');
	//amount_benefit[x] = document.getElementById('amount_benefit').value;
	//advance[y] = document.getElementById('advance').value;
	//var qty1 = document.getElementById('qty').value;
    //var price2 = document.getElementById('price').value;
			//var num3 = document.getElementById('ppn11').value;
			//var num4 = document.getElementById('pph11').value;
			//var num5 = document.getElementById('termin11').value;
	//var resultdp1 = (parseFloat(qty1) * parseFloat(price2));
			//var result1 = parseInt(num1) * parseInt(num2) + parseInt(num3) - parseInt(num4);
            //if (!isNaN(result)) {
             //   document.getElementById('amount').value = result;
			 //	document.getElementById('amount_benefit').value = result;
            //}
	//alert (resultdp1);
	
	 /*for (var i = 0; i < checkedSlips.length; i++) {
			
		 alert('test');
           if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }*/
	
	//for (i = 0; i < dpCheck.length; i++) {
        //if (dpCheck[i].type == "checkbox") {
           // dpCheck[i].checked = true;
		//	alert ('test');
       // }
    //}
	
	/*for (i = 0; i < dpCheck.length; i++) {
		alert ('test');
		if (dpCheck[i].value != '') {
			alert ('test1');
		 for (j = 0; j < dpAmount.length; j++) {
			 alert (resultdp1);
                 dpAmount[j].value = resultdp1;
			
		 }
		
		}else{
			 for (j = 0; j < dpAmount.length; j++) {
			
				 dpAmount[j].value = "";
				 
				
			
		 }
		}
	}*/
	
	
	
	/*for (var i = 0; i < dpCheck.length; i++) {
		alert ('test');
		if (dpCheck[i].checked) {
			alert ('test');
		 for (var j = 0; j < dpAmount.length; j++) {
			// if (dpAmount[i].type == 'text') {
				 alert ('test');
                 dpAmount[i].value = result;
		//	}
		 }
		
		}else{
			 for (var j = 0; j < dpAmount.length; j++) {
			 if (dpAmount[i].type == 'text') {
                 //text[i].readOnly = true;
				 dpAmount[i].value = "";
				 
				
			}
		 }
		}
	}*/
		 //sum2('');
//}		
	
	<?php
        if($origin != "") {
        ?>
		//alert('bb');
		setUser(1,<?php echo $origin; ?>,<?php echo $userTo; ?>);
		<?php
		}
        ?>

		


function setUser(type, stockpile_id, userTo) {
		//alert(userTo);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getUserTo',
					stockpile_id: stockpile_id
                    //checkedSlips: checkedSlips,
					//checkedSlips2: checkedSlips2,
                    //ppn11: ppn11,
                   // pph11: pph11,
					//invoiceMethod: invoiceMethod
					
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
                        document.getElementById('userTo').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('userTo').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('userTo').options.add(x);
                    }
				
				if(type == 1) {
                        $('#userTo').find('option').each(function(i,e){
                            if($(e).val() == userTo){
                                $('#userTo').prop('selectedIndex',i);
								
								  $("#userTo").select2({
                                    width: "100%",
                                    placeholder: userTo
                                });
                            }
                        });
				}
                
                }
            }
        });
    }
	
		<?php
        if($id_user != "") {
        ?>
		//alert('bb');
		//setAdvancePerdin(<?php echo $id_user; ?>);
		<?php
		}
        ?>	


function setAdvancePerdin(id_user) {
		//alert('aa');
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAdvancePerdin',
					id_user: id_user
                    //checkedSlips: checkedSlips,
					//checkedSlips2: checkedSlips2,
                    //ppn11: ppn11,
                   // pph11: pph11,
					//invoiceMethod: invoiceMethod
					
            },
            success: function(data){
                if(data != '') {
                    $('#CDP').show();
                    document.getElementById('CDP').innerHTML = data;
                }
            }
        });
    }
	$(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });
</script>
<span style="color: red;">(MOHON TIDAK MENGGUNAKAN TANDA ' ATAU " (PETIK))</span>
<input type="hidden" id="action" name="action" value="perdin_settle_detail">
<input type="hidden" class="span12"  tabindex="" id="id_user" name="id_user" value="<?php echo $id_user; ?>"  >
<input type="hidden" class="span12"  tabindex="" id="settleId" name="settleId" value="<?php echo $settleId; ?>"  >
<input type="hidden" class="span12"  tabindex="" id="hrd" name="hrd" value="1"  >


<!--<div class="row-fluid">
    <div class="span4 lightblue">
        <label>Type</label>
		<?php //createCombo("", $paymentCashType, "", "paymentCashType", "id", "info", "", "", "select2combobox100", 1);?>
        <input type="hidden" class="span12" tabindex="" id="paymentCashMethod" name="paymentCashMethod" value="<?php //echo $paymentCashMethod; ?>">
    </div>
   
    <div class="span4 lightblue">
        <label>Account</label>
		<?php //createCombo("", $accountId11, "", "accountId11", "id", "info", "", "", "select2combobox100", 4);	?>
    </div>
    
    <div class="span4 lightblue">
        <label>Method<span style="color: red;">*</span></label>
		<?php
            /*createCombo("SELECT '1' as id, 'IN' as info UNION
                    SELECT '2' as id, 'OUT' as info;", $pcMethod, "", "pcMethod", "id", "info", 
                "", "", "select2combobox100", 1);*/
            ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span4 lightblue">
        <label>Remark (Stockpile)</label>
		<?php //createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", "", "", "stockpileId11", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);?>	
    </div>
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
            <?php
            /*createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId11", "currency_id", "currency_code", 
                    "", "", "select2combobox100");*/
            ?>
    </div>
   <div class="span4 lightblue" id="exchangeRate11" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate11" name="exchangeRate11" value="<?php //echo $exchangeRate11; ?>">
        </div>  
</div>
<div class="row-fluid">
    <div class="span4 lightblue">
        <label>Slip No.</label>
            <?php //createCombo("", $transaction_id, "", "transaction_id", "id", "info", "", "", "select2combobox100", 2);	?>
    </div>
    <div class="span4 lightblue">
        <label>Shipment Code</label>
		<?php //createCombo("SELECT shipment_id, shipment_no FROM shipment WHERE 1=1 ORDER BY shipment_id DESC", "", "", "shipmentId11", "shipment_id", "shipment_no", "", "", "select2combobox100", 1);?>
    </div>
   <div class="span4 lightblue" >
            
        </div>  
</div>-->
<div class="row-fluid">
   
	<div class="span3 lightblue">
            <label>Type <?php echo $settlementType;?></label>
			<?php 
			createCombo("SELECT '4' as id, 'Loading' as info UNION
                    SELECT '5' as id, 'Umum' as info;", $settlementType, "", "settlementType", "id", "info", 
                "", 11, "select2combobox100");?>
			
        </div> 
	<div class="span5 lightblue">
        <label>Account</label>
		<?php createCombo("", $accountId, "", "accountId", "id", "info", "", "", "select2combobox100", 4);	?>
    </div>
		  <div class="span4 lightblue">
        
    </div>
  
    <!--<div class="span2 lightblue">
        <label>Termin (%)</label>
        <input type="text" class="span12"  tabindex="" id="termin" name="termin"  maxlength="3" min="0" max="100">
    </div>-->
    
</div>
<div class="row-fluid">
   
	
	<div class="span3 lightblue">
        <label>To <span style="color: red;">*</span></label>
            <?php createCombo("", $userTo, "", "userTo", "id_user", "nama", "", "", "select2combobox100", 4);	?>
    </div>
<div class="span4 lightblue">
            <label>Items <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT item_id, item FROM perdin_item ORDER BY item_id DESC", $item_id, "", "item_id", "item_id", "item", "", "", "select2combobox100", 1);
            ?>
        </div> 	
    <div class="span3 lightblue">
        <label>Date<span style="color: red;">*</span></label>
      	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="tanggal" name="tanggal" value="<?php echo $tanggal; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    <!--<div class="span2 lightblue">
        <label>Termin (%)</label>
        <input type="text" class="span12"  tabindex="" id="termin" name="termin"  maxlength="3" min="0" max="100">
    </div>-->
    
</div>
<div class="row-fluid">
   <div class="span3 lightblue">
        <label>UOM <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT *
                    FROM uom 
                    ORDER BY uom_type ASC", $uom, "", "uom", "idUOM", "uom_type", 
                    "", "", "select2combobox100");
            ?>
    </div>
	
	<div class="span3 lightblue">
        <label>Qty</label>
        <input type="text" class="span12"  tabindex="" id="qty" name="qty" value="<?php echo $qty ?>" >
    </div>		
    <div class="span3 lightblue">
        <label>Unit Price</label>
        <input type="text" class="span12"  tabindex="" id="price" name="price"  value="<?php echo $price ?>" >
    </div>
    <!--<div class="span2 lightblue">
        <label>Termin (%)</label>
        <input type="text" class="span12"  tabindex="" id="termin" name="termin"  maxlength="3" min="0" max="100">
    </div>-->
    <div class="span3 lightblue">
       	<label>Amount</label>
        <input type="text" readonly class="span12" tabindex="" id="amount" name="amount" value="<?php echo $amount ?>" >
                    
    </div>
</div>
<!--<div class="row-fluid">
	<div class="span6 lightblue">
        <label>Vendor <span style="color: red;">*</span></label>
            <?php
           /* createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId11, $readonlyProperty, "generalVendorId11", "general_vendor_id", "general_vendor_name", 
                "", "", "select2combobox100", 1, "", true);*/
            ?>
    </div>
	<div class="span3 lightblue">
       				<label>PPN</label>
                    <input type="text" readOnly class="span12" tabindex="" id="ppn11" name="ppn11" value="<?php //echo $_SESSION['paymentCash']['ppn11']; ?>">
                    <input type="hidden" class="span12"  id="ppnID11" name="ppnID11" value="<?php //echo $_SESSION['paymentCash']['ppnID11']; ?>">
					<input type="checkbox" name="checkedPPN" id="checkedPPN" onclick="checkPPN()" checked="checked" />
    </div>
    <div class="span3 lightblue">
        <label>PPh</label>
		<?php //createCombo("", $pphTaxId, "", "pphTaxId", "id", "info", "", "", "select2combobox100", 4);	?>
    </div>
    
</div>-->
<div class="row-fluid">
	<div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span12" rows="3" tabindex="" id="notes"  name="notes" ><?php echo $notes ?></textarea>
    </div>
</div>
<div class="row-fluid" id="CDP" style="display: none;">
        Petty Cash DP
    </div>
<div class="row-fluid">
    
</div>


