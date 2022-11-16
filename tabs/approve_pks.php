<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$paymentDate = $date->format('d/m/Y');

$allowAccount = true;
$allowBank = true;
$allowGeneralVendor = true;
$idPP = '';
$paymentMethod = '';
$stockpileId = '';
$paymentType = '';
$paymentFor = '';
$vendorid = '';
$stockpileContractId = '';
$stockpileId1 = '';

$paymentFrom1 = '';
$paymentTo1 = '';
$vendorHandling = '';
$freightId = '';
$vendorFreightId = '';

$qty = '';
$price = '';
$termin = '';

$currencyId = '';
$journalCurrencyId='';
$bankCurrencyId = '';
$bankId = '';

$exchangeRate = '';
$amount = '';
$taxInvoice = '';
$invoiceNo = '';
$invoiceDate = '';
// $chequeNo = '';
$remarks = '';
// $remarks2 = '';
$beneficiary = '';
$bank = '';
$rek = '';
$swift = '';
$transaksiID = '';
$vendorBankID = '';
$vendorFreightIds = '';
$file1 = '';
$laborId = '';
$whereproperty = '';
$freightCostId1 = '';
$generatedInvoiceNo = '';
$invNotim = '';



$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 14) {
            $allowAccount = true;
        } elseif($row->module_id == 15) {
            $allowBank = true;
        } elseif($row->module_id == 11) {
            $allowGeneralVendor = true;
        }
    }
}



// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "`", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
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
        echo "<option value=''>-- All --</option>";
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

if($_POST['idPP'] != '') {
    $idPP = $_POST['idPP'];

    $sqlPP = "SELECT DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, 
                DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo,
                DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoiceDate1, 
                inv.inv_notim_no, rp.remarks as reject_remarks,
                pp.* FROM pengajuan_payment pp 
                LEFT JOIN invoice_notim inv ON inv.inv_notim_id = pp.inv_notim_id 
				LEFT JOIN reject_ppayment rp on rp.idPP = pp.idPP WHERE pp.idPP = {$idPP}";
            //echo $sqlPP;
    $resultPP = $myDatabase->query($sqlPP, MYSQLI_STORE_RESULT);

    if ($resultPP !== false && $resultPP->num_rows > 0) {
        $rowDataPP = $resultPP->fetch_object();
        $paymentMethod = $rowDataPP->payment_method;
        $stockpileId = $rowDataPP->stockpile_id;
        $paymentType = $rowDataPP->payment_type;
        $paymentFor = $rowDataPP->payment_for;
        $vendorid = $rowDataPP->vendor_id;
        $stockpileContractId = $rowDataPP->stockpile_contract_id;
		$stockpileId2 = $rowDataPP->stockpile_contract_id;

        $stockpileId1 = $rowDataPP->stockpile_id;
        $paymentFrom1 = $rowDataPP->dateFrom;
        $paymentTo1 = $rowDataPP->dateTo;

        $vendorId1 = $rowDataPP->vendor_id;
        $vendorHandling = $rowDataPP->vendor_handling_id;
        $freightId = $rowDataPP->freight_id;
        $vendorFreightId = $rowDataPP->transaction_id;
        $generatedInvoiceNo = $rowDataPP->inv_notim_no;
        $freightCostId1 = $rowDataPP->freight_cost_id;

        $qty = $rowDataPP->qty;
        $price = $rowDataPP->price;
        $termin = $rowDataPP->termin;

        // $currencyId = $rowDataPP->currency_id;
        // $journalCurrencyId = $rowDataPP->journal_currencyId;
        // $bankCurrencyId = $rowDataPP->bank_currencyId;
        // $bankId = $rowDataPP->bankId;

        $amount = $rowDataPP->amount; 
        $exchangeRate = $rowDataPP->exchange_rate;
        $taxInvoice = $rowDataPP->tax_invoice;
        $invoiceNo = $rowDataPP->invoice_no;
        $invoiceDate = $rowDataPP->invoiceDate1;
        // $chequeNo = $rowDataPP->cheque_no;
        $remarks = $rowDataPP->remarks;
        // $remarks2 = $rowDataPP->remaks2;
        $beneficiary = $rowDataPP->beneficiary;
        $bank = $rowDataPP->bank;
        $rek = $rowDataPP->rek;
        $swift = $rowDataPP->swift;
       
        $vendorBankID = $rowDataPP->vendor_bank_id;

        $laborId = $rowDataPP->labor_id;
        $file1 = $rowDataPP->file;
		$reject_remarks = $rowDataPP->reject_remarks;

        if($rowDataPP->status_ppn == 1){
            $checkedPpn ='checked = "checked"';
        }

        if($rowDataPP->status_pph == 1){
            $checkedPph ='checked = "checked"';
        }
    }

    if($paymentFor == 2 && $idPP != ''){  //GET FREIGHT
        $vendorFreightNames = '';
        $vendorFreightName = '';
        $sqlSupplier = "SELECT pps.vendor_id FROM pengajuan_payment_supplier pps
                        INNER JOIN pengajuan_payment pp ON pp.`idPP` = pps.idPP
                        WHERE pps.idPP = {$idPP} AND pp.`freight_id` = {$freightId} ";
        $resultSupplier = $myDatabase->query($sqlSupplier, MYSQLI_STORE_RESULT);
        //echo $sqlSupplier;
       
        if ($resultSupplier !== false && $resultSupplier->num_rows > 0) {
        
            while( $rowSupplier = $resultSupplier->fetch_object()){
                $vendorFreightId = $rowSupplier->vendor_id;
            // $vendorFreightIds = $vendorFreightIds . ',' . $vendorFreightId;
                if($vendorFreightIds == '') {
                    $vendorFreightIds = $vendorFreightId;
                } else {
                    $vendorFreightIds = $vendorFreightIds. ',' . $vendorFreightId;
                }
            
            }
        }
           // $vendorFreightIds = json_encode(explode(',',$vendorFreightIds));

        $sqlVendor = "SELECT vendor_name FROM vendor WHERE vendor_id IN ({$vendorFreightIds})";
        $resultVendor = $myDatabase->query($sqlVendor, MYSQLI_STORE_RESULT);
        if ($resultVendor !== false && $resultVendor->num_rows > 0) {
        
            while($rowVendor = $resultVendor->fetch_object()){
                $vendorFreightName = $rowVendor->vendor_name;
            // $vendorFreightIds = $vendorFreightIds . ',' . $vendorFreightId;
                if($vendorFreightNames == '') {
                    $vendorFreightNames = $vendorFreightName;
                } else {
                    $vendorFreightNames = $vendorFreightNames. ' , ' . $vendorFreightName;
                }
            
            }
        }

    }

if($paymentFor == 1){
    $whereproperty = "ppayment_id = {$idPP}";
}else if($paymentFor == 2){
    $whereproperty = "fc_ppayment_id = {$idPP}";
}else if($paymentFor == 9){
    $whereproperty = "hc_ppayment_id = {$idPP}";
}else if($paymentFor == 3){
    $whereproperty = "uc_ppayment_id = {$idPP}";
}

//GET CONTRACT ID
if(($paymentFor == 2 || $paymentFor == 9) && $idPP != ''){
    // Get Multiple Contract_id
    if($paymentMethod != 1){
        $sqlContract = "SELECT ppc.contract_id FROM pengajuan_pks_contract ppc
        WHERE ppc.idPP = {$idPP}";
        $resultContract = $myDatabase->query($sqlContract, MYSQLI_STORE_RESULT);

        if ($resultContract !== false && $resultContract->num_rows > 0) {
            while( $rowContract = $resultContract->fetch_object()){
                $contractId = $rowContract->contract_id;
                if($contractIds == '') {
                    $contractIds = $contractId;
                } else {
                    $contractIds = $contractIds. ',' . $contractId;
                }
            
            }
        }

        // GET Contract_Name
        $sqlContract = "SELECT contract_no FROM contract WHERE contract_id IN ({$contractIds})";
        $resultContract = $myDatabase->query($sqlContract, MYSQLI_STORE_RESULT);
        if ($resultContract !== false && $resultContract->num_rows > 0) {
        
            while($rowContract = $resultContract->fetch_object()){
                $contractName = $rowContract->contract_no;
                if($contractNames == '') {
                    $contractNames = $contractName;
                } else {
                    $contractNames = $contractNames. ' , ' . $contractName;
                }
            
            }
        }
    }
}


$sql2 = "SELECT * FROM TRANSACTION WHERE {$whereproperty}";
$result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

if ($result2 !== false && $result2->num_rows > 0) {
    while( $row2 = $result2->fetch_object()){
        $transaksiID = $row2->transaction_id;
    }   
}

//Temporary Comment by Yeni
//$sqlIN = "SELECT DATE_FORMAT(inv.due_date_inv, '%d/%m/%Y') AS duedate1, inv.* FROM invoice_notim inv WHERE idPP = {$idPP}";
$sqlIN = "SELECT payment_status FROM pengajuan_payment WHERE idPP = {$idPP}";
$resultIN = $myDatabase->query($sqlIN, MYSQLI_STORE_RESULT);
if ($resultIN !== false && $resultIN->num_rows > 0) {
	while( $rowIN = $resultIN->fetch_object()){
		$status = $rowIN->payment_status;
		if($status == 1 || $status == 5){
			$disableProperty = 'disabled';
		}else{
			$disableProperty = 'enabled';
		}
		
	}
}


}
// </editor-fold>
?>

<!-- <input type="text"  id="vendorFreightId" value="<?php// echo $vendorFreightId; ?>" > -->
<input type="hidden"  id="stockpileIdVal" value="<?php echo $stockpileId; ?>" >
<input type="hidden" id="vendorId1Val" value="<?php echo $vendorId1 ?>" />
<input type="hidden" id="vendorHandlingVal" value="<?php echo $vendorHandling ?>" />
<input type="hidden" id="freightIdVal" value="<?php echo $freightId ?>" />
<input type="hidden" id="vendorFreightIdVal" value="<?php echo $vendorFreightId ?>" />

<input type="hidden" id="amountVal" value="<?php echo $amount ?>" />


<script type="text/javascript">
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
        $('#amount').number(true, 2);

        <?php
        if(isset($_SESSION['payment']) || (isset($_POST['idPP']) && $_POST['idPP'] != '')) {
        ?>
        if(document.getElementById('paymentMethod').value != '') {
            setPaymentType(1, <?php echo $paymentType; ?>);
            setStockpileLocation_GET(<?php echo $stockpileId ?>, <?php echo $idPP ?>); //Get Stockpile Location jika id Ppayment ada

            <?php if($invNotim == ''){ ?>
                getInvoiceNotim(<?php echo $idPP; ?>);
           <?php } ?>
           
            if(document.getElementById('paymentFor').value != '') {
                if(document.getElementById('paymentType').value == 2) {
                    // OUT
                   if(document.getElementById('paymentFor').value == 1) {
                        $('#curahPayment').show();
                    } else if(document.getElementById('paymentFor').value == 2) {
                        if(document.getElementById('paymentMethod').value == 2){
							$('#freightDownPayment').show();
                            document.getElementById('currencyId').value = 1;
							//$('#freightDownPayment2').show();
						}else{
							$('#freightPayment').show();
						}
                    } else if(document.getElementById('paymentFor').value == 9) {
                        //$('#handlingPayment').show();
						if(document.getElementById('paymentMethod').value == 2){
							$('#handlingDownPayment').show();
						}else{
							$('#handlingPayment').show();
						}
                    } else if(document.getElementById('paymentFor').value == 3) {
                        if(document.getElementById('paymentMethod').value == 2){
							$('#unloadingDownPayment').show();
						}else{
							$('#unloadingPayment').show();
						}
                    }
                }

            } 

            //CURAH
            <?php if($idPP != '' && $paymentFor == 1){ //GET DATA FROM ADMIN?>
                setSlipCurah_copy($('select[id="stockpileId1"]').val(), $('select[id="vendorId1"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFrom1"]').val(), $('input[id="paymentTo1"]').val(),  <?php echo $idPP; ?>);
                getVendorBank(2,<?php echo $vendorId1; ?>, <?php echo $paymentFor; ?>, <?php echo $vendorBankID; ?>);
            <?php } ?> 

            //HANDLING
            <?php if($idPP != '' && $paymentFor == 9){ //GET DATA FROM ADMIN?> 
                setSlipHandling_copy($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val(), <?php echo $idPP; ?>);
                <?php if( $paymentMethod == 2){ ?>
                     getHandlingTax(<?php echo $vendorHandling ?>); 
                <?php } ?>
            <?php } ?>

            //FREIGHT   
            <?php if($idPP != '' && $paymentFor == 2){ //GET DATA FROM ADMIN?> 
                setSlipFreight_copy($('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), $('select[id="contractPKHOA"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val(), <?php echo $idPP; ?>);

                <?php  if($paymentMethod == 1) {?>
                    $('#freightPayment1').show();
                <?php } ?>

                <?php if($paymentMethod == 2) {?>
                    getFreightTax(<?php echo $freightId ?>);
                <?php } ?>

                <?php }?>
                //END FREIGHT
			
                //UNLOADING C
                <?php if($idPP != '' && $paymentFor == 3) { ?>
                    setSlipUnloading_copy($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val(), <?php echo $idPP ; ?>);
                    <?php if( $paymentMethod == 2){ ?>
                        getLaborTax(<?php echo $laborId ?>);
                <?php } ?>
                <?php } ?>
        }
        <?php
    }
        ?>
        <?php if($idPP != '' && ($paymentMethod == 1 || $paymentMethod == 2 )){ //GET DATA FROM ADMIN?> 
            $('#divInvoice').show();
            document.getElementById('currencyId').value = 1;
            document.getElementById('amount').value = document.getElementById('amountVal').value;
        <?php } ?>


      //SUBMIT FORM
      $("#paymentDataForm").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                success: function (data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/invoice_pks_views.php', {paymentId: returnVal[3], direct: 1}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $("#PengajuanPaymentDataForm").validate({
                rules: {
                    paymentType: "required",
                    bankId: "required",
                    paymentFor: "required",
                    stockpileId: "required",
                    vendorId: "required",
                    stockpileContractId: "required",
                    customerId: "required",
                    salesId: "required",
                    currencyId: "required",
                    exchangeRate: "required",
                    generalVendorId: "required"
                },
                messages: {
                    paymentType: "Type is a required field.",
                    bankId: "Bank Account is a required field.",
                    paymentFor: "Payment For is a required field.",
                    stockpileId: "Stockpile is a required field.",
                    vendorId: "Vendor is a required field.",
                    stockpileContractId: "PO No. is a required field.",
                    customerId: "Buyer is a required field.",
                    salesId: "Sales Agreement is a required field.",
                    currencyId: "Currency is a required field.",
                    exchangeRate: "Exchange Rate is a required field.",
                    generalVendorId: "Payment From/To is a required field."
                },
            submitHandler: function (form) {
                $('#submitButton2').attr("disabled", true);
            }
        });

        
        $("#reject_form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                success: function (data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('contents/invoice_pks_content.php', {}, iAmACallbackFunction);
                        }
                        $('#returnPpayment1').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    });


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

    function setVendorFreight(type, stockpileId, freightId, vendorFreightId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getVendorFreight',
                stockpileId: stockpileId,
                freightId: freightId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('vendorFreightId_1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('vendorFreightId_1').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorFreightId_1').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('vendorFreightId_1').value = vendorFreightId;
                    }
                }
            }
        });
    }

	function resetFreightBankDP(paymentFor) {
		 if(paymentFor == 2){
			document.getElementById('freightBankDp').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('freightBankDp').options.add(x);
		}
    }

    function getFreightBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				 if(paymentFor == 2){
					if(returnValLength > 0) {
                        document.getElementById('freightBankDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightBankDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightBankDp').options.add(x);
                    }
				}
                    if(type == 1) {
						 if(paymentFor == 2){
						document.getElementById('freightBankDp').value = vendorId;
						}
                    }
                }
            }
        });
    }


	function resetLaborBankDP(paymentFor) {
		 if(paymentFor == 3){
			document.getElementById('laborBankDp').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('laborBankDp').options.add(x);
		}
    }

    function getLaborBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				 if(paymentFor == 3){
					if(returnValLength > 0) {
                        document.getElementById('laborBankDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborBankDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborBankDp').options.add(x);
                    }
				}
                    if(type == 1) {
						 if(paymentFor == 3){
						document.getElementById('laborBankDp').value = vendorId;
						}
                    }
                }
            }
        });
    }

    function getLaborTax(laborDp) {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getLaborTax',
                    laborDp: laborDp
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {
                        document.getElementById('ppnOB').value = returnVal[1];
                        document.getElementById('pphOB').value = returnVal[2];
                        document.getElementById('taxidPpnOB').value = returnVal[3];
                        document.getElementById('taxidPphOB').value = returnVal[4];
                    }
                }
            });
    }



	function resetVendorHandlingBankDP(paymentFor) {
		 if(paymentFor == 9){
			document.getElementById('vendorHandlingBankDp').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('vendorHandlingBankDp').options.add(x);
		}
    }

    function getVendorHandlingBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				 if(paymentFor == 9){
					if(returnValLength > 0) {
                        document.getElementById('vendorHandlingBankDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorHandlingBankDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorHandlingBankDp').options.add(x);
                    }
				}
                    if(type == 1) {
						 if(paymentFor == 9){
						document.getElementById('vendorHandlingBankDp').value = vendorId;
						}
                    }
                }
            }
        });
    }

    function resetVendorBank(paymentFor) {
		//alert (paymentFor);
		if(paymentFor == 0){
			document.getElementById('vendorBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('vendorBankId').options.add(x);
		}else if(paymentFor == 1){
			document.getElementById('curahBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('curahBankId').options.add(x);
		}else if(paymentFor == 2){
			document.getElementById('freightBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('freightBankId').options.add(x);
		}else if(paymentFor == 3){
			document.getElementById('laborBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('laborBankId').options.add(x);
		}else if(paymentFor == 8){
			document.getElementById('gvBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('gvBankId').options.add(x);
		}else if(paymentFor == 9){
			document.getElementById('vendorHandlingBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('vendorHandlingBankId').options.add(x);
		}
    }

    function getVendorBank(type, vendorId, paymentFor, bankId) { //
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				if(paymentFor == 0){
                    if(returnValLength > 0) {
                        document.getElementById('vendorBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorBankId').options.add(x);
                    }
				}else if(paymentFor == 1){
					if(returnValLength > 0) {
                        document.getElementById('curahBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('curahBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('curahBankId').options.add(x);
                    }
				}else if(paymentFor == 2){
					if(returnValLength > 0) {
                        document.getElementById('freightBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightBankId').options.add(x);
                    }
				}else if(paymentFor == 3){
					if(returnValLength > 0) {
                        document.getElementById('laborBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborBankId').options.add(x);
                    }
				}else if(paymentFor == 8){
					if(returnValLength > 0) {
                        document.getElementById('gvBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('gvBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('gvBankId').options.add(x);
                    }
				}else if(paymentFor == 9){
					if(returnValLength > 0) {
                        document.getElementById('vendorHandlingBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorHandlingBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorHandlingBankId').options.add(x);
                    }
				}
                    if(type == 1) {
						if(paymentFor == 0){
                        document.getElementById('vendorBankId').value = vendorId;
						}else if(paymentFor == 1){
						document.getElementById('curahBankId').value = vendorId;
						}else if(paymentFor == 2){
						document.getElementById('freightBankId').value = vendorId;
						}else if(paymentFor == 3){
						document.getElementById('laborBankId').value = vendorId;
						}else if(paymentFor == 8){
						document.getElementById('gvBankId').value = vendorId;
						}else if(paymentFor == 9){
						document.getElementById('vendorHandlingBankId').value = vendorId;
						}
                    }//add by yeni
                    else if(type == 2){
                        if(paymentFor == 0){
                        document.getElementById('vendorBankId').value = bankId;
						}else if(paymentFor == 1){
						document.getElementById('curahBankId').value = bankId;
						}else if(paymentFor == 2){
						document.getElementById('freightBankId').value = bankId;
						}else if(paymentFor == 3){
						document.getElementById('laborBankId').value = bankId;
						}else if(paymentFor == 8){
						document.getElementById('gvBankId').value = bankId;
						}else if(paymentFor == 9){
						document.getElementById('vendorHandlingBankId').value = bankId;
						}
                    }
                }
            }
        });
    }

	function resetVendorBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
		document.getElementById('rek').value = '';
		document.getElementById('swift').value = '';
    }

    function getVendorBankDetail(vendorBankId, paymentFor) {


        if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getVendorBankDetail',
                        vendorBankId: vendorBankId,
                        paymentFor: paymentFor
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('beneficiary').value = returnVal[1];
                        document.getElementById('bank').value = returnVal[2];
						document.getElementById('rek').value = returnVal[3];
						document.getElementById('swift').value = returnVal[4];
                    }
                }
            });
        }
	}


	function resetBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
		document.getElementById('rek').value = '';
		document.getElementById('swift').value = '';
    }

    function getBankDetail(bankVendor, paymentFor) {

            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getBankDetail',
                        bankVendor: bankVendor,
                        paymentFor: paymentFor
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('beneficiary').value = returnVal[1];
                        document.getElementById('bank').value = returnVal[2];
						document.getElementById('rek').value = returnVal[3];
						document.getElementById('swift').value = returnVal[4];
                    }
                }
            });
        
	}


    function setSlipCurah_copy(stockpileId, vendorId, checkedSlips, ppn, pph, paymentFrom1, paymentTo1,idPP) {
        console.log('copy')
	//	alert(paymentFrom1 +', '+ paymentTo1);
    //alert(checkedSlips);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipPengajuanCurah_copy',
                    vendorId: vendorId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom1: paymentFrom1,
					paymentTo1: paymentTo1,
					stockpileId: stockpileId,
                    idPP: idPP
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    // console.log(data);
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }

    function setSlipFreight_copy(stockpileId, freightId, vendorFreightIds, contractPKHOA, checkedSlips, ppn, pph, paymentFrom, paymentTo, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipFreight_copy',
                stockpileId: stockpileId,
                freightId: freightId,
                vendorFreightId: vendorFreightIds,
                contractPKHOA: contractPKHOA,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFrom: paymentFrom,
                paymentTo: paymentTo,
                idPP: idPP
            },
            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }

    function setSlipHandling_copy(stockpileId, vendorHandlingId, checkedSlips, ppn, pph, paymentFromHP, paymentToHP, idPP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipHandling_copy',
                    stockpileId: stockpileId,
                    vendorHandlingId: vendorHandlingId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFromHP: paymentFromHP,
					paymentToHP: paymentToHP,
                    idPP: idPP

            },

            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }

    function setSlipUnloading_copy(stockpileId, laborId, checkedSlips, ppn, pph, paymentFromUP, paymentToUP, idPP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipPengajuanUnloading',
                    stockpileId: stockpileId,
                    laborId: laborId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFromUP: paymentFromUP,
					paymentToUP: paymentToUP,
                    idPP: idPP

            },

            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }

    function resetExchangeRate() {
//        $('#exchangeRate').attr("readonly", true);
        $('#labelExchangeRate').hide();
        $('#inputExchangeRate').hide();
        document.getElementById('bankId').value = '';
        document.getElementById('exchangeRate').value = '';
//        document.getElementById('labelExchangeRate').innerHTML = '';
    }

    function setExchangeRate(bankId, currencyId, journalCurrencyId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setExchangeRate',
                    bankId: bankId,
                    currencyId: currencyId,
                    journalCurrencyId: journalCurrencyId
            },
            success: function(data){
//                alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
//                    alert(returnVal[2]);
//                    document.getElementById('labelExchangeRate').innerHTML = returnVal[1];
                   document.getElementById('exchangeRate').value = returnVal[3];
                    document.getElementById('bankCurrencyId').value = returnVal[1];

                    if(returnVal[1] == 1 && returnVal[1] == returnVal[2] && returnVal[1] == returnVal[3]) {
                        $('#labelExchangeRate').hide();
                        $('#inputExchangeRate').hide();
                    } else {
                        $('#labelExchangeRate').show();
                        $('#inputExchangeRate').show();
                    }

                }
            }
        });
    }

    function getHandlingTax(vendorHandlingDp) {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getHandlingTax',
                    vendorHandlingDp: vendorHandlingDp
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {
                        document.getElementById('ppnH').value = returnVal[1];
                        document.getElementById('pphH').value = returnVal[2];
                        document.getElementById('taxidPpnH').value = returnVal[3];
                        document.getElementById('taxidPphH').value = returnVal[4];
                    }
                }
            });
    }

    function getFreightTax(freightId) {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getFreightTax',
                    freightId: freightId
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {
                        document.getElementById('ppn').value = returnVal[1];
                        document.getElementById('pph').value = returnVal[2];
                        document.getElementById('taxidPpn').value = returnVal[3];
                        document.getElementById('taxidPph').value = returnVal[4];
                    }
                }
            });
    }


    function resetPaymentType(text) {
        document.getElementById('paymentType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('paymentType').options.add(x);
    }

    function setPaymentType(type, paymentType) {
        document.getElementById('paymentType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('paymentType').options.add(x);

        var x = document.createElement('option');
        x.value = '2';
        x.text = 'OUT / Debit';
        document.getElementById('paymentType').options.add(x);

        if(type == 1) {
            document.getElementById('paymentType').value = paymentType;
        }
    }

    function setStockpileLocation_GET(stockpileIdVal, idPP) {
		//alert(idPP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setStockpileLocation_GET',
                stockpileId: stockpileIdVal,
                idPP: idPP
            },
            success: function(data){
                //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    if(returnVal[1] > 1) {
                        $('#stockpileLocationLabel').show();
                    }
                    document.getElementById('stockpileLocationDiv').innerHTML = returnVal[2];
                }
            }
        });
    }

	function hitungPPN() {
    var a = $(".dpSales").val();
    //var b = $(".b2").val();
    c = a * 0.1; //a kali b
    $(".ppnSales").val(c);
    }

    <?php if($invNotim == "") {?>
        function getInvoiceNotim(idPP) {
            //alert(idPP);
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: { action: 'getInvoice_notim',
                        idPP: idPP
                    },
                success: function(data){
    //                alert(data);
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('generatedInvoiceNo').value = returnVal[1];
                    }
                }
            });
        }
    <?php } ?>
    

    function reject2() {
        $(".btn").hide();
        alertify.alert('Processing please Wait...').set('basic', true); 
		$.blockUI({ message: '<h4>Please wait...</h4>' }); 

        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'pengajuan_payment',
                _method: 'REJECT',
                idPP: document.getElementById('idPP').value,
                reject_remarks: document.getElementById('reject_remarks').value
            },
            success: function (data) {
                console.log(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[3]) != 0)	//if no errors
                {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert(returnVal[2]);

                    if (returnVal[1] == 'OK') {
                        $('#pageContent').load('views/invoice_pks_views.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').hide();
                }
            }
        });
    }

    function canceled() {
        $(".btn").hide();
        alertify.alert('Processing please Wait...').set('basic', true); 
		$.blockUI({ message: '<h4>Please wait...</h4>' }); 

        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'pengajuan_payment',
                _method: 'CANCEL',
                idPP: document.getElementById('idPP').value,
                reject_remarks: document.getElementById('reject_remarks').value
            },
            success: function (data) {
                console.log(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                $('#dataContent').load('views/invoice_pks_views.php', {}, iAmACallbackFunction2);
                    }
                    $('#submitButton').hide();
                }
            }
        });
    }

</script>

<form method="post" id="paymentDataForm"  enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="approve_invoice_pks" />
    <input type="hidden" id="vendoridVal" value="<?php echo $vendorid ?>" />
    <input type="hidden" id="stockpileContractIdVal" value="<?php echo $stockpileContractId ?>" />
    <input type="hidden" id="vendorBankID" value="<?php echo $vendorBankID ?>" />

    <input type="hidden" id="idPP" name="idPP" value="<?php echo $idPP ?>" />

    <div class="row-fluid">
        <div class="span1 lightblue">
            <label>Generated<span style="color: red;">*</span> Invoice.</label>
        </div>
        <div class="span2 lightblue">
            <input type="text" class="span12" readonly id="generatedInvoiceNo" name="generatedInvoiceNo"
                   value="<?php echo $invNotim; ?>">
        </div>

        <?php if (isset($file1) && $file1 != '') { ?>
            <div class="span1 lightblue">
                <label>Open File :</label>
            </div>
            <div class="span1 lightblue">
                <a href="<?php echo $file1 ?>" target="_blank">Invoice</a>
            </div>
        <?php } ?>

        <?php if (isset($file2) && $file2 != '') { ?>
            <div class="span1 lightblue">
                <label>Open file : </label>
            </div>
            <div class="span1 lightblue">
                <a href="<?php echo $file2 ?>" target="_blank">Approve</a>
            </div>
        <?php } else {?>
            <div class="span1 lightblue">
                <label>Upload<span style="color: red;">*</span> file Approve:</label>
            </div>
            <div class="span2 lightblue">
                <input type="file" placeholder="File Upload" tabindex="" id="file2" name="file2" class="span12">
            </div>
        <?php } ?>

        <div class="span1 lightblue">
              <label>Due Date<span style="color: red;">*</span></label>
        </div>
        <div class="span1 lightblue">
            <input type="text" placeholder="DD/MM/YYYY" tabindex=""  id="duedate" name="duedate" value = "<?php echo $duedate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>

    </div>

    <div class="row-fluid">
        <div class="span2 lightblue">
            <label>Method <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT '1' as id, 'Payment' as info UNION
                    SELECT '2' as id, 'Down Payment' as info;", $paymentMethod, $disabled, "paymentMethod", "id", "info",
                "", 2);
            ?>
        </div>

        <div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("", "", "", "paymentType", "id", "info",
                "", 3, "", 5);
            ?>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span2 lightblue">
            <label id="stockpileLocationLabel" style="display: none;">Stockpile Location <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="stockpileLocationDiv">

        </div>

        <div class="span2 lightblue">
            <label>Payment For <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT type_transaction_id, type_transaction_name
                            FROM type_transaction", $paymentFor, "", "paymentFor", "type_transaction_id", "type_transaction_name",
                            "", 3, "",5);

                           // echo $paymentFor;
            ?>
        </div>
       
    </div>

<!-- CURAH PAYMENT / OUT-->
    <div class="row-fluid" id="curahPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span2 lightblue">
			<label>Periode From<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">
			<input type="text" readonly placeholder="DD/MM/YYYY" tabindex=""  id="paymentFrom1" name="paymentFrom1" value = "<?php echo $paymentFrom1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
		<div class="span2 lightblue">
			<label>Periode To<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">

			<input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentTo1" name="paymentTo1" value = "<?php echo $paymentTo1; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		</div>

		<div class="row-fluid">
		<div class="span4 lightblue">
			<label>Stockpile <span style="color: red;">*</span></label>
			<?php
            if($idPP != '' && $paymentMethod == 1){
                createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                FROM stockpile s
                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId1, "", "stockpileId1", "stockpile_id", "stockpile_full",
            "", 14, "select2combobox100");
            }else {
                createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                FROM stockpile s
                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId1'], "", "stockpileId1", "stockpile_id", "stockpile_full",
            "", 14, "select2combobox100");
            }
            ?>
        </div>
		<div class="span4 lightblue">
		 <label>Vendor <span style="color: red;">*</span></label>
         <?php
            if($idPP != '' && $paymentFor == 1 && $stockpileId1 != '' ){
                createCombo("SELECT DISTINCT(con.vendor_id), CONCAT(v.vendor_name,' - ', v.vendor_code) AS vendor_name
                FROM stockpile_contract sc
                LEFT JOIN transaction t
                    ON t.stockpile_contract_id = sc.stockpile_contract_id
                INNER JOIN contract con
                    ON con.contract_id = sc.contract_id
                INNER JOIN vendor v
                    ON v.vendor_id = con.vendor_id
                WHERE sc.stockpile_id = {$stockpileId1}
                AND con.contract_type = 'C'
                -- AND t.ppayment_id IS NULL
				 AND t.payment_id IS NULL
                AND con.company_id = {$_SESSION['companyId']}
                AND v.active = 1
                ORDER BY vendor_name ASC", $vendorId1, "", "vendorId1", "vendor_id", "vendor_name",
                "", 10, "select2combobox100", 0);
				
				//echo $vendorId1;
            }else{
                createCombo("", "", "", "vendorId1", "vendor_id", "vendor_name",
                    "", 13, "select2combobox100", 2);
            }
            
            ?>
        </div>
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
             if($idPP != '' && $paymentFor == 1){
                createCombo("SELECT v_bank_id, CONCAT(bank_name,' - ',account_no) AS bank_name 
                             FROM vendor_bank WHERE vendor_id = {$vendorId1}", $vendorBankID, "", "curahBankId", "v_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2) ;
            }else{
                createCombo("", "", "", "curahBankId", "v_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);
            }
            
            ?>
    </div>
</div>
</div>

<!-- FREIGHT DOWN PAYMENT / IN -->
<div class="row-fluid" id="freightDownPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                            FROM stockpile s
                            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileIdFcDp", "stockpile_id", "stockpile_full",
                        "", 14, "select2combobox100");
                    ?>
            </div>
            <div class="span3 lightblue">
                <label>Freight Supplier <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT DISTINCT(fc.freight_id), CONCAT(f.freight_code,' - ', f.freight_supplier) AS freight_supplier
                                FROM freight_cost fc
                                LEFT JOIN transaction t
                                    ON t.freight_cost_id = fc.freight_cost_id
                                INNER JOIN freight f
                                    ON f.freight_id = fc.freight_id
                                INNER JOIN vendor v
                                    ON v.vendor_id = fc.vendor_id
                                WHERE fc.stockpile_id = {$stockpileId}
                                AND t.fc_payment_id IS NULL
                                AND f.active = 1
                                ORDER BY freight_supplier ASC", $freightId, "", "freightIdFcDp", "freight_id", "freight_supplier",
                        "", 15, "select2combobox100", 2);
                    ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT f_bank_id, CONCAT(bank_name,' - ',account_no) AS bank_name 
                            FROM freight_bank WHERE freight_id = {$freightId}", $vendorBankID, "", "freightBankDp", "f_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                ?>
            </div>
        </div>
            <br>

        <div class="row-fluid">
            <div class="span3 lightblue" id = "supplier1">
                <!-- ini untuk get text dari pengajuan supplier -->
                <label>Supplier <span style="color: red;">*</span></label> 
                <textarea disabled class="span12" rows="1" tabindex="" id="vendorFreightNames" name="vendorFreightNames"><?php echo $vendorFreightNames; ?></textarea>
                <input type="hidden"  id="vendorFreightIds" value="<?php echo $vendorFreightIds; ?>" >
            </div>

            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                    <textarea class="span12" rows="1" readonly tabindex="" id="contractNames" name="contractNames"><?php echo $contractNames; ?></textarea>
            </div>

                
            <div class="span3 lightblue">
                <table   cellspacing="25">
                    <tr>
                        <td colspan = "3">PPn% </td>
                        <td colspan = "2">PPh%</td>
                    </tr>
                    <tr>
                        <td><input type="text" readonly class="span12" tabindex="" id="ppn" name="ppn" /></td>
                        <td><input name="ppnStatus1" type="checkbox" id="ppnStatus1" onclick="checktaxstatus()" <?php echo $checkedPpn ?> <?php echo $disableProperty1 ?> >
                            <input type="hidden" class="span12" readonly id="ppnStatus" name="ppnStatus" value="1"></td>
                        <td style="padding: 10px"></td>
                        <td><input type="text" readonly class="span12" tabindex="" id="pph" name="pph"/></td>
                        <td><input name="pphStatus1" type="checkbox" id="pphStatus1" onclick="checktaxstatus()" <?php echo $checkedPph ?> <?php echo $disableProperty1 ?>>
                        <input type="hidden" class="span12" readonly id="pphStatus" name="pphStatus" value="1"></td>
                    </tr>
                    <input type="hidden" class="span12" readonly id="taxidPpn" name="taxidPpn"></td>
                    <input type="hidden" class="span12" readonly id="taxidPph" name="taxidPph"></td>
                </table>
            </div>
		</div>
</div>

	
	<!-- FREIGHT PAYMENT - 1 / OUT--> 
    <div class="row-fluid" id="freightPayment1" style="display: none;">
		<div class="row-fluid">
            <div class="span2 lightblue">
                <label>Periode From<span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom" name="paymentFrom" value = "<?php echo $paymentFrom1; ?>"  data-date-format="dd/mm/yyyy" class="datepicker"  >
            </div>
            <div class="span2 lightblue">
                <label>Periode To<span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentTo" name="paymentTo" value = "<?php echo $paymentTo1; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
		</div>

		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId2, "", "stockpileId2", "stockpile_id", "stockpile_full",
                "", 14, "select2combobox100");
                ?>
            </div>


            <div class="span3 lightblue">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                    <?php
                        if($idPP != '' && $paymentFor == 2){
                            createCombo("SELECT DISTINCT(fc.freight_id), CONCAT(f.freight_code,' - ', f.freight_supplier) AS freight_supplier
                                        FROM freight_cost fc
                                        LEFT JOIN transaction t
                                            ON t.freight_cost_id = fc.freight_cost_id
                                        INNER JOIN freight f
                                            ON f.freight_id = fc.freight_id
                                        INNER JOIN vendor v
                                            ON v.vendor_id = fc.vendor_id
                                        WHERE fc.stockpile_id = {$stockpileId2}
                                        AND t.fc_payment_id IS NULL
                                        AND fc.company_id = {$_SESSION['companyId']}
                                        AND f.active = 1
                                        ORDER BY freight_supplier ASC", $freightId, "", "freightId", "freight_id", "freight_supplier",
                                "", 15, "select2combobox100", 2);
                        }
                        ?>
            </div>


            <div class="span3 lightblue">
            <label>Bank <span style="color: red;">*</span></label>
                    <?php
                        createCombo("SELECT f_bank_id, 
                        CONCAT(bank_name,' - ',account_no) AS bank_name 
                        FROM freight_bank WHERE freight_id = {$freightId}", $vendorBankID, "", "freightBankId", "f_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                    
                    ?>
            </div>
            <div class="span3 lightblue">
                <labe >Supplier <span style="color: red;">*</span></label>
                     <textarea class="span12" rows="1" readonly tabindex="" id="vendorFreightNames" name="remarks"><?php echo $vendorFreightNames; ?></textarea>
            </div>
		</div>
    </div>


<!-- UNLOADING DOWN PAYMENT / IN -->
    <div class="row-fluid" id="unloadingDownPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span4 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                    <?php
                        createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM stockpile s
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileLaborDp", "stockpile_id", "stockpile_full",
                            "", 14, "select2combobox100");
                        ?>
            </div>

            <div class="span4 lightblue">
                <label>Labor <span style="color: red;">*</span></label>
                    <?php
                        createCombo("SELECT DISTINCT(l.labor_id), l.labor_name
                                FROM labor l
                                LEFT JOIN transaction t
                                    ON l.labor_id = t.labor_id AND t.uc_payment_id IS NULL
                                LEFT JOIN unloading_cost uc
                                    ON uc.unloading_cost_id = t.unloading_cost_id AND uc.stockpile_id = {$stockpileId} AND uc.company_id = {$_SESSION['companyId']}
                                WHERE l.active = 1
                                ORDER BY labor_name ASC", $laborId, "", "laborDp", "labor_id", "labor_name",
                            "", 15, "select2combobox100", 2);
                    ?>
            </div>

            <div class="span4 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT l_bank_id, CONCAT(bank_name,' - ',account_no) AS bank_name 
                            FROM labor_bank WHERE labor_id = {$laborId}", $vendorBankID, "", "laborBankDp", "l_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                ?>
            </div>
		</div>

        <div class="row-fluid">
            <div class="span3 lightblue">
                <table   cellspacing="25">
                    <tr>
                        <td colspan = "3">PPn% </td>
                        <td colspan = "2">PPh%</td>
                    </tr>
                    <tr>
                        <td><input type="text" readonly class="span12" tabindex="" id="ppnOB" name="ppnOB" /></td>
                        <td>
                            <input name="ppnStatusOb1" type="checkbox" id="ppnStatusOb1" onclick="checktaxLabor()" <?php echo $checkedPpn ?> <?php echo $disableProperty1 ?> >
                            <input type="hidden" class="span12" readonly id="ppnStatusOb" name="ppnStatusOb" value="1">
                        </td>
                        <td style="padding: 10px"></td>
                        <td><input type="text" readonly class="span12" tabindex="" id="pphOB" name="pphOB"/></td>
                        <td><input name="pphStatusOb1" type="checkbox" id="pphStatusOb1" onclick="checktaxLabor()" <?php echo $checkedPph ?> <?php echo $disableProperty1 ?>>
                        <input type="hidden" class="span12" readonly id="pphStatusOb" name="pphStatusOb" value="1"></td>
                    </tr>
                    <input type="hidden" class="span12" readonly id="taxidPpnOB" name="taxidPpnOB"></td>
                    <input type="hidden" class="span12" readonly id="taxidPphOB" name="taxidPphOB"></td>
                </table>
            </div>

            <div class="span3 lightblue">
                <label>Quantity <span style="color: red;">*</span></label>
                <input type="text" readonly class="span12" tabindex="" id="qtyLabor" name="qtyLabor" value="<?php echo $qty; ?>" />  
            </div>
            <div class="span3 lightblue">
                    <label>Price<span style="color: red;">*</span></label>
                    <input type="text" class="span12" readonly tabindex="" id="priceLabor" name="priceLabor" value="<?php echo $price ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Termin <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" readonly id="terminLabor" name="terminLabor" value="<?php echo $termin ?>" />

            </div>
	    </div>
    </div>

    <!-- UNLOADING PAYMENT / OUT -->
	<div class="row-fluid" id="unloadingPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span2 lightblue">
           <label>Periode From<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">
		<input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP" value="<?php echo $paymentFrom1; ?>" name="paymentFromUP"  data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		<div class="span2 lightblue">
           <label>Periode To<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">
		<input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP" value = "<?php echo $paymentTo1 ?>" name="paymentToUP" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		</div>
		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
        <?php
			createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId3", "stockpile_id", "stockpile_full",
                "", 16,"select2combobox100");

            ?>
        </div>
		<div class="span4 lightblue">
		<label>Labor <span style="color: red;">*</span></label>
        <?php
            if($idPP != '' && $paymentFor == 3){
                createCombo("SELECT DISTINCT(l.labor_id), l.labor_name
                            FROM labor l
                            LEFT JOIN transaction t
                                ON l.labor_id = t.labor_id AND t.uc_payment_id IS NULL
                            LEFT JOIN unloading_cost uc
                                ON uc.unloading_cost_id = t.unloading_cost_id AND uc.stockpile_id = {$stockpileId} AND uc.company_id = {$_SESSION['companyId']}
                            WHERE l.active = 1
                            ORDER BY labor_name ASC",$laborId,"", "laborId", "labor_id", "labor_name",
                    "", 15, "select2combobox100", 2);
            
            }
            
            ?>
        </div>
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            if($idPP != '' && $paymentFor == 3){
                createCombo("SELECT l_bank_id, 
                            CONCAT(bank_name,' - ',account_no) AS bank_name 
                            FROM labor_bank WHERE labor_id = {$laborId}",$vendorBankID,"", "laborBankId", "l_bank_id", "bank_name",
                "", 10, "select2combobox100", 2);
            }
           
            ?>
        </div>
		</div>
    </div>

    <!-- HANDLING PAYMENT / OUT -->
	<div class="row-fluid" id="handlingPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span2 lightblue">
           <label>Periode From<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">
		<input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFromHP" name="paymentFromHP" value = "<?php echo $paymentFrom1; ?>"  data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		<div class="span2 lightblue">
           <label>Periode To<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">
		<input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentToHP" name="paymentToHP" value = "<?php echo $paymentTo1; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		</div>
		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
        <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId4", "stockpile_id", "stockpile_full",
                "", 16,"select2combobox100");
            ?>
        </div>
		<div class="span4 lightblue">
		<label>Vendor Handling <span style="color: red;">*</span></label>
        <?php
            if($idPP != '' && $paymentFor == 9){
                createCombo("SELECT DISTINCT(vhc.vendor_handling_id), CONCAT(vh.vendor_handling_code,' - ', vh.vendor_handling_name) AS vendor_handling
                            FROM vendor_handling_cost vhc
                            LEFT JOIN `transaction` t
                                ON t.handling_cost_id = vhc.handling_cost_id
                            LEFT JOIN vendor_handling vh
                                ON vh.`vendor_handling_id` = vhc.`vendor_handling_id`
                            LEFT JOIN vendor v
                                ON v.vendor_id = vhc.vendor_id
                            WHERE vhc.stockpile_id = {$stockpileId}
                            AND t.hc_payment_id IS NULL
                            AND vhc.company_id = {$_SESSION['companyId']}
                            AND vh.active = 1
                            ORDER BY vendor_handling ASC",$vendorHandling, "", "vendorHandlingId", "vendor_handling_id", "vendor_handling",
                    "", 15, "select2combobox100", 2);
            }
            
            ?>
        </div>
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            if($idPP != '' && $paymentFor == 9){
                createCombo("SELECT vh_bank_id, 
                    CONCAT(bank_name,' - ',account_no) AS bank_name 
                    FROM vendor_handling_bank WHERE vendor_handling_id = {$vendorHandling}",$vendorBankID,"", "vendorHandlingBankId", "vh_bank_id", "bank_name",
                "", 10, "select2combobox100", 2);
            }
            ?>
        </div>
		</div>
    </div>

    <!-- HANDLING DOWN PAYMENT / IN -->
	<div class="row-fluid" id="handlingDownPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileVhDp", "stockpile_id", "stockpile_full",
                                "", 16,"select2combobox100");
                    ?>
            </div>
            <div class="span3 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT DISTINCT(vhc.vendor_handling_id), CONCAT(vh.vendor_handling_code,' - ', vh.vendor_handling_name) AS vendor_handling
                                FROM vendor_handling_cost vhc
                                LEFT JOIN `transaction` t
                                    ON t.handling_cost_id = vhc.handling_cost_id
                                LEFT JOIN vendor_handling vh
                                    ON vh.`vendor_handling_id` = vhc.`vendor_handling_id`
                                LEFT JOIN vendor v
                                    ON v.vendor_id = vhc.vendor_id
                                WHERE vhc.stockpile_id = {$stockpileId}
                                AND t.hc_payment_id IS NULL
                                AND vhc.company_id = {$_SESSION['companyId']}
                                AND vh.active = 1
                                ORDER BY vendor_handling ASC", $vendorHandling,"", "vendorHandlingDp", "vendor_handling_id", "vendor_handling",
                                "", 15, "select2combobox100", 2);
                    ?>
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                    createCombo("SELECT vh_bank_id, CONCAT(bank_name,' - ',account_no) AS bank_name 
                                FROM vendor_handling_bank WHERE vendor_handling_id = {$vendorHandling}", $vendorBankID,"", "vendorHandlingBankDp", "vh_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);

                          
                    ?>
            </div>
            
            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                    <textarea class="span12" rows="1" readonly tabindex="" id="contractNames" name="contractNames"><?php echo $contractNames; ?></textarea>
            </div>
		</div>

        <div class="row-fluid">
            <div class="span3 lightblue">
                <table   cellspacing="25">
                    <tr>
                        <td colspan = "3">PPn% </td>
                        <td colspan = "2">PPh%</td>
                    </tr>
                    <tr>
                        <td><input type="text" readonly class="span12" tabindex="" id="ppnH" name="ppnH" /></td>
                        <td>
                            <input name="ppnStatusHC1" type="checkbox" id="ppnStatusHC1" onclick="checktaxHC()" <?php echo $checkedPpn ?> <?php echo $disableProperty1 ?> >
                            <input type="hidden" class="span12" readonly id="ppnStatusHC" name="ppnStatusHC" value="1">
                        </td>
                        <td style="padding: 10px"></td>
                        <td><input type="text" readonly class="span12" tabindex="" id="pphH" name="pphH"/></td>
                        <td><input name="pphStatusHC1" type="checkbox" id="pphStatusHC1" onclick="checktaxHC()" <?php echo $checkedPph ?> <?php echo $disableProperty1 ?>>
                        <input type="hidden" class="span12" readonly id="pphStatusHC" name="pphStatusHC" value="1"></td>
                    </tr>
                    <input type="hidden" class="span12" readonly id="taxidPpnH" name="taxidPpnH"></td>
                    <input type="hidden" class="span12" readonly id="taxidPphH" name="taxidPphH"></td>
                </table>
            </div>
            <div class="span3 lightblue">
                <label>Quantity <span style="color: red;">*</span></label>
                <input type="text" class="span12" <?php echo $readonly1 ?> tabindex="" id="qtyHandlingDP" name="qtyHandlingDP" value="<?php echo $qty; ?> "/>
            </div>
            <div class="span3 lightblue">
                <label>Price<span style="color: red;">*</span></label>
                <input type="text" class="span12" <?php echo $readonly1 ?> tabindex="" id="priceHandlingDP" name="priceHandlingDP" value="<?php echo $price ?>"/>
            </div>
            <div class="span3 lightblue">
                <label>Termin <span style="color: red;">*</span></label>
                <input type="text" class="span12" <?php echo $readonly1 ?> tabindex="" id="terminHandlingDP" name="terminHandlingDP" value="<?php echo $termin ?>"/>
            </div>
		</div>
        
    </div>
	</br>

    <!-- SLIP PAYMENT  -->
    <div class="row-fluid" id="slipPayment" style="display: none;">
        slip
    </div>
    <div class="row-fluid" id="summaryPayment" style="display: none;">
        summary
    </div>
    <div class="row-fluid">

<!-- GET BANK  -->
    <div class="span1 lightblue">
        <label>From/To <span style="color: red;">*</span></label>
    </div>

    <div class="span3 lightblue">
        <input type="hidden" name="currencyId" id="currencyId" value="<?php echo $currencyId; ?>" />
        <input type="hidden" name="journalCurrencyId" id="journalCurrencyId" value="1" />
        <input type="hidden" name="bankCurrencyId" id="bankCurrencyId" value="<?php echo $_SESSION['payment']['bankCurrencyId']; ?>" />

        <?php
            createCombo("SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full
                    FROM bank b
                    INNER JOIN currency cur
                        ON cur.currency_id = b.currency_id
                    ORDER BY b.bank_name ASC, cur.currency_code ASC, b.bank_account_name", $bankId, "", "bankId", "bank_id", "bank_full",
                "", 18, "select2combobox100", 1, "");
        ?>
    </div>

    <div class="span1 lightblue">
        <label>Amount :<span style="color: red;">*</span></label>
    </div>
    <div class="span3 lightblue">
        <input type="text"  readonly class="span12" tabindex="" id="amount" name="amount" value="<?php echo $amount?>">
    </div>

    <div class="span1 lightblue" id="labelExchangeRate" style="display: none;">
        <label>ExchangeRate<span style="color: red;">*</span></label>
    </div>
    <div class="span3 lightblue" id="inputExchangeRate" style="display: none;">
        <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $kurs; ?>" />
    </div>

    </div>
    <!-- GET INVOICE -->
    <div class="row-fluid" id="divInvoice" style="display: none;">
        <div class="span1 lightblue">
            <label>Tax Invoice:</label>
        </div>
        <div class="span3 lightblue">
            <input type="text" class="span12" readonly tabindex="" id="taxInvoice" name="taxInvoice" value="<?php echo $taxInvoice; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Invoice No:</label>
        </div>
        <div class="span3 lightblue">
            <input type="text" class="span12" readonly tabindex="" id="invoiceNo" name="invoiceNo" value="<?php echo $invoiceNo; ?>">
        </div>

        <div class="span1 lightblue">
            <label>Invoice Date :</label>
        </div>
        <div class="span3 lightblue">
        <input type="text" placeholder="DD/MM/YYYY" readonly tabindex="" id="invoiceDate" name="invoiceDate" value = "<?php echo $invoiceDate ; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

    </div>


    <div class="row-fluid">
        <div class="span1 lightblue">
            <label>Remarks :</label>
        </div>
        <div class="span11 lightblue">
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>


        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span1 lightblue">
            <label>Beneficiary:</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary" value="<?php echo $beneficiary; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Bank :</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="bank" name="bank" value="<?php echo $bank; ?>">
        </div>

    </div>

    <div class="row-fluid">

        <div class="span1 lightblue">
            <label>No Rek :</label>
        </div>
        <div class="span5 lightblue">
       		<input type="text" readonly class="span12" tabindex="" id="rek" name="rek" value="<?php echo $rek; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Swift Code:</label>
        </div>
        <div class="span5 lightblue">
        	<input type="text" readonly class="span12" tabindex="" id="swift" name="swift" value="<?php echo $swift; ?>">
        </div>

    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>  id="submitButton2">Receive</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<hr>


<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject/Cancel Remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
    </div>

</div>

<div class="row-fluid">
    <div class="span12 lightblue">
    <button class="btn btn-danger"  <?php echo $disableProperty; ?> id="canceled" onclick= "canceled()" style="margin: 0px;">Cancel</button>
    <button class="btn btn-warning"  <?php echo $disableProperty; ?> id="reject1" onclick= "reject2()" style="margin: 0px;">Reject</button>
    <!-- <button class="btn btn-danger" onclick="cancelPG()">CANCEL</button> -->
    </div>
</div>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" >
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
