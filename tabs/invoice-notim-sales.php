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
$invNotim = '';
$disableProperty1 = 'disabled';

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "`", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
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

    $sqlPP = "SELECT sp.stockpile_name AS spNameLocation, ty.type_transaction_name AS paymentFor,
            CASE WHEN pp.payment_method  = 1 THEN 'Payment'
                WHEN pp.payment_method = 3 THEN 'Final Payment'
            ELSE 'Down Payment' END AS methodText,
            DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, 
            DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo,
            DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoiceDatePengajuan, 
            spVendor.stockpile_name,
            CASE WHEN pp.freight_id <> 0 THEN f.freight_supplier
            WHEN pp.freight_id <> 0 THEN lab.labor_name
            WHEN pp.vendor_handling_id <> 0 THEN vh.vendor_handling_name
            WHEN pp.vendor_id <> 0 THEN v.vendor_name
            WHEN pp.customer_id <> 0 THEN  cus.customer_name ELSE '' END AS vendorName,
            CASE WHEN pp.payment_type = 1 THEN 'In/Credit' ELSE 'Out/Debit' END AS  paymentTypeText,
            CASE WHEN pp.freight_id <> 0 THEN CONCAT(fb.bank_name,' - ',fb.account_no)
            WHEN pp.freight_id <> 0 THEN  CONCAT(lbank.bank_name,' - ',lbank.account_no)
            WHEN pp.vendor_handling_id <> 0 THEN  CONCAT(vhb.bank_name,' - ',vhb.account_no)
            WHEN pp.vendor_id <> 0 THEN  CONCAT(vb.bank_name,' - ',vb.account_no)
            WHEN pp.customer_id <> 0 THEN   CONCAT(cb.bank_name,' - ',cb.account_no) ELSE '' END AS vendorbank,
            CASE WHEN pp.customer_id <> 0 THEN (SELECT a.shipment_no FROM shipment a 
            LEFT JOIN TRANSACTION b ON a.shipment_id = b.shipment_id 
            LEFT JOIN pengajuan_sales c ON c.transaction_id = b.transaction_id
            WHERE c.idPP = pp.idPP LIMIT 1) ELSE '' END AS contractNo,
            pp.* FROM pengajuan_payment_sales pp 
            LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_location
            LEFT JOIN type_transaction ty ON ty.type_transaction_id = pp.payment_for
            LEFT JOIN pengajuan_payment_supplier pps ON pps.idpp = pp.idpp
            LEFT JOIN stockpile spVendor ON spVendor.stockpile_id = pp.stockpile_id
            LEFT JOIN vendor v ON v.vendor_id = pp.vendor_id
            LEFT JOIN vendor_bank vb ON vb.vendor_id = pp.vendor_id
            LEFT JOIN freight f ON f.freight_id = pp.freight_id
            LEFT JOIN freight_bank fb ON fb.freight_id = pp.freight_id
            LEFT JOIN labor lab ON lab.labor_id = pp.labor_id
            LEFT JOIN labor_bank lbank ON lbank.labor_id = pp.labor_id
            LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id
            LEFT JOIN vendor_handling_bank vhb ON vhb.vendor_handling_id = pp.vendor_handling_id
            LEFT JOIN customer cus ON cus.customer_id = pp.customer_id
            LEFT JOIN customer_bank cb ON cb.cust_bank_id = pp.vendor_bank_id
            WHERE pp.idPP = {$idPP}";
		//	echo $sqlPP;
    $resultPP = $myDatabase->query($sqlPP, MYSQLI_STORE_RESULT);

    if ($resultPP !== false && $resultPP->num_rows > 0) {
        $row = $resultPP->fetch_object();
        $filePengajuan = $row->file;
        $stockpileLocationId = $row->stockpile_location;
        $stockpileLocationText = $row->spNameLocation;
        $paymentMethod = $row->payment_method;
        $methodText = $row->methodText;
        $paymentForId = $row->payment_for;
        $paymentForText = $row->paymentFor;
        $amount = number_format($row->grand_total, 2, ".", ",");

        $taxInvoice = $row->tax_invoice;
        $invoiceNo = $row->invoice_no;
        $invoiceDate = $row->invoiceDatePengajuan;
        $remarks = $row->remarks;
        $beneficiary = $row->beneficiary;
        $bankName = $row->bank;
        $rekNo = $row->rek;
        $swift = $row->swift;
        $dppamount = $row->total_dpp;
        $totalPPn = $row->total_ppn_amount;
        $totalPPh = $row->total_pph_amount;
        $totalAmount = $row->total_amount;

        $paymentFrom = $row->dateFrom;
        $paymentTo = $row->dateTo;
        $bankvendorId = $row->vendor_bank_id;
		$paymentTypeId = $row->payment_type;
		$paymentTypeText = $row->paymentTypeText;
		$contractNo = $row->contractNo;
		
		//Sales
		
		$stockpileIdSales = $row->stockpile_id;
        $stockpileIdSalesText = $row->stockpile_name;
        $customerId = $row->customer_id;
        $customerText = $row->vendorName;
        $salesBankText = $row->vendorbank;

        //CURAH
        $stockpileIdCurahId = $row->stockpile_id;
        $stockpileIdCurahText = $row->stockpile_name;
        $vendorId = $row->vendor_id;
        $vendorText = $row->vendorName;
        $curahBankText = $row->vendorbank;
        

        //FREIGHT PAYMENT
        $stockpileIdFreightId = $row->stockpile_id;
        $stockpileIdFreightText = $row->stockpile_name;
        $freightId = $row->freight_id;
        $freightName = $row->freight_supplier;
        $freightbankText = $row->vendorbank;

        //Freight Dp
        $supplierId = $row->vendor_id;
        $supplierText = $row->vendorName;

        //HANDLING PAYMENT
        $stockpileIdHandlingId = $row->stockpile_id;
        $stockpileIdHandlingText = $row->stockpile_name;
        $vendorHandlingId = $row->vendor_handling_id;
        $vendorHandlingText = $row->vendor_handling_name;
        $vhbank = $row->vendorbank;

        //UNLOADING
        $stockpileOBId = $row->stockpile_id;
        $stockpileOBText = $row->stockpile_name;
        $laborId = $row->labor_id;
        $laborText = $row->labor_name;
        $lbank = $row->vendorbank;

        //DOWN PAYMENT
        $qtyDp = number_format($row->total_qty, 2, ".", ",");
        $priceDp = number_format($row->price, 2, ".", ",");
        $terminDp = number_format($row->termin, 2, ".", ",");
        $originalAmountDp = number_format($row->total_dpp, 2, ".", ",");


    }

    if(($paymentForId == 2 || $paymentForId == 9 || $paymentForId == 1) && $idPP != ''){
        $sqlContract = "SELECT ppc.contract_id FROM pengajuan_pks_contract ppc
            WHERE ppc.idPP = {$idPP}";
        $resultContract = $myDatabase->query($sqlContract, MYSQLI_STORE_RESULT);
      //  echo "AA ". $sqlContract;
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

    $method = 'INSERT';
}
?>


<script type="text/javascript">
    $(document).ready(function(){

        $(".select2combobox100").select2({
            width: "100%"
        });

        $('#amount').number(true, 2);
        $('#paymentMethod').attr("readonly", true);
        $('#paymentType').attr("readonly", true);
        $('#paymentFor').attr("readonly", true);

        <?php if($idPP != '') { ?>
            getInvoiceNotim(<?php echo $idPP; ?>);

            //SALES  
            <?php if ($paymentForId == 1 && $paymentTypeId == 1) { //GET DATA FROM ADMIN 
            ?>
                <?php if ($paymentMethod == 2) { ?> //downpayment
                    $('#salesPayment').hide();
                    $('#salesDownPayment').show();
                    setSlipSalesDp(<?php echo $idPP; ?>);
                <?php } else if ($paymentMethod == 1) { ?> //payment
                    $('#salesPayment').show();
                    $('#salesDownPayment').hide();
                    document.getElementById("paymentFromSales").disabled = true;
                    document.getElementById("paymentToSales").disabled = true;
                    setSlipSales(<?php echo $stockpileIdSales ?>, <?php echo $customerId ?>, '', '', 'NONE', 'NONE', '', '', <?php echo $idPP ?>);
                <?php } else if ($paymentMethod == 3) { ?>
                    $('#salesDownPayment').hide();
                    $('#salesPayment').hide();
                    document.getElementById("paymentFrom").disabled = true;
                    document.getElementById("paymentTo").disabled = true;
                <?php } ?>
                <?php if ($tipeBayar == 1) { ?>
                    $('#tglBayarDiv').show();
                <?php } else { ?>
                    $('#tglBayarDiv').hide();
            <?php }
            } else if ($paymentForId == 1) {?>
			
			//CURAH  
            
                <?php if ($paymentMethod == 2) { ?> //downpayment
                    $('#curahPayment').hide();
                    $('#curahDownPayment').show();
                    setSlipDp(<?php echo $idPP; ?>);
                <?php } else if ($paymentMethod == 1) { ?> //payment
                    $('#curahPayment').show();
                    $('#curahDownPayment').hide();
                    document.getElementById("paymentFromCur").disabled = true;
                    document.getElementById("paymentToCur").disabled = true;
                    setSlipCurah(<?php echo $stockpileIdCurahId ?>, <?php echo $vendorId ?>, '', '', 'NONE', 'NONE', '', '', <?php echo $idPP ?>);
                <?php } else if ($paymentMethod == 3) { ?>
                    $('#curahDownPayment').hide();
                    $('#curahPayment').hide();
                    document.getElementById("paymentFrom").disabled = true;
                    document.getElementById("paymentTo").disabled = true;
                <?php } ?>
                <?php if ($tipeBayar == 1) { ?>
                    $('#tglBayarDiv').show();
                <?php } else { ?>
                    $('#tglBayarDiv').hide();
            <?php }
            } ?>

             //FREIGHT   
            <?php if($paymentForId == 2){ //GET DATA FROM ADMIN ?> 
                // setSlipFreight_settleApprove(<?php echo $stockpileId ?>, <?php echo $freightId ?>,$('input[id="paymentFromFP"]').val(),$('input[id="paymentToFP"]').val(), <?php echo $idPP; ?>);
                <?php if($paymentMethod == 2) {?>
                    $('#freightPayment1').hide();
                    $('#freightDownPayment').show();
                    setSlipDp(<?php echo $idPP; ?>);
                <?php } else if($paymentMethod != 2) {?> 
                    $('#freightPayment1').show(); 
                    $('#freightDownPayment').hide();
                    document.getElementById("paymentFromFP").disabled = true;
                    document.getElementById("paymentToFP").disabled = true;
                    <?php if($paymentMethod == 1){ ?>
                    setSlipFreight_1(<?php echo $stockpileIdFreightId ?>, <?php echo $freightId ?>, '', '', 'NONE', 'NONE','','', <?php echo $idPP ?>);
                <?php } else { ?>
                    updatePengajuanPayment(<?php echo $idPP ?>);
                    setSlipFreight_settle(<?php echo $stockpileIdFreightId ?>, <?php echo $freightId ?>, '',  '', '', '', 'NONE', 'NONE', '', '', <?php echo $idPP ?>);
                <?php }
                }?>
                <?php if($tipeBayar == 1) { ?>
                    $('#tglBayarDiv').show(); 
                <?php }else{ ?>
                    $('#tglBayarDiv').hide(); 
            <?php }
            }?> 
        <?php } ?>

		//HANDLING
		<?php if ($paymentForId == 9) { //GET DATA FROM ADMIN 
		?>
			<?php if ($paymentMethod != 1) { ?> //downpayment
				$('#handlingPayment').hide();
				$('#handlingDownPayment').show();
				setSlipDp(<?php echo $idPP; ?>);
			<?php } else if ($paymentMethod != 2) { ?> //payment
				$('#handlingPayment').show(); 
				$('#handlingDownPayment').hide();
				$('#handlingPaymentSettle').hide(); 
				document.getElementById("paymentFromHP").disabled = true;
				document.getElementById("paymentToHP").disabled = true;
				setSlipHandling(<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId?>, '','', 'NONE', 'NONE', '','',<?php echo $idPP ?>);
			<?php } ?>
		 
			<?php if ($tipeBayar == 1) { ?>
				$('#tglBayarDiv').show();
			<?php } else { ?>
				$('#tglBayarDiv').hide();
			<?php }
			} ?>

        //UNLOADING (OB)   
        <?php if ($paymentForId == 3) { //GET DATA FROM ADMIN 
        ?>
            <?php if ($paymentMethod == 2) { ?> //downpayment
                $('#unloadingPayment').hide();
                $('#unloadingDownPayment').show();
                setSlipDp(<?php echo $idPP; ?>);
            <?php } else if ($paymentMethod == 1) { ?> //payment
                $('#unloadingPayment').show();
                $('#unloadingDownPayment').hide();
                document.getElementById("paymentFromUP").disabled = true;
                document.getElementById("paymentToUP").disabled = true;
                setSlipUnloading(<?php echo $stockpileOBId; ?>, <?php echo $laborId; ?>, '', 'NONE', 'NONE', '', '', <?php echo $idPP; ?>);
            <?php } else if ($paymentMethod == 3) { ?>
                $('#unloadingDownPayment').hide();
                $('#unloadingPayment').hide();
                document.getElementById("paymentFrom").disabled = true;
                document.getElementById("paymentTo").disabled = true;
            <?php } ?>
            <?php if ($tipeBayar == 1) { ?>
                $('#tglBayarDiv').show();
            <?php } else { ?>
                $('#tglBayarDiv').hide();
            <?php }
        } ?>


	$('#jurnalInvoiceSales').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice_sales',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/invoice-notim-sales.php', {idPP: <?php echo $idPP; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
      //SUBMIT FORM
      $("#ApproveInvoiceForm").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
			
			$.blockUI({ message: '<h4>Please wait...</h4>' });
			$('#loading').css('visibility','visible');

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
                            $('#pageContent').load('views/invoice-notim-sales.php', {idPP: returnVal[3], direct: 1}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
			$('#loading').css('visibility','hidden');
        });

        $("#ApproveInvoiceForm").validate({
                rules: {
                    paymentType: "required",
                    bankId: "required",
                    paymentFor: "required",
                    stockpileId: "required",
                    vendorId: "required",
                    stockpileContractId: "required",
                    currencyId: "required",
                    exchangeRate: "required",
                },
                messages: {
                    paymentType: "Type is a required field.",
                    bankId: "Bank Account is a required field.",
                    paymentFor: "Payment For is a required field.",
                    stockpileId: "Stockpile is a required field.",
                    vendorId: "Vendor is a required field.",
                    stockpileContractId: "PO No. is a required field.",
                    currencyId: "Currency is a required field.",
                    exchangeRate: "Exchange Rate is a required field.",
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

    function setSlipFreight_1(stockpileId_1, freightId_1, contractFreight, checkedSlips, ppn, pph, paymentFromFP, paymentToFP, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipFreight_1',
                    stockpileId_1: stockpileId_1,
                    freightId_1: freightId_1,
                    contractFreight: contractFreight,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
                    paymentFromFP: paymentFromFP,
                    paymentToFP: paymentToFP,
                    idPP: idPP
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if(parseInt(return_val[0])!=0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }

    function setSlipFreight_settle(stockpileId, freightId, settle, contractNo, checkedSlips, checkedSlipsDP, ppn, pph, paymentFrom, paymentTo, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipFreight_settle',
                    stockpileId: stockpileId,
                    freightId: freightId,
                    contractFreight: contractNo,
                    checkedSlips: checkedSlips,
                    checkedSlipsDP: checkedSlipsDP,
                    ppn: ppn,
                    pph: pph,
                    paymentFrom: paymentFrom,
                    paymentTo: paymentTo,
                    settle : settle,
                    idPP : idPP
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                    if(parseInt(return_val[0])!=0)	//if no errors
                    {
                        document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }

    function setSlipHandling(stockpileId, vendorHandlingId, contractHandling, checkedSlips, ppn, pph, paymentFromHP, paymentToHP, idPP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipHandling',
                    stockpileId: stockpileId,
                    vendorHandlingId: vendorHandlingId,
                    contractHandling: contractHandling,
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

                var return_val = data.split('|');
                if(parseInt(return_val[0])!=0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }

    // DP FREIGHT
    function setSlipDp(idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipDp',
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

    function setSlipUnloading(stockpileId, laborId, checkedSlips, ppn, pph, paymentFromUP, paymentToUP, idPP) {
        //alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipUnloading',
                stockpileId: stockpileId,
                laborId: laborId,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFromUP: paymentFromUP,
                paymentToUP: paymentToUP,
                idPP: idPP

            },

            success: function(data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0) //if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }

    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
			orientation: "bottom auto",
			autoclose:true,
            startView: 0
        });
    });

        function getInvoiceNotim(idPP) {
            //alert(idPP);
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: { action: 'getInvoice_notim_sales',
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
    
    function reject2() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'pengajuan_payment_sales',
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
                        $('#pageContent').load('views/invoice-notim-sales.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function canceled() {
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
                if (parseInt(returnVal[3]) != 0)	//if no errors
                {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert(returnVal[2]);

                    if (returnVal[1] == 'OK') {
                        $('#pageContent').load('views/invoice-pks-views.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function setSlipSales(stockpileIdSales, customerId, contractSales, checkedSlips, ppn, pph, paymentFromSales, paymentToSales, idPP) { //checkedSlips awalnya Kosong
		//alert(vendorId +', '+ ppn +', '+ pph);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipSales',
                    stockpileIdSales: stockpileIdSales,
                    customerId: customerId,
                    contractSales: contractSales,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFromSales: paymentFromSales,
					paymentToSales: paymentToSales,
                    idPP: idPP
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                    if(parseInt(return_val[0])!=0)	//if no errors
                    {
                        document.getElementById('amount').value = return_val[1];
                    }

            }
        });
    }
	
	function setSlipCurah(stockpileId, vendorId, contractCurah, checkedSlips, ppn, pph, paymentFromCur, paymentToCur, idPP) { //checkedSlips awalnya Kosong
		//alert(vendorId +', '+ ppn +', '+ pph);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipCurah',
                    stockpileId: stockpileId,
                    vendorId: vendorId,
                    contractCurah: contractCurah,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom1: paymentFromCur,
					paymentTo1: paymentToCur,
                    idPP: idPP
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                    if(parseInt(return_val[0])!=0)	//if no errors
                    {
                        document.getElementById('amount').value = return_val[1];
                    }

            }
        });
    }

    function updatePengajuanPayment(idPP) {
            //alert(idPP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'updatePengajuanPayment',
                idPP: idPP
                },
            success: function(data){
            }
        });
    }
</script>

<form method="post" id="ApproveInvoiceForm"  autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="approve_invoice_sales" />
    <input type="hidden" name="_method" value="<?php echo $method; ?>">
    <input type="hidden" id="idPP" name="idPP" value="<?php echo $idPP ?>" />
    <input type="hidden" id="invId" name="invId" value="<?php echo $invId ?>" />

    <div class="row-fluid">
        <?php if($invStatus == '4I' || $invStatus == 2 || $pStatus == 2 ) { ?>
            <div class="span12 lightblue">
                <label style="color: red;"><center><b>RETURNED/CANCELED</center></b></label>
            </div>
            <hr>
        <?php } ?>
    </div>

    <div class="row-fluid">
        <div class="span2 lightblue">
            <label>Pengajuan No : <b><?php echo $idPP ?> </b></label>
        </div>

        <div class="span1 lightblue">
              <label>Due Date<span style="color: red;">*</span></label>
        </div>
        <div class="span1 lightblue">
            <input type="text" <?php echo $readOnly ?> placeholder="DD/MM/YYYY" tabindex=""  id="duedate" name="duedate" value = "<?php echo $duedate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
       
    </div>
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Generated Invoice<span style="color: red;">*</span> </label>
            <input type="text" class="span12" readonly id="generatedInvoiceNo" name="generatedInvoiceNo"
                   value="<?php echo $generatedInvoiceNo; ?>">
        </div>

       
        <div class="span1 lightblue" style = "width:28%;">
            <?php if (isset($filePengajuan) && $filePengajuan != '') { ?>
                <label>File Pengajuan : <a href="<?php echo $filePengajuan ?>" target="_blank"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></label>
            <?php } ?>
            <?php if(isset($invId) && $invId != '') { ?>
                <label>file Invoice : <a href="<?php echo $file2 ?>" target="_blank">Approve</a></label>
            <?php } else {?>
                <label>
                    Upload file :   <input type="file" placeholder="File Upload"  id="file2" name="file2">
                </label>
            <?php } ?>
        </div>

        <div class="span4 lightblue">
            <label id="stockpileLocationLabel">Stockpile Location <span style="color: red;">*</span></label>
            <input type="hidden" id="stockpileLocationId" name="stockpileLocationId" value = "<?php echo $stockpileLocationId ?>">
            <input type="text" id="stockpileLocationText" readonly name="stockpileLocationText" value = "<?php echo $stockpileLocationText ?>">

        </div>
    </div>
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label id="stockpileLocationLabel">Method <span style="color: red;">*</span></label>
            <input type="hidden" id="paymentMethod" name="paymentMethod" value = "<?php echo $paymentMethod ?>">
            <input type="text" id="methodText" readonly name="methodText" value = "<?php echo $methodText ?>">

        </div>
        <div class="span3 lightblue">
            <label>Type <span style="color: red;">*</span></label>
            <input type="hidden" id="paymentTypeId" name="paymentTypeId" value = "<?php echo $paymentTypeId ?>">
            <input type="text" id="paymentTypeText" readonly name="paymentTypeText" value = "<?php echo $paymentTypeText ?>">

        </div>
        <div class="span4 lightblue">
            <label>Payment For <span style="color: red;">*</span></label>
            <input type="hidden" id="paymentForId" name="paymentForId" value = "<?php echo $paymentForId ?>">
            <input type="text" id="paymentTypeText" readonly name="paymentTypeText" value = "<?php echo $paymentForText ?>">
        </div>
    </div>
	
	<!-- Sales PAYMENT -->
    <div class="row-fluid" id="salesPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFromSales" name="paymentFromSales" value="<?php echo $paymentFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?> /> 
            </div>    
                
            <div class="span3 lightblue">
                <label>Periode To <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentToSales" value="<?php echo $paymentTo; ?>" name="paymentToSales" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?> />
            </div>
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdSales" name="stockpileIdSales" value="<?php echo $stockpileIdSales ?>" />
                <input type="text" readonly id="stockpileIdSalesText" value="<?php echo $stockpileIdSalesText ?>" />
            </div>
        </div>
                
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="customerId" name="customerId" value="<?php echo $customerId ?>" />
                <input type="text" readonly id="customerText" value="<?php echo $customerText ?>" />
            </div>
            <div class="span3 lightblue">
                <label>Shipment No <span style="color: red;">*</span></label>
                <input type="text" readonly id="contractNo" name="contractNo" value="<?php echo $contractNo ?>" />
                
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="salesbankId" name="salesbankId" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="salesBankText " value="<?php echo $salesBankText  ?>" />
            </div>
        </div>
    </div>
	<!-- SALES DP -->
	    <div class="row-fluid" id="salesDownPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdCurDp" name="stockpileIdCurDp" value="<?php echo $stockpileIdCurahText ?>" />
                <input type="text" readonly id="stockpileIdCurahText" value="<?php echo $stockpileIdCurahText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="curahBankDp" name="curahBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="curahBankText " value="<?php echo $curahBankText  ?>" />
            </div>
            <div class="span3 lightblue">
                <!-- ini untuk get text dari pengajuan supplier -->
                <label>Vendor <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="vendorId" name="vendorId" value="<?php echo $vendorId ?>" />
                <input type="text" readonly id="vendorText" value="<?php echo $vendorText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="contractIds" name="contractIds" value="<?php echo $contractIds ?>" />
                <input type="text" readonly id="contractNames" value="<?php echo $contractNames ?>" />

            </div>
        </div>
    
    </div>

    <!-- CURAH PAYMENT -->
    <div class="row-fluid" id="curahPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFromCur" name="paymentFromCur" value="<?php echo $paymentFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?> /> 
            </div>    
                
            <div class="span3 lightblue">
                <label>Periode To <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentToCur" value="<?php echo $paymentTo; ?>" name="paymentToCur" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?> />
            </div>
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdCurDp" name="stockpileIdCurDp" value="<?php echo $stockpileIdCurahText ?>" />
                <input type="text" readonly id="stockpileIdCurahText" value="<?php echo $stockpileIdCurahText ?>" />
            </div>
        </div>
                
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="vendorId" name="vendorId" value="<?php echo $vendorId ?>" />
                <input type="text" readonly id="vendorText" value="<?php echo $vendorText ?>" />
            </div>
            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="contractIds" name="contractIds" value="<?php echo $contractIds ?>" />
                <textarea type="text" style="width:95%;" readonly id="contractNames"><?php echo $contractNames ?> </textarea>
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="curahBankDp" name="curahBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="curahBankText " value="<?php echo $curahBankText  ?>" />
            </div>
        </div>
    </div>

        <!-- CURAH DOWN PAYMENT / IN -->
    <div class="row-fluid" id="curahDownPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdCurDp" name="stockpileIdCurDp" value="<?php echo $stockpileIdCurahText ?>" />
                <input type="text" readonly id="stockpileIdCurahText" value="<?php echo $stockpileIdCurahText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="curahBankDp" name="curahBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="curahBankText " value="<?php echo $curahBankText  ?>" />
            </div>
            <div class="span3 lightblue">
                <!-- ini untuk get text dari pengajuan supplier -->
                <label>Vendor <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="vendorId" name="vendorId" value="<?php echo $vendorId ?>" />
                <input type="text" readonly id="vendorText" value="<?php echo $vendorText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="contractIds" name="contractIds" value="<?php echo $contractIds ?>" />
                <input type="text" readonly id="contractNames" value="<?php echo $contractNames ?>" />

            </div>
        </div>
    
    </div>

<!-- FREIGHT DOWN PAYMENT / IN -->
	<div class="row-fluid" id="freightDownPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                    <input type="hidden" readonly id="stockpileIdFcDp" name = "stockpileIdFcDp" value="<?php echo $stockpileIdFreightId ?>" />
                    <input type="text" readonly id="stockpileIdFreightText" value="<?php echo $stockpileIdFreightText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                    <input type="hidden" readonly id="freightIdFcDp" name = "freightIdFcDp" value="<?php echo $freightId ?>" />
                    <input type="text" readonly id="freightName" value="<?php echo $freightName ?>" />

            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>

                    <input type="hidden" readonly id="freightBankDp" name = "freightBankDp" value="<?php echo $bankvendorId ?>" />
                    <input type="text" readonly id="freightbankText" value="<?php echo $freightbankText ?>" />

            </div>
        </div>
            <br>

        <div class="row-fluid">
            <div class="span3 lightblue">
                <!-- ini untuk get text dari pengajuan supplier -->
                <label>Supplier <span style="color: red;">*</span></label> 
                <input type="hidden" readonly id="suplierId" name = "suplierId" value="<?php echo $supplierId ?>" />
                <input type="text" readonly id="supplierText" value="<?php echo $supplierText ?>" />

            </div>

            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                    <input type="hidden" readonly id="contractIds" name = "contractIds" value="<?php echo $contractIds ?>" />
                    <input type="text" readonly id="contractNames" value="<?php echo $contractNames ?>" />
   
            </div>
		</div>
    </div>
	
 <!-- FREIGHT PAYMENT / OUT--> 
 <div class="row-fluid" id="freightPayment1" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromFP" name="paymentFromFP" value = "<?php echo $paymentFrom; ?>"  data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?> >
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToFP" name="paymentToFP" value = "<?php echo $paymentTo; ?>" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?> >
            </div>

            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>

                    <input type="hidden" readonly id="stockpileIdFreight" value="<?php echo $stockpileIdFreightId ?>" />
                    <input type="text" readonly id="stockpileIdFreightText" value="<?php echo $stockpileIdFreightText ?>" />
               
            </div>
		</div>

		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                    <input type="hidden" readonly id="freightId_1" name = "freightId_1" value="<?php echo $freightId ?>" />
                    <input type="text" readonly id="freightName" value="<?php echo $freightName ?>" />

            </div>

            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                     <input type="hidden" readonly id="contractFreight" value="<?php echo $contractIds ?>" />
                     <textarea  type="text" style="width:95%;" readonly id="contractFreightText" ><?php echo $contractNames ?> </textarea>

            </div>

            <div class="span4 lightblue" >
                <label>Bank <span style="color: red;">*</span></label>
                       <input type="hidden" readonly id="freightBankId" name = "freightBankId" value="<?php echo $vendorBankId ?>" />
                       <input type="text" readonly id="freightbankText" value="<?php echo $freightbankText ?>" />
            </div>
		</div>
    </div>
<!-- END FREIGHT-1 -->


<!-- UNLOADING DOWN PAYMENT / IN -->
    <div class="row-fluid" id="unloadingDownPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdOBDp" name="stockpileIdOBDp" value="<?php echo $stockpileOBText ?>" />
                <input type="text" readonly id="stockpileOBText" value="<?php echo $stockpileOBText ?>" />

            </div>
            <div class="span3 lightblue">
                <!-- ini untuk get text dari pengajuan supplier -->
                <label>Labor <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="laborId" name="laborId" value="<?php echo $laborId ?>" />
                <input type="text" readonly id="laborText" value="<?php echo $laborText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="lBankDp" name="lBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="lbank " value="<?php echo $lbank  ?>" />

            </div>
        </div>
    </div>

      <!-- UNLOADING PAYMENT / OUT -->
      <div class="row-fluid" id="unloadingPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">* </span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP" value="<?php echo $paymentFrom; ?>" name="paymentFromUP" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">* </span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP" value="<?php echo $paymentTo ?>" name="paymentToUP" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdOBDp" name="stockpileIdOBDp" value="<?php echo $stockpileOBText ?>" />
                <input type="text" readonly id="stockpileOBText" value="<?php echo $stockpileOBText ?>" />
            </div>

            <div class="span3 lightblue">
                <label>Labor <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="laborId" name="laborId" value="<?php echo $laborId ?>" />
                <input type="text" readonly id="laborText" value="<?php echo $laborText ?>" />
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="lBankDp" name="lBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="lbank " value="<?php echo $lbank  ?>" />
            </div>
        </div>
    </div>


<!-- HANDLING PAYMENT -->
    <div class="row-fluid" id="handlingPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
            <label>Periode From<span style="color: red;">*</span> </label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromHP" name="paymentFromHP" value = "<?php echo $paymentFrom; ?>"  data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?>>
            </div>
            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">*</span> </label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToHP" name="paymentToHP" value = "<?php echo $paymentTo; ?>" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly ?>>
            </div>
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>

                    <input type="hidden" readonly id="stockpileIdHandlingId" value="<?php echo $stockpileIdHandlingId ?>" />
                    <input type="text" readonly id="stockpileIdHandlingText" value="<?php echo $stockpileIdHandlingText ?>" />
            </div>
		</div>

		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                    <input type="hidden" style = "width:100%;" readonly id="vendorHandlingId" name = "vendorHandlingId" value="<?php echo $vendorHandlingId ?>" />
                    <input type="text" readonly style = "width:100%;" readonly id="vendorHandlingText"  value="<?php echo $vendorHandlingText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                
                    <input type="hidden" style = "width:100%;" readonly id="contractHandlingId" name = "contractHandlingId" value="<?php echo $contractIds ?>" />
                    <textarea class="span12" rows="1" readonly tabindex="" id="contractHandlingText" name="contractHandlingText"><?php echo $contractNames; ?></textarea>
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <input type="hidden" readonly id="handlingBankId" name ="handlingBankId" value="<?php echo $vendorBankId ?>" />
                    <input type="text" readonly id="vhbank" value="<?php echo $vhbank ?>" />
            </div>
		</div>
    </div>
<!-- END HANDLING -->

    <!-- HANDLING DOWN PAYMENT -->
    <div class="row-fluid" id="handlingDownPayment" style="display: none;">
        
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="stockpileIdHandlingDp" name="stockpileIdHandlingDp" value="<?php echo $stockpileIdHandlingText ?>" />
                <input type="text" readonly id="stockpileIdHandlingText" value="<?php echo $stockpileIdHandlingText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="vhBankDp" name="vhBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="vhbank " value="<?php echo $vhbank  ?>" />

            </div>
        
            <div class="span3 lightblue">
                <!-- ini untuk get text dari pengajuan supplier -->
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="vendorHandlingId" name="vendorHandlingId" value="<?php echo $vendorHandlingId ?>" />
                <input type="text" readonly id="vendorHandlingText" value="<?php echo $vendorHandlingText ?>" />

            </div>
            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                <input type="hidden" readonly id="contractIds" name="contractIds" value="<?php echo $contractIds ?>" />
                <input type="text" readonly id="contractNames" value="<?php echo $contractNames ?>" />
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


    <!-- GET INVOICE -->
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Amount :<span style="color: red;">*</span></label>
            <input type="text"  readonly class="span12" tabindex="" id="amount" name="amount" value="<?php echo $amount?>">
            <input type="hidden"  readonly class="span12" tabindex="" id="dppamount" name="dppamount" value="<?php echo $dppamount?>">
            <input type="hidden"  readonly class="span12" tabindex="" id="totalprice" name="totalprice" value="<?php echo $totalAmount?>">
            <input type="hidden"  readonly class="span12" tabindex="" id="totalppn" name="totalppn" value="<?php echo $totalPPn?>">
            <input type="hidden"  readonly class="span12" tabindex="" id="totalpph" name="totalpph" value="<?php echo $totalPPh?>">

        </div>

        <div class="span3 lightblue">
            <label>Tax Invoice:</label>
            <input type="text" class="span12" readonly tabindex="" id="taxInvoice" name="taxInvoice" value="<?php echo $taxInvoice; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Invoice No:</label>
            <input type="text" class="span12" readonly tabindex="" id="invoiceNo" name="invoiceNo" value="<?php echo $invoiceNo; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Invoice Date :</label>
            <input type="text" placeholder="DD/MM/YYYY" readonly tabindex="" id="invoiceDate" name="invoiceDate" value = "<?php echo $invoiceDate ; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>

    <div class="row-fluid">
        <div class="span11 lightblue">
            <label>Remarks :</label>
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
            <input type="text" readonly class="span12" tabindex="" id="bank" name="bank" value="<?php echo $bankName; ?>">
        </div>

    </div>

    <div class="row-fluid">

        <div class="span1 lightblue">
            <label>No Rek :</label>
        </div>
        <div class="span5 lightblue">
       		<input type="text" readonly class="span12" tabindex="" id="rek" name="rek" value="<?php echo $rekNo; ?>">
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
            <button class="btn btn-primary" <?php echo $disableProperty; ?>  id="submitButton2">Approve</button>
            <button class="btn" type="button" onclick="back()">Back</button>
			
        </div>
    </div>
</form>
<button class="btn btn-warning" id="jurnalInvoiceSales">JP</button>
<hr>



<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
    </div>

</div>

<?php if(isset($invId) && $invId != '') { ?>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger" <?php echo $disableProperty1 ?>   id="retur" onclick= "canceled()" style="margin: 0px;">Return</button>
        </div>
    </div>  
<?php } else if($invId == '') { ?>
    <div class="row-fluid">
        <div class="span12 lightblue">
           <!-- <button class="btn btn-danger"  <?php //echo $disableProperty; ?> id="canceled" onclick= "canceled()" style="margin: 0px;">Cancel</button>-->
            <button class="btn btn-warning"  <?php echo $disableProperty; ?> id="reject1" onclick= "reject2()" style="margin: 0px;">Reject</button>
            <!-- <button class="btn btn-danger" onclick="cancelPG()">CANCEL</button> -->
        </div>
    </div>  
<?php } ?>

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
