<?php
error_rePOrting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connectionre
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'PO';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">
$inputDate = $date->format('d/m/Y');
$POId = '';
$PONo = '';
$inputnopenawaran = '';
$generalVendorId = '';
$generatedPONo = '';
$currencyId = '';
$POMethod = '';
$price = '';
$quantity = '';
$amount = '';
$amountDP = 0;
$pphPO1 = '';
$ppnPO1 = ''; 
$ppnPOID = '';
$pphPOID = '';
$pph2 = 0;
$ppn2 = 0; 
$exchangeRate = '';
$shipmentId='';
$toc='';
$totalpph='';
$totalppn='';
$totalall='';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['POId']) && $_POST['POId'] != '') {
    
    $POId = $_POST['POId'];
    
    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    /*
	
    $sql = "SELECT inv.*, DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') AS invoice_date2, DATE_FORMAT(inv.input_date, '%d/%m/%Y') AS input_date, DATE_FORMAT(inv.request_date, '%d/%m/%Y') AS request_date, DATE_FORMAT(inv.tax_date, '%d/%m/%Y') AS tax_date
            FROM invoice inv
            WHERE inv.invoice_id = {$invoiceId}
            ORDER BY inv.invoice_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $invoiceTax = $rowData->invoice_tax;
        $invoiceDate = $rowData->invoice_date2;
		$inputDate = $rowData->input_date;
		$requestDate = $rowData->request_date;
		$taxDate = $rowData->tax_date;
        $generatedInvoiceNo = $rowData->invoice_no;
		$generatedInvoiceNo2 = $rowData->invoice_no2;
		$stockpileId = $rowData->stockpileId;
		$stockpileContractId3 = $rowData->PO_id;
		$remark = $rowData->remark;
		$generalVendorId2 = $rowData->generalVendorId;
    }
    
    // </editor-fold>
    
} else {
    $generatedInvoiceNo = "";
    if(isset($_SESSION['invoice'])) {
        $invoiceTax = $_SESSION['invoice']['invoiceTax'];
        $invoiceDate = $_SESSION['invoice']['invoiceDate'];
		$inputDate = $_SESSION['invoice']['inputDate'];
		$requestDate = $_SESSION['invoice']['requestDate'];
		$taxDate = $_SESSION['invoice']['taxDate'];
		$generatedInvoiceNo = $_SESSION['invoice']['generatedInvoiceNo'];
		$generatedInvoiceNo2 = $_SESSION['invoice']['generatedInvoiceNo2'];
        $stockpileId = $_SESSION['invoice']['stockpileId'];
        $stockpileContractId3 = $_SESSION['invoice']['stockpileContractId3'];
		$remark = $_SESSION['invoice']['remark'];
		$generalVendorId2 = $_SESSION['generalVendorId2']['remark'];
    }
	*/
}

// <editor-fold defaultstate="collapsed" desc="Functions">
/*
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
*/

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
        
       // $('#qty').number(true, 2);
        
      //  $('#price').number(true, 2);
		
		
        
       /* if(document.getElementById('vendorId').value == 'INSERT') {
            $('#vendorDetail').show();
            $('#vendorDetail2').show();
            $('#vendorDetail3').show();
        } else {
            $('#vendorDetail').hide();
            $('#vendorDetail2').hide();
            $('#vendorDetail3').hide();
        }*/
		
		
		
	
   
	
	
	
	
	
    
	
	/* function resetInvoiceDP(text) {
        document.getElementById('invoice_dp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoice_dp').options.add(x);
		document.getElementById('ppn1').value = '';
        document.getElementById('pph1').value = '';
    }
    
    function setInvoiceDP(generalVendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoiceDP',
                    invoiceType: invoiceType
					
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
                        document.getElementById('invoice_dp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoice_dp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoice_dp').options.add(x);
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
    }*/
        
       // if(document.getElementById('generalVendorId').value != "") {
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
					$('#addPO').hide();
					
                }
				//setInvoiceType(generatedInvoiceNo);
            }
			
        });
               /*     $.ajax({
                     url: './get_data.php',
                     method: 'POST',
                   data: "action=getInvoiceNO"
                 	success: function(data) {
                   document.getElementById('generatedInvoiceNo').value = data;
                        }
                    });
                } else {
                  document.getElementById('generatedInvoiceNo').value = "";
              }*/
      
		
		//setInvoiceType(generatedInvoiceNo);
		/*
        ?>
       
        
		
		 if(document.getElementById('invoiceMethod').value == 2 || document.getElementById('invoiceMethod').value == '') {
            $('#generalVendorId2').hide();
        } else {
            $('#generalVendorId2').show();
        }
		*/
	$('#generalVendorId').change(function() {
            if(document.getElementById('generalVendorId').value != '') {
				 setPODetail(document.getElementById('generatedPONo').value);
                $('#addPO').show();
            } else {
               $('#addPO').hide();
			}
		});
        
        <?php
        
			//if($generatedPONo == "") {
        ?>
      /*  $('#contractSeq'	).change(function() {
            if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPONo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPONo').value = "";
            }
        });
        
        $('#contractType').change(function() {
            if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPONo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPONo').value = "";
            }
        });*/
        <?php
     //   }
        ?>
        
        $('#POMethod').change(function() {
            if(document.getElementById('POMethod').value != '') {
                $('#addPO').show();
            } else {
               $('#addPO').hide();
			}
		});
                
                <?php
               // if($generatedInvoiceNo == "") {
                ?>
               // if(document.getElementById('generalVendorId').value != "") {
                 //   $.ajax({
                 //      url: './get_data.php',
                 //       method: 'POST',
                 //       data: "action=getInvoiceNO&="
                //        success: function(data) {
                  //          document.getElementById('generatedInvoiceNo').value = data;
                  //      }
                  //  });
               // } else {
               //     document.getElementById('generatedInvoiceNo').value = "";
               // }
                <?php
               // }
                ?>
          //  }
      //  });
        
        $("#PODataForm").validate({
            rules: {
                //contractType: "required",
                generalVendorId: "required",
                currencyId: "required",
                //exchangeRate: "required",
                //accountId: "required",
                //invoiceType: "required",
				amount: "required",
                stockpileId: "required",
				requestDate: "required"
            },
            messages: {
               // contractType: "Contract Type is a required field.",
                generalVendorId: "Vendor is a required field.",
                currencyId: "Currency is a required field.",
                //exchangeRate: "Exchange Rate is a required field.",
                //accountId: "Account is a required field.",
                //invoiceType: "Invoice Type is a required field.",
				amount: "Amount is a required field.",
                stockpileId: "Stockpile is a required field.",
				requestDate: "Date is a required field"
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#PODataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
								//window.alert(returnval[3]);
                                //document.getElementById('generalPOId').value = returnVal[3];      
                                $('#dataContent').load('contents/PO.php',{} , iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
	$("#insertForm").validate({
			 rules: {
                //contractType: "required",
                //POMethod: "required",
                //currencyId: "required",
               // exchangeRate: "required",
                itemId: "required",
                //POType: "required",
				qty: "required",
				price: "required",
				//generalVendorId: "required",
				amount: "required",
                stockpileId: "required"
				 
            },
            messages: {
               // contractType: "Contract Type is a required field.",
                //POMethod: "Method is a required field.",
                //currencyId: "Currency is a required field.",
                //exchangeRate: "Exchange Rate is a required field.",
                itemId: "Item is a required field.",
                //POType: "PO Type is a required field.",
				qty: "Quantity Type is a required field.",
				price: "Price Type is a required field.",
				//generalVendorId: "Vendor Type is a required field.",
				amount: "Amount is a required field.",
                stockpileId: "Stockpile is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');
						  setPODetail(document.getElementById('generatedPONo').value);
						 $('#insertModal').modal('hide');
                        /*
						if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
								window.alert("berhasil");
                                var resultData = returnVal[3].split('~');
                                 setPODetail(document.getElementById('generatedPONo').value);
                               /* if (resultData[0] == 'INVOICE_DETAIL') {
                                    //setContract(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
//                                    resetFreight(' ');
//                                    setFreight(0, resultData[1], 0);
                                   
                                } */
                                /*
                                $('#insertModal').modal('hide');
                            }
							
							else {
								window.alert("gagal");
					
								
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
								
								}
								
                            
                        }
					   */
                    }
                });
            }
        });
		
		
        
    });
	
	 
</script>
<script type="text/javascript">
	/*
	var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();
 if(dd<10){
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 

today = yyyy+'-'+mm+'-'+dd;
document.getElementById("datefield").setAttribute("max", today);
	*/
	
$(document).ready(function(){
 $('#showTransaction').click(function(e){
	 	
            e.preventDefault();  
        $('#insertModal').modal('show');
        $('#insertModalForm').load('forms/PO-data.php', {
			generalVendorId: $('select[id="generalVendorId"]').val(), 
			generatedPONo: document.getElementById('generatedPONo').value
		});
        });	
});

</script>
<script type="text/javascript">
                    
	if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
	
	  $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });
	
	
	function deletePODetail(idpo_detail) {
		
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: { action: 'delete_po_detail',
					poDetailId: idpo_detail
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
                     setPODetail(document.getElementById('generatedPONo').value);
                }
            }
        });
    }
	
	 /*   
	$('#generalVendorId').change(function() {
            //resetExchangeRate();
			$('#IDP').hide();
            resetGeneralVendorTax();
            
            if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                getGeneralVendorTax2($('select[id="generalVendorId"]').val);
				
	            } 
        });
		

function resetGeneralVendorTax() {
        document.getElementById('ppnPO1').value = '';
        document.getElementById('pphPO1').value = '';
		document.getElementById('ppnPOID').value = '';
		document.getElementById('pphID').value = '';
    }
	
	function getGeneralVendorTax2(generalVendorId) {
		
        if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getGeneralVendorTax2',
                        generalVendorId: generalVendorId,
                        amount: amount
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('ppnPO1').value = returnVal[1];
                        document.getElementById('pphPO1').value = returnVal[2];
						document.getElementById('ppnPOID').value = returnVal[3];
						document.getElementById('pphPOID').value = returnVal[4];
						
                    }
                }
            });
        } else {
            document.getElementById('ppnPO1').value = '0';
            document.getElementById('pphPO1').value = '0';
			document.getElementById('ppnPOID').value = '0';
			document.getElementById('pphPOID').value = '0';
        }
	}
	
	*/
	function setPODetail(generatedPONo) {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setPODetail',
				   generatedPONo: generatedPONo
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
                    $('#PODetail').show();
                    document.getElementById('PODetail').innerHTML = data;
                } else {
					$('#PODetail').hide();
				}
            }
        });
    }
	
function checkSlipInvoice(generalVendorId, ppnPO1, pphPO1, invoiceMethod) {
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
            if (checkedSlips2[i].checked) {
                if(selected2 == "") {
                    selected2 = checkedSlips2[i].value;
                } else {
                    selected2 = selected2 + "," + checkedSlips2[i].value;
                }
            }
        }
        
        var ppnPOValue = 'NONE';
        var pphPOValue = 'NONE';
        
        if (typeof(ppnPO1) != 'undefined' && ppnPO1 != null && typeof(pphPO1) != 'undefined' && pphPO1 != null)
        {
            if(ppnPO1 != 'NONE') {
                if(ppnPO1.value != '') {
                    ppnPOValue = ppnPO1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pphPO1 != 'NONE') {
                if(pphPO1.value != '') {
                    pphPOValue = pphPO1.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
       

        
		setInvoiceDP(generalVendorId, selected2, selected, ppnPOValue, pphPOValue, invoiceMethod);
				
 //alert(generalVendorId);
    }
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
	 checkSlipInvoice(generalVendorId, ppnPO1, pphPO1, invoiceMethod);
 }


	
/*
function setInvoiceDP(generalVendorId, checkedSlips, checkedSlips2, ppn1, pph1, invoiceMethod) {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setInvoiceDP',
					generalVendorId: generalVendorId,
                    checkedSlips: checkedSlips,
					checkedSlips2: checkedSlips2,
                    ppn1: ppn1,
                    pph1: pph1,
					invoiceMethod: invoiceMethod
					
            },
            success: function(data){
                if(data != '') {
                    $('#IDP').show();
                    document.getElementById('IDP').innerHTML = data;
                }
            }
        });
    }

*/


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
<form method="POst" id="PODataForm">
    <input type="hidden" name="action" id="action" value="PO_data" />
    <input type="text" name="POId" id="POId" value="<?php echo $POId; ?>" />
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
            <label>Generated PO No.</label>
            <input type="text" class="span12" readonly id="generatedPONo" name="generatedPONo" value="<?php echo $generatedPONo; ?>">
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
        <label>No Penawaran</label>
     	<input type="text" tabindex="" id="inputnopenawaran" name="inputnopenawaran"  value="<?php echo $inputnopenawaran; ?>" >
        </div>	
        <div class="span1 lightblue">
        </div>
       
        
    </div>
     <div class="row-fluid" style="margin-bottom: 7px;">  
     <div class="span3 lightblue">Tanggal PO
       <input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate" value="<?php echo $requestDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
       </div>      
	   <div class="span1 lightblue">
        </div>

        <div class="span6 lightblue">
          <label>Vendor <span style="color: red;">*</span></label>
          <?php
            createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name", $generalVendorId, $readonlyProperty, "generalVendorId", "general_vendor_id", "general_vendor_name", 
                "", "", "select2combobox100", 1, "", true);
            ?>
        </div>
      </div>
	<div class="row-fluid" style="margin-bottom: 7px;">  
           <div class="span6 lightblue">
          <label>Check by<span style="color: red;">*</span></label>
          <?php
            createCombo("SELECT idmaster_sign, name
                        FROM master_sign", $signId, $readonlyProperty, "signId", "idmaster_sign", "name", 
                "", "", "select2combobox100", 1, "", false);
            ?>
        </div>
      </div>
	<div class="row-fluid">
    
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", "", "select2combobox100");
            ?>
    </div>
   <div class="span4 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div>
<div >
            
        </div>  		
</div>
     <div class="row-fluid" style="margin-bottom: 7px;">  
     <div class="span3 lightblue"></div>
    </div>
 <div id="addPO" class="row-fluid" style="margin-bottom: 7px;">  
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
	<div class="row-fluid" id="PODetail" style="display: none;">
        PO detail
    </div>
   
    
     <div class="row-fluid" id="IDP1" style="display: none;">
        PO DP
    </div>
     <div class="row-fluid" style="margin-bottom: 7px;"> 
        <div class="span8 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>
        
    </div>
	 <div class="row-fluid" style="margin-bottom: 7px;"> 
        <div class="span8 lightblue">
            <label>Terms of Condition</label>
            <textarea class="span12" rows="3" tabindex="" id="toc" name="toc"><?php echo $toc; ?></textarea>
        </div>
        
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;" >
    <form id="insertForm" method="POst" style="margin: 0px;">
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
            <button class="btn btn-primary">Add</button>
        </div>
    </form>
</div>