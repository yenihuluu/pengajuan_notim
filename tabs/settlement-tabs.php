<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Pengajuan Payments';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('d/m/Y');
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('my');

$allowAccount = true;
$allowBank = true;
$allowGeneralVendor = true;
$idPP = '';
$stockpileId = '';
$paymentType = '';
$paymentFor = '';
$vendorid = '';
$stockpileContractId = '';
// $stockpileId1 = '';

$paymentFrom1 = '';
$paymentTo1 = '';
$vendorHandling = '';
$freightId = '';
$vendorFreightId = '';
$contractPKHOA = '';
$paymentMethod = '';


$journalCurrencyId='';
$bankCurrencyId = '';
$bankId = '';

// $currencyId = '';
// $amount = '';
// $taxInvoice = '';
// $invoiceNo = '';
// $invoiceDate = '';
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
$paymentMethods = '';
$paymentFor_1 = '';
$method = '';

$qty = 0;
$price = 0;
$termin = 0;
$dpp = 0;
$kontrakPKHOA = '';
$contractId = '';
$laborId = '';

$whereproperty = '';

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

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
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
        echo "<option value=''>-- Please Select Supplier --</option>";
    } else if($empty == 7) {
        echo "<option value=''>-- Please Select No Kontrak --</option>";
    }else if($empty == 8) {
        echo "<option value=''>-- Please Select Kontrak PKHOA --</option>";
    }else if($empty == 9) {
        echo "<option value=''>-- Please Select Vendor --</option>";
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

// </editor-fold>
if(isset($_POST['InvNotim']) && $_POST['InvNotim'] != '') {
    $InvNotim = $_POST['InvNotim'];

    $sql = "SELECT CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE NULL END AS paymentMethod, 
                ty.type_transaction_name AS paymentFor, sp.stockpile_name, f.freight_supplier, lab.labor_name, vh.vendor_handling_name, 
                v.vendor_name AS vendor,  invn.file1,
                CONCAT(vb.bank_name,' - ',vb.account_no) AS vbank, 
                CONCAT(lbank.bank_name,' - ',lbank.account_no) AS lbank, CONCAT(fb.bank_name,' - ',fb.account_no) AS fbank, 
                CONCAT(vhb.bank_name,' - ',vhb.account_no) AS vhbank, DATE_FORMAT(pp.urgent_payment_date, '%d/%m/%Y') AS tglReguest, 
                DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo, 
                DATE_FORMAT(invn.entry_date, '%d/%m/%Y') AS invoiceDate1, pp.* 
                FROM invoice_notim invn 
                INNER JOIN pengajuan_payment pp on pp.idPP = invn.idpp
                LEFT JOIN type_transaction ty ON ty.type_transaction_id = pp.payment_for 
                LEFT JOIN pengajuan_payment_supplier pps ON pps.idpp = invn.idpp 
                LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id 
                LEFT JOIN vendor v ON v.vendor_id = invn.vendor_id OR v.vendor_id = pps.vendor_id 
                LEFT JOIN vendor_bank vb ON vb.vendor_id = invn.vendor_id 
                LEFT JOIN freight f ON f.freight_id = invn.freightId 
                LEFT JOIN freight_bank fb ON fb.freight_id = invn.freightId 
                LEFT JOIN labor lab ON lab.labor_id = invn.laborId 
                LEFT JOIN labor_bank lbank ON lbank.labor_id = invn.laborId 
                LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = invn.vendorHandlingId 
                LEFT JOIN vendor_handling_bank vhb ON vhb.vendor_handling_id = invn.vendorHandlingId 
               where invn.inv_notim_id = {$InvNotim}";
       // echo $sqlPP;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result !== false && $result->num_rows > 0) {
        $rowData = $result->fetch_object();
        $paymentMethod = $rowData->payment_method;
        $paymentMethods = $rowData->paymentMethod;
        $stockpileId = $rowData->stockpile_id;
        $stockpileName = $rowData->stockpile_name;
        $paymentType = $rowData->payment_type;
        $paymentFor = $rowData->payment_for;
        $paymentFor_1 = $rowData->paymentFor;
        $vendorid = $rowData->vendor_id;
        $stockpileContractId = $rowData->stockpile_contract_id;

        // $stockpileId1 = $rowDataPP->stockpile_id;
        $paymentFrom1 = $rowData->dateFrom;
        $paymentTo1 = $rowData->dateTo;

        // $vendorId1 = $rowData->vendor_id;
        $vendorName = $rowData->vendor;
        $vbank = $rowData->vbank;
        $vendorHandling = $rowData->vendor_handling_id;
        $vendorHandlingName = $rowData->vendor_handling_name;
        $vhbank = $rowData->vhbank;
        $freightId = $rowData->freight_id;
        $freightName = $rowData->freight_supplier;
        $freightbankName = $rowData->fbank;
        $vendorFreightId = $rowData->transaction_id;

        $taxInvoice = $rowData->tax_invoice;
        $invoiceNo = $rowData->invoice_no;
        $invoiceDate = $rowData->invoiceDate1;
        $chequeNo = $rowData->cheque_no;
        $remarks = $rowData->remarks;
        $remarks2 = $rowData->remaks2;
        $amountVal = $rowData->amount;
        $beneficiary = $rowData->beneficiary;
        $bank = $rowData->bank_name;
        $rek = $rowData->no_rek;
        $swift = $rowData->swift;
       
        $vendorBankID = $rowData->vendor_bank_id;

        $laborId = $rowData->labor_id;
        $laborName = $rowData->labor_name;
        $bankName = $rowData->lbank;
        $file_invoice = $rowData->file1;

        $qty = $rowData->qty;
        $price = $rowData->price;
        $termin = $rowData->termin;
        $dpp = $rowData->dpp;

        $kontrakPKHOA = $rowData->contract_pkhoa;
        $contractNo1 = $rowData->contractNo1;
        $idPP = $rowData->idPP;
    }

    if($paymentFor == 2 && $InvNotim != ''){  //GET FREIGHT
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

    $sql2 = "SELECT * FROM TRANSACTION WHERE {$whereproperty}";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    if ($result2 !== false && $result2->num_rows > 0) {
        while( $row2 = $result2->fetch_object()){
            $transaksiID = $row2->transaction_id;
        }   
    }

    $method = 'INSERT';
}else{
   // $method = 'INSERT';
}

?>

<!-- <input type="hidden"  id="stockpileIdVal" value="<?php echo $stockpileId; ?>" > -->
<!-- <input type="hidden" id="vendorId1Val" value="<?php echo $vendorId1 ?>" /> -->
<input type="hidden" id="vendorHandlingVal" value="<?php echo $vendorHandling ?>" />
<input type="hidden" id="freightIdVal" value="<?php echo $freightId ?>" />
<input type="hidden" id="vendorFreightIdVal" value="<?php echo $vendorFreightIds ?>" />
<input type="hidden" readonly class="span12" tabindex="" id="validasiPKHOA" name="validasiPKHOA">
<input type="hidden" readonly class="span12" tabindex="" id="todayDate" name="todayDate" value="<?php echo $todayDate ?>">


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

        // $('#qtyFreight').number(true, 2); 
        // $('#priceFreight').number(true, 2);

        $('#qtyLabor').number(true, 2);
        $('#priceLabor').number(true, 2);

        $('#tglBayarDiv').hide();
        $('#amount1').hide();
        $('#amount2').show();
        $('#paymentType').attr("readonly", true);
        $('#paymentMethod').attr("readonly", true);




    setStockpileLocation(<?php echo $stockpileId ?>, <?php echo $idPP ?>); //DIPINDAH KESINI
    setPaymentType(1, <?php echo $paymentType; ?>);
    setPaymentMethod(1, <?php echo $paymentMethod; ?>);

    // <?php if($InvNotim != ''){ ?>
    //     $('#amount1').show();
    //     $('#amount2').hide();
    // <?php } ?>

            
    //FREIGHT   
    <?php if($InvNotim != '' && $paymentFor == 2){ //GET DATA FROM ADMIN?> 
        // setSlipFreight_1(<?php echo $stockpileId; ?>, <?php echo $freightId; ?>, '', '','NONE', 'NONE', $('input[id="paymentFrom_1"]').val(),$('input[id="paymentTo_1"]').val(), <?php echo $idPP; ?>);
    <?php } ?>

    //UNLOADING C
    <?php if($InvNotim != '' && $paymentFor == 3) { ?>
        //setSlipUnloading_settle($(<?php echo $stockpileId ?>, <?php echo $laborId ?>, '', '', '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val());
        <?php if($paymentMethod == 3) {?>
            $('#unloadingPayment').show();
    <?php }
    } ?>

      //HANDLING
    <?php if($InvNotim != '' && $paymentFor == 9){ //GET DATA FROM ADMIN?> 
        setSlipHandling(<?php echo $stockpileId; ?>, <?php echo $vendorHandling; ?>, '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val(), <?php echo $idPP; ?>);
       <?php if($paymentMethod == 3) {?>
            $('#handlingPaymentSettle').show();
        <?php }
        } ?>

     $('#divInvoice').show();
     $('#divPaymentType1').hide()
     $('#tglBayarDiv').hide();

    $('#paymentFor').change(function() {
	    resetBankDetail(' ');
        $('#slipPayment').hide();
        $('#summaryPayment').hide();
		$('#freightPaymentSetlemen').hide();
		$('#handlingPaymentSettle').hide();
        $('#unloadingPayment').hide();
        resetVendorHandling('vendorHandlingId', ' Stockpile ');
        if(document.getElementById('paymentFor').value != '') {
            if(document.getElementById('paymentType').value == 2) {
                // OUT
               if(document.getElementById('paymentFor').value == 2) {
                    document.getElementById('stockpileId2').value = '';
                    resetSupplier(' Stockpile ');
		            resetVendorFreight(' Stockpile ');
                    $('#freightPaymentSetlemen').show();
                    // document.getElementById('currencyId').value = 1;
                } else if(document.getElementById('paymentFor').value == 9) {
                    document.getElementById('stockpileId4').value = '';
                    resetVendorHandling('vendorHandlingId', ' Stockpile ');
				    if(document.getElementById('paymentMethod').value == 3){
                        $('#handlingPaymentSettle').show();
                    }
                } else if(document.getElementById('paymentFor').value == 3) {
                    document.getElementById('stockpileId3').value = '';
                    resetLabor('Stockpile ');
				    if(document.getElementById('paymentMethod').value == 3){
					    $('#unloadingPayment').show();
					}
                }     
            } 
                
        } 
    });


// SETTLEMENT FREIGHT
    $('#stockpileId2').change(function() {
            resetSupplier('freightId', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('stockpileId2').value != '') {
                resetSupplier(' ');
             //   setSupplier(0, $('select[id="stockpileId2"]').val(), 0);
             setSupplier_1(0, $('select[id="stockpileId2"]').val(), 0);
            }
    });
    $('#freightId').change(function() {
            resetVendorFreight(' ');
			resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetVendorBank ($('select[id="paymentFor"]').val());
            if(document.getElementById('freightId').value != '') {
                setVendorFreight(0, $('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), 0);
                getVendorBank(0,$('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
            }
        });

    $('#freightBankId').change(function() {
        resetVendorBankDetail ($('select[id="paymentFor"]').val());

        if(document.getElementById('freightBankId').value != '') {
            getVendorBankDetail($('select[id="freightBankId"]').val(), $('select[id="paymentFor"]').val());
        }
    });

    $('#vendorFreightId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            if(document.getElementById('vendorFreightId').value != '' && document.getElementById('freightId').value != '') {
                if(document.getElementById('paymentMethod').value == 3) {
                    setNoKontrak(0, $('select[id="vendorFreightId"]').val(), 0);

                }
            }
    });

    $('#contractNo').change(function() {
      //  ValidationPKHOA($('select[id="contractPKHOA"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            if(document.getElementById('contractNo').value != '' && document.getElementById('vendorFreightId').value != '') {
                if(document.getElementById('paymentMethod').value == 3) {
 					//setSlipFreight_settle($('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), $('select[id="contractPKHOA"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val());
                     setSlipFreight_settle($('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), '2', $('select[id="contractNo"]').val(), '', '','NONE', 'NONE',$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val());

                }
            }
    });
// END SETTLEMENT FREIGHT

//HANDLING
	$('#stockpileId4').change(function() {
        resetVendorHandling();
        $('#slipPayment').hide();
        $('#summaryPayment').hide();

        if(document.getElementById('stockpileId4').value != '') {
            resetVendorHandling(' ');
            setVendorHandling(0, $('select[id="stockpileId4"]').val(), 0);
        }
    });

    $('#vendorHandlingId').change(function() {
        $('#slipPayment').hide();
        $('#summaryPayment').hide();
			
        resetContractHandling(' ');
		resetVendorBank ($('select[id="paymentFor"]').val());
			//alert('html from to nya');
        if(document.getElementById('paymentMethod').value == 3) {
			getVendorBank(0,$('select[id="vendorHandlingId"]').val(), $('select[id="paymentFor"]').val());
			setContractHandling($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val());
        }
        
    });

    $('#contractHandling').change(function() {
        $('#slipPayment').hide();
        $('#summaryPayment').hide();

        if(document.getElementById('vendorHandlingId').value != '') {
            setSlipHandling_settle($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val(), '2', $('select[id="contractHandling"]').val(), '', '','NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val());
        }
    });

	$('#vendorHandlingBankId').change(function() {
		resetVendorBankDetail ($('select[id="paymentFor"]').val());
        if(document.getElementById('vendorHandlingBankId').value != '') {
			getVendorBankDetail($('select[id="vendorHandlingBankId"]').val(), $('select[id="paymentFor"]').val());
        } 
    });

    //END HANDLING

    //UNLOADING
       $('#stockpileId3').change(function() {
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileId3').value != '') {
                resetLabor(' ');
                setLabor(0, $('select[id="stockpileId3"]').val(), 0);
            }
        });

       $('#laborId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorBank(' ');
			//alert('html from to nya');

            if(document.getElementById('laborId').value != '') {
               if(document.getElementById('paymentMethod').value == 3) {
                    setSlipUnloading_settle($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '2', '', '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val());
					getVendorBank(0,$('select[id="laborId"]').val(), $('select[id="paymentFor"]').val());
                }
            }
        });

		$('#laborBankId').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            if(document.getElementById('laborBankId').value != '') {
				 getVendorBankDetail($('select[id="laborBankId"]').val(), $('select[id="paymentFor"]').val());
            } 
        });
    //END UNLOADING
    
    // $('#bankId').change(function() {
    //         //resetExchangeRate();   
    //     if(document.getElementById('bankId').value != '' && document.getElementById('bankId').value != 'INSERT') {
    //         if(document.getElementById('currencyId').value != '' && document.getElementById('currencyId').value != 0) {
    //             setExchangeRate($('select[id="bankId"]').val(), $('input[id="currencyId"]').val(), $('input[id="journalCurrencyId"]').val());
    //         } 
    //     } 
    // });



        //SUBMIT FORM
        $("#settle100persenDataForm").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                _method: 'INSERT',
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
                            $('#pageContent').load('views/settlement-views.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

       $("#settle100persenDataForm").validate({
                rules: {
                    paymentMethod: "required",
                    paymentType: "required",
                    paymentFor: "required",
                    stockpileId: "required",
                    vendorId: "required",
                    stockpileContractId: "required",
                    invoiceNo: "required",
                    invoiceDate: "required",
                    contractPKHOA: "required",
                    vendorFreightId: "required",
                    freightId: "required",
                    HCcontractPKHOA: "required",
                    // file: {required: true, filesize: 1048576}
                },
                messages: {
                    paymentMethod: "Method is a required field.",
                    paymentType: "Type is a required field.",
                    paymentFor: "Payment For is a required field.",
                    stockpileId: "Stockpile is a required field.",
                    vendorId: "Vendor is a required field.",
                    stockpileContractId: "PO No. is a required field.",
                    invoiceNo: "Invoice No is a required field.",
                    invoiceDate: "Invoice Date  is a required field.",
                    contractPKHOA: "Kontrak PKHOA  is a required field.",
                    vendorFreightId: "Supplier  is a required field.",
                    freightId: "Freight id is a required field.",
                    HCcontractPKHOA: "Kontrak PKHO is a required field",

                    // file: "invoice file   is a required field."
                },
            submitHandler: function (form) {
                $('#submitButton2').attr("disabled", true);
            }
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


    function resetVendorBank(paymentFor) {
		//alert (paymentFor);
		if(paymentFor == 2){
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
		}else if(paymentFor == 9){
			document.getElementById('vendorHandlingBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('vendorHandlingBankId').options.add(x);
		}
    }

    function getVendorBank(type, vendorId, paymentFor) {
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
		        if(paymentFor == 2){
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
						if(paymentFor == 2){
						    document.getElementById('freightBankId').value = vendorId;
						}else if(paymentFor == 3){
						    document.getElementById('laborBankId').value = vendorId;
						}else if(paymentFor == 9){
						    document.getElementById('vendorHandlingBankId').value = vendorId;
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

    function ValidationPKHOA(contractPKHOA) {
        //alert(contractPKHOA);
        if(contractPKHOA != '') {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: { action: 'getValidationPKHOA',
                    contractPKHOA: contractPKHOA
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('validasiPKHOA').value = returnVal[1];
                    }
                    //  if(document.getElementById('validasiPKHOA').value == 0){
                    //      alert("Down Payment Belum Lunas");
                    //  }
                }
            });
        }
    }
	
// FREIGHT FUNCTION
function resetSupplier(text) {
        document.getElementById('freightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightId').options.add(x);
    }

	function setSupplier_1(type, stockpileId, freightId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getFreightPayment_1',
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

    function resetNoKontrak(text) {
        document.getElementById('contractNo').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractNo').options.add(x);
    }

    function setNoKontrak(type, vendorFreightIdDp, contractNo) {
    //  alert(vendorFreightId);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getNoKontrak',
                vendorFreightIdDp: vendorFreightIdDp
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
                    document.getElementById('contractNo').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                            x.text = '-- Please Select --';
                            document.getElementById('contractNo').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractNo').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('contractNo').value = contractNo;
                    }
                }
            }
        });
    }
    // END FREIGHT

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


function resetBankDetail() {
    document.getElementById('beneficiary').value = '';
    document.getElementById('bank').value = '';
    document.getElementById('rek').value = '';
    document.getElementById('swift').value = '';

}

function getBankDetail(bankVendor, paymentFor) {

    if(amount != '') {
        $.ajax({
            url: 'get-data-Ppayment.php',
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

//FREIGHT SETTLE
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
	 //checkSlipFreight(freightId, ppn, pph, paymentFrom, paymentTo);
     checkSlipFreight(stockpileId, freightId, settle, contractFreight, ppn, pph, paymentFrom, paymentTo);
 }

function checkSlipFreight(stockpileId, freightId, settle, contractFreight, ppn, pph, paymentFrom, paymentTo) {
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


    var selectedDP = 'NONE';
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
    setSlipFreight_settle(stockpileId, freightId, settle, contractFreight, selected, selectedDP, ppnValue, pphValue, paymentFrom, paymentTo);
}

function checkSlipFreightDP(stockpileId, freightId,settle, contractFreight, ppn, pph, paymentFrom, paymentTo) {
	var checkedSlipsDP = document.getElementsByName('checkedSlipsDP[]');
    var selectedDP = "";
    for (var j = 0; j < checkedSlipsDP.length; j++) {
        if (checkedSlipsDP[j].checked) {
            if(selectedDP == "") {
                selectedDP = checkedSlipsDP[j].value;
            } else {
                selectedDP = selectedDP + "," + checkedSlipsDP[j].value;
            }
         }
    }
		
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
        setSlipFreight_settle(stockpileId, freightId, settle, contractFreight, selected, selectedDP, ppnValue, pphValue, paymentFrom, paymentTo);
}

function checkSettle(stockpileId, freightId, settle, contractFreight, ppn, pph, paymentFrom, paymentTo) {
     var checkedSettle = document.getElementById('checkedSettle');
		var checkedSlipsDP = document.getElementsByName('checkedSlipsDP[]');
        var selectedDP = "";
        for (var j = 0; j < checkedSlipsDP.length; j++) {
            if (checkedSlipsDP[j].checked) {
                if(selectedDP == "") {
                    selectedDP = checkedSlipsDP[j].value;
                } else {
                    selectedDP = selectedDP + "," + checkedSlipsDP[j].value;
                }
            }
        }
		
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

		//var selected = 'NONE';
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
		
	      if (checkedSettle.checked == true ) {
				var selectedSettle = 1;
			} else {
				var selectedSettle = 0;
			}
	 
     setSlipFreight_settle(stockpileId, freightId, selectedSettle, contractFreight, selected, selectedDP, ppnValue, pphValue, paymentFrom, paymentTo);

 }

function setSlipFreight_settle(stockpileId, freightId, settle, contractNo, checkedSlips, checkedSlipsDP, ppn, pph, paymentFrom, paymentTo) {
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
                settle : settle
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

//HANDLING
function checkAllHandling(a) {
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
	 checkSlipHandling(vendorHandlingId, ppn, pph, paymentFrom, paymentTo);
 }

function checkSlipHandling(stockpileId, vendorHandlingId, ppn, pph, paymentFrom, paymentTo) {
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

            setSlipHandling(stockpileId, vendorHandlingId, selected, ppnValue, pphValue, paymentFrom, paymentTo);
    }

    function setSlipHandling(stockpileId, vendorHandlingId, checkedSlips, ppn, pph, paymentFromHP, paymentToHP, idPP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipHandling',
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

                var return_val = data.split('|');
                if(parseInt(return_val[0])!=0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
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


//UNLOADING
function checkAllUC(a) {
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
	 checkSlipUnloading(stockpileId, laborId, ppn, pph, paymentFromUP, paymentToUP);
 }
    function checkSlipUnloading(stockpileId, laborId, ppn, pph, paymentFromUP, paymentToUP) {
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

      //alert(ppn +', '+ pph);

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

        setSlipUnloading(stockpileId, laborId, selected, ppnValue, pphValue, paymentFromUP, paymentToUP);
    }

// UNLOADING SETTLE
function setSlipUnloading_settle(stockpileId, laborId, settle, checkedSlips, checkedSlipsDP, ppn, pph, paymentFromUP, paymentToUP) {
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
                    paymentToUP: paymentToUP
                    
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

    function checkSlipOB(stockpileId, laborId, settle, ppn, pph, paymentFromUP, paymentToUP) {
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

        var selectedDP = 'NONE';
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
        setSlipUnloading_settle(stockpileId, laborId, settle, selected, selectedDP, ppnValue, pphValue, paymentFromUP, paymentToUP);
    }

    function checkSlipUnloadingDP(stockpileId, laborId,settle, ppn, pph, paymentFromUP, paymentToUP) {
        var checkedSlipsDP = document.getElementsByName('checkedSlipsDP[]');
        var selectedDP = "";
        for (var j = 0; j < checkedSlipsDP.length; j++) {
            if (checkedSlipsDP[j].checked) {
                if(selectedDP == "") {
                    selectedDP = checkedSlipsDP[j].value;
                } else {
                    selectedDP = selectedDP + "," + checkedSlipsDP[j].value;
                }
            }
        }
            
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
        setSlipUnloading_settle(stockpileId, laborId, settle, selected, selectedDP, ppnValue, pphValue, paymentFromUP, paymentToUP);
    }

    function checkSettle_unloading(stockpileId, laborId, settle, ppn, pph, paymentFromUP, paymentToUP) {
        var checkedSettle = document.getElementById('checkedSettle');
            var checkedSlipsDP = document.getElementsByName('checkedSlipsDP[]');
            var selectedDP = "";
            for (var j = 0; j < checkedSlipsDP.length; j++) {
                if (checkedSlipsDP[j].checked) {
                    if(selectedDP == "") {
                        selectedDP = checkedSlipsDP[j].value;
                    } else {
                        selectedDP = selectedDP + "," + checkedSlipsDP[j].value;
                    }
                }
            }
            
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

            //var selected = 'NONE';
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
            
            if (checkedSettle.checked == true ) {
                    var selectedSettle = 1;
                } else {
                    var selectedSettle = 0;
                }
        
        setSlipUnloading_settle(stockpileId, laborId, selectedSettle, selected, selectedDP, ppnValue, pphValue, paymentFromUP, paymentToUP);
    }

    function resetLabor(text) {
        document.getElementById('laborId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('laborId').options.add(x);
    }

    function setLabor(type, stockpileId, laborId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
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
                        document.getElementById('laborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('laborId').value = laborId;
                    }
                }
            }
        });
    }
    //END UNLOADING

//HANDLING SETTLE
    function resetContractHandling(text) {
            document.getElementById('contractHandling').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select' + text + '--';
            document.getElementById('contractHandling').options.add(x);
    }

    function setContractHandling(stockpileId, vendorHandlingId) {
		//alert('test1');
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getContractHandling',
                    stockpileId: stockpileId,
					vendorHandlingId: vendorHandlingId
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
                        document.getElementById('contractHandling').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('contractHandling').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractHandling').options.add(x);
                    }

                    /*if(type == 1) {
                        document.getElementById('contractHandling').value = contractHandling;
                    }*/
                }
            }
        });
    }

    function resetVendorHandling(text) {
        document.getElementById('vendorHandlingId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorHandlingId').options.add(x);
    }

    function setVendorHandling(type, stockpileId, vendorHandlingId) {
		//alert('test1');
        $.ajax({
            url: 'get-data-Ppayment.php',
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
                        document.getElementById('vendorHandlingId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorHandlingId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorHandlingId').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('vendorHandlingId').value = vendorHandlingId;
                    }
                }
            }
        });
    }

    function checkSlipHandling_1(stockpileId, vendorHandlingId, settle, contractHandling, ppn, pph, paymentFromHP, paymentToHP) {
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

		var selectedDP = 'NONE';
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


        setSlipHandling_settle(stockpileId, vendorHandlingId, settle, contractHandling, selected, selectedDP, ppnValue, pphValue, paymentFromHP, paymentToHP);
    }

    function checkSlipHandlingDP(stockpileId, vendorHandlingId, settle, contractHandling, ppn, pph, paymentFromHP, paymentToHP) {
//        var checkedSlips = document.forms[0].checkedSlips;
		var checkedSlipsDP = document.getElementsByName('checkedSlipsDP[]');
        var selectedDP = "";
        for (var j = 0; j < checkedSlipsDP.length; j++) {
            if (checkedSlipsDP[j].checked) {
                if(selectedDP == "") {
                    selectedDP = checkedSlipsDP[j].value;
                } else {
                    selectedDP = selectedDP + "," + checkedSlipsDP[j].value;
                }
            }
        }
		
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

		//var selected = 'NONE';
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


        setSlipHandling_settle(stockpileId, vendorHandlingId, settle, contractHandling, selected, selectedDP, ppnValue, pphValue, paymentFromHP, paymentToHP);
    }
    
    function setSlipHandling_settle(stockpileId, vendorHandlingId, settle, contractHandling, checkedSlips, checkedSlipsDP, ppn, pph, paymentFromHP, paymentToHP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipHandling_settle',
                    stockpileId: stockpileId,
                    vendorHandlingId: vendorHandlingId,
					contractHandling: contractHandling,
                    checkedSlips: checkedSlips,
					checkedSlipsDP: checkedSlipsDP,
                    ppn: ppn,
                    pph: pph,
					paymentFromHP: paymentFromHP,
					paymentToHP: paymentToHP,
                    settle : settle

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

    function checkSettle_handling(stockpileId, vendorHandlingId, settle, contractHandling, ppn, pph, paymentFromHP, paymentToHP) {
        var checkedSettle = document.getElementById('checkedSettle');
            var checkedSlipsDP = document.getElementsByName('checkedSlipsDP[]');
            var selectedDP = "";
            for (var j = 0; j < checkedSlipsDP.length; j++) {
                if (checkedSlipsDP[j].checked) {
                    if(selectedDP == "") {
                        selectedDP = checkedSlipsDP[j].value;
                    } else {
                        selectedDP = selectedDP + "," + checkedSlipsDP[j].value;
                    }
                }
            }
            
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

            //var selected = 'NONE';
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
            
            if (checkedSettle.checked == true ) {
                    var selectedSettle = 1;
                } else {
                    var selectedSettle = 0;
                }
        
            setSlipHandling_settle(stockpileId, vendorHandlingId, selectedSettle, contractHandling, selected, selectedDP, ppnValue, pphValue, paymentFromHP, paymentToHP);
    }
//END HANDLING
    
function resetVendorFreight(text) {
    document.getElementById('vendorFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorFreightId').options.add(x);
    }


    function setVendorFreight(type, stockpileId2, freightId, vendorFreightId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getVendorFreight',
                    stockpileId: stockpileId2,
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

    function setPaymentType(type, paymentType) {
        document.getElementById('paymentType').value = 2;
    }

    function setPaymentMethod(type, paymentMethod) {
        document.getElementById('paymentMethod').value = 3;
    }

	function setStockpileLocation(stockpileIdVal, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setStockpileLocation',
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

    function CompareDate(){
        //var date1 = document.getElementById('invoiceDate').value;
        //var date2 = document.getElementById('todayDate').value;
        //if(date1 > date2){
         //   alert("tanggal tidak boleh lebih besar dari hari ini")
       // }
    }
</script>

<form method="post" id="settle100persenDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="settle100persen" />
    <input type="hidden" name="_method" value="<?php echo $method; ?>">
    <input type="hidden" id="paymentForVal" value="<?php echo $paymentFor ?>" />
    <input type="hidden" id="stockpileContractIdVal" value="<?php echo $stockpileContractId ?>" />
    <input type="hidden" id="paymentTypeVal" value="<?php echo $paymentType ?>" />
    <input type="hidden" id="vendorFreightIds" value="<?php echo $vendorFreightIds ?>" />
    <input type="hidden" id="InvNotim" name="InvNotim" value="<?php echo $InvNotim ?>" />
    
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Metode Pembayaran<span style="color: red;">*</span> </label>
            <?php
                createCombo("SELECT '3' as id, 'Payment' as info;", $paymentMethod, "", "paymentMethod", "id", "info",
                "","","",1);
            ?>
        </div>

        <div class="span3 lightblue">
            <label>Jenis Pembayaran<span style="color: red;">*</span> </label>
            <?php
                createCombo("SELECT '2' as id, 'OUT / Debit' as info;", $paymentType, "", "paymentType", "id", "info",
            "");
            ?>
        </div>

        <div class="span3 lightblue">
            <label id="stockpileLocationLabel">Stockpile Location <span style="color: red;">*</span></label>
            <div id="stockpileLocationDiv">
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>OA/OB/Hand<span style="color: red;">*</span></label>
                <?php   
                createCombo("SELECT type_transaction_id, type_transaction_name
                            FROM type_transaction", '', "", "paymentFor", "type_transaction_id", "type_transaction_name",
                    "", 3, "",5);
                ?>
        </div>

        <div class="span3 lightblue">
            <label>File Invoice<span style="color: red;">*</span> </label>
            <?php if($file_invoice != ''){ ?>
                <a href="<?php echo $file_invoice; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
            <?php } ?>
        </div>

        <div class="span3 lightblue">
            <label>Upload File Settle<span style="color: red;">*</span> </label>
            <input type="file" placeholder="File" tabindex="" id="file" name="file" class="span12" value="<?php echo $file_invoice; ?>" >
        </div>
    </div>

<!-- FREIGHT PAYMENT SETTLE 100%--> 
    <div class="row-fluid" id="freightPaymentSetlemen" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom" name="paymentFrom" value = "<?php echo $todayDate; ?>"  data-date-format="dd/mm/yyyy" class="datepicker"  >
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo" name="paymentTo" value = "<?php echo $todayDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>

            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php

                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                        FROM user_stockpile us
                                        INNER JOIN stockpile s
                                            ON s.stockpile_id = us.stockpile_id
                                        WHERE us.user_id = {$_SESSION['userId']}
                                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId2'], "", "stockpileId2", "stockpile_id", "stockpile_full",
                "", 14, "select2combobox100");
            
            
                ?>
            </div>
		</div>

		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "freightId", "freight_id", "freight_supplier",
                        "", 15, "select2combobox100", 2);
                    ?>
            </div>

            <div class="span3 lightblue" >
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                        createCombo("", "", "", "freightBankId", "f_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                    ?>
            </div>
      
            <!-- ini untuk get vendorFreightId dan set slipFreight  -->
            <div class="span3 lightblue" id = "supplier">
                <labe >Supplier <span style="color: red;">*</span></label>
                    <?php
                
                        createCombo("", "", "", "vendorFreightId", "vendor_id", "vendor_freight",
                            "", 15, "select2combobox100", 7);
                    ?>
            </div>

            <div class="span3 lightblue">
                <label>No PKS Kontrak <span style="color: red;">*</span></label>
                    <?php
                        
                        createCombo("", "", "", "contractNo", "contract_id", "contract_no",
                            "", 15, "select2combobox100", 7, "multiple");
                
                    ?>
            </div>

        </div>
    </div>
<!-- END FREIGHT SETTLEMENT 50% -->

<!-- UNLOADIN SETTLE-->
    <div class="row-fluid" id="unloadingPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">* </span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP" value="<?php echo $todayDate; ?>" name="paymentFromUP"  data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">* </span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP" value = "<?php echo $todayDate ?>" name="paymentToUP" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
		</div>

		<div class="row-fluid">
		    <div class="span4 lightblue">
		        <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId3'], "", "stockpileId3", "stockpile_id", "stockpile_full",
                        "", 16,"select2combobox100");

                 ?>
            </div>

		    <div class="span4 lightblue">
		        <label>Labor <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "laborId", "labor_id", "labor_name",
                        "", 15, "select2combobox100", 2);
                    
                ?>
            </div>

            <div class="span4 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "laborBankId", "l_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);
               ?>
            </div>

		</div>
    </div>
    <!-- END UNLOAING PAY -->

    <!-- HANDLING -->
    <div class="row-fluid" id="handlingPaymentSettle" style="display: none;">
		<div class="row-fluid">
            <div class="span1 lightblue">
                <label>Periode<span style="color: red;">*</span> From</label>
            </div>
            <div class="span3 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromHP" name="paymentFromHP" value = "<?php echo $todayDate; ?>"  data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>

            <div class="span1 lightblue">
                <label>Periode <span style="color: red;">*</span> To</label>
            </div>
            <div class="span3 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToHP" name="paymentToHP" value = "<?php echo $todayDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
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
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC",  $_SESSION['payment']['stockpileId4'], "", "stockpileId4", "stockpile_id", "stockpile_full",
                        "", 16,"select2combobox100");
                 ?>
            </div>

            <div class="span3 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                        createCombo("", "", "", "vendorHandlingId", "vendor_handling_id", "vendor_handling",
                            "", 15, "select2combobox100", 2);
                ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                        createCombo("", "", "", "vendorHandlingBankId", "vh_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                            
                    ?>
            </div>

            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "contractHandling", "contract_id", "contract_no",
                            "", 15, "select2combobox100", 2, "multiple");
                    ?>
            </div>
		</div>
    </div>
<!-- END HANDLING -->

	</br>
    <!-- DETAIL VENDOR -->
    <div class="row-fluid" id="slipPayment" style="display: none;">
        slip
    </div>

    <div class="row-fluid" id="summaryPayment" style="display: none;">
        summary
    </div>

    <div class="row-fluid">
        <!-- <div class="span3 lightblue">
            <label>From/To <span style="color: red;">*</span></label>
            <input type="hidden" name="currencyId" id="currencyId" />
            <input type="hidden" name="journalCurrencyId" id="journalCurrencyId" value="1" />
            <input type="hidden" name="bankCurrencyId" id="bankCurrencyId" value="<?php echo $_SESSION['payment']['bankCurrencyId']; ?>" />

            <?php
                // createCombo("SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full
                //         FROM bank b
                //         INNER JOIN currency cur
                //             ON cur.currency_id = b.currency_id
                //         ORDER BY b.bank_name ASC, cur.currency_code ASC, b.bank_account_name", $bankId, "", "bankId", "bank_id", "bank_full",
                //     "", 18, "select2combobox100", 1, "");
            ?>
        </div> -->

        <div class="span3 lightblue" id="amount2" >
            <label>Amount<span style="color: red;">*</span></label>
                <input type="text" class="span12" readonly tabindex="" id="amount" name="amount" > <!--value="<?php echo $_SESSION['payment']['amount']; ?>" -->
        </div>

        <div class="span3 lightblue">
            <label>Penerima<span style="color: red;">*</span></label>
            <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary">
            <input type="hidden" readonly class="span12" tabindex="" id="bank" name="bank">
            <input type="hidden" readonly class="span12" tabindex="" id="rek" name="rek">
            <input type="hidden" readonly class="span12" tabindex="" id="swift" name="swift">
        </div>

        <!-- <div class="span3 lightblue" id="inputExchangeRate" style="display: none;">
            <label>ExchangeRate<span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $kurs; ?>" />
        </div> -->
       
    </div>

    <div class="row-fluid">
        <div class="span1 lightblue">
            <label>Catatan</label>
        </div>
        <div class="span6 lightblue">
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>

        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton2">Kirim</button>
        </div>
    </div>
</form>


