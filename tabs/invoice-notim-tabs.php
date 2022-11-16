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
$invoiceId = '';
$whereJoin = '';
$generatedInvoiceNo = '';

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
}


if($_POST['invId'] != '') {
$invId = $_POST['invId'];
$when = '';

    $sqlInv = "SELECT inv_notim_id, inv_notim_no, status_payment, invoice_status, idPP FROM invoice_notim WHERE inv_notim_id = {$invId} and invoice_status != 2";
    $result = $myDatabase->query($sqlInv, MYSQLI_STORE_RESULT);
    if ($result == true && $result->num_rows > 0) {
        $rowInv = $result->fetch_object();
        $generatedInvoiceNo = $rowInv->inv_notim_no;
        $statusPayment = $rowInv->status_payment;
        $invoiceStatus = $rowInv->invoice_status;
        $idPP = $rowInv->idPP;
        $selectInv = " DATE_FORMAT(inv.due_date_inv, '%d/%m/%Y') AS duedate, inv.file1, inv.status_payment,";
        $whereJoin = "  LEFT JOIN invoice_notim inv1 ON inv.idpp = pp.idpp";
        $when = " WHEN inv1.status_payment = 2 OR inv1.invoice_status = 4 THEN inv.return_remarks";
    }
    //   echo $sqlInv;
}

    $sqlPP = "SELECT sp.stockpile_name as spNameLocation, ty.type_transaction_name AS paymentFor,
            CASE WHEN pp.payment_method  = 1 THEN 'Payment'
                WHEN pp.payment_method = 3 THEN 'Final Payment'
            ELSE 'Down Payment' END AS methodText,
            DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, 
            DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo,
            DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoiceDatePengajuan, 
            spVendor.stockpile_name,
            f.freight_supplier,
            lab.labor_name,
            vh.vendor_handling_name,
            v.vendor_name AS vendorName,
            CONCAT(vb.bank_name,' - ',vb.account_no) AS vbank,
            CONCAT(lbank.bank_name,' - ',lbank.account_no) AS lbank,
            CONCAT(fb.bank_name,' - ',fb.account_no) AS fbank,
            CONCAT(vhb.bank_name,' - ',vhb.account_no) AS vhbank, 
            pp.status as pStatus,
            CASE WHEN pp.status = 2 THEN pp.reject_remarks {$when} ELSE NULL END AS rejectRemarks,
            {$selectInv}
            pp.*, inv.inv_notim_id, inv.invoice_status as invStatus FROM pengajuan_payment pp 
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
            LEFT JOIN invoice_notim inv ON inv.idpp = pp.idpp
            {$whereJoin}
            WHERE pp.idPP = {$idPP}";
    $resultPP = $myDatabase->query($sqlPP, MYSQLI_STORE_RESULT);
//  echo $sqlPP;
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
        $invId = $row->inv_notim_id;
        $invoiceStatus = $row->invStatus;

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
        $pStatus = $row->pStatus;
        $status_payment = $row->status_payment;
        $reject_remarks = $row->rejectRemarks;

        //CURAH
        $stockpileIdCurahId = $row->stockpile_id;
        $stockpileIdCurahText = $row->stockpile_name;
        $vendorId = $row->vendor_id;
        $vendorText = $row->vendorName;
        $curahBankText = $row->vbank;
        

        //FREIGHT PAYMENT
        $stockpileIdFreightId = $row->stockpile_id;
        $stockpileIdFreightText = $row->stockpile_name;
        $freightId = $row->freight_id;
        $freightName = $row->freight_supplier;
        $freightbankText = $row->fbank;

        //Freight Dp
        $supplierId = $row->vendor_id;
        $supplierText = $row->vendorName;

        //HANDLING PAYMENT
        $stockpileIdHandlingId = $row->stockpile_id;
        $stockpileIdHandlingText = $row->stockpile_name;
        $vendorHandlingId = $row->vendor_handling_id;
        $vendorHandlingText = $row->vendor_handling_name;
        $vhbank = $row->vhbank;

        //UNLOADING
        $stockpileOBId = $row->stockpile_id;
        $stockpileOBText = $row->stockpile_name;
        $laborId = $row->labor_id;
        $laborText = $row->labor_name;
        $lbank = $row->lbank;

        //DOWN PAYMENT
        $qtyDp = number_format($row->total_qty, 2, ".", ",");
        $priceDp = number_format($row->price, 2, ".", ",");
        $terminDp = number_format($row->termin, 2, ".", ",");
        $originalAmountDp = number_format($row->total_dpp, 2, ".", ",");

        //INVOICE
        $duedate = $row->duedate;
        $invFile = $row->file1;

        if((($pStatus == 3 || $pStatus == 1) && ($invoiceStatus == 1 || $invoiceStatus == 4)  && ($statusPayment == 1 || $statusPayment == 2 || $statusPayment == 0))){
            $disableProperty1 = 'disabled';
        }

        if(($pStatus == 3 && $invoiceStatus == 1 && ($statusPayment == 1 || $statusPayment == 2) &&  ($amount > 0))){
            $disableProperty = 'disabled';
        }


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
    if($invId != '' && $invoiceStatus == 1){
        $method = 'UPDATE';
        $button = 'Update';
    }else{
        $method = 'INSERT';
        $button = 'Approve';
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
            <?php if($generatedInvoiceNo == '') { ?>
                getInvoiceNotim(<?php echo $idPP; ?>);
            <?php } ?>

            //CURAH  
            <?php if ($paymentForId == 1) { //GET DATA FROM ADMIN 
            ?>
                getVendorBank(1,<?php echo $vendorId ?>, <?php echo $paymentForId ?>, <?php echo $bankvendorId ?>);
                getVendorBankDetail(<?php echo $bankvendorId ?>, <?php echo $paymentForId ?>);

                <?php if ($paymentMethod == 2) { ?> //downpayment
                    $('#curahPayment').hide();
                    $('#curahDownPayment').show();
                    setSlipDp(<?php echo $idPP; ?>);
                <?php } else if ($paymentMethod != 2) { ?> //payment
                    $('#curahPayment').show();
                    $('#curahDownPayment').hide();
                    document.getElementById("paymentFromCur").disabled = true;
                    document.getElementById("paymentToCur").disabled = true;
                    <?php if($paymentMethod == 1){ ?>
                    setSlipCurah(<?php echo $stockpileIdCurahId ?>, <?php echo $vendorId ?>, '', '', 'NONE', 'NONE', '', '', <?php echo $idPP ?>);
                <?php } else { ?>
                    updatePengajuanPayment(<?php echo $idPP ?>, <?php echo $paymentForId ?>);
                    setSlipCurah_settle(<?php echo $stockpileIdCurahId ?>, <?php echo $vendorId?>, '','', '','','NONE','NONE', '', '', <?php echo $idPP ?>);
                <?php } 
                }?>
                <?php if ($tipeBayar == 1) { ?>
                    $('#tglBayarDiv').show();
                <?php } else { ?>
                    $('#tglBayarDiv').hide();
            <?php }
            } ?>

             //FREIGHT   
            <?php if($paymentForId == 2){ //GET DATA FROM ADMIN ?> 
                getVendorBank(1,<?php echo $freightId ?>, <?php echo $paymentForId ?>, <?php echo $bankvendorId ?>);
                getVendorBankDetail(<?php echo $bankvendorId ?>, <?php echo $paymentForId ?>);

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
                    updatePengajuanPayment(<?php echo $idPP ?>, <?php echo $paymentForId ?>);
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
        getVendorBank(1,<?php echo $vendorHandlingId ?>, <?php echo $paymentForId ?>, <?php echo $bankvendorId ?>);
        getVendorBankDetail(<?php echo $bankvendorId ?>, <?php echo $paymentForId ?>);

        <?php if ($paymentMethod == 2) { ?> //downpayment
            $('#handlingPayment').hide();
            $('#handlingDownPayment').show();
            setSlipDp(<?php echo $idPP; ?>);
        <?php } else if ($paymentMethod != 2) { ?> //payment
            $('#handlingPayment').show(); 
            $('#handlingDownPayment').hide();
            document.getElementById("paymentFromHP").disabled = true;
            document.getElementById("paymentToHP").disabled = true;
            <?php if($paymentMethod == 1){ ?>
                setSlipHandling(<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId?>, '','', 'NONE', 'NONE', '','',<?php echo $idPP ?>);
            <?php }else{ ?>
                updatePengajuanPayment(<?php echo $idPP ?>, <?php echo $paymentForId ?>);
                 setSlipHandling_settle(<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId?>, '','', '','','NONE','NONE', '', '', <?php echo $idPP ?>);

        <?php } 
        }?>
     
        <?php if ($tipeBayar == 1) { ?>
            $('#tglBayarDiv').show();
        <?php } else { ?>
            $('#tglBayarDiv').hide();
        <?php }
        } ?>

        //UNLOADING (OB)   
        <?php if ($paymentForId == 3) { //GET DATA FROM ADMIN 
        ?>
                getVendorBank(1,<?php echo $laborId ?>, <?php echo $paymentForId ?>, <?php echo $bankvendorId ?>);
                getVendorBankDetail(<?php echo $bankvendorId ?>, <?php echo $paymentForId ?>);

            <?php if ($paymentMethod == 2) { ?> //downpayment
                $('#unloadingPayment').hide();
                $('#unloadingDownPayment').show();
                setSlipDp(<?php echo $idPP; ?>);
            <?php } else if ($paymentMethod != 2) { ?> //payment
                $('#unloadingPayment').show();
                $('#unloadingDownPayment').hide();
                document.getElementById("paymentFromUP").disabled = true;
                document.getElementById("paymentToUP").disabled = true;
                <?php if($paymentMethod == 1){ ?>
                    setSlipUnloading(<?php echo $stockpileOBId; ?>, <?php echo $laborId; ?>, '', 'NONE', 'NONE', '', '', <?php echo $idPP; ?>);
                <?php }else{ ?>
                    updatePengajuanPayment(<?php echo $idPP ?>, <?php echo $paymentForId ?>);
                    setSlipUnloading_settle(<?php echo $stockpileOBId; ?>, <?php echo $laborId; ?>, '', '', '', 'NONE', 'NONE', '', '', <?php echo $idPP; ?>);
            <?php } 
            }?>
              
            <?php if ($tipeBayar == 1) { ?>
                $('#tglBayarDiv').show();
            <?php } else { ?>
                $('#tglBayarDiv').hide();
            <?php }
            } ?>

      //SUBMIT FORM
      $("#ApproveInvoiceForm").submit(function (e) {
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
                            $('#pageContent').load('views/invoice-notim-views.php', {idPP: returnVal[3], direct: 1}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
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

        
        $('#jurnalPayment').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnalInvNotim&invId=<?php echo $invId; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/invoice-notim-forms', {}, iAmACallbackFunction2);

                        }
                    }
                }
            });
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

        $('#curahBankId').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('curahBankId').value != '') {
				 getVendorBankDetail($('select[id="curahBankId"]').val(), $('input[id="paymentForId"]').val());
            }
        });
        $('#curahBankDp').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('curahBankDp').value != '') {
				 getVendorBankDetail($('select[id="curahBankDp"]').val(), $('input[id="paymentForId"]').val());
            }
        });

        $('#freightBankId_1').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('freightBankId_1').value != '') {
				 getVendorBankDetail($('select[id="freightBankId_1"]').val(), $('input[id="paymentForId"]').val());
            }
        });
        $('#freightBankDp').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('freightBankDp').value != '') {
				 getVendorBankDetail($('select[id="freightBankDp"]').val(), $('input[id="paymentForId"]').val());
            }
        });
        $('#laborBankId').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('laborBankId').value != '') {
				 getVendorBankDetail($('select[id="laborBankId"]').val(), $('input[id="paymentForId"]').val());
            }
        });
        $('#laborBankDp').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('laborBankDp').value != '') {
				 getVendorBankDetail($('select[id="laborBankDp"]').val(), $('input[id="paymentForId"]').val());
            }
        });
        $('#vendorHandlingBankId').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('vendorHandlingBankId').value != '') {
				 getVendorBankDetail($('select[id="vendorHandlingBankId"]').val(), $('input[id="paymentForId"]').val());
            }
        });
        $('#vendorHandlingBankDp').change(function() {
			resetVendorBankDetail ($('input[id="paymentForId"]').val());

            if(document.getElementById('vendorHandlingBankDp').value != '') {
				 getVendorBankDetail($('select[id="vendorHandlingBankDp"]').val(), $('input[id="paymentForId"]').val());
            }
        });
    });

    function resetVendorBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
        document.getElementById('rek').value = '';
        document.getElementById('swift').value = '';

    }

    function getVendorBankDetail(vendorBankId, paymentFor) {
        if(amount != '') {
            $.ajax({
                url: 'get-data-Ppayment.php',
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

    function setSlipHandling_settle(stockpileId, vendorHandlingId, settle, contractHandling, checkedSlips, checkedSlipsDP, ppn, pph, paymentFromHP, paymentToHP, idPP) {
		//alert(stockpileId +', '+ vendorHandlingId);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipHandling_settle',
                    stockpileId: stockpileId,
                    vendorHandlingId: vendorHandlingId,
                    settle : settle,
					contractHandling: contractHandling,
                    checkedSlips: checkedSlips,
					checkedSlipsDP: checkedSlipsDP,
                    ppn: ppn,
                    pph: pph,
					paymentFromHP: paymentFromHP,
					paymentToHP: paymentToHP,
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

    function setSlipUnloading_settle(stockpileId, laborId, settle, checkedSlips, checkedSlipsDP, ppn, pph, paymentFromUP, paymentToUP, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipUnloading_settle',
                    stockpileId: stockpileId,
                    laborId: laborId,
                    settle : settle,
                    checkedSlips: checkedSlips,
                    checkedSlipsDP: checkedSlipsDP,
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

                var return_val = data.split('|');
                    if(parseInt(return_val[0])!=0)	//if no errors
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
            startView: 0
        });
    });

    <?php if($generatedInvoiceNo == "") {?>
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
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'approve_invoice_notim',
                _method: 'REJECT',
                idPP: document.getElementById('idPP').value,
                invId: document.getElementById('inv2').value,
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
                        $('#pageContent').load('views/invoice-notim-views.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }
    
    function jurnalTest() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: 'action=jurnalInvNotim&invId=<?php echo $invId; ?>',
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
                        $('#pageContent').load('forms/invoice-notim-forms.php', {idPP: returnVal[3]}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }


    function returned() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'approve_invoice_notim',
                _method: 'RETURNED',
                invId: document.getElementById('inv2').value,
                reject_remarks: document.getElementById('reject_remarks').value,
                idPP: document.getElementById('idPP').value,
                method: document.getElementById('paymentMethod').value,
                paymentFor: document.getElementById('paymentForId').value

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
                        $('#pageContent').load('forms/invoice-notim-forms.php', {idPP: returnVal[3]}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
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

    function setSlipCurah_settle(stockpileId, vendorId, settle, contractCurah, checkedSlips, checkedSlipsDP, ppn, pph, paymentFromCur, paymentToCur, idPP) {
		//alert(stockpileId +', '+ vendorHandlingId);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipCurah_settle',
                    stockpileId: stockpileId,
                    vendorId: vendorId,
                    settle : settle,
					contractCurah: contractCurah,
                    checkedSlips: checkedSlips,
					checkedSlipsDP: checkedSlipsDP,
                    ppn: ppn,
                    pph: pph,
					paymentFromCur: paymentFromCur,
					paymentToCur: paymentToCur,
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

    function updatePengajuanPayment(idPP, paymentFor) {
            //alert(idPP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'updatePengajuanPayment',
                idPP: idPP,
                paymentFor: paymentFor
                },
            success: function(data){
            }
        });
    }

    //GET VENDOR BANK
    function getVendorBank(type, vendorId, paymentFor, vendorBankId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
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
                if(paymentFor == 1){
					if(returnValLength > 0) {
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('curahBankId').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('curahBankDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('curahBankId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('curahBankDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('curahBankId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('curahBankDp').options.add(x);
                        }
                    }
				}else if(paymentFor == 2){
					if(returnValLength > 0) {
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('freightBankId_1').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('freightBankDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('freightBankId_1').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('freightBankDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
						 document.getElementById('freightBankId_1').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('freightBankDp').options.add(x);
                        }
                    }
				}
                else if(paymentFor == 3){
					if(returnValLength > 0) {
                        if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('laborBankDp').options.length = 0;
                        }else{
                            document.getElementById('laborBankId').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('laborBankDp').options.add(x);
                        }else{
                            document.getElementById('laborBankId').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('laborBankDp').options.add(x);
                        }else{
                            document.getElementById('laborBankId').options.add(x);
                        }
                    }
				}
                else if(paymentFor == 9){
					if(returnValLength > 0) {
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingBankId').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('vendorHandlingBankDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingBankId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('vendorHandlingBankDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingBankId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorHandlingBankDp').options.add(x);
                        }
                        
                    }
				}
        
                    if(type == 1) {
                        if(paymentFor == 1){
                            if(document.getElementById('paymentMethod').value != 2){
                                $('#curahBankId').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#curahBankId').prop('selectedIndex',i);
                                        
                                        $("#curahBankId").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }else{
                                $('#curahBankDp').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#curahBankDp').prop('selectedIndex',i);
                                        
                                        $("#curahBankDp").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }
                        }else if(paymentFor == 2){
                            if(document.getElementById('paymentMethod').value != 2){
                                $('#freightBankId_1').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#freightBankId_1').prop('selectedIndex',i);
                                        
                                        $("#freightBankId_1").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }else{
                                $('#freightBankDp').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#freightBankDp').prop('selectedIndex',i);
                                        
                                        $("#freightBankDp").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }
                        }
                        else if(paymentFor == 9){
                            if(document.getElementById('paymentMethod').value != 2){
                                $('#vendorHandlingBankId').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#vendorHandlingBankId').prop('selectedIndex',i);
                                        
                                        $("#vendorHandlingBankId").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }else{
                                $('#vendorHandlingBankDp').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#vendorHandlingBankDp').prop('selectedIndex',i);
                                        
                                        $("#vendorHandlingBankDp").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }
                        }
                        else if(paymentFor == 3){
                            if(document.getElementById('paymentMethod').value != 2){
                                $('#laborBankId').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#laborBankId').prop('selectedIndex',i);
                                        
                                        $("#laborBankId").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }else{
                                $('#laborBankDp').find('option').each(function(i,e){
                                    if($(e).val() == vendorBankId){
                                        $('#laborBankDp').prop('selectedIndex',i);
                                        
                                        $("#laborBankDp").select2({
                                            width: "100%",
                                            placeholder: vendorBankId
                                        });
                                    }
                                });
                            }
                        }

                    }
                }
            }
        });
    }

</script>

<form method="post" id="ApproveInvoiceForm"  enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="approve_invoice_notim" />
    <input type="hidden" name="_method" value="<?php echo $method; ?>">
    <input type="hidden" id="idPP" name="idPP" value="<?php echo $idPP ?>" />
    <input type="hidden" id="invId" name="invId" value="<?php echo $invId ?>" />

    <div class="row-fluid">
        <?php if($invStatus == 2) { ?>
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
            <label>
                    Upload file :   <input type="file" placeholder="File Upload"  id="file2" name="file2">
                
            <?php if(isset($invId) && $invId != '') { ?>
                file invoice :<a href="<?php echo $invFile ?>" target="_blank"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
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
            <input type="hidden" id="paymentTypeId" name="paymentTypeId" value = "2">
            <input type="text" id="paymentTypeText" readonly name="paymentTypeText" value = "OUT/DEBIT">

        </div>
        <div class="span4 lightblue">
            <label>Payment For <span style="color: red;">*</span></label>
            <input type="hidden" id="paymentForId" name="paymentForId" value = "<?php echo $paymentForId ?>">
            <input type="text" id="paymentTypeText" readonly name="paymentTypeText" value = "<?php echo $paymentForText ?>">
        </div>
    </div>

    <!-- CURAH PAYMENT -->
    <div class="row-fluid" id="curahPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentFromCur" name="paymentFromCur" value="<?php echo $paymentFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" <? echo $readonly ?>> 
            </div>    
                
            <div class="span3 lightblue">
                <label>Periode To <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="" id="paymentToCur" value="<?php echo $paymentTo; ?>" name="paymentToCur" data-date-format="dd/mm/yyyy" class="datepicker" <? echo $readonly ?>>
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
                <!-- <input type="hidden" readonly id="curahBankDp" name="curahBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="curahBankText " value="<?php echo $curahBankText  ?>" /> -->
                <?php
                      createCombo("", "", "", "curahBankId", "v_bank_id", "bank_name",
                      "", 10, "select2combobox100", 2);
                ?>
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
                <!-- <input type="hidden" readonly id="curahBankDp" name="curahBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="curahBankText " value="<?php echo $curahBankText  ?>" /> -->
                <?php
                      createCombo("", "", "", "curahBankDp", "v_bank_id", "bank_name",
                      "", 10, "select2combobox100", 2);
                ?>
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

            <div class="span3 lightblue"> <!--FREIGHT DP BANK -->
                <label>Bank <span style="color: red;">*</span></label>

                    <!-- <input type="hidden" readonly id="freightBankDp" name = "freightBankDp" value="<?php echo $bankvendorId ?>" />
                    <input type="text" readonly id="freightbankText" value="<?php echo $freightbankText ?>" /> -->
                    <?php
                            createCombo("", "", "", "freightBankDp", "f_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                        ?>


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

            <div class="span4 lightblue" > <!--FREIGHT BANK -->
                <label>Bank <span style="color: red;">*</span></label>
                       <!-- <input type="text" readonly id="freightBankId" name = "freightBankId" value="<?php echo $bankvendorId ?>" />
                       <input type="text" readonly id="freightbankText" value="<?php echo $freightbankText ?>" /> -->
                       <?php
                            createCombo("", "", "", "freightBankId_1", "f_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                        ?>
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
                <!-- <input type="hidden" readonly id="lBankDp" name="lBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="lbank " value="<?php echo $lbank  ?>" /> -->
                <?php
                    createCombo("", "", "", "laborBankDp", "l_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                ?>
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
                <!-- <input type="hidden" readonly id="lBankDp" name="lBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="lbank " value="<?php echo $lbank  ?>" /> -->
                <?php
                    createCombo("", "", "", "laborBankId", "l_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                ?>
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
                    <!-- <input type="hidden" readonly id="handlingBankId" name ="handlingBankId" value="<?php echo $vendorBankId ?>" />
                    <input type="text" readonly id="vhbank" value="<?php echo $vhbank ?>" /> -->
                    <?php
                        createCombo("", "", "", "vendorHandlingBankId", "vh_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                   ?>
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
                <!-- <input type="hidden" readonly id="vhBankDp" name="vhBankDp" value="<?php echo $bankvendorId ?>" />
                <input type="text" readonly id="vhbank " value="<?php echo $vhbank  ?>" /> -->
                <?php
                        createCombo("", "", "", "vendorHandlingBankDp", "vh_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                   ?>
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
            <input type="text" class="span12"  tabindex="" id="taxInvoice" name="taxInvoice" value="<?php echo $taxInvoice; ?>">
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
            <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary">
        </div>
        <div class="span1 lightblue">
            <label>Bank :</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="bank" name="bank">
        </div>

    </div>

    <div class="row-fluid">

        <div class="span1 lightblue">
            <label>No Rek :</label>
        </div>
        <div class="span5 lightblue">
       		<input type="text" readonly class="span12" tabindex="" id="rek" name="rek">
        </div>
        <div class="span1 lightblue">
            <label>Swift Code:</label>
        </div>
        <div class="span5 lightblue">
        	<input type="text" readonly class="span12" tabindex="" id="swift" name="swift">
        </div>

    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty1; ?>  id="submitButton2"><?php echo $button ?></button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<hr>



<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject/Cancel/Retur Remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
        <input type="hidden"  class="span12" tabindex="" id="inv2" name="inv2" value="<?php echo $invId; ?>">
    </div>

</div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <!-- <button class="btn btn-danger"  id="canceled" onclick= "canceled()" style="margin: 0px;">Cancel</button> -->
            <?php if($invoiceStatus == 1 || $invoiceStatus == 4){ ?>
                <button class="btn btn-danger"  <?php echo $disableProperty; ?> id="returned" onclick= "returned()" style="margin: 0px;">Returned</button>
            <?php } ?>
            <?php if($pStatus == 0 || ($pStatus == 3 && $invoiceStatus == 2) || ($pStatus == 1 && $invoiceStatus == 2)){ ?>
                <button class="btn btn-danger"  <?php echo $disableProperty; ?> id="reject1" onclick= "reject2()" style="margin: 0px;">Reject</button>
            <?php } ?>
            <?php if($_SESSION['userId'] == 200) { ?>

            <button class="btn btn-warning" id="jurnalInvNotim" onclick="jurnalTest()">J-INV</button>
            <?php }?> 
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
