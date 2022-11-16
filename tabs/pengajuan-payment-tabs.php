<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';


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
$stockpileId1 = '';

$paymentFrom1 = '';
$paymentTo1 = '';
$vendorHandling = '';
$freightId = '';
$vendorFreightId = '';
$contractPKHOA = '';
$paymentMethod = '';

$currencyId = '';
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
$paymentMethods = '';
$paymentFor_1 = '';
$method = '';

$qty = 0;
$price = 0;
$termin = '';
$dpp = 0;
$tipeBayar = 0;


$laborId = '';

$whereproperty = '';
$disableProperty = '';

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 14) {
            $allowAccount = true;
        } elseif ($row->module_id == 15) {
            $allowBank = true;
        } elseif ($row->module_id == 11) {
            $allowGeneralVendor = true;
        }
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if ($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if ($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if ($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if ($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } else if ($empty == 7) {
        echo "<option value=''>-- All --</option>";
    }

    if ($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if ($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>
if (isset($_POST['idPP']) && $_POST['idPP'] != '') {
    $idPP = $_POST['idPP'];

    $sqlPP = "SELECT CASE WHEN pp.payment_method  = 1 then 'Payment' ELSE 'Down Payment' END as paymentMethod,
              ty.type_transaction_name as paymentFor,
              sp.stockpile_name,
              f.freight_supplier,
              lab.labor_name,
              vh.vendor_handling_name,
              v.vendor_name,
              CONCAT(vb.bank_name,' - ',vb.account_no) AS vbank,
              CONCAT(lbank.bank_name,' - ',lbank.account_no) as lbank,
              CONCAT(fb.bank_name,' - ',fb.account_no) AS fbank,
              CONCAT(vhb.bank_name,' - ',vhb.account_no) AS vhbank, 
              DATE_FORMAT(pp.urgent_payment_date, '%d/%m/%Y') AS tglReguest, 
              DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, 
                    DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo,
              DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoiceDate1, 
              pp.* From pengajuan_payment pp
              LEFT JOIN type_transaction ty on ty.type_transaction_id = pp.payment_for
              LEFT JOIN stockpile sp on sp.stockpile_id = pp.stockpile_id
              LEFT JOIN vendor v on v.vendor_id = pp.vendor_id
              LEFT JOIN vendor_bank vb on vb.vendor_id = pp.vendor_id
              LEFT JOIN freight f on f.freight_id = pp.freight_id
              LEFT JOIN freight_bank fb on fb.freight_id = pp.freight_id
              LEFT JOIN labor lab on lab.labor_id = pp.labor_id
              LEFT JOIN labor_bank lbank on lbank.labor_id = pp.labor_id
              LEFT JOIN vendor_handling vh on vh.vendor_handling_id = pp.vendor_handling_id
              LEFT JOIN vendor_handling_bank vhb on vhb.vendor_handling_id = pp.vendor_handling_id
               where pp.idPP = {$idPP}";
    //echo $sqlPP;
    $resultPP = $myDatabase->query($sqlPP, MYSQLI_STORE_RESULT);

    if ($resultPP !== false && $resultPP->num_rows > 0) {
        $rowDataPP = $resultPP->fetch_object();
        $paymentMethod = $rowDataPP->payment_method;
        $paymentMethods = $rowDataPP->paymentMethod;
        $stockpileId = $rowDataPP->stockpile_id;
        $stockpileName = $rowDataPP->stockpile_name;
        $paymentType = $rowDataPP->payment_type;
        $paymentFor = $rowDataPP->payment_for;
        $paymentFor_1 = $rowDataPP->paymentFor;
        $vendorid = $rowDataPP->vendor_id;
        $stockpileContractId = $rowDataPP->stockpile_contract_id;

        $stockpileId1 = $rowDataPP->stockpile_id;
        $paymentFrom1 = $rowDataPP->dateFrom;
        $paymentTo1 = $rowDataPP->dateTo;

        $vendorId1 = $rowDataPP->vendor_id;
        $vendorName = $rowDataPP->vendor_name;
        $vbank = $rowDataPP->vbank;
        $vendorHandling = $rowDataPP->vendor_handling_id;
        $vendorHandlingName = $rowDataPP->vendor_handling_name;
        $vhbank = $rowDataPP->vhbank;
        $freightId = $rowDataPP->freight_id;
        $freightName = $rowDataPP->freight_supplier;
        $freightbankName = $rowDataPP->fbank;
        $vendorFreightId = $rowDataPP->transaction_id;

        $taxInvoice = $rowDataPP->tax_invoice;
        $invoiceNo = $rowDataPP->invoice_no;
        $invoiceDate = $rowDataPP->invoiceDate1;
        $chequeNo = $rowDataPP->cheque_no;
        $remarks = $rowDataPP->remarks;
        $remarks2 = $rowDataPP->remaks2;
        $amountVal = $rowDataPP->amount;
        $beneficiary = $rowDataPP->beneficiary;
        $bank = $rowDataPP->bank_name;
        $rek = $rowDataPP->no_rek;
        $swift = $rowDataPP->swift;
        $tipeBayar = $rowDataPP->urgent_payment_type;
        $tglBayarUrgent = $rowDataPP->tglReguest;

        $vendorBankID = $rowDataPP->vendor_bank_id;

        $laborId = $rowDataPP->labor_id;
        $laborName = $rowDataPP->labor_name;
        $bankName = $rowDataPP->lbank;
        $file_invoice = $rowDataPP->file;

        $qty = $rowDataPP->qty;
        $price = $rowDataPP->price;
        $termin = $rowDataPP->termin;
        $dpp = $rowDataPP->dpp;

        if($rowDataPP->status_ppn == 1){
            $checkedPpn ='checked = "checked"';
        }

        if($rowDataPP->status_pph == 1){
            $checkedPph ='checked = "checked"';
        }
        $disableProperty1 = 'disabled';

    }

    if ($paymentFor == 2 && $idPP != '') {  //GET FREIGHT
        $vendorFreightNames = '';
        $vendorFreightName = '';
        $sqlSupplier = "SELECT pps.vendor_id FROM pengajuan_payment_supplier pps
                        INNER JOIN pengajuan_payment pp ON pp.`idPP` = pps.idPP
                        WHERE pps.idPP = {$idPP} AND pp.`freight_id` = {$freightId} ";
        $resultSupplier = $myDatabase->query($sqlSupplier, MYSQLI_STORE_RESULT);
        //echo $sqlSupplier;

        if ($resultSupplier !== false && $resultSupplier->num_rows > 0) {

            while ($rowSupplier = $resultSupplier->fetch_object()) {
                $vendorFreightId = $rowSupplier->vendor_id;
                // $vendorFreightIds = $vendorFreightIds . ',' . $vendorFreightId;
                if ($vendorFreightIds == '') {
                    $vendorFreightIds = $vendorFreightId;
                } else {
                    $vendorFreightIds = $vendorFreightIds . ',' . $vendorFreightId;
                }

            }
        }

        // $vendorFreightIds = json_encode(explode(',',$vendorFreightIds));
        $sqlVendor = "SELECT vendor_name FROM vendor WHERE vendor_id IN ({$vendorFreightIds})";
        $resultVendor = $myDatabase->query($sqlVendor, MYSQLI_STORE_RESULT);
        if ($resultVendor !== false && $resultVendor->num_rows > 0) {

            while ($rowVendor = $resultVendor->fetch_object()) {
                $vendorFreightName = $rowVendor->vendor_name;
                // $vendorFreightIds = $vendorFreightIds . ',' . $vendorFreightId;
                if ($vendorFreightNames == '') {
                    $vendorFreightNames = $vendorFreightName;
                } else {
                    $vendorFreightNames = $vendorFreightNames . ' , ' . $vendorFreightName;
                }

            }
        }
    }

    if ($paymentFor == 1) {
        $whereproperty = "ppayment_id = {$idPP}";
    } else if ($paymentFor == 2) {
        $whereproperty = "fc_ppayment_id = {$idPP}";
    } else if ($paymentFor == 9) {
        $whereproperty = "hc_ppayment_id = {$idPP}";
    } else if ($paymentFor == 3) {
        $whereproperty = "uc_ppayment_id = {$idPP}";
    }

    
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
        while ($row2 = $result2->fetch_object()) {
            $transaksiID = $row2->transaction_id;
        }
    }

    $method = 'UPDATE';
} else {
    $method = 'INSERT';
    $checkedPpn ='checked = "checked"';
    $checkedPph ='checked = "checked"';
}

?>

<input type="hidden" id="stockpileIdVal" value="<?php echo $stockpileId; ?>">
<input type="hidden" id="vendorId1Val" value="<?php echo $vendorId1 ?>"/>
<input type="hidden" id="vendorHandlingVal" value="<?php echo $vendorHandling ?>"/>
<input type="hidden" id="freightIdVal" value="<?php echo $freightId ?>"/>
<input type="hidden" id="vendorFreightIdVal" value="<?php echo $vendorFreightIds ?>"/>
<input type="hidden" readonly class="span12" tabindex="" id="validasiPKHOA" name="validasiPKHOA">
<input type="hidden" readonly class="span12" tabindex="" id="todayDate" name="todayDate"
       value="<?php echo $todayDate ?>">


<script type="text/javascript">
    $(document).ready(function () {

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

        $('#qtyFreight').number(true, 2);
        $('#priceFreight').number(true, 2);

        $('#qtyLabor').number(true, 2);
        $('#priceLabor').number(true, 2);

        $('#qtyHandlingDP').number(true, 2);
        $('#priceHandlingDP').number(true, 2);
        // $('#tglBayarDiv').hide();
        $('#amount1').hide();
        $('#amount2').show();

        <?php if($tipeBayar == 0){ ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", true);
        $("#tglBayarUrgent2").prop("disabled", false);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setPlanPayDate',
            },
            success: function (data) {
                $('#tglBayarUrgent').val(data)
                $('#tglBayarUrgent2').val(data)
            }
        });
        <?php } ?>



        setStockpileLocation(); //DIPINDAH KESINI
        setPaymentType(1, <?php echo $paymentType; ?>);
        <?php
        if($paymentType != '' && $idPP == '') {
        ?>

        if (document.getElementById('paymentFor').value != '') {
            if (document.getElementById('paymentType').value == 2) {
                // OUT
                if (document.getElementById('paymentFor').value == 1) {
                    $('#curahPayment').show();
                } else if (document.getElementById('paymentFor').value == 2) {
                    if (document.getElementById('paymentMethod').value == 2) {
                        $('#freightDownPayment').show();
                    } else {
                        //$('#freightPayment').show();
                        $('#freightPayment1').show();
                    }
                } else if (document.getElementById('paymentFor').value == 9) {
                    if (document.getElementById('paymentMethod').value == 2) {
                        $('#handlingDownPayment').show();
                    } else {
                        $('#handlingPayment').show();
                    }
                } else if (document.getElementById('paymentFor').value == 3) {
                    if (document.getElementById('paymentMethod').value == 2) {
                        $('#unloadingDownPayment').show();
                    } else {
                        $('#unloadingPayment').show();
                    }
                } else if (document.getElementById('paymentFor').value == 8) {
                    //$('#divPaymentTo').show();
                    //$('#divTax').show();
                    $('#divInvoice').hide();
                    $('#invoicePayment').show();
                    setStockpileLocation();
                }
            }
        }
        <?php
        } else {
        ?>
        // setPaymentType(0, 0);
        <?php
        }
        ?>

        <?php if($idPP != ''){ ?>
        $('#amount1').show();
        $('#amount2').hide();
        <?php } ?>

        //CURAH
        <?php if($idPP != '' && $paymentFor == 1){ //GET DATA FROM ADMIN?>
        setSlipCurah(<?php echo $stockpileId; ?>, <?php echo $vendorId1; ?>, '', 'NONE', 'NONE', $('input[id="paymentFrom1"]').val(), $('input[id="paymentTo1"]').val(),  <?php echo $idPP; ?>);
        document.getElementById("paymentFrom1").disabled = true;
        document.getElementById("paymentTo1").disabled = true;
        <?php if($tipeBayar == 1) { ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", false);
        $("#tglBayarUrgent2").prop("disabled", true);
        <?php }else{ ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", true);
        $("#tglBayarUrgent2").prop("disabled", false);
        <?php } }?>

        //FREIGHT
        <?php if($idPP != '' && $paymentFor == 2){ //GET DATA FROM ADMIN?>
        setSlipFreight_1(<?php echo $stockpileId; ?>, <?php echo $freightId; ?>, <?php echo $vendorFreightIds; ?>, 'NONE', 'NONE', 'NONE', $('input[id="paymentFrom_1"]').val(), $('input[id="paymentTo_1"]').val(), <?php echo $idPP; ?>);
        //console.log(<?php echo $vendorFreightIds; ?>);
        <?php if($paymentMethod == 2) {?>
        $('#freightPayment1').hide();
        $('#freightDownPayment').show();
        getFreightTax(<?php echo $freightId ?>);

        <?php } else {?>
        $('#freightPayment1').show();
        $('#freightDownPayment').hide();
        document.getElementById("paymentFrom_1").disabled = true;
        document.getElementById("paymentTo_1").disabled = true;
        <?php } ?>

        <?php if($tipeBayar == 1) { ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", false);
        $("#tglBayarUrgent2").prop("disabled", true);
        <?php }else{ ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", true);
        $("#tglBayarUrgent2").prop("disabled", false);
        <?php }
        }?>

        //UNLOADING C
        <?php if($idPP != '' && $paymentFor == 3) { ?>
        setSlipUnloading(<?php echo $stockpileId; ?>, <?php echo $laborId; ?>, '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val(), <?php echo $idPP; ?>);
        <?php if($paymentMethod == 2) {?>
        $('#unloadingPayment').hide();
        $('#unloadingDownPayment').show();
        getLaborTax(<?php echo $laborId ?>);

        <?php } else {?>
        $('#unloadingPayment').show();
        $('#unloadingDownPayment').hide();
        document.getElementById("paymentFromUP").disabled = true;
        document.getElementById("paymentToUP").disabled = true;
        <?php } ?>

        <?php if($tipeBayar == 1) { ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", false);
        $("#tglBayarUrgent2").prop("disabled", true);
        <?php }else{ ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", true);
        $("#tglBayarUrgent2").prop("disabled", false);
        <?php }} ?>

        //HANDLING
        <?php if($idPP != '' && $paymentFor == 9){ //GET DATA FROM ADMIN?>
        setSlipHandling(<?php echo $stockpileId; ?>, <?php echo $vendorHandling; ?>, '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val(), <?php echo $idPP; ?>);
        <?php if($paymentMethod == 2) {?>
        $('#handlingPayment').hide();
        $('#handlingDownPayment').show();
        getHandlingTax(<?php echo $vendorHandling ?>);
        <?php } else {?>
        $('#handlingPayment').show();
        $('#handlingDownPayment').hide();
        document.getElementById("paymentFromHP").disabled = true;
        document.getElementById("paymentToHP").disabled = true;
        <?php } ?>

        <?php if($tipeBayar == 1) { ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", false);
        $("#tglBayarUrgent2").prop("disabled", true);
        <?php }else{ ?>
        $('#tglBayarDiv').show();
        $("#tglBayarUrgent").prop("disabled", true);
        $("#tglBayarUrgent2").prop("disabled", false);
        <?php }} ?>



        $('#divInvoice').show();
        $('#divPaymentType1').hide()
        //  $('#tglBayarDiv').hide();
        $('#paymentMethod').change(function () {
            // resetPaymentType(' Method ');
            // resetPaymentFor(' Type ');
            resetBankDetail(' ');
            $('#vendorPayment').hide();
            $('#curahPayment').hide();
            $('#freightDownPayment').hide();
            $('#freightPayment').hide();
            $('#freightPayment1').hide();
            $('#handlingPayment').hide();
            $('#handlingDownPayment').hide();
            $('#unloadingPayment').hide();
            $('#unloadingDownPayment').hide();
            document.getElementById('stockpileId').value = '';
            document.getElementById('stockpileId1').value = '';
            resetVendor('vendorId', ' Stockpile ');
            resetVendor('vendorId1', ' Stockpile ');
            resetContract(' Stockpile ');
            document.getElementById('stockpileId4').value = '';
            resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId2').value = '';
            resetSupplier(' Stockpile ');
            resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            $('#divAmount').show();
            // $('#divInvoice').hide();
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            // setPaymentType(0, 0);
            setStockpileLocation();
            if (document.getElementById('paymentMethod').value == 1) {
                $('#divPaymentType1').show();
            } else {
                $('#divPaymentType1').hide();

            }
        });

        $('#paymentType').change(function () {
            // resetPaymentFor(' Type ');
            resetBankDetail(' ');
            $('#vendorPayment').hide();
            $('#curahPayment').hide();
            $('#freightDownPayment').hide();
            $('#freightPayment').hide();
            $('#freightPayment1').hide();
            $('#handlingPayment').hide();
            $('#handlingDownPayment').hide();
            $('#unloadingPayment').hide();
            $('#unloadingDownPayment').hide();
            document.getElementById('stockpileId').value = '';
            document.getElementById('stockpileId1').value = '';
            resetVendor('vendorId', ' Stockpile ');
            resetVendor('vendorId1', ' Stockpile ');
            resetContract(' Stockpile ');
            document.getElementById('stockpileId4').value = '';
            resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId4').value = '';
            resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId2').value = '';
            resetSupplier(' Stockpile ');
            resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            $('#divAmount').show();
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            if (document.getElementById('paymentType').value != '') {
                // setPaymentFor($('select[id="paymentType"]').val());
                setStockpileLocation();
            }
        });


        $('#paymentFor').change(function () {
            document.getElementById('stockpileId').value = '';
            document.getElementById('stockpileId1').value = '';
            resetVendor('vendorId', ' Stockpile ');
            resetVendor('vendorId1', ' Stockpile ');
            resetContract(' Stockpile ');
            resetBankDetail(' ');
            document.getElementById('stockpileId4').value = '';
            resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId2').value = '';
            resetSupplier(' Stockpile ');
            resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            $('#vendorPayment').hide();
            $('#curahPayment').hide();
            $('#freightDownPayment').hide();
            $('#freightPayment').hide();
            $('#freightPayment1').hide();
            $('#handlingPayment').hide();
            $('#handlingDownPayment').hide();
            $('#unloadingPayment').hide();
            $('#unloadingDownPayment').hide();
            if (document.getElementById('paymentFor').value != '') {
                if (document.getElementById('paymentType').value == 2) {
                    // OUT
                    if (document.getElementById('paymentFor').value == 0) {
                        $('#vendorPayment').show();
                    } else if (document.getElementById('paymentFor').value == 1) {
                        $('#curahPayment').show();
                    } else if (document.getElementById('paymentFor').value == 2) {
                        if (document.getElementById('paymentMethod').value == 2) {
                            $('#freightDownPayment').show();
                            //$('#freightDownPayment2').show();
                        } else {
                            //$('#freightPayment').show();
                            $('#freightPayment1').show();
                        }
                    } else if (document.getElementById('paymentFor').value == 9) {
                        if (document.getElementById('paymentMethod').value == 2) {
                            $('#handlingDownPayment').show();
                        } else {
                            $('#handlingPayment').show();
                        }
                    } else if (document.getElementById('paymentFor').value == 3) {
                        if (document.getElementById('paymentMethod').value == 2) {
                            $('#unloadingDownPayment').show();
                        } else {
                            $('#unloadingPayment').show();
                        }
                    } else if (document.getElementById('paymentFor').value == 8) {
                        //$('#divPaymentTo').show();
                        //$('#divTax').show();
                        $('#divInvoice').hide();
                        $('#invoicePayment').show();
                        //setStockpileLocation();
                    }
                }

            }
        });


        $('#invoiceId').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('invoiceId').value != '') {
                refreshInvoice($('select[id="invoiceId"]').val(), $('select[id="paymentMethod"]').val(), 'NONE', 'NONE');
            }
        });

        $('#invoiceDate').change(function () {
            CompareDate();
        });

        //KONTRAK
        $('#stockpileId').change(function () {
            resetVendor('vendorId', ' Stockpile ');
            resetContract(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('stockpileId').value != '') {
                resetVendor('vendorId', ' ');
                setVendor(0, 'vendorId', $('select[id="stockpileId"]').val(), 'P', 0);
                resetContract(' Vendor ');
            }
        });

        $('#vendorId').change(function () {
            resetContract(' ');
            resetVendorBank($('select[id="paymentFor"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('vendorId').value != '') {
                setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
                getVendorBank(0, $('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetContract(' Vendor ');
            }
        });

        $('#vendorBankId').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());

            if (document.getElementById('vendorBankId').value != '') {
                getVendorBankDetail($('select[id="vendorBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });

        $('#stockpileContractId').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('stockpileContractId').value != '') {
                refreshSummary($('select[id="stockpileContractId"]').val(), $('select[id="paymentMethod"]').val(), 'NONE', 'NONE', $('select[id="paymentType"]').val());

            }
        });

        //END KONTRAK 
        $('#stockpileId1').change(function () {
            resetVendor('vendorId1', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('stockpileId1').value != '') {
                resetVendor('vendorId1', ' ');
                setVendor(0, 'vendorId1', $('select[id="stockpileId1"]').val(), 'C', 0);
            }
        });

        $('#vendorId1').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('vendorId1').value != '') {
                if (document.getElementById('paymentMethod').value == 1) {
                    setSlipCurah($('select[id="stockpileId1"]').val(), $('select[id="vendorId1"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFrom1"]').val(), $('input[id="paymentTo1"]').val());
                    getVendorBank(0, $('select[id="vendorId1"]').val(), $('select[id="paymentFor"]').val());
                }
            }
        });

        $('#curahBankId').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());
            if (document.getElementById('curahBankId').value != '') {
                getVendorBankDetail($('select[id="curahBankId"]').val(), $('select[id="paymentFor"]').val());

            }
        });

        $('#stockpileId2').change(function () {
            resetSupplier('freightId', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('stockpileId2').value != '') {
                resetSupplier(' ');
                setSupplier(0, $('select[id="stockpileId2"]').val(), 0);
            }
        });

        $('#stockpileIdFcDp').change(function () {
            resetSupplierFcDp('freightIdFcDp', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('stockpileIdFcDp').value != '') {
                resetSupplierFcDp(' ');
                setSupplierFcDp(0, $('select[id="stockpileIdFcDp"]').val(), 0);
            }
        });

        $('#freightIdFcDp').change(function () {
            resetVendorFreight(' ');
            //resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            //resetVendorBank ($('select[id="paymentFor"]').val());

            if (document.getElementById('freightIdFcDp').value != '') {
                setVendorFreightDp(0, $('select[id="stockpileIdFcDp"]').val(), $('select[id="freightIdFcDp"]').val(), 0);
                // getVendorBank(0,$('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
                getFreightTax($('select[id="freightIdFcDp"]').val())
                getFreightBankDP(0, $('select[id="freightIdFcDp"]').val(), $('select[id="paymentFor"]').val());

            } else {
                resetVendorFreightDp(' Stockpile ');
            }
        });

        $('#vendorFreightIdDp').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			// resetKontrakPKHOA1 ($('select[id="paymentFor"]').val());

            if(document.getElementById('freightIdFcDp').value != '') {
                setNoKontrak(0, $('select[id="vendorFreightIdDp"]').val(), 0);
            }
        });

        $("#qtyFreight, #priceFreight").keyup( 
                function(){
                    var statusPpn = document.getElementById('ppnStatus').value;
                    var statusPph = document.getElementById('pphStatus').value;
                    var a = parseFloat($("#qtyFreight").val());
                    var b = parseFloat($("#priceFreight").val());
                    var c = parseFloat(a) * parseFloat(b); 
                    $("#originalAmount").val(parseFloat(a) * parseFloat(b));

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
                    $("#originalAmount").val(parseFloat(a) * parseFloat(b));
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
                    $("#originalAmount").val(parseFloat(a) * parseFloat(b));

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

        // $('#contractPKHOA1').change(function() {
        //     $('#slipPayment').hide();
        //     $('#summaryPayment').hide();
        //     // $('#DownPayment').show();
        //     // $('#payment').hide();
        //     resetValidationPKHOA(' ');
        //     if(document.getElementById('contractPKHOA1').value != '') {
        //         // console.log(document.getElementById('contractPKHOA1').value);
        //         ValidationPKHOA($('select[id="contractPKHOA"]').val());
        //     }
        // });

        $('#freightBankDp').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());
            if (document.getElementById('freightBankDp').value != '') {
                getVendorBankDetail($('select[id="freightBankDp"]').val(), $('select[id="paymentFor"]').val());
            }
        });


//FREIGHT
        $('#freightId').change(function () {
            resetVendorFreight(' ');
            //resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            resetVendorBank($('select[id="paymentFor"]').val());

            if (document.getElementById('freightId').value != '') {
                setVendorFreight(0, $('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), 0);

                getVendorBank(0, $('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
            }
        });

        $('#freightId_1').change(function () {
            //  resetVendorFreight(' ');
            //resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorBank($('select[id="paymentFor"]').val());
            if (document.getElementById('freightId_1').value != '') {
                //   setSlipFreight_1($('select[id="stockpileId2_1"]').val(), $('select[id="freightId_1"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFrom_1"]').val(),$('input[id="paymentTo_1"]').val());
                setVendorFreight(0, $('select[id="stockpileId2_1"]').val(), $('select[id="freightId_1"]').val(), 0);
                getVendorBank(0, $('select[id="freightId_1"]').val(), $('select[id="paymentFor"]').val());
            }
        });

        $('#freightBankId_1').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());

            if (document.getElementById('freightBankId_1').value != '') {
                getVendorBankDetail($('select[id="freightBankId_1"]').val(), $('select[id="paymentFor"]').val());
            }
        });

        $('#vendorFreightId_1').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            //  resetBankDetail(' ');
            if (document.getElementById('vendorFreightId_1').value != '' && document.getElementById('freightId_1').value != '') {
                setSlipFreight_1($('select[id="stockpileId2_1"]').val(), $('select[id="freightId_1"]').val(), $('select[id="vendorFreightId_1"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFrom_1"]').val(), $('input[id="paymentTo_1"]').val());
            }
        });

        $('#freightBankId').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());

            if (document.getElementById('freightBankId').value != '') {
                getVendorBankDetail($('select[id="freightBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });


        $('#vendorFreightId').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetBankDetail(' ');
            if (document.getElementById('vendorFreightId').value != '' && document.getElementById('freightId').value != '') {
                if (document.getElementById('paymentMethod').value == 1) {
                    setKontrakPKHOA(0, $('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), 0);
                    //alert(setSlipFreight);
                }
            } else {
                resetKontrakPKHOA('Stockpile');
            }
        });


        $('#contractPKHOA').change(function () {
            resetValidationPKHOA(' ');
            ValidationPKHOA($('select[id="contractPKHOA"]').val());
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetBankDetail(' ');
            if (document.getElementById('contractPKHOA').value != '' && document.getElementById('vendorFreightId').value != '') {
                if (document.getElementById('paymentMethod').value == 1) {
                    setSlipFreight($('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), $('select[id="contractPKHOA"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFrom"]').val(), $('input[id="paymentTo"]').val());
                    //alert(setSlipFreight);
                    getBankDetail($('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
                }
            }


        });
//END FREIGHT


//HANDLING
        $('#stockpileId4').change(function () {
            resetVendorHandling(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if (document.getElementById('stockpileId4').value != '') {
                resetVendorHandling(' ');
                setVendorHandling(0, $('select[id="stockpileId4"]').val(), 0);
            }
        });

        $('#vendorHandlingId').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorBank($('select[id="paymentFor"]').val());

            if (document.getElementById('vendorHandlingId').value != '') {
                if (document.getElementById('paymentMethod').value == 1) {
                    setSlipHandling($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val());
                    getVendorBank(0, $('select[id="vendorHandlingId"]').val(), $('select[id="paymentFor"]').val());

                }
            }
        });

        $('#vendorHandlingBankId').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());
            if (document.getElementById('vendorHandlingBankId').value != '') {
                getVendorBankDetail($('select[id="vendorHandlingBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });

        //END HANDLING


        //HANDLING DP
        $('#stockpileVhDp').change(function () {
            resetVhDp(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if (document.getElementById('stockpileVhDp').value != '') {
                resetVhDp(' ');
                setVhDp(0, $('select[id="stockpileVhDp"]').val(), 0);
            }
        });
		$('#vendorHandlingDp').change(function() {
				$('#slipPayment').hide();
				$('#summaryPayment').hide();
				//resetBankDetail(' ');
				resetVendorHandlingBankDP ($('select[id="paymentFor"]').val());
				resetContractHandlingDp (' ');

				//alert('html from to nya');
				resetgetHandlingTax('');
				if(document.getElementById('vendorHandlingDp').value != '') {
					 if(document.getElementById('paymentMethod').value == 2) {
						getVendorHandlingBankDP(0,$('select[id="vendorHandlingDp"]').val(), $('select[id="paymentFor"]').val());
						setContractHandlingDp($('select[id="stockpileVhDp"]').val(), $('select[id="vendorHandlingDp"]').val());
						getHandlingTax($('select[id="vendorHandlingDp"]').val())

					}
				}
		});

        $('#vendorHandlingBankDp').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());
            if (document.getElementById('vendorHandlingBankDp').value != '') {
                getVendorBankDetail($('select[id="vendorHandlingBankDp"]').val(), $('select[id="paymentFor"]').val());
            }
        });


        // UNLOADING DOWN PAYMENT
        $('#stockpileLaborDp').change(function () {
            resetLaborDp(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if (document.getElementById('stockpileLaborDp').value != '') {
                resetLaborDp(' ');
                setLaborDp(0, $('select[id="stockpileLaborDp"]').val(), 0);
            }
        });

        $('#stockpileId2_1').change(function () {
            resetSupplier('freightId', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();

            if (document.getElementById('stockpileId2_1').value != '') {
                resetSupplier(' ');
                setSupplier_1(0, $('select[id="stockpileId2_1"]').val(), 0);
            }
        });

        $('#laborDp').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetLaborBankDP($('select[id="paymentFor"]').val());
            //alert('html from to nya');

            if (document.getElementById('laborDp').value != '') {
                if (document.getElementById('paymentMethod').value == 2) {
                    getLaborBankDP(0, $('select[id="laborDp"]').val(), $('select[id="paymentFor"]').val());
                    getLaborTax($('select[id="laborDp"]').val());

                }
            }
        });

        $('#laborBankDp').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());

            if (document.getElementById('laborBankDp').value != '') {
                getVendorBankDetail($('select[id="laborBankDp"]').val(), $('select[id="paymentFor"]').val());
            }
        });

        //UNLOADING
        $('#stockpileId3').change(function () {
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();


            if (document.getElementById('stockpileId3').value != '') {
                resetLabor(' ');
                setLabor(0, $('select[id="stockpileId3"]').val(), 0);
            }
        });

        $('#laborId').change(function () {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorBank(' ');
            //alert('html from to nya');

            if (document.getElementById('laborId').value != '') {
                if (document.getElementById('paymentMethod').value == 1) {
                    setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val());
                    getVendorBank(0, $('select[id="laborId"]').val(), $('select[id="paymentFor"]').val());
                }
            }
        });

        $('#laborBankId').change(function () {
            resetVendorBankDetail($('select[id="paymentFor"]').val());
            if (document.getElementById('laborBankId').value != '') {
                getVendorBankDetail($('select[id="laborBankId"]').val(), $('select[id="paymentFor"]').val());
            }
        });
        //END UNLOADING

        $('#tipeBayar').change(function () {
            if (document.getElementById('tipeBayar').value == 1) {
                $('#tglBayarDiv').show();
                $("#tglBayarUrgent").prop("disabled", false).val("");
                $("#tglBayarUrgent2").prop("disabled", true);
            } else {
                $('#tglBayarDiv').show();
                $("#tglBayarUrgent").prop("disabled", true);
                $("#tglBayarUrgent2").prop("disabled", false);
                $.ajax({
                    url: 'get_data.php',
                    method: 'POST',
                    data: {
                        action: 'setPlanPayDate',
                    },
                    success: function (data) {
                        $('#tglBayarUrgent').val(data)
                        $('#tglBayarUrgent2').val(data)
                    }
                });
            }

        });

        //SUBMIT FORM
        $("#PengajuanPaymentDataForm").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
			$.blockUI({ message: '<h4>Please wait...</h4>' }); 
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
                            $('#pageContent').load('views/pengajuan-payment-admin.php', {}, iAmACallbackFunction);
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
                tglBayarUrgent: "required",
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

    $(function () {
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
        if (paymentFor == 0) {
            document.getElementById('vendorBankId').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('vendorBankId').options.add(x);
        } else if (paymentFor == 1) {
            document.getElementById('curahBankId').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('curahBankId').options.add(x);
        } else if (paymentFor == 2) {
            document.getElementById('freightBankId').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('freightBankId').options.add(x);
        } else if (paymentFor == 3) {
            document.getElementById('laborBankId').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('laborBankId').options.add(x);
        } else if (paymentFor == 9) {
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
            data: {
                action: 'getVendorBank',
                vendorId: vendorId,
                paymentFor: paymentFor
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
                    if (paymentFor == 0) {
                        if (returnValLength > 0) {
                            document.getElementById('vendorBankId').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('vendorBankId').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('vendorBankId').options.add(x);
                        }
                    } else if (paymentFor == 1) {
                        if (returnValLength > 0) {
                            document.getElementById('curahBankId').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('curahBankId').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('curahBankId').options.add(x);
                        }
                    } else if (paymentFor == 2) {
                        if (returnValLength > 0) {
                            document.getElementById('freightBankId_1').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('freightBankId_1').options.add(x);
                        }

                        if (returnValLength > 0) {
                            document.getElementById('freightBankId').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('freightBankId').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('freightBankId').options.add(x);
                            document.getElementById('freightBankId_1').options.add(x);
                        }
                    } else if (paymentFor == 3) {
                        if (returnValLength > 0) {
                            document.getElementById('laborBankId').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('laborBankId').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('laborBankId').options.add(x);
                        }
                    } else if (paymentFor == 9) {
                        if (returnValLength > 0) {
                            document.getElementById('vendorHandlingBankId').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('vendorHandlingBankId').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('vendorHandlingBankId').options.add(x);
                        }
                    }
                    if (type == 1) {
                        if (paymentFor == 0) {
                            document.getElementById('vendorBankId').value = vendorId;
                        } else if (paymentFor == 1) {
                            document.getElementById('curahBankId').value = vendorId;
                        } else if (paymentFor == 2) {
                            document.getElementById('freightBankId').value = vendorId;
                        } else if (paymentFor == 3) {
                            document.getElementById('laborBankId').value = vendorId;
                        } else if (paymentFor == 9) {
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

    function resetValidationPKHOA() {
        document.getElementById('validasiPKHOA').value = '';
    }

    function ValidationPKHOA(contractPKHOA) {
        //alert(contractPKHOA);
        if (contractPKHOA != '') {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getValidationPKHOA',
                    contractPKHOA: contractPKHOA
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
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

    function setSupplier_1(type, stockpileId_1, freightId_1) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getFreightPayment_1',
                stockpileId: stockpileId_1
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
                        document.getElementById('freightId_1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightId_1').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightId_1').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('freightId_1').value = freightId;
                    }
                }
            }
        });
    }

    function getVendorBankDetail(vendorBankId, paymentFor) {
        if (amount != '') {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getVendorBankDetail',
                    vendorBankId: vendorBankId,
                    paymentFor: paymentFor
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
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

        if (amount != '') {
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: {
                    action: 'getBankDetail',
                    bankVendor: bankVendor,
                    paymentFor: paymentFor
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
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

        if (paymentMethod == 1) {
            if (ppn != 'NONE') {  //ppn sumbernya darimana?
                if (ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph != 'NONE') {
                if (pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }

        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'refreshSummary',
                stockpileContractId: stockpileContractId,
                paymentMethod: paymentMethod,
                ppn: ppnValue,
                pph: pphValue,
                paymentType: paymentType
            },
            success: function (data) {
                if (data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                    //console.log( document.getElementById('amount').value);
                }
            }
        });
    }

    //KONTRAK
    function checkSlip(stockpileContractId) {
//        var checkedSlips = document.forms[0].checkedSlips;
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }
        setSlip(stockpileContractId, selected);
    }

    //CURAH
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
        checkSlipCurah(stockpileId, vendorId, ppn, pph, paymentFrom1, paymentTo1);
    }

    function checkSlipCurah(stockpileId, vendorId, ppn, pph, paymentFrom1, paymentTo1) {
//        var checkedSlips = document.forms[0].checkedSlips;
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }

        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn) != 'undefined' && ppn != null && typeof (pph) != 'undefined' && pph != null) {
            if (ppn != 'NONE') {
                if (ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph != 'NONE') {
                if (pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        setSlipCurah(stockpileId, vendorId, selected, ppnValue, pphValue, paymentFrom1, paymentTo1);
        //Panggil ulang function setSlipCurah dimana checkedSlips = selected (sudah ada isi)
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
        checkSlipFreight(freightId, ppn, pph, paymentFrom, paymentTo);
    }

    function checkSlipFreight_1(stockpileId_1, freightId_1, vendorFreightId_1, ppn, pph, paymentFrom_1, paymentTo_1) {

        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }

        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn) != 'undefined' && ppn != null && typeof (pph) != 'undefined' && pph != null) {
            if (ppn != 'NONE') {
                if (ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph != 'NONE') {
                if (pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        //alert(ppnValue + "," + pphValue);
        // alert(vendorFreights);
        setSlipFreight_1(stockpileId_1, freightId_1, vendorFreightId_1, selected, ppnValue, pphValue, paymentFrom_1, paymentTo_1);
    }

    function setSlipFreight_1(stockpileId_1, freightId_1, vendorFreightId_1, checkedSlips, ppn, pph, paymentFrom_1, paymentTo_1, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipFreight_1',
                stockpileId_1: stockpileId_1,
                freightId_1: freightId_1,
                vendorFreightId_1: vendorFreightId_1,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFrom_1: paymentFrom_1,
                paymentTo_1: paymentTo_1,
                idPP: idPP
            },
            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }


    function checkSlipFreight(stockpileId, freightId, vendorFreights, contractPKHOA, ppn, pph, paymentFrom, paymentTo) {

        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }

        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn) != 'undefined' && ppn != null && typeof (pph) != 'undefined' && pph != null) {
            if (ppn != 'NONE') {
                if (ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph != 'NONE') {
                if (pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        //alert(ppnValue + "," + pphValue);

        // alert(vendorFreights);

        setSlipFreight(stockpileId, freightId, vendorFreights, contractPKHOA, selected, ppnValue, pphValue, paymentFrom, paymentTo);
    }

    // function hitungDp() {
    // var a = $(".qtyFreight").val();
    // var b = $(".priceFreight").val();
    // //var b = $(".b2").val();
    // c = a * b; //a kali b
    // $(".amount").val(c);
    // }


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
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }


        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn) != 'undefined' && ppn != null && typeof (pph) != 'undefined' && pph != null) {
            if (ppn != 'NONE') {
                if (ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph != 'NONE') {
                if (pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        //alert(ppnValue + "," + pphValue);

        setSlipHandling(stockpileId, vendorHandlingId, selected, ppnValue, pphValue, paymentFrom, paymentTo);
    }

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
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }

        //alert(ppn +', '+ pph);

        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn) != 'undefined' && ppn != null && typeof (pph) != 'undefined' && pph != null) {
            if (ppn != 'NONE') {
                if (ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph != 'NONE') {
                if (pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }

        setSlipUnloading(stockpileId, laborId, selected, ppnValue, pphValue, paymentFromUP, paymentToUP);
    }


    function setSlip(stockpileContractId, checkedSlips) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlip',
                stockpileContractId: stockpileContractId,
                checkedSlips: checkedSlips
            },
            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }

    function setSlipCurah(stockpileId, vendorId, checkedSlips, ppn, pph, paymentFrom1, paymentTo1, idPP) { //checkedSlips awalnya Kosong
        //alert(vendorId +', '+ ppn +', '+ pph);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipCurah',
                vendorId: vendorId,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFrom1: paymentFrom1,
                paymentTo1: paymentTo1,
                stockpileId: stockpileId,
                idPP: idPP
            },
            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }

            }
        });
    }

    function setSlipCurah_copy(stockpileId, vendorId, checkedSlips, ppn, pph, paymentFrom1, paymentTo1, idPP) {
        console.log('copy')
        //	alert(paymentFrom1 +', '+ paymentTo1);
        //alert(checkedSlips);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipPengajuanCurah_copy',
                vendorId: vendorId,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFrom1: paymentFrom1,
                paymentTo1: paymentTo1,
                stockpileId: stockpileId,
                idPP: idPP
            },
            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    // console.log(data);
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }

    function setSlipFreight(stockpileId, freightId, vendorFreightIds, contractPKHOA, checkedSlips, ppn, pph, paymentFrom, paymentTo) {

        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipFreight',
                stockpileId: stockpileId,
                freightId: freightId,
                vendorFreightId: vendorFreightIds,
                contractPKHOA: contractPKHOA,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFrom: paymentFrom,
                paymentTo: paymentTo
            },
            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }

    function setSlipFreight_copy(stockpileId, freightId, vendorFreightIds, checkedSlips, ppn, pph, paymentFrom, paymentTo, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipFreight_copy',
                stockpileId: stockpileId,
                freightId: freightId,
                vendorFreightId: vendorFreightIds,
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


    function setSlipHandling(stockpileId, vendorHandlingId, checkedSlips, ppn, pph, paymentFromHP, paymentToHP, idPP) {
        //alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipHandling',
                stockpileId: stockpileId,
                vendorHandlingId: vendorHandlingId,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFromHP: paymentFromHP,
                paymentToHP: paymentToHP,
                idPP: idPP

            },

            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0)	//if no errors
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
            data: {
                action: 'setSlipHandling_copy',
                stockpileId: stockpileId,
                vendorHandlingId: vendorHandlingId,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFromHP: paymentFromHP,
                paymentToHP: paymentToHP,
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
            data: {
                action: 'getLaborPayment',
                stockpileId: stockpileId
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
                        document.getElementById('laborDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborDp').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborDp').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('laborDp').value = laborId;
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


    function resetLaborBankDP(paymentFor) {
        if (paymentFor == 3) {
            document.getElementById('laborBankDp').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('laborBankDp').options.add(x);
        }
    }

    function getLaborBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getVendorBank',
                vendorId: vendorId,
                paymentFor: paymentFor
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
                    if (paymentFor == 3) {
                        if (returnValLength > 0) {
                            document.getElementById('laborBankDp').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('laborBankDp').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('laborBankDp').options.add(x);
                        }
                    }
                    if (type == 1) {
                        if (paymentFor == 3) {
                            document.getElementById('laborBankDp').value = vendorId;
                        }
                    }
                }
            }
        });
    }

    // UNLOADING
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

            success: function (data) {
                if (data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }

                var return_val = data.split('|');
                if (parseInt(return_val[0]) != 0)	//if no errors
                {
                    document.getElementById('amount').value = return_val[1];
                }
            }
        });
    }

    function setSlipUnloading_copy(stockpileId, laborId, checkedSlips, ppn, pph, paymentFromUP, paymentToUP, idPP) {
        //alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setSlipPengajuanUnloading',
                stockpileId: stockpileId,
                laborId: laborId,
                checkedSlips: checkedSlips,
                ppn: ppn,
                pph: pph,
                paymentFromUP: paymentFromUP,
                paymentToUP: paymentToUP,
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
            data: {
                action: 'getLaborPayment',
                stockpileId: stockpileId
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
                        document.getElementById('laborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('laborId').value = laborId;
                    }
                }
            }
        });
    }

    //END UNLOADING

    //HANDLING DP
    function resetVhDp(text) {
        document.getElementById('vendorHandlingDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorHandlingDp').options.add(x);
    }

    function setVhDp(type, stockpileId, vendorHandlingId) {
        //alert('test');
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getHandlingPayment',
                stockpileId: stockpileId
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
                        document.getElementById('vendorHandlingDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorHandlingDp').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorHandlingDp').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('vendorHandlingDp').value = vendorHandlingId;
                    }
                }
            }
        });
    }
	
	    function resetContractHandlingDp(text) {
        document.getElementById('contractHandlingDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractHandlingDp').options.add(x);
    }

    function setContractHandlingDp(stockpileId, vendorHandlingId) {
            //alert('test1');
            $.ajax({
                url: 'get-data-Ppayment.php',
                method: 'POST',
                data: { action: 'getContractHandlingDp',
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
                            document.getElementById('contractHandlingDp').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('contractHandlingDp').options.add(x);
                        }

                        for (i=0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('contractHandlingDp').options.add(x);
                        }

                        /*if(type == 1) {
                            document.getElementById('contractHandlingDp').value = contractHandlingDp;
                        }*/
                }
            }
        });
    }

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


    function resetVendorHandlingBankDP(paymentFor) {
        if (paymentFor == 9) {
            document.getElementById('vendorHandlingBankDp').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('vendorHandlingBankDp').options.add(x);
        }
    }

    function getVendorHandlingBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getVendorBank',
                vendorId: vendorId,
                paymentFor: paymentFor
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
                    if (paymentFor == 9) {
                        if (returnValLength > 0) {
                            document.getElementById('vendorHandlingBankDp').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('vendorHandlingBankDp').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('vendorHandlingBankDp').options.add(x);
                        }
                    }
                    if (type == 1) {
                        if (paymentFor == 9) {
                            document.getElementById('vendorHandlingBankDp').value = vendorId;
                        }
                    }
                }
            }
        });
    }

    //HANDLING
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
            data: {
                action: 'getHandlingPayment',
                stockpileId: stockpileId
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
                        document.getElementById('vendorHandlingId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorHandlingId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorHandlingId').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('vendorHandlingId').value = vendorHandlingId;
                    }
                }
            }
        });
    }

    //END HANDLING

    function resetSupplierFcDp(text) {
        document.getElementById('freightIdFcDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightIdFcDp').options.add(x);
    }

    function setSupplierFcDp(type, stockpileId, freightId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getFreightPayment',
                stockpileId: stockpileId
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
                        document.getElementById('freightIdFcDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightIdFcDp').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightIdFcDp').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('freightIdFcDp').value = freightId;
                    }
                }
            }
        });
    }

    // function resetKontrakPKHOA1(text) {
    //         document.getElementById('contractPKHOA1').options.length = 0;
    //         var x = document.createElement('option');
    //         x.value = '';
    //         x.text = '-- Please Select' + text + '--';
    //         document.getElementById('contractPKHOA1').options.add(x);
    // }

    // function setKontrakPKHOA1(type, stockpileId, freightIdFcDp, contractPKHOA1) {
    //    // alert(freightIdFcDp);
    //         $.ajax({
    //             url: 'get-data-Ppayment.php',
    //             method: 'POST',
    //             data: { action: 'getDpKontrakPKHOA',
    //                     stockpileId: stockpileId,
    //                     freightIdFcDp: freightIdFcDp
    //             },
    //             success: function(data){
    //                 var returnVal = data.split('~');
    //                 if(parseInt(returnVal[0])!=0)	//if no errors
    //                 {
    //                     //alert(returnVal[1].indexOf("{}"));
    //                     if(returnVal[1] == '') {
    //                         returnValLength = 0;
    //                     } else if(returnVal[1].indexOf("{}") == -1) {
    //                         isResult = returnVal[1].split('{}');
    //                         returnValLength = 1;
    //                     } else {
    //                         isResult = returnVal[1].split('{}');
    //                         returnValLength = isResult.length;
    //                     }

    //                     //alert(isResult);
    //                     if(returnValLength > 0) {
    //                         document.getElementById('contractPKHOA1').options.length = 0;
    //                         var x = document.createElement('option');
    //                         x.value = '';
    //                         x.text = '-- Please Select --';
    //                         document.getElementById('contractPKHOA1').options.add(x);
    //                     }

    //                     for (i=0; i < returnValLength; i++) {
    //                         var x = document.createElement('option');
    //                         resultOption = isResult[i].split('||');
    //                         x.value = resultOption[0];
    //                         x.text = resultOption[1];
    //                         document.getElementById('contractPKHOA1').options.add(x);
    //                     }

    //                     if(type == 1) {
    //                         document.getElementById('contractPKHOA1').value = freightId;
    //                     }
    //                 }
    //             }
    //         });
    // }


    // function resetPriceDp(paymentFor) {
    //     document.getElementById('priceFreightDp').value = '';
    // }

    // function getPriceDp(contractPKHOA1, paymentFor) {
    //     //alert(paymentFor);
    //     if(priceFreightDp != '') {
    //         $.ajax({
    //             url: 'get-data-Ppayment.php',
    //             method: 'POST',
    //             data: { action: 'getPriceDp',
    //                     contractPKHOA1: contractPKHOA1,
    //                     paymentFor: paymentFor
    //             },
    //             success: function(data){
    //                 var returnVal = data.split('|');
    //                 if(parseInt(returnVal[0])!=0)	//if no errors
    //                 {
    //                     document.getElementById('priceFreightDp').value = returnVal[1];

    //                 }
    //             }
    //         });
    //     }
    // }

    function resetKontrakPKHOA1(text) {
        document.getElementById('contractPKHOA1').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractPKHOA1').options.add(x);
    }

    function setKontrakPKHOA1(type, stockpileIdFcDp, freightIdFcDp, vendorFreightIdDp, contractPKHOA1) {
        //  alert(vendorFreightId);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getKontrakPKHOA1',
                stockpileIdFcDp: stockpileIdFcDp,
                freightIdFcDp: freightIdFcDp,
                vendorFreightIdDp: vendorFreightIdDp
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
                        document.getElementById('contractPKHOA1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('contractPKHOA1').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractPKHOA1').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('contractPKHOA1').value = contractPKHOA;
                    }
                }
            }
        });
    }


    function resetFreightBankDP(paymentFor) {
        if (paymentFor == 2) {
            document.getElementById('freightBankDp').options.length = 0;
            var x = document.createElement('option');
            x.value = '';
            x.text = '-- Please Select --';
            document.getElementById('freightBankDp').options.add(x);
        }
    }

    function getFreightBankDP(type, vendorId, paymentFor) {
        //  alert(vendorId);
        $.ajax({
            url: 'get-data-ppayment.php',
            method: 'POST',
            data: {
                action: 'getVendorBank',
                vendorId: vendorId,
                paymentFor: paymentFor
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
                    if (paymentFor == 2) {
                        if (returnValLength > 0) {
                            document.getElementById('freightBankDp').options.length = 0;
                            var x = document.createElement('option');
                            x.value = '';
                            x.text = '-- Please Select --';
                            document.getElementById('freightBankDp').options.add(x);
                        }

                        for (i = 0; i < returnValLength; i++) {
                            var x = document.createElement('option');
                            resultOption = isResult[i].split('||');
                            x.value = resultOption[0];
                            x.text = resultOption[1];
                            document.getElementById('freightBankDp').options.add(x);
                        }
                    }
                    if (type == 1) {
                        if (paymentFor == 2) {
                            document.getElementById('freightBankDp').value = vendorId;
                        }
                    }
                }
            }
        });
    }


    function resetSupplier(text) {
        document.getElementById('freightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightId').options.add(x);
    }

    function setSupplier(type, stockpileId, freightId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getFreightPayment',
                stockpileId: stockpileId
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
                        document.getElementById('freightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightId').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('freightId').value = freightId;
                    }
                }
            }
        });
    }

    function resetVendorFreight(text) {
        document.getElementById('vendorFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorFreightId').options.add(x);
    }


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

    function resetNoKontrak(text) {
        document.getElementById('contractNoDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractNoDp').options.add(x);
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
                        document.getElementById('contractNoDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- Please Select --';
                        document.getElementById('contractNoDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractNoDp').options.add(x);
                    }

                    if(type == 1) {
                        document.getElementById('contractNoDp').value = contractNo;
                    }
                }
            }
        });
    }

    function resetVendorFreightDp(text) {
        document.getElementById('vendorFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorFreightId').options.add(x);
    }


    function setVendorFreightDp(type, stockpileId, freightId, vendorFreightId) {
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
                        document.getElementById('vendorFreightIdDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('vendorFreightIdDp').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorFreightIdDp').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('vendorFreightIdDp').value = vendorFreightId;
                    }
                }
            }
        });
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



    function resetKontrakPKHOA(text) {
        document.getElementById('contractPKHOA').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractPKHOA').options.add(x);
    }

    function setKontrakPKHOA(type, stockpileId, freightId, vendorFreightId, contractPKHOA) {
        //  alert(vendorFreightId);
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getKontrakPKHOA',
                stockpileId: stockpileId,
                freightId: freightId,
                vendorFreightId: vendorFreightId
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
                        document.getElementById('contractPKHOA').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('contractPKHOA').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractPKHOA').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('contractPKHOA').value = contractPKHOA;
                    }
                }
            }
        });
    }

    function resetVendor(elementId, text) {
        document.getElementById(elementId).options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById(elementId).options.add(x);
    }

    function setVendor(type, elementId, stockpileId, contractType, vendorId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getVendorPayment',
                stockpileId: stockpileId,
                contractType: contractType
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
                        document.getElementById(elementId).options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById(elementId).options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById(elementId).options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById(elementId).value = vendorId;
                    }
                }
            }
        });
    }

    function resetContract(text) {
        document.getElementById('stockpileContractId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('stockpileContractId').options.add(x);
    }

    function setContract(type, stockpileId, vendorId, stockpileContractId, paymentType) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'getStockpileContractPayment',
                stockpileId: stockpileId,
                vendorId: vendorId,
                paymentType: paymentType
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
                        document.getElementById('stockpileContractId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileContractId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }

                    if (type == 1) {
                        document.getElementById('stockpileContractId').value = stockpileContractId;
                    }
                }
            }
        });
    }


    // function resetPaymentType(text) {
    //     document.getElementById('paymentType').options.length = 0;
    //     var x = document.createElement('option');
    //     x.value = '';
    //     x.text = '-- Please Select' + text + '--';
    //     document.getElementById('paymentType').options.add(x);
    // }

    function setPaymentType(type, paymentType) {
        document.getElementById('paymentType').value = 2;
        // document.getElementById('paymentType').options.length = 1;
        // var x = document.createElement('option');
        // x.value = '';
        // x.text = '-- Please Select --';
        // document.getElementById('paymentType').options.add(x);

        // var x = document.createElement('option');
        // x.value = '2';
        // x.text = 'OUT / Debit';
        // document.getElementById('paymentType').options.add(x);

        // if(type == 1) {
        //     document.getElementById('paymentType').value = 2;
        // }
    }

    function setStockpileLocation(stockpileIdVal, idPP) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: {
                action: 'setStockpileLocation',
                stockpileId: stockpileIdVal,
                idPP: idPP
            },
            success: function (data) {
                //alert(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    if (returnVal[1] > 1) {
                        $('#stockpileLocationLabel').show();
                    }
                    document.getElementById('stockpileLocationDiv').innerHTML = returnVal[2];
                }
            }
        });
    }

    // Filevalidation = () => { 
    //     const fi = document.getElementById('file'); 
    //     // Check if any file is selected. 
    //     if (fi.files.length > 0) { 
    //         for (const i = 0; i <= fi.files.length - 1; i++) { 

    //             const fsize = fi.files.item(i).size; 
    //             const file = Math.round((fsize / 1024)); 
    //             // The size of the file. 
    //             if (file >= 1048) { 
    //                 alert( 
    //                   "File too Big, please select a file less than 4mb"); 
    //             } 
    //             // else if (file < 1048) { 
    //             //     alert( 
    //             //       "File too small, please select a file greater than 2mb"); 
    //             // } 
    //             else { 
    //                 document.getElementById('size').innerHTML = '<b>'
    //                 + file + '</b> KB'; 
    //             } 
    //         } 
    //     } 
    // } 

    function CompareDate() {
        //var date1 = document.getElementById('invoiceDate').value;
        //var date2 = document.getElementById('todayDate').value;
        //if(date1 > date2){
        //   alert("tanggal tidak boleh lebih besar dari hari ini")
        // }
    }

    // // Session Storage Browser
    // Object.keys(sessionStorage).forEach((key) => {
    //     var newKey = key.split('.');
    //     if (newKey[0] == "pengajuanPayment" && newKey[1] != "") {
    //         document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
    //         $('#' + newKey[1]).trigger('change');
    //     }
    // });
    // $(":input").change(function () {
    //     sessionStorage.setItem("pengajuanPayment." + this.id, this.value);
    // });
</script>

<form method="post" id="PengajuanPaymentDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_payment"/>
    <input type="hidden" name="_method" value="<?php echo $method; ?>">
    <input type="hidden" id="paymentForVal" value="<?php echo $paymentFor ?>"/>
    <input type="hidden" id="vendoridVal" value="<?php echo $vendorid ?>"/>
    <input type="hidden" id="stockpileContractIdVal" value="<?php echo $stockpileContractId ?>"/>
    <input type="hidden" id="paymentTypeVal" value="<?php echo $paymentType ?>"/>
    <input type="hidden" id="vendorFreightIds" value="<?php echo $vendorFreightIds ?>"/>
    <input type="hidden" id="idPP" name="idPP" value="<?php echo $idPP ?>"/>

    <div class="row-fluid" id="divInvoice" style="display: none;">
        <div class="span3 lightblue">
            <label>No.Invoice Vendor<span style="color: red;">*</span> </label>
            <input type="text" class="span12" tabindex="" id="invoiceNo" name="invoiceNo"
                   value="<?php echo $invoiceNo; ?>">
        </div>

        <div class="span3 lightblue">
            <label>Tanggal Invoice<span style="color: red;">*</span> </label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate"
                   value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>

        <div class="span3 lightblue">
            <label>Tax Invoice</label>
            <input type="text" class="span12" tabindex="" id="taxInvoice" name="taxInvoice"
                   value="<?php echo $taxInvoice; ?>">
        </div>

        <div class="span3 lightblue">
            <label>File Invoice<span style="color: red;">*</span> </label>
            <input type="file" placeholder="File" tabindex="" id="file" name="file" class="span12"
                   value="<?php echo $file_invoice; ?>">
            <?php if ($file_invoice != '') { ?>
                <a href="<?php echo $file_invoice; ?>" target="_blank" role="button" title="view file"><img
                            src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;"/></a>
            <?php } ?>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Metode Pembayaran<span style="color: red;">*</span> </label>
            <?php
            if ($idPP != '') { ?>
                <input type="text" readonly class="span12" tabindex="" id="paymentMethod" name="paymentMethods"
                       value="<?php echo $paymentMethods; ?>">
            <?php } else {
                createCombo("SELECT '1' as id, 'Payment' as info UNION
                            SELECT '2' as id, 'Down Payment' as info;", $paymentMethod, "", "paymentMethod", "id", "info",
                    "", "", "", 1);
            } ?>
        </div>

        <div class="span3 lightblue">
            <label>Jenis Pembayaran<span style="color: red;">*</span> </label>
            <?php
            createCombo("SELECT '2' as id, 'OUT / Debit' as info;", $paymentType, "", "paymentType", "id", "info",
                "");
            ?>
        </div>

        <div class="span1 lightblue" style="display: none;">
            <label id="stockpileLocationLabel" style="display: none;">Stockpile Location <span
                        style="color: red;">*</span></label>
        </div>
        <div class="span3 lightblue" id="stockpileLocationDiv" style="display: none;">
        </div>

        <div class="span3 lightblue">
            <label>OA/OB/Hand<span style="color: red;">*</span></label>
            <?php if ($idPP != '') { ?>
                <input type="text" id="paymentFor_1" readonly value="<?php echo $paymentFor_1 ?>"/>
            <?php } else { ?>
                <?php
                createCombo("SELECT type_transaction_id, type_transaction_name
                            FROM type_transaction", $_SESSION['payment']['paymentFor'], "", "paymentFor", "type_transaction_id", "type_transaction_name",
                    "", 3, "", 5);
                ?>
            <?php } ?>
        </div>

    </div>


    <!-- KONTRAK -->
    <div class="row-fluid" id="vendorPayment" style="display: none;">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                        FROM user_stockpile us
                        INNER JOIN stockpile s
                            ON s.stockpile_id = us.stockpile_id
                        WHERE us.user_id = {$_SESSION['userId']}
                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId'], "", "stockpileId", "stockpile_id", "stockpile_full",
                "", 9, "select2combobox100");
            ?>
        </div>

        <div class="span3 lightblue">
            <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorId", "vendor_id", "vendor_name",
                "", 10, "select2combobox100", 2);
            ?>
        </div>

        <div class="span3 lightblue">
            <label>Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorBankId", "v_bank_id", "bank_name",
                "", 10, "select2combobox100", 2);
            ?>
        </div>

        <div class="span3 lightblue">
            <label>PO No. <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "stockpileContractId", "stockpile_contract_id", "po_no",
                "", 11, "select2combobox100", 2);
            ?>
        </div>
    </div>


    <!-- CURAH -->
    <div class="row-fluid" id="curahPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span1 lightblue">
                <label>Periode <span style="color: red;">*</span>From</label>
            </div>

            <div class="span3 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom1" name="paymentFrom1"
                       value="<?php echo $paymentFrom1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>

            <div class="span1 lightblue">
                <label>Periode <span style="color: red;">*</span>To</label>
            </div>

            <div class="span3 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo1"
                       value="<?php echo $paymentTo1; ?>" name="paymentTo1" data-date-format="dd/mm/yyyy"
                       class="datepicker">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span4 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                            FROM user_stockpile us
                            INNER JOIN stockpile s
                            ON s.stockpile_id = us.stockpile_id
                             WHERE us.user_id = {$_SESSION['userId']}
                            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId1'], "", "stockpileId1", "stockpile_id", "stockpile_full",
                        "", 14, "select2combobox100");
                } else { ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>"/>
                <?php } ?>
            </div>

            <div class="span4 lightblue">
                <label>Vendor <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "vendorId1", "vendor_id", "vendor_name",
                        "", 13, "select2combobox100", 2);
                } else { ?>
                    <input type="text" readonly id="vendorName" value="<?php echo $vendorName ?>"/>
                <?php } ?>
            </div>

            <div class="span4 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "curahBankId", "v_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                } else { ?>
                    <input type="text" readonly id="vbank" value="<?php echo $vbank ?>"/>
                <?php } ?>
            </div>
        </div>
    </div>

<!-- FREIGHT DOWN PAYMENT -->
<div class="row-fluid" id="freightDownPayment" style="display: none;">
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
                                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileIdFcDp'], "", "stockpileIdFcDp", "stockpile_id", "stockpile_full",
                                    "", 2, "select2combobox100", 2);
                } else { ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>" />
            <?php } ?>
         </div>

        <div class="span3 lightblue">
            <label>Vendor Freight <span style="color: red;">*</span></label>
                <?php
                    if($idPP == ''){
                        createCombo("", "", "", "freightIdFcDp", "freight_id", "freight_supplier",
                            "", 15, "select2combobox100", 9 );
                    }else{?>
                        <input type="text" readonly id="freightName" value="<?php echo $freightName ?>" />
                <?php } ?>
        </div>

        <div class="span3 lightblue">
            <label>Bank Vendor<span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                createCombo("", "", "", "freightBankDp", "f_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                }else{?>
                    <input type="text" readonly id="freightbankName" value="<?php echo $freightbankName ?>" />
                <?php } ?>
        </div>
    </div>
    </br>

	<div class="row-fluid">
        <div class="span3 lightblue" id = "supplier">
            <labe >Supplier <span style="color: red;">*</span></label>
            <?php
                if($idPP == ''){
                    createCombo("", "", "", "vendorFreightIdDp", "vendor_id", "vendor_freight",
                    "", 15, "select2combobox100", 6);
                }else { ?>
                    <textarea class="span12" <?php echo $disableProperty1 ?> rows="1" tabindex="" id="vendorName" name="vendorName"><?php echo $vendorFreightNames; ?></textarea>
                <?php } ?>
        </div>

        <div class="span3 lightblue">
            <label>No PKS Kontrak <span style="color: red;">*</span></label>
                <?php
                    if($idPP == ''){
                    createCombo("", "", "", "contractNoDp", "contract_id", "contract_no",
                        "", 15, "select2combobox100", 7);
                    }else{?>
                        <input type="text" readonly id="contractNames" value="<?php echo $contractNames ?>" />
                    <?php } ?>
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
    <br>
    <div class="row-fluid">
        <div class="span3 lightblue">            
            <label>Quantity <span style="color: red;">*</span></label>
            <input type="text" <?php echo $disableProperty1 ?> class="span12" tabindex="" id="qtyFreight" name="qtyFreight" value="<?php echo $qty ?>" />
        </div>

	    <div class="span3 lightblue">
		    <label>Harga<span style="color: red;">*</span></label>
			<input type="text" <?php echo $disableProperty1 ?> class="span12" tabindex="" id="priceFreight" name="priceFreight" value="<?php echo $price ?>" />
        </div>

		<div class="span3 lightblue">
		    <label>Termin <span style="color: red;">*</span></label>
            <input type="text" <?php echo $disableProperty1 ?> class="span12" tabindex="" id="terminFreight" name="terminFreight" value="<?php echo $termin ?>" />
        </div>
    </div>
</div>
<!-- END DP FREIGHT -->

    <!-- FREIGHT PAYMENT / OUT-->
    <div class="row-fluid" id="freightPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom" name="paymentFrom"
                       value="<?php echo $paymentFrom1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo" name="paymentTo"
                       value="<?php echo $paymentTo1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
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

            <!-- ini untuk get vendorFreightId dan set slipFreight  -->
            <div class="span3 lightblue" id="supplier">
                <label>Supplier <span style="color: red;">*</span></label>
                    <?php

                    createCombo("", "", "", "vendorFreightId", "vendor_id", "vendor_freight",
                        "", 15, "select2combobox100", 7, "multiple");
                    ?>
            </div>

            <div class="span3 lightblue" id="contract_pkhoa">
                <label>Kontrak PKHOA <span style="color: red;">*</span></label>
                    <?php

                    createCombo("", "", "", "contractPKHOA", "freight_cost_id", "contract_pkhoa",
                        "", 15, "select2combobox100", 7);

                    ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                createCombo("", "", "", "freightBankId", "f_bank_id", "bank_name",
                    "", 10, "select2combobox100", 2);

                ?>
            </div>
        </div>
    </div>
    <!-- END FREIGHT -->

    <!-- FREIGHT PAYMENT-1 / OUT-->
    <div class="row-fluid" id="freightPayment1" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom_1" name="paymentFrom_1"
                       value="<?php echo $paymentFrom1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo_1" name="paymentTo_1"
                       value="<?php echo $paymentTo1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>

            <div class="span3 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                        FROM user_stockpile us
                                        INNER JOIN stockpile s
                                            ON s.stockpile_id = us.stockpile_id
                                        WHERE us.user_id = {$_SESSION['userId']}
                                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId2'], "", "stockpileId2_1", "stockpile_id", "stockpile_full",
                        "", 14, "select2combobox100");

                } else { ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>"/>
                <?php } ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "freightId_1", "freight_id", "freight_supplier",
                        "", 15, "select2combobox100", 2);
                } else {
                    ?>
                    <input type="text" readonly id="freightName" value="<?php echo $freightName ?>"/>
                <?php } ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "freightBankId_1", "f_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);

                } else {
                    ?>
                    <input type="text" readonly id="freightbankName" value="<?php echo $freightbankName ?>"/>

                <?php } ?>
            </div>


            <div class="span3 lightblue">
                <label>Supplier <span style="color: red;">*</span></label>
                    <?php
                    if ($idPP == '') {
                        createCombo("", "", "", "vendorFreightId_1", "vendor_id", "vendor_freight",
                            "", 15, "select2combobox100", 7, "multiple");
                    } else {
                        ?>
                        <textarea class="span12" rows="2" readonly tabindex="" id="vendorFreightNames"
                                  name="remarks"><?php echo $vendorFreightNames; ?></textarea>
                    <?php } ?>
            </div>
        </div>
    </div>
    <!-- END FREIGHT-1 -->


 <!-- UNLOADING DOWN PAYMENT -->
<div class="row-fluid" id="unloadingDownPayment" style="display: none;">
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
                            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileLaborDp", "stockpile_id", "stockpile_full",
                            "", 14, "select2combobox100");
                } else {  ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>" />
                <?php } ?>
        </div>

        <div class="span3 lightblue">
            <label>Labor <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("", "", "", "laborDp", "labor_id", "labor_name",
                            "", 15, "select2combobox100", 2);
                }else{ ?>
                    <input type="text" class="span12" tabindex="" readonly id="laborName" name="laborName" value="<?php echo $laborName ?>" />
                <?php } ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                    if($idPP == ''){
                    createCombo("", "", "", "laborBankDp", "l_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                    }else{ ?>
                        <input type="text" class="span12" tabindex="" readonly id="bankName" name="bankName" value="<?php echo $bankName ?>" />
                    <?php } ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        
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
            <?php if($idPP == ''){ ?>
			<input type="text" class="span12" tabindex="" id="qtyLabor" name="qtyLabor" value="" />
            <?php }else{?>
                <input type="text" readonly class="span12" tabindex="" id="qtyLabor" name="qtyLabor" value="<?php echo $qty; ?>" />
            <?php } ?>    
        </div>
        <div class="span3 lightblue">
                <label>Price<span style="color: red;">*</span></label>
                <?php if($idPP == ''){ ?>
                    <input type="text" class="span12" tabindex="" id="priceLabor" name="priceLabor" />
                <?php } else { ?>
                    <input type="text" class="span12" readonly tabindex="" id="priceLabor" name="priceLabor" value="<?php echo $price ?>" />
                <?php } ?>
        </div>
        <div class="span3 lightblue">
            <label>Termin <span style="color: red;">*</span></label>
            <?php if($idPP == ''){ ?>
                <input type="text" class="span12" tabindex="" id="terminLabor" name="terminLabor" value="" />
            <?php } else { ?>
                <input type="text" class="span12" tabindex="" readonly id="terminLabor" name="terminLabor" value="<?php echo $termin ?>" />
            <?php } ?>
        </div>
	</div>
</div>

    <!-- UNLOADIN PAYMENT -->
    <div class="row-fluid" id="unloadingPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span3 lightblue">
                <label>Periode From<span style="color: red;">* </span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP"
                       value="<?php echo $paymentFrom1; ?>" name="paymentFromUP" data-date-format="dd/mm/yyyy"
                       class="datepicker">
            </div>

            <div class="span3 lightblue">
                <label>Periode To<span style="color: red;">* </span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP"
                       value="<?php echo $paymentTo1 ?>" name="paymentToUP" data-date-format="dd/mm/yyyy"
                       class="datepicker">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span4 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId2'], "", "stockpileId3", "stockpile_id", "stockpile_full",
                        "", 16, "select2combobox100");

                } else { ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>"/>
                <?php } ?>
            </div>

            <div class="span4 lightblue">
                <label>Labor <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "laborId", "labor_id", "labor_name",
                        "", 15, "select2combobox100", 2);

                } else { ?>
                    <input type="text" readonly id="laborName" value="<?php echo $laborName ?>"/>
                <?php } ?>
            </div>

            <div class="span4 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "laborBankId", "l_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);
                } else {
                    ?>
                    <input type="text" readonly id="bankName" value="<?php echo $bankName ?>"/>
                <?php } ?>
            </div>

        </div>
    </div>
    <!-- END UNLOAING PAY -->

      <!-- HANDLING DOWN PAYMENT -->
     <div class="row-fluid" id="handlingDownPayment" style="display: none;">
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
                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileVhDp'], "", "stockpileVhDp", "stockpile_id", "stockpile_full",
                        "", 2, "select2combobox100", 2);   
                } else {  ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>" />
                <?php } ?>
            </div>
            <div class="span3 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                if($idPP == ''){
                    createCombo("", "", "", "vendorHandlingDp", "vendor_handling_id", "vendor_handling",
                            "", 15, "select2combobox100", 2);
                } else { ?>
                    <input type="text" readonly id="vendorHandlingName" value="<?php echo $vendorHandlingName ?>" />
                <?php } ?>
            </div>

            <div class="span3 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                    <?php
                     if($idPP == ''){
                        createCombo("", "", "", "vendorHandlingBankDp", "vh_bank_id", "bank_name",
                            "", 10, "select2combobox100", 2);
                    } else { ?>
                        <input type="text" readonly id="vhbank" value="<?php echo $vhbank ?>" />
                     <?php } ?>
            </div>

            <div class="span3 lightblue">
                <label>Contract PKS No <span style="color: red;">*</span></label>
                <?php
                 if($idPP == ''){
                    createCombo("", "", "", "contractHandlingDp", "contract_id", "contract_no",
                            "", 15, "select2combobox100", 2);
                    
                 } else { ?>
                    <textarea class="span12" rows="1" readonly tabindex="" id="contractNames" name="contractNames"><?php echo $contractNames; ?></textarea>
                <?php } ?>
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

    <!-- HANDLING -->
    <div class="row-fluid" id="handlingPayment" style="display: none;">
        <div class="row-fluid">
            <div class="span1 lightblue">
                <label>Periode<span style="color: red;">*</span> From</label>
            </div>
            <div class="span3 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromHP" name="paymentFromHP"
                       value="<?php echo $paymentFrom1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>

            <div class="span1 lightblue">
                <label>Periode <span style="color: red;">*</span> To</label>
            </div>
            <div class="span3 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToHP" name="paymentToHP"
                       value="<?php echo $paymentTo1; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span4 lightblue">
                <label>Stockpile <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId4'], "", "stockpileId4", "stockpile_id", "stockpile_full",
                        "", 16, "select2combobox100");
                } else { ?>
                    <input type="text" readonly id="stockpileName" value="<?php echo $stockpileName ?>"/>
                <?php } ?>
            </div>

            <div class="span4 lightblue">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "vendorHandlingId", "vendor_handling_id", "vendor_handling",
                        "", 15, "select2combobox100", 2);
                } else { ?>
                    <input type="text" readonly id="vendorHandlingName" value="<?php echo $vendorHandlingName ?>"/>
                <?php } ?>
            </div>

            <div class="span4 lightblue">
                <label>Bank <span style="color: red;">*</span></label>
                <?php
                if ($idPP == '') {
                    createCombo("", "", "", "vendorHandlingBankId", "vh_bank_id", "bank_name",
                        "", 10, "select2combobox100", 2);

                } else { ?>
                    <input type="text" readonly id="vhbank" value="<?php echo $vhbank ?>"/>
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
        <div class="span2 lightblue" id="amount2" style="display: none;">
            <label>Amount<span style="color: red;">*</span></label>
                <input type="text" class="span12" readonly tabindex="" id="amount" name="amount" > <!--value="<?php echo $_SESSION['payment']['amount']; ?>" -->
                <input type="hidden" class="span12" readonly tabindex="" id="originalAmount" name="originalAmount" > <!--value="<?php echo $_SESSION['payment']['amount']; ?>" -->

        </div>

        <div class="span2 lightblue" id="amount1" style="display: none;">
            <label>Amount<span style="color: red;">*</span></label>
            <input type="text" class="span12" readonly tabindex="" id="amount1" name="amount1"
                   value="<?php echo number_format($amountVal, 0, ".", ","); ?>">
        </div>

        <div class="span3 lightblue">
            <label>Penerima<span style="color: red;">*</span></label>
            <?php if ($idPP == '') { ?>
                <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary">
            <?php } else { ?>
                <input type="text" class="span12" readonly tabindex="" id="amount1" name="amount1"
                       value="<?php echo $beneficiary; ?>">
            <?php } ?>
            <input type="hidden" readonly class="span12" tabindex="" id="bank" name="bank">
            <input type="hidden" readonly class="span12" tabindex="" id="rek" name="rek">
            <input type="hidden" readonly class="span12" tabindex="" id="swift" name="swift">
        </div>


        <div class="span3 lightblue">
            <label style="color: red;">Tipe Pembayaran</label>
            <?php
            createCombo("SELECT '0' as id, 'Normal' as info UNION
			SELECT '1' as id, 'Urgent' as info;", $tipeBayar, "", "tipeBayar", "id", "info", "", "", "", 0);
            ?>
        </div>

        <div class="span2 lightblue" id="tglBayarDiv">
            <label>Tanggal Request <span style="color: red;">*</span> </label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="tglBayarUrgent" name="tglBayarUrgent"
                   value="<?php echo $tglBayarUrgent ?>" data-date-format="dd/mm/yyyy" class="datepicker" disabled>
            <input type="hidden" placeholder="DD/MM/YYYY" tabindex="" id="tglBayarUrgent2" name="tglBayarUrgent"
                   value="<?php echo $tglBayarUrgent ?>" disabled>

        </div>


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


