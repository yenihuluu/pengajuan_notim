<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Payments';

$date = new DateTime();
$paymentDate = $date->format('d/m/Y');
$disableProperty = '';
$allowAccount = true;
$allowBank = true;
$allowGeneralVendor = true;
$idPP = '';
$paymentMethodText = '';
$stockpileId = '';
$paymentTypeText = '';
$paymentFor = '';
$vendorid = '';
$stockpileContractId = '';
$stockpileId1 = '';

$paymentFrom1 = '';
$paymentTo1 = '';
$vendorHandling = '';
$freightId = '';
$vendorFreightId = '';

$currencyId = '';
$journalCurrencyId='';
$bankCurrencyId = '';
$bankId = '';
$exchangeRate = '';
$amount = '';
$taxInvoice = '';
$invoiceNo = '';
$invoiceDate = '';
$chequeNo = '';
$remarks = '';
$remarks2 = '';
$beneficiary = '';
$bank = '';
$rek = '';
$swift = '';
$transaksiID = '';
$vendorBankID = '';
$vendorFreightIds = '';
$payment_type = 1;
$inv_notim_id = '';
$lobookId = '';
$paymentFor_1 = '';


$laborId = '';

$whereproperty = '';

 $logbookId = $_POST['logbookId'];



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

if(isset($_SESSION['payment'])) {
    $paymentMethodText = $_SESSION['payment']['paymentMethodText'];
	
	
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

$sqlLogbook = "select l.ppayment_id, l.type_pengajuan from logbook_new l 
				where l.logbook_id = {$logbookId}";
          //  echo $sqlLogbook;
$result = $myDatabase->query($sqlLogbook, MYSQLI_STORE_RESULT);
    if ($result !== false && $result->num_rows == 1) {
		$row1 = $result->fetch_object();
		$inv_notim_id = $row1->ppayment_id;
		//$invGeneralId = $row1->inv_general_id;
		$typePengajuan = $row1->type_pengajuan;
        $logbookStatus = $row1->status1;
     
      
	}
//echo $sqlLogbook;

if($typePengajuan == 1){
$sql = "SELECT i.inv_notim_id,pp.vendor_bank_id, pp.`payment_method` AS paymentMethod, pp.payment_type, 
            CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE 'Payment' END AS payment_method2,
            CASE WHEN pp.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS payment_type2,
            CASE WHEN pp.payment_for = 1 THEN 'Invoice Sales' 
                WHEN pp.payment_for = 2 THEN 'Invoice OA'
                WHEN pp.payment_for = 3 THEN 'Invoice OB'
                WHEN pp.payment_for = 9 THEN 'Invoice Handling'
                WHEN pp.payment_for = 8 THEN 'Invoice'
            ELSE NULL END AS paymentFor_name,
            pp.payment_for AS paymentFor, pp.total_ppn_amount, pp.total_pph_amount,
            pp.stockpile_id AS stockpileId, s.stockpile_name AS stockpileName, pp.remarks,  pp.idPP AS idPP1, pp.total_qty, pp.price, pp.grand_total AS amount, pp.termin,
            f.freight_supplier, i.freightId,
            lab.labor_name, i.laborId,
            i.vendorHandlingId, vh.vendor_handling_name,
            i.vendor_id,  cu.customer_name AS vendor_name, i.customer_id,
            i.inv_notim_no AS invNo,
            i.inv_notim_id AS invId,
            i.status_payment,
            STR_TO_DATE(pay.payment_date, '%d/%m/%Y %H:%i:%s') AS paymentDate1
            FROM invoice_sales_oa i
            LEFT JOIN pengajuan_payment_sales_oa pp ON pp.`idPP` = i.idPP
            LEFT JOIN stockpile s ON s.stockpile_id = pp.stockpile_id
            LEFT JOIN freight_local_sales f ON f.freight_id = i.freightId
            LEFT JOIN labor lab ON lab.labor_id = pp.labor_id
            LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = i.vendorHandlingId
            LEFT JOIN vendor v ON v.vendor_id = i.vendor_id
            LEFT JOIN payment pay ON pay.payment_id = i.payment_id
			LEFT JOIN customer cu ON cu.customer_id = i.customer_id
			WHERE i.idPP = {$inv_notim_id}";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   // echo $sql;
}else if($typePengajuan == 2){
	$sql = "SELECT inv.invoice_id as invId, 8 AS paymentFor, 'Invoice' as paymentFor_name, 2 as vendorType, inv.invoice_method AS paymentMethod, inv.`stockpileId` AS stockpileId, 
			gv.general_vendor_name AS gv_text, gv.`general_vendor_id` as gvId,
			(SELECT bank_name FROM general_vendor_bank WHERE gv_bank_id = invd.gv_bank_id LIMIT 1) AS bankName,
			invd.`gv_bank_id` AS gvBankId, inv.invoice_no as invNo, inv.invoice_id as invId, inv.remarks 
			FROM invoice inv
			LEFT JOIN invoice_detail invd ON invd.`invoice_id` = inv.`invoice_id`
			LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = invd.`general_vendor_id`
			WHERE inv.invoice_id = {$invGeneralId} LIMIT 1";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	//echo $sql;
}	
    if ($result !== false && $result->num_rows > 0) {
		$row= $result->fetch_object();
		$paymentMethod_ = $row->paymentMethod;
		$paymentFor_ = $row->paymentFor;
		$paymentForText = $row->paymentFor_name;
		$stockpileId_ = $row->stockpileId;
		$stockpileName = $row->stockpileName;
		$invoiceFreightId_ = $row->freightId;
		$invoiceFreightText = $row->freight_supplier;
		$invoiceId_ = $row->invId;
		$invoiceText = $row->invNo;
		$invoiceLaborId_ = $row->laborId;
		$invoiceLaborText = $row->labor_name;
		$invoiceVendorHandlingId_ = $row->vendorHandlingId;
		$invoiceVendorHandlingText = $row->vendor_handling_name;
		$invoiceVendorSalesId_ = $row->customer_id;
		$invoiceVendorSalesText = $row->vendor_name;
		$gv_text = $row->gv_text;
		$gvId = $row->gvId;
		$gvBankId = $row->gvBankId;
		$gvBank_text = $row->bankName;
		$vendorType = $row->vendorType;
		$remarks = $row->remarks;
        $idPP = $row->idPP1;
        if($row->status_payment == 2){
            $disableProperty = "disabled";
        }
	}

//echo $sql;
// </editor-fold>
?>

<!-- ID DIPAKAI DI KEPERLUAN JAVASCRIPT -->
<input type="hidden" id="paymentFor1" value="<?php echo $paymentFor_ ?>" />
<input type="hidden" id="invoiceId1" value="<?php echo $invoiceId_ ?>" />


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
        $('#exchangeRate').number(true, 2);

       /* $('#insertModal').on('hidden', function () {
            // do something…
            if (document.getElementById('accountId').value == 'INSERT') {
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
            } else if (document.getElementById('bankId').value == 'INSERT') {
                setBank(0, 0);
            }
        });*/
		
		if(document.getElementById('paymentFor1').value != '') {
			setAccount($('input[id="paymentFor1"]').val());
			if(document.getElementById('paymentFor1').value == 2) { //invoice OA
				$('#divInvoice').hide();
				$('#invoiceOA').show();
				document.getElementById('currencyId').value = 1;
						
				document.getElementById("remarks").readOnly = true;
				refreshInvoiceOAPayment(<?php echo $inv_notim_id; ?>);
				refreshInvoiceOA($('input[id="invoiceId1"]').val(), $('input[id="paymentMethodText"]').val(), 'NONE', 'NONE');
				

            }/*else if(document.getElementById('paymentFor1').value == 3) { //invoice OB
				$('#divInvoice').hide();
				$('#invoiceOB').show();
				document.getElementById('currencyId').value = 1;
						
				document.getElementById("remarks").readOnly = true;
				refreshInvoiceOB($('input[id="invoiceId1"]').val(), $('input[id="paymentMethodText"]').val(), 'NONE', 'NONE');
				refreshInvoiceOBPayment($('input[id="invoiceId1"]').val());

            } else if(document.getElementById('paymentFor1').value == 9) { //invoice Handling
				$('#divInvoice').hide();
				$('#invoiceHandling').show();
				$('#divAmount').hide();
				document.getElementById('currencyId').value = 1;
						
				document.getElementById("remarks").readOnly = true;
				refreshInvoiceHandling($('input[id="invoiceHandlingId"]').val(), $('input[id="paymentMethodText"]').val(), 'NONE', 'NONE');
				refreshInvoiceHandlingPayment($('input[id="invoiceHandlingId"]').val());

            }else if(document.getElementById('paymentFor1').value == 8) { //invoice Sales
				$('#divInvoice').hide();
				$('#invoicePayment').show();
				document.getElementById('currencyId').value = 1;
				document.getElementById("remarks").readOnly = false;
				getVendorBank(1,$('input[id="gvId"]').val(), $('input[id="paymentFor1"]').val(),$('input[id="vendorType"]').val());
				getVendorBankDetail($('input[id="gvBankId"]').val(), $('input[id="paymentFor1"]').val());

				refreshInvoice($('input[id="invoiceId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
				refreshInvoicePayment($('input[id="invoiceId"]').val());

            }if(document.getElementById('paymentFor1').value == 1) { //invoice Sales
				$('#divInvoice').hide();
				$('#invoiceSales').show();
				document.getElementById('currencyId').value = 1;
				document.getElementById("remarks").readOnly = true;
				refreshInvoiceSales($('input[id="invoiceId1"]').val(), $('input[id="paymentMethodText"]').val(), 'NONE', 'NONE');
				refreshInvoiceSalesPayment($('input[id="invoiceId1"]').val());
            }*/ 
			                   
		}
		
		document.getElementById("remarks").readOnly = true;
		
		 $('#paymentFor').change(function() {
            
			/*resetInvoice('invoiceId', 'General Vendor');
			
			resetInvoiceOB('invId', 'labor');
			resetInvoiceHandling('invId', 'vendor handling');
			resetRefreshInvoiceHandlingPayment();
			resetRefreshInvoicePayment();
			
			resetRefreshInvoiceOBPayment();
			resetInvoiceSales('invId', 'Vendor');
			resetRefreshInvoiceSalesPayment();*/
			resetInvoiceOA('invId', 'freight');
			resetRefreshInvoiceOAPayment();
            
            /*$('#slipPayment').hide();
            $('#summaryPayment').hide();
			$('#invoicePayment').hide();
			
			$('#invoiceOB').hide();
			$('#invoiceHandling').hide();
			$('#invoiceSales').hide();*/
			$('#invoiceOA').hide();
			
            
            if(document.getElementById('paymentFor').value != '') {
                //resetAccount(' ');
                setAccount($('select[id="paymentFor"]').val());
                
				/*if(document.getElementById('paymentFor').value == 8) {
                        //$('#divPaymentTo').show();
                        //$('#divTax').show();
						$('#divInvoice').hide();
						$('#invoicePayment').show();
						//setstockpileLocationText();
						document.getElementById('currencyId').value = 1;
						
						//document.getElementById("chequeNo").readOnly = false;
						document.getElementById("remarks").readOnly = false;
						//document.getElementById("remarks2").readOnly = false;
						
                    } else */if(document.getElementById('paymentFor').value == 2) { //invoice OA
						$('#divInvoice').hide();
						$('#invoiceOA').show();
						document.getElementById('currencyId').value = 1;
						
						document.getElementById("remarks").readOnly = true;
                    } /*else if(document.getElementById('paymentFor').value == 3) { //invoice OB
						$('#divInvoice').hide();
						$('#invoiceOB').show();
						document.getElementById('currencyId').value = 1;
						
						document.getElementById("remarks").readOnly = true;
                    } else if(document.getElementById('paymentFor').value == 9) { //invoice Handling
						$('#divInvoice').hide();
						$('#invoiceHandling').show();
						$('#divAmount').hide();
						document.getElementById('currencyId').value = 1;
						
						document.getElementById("remarks").readOnly = true;
                    } else if(document.getElementById('paymentFor').value == 1) { //invoice Sales
						$('#divInvoice').hide();
						$('#invoiceSales').show();
						document.getElementById('currencyId').value = 1;
						document.getElementById("remarks").readOnly = true;
                    }*/
                 
            } else {
                resetAccount(' Payment For ');
            }
        });
		
		/*$('#vendorType').change(function() {
            resetVendorInvoice(' Invoice ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('vendorType').value != '') {
                resetVendorInvoice(' Invoice ');
                setVendorInvoice(0,$('select[id="paymentFor"]').val(), $('select[id="vendorType"]').val(),  0);
            }
        });
		
		$('#gvId').change(function() {
            resetInvoice(' Invoice ');
            resetVendorBank ($('select[id="paymentFor"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('gvId').value != '') {
                resetInvoice(' Invoice ');
                setInvoice(0,$('select[id="paymentFor"]').val(), $('select[id="vendorType"]').val(), $('select[id="gvId"]').val(),  0);
				//getBankDetail($('select[id="gvId"]').val(), $('select[id="paymentFor"]').val());
				getVendorBank(0,$('select[id="gvId"]').val(), $('select[id="paymentFor"]').val(),$('select[id="vendorType"]').val());

                //resetContract(' Vendor ');
            }
        });
		
		$('#gvBankId').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());

            if(document.getElementById('gvBankId').value != '') {
				 getVendorBankDetail($('select[id="gvBankId"]').val(), $('select[id="paymentFor"]').val());
            } 
        });

		 $('#invoiceId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetRefreshInvoicePayment(' ');

            if(document.getElementById('invoiceId').value != '') {
                    refreshInvoice($('select[id="invoiceId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
					refreshInvoicePayment($('select[id="invoiceId"]').val());
            }
        });*/
		
		//INVOICE OA
		
		$('#stockpileOAId').change(function() {
            resetSupplier('InvoiceFreightId');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('stockpileOAId').value != '') {
                resetSupplier('InvoiceFreightId');
                setSupplier(0, $('select[id="stockpileOAId"]').val(), 0);
            }
        });
		
		
		$('#invoiceFreightId').change(function() {
            resetInvoiceOA(' InvoiceOA ');
            //resetVendorBank ($('select[id="paymentFor"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('invoiceFreightId').value != '') {
                resetInvoiceOA(' InvoiceOA ');
                setInvoiceOA(0,$('select[id="paymentFor"]').val(), $('select[id="invoiceFreightId"]').val(),  0);
				
            }
        });

		 $('#invoiceOAId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetRefreshInvoiceOAPayment(' ');

            if(document.getElementById('invoiceOAId').value != '') {
                    refreshInvoiceOA($('select[id="invoiceOAId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
					refreshInvoiceOAPayment($('select[id="invoiceOAId"]').val());
					
            }
        });
		/*
		//INVOICE OB
		
		$('#stockpileOBId').change(function() {
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileOBId').value != '') {
                resetLabor(' ');
                setLabor(0, $('select[id="stockpileOBId"]').val(), 0);
            }
        });
		
		$('#invoiceLaborId').change(function() {
            resetInvoiceOB(' InvoiceOB ');
            //resetVendorBank ($('select[id="paymentFor"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetRefreshInvoiceOBPayment(' ');

            if(document.getElementById('invoiceLaborId').value != '') {
                resetInvoiceOB(' InvoiceOB ');
                setInvoiceOB(0,$('select[id="paymentFor"]').val(), $('select[id="invoiceLaborId"]').val(),  0);
				
            }
        });

		

		 $('#invoiceOBId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('invoiceOBId').value != '') {
                    refreshInvoiceOB($('select[id="invoiceOBId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
					refreshInvoiceOBPayment($('select[id="invoiceOBId"]').val());
					
					 
					//getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });
		
		//INVOICE Handling
		
		$('#stockpileHandlingId').change(function() {
			//alert('test');
            resetVendorHandling(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileHandlingId').value != '') {
				
                resetVendorHandling(' ');
                setVendorHandling(0, $('select[id="stockpileHandlingId"]').val(), 0);
            }
        });
		
		
		$('#invoiceVendorHandlingId').change(function() {
            resetInvoiceHandling(' InvoiceHandling ');
            //resetVendorBank ($('select[id="paymentFor"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetRefreshInvoiceHandlingPayment(' ');

            if(document.getElementById('invoiceVendorHandlingId').value != '') {
                resetInvoiceHandling(' InvoiceHandling ');
                setInvoiceHandling(0, $('select[id="paymentFor"]').val(), $('select[id="invoiceVendorHandlingId"]').val(), 0);
				//refreshInvoiceHandlingPayment($('select[id="invoiceVendorHandlingId"]').val());
				
            }
        });

		

		 $('#invoiceHandlingId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('invoiceHandlingId').value != '') {
                    //refreshInvoiceHandlingPayment($('select[id="invoiceHandlingId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
					refreshInvoiceHandling($('select[id="invoiceHandlingId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
					refreshInvoiceHandlingPayment($('select[id="invoiceHandlingId"]').val());
					//getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });
		
		//INVOICE Sales
		
		$('#stockpileSalesId').change(function() {
            resetVendor('invoiceVendorSalesId', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('stockpileSalesId').value != '') {
                resetVendor('invoiceVendorSalesId', ' ');
                setVendor(0, 'invoiceVendorSalesId', $('select[id="stockpileSalesId"]').val(), 'C', 0);
            }
        });
		
		$('#invoiceVendorSalesId').change(function() {
            resetInvoiceSales(' InvoiceSales ');
            //resetVendorBank ($('select[id="paymentFor"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetRefreshInvoiceSalesPayment(' ');

            if(document.getElementById('invoiceVendorSalesId').value != '') {
                resetInvoiceSales(' InvoiceSales ');
                setInvoiceSales(0,$('select[id="paymentFor"]').val(), $('select[id="invoiceVendorSalesId"]').val(), 0);
				
				
            }
        });

		 $('#invoiceSalesId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('invoiceSalesId').value != '') {
                    refreshInvoiceSales($('select[id="invoiceSalesId"]').val(), $('select[id="paymentMethodText"]').val(), 'NONE', 'NONE');
					refreshInvoiceSalesPayment($('select[id="invoiceSalesId"]').val());
					//getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });*/

        $('#bankId').change(function () {
            //resetExchangeRate();

            if (document.getElementById('bankId').value != '' && document.getElementById('bankId').value != 'INSERT') {
                if (document.getElementById('currencyId').value != '' && document.getElementById('currencyId').value != 0) {
                    setExchangeRate($('select[id="bankId"]').val(), $('input[id="currencyId"]').val(), $('input[id="journalCurrencyId"]').val());
                } else {
                   /* if (document.getElementById('paymentFor').value == 7 && document.getElementById('paymentType').value == 2) {
                        if (document.getElementById('accountId').value != '') {
                            setCurrencyId($('select[id="paymentType"]').val(), $('select[id="accountId"]').val(), 0);
                        }
                    } else if (document.getElementById('paymentFor').value == 7 && document.getElementById('paymentType').value == 1) {
                        if (document.getElementById('accountId').value != '') {
                            setCurrencyId($('select[id="paymentType"]').val(), $('select[id="accountId"]').val(), 0);
                        }
                    } else*/ if (document.getElementById('paymentFor').value == 1 && document.getElementById('paymentType').value == 1) {
                        if (document.getElementById('salesId').value != '') {
                            setCurrencyId($('select[id="paymentType"]').val(), 0, $('select[id="salesId"]').val());
                        }
                    }
                }
            } else if (document.getElementById('bankId').value != '' && document.getElementById('bankId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/payment-bank.php', {});
            }
            document.getElementById('exchangeRate').value = 1;
        });


              //SUBMIT FORM
      $("#paymentDataForm").submit(function (e) {
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
                            $('#pageContent').load('forms/search-payment-salesOA.php', {paymentId: returnVal[3], direct: 1}, iAmACallbackFunction);
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

        
	});	

    $("#cancelForm").submit(function (e) {
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
                            $('#pageContent').load('contents/pengajuan-payment-contents.php', {paymentId: returnVal[3], direct: 1}, iAmACallbackFunction);
                        }
                        $('#submitButton').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

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
            data: {
                action: 'setExchangeRate',
                bankId: bankId,
                currencyId: currencyId,
                journalCurrencyId: journalCurrencyId
            },
            success: function (data) {
                var returnVal = data.split('|');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    document.getElementById('bankCurrencyId').value = returnVal[1];
                    if (returnVal[1] == 1 && returnVal[1] == returnVal[2] && returnVal[1] == returnVal[3]) {
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

    function setCurrencyId(paymentType, accountId, salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setCurrencyId',
                paymentType: paymentType,
                accountId: accountId,
                salesId: salesId
            },
            success: function (data) {
//                alert(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    document.getElementById('currencyId').value = returnVal[1];
                    if (document.getElementById('bankId').value != '') {
                        setExchangeRate($('select[id="bankId"]').val(), returnVal[1], $('input[id="journalCurrencyId"]').val());
                    }
                }
            }
        });
    }

	/*function resetInvoice(text) {
        document.getElementById('invoiceId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceId').options.add(x);
    }

    function setInvoice(type, paymentFor, vendorType, gvId, invoiceId) {
		//alert ('test');
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoice',
                    gvId: gvId,
					paymentFor: paymentFor,
					vendorType: vendorType
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
                        document.getElementById('invoiceId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceId').value = invoiceId;

                    }
                }
            }
        });
    }*/
	
	function resetInvoiceOA(text) {
        document.getElementById('invoiceOAId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceOAId').options.add(x);
    }

    function setInvoiceOA(type, paymentFor, invoiceFreightId, invoiceOAId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoiceOA',
                    invoiceFreightId: invoiceFreightId,
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
                    if(returnValLength > 0) {
                        document.getElementById('invoiceOAId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceOAId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceOAId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceOAId').value = invoiceOAId;
                    }
                }
            }
        });
    }
	/*
	function resetInvoiceOB(text) {
        document.getElementById('invoiceOBId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceOBId').options.add(x);
    }

    function setInvoiceOB(type, paymentFor, invoiceLaborId, invoiceOBId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoiceOB',
                    invoiceLaborId: invoiceLaborId,
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
                    if(returnValLength > 0) {
                        document.getElementById('invoiceOBId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceOBId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceOBId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceOBId').value = invoiceOBId;
                    }
                }
            }
        });
    }
	
	function resetInvoiceHandling(text) {
        document.getElementById('invoiceHandlingId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceHandlingId').options.add(x);
    }

    function setInvoiceHandling(type, paymentFor, invoiceVendorHandlingId, invoiceHandlingId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoiceHandling',
                    invoiceVendorHandlingId: invoiceVendorHandlingId,
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
                    if(returnValLength > 0) {
                        document.getElementById('invoiceHandlingId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceHandlingId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceHandlingId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceHandlingId').value = invoiceHandlingId;
                    }
                }
            }
        });
    }
	
	function resetInvoiceSales(text) {
        document.getElementById('invoiceSalesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceSalesId').options.add(x);
    }

    function setInvoiceSales(type, paymentFor, invoiceVendorSalesId, invoiceSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoiceSales',
                    invoiceVendorSalesId: invoiceVendorSalesId,
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
                    if(returnValLength > 0) {
                        document.getElementById('invoiceSalesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceSalesId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceSalesId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceSalesId').value = invoiceSalesId;
                    }
                }
            }
        });
    }*/
   
	
	function setAccount(paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountPaymentSales',
                    paymentFor: paymentFor
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');
                    
                    
					document.getElementById('accountId').value = returnVal[0];
					document.getElementById('accountName').value = returnVal[1];
					
                }
            }
        });
    }
	
	/*function resetVendorInvoice(text) {
        document.getElementById('gvId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('gvId').options.add(x);
    }

    function setVendorInvoice(type, paymentFor, vendorType) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorInvoice',
                    vendorType: vendorType,
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
                    if(returnValLength > 0) {
                        document.getElementById('gvId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('gvId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('gvId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('gvId').value = gvId;
                    }
                }
            }
        });
    }*/
	

	
	function resetVendorBank(paymentFor) {
		//alert (paymentFor);
		 if(paymentFor == 1){
			document.getElementById('SalesBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('SalesBankId').options.add(x);
		}/*else if(paymentFor == 2){
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
		}*/
    }

    function getVendorBank(type, vendorId, paymentFor, vendorType, bankId) { //
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor,
					vendorType: vendorType
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
				 /*if(paymentFor == 1){
					if(returnValLength > 0) {
                        document.getElementById('SalesBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('SalesBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('SalesBankId').options.add(x);
                    }
				}else*/ if(paymentFor == 2){
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
				}/*else if(paymentFor == 3){
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
				}*/
                    
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

        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getVendorBankOASales',
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
       // }
	}


	function resetBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
		document.getElementById('rek').value = '';
		document.getElementById('swift').value = '';
    }

    function getBankDetail(bankVendor, paymentFor) {

        if(amount != '') {
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
	}
	
	/*function refreshInvoice(invoiceId, paymentMethodText, ppn1, pph1) {
        var ppnValue = 'NONE';
        var pphValue = 'NONE';

         if(paymentMethodText == 1) {
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

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshInvoice',
                    invoiceId: invoiceId,
                    paymentMethodText: paymentMethodText,
                    ppn1: ppnValue,
                    pph1: pphValue
            },
            success: function(data){
				//alert(data);
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
                }
            }
        });
    }*/
	
	function refreshInvoiceOA(invoiceOAId, paymentMethodText) {
       

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshInvoiceOA',
                    invoiceOAId: invoiceOAId,
                    paymentMethodText: paymentMethodText
				},
            success: function(data){
				//alert(data);
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
					getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('input[id="paymentFor"]').val());
                }
            }
			
        });
    }
	
	/*function refreshInvoiceOB(invoiceOBId, paymentMethodText) {
       

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshInvoiceOB',
                    invoiceOBId: invoiceOBId,
                    paymentMethodText: paymentMethodText
            },
            success: function(data){
				//alert(data);
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
					getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('input[id="paymentFor"]').val());
                }
            }
        });
    }
	
	function refreshInvoiceHandling(invoiceHandlingId, paymentMethodText) {
       

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshInvoiceHandling',
                    invoiceHandlingId: invoiceHandlingId,
                    paymentMethodText: paymentMethodText
            },
            success: function(data){
				//alert(data);
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
					getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('input[id="paymentFor"]').val());
                }
            }
        });
    }
	
	function refreshInvoiceSales(invoiceSalesId, paymentMethodText) {
        

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshInvoiceSales',
                    invoiceSalesId: invoiceSalesId,
                    paymentMethodText: paymentMethodText
            },
            success: function(data){
				//alert(data);
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
					getVendorBankDetail($('input[id="invoiceBankId"]').val(), $('input[id="paymentFor"]').val());
                }
            }
        });
    }*/
	
	/*function resetRefreshInvoicePayment() {
        document.getElementById('paymentMethodText').value = '';
		document.getElementById('paymentMethod').value = '';
		document.getElementById('paymentTypeText').value = '';
		document.getElementById('paymentType').value = '';
        document.getElementById('stockpileLocationText').value = '';
		document.getElementById('stockpileLocation').value = '';
		document.getElementById('paymentLocationText').value = '';
		document.getElementById('paymentLocation').value = '';
		document.getElementById('bankId').value = '';
		document.getElementById('bankCurrencyId').value = '';
		document.getElementById('exchangeRate').value = '';
    }

    function refreshInvoicePayment(invoiceId) {

        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'refreshInvoicePayment',
                        invoiceId: invoiceId
                },
                success: function(data){
                    var returnVal = data.split('||');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('paymentMethodText').value = returnVal[4];
						document.getElementById('paymentMethod').value = returnVal[3];
						document.getElementById('paymentTypeText').value = returnVal[6];
						document.getElementById('paymentType').value = returnVal[5];
						document.getElementById('stockpileLocationText').value = returnVal[2];
						document.getElementById('stockpileLocation').value = returnVal[1];
						document.getElementById('paymentLocationText').value = returnVal[8];
						document.getElementById('paymentLocation').value = returnVal[7];
						document.getElementById('bankCurrencyId').value = returnVal[9];
						document.getElementById('exchangeRate').value = returnVal[10];
						document.getElementById('idPG').value = returnVal[11];
						//alert (returnVal[10]);
                    }
                }
            });
        //}
	}*/
	
	function resetRefreshInvoiceOAPayment() {
        document.getElementById('paymentMethodText').value = '';
		document.getElementById('paymentMethod').value = '';
		document.getElementById('paymentTypeText').value = '';
		document.getElementById('paymentType').value = '';
        document.getElementById('stockpileLocationText').value = '';
		document.getElementById('stockpileLocation').value = '';
		document.getElementById('paymentLocationText').value = '';
		document.getElementById('paymentLocation').value = '';
		document.getElementById('remarks').value = '';
		
    }

    function refreshInvoiceOAPayment(invoiceOAId) {

        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'refreshInvoiceOAPayment',
                        invoiceOAId: invoiceOAId
                },
                success: function(data){
                    var returnVal = data.split('||');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('stockpileLocation').value = returnVal[1];
                        document.getElementById('stockpileLocationText').value = returnVal[2];
						document.getElementById('paymentMethod').value = returnVal[3];
                        document.getElementById('paymentMethodText').value = returnVal[4];
						document.getElementById('paymentTypeText').value = returnVal[6];
						document.getElementById('paymentType').value = returnVal[5];
						document.getElementById('paymentLocationText').value = returnVal[8];
						document.getElementById('paymentLocation').value = returnVal[7];
						document.getElementById('freightbankDp').value = returnVal[9];
						document.getElementById('remarks').value = returnVal[10];
						document.getElementById('idPP').value = returnVal[11];
						document.getElementById('qtyDp').value = returnVal[12];
						document.getElementById('priceDp').value = returnVal[13];
						document.getElementById('amount').value = returnVal[14];
						document.getElementById('termin').value = returnVal[15];
						//document.getElementById('ppnInvoice').value = returnVal[16];
						//document.getElementById('pphInvoice').value = returnVal[17];

						
						$('#bankId').find('option').each(function(i,e){
                            if($(e).val() == returnVal[14]){
                                $('#bankId').prop('selectedIndex',i);
                                
                                $("#bankId").select2({
                                    width: "100%",
                                    placeholder: returnVal[14]
								});
                            }
                        });
						
                    }
                }
            });
        //}
	}
	/*
	function resetRefreshInvoiceOBPayment() {
        document.getElementById('paymentMethodText').value = '';
		document.getElementById('paymentMethod').value = '';
		document.getElementById('paymentTypeText').value = '';
		document.getElementById('paymentType').value = '';
        document.getElementById('stockpileLocationText').value = '';
		document.getElementById('stockpileLocation').value = '';
		document.getElementById('paymentLocationText').value = '';
		document.getElementById('paymentLocation').value = '';
		document.getElementById('remarks').value = '';
		
    }

    function refreshInvoiceOBPayment(invoiceOBId) {

        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'refreshInvoiceOBPayment',
                        invoiceOBId: invoiceOBId
                },
                success: function(data){
                    var returnVal = data.split('||');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('paymentMethodText').value = returnVal[4];
						document.getElementById('paymentMethod').value = returnVal[3];
						document.getElementById('paymentTypeText').value = returnVal[6];
						document.getElementById('paymentType').value = returnVal[5];
						document.getElementById('stockpileLocationText').value = returnVal[2];
						document.getElementById('stockpileLocation').value = returnVal[1];
						document.getElementById('paymentLocationText').value = returnVal[8];
						document.getElementById('paymentLocation').value = returnVal[7];
						document.getElementById('laborBankDp').value = returnVal[9];
						document.getElementById('remarks').value = returnVal[10];
						document.getElementById('idPP').value = returnVal[11];
						document.getElementById('qtyDp').value = returnVal[12];
						document.getElementById('priceDp').value = returnVal[13];
						document.getElementById('amount').value = returnVal[14];
						document.getElementById('termin').value = returnVal[15];
						document.getElementById('ppnInvoice').value = returnVal[16];
						document.getElementById('pphInvoice').value = returnVal[17];
						
						$('#bankId').find('option').each(function(i,e){
                            if($(e).val() == returnVal[14]){
                                $('#bankId').prop('selectedIndex',i);
                                
                                $("#bankId").select2({
                                    width: "100%",
                                    placeholder: returnVal[14]
								});
                            }
                        });
						
                    }
                }
            });
        //}
	}
	
	function resetRefreshInvoiceSalesPayment() {
        document.getElementById('paymentMethodText').value = '';
		document.getElementById('paymentMethod').value = '';
		document.getElementById('paymentTypeText').value = '';
		document.getElementById('paymentType').value = '';
        document.getElementById('stockpileLocationText').value = '';
		document.getElementById('stockpileLocation').value = '';
		document.getElementById('paymentLocationText').value = '';
		document.getElementById('paymentLocation').value = '';
		document.getElementById('remarks').value = '';
		document.getElementById('bankId').value = '';
		document.getElementById('bankCurrencyId').value = '';
		document.getElementById('exchangeRate').value = '';
		
    }

    function refreshInvoiceSalesPayment(invoiceSalesId) {

        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'refreshInvoiceSalesPayment',
                        invoiceSalesId: invoiceSalesId
                },
                success: function(data){
                    var returnVal = data.split('||');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('paymentMethodText').value = returnVal[4];
						document.getElementById('paymentMethod').value = returnVal[3];
						document.getElementById('paymentTypeText').value = returnVal[6];
						document.getElementById('paymentType').value = returnVal[5];
						document.getElementById('stockpileLocationText').value = returnVal[2];
						document.getElementById('stockpileLocation').value = returnVal[1];
						document.getElementById('paymentLocationText').value = returnVal[8];
						document.getElementById('paymentLocation').value = returnVal[7];
						document.getElementById('SalesBankDp').value = returnVal[9];
						document.getElementById('remarks').value = returnVal[10];
						document.getElementById('idPP').value = returnVal[11];
						document.getElementById('qtyDp').value = returnVal[12];
						document.getElementById('priceDp').value = returnVal[13];
						document.getElementById('amount').value = returnVal[14];
						document.getElementById('termin').value = returnVal[15];
						document.getElementById('ppnInvoice').value = returnVal[16];
						document.getElementById('pphInvoice').value = returnVal[17];
						
						$('#bankId').find('option').each(function(i,e){
                            if($(e).val() == returnVal[14]){
                                $('#bankId').prop('selectedIndex',i);
                                
                                $("#bankId").select2({
                                    width: "100%",
                                    placeholder: returnVal[14]
								});
                            }
                        });
						
                    }
                }
            });
        //}
	}*/
	
	/*function resetRefreshInvoiceHandlingPayment() {
        document.getElementById('paymentMethodText').value = '';
		document.getElementById('paymentMethod').value = '';
		document.getElementById('paymentTypeText').value = '';
		document.getElementById('paymentType').value = '';
        document.getElementById('stockpileLocationText').value = '';
		document.getElementById('stockpileLocation').value = '';
		document.getElementById('paymentLocationText').value = '';
		document.getElementById('paymentLocation').value = '';
		document.getElementById('remarks').value = '';
		
    }

    function refreshInvoiceHandlingPayment(invoiceHandlingId) {

        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'refreshInvoiceHandlingPayment',
                        invoiceHandlingId: invoiceHandlingId
                },
                success: function(data){
                    var returnVal = data.split('||');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('paymentMethodText').value = returnVal[4];
						document.getElementById('paymentMethod').value = returnVal[3];
						document.getElementById('paymentTypeText').value = returnVal[6];
						document.getElementById('paymentType').value = returnVal[5];
						document.getElementById('stockpileLocationText').value = returnVal[2];
						document.getElementById('stockpileLocation').value = returnVal[1];
						document.getElementById('paymentLocationText').value = returnVal[8];
						document.getElementById('paymentLocation').value = returnVal[7];
						document.getElementById('handlingBankDp').value = returnVal[9];
						document.getElementById('remarks').value = returnVal[10];
						document.getElementById('idPP').value = returnVal[11];
						document.getElementById('qtyDp').value = returnVal[12];
						document.getElementById('priceDp').value = returnVal[13];
						document.getElementById('amount').value = returnVal[14];
						document.getElementById('termin').value = returnVal[15];
						document.getElementById('ppnInvoice').value = returnVal[16];
						document.getElementById('pphInvoice').value = returnVal[17];
						
						$('#bankId').find('option').each(function(i,e){
                            if($(e).val() == returnVal[14]){
                                $('#bankId').prop('selectedIndex',i);
                                
                                $("#bankId").select2({
                                    width: "100%",
                                    placeholder: returnVal[14]
								});
                            }
                        });
						
                    }
                }
            });
        //}
	}
	
	function resetSupplier(text) {
        document.getElementById('invoiceFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceFreightId').options.add(x);
    }

    function setSupplier(type, stockpileId, invoiceFreightId) {
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
                        document.getElementById('invoiceFreightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceFreightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceFreightId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceFreightId').value = invoiceFreightId;
                    }
                }
            }
        });
    }*/
	
	function resetVendor(elementId, text) {
        document.getElementById(elementId).options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById(elementId).options.add(x);
    }

    function setVendor(type, elementId, stockpileId, contractType, invoiceVendorSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorPayment',
                    stockpileId: stockpileId,
                    contractType: contractType
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
                        document.getElementById(elementId).options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById(elementId).options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById(elementId).options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById(elementId).value = invoiceVendorSalesId;
                    }
                }
            }
        });
    }
	
/*	function resetLabor(text) {
        document.getElementById('invoiceLaborId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceLaborId').options.add(x);
    }

    function setLabor(type, stockpileId, invoiceLaborId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getLaborPayment',
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
                        document.getElementById('invoiceLaborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceLaborId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceLaborId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceLaborId').value = invoiceLaborId;
                    }
                }
            }
        });
    }
    function resetVendorHandling(text) {
        document.getElementById('invoiceVendorHandlingId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceVendorHandlingId').options.add(x);
    }

    function setVendorHandling(type, stockpileId, vendorHandlingId) {
		//alert('test1');
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getHandlingPayment',
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
                        document.getElementById('invoiceVendorHandlingId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceVendorHandlingId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceVendorHandlingId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('invoiceVendorHandlingId').value = vendorHandlingId;
                    }
                }
            }
        });
    }*/
	
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


<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<form method="post" id="paymentDataForm" autocomplete = "off">
    <input type="hidden" name="action" id="action" value="payment_data_sales_oa" />
    <input type="hidden" name="_method" value="INSERT">
    <input type="hidden" name="idPP" id="idPP">
    <input type="hidden" name="idPG" id="idPG">
    <input type="hidden" name="priceDp" id="priceDp">
    <input type="hidden" name="qtyDp" id="qtyDp">
    <input type="hidden" name="amount" id="amount">
    <input type="hidden" name="termin" id="termin">
    
    <input type="hidden" name="freightbankDp" id="freightbankDp">
    <!--<input type="hidden" name="laborBankDp" id="laborBankDp">
    <input type="hidden" name="handlingBankDp" id="handlingBankDp"> 
    <input type="hidden" name="SalesBankDp" id="SalesBankDp">  -->

    <!-- KEPRLUAN insert ke tabel payment -->
    <div class="row-fluid">
        <?php if($logbookStatus == 2) { ?>
            <div class="span12 lightblue">
                <label style="color: red;"><center><b>CANCELED</center></b></label>
            </div>
            <hr>
        <?php } ?>
    </div>

    <div class="row-fluid">
        <div class="span3 lightblue">
			<label>Payment Date</label>
			<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentDate" name="paymentDate" data-date-format="dd/mm/yyyy" class="datepicker" value = "<?php echo $paymentDate1 ?>">
        </div>
		<div class="span3 lightblue">
            <label>Method</label>
			<input readonly type="text" class="span12" tabindex="" id="paymentMethodText" name="paymentMethodText">
			<input readonly type="hidden" class="span12" tabindex="" id="paymentMethod" name="paymentMethod">
        </div>
        <div class="span3 lightblue">
            <label>Type</label>
			<input readonly type="text" class="span12" tabindex="" id="paymentTypeText" name="paymentTypeText">
			<input readonly type="hidden" class="span12" tabindex="" id="paymentType" name="paymentType">
        </div>
        <div class="span3 lightblue">
			<label>Payment Type</label>
			<?php
			createCombo("SELECT '1' as id, 'TT' as info UNION
                    SELECT '2' as id, 'Cek/Giro' as info UNION
					SELECT '3' as id, 'Tunai' as info UNION
					SELECT '4' as id, 'Bill Payment' as info UNION
					SELECT '5' as id, 'Auto Debet' as info;", $payment_type, "", "payment_type", "id", "info",
                "", 2,"select2combobox100");
				?>
        </div>
    </div>
	<div class="row-fluid">
        <div class="span3 lightblue">
			<label>Payment For</label>
			<input readonly type="text" class="span12" tabindex="" id="paymentForText" name="paymentForText" value= "<?php echo $paymentForText ?>">
			<input type="hidden" id="paymentFor" name="paymentFor" value="<?php echo $paymentFor_ ?>" />
        </div>
		<div class="span3 lightblue">
            <label>Stockpile Location</label>
			<input readonly type="text" class="span12" tabindex="" id="stockpileLocationText" name="stockpileLocationText">
			<input readonly type="hidden" class="span12" tabindex="" id="stockpileLocation" name="stockpileLocation">
        </div>
        <div class="span3 lightblue">
            <label>Payment Location</label>
			<input readonly type="text" class="span12" tabindex="" id="paymentLocationText" name="paymentLocationText">
			<input readonly type="hidden" class="span12" tabindex="" id="paymentLocation" name="paymentLocation">
        </div>
        <div class="span3 lightblue">
			<label>Account</label>
			<input readonly type="text" class="span12" tabindex="" id="accountName" name="accountName">
			<input readonly type="hidden" class="span12" tabindex="" id="accountId" name="accountId">
        </div>
    </div>


    
    <!-- INVOICE PAYMENT -->
   <!-- <div class="row-fluid" id="invoicePayment" style="display: none;">
	
	
		
    <div class="span3 lightblue">
        <label>Vendor Type <span style="color: red;">*</span></label>
        <?php
        /*createCombo("SELECT '1' as id, 'Pks' as info UNION
                    SELECT '2' as id, 'General' as info UNION
                    SELECT '3' as id, 'Freight' as info  UNION
                    SELECT '4' as id, 'Labor' as info UNION
                    SELECT '5' as id, 'Handling' as info UNION
                    SELECT '6' as id, 'PettyCash' as info;", $vendorType, '', "vendorType", "id", "info",
            "", 21, "select2combobox100");*/
        ?>
    </div>
        <div class="span3 lightblue">
			<label>Vendor <span style="color: red;">*</span></label> 
				<input readonly type="text" class="span12" tabindex="" id="gv_text" name="gv_text" value = "<?php //echo $gv_text ?>">
				<input type="hidden" id="gvId" name="gvId" value="<?php //echo $gvId ?>" />
        </div>
		<div class="span3 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
				<input readonly type="text" class="span12" tabindex="" id="gvBank_text" name="gvBank_text" value = "<?php //echo $gvBank_text ?>">
				<input type="hidden" id="gvBankId" name="gvBankId" value="<?php //echo $gvBankId ?>" />
        </div>

        <div class="span3 lightblue">
			<label>Invoice No. <span style="color: red;">*</span></label>
				<input readonly type="text" class="span12" tabindex="" id="invoiceId_text" name="invoiceId_text" value = "<?php //echo $invoiceText ?>">
				<input type="hidden" id="invoiceId" name="invoiceId"  value="<?php //echo $invoiceId_ ?>" />    
        </div>
    </div>-->
	
	<!-- INVOICE OA -->
    <!-- <div class="row-fluid" id="invoiceOA" style="display: none;">

        <div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="stockpileName" name="stockpileName" value = "<?php //echo $stockpileName ?>">
			<input type="hidden" id="stockpileOAId" name="stockpileOAId" value="<?php //echo $stockpileId_ ?>" />
        </div>
		
		<div class="span4 lightblue">
			<label>Freight <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="invoiceFreightText" name="invoiceFreightText" value = "<?php //echo $invoiceFreightText ?>">
			<input type="hidden" id="invoiceFreightId" name="invoiceFreightId" value="<?php //echo $invoiceFreightId_ ?>" />
        </div>
		<div class="span4 lightblue">
		<label>Invoice No. <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="invoiceOAText" name="invoiceOAText" value = "<?php //echo $invoiceText ?>">
			<input type="hidden" id="invoiceOAId" name="invoiceOAId"  value="<?php //echo $invoiceId_ ?>" />    
        </div>

    </div>-->
	
	<!-- INVOICE OB -->
    <!--<div class="row-fluid" id="invoiceOB" style="display: none;">

	
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="stockpileName" name="stockpileName" value = "<?php //echo $stockpileName ?>">
			<input type="hidden" id="stockpileOBId" name="stockpileOBId" value="<?php //echo $stockpileId_ ?>" />
        </div>
        <div class="span4 lightblue">
			<label>Labor <span style="color: red;">*</span></label>
				<input readonly type="text" class="span12" tabindex="" id="invoiceLaborText" name="invoiceLaborText" value = "<?php //echo $invoiceLaborText ?>">
				<input type="hidden" id="invoiceLaborId" name="invoiceLaborId" value="<?php //echo $invoiceLaborId_ ?>" />

        </div>
		<div class="span4 lightblue">
		<label>Invoice No. <span style="color: red;">*</span></label>
				<input readonly type="text" class="span12" tabindex="" id="invoiceText" name="invoiceText" value = "<?php //echo $invoiceText ?>">
				<input type="hidden" id="invoiceOBId" name="invoiceOBId" value="<?php //echo $invoiceId_ ?>" />
        </div>
    </div>-->
	
	<!-- INVOICE Handling -->
    <!--<div class="row-fluid" id="invoiceHandling" style="display: none;">
		
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="stockpileName" name="stockpileName" value = "<?php//echo $stockpileName ?>">
			<input type="hidden" id="stockpileHandlingId" name="stockpileHandlingId" value="<?php //echo $stockpileId_ ?>" />
        </div>
        <div class="span4 lightblue">
			<label>Vendor Handling <span style="color: red;">*</span></label>
				<input readonly type="text" class="span12" tabindex="" id="invoiceVendorHandlingText" name="invoiceVendorHandlingText" value = "<?php echo $invoiceVendorHandlingText ?>">
				<input type="hidden" id="invoiceVendorHandlingId" name="invoiceVendorHandlingId" value="<?php //echo $invoiceVendorHandlingId_ ?>" />
        </div>
		<div class="span4 lightblue">
		<label>Invoice No. <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="invoiceText" name="invoiceText" value = "<?php //echo $invoiceText ?>">
			<input type="hidden" id="invoiceHandlingId" name="invoiceHandlingId" value="<?php //echo $invoiceId_ ?>" />
        </div>
    </div>-->
	
	<!-- INVOICE Sales -->
    <div class="row-fluid" id="invoiceSales" style="display: none;">
		
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="stockpileName" name="stockpileName" value = "<?php echo $stockpileName ?>">
			<input type="hidden" id="stockpileSalesId" name="stockpileSalesId" value="<?php echo $stockpileId_ ?>" />
        </div>
        <div class="span4 lightblue">
			<label>Vendor Sales <span style="color: red;">*</span></label>
				<input readonly type="text" class="span12" tabindex="" id="invoiceVendorSalesText" name="invoiceVendorSalesText" value = "<?php echo $invoiceVendorSalesText ?>">
				<input type="hidden" id="invoiceVendorSalesId" name="invoiceVendorSalesId" value="<?php echo $invoiceVendorSalesId?>" />
        </div>
		<div class="span4 lightblue">
		<label>Invoice No. <span style="color: red;">*</span></label>
			<input readonly type="text" class="span12" tabindex="" id="invoiceText" name="invoiceText" value = "<?php echo $invoiceText ?>">
			<input type="hidden" id="invoiceSalesId" name="invoiceSalesId" value="<?php echo $invoiceId_ ?>" />
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
    <div class="row-fluid">
        <div class="span2 lightblue">
            <label>From/To <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="hidden" name="currencyId" id="currencyId"
                value="<?php echo $_SESSION['payment']['currencyId']; ?>"/>
            <input type="hidden" name="journalCurrencyId" id="journalCurrencyId" value="1"/>
            <input type="hidden" name="bankCurrencyId" id="bankCurrencyId"
                value="<?php echo $_SESSION['payment']['bankCurrencyId']; ?>"/>
            <?php
            createCombo("SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full
                    FROM bank b
                    INNER JOIN currency cur
                        ON cur.currency_id = b.currency_id
                    ORDER BY b.bank_name ASC, cur.currency_code ASC, b.bank_account_name", $_SESSION['payment']['bankId'], "", "bankId", "bank_id", "bank_full",
                "", 18, "select2combobox100", 1, "");
            ?>
        </div>
        <div class="span2 lightblue" id="labelExchangeRate" style="display: none;">
            <label>Exchg Rate USD to IDR <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="inputExchangeRate" style="display: none;">
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate"
                value="<?php echo $_SESSION['payment']['exchangeRate']; ?>"/>
        </div>
    </div>

    <!-- GET CHEQUE NO -->
    </br>
    </br>
    <div class="row-fluid">
        <div class="span2 lightblue">
            <label>Cheque No</label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="chequeNo" name="chequeNo" value="<?php echo $chequeNo; ?>" >
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span2 lightblue">
            <label>Remarks</label>
        </div>
        <div class="span4 lightblue">
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span2 lightblue">
            <label>Remarks Bank(Max 19 character)</label>
        </div>
        <div class="span4 lightblue">
            <textarea class="span12" rows="3" tabindex="" id="remarks2" name="remarks2"><?php echo $remarks2; ?></textarea>
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span1 lightblue">
            <label>Beneficiary</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary" value="<?php echo $beneficiary; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Bank</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="bank" name="bank" value="<?php echo $bank; ?>">
        </div>

    </div>
    <div class="row-fluid">

        <div class="span1 lightblue">
            <label>No Rek.</label>
        </div>
        <div class="span5 lightblue">
       		<input type="text" readonly class="span12" tabindex="" id="rek" name="rek" value="<?php echo $rek; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Swift Code</label>
        </div>
        <div class="span5 lightblue">
        	<input type="text" readonly class="span12" tabindex="" id="swift" name="swift" value="<?php echo $swift; ?>">
        </div>

    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" id="submitButton2">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<!--<form method="post" id="cancelForm" >
<input type="hidden" name="action" id="action" value="reject_invoice_notim" />
<input type="hidden" name="idPP1" id="idPP1" value="<?php echo $idPP ?>">

<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Return Remarks</label>
        <textarea class="span12" rows="3" id="return_remarks" name="return_remarks"></textarea>
    </div>
</div>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-danger" <?php echo $disableProperty; ?>  id="submitButton">Cancel</button>
       
    </div>
</div>
</form>-->
