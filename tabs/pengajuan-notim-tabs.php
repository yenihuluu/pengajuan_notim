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
$qty = 0;
$price = 0;
$termin = 0;
$dpp = 0;
$contractId = '';
$laborId = '';

$whereproperty = '';
$readonly = '';
$disableProperty1 = '';
$checkedPpn = '';
$checkedPph = '';

//UNTUK GET DATA STOCKPILE LOCATION
$sql = "SELECT sp.stockpile_id,  CONCAT(sp.stockpile_code, ' - ', sp.stockpile_name) AS stockpile_full 
        FROM user_stockpile us
        LEFT JOIN stockpile sp ON us.stockpile_id = sp.stockpile_id 
        WHERE user_id = {$_SESSION['userId']}";
//    echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
$count = $result->num_rows;
if($result !== false) {
    $row = $result->fetch_object();
    if($count == 1){
        $stockpileLocationId = $row->stockpile_id;
        $stockpileLocationText = $row->stockpile_full;
    }else{
        $stockpileLocationId = $row->stockpile_id;
        
    }
   // echo $stockpileLocationId;
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT style = 'width:80%;' class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

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
if(isset($_POST['idPP']) && $_POST['idPP'] != '') {
    $idPP = $_POST['idPP'];

    $sqlPP = "SELECT CASE WHEN pp.payment_method  = 1 THEN 'Payment'
                        WHEN pp.payment_method = 3 THEN 'Final Payment'
                     ELSE 'Down Payment' END AS paymentMethod,
                ty.type_transaction_name AS paymentFor,
                sp.stockpile_name,
                spl.stockpile_name as spNameLocation,
                f.freight_supplier,
                lab.labor_name,
                vh.vendor_handling_name,
                v.vendor_name AS vendorName,
                CONCAT(vb.bank_name,' - ',vb.account_no) AS vbank,
                CONCAT(lbank.bank_name,' - ',lbank.account_no) AS lbank,
                CONCAT(fb.bank_name,' - ',fb.account_no) AS fbank,
                CONCAT(vhb.bank_name,' - ',vhb.account_no) AS vhbank, 
                DATE_FORMAT(pp.urgent_payment_date, '%d/%m/%Y') AS tglReguest, 
                DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, 
                    DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo,
                DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoiceDate, 
                pp.* 
            FROM pengajuan_payment pp
            LEFT JOIN type_transaction ty ON ty.type_transaction_id = pp.payment_for
            LEFT JOIN pengajuan_payment_supplier pps ON pps.idpp = pp.idpp
            LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
            LEFT JOIN stockpile spl ON spl.stockpile_id = pp.stockpile_location
            LEFT JOIN vendor v ON v.vendor_id = pp.vendor_id
            LEFT JOIN vendor_bank vb ON vb.vendor_id = pp.vendor_id
            LEFT JOIN freight f ON f.freight_id = pp.freight_id
            LEFT JOIN freight_bank fb ON fb.freight_id = pp.freight_id
            LEFT JOIN labor lab ON lab.labor_id = pp.labor_id
            LEFT JOIN labor_bank lbank ON lbank.labor_id = pp.labor_id
            LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id
            LEFT JOIN vendor_handling_bank vhb ON vhb.vendor_handling_id = pp.vendor_handling_id
            WHERE pp.idPP = {$idPP}";
        //echo $sqlPP;
    $resultPP = $myDatabase->query($sqlPP, MYSQLI_STORE_RESULT);

    if ($resultPP !== false && $resultPP->num_rows > 0) {
        $rowDataPP = $resultPP->fetch_object();
        $paymentMethod = $rowDataPP->payment_method;
        $paymentMethods = $rowDataPP->paymentMethod;
        $invoiceNo = $rowDataPP->invoice_no;
        $invoiceDate = $rowDataPP->invoiceDate;
        $taxInvoice = $rowDataPP->tax_invoice;
        $emailDate = $rowDataPP->email_date;
        $file_invoice = $rowDataPP->file;
        $stockpileLocationId = $rowDataPP->stockpile_location;
        $stockpileLocationText = $rowDataPP->spNameLocation;
        $methodId = $rowDataPP->payment_method;
        $methodText = $rowDataPP->paymentMethod;
        $paymentType = $rowDataPP->payment_type;
        $paymentForId = $rowDataPP->payment_for;
        $paymentForText = $rowDataPP->paymentFor;
        $tipeBayar = $rowDataPP->urgent_payment_type;
        $requestPaymentDate = $rowDataPP->tglReguest;
        $pStatus = $rowDataPP->status;
        $vendorBankId = $rowDataPP->vendor_bank_id;
        $beneficiary = $rowDataPP->beneficiary;
        $amount = number_format($rowDataPP->grand_total, 2, ".", ",");
        $remarks = $rowDataPP->remarks;
        $count = 1;
        $reject_remarks = $rowDataPP->reject_remarks;

        $paymentFrom = $rowDataPP->dateFrom;
        $paymentTo = $rowDataPP->dateTo;

        //CURAH
        $stockpileIdCurahId = $rowDataPP->stockpile_id;
        $stockpileIdCurahText = $rowDataPP->stockpile_name;
        $vendorId = $rowDataPP->vendor_id;
        $vendorText = $rowDataPP->vendorName;
        $curahBankText = $rowDataPP->vbank;

        //FREIGHT PAYMENT
        $stockpileIdFreightId = $rowDataPP->stockpile_id;
        $stockpileIdFreightText = $rowDataPP->stockpile_name;
        $freightId = $rowDataPP->freight_id;
        $freightName = $rowDataPP->freight_supplier;
        $freightbankText = $rowDataPP->fbank;

        //HANDLING PAYMENT
        $stockpileIdHandlingId = $rowDataPP->stockpile_id;
        $stockpileIdHandlingText = $rowDataPP->stockpile_name;
        $vendorHandlingId = $rowDataPP->vendor_handling_id;
        $vendorHandlingText = $rowDataPP->vendor_handling_name;
        $vhbank = $rowDataPP->vhbank;

        //UNLOADING
        $stockpileOBId = $rowDataPP->stockpile_id;
        $stockpileOBText = $rowDataPP->stockpile_name;
        $laborId = $rowDataPP->labor_id;
        $laborText = $rowDataPP->labor_name;
        $lbank = $rowDataPP->lbank;

        //DOWN PAYMENT
        $qtyDp = number_format($rowDataPP->total_qty, 2, ".", ",");
        $priceDp = number_format($rowDataPP->price, 2, ".", ",");
        $terminDp = number_format($rowDataPP->termin, 2, ".", ",");
        $originalAmountDp = number_format($rowDataPP->total_dpp, 2, ".", ",");

        if($emailDate != ''){
            $disableProperty = 'disabled';
            $readonly = 'readonly';
        }else if($pStatus == 2){
            $disableProperty = 'disabled';
        }else {
            $disableProperty = 'enabled';
        }

        //KEPERLUAN DI VIEW DP
        if($rowDataPP->status_ppn == 1){
            $checkedPpn ='checked = "checked"';
        }

        if($rowDataPP->status_pph == 1){
            $checkedPph ='checked = "checked"';
        }
    }

    if(($paymentForId == 2 || $paymentForId == 9 || $paymentForId == 1) && $idPP != ''){
        $sqlContract = "SELECT ppc.contract_id FROM pengajuan_pks_contract ppc WHERE ppc.idPP = {$idPP}";
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

    // $sql2 = "SELECT * FROM TRANSACTION WHERE {$whereproperty}";
    // $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    // if ($result2 !== false && $result2->num_rows > 0) {
    //     while( $row2 = $result2->fetch_object()){
    //         $transaksiID = $row2->transaction_id;
    //     }   
    // }
    $method = 'UPDATE';
    $disableProperty1 = 'disabled';
    $readonly = 'readonly';
    $button = 'UPDATE';
}else{
    $method = 'INSERT';
    $checkedPpn ='checked = "checked"';
    $checkedPph ='checked = "checked"';
    $temp = 0;
    $button = 'SUBMIT';

}

?>

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
        $('#ppn').number(true, 2);
        $('#pph').number(true, 2);

        $('#qtyFreight').number(true, 2); 
        $('#priceFreight').number(true, 2);

        $('#qtyLabor').number(true, 2);
        $('#priceLabor').number(true, 2);

        
        $('#qtyCurah').number(true, 2);
        $('#priceCurah').number(true, 2);

        $('#qtyHandlingDP').number(true, 2);
        $('#priceHandlingDP').number(true, 2);
        $('#tglBayarDiv').hide();
        $('#amount1').hide();
        $('#amount2').show();
        $('#paymentType').attr("readonly", true); 

        <?php if($emailDate != ''){ ?>
            $('#tipeBayar').attr("readonly", true); 
        <?php } ?>

    
    // setStockpileLocation(); //DIPINDAH KESINI
    setPaymentType(1, <?php echo $paymentType; ?>);
    <?php if($idPP != ''){ ?>
        $('#amount1').show();
        $('#amount2').hide();
    <?php } ?>

      //CURAH
    <?php if ($paymentForId == 1) { //GET DATA FROM ADMIN 
        ?>
        <?php if ($paymentMethod == 2) { ?> //downpayment
            $('#curahPayment').hide();
            $('#curahDownPayment').show();
            // setSlipDp(<?php echo $idPP; ?>);
            setVendorCurah(1, <?php echo $stockpileIdCurahId ?>, <?php echo $vendorId ?>);
            getCurahTax(<?php echo $stockpileIdCurahId ?>);
            getVendorBank(1,<?php echo $vendorId ?>, <?php echo $paymentForId ?>, <?php echo $vendorBankId ?>);
            setContractCurah(1, <?php echo $stockpileIdCurahId ?>, <?php echo $vendorId ?>, <?php echo $contractIds ?>);
            getVendorBankDetail(<?php echo $vendorBankId ?>, <?php echo $paymentForId ?>);
        <?php } else if ($paymentMethod != 2) { ?> //payment
            $('#curahPayment').show();
            $('#curahDownPayment').hide();
            document.getElementById("paymentFromCur").disabled = true;
            document.getElementById("paymentToCur").disabled = true;
            <?php if($paymentMethod == 1){ ?>
            setSlipCurah(<?php echo $stockpileIdCurahId ?>, <?php echo $vendorId ?>, '', '', 'NONE', 'NONE', '', '', <?php echo $idPP ?>);
       <?php } else {?>
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
    <?php if($idPP != '' && $paymentForId == 2){ //GET DATA FROM ADMIN ?> 
        getVendorBankDetail(<?php echo $vendorBankId ?>, <?php echo $paymentForId ?>);
        <?php if($paymentMethod == 2) {?>
            $('#freightPayment1').hide();
            $('#freightDownPayment').show();
            setSupplier(1, <?php echo $stockpileIdFreightId ?>, <?php echo $freightId ?>, <?php echo $vendorId ?>);
            getFreightTax(<?php echo $freightId ?>);
            setVendorFreight(1, <?php echo $stockpileIdFreightId ?>, <?php echo $freightId ?>);
            getVendorBank(1,<?php echo $freightId ?>, <?php echo $paymentForId ?>, <?php echo $vendorBankId ?>);
            setContractFreightDp(1, <?php echo $stockpileIdFreightId ?>, <?php echo $freightId ?>, <?php echo $vendorId ?>, <?php echo $contractIds ?>);
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
            <?php } ?>
        <?php }?>
       
        <?php if($tipeBayar == 1) { ?>
            $('#tglBayarDiv').show(); 
        <?php }else{ ?>
            $('#tglBayarDiv').hide(); 
    <?php }
    }?>

    //UNLOADING C
    <?php if ($paymentForId == 3) { //GET DATA FROM ADMIN 
    ?>
    <?php if ($paymentMethod == 2) { ?> //downpayment
        $('#unloadingPayment').hide();
        $('#unloadingDownPayment').show();
        // setSlipDp(<?php echo $idPP; ?>);
        setLaborDp(1, <?php echo $stockpileOBId; ?>, <?php echo $laborId; ?>);
        getVendorBank(1,<?php echo $laborId ?>, <?php echo $paymentForId ?>, <?php echo $vendorBankId ?>);
        getLaborTax(<?php echo $laborId ?>);
        getVendorBankDetail(<?php echo $vendorBankId ?>, <?php echo $paymentForId ?>);
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

    //HANDLING
    <?php if ($paymentForId == 9) { //GET DATA FROM ADMIN 
    ?>
        <?php if ($paymentMethod == 2) { ?> //downpayment
            $('#handlingPayment').hide();
            $('#handlingDownPayment').show();
            // setSlipDp(<?php echo $idPP; ?>);
            setVendorHandling(1,<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId ?>);
            getVendorBank(1,<?php echo $vendorHandlingId ?>, <?php echo $paymentForId ?>, <?php echo $vendorBankId ?>);
            getHandlingTax(<?php echo $vendorHandlingId ?>);
            setContractHandling(1,<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId ?>, <?php echo $contractIds ?>);
            getVendorBankDetail(<?php echo $vendorBankId ?>, <?php echo $paymentForId ?>);
        <?php } else if ($paymentMethod != 2) { ?> //payment
            $('#handlingPayment').show(); 
            $('#handlingDownPayment').hide();
            document.getElementById("paymentFromHP").disabled = true;
            document.getElementById("paymentToHP").disabled = true;
            <?php if($paymentMethod == 1) {?>
                setSlipHandling(<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId?>, '','', 'NONE', 'NONE', '','',<?php echo $idPP ?>);
            <?php } else {?>
                updatePengajuanPayment(<?php echo $idPP ?>, <?php echo $paymentForId ?>);
                 setSlipHandling_settle(<?php echo $stockpileIdHandlingId ?>, <?php echo $vendorHandlingId?>, '','', '','','NONE','NONE', '', '', <?php echo $idPP ?>);

        <?php }
        } ?>

        <?php if ($tipeBayar == 1) { ?>
        $('#tglBayarDiv').show();
        <?php } else { ?>
            $('#tglBayarDiv').hide();
    <?php }
    } ?>

    <?php if(isset($tipeBayar) && $tipeBayar == 1) { ?>
        $('#divrequestPaymentDate').show();
        $('#requestPaymentDate1').show();
        $('#requestPaymentDate').hide();
    <?php }else if(isset($tipeBayar) && ($tipeBayar == 0)) {?>
        $('#divrequestPaymentDate').show();
        $('#requestPaymentDate1').hide();
        $('#requestPaymentDate').show();
    <?php }else{ ?>
        setnormalpayment(1, <?php echo $temp; ?>);
    <?php $tipeBayar = $temp; } ?>

     $('#divInvoice').show();
     $('#divPaymentType1').hide()
     $('#tglBayarDiv').hide();

     $('#tipeBayar').change(function () {
       // console.log(this.value)
       $('#divrequestPaymentDate').show();
        if (this.value == 1) {
            $('#requestPaymentDate1').show();
            $('#requestPaymentDate').hide();
        } else if (this.value == 0) {
            reqpaymentdate();
        }
    });

     $('#paymentMethod').change(function() {
            // resetPaymentType(' Method ');
            // resetPaymentFor(' Type ');
			resetBankDetail(' ');
            $('#curahPayment').hide();
            $('#curahDownPayment').hide();
            $('#freightDownPayment').hide();
            // $('#freightPayment').hide();
			$('#freightPayment1').hide();
			$('#handlingPayment').hide();
            $('#handlingDownPayment').hide();
            $('#unloadingPayment').hide();
            $('#unloadingDownPayment').hide();
            document.getElementById('stockpileIdCurah').value = '';
            resetVendorCurah(' Stockpile ');
			document.getElementById('stockpileIdHandling').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileIdFreight').value = '';
            resetSupplier(' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileOB').value = '';
            resetLabor(' Stockpile ');
            $('#divAmount').show();
            // $('#divInvoice').hide();
			$('#slipPayment').hide();
            $('#summaryPayment').hide();
            // setPaymentType(0, 0);
			// setStockpileLocation();
            if(document.getElementById('paymentMethod').value == 1) {
                $('#divPaymentType1').show();
            }else {
                $('#divPaymentType1').hide();

            }
        });

        $('#paymentType').change(function() {
            // resetPaymentFor(' Type ');
			resetBankDetail(' ');
            $('#curahPayment').hide();
            $('#curahDownPayment').hide();
            $('#freightDownPayment').hide();
			$('#freightPayment1').hide();
			$('#handlingPayment').hide();
            $('#handlingDownPayment').hide();
            $('#unloadingPayment').hide();
            $('#unloadingDownPayment').hide();
            document.getElementById('stockpileIdCurah').value = '';
            resetVendorCurah(' Stockpile ');
			document.getElementById('stockpileIdHandling').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
			document.getElementById('stockpileIdHandling').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileIdFreight').value = '';
            resetSupplier( ' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileOB').value = '';
            resetLabor(' Stockpile ');
            $('#divAmount').show();
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            if(document.getElementById('paymentType').value != '') {
                // setPaymentFor($('select[id="paymentType"]').val());
                // setStockpileLocation();
            }
        });


        $('#paymentFor').change(function() {
            document.getElementById('stockpileIdCurah').value = '';
            resetVendorCurah('Stockpile ');
			resetBankDetail(' ');
			document.getElementById('stockpileIdHandling').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileIdFreight').value = '';
            resetSupplier(' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileOB').value = '';
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            $('#curahPayment').hide();
            $('#curahDownPayment').hide();
            $('#freightDownPayment').hide();
            // $('#freightPayment').hide();
			$('#freightPayment1').hide();
			$('#handlingPayment').hide();
            $('#handlingDownPayment').hide();
            $('#unloadingPayment').hide();
            $('#unloadingDownPayment').hide();
            if(document.getElementById('paymentFor').value != '') {
                if(document.getElementById('paymentType').value == 2) {
                    // OUT
                    if(document.getElementById('paymentFor').value == 1) {
                        
                        $('#freightDownPayment').hide();
                        $('#handlingDownPayment').hide();
                        $('#unloadingDownPayment').hide();
                        $('#unloadingPayment').hide();
                        $('#freightPayment1').hide();
                        if(document.getElementById('paymentMethod').value == 2){
                            $('#curahDownPayment').show();
                            $('#curahPayment').hide();
                        }else{
                            $('#curahDownPayment').hide();
                            $('#curahPayment').show();
                        }
                    } else if(document.getElementById('paymentFor').value == 2) {
						if(document.getElementById('paymentMethod').value == 2){
							$('#freightDownPayment').show();
                            $('#freightPayment1').hide();
						}else{
							//$('#freightPayment').show();
							$('#freightPayment1').show();
                            $('#freightDownPayment').hide();
						}
                        $('#handlingPayment').hide();
                        $('#handlingDownPayment').hide();
                        $('#unloadingDownPayment').hide();
                        $('#unloadingPayment').hide();
                        $('#curahPayment').hide();
                    } else if(document.getElementById('paymentFor').value == 9) {
                        $('#freightDownPayment').hide();
                        $('#freightPayment1').hide();
						if(document.getElementById('paymentMethod').value == 2){
                            $('#handlingPayment').hide();
                            $('#handlingDownPayment').show();
                        }else {
                            $('#handlingDownPayment').hide();
                            $('#handlingPayment').show();
                        }
                        $('#unloadingDownPayment').hide();
                        $('#unloadingPayment').hide();
                        $('#freightDownPayment').hide();
                        $('#freightPayment1').hide();
                        $('#curahPayment').hide();
                    } else if(document.getElementById('paymentFor').value == 3) {
						if(document.getElementById('paymentMethod').value == 2){
							$('#unloadingDownPayment').show();
                            $('#unloadingPayment').hide();
						}else{
							$('#unloadingPayment').show();
                            $('#unloadingDownPayment').hide();
						}
                        $('#handlingPayment').hide();
                        $('#handlingDownPayment').hide();
                        $('#freightDownPayment').hide();
                        $('#freightPayment1').hide();
                        $('#curahPayment').hide();
                    }    
                } 
            } 
        });


        $('#invoiceId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('invoiceId').value != '') {
                    refreshInvoice($('select[id="invoiceId"]').val(), $('select[id="paymentMethod"]').val(), 'NONE', 'NONE');
            }
        });

        $('#invoiceDate').change(function() {
           // CompareDate();
        });

        $('#invoiceNo').change(function() {
            getInvoiceNotim($('input[id="invoiceNo"]').val());
        });

    //CURAH PAYMENT
    $('#stockpileIdCurah').change(function() {
        resetVendorCurah(' Stockpile ');
        $('#slipPayment').hide();
        $('#summaryPayment').hide();

        if(document.getElementById('stockpileIdCurah').value != '') {
            resetVendorCurah(' ');
            setVendorCurah(0, $('select[id="stockpileIdCurah"]').val(), 0);
        }
    });

    $('#vendorId').change(function() {
        $('#slipPayment').hide();
        $('#summaryPayment').hide();

        if(document.getElementById('vendorId').value != '') {
                // setSlipCurah($('select[id="stockpileIdCurah"]').val(), $('select[id="vendorId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFrom1"]').val(),$('input[id="paymentTo1"]').val());
				getVendorBank(0,$('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val(), 0);
                setContractCurah(0, $('select[id="stockpileIdCurah"]').val(), $('select[id="vendorId"]').val(), 0);
        }
    });
    
    $('#contractCurah').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('vendorId').value != '') {
                if(document.getElementById('paymentMethod').value == 1){
                setSlipCurah($('select[id="stockpileIdCurah"]').val(), $('select[id="vendorId"]').val(), $('select[id="contractCurah"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromCur"]').val(), $('input[id="paymentToCur"]').val());
                }else{
                    setSlipCurah_settle($('select[id="stockpileIdCurah"]').val(), $('select[id="vendorId"]').val(), '', $('select[id="contractCurah"]').val(), '', '','NONE', 'NONE', $('input[id="paymentFromCur"]').val(), $('input[id="paymentToCur"]').val(), '');

                }
            }
        });

	$('#curahBankId').change(function() {
	    resetVendorBankDetail ($('select[id="paymentFor"]').val());
        if(document.getElementById('curahBankId').value != '') {
		    getVendorBankDetail($('select[id="curahBankId"]').val(), $('select[id="paymentFor"]').val());
				
        } 
    });
    // END CURAH

    //CURAH DP
    $('#stockpileIdCurahDp').change(function () {
        resetVendorCurah('vendorIdCurahDp', ' Stockpile ');
        $('#slipPayment').hide();
        $('#summaryPayment').hide();

        if (document.getElementById('stockpileIdCurahDp').value != '') {
            resetVendorCurah('vendorIdCurahDp', ' ');
            setVendorCurah(0, $('select[id="stockpileIdCurahDp"]').val(), 0);
        }
    });

    $('#vendorIdCurahDp').change(function() {
        $('#slipPayment').hide();
        $('#summaryPayment').hide();

        if(this.value != '') {
            getCurahTax($('select[id="vendorIdCurahDp"]').val())
            <?php if($idPP != ''){ ?>
                getVendorBank(0,$('select[id="vendorIdCurahDp"]').val(), $('input[id="paymentFor"]').val(), 0);
            <?php }else { ?>
                getVendorBank(0,$('select[id="vendorIdCurahDp"]').val(), $('select[id="paymentFor"]').val(), 0);
            <?php } ?>
            setContractCurah(0, $('select[id="stockpileIdCurahDp"]').val(), $('select[id="vendorIdCurahDp"]').val(), 0);
            
        }
    });

    $('#curahBankDp').change(function() {
	    resetVendorBankDetail ($('select[id="paymentFor"]').val());
        if(this.value != '') {
            <?php if($idPP != ''){ ?>
		    getVendorBankDetail($('select[id="curahBankDp"]').val(), $('input[id="paymentFor"]').val());
            <?php }else { ?>
                getVendorBankDetail($('select[id="curahBankDp"]').val(), $('select[id="paymentFor"]').val());
            <?php } ?>
        } 
    });

//DP Freigth
    $('#stockpileIdFcDp').change(function() {
            resetSupplier('freightIdFcDp', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorFreight(' ');
            if(document.getElementById('stockpileIdFcDp').value != '') {
                setVendorFreight(0, $('select[id="stockpileIdFcDp"]').val(), 0);
            }
    });

        $('#freightIdFcDp').change(function() {
			resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetVendorBank ($('select[id="paymentFor"]').val());
            resetSupplier(' ');
            resetgetFreightTax('');
            if(document.getElementById('freightIdFcDp').value != '') {
                setSupplier(0, $('select[id="stockpileIdFcDp"]').val(), $('select[id="freightIdFcDp"]').val(), 0);
                getFreightTax($('select[id="freightIdFcDp"]').val())
                <?php if($idPP != ''){ ?>
                getVendorBank(0,$('select[id="freightIdFcDp"]').val(), $('input[id="paymentFor"]').val(), 0);
               <?php }else{ ?>
                    getVendorBank(0,$('select[id="freightIdFcDp"]').val(), $('select[id="paymentFor"]').val(), 0);

               <?php  } ?>

            }
        });

		$('#supplierIdDp').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
             resetContractFreightDp(' ');
            if(this.value != '') {
                setContractFreightDp(0, $('select[id="stockpileIdFcDp"]').val(), $('select[id="freightIdFcDp"]').val(), $('select[id="supplierIdDp"]').val(), 0);
            }
        });

        $("#qtyCurah, #priceCurah").keyup( 
                function(){
                    var statusPpn = document.getElementById('ppnStatusC').value;
                    var statusPph = document.getElementById('pphStatusC').value;
                    var a = parseFloat($("#qtyCurah").val());
                    var b = parseFloat($("#priceCurah").val());
                    var c = parseFloat(a) * parseFloat(b); 
                    $("#originalAmountDp").val(parseFloat(a) * parseFloat(b));

                    if(statusPpn == 1){
                        var d = (parseFloat($("#ppnC").val())/100) * parseFloat(c); 
                    }else{
                        var d = 0; 
                    }

                    if(statusPph == 1){
                        var e = (parseFloat($("#pphC").val())/100) * parseFloat(c); 
                    }else{
                        var e = 0;
                    }
                    var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
                    $("#amount").val(c);  
                         
                }
        );


        $("#qtyFreight, #priceFreight").keyup( 
                function(){
                    var statusPpn = document.getElementById('ppnStatus').value;
                    var statusPph = document.getElementById('pphStatus').value;
                    var a = parseFloat($("#qtyFreight").val());
                    var b = parseFloat($("#priceFreight").val());
                    var c = parseFloat(a) * parseFloat(b); 
                    $("#originalAmountDp").val(parseFloat(a) * parseFloat(b));

                    if(statusPpn == 1){
                        var d = (parseFloat($("#ppn").val())/100) * parseFloat(c); 
                    }else{
                        var d = 0; 
                    }

                    if(statusPph == 1){
                        var e = (parseFloat($("#pph").val())/100) * parseFloat(c); 
                    }else{
                        var e = 0;
                    }
                    var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
                    $("#amount").val(c);  
                         
                }
        );

        $("#qtyLabor, #priceLabor").keyup( 
                function(){
                    var statusPpn = document.getElementById('ppnStatusOb').value;
                    var statusPph = document.getElementById('pphStatusOb').value;
                    var a = parseFloat($("#qtyLabor").val());
                    var b = parseFloat($("#priceLabor").val());
                    var c = parseFloat(a) * parseFloat(b); 
                    $("#originalAmountDp").val(parseFloat(a) * parseFloat(b));
                    if(statusPpn == 1){
                        var d = (parseFloat($("#ppnOB").val())/100) * parseFloat(c); 
                    }else{
                        var d = 0; 
                    }

                    if(statusPph == 1){
                        var e = (parseFloat($("#pphOB").val())/100) * parseFloat(c); 
                    }else{
                        var e = 0;
                    }
                    var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
                    $("#amount").val(c);    
                }
        );

        $("#priceHandlingDP, #qtyHandlingDP").keyup( 
                function(){
                    var statusPpn = document.getElementById('ppnStatusHC').value;
                    var statusPph = document.getElementById('pphStatusHC').value;
                    var a = parseFloat($("#qtyHandlingDP").val());
                    var b = parseFloat($("#priceHandlingDP").val());
                    var c = parseFloat(a) * parseFloat(b);  
                    $("#originalAmountDp").val(parseFloat(a) * parseFloat(b));

                    if(statusPpn == 1){
                        var d = (parseFloat($("#ppnH").val())/100) * parseFloat(c); 
                    }else{
                        var d = 0; 
                    }

                    if(statusPph == 1){
                        var e = (parseFloat($("#pphH").val())/100) * parseFloat(c); 
                    }else{
                        var e = 0;
                    }
                    var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);   
                    $("#amount").val(c);    
                }
        );

        $('#freightBankDp').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            if(document.getElementById('freightBankDp').value != '') {
                <?php if($idPP != ''){ ?>
				 getVendorBankDetail($('select[id="freightBankDp"]').val(), $('input[id="paymentFor"]').val());
                 <?php }else{ ?>
                    getVendorBankDetail($('select[id="freightBankDp"]').val(), $('select[id="paymentFor"]').val());
                <?php } ?>
                } 
        });
		

//CLAIM PAYMENT FREIGHT 
    $('#stockpileIdFreight').change(function() {
            resetSupplier('freightId_1', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if(document.getElementById('stockpileIdFreight').value != '') {
                resetSupplier(' ');
                setVendorFreight(0, $('select[id="stockpileIdFreight"]').val(), 0);
            }
    });

		$('#freightId_1').change(function() {
          //  resetVendorFreight(' ');
			resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetVendorBank ($('select[id="paymentFor"]').val());
            resetContractFreight(' ');
            if(document.getElementById('freightId_1').value != '') {
                setContractFreight(0, $('select[id="stockpileIdFreight"]').val(), $('select[id="freightId_1"]').val(), 0);
                getVendorBank(0,$('select[id="freightId_1"]').val(), $('select[id="paymentFor"]').val(), 0);
            } 
        });
		
		 $('#freightBankId_1').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());

            if(document.getElementById('freightBankId_1').value != '') {
				 getVendorBankDetail($('select[id="freightBankId_1"]').val(), $('select[id="paymentFor"]').val());
            }
        });

        $('#contractFreight').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            if(this.value != '' && document.getElementById('freightId_1').value != '') {
                if(document.getElementById('paymentMethod').value == 1){
                    setSlipFreight_1($('select[id="stockpileIdFreight"]').val(), $('select[id="freightId_1"]').val(), $('select[id="contractFreight"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFromFP"]').val(),$('input[id="paymentToFP"]').val());
                }else if (document.getElementById('paymentMethod').value == 3){
                    setSlipFreight_settle($('select[id="stockpileIdFreight"]').val(), $('select[id="freightId_1"]').val(), '',  $('select[id="contractFreight"]').val(), '', '', 'NONE', 'NONE', $('input[id="paymentFromFP"]').val(), $('input[id="paymentToFP"]').val(), $('input[id="idPP"]').val());
                }
            }
        });
//END FREIGHT


//HANDLING PAYMENT
		$('#stockpileIdHandling').change(function() {
            resetVendorHandling(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileIdHandling').value != '') {
                resetVendorHandling(' ');
                setVendorHandling(0, $('select[id="stockpileIdHandling"]').val(), 0);
            }
        });

       $('#vendorHandlingId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetVendorBank ($('select[id="paymentFor"]').val());
            resetContractHandling(' ');

            if(document.getElementById('vendorHandlingId').value != '') {
					getVendorBank(0,$('select[id="vendorHandlingId"]').val(), $('select[id="paymentFor"]').val(), 0);
                    setContractHandling(0, $('select[id="stockpileIdHandling"]').val(), $('select[id="vendorHandlingId"]').val());
            }
        });
        $('#contractHandling').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('vendorHandlingId').value != '') {
                if(document.getElementById('paymentMethod').value == 1){
                    setSlipHandling($('select[id="stockpileIdHandling"]').val(), $('select[id="vendorHandlingId"]').val(), $('select[id="contractHandling"]').val(),'', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val());
                }else if (document.getElementById('paymentMethod').value == 3){
                    setSlipHandling_settle($('select[id="stockpileIdHandling"]').val(), $('select[id="vendorHandlingId"]').val(), '', $('select[id="contractHandling"]').val(), '', '','NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val(), '');
                }
            }
        });

		$('#vendorHandlingBankId').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            if(document.getElementById('vendorHandlingBankId').value != '') {
				 getVendorBankDetail($('select[id="vendorHandlingBankId"]').val(), $('select[id="paymentFor"]').val());
            } 
        });

    //END HANDLING


    //HANDLING DP
    $('#stockpileVhDp').change(function() {
            resetVendorHandling(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileVhDp').value != '') {
                setVendorHandling(0, $('select[id="stockpileVhDp"]').val(), 0);
            }
    });

    $('#vendorHandlingDp').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            //resetBankDetail(' ');
            resetVendorBank ($('select[id="paymentFor"]').val());
            resetContractHandling(' ');

			//alert('html from to nya');
            resetgetHandlingTax('');
            if(document.getElementById('vendorHandlingDp').value != '') {
                 if(document.getElementById('paymentMethod').value == 2) {
                    <?php if($idPP != ''){ ?>
					getVendorBank(0,$('select[id="vendorHandlingDp"]').val(), $('input[id="paymentFor"]').val(), 0);
                    <?php }else{ ?>
                        getVendorBank(0,$('select[id="vendorHandlingDp"]').val(), $('select[id="paymentFor"]').val(), 0);
                    <?php  } ?>
                    setContractHandling(0, $('select[id="stockpileVhDp"]').val(), $('select[id="vendorHandlingDp"]').val());
                    getHandlingTax($('select[id="vendorHandlingDp"]').val());

                }
            }
    });

    $('#vendorHandlingBankDp').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            if(document.getElementById('vendorHandlingBankDp').value != '') {
                <?php if($idPP != ''){ ?>
                getVendorBankDetail($('select[id="vendorHandlingBankDp"]').val(), $('input[id="paymentFor"]').val());
                 <?php }else{ ?>
                    getVendorBankDetail($('select[id="vendorHandlingBankDp"]').val(), $('select[id="paymentFor"]').val());
                <?php  } ?>
                } 
        });



    // UNLOADING DOWN PAYMENT
    $('#stockpileLaborDp').change(function() {
            resetLaborDp(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileLaborDp').value != '') {
                resetLaborDp(' ');
                setLaborDp(0, $('select[id="stockpileLaborDp"]').val(), 0);
            }
    });


    $('#laborDp').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			resetVendorBank ($('select[id="paymentFor"]').val());
			//alert('html from to nya');
            resetgetLaborTax('');
            if(document.getElementById('laborDp').value != '') {
                 if(document.getElementById('paymentMethod').value == 2) {
                    <?php if($idPP != ''){ ?>
					getVendorBank(0,$('select[id="laborDp"]').val(), $('input[id="paymentFor"]').val(), 0);
                    <?php }else{ ?>
                        getVendorBank(0,$('select[id="laborDp"]').val(), $('select[id="paymentFor"]').val(), 0);
                    <?php  } ?>
                    getLaborTax($('select[id="laborDp"]').val());

            }
        }
    });

    $('#laborBankDp').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());

            if(this.value != '') {
                <?php if($idPP != ''){ ?>
				 getVendorBankDetail($('select[id="laborBankDp"]').val(), $('input[id="paymentFor"]').val());
                 <?php }else{ ?>
                    getVendorBankDetail($('select[id="laborBankDp"]').val(), $('select[id="paymentFor"]').val());
                <?php  } ?>
            }
    });

        //UNLOADING
       $('#stockpileOB').change(function() {
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if(document.getElementById('stockpileOB').value != '') {
                resetLabor(' ');
                setLabor(0, $('select[id="stockpileOB"]').val(), 0);
            }
        });

       $('#laborId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorBank(' ');
			//alert('html from to nya');

            if(document.getElementById('laborId').value != '') {
               if(document.getElementById('paymentMethod').value == 1) {
                    setSlipUnloading($('select[id="stockpileOB"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val());
                }else if(document.getElementById('paymentMethod').value == 3){
                    setSlipUnloading_settle($('select[id="stockpileOB"]').val(), $('select[id="laborId"]').val(), '', '', '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val(), '');
                }
                getVendorBank(0,$('select[id="laborId"]').val(), $('select[id="paymentFor"]').val());

            }
        });

		$('#laborBankId').change(function() {
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            if(document.getElementById('laborBankId').value != '') {
				 getVendorBankDetail($('select[id="laborBankId"]').val(), $('select[id="paymentFor"]').val());
            } 
        });
    //END UNLOADING

	    $('#tipeBayar').change(function() {
		if(document.getElementById('tipeBayar').value == 1){
            $('#tglBayarDiv').show();
        }else{
            $('#tglBayarDiv').hide();
        }
    });

        //SUBMIT FORM
        $("#PengajuanPaymentDataForm").submit(function (e) {
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
                            $('#pageContent').load('views/pengajuan-notim-views.php', {}, iAmACallbackFunction);
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
                    tglBayarUrgent : "required",
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
                    tglBayarUrgent: "Request payment Date is a required field "

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

    function setnormalpayment(type, normal) {
        document.getElementById('tipeBayar').value = 0;
        $('#divrequestPaymentDate').show();
        reqpaymentdate();
    }

    function reqpaymentdate() {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                    action: 'setPlanPayDate',
            },
            success: function (data) {
                var returnVal = data.split('|');
                $('#requestPaymentDate').show();
                $('#requestPaymentDate1').hide();
                $('#requestPaymentDate').val(returnVal[1]);
            }
        });
    }

    function resetVendorBank(paymentFor) {
		//alert (paymentFor);
        if(paymentFor == 1){
			document.getElementById('curahBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('curahBankId').options.add(x);
		}else if(paymentFor == 2){
            if(document.getElementById('paymentMethod').value != 2){ 
			    document.getElementById('freightBankId_1').options.length = 0;
            }else if(document.getElementById('paymentMethod').value == 2){  
                document.getElementById('freightBankDp').options.length = 0;
            }
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
            if(document.getElementById('paymentMethod').value != 2){  
			    document.getElementById('freightBankId_1').options.add(x);
            }else if(document.getElementById('paymentMethod').value == 2){  
                document.getElementById('freightBankDp').options.add(x);
            }
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

    //CURAH
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
                        x.value = '';
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
                        x.value = '';
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
				}else if(paymentFor == 3){
					if(returnValLength > 0) {
                        if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('laborBankDp').options.length = 0;
                        }else{
                            document.getElementById('laborBankId').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '';
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
				}else if(paymentFor == 9){
					if(returnValLength > 0) {
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingBankId').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){  
                            document.getElementById('vendorHandlingBankDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '';
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
                            $('#curahBankDp').find('option').each(function(i,e){
                                if($(e).val() == vendorBankId){
                                    $('#curahBankDp').prop('selectedIndex',i);
                                    
                                    $("#curahBankDp").select2({
                                        width: "100%",
                                        placeholder: vendorBankId
                                    });
                                }
                            });
                        }else if(paymentFor == 2){
                            $('#freightBankDp').find('option').each(function(i,e){
                                if($(e).val() == vendorBankId){
                                    $('#freightBankDp').prop('selectedIndex',i);
                                    
                                    $("#freightBankDp").select2({
                                        width: "100%",
                                        placeholder: vendorBankId
                                    });
                                }
                            });
                        }else if(paymentFor == 9){
                            $('#vendorHandlingBankDp').find('option').each(function(i,e){
                                if($(e).val() == vendorBankId){
                                    $('#vendorHandlingBankDp').prop('selectedIndex',i);
                                    
                                    $("#vendorHandlingBankDp").select2({
                                        width: "100%",
                                        placeholder: vendorBankId
                                    });
                                }
                            });
                        }else if(paymentFor == 3){
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
        });
    }

	function resetVendorBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
        document.getElementById('rek').value = '';
        document.getElementById('swift').value = '';

    }

	
// FREIGHT FUNCTION
	function setVendorFreight(type, stockpileId, freightId) { //
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getVendorFreight',
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
                        if(document.getElementById('paymentMethod').value != 2 ){
                            document.getElementById('freightId_1').options.length = 0;
                        }else{
                            document.getElementById('freightIdFcDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value != 2 ){
                            document.getElementById('freightId_1').options.add(x);
                        }else{
                            document.getElementById('freightIdFcDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2 ){
                            document.getElementById('freightId_1').options.add(x);
                        }else{
                            document.getElementById('freightIdFcDp').options.add(x);
                        }
                    }

                    if(type == 1) {
                        $('#freightIdFcDp').find('option').each(function(i,e){
                            if($(e).val() == freightId){
                                $('#freightIdFcDp').prop('selectedIndex',i);
                                
                                $("#freightIdFcDp").select2({
                                    width: "100%",
                                    placeholder: freightId
                                });
                            }
                        });

                    }
                }
            }
        });
    }

    function resetSupplier(text) {
        if(document.getElementById('paymentMethod').value == 3){
            // document.getElementById('vendorFreightId').options.length = 0;
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('supplierIdDp').options.length = 0;
        }
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value == 3){
            // document.getElementById('vendorFreightId').options.add(x);
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('supplierIdDp').options.add(x);
        }
    }
    
    function setSupplier(type, stockpileId2, freightId, supplierIdDp) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getSupplier',
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
                        if(document.getElementById('paymentMethod').value == 3){
                            document.getElementById('vendorFreightId').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('supplierIdDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        if(document.getElementById('paymentMethod').value == 3){
                            document.getElementById('vendorFreightId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('supplierIdDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value == 3){
                            document.getElementById('vendorFreightId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('supplierIdDp').options.add(x);
                        }
                    }
                    if(type == 1) {
                        $('#supplierIdDp').find('option').each(function(i,e){
                            if($(e).val() == supplierIdDp){
                                $('#supplierIdDp').prop('selectedIndex',i);
                                
                                $("#supplierIdDp").select2({
                                    width: "100%",
                                    placeholder: supplierIdDp
                                });
                            }
                        });

                    }
                }
            }
        });
    }

//----------------END--------------------

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

function refreshSummary(stockpileContractId, ppn, pph, paymentType) {
        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if(paymentMethod == 1) {
            if(ppn != 'NONE') {  //ppn sumbernya darimana?
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

        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'refreshSummary',
                    stockpileContractId: stockpileContractId,
                    paymentMethod: paymentMethod,
                    ppn: ppnValue,
                    pph: pphValue,
					paymentType: paymentType
            },
            success: function(data){
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                    if(parseInt(return_val[0])!=0)	//if no errors
                    {
                        document.getElementById('amount').value = return_val[1];
                        //console.log( document.getElementById('amount').value);
                    }
            }
        });
}


//CURAH
function resetContractCurah(text) {
        document.getElementById('contractCurah').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractCurah').options.add(x);
    }

    function setContractCurah(type, stockpileId, vendorId, contractNoDp) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getContractCurah',
                stockpileId: stockpileId,
                vendorId: vendorId
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
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('contractCurah').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('contractCurahDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        if(document.getElementById('paymentMethod').value != 2){
                            x.value = '';
                            x.text = '-- ALL --';
                            document.getElementById('contractCurah').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            x.value = '';
                            x.text = '-- Please select contract --';
                            document.getElementById('contractCurahDp').options.add(x);
                        }
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('contractCurah').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('contractCurahDp').options.add(x);
                        }
                    }

                    if(type == 1) {
                        $('#contractCurahDp').find('option').each(function(i,e){
                            if($(e).val() == contractNoDp){
                                $('#contractCurahDp').prop('selectedIndex',i);
                                
                                $("#contractCurahDp").select2({
                                    width: "100%",
                                    placeholder: contractNoDp
                                });
                            }
                        });

                    }
                }
            }
        });
    }

    function getCurahTax(vendorIdCurahDp) {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getCurahTax',
                    vendorIdCurahDp: vendorIdCurahDp
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {
                        document.getElementById('ppnC').value = returnVal[1];
                        document.getElementById('pphC').value = returnVal[2];
                        document.getElementById('taxidPpnC').value = returnVal[3];
                        document.getElementById('taxidPphC').value = returnVal[4];
                    }
                }
            });
    }

    function checktaxCurah() {
        var checkbox = document.getElementById('ppnStatusC1');
        var a = parseFloat($("#qtyCurah").val());
        var b = parseFloat($("#priceCurah").val());
        var c = parseFloat(a) * parseFloat(b);  
        if (checkbox.checked != true) {
            document.getElementById('ppnStatusC').value = 0;
            var d = 0;
        } else {
            document.getElementById('ppnStatusC').value = 1;
            var d = (parseFloat($("#ppnC").val())/100) * parseFloat(c); 
        }

        var checkbox = document.getElementById('pphStatusC1');
        if (checkbox.checked != true) {
            document.getElementById('pphStatusC').value = 0;
            var e = 0;
        } else {
            document.getElementById('pphStatusC').value = 1;
            var e = (parseFloat($("#pphC").val())/100) * parseFloat(c); 
        }
        var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
        $("#amount").val(c);  
    }


function checkAllCurah(a) {
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
	 checkSlipCurah(stockpileId, vendorId, ppn, pph, paymentFromCur, paymentToCur);
}

    function checkSlipCurah(stockpileId, vendorId, contractCurah, ppn, pph, paymentFromCur, paymentToCur) {
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
            setSlipCurah(stockpileId, vendorId, contractCurah, selected, ppnValue, pphValue, paymentFromCur, paymentToCur);
        //Panggil ulang function setSlipCurah dimana checkedSlips = selected (sudah ada isi)
    }

    function setSlip(stockpileContractId, checkedSlips) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlip',
                    stockpileContractId: stockpileContractId,
                    checkedSlips: checkedSlips
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
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

    function checkSlipCurah1(stockpileId, vendorId, settle, contractCurah, ppn, pph, paymentFromCur, paymentToCur, idPP) {
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


        setSlipCurah_settle(stockpileId, vendorId, settle, contractCurah, selected, selectedDP, ppnValue, pphValue, paymentFromCur, paymentToCur, idPP);
    }

    function checkSlipCurahDP(stockpileId, vendorId, settle, contractCurah, ppn, pph, paymentFromCur, paymentToCur, idPP) {
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


        setSlipCurah_settle(stockpileId, vendorId, settle, contractCurah, selected, selectedDP, ppnValue, pphValue, paymentFromCur, paymentToCur, idPP);
    }

    function checkSettle_Curah(stockpileId, vendorId, settle, contractCurah, ppn, pph, paymentFromCur, paymentToCur, idPP) {
        // alert(temp110Percent +', '+ grandTotal);
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
        
                setSlipCurah_settle(stockpileId, vendorId, selectedSettle, contractCurah, selected, selectedDP, ppnValue, pphValue, paymentFromCur, paymentToCur, idPP);
    }


    function setSlipCurah_copy(stockpileId, vendorId, checkedSlips, ppn, pph, paymentFromCur, paymentToCur,idPP) {
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
					paymentFrom1: paymentFromCur,
					paymentTo1: paymentToCur,
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

//FREIGHT
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
        checkSlipFreight(stockpileId, freightId, settle, contractFreight, ppn, pph, paymentFrom, paymentTo, idPP);
    }
 

    function checkSlipFreight_1(stockpileId_1, freightId_1, contractFreight, ppn, pph, paymentFromFP, paymentToFP) {
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
        setSlipFreight_1(stockpileId_1, freightId_1, contractFreight, selected, ppnValue, pphValue, paymentFromFP, paymentToFP);
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

     function resetContractFreight(text) {
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('contractFreight').options.length = 0;
        }
         var x = document.createElement('option');
         x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('contractFreight').options.add(x);
        }
     }

    function setContractFreight(type, stockpileId2, freightId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getContractFreight',
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
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('contractFreight').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        if(document.getElementById('paymentMethod').value != 2){
                        document.getElementById('contractFreight').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('contractFreight').options.add(x);
                        }
                    }

                }
            }
        });
    }

    function resetContractFreightDp(text) {
        if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('contractNoDp').options.length = 0;
        }
         var x = document.createElement('option');
         x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('contractNoDp').options.add(x);
        }
     }

    function setContractFreightDp(type, stockpileId2, freightId, vendorId, contractNoDp) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getContractFreightDp',
                    stockpileId: stockpileId2,
					freightId: freightId,
                    vendorId: vendorId
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
                        if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('contractNoDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        if(document.getElementById('paymentMethod').value == 2){
                        document.getElementById('contractNoDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('contractNoDp').options.add(x);
                        }
                    }

                    if(type == 1) {
                        $('#contractNoDp').find('option').each(function(i,e){
                            if($(e).val() == contractNoDp){
                                $('#contractNoDp').prop('selectedIndex',i);
                                
                                $("#contractNoDp").select2({
                                    width: "100%",
                                    placeholder: contractNoDp
                                });
                            }
                        });

                    }
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

    function checkSlipFreight(stockpileId, freightId, settle, contractFreight, ppn, pph, paymentFrom, paymentTo, idPP) {
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
        setSlipFreight_settle(stockpileId, freightId, settle, contractFreight, selected, selectedDP, ppnValue, pphValue, paymentFrom, paymentTo, idPP);
    }

    function checkSettle(stockpileId, freightId, settle, contractFreight, ppn, pph, paymentFrom, paymentTo, idPP) {
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
        
        setSlipFreight_settle(stockpileId, freightId, selectedSettle, contractFreight, selected, selectedDP, ppnValue, pphValue, paymentFrom, paymentTo, idPP);
    }

//  FREIGHT DP
    function checkSlipFreightDP(stockpileId, freightId,settle, contractFreight, ppn, pph, paymentFrom, paymentTo, idPP) {
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
           // validationGrandTotal(selected, selectedDP, 2);
            setSlipFreight_settle(stockpileId, freightId, settle, contractFreight, selected, selectedDP, ppnValue, pphValue, paymentFrom, paymentTo, idPP);
    }

    function resetgetFreightTax() {
        document.getElementById('ppn').value = 0;
        document.getElementById('pph').value = 0;
        // document.getElementById('taxidPpn').value = 0;
        // document.getElementById('taxidPph').value = 0;
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

    function checktaxstatus() {
        var checkbox = document.getElementById('ppnStatus1');
        var a = parseFloat($("#qtyFreight").val());
        var b = parseFloat($("#priceFreight").val());
        var c = parseFloat(a) * parseFloat(b); 
        if (checkbox.checked != true) {
            document.getElementById('ppnStatus').value = 0;
            var d = 0;
        } else {
            document.getElementById('ppnStatus').value = 1;
            var d = (parseFloat($("#ppn").val())/100) * parseFloat(c); 
        }

        var checkbox = document.getElementById('pphStatus1');
        if (checkbox.checked != true) {
            document.getElementById('pphStatus').value = 0;
            var e = 0;
        } else {
            document.getElementById('pphStatus').value = 1;
            var e = (parseFloat($("#pph").val())/100) * parseFloat(c); 
        }
        var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
        $("#amount").val(c);  
    }

// UNLOADING
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



    // UNLOADING DP
    function resetLaborDp(text) {
        document.getElementById('laborDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('laborDp').options.add(x);
    }

    function setLaborDp(type, stockpileId, laborId) {
		//alert('test');
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
                        document.getElementById('laborDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborDp').options.add(x);
                    }

                    if(type == 1) {
                        $('#laborDp').find('option').each(function(i,e){
                            if($(e).val() == laborId){
                                $('#laborDp').prop('selectedIndex',i);
                                
                                $("#laborDp").select2({
                                    width: "100%",
                                    placeholder: laborId
                                });
                            }
                        });

                    }
                }
            }
        });
    }

    function resetgetLaborTax() {
        document.getElementById('ppn').value = 0;
        document.getElementById('pph').value = 0;
        // document.getElementById('taxidPpn').value = 0;
        // document.getElementById('taxidPph').value = 0;
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

    function checktaxLabor() {
        var checkbox = document.getElementById('ppnStatusOb1');
        var a = parseFloat($("#qtyLabor").val());
        var b = parseFloat($("#priceLabor").val());
        var c = parseFloat(a) * parseFloat(b); 
        if (checkbox.checked != true) {
            document.getElementById('ppnStatusOb').value = 0;
            var d = 0;
        } else {
            document.getElementById('ppnStatusOb').value = 1;
            var d = (parseFloat($("#ppnOB").val())/100) * parseFloat(c); 
        }

        var checkbox = document.getElementById('pphStatusOb1');
        if (checkbox.checked != true) {
            document.getElementById('pphStatusOb').value = 0;
            var e = 0;
        } else {
            document.getElementById('pphStatusOb').value = 1;
            var e = (parseFloat($("#pphOB").val())/100) * parseFloat(c); 
        }
        var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
        $("#amount").val(c);  
    }


    // UNLOADING
    function checkSlipUnloading(stockpileId, laborId, ppn, pph, paymentFromUP, paymentToUP) {
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
    
    function setSlipUnloading(stockpileId, laborId, checkedSlips, ppn, pph, paymentFromUP, paymentToUP, idPP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'setSlipUnloading',
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

                var return_val = data.split('|');
                if(parseInt(return_val[0])!=0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }


    function setSlipUnloading_settleApprove(stockpileId, laborId, paymentFromUP, paymentToUP, idPP) {
    $.ajax({
        url: 'get-data-Ppayment.php',
        method: 'POST',
        data: { action: 'setSlipUnloading_settleApprove',
                stockpileId: stockpileId,
                laborId: laborId,
                paymentFromUP: paymentFromUP,
                paymentToUP: paymentToUP,
                idPP : idPP
        },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

            }
        });
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

    //UNLOADING SETTLE
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

    // CHECKLIST SLIP NOTIM
    function checkSlipOB(stockpileId, laborId, settle, ppn, pph, paymentFromUP, paymentToUP, idPP) {
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
        setSlipUnloading_settle(stockpileId, laborId, settle, selected, selectedDP, ppnValue, pphValue, paymentFromUP, paymentToUP, idPP);
    }

    // CHECKLIST UNTUK PILIH DP
    function checkSlipUnloadingDP(stockpileId, laborId,settle, ppn, pph, paymentFromUP, paymentToUP, idPP) {
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
        setSlipUnloading_settle(stockpileId, laborId, settle, selected, selectedDP, ppnValue, pphValue, paymentFromUP, paymentToUP, idPP );
    }

    //CHECKLIST UNTUK SETTLE ONLY?
    function checkSettle_unloading(stockpileId, laborId, settle, ppn, pph, paymentFromUP, paymentToUP, idPP) {
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
        setSlipUnloading_settle(stockpileId, laborId, selectedSettle, selected, selectedDP, ppnValue, pphValue, paymentFromUP, paymentToUP, idPP);
    }


//HANDLING DP
    function resetgetHandlingTax() {
        document.getElementById('ppnH').value = 0;
        document.getElementById('pphH').value = 0;
        // document.getElementById('taxidPpn').value = 0;
        // document.getElementById('taxidPph').value = 0;
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

    function checktaxHC() {
        var checkbox = document.getElementById('ppnStatusHC1');
        var a = parseFloat($("#qtyHandlingDP").val());
        var b = parseFloat($("#priceHandlingDP").val());
        var c = parseFloat(a) * parseFloat(b);  
        if (checkbox.checked != true) {
            document.getElementById('ppnStatusHC').value = 0;
            var d = 0;
        } else {
            document.getElementById('ppnStatusHC').value = 1;
            var d = (parseFloat($("#ppnH").val())/100) * parseFloat(c); 
        }

        var checkbox = document.getElementById('pphStatusHC1');
        if (checkbox.checked != true) {
            document.getElementById('pphStatusHC').value = 0;
            var e = 0;
        } else {
            document.getElementById('pphStatusHC').value = 1;
            var e = (parseFloat($("#pphH").val())/100) * parseFloat(c); 
        }
        var c = (parseFloat(c) + parseFloat(d)) - parseFloat(e);
        $("#amount").val(c);  
    }
//-------------------------END---------------------

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
        // checkSlipHandling(vendorHandlingId, ppn, pph, paymentFrom, paymentTo);
    }

    function resetVendorHandling(text) {
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('vendorHandlingId').options.length = 0;
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('vendorHandlingDp').options.length = 0;
        }
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('vendorHandlingId').options.add(x);
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('vendorHandlingDp').options.add(x);
        }
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
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingId').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorHandlingDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorHandlingDp').options.add(x);
                        }
                        
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorHandlingId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorHandlingDp').options.add(x);
                        }
                        
                    }

                    // if(type == 1) {
                    //     if(document.getElementById('paymentMethod').value == 1){
                    //         document.getElementById('vendorHandlingId').value = vendorHandlingId;
                    //     }else if(document.getElementById('paymentMethod').value == 2){
                    //         document.getElementById('vendorHandlingDp').value = vendorHandlingId;
                    //     }
                    // }
                    if(type == 1) {
                        $('#vendorHandlingDp').find('option').each(function(i,e){
                            if($(e).val() == vendorHandlingId){
                                $('#vendorHandlingDp').prop('selectedIndex',i);
                                
                                $("#vendorHandlingDp").select2({
                                    width: "100%",
                                    placeholder: vendorHandlingId
                                });
                            }
                        });

                    }
                }
            }
        });
    }

    function resetContractHandling(text) {
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('contractHandling').options.length = 0;
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('contractHandlingDp').options.length = 0;
        }
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('contractHandling').options.add(x);
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('contractHandlingDp').options.add(x);
        }
    }

    function setContractHandling(type, stockpileId, vendorHandlingId, contractHandling) {
        //alert('test1');
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getContractHandling',
                stockpileId: stockpileId,
                vendorHandlingId: vendorHandlingId
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
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('contractHandling').options.length = 0;
                        }else  if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('contractHandlingDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        if(document.getElementById('paymentMethod').value != 2){
                            x.value = '0';
                            x.text = '-- ALL --';
                            document.getElementById('contractHandling').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            x.value = '';
                            x.text = '-- Please Select Contract --';
                            document.getElementById('contractHandlingDp').options.add(x);
                        }
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                         document.getElementById('contractHandling').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('contractHandlingDp').options.add(x);
                        }
                    }

                    if(type == 1) {
                        $('#contractHandlingDp').find('option').each(function(i,e){
                            if($(e).val() == contractHandling){
                                $('#contractHandlingDp').prop('selectedIndex',i);
                                
                                $("#contractHandlingDp").select2({
                                    width: "100%",
                                    placeholder: contractHandling
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function checkSlipHandling(stockpileId, vendorHandlingId, contractHandling, ppn, pph, paymentFromHP, paymentToHP) {
    // var checkedSlips = document.forms[0].checkedSlips;
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

           setSlipHandling(stockpileId, vendorHandlingId, contractHandling, selected, ppnValue, pphValue, paymentFromHP, paymentToHP);
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

    function checkSlipHandling_1(stockpileId, vendorHandlingId, settle, contractHandling, ppn, pph, paymentFromHP, paymentToHP, idPP) {
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


        setSlipHandling_settle(stockpileId, vendorHandlingId, settle, contractHandling, selected, selectedDP, ppnValue, pphValue, paymentFromHP, paymentToHP, idPP);
    }

    function checkSlipHandlingDP(stockpileId, vendorHandlingId, settle, contractHandling, ppn, pph, paymentFromHP, paymentToHP, idPP) {
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


        setSlipHandling_settle(stockpileId, vendorHandlingId, settle, contractHandling, selected, selectedDP, ppnValue, pphValue, paymentFromHP, paymentToHP, idPP);
    }

    function checkSettle_handling(stockpileId, vendorHandlingId, settle, contractHandling, ppn, pph, paymentFromHP, paymentToHP, idPP) {
        // alert(temp110Percent +', '+ grandTotal);
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
        
            setSlipHandling_settle(stockpileId, vendorHandlingId, selectedSettle, contractHandling, selected, selectedDP, ppnValue, pphValue, paymentFromHP, paymentToHP, idPP);
    }
//END HANDLING
    
function resetVendorFreight(text) {
    if(document.getElementById('paymentMethod').value != 2){
        document.getElementById('contractFreight').options.length = 0;
    }else if(document.getElementById('paymentMethod').value == 2){
        document.getElementById('supplierIdDp').options.length = 0;
    }
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value != 2){
            document.getElementById('contractFreight').options.add(x);
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('supplierIdDp').options.add(x);
        }
    }

    //CURAH PAYMENT
    function resetVendorCurah(text) {
        if(document.getElementById('paymentMethod').value != 2){
         document.getElementById('vendorId').options.length = 0;
        }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('vendorIdCurahDp').options.length = 0;
        }
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        if(document.getElementById('paymentMethod').value != 2){
        document.getElementById('vendorId').options.add(x);
         }else if(document.getElementById('paymentMethod').value == 2){
            document.getElementById('vendorIdCurahDp').options.add(x);
        }
    }

    function setVendorCurah(type, stockpileId, vendorId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getVendorPayment',
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
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorId').options.length = 0;
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorIdCurahDp').options.length = 0;
                        }
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorId').options.add(x);
                        }else  if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorIdCurahDp').options.add(x);
                        }
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        if(document.getElementById('paymentMethod').value != 2){
                            document.getElementById('vendorId').options.add(x);
                        }else if(document.getElementById('paymentMethod').value == 2){
                            document.getElementById('vendorIdCurahDp').options.add(x);
                        }
                    }

                    if(type == 1) {
                        $('#vendorIdCurahDp').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#vendorIdCurahDp').prop('selectedIndex',i);
                                
                                $("#vendorIdCurahDp").select2({
                                    width: "100%",
                                    placeholder: vendorId
                                });
                            }
                        });
                    }
                }
            }
        });
    }


    function setPaymentType(type, paymentType) {
        document.getElementById('paymentType').value = 2;
    }

   function getInvoiceNotim(invoiceNo) {
            //alert(idPP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getValidateInvoiceNo',
                invoiceNo: invoiceNo
                },
            success: function(data){
    //       alert(data);
            var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    document.getElementById('invnotemp').value = returnVal[0];
                    if(document.getElementById('invnotemp').value == 1){
                        alert("Invoice No already exists");
                    }
                       
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

    
    function validationGrandTotal(checkedSlips, checkedSlipsDP, paymentFor) {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: { action: 'validationGrandTotal',
                        checkedSlips: checkedSlips,
                        checkedSlipsDP: checkedSlipsDP,
                        paymentFor: paymentFor
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        var validation = returnVal[1];
                        alert(validation);
                    }
                }
              
            });
	}

    function canceled() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'pengajuan_payment1',
                _method: 'CANCEL',
                idPP: document.getElementById('idPP').value,
                reject_remarks: document.getElementById('reject_remarks').value,
                paymentMethod: document.getElementById('paymentMethod').value,
                paymentFor: document.getElementById('paymentFor').value
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
                        $('#pageContent').load('views/pengajuan-notim-views.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }


</script>

<form method="post" id="PengajuanPaymentDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_payment1" />
    <input type="hidden" name="_method" value="<?php echo $method; ?>">
    <input type="hidden" id="idPP" name="idPP" value="<?php echo $idPP ?>" />
    <input type="hidden" id="invnotemp" name="invnotemp" />
    <input type="hidden" readonly class="span12" tabindex="" id="todayDate" name="todayDate" value="<?php echo $todayDate ?>">


    <?php if($idPP != '' && ($pStatus == 2 || $pStatus == 5)) { ?>
    <div class="row-fluid">
        <div class="span2 lightblue">
        </div>
        <div class="span8 lightblue">
             <?php if($pStatus = 2) { ?>
                 <label style="color: red;"><center><b>RETURNED/CANCELED</center></b></label>
            <?php } else if($pStatus = 5) { ?>
                <label style="color: orange;"><center><b>REJECT</center></b></label>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="row-fluid" id="divInvoice" style="display: none;">
        <div class="span3 lightblue">
            <label>No.Invoice Vendor<span style="color: red;">*</span> </label>
            <input type="text" class="span12" tabindex="" id="invoiceNo" name="invoiceNo" value="<?php echo $invoiceNo; ?>">
        </div>

        <div class="span3 lightblue">
            <label>Tanggal Invoice<span style="color: red;">*</span> </label>
            <input type="text" placeholder="DD/MM/YYYY"   tabindex="" id="invoiceDate" name="invoiceDate" value = "<?php echo $invoiceDate ; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

        <div class="span3 lightblue">
            <label>Tax Invoice</label>
            <input type="text" class="span12" tabindex="" id="taxInvoice" name="taxInvoice" value="<?php echo $taxInvoice; ?>">
        </div>

        <div class="span3 lightblue">
            <label>File Invoice<span style="color: red;">*</span> </label>
            <?php if($emailDate == ''){ ?>
            <input type="file" placeholder="File" tabindex="" id="file" name="file" class="span12" value="<?php echo $file_invoice; ?>" >
            <?php } if($file_invoice != ''){ ?>
                <a href="<?php echo $file_invoice; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
            <?php } ?>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Stockpile Location <span style="color: red;">*</span></label>
            <?php
            if($count > 1){
                createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                            FROM user_stockpile us
                            INNER JOIN stockpile s
                                ON s.stockpile_id = us.stockpile_id
                            WHERE us.user_id = {$_SESSION['userId']}
                            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileLocationId, "", "stockpileLocationId", "stockpile_id", "stockpile_full",
                            "", 2, "select2combobox100", 2);
            } else if($count == 1){?> 
                <input type="hidden" name="stockpileLocationId" id="stockpileLocationId" value="<?php echo $stockpileLocationId ?>"/>
                <input type="text" name="stockpileLocationText" id="stockpileLocationText" value = "<?php echo $stockpileLocationText ?>" readonly/>
            <?php } ?>
        </div>

        <div class="span3 lightblue">
            <label>Metode Pembayaran<span style="color: red;">*</span> </label>
            <?php
                if($idPP != ''){ ?>
                        <input type="hidden" readonly class="span12" tabindex="" id="paymentMethod" name="paymentMethod" value="<?php echo $methodId; ?>">
                        <input type="text" readonly class="span12" tabindex="" id="methodText" name="methodText" value="<?php echo $methodText; ?>">
                <?php } else {
                    createCombo("SELECT '2' as id, 'Down Payment' as info UNION
                                SELECT '3' as id, 'Final Payment' as info UNION 
                                SELECT '1' as id, 'Payment' as info;", "", "", "paymentMethod", "id", "info",
                        "","","",1);
                 } ?>
        </div>

        <div class="span3 lightblue">
            <label>Jenis Pembayaran<span style="color: red;">*</span> </label>
            <?php
                createCombo("SELECT '2' as id, 'OUT / Debit' as info;", $paymentType, "", "paymentType", "id", "info",
            "");
            ?>
        </div>

        <div class="span3 lightblue">
            <label>OA/OB/Hand<span style="color: red;">*</span></label>
            <?php if($idPP != ''){ ?>
                <input type="hidden" id="paymentFor"  name="paymentFor" readonly value="<?php echo $paymentForId ?>" />
                <input type="text" id="paymentForText" name="paymentForText" readonly value="<?php echo $paymentForText ?>" />
            <?php } else if($idPP == '') { ?>
                <?php   
                createCombo("SELECT type_transaction_id, type_transaction_name
                            FROM type_transaction","", "", "paymentFor", "type_transaction_id", "type_transaction_name",
                    "", 3, "",5);
                ?>
            <?php } ?>
        </div>
    </div>

    <!-- CURAH DP -->
    <div class="row-fluid" id="curahDownPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC",$stockpileIdCurahId, "", "stockpileIdCurahDp", "stockpile_id", "stockpile_full",
                    "", 14, "select2combobox100");
                ?>
            </div>
            <div class="span3 lightblue">
                <label>Vendor <span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "vendorIdCurahDp", "vendor_id", "vendor_name",
                    "", 15, "select2combobox100", 2);
                ?>
            </div>
            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "contractCurahDp", "contract_id", "contract_no",
                    "", 15, "select2combobox100", 2);
                ?>
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "curahBankDp", "v_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);
                ?>
            </div>
        </div>
        <br>
        <div class="row-fluid">
            <div class="span3 lightblue">
                <table cellspacing="25">
                    <tr>
                        <td colspan = "3">PPn% </td>
                        <td colspan = "2">PPh%</td>
                    </tr>
                    <tr>
                        <td><input type="text" readonly class="span12" tabindex="" id="ppnC" name="ppnC" /></td>
                        <td>
                            <input name="ppnStatusHC1" type="checkbox" id="ppnStatusC1" onclick="checktaxCurah()" <?php echo $checkedPpn ?>>
                            <input type="hidden" class="span12" readonly id="ppnStatusC" name="ppnStatusC" value="1">
                        </td>
                        <td style="padding: 10px"></td>
                        <td><input type="text" readonly class="span12" tabindex="" id="pphC" name="pphC"/></td>
                        <td><input name="pphStatusHC1" type="checkbox" id="pphStatusC1" onclick="checktaxCurah()" <?php echo $checkedPph ?>>
                        <input type="hidden" class="span12" readonly id="pphStatusC" name="pphStatusC" value="1"></td>
                    </tr>
                    <input type="hidden" class="span12" readonly id="taxidPpnC" name="taxidPpnC"></td>
                    <input type="hidden" class="span12" readonly id="taxidPphC" name="taxidPphC"></td>
                </table>
            </div>
            <div class="span3 lightblue">
                <label>Quantity <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="qtyCurah" name="qtyCurah" value="<?php echo $qtyDp ?>"/>
            </div>
            <div class="span3 lightblue">
                <label>Price<span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="priceCurah" name="priceCurah" value="<?php echo $priceDp ?>"/>
            </div>
            <div class="span3 lightblue">
                <label>Termin <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="terminCurah" name="terminCurah" value="<?php echo $terminDp ?>"/>
            </div>
        </div>
    </div>

    <!-- CURAH PAYMENT -->
    <div class="row-fluid" id="curahPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From <span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromCur" name="paymentFromCur" value = "<?php echo $paymentFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly; ?> >
            </div>

            <div class="span3 lightblue">
                <label>Periode To <span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToCur" value = "<?php echo $paymentTo; ?>" name="paymentToCur" data-date-format="dd/mm/yyyy" class="datepicker" <?php echo $readonly; ?> >
            </div>
            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                    <?php
                    if($idPP == ''){
                        createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                    FROM user_stockpile us
                                    INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                    WHERE us.user_id = {$_SESSION['userId']}
                                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "stockpileIdCurah", "stockpile_id", "stockpile_full",
                        "", 14, "select2combobox100");
                    } else { ?>
                    <input type="hidden" readonly id="stockpileIdCurah" value="<?php echo $stockpileIdCurahId ?>" />
                        <input type="text" readonly id="stockpileIdCurahText" value="<?php echo $stockpileIdCurahText ?>" />
                <?php } ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("", "", "", "vendorId", "vendor_id", "vendor_name",
                            "", 13, "select2combobox100", 2);
                }else{   ?>  
                    <input type="hidden" readonly id="vendorId1" value="<?php echo $vendorId ?>" />
                    <input type="text" readonly id="vendorText" value="<?php echo $vendorText ?>" />
                <?php } ?>
            </div>
            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                    <?php
                    if($idPP == ''){
                    createCombo("", "", "", "contractCurah", "contract_id", "contract_no",
                        "", 15, "select2combobox100", 2, "multiple");
                    }else{?>
                        <input type="hidden" readonly id="contractCurah" value="<?php echo $contractIds ?>" />
                        <textarea  type="text" style="width:95%;" readonly id="contractCurahText" ><?php echo $contractNames ?> </textarea>
                    <?php } ?>
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                    if($idPP == ''){
                        createCombo("", "", "", "curahBankId", "v_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                    } else {?>
                        <input type="hidden" readonly id="curahBankId" value="<?php echo $vendorBankId ?>" />
                        <input type="text" readonly id="curahBankText" value="<?php echo $curahBankText ?>" />
                    <?php } ?>
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
                <?php
                if($idPP == ''){
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                        FROM user_stockpile us
                                        INNER JOIN stockpile s
                                            ON s.stockpile_id = us.stockpile_id
                                        WHERE us.user_id = {$_SESSION['userId']}
                                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC","", "", "stockpileIdFreight", "stockpile_id", "stockpile_full",
                "", 14, "select2combobox100");
            
                }else { ?>
                    <input type="hidden" readonly id="stockpileIdFreight" value="<?php echo $stockpileIdFreightId ?>" />
                    <input type="text" readonly id="stockpileIdFreightText" value="<?php echo $stockpileIdFreightText ?>" />
                <?php } ?>
            </div>
		</div>

		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("", "", "", "freightId_1", "freight_id", "freight_supplier",
                        "", 15, "select2combobox100", 2);
                }else{?>
                    <input type="hidden" readonly id="freightId_1" value="<?php echo $freightId ?>" />
                    <input type="text" readonly id="freightName" value="<?php echo $freightName ?>" />

                <?php } ?>
            </div>

            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                createCombo("", "", "", "contractFreight", "contract_id", "contract_no",
                    "", 15, "select2combobox100", 2, "multiple");
                } else {?>
                     <input type="hidden" readonly id="contractFreight" value="<?php echo $contractIds ?>" />
                     <textarea  type="text" style="width:95%;" readonly id="contractFreightText" ><?php echo $contractNames ?> </textarea>

                <?php } ?>
            </div>

            <div class="span4 lightblue" >
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                    if($idPP == ''){
                        createCombo("", "", "", "freightBankId_1", "f_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                    
                    }else{?>
                       <input type="hidden" readonly id="freightBankId" value="<?php echo $vendorBankId ?>" />
                       <input type="text" readonly id="freightbankText" value="<?php echo $freightbankText ?>" />

                    <?php } ?>
            </div>
		</div>
    </div>
<!-- END FREIGHT-1 -->

<!-- FREIGHT DOWN PAYMENT -->
<div class="row-fluid" id="freightDownPayment" style="display: none;">
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                    FROM user_stockpile us
                                    INNER JOIN stockpile s
                                        ON s.stockpile_id = us.stockpile_id
                                    WHERE us.user_id = {$_SESSION['userId']}
                                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "$stockpileIdFreightId", "", "stockpileIdFcDp", "stockpile_id", "stockpile_full",
                                    "", 2, "select2combobox100", 2);
               ?>
                    
        </div>

        <div class="span3 lightblue">
            <label>Vendor Freight <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "freightIdFcDp", "freight_id", "freight_supplier",
                        "", 15, "select2combobox100", 9 );
                   ?>
        </div>

        <div class="span3 lightblue">
            <label>Bank Vendor<span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "freightBankDp", "f_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                ?>
        </div>
    </div>
    </br>

	<div class="row-fluid">
        <div class="span3 lightblue" id = "supplier">
            <labe >Supplier <span style="color: red;">*</span></label>
            <?php
                createCombo("", "", "", "supplierIdDp", "vendor_id", "vendor_freight",
                    "", 15, "select2combobox100", 6);
             ?> 
        </div>

        <div class="span3 lightblue">
            <label>No PKS Kontrak <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "contractNoDp", "contract_id", "contract_no",
                        "", 15, "select2combobox100", 7);
                ?>
        </div>
      
        <div class="span3 lightblue">
            <table   cellspacing="25">
                <tr>
                    <td colspan = "3">PPn% </td>
                    <td colspan = "2">PPh%</td>
                </tr>
                <tr>
                    <td><input type="text" readonly class="span12" tabindex="" id="ppn" name="ppn" /></td>
                    <td><input name="ppnStatus1" type="checkbox" id="ppnStatus1" onclick="checktaxstatus()" <?php echo $checkedPpn ?> >
                        <input type="hidden" class="span12" readonly id="ppnStatus" name="ppnStatus" value="1"></td>
                    <td style="padding: 10px"></td>
                    <td><input type="text" readonly class="span12" tabindex="" id="pph" name="pph"/></td>
                    <td><input name="pphStatus1" type="checkbox" id="pphStatus1" onclick="checktaxstatus()" <?php echo $checkedPph ?>>
                    <input type="hidden" class="span12" readonly id="pphStatus" name="pphStatus" value="1"></td>
                </tr>
                <input type="hidden" class="span12" readonly id="taxidPpn" name="taxidPpn"></td>
                <input type="hidden" class="span12" readonly id="taxidPph" name="taxidPph"></td>
            </table>
        </div>
	</div>
    <br>
    <div class="row-fluid">
        <div class="span3 lightblue">            
            <label>Quantity <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="qtyFreight" name="qtyFreight" value="<?php echo $qtyDp ?>" />
        </div>

	    <div class="span3 lightblue">
		    <label>Harga<span style="color: red;">*</span></label>
			<input type="text"  class="span12" tabindex="" id="priceFreight" name="priceFreight" value="<?php echo $priceDp ?>" />
        </div>

		<div class="span3 lightblue">
		    <label>Termin <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="terminFreight" name="terminFreight" value="<?php echo $terminDp ?>" />
        </div>
    </div>
</div>
<!-- END DP FREIGHT -->

<!-- UNLOADING DOWN PAYMENT -->
<div class="row-fluid" id="unloadingDownPayment" style="display: none;">
	<div class="row-fluid">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                            FROM user_stockpile us
                            INNER JOIN stockpile s
                            ON s.stockpile_id = us.stockpile_id
                            WHERE us.user_id = {$_SESSION['userId']}
                            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC","$stockpileOBId", "", "stockpileLaborDp", "stockpile_id", "stockpile_full",
                            "", 14, "select2combobox100");
               ?>
                  
        </div>

        <div class="span3 lightblue">
            <label>Labor <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "laborDp", "labor_id", "labor_name",
                            "", 15, "select2combobox100", 2);
                ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "laborBankDp", "l_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        
    </div>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <table cellspacing="25">
                <tr>
                    <td colspan = "3">PPn% </td>
                    <td colspan = "2">PPh%</td>
                </tr>
                <tr>
                    <td><input type="text" readonly class="span12" tabindex="" id="ppnOB" name="ppnOB" /></td>
                    <td>
                        <input name="ppnStatusOb1" type="checkbox" id="ppnStatusOb1" onclick="checktaxLabor()" <?php echo $checkedPpn ?> >
                        <input type="hidden" class="span12" readonly id="ppnStatusOb" name="ppnStatusOb" value="1">
                    </td>
                    <td style="padding: 10px"></td>
                    <td><input type="text" readonly class="span12" tabindex="" id="pphOB" name="pphOB"/></td>
                    <td><input name="pphStatusOb1" type="checkbox" id="pphStatusOb1" onclick="checktaxLabor()" <?php echo $checkedPph ?> >
                    <input type="hidden" class="span12" readonly id="pphStatusOb" name="pphStatusOb" value="1"></td>
                </tr>
                <input type="hidden" class="span12" readonly id="taxidPpnOB" name="taxidPpnOB"></td>
                <input type="hidden" class="span12" readonly id="taxidPphOB" name="taxidPphOB"></td>
            </table>
        </div>
		<div class="span3 lightblue">
		    <label>Quantity <span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="" id="qtyLabor" name="qtyLabor" value="<?php echo $qtyDp?>" />
            
        </div>
        <div class="span3 lightblue">
                <label>Price<span style="color: red;">*</span></label>
                    <input type="text" class="span12" tabindex="" id="priceLabor" name="priceLabor" value="<?php echo $priceDp?>"/>
        </div>
        <div class="span3 lightblue">
            <label>Termin <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="terminLabor" name="terminLabor" value="<?php echo $terminDp?>" />
        </div>
	</div>
</div>


<!-- UNLOADIN PAYMENT -->
    <div class="row-fluid" id="unloadingPayment" style="display: none;">
		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">* </span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP" value="<?php echo $paymentFrom; ?>" name="paymentFromUP"  data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">* </span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP" value = "<?php echo $paymentTo ?>" name="paymentToUP" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
		</div>

		<div class="row-fluid">
		    <div class="span3 lightblue">
		        <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC","", "", "stockpileOB", "stockpile_id", "stockpile_full",
                        "", 16,"select2combobox100");

                } else {  ?>
                  <input type="hidden" readonly id="stockpileOBId" value="<?php echo $stockpileOBId ?>" />
                    <input type="text" readonly id="stockpileOBText" value="<?php echo $stockpileOBText ?>" />
                <?php } ?>
            </div>

		    <div class="span3 lightblue">
		        <label>Labor <span style="color: red;">*</span></label>
                <?php
                    if($idPP == ''){
                            createCombo("", "", "", "laborId", "labor_id", "labor_name",
                                "", 15, "select2combobox100", 2);
                        
                    }else{ ?>
                        <input type="hidden" readonly id="laborId" value="<?php echo $laborId ?>" />
                        <input type="text" readonly id="laborText" value="<?php echo $laborText ?>" />
                <?php } ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("", "", "", "laborBankId", "l_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);
                }else{?>
                <input type="hidden" readonly id="laborBankId" value="<?php echo $vendorBankId ?>" />
                 <input type="text" readonly id="lbank" value="<?php echo $lbank ?>" />
            <?php } ?>
            </div>
		</div>
    </div>
    <!-- END UNLOAING -->

    <!-- HANDLING DOWN PAYMENT -->
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
                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileIdHandlingId, "", "stockpileVhDp", "stockpile_id", "stockpile_full",
                        "", 2, "select2combobox100", 2);   
                ?>
            </div>
            <div class="span3 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "vendorHandlingDp", "vendor_handling_id", "vendor_handling",
                            "", 15, "select2combobox100", 2);
               ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                        createCombo("", "", "", "vendorHandlingBankDp", "vh_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                   ?>
            </div>

            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <?php
                    createCombo("", "", "", "contractHandlingDp", "contract_id", "contract_no",
                            "", 15, "select2combobox100", 2);
                    
               ?>
            </div>
		</div>
     <br>
        <div class="row-fluid">
            <div class="span3 lightblue">
                <table cellspacing="25">
                    <tr>
                        <td colspan = "3">PPn% </td>
                        <td colspan = "2">PPh%</td>
                    </tr>
                    <tr>
                        <td><input type="text" readonly class="span12" tabindex="" id="ppnH" name="ppnH" /></td>
                        <td>
                            <input name="ppnStatusHC1" type="checkbox" id="ppnStatusHC1" onclick="checktaxHC()" <?php echo $checkedPpn ?>>
                            <input type="hidden" class="span12" readonly id="ppnStatusHC" name="ppnStatusHC" value="1">
                        </td>
                        <td style="padding: 10px"></td>
                        <td><input type="text" readonly class="span12" tabindex="" id="pphH" name="pphH"/></td>
                        <td><input name="pphStatusHC1" type="checkbox" id="pphStatusHC1" onclick="checktaxHC()" <?php echo $checkedPph ?>>
                        <input type="hidden" class="span12" readonly id="pphStatusHC" name="pphStatusHC" value="1"></td>
                    </tr>
                    <input type="hidden" class="span12" readonly id="taxidPpnH" name="taxidPpnH"></td>
                    <input type="hidden" class="span12" readonly id="taxidPphH" name="taxidPphH"></td>
                </table>
            </div>
            <div class="span3 lightblue">
                <label>Quantity <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="qtyHandlingDP" name="qtyHandlingDP" value="<?php echo $qtyDp; ?> "/>
            </div>
            <div class="span3 lightblue">
                <label>Price<span style="color: red;">*</span></label>
                <input type="text" class="span12"  tabindex="" id="priceHandlingDP" name="priceHandlingDP" value="<?php echo $priceDp ?>"/>
            </div>
            <div class="span3 lightblue">
                <label>Termin <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="" id="terminHandlingDP" name="terminHandlingDP" value="<?php echo $terminDp ?>"/>
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
                <?php
                  if($idPP == ''){
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC",  "", "", "stockpileIdHandling", "stockpile_id", "stockpile_full",
                        "", 16,"select2combobox100");
                } else {  ?>
                    <input type="hidden" readonly id="stockpileIdHandlingId" value="<?php echo $stockpileIdHandlingId ?>" />
                    <input type="text" readonly id="stockpileIdHandlingText" value="<?php echo $stockpileIdHandlingText ?>" />
                <?php } ?>
            </div>
		</div>

		<div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                        createCombo("", "", "", "vendorHandlingId", "vendor_handling_id", "vendor_handling",
                            "", 15, "select2combobox100", 2);
                } else { ?>
                    <input type="hidden" style = "width:100%;" readonly id="vendorHandlingId" value="<?php echo $vendorHandlingId ?>" />
                    <input type="text" readonly style = "width:100%;" readonly id="vendorHandlingText" value="<?php echo $vendorHandlingText ?>" />

            <?php } ?>
            </div>
            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("", "", "", "contractHandling", "contract_id", "contract_no",
                        "", 15, "select2combobox100", 2, "multiple");
                    }else{ ?>
                        <input type="hidden" style = "width:100%;" readonly id="contractHandlingId" value="<?php echo $contractIds ?>" />
                        <textarea class="span12" rows="1" readonly tabindex="" id="contractHandlingText" name="contractHandlingText"><?php echo $contractNames; ?></textarea>
                <?php } ?>
            </div>
            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                if($idPP == ''){
                    createCombo("", "", "", "vendorHandlingBankId", "vh_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);
                    
                } else { ?>
                        <input type="hidden" readonly id="handlingBankId" value="<?php echo $vendorBankId ?>" />
                       <input type="text" readonly id="vhbank" value="<?php echo $vhbank ?>" />
                 <?php } ?>
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
        <div class="span2 lightblue"  >
            <label>Amount<span style="color: red;">*</span></label>
                <input type="text" class="span12" readonly tabindex="" id="amount" name="amount" value="<?php echo $amount ?>">
                <input type="hidden" class="span12" readonly tabindex="" id="originalAmountDp" name="originalAmountDp" value="<?php echo $originalAmountDp ?>">

        </div>

        <div class="span3 lightblue">
            <label>Penerima<span style="color: red;"></span></label>
            <?php if($idPP == ''){ ?>
                <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary">
            <?php } else {?>
                <input type="text" class="span12" readonly tabindex="" id="beneficiary" name="beneficiary" value="<?php echo $beneficiary; ?>"> 
            <?php } ?>    
            <input type="hidden" readonly class="span12" tabindex="" id="bank" name="bank">
            <input type="hidden" readonly class="span12" tabindex="" id="rek" name="rek">
            <input type="hidden" readonly class="span12" tabindex="" id="swift" name="swift">
        </div>

        <div class="span3 lightblue">
            <label>Payment Type</label>
            <?php
            createCombo("SELECT '0' as id, 'Normal' as info UNION 
                        SELECT '1' as id, 'Urgent' as info;", $tipeBayar, "", "tipeBayar", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>

        <div class="span3 lightblue" id="divrequestPaymentDate">
            <label>Payment Date</label>
            <input type="text" name="requestPaymentDate" id="requestPaymentDate"  style="display: none"  value="<?php echo $requestPaymentDate; ?>" readonly>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" name="requestPaymentDate1" id="requestPaymentDate1"
                   value="<?php echo $requestPaymentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" style="display: none">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span1 lightblue">
            <label>Catatan</label>
        </div>
        <div class="span6 lightblue">
            <textarea class="span12" rows="3" tabindex=""  id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>

        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton2"><?php echo $button ?></button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject/Cancel/Retur Remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
    </div>

</div>

<div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger"  id="canceled" onclick= "canceled()" style="margin: 0px;">Cancel</button>         
        </div>
    </div>  


