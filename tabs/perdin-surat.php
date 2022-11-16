<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'invoice';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">
$inputDate = $date->format('d/m/Y');
$sa_id = '';
$sa_no = '';
$id_user = '';
$sa_method = '';
$amount = '';


if(isset($_POST['sa_id']) && $_POST['sa_id'] != '') {

    $sa_id = $_POST['sa_id'];

    //$readonlyProperty2 = ' readonly ';
    //$disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT a.*, DATE_FORMAT(tanggal, '%d/%m/%Y') AS tanggal2, DATE_FORMAT(date_from, '%d/%m/%Y') AS date_from2, DATE_FORMAT(date_to, '%d/%m/%Y') AS date_to2,
b.`general_vendor_name`, d.stockpile_name AS origin2, e.stockpile_name AS destination2, c.stockpile_name 
FROM perdin_surat a
LEFT JOIN general_vendor b ON b.`general_vendor_id` = a.`id_user`
LEFT JOIN stockpile c ON c.`stockpile_id` = a.`stockpile_id`
LEFT JOIN stockpile d ON d.`stockpile_id` = a.`origin`
LEFT JOIN stockpile e ON e.`stockpile_id` = a.`destination`

			WHERE a.sa_id = {$sa_id}
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        //$contractType = $rowData->contract_type;
		$sa_id = $rowData->sa_id;
        $sa_no = $rowData->sa_no;
        $stockpile_id = $rowData->stockpile_id;
        //$shipment_id = $rowData->shipment_id;
        $tanggal = $rowData->tanggal2;
        $date_from = $rowData->date_from2;
        $date_to = $rowData->date_to2;
		$origin = $rowData->origin;
        $destination = $rowData->destination;
		//$sa_method = $rowData->sa_method;
		$id_user = $rowData->id_user;
		$general_vendor_name =  $rowData->general_vendor_name;
		$origin2 =  $rowData->origin2;
		$destination2 =  $rowData->destination2;
		$stockpile_name =  $rowData->stockpile_name;
		//$shipment_no =  $rowData->shipment_no;
		//$sa_method2 =  $rowData->sa_method2;
		$remarks =  $rowData->remarks;
		$paymentFrom =  $rowData->payment_from;
		

		
    }

    // </editor-fold>

}
// </editor-fold>



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
        <?php
        if($sa_id == "") {
        ?>
       // if(document.getElementById('generalVendorId').value != "") {
		   $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getPerdinNo'
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                if(data != '') {
                    document.getElementById('sa_no').value = data;
					$('#addSettlement').hide();
					
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
        <?php
		}else{
        ?>
		
		//setInvoiceType(generatedInvoiceNo);
		<?php
		}
        ?>
        
        
		
		/* if(document.getElementById('invoiceMethod').value == 2 || document.getElementById('invoiceMethod').value == '') {
            $('#generalVendorId2').hide();
        } else {
            $('#generalVendorId2').show();
        }
		
		$('#invoiceMethod').change(function() {
            if(document.getElementById('invoiceMethod').value == 2 || document.getElementById('invoiceMethod').value == '') {
                $('#generalVendorId2').hide();
            } else {
                $('#generalVendorId2').show();
            }
        });*/
        
        <?php
        //if($generatedPoNo == "") {
        ?>
      /*  $('#contractSeq').change(function() {
            if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });
        
        $('#contractType').change(function() {
            if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });*/
        <?php
     //   }
        ?>
		/*$('#benefitType').hide();
		$('#benefitType').change(function() {
			
			$('#advanceDetail').hide();
			$('#addSettlement').hide();
			$('#settlementDetail').hide();
			document.getElementById('id_user').value = '';
			
			$("#id_user").select2({
                width: "100%",
                placeholder: document.getElementById('id_user').value
            });
			//resetIdUser(' Benefit Type ');
		});
		
		$('#sa_method').change(function() {
			
			$('#advanceDetail').hide();
			$('#addSettlement').hide();
			$('#settlementDetail').hide();
			
			document.getElementById('id_user').value = '';
			document.getElementById('benefitType').value = '';
			
			if(document.getElementById('sa_method').value == 2) {
				$('#benefitType').show();
			}else{
				$('#benefitType').hide();
			}
			
			$("#id_user").select2({
                width: "100%",
                placeholder: document.getElementById('id_user').value
            });
			
			//$("#benefitType").select2({
              //  width: "100%",
               // placeholder: document.getElementById('benefitType').value
            //});
			
			//resetIdUser(' Benefit Type ');
			//resetBenefitType(' Benefit Type ');
		});
        
        $('#id_user').change(function() {
			
			$('#advanceDetail').hide();
			$('#addSettlement').hide();
			$('#settlementDetail').hide();
			if(document.getElementById('id_user').value != '') {
				if(document.getElementById('sa_method').value == 2) {
					
					var date_from = document.getElementById('date_from').innerHTML ;
					var date_to = document.getElementById('date_to').innerHTML ;
					var jsDate1 = $('#date_from').datepicker('getDate');
					var jsDate2 = $('#date_to').datepicker('getDate');
					//var a =  jsDate.getFullYear();
					var date1 = new Date(jsDate1);
					var date2 = new Date(jsDate2);
					
					//var date1 = new Date("06/30/2019");
					//var date2 = new Date("07/30/2019");
					var Difference_In_Time = date2.getTime() - date1.getTime();
					var Difference_In_Days = (Difference_In_Time / (1000 * 3600 * 24)) + 1;
					
					//alert(Difference_In_Days);
					
					//var hari = 24*60*60*1000; // format perhitungan dalam 1 hari
					//var firstDate = new Date($("#date_from").val());
					//var secondDate = new Date($("#date_to").val());
					//var total_hari = Math.round(Math.round((tgl_2.getTime() – tgl_1.getTime()) / hari));
					//var diffDays = Math.round(Math.round((secondDate.getTime() - firstDate.getTime()) / (hari)));
					
					//alert(diffDays);
					
                setAdvance($('select[id="id_user"]').val(),Difference_In_Days,$('select[id="benefitType"]').val());
				document.getElementById('hari').value = Difference_In_Days;
				
				}else if(document.getElementById('sa_method').value == 1) {
					
					$('#addSettlement').show();
				
				}
            } else {
               //$('#addSettlement').hide();
			}
		});*/
                
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
        
        $("#AdvanceDataForm").validate({
            rules: {
                //contractType: "required",
                id_user: "required",
				//sa_method: "required",
                sa_no: "required",
                date_from: "required",
                date_to: "required",
                origin: "required",
				tanggal: "required",
				stockpile: "required",
				destination: "required",
				paymentFrom: "required"
                //stockpileId: "required"
            },
            messages: {
               // contractType: "Contract Type is a required field.",
                
                id_user: "This is a required field.",
				//sa_method: "This is a required field.",
                sa_no: "This is a required field.",
                date_from: "This is a required field.",
                date_to: "This is a required field.",
                origin: "This is a required field.",
				tanggal: "This is a required field.",
				stockpile: "This is a required field.",
				destination: "This is a required field.",
				paymentFrom: "This is a required field."
                //stockpileId: "Stockpile is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#AdvanceDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('sa_id').value = returnVal[3];
                                
                                //$('#dataContent').load('contents/perdin-adv_settle.php', { sa_id: returnVal[3] }, iAmACallbackFunction2);
								
								$('#pageContent').load('forms/print-perdin-surat.php', {sa_id: returnVal[3], direct: 1}, iAmACallbackFunction);	
								
//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
	/*$("#insertForm").validate({
			 rules: {
                //contractType: "required",
                sa_id: "required",
                qty: "required",
				price: "required",
				amount: "required",
				notes: "required",
				settlementType: "required",
				accountId: "required",
				items: "required",
				uom: "required",
				
                //stockpileId: "required"
            },
            messages: {
               // contractType: "Contract Type is a required field.",
                sa_id: "This is a required field.",
                qty: "This is a required field.",
				price: "This is a required field.",
				amount: "This is a required field.",
				notes: "This is a required field.",
				settlementType: "This is a required field.",
				accountId: "This is a required field.",
				items: "This is a required field.",
				uom: "This is a required field."
                //stockpileId: "Stockpile is a required field."
            },
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
                                 setSettlementDetail($('input[id="id_user"]').val(),$('input[id="sa_id"]').val());
                                //if (resultData[0] == 'INVOICE_DETAIL') {
                                    //setContract(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), //resultData[1]);
//                                  //  resetFreight(' ');
//                                    setFreight(0, resultData[1], 0);
                                   
                               // } 
                                
                                $('#insertModal').modal('hide');
                            } else {
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
                        }
                    }
                });
            }
        });*/
		
		
        
    });
	
	 
</script>
<script type="text/javascript">
$(document).ready(function(){
 /*$('#showTransaction').click(function(e){
            e.preventDefault();  
        $('#insertModal').modal('show');
        $('#insertModalForm').load('forms/perdin-settle-data.php', {id_user: $('select[id="id_user"]').val(),stockpile_id: $('select[id="stockpile_id"]').val(),sa_id: $('input[id="sa_id"]').val()});
        });	*/
});
//$('#grandTotal').number(true, 2);

/*function sum() {
	
	//alert('TEST');
            //var num1 = document.getElementsByName('checkedSlips2');
            var num2 = document.getElementById('amount_benefit').value;
			var num3 = document.getElementById('grandTotal2').value;
			//var num4 = document.getElementById('pph1').value;
			//var num5 = document.getElementById('termin').value;
			//var total = 0;
			
			//var qty = document.getElementsByName('amount_benefit').value;
			//var result = 0;
			//for (var i = 0; i < qty.length ; i++) {
			//result += qty[i];
			//}
			//for(i = 0; i < num1.length; i++){
			//total += num1[i];
			//}
			//var result = parseFloat(num1) * 100;
			var result = parseFloat(num3) + parseFloat(num2);
            if (!isNaN(result)) {
                document.getElementById('grandTotal').value = result;

				//document.getElementById('tamount').value = result1;
            }
        }*/
</script>
<script type="text/javascript">

	$(document).ready(function () {
		
		/*<?php if($sa_id != ''){ ?>
		
		if(document.getElementById('sa_method').value == 2) {
					$('#addSettlement').hide();
					$('#benefitType').show();
					var date_from = document.getElementById('date_from').innerHTML ;
					var date_to = document.getElementById('date_to').innerHTML ;
					var jsDate1 = $('#date_from').datepicker('getDate');
					var jsDate2 = $('#date_to').datepicker('getDate');
					//var a =  jsDate.getFullYear();
					var date1 = new Date(jsDate1);
					var date2 = new Date(jsDate2);
					
					//var date1 = new Date("06/30/2019");
					//var date2 = new Date("07/30/2019");
					var Difference_In_Time = date2.getTime() - date1.getTime();
					var Difference_In_Days = (Difference_In_Time / (1000 * 3600 * 24)) + 1;
					
					//alert(Difference_In_Days);
					
					//var hari = 24*60*60*1000; // format perhitungan dalam 1 hari
					//var firstDate = new Date($("#date_from").val());
					//var secondDate = new Date($("#date_to").val());
					//var total_hari = Math.round(Math.round((tgl_2.getTime() – tgl_1.getTime()) / hari));
					//var diffDays = Math.round(Math.round((secondDate.getTime() - firstDate.getTime()) / (hari)));
					
					//alert(diffDays);
					
                setAdvance($('select[id="id_user"]').val(),Difference_In_Days,$('select[id="benefitType"]').val());
				document.getElementById('hari').value = Difference_In_Days;
				
				}else if(document.getElementById('sa_method').value == 1) {
					
					$('#addSettlement').show();
					setSettlementDetail(<?php echo $id_user; ?>, <?php echo $sa_id; ?>);
				}
				
        
		
		
		<?php } ?>*/
		//$('#addSettlement').show();
    });
	
	
	
	
	/*function editSettleDetail(settle_id,sa_id) {
		
   <?php if($sa_id != ''){ ?>
   
		$('#insertModal').modal('show');
        $('#insertModalForm').load('forms/perdin-settle-data.php', {settleId: settle_id,id_user: <?php echo $id_user; ?>,stockpile_id: $('select[id="stockpile_id"]').val()});
		//setAdvancePerdin(<?php echo $id_user; ?>);
        <?php } ?>
    }
	
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
                    
	
	function deleteSettleDetail(settle_id,id_user,sa_id) {
		
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: { action: 'delete_settle_detail',
					settle_id: settle_id
				
            },
            success: function(data){
                if(data != '') {
                     setSettlementDetail(id_user,sa_id);
                }
            }
        });
    }
	
	function setSettlementDetail(id_user,sa_id) {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSettlementDetail',
					id_user: id_user,
					sa_id: sa_id
				
            },
            success: function(data){
                if(data != '') {
                    $('#settlementDetail').show();
                    document.getElementById('settlementDetail').innerHTML = data;
                } else {
					$('#settlementDetail').hide();
				}
            }
        });
    }
	
	function resetBenefitType(text) {
        document.getElementById('benefitType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('benefitType').options.add(x);
    }
	
	function resetIdUser(text) {
        document.getElementById('id_user').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('id_user').options.add(x);
    }*/
	
/*function checkSlipInvoice(id_user, ppn1, pph1, invoiceMethod) {
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
        
        var ppnValue = 'NONE';
        var pphValue = 'NONE';
        
        if (typeof(ppn1) != 'undefined' && ppn1 != null && typeof(pph1) != 'undefined' && pph1 != null)
        {
            if(ppn1 != 'NONE') {
                if(ppn1.value != '') {
                    ppnValue = ppn1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph1 != 'NONE') {
                if(pph1.value != '') {
                    pphValue = pph1.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
       

        
		setInvoiceDP(generalVendorId, selected2, selected, ppnValue, pphValue, invoiceMethod);
				
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
	 checkSlipInvoice(generalVendorId, ppn1, pph1, invoiceMethod);
 }


	

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
    }*/
	
/*function setAdvance(id_user, Difference_In_Days, benefitType, checkedSlips, checkedSlips2) {
	
					//alert (diffDays)
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setAdvancePerdin',
					id_user: id_user,
                    checkedSlips: checkedSlips,
					checkedSlips2: checkedSlips2,
					Difference_In_Days:Difference_In_Days,
					benefitType: benefitType
					
            },
            success: function(data){
                if(data != '') {
                    $('#advanceDetail').show();
                    document.getElementById('advanceDetail').innerHTML = data;
                }
            }
        });
    }
function checkSlipAdvance(id_user) {
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
         
		setAdvance(id_user, selected, selected2);
				
 //alert(selected);
    }*/



</script>
<script type="text/javascript">
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
<form method="post" id="AdvanceDataForm" autocomplete="off">
    <input type="hidden" name="action" id="action" value="perdin_surat_data" />
    <input type="hidden" name="sa_id" id="sa_id" value="<?php echo $sa_id; ?>" />
	<input type="hidden" name="hari" id="hari" />
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
            <label>Generated No.</label>
            <input type="text" class="span12" readonly id="sa_no" name="sa_no" value="<?php echo $sa_no; ?>">
        </div>
       
        <div class="span3 lightblue">
       <label>Payment From <span style="color: red;">*</span></label>
       <?php
            createCombo("SELECT '0' as id, 'HO' as info UNION
              		   SELECT '1' as id, 'Stockpile' as info;", $paymentFrom, "", "paymentFrom", "id", "info", "", "", "select2combobox100",1);
            ?>
     	
        </div>
        
       
        
    </div>
	<div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
        <label>Tanggal Advance/Settlement<span style="color: red;">*</span></label>
      	<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="tanggal" name="tanggal" value="<?php echo $tanggal; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
       
        <div class="span3 lightblue">
        <label>On Behalf <span style="color: red;">*</span></label>
            <?php
           createCombo("SELECT general_vendor_id, general_vendor_name FROM general_vendor WHERE (stockpile_id IS NOT NULL AND stockpile_id != 0)", $id_user, "", "id_user", "general_vendor_id", "general_vendor_name", "", "", "select2combobox100", 1);
            ?>
        </div>
        
        <div class="span3 lightblue">
       <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile ORDER BY stockpile_name ASC", $stockpile_id, "", "stockpile_id", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
        
    </div>
     <div class="row-fluid" style="margin-bottom: 7px;">  
     <div class="span3 lightblue">
        <label>Date From<span style="color: red;">*</span></label>
      	<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="date_from" name="date_from" value="<?php echo $date_from; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
       
        <div class="span3 lightblue">
        <label>Date To<span style="color: red;">*</span></label>
      	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="date_to" name="date_to" value="<?php echo $date_to; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
       
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">  
     <div class="span3 lightblue">
        <label>Origin <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $origin, "", "origin", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
       
        <div class="span3 lightblue">
        <label>Destination <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $destination, "", "destination", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
       
    </div>
    
     <div class="row-fluid" style="margin-bottom: 7px;"> 
        <div class="span8 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>
        
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
