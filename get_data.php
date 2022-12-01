<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('y');
$currentYearMonth = $date->format('ym');

switch ($_POST['action']) {
    case "setPlanPayDateGeneral":
        setPlanPayDateGeneral($todayDate);
        break;
    case "getValidationRSB":
        getValidationRSB($_POST['stockpileId'], $_POST['qty_rsb']);;
    break;
    case "getValidationGGL":
        getValidationGGL($_POST['stockpileId'], $_POST['qty_ggl']);;
    break;
    case "getValidationRG":
        getValidationRG($_POST['stockpileId'], $_POST['qty_RG']);;
    break;
    case "getValidationUN":
        getValidationUN($_POST['stockpileId'], $_POST['qty_UN']);;
    break;
    case "setPengajuanGeneralDetail":
        setPengajuanGeneralDetail();
        break;
    case "getMonth":
        getMonth($_POST['date']);
        break;
    case "getWeek":
        getWeek($_POST['date']);
        break;
    case "getUnloadingCost":
        getUnloadingCost($_POST['stockpileId'], $currentDate, $_POST['unloadingCostId']);
        break;
    case "getVendor":
        getVendor($_POST['stockpileId'], $_POST['newVendorId']);
        break;
   case "getSpBlockData":
        getSpBlockData($_POST['stockpileId'], $_POST['ggl'], $_POST['rsb'], $_POST['rg'], $_POST['un'], $_POST['newblock']);
    break;
    case "getSupplier":
        getSupplier();
        break;
    case "getFreightCost":
        getFreightCost($_POST['stockpileId'], $_POST['vendorId'], $currentDate, $_POST['freightCostId'], $_POST['trxDate']);
        break;
    case "getStockpileContractTransaction":
        getStockpileContractTransaction($_POST['stockpileId'], $_POST['vendorId']);
        break;
    case "getStockpileContract":
        getStockpileContract($_POST['stockpileId'], $_POST['vendorId']);
        break;
    case "getContractDetail":
        getContractDetail($_POST['stockpileContractId']);
        break;
    case "getFreightDetail":
        getFreightDetail($_POST['freightCostId']);
        break;
    case "getUnloadingDetail":
        getUnloadingDetail($_POST['unloadingCostId']);
        break;
    case "getSales":
        getSales($_POST['customerId'], $_POST['stockpileId']);
        break;
    case "getShipment":
        getShipment($_POST['salesId'], $_POST['shipmentId']);
        break;
    case "getShipmentPayment":
        getShipmentPayment($_POST['customerId']);
        break;
    case "getShipmentDetail":
        getShipmentDetail($_POST['shipmentId']);
        break;
    case "getCustomer":
        getCustomer();
        break;
    case "getAccount":
        getAccount($_POST['paymentFor'], $_POST['paymentMethod'], $_POST['paymentType']);
        break;
    case "getBank":
        getBank();
        break;
    case "setSlip":
        setSlip($_POST['stockpileContractId'], $_POST['checkedSlips']);
        break;
    case "getStockpileContractPayment":
        getStockpileContractPayment($_POST['stockpileId'], $_POST['vendorId'], $_POST['paymentType']);
        break;
    case "getVendorPayment":
        getVendorPayment($_POST['stockpileId'], $_POST['contractType']);
        break;
    case "getFreightPayment":
        getFreightPayment($_POST['stockpileId']);
        break;
    case "getLaborPayment":
        getLaborPayment($_POST['stockpileId']);
        break;
    case "refreshSummary":
        refreshSummary($_POST['stockpileContractId'], $_POST['paymentMethod'], $_POST['ppn'], $_POST['pph'], $_POST['paymentType']);
        break;
    case "setExchangeRate":
        setExchangeRate($_POST['bankId'], $_POST['currencyId'], $_POST['journalCurrencyId']);
        break;
    case "setCurrencyId":
        setCurrencyId($_POST['paymentType'], $_POST['accountId'], $_POST['salesId']);
        break;
   case "setSlipCurah":
        setSlipCurah($_POST['stockpileId'], $_POST['vendorId'], $_POST['contractCurah'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'],$_POST['paymentFrom1'], $_POST['paymentTo1']);
        break;
    case "setSlipFreight":
        setSlipFreight($_POST['stockpileId'], $_POST['freightId'], $_POST['contractFreight'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo']);
        break;
    case "setSlipUnloading":
        setSlipUnloading($_POST['stockpileId'], $_POST['laborId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromUP'], $_POST['paymentToUP']);
        break;
    case "setSlipShipment":
        setSlipShipment($_POST['salesId']);
        break;
    case "refreshSummaryShipment":
        refreshSummaryShipment($_POST['salesId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph']);
        break;
    case "getLabor":
        getLabor();
        break;
    case "getGeneralVendor":
        getGeneralVendor();
        break;
    case "getVendorReport":
        getVendorReport($_POST['paymentType'], $_POST['stockpileId']);
        break;
    case "getGeneralVendorTax":
        getGeneralVendorTax($_POST['generalVendorId'], $_POST['amount'], $_POST['tax_pph1']);
    break;
    case "setPaymentLocation":
        setPaymentLocation();
        break;
    case "setStockpileLocation":
        setStockpileLocation();
        break;
    case "refreshPo":
        refreshPo($_POST['po_pks_id']);
        break;
    case "getContractPO":
        getContractPO($currentYearMonth, $_POST['contractType'], $_POST['vendorId'], $_POST['contractSeq']);
        break;
    case "getGeneratedContract":
        getGeneratedContract($_POST['contract_no']);
        break;
    case "getInvoiceNo":
        getInvoiceNo($currentYearMonth);
        break;
    case "getAccountInvoice":
        getAccountInvoice($_POST['invoiceType']);
        break;
	case "getGVprediksi":
        getGVprediksi($_POST['shipmentId'], $_POST['accountId'], $_POST['prediksiDetailId']);
    break;
    case "getAccountInvoicePrediksi":
        getAccountInvoicePrediksi($_POST['shipmentId1'], $_POST['prediksiDetailId']);
    break;
    case "getPoInvoice":
        getPoInvoice($_POST['accountId'], $_POST['stockpileId2']);
        break;
    case "getPO_OKS_AKT":
        getPO_OKS_AKT($_POST['gvId']);
    break;
        
    case "getInvoice":
        getInvoice($_POST['paymentFor'], $_POST['gvId']);
        break;
    case "refreshInvoice":
        refreshInvoice($_POST['invoiceId'], $_POST['paymentMethod'], $_POST['ppn1'], $_POST['pph1']);
        break;
    case "setInvoiceDP":
        setInvoiceDP($_POST['podID'], $_POST['generalVendorId'], $_POST['method'], $_POST['checkedSlips'], $_POST['checkedSlips2'], $_POST['checkedSlips3'], $_POST['checkedSlips4'], $_POST['ppn1'], $_POST['pph1']);
        break;
    case "setInvoiceDetail":
        setInvoiceDetail();
        break;
    case "getJurnalNo":
        getJurnalNo($currentYearMonth);
        break;
    case "getJurnalSlip":
        getJurnalSlip($_POST['stockpile_id']);
        break;
    case "getJurnalPo":
        getJurnalPo($_POST['stockpile_id']);
        break;
    case "getJurnalShipment":
        getJurnalShipment($_POST['stockpile_id']);
        break;
    case "getJurnalInvoice":
        getJurnalInvoice($_POST['stockpile_id']);
        break;
    case "setJurnalDetail":
        setJurnalDetail();
        break;
    case "getAccountPC":
        getAccountPC($_POST['paymentFor'], $_POST['paymentMethod'], $_POST['paymentType']);
        break;
    case "getAccountPaymentCash":
        getAccountPaymentCash($_POST['paymentCashType']);
        break;
    case "getGeneralVendorTax2":
        getGeneralVendorTax2($_POST['generalVendorId11'], $_POST['amount11']);
        break;
    case "setCashDP":
        setCashDP($_POST['generalVendorId11'], $_POST['ppn11'], $_POST['pph11']);
        break;
    case "setPaymentDetail":
        setPaymentDetail();
        break;
    case "getBankPC":
        getBankPC($_POST['paymentMethod']);
        break;
    case "setAmountPayment":
        setAmountPayment();
        break;
    case "getDestination":
        getDestination($_POST['periode'], $_POST['stockpile_id']);
        break;
    case "getBankDetail":
        getBankDetail($_POST['bankVendor'], $_POST['paymentFor']);
        break;
    case "getVendorFreight":
        getVendorFreight($_POST['stockpileId'], $_POST['freightId']);
        break;
    case "getHandlingCost":
        getHandlingCost($_POST['stockpileId'], $_POST['vendorId'], $currentDate, $_POST['handlingCostId']);
        break;
    case "getHandlingDetail":
        getHandlingDetail($_POST['handlingCostId']);
        break;
    case "getHandlingPayment":
        getHandlingPayment($_POST['stockpileId']);
        break;
    case "setSlipHandling":
        setSlipHandling($_POST['stockpileId'], $_POST['vendorHandlingId'], $_POST['contractHandling'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromHP'], $_POST['paymentToHP']);
        break;
    case "getContractAdjustmentDetail":
        getContractAdjustmentDetail($_POST['contractAdjustmentDetail']);
        break;
    case "getFreightReport":
        getFreightReport($_POST['stockpileId']);
        break;
    case "getPONo":
        getPONo($currentYearMonth);
        break;
    case "setPODetail":
        setPODetail($_POST['generatedPONo'], $_POST['idPOHDR'],  $_POST['gvid'], $todayDate);
        break;
    case "getItemNo":
        getItemNo($_POST['groupitemId']);
        break;
    case "getItem":
        getItem($_POST['groupItemId']);
        break;
    case "getDBsum":
        getDBsum($_POST['requestDate'], $_POST['intervalmonth'], $_SESSION['userId']);
        break;
    case "getSuratTugas":
        getSuratTugas($_POST['stockpileId'], $_POST['vendorId']);
        break;
    case "getSuratTugasDetail":
        getSuratTugasDetail($_POST['idSuratTugas']);
        break;
    case "getVendorNewNotim":
        getVendorNewNotim($_POST['tiketId'], $_POST['newVendorId']);
        break;
    case "getStockpileContractNewNotim":
        getStockpileContractNewNotim($_POST['tiketId'], $_POST['stockpileId'], $_POST['vendorId']);
        break;
    case "getContractDoc":
        getContractDoc($_POST['contract']);
        break;
    case "getSPBDoc":
        getSPBDoc($_POST['spb']);
        break;
    case "getVendorPOPKS":
        getVendorPOPKS($_POST['vendorId']);
        break;
    case "getStockpilePOPKS":
        getStockpilePOPKS($_POST['stockpileId']);
        break;
    case "getConDoc":
        getConDoc($_POST['contract']);
        break;
    case "getConDoc_curah":
        getConDoc_curah($_POST['contract']);
    break;
    case "getContractDoc_curah":
        getContractDoc_curah($_POST['contract']);
    break;
    case "getSPB":
        getSPBDoc($_POST['spb']);
        break;
    case "getGeneralVendorPPh":
        getGeneralVendorPPh($_POST['generalVendorId']);
        break;
    case "getVendorBank":
        getVendorBank($_POST['vendorId'], $_POST['paymentFor']);
        break;
    case "getVendorBankDetail":
        getVendorBankDetail($_POST['vendorBankId'], $_POST['paymentFor']);
        break;
    case "getBankName":
        getBankName($_POST['masterBankId']);
        break;
    case "getPaymentNo":
        getPaymentNo($_POST['searchPeriodFrom'], $_POST['searchPeriodTo']);
        break;
    case "getFreightName":
        getFreightName($_POST['vendorId']);
        break;
    case "getShipmentInvoice":
        getShipmentInvoice($_POST['stockpileId2']);
        break;
	 case "getShipmentInvoicePrediksi":
        getShipmentInvoicePrediksi($_POST['invoiceType'], $_POST['prediksiDetailId']);
    break;
    case "getShipmentAudit":
        getShipmentAudit($_POST['stockpileId']);
        break;
    case "getUnitCost":
        getUnitCost($_POST['shipmentId']);
        break;
    case "getShipmentReturn":
        getShipmentReturn($currentYear);
        break;
    case "getSalesPriceReturn":
        getSalesPriceReturn($_POST['shipmentId']);
        break;
    case "getInvCategory":
        getInvCategory($_POST['categoryId'], $_POST['newInvCategoryId']);
        break;
    case "getPPNPPH":
        getPPNPPH($_POST['vendorType'], $_POST['vendorName']);
        break;
    case "getGeneralVendorPPN":
        getGeneralVendorPPN($_POST['generalVendorId']);
        break;
    case "getMutasiHeader":
        getMutasiHeader();
        break;
    case "setMutasiDetail":
        setMutasiDetail($_POST['mutasiId']);
        break;
    case "getVendorBankPO":
        getVendorBankPO($_POST['vendorId']);
        break;
    case "getStockpileContractShipment":
        getStockpileContractShipment($_POST['stockpileId']);
        break;
    case "getNumberPO":
        getNumberPO($_POST['vendorId']);
        break;
    case "getPoNoCurah":
        getPoNoCurah($_POST['stockpileId'], $_POST['vendorId'], $_POST['stockpileContractId']);
        break;
    case "getPengajuanNo":
        getPengajuanNo($currentYearMonth);
        break;
    case "setSlipUnloading2":
        setSlipUnloading2($_POST['stockpileId'], $_POST['laborId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['transactionId']);
        break;
    case "setSlipFreight2":
        setSlipFreight2($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorFreightId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['transactionId']);
    case "getSlipNo":
        getSlipNo($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorFreightId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo']);
        break;
    case "getSlipNoUnloading":
        getSlipNoUnloading($_POST['stockpileId'], $_POST['laborId'], $_POST['paymentFrom'], $_POST['paymentTo']);
        break;
    case "getVendorHandlingReport":
        getVendorHandlingReport($_POST['stockpileId']);
        break;
	case "getContractHandlingDp":
        getContractHandlingDp($_POST['stockpileId'],$_POST['vendorHandlingId']);
        break;
	case "getContractHandling":
        getContractHandling($_POST['stockpileId'],$_POST['vendorHandlingId']);
        break;
	case "getContractFreightDp":
        getContractFreightDp($_POST['stockpileId'],$_POST['freightId']);
        break;
	case "getContractFreight":
        getContractFreight($_POST['stockpileId'],$_POST['freightId']);
        break;
	case "getContractCurahDp":
        getContractCurahDp($_POST['stockpileId'],$_POST['vendorId']);
        break;
	case "getContractCurah":
        getContractCurah($_POST['stockpileId'],$_POST['vendorId']);
        break;
	
	case "getValidateInvoiceNo":
        getValidateInvoiceNo($_POST['invoiceNo']);
    break;
	
	case "setSlipFreightPC":
        setSlipFreightPC($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorFreightId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo']);
        break;
    case "getVendorGGL_SRB":
        getVendorGGL_SRB($_POST['vendor_id']);
    break;
    case "getShipmentGGL_SRB":
        getShipmentGGL_SRB($_POST['salesId']);
    break;
    // case "getAvailableQty_RSB":
    //     getAvailableQty_RSB($_POST['stockpileId']);
    // break;
    // case "getAvailableQty_ggl":
    //     getAvailableQty_ggl($_POST['stockpileId']);
    // break;
    // case "getAvailableQty_RG":
    //     getAvailableQty_RG($_POST['stockpileId']);
    // break;
    // case "getAvailableQty_uncertified":
    //     getAvailableQty_uncertified($_POST['stockpileId'], $_POST['transactionDate']);
    // break;
    case "getAvailableQtyAll":
        getAvailableQtyAll($_POST['stockpileId']);
    break;

    case "getShipmentGGL_SRB1":
        getShipmentGGL_SRB1($_POST['shipmentId']);
      
	case "addNewData":
        addNewData($_POST['headerCostingID'], $_POST['stockpileId'], $_POST['qtyVessel'], $_POST['prediksiId'],  $_POST['exRateCosting'], $_POST['kodeprediksi']);
    break;


    case "insertTempCosting":
        insertTempCosting($_POST['headerCostingID'], $_POST['kodeprediksi']);
    break;
    case "insertTempCosting_Edit":
        insertTempCosting_Edit($_POST['prediksiId']);
    break;
    case "getTempCosting":
        getTempCosting($_POST['prediksiId']);
    break;
    
    case "hitungCosting":
        hitungCosting($_POST['qty'], $_POST['tempID']);
    break;
    case "hitungCosting1":
        hitungCosting1($_POST['qtyVessel'], $_POST['exRateCosting']);
    break;
    case "getShipmentCosting":
        getShipmentCosting($_POST['stockpileId']);
    break;
    case "getSalesPrediksi":
        getSalesPrediksi($_POST['shipmentId']);
    break;
    case "getLoadingPort":
        getLoadingPort($_POST['headerCostingID']);
    break;
     case "getGenerateCodePrediksi":
        getGenerateCodePrediksi($currentYearMonth);
    break;
    case "getGenerateCodeInv":
        getGenerateCodeInv($_POST['stockpileId'], $currentYearMonth);
    break;

     case "getValueUpdate":
        getValueUpdate($_POST['detailId'], $_POST['priceMT'], $_POST['qty']);
    break;
	
	case "getPrediksiId":
        getPrediksiId($_POST['shipmentId'], $_POST['tipeBiayaId'], $_POST['generalVendorId'], $_POST['prediksiId']);
    break;
	 case "setPrediksiDetail":
        setPrediksiDetail($_POST['prediksiId'], $_POST['status']);
    break;
	
	case "getAccountPrediksi":
        getAccountPrediksi($_POST['biayaId']);
    break;
	
	case "setPlanPayDate":
        setPlanPayDate($todayDate);
    break;
    case "getAccountPayment":
        getAccountPayment($_POST['paymentFor'], $_POST['bankIdSp']);
        break;
    case "refreshInvoice":
        refreshInvoice($_POST['invoiceId'], $_POST['paymentMethod'], $_POST['ppn1'], $_POST['pph1']);
    break;
    case "refreshInvoiceOA":
        refreshInvoiceOA($_POST['invoiceOAId'], $_POST['paymentMethod']);
        break;
	case "refreshInvoiceOB":
        refreshInvoiceOB($_POST['invoiceOBId'], $_POST['paymentMethod']);
        break;
	case "refreshInvoiceHandling":
        refreshInvoiceHandling($_POST['invoiceHandlingId'], $_POST['paymentMethod']);
        break;
	case "refreshInvoiceCurah":
        refreshInvoiceCurah($_POST['invoiceCurahId'], $_POST['paymentMethod']);
        break;
    case "refreshInvoicePayment":
        refreshInvoicePayment($_POST['invoiceId']);
    break;
    case "refreshInternalTransfer":
        refreshInternalTransfer($_POST['internalTF']);
    break;
    case "refreshInvoiceOAPayment":
        refreshInvoiceOAPayment($_POST['invoiceOAId']);
    break;
    case "refreshInvoiceHandlingPayment":
        refreshInvoiceHandlingPayment($_POST['invoiceHandlingId']);
    break;
	case "refreshInvoiceOBPayment":
        refreshInvoiceOBPayment($_POST['invoiceOBId']);
    break;
	case "refreshInvoiceCurahPayment":
        refreshInvoiceCurahPayment($_POST['invoiceCurahId']);
    break;
    case "getTax_PPH":
        getTax_PPH($_POST['pphTaxId'], $_POST['amount']);
    break;
    case "getQtyPo":
        getQtyPo($_POST['poId'], $_POST['gvId'], $todayDate);
    break;
    case "QtyValidation":
        QtyValidation($_POST['quantity'], $_POST['contractId']);
    break;
    case "searchPrice":
        searchPrice($_POST['tempPrice'], $_POST['gvId'], $_POST['contractId'], $todayDate);
    break;
    case "setMethod":
        setMethod($_POST['invoiceMethod'], $_POST['pgId']);
    break;
    case "getVendorEmail":
        getVendorEmail($_POST['generalVendorId']);
    break;

    case "getEmails":
        getEmails($_POST['invId']);
    break;
    case "setVendorOKS":
        setVendorOKS($_POST['oksAKT'], $_POST['vendorId']);
    break;
    case "getPKSOks":
        getPKSOks();
    break;
    // case "check_price_oks_akt":
    //     check_price_oks_akt($_POST['vendorId'], $_POST['gvId'], $_POST['priceOKS'], $todayDate);
    // break;
    case "setSlipVehicle":
        setSlipVehicle($_POST['stockpileId'], $_POST['checkedSlips'],  $_POST['periodeFrom'], $_POST['periodeTo'], $_POST['vendorId'], $_POST['gvId'], $_POST['pg_id'], $todayDate);
    break;
    case "getContractNo":
        getContractNo($_POST['contractType']);
    break;
};

function getContractNo($contractType)
{
    global $myDatabase;
    $returnValue = '';

    if($contractType == 'P'){
        $sql = "SELECT po.*
                FROM po_pks po 
                LEFT JOIN purchasing pur ON pur.purchasing_id = po.purchasing_id 
                WHERE po.po_status = 0 
                AND po.quantity > 0 AND po.reject_status = 0 AND pur.type = 1
                ORDER BY po.po_pks_id DESC";
    }else if($contractType == 'C'){
        $sql = "SELECT po.*
        FROM po_pks po 
        LEFT JOIN purchasing pur ON pur.purchasing_id = po.purchasing_id 
        WHERE po.po_status = 0 
        AND po.quantity > 0 AND po.reject_status = 0 AND pur.type = 2
        ORDER BY po.po_pks_id DESC";
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);    
    

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->po_pks_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->po_pks_id . '||' . $row->contract_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;

}

// function check_price_oks_akt($vendorId, $gvId, $priceOKS, $todayDate)
// {
//     global $myDatabase;
    
//    // $price = str_replace( ',', '', $tempPrice);
//     $sql = "SELECT ok.oks_akt_id, ok.general_vendor_id, ok.type, ok.active_date, round(ok.price) as price1
//              FROM oks_akt ok 
//              LEFT JOIN temp_oks_akt tok ON tok.id_oks_akt = ok.oks_akt_id
//              WHERE ok.general_vendor_id = {$gvId}  AND ok.vendor_id = {$vendorId} AND ok.type = 3 
//              AND round(ok.price) = {$priceOKS}
//            --  AND ok.oks_akt_id NOT IN (SELECT id_oks_akt FROM temp_oks_akt_others ) 
//              AND ok.active_date <= '{$todayDate}' ORDER BY active_date DESC";
//   //  echo $sql;
//     $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//     if($result !== false && $result->num_rows > 0){
//         $tempDate = '';
//         $flag = 0;
//         while($row = $result->fetch_object()) { 
//         //    if($row->price1 == $tempPrice){ 
//                 $oksAKT = 0;
//                 if($tempDate == ''){
//                     $tempDate = $row->active_date;
//                     if($tempDate <= $todayDate && $row->price1 == $priceOKS){
//                         $flag = 1;
//                         $oksAKT = $row->oks_akt_id;
//                         break;
//                     }
//                 }
//                 else if($tempDate <= $row->active_date){
//                     $tempDate = $row->active_date;
//                     if($tempDate <= $todayDate){
//                         $flag = 1;
//                         $oksAKT = $row->oks_akt_id;
//                         break;
//                     }
//                 }
//            // }
//         }
//     }

//     if ($flag == 1) {
//         $returnValue ='|' . 0 . ' | ' . $oksAKT;
//     }else{
//           $returnValue ='|' . 1 . ' | ' . 0;
//     }
//     echo $returnValue;
// }

function setSlipVehicle($stockpileId, $checkedSlips, $periodeFrom, $periodeTo, $vendorId, $gvId, $pg_id, $todayDate)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $boolCheked = false;
    $whereContractIds = '';
    $count_slip = 0;
    $total_pph = 0;
    $grand_total = 0;

    // if($pg_id == ''){
    //     $whereProperty = "AND t.company_id = {$_SESSION['companyId']}
    //                     AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
    //                       AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo1', '%d/%m/%Y')
    //                       AND t.payment_id IS NULL AND t.fc_payment_status <> 1";
    // }else if($pg_id != ''){
    //     $selectPaymentC = "pc.qty, pc.dpp, pc.total_amount as totalAmount, pc.ppn_id, pc.ppn_value, pc.pph_id, pc.pph_value, pp.total_dpp,  
    //                          pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
    //     $whereIdPP = " AND pc.idPP = {$idPP}";
    //     $wherePengajuan = " LEFT JOIN payment_curah pc ON pc.transaction_id = t.transaction_id
    //                         LEFT JOIN pengajuan_payment pp ON pp.idPP = pc.idpp ";
    // }
    
    $sql_gv = "SELECT pph_tax_id, round(pph) as pph FROM general_vendor WHERE general_vendor_id = {$gvId}";
    $result_gv = $myDatabase->query($sql_gv, MYSQLI_STORE_RESULT);
    if ($result_gv->num_rows == 1) {
        $row_gv = $result_gv->fetch_object();
        $pph_id = $row_gv->pph_tax_id;
        $pph_value = $row_gv->pph;
    }

    $sql = "SELECT * FROM (
        SELECT tt.transaction_date,
        oks.oks_akt_id,
        tt.vehicle_no,
        v.vendor_name, 
        t.slip_no, 
        s.stockpile_name, 
        vh.vehicle_name, 
        c.contract_no, 
        round(tt.quantity) as qty,
        tt.slip,
        tt.driver,
        tt.vendor_id,
        tt.transaction_id,
        CASE WHEN tt.quantity < 15000 THEN 'Small Car' ELSE 'Big Car' END AS type_cars, 
        CASE WHEN tt.quantity < 15000 AND oks.car_type = 1 
                AND oks.active_date <= '{$todayDate}' 
                AND oks.type = 3 
                THEN oks.price ELSE 0 END AS smallPrice, 
        CASE WHEN tt.quantity > 15000 
                AND oks.car_type = 2 
                AND oks.active_date <= '{$todayDate}' 
                AND oks.type = 3 
                THEN oks.price ELSE 0 END AS bigPrice
        FROM transaction_timbangan tt 
          LEFT JOIN `transaction` t ON tt.transaction_id = t.t_timbangan 
          LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id 
          LEFT JOIN contract c ON sc.contract_id = c.contract_id 
          LEFT JOIN vendor v ON tt.vendor_id = v.vendor_id 
          LEFT JOIN stockpile s ON tt.stockpile_id = s.stockpile_id 
          LEFT JOIN unloading_cost uc ON tt.unloading_cost_id = uc.unloading_cost_id 
          LEFT JOIN vehicle vh ON uc.vehicle_id = vh.vehicle_id 
          LEFT JOIN oks_akt oks ON oks.vendor_id = tt.vendor_id 
        WHERE tt.transaction_date BETWEEN STR_TO_DATE('{$periodeFrom}', '%d/%m/%Y') 
          AND STR_TO_DATE('{$periodeTo}', '%d/%m/%Y') 
          AND tt.netto_weight >= '300' 
          AND tt.tarra_weight != '0' 
          AND tt.send_weight != '0' 
          AND t.slip_retur IS NULL 
          AND t.notim_status = 0 
          AND tt.stockpile_id = {$stockpileId} 
          AND tt.vendor_id = {$vendorId} 
          AND oks.general_vendor_id = {$gvId} 
          AND t.slip_no NOT IN (
            SELECT slip_no 
            FROM temp_oks_akt_others WHERE status = 0
          )
      ) AS vehicleData 
      WHERE vehicleData.smallPrice > 0 OR vehicleData.bigPrice > 0";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// echo "Vehicle <br> " .$sql;
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onClick="checkAll(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th></th>
                                <th>Stockpile</th></th>
                                <th>Transaction Date</th>
                                <th>Vehicle No</th>
                                <th>Vehicle</th>
                                <th>Cars Type</th>
                                <th>Driver</th>
                                <th>Vendor Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>PPh</th>
                                <th>Total Amount</th>
                                <th>Slip</th>
                                <th>Contract No</th>
                         </thead>';
        $returnValue .= '<tbody>';

        $temp_total_price = 0;
        $temp_pph = 0;
        $temp_total_amount = 0 ;
        $sub_total = 0;
        $total_price = 0;
        $total_pph = 0;
        $priceArr = array();

       // echo " TEST PG " . $pg_id;

        while ($row = $result->fetch_object()) {
            $vendorName = '';
            $vendorName = $row->vendor_name;
            $price = $row->qty > 15000 ? $row->bigPrice : $row->smallPrice;
            $temp_pph = $pph_value/100 * $price;
            $temp_total_amount = $price -  $temp_pph;

            // echo " EE " .$checkedSlips ;

            $returnValue .= '<tr>';
            if($pg_id == ''){                
                if ($checkedSlips != '') {
                    $boolCheked = true;
                    $pos = strpos($checkedSlips, $row->transaction_id);

                    if ($pos === false) {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $stockpileId . ',' .' \'' . $periodeFrom . '\', \'' . $periodeTo . '\', ' .$row->vendor_id .', '.$gvId.');" /></td>';
                    } else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $stockpileId . ',' . ' \'' . $periodeFrom . '\', \'' . $periodeTo . '\', ' .$row->vendor_id .' , ' .$gvId. ');" checked /></td>';
                        $temp_total_price += $price;
                        $PPh = ($pph_value/100) * $price;
                        $grand_total += $price-$PPh;
                        $total_pph += $PPh;
                        if (!in_array($price, $priceArr)) array_push($priceArr, $price);
                        $count_slip++;
                    }
                    
                } else {
                    $boolCheked = false;
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $stockpileId . ',' . ' \'' . $periodeFrom . '\', \'' . $periodeTo . '\', ' .$row->vendor_id .', '.$gvId. ');" /></td>';
                }
            } else {
                $boolCheked = true;
                $returnValue .= '<td><input type="hidden" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $stockpileId . ',' .' \'' . $periodeFrom . '\', \'' . $periodeTo . '\', ' .$row->vendor_id .', ' . $gvId . ');" /></td>';
            }

            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->vehicle_no . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->vehicle_name . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->type_cars. '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->driver . '</td>';
            $returnValue .= '<td style="width: 25%;">' . $row->vendor_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qty, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($temp_pph, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($temp_total_amount, 2, ".", ",") . '</td>';
            
            $returnValue .= '<td style="width: 15%;">' . $row->slip . '</td>';
            $returnValue .= '<td style="width: 25%;">' . $row->contract_no . '</td>';
            $returnValue .= '</tr>';
        }
     
        $returnValue .= '</tbody>';
        if ($boolCheked) {
            $returnValue .= '<tfoot>';
            // print_r($priceArr[0]);
              // $checks = $_POST['checkedSlips'];
              for ($i = 0; $i < sizeof($priceArr); $i++) {
                  $temp_priceArr += $priceArr[$i];
              }
            //  echo $temp_priceArr;

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td  colspan="2" style="text-align: right;">' . number_format($grand_total, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '</tfoot>';
        }

        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $count_slip . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . $grand_total . '" />';
        $returnValue .= '<input type="hidden" id="pph_id" name="pph_id" value="' . $pph_id . '" />';
        $returnValue .= '<input type="hidden" id="pph_value" name="pph_value" value="' . $pph_value . '" />';
        $returnValue .= '<input type="hidden" id="total_pph" name="total_pph" value="' . $total_pph . '" />';
        $returnValue .= '<input type="hidden" id="priceOKS" name="priceOKS" value="' . $temp_priceArr . '" />'; 
        $returnValue .= '<input type="hidden" id="total_dpp" name="total_dpp" value="' . $temp_total_price . '" />'; 
        $returnValue .= '</div>';
    }
   echo $returnValue;
}

//General
function getEmails($invId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pgd.vendor_email, pg.pic_email FROM pengajuan_general pg
            LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id
            WHERE pg.invoice_id = {$invId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);    
    

    if ($result->num_rows >0) {
        $row = $result->fetch_object();
        $returnValue = '|' . $row->vendor_email . '|' . $row->pic_email;

    } else {
        $returnValue = '|-';
    }
// echo $sql;
    echo $returnValue;
}

function setVendorOKS($oksAkt, $vendorId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';

    if($oksAkt == 3){
        $whereProperty = " WHERE ok.vendor_id = {$vendorId} AND ok.type = 3";
    }

    if($oksAkt == 1){
        $sql = "SELECT gv.general_vendor_id, gv.general_vendor_name FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name";
    }else{
        $sql = "SELECT gv.`general_vendor_name`, gv.`general_vendor_id` FROM oks_akt ok
                INNER JOIN general_vendor gv ON gv.`general_vendor_id` = ok.`general_vendor_id`
                {$whereProperty}
                GROUP BY gv.general_vendor_name";
    }
   //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows >= 1) {
        while ($row = $result->fetch_object()) {
            if ($row->general_vendor_id > 0) {
                //if($row->contract_amount  > $row->paid_amount)  {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->general_vendor_id . '||' . $row->general_vendor_name;
                } else {
                    $returnValue = $returnValue . '{}' . $row->general_vendor_id . '||' . $row->general_vendor_name;
                }
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getPKSOks()
{
    global $myDatabase;
    $returnValue = '';

   
    $sql = "SELECT v.`vendor_name`, ok.`vendor_id` FROM oks_akt ok
            INNER JOIN vendor v ON v.`vendor_id` = ok.`vendor_id`
            WHERE ok.type = 3
            GROUP BY v.vendor_name";
  echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows >= 1) {
        while ($row = $result->fetch_object()) {
            if ($row->vendor_id > 0) {
                //if($row->contract_amount  > $row->paid_amount)  {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
                } else {
                    $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
                }
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getVendorEmail($vendorBankId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT general_vendor_email FROM general_vendor  WHERE general_vendor_id = {$vendorBankId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);    
    

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = '|' . $row->general_vendor_email;

    } else {
        $returnValue = '|-';
    }

    echo $returnValue;
}


function setMethod($invoiceMethod, $pgId)
{
    global $myDatabase;
    //edit by YENI
	$sql = "UPDATE pengajuan_general SET invoice_method = {$invoiceMethod} WHERE pengajuan_general_id = {$pgId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  
	$sqlB = "UPDATE pengajuan_general_detail SET invoice_method_detail = {$invoiceMethod} WHERE pg_id = {$pgId}";
    $result = $myDatabase->query($sqlB, MYSQLI_STORE_RESULT);
    echo $returnValue;
}

function getTax_PPH($pphTaxId, $amount)
{
    global $myDatabase;

//edit by YENI
	$sql = "SELECT round(tax_value) as pphValue FROM tax  WHERE tax_id  = {$pphTaxId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    echo $sql;

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		$pphValue = $amount * ($row->pphValue/100);
		
        $returnValue ='|' . $pphValue;

        //echo $row->stockpile_contract_id;
    }else{
		  $returnValue ='|' . 0;
	}

    echo $returnValue;
}


function getQtyPo($poId, $gvId, $todayDate)
{
    global $myDatabase;
    $qtyVal = 0;
    $price = 0;
    $oksAKT = 0;

    $sqlA = "SELECT general_vendor_id, type, active_date
             FROM oks_akt WHERE general_vendor_id = {$gvId} 
            -- AND  active_date >= STR_TO_DATE('$currentDate', '%d/%m/%Y') 
             ORDER BY active_date DESC";
    $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);
    if($resultA !== false && $resultA->num_rows > 0){
        $tempDate = '';
        $flag = 0;
        while($rowA = $resultA->fetch_object()) { //2022-08-05 vs 2022-08-30
            if($rowA->type == 1){
                if($tempDate == ''){
                    $tempDate = $rowA->active_date;
                    if($tempDate <= $todayDate){
                        $flag = 1;
                        break;
                    }
                }else if($tempDate > $rowA->active_date){
                    $tempDate = $rowA->active_date;
                    if($tempDate <= $todayDate){
                        $flag = 1;
                        break;
                    }
                }
            }
            
        }
    }

    //supaya ga ambil tgl sblm tgl active makanya ada validasi diatas
    // echo $tempDate . ' - ' . $rowA->type;
    if($rowA->type == 1 && $flag == 1){ //Fix
        $sql = "SELECT ok.oks_akt_id, ok.vendor_id, COALESCE(c.quantity,0) AS qtyValue, COALESCE(ok.price,0) as priceVal 
                FROM contract c 
                INNER JOIN oks_akt ok ON ok.vendor_id = c.vendor_id 
                WHERE c.contract_id  = {$poId}
                AND ok.general_vendor_id = {$gvId} AND ok.active_date = '{$tempDate}' AND ok.type = 1
                AND ok.oks_akt_id NOT IN (SELECT id_oks_akt FROM temp_oks_akt WHERE contract_id = {$poId}) 
                ORDER BY active_date ASC LIMIT 1 ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        $row = $result->fetch_object();
        $qtyVal = $row->qtyValue;
        $price = $row->priceVal;
        $oksAKT = $row->oks_akt_id;
        $pksId = $row->vendor_id;
    }

    if ($result->num_rows == 1) {
        $returnValue ='|' . number_format($qtyVal, 2, ".", ",") . ' | ' . 1 . ' | ' . number_format($price, 10, ".", ",") . ' | ' . $oksAKT . ' | ' . $pksId;
    }else{
          $returnValue ='|' . 0 . ' | ' . 0 . ' | ' . 0  . '|' . 0 . '|' . 0;
    }
    echo $returnValue;
}

function searchPrice($tempPrice, $gvId, $contractId, $todayDate)
{
    global $myDatabase;
    
   // $price = str_replace( ',', '', $tempPrice);
    $sql = "SELECT ok.oks_akt_id, ok.vendor_id, ok.general_vendor_id, ok.type, ok.active_date, round(ok.price) as price1
             FROM oks_akt ok 
           --  LEFT JOIN temp_oks_akt tok ON tok.id_oks_akt = ok.oks_akt_id
             WHERE ok.general_vendor_id = {$gvId}  AND ok.type = 2 
             AND ok.oks_akt_id NOT IN (SELECT id_oks_akt FROM temp_oks_akt WHERE contract_id = {$contractId} AND status != 2) 
             AND ok.active_date <= '{$todayDate}' AND ok.price = {$tempPrice} ORDER BY active_date DESC ";
      //  echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows > 0){
        $tempDate = '';
        $flag = 0;
        while($row = $result->fetch_object()) { 
        //    if($row->price1 == $tempPrice){ 
                $oksAKT = 0;
                if($tempDate == ''){
                    $tempDate = $row->active_date;
                    if($tempDate <= $todayDate && $row->price1 == $tempPrice){
                        $flag = 1;
                        $oksAKT = $row->oks_akt_id;
                        $pksId = $row->vendor_id;
                        break;
                    }
                }
                else if($tempDate <= $row->active_date){
                    $tempDate = $row->active_date;
                    if($tempDate <= $todayDate){
                        $flag = 1;
                        $oksAKT = $row->oks_akt_id;
                        $pksId = $row->vendor_id;
                        break;
                    }
                }
           // }
        }
    }

    if ($flag == 1) {
        $returnValue ='|' . 0 . ' | ' . $oksAKT . ' | ' . $pksId;
    }else{
          $returnValue ='|' . 1 . ' | ' . 0 . ' | ' . 0;
    }
    echo $returnValue;
}

function QtyValidation($quantity, $contractId)
{
    global $myDatabase;
    
    $sql = "SELECT COALESCE(c.quantity,0) AS qtyValue FROM contract c 
            WHERE c.contract_id  = {$contractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        if($quantity > $row->qtyValue){
            $returnValue ='|' . 1 . ' | ' . number_format($row->qtyValue, 2, ".", ",");
        }else{
            $returnValue ='|' . 0;
        }
    }

    echo $returnValue;
}

function refreshInternalTransfer($internalTF)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    
    //edit by YENI
	$sql = "SELECT itf.stockpile, s.stockpile_name, itf.payment_method, itf.payment_type,
                   itf.bank_id, itf.remarks, itf.amount, itf.pengajuan_interalTF_id
            FROM pengajuan_internaltf itf
			LEFT JOIN stockpile s ON s.stockpile_id = itf.stockpile where pengajuan_interalTF_id = {$internalTF} LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->pengajuan_interalTF_id .'||'. $row->stockpile . '||' . $row->stockpile_name .  '||' . 1 . '||' . 'Payment' . '||' . 2 . '||' . 'OUT' . '||' . 0 . '||'.' HO' .'||'.  $row->amount. '||' . $row->remarks . '||' . $row->bank_id .'||'.$row->pengajuan_interalTF_id;

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function refreshInvoicePayment($invoiceId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';
    
//edit by YENI
	$sql = "SELECT i.invoice_id, i.stockpileId, s.stockpile_name, id.`currency_id`, pg.transaksi_oks_akt,
                id.`exchange_rate`, pg.`pengajuan_general_id`, i.total_dpp as amount,
                i.total_qty, i.total_price, i.total_ppn, i.total_pph, i.invoice_no2,
                i.invoice_tax, DATE_FORMAT(i.invoice_date, '%d/%m/%Y') as invoice_date, i.invoice_method, 
                CASE WHEN i.invoice_method = 1 THEN 'Payment' ELSE 'Down Payment' END AS invMethodText
            FROM invoice i
			LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id
			LEFT JOIN stockpile s ON s.stockpile_id = i.stockpileId
			LEFT JOIN pengajuan_general pg ON pg.`invoice_id` = i.`invoice_id` WHERE i.invoice_id = {$invoiceId} LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		
		
        $returnValue = $row->invoice_id . '||' . $row->stockpileId . '||' . $row->stockpile_name. '||' . $row->invoice_method . '||' . $row->invMethodText . '||' . 2 . '||' . 'OUT' . '||' . 0 . '||' . 'HO'
                        . '||' . $row->currency_id . '||' . $row->exchange_rate . '||' . $row->pengajuan_general_id . '||' .$row->amount
                        .'||' . $row->total_qty .'||' . $row->total_price . '||'. $row->total_ppn . '||' . $row->total_pph 
                        . '||' . $row->invoice_no2 . '||' . $row->invoice_tax . '||' . $row->invoice_date . '||' . $row->transaksi_oks_akt ;

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

// JOB COSTING
function setPlanPayDate($todayDate){
    global $myDatabase;
    $returnValue = '';

    $sql = "CALL PlanPayDate('{$todayDate}')";
    echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    while ($row = $result->fetch_object()){
        $date = $row->tglBayar;
    }
    $newDate = date_create($date);
   // echo date_format($newDate,"d/m/Y");
    $returnValue ='|' . date_format($newDate,"d/m/Y");
    
    echo $returnValue;
}


function getAccountPrediksi($biayaId)
{
    global $myDatabase;
   // CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,acc.account_name) 
	$sql = "SELECT DISTINCT(a.account_id) as accountId, CONCAT(a.account_no, ' - ', a.account_name) as fullName FROM account a
    LEFT JOIN mst_tipe_biaya b ON b.account_id = a.account_id WHERE b.id = {$biayaId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue ='|' . $row->fullName . '|' . $row->accountId;
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getValueUpdate($detailId, $priceMT, $qty)
{
    global $myDatabase;
    // $tempPrice = str_replace( ',', '', $priceMT);
    // $tempQty =  str_replace( ',', '', $qty);
    $maxused = 0;
    $minused = 0;
    $totalAmount = 0;
    $total = 0;
    $takenValue = 0;
    $inRupiah = 0;

    $sql = "SELECT mc.*, ap.exchange_rate FROM mst_costing mc
                LEFT JOIN accrue_prediction_detail apd ON mc.mst_costing_id = apd.mst_costing_id
                LEFT JOIN accrue_prediction ap ON apd.prediction_id = ap.prediction_id
                WHERE apd.prediction_detail_id = {$detailId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $maxc = $row->max_charge; //nilai max
        $minc = $row->min_charge; //nilai minimum
        $maxType = $row->max_type; // 1 = lowest, 2=highest
        $minType = $row->min_type; // 1 = lowest, 2=highest
        $priceType = $row->price_type; //1 = Var, 2 = fix
        $qtyType = $row->qty_type; //1 = Vessel, 2 = Tongkang
        $exchangeRate = $row->exchange_rate;
        // echo $priceType;

        if($qtyType == 1){
            $total = $priceMT * $qty;
            if($priceType == 1){ //VAR
                $maxcp = ($row->max_chargeP/100) * $total;
                $mincp = ($row->min_chargeP/100) * $total;

                if($maxType == 1){ //MAX TYPE U/ LOWEST
                    if($maxc > $maxcp){
                        $maxused = $maxcp;
                    }else{
                        $maxused = $maxc;
                    }
                }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                    if($maxc > $maxcp){
                        $maxused = $maxc;
                    }else{
                        $maxused = $maxcp;
                    }
                }else{
                    $maxused = 0;
                }

                if($minType == 1){ //MIN TYPE U/ LOWEST
                    if($minc > $mincp){
                        $minused = $mincp;
                    }else{
                        $minused = $minc;
                    }
                }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                    if($minc > $mincp){
                        $minused = $minc;
                    }else{
                        $minused = $mincp;
                    }
                }else{
                    $minused = 0;
                }

                if($total > $maxused){
                    if($maxused > $minused){
                        $totalAmount = $maxused;
                    }else if($maxused < $minused){
                        $totalAmount = $minused;
                    }else{
                        $totalAmount = $total;
                    }
                }else if($total  < $maxused){
                    if($total > $minused){
                        $totalAmount = $total;
                    }else{
                        $totalAmount = $minused;
                    }
                }
            }else if($priceType == 2){ //FIX type
                $takenValue = 0;
                $totalAmount = $total;
            }
        }else if ($qtyType == 2){
            $total = $priceMT * $qty;
            if($priceType == 1){ //VAR
                $maxcp = ($row->max_chargeP/100) * $total;
                $mincp = ($row->min_chargeP/100) * $total;

                if($maxType == 1){ //MAX TYPE U/ LOWEST
                    if($maxc > $maxcp){
                        $maxused = $maxcp;
                    }else{
                        $maxused = $maxc;
                    }
                }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                    if($maxc > $maxcp){
                        $maxused = $maxc;
                    }else{
                        $maxused = $maxcp;
                    }
                }

                if($minType == 1){ //MIN TYPE U/ LOWEST
                    if($minc > $mincp){
                        $minused = $mincp;
                    }else{
                        $minused = $minc;
                    }
                }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                    if($minc > $mincp){
                        $minused = $minc;
                    }else{
                        $minused = $mincp;
                    }
                }

                if($total > $maxused){
                    if($maxused > $minused){
                        $totalAmount = $maxused;
                    }else if($maxused < $minused){
                        $totalAmount = $minused;
                    }else{
                        $totalAmount = $total;
                    }
                }else if($total  < $maxused){
                    if($total > $minused){
                        $totalAmount = $total;
                    }else{
                        $totalAmount = $minused;
                    }
                }
            }else if($priceType == 2){ //FIX type
                $takenValue = 0;
                $totalAmount = $total;
            }
        }
        $inRupiah = $exchangeRate * $totalAmount;
    } else{
        $returnValue = '|0|0|0' ;
    }
    $returnValue = '|' . number_format($maxused, 0, ".", ",") . '|' . number_format($minused, 0, ".", ",") . '|' .number_format($totalAmount, 0, ".", ",") . '|' . number_format($inRupiah, 0, ".", ",") ;
    echo $returnValue;
}


function getGenerateCodeInv($stockpileId, $currentYearMonth)
{
    global $myDatabase;
    $checkGenerateNo = '';
    $sqlSp = "SELECT stockpile_code FROM stockpile where stockpile_id = {$stockpileId}";
    $resultSp = $myDatabase->query($sqlSp, MYSQLI_STORE_RESULT);
    if ($resultSp->num_rows == 1) {
        $rowSp = $resultSp->fetch_object();
        $spCode = $rowSp->stockpile_code;
    }

    $checkGenerateNo = $spCode .'/INV-PRED/'. $currentYearMonth;

    $sql = "SELECT  invoice_no FROM invoice
            WHERE invoice_no like '{$checkGenerateNo}%' ORDER BY invoice_id DESC LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    echo $sql;

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $splitNo = explode('/', $row->invoice_no);
        $lastExplode = count($splitNo) - 1;
        $nextNo = ((float)$splitNo[$lastExplode]) + 1;
        $GenerateInvCode = $checkGenerateNo . '/' . $nextNo ;
    } else{
        $GenerateInvCode = $checkGenerateNo . '/1' ;
    }
    $returnValue = '|' . $GenerateInvCode ;
    echo $returnValue;
}

function getGenerateCodePrediksi($currentYearMonth)
{
    global $myDatabase;
    $checkGenerateNo = '';
    $checkGenerateNo = 'JC-JPJ/'. $currentYearMonth;

    $sql = "SELECT  prediction_code FROM accrue_prediction
            WHERE prediction_code like '{$checkGenerateNo}%' ORDER BY prediction_id DESC LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   // echo $sql;

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $splitNo = explode('/', $row->prediction_code);
        $lastExplode = count($splitNo) - 1;
        // $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]);
        $nextNo = ((float)$splitNo[$lastExplode]) + 1;
        $GenerateCode = $checkGenerateNo . '/' . $nextNo ;
    } else{
        $GenerateCode = $checkGenerateNo . '/1' ;
    }
    $returnValue = '|' . $GenerateCode ;
    echo $returnValue;
}

function insertTempCosting($headerCostingID, $kodeprediksi)
{
    global $myDatabase;
    $whereProperty = '';
    $whereProperty2 = '';

    $delete = "DELETE FROM temp_mst_costing";
    $result1 = $myDatabase->query($delete, MYSQLI_STORE_RESULT);

        // $sqlSp = "SELECT stockpile_code as spCode FROM header_costing hc 
        //             INNER JOIN stockpile sp ON sp.stockpile_id = hc.loading_port 
        //         WHERE hc.header_costing_id = {$headerCostingID}";
        // $resulSp = $myDatabase->query($sqlSp, MYSQLI_STORE_RESULT);
        // if ($resulSp->num_rows == 1) {
        //     $rowSp = $resulSp->fetch_object();
        //     $spCode = $rowSp->spCode;
        // }

      //  $codeText = $spCode .'/PRED';
        $code = 0;
        $codeText = $kodeprediksi;
        $sqlCode = "SELECT generate_code_detail FROM accrue_prediction_detail
                WHERE generate_code_detail like '{$codeText}%' ORDER BY prediction_detail_id DESC LIMIT 1";
        $resultCode = $myDatabase->query($sqlCode, MYSQLI_STORE_RESULT);
                if ($resultCode->num_rows == 1) {
                    $rowCode = $resultCode->fetch_object();
                    $splitNo = explode('-', $rowCode->generate_code_detail);
                    $lastExplode = count($splitNo) - 1;
                    $nextNo = ((float)$splitNo[$lastExplode]) + 1;
                    $code =  $nextNo;
                } else{
                    $code = 1 ;
                }

        $sql = "SELECT mc.*
                FROM header_costing hc
                INNER JOIN mst_costing_detail mc ON hc.header_costing_id = mc.header_costing_id
                WHERE mc.active = 1 AND mc.header_costing_id = {$headerCostingID} 
                ORDER BY mc.mcd_id ASC";

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
            $tempQtyV = 0; 
            $tempQtyT = 0; 
            while ($row = $result->fetch_object()) {
                $maxused = 0;
                $minused = 0;
                $totalAmount = 0;
                $QtyType = 0;
                $total = 0;
                $generateCode = '';
                $inRupiah = 0;
                $maxc = $row->max_charge; //nilai max
                $minc = $row->min_charge; //nilai minimum
                $maxType = $row->max_type; // 1 = lowest, 2=highest
                $minType = $row->min_type;

                $total = $row->priceMT;

                if($row->price_type == 1){ //VAR
                    $maxcp = ($row->max_chargeP/100) * $total;
                    $mincp = ($row->min_chargeP/100) * $total;

                    if($maxType == 1){ //MAX TYPE U/ LOWEST
                        if($maxc > $maxcp){
                            $maxused = $maxcp;
                        }else{
                            $maxused = $maxc;
                        }
                    }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                        if($maxc > $maxcp){
                            $maxused = $maxc;
                        }else{
                            $maxused = $maxcp;
                        }
                    }else{
                        $maxused = 0;
                    }

                    if($minType == 1){ //MIN TYPE U/ LOWEST
                        if($minc > $mincp){
                            $minused = $mincp;
                        }else{
                            $minused = $minc;
                        }
                    }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                        if($minc > $mincp){
                            $minused = $minc;
                        }else{
                            $minused = $mincp;
                        }
                    }else{
                        $minused = 0;
                    }

                    if($total > $maxused){
                        if($maxused > $minused){
                            $totalAmount = $maxused;
                        }else if($maxused < $minused){
                            $totalAmount = $minused;
                        }else{
                            $totalAmount = $total;
                        }
                    }else if($total < $maxused){
                        if($total > $minused){
                            $totalAmount = $total;
                        }else{
                            $totalAmount = $minused;
                        }
                    }
                }else if($row->price_type == 2){ //FIX type
                    $takenValue = 0;
                    $QtyType = 1;
                    $totalAmount =  $row->priceMT;
                }

               $generateCode = $codeText. '-' .$code;
               $inRupiah = $totalAmount * $row->exchange_rate;
               //echo $generateCode;
               
               $sqlA = "INSERT INTO `temp_mst_costing` (id_mst, generate_code, nama_biaya, general_vendor_id, account_id, max_type, maxused, max_charge, max_chargeP,  "
                        . "min_type, minused, min_charge, min_chargeP, qty_type_id, price_type_id, "
                        . "qty_value, price, total_amount, kurs, rupiah, stockpile_id, uom_id, currency) VALUES ("
                        . "{$row->mcd_id}, '{$generateCode}', {$row->cost}, {$row->general_vendor_id}, '{$row->account_id}', {$row->max_type}, {$maxused}, {$row->max_charge}, "
                        . "{$row->max_chargeP}, {$row->min_type}, {$minused}, {$row->min_charge}, {$row->min_chargeP},  {$row->qty_type}, {$row->price_type}, "
                        . "{$QtyType}, {$row->priceMT}, {$totalAmount}, {$row->exchange_rate}, {$inRupiah}, {$row->stockpile_id}, {$row->uom}, {$row->currency})";
                $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);
                $code++;
               // echo $sqlA;
            }
        }
    echo $returnValue;
}
function insertTempCosting_Edit($prediksiId)
{
    global $myDatabase;
    $returnValue = '';

    $delete = "DELETE FROM temp_mst_costing";
    $result1 = $myDatabase->query($delete, MYSQLI_STORE_RESULT);

    $sqlx = "SELECT apd.*, mc.uom
            FROM accrue_prediction_detail apd
            INNER JOIN accrue_prediction ap ON ap.prediction_id = apd.prediction_id
            LEFT JOIN mst_costing_detail mc ON apd.mcd_id = mc.mcd_id
            WHERE ap.prediction_id = {$prediksiId}";
    $resultx = $myDatabase->query($sqlx, MYSQLI_STORE_RESULT);
    echo $sqlx . "<br>";

    if ($resultx->num_rows > 0) {
        while ($rowx = $resultx->fetch_object()) {
            $mcd_id = $rowx->prediction_id;
            $sqlA = "INSERT INTO `temp_mst_costing` (header_prediksi, id_mst, prediksi_detail_id, generate_code, nama_biaya, general_vendor_id, account_id, max_type, maxused, max_charge, max_chargeP,  "
                    . "min_type, minused, min_charge, min_chargeP, qty_type_id, price_type_id, "
                    . "qty_value, price, total_amount, currency, kurs, rupiah, stockpile_id, uom_id, status, journal_status) VALUES ("
                    . "{$rowx->prediction_id}, {$rowx->mcd_id}, {$rowx->prediction_detail_id}, '{$rowx->generate_code_detail}', {$rowx->cost_name}, {$rowx->general_vendor_id}, '{$rowx->account_id}', {$rowx->max_type}, {$rowx->maxused}, {$rowx->max_charge}, "
                    . "{$rowx->max_chargeP}, {$rowx->min_type}, {$rowx->minused}, {$rowx->min_charge}, {$rowx->min_chargeP},  {$rowx->qty_type_id}, {$rowx->price_type_id}, "
                    . "{$rowx->qty}, {$rowx->priceMT}, {$rowx->total_amount},{$rowx->currency}, {$rowx->exchange_rate}, {$rowx->in_rupiah}, {$rowx->stockpile_id}, {$rowx->uom}, {$rowx->status}, {$rowx->journal_status})";
            $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);
        echo "<br> SQLA =>" . $sqlA;
        }
    }
    echo $returnValue;
}

function getTempCosting(){
    global $myDatabase;
    $whereProperty = '';
    $whereProperty2 = '';

    $sql = "SELECT tmc.*, tmc.id AS tempID, tmc.id_mst AS costingId, 
            sp.stockpile_name AS spRemarks, sp.stockpile_code AS spCode, 
            gv.general_vendor_name,  CONCAT(a.account_no, ' - ', a.account_name) AS accountName,
            CASE WHEN tmc.max_type = 1 THEN 'Lowest' ELSE 'Highest' END AS maxType,
            CASE WHEN tmc.min_type = 1 THEN 'Lowest' ELSE 'Highest' END AS minType,
            CASE WHEN tmc.price_type_id = 1 THEN 'Var' ELSE 'Fix' END AS priceType,
            CASE WHEN tmc.qty_type_id = 1 THEN 'Vessel'
                WHEN tmc.qty_type_id = 2 THEN 'Tongkang' 
                WHEN tmc.qty_type_id = 3 THEN 'Timbangan'
                WHEN tmc.qty_type_id = 4 THEN 'Volume' ELSE 'Others' END AS coaType, 
            tmc.qty_type_id AS tipeQty,
            u.uom_type, cur.currency_code, tb.tipe_biaya,
            CASE WHEN tmc.currency = 2 THEN '$'
            WHEN tmc.currency = 3 THEN 'S$'
            WHEN tmc.currency = 4 THEN '¥' ELSE 'Rp.' END AS curr
        FROM temp_mst_costing tmc
        LEFT JOIN stockpile sp ON sp.stockpile_id = tmc.stockpile_id
        LEFT JOIN general_vendor gv ON gv.general_vendor_id = tmc.general_vendor_id
        LEFT JOIN uom u ON u.idUOM = tmc.uom_id 
        LEFT JOIN currency cur ON cur.currency_id = tmc.kurs
        LEFT JOIN account a ON a.account_id = tmc.account_id
        LEFT JOIN mst_tipe_biaya tb ON tb.id = tmc.nama_biaya
        ORDER BY tmc.id_mst ASC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //    echo  $sql;

        if ($result->num_rows > 0) {
            $returnValue = '<div class="span12 lightblue">';
            $returnValue .= '<form id = "formDetailCosting">';
            $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
            $returnValue .= '<thead>
                                <tr>
                                    <th>Action</th>
                                    <th><input type="checkbox" class="checkAll1" name="checkedCosting[id][]" onchange ="checkAll(this)" value="0"/></th>
                                    <th>Status Invoice</th>
                                    <th>Status Jurnal</th>
                                    <th>Id detail costing</th>
                                    <th>Generate Code</th>
                                    <th>Cost Name</th>
                                    <th>General Vendor</th>
                                    <th>Account Name</th>
                                    <th>Max Charge</th>
                                    <th>Min Charge</th>
                                    <th>Qty Type</th>
                                    <th>Qty Value <br><span style="color: red;"><b>(required!)</b></span></br></th>
                                    <th>Price type</th>
                                    <th>UOM</th>
                                    <th>Price</th>
                                    <th>Total Amount</th>
                                    <th>Kurs</th>
                                    <th>Rupiah</th>
                                    <th>Stockpile Remarks</th>
                                   
                                </tr>
                            </thead>';
            $returnValue .= '<tbody>';
    
    
                $tempQtyV = 0; 
                $tempQtyT = 0; 
                while ($row = $result->fetch_object()) {
                    $vesselType = '';
                    $vesselType = $row->qty_type_id;
                    
                    $returnValue .= '<tr>';
                    if($row->journal_status == 0 && $row->status == 0 ){
                        $returnValue .= '<td style="text-align: right; width: 8%;">';
                        $returnValue .= '<a href="#" id="update|pg|' . $row->tempID . '" role="button" title="Edit" onclick="editDetail(' . $row->tempID . ');">
                                            <img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>';
                        $returnValue .= ' </td>';
                    }else if(($row->journal_status == 2 || $row->journal_status == 1)){
                        $returnValue .= '<td></td>';
                    }else{
                        $returnValue .= '<td></td>';
                    }

                    if($row->journal_status == 2){
                        $returnValue .= '<td></td>';
                    }else{
                        $returnValue .= '<td><input type="checkbox" class="check1" name="checkedCosting[id][]" onclick="checkCosting()"  value="' . $row->tempID . '"/>
                                        <input type="hidden" id="check2'. $row->tempID.'" name="checkedCosting1[id][]" value = ""></td>';
                    }
                    if($row->status == 1){
                        $returnValue .= '<td><img src="assets/ico/checklist.png" width="18px" height="18px" style="margin-bottom: 5px;" /></td>'; 
                    }else{
                        $returnValue .= '<td></td>';
                    }
                    if($row->journal_status == 2){
                        $returnValue .= '<td><img src="assets/ico/cancel.png" width="18px" height="18px" style="margin-bottom: 5px;" /><a href="#" id="view|jurnal|' . $row->prediksi_detail_id . '" role="button" title="view" onclick="viewJurnalAccrue(' . $row->prediksi_detail_id . ');"><br><hr><img src="assets/ico/preview.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>'; 
                    }else if($row->journal_status == 1){
                        $returnValue .= '<td color: green;"> <img src="assets/ico/checklist.png" width="18px" height="18px" style="margin-bottom: 5px;" /><a href="#" id="view|jurnal|' . $row->prediksi_detail_id . '" role="button" title="view" onclick="viewJurnalAccrue(' . $row->prediksi_detail_id . ');"><br><hr><img src="assets/ico/preview.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>'; 
                    }else{
                        $returnValue .= '<td></td>';
                    }
                    $returnValue .= '<td style="width: 10%;">' . $row->costingId. '</td>';
                    $returnValue .= '<td style="width: 15%;">' . $row->generate_code. '</td>';
                    $returnValue .= '<td style="width: 10%;">' . $row->tipe_biaya . '</td>';
                    $returnValue .= '<td style="width: 10%;">' . $row->general_vendor_name . '</td>';
                    $returnValue .= '<td style="width: 10%;">' . $row->accountName . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->maxused, 0, ".", ",") . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->minused, 0, ".", ",") . '</td>';
                    $returnValue .= '<td style="width: 10%;">' . $row->coaType . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qty_value, 0, ".", ",") . '</td>'; 
                    $returnValue .= '<td style="width: 10%;">' . $row->priceType . '</td>';
                    $returnValue .= '<td style="width: 10%;">' . $row->uom_type . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . $row->curr . ''. number_format($row->price, 0, ".", ",") . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . $row->curr . ''.number_format($row->total_amount, 0, ".", ",") . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->kurs, 0, ".", ",") . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . 'Rp.'.number_format($row->rupiah, 2, ".", ",") . '</td>';
                    $returnValue .= '<td style="text-align: right; width: 10%;">' . $row->spRemarks . '</td>';
                    $returnValue .= '</tr>';
                    $code++;
                }
                $returnValue .= '</tbody>';
                $returnValue .= '</table>';
                $returnValue .= '</form>';
                $returnValue .= '</div>';
    
            }
        echo $returnValue;
}

//HITUNG QTY TIMBANGAN | TONGKANG | VOLUME | OTHERS
function hitungCosting($qty, $tempID){

    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT * FROM temp_mst_costing tmc
           WHERE tmc.id = {$tempID}";
       //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $tempQty = 0; 
        while ($row = $result->fetch_object()) {
        if($row->journal_status == 0) {

            $maxused = 0;
            $minused = 0;
            $totalAmount = 0;
            $QtyType = 0;
            $total = 0;
            $inRupiah = 0;

            $maxc = $row->max_charge; //nilai max
            $minc = $row->min_charge; //nilai minimum
            $maxType = $row->max_type; // 1 = lowest, 2=highest
            $minType = $row->min_type;

            $tempQty = str_replace( ',', '', $qty);
            
            if($tempQty > 0){
                $total = $row->price * $tempQty;
            }else{
                $total = $row->price;
                $tempQty = 1;
            }

            if($row->price_type_id == 1){ //VAR
                $maxcp = ($row->max_chargeP/100) * $total;
                $mincp = ($row->min_chargeP/100) * $total;

                if($maxType == 1){ //MAX TYPE U/ LOWEST
                    if($maxc > $maxcp){
                        $maxused = $maxcp;
                    }else{
                        $maxused = $maxc;
                    }
                }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                    if($maxc > $maxcp){
                        $maxused = $maxc;
                    }else{
                        $maxused = $maxcp;
                    }
                }else{
                    $maxused = 0;
                }

                if($minType == 1){ //MIN TYPE U/ LOWEST
                    if($minc > $mincp){
                        $minused = $mincp;
                    }else{
                        $minused = $minc;
                    }
                }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                    if($minc > $mincp){
                        $minused = $minc;
                    }else{
                        $minused = $mincp;
                    }
                }else{
                    $minused = 0;
                }

                if($total > $maxused){
                    if($maxused > $minused){
                        $totalAmount = $maxused;
                    }else if($maxused < $minused){
                        $totalAmount = $minused;
                    }else{
                        $totalAmount = $total;
                    }
                }else if($total < $maxused){
                    if($total > $minused){
                        $totalAmount = $total;
                    }else{
                        $totalAmount = $minused;
                    }
                }
            }else if($row->price_type_id == 2){ //FIX type
                $takenValue = 0;
                $totalAmount = $row->price;
            }
            $inRupiah = $totalAmount * $row->kurs;

            $returnValue = '|' . number_format($maxused, 0, ".", ",") . '|' . number_format($minused, 0, ".", ",") . '|' .number_format($totalAmount, 0, ".", ",") . '|' . number_format($inRupiah, 0, ".", ",");

        }else{
            $returnValue = '|FAIL|Journal already exists|' ;
        }
        }

    }


    echo $returnValue;
}
//HITUNG QTY VESSEL
function hitungCosting1($qtyVessel, $exRateCosting){

    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';

    $sql = "SELECT * FROM temp_mst_costing tmc WHERE journal_status = 0 AND status = 0;";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $tempQty = 0; 
        while ($row = $result->fetch_object()) {
            $maxused = 0;
            $minused = 0;
            $totalAmount = 0;
            $QtyType = 0;
            $total = 0;
            $inRupiah = 0;

            $maxc = $row->max_charge; //nilai max
            $minc = $row->min_charge; //nilai minimum
            $maxType = $row->max_type; // 1 = lowest, 2=highest
            $minType = $row->min_type;

            if($row->qty_type_id == 1){ //Vessel
                $tempIDV = $row->id;
                $tempQtyV = str_replace( ',', '', $qtyVessel);
                $total = $row->price * $tempQtyV;
              
                if($row->price_type_id == 1){ //VAR
                    $maxcp = ($row->max_chargeP/100) * $total;
                    $mincp = ($row->min_chargeP/100) * $total;

                    if($maxType == 1){ //MAX TYPE U/ LOWEST
                        if($maxc > $maxcp){
                            $maxused = $maxcp;
                        }else{
                            $maxused = $maxc;
                        }
                    }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                        if($maxc > $maxcp){
                            $maxused = $maxc;
                        }else{
                            $maxused = $maxcp;
                        }
                    }else{
                        $maxused = 0;
                    }

                    if($minType == 1){ //MIN TYPE U/ LOWEST
                        if($minc > $mincp){
                            $minused = $mincp;
                        }else{
                            $minused = $minc;
                        }
                    }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                        if($minc > $mincp){
                            $minused = $minc;
                        }else{
                            $minused = $mincp;
                        }
                    }else{
                        $minused = 0;
                    }

                    if($total > $maxused){
                        if($maxused > $minused){
                            $totalAmount = $maxused;
                        }else if($maxused < $minused){
                            $totalAmount = $minused;
                        }else{
                            $totalAmount = $total;
                        }
                    }else if($total < $maxused){
                        if($total > $minused){
                            $totalAmount = $total;
                        }else{
                            $totalAmount = $minused;
                        }
                    }
                }else if($row->price_type_id == 2){ //FIX type
                    $takenValue = 0;
                    $totalAmount =  $row->price;
                }
            
                if($row->currency != 1 && $exRateCosting != ''){
                    $inRupiah = $totalAmount * $exRateCosting; 
                    $exRateCosting1 = $exRateCosting;
                }else {
                    $inRupiah = $totalAmount * $row->kurs;
                    $exRateCosting1 = $row->kurs;
                }
                $sqlA = "UPDATE temp_mst_costing set maxused = {$maxused}, minused = {$minused}, qty_value = {$tempQtyV}, total_amount = {$totalAmount}, kurs = {$exRateCosting1}, rupiah = {$inRupiah} WHERE id = {$tempIDV}";
                $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);           
            }
            // else{

            //     $tempIDV = $row->id;
            //     $total = $row->price;
              
            //     if($row->price_type_id == 1){ //VAR
            //         $maxcp = ($row->max_chargeP/100) * $total;
            //         $mincp = ($row->min_chargeP/100) * $total;

            //         if($maxType == 1){ //MAX TYPE U/ LOWEST
            //             if($maxc > $maxcp){
            //                 $maxused = $maxcp;
            //             }else{
            //                 $maxused = $maxc;
            //             }
            //         }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
            //             if($maxc > $maxcp){
            //                 $maxused = $maxc;
            //             }else{
            //                 $maxused = $maxcp;
            //             }
            //         }else{
            //             $maxused = 0;
            //         }

            //         if($minType == 1){ //MIN TYPE U/ LOWEST
            //             if($minc > $mincp){
            //                 $minused = $mincp;
            //             }else{
            //                 $minused = $minc;
            //             }
            //         }else if($minType == 2){ //MIN TYPE U/ HIGHEST
            //             if($minc > $mincp){
            //                 $minused = $minc;
            //             }else{
            //                 $minused = $mincp;
            //             }
            //         }else{
            //             $minused = 0;
            //         }

            //         if($total > $maxused){
            //             if($maxused > $minused){
            //                 $totalAmount = $maxused;
            //             }else if($maxused < $minused){
            //                 $totalAmount = $minused;
            //             }else{
            //                 $totalAmount = $total;
            //             }
            //         }else if($total < $maxused){
            //             if($total > $minused){
            //                 $totalAmount = $total;
            //             }else{
            //                 $totalAmount = $minused;
            //             }
            //         }
            //     }else if($row->price_type_id == 2){ //FIX type
            //         $takenValue = 0;
            //         $totalAmount =  $row->price;
            //     }
            
            //     if($row->currency != 1 && $exRateCosting != ''){
            //         $inRupiah = $totalAmount * $exRateCosting; 
            //         $exRateCosting1 = $exRateCosting;
            //     }else {
            //         $inRupiah = $totalAmount * $row->kurs;
            //         $exRateCosting1 = $row->kurs;
            //     }
            //     $sqlA = "UPDATE temp_mst_costing set maxused = {$maxused}, minused = {$minused}, total_amount = {$totalAmount}, kurs = {$exRateCosting1}, rupiah = {$inRupiah} WHERE id = {$tempIDV}";
            //     $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);   
            //    // echo $sqlA;        
            // }

        }
    }
    // else{
    //     $returnValue = '|0|0|0' ;
    // }

    $returnValue = '|0' ;
    echo $returnValue;
}

function addNewData($headerCostingID, $stockpileId, $qtyVessel, $prediksiId, $exRateCosting, $kodeprediksi)
{
    global $myDatabase;
    $whereProperty = '';
    $whereProperty2 = '';
    $generateCode = '';
    $temp_exRate = str_replace( ',', '', $exRateCosting);
    //echo $temp_exRate;

        $code = 0;
        // $sqlSp = "SELECT stockpile_code as spCode FROM stockpile where stockpile_id = {$stockpileId}";
        // $resulSp = $myDatabase->query($sqlSp, MYSQLI_STORE_RESULT);
        // if ($resulSp->num_rows == 1) {
        //     $rowSp = $resulSp->fetch_object();
        //     $spCode = $rowSp->spCode;
        // }

        // $codeText = $spCode .'/PRED';
        $codeText = $kodeprediksi;
        $sqlCode = "SELECT generate_code_detail FROM accrue_prediction_detail
                WHERE generate_code_detail like '{$codeText}%' ORDER BY prediction_detail_id DESC LIMIT 1";
     //   echo $sqlCode;
        $resultCode = $myDatabase->query($sqlCode, MYSQLI_STORE_RESULT);
                if ($resultCode->num_rows == 1) {
                    $rowCode = $resultCode->fetch_object();
                    $splitNo = explode('-', $rowCode->generate_code_detail);
                    $lastExplode = count($splitNo) - 1;
                    // $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]);
                    $nextNo = ((float)$splitNo[$lastExplode]) + 1;
                    $code =  $nextNo;
                } else{
                    $code = 1 ;
                }

        $sqlprediksi = "SELECT mcd_id FROM accrue_prediction_detail WHERE prediction_id = {$prediksiId} AND journal_status != 2";
        $resultprediksi = $myDatabase->query($sqlprediksi, MYSQLI_STORE_RESULT);
		//echo $sqlprediksi;
        if ($resultprediksi->num_rows > 0) {
            while($rowpred = $resultprediksi->fetch_object()) {
                if ($tempId == '') {
                    $tempId .= $rowpred->mcd_id;;
                } else {
                    $tempId .= ', ' . $rowpred->mcd_id;
                }
            }
        }
      //  echo $tempId;

    $sql = "SELECT mc.*, mc.mcd_id AS costingId, sp.stockpile_name, sp.stockpile_code as spCode, gv.general_vendor_name,  
            CONCAT(a.account_no, ' - ', a.account_name) AS accountName,
            CASE WHEN mc.max_type = 1 THEN 'Lowest' ELSE 'Highest' END AS maxType,
            CASE WHEN mc.min_type = 1 THEN 'Lowest' ELSE 'Highest' END AS minType,
            CASE WHEN mc.price_type = 1 THEN 'Var' ELSE 'Fix' END AS priceType,
            CASE WHEN mc.qty_type = 1 THEN 'Vessel' 
                WHEN mc.qty_type = 2 THEN 'Tongkang'
                WHEN mc.qty_type = 3 THEN 'Timbangan'
                WHEN mc.qty_type = 4 THEN 'Volume'
            ELSE 'Others' END AS coaType, 
            u.uom_type, cur.currency_code, tb.tipe_biaya, 
            CASE WHEN mc.active = 0 THEN 'Non Active' ELSE 'Active' END AS active, 
            (SELECT user_name FROM USER WHERE user_id = mc.entry_by) AS user1, mc.entry_date,
            CASE WHEN mc.currency = 2 THEN '$'
                WHEN mc.currency = 3 THEN 'S$'
                WHEN mc.currency = 4 THEN '¥' ELSE 'Rp.' END AS curr
            FROM header_costing hc
            LEFT JOIN mst_costing_detail mc ON mc.header_costing_id = hc.header_costing_id
            LEFT JOIN stockpile sp ON sp.stockpile_id = mc.stockpile_id
            LEFT JOIN general_vendor gv ON gv.general_vendor_id = mc.general_vendor_id
            LEFT JOIN uom u ON u.idUOM = mc.uom 
            LEFT JOIN currency cur ON cur.currency_id = mc.currency
            LEFT JOIN account a ON a.account_id = mc.account_id
            LEFT JOIN mst_tipe_biaya tb on tb.id = mc.cost
            WHERE mc.active = 1 AND mc.header_costing_id = {$headerCostingID} AND mc.mcd_id not in ($tempId)
            ORDER BY mc.mcd_id ASC";
     //  echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th><input type="checkbox" class="checkAll1" name="checkedCostingModal[id][]" onchange ="checkAllModal(this)" value="0"/></th>
                                <th>Code MST Costing</th>
                                <th>Generate Code</th>
                                <th>Cost Name</th>
                                <th>General Vendor</th>
                                <th>Account Name</th>
                                <th>Max Charge</th>
                                <th>Min Charge</th>
                                <th>Qty Type</th>
                                <th>Qty Value</th>
                                <th>Price Type</th>
                                <th>UOM</th>
                                <th>Price</th>
                                <th>Total Amount</th>
                                <th>Kurs</th>
                                <th>Rupiah</th>
                                <th>Stockpile Remarks</th>

                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';


            $tempQtyV = 0; 
            while ($row = $result->fetch_object()) {
                $maxused = 0;
                $minused = 0;
                $totalAmount = 0;
                $QtyType = 0;
                $total = 0;
                $takenValue = 0;
                $inRupiah = 0;
                $maxc = $row->max_charge; //nilai max
                $minc = $row->min_charge; //nilai minimum
                $maxType = $row->max_type; // 1 = lowest, 2=highest
                $minType = $row->min_type;
                $generateCode = $codeText .'-' .$code;
                // $chargeCategory = $row->charge_category; // 1 = min, 2 = max
                $priceType = $row->price_type; //1 = Var, 2 = fix


                if($row->qty_type == 1){ //Vessel
                 
                    if($qtyVessel > 0){
                        $tempQtyV = str_replace( ',', '', $qtyVessel);
                        $total = $row->priceMT * $tempQtyV;
                        $QtyType = $tempQtyV;
                    }else{
                        $total = $row->priceMT;
                    } 

                    if($row->price_type == 1){ //VAR
                        $maxcp = ($row->max_chargeP/100) * $total;
                        $mincp = ($row->min_chargeP/100) * $total;

                        if($maxType == 1){ //MAX TYPE U/ LOWEST
                            if($maxc > $maxcp){
                                $maxused = $maxcp;
                            }else{
                                $maxused = $maxc;
                            }
                        }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                            if($maxc > $maxcp){
                                $maxused = $maxc;
                            }else{
                                $maxused = $maxcp;
                            }
                        }else{
                            $maxused = 0;
                        }

                        if($minType == 1){ //MIN TYPE U/ LOWEST
                            if($minc > $mincp){
                                $minused = $mincp;
                            }else{
                                $minused = $minc;
                            }
                        }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                            if($minc > $mincp){
                                $minused = $minc;
                            }else{
                                $minused = $mincp;
                            }
                        }else{
                            $minused = 0;
                        }

                        if($total > $maxused){
                            if($maxused > $minused){
                                $totalAmount = $maxused;
                            }else if($maxused < $minused){
                                $totalAmount = $minused;
                            }else{
                                $totalAmount = $total;
                            }
                        }else if($total < $maxused){
                            if($total > $minused){
                                $totalAmount = $total;
                            }else{
                                $totalAmount = $minused;
                            }
                        }
                    }else if($row->price_type == 2){ //FIX type
                        $takenValue = 0;
                        $QtyType = 1;
                        $totalAmount =  $row->priceMT;
                    }
                } else { //TONGKANG   || TIMBANGAN
                    
                    $total = $row->priceMT;     
                    if($row->price_type == 1){    //VAR
                        $maxcp = ($row->max_chargeP/100) * $total;
                        $mincp = ($row->min_chargeP/100) * $total;                
                                    
                        if($maxType == 1){ //MAX TYPE U/ LOWEST
                            if($maxc > $maxcp){
                                $maxused = $maxcp;
                            }else{
                                $maxused = $maxc;
                            }
                        }else if($maxType == 2){ //MAX TYPE U/ HIGHEST
                            if($maxc > $maxcp){
                                $maxused = $maxc;
                            }else{
                                $maxused = $maxcp;
                            }
                        }else{
                            $maxused = 0;
                        }    
    
                        if($minType == 1){ //MIN TYPE U/ LOWEST
                            if($minc > $mincp){
                                $minused = $mincp;
                            }else{
                                $minused = $minc;
                            }
                        }else if($minType == 2){ //MIN TYPE U/ HIGHEST
                            if($minc > $mincp){
                                $minused = $minc;
                            }else{
                                $minused = $mincp;
                            }
                        }else{
                            $minused = 0;
                        }            
                        
                        if($total > $maxused){
                            if($maxused > $minused){
                                $totalAmount = $maxused;
                            }else if($maxused < $minused){
                                $totalAmount = $minused;
                            }else{
                                $totalAmount = $total;
                            }
                        }else if($total  < $maxused){
                            if($total > $minused){
                                $totalAmount = $total;
                            }else{
                                $totalAmount = $minused;
                            }
                        }
                    }else if($row->price_type == 2){ //FIX type
                        $takenValue = 0;
                        $QtyType = 1;
                        $totalAmount =  $row->priceMT;
                    }
                }

                if($row->currency != 1 && $exRateCosting != ''){
                    $inRupiah = $totalAmount * $temp_exRate;
                    $exRate = $temp_exRate;
                }else{
                    $inRupiah = $totalAmount * $row->exchange_rate;
                    $exRate = $row->exchange_rate;
                }
              
                $returnValue .= '<tr>';
               
                $returnValue .= '<td><input type="checkbox" class="checkA" name="checkedCostingModal[id][]" onclick="checkCostingModal()"  value="' . $row->costingId . '"/>
                                        <input type="hidden" id="checkB'. $row->costingId.'" name="checkedCostingModal1[id][]" value=""></td>';
                $returnValue .= '<td style="width: 15%;">' . $row->costingId. '</td>';
                $returnValue .= '<td style="width: 15%;">' . $generateCode. '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->tipe_biaya . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->general_vendor_name . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->accountName . '</td>';     
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($maxused, 3, ".", ",") . ' </td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($minused, 3, ".", ",") . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->coaType . ' </td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($QtyType, 3, ".", ",") . '</td>'; 

                $returnValue .= '<td style="width: 10%;">' . $row->priceType . ' </td>';
                $returnValue .= '<td style="width: 10%;">' . $row->uom_type . ' </td>';

                $returnValue .= '<td style="text-align: right; width: 10%;">' .$row->curr. ''. number_format($row->priceMT, 3, ".", ",") . ' </td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . $row->curr. '' .number_format($totalAmount, 3, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($exRate, 0, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . 'Rp.'.number_format($inRupiah, 3, ".", ",") . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->stockpile_name . '</td>';   
                $returnValue .= '</tr>';
                $code++;
            }
            $returnValue .= '</tbody>';
            $returnValue .= '</table>';
            $returnValue .= '</form>';
            $returnValue .= '</div>';

        }else{
           $returnValue .= '<h1>DATA BELUM ADA DI MASTER </h1>';
        }
    echo $returnValue;
}
function getShipmentCosting($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT DISTINCT(sh.shipment_id) as shipmentId, sh.shipment_no
            FROM shipment sh
            LEFT JOIN sales sl
                ON sl.sales_id = sh.sales_id
            INNER JOIN stockpile sp
                ON sp.stockpile_id = sl.stockpile_id
            WHERE sp.stockpile_id = {$stockpileId} AND sh.status_prediksi = 0 AND sl.sales_status = 0
            ORDER BY sh.shipment_code ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  //  echo $sql;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->shipmentId . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipmentId . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSalesPrediksi($shipmentId)
{
    global $myDatabase;
    $returnValue = '';
    $sql = "SELECT sl.sales_id, sl.customer_id, cus.customer_name, sl.mother_vessel, DATE_FORMAT(sl.peb_fp_date, '%d/%m/%Y') AS pebDate, 
                cur.currency_id, cur.currency_code, sl.quantity as qtySL, sl.exchange_rate as exchangerRate  FROM sales sl
            INNER JOIN shipment sh ON sh.sales_id = sl.sales_id
            LEFT JOIN customer cus ON cus.customer_id = sl.customer_id
            LEFT JOIN currency cur ON cur.currency_id = sl.currency_id
            WHERE sh.shipment_id = {$shipmentId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $qty = $row->qtySL;
       

        $returnValue = '|' .  number_format($qty, 3, ".", ",") . '|' . $row->customer_id . '|' .$row->customer_name . '|' . $row->mother_vessel . '|' . $row->pebDate . '|' . $row->currency_id . '|' . $row->currency_code . '|' .  number_format($row->exchangerRate, 0, ".", ",") . '|' .$row->sales_id;

    } else {
        $returnValue = '|0|-|-|-|-|-|-|1|-';
    }

    echo $returnValue;
}

function getLoadingPort($headerCostingID)
{
    global $myDatabase;
    $returnValue = '';
    
    $sql = "SELECT hc.loading_port as stockpileId, sp.stockpile_name FROM header_costing hc
            INNER JOIN stockpile sp ON sp.stockpile_id = hc.loading_port
             WHERE hc.header_costing_id = {$headerCostingID}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();       

        $sqlA = "SELECT exchange_rate, currency FROM header_costing hc 
                    INNER JOIN mst_costing_detail mcd ON hc.header_costing_id = mcd.header_costing_id 
                    WHERE mcd.currency <> 1 AND hc.header_costing_id = {$headerCostingID} limit 1";
        $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);
        $rowA = $resultA->fetch_object();  

        $returnValue = '|' . $row->stockpileId . '|' .$row->stockpile_name . '|' .$rowA->currency . '|' .  number_format($rowA->exchange_rate, 0, ".", ",");

    } else {
        $returnValue = '|-|-|-|-';
    }

    echo $returnValue;
}

// RSB GGL

function getVendorGGL_SRB($vendor_id)
{
    global $myDatabase;

//edit by YENI
	$sql = "SELECT case when rsb = 1 and ggl = 0 then 'RSB'
                        when ggl = 1 and rsb = 0 then 'GGL' 
                        when ggl = 1 and rsb = 1 then 'RSB & GGL' else 'Uncertified' end as ggl_rsb,
                    CASE WHEN rsb = 1 and ggl = 0 then 1 else 0 end as rsb1,
                    CASE WHEN rsb = 0 and ggl = 1 then 1 else 0 end as ggl1,
                    CASE WHEN rsb = 1 and ggl = 1 then 1 else 0 end as rsb_ggl1,
                    CASE WHEN rsb = 0 and ggl = 0 then 1 else 0 end as uncertified1
                    FROM vendor where vendor_id = {$vendor_id}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  //  echo $sql;

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		if($row->rsb1 != 1){
            $tempRSB = 0;
        }else{
            $tempRSB = $row->rsb1;
        }

        if($row->ggl1 != 1){
            $tempGGL = 0;
        }else{
            $tempGGL = $row->ggl1;
        }

        if($row->rsb_ggl1 != 1){
            $tempRG = 0;
        }else{
            $tempRG = $row->rsb_ggl1;
        }

        if($row->uncertified1 != 1){
            $tempUN = 0;
        }else{
            $tempUN = $row->uncertified1;
        }

        $returnValue ='|' . $tempRSB . '|' . $tempGGL. '|'. $tempRG . '|' . $tempUN . '|' .$row->ggl_rsb;

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getShipmentGGL_SRB($salesId)
{
    global $myDatabase;

//edit by YENI
	$sql = "SELECT CASE WHEN rsb = 1 AND ggl = 0 THEN 'RSB'
            WHEN ggl = 1 AND rsb = 0 THEN 'GGL' 
            WHEN ggl = 1 AND rsb = 1 THEN 'RSB & GGL' ELSE 'Uncertified' END AS ggl_rsb,
           --  rsb, ggl,
            CASE WHEN rsb = 0 and ggl = 1 then '1' else 0 end as status_ggl,
            CASE WHEN rsb = 1 and ggl = 0 then '1' else 0 end as status_rsb,
            CASE WHEN rsb = 1 and ggl = 1 then '1' else 0 end as RG,
            CASE WHEN rsb = 0 and ggl = 0 then '1' else 0 end as Uncertified
        FROM sales WHERE sales_id= {$salesId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  //  echo $sql;

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		
        $returnValue ='|' . $row->status_rsb . '|' . $row->status_ggl. '|'. $row->ggl_rsb. '|'. $row->RG . '|'. $row->Uncertified;

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

// function getAvailableQty_RSB($stockpileId)
// {
//     global $myDatabase;

// //edit by YENI
// 	$sql = "SELECT FORMAT(SUM(transaction_qty - rsb.qty_deliv),2)  AS qty_rsb 
//             FROM (
//                 SELECT IFNULL(SUM(t1.quantity),0) AS transaction_qty,
//                 (SELECT IFNULL(SUM(dv.quantity),0) FROM delivery dv, TRANSACTION t2 WHERE dv.transaction_id = t2.`transaction_id` 
//                     AND t2.`delivery_status` IN (0,2) AND t2.`transaction_type` = 1 
//                     AND t2.`rsb` = 1 AND t2.`ggl` = 0 AND sc.`stockpile_id` ={$stockpileId}
//                 ) AS qty_deliv
//                 FROM TRANSACTION t1
//                 LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t1.stockpile_contract_id 
//                 WHERE t1.`delivery_status` IN (0,2) AND t1.`rsb` = 1 AND t1.`ggl` = 0 
//                 AND sc.`stockpile_id` = {$stockpileId} AND t1.`transaction_type` = 1 
//             ) AS rsb";
//         $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//   //  echo $sql;

//         if ($result->num_rows == 1) {
//             $row = $result->fetch_object();
//             if($row->qty_rsb > 0){
//                 $qtyR = $row->qty_rsb;
//             }else{
//                 $qtyR = 0;
//             }
//             $returnValue ='|' . $qtyR;
//             //echo $row->stockpile_contract_id;
//         }
//         if ($returnValue == '') {
//             $returnValue = '~';
//         }
//     echo $returnValue;
// }   

// function getAvailableQty_ggl($stockpileId)
// {
//     global $myDatabase;

// //edit by YENI
// 	$sql = "SELECT FORMAT(SUM(transaction_qty - ggl.qty_deliv),2) AS qty_ggl
//             FROM (
//                 SELECT IFNULL(SUM(t1.quantity),0) AS transaction_qty,
//                 (SELECT IFNULL(SUM(dv.quantity),0) FROM delivery dv WHERE dv.transaction_id = t1.`transaction_id` 
//                     AND t1.`delivery_status` IN (0,2) AND t1.transaction_type = 1 
//                     AND t1.`rsb` = 0 AND t1.`ggl` = 1 AND sc.`stockpile_id` = {$stockpileId}
//                 ) AS qty_deliv
//                 FROM TRANSACTION t1
//                 LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t1.stockpile_contract_id 
//                 WHERE t1.`delivery_status` IN (0,2) AND t1.transaction_type = 1  AND sc.`stockpile_id` = {$stockpileId}
//                 AND t1.`rsb` = 0 AND t1.`ggl` = 1 
//                 GROUP BY t1.`transaction_id`
//             ) AS ggl";
//         $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//   //  echo $sql;

//         if ($result->num_rows == 1) {
//             $row = $result->fetch_object();
//             if($row->qty_ggl > 0){
//                 $qtyG = $row->qty_ggl;
//             }else{
//                 $qtyG = 0;
//             }
//             $returnValue ='|' . $qtyG;
//             //echo $row->stockpile_contract_id;
//         }
//         if ($returnValue == '') {
//             $returnValue = '~';
//         }
//     echo $returnValue;
// }  

// function getAvailableQty_RG($stockpileId)
// {
//     global $myDatabase;
// //edit by YENI
// 	$sql = "SELECT FORMAT(SUM(transaction_qty - rg.qty_deliv),2) AS qty_RG
//             FROM (
//                 SELECT IFNULL(SUM(t1.quantity),0) AS transaction_qty,
//                 (SELECT IFNULL(SUM(dv.quantity),0) FROM delivery dv WHERE dv.transaction_id = t1.`transaction_id` 
//                     AND t1.`delivery_status` IN (0,2) AND t1.transaction_type = 1 
//                     AND t1.`rsb` = 1 AND t1.`ggl` = 1 AND sc.stockpile_id = {$stockpileId}
//                 ) AS qty_deliv
//                 FROM TRANSACTION t1
//                 LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t1.stockpile_contract_id 
//                 WHERE t1.`delivery_status` IN (0,2) AND t1.transaction_type = 1
//                 AND t1.`rsb` = 1 AND t1.`ggl` = 1 AND sc.stockpile_id = {$stockpileId}
//                 GROUP BY t1.`transaction_id`
//             ) AS rg";
//         $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//   //  echo $sql;

//         if ($result->num_rows == 1) {
//             $row = $result->fetch_object();
//             if($row->qty_RG > 0){
//                 $qtyRG = $row->qty_RG;
//             }else{
//                 $qtyRG = 0;
//             }
            
//             $returnValue ='|' . $qtyRG;
//             //echo $row->stockpile_contract_id;
//         }
//         if ($returnValue == '') {
//             $returnValue = '~';
//         }
//     echo $returnValue;
// }  

function getAvailableQtyAll($stockpileId)
{
    global $myDatabase;

    $sqlSP = "SELECT stockpile_code FROM stockpile  WHERE stockpile_id = {$stockpileId}";
    $resultSP = $myDatabase->query($sqlSP, MYSQLI_STORE_RESULT);
    $rowSP = $resultSP->fetch_object();
	$stockpileCode = $rowSP->stockpile_code;

//edit by YENI
    $sql ="SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb = 1 THEN t.quantity ELSE 0 END)) AS qty_availableR, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND  t.ggl = 1 THEN t.quantity ELSE 0 END)) AS qty_availableG, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb_ggl = 1 THEN t.quantity ELSE 0 END)) AS qty_availableRG, 
            ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.uncertified = 1 THEN t.quantity ELSE 0 END)) AS qty_availableUN,
            ROUND(SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2 ) AS shrink
        FROM `transaction` t 
        WHERE SUBSTRING(t.slip_no,1,3) = '{$stockpileCode}'  ORDER BY t.transaction_date ASC ";
        // echo $sql ."         ";
    
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows == 1) {
		$row = $result->fetch_object();	

		$begining1 = $row->qty_available;
        $begining1_R = $row->qty_availableR;
        $begining1_G = $row->qty_availableG;
        $begining1_RG = $row->qty_availableRG;
        $begining1_UN = $row->qty_availableUN;
    }

    $sql3 = "SELECT SUM(qtyRSB) AS RS, SUM(qtyGGL) AS GG, SUM(qtyRG) AS RG, SUM(qtyUN) AS UN FROM ( 
        SELECT CASE WHEN d.qty_rsb <> 0 THEN SUM(d.qty_rsb) ELSE 0 END AS qtyRSB, 
            CASE WHEN d.qty_ggl <> 0 THEN SUM(d.qty_ggl) ELSE 0 END AS qtyGGL, 
            CASE WHEN d.qty_rsb_ggl <> 0 THEN SUM(d.qty_rsb_ggl) ELSE 0 END AS qtyRG, 
            CASE WHEN d.qty_uncertified <> 0 THEN SUM(d.qty_uncertified) ELSE 0 END AS qtyUN, 
            t2.slip_no, t2.`transaction_date` 
        FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
        LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
        AND SUBSTRING(t2.slip_no,1,3) = '{$stockpileCode}' AND t2.transaction_type = 2
        GROUP BY d.qty_rsb, d.qty_ggl, d.qty_rsb_ggl, d.qty_uncertified
    ) a ";
    //   echo $sql3 ."         ";
    $result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
    if($result3->num_rows == 1) {
        $row3 = $result3->fetch_object();
            
            $begining2_R =  $row3->RS;
            $begining2_G = $row3->GG;
            $begining2_RG = $row3->RG;
            $begining2_UN = $row3->UN;
    }

    $beginingR = $begining1_R - $begining2_R;
	$beginingG = $begining1_G - $begining2_G;
	$beginingRG = $begining1_RG - $begining2_RG;
	$beginingUN = $begining1_UN - $begining2_UN;

    if($beginingR <= 0){
        $beginingR = 0;
    }
    if($beginingG <= 0){
        $beginingG = 0;
    }
    if($beginingRG <= 0){
        $beginingRG = 0;
    }
    if($beginingUN <= 0){
        $beginingUN = 0;
    }

    $returnValue ='|'. number_format($beginingR, 2, ".", ",") .'|'.  number_format($beginingG, 2, ".", ",") .'|' .  number_format($beginingRG, 2, ".", ","). '|' .  number_format($beginingUN, 2, ".", ",");

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}  

function getShipmentGGL_SRB1($shipmentId)
{
    global $myDatabase;

//edit by YENI
	$sql = "SELECT CASE WHEN rsb = 1 AND ggl = 0 THEN 'RSB'
            WHEN ggl = 1 AND rsb = 0 THEN 'GGL' 
            WHEN ggl = 1 AND rsb = 1 THEN 'RSB & GGL' ELSE 'Uncertified' END AS ggl_rsb,
        rsb, ggl FROM temp_transaction WHERE shipment_id= {$shipmentId} AND STATUS = 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  //  echo $sql;

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		
        $returnValue ='|' . $row->rsb . '|' . $row->ggl. '|'. $row->ggl_rsb;

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getValidationGGL($stockpileId, $qty_ggl) //Validasi GGL
{
    global $myDatabase;
    $htmlValue = '';

    $sqlSP = "SELECT stockpile_code FROM stockpile  WHERE stockpile_id = {$stockpileId}";
    $resultSP = $myDatabase->query($sqlSP, MYSQLI_STORE_RESULT);
    $rowSP = $resultSP->fetch_object();
	$stockpileCode = $rowSP->stockpile_code;

//edit by YENI
    $sql ="SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND  t.ggl = 1 THEN t.quantity ELSE 0 END)) AS qty_availableG
        FROM `transaction` t 
        WHERE SUBSTRING(t.slip_no,1,3) = '{$stockpileCode}'  ORDER BY t.transaction_date ASC ";
    //     echo "SATU " .$sql;
    
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows == 1) {
		$row = $result->fetch_object();	
        $begining1_G = $row->qty_availableG;
    }

    $sql3 = "SELECT SUM(qtyGGL) AS ggl1 FROM 
        (
            SELECT CASE WHEN d.qty_ggl <> 0 THEN SUM(d.qty_ggl) ELSE 0 END AS qtyGGL
            FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
            LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
            AND SUBSTRING(t2.slip_no,1,3) = '{$stockpileCode}' AND t2.transaction_type = 2
            GROUP BY d.qty_ggl
        ) a ";
     // echo "DUA  ". $sql3;
    $result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
    if($result3->num_rows == 1) {
        $row3 = $result3->fetch_object();     
        $begining2_G = $row3->ggl1;
    }

	$beginingG = $begining1_G - $begining2_G;
   // echo $qty_UN;
    if($beginingG <= 0){
        $beginingG = 0;
    }
   
    if($qty_ggl > $beginingG){
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<b><span>Nilai Inputan Un-Certified SALAH</span></b>";
    }else {
        $htmlValue = "|0|";
        $htmlValue = $htmlValue . "<span></span>";
    }

    echo $htmlValue;
}  


function getValidationRG($stockpileId, $qty_RG) //Validasi RSB + GGL
{
    global $myDatabase;
    $htmlValue = '';

    $sqlSP = "SELECT stockpile_code FROM stockpile  WHERE stockpile_id = {$stockpileId}";
    $resultSP = $myDatabase->query($sqlSP, MYSQLI_STORE_RESULT);
    $rowSP = $resultSP->fetch_object();
	$stockpileCode = $rowSP->stockpile_code;

//edit by YENI
    $sql ="SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb_ggl = 1 THEN t.quantity ELSE 0 END)) AS qty_availableRG
        FROM `transaction` t 
        WHERE SUBSTRING(t.slip_no,1,3) = '{$stockpileCode}'  ORDER BY t.transaction_date ASC ";
    //     echo "SATU " .$sql;
    
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows == 1) {
		$row = $result->fetch_object();	
        $begining1_RG = $row->qty_availableRG;
    }

    $sql3 = "SELECT SUM(qtyRG) AS RG FROM 
        (
            SELECT CASE WHEN d.qty_rsb_ggl <> 0 THEN SUM(d.qty_rsb_ggl) ELSE 0 END AS qtyRG
            FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
            LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
            AND SUBSTRING(t2.slip_no,1,3) = '{$stockpileCode}' AND t2.transaction_type = 2
            GROUP BY d.qty_rsb_ggl
        ) a ";
     // echo "DUA  ". $sql3;
    $result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
    if($result3->num_rows == 1) {
        $row3 = $result3->fetch_object();     
        $begining2_RG = $row3->RG;
    }

	$beginingRG = $begining1_RG - $begining2_RG;
   // echo $qty_UN;
    if($beginingRG <= 0){
        $beginingRG = 0;
    }
   
    if($qty_RG > $beginingRG){
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<b><span>Nilai Inputan Un-Certified SALAH</span></b>";
    }else {
        $htmlValue = "|0|";
        $htmlValue = $htmlValue . "<span></span>";
    }

    echo $htmlValue;
}  

function getValidationRSB($stockpileId, $qty_rsb) //Validasi RSB
{
    global $myDatabase;
    $htmlValue = '';

    $sqlSP = "SELECT stockpile_code FROM stockpile  WHERE stockpile_id = {$stockpileId}";
    $resultSP = $myDatabase->query($sqlSP, MYSQLI_STORE_RESULT);
    $rowSP = $resultSP->fetch_object();
	$stockpileCode = $rowSP->stockpile_code;

//edit by YENI
    $sql ="SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.rsb = 1 THEN t.quantity ELSE 0 END)) AS qty_availableR
        FROM `transaction` t 
        WHERE SUBSTRING(t.slip_no,1,3) = '{$stockpileCode}'  ORDER BY t.transaction_date ASC ";
    //     echo "SATU " .$sql;
    
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows == 1) {
		$row = $result->fetch_object();	
        $begining1_R = $row->qty_availableR;
    }

    $sql3 = "SELECT  SUM(qtyRSB) AS RS FROM 
        (
            SELECT CASE WHEN d.qty_rsb <> 0 THEN SUM(d.qty_rsb) ELSE 0 END AS qtyRSB
            FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
            LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
            AND SUBSTRING(t2.slip_no,1,3) = '{$stockpileCode}' AND t2.transaction_type = 2
            GROUP BY d.qty_rsb
        ) a ";
  //    echo "DUA  ". $sql3;
    $result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
    if($result3->num_rows == 1) {
        $row3 = $result3->fetch_object();     
            $begining2_R =  $row3->RS;
    }

	$beginingR = $begining1_R - $begining2_R;
   // echo $qty_UN;
    if($beginingR <= 0){
        $beginingR = 0;
    }
   

    if($qty_rsb > $beginingR){
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<b><span>Nilai Inputan Un-Certified SALAH</span></b>";
    }else {
        $htmlValue = "|0|";
        $htmlValue = $htmlValue . "<span></span>";
    }

    echo $htmlValue;
}  

function getValidationUN($stockpileId, $qty_UN) //Validasi  UN
{
    global $myDatabase;
    $htmlValue = '';

    $sqlSP = "SELECT stockpile_code FROM stockpile  WHERE stockpile_id = {$stockpileId}";
    $resultSP = $myDatabase->query($sqlSP, MYSQLI_STORE_RESULT);
    $rowSP = $resultSP->fetch_object();
	$stockpileCode = $rowSP->stockpile_code;

//edit by YENI
    $sql ="SELECT ROUND(SUM( CASE WHEN t.transaction_type = 1 AND t.uncertified THEN t.quantity ELSE 0 END)) AS qty_availableUN
        FROM `transaction` t 
        WHERE SUBSTRING(t.slip_no,1,3) = '{$stockpileCode}'  ORDER BY t.transaction_date ASC ";
    //     echo "SATU " .$sql;
    
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows == 1) {
		$row = $result->fetch_object();	
        $begining1_UN = $row->qty_availableUN;
    }

    $sql3 = "SELECT  SUM(qtyUN) AS UN 
        FROM (
            SELECT CASE WHEN d.qty_uncertified <> 0 THEN SUM(d.qty_uncertified) ELSE 0 END AS qtyUN
            FROM `delivery` d INNER JOIN shipment sh ON sh.shipment_id = d.`shipment_id` 
            LEFT JOIN TRANSACTION t2 ON t2.`shipment_id` = sh.`shipment_id` WHERE 1=1 
            AND SUBSTRING(t2.slip_no,1,3) = '{$stockpileCode}' AND t2.transaction_type = 2
            GROUP BY d.qty_uncertified
    ) a ";
    $result3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);			
    if($result3->num_rows == 1) {
        $row3 = $result3->fetch_object();     
            $begining2_UN = $row3->UN;
    }

	$beginingUN = $begining1_UN - $begining2_UN;
    if($beginingUN <= 0){
        $beginingUN = 0;
    }
   

    if($qty_UN > $beginingUN){
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<b><span>Nilai Inputan Un-Certified SALAH</span></b>";
    }else {
        $htmlValue = "|0|";
        $htmlValue = $htmlValue . "<span></span>";
    }

    echo $htmlValue;
}  

function setSlipFreightPC($stockpileId, $freightId, $vendorFreightId, $checkedSlips, $ppn, $pph, $paymentFrom, $paymentTo)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    //$vendorFreightId[] = array();
    if ($checkedSlips == '') {
        for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
            if ($vendorFreightIds == '') {
                $vendorFreightIds .= $vendorFreightId[$i];
            } else {
                $vendorFreightIds .= ',' . $vendorFreightId[$i];
            }
        }
    } else {
        $vendorFreightIds = $vendorFreightId;
    }

    //echo $vendorFreightIds ;

    if ($vendorFreightIds != 0) {
        $whereProperty = "AND t.vendor_id IN ({$vendorFreightIds})";
    }

    $sql = "SELECT t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`,

			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1

				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim
            FROM TRANSACTION t
            LEFT JOIN freight_cost fc
                ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.fc_tax_id
			LEFT JOIN vendor v
				ON fc.vendor_id = v.vendor_id
			LEFT JOIN stockpile_contract sc
		        ON sc.stockpile_contract_id = t.stockpile_contract_id
			LEFT JOIN contract con
				ON con.contract_id = sc.contract_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
			LEFT JOIN transaction_shrink_weight ts
				ON t.transaction_id = ts.transaction_id
            WHERE fc.freight_id = {$freightId}
			AND sc.stockpile_id = {$stockpileId}
			{$whereProperty}
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.fc_payment_id IS NULL AND freight_price != 0
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')
			AND (t.posting_status = 2 OR t.posting_status IS NULL)
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	//echo $sql;
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th><th>Slip No.</th><th>Transaction Date</th><th>PO No.</th><th>Vendor Code</th><th>Vehicle No</th><th>Quantity</th><th>Freight Cost / kg</th><th>Amount</th><th>Shrink Qty Claim</th><th>Shrink Price Claim</th><th>Shrink Amount</th><th>Total Amount</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if ($row->freight_rule == 1) {
                $fp = $row->freight_price * $row->send_weight;
                $fq = $row->send_weight;
            } else {
                $fp = $row->freight_price * $row->freight_quantity;
                $fq = $row->freight_quantity;
            }

            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
                $dppTotalPrice = $fp;
                $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
            } else {
                if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                    $dppTotalPrice = $fp;
                    $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                } else {
                    if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                        $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                        $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                    } else {
                        if ($row->pph_tax_category == 1) {
                            $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                            $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                        } else {
                            $dppTotalPrice = $fp;
                            $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                        }
                    }

                }
            }
            $freightPrice = 0;
            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                $freightPrice = $fp;
            } else {
                $freightPrice = $fp;
            }

            $amountPrice = $dppTotalPrice - $dppShrinkPrice;
//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }

            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" checked /></td>';


                    $totalPrice = $totalPrice + $amountPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vehicle_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($fq, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($freightPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qtyClaim, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->trx_shrink_claim, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppShrinkPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($amountPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, f.pph, p.stockpile_location, f.freight_id
FROM payment p LEFT JOIN freight f ON p.`freight_id` = f.`freight_id`
WHERE p.freight_id = {$freightId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $dp = $row->down_payment * ((100 - $row->pph) / 100);
                $fc_ppn_dp = $row->down_payment * ($row->ppn / 100);
                $downPayment = $dp + $fc_ppn_dp;

                //if($row->freight_id == 312 && $row->stockpile_location != 8){
                //	$downPayment = 0;
                //}

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="12" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

//            $ppnDB = 0;
//            $pphDB = 0;
//            $sql = "SELECT freight_id, ppn, pph FROM freight WHERE freight_id = {$freightId}";
//            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($result->num_rows == 1) {
//                $row = $result->fetch_object();
//                $ppnDB = $row->ppn;
//                $pphDB = $row->pph;
//            }
//
//            $ppnDBAmount = ($ppnDB/100) * $totalPrice;
//            $pphDBAmount = ($pphDB/100) * $totalPrice;

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            if ($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif ($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if ($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif ($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }

//            $totalPpn = ($ppnValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            $returnValue .= '</tr>';

            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


            $totalPrice = ($totalPrice + $totalPpn) - $totalPph;

            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        //$returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="vendorFreights" id="vendorFreights" value="'. $vendorFreightIds .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', this, document.getElementById(\'ppn\'), document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" />';
        $returnValue .= '<input type="hidden" id="fc_ppn_dp" name="fc_ppn_dp" value="' . $fc_ppn_dp . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
	
}
function getContractCurah($stockpileId, $vendorId) {
    global $myDatabase;
    $returnValue = '';
   /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT c.* FROM contract c
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
WHERE sc.`stockpile_id` = {$stockpileId} AND c.`vendor_id` = {$vendorId} AND t.`payment_id` IS NULL
GROUP BY t.`stockpile_contract_id`";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            if($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }
    
    if ($returnValue == '') {
        $returnValue = '~';
    } 
    
    echo $returnValue;
}
function getContractCurahDp($stockpileId, $vendorId) {
    global $myDatabase;
    $returnValue = '';
   /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT c.* FROM contract c
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
WHERE sc.`stockpile_id` = {$stockpileId} AND c.vendor_id = {$vendorId} AND c.`contract_type` = 'C' AND DATE_FORMAT(c.`entry_date`,'%Y') > DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 YEAR), '%Y')
ORDER BY sc.`stockpile_contract_id` DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            if($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }
    
    if ($returnValue == '') {
        $returnValue = '~';
    } 
    
    echo $returnValue;
}
function getContractFreight($stockpileId, $freightId) {
    global $myDatabase;
    $returnValue = '';
   /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT c.* FROM contract c
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN freight_cost vhc ON vhc.`freight_cost_id` = t.`freight_cost_id`
WHERE vhc.`stockpile_id` = {$stockpileId} AND vhc.`freight_id` = {$freightId} AND t.`fc_payment_id` IS NULL
GROUP BY t.`stockpile_contract_id`";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            if($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }
    
    if ($returnValue == '') {
        $returnValue = '~';
    } 
    
    echo $returnValue;
}
function getContractFreightDp($stockpileId, $freightId) {
    global $myDatabase;
    $returnValue = '';
   /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT c.* FROM contract c
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
WHERE sc.`stockpile_id` = {$stockpileId} AND c.`contract_type` = 'P' AND DATE_FORMAT(c.`entry_date`,'%Y') > DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 YEAR), '%Y')
ORDER BY sc.`stockpile_contract_id` DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            if($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }
    
    if ($returnValue == '') {
        $returnValue = '~';
    } 
    
    echo $returnValue;
}
function getContractHandling($stockpileId, $vendorHandlingId) {
    global $myDatabase;
    $returnValue = '';
   /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT c.* FROM contract c
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN vendor_handling_cost vhc ON vhc.`handling_cost_id` = t.`handling_cost_id`
WHERE vhc.`stockpile_id` = {$stockpileId} AND vhc.`vendor_handling_id` = {$vendorHandlingId} AND t.`hc_payment_id` IS NULL
GROUP BY t.`stockpile_contract_id`";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            if($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }
    
    if ($returnValue == '') {
        $returnValue = '~';
    } 
    
    echo $returnValue;
}
function getContractHandlingDp($stockpileId, $vendorHandlingId) {
    global $myDatabase;
    $returnValue = '';
   /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT c.* FROM contract c
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
WHERE sc.`stockpile_id` = {$stockpileId} AND c.`contract_type` = 'P' AND DATE_FORMAT(c.`entry_date`,'%Y') > DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 YEAR), '%Y')
ORDER BY sc.`stockpile_contract_id`";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            if($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }
    
    if ($returnValue == '') {
        $returnValue = '~';
    } 
    
    echo $returnValue;
}

function getVendorHandlingReport($stockpileId)
{
    global $myDatabase;
    $returnValue = '';
    /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT DISTINCT(fc.vendor_handling_id), CONCAT(f.vendor_handling_code,' - ', f.vendor_handling_name) AS vendor_handling_name
            FROM vendor_handling_cost fc
            LEFT JOIN vendor_handling f
                ON f.vendor_handling_id = fc.vendor_handling_id
            WHERE fc.stockpile_id IN ({$stockpileId})
            AND fc.company_id = {$_SESSION['companyId']}
			ORDER BY f.vendor_handling_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_handling_id . '||' . $row->vendor_handling_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_handling_id . '||' . $row->vendor_handling_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSlipNoUnloading($stockpileId, $laborId, $paymentFrom, $paymentTo)
{
    global $myDatabase;
    $returnValue = '';


    if ($paymentFrom !== '' && $paymentTo == '') {
        $whereProperty = "AND t.transaction_date >= STR_TO_DATE('$paymentFrom', '%d/%m/%Y')";

    } else if ($paymentFrom == '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date <= STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    } else if ($paymentFrom !== '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    }

    if ($checkedSlips == '') {

        for ($i = 0; $i < sizeof($transactionId); $i++) {
            if ($transactionIds == '') {
                $transactionIds .= $transactionId[$i];
            } else {
                $transactionIds .= ',' . $transactionId[$i];
            }
        }
    } else {

        $transactionIds = $transactionId;
    }


    if ($transactionIds != 0) {
        $whereProperty = "AND t.transaction_id IN ({$transactionIds})";
    }

    $sql = "SELECT t.*,
                l.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value
            FROM `transaction` t
            INNER JOIN labor l
                ON l.labor_id = t.labor_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = l.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.uc_tax_id
            WHERE t.labor_id = {$laborId}
            AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.uc_payment_id IS NULL AND t.unloading_cost_id IS NOT NULL AND t.adj_ob IS NULL
			{$whereProperty}
			
			ORDER BY t.transaction_date ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->transaction_id . '||' . $row->slip_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->transaction_id . '||' . $row->slip_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSlipNo($stockpileId, $freightId, $vendorFreightId, $checkedSlips, $ppn, $pph, $paymentFrom, $paymentTo)
{
    global $myDatabase;
    $returnValue = '';


    for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
        if ($vendorFreightIds == '') {
            $vendorFreightIds .= $vendorFreightId[$i];
        } else {
            $vendorFreightIds .= ',' . $vendorFreightId[$i];
        }
    }


    //echo $vendorFreightIds ;
    if ($paymentFrom !== '' && $paymentTo == '') {
        $whereProperty = "AND t.transaction_date >= STR_TO_DATE('$paymentFrom', '%d/%m/%Y')";

    } else if ($paymentFrom == '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date <= STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    } else if ($paymentFrom !== '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    }
    if ($vendorFreightIds != 0) {
        $whereProperty = "AND t.vendor_id IN ({$vendorFreightIds})";
    }

    $sql = "SELECT t.*
            FROM TRANSACTION t
            LEFT JOIN freight_cost fc
                ON fc.freight_cost_id = t.freight_cost_id
			LEFT JOIN stockpile_contract sc
		        ON sc.stockpile_contract_id = t.stockpile_contract_id
			LEFT JOIN contract con
				ON con.contract_id = sc.contract_id
            WHERE fc.freight_id = {$freightId}
			AND sc.stockpile_id = {$stockpileId}
			{$whereProperty}
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.fc_payment_id IS NULL AND t.freight_price != 0 AND t.adj_oa IS NULL
			
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->transaction_id . '||' . $row->slip_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->transaction_id . '||' . $row->slip_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function setSlipFreight2($stockpileId, $freightId, $vendorFreightId, $checkedSlips, $ppn, $pph, $paymentFrom, $paymentTo, $transactionId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    //$vendorFreightId[] = array();
    if ($checkedSlips == '') {
        for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
            if ($vendorFreightIds == '') {
                $vendorFreightIds .= $vendorFreightId[$i];
            } else {
                $vendorFreightIds .= ',' . $vendorFreightId[$i];
            }
        }

        for ($i = 0; $i < sizeof($transactionId); $i++) {
            if ($transactionIds == '') {
                $transactionIds .= $transactionId[$i];
            } else {
                $transactionIds .= ',' . $transactionId[$i];
            }
        }
    } else {
        $vendorFreightIds = $vendorFreightId;
        $transactionIds = $transactionId;
    }

    //echo $vendorFreightIds ;
    if ($paymentFrom !== '' && $paymentTo == '') {
        $whereProperty = "AND t.transaction_date >= STR_TO_DATE('$paymentFrom', '%d/%m/%Y')";

    } else if ($paymentFrom == '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date <= STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    } else if ($paymentFrom !== '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    }

    if ($vendorFreightIds != 0) {
        $whereProperty = "AND t.vendor_id IN ({$vendorFreightIds})";
    }

    if ($transactionIds != 0) {
        $whereProperty = "AND t.transaction_id IN ({$transactionIds})";
    }

    $sql = "SELECT t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`, 
               
			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
				
				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
				
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim
            FROM TRANSACTION t
            LEFT JOIN freight_cost fc
                ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.fc_tax_id
			LEFT JOIN vendor v
				ON fc.vendor_id = v.vendor_id
			LEFT JOIN stockpile_contract sc
		        ON sc.stockpile_contract_id = t.stockpile_contract_id
			LEFT JOIN contract con
				ON con.contract_id = sc.contract_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
			LEFT JOIN transaction_shrink_weight ts
				ON t.transaction_id = ts.transaction_id
            WHERE fc.freight_id = {$freightId}
			AND sc.stockpile_id = {$stockpileId}
			{$whereProperty}
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.fc_payment_id IS NULL AND freight_price != 0 AND t.adj_oa IS NULL
			
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th><th>Slip No.</th><th>Transaction Date</th><th>PO No.</th><th>Vendor Code</th><th>Vehicle No</th><th>Quantity</th><th>Freight Cost / kg</th><th>Amount</th><th>Shrink Qty Claim</th><th>Shrink Price Claim</th><th>Shrink Amount</th><th>Total Amount</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if ($row->freight_rule == 1) {
                $fp = $row->freight_price * $row->send_weight;
                $fq = $row->send_weight;
            } else {
                $fp = $row->freight_price * $row->freight_quantity;
                $fq = $row->freight_quantity;
            }

            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
                $dppTotalPrice = $fp;
                $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
            } else {
                if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                    $dppTotalPrice = $fp;
                    $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                } else {
                    if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                        $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                        $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                    } else {
                        if ($row->pph_tax_category == 1) {
                            $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                            $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                        } else {
                            $dppTotalPrice = $fp;
                            $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                        }
                    }

                }
            }
            $freightPrice = 0;
            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                $freightPrice = $fp;
            } else {
                $freightPrice = $fp;
            }

            $amountPrice = $dppTotalPrice - $dppShrinkPrice;
//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }

            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" checked /></td>';


                    $totalPrice = $totalPrice + $amountPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vehicle_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($fq, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($freightPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qtyClaim, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->trx_shrink_claim, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppShrinkPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($amountPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, f.pph, f.ppn, p.stockpile_location, f.freight_id
FROM payment p LEFT JOIN freight f ON p.`freight_id` = f.`freight_id`
WHERE p.freight_id = {$freightId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                //$dp = $row->down_payment * ((100 - $row->pph)/100);
                //$fc_ppn_dp = $row->down_payment * ($row->ppn/100);
                //$downPayment = $dp + $fc_ppn_dp;

                $dp = 0;
                $fc_ppn_dp = 0;
                $downPayment = 0;

                //if($row->freight_id == 312 && $row->stockpile_location != 8){
                //	$downPayment = 0;
                //}

                /* $returnValue .= '<tr>';
                $returnValue .= '<td colspan="12" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';*/
            }

//            $ppnDB = 0;
//            $pphDB = 0;
//            $sql = "SELECT freight_id, ppn, pph FROM freight WHERE freight_id = {$freightId}";
//            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($result->num_rows == 1) {
//                $row = $result->fetch_object();
//                $ppnDB = $row->ppn;
//                $pphDB = $row->pph;
//            }
//
//            $ppnDBAmount = ($ppnDB/100) * $totalPrice;
//            $pphDBAmount = ($pphDB/100) * $totalPrice;

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            if ($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif ($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if ($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif ($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }

//            $totalPpn = ($ppnValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
            $returnValue .= '</tr>';

            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


            $totalPrice = ($totalPrice);

            if ($grandTotal < 0 && $downPayment > 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        //$returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="vendorFreights" id="vendorFreights" value="'. $vendorFreightIds .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', this, document.getElementById(\'ppn\'), document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" />';
        //$returnValue .= '<input type="hidden" id="fc_ppn_dp" name="fc_ppn_dp" value="'. $fc_ppn_dp .'" />';
        //$returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="'. $downPayment .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function setSlipUnloading2($stockpileId, $laborId, $checkedSlips, $ppn, $pph, $paymentFrom, $paymentTo, $transactionId)
{
    global $myDatabase;
    $returnValue = '';

    if ($paymentFrom !== '' && $paymentTo == '') {
        $whereProperty = "AND t.transaction_date >= STR_TO_DATE('$paymentFrom', '%d/%m/%Y')";

    } else if ($paymentFrom == '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date <= STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    } else if ($paymentFrom !== '' && $paymentTo !== '') {
        $whereProperty = "AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')";

    }

    if ($checkedSlips == '') {

        for ($i = 0; $i < sizeof($transactionId); $i++) {
            if ($transactionIds == '') {
                $transactionIds .= $transactionId[$i];
            } else {
                $transactionIds .= ',' . $transactionId[$i];
            }
        }
    } else {

        $transactionIds = $transactionId;
    }


    if ($transactionIds != 0) {
        $whereProperty = "AND t.transaction_id IN ({$transactionIds})";
    }

    $sql = "SELECT t.*,
                l.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value
            FROM `transaction` t
            INNER JOIN labor l
                ON l.labor_id = t.labor_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = l.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.uc_tax_id
            WHERE t.labor_id = {$laborId}
            AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.uc_payment_id IS NULL AND t.unloading_cost_id IS NOT NULL AND t.adj_ob IS NULL AND t.unloading_price > 0
			AND t.notim_status = 0
			{$whereProperty}
			
			ORDER BY t.transaction_date ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAllUC(this)" name="checkedSlips[]" /></th><th>Slip No.</th><th>Transaaction Date</th><th>Quantity</th><th>Currency</th><th>Unloading Cost</th><th>Total</th><th>DPP</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $row->unloading_price;
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = $row->unloading_price / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->unloading_price;
                }
            }

//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }

            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" checked /></td>';
                    $totalPrice = $totalPrice + $dppTotalPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 20%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row->quantity, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 5%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->unloading_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row->unloading_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment FROM payment WHERE labor_id = {$laborId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $downPayment = 0;

                /*$returnValue .= '<tr>';
                $returnValue .= '<td colspan="7" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';*/
            }

//            $ppnDB = 0;
//            $pphDB = 0;
//            $sql = "SELECT labor_id, ppn, pph FROM labor WHERE labor_id = {$laborId}";
//            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($result->num_rows == 1) {
//                $row = $result->fetch_object();
//                $ppnDB = $row->ppn;
//                $pphDB = $row->pph;
//            }

//            $ppnDBAmount = ($ppnDB/100) * $totalPrice;
//            $pphDBAmount = ($pphDB/100) * $totalPrice;

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            if ($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif ($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if ($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif ($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }

//            $totalPpn = ($ppn/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', this, document.getElementById(\'ppn\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', document.getElementById(\'pph\'), this, \'' . $paymentFrom . '\', \'' . $paymentTo . '\',\'' . $transactionIds . '\');" /></td>';
            $returnValue .= '</tr>';


            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;

            $totalPrice = ($totalPrice);
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        //$returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="'. $downPayment .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function getNumberPO($vendorId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT c.contract_id, c.po_no FROM contract c LEFT JOIN vendor v ON c.vendor_id = v.vendor_id WHERE v.vendor_name = '{$vendorId}' ORDER by c.po_no DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->po_no . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->po_no . '||' . $row->po_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $sql;
    echo $returnValue;
}

function getStockpileContractShipment($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sc.stockpile_contract_id, CONCAT(c.po_no,' # ', c.contract_no) AS contractNo 
FROM stockpile_contract sc
LEFT JOIN contract c ON sc.contract_id = c.contract_id WHERE sc.stockpile_id = {$stockpileId}
ORDER BY stockpile_contract_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->contractNo;
            } else {
                $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->contractNo;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $sql;
    echo $returnValue;
}

function getVendorBankPO($vendorId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';


    $sql = "SELECT gv_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM general_vendor_bank WHERE general_vendor_id = {$vendorId}";

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->vBankId . '||' . $row->bank_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vBankId . '||' . $row->bank_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function setMutasiDetail($mutasiId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT a.*, b.percentage, c.account_name,
	CASE WHEN a.uom IS NOT NULL THEN (SELECT uom_type FROM uom WHERE idUOM = a.uom) ELSE '-' END AS uom,
((a.total_per_termin + a.ppn_converted) - a.pph_converted) AS totalPerTermin, 
(IFNULL((SELECT SUM(a.price * aa.qtyInvoice) * ((SELECT tax_value FROM tax WHERE tax_id = aa.ppnId)/100) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id and aa.status = 0),0)) AS ppnMutasi,
(IFNULL((SELECT SUM(a.price * aa.qtyInvoice) * ((SELECT tax_value FROM tax WHERE tax_id = aa.pphId)/100) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id and aa.status = 0),0)) AS pphMutasi,
(((a.total_per_termin + a.ppn_converted) - a.pph_converted) - 
 ((IFNULL((SELECT SUM(a.price * aa.qtyInvoice) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id and aa.status = 0),0)) + 
 (IFNULL((SELECT SUM(aa.ppnAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id and aa.status = 0),0)))+ 
 (IFNULL((SELECT SUM(aa.pphAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id and aa.status = 0),0))) AS availableAmount,
(a.qty * (b.percentage/100) - IFNULL((SELECT SUM(qtyInvoice) FROM mutasi_qty_price WHERE mutasi_detail_id = a.mutasi_detail_id and status = 0),0)) AS qtyInvoice,
(100 - IFNULL((SELECT SUM(termin) FROM mutasi_qty_price WHERE mutasi_detail_id = a.mutasi_detail_id and status = 0),0)) AS availableTermin   
FROM mutasi_detail a
LEFT JOIN termin_detail b ON a.termin_detail_id = b.id 
LEFT JOIN account c ON a.account_id = c.account_id WHERE a.mutasi_header_id = {$mutasiId} AND a.status = 0";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';

        $returnValue .= '<form id = "invoice">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<h5>MUTASI DETAIL</h5>';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Tipe Biaya</th><th>Vendor</th><th>Quantity</th><th>Price</th><th>Total</th><th>Termin</th><th>Sub Total</th><th>Account Name</th><th>Available Amount</th><th>Termin (Invoice)</th><th>Quantity (Invoice)</th><th>PPN</th><th>PPh</th><th>Qty Estimasi</th><th></th></tr></thead>';
        $returnValue .= '<tbody>';

        /* $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $dp_ppn = 0;
        $dp_pph = 0;
        $count = 0;*/
        while ($row = $result->fetch_object()) {

            /*$dppTotalPrice = $row->amount;

            if ($row->ppn != 0 && $row->ppn != '') {
                $totalPPN = $row->ppn;
            } else {
                $totalPPN = 0;
            }

            if ($row->pph != 0 && $row->pph != '') {
                $totalPPh = $row->pph;
            } else {
                $totalPPh = 0;
            }

            if ($row->dp_ppn != 0 && $row->ppn != 0) {
                $dp_ppn = $row->total_dp * ($row->dp_ppn / 100);
            } else {
                $dp_ppn = 0;
            }

            if ($row->dp_pph != 0 && $row->pph != 0) {
                $dp_pph = $row->total_dp * ($row->dp_pph / 100);
            } else {
                $dp_pph = 0;
            }

            $total = ($dppTotalPrice + $totalPPN) - $totalPPh;
            $total2 = ($row->total_dp + $dp_ppn) - $dp_pph;
            $total_dp = $total - $total2;*/

            $returnValue .= '<tr>';


            $returnValue .= '<td>' . $row->tipe_biaya . '</td>';
            $returnValue .= '<td>' . $row->vendor . '</td>';
            //$returnValue .= '<td style="width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 0%;">' . number_format($row->qty, 2, ".", ",") . ' ' . $row->uom . '</td>';
            $returnValue .= '<td style="text-align: right; width: 0%;">' . number_format($row->price, 10, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 0%;">' . number_format($row->total, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 0%;">' . number_format($row->percentage, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 0%;">' . number_format($row->total_per_termin, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 100%;">' . $row->account_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 0%;">' . number_format($row->availableAmount, 2, ".", ",") . '</td>';
            //$returnValue .= '<td style="text-align: right; width: 15%;">'. number_format($total_dp, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right;"><input type="text" id="terminDetailInvoice" name="checkedMutasi1[' . $count . ']" value="' . $row->availableTermin . '" onkeyup="sum2(' . $count . ');"/></td>';
            $returnValue .= '<input type="hidden" id="qtyInvoiceValue" name="checkedMutasi2[' . $count . ']" value="' . round($row->qtyInvoice, 10) . '"/>';
            $returnValue .= '<td style="text-align: right;"><input type="text" id="qtyInvoice" name="checkedMutasi3[' . $count . ']" value="' . round($row->qtyInvoice, 10) . '"/></td>';

            $returnValue .= '<td><input type="checkbox" name="checkedMutasi4[' . $count . ']" id="ppnMutasi"  value="' . $row->ppnId . '"   checked/></td>';

            $returnValue .= '<td><input type="checkbox" name="checkedMutasi5[' . $count . ']" id="pphMutasi"  value="' . $row->pphId . '"  checked/></td>';
            $returnValue .= '<td><input type="checkbox" name="checkedMutasi6[' . $count . ']" id="estimasi"  value="1"  checked/></td>';

            /* if ($checkedMutasi != '') {
                $pos = strpos($checkedMutasi4, $row->ppnId);

                if ($pos === false) {

                    $returnValue .= '<td><input type="checkbox" name="checkedMutasi4[' . $count . ']" id="ppnMutasi"   value="' . $row->ppnId . '" /></td>';
                } else {

                    $returnValue .= '<td><input type="checkbox" name="checkedMutasi4[' . $count . ']" id="ppnMutasi"  value="' . $row->ppnId . '" checked /></td>';

				}
            } else {

                $returnValue .= '<td><input type="checkbox" name="checkedMutasi4[' . $count . ']" id="ppnMutasi"  value="' . $row->ppnId . '" /></td>';
            }
			if ($checkedMutasi != '') {
                $pos = strpos($checkedMutasi5, $row->pphId);

                if ($pos === false) {

                    $returnValue .= '<td><input type="checkbox" name="checkedMutasi5[' . $count . ']" id="pphMutasi"   value="' . $row->pphId . '" /></td>';
                } else {

                    $returnValue .= '<td><input type="checkbox" name="checkedMutasi5[' . $count . ']" id="pphMutasi"  value="' . $row->pphId . '" checked /></td>';

                }
            } else {

                $returnValue .= '<td><input type="checkbox" name="checkedMutasi5[' . $count . ']" id="pphMutasi"  value="' . $row->pphId . '" /></td>';
            }*/
            if ($checkedMutasi != '') {
                $pos = strpos($checkedMutasi, $row->mutasi_detail_id);

                if ($pos === false) {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedMutasi[' . $count . ']" id="inv"   value="' . $row->mutasi_detail_id . '" /></td>';
                } else {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedMutasi[' . $count . ']" id="inv"  value="' . $row->mutasi_detail_id . '" checked /></td>';

                    //$dppPrice = $dppPrice + $dppTotalPrice;
                    //$totalPrice = $totalPrice + $total;
                    //$total_ppn = $total_ppn + $totalPPN;
                    //$total_pph = $total_pph + $totalPPh;

                }
            } else {
                //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                $returnValue .= '<td><input type="checkbox" name="checkedMutasi[' . $count . ']" id="inv"  value="' . $row->mutasi_detail_id . '" /></td>';
            }

            $returnValue .= '</tr>';
            $count = $count + 1;
        }

        $returnValue .= '</tbody>';
        /* if($checkedSlips != '') {

			$grandTotal = $totalPrice;
			if($grandTotal < 0) {
                $grandTotal = 0;
            }*/
        /*$returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Grand Total</td>';
            //$returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right;"><input type="text" readonly class="span12" id="dp_total" name="dp_total"></td>';
            $returnValue .= '</tr>';
    		$returnValue .= '</tfoot>';*/
        /* }else{
			$grandTotal = 0;
			$total_pph = 0;
			$total_ppn = 0;
		}*/
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        //$returnValue .= '<input type="hidden" id="available_dp" name="available_dp	" value="' . number_format($total_dp, 2, ".", ",") . '" />';
        //$returnValue .= '<input type="hidden" id="ppnDP2" name="ppnDP2" value="' . number_format($totalPPN, 2, ".", ",") . '" />';
        // $returnValue .= '<input type="hidden" id="pphDP2" name="pphDP2" value="' . number_format($totalPPh, 2, ".", ",") . '" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function getGeneralVendorPPN($id)
{
    global $myDatabase;

    $sql = "SELECT * FROM general_vendor where general_vendor_id = {$id}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn = $row->ppn;
        $ppn_tax_id = $row->ppn_tax_id;
        echo $ppn . '-' . $ppn_tax_id;
    }
}

function getMutasiHeader()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT mutasi_header_id, kode_mutasi FROM mutasi_header ORDER BY mutasi_header_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->mutasi_header_id . '||' . $row->kode_mutasi;
            } else {
                $returnValue = $returnValue . '{}' . $row->mutasi_header_id . '||' . $row->kode_mutasi;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $sql;
    echo $returnValue;
}

function getPPNPPH($vendorType, $vendorIdName)
{
    global $myDatabase;
    $vendor = explode("-", $vendorIdName);
    $vendorId = $vendor[0];
    $vendorName = $vendor[1];

    if (isset($vendorType) && $vendorType == 'Normal') {
        $sql = "SELECT * FROM vendor where vendor_id = {$vendorId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $ppn = $row->ppn;
            $pph = $row->pph;
            $ppn_tax_id = $row->ppn_tax_id;
            $pph_tax_id = $row->pph_tax_id;
        }
    } elseif (isset($vendorType) && $vendorType == 'Freight') {
        $sql = "SELECT * FROM freight where freight_id = {$vendorId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $ppn = $row->ppn;
            $pph = $row->pph;
            $ppn_tax_id = $row->ppn_tax_id;
            $pph_tax_id = $row->pph_tax_id;

        }

    } elseif (isset($vendorType) && $vendorType == 'Labor') {
        $sql = "SELECT * FROM labor where labor_id = {$vendorId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $ppn = $row->ppn;
            $pph = $row->pph;
            $ppn_tax_id = $row->ppn_tax_id;
            $pph_tax_id = $row->pph_tax_id;

        }

    } elseif (isset($vendorType) && $vendorType == 'Handling') {
        $sql = "SELECT * FROM vendor_handling where vendor_handling_id = {$vendorId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $ppn = $row->ppn;
            $pph = $row->pph;
            $ppn_tax_id = $row->ppn_tax_id;
            $pph_tax_id = $row->pph_tax_id;
        }

    } elseif (isset($vendorType) && $vendorType == 'PettyCash') {
        $ppn = 0;
        $pph = 0;
        $ppn_tax_id = 0;
        $pph_tax_id = 0;
    } else {
        $ppn = 0;
        $pph = 0;
        $ppn_tax_id = 0;
        $pph_tax_id = 0;
    }

    if ($vendorType != 'General') {
        echo $ppn . '-' . $pph . '-' . $ppn_tax_id . '-' . $pph_tax_id;
    }
}

function getInvCategory($categoryId, $newInvCategoryId)
{
    global $myDatabase;
    $returnValue = '';
    $unionSql = '';

    if ($newInvCategoryId == 0 or $newInvCategoryId == '') {
        $unionSql = '';
    } else {
        $unionSql = " UNION SELECT id,name FROM  logbook_inv_category WHERE id = {$newInvCategoryId}";
    }

    $sql = "SELECT id,name FROM logbook_inv_category WHERE logbook_category_id =  {$categoryId} {$unionSql}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->id . '||' . $row->name;
            } else {
                $returnValue = $returnValue . '{}' . $row->id . '||' . $row->name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSalesPriceReturn($shipmentId)
{
    global $myDatabase;

    $sql = "SELECT price_converted FROM shipment sh LEFT JOIN sales sl  ON sl.`sales_id` = sh.`sales_id` WHERE sh.shipment_id = {$shipmentId} LIMIT 1";
    $resultShipment = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultShipment !== false && $resultShipment->num_rows == 1) {
        $rowShipment = $resultShipment->fetch_object();

        $salesPrice = $rowShipment->price_converted;

    }

    echo $salesPrice;
}

function getShipmentReturn($currentYear)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sh.shipment_id, sh.shipment_no FROM shipment sh 
LEFT JOIN sales sl ON sl.`sales_id` = sh.`sales_id`
WHERE sl.`return_status` = 1
GROUP BY sh.`sales_id`
ORDER BY shipment_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $sql;
    echo $returnValue;
}

function getUnitCost($shipmentId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT (SUM(cogs_pks)/SUM(qty)) AS cogsPKS, (SUM(cogs_oa)/SUM(qty)) AS cogsOA, (SUM(cogs_ob)/SUM(qty)) AS cogsOB, (SUM(cogs_handling)/SUM(qty)) AS cogsHandling FROM adjustment_audit_qty WHERE shipment_id = {$shipmentId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->cogsPKS . '||' . $row->cogsOA . '||' . $row->cogsOB . '||' . $row->cogsHandling;
    }

    echo $returnValue;
}

function getShipmentAudit($stockpileId)
{
    global $myDatabase;
    $returnValue = '';
    //$whereProperty = '';
    //$joinProperty = '';


    $sql = "SELECT a.shipment_id, CONCAT(a.shipment_no, ' - ', SUBSTR(a.`shipment_code`,-1)) AS shipment_no, b.`stockpile_id`, a.`shipment_date`  
FROM shipment a LEFT JOIN sales b ON a.`sales_id` = b.`sales_id` WHERE b.`sales_status` != 4 AND b.stockpile_id = {$stockpileId} AND SUBSTR(a.`shipment_no`,-2) != '-S'
AND DATE_FORMAT(a.`shipment_date`,'%Y') = (DATE_FORMAT(CURDATE(),'%Y')) -1
ORDER BY shipment_id DESC LIMIT 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    //echo $sql;
    echo $returnValue;
}
function getPrediksiId($shipmentId, $tipeBiayaId, $generalVendorId, $prediksiId)
{
    global $myDatabase;
    $returnValue = '';

    if($prediksiId == 0){

        $sql = "SELECT apd.prediction_detail_id, apd.generate_code_detail, apd.prediction_id FROM accrue_prediction_detail apd
                LEFT JOIN accrue_prediction ap ON ap.`prediction_id` = apd.`prediction_id`
                WHERE ap.`shipment_id` = {$shipmentId} AND apd.cost_name = {$tipeBiayaId} 
                AND apd.`general_vendor_id` = {$generalVendorId} 
                AND apd.status in (0,2) AND apd.journal_status <> 2 AND apd.qty > 0";
    }else{
        $sql = "SELECT apd.prediction_detail_id, apd.generate_code_detail, apd.prediction_id FROM accrue_prediction_detail apd
                LEFT JOIN accrue_prediction ap ON ap.`prediction_id` = apd.`prediction_id`
                WHERE ap.`shipment_id` = {$shipmentId} AND apd.cost_name = {$tipeBiayaId} 
                AND apd.`general_vendor_id` = {$generalVendorId} 
                AND apd.status in (1) AND apd.journal_status <> 2 AND apd.qty > 0 AND apd.prediction_detail_id = {$prediksiId}";

    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    echo $sql;


    if ($result->num_rows > 1) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->prediction_detail_id . '||' . $row->generate_code_detail . '||' . $row->prediction_id;
            } else {
                $returnValue = $returnValue . '{}' . $row->prediction_detail_id . '||' . $row->generate_code_detail . '||' . $row->prediction_id;
            }
        }
    }else if($result->num_rows == 1){
        $row = $result->fetch_object();
        $returnValue = '~' . $row->prediction_detail_id . '~' . $row->generate_code_detail . '~' . $row->prediction_id;
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    //echo $sql;
    echo $returnValue;
}

function setPrediksiDetail($prediksiId, $status)
{
    global $myDatabase;
    $returnValue = '';
    $whereStatus = '';

    if($status == 0){
        $whereStatus = " apd.status in (0,2)";
    }else{
        $whereStatus = " apd.status in (1)";
    }


    $sql = "SELECT CONCAT(a.account_no, ' - ', a.account_name) AS accountName, apd.mcd_id,
                CASE WHEN apd.qty_type_id = 1 THEN 'Vessel' 
		    WHEN apd.qty_type_id = 2 THEN 'Tongkang'
		    WHEN apd.qty_type_id = 3 THEN 'Timbangan Darat'
		    WHEN apd.qty_type_id = 4 THEN 'Volume'
		     ELSE 'Others' END AS jenisQty,
                apd.max_charge, apd.min_charge, apd.qty, apd.priceMT, apd.total_amount, ap.prediction_code,
                apd.exchange_rate, apd.in_rupiah, s.stockpile_name AS spremarks, apd.generate_code_detail, 
                apd.prediction_detail_id,
                CASE WHEN apd.currency = 2 THEN '$'
		    WHEN apd.currency = 3 THEN 'S$'
		    WHEN apd.currency = 4 THEN '¥' ELSE 'Rp.' END AS curr,
		CASE WHEN apd.price_type_id = 1 THEN 'Var' ELSE 'Fix' END AS priceType, u.uom_type
		 
			FROM accrue_prediction ap
            LEFT JOIN accrue_prediction_detail apd ON apd.`prediction_id` = ap.`prediction_id`
            LEFT JOIN account a ON a.`account_id` = apd.`account_id`
            LEFT JOIN stockpile s ON s.stockpile_id = apd.stockpile_id
            LEFT JOIN currency cur ON cur.currency_id = apd.currency
            LEFT JOIN mst_costing_detail mcd ON mcd.mcd_id = apd.mcd_id
             LEFT JOIN uom u ON u.idUOM = mcd.uom 
            WHERE apd.prediction_detail_id = {$prediksiId} AND {$whereStatus}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th>Prediksi Code</th>
								<th>Qty Type</th>
								<th>Price Type</th>
                                <th>Max Charge</th>
                                <th>Min Charge</th>
                                <th>Qty Value</th>
								 <th>UOM</th>
                                <th>Price</th>
                                <th>Total Amount</th>
                                <th>Kurs</th>
                                <th>Rupiah</th>
                                <th>Stockpile Remarks</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';
        while ($row = $result->fetch_object()) {
            $prediksiAmount = $row->total_amount;
            $codePrediksi = $row->generate_code_detail;
            $tempPrediksi = $row->prediction_detail_id;
            $returnValue .= '<tr>';
            $returnValue .= '<td style="width: 10%;">' . $row->prediction_code . '</td>';
			$returnValue .= '<td style="width: 5%;">' . $row->jenisQty . '</td>';
			$returnValue .= '<td style="width: 5%;">' . $row->priceType . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->max_charge, 5, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->min_charge, 5, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qty, 5, ".", ",") . '</td>';
			$returnValue .= '<td style="width: 5%;">' . $row->uom_type . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' .$row->curr .''. number_format($row->priceMT, 5, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . $row->curr .''. number_format($row->total_amount, 5, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . 'Rp.'.number_format($row->exchange_rate, 5, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. 'Rp.'.number_format($row->in_rupiah, 5, ".", ",") .'</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->spremarks . '</td>';
        }

        $returnValue .= '</tbody>';
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="prediksi_amount" name="prediksi_amount" value="'.number_format($prediksiAmount, 2, ".", ",").'" />';
        $returnValue .= '<input type="hidden" id="codePrediksi" name="codePrediksi" value="' . $codePrediksi . '" />';
        $returnValue .= '<input type="hidden" id="prediksiDetailId" name="prediksiDetailId" value="' . $tempPrediksi . '" />';


        $returnValue .= '</div>';
    }

    echo $returnValue;
}
function getShipmentInvoice($stockpileId2)
{
    global $myDatabase;
    $returnValue = '';
    //$whereProperty = '';
    //$joinProperty = '';


    $sql = "SELECT a.shipment_id, CONCAT(a.shipment_no, ' - ', SUBSTR(a.`shipment_code`,-1)) AS shipment_no, b.`stockpile_id`  
            FROM shipment a LEFT JOIN sales b ON a.`sales_id` = b.`sales_id` WHERE b.`sales_status` != 4 ORDER BY shipment_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    //echo $sql;
    echo $returnValue;
}

function getShipmentInvoicePrediksi($invoiceType, $prediksiDetailId)
{
    global $myDatabase;
    $returnValue = '';
    //$whereProperty = '';
    //$joinProperty = '';

if($prediksiDetailId == 0){
    $sql = "SELECT a.shipment_id, CONCAT(a.shipment_no, ' - ', SUBSTR(a.`shipment_code`,-1)) AS shipment_no, b.`stockpile_id`  
            FROM shipment a 
            INNER JOIN accrue_prediction b ON a.`shipment_id` = b.`shipment_id` 
            LEFT JOIN sales sl
                ON sl.sales_id = a.sales_id
            WHERE b.`invoice_type` = {$invoiceType} 
            AND b.status <> 1 AND sl.sales_status = 0 ORDER BY shipment_id DESC";
}else{
    $sql = "SELECT a.shipment_id, CONCAT(a.shipment_no, ' - ', SUBSTR(a.`shipment_code`,-1)) AS shipment_no
            FROM shipment a 
            INNER JOIN pengajuan_general_detail b ON a.`shipment_id` = b.`shipment_id` 
            LEFT JOIN sales sl
                ON sl.sales_id = a.sales_id
    WHERE b.prediction_detail_id = {$prediksiDetailId} AND sl.sales_status = 0 ORDER BY shipment_id DESC";
}

// echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    //echo $sql;
    echo $returnValue;
}



function getFreightName($freightId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT * from freight where freight_id=$freightId";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->freight_id . '||' . $row->freight_name;

    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getPaymentNo($searchPeriodFrom, $searchPeriodTo)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($searchPeriodTo != '' && $searchPeriodFrom == '') {
        $whereProperty = "AND payment_date <= STR_TO_DATE('{$searchPeriodFrom}', '%d/%m/%Y')";
    } else if ($searchPeriodTo == '' && $searchPeriodFrom != '') {
        $whereProperty = "AND payment_date >= STR_TO_DATE('{$searchPeriodFrom}', '%d/%m/%Y')";
    } else if ($searchPeriodTo != '' && $searchPeriodFrom != '') {
        $whereProperty = "AND payment_date BETWEEN STR_TO_DATE('{$searchPeriodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$searchPeriodTo}', '%d/%m/%Y')";
    }

    $sql = "SELECT DISTINCT(payment_no) FROM Payment WHERE 1=1 {$whereProperty} ORDER BY payment_no ASC";

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->payment_no . '||' . $row->payment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->payment_no . '||' . $row->payment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getBankName($masterBankId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT * from master_bank where master_bank_id=$masterBankId";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->master_bank_id . '||' . $row->bank_name;

    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getVendorBankDetail($vendorBankId, $paymentFor)
{
    global $myDatabase;
    $returnValue = '';

    if ($paymentFor == 0 || $paymentFor == 1) { //PKS / Curah
        $sql = "SELECT *
            FROM vendor_bank
            WHERE v_bank_id = {$vendorBankId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 2) { //Freight
        $sql = "SELECT *
            FROM freight_bank
            WHERE f_bank_id = {$vendorBankId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 3) { //Unloading
        $sql = "SELECT *
            FROM labor_bank
            WHERE l_bank_id = {$vendorBankId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 6 || $paymentFor == 8 || $paymentFor == 10) { //HO / Invoice
        $sql = "SELECT *
            FROM general_vendor_bank
            WHERE gv_bank_id = {$vendorBankId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 9) { //vendor_hanndling
        $sql = "SELECT *
            FROM vendor_handling_bank
            WHERE vh_bank_id = {$vendorBankId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $beneficiary = $row->beneficiary;
        $bank = $row->bank_name;
        $rek = $row->account_no;
        $swift = $row->swift_code;


        $returnValue = '|' . $beneficiary . '|' . $bank . '|' . $rek . '|' . $swift;

    } else {
        $returnValue = '|-|-|-|-';
    }
    echo $returnValue;
}


function getVendorBank($vendorId, $paymentFor)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($paymentFor == 0 || $paymentFor == 1) {//PKS / Curah
        $sql = "SELECT v_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM vendor_bank WHERE vendor_id = {$vendorId} AND active = 0";

    } else if ($paymentFor == 2) {//Freight
        $sql = "SELECT f_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM freight_bank WHERE freight_id = {$vendorId} AND active = 0";

    } else if ($paymentFor == 3) {//Labor
        $sql = "SELECT l_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM labor_bank WHERE labor_id = {$vendorId} AND active = 0";

    } else if ($paymentFor == 8 || $paymentFor == 10) {//Invoice
        $sql = "SELECT gv_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM general_vendor_bank WHERE general_vendor_id = {$vendorId} AND active = 0";
      //  echo $sql;

    } else if ($paymentFor == 9) {//vendor_hanndling
        $sql = "SELECT vh_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM vendor_handling_bank WHERE vendor_handling_id = {$vendorId} AND active = 0";

    }
    echo $sql;


    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->vBankId . '||' . $row->bank_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vBankId . '||' . $row->bank_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getGeneralVendorPPh($generalVendorId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';


    $sql = "SELECT acc.pph_tax_id, tx.tax_name
            FROM general_vendor_pph acc 
			LEFT JOIN tax tx on tx.tax_id = acc.pph_tax_id
            WHERE acc.status = 0 AND acc.general_vendor_id = {$generalVendorId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->pph_tax_id . '||' . $row->tax_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->pph_tax_id . '||' . $row->tax_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSPB($spb)
{
    global $myDatabase;
    $returnValue = '';
    if ($spb == '') {
        $sql = "SELECT purchasing_id AS spbDoc FROM purchasing WHERE contract_type = 2 AND admin_input IS NULL AND status = 0 AND type = 1
			ORDER BY purchasing_id ASC";

    } else {
        $sql = "SELECT purchasing_id AS spbDoc FROM purchasing WHERE contract_type = 2 AND (purchasing_id = {$spb} OR admin_input IS NULL) AND status = 0 AND type = 1
			ORDER BY purchasing_id ASC";
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->spbDoc . '||' . $row->spbDoc;
            } else {
                $returnValue = $returnValue . '{}' . $row->spbDoc . '||' . $row->spbDoc;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getConDoc($contract)
{
    global $myDatabase;
    $returnValue = '';
    if ($contract == 0) {
        $sql = "SELECT purchasing_id,concat(purchasing_id,'-',open_add) as purchasing_code FROM purchasing WHERE contract_type = 1 AND (admin_input IS NULL OR open_add=1) AND status = 0 AND type = 1
			ORDER BY purchasing_id ASC";

    } else {
        $sql = "SELECT  purchasing_id,concat(purchasing_id,'-',open_add) as purchasing_code FROM purchasing WHERE contract_type = 1 AND purchasing_id = {$contract}  AND type = 1
			ORDER BY purchasing_id ASC";
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->purchasing_id . '||' . $row->purchasing_code;
            } else {
                $returnValue = $returnValue . '{}' . $row->purchasing_id . '||' . $row->purchasing_code;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getContractDoc_curah($contract)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pu.purchasing_id, v.`vendor_name`, pu.`vendor_id`, s.`stockpile_name`, pu.`stockpile_id`, pu.`upload_file`,pu.`ho`,
			FORMAT(pu.quantity,2) AS quantity, FORMAT(pu.price,2) AS price
			FROM purchasing pu 
			LEFT JOIN vendor v ON v.`vendor_id` = pu.`vendor_id`
			LEFT JOIN stockpile s ON s.`stockpile_id` = pu.`stockpile_id`
			WHERE pu.`purchasing_id` = {$contract} AND pu.status = 0 AND pu.type = 2";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->purchasing_id . '||' . $row->vendor_name . '||' . $row->vendor_id . '||' . $row->stockpile_name . '||' . $row->stockpile_id . '||' . $row->upload_file . '||' . $row->quantity . '||' . $row->price . '||' . $row->ho;

    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getConDoc_curah($contract)
{
    global $myDatabase;
    $returnValue = '';
    if ($contract == 0) {
        $sql = "SELECT purchasing_id,concat(purchasing_id,'-',open_add) as purchasing_code FROM purchasing WHERE contract_type = 1 AND (admin_input IS NULL OR open_add=1) AND status = 0 AND type = 2
			ORDER BY purchasing_id ASC";

    } else {
        $sql = "SELECT  purchasing_id,concat(purchasing_id,'-',open_add) as purchasing_code FROM purchasing WHERE contract_type = 1 AND purchasing_id = {$contract} AND type = 2
			ORDER BY purchasing_id ASC";
    }
    // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->purchasing_id . '||' . $row->purchasing_code;
            } else {
                $returnValue = $returnValue . '{}' . $row->purchasing_id . '||' . $row->purchasing_code;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getStockpilePOPKS($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->stockpile_id . '||' . $row->stockpile_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->stockpile_id . '||' . $row->stockpile_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getVendorPOPKS($vendorId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT v.vendor_id, CONCAT(v.vendor_name, ' - ', v.vendor_code) AS vendor_full
                FROM vendor v WHERE v.active = 1 ORDER BY v.vendor_name";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSPBDoc($spb)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pu.purchasing_id, v.`vendor_name`, pu.`vendor_id`, s.`stockpile_name`, pu.`stockpile_id`, pu.`upload_file`,
			FORMAT(pu.quantity,2) AS quantity, FORMAT(pu.price,2) AS price
			FROM purchasing pu 
			LEFT JOIN vendor v ON v.`vendor_id` = pu.`vendor_id`
			LEFT JOIN stockpile s ON s.`stockpile_id` = pu.`stockpile_id`
			WHERE pu.`purchasing_id` = {$spb} AND pu.type = 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->purchasing_id . '||' . $row->vendor_name . '||' . $row->vendor_id . '||' . $row->stockpile_name . '||' . $row->stockpile_id . '||' . $row->upload_file . '||' . $row->quantity . '||' . $row->price;

    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getContractDoc($contract)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pu.purchasing_id, pu.link, v.`vendor_name`, pu.`vendor_id`, s.`stockpile_name`, pu.`stockpile_id`, pu.`upload_file`,pu.`ho`,
			FORMAT(pu.quantity,2) AS quantity, FORMAT(pu.price,2) AS price
			FROM purchasing pu 
			LEFT JOIN vendor v ON v.`vendor_id` = pu.`vendor_id`
			LEFT JOIN stockpile s ON s.`stockpile_id` = pu.`stockpile_id`
			WHERE pu.`purchasing_id` = {$contract} AND pu.status = 0 AND pu.type = 1";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->purchasing_id . '||' . $row->vendor_name . '||' . $row->vendor_id . '||' . $row->stockpile_name . '||' . $row->stockpile_id . '||' . $row->upload_file . '||' . $row->quantity . '||' . $row->price . '||' . $row->ho;

    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getStockpileContractNewNotim($tiketId, $stockpileId, $vendorId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sc.stockpile_contract_id, con.po_no
            FROM stockpile_contract sc
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            INNER JOIN surattugas st ON st.`stockpile_contract_id` = sc.`stockpile_contract_id`
            INNER JOIN transaction_upload tu ON tu.`no_do` = st.`counter`
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.vendor_id = {$vendorId}
            AND con.company_id = {$_SESSION['companyId']}
			AND tu.`slip_id` = {$tiketId}
            ORDER BY con.po_no DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->po_no;
            }
        }
    } else {
        $sql = "SELECT sc.stockpile_contract_id, con.po_no
            FROM stockpile_contract sc
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.vendor_id = {$vendorId}
            AND con.company_id = {$_SESSION['companyId']}
			
            ORDER BY con.po_no DESC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->po_no;
                } else {
                    $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->po_no;
                }
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getVendorNewNotim($tiketId, $newVendorId)
{
    global $myDatabase;
    $returnValue = '';
    $unionSql = '';

    /*if($newVendorId != 0 || $newVendorId != '') {
        $unionSql = " UNION SELECT v.vendor_id, v.vendor_name
                    FROM vendor v WHERE v.vendor_id = {$newVendorId}";
    }*/

    /*$sql = "SELECT DISTINCT(con.vendor_id), CONCAT(v.`vendor_code`, ' - ', v.vendor_name) AS vendor_name
            FROM stockpile_contract sc
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            INNER JOIN vendor v
                ON v.vendor_id = con.vendor_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.company_id = {$_SESSION['companyId']}
            {$unionSql}
			AND v.active = 1
            ORDER BY vendor_name ASC";*/
    $sql = "SELECT DISTINCT(c.vendor_id), CONCAT(v.`vendor_code`, ' - ', v.vendor_name) AS vendor_name
FROM vendor v 
LEFT JOIN contract c ON c.`vendor_id` = v.`vendor_id`
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
LEFT JOIN surattugas st ON st.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN transaction_upload tu ON tu.`no_do` = st.`counter`
WHERE v.active = 1 AND tu.`slip_id` = {$tiketId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
            }
        }
    } else {
        $sql = "SELECT DISTINCT(c.vendor_id), CONCAT(v.`vendor_code`, ' - ', v.vendor_name) AS vendor_name
FROM vendor v 
LEFT JOIN contract c ON c.`vendor_id` = v.`vendor_id`
LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
LEFT JOIN transaction_upload tu ON tu.`stockpile_id` = sc.`stockpile_id`
WHERE v.active = 1 AND tu.`slip_id` = {$tiketId} ORDER BY vendor_name ASC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
                } else {
                    $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
                }
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSuratTugasDetail($idSuratTugas)
{
    global $myDatabase;
    $returnValue = '';

    $slipId = $idSuratTugas;

    $sql = "SELECT tt.*, v.`vendor_name`, c.`po_no`, c.`contract_no`, DATE_FORMAT(tt.loading_date, '%d/%m/%Y') AS loadingDate,
FORMAT(((SELECT COALESCE(SUM(sc.quantity),0) FROM stockpile_contract sc WHERE sc.contract_id = c.contract_id) - (SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id)) -
(SELECT COALESCE(SUM(t.send_weight), 0) FROM TRANSACTION t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.`stockpile_contract_id`
WHERE sc.`contract_id` = c.`contract_id`), 2) AS quantity_available, (ROUND(tt.send_weight,0) - 
(SELECT COALESCE(SUM(t.`send_weight`),0) FROM TRANSACTION t WHERE t.t_timbangan = tt.transaction_id AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL)) AS send_weight2, 
(ROUND(tt.bruto_weight,10) - (SELECT COALESCE(SUM(t.`bruto_weight`),0) FROM TRANSACTION t WHERE t.t_timbangan = tt.transaction_id AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL)) AS bruto_weight2, 
(ROUND(tt.tarra_weight,10) - (SELECT COALESCE(SUM(t.`tarra_weight`),0) FROM TRANSACTION t WHERE t.t_timbangan = tt.transaction_id AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL)) AS tarra_weight2, 
(ROUND(tt.netto_weight,10) - (SELECT COALESCE(SUM(t.`netto_weight`),0) FROM TRANSACTION t WHERE t.t_timbangan = tt.transaction_id AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL)) AS netto_weight2,
(COALESCE(((SELECT COALESCE(SUM(sc.quantity),0) FROM stockpile_contract sc WHERE sc.contract_id = c.contract_id) - (SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id)) -
(SELECT COALESCE(SUM(t.send_weight), 0) FROM TRANSACTION t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.`stockpile_contract_id`
WHERE sc.`contract_id` = c.`contract_id`), 0) / (ROUND(tt.send_weight,0) - 
(SELECT COALESCE(SUM(t.`send_weight`),0) FROM TRANSACTION t WHERE t.t_timbangan = tt.transaction_id AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL))) AS persen,
(SELECT COUNT(t.t_timbangan) FROM TRANSACTION t WHERE t.t_timbangan = tt.transaction_id AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL) AS notim
FROM transaction_timbangan tt
LEFT JOIN vendor v ON v.`vendor_id` = tt.`vendor_id`
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = tt.`stockpile_contract_id`
LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
WHERE tt.transaction_id = {$slipId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();

        if ($row->pecah_slip == 1 && $row->notim == 0) {

            $persentase = $row->persen;
            $send_weight = $row->quantity_available;
            $bruto_weight = $row->bruto_weight2 * $persentase;
            $tarra_weight = $row->tarra_weight2 * $persentase;
            $netto_weight = $row->netto_weight2 * $persentase;

        } else {
            $send_weight = $row->send_weight2;
            $bruto_weight = $row->bruto_weight2;
            $tarra_weight = $row->tarra_weight2;
            $netto_weight = $row->netto_weight2;
        }
        $returnValue = $row->stockpile_contract_id . '||' . $row->po_no . '||' . $row->contract_no . '||' . $row->vehicle_no . '||' . $row->driver . '||' . $send_weight . '||' . $bruto_weight . '||' . $tarra_weight . '||' . $row->quantity_available . '||' . $netto_weight . '||' . $row->vendor_id . '||' . $row->vendor_name . '||' . $row->loadingDate . '||' . $row->unloading_cost_id . '||' . $row->freight_cost_id . '||' . $row->labor_id . '||' . $row->handling_cost_id . '||' . $row->counter . '||' . $row->pecah_slip . '||' . $row->persen;

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getSuratTugas($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    /*  $sql = "SELECT st.`idsurattugas`, st.`no_surattugas`
FROM transaction_upload tu
LEFT JOIN surattugas st ON tu.`no_do` = st.`no_surattugas`
LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = st.`stockpile_contract_id`
WHERE sc.stockpile_id = {$stockpileId} AND status_srtgs = 0
            ORDER BY st.`no_surattugas` DESC";*/
    /*$sql = "SELECT
CASE WHEN tu.no_do != '' THEN (SELECT idsurattugas FROM surattugas WHERE no_surattugas = tu.no_do AND status_srtgs = 0)
ELSE SUBSTRING(tu.no_slip,1,6) END AS idsurattugas,
CASE WHEN tu.no_do != '' THEN (SELECT no_surattugas FROM surattugas WHERE no_surattugas = tu.no_do AND status_srtgs = 0)
ELSE tu.no_slip END AS no_surattugas
FROM transaction_upload tu
WHERE tu.timbang1 > tu.timbang2 AND tu.stockpile_id = {$stockpileId}
ORDER BY tu.tgl_masuk ASC";*/

    $sql = "SELECT tt.`transaction_id`, 
			CASE WHEN tt.pecah_slip = 1 THEN CONCAT(tt.slip,' (PECAH SLIP)') ELSE tt.`slip` END AS slip  FROM transaction_timbangan tt

			WHERE tt.stockpile_id = {$stockpileId}
			AND tt.notim_status = 0
			ORDER BY tt.slip ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->transaction_id . '||' . $row->slip;
            } else {
                $returnValue = $returnValue . '{}' . $row->transaction_id . '||' . $row->slip;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = $sql;
    }

    echo $returnValue;
}

function getDBsum($requestDate, $intervalmonth, $userId)
{
    global $myDatabase;
    $returnValue = '';
    /*
	$sql = "SELECT no_po,a.account_name,pd.keterangan,pd.qty,pd.harga,pd.amount,idpo_detail
			FROM provident_inventory.po_detail pd
			left join account a on a.account_id = pd.account_id
			where no_po = '{$generatedPONo}' AND pd.entry_by = {$_SESSION['userId']} ORDER BY idpo_detail ASC";
	*/
    //$connection = mysqli_connect($myDatabase);
    //$result = mysqli_query($connection, "CALL `SP_BalanceQtyStockpile`(STR_TO_DATE('$requestDate','%d/%m/%Y'),$intervalmonth)");
    $sql = "CALL SP_BalanceQtyStockpile(STR_TO_DATE('$requestDate','%d/%m/%Y'),$intervalmonth,$userId)";


    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //$result=mysqli_query($myDatabase,$sql);
    //echo $myDatabase;
    //echo $sql;

    $returnValue = '<div class="span12 lightblue">';
    //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
    $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
    $returnValue .= '<thead>';
    $returnValue .= '<tr><th>Stockpile</th><th>Balance Last Month</th><th>Last Incoming</th><th>Qty IN</th><th>Last Loading</th><th>Qty Out</th><th>Qty Shrink</th><th>Balance</th><th>AVG Incoming per day</th><th>AVG Truck per day</th></tr></thead>';
    $returnValue .= '<tbody>';
    $totalqtyin = 0;
    $totalqtyout = 0;
    $totalqtyshrink = 0;
    $totalbalanceqty = 0;
    $totalavgtrxincoming = 0;
    $totalavgtruck = 0;
    $totalavgtrxloading = 0;
    $totalloadingday = 0;
    //while($row = $result->fetch_assoc())
    while ($row = $result->fetch_object()) {
        $totalqtyin = $totalqtyin + $row->QtyIN;
        $totalqtyout = $totalqtyout + $row->QtyOUT;
        $totalqtyshrink = $totalqtyshrink + $row->QtyShrink;
        $totalbalanceqty = $totalbalanceqty + $row->BalanceQty;
        $totalavgtrxincoming = $totalavgtrxincoming + $row->AVGTrxIncomingPerDay;
        $totalavgtruck = $totalavgtruck + $row->AVGTruckIncomingPerDay;
        $totalavgtrxloading = $totalavgtrxloading + $row->AVGTrxLoading;
        $totalloadingday = $totalloadingday + $row->loadingProses;
        $totalbalancelm = $totalbalancelm + $row->BalanceLastmonth;
        $dateas = $row->AsoffDate;
        $dateas2 = date_create($dateas);
        $returnValue .= '<tr>';
        $returnValue .= '<td style="width: 8%;">' . $row->Stockpile . '</td>';
        $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->BalanceLastmonth, 2, ".", ",") . '</td>';
        $returnValue .= '<td style="width: 8%;">' . $row->MaxDateIncoming . '</td>';
        $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->QtyIN, 2, ".", ",") . '</td>';
        $returnValue .= '<td style="width: 8%;">' . $row->MaxDateLoading . '</td>';
        $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->QtyOUT, 2, ".", ",") . '</td>';
        $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->QtyShrink, 2, ".", ",") . '</td>';
        $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($row->BalanceQty, 2, ".", ",") . '</strong></td>';
        $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->AVGTrxIncomingPerDay, 2, ".", ",") . '</td>';
        $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->AVGTruckIncomingPerDay, 0, ".", ",") . '</td>';

    }


    $returnValue .= '</tbody>';
    $returnValue .= '<tfoot>';
    $returnValue .= '<tr>';
    $returnValue .= '<td colspan="1"><strong>' . Total . '</strong></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalbalancelm, 2, ".", ",") . '</strong></td>';
    $returnValue .= '<td style="width: 8%;"></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalqtyin, 2, ".", ",") . '</strong></td>';
    $returnValue .= '<td style="width: 8%;"></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalqtyout, 2, ".", ",") . '</strong></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalqtyshrink, 2, ".", ",") . '</strong></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalbalanceqty, 2, ".", ",") . '</strong></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalavgtrxincoming, 2, ".", ",") . '</strong></td>';
    $returnValue .= '<td style="text-align: right; width: 8%;"><strong>' . number_format($totalavgtruck, 0, ".", ",") . '</strong></td>';
    $returnValue .= '</tfoot>';
    $returnValue .= '</table>';
    $returnValue .= '<div class="row-fluid" style="margin-bottom: 7px;">   
							<strong>Note:</strong></div>
							<div class="row-fluid" style="margin-bottom: 7px;">   
							<div class="span6 lightblue">
							<div><font size="2">- <strong>Balance Last Month</strong> = Balance stock as of ' . date_format($dateas2, 'd/m/Y') . '</div>
							<div>- <strong>Last Incoming</strong> = Last Date incoming <i>cangkang</i> as of ' . $requestDate . '</div>	
							<div>- <strong>Qty In</strong> = Incoming <i>cangkang</i> between <strong>Balance last month</strong> to ' . $requestDate . '</div>	
							<div>- <strong>Last Loading</strong> = Last date loading as of ' . $requestDate . '</div>			
							<div>- <strong>Qty Out</strong> = Loading amount between <strong>Balance last month</strong> to ' . $requestDate . '</div>			
							</div>
							<div class="span6 lightblue">
							<div>- <strong>Qty Shrink</strong> = Stock shrink between <strong>Balance last month</strong> to ' . $requestDate . '</div>
							<div>- <strong>Balance</strong> = Stock as ' . $requestDate . '</div>	
							<div>- <strong>AVG Incoming per day</strong> = Average incoming <i>cangkang</i> per day for the last ' . $intervalmonth . ' month</div>	
							<div>- <strong>AVG Truck per day</strong> =  Average truck incoming per day for the last ' . $intervalmonth . ' month</font></div>			
							</div>
							</div>';

    echo $returnValue;
}

function getPONo($currentYearMonth)
{
    global $myDatabase;

    /*$sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($resultVendor !== false && $resultVendor->num_rows == 1) {
        $rowVendor = $resultVendor->fetch_object();
        $vendorCode = $rowVendor->vendor_code;
    }*/
    $sql2 = "SELECT stockpile_id FROM USER WHERE USER_ID = {$_SESSION['userId']}";
    $resultsql2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
    if ($resultsql2->num_rows == 1) {
        $rowsql2 = $resultsql2->fetch_object();
        $stockpileid = $rowsql2->stockpile_id;
    }

    $sql3 = "SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpileid}";
    $resultsql3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
    if ($resultsql3->num_rows == 1) {
        $rowsql3 = $resultsql3->fetch_object();
        $stockpilename = $rowsql3->stockpile_code;
    }


    $checkPONo = 'PO-' . $stockpilename . '/' . $currentYearMonth;
    /* if($contractSeq != "") {
        $poNo = $checkInvoiceNo .'/'. $contractSeq;
    } else {*/
    $sql = "SELECT no_po FROM po_hdr WHERE no_po LIKE '{$checkPONo}%' ORDER BY idpo_hdr DESC LIMIT 1";
    $resultPO = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultPO->num_rows == 1) {
        $rowPO = $resultPO->fetch_object();
        $splitPONo = explode('/', $rowPO->no_po);
        $lastExplode = count($splitPONo) - 1;
        $nextPONo = ((float)$splitPONo[$lastExplode]) + 1;
        $PO_number = $checkPONo . '/' . $nextPONo;
    } else {
        $PO_number = $checkPONo . '/1';

    }

    echo $PO_number;
}

function getPengajuanNo($currentYearMonth)
{
    global $myDatabase;

    /*$sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($resultVendor !== false && $resultVendor->num_rows == 1) {
        $rowVendor = $resultVendor->fetch_object();
        $vendorCode = $rowVendor->vendor_code;
    }*/

    $checkInvoiceNo = 'PGJ/JPJ/' . $currentYearMonth;
    /* if($contractSeq != "") {
        $poNo = $checkInvoiceNo .'/'. $contractSeq;
    } else {*/
    $sql = "SELECT pengajuan_no FROM pengajuan_general WHERE company_id = {$_SESSION['companyId']} 
            AND pengajuan_no LIKE '{$checkInvoiceNo}%' 
            ORDER BY pengajuan_general_id DESC LIMIT 1";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    // echo " NO " . $sql;
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        // $splitInvoiceNo = explode('/', $rowInvoice->invoice_no);
        $splitInvoiceNo = explode('/', $rowInvoice->pengajuan_no);
        $lastExplode = count($splitInvoiceNo) - 1;
        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
        $pengajuanNo = $checkInvoiceNo . '/' . $nextInvoiceNo;
    } else {
        $pengajuanNo = $checkInvoiceNo . '/1';
    }

    echo $pengajuanNo;
}

function setPODetail($poID, $idPOHDR, $gvid, $currentDate)
{
    global $myDatabase;
    $returnValue = '';
   
    /*
	$sql = "SELECT no_po,a.account_name,pd.keterangan,pd.qty,pd.harga,pd.amount,idpo_detail
			FROM provident_inventory.po_detail pd
			left join account a on a.account_id = pd.account_id
			where no_po = '{$currentDate}' AND pd.entry_by = {$_SESSION['userId']} ORDER BY idpo_detail ASC";
	*/

    if($idPOHDR != ''){
        $sql = "SELECT no_po,a.account_name,i.item_name,pd.qty,pd.harga,pd.termin, pd.amount,pd.notes,
        (CASE WHEN pd.pphstatus = 1 THEN pd.pph ELSE 0 END) AS pph,
        (CASE WHEN pd.ppnstatus = 1 THEN pd.ppn ELSE 0 END) AS ppn,
        (pd.amount+(CASE WHEN pd.ppnstatus = 1 THEN pd.ppn ELSE 0 END)-(CASE WHEN pd.pphstatus = 1 THEN pd.pph ELSE 0 END)) AS grandtotal,
            idpo_detail,s.`stockpile_name`, sh.`shipment_no`, CONCAT(i.item_name,' - ',g.group_name,' - ',u.uom_type) as item_detail
                FROM po_detail pd
                LEFT JOIN master_item i ON i.idmaster_item = pd.item_id
                LEFT JOIN master_groupitem g ON g.idmaster_groupitem = i.group_itemid
                LEFT JOIN account a ON a.account_id = g.account_id
                LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
                LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
                LEFT JOIN uom u ON u.idUOM = i.uom_id
                WHERE pd.no_po = '{$poID}' ORDER BY idpo_detail ASC";
    }else{
        $sql = "SELECT no_po,a.account_name,i.item_name,pd.qty,pd.harga, pd.termin, pd.amount,pd.notes,
        (CASE WHEN pd.pphstatus = 1 THEN pd.pph ELSE 0 END) AS pph,
        (CASE WHEN pd.ppnstatus = 1 THEN pd.ppn ELSE 0 END) AS ppn,
        (pd.amount+(CASE WHEN pd.ppnstatus = 1 THEN pd.ppn ELSE 0 END)-(CASE WHEN pd.pphstatus = 1 THEN pd.pph ELSE 0 END)) AS grandtotal,
            idpo_detail,s.`stockpile_name`, sh.`shipment_no`, CONCAT(i.item_name,' - ',g.group_name,' - ',u.uom_type) as item_detail
                FROM po_detail pd
                LEFT JOIN master_item i ON i.idmaster_item = pd.item_id
                LEFT JOIN master_groupitem g ON g.idmaster_groupitem = i.group_itemid
                LEFT JOIN account a ON a.account_id = g.account_id
                LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
                LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
                LEFT JOIN uom u ON u.idUOM = i.uom_id
                WHERE po_hdr_id IS NULL AND DATE_FORMAT(pd.entry_date,'%Y-%m-%d') = '{$currentDate}'
                 AND pd.entry_by = {$_SESSION['userId']} 
                 AND pd.general_vendor_id = {$gvid} ORDER BY idpo_detail ASC";
    
    }

   // echo " PO " . $sql;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "PODetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th>Shipment Code</th>
                                <th>Stockpile</th>
                                <th>Item Detail</th>
                                <th>Notes</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Termin</th>
                                <th>DPP</th>
                                <th>PPN</th>
                                <th>PPH</th>
                                <th>Down Payment</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';


        $totalPrice = 0;
        $totalpph = 0;
        $totalppn = 0;
        $totalDownPayment = 0;
        $totalall = 0;
        while ($row = $result->fetch_object()) {

            $downPayment = 0;
            // $totalPrice = 0;
            // $totalpph = 0;
            // $totalppn = 0;
            // $totalDownPayment = 0;
            // $totalall = 0;

            $sqlDP = "SELECT SUM((idp.amount_payment + idp.ppn_value) - idp.pph_value) AS down_payment,
                       idp.ppn_value AS ppn, 
                       idp.pph_value AS pph 
                        FROM invoice_dp idp 
                        WHERE idp.status = 0 AND idp.po_detail_id_dp = {$row->idpo_detail}";
            $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
            if ($resultDP !== false && $resultDP->num_rows > 0) {

                $rowDP = $resultDP->fetch_object();

                if ($rowDP->ppn == 0) {
                    $dp_ppn = 0;
                } else {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    $dp_ppn = $rowDP->ppn;
                }

                if ($rowDP->pph == 0) {
                    $dp_pph = 0;
                } else {
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $dp_pph = $rowDP->pph;
                }


                if ($rowDP->down_payment != 0) {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $downPayment = $rowDP->down_payment;
                    // echo " AKA " . $downPayment;
                } else {
                    $downPayment = 0;
                }
            }

            $grandTotal = $row->grandtotal - $downPayment;
            $tamount = $row->amount;
            $tpph = $row->pph;
            $tppn = $row->ppn;
            $tgtotal = $grandTotal;
            $totalPrice = $totalPrice + $tamount;
            $totalpph = $totalpph + $tpph;
            $totalppn = $totalppn + $tppn;
            $totalDownPayment = $totalDownPayment + $downPayment;
            $totalall = $totalall + $tgtotal;
            
            $returnValue .= '<tr>';
            /* if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->invoice_detail_id);

                  if($pos === false) {
                  $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';

					$dppPrice = $dppPrice + $dppTotalPrice;
					$totalPrice = $totalPrice + $total;
					$total_ppn = $total_ppn + $totalPPN;
					$total_pph = $total_pph + $totalPPh;

                }
            } else {
               		$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
            }
			*/


            $returnValue .= '<td style="width: 8%;">' . $row->shipment_no . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->item_detail . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->qty, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->harga, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->termin  . '%</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->amount, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->ppn, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->pph, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($downPayment, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($grandTotal, 4, ".", ",") . '</td>';

            $returnValue .= '<td style="text-align: Left; width: 10%;">
                                <a href="#" id="edit|PO|' . $row->idpo_detail . '" role="button" title="Edit" 
                                onclick="EditPODetail(' . $row->idpo_detail . ');">
                                <img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                                || 
                                <a href="#" id="delete|PO|' . $row->idpo_detail . '" role="button" title="Delete" 
                                    onclick="deletePODetail(' . $row->idpo_detail . ');">
                                <img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                            </td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';


        $grandTotal = $totalPrice;

        $returnValue .= '<tfoot>';
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($grandTotal, 4, ".", ",") . '</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($totalppn, 4, ".", ",") . '</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($totalpph, 4, ".", ",") . '</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($totalDownPayment, 4, ".", ",") . '</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($totalall, 4, ".", ",") . '</td>';
        $returnValue .= '<td></td>';
        $returnValue .= '</tr>';
        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalpph" name="totalpph" value="' . round($totalpph, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totalppn" name="totalppn" value="' . round($totalppn, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totalall" name="totalall" value="' . round($totalall, 2) . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        //$returnValue .= '</div>';
    }
// $returnValue = $sql;
    echo $returnValue;
}

function getItemNo($groupitemId)
{
    global $myDatabase;


    $sql2 = "SELECT gi.idmaster_groupitem,  a.account_no,a.account_name
				FROM master_groupitem gi
				left join account a on a.account_id = gi.account_id
				WHERE idmaster_groupitem = {$groupitemId}";
    $resultsql2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
    if ($resultsql2->num_rows == 1) {
        $rowsql2 = $resultsql2->fetch_object();
        $accountNo = $rowsql2->account_no;
    }

    $checkInvoiceNo = $accountNo;

    $sql = "SELECT item_code FROM master_item WHERE item_code LIKE '{$checkInvoiceNo}%' ORDER BY idmaster_item DESC LIMIT 1";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        $splitInvoiceNo = explode('/', $rowInvoice->item_code);
        $lastExplode = count($splitInvoiceNo) - 1;
        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
        $InvoiceNo = $checkInvoiceNo . '/' . $nextInvoiceNo;
    } else {
        $InvoiceNo = $checkInvoiceNo . '/1';

    }

    echo $InvoiceNo;

    //echo $groupitemId;
}

function getItem($groupItemId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT idmaster_item , CONCAT(item_name, ' ', item_code) AS item_full 
            FROM master_item
			where group_itemid = {$groupItemId}
            ORDER BY item_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->idmaster_item . '||' . $row->item_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->idmaster_item . '||' . $row->item_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getFreightReport($stockpileId)
{
    global $myDatabase;
    $returnValue = '';
    /* for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }*/
    $sql = "SELECT DISTINCT(fc.freight_id), CONCAT(f.freight_code,' - ', f.freight_supplier) AS freight_supplier
            FROM freight_cost fc
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            WHERE fc.stockpile_id IN ({$stockpileId})
            AND fc.company_id = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY f.freight_supplier ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->freight_id . '||' . $row->freight_supplier;
            } else {
                $returnValue = $returnValue . '{}' . $row->freight_id . '||' . $row->freight_supplier;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getContractAdjustmentDetail($contractAdjustmentDetail)
{
    global $myDatabase;
    $returnValue = '';

    if ($contractAdjustmentDetail != 0) {
        $sql = "SELECT contract_no, COALESCE(quantity,2) AS quantity
            FROM contract
            WHERE contract_id = {$contractAdjustmentDetail}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    }
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $contractNo = $row->contract_no;
        $quantity = $row->quantity;


        $returnValue = '|' . $contractNo . '|' . number_format($quantity, 0, ".", ",") . '|';

    } else {
        $returnValue = '|-|-|';
    }

    echo $returnValue;
}

function setSlipHandling($stockpileId, $vendorHandlingId, $contractHandling, $checkedSlips, $checkedSlipsDP, $ppn, $pph, $paymentFromHP, $paymentToHP) {
    global $myDatabase;
    $returnValue = '';
	
	if($checkedSlips == ''){
	for ($i = 0; $i < sizeof($contractHandling); $i++) {
                        if($contractHandlings == '') {
                            $contractHandlings .=  $contractHandling[$i];
                        } else {
                            $contractHandlings .= ','. $contractHandling[$i];
                        }
                    }
	}else{
		$contractHandlings = $contractHandling;
	}

    $sql = "SELECT t.*, sc.stockpile_id, vhc.vendor_handling_id, con.po_no, vh.vendor_handling_rule,
                vh.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code
            FROM TRANSACTION t
            INNER JOIN vendor_handling_cost vhc
                ON vhc.handling_cost_id = t.handling_cost_id
            INNER JOIN vendor_handling vh
                ON vh.vendor_handling_id = vhc.vendor_handling_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = vh.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = vh.pph_tax_id
			INNER JOIN vendor v
				ON vhc.vendor_id = v.vendor_id
			INNER JOIN stockpile_contract sc
		        ON sc.stockpile_contract_id = t.stockpile_contract_id
			LEFT JOIN contract con
				ON con.contract_id = sc.contract_id
            WHERE vhc.vendor_handling_id = {$vendorHandlingId}
			AND sc.stockpile_id = {$stockpileId}
			AND sc.contract_id IN ({$contractHandlings})
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.hc_payment_id IS NULL AND vhc.price != 0 AND t.handling_quantity != 0
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromHP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToHP', '%d/%m/%Y')
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
		$returnValue .= '<form id = "frm10">';
		//$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAllHandling(this)" name="checkedSlips[]" /></th><th>Slip No.</th><th>Transaction Date</th><th>PO No.</th><th>Vendor Code</th><th>Quantity</th><th>Currency</th><th>Handling Cost / kg</th><th>Total</th><th>DPP</th></tr></thead>';
        $returnValue .= '<tbody>';
        
        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
			
				$hp = $row->handling_price * $row->handling_quantity;
				$hq = $row->handling_quantity;
			
            if($row->transaction_date >= '2015-10-05'&& $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
			    $dppTotalPrice = $hp;
			  } else{ 
			  if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $hp;
            }else {
					if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  && $row->stockpile_id == 1){
					$dppTotalPrice = ($hp) / ((100 - $row->pph_tax_value) / 100);
			} else {
                if($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($hp) / ((100 - $row->pph_tax_value) / 100);

				}else {
                  $dppTotalPrice = $hp;
               }
            }
		
		}
	}
            $handlingPrice = 0;
            if($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
				$handlingPrice = $hp;
			}else{
				$handlingPrice = $hp;
			}
            
//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//            
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }
            
            $returnValue .= '<tr>';
            if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);
               // echo $row->transaction_id;
                if($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipHandling('. $stockpileId .', '. $row->vendor_handling_id .', '. $contractHandlings .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipHandling('. $stockpileId .', '. $row->vendor_handling_id .', '. $contractHandlings .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\');" checked /></td>';
                    $totalPrice = $totalPrice + $dppTotalPrice;
					$totalPrice2 = $totalPrice2 + $dppTotalPrice;
                    
                    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
					$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipHandling('. $stockpileId .', '. $row->vendor_handling_id .', '. $contractHandlings .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\');" /></td>';
            }
            $returnValue .= '<td style="width: 15%;">'. $row->slip_no .'</td>';
			$returnValue .= '<td style="width: 10%;">'. $row->transaction_date .'</td>';
			$returnValue .= '<td style="width: 15%;">'. $row->po_no .'</td>';
			$returnValue .= '<td style="width: 10%;">'. $row->vendor_code .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($hq, 0, ".", ",") .'</td>';
            $returnValue .= '<td style="width: 5%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($row->handling_price, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($handlingPrice, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 25%;">'. number_format($dppTotalPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
        }
        
        $returnValue .= '</tbody>';
        if($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
            
            /*$sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, vh.pph, c.`po_no`, c.`contract_no` ,p.payment_no 
FROM payment p LEFT JOIN vendor_handling vh ON p.`vendor_handling_id` = vh.`vendor_handling_id` 
LEFT JOIN contract c ON c.`contract_id` = p.`handlingContract` 
WHERE p.vendor_handling_id = {$vendorHandlingId} AND p.handlingContract = {$contractHandling} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            $downPayment = 0;
            if($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
				$dp = $row->down_payment * ((100 - $row->pph)/100);
                $downPayment = $dp;
                
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="7" style="text-align: right;">Down Payment</td>';
				//$returnValue .= '<td  style="text-align: right;">'. $row->payment_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';
            }*/
			
			$sql = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`, p.ppn_amount ,p.pph_amount, vh.pph, c.`po_no`, c.`contract_no`,vh.ppn 
FROM payment p LEFT JOIN vendor_handling vh ON p.`vendor_handling_id` = vh.`vendor_handling_id`
LEFT JOIN contract c ON c.`contract_id` = p.`handlingContract` 
WHERE p.vendor_handling_id = {$vendorHandlingId} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            $downPayment = 0;
			
			if ($result->num_rows > 0) {
			while($row = $result->fetch_object()) {
				
			//$dp = $row->amount_converted * ((100 - $row->pph)/100);
			$dpHC = $row->amount_converted;
			$dpPPN = $row->amount_converted * ($row->ppn/100);
			$dpPPh = $row->amount_converted * ($row->pph/100);
			$dp = ($row->amount_converted + $dpPPN) - $dpPPh ;
            //$downPayment = $dp;
			
				$returnValue .= '<tr>';
                $returnValue .= '<td colspan="5" style="text-align: right;">Down Payment</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->payment_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
				
				if($checkedSlipsDP != '') {
                $posDP = strpos($checkedSlipsDP, $row->payment_id);
                //echo $row->payment_id;
                if($posDP === false) {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipHandlingDP('. $stockpileId .', '. $vendorHandlingId .', '. $contractHandlings .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\');" /></td>';
                } else {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipHandlingDP('. $stockpileId .', '. $vendorHandlingId .', '. $contractHandlings .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\');" checked /></td>';
                    $downPayment = $downPayment + $dp; 
					
					$downPaymentHC = $downPaymentHC + $dpHC; 
                    
           
                }
            } else {
					$returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipHandlingDP('. $stockpileId .', '. $vendorHandlingId .', '. $contractHandlings .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\');" /></td>';
            }
                $returnValue .= '<td style="text-align: right;">'. number_format($dp, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';
            
        }
    }
            
            
//            $ppnDB = 0;
//            $pphDB = 0;
//            $sql = "SELECT freight_id, ppn, pph FROM freight WHERE freight_id = {$freightId}";
//            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($result->num_rows == 1) {
//                $row = $result->fetch_object();
//                $ppnDB = $row->ppn;
//                $pphDB = $row->pph;
//            }
//            
//            $ppnDBAmount = ($ppnDB/100) * $totalPrice;
//            $pphDBAmount = ($pphDB/100) * $totalPrice;
            
            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;
            
            if($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }
            
//            $totalPpn = ($ppnValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" readOnly style="text-align: right;" class="span12" name="ppn" id="ppn" value="'. number_format($totalPpn, 2, ".", ",") .'";" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9"  style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" readOnly style="text-align: right;" class="span12" name="pph" id="pph" value="'. number_format($totalPph, 2, ".", ",") .'";" /></td>';
            $returnValue .= '</tr>';
            
            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
			$grandTotal1 = $totalPrice2 - $downPaymentHC;
			//$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


            $totalPrice = ($totalPrice + $totalPpn) - $totalPph;
			$totalPrice2 = $totalPrice2;

            if($grandTotal < 0 && $downPayment > 0) {
                $grandTotal = 0;
				$downPaymentHC = $totalPrice2;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
            
            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
	    $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="'. round($totalPrice, 2) .'" />';
		$returnValue .= '<input type="hidden" id="totalPrice2" name="totalPrice2" value="'. round($totalPrice2, 2) .'" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="'. $downPayment .'" />';
		$returnValue .= '<input type="hidden" id="downPaymentHC" name="downPaymentHC" value="'. $downPaymentHC .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }
    
    echo $returnValue;
}

function getHandlingPayment($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT DISTINCT(vhc.vendor_handling_id), CONCAT(vh.vendor_handling_code,' - ', vh.vendor_handling_name) AS vendor_handling
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
            ORDER BY vendor_handling ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_handling_id . '||' . $row->vendor_handling;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_handling_id . '||' . $row->vendor_handling;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getHandlingDetail($handlingCostId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT CONCAT(cur.currency_code, ' ', FORMAT(vhc.price, 2)) AS price
            FROM vendor_handling_cost vhc
            INNER JOIN currency cur
                ON cur.currency_id = vhc.currency_id
            WHERE vhc.handling_cost_id = {$handlingCostId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->price;
    }

    echo $returnValue;
}

function getHandlingCost($stockpileId, $vendorId, $currentDate, $handlingCostId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = "";

    if ($handlingCostId == '') {
        $whereProperty = " AND vendor_id = {$vendorId} ";
    } else {
        $whereProperty = " AND handling_cost_id = {$handlingCostId} ";
    }

    $sql = "SELECT DISTINCT(vh.vendor_handling_id) AS vendor_handling_id, CONCAT(vh.vendor_handling_name, '-', v.vendor_code, '-', vh.vendor_handling_code) AS vendor_handling_code, 
                (SELECT handling_cost_id FROM vendor_handling_cost 
                    WHERE vendor_handling_id = vh.vendor_handling_id
					AND stockpile_id = {$stockpileId}
                    {$whereProperty}
                    ORDER BY entry_date DESC LIMIT 1
                ) AS handling_cost_id
            FROM vendor_handling vh
            INNER JOIN vendor_handling_cost vhc
                ON vhc.vendor_handling_id = vh.vendor_handling_id
            INNER JOIN vendor v
                ON v.vendor_id = vhc.vendor_id
            WHERE vhc.stockpile_id = {$stockpileId}
            AND vhc.vendor_id = {$vendorId}
            AND vhc.company_id = {$_SESSION['companyId']}
			AND vh.active = 1
            ORDER BY vh.vendor_handling_code ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->handling_cost_id . '||' . $row->vendor_handling_code;
            } else {
                $returnValue = $returnValue . '{}' . $row->handling_cost_id . '||' . $row->vendor_handling_code;
            }
        }
    }


    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getVendorFreight($stockpileId, $freightId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT DISTINCT(v.vendor_id), CONCAT(v.vendor_code,' - ', v.vendor_name) AS vendor_freight
            FROM vendor v
            LEFT JOIN freight_cost fc
                ON v.vendor_id = fc.vendor_id
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            WHERE fc.stockpile_id = {$stockpileId}
			AND fc.freight_id = {$freightId}
            AND fc.company_id = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY vendor_freight ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_freight;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_freight;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getBankDetail($bankVendor, $paymentFor)
{
    global $myDatabase;
    $returnValue = '';

    if ($paymentFor == 0 || $paymentFor == 1) { //PKS / Curah
        $sql = "SELECT *
            FROM vendor
            WHERE vendor_id = {$bankVendor}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 2) { //Freight
        $sql = "SELECT *
            FROM freight
            WHERE freight_id = {$bankVendor}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 3) { //Unloading
        $sql = "SELECT *
            FROM labor
            WHERE labor_id = {$bankVendor}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } elseif ($paymentFor == 6 || $paymentFor == 8) { //HO / Invoice
        $sql = "SELECT *
            FROM general_vendor
            WHERE general_vendor_id = {$bankVendor}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $beneficiary = $row->beneficiary;
        $bank = $row->bank_name;
        $rek = $row->account_no;
        $swift = $row->swift_code;


        $returnValue = '|' . $beneficiary . '|' . $bank . '|' . $rek . '|' . $swift;

    } else {
        $returnValue = '|-|-|-|-';
    }

    echo $returnValue;
}

function getDestination($periode, $stockpile_id)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sl.sales_id, CONCAT(sl.`destination`, ' - ',
			CASE WHEN sl.sales_id IS NOT NULL THEN (SELECT sh.shipment_no FROM shipment sh WHERE sh.sales_id = sl.sales_id ORDER BY sh.shipment_no DESC LIMIT 1)
			ELSE '' END) AS destination
			FROM sales sl             
			WHERE DATE_FORMAT(sl.shipment_date, '%m/%Y') = '{$periode}' AND sl.stockpile_id = {$stockpile_id}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->sales_id . '||' . $row->destination;
            } else {
                $returnValue = $returnValue . '{}' . $row->sales_id . '||' . $row->destination;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';

    }

    echo $returnValue;

}

function setAmountPayment()
{
    global $myDatabase;
    //$htmlValue = '';
    //$returnValue = '';
    $htmlValue = "|2|";
    $htmlValue = $htmlValue . "<input type='text' class='span12' tabindex='' id='amount' name='amount' value=''>";


    echo $htmlValue;
}

function getBankPC($paymentMethod)
{
    global $myDatabase;
    $returnValue = '';
    //$whereProperty = '';
    $joinProperty = '';
    $sId = 0;

    $whereProperty = " AND b.stockpile_id IN (SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']}) ";

    $sql = "SELECT  stockpile_id
            FROM user_stockpile   
            WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            //$stockpileId = $row->stockpile_id;

            if ($row->stockpile_id == 10) {
                //$joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
                $whereProperty = " AND b.bank_type = 2 AND b.stockpile_id = 10 ";
            }//else {
            //$joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";

            //}
        }
    }


    $sql = "SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full 
                    FROM bank b
                    INNER JOIN currency cur
                        ON cur.currency_id = b.currency_id
					WHERE 1=1 {$whereProperty}
                    ORDER BY b.bank_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->bank_id . '||' . $row->bank_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->bank_id . '||' . $row->bank_full;
            }
        }
    } else {
        echo 'WRONG';
        echo $sql;
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function setPaymentDetail()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pc.payment_cash_id,
CASE WHEN pc.type = 4 THEN 'Loading'
WHEN pc.type = 5 THEN 'Umum'
WHEN pc.type = 6 THEN 'HO' ELSE '' END AS TYPE, a.account_name, sh.shipment_no, t.slip_no, s.stockpile_name, pc.notes, 
pc.qty, pc.price, pc.termin, pc.amount, pc.ppn, pc.pph, pc.tamount, gv.general_vendor_name, gv.pph AS gv_pph, gv.ppn AS gv_ppn,
CASE WHEN idUOM IS NOT NULL THEN (SELECT uom_type FROM uom WHERE idUOM = pc.idUOM) ELSE '-' END AS uom
FROM payment_cash pc LEFT JOIN account a ON pc.account_id = a.account_id
LEFT JOIN shipment sh ON pc.shipment_id = sh.shipment_id
LEFT JOIN stockpile s ON pc.stockpile_remark = s.stockpile_id
LEFT JOIN general_vendor gv ON pc.general_vendor_id = gv.general_vendor_id
LEFT JOIN `transaction` t ON t.transaction_id = pc.transaction_id
WHERE pc.payment_id IS NULL AND pc.entry_by = {$_SESSION['userId']} AND payment_cash_status = 0 ORDER BY pc.payment_cash_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "paymentDetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Type</th><th>Account</th><th>Vendor</th><th>Shipment Code</th><th>Slip No</th><th>Remark (SP)</th><th>Notes</th><th>Qty</th><th>Unit Price</th><th>Termin</th><th>Amount</th><th>PPN</th><th>PPh</th><th>Down Payment</th><th>Total Amount</th><th>Action</th></tr></thead>';
        $returnValue .= '<tbody>';


        while ($row = $result->fetch_object()) {


            $returnValue .= '<tr>';
            /* if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->invoice_detail_id);

                  if($pos === false) {
                  $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';

					$dppPrice = $dppPrice + $dppTotalPrice;
					$totalPrice = $totalPrice + $total;
					$total_ppn = $total_ppn + $totalPPN;
					$total_pph = $total_pph + $totalPPh;

                }
            } else {
               		$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
            }
			*/
            /*$sqlDP = "SELECT SUM(tamount_converted) AS down_payment FROM payment_cash WHERE payment_cash_dp = {$row->payment_cash_id}";
    		$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
			if($resultDP !== false && $resultDP->num_rows == 1) {
				 $rowDP = $resultDP->fetch_object();
			if($rowDP->down_payment != 0){
				 $downPayment = $rowDP->down_payment;
			}else{
				 $downPayment = 0;
			}
			}*/
            $sqlDP = "SELECT SUM(pcdp.amount_payment) AS down_payment, SUM(pc.ppn) AS ppn, SUM(pc.pph) AS pph FROM payment_cash_dp pcdp LEFT JOIN payment_cash pc ON pc.`payment_cash_id` = pcdp.`payment_cash_dp`
WHERE pcdp.payment_cash_id = {$row->payment_cash_id}";
            $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
            if ($resultDP !== false && $resultDP->num_rows == 1) {
                $rowDP = $resultDP->fetch_object();

                if ($rowDP->ppn == 0) {
                    $dp_ppn = 0;
                } else {
                    $dp_ppn = $rowDP->down_payment * ($row->gv_ppn / 100);
                }

                if ($rowDP->pph == 0) {
                    $dp_pph = 0;
                } else {
                    $dp_pph = $rowDP->down_payment * ($row->gv_pph / 100);
                }


                if ($rowDP->down_payment != 0) {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $downPayment = ($rowDP->down_payment + $dp_ppn) - $dp_pph;
                } else {
                    $downPayment = 0;
                }
            }
            $tamount1 = $row->amount + $row->ppn - $row->pph;
            $tamount = $tamount1 - $downPayment;
            $totalPrice = $totalPrice + $tamount;

            $returnValue .= '<td style="width: 8%;">' . $row->type . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->account_name . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->general_vendor_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->shipment_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->qty, 2, ".", ",") . ' ' . $row->uom . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->termin, 0, ".", ",") . '%</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->amount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->ppn, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->pph, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($downPayment, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($tamount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;"><a href="#" id="delete|paymentCash|' . $row->payment_cash_id . '" role="button" title="Delete" onclick="deletePaymentDetail(' . $row->payment_cash_id . ');"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';


        $amount = $totalPrice;

        $returnValue .= '<tfoot>';
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td colspan="2" style="text-align: right;">' . number_format($amount, 2, ".", ",") . '</td>';
        $returnValue .= '<td></td>';
        $returnValue .= '</tr>';
        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        // $returnValue .= '<input type="hidden" id="pph2" name="pph2" value="'. round($total_pph, 2) .'" />';
        // $returnValue .= '<input type="hidden" id="ppn2" name="ppn2" value="'. round($total_ppn, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        $returnValue .= '<input type="hidden" id="amount" name="amount" value="' . $amount . '" />';
        //$returnValue .= '</div>';
    }

    echo $returnValue;
}

function setCashDP($generalVendorId11, $ppn11, $pph11){
 global $myDatabase;
    $returnValue = '';
	
	$whereProperty = "";
	$sql2 = "SELECT  stockpile_id
            FROM user 
            WHERE user_id = {$_SESSION['userId']}";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
    if ($result2->num_rows > 0) {
        while ($row2 = $result2->fetch_object()) {
            $stockpileId = $row2->stockpile_id;

            if ($stockpileId != 10) {
                //$joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
                $whereProperty = " AND pc.stockpile_remark = {$stockpileId} ";
            }else {
           // $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
			$whereProperty = "";
            }
        }
    }


   

    $sql = "SELECT pc.*, p.payment_no,
(SELECT SUM(amount_payment) FROM payment_cash_dp WHERE payment_cash_dp = pc.payment_cash_id AND `status` = 0) AS total_dp
	FROM payment_cash pc
LEFT JOIN payment p ON p.`payment_id` = pc.`payment_id`
WHERE pc.general_vendor_id = {$generalVendorId11} AND pc.payment_cash_method = 2
AND pc.company_id = {$_SESSION['companyId']} AND (pc.`amount` - (SELECT COALESCE(SUM(amount_payment),0) FROM payment_cash_dp WHERE payment_cash_dp = pc.payment_cash_id AND `status` = 0)) > 0 AND p.payment_status = 0 
{$whereProperty}
ORDER BY pc.payment_cash_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "cashDP">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<h5>Down Payment  <span style="color: red;">(MASUKAN AMOUNT DPP & CENTANG DP)</span></h5>';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Payment No.</th><th>Notes</th><th>Amount</th><th>PPN</th><th>PPh</th><th>Available DP</th><th>Amount</th><th></th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $dp_ppn = 0;
        $dp_pph = 0;
        $count = 0;
        while ($row = $result->fetch_object()) {


            $dppTotalPrice = $row->amount;

            if ($row->ppn != 0 && $row->ppn != '') {
                $totalPPN = $row->ppn;
            }

            if ($row->pph != 0 && $row->pph != '') {
                $totalPPh = $row->pph;
            }
            if ($row->dp_ppn != 0 && $row->ppn != 0) {
                $dp_ppn = $row->total_dp * ($row->dp_ppn / 100);
            }

            if ($row->dp_pph != 0 && $row->pph != 0) {
                $dp_pph = $row->total_dp * ($row->dp_pph / 100);
            }
            $total = ($dppTotalPrice + $totalPPN) - $totalPPh;
            $total2 = ($row->total_dp + $dp_ppn) - $dp_pph;
            $total_dp = $total - $total2;

            $returnValue .= '<tr>';
            /*if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->payment_cash_id);

                if($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="pc" value="'. $row->payment_cash_id .'" onclick="checkSlipPC('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="pc" value="'. $row->payment_cash_id .'" onclick="checkSlipPC('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';

					$dppPrice = $dppPrice + $dppTotalPrice;
					$totalPrice = $totalPrice + $total;
					$total_ppn = $total_ppn + $totalPPN;
					$total_pph = $total_pph + $totalPPh;

                }
            } else {
               		$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="pc" value="'. $row->payment_cash_id .'" onclick="checkSlipPC('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
            }*/


            $returnValue .= '<td style="width: 20%;">' . $row->payment_no . '</td>';
            $returnValue .= '<td style="width: 40%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($total_dp, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;"><input type="text" id="paymentTotal" name="checkedSlips2[' . $count . ']" value="" /></td>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->payment_cash_id);

                if ($pos === false) {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="pc"  value="' . $row->payment_cash_id . '" /></td>';
                } else {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="pc"  value="' . $row->payment_cash_id . '" checked /></td>';

                    //$dppPrice = $dppPrice + $dppTotalPrice;
                    //$totalPrice = $totalPrice + $total;
                    //$total_ppn = $total_ppn + $totalPPN;
                    //$total_pph = $total_pph + $totalPPh;

                }
            } else {
                //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="pc"  value="' . $row->payment_cash_id . '" /></td>';
            }
            $returnValue .= '</tr>';
            $count = $count + 1;
        }

        $returnValue .= '</tbody>';
        /* if($checkedSlips != '') {

			$grandTotal = $totalPrice;
		if($grandTotal < 0) {
                $grandTotal = 0;
            }*/
        /* $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
            //$returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right;"><input type="text" readonly class="span12" id="dp_total" name="dp_total"></td>';
            $returnValue .= '</tr>';
    		$returnValue .= '</tfoot>';*/
        /* }else{
			$grandTotal = 0;
			$total_pph = 0;
			$total_ppn = 0;
		}*/
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="pph12" name="pph12" value="' . round($totalPPh, 2) . '" />';
        $returnValue .= '<input type="hidden" id="ppn12" name="ppn12" value="' . round($totalPPN, 2) . '" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }
	//echo $sql;
    echo $returnValue;
}

function getGeneralVendorTax2($generalVendorId11, $amount11)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT *
            FROM general_vendor
            WHERE general_vendor_id = {$generalVendorId11}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn11 = $row->ppn;
        $pph11 = $row->pph;
        $ppnID11 = $row->ppn_tax_id;
        $pphID11 = $row->pph_tax_id;

        $ppnAmount = ($ppn11 / 100) * $amount11;
        $pphAmount = ($pph11 / 100) * $amount11;

        $returnValue = '|' . number_format($ppnAmount, 2, ".", ",") . '|' . number_format($pphAmount, 2, ".", ",") . '|' . $ppnID11 . '|' . $pphID11;

    } else {
        $returnValue = '|0|0|0|0';
    }

    echo $returnValue;
}

function getAccountPaymentCash($paymentCashType)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($invoiceType == 7) {
        $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
    }

    $sql = "SELECT acc.account_id, 
			CASE WHEN acc.description IS NOT NULL THEN acc.description ELSE acc.account_name END AS account_full
            FROM account acc {$joinProperty}
            WHERE acc.status = 0 AND acc.account_type = {$paymentCashType} AND acc.account_no NOT LIKE '51%'";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->account_id . '||' . $row->account_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->account_id . '||' . $row->account_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getAccountPC($paymentFor, $paymentMethod, $paymentType)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($paymentFor == 10) {
        //$joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
        $whereProperty = " AND acc.account_no LIKE '110100' ";
    } elseif ($paymentFor == 2) {
        if ($paymentMethod == 1) {
            $whereProperty = " AND acc.account_no in (210103) AND acc.account_type = {$paymentType} ";
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_no in (130003) AND acc.account_type = {$paymentType} ";
        }
    } elseif ($paymentFor == 3) {
        if ($paymentMethod == 1) {
            $whereProperty = " AND acc.account_no in (210104) ";
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_no in (130004) ";
        }
    } elseif ($paymentFor == 7) {
        if ($paymentMethod == 1) {
            $whereProperty = " AND acc.account_type in (7) ";
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_type in (7) ";
        }
    }

    $sql = "SELECT acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
            FROM account acc 
            WHERE acc.status = 0 {$whereProperty}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->account_id . '||' . $row->account_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->account_id . '||' . $row->account_full;
            }
        }
    } else {
        echo 'WRONG';
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function setJurnalDetail()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT jd.*, s.stockpile_name, a.account_no, a.account_name, cur.currency_code
FROM gl_detail jd 
LEFT JOIN stockpile s ON s.`stockpile_id` = jd.`stockpile_id`
LEFT JOIN account a ON a.`account_id` = jd.`account_id`
LEFT JOIN currency cur ON cur.`currency_id` = jd.`currency_id`
WHERE gl_add_id IS NULL AND jd.entry_by = {$_SESSION['userId']} ORDER BY jd.gl_detail_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "jurnalDetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Stockpile</th><th>Account No</th><th>Account Name</th><th>Currency</th><th>Notes</th><th>Debit</th><th>Credit</th><th>Action</th></tr></thead>';
        $returnValue .= '<tbody>';


        while ($row = $result->fetch_object()) {


            $returnValue .= '<tr>';


            $returnValue .= '<td style="width: 5%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="width: 5%;">' . $row->account_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->account_name . '</td>';
            $returnValue .= '<td style="width: 5%;">' . $row->currency_code . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->notes . '</td>';
            if ($row->gl_type == 1) {
                $debit = $row->amount_converted;
            } else {
                $debit = 0;
            }
            if ($row->gl_type == 2) {
                $credit = $row->amount_converted;
            } else {
                $credit = 0;
            }
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($debit, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($credit, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 5%;"><a href="#" id="delete|jurnal|' . $row->gl_detail_id . '" role="button" title="Delete" onclick="deleteJurnalDetail(' . $row->gl_detail_id . ');"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';


        $returnValue .= '</table>';
        $returnValue .= '</form>';
        // $returnValue .= '<input type="hidden" id="pph2" name="pph2" value="'. round($total_pph, 2) .'" />';
        // $returnValue .= '<input type="hidden" id="ppn2" name="ppn2" value="'. round($total_ppn, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        //$returnValue .= '</div>';
    }

    echo $returnValue;
}

function getJurnalInvoice($stockpile_id)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    $sql = "SELECT invoice_id, invoice_no FROM invoice WHERE stockpileId = {$stockpile_id} ORDER BY invoice_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->invoice_id . '||' . $row->invoice_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->invoice_id . '||' . $row->invoice_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getJurnalShipment($stockpile_id)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    $sql = "SELECT sh.shipment_id, shipment_no FROM shipment sh LEFT JOIN sales sl ON sl.`sales_id` = sh.`sales_id` WHERE sl.`stockpile_id` = {$stockpile_id} ORDER BY sh.shipment_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getJurnalPo($stockpile_id)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    $sql = "SELECT c.contract_id, c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
WHERE sc.`stockpile_id` = {$stockpile_id} ORDER BY c.contract_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->po_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getJurnalSlip($stockpile_id)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';
    $dateSlip = new DateTime();
    $currentMonthSlip = $dateSlip->format('m');


    $sql1 = "SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpile_id}";
    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
    if ($result1->num_rows == 1) {
        $row1 = $result1->fetch_object();
        $stockpileCode = $row1->stockpile_code;
    }

    $sql = "SELECT transaction_id, slip_no FROM transaction WHERE slip_no LIKE '{$stockpileCode}%' AND transaction_date > (DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH))
			ORDER BY slip_no DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->transaction_id . '||' . $row->slip_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->transaction_id . '||' . $row->slip_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getJurnalNo($currentYearMonth)
{
    global $myDatabase;


    $checkJurnalNo = 'JM/JPJ/' . $currentYearMonth;

    $sql = "SELECT gl_add_no FROM gl_add WHERE gl_add_no LIKE '{$checkJurnalNo}%' ORDER BY gl_add_id DESC LIMIT 1";
    $resultJurnal = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultJurnal->num_rows == 1) {
        $rowJurnal = $resultJurnal->fetch_object();
        $splitJurnalNo = explode('/', $rowJurnal->gl_add_no);
        $lastExplode = count($splitJurnalNo) - 1;
        $nextJurnalNo = ((float)$splitJurnalNo[$lastExplode]) + 1;
        $jurnalNo = $checkJurnalNo . '/' . $nextJurnalNo;
    } else {
        $jurnalNo = $checkJurnalNo . '/1';

    }

    echo $jurnalNo;
}

function refreshPo($po_pks_id)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT po.*, v.vendor_name, s.stockpile_name, cur.currency_code,
(po.quantity - (SELECT COALESCE(SUM(a.quantity),0) FROM po_contract a LEFT JOIN contract b ON a.contract_id = b.contract_id WHERE a.po_pks_id = po.`po_pks_id` AND b.contract_status = 0)) AS balance
FROM po_pks po
LEFT JOIN vendor v ON v.vendor_id = po.vendor_id
LEFT JOIN stockpile s ON s.stockpile_id = po.stockpile_id
LEFT JOIN currency cur ON cur.currency_id = po.currency_id
				WHERE po.po_pks_id = {$po_pks_id}
				
				AND po.company_id = {$_SESSION['companyId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();

        $dppTotalPrice = 0;


        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<p style="text-align: center; font-weight: bold;"></p>';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Vendor</th><th>Contract No.</th><th>Stockpile</th><th>Currency</th><th>Unit Price</th><th>Quantity</th><th>Balance</th></tr></thead>';
        $returnValue .= '<tbody>';
        $returnValue .= '<tr>';
        $returnValue .= '<td style="text-align: right; width: 20%;">' . $row->vendor_name . '</td>';
        $returnValue .= '<td style="text-align: right; width: 20%;">' . $row->contract_no . '</td>';
        $returnValue .= '<td style="text-align: right; width: 15%;">' . $row->stockpile_name . '</td>';
        $returnValue .= '<td>' . $row->currency_code . '</td>';
        $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row->price, 4, ".", ",") . '</td>';
        $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row->quantity, 2, ".", ",") . '</td>';
        $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row->balance, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
        $returnValue .= '</tbody>';

        //$returnValue .= '<input type="hidden" id="po_pks_id" name="po_pks_id" value="'. $row->po_pks_id .'" />';
        $returnValue .= '<input type="hidden" id="contract_no" name="contract_no" value="' . $row->contract_no . '" />';
        $returnValue .= '<input type="hidden" id="vendorId" name="vendorId" value="' . $row->vendor_id . '" />';
        $returnValue .= '<input type="hidden" id="stockpile_id" name="stockpile_id" value="' . $row->stockpile_id . '" />';
        $returnValue .= '<input type="hidden" id="currencyId" name="currencyId" value="' . $row->currency_id . '" />';
        $returnValue .= '<input type="hidden" id="price" name="price" value="' . $row->price . '" />';
        $returnValue .= '<input type="hidden" id="quantity" name="quantity" value="' . $row->quantity . '" />';
        $returnValue .= '<input type="hidden" id="balance" name="balance" value="' . $row->balance . '" />';
        $returnValue .= '<input type="hidden" id="exchangeRate" name="exchangeRate" value="' . $row->exchange_rate . '" />';
        $returnValue .= '</table>';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function setInvoiceDetail()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT id.invoice_detail_id,
CASE WHEN id.type = 4 THEN 'Loading'
WHEN id.type = 5 THEN 'Umum'
WHEN id.type = 6 THEN 'HO' ELSE '' END AS `type2`, 
CASE WHEN id.poId IS NOT NULL THEN c.po_no
WHEN id.shipment_id IS NOT NULL THEN  sh.shipment_no
ELSE '' END AS po_shipment,
a.account_name, s.stockpile_name, id.notes, cur.currency_code, id.exchange_rate,
id.qty, id.price, id.termin, id.amount, id.ppn, id.pph, id.tamount, gv.general_vendor_name, gv.pph AS gv_pph, gv.ppn AS gv_ppn,
CASE WHEN idUOM IS NOT NULL THEN (SELECT uom_type FROM uom WHERE idUOM = id.idUOM) ELSE '-' END AS uom
FROM invoice_detail id LEFT JOIN account a ON id.account_id = a.account_id
LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
LEFT JOIN contract c ON c.contract_id = id.poId
LEFT JOIN currency cur ON cur.currency_id = id.currency_id
WHERE id.invoice_id IS NULL AND id.entry_by = {$_SESSION['userId']} ORDER BY id.invoice_detail_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "invoiceDetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Type</th><th>Account</th><th>Vendor</th><th>PO No/Shipment Code</th><th>Remark (SP)</th><th>Notes</th><th>Qty</th><th>Currency(Rate)</th><th>Unit Price</th><th>Termin</th><th>Amount</th><th>PPN</th><th>PPh</th><th>Down Payment</th><th>Total Amount</th><th>Action</th></tr></thead>';
        $returnValue .= '<tbody>';


        while ($row = $result->fetch_object()) {


            $returnValue .= '<tr>';
            /* if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->invoice_detail_id);

                  if($pos === false) {
                  $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';

					$dppPrice = $dppPrice + $dppTotalPrice;
					$totalPrice = $totalPrice + $total;
					$total_ppn = $total_ppn + $totalPPN;
					$total_pph = $total_pph + $totalPPh;

                }
            } else {
               		$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
            }
			*/
            $sqlDP = "SELECT SUM(idp.amount_payment) AS down_payment,
SUM(CASE WHEN id.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END) AS ppn, 
SUM(CASE WHEN id.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END) AS pph 
FROM invoice_dp idp 
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.`invoice_detail_dp`
LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
WHERE idp.status = 0 AND idp.invoice_detail_id = {$row->invoice_detail_id}";
            $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
            if ($resultDP !== false && $resultDP->num_rows == 1) {
                $rowDP = $resultDP->fetch_object();

                if ($rowDP->ppn == 0) {
                    $dp_ppn = 0;
                } else {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    $dp_ppn = $rowDP->ppn;
                }

                if ($rowDP->pph == 0) {
                    $dp_pph = 0;
                } else {
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $dp_pph = $rowDP->pph;
                }


                if ($rowDP->down_payment != 0) {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $downPayment = ($rowDP->down_payment + $dp_ppn) - $dp_pph;
                } else {
                    $downPayment = 0;
                }
            }
            $tamount1 = $row->amount + $row->ppn - $row->pph;
            $tamount = $tamount1 - $downPayment;
            $totalPrice = $totalPrice + $tamount;

            $returnValue .= '<td style="width: 8%;">' . $row->type2 . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->account_name . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->general_vendor_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->po_shipment . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->qty, 2, ".", ",") . ' ' . $row->uom . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->currency_code . '(' . number_format($row->exchange_rate, 0, ".", ",") . ')' . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->termin, 0, ".", ",") . '%</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->amount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->ppn, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->pph, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($downPayment, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($tamount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;"><a href="#" id="delete|invoice|' . $row->invoice_detail_id . '" role="button" title="Delete" onclick="deleteInvoiceDetail(' . $row->invoice_detail_id . ');"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';


        $grandTotal = $totalPrice;

        $returnValue .= '<tfoot>';
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td colspan="2" style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '<td></td>';
        $returnValue .= '</tr>';
        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        // $returnValue .= '<input type="hidden" id="pph2" name="pph2" value="'. round($total_pph, 2) .'" />';
        // $returnValue .= '<input type="hidden" id="ppn2" name="ppn2" value="'. round($total_ppn, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        //$returnValue .= '</div>';
    }

    echo $returnValue;
}

function setInvoiceDP($podID, $generalVendorId,$method, $ppn1)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';

    if ($method == "INSERT") {
        $whereProperty = 'AND (CASE WHEN id.`amount` < 0 THEN ((id.`amount` *-1) - (SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0)) 
        ELSE ((id.`amount`) - (SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0))END) > 0.001';
    } else {
        $whereProperty = "AND (CASE WHEN id.`amount` < 0 THEN ((id.`amount` *-1) - (SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0)) 
        ELSE ((id.`amount`) - (SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0))END) < 0.001 AND idp.`po_detail_id_dp` = {$podID}";
    }

    $sql = "SELECT i.*, id.*,gv.`pph` AS dp_pph, gv.`ppn` AS dp_ppn,
            (SELECT SUM(amount_payment) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND status = 0) AS total_dp
            FROM invoice i 
            LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id`
            LEFT JOIN invoice_dp idp ON idp.`invoice_detail_dp` = id.`invoice_detail_id`
            LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
            WHERE id.general_vendor_id = {$generalVendorId} AND id.invoice_method_detail = 2 AND id.invoice_detail_status = 0 AND i.company_id = {$_SESSION['companyId']} AND i.`payment_status` != 2 AND i.`invoice_status` != 2
            $whereProperty
            ORDER BY i.invoice_id DESC, id.invoice_detail_id DESC";

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    // echo " AI " . $sql;

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "invoice">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<h5>DOWNPAYMENT <span style="color: red;">(MASUKAN AMOUNT DPP & CENTANG DP)</span></h5>';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr>
                                    <th>Invoice No.</th>
                                    <th>Original Invoice No.</th>
                                    <th>Notes</th>
                                    <th>Amount DPP</th>
                                    <th>PPN</th>
                                    <th>PPh</th>
                                    <th>Available DP (Include PPN & PPh)</th>
                                    <th>Available DP (DPP)</th>';
                                    if ($method == 'INSERT') { 
                   $returnValue .= '<th>Input Amount DPP</th>
                                    <th>'; 
                                     } 
                   $returnValue .='</th>
                                </tr>
                            </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $dp_ppn = 0;
        $dp_pph = 0;
        $count = 0;
        while ($row = $result->fetch_object()) {

            $dppTotalPrice = $row->amount;

            if ($row->ppn != 0 && $row->ppn != '') {
                $totalPPN = $row->ppn;
            } else {
                $totalPPN = 0;
            }

            if ($row->pph != 0 && $row->pph != '') {
                $totalPPh = $row->pph;
            } else {
                $totalPPh = 0;
            }

            if ($row->dp_ppn != 0 && $row->ppn != 0) {
                $dp_ppn = $row->total_dp * ($row->dp_ppn / 100);
            } else {
                $dp_ppn = 0;
            }

            if ($row->dp_pph != 0 && $row->pph != 0) {
                $dp_pph = $row->total_dp * ($row->dp_pph / 100);
            } else {
                $dp_pph = 0;
            }

			
            if ($method == "INSERT") {
                $total = ($dppTotalPrice + $totalPPN) - $totalPPh;
                $total2 = ($row->total_dp + $dp_ppn) - $dp_pph;
                $total_dp = $total - $total2;
                $total_dp_dpp = $dppTotalPrice - $row->total_dp;
            } else {
                $total_dp = ($dppTotalPrice + $totalPPN) - $totalPPh;
                $total_dp_dpp = $dppTotalPrice;
            }
            

            $returnValue .= '<tr>';


            $returnValue .= '<td style="width: 20%;">' . $row->invoice_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->invoice_no2 . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            // $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
            // $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;"><input type="text" readonly id="totalPPN" name="checkedSlips3[' . $count . ']" value="'. $totalPPN .'" /></td>';
            $returnValue .= '<td style="text-align: right;"><input type="text" readonly id="totalPPh" name="checkedSlips4[' . $count . ']" value="'. $totalPPh .'" /></td>';

            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($total_dp, 8, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($total_dp_dpp, 8, ".", ",") . '</td>';
            //$returnValue .= '<td style="text-align: right; width: 15%;">'. number_format($total_dp, 2, ".", ",") .'</td>';
            if ($method == 'INSERT') {
                $returnValue .= '<td style="text-align: right;"><input type="text" id="paymentTotal" name="checkedSlips2[' . $count . ']" max="'. $total_dp_dpp .'" /></td>';
                if ($checkedSlips != '') {
                    $pos = strpos($checkedSlips, $row->invoice_detail_id);
    
                    if ($pos === false) {
                        //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="inv"   value="' . $row->invoice_detail_id . '" /></td>';
                    } else {
                        //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="inv"  value="' . $row->invoice_detail_id . '" checked /></td>';
    
                        //$dppPrice = $dppPrice + $dppTotalPrice;
                        //$totalPrice = $totalPrice + $total;
                        //$total_ppn = $total_ppn + $totalPPN;
                        //$total_pph = $total_pph + $totalPPh;
    
                    }
                } else {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="inv"  value="' . $row->invoice_detail_id . '" /></td>';
                }
            }
            $returnValue .= '</tr>';
            $count = $count + 1;
        }

        $returnValue .= '</tbody>';
        /* if($checkedSlips != '') {

			$grandTotal = $totalPrice;
			if($grandTotal < 0) {
                $grandTotal = 0;
            }*/
        /*$returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Grand Total</td>';
            //$returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right;"><input type="text" readonly class="span12" id="dp_total" name="dp_total"></td>';
            $returnValue .= '</tr>';
    		$returnValue .= '</tfoot>';*/
        /* }else{
			$grandTotal = 0;
			$total_pph = 0;
			$total_ppn = 0;
		}*/
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="available_dp" name="available_dp	" value="' . number_format($total_dp, 2, ".", ",") . '" />';
        $returnValue .= '<input type="hidden" id="ppnDP2" name="ppnDP2" value="' . number_format($totalPPN, 2, ".", ",") . '" />';
        $returnValue .= '<input type="hidden" id="pphDP2" name="pphDP2" value="' . number_format($totalPPh, 2, ".", ",") . '" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function refreshInvoice($invoiceId, $paymentMethod, $ppn12, $pph12)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT i.invoice_id, i.invoice_no, id.invoice_detail_id, id.invoice_id, id.dp_amount,
                    CASE WHEN id.type = 4 THEN 'Loading'
                    WHEN id.type = 5 THEN 'Umum'
                    WHEN id.type = 6 THEN 'HO' ELSE '' END AS invoice_type, a.account_name, sh.shipment_no, s.stockpile_name, id.notes,
                    CASE WHEN i.invoice_id IS NOT NULL THEN (SELECT COALESCE(SUM(p.original_amount_converted), 0) FROM payment p WHERE p.invoice_id = i.invoice_id AND p.payment_method = 2 AND p.payment_status = 0)
                    ELSE 0 END AS dp,
                    CASE WHEN id.shipment_id IS NOT NULL THEN  sh.shipment_no
                    WHEN id.mutasi_detail_id IS NOT NULL THEN (SELECT a.kode_mutasi FROM mutasi_header a LEFT JOIN mutasi_detail b ON a.mutasi_header_id = b.mutasi_header_id WHERE b.mutasi_detail_id = id.mutasi_detail_id LIMIT 1)
                    ELSE '' END AS referenceCode,
                    CASE WHEN id.invoice_detail_id IS NOT NULL THEN (SELECT COALESCE(GROUP_CONCAT(invoice_dp),0) FROM invoice_dp WHERE invoice_detail_id = id.invoice_detail_id ) ELSE 0 END AS iddp,
                    id.qty, id.price, id.termin, id.amount, id.ppn, id.pph, id.tamount, gv.general_vendor_name, gv.ppn AS gv_ppn, gv.pph AS gv_pph, c.po_no, id.mutasi_detail_id,
                    CASE WHEN id.mutasi_detail_id > 0 THEN (SELECT CONCAT(ROUND(id.termin,0),'% / ',a.percentage,'%') FROM termin_detail a LEFT JOIN mutasi_detail b ON a.id = b.termin_detail_id WHERE b.mutasi_detail_id = id.mutasi_detail_id)
                    ELSE CONCAT(ROUND(id.termin,0),'%') END AS terminNew,
                    CASE WHEN idUOM IS NOT NULL THEN (SELECT uom_type FROM uom WHERE idUOM = id.idUOM) ELSE '-' END AS uom
            FROM invoice_detail id LEFT JOIN account a ON id.account_id = a.account_id
            LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
            LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
            LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
            LEFT JOIN invoice i ON i.invoice_id = id.invoice_id
            LEFT JOIN contract c ON c.contract_id = id.poId
            WHERE id.invoice_id = {$invoiceId} ORDER BY id.invoice_detail_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th>Type</th>
                                <th>Account</th>
                                <th>Vendor</th>
                                <th>PO No</th>
                                <th>Reference Code</th>
                                <th>Remark (SP)</th>
                                <th>Notes</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Termin</th>
                                <th>Amount</th>
                                <th>PPN</th>
                                <th>PPh</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $tamount = $row->amount + $row->ppn - $row->pph;
        $totalPrice = $totalPrice + $tamount;

        $iddp = array();
        $downPayment = 0;
        while ($row = mysqli_fetch_array($result)) {
            // while($row = $result->fetch_object()) {
            $tamount = ($row['amount']+ $row['ppn'] - $row['pph']);
            $invoiceDetailId[] = $row['invoice_detail_id'];
            $totalPrice = $totalPrice + $tamount;
            $downPayment = $downPayment + $row['dp_amount'];
            $iddp[] = $row['iddp'];
            $gv_ppn = $row['gv_ppn'];
            $gv_pph = $row['gv_pph'];
            $dp = $row['dp'];

            $returnValue .= '<tr>';
            $returnValue .= '<td style="width: 10%;">' . $row['invoice_type'] . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row['account_name'] . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row['general_vendor_name'] . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row['po_no'] . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row['referenceCode'] . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row['stockpile_name'] . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row['notes'] . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row['qty'], 2, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row['price'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . $row['terminNew'] . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row['amount'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row['ppn'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row['pph'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($tamount, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        $returnValue .= '<tfoot>';
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="13" style="text-align: right;">Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';


        $iddps = implode(', ', $invoiceDetailId);
        //echo $iddps;
        // $sql = "SELECT SUM(idp.amount_payment) AS down_payment,
        //         SUM(CASE WHEN id.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END) AS ppn,
        //         SUM(CASE WHEN id.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END) AS pph
        //         FROM invoice_dp idp
        //         LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.`invoice_detail_dp`
        //         LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
        //         LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
        //         WHERE idp.status = 0 AND idp.invoice_detail_id IN ({$iddps})";
        // $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        // //$downPayment = 0;
        // if ($result !== false && $result->num_rows == 1) {
        //     $row = $result->fetch_object();
        //     if ($row->down_payment != 0) {
        //         if ($row->ppn == 0) {
        //             $dp_ppn = 0;
        //         } else {
        //             //$dp_ppn = $row->down_payment * ($row->gv_ppn/100);
        //             $dp_ppn = $row->ppn;
        //         }
        //         if ($row->pph == 0) {
        //             $dp_pph = 0;
        //         } else {
        //             //$dp_pph = $row->down_payment * ($row->gv_pph/100);
        //             $dp_pph = $row->pph;
        //         }
        //         $downPayment1 = ($row->down_payment + $dp_ppn) - $dp_pph;
        //     } else {
        //         $downPayment1 = 0;
        //     }

        //     if ($dp != 0) {
        //         $dp2 = $dp;
        //     } else {
        //         $dp2 = 0;
        //     }

        //     $downPayment = $downPayment1 + $dp2;
        // } elseif ($dp != 0) {
        //     $downPayment = $dp;
        // } else {
        //     $downPayment = 0;
        // }
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="13" style="text-align: right;">Down Payment</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';


        $ppn12 = 0;
        $pph12 = 0;
//
        //edited by alan
        $grandTotal = $totalPrice - $downPayment;
        //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


        // $totalPrice = ($totalPrice + $totalPpn) - $totalPph;


        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="ppn12" name="ppn12" value="' . round($ppn12, 2) . '" />';
        $returnValue .= '<input type="hidden" id="pph12" name="pph12" value="' . round($pph12, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
		$returnValue .= '<input type="hidden" id="amountInvoice" name="amountInvoice" value="' . round($grandTotal, 2)  . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function getAccountPayment($paymentFor, $bankIdSp)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($paymentFor == 1) {
        
            $whereProperty = " AND acc.account_no in (210101) ";
           
    } elseif ($paymentFor == 2) {
        
            $whereProperty = " AND acc.account_no in (210103) ";
        
    } elseif ($paymentFor == 3) {
        
            $whereProperty = " AND acc.account_no in (210104) ";
        
    } elseif ($paymentFor == 8) {
			
			$whereProperty = " AND acc.account_no in (210105) ";
			
    } elseif ($paymentFor == 9) {
        
            $whereProperty = " AND acc.account_no in (210106) ";
        
    }elseif ($paymentFor == 7) {
        $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id 
                          LEFT JOIN vendor_pettycash vp ON vp.account_no = acc.account_no";
        $whereProperty = " AND b.bank_id = {$bankIdSp} ";
        $select = "vp.bank, vp.beneficiary, vp.no_rek, vp.branch, ";
    }

    $sql = "SELECT {$select} acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
            FROM account acc  {$joinProperty}
            WHERE acc.status = 0 AND acc.account_type = {$paymentFor} {$whereProperty}";
            // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		if($paymentFor == 7){
            $returnValue = $row->account_id . '||' . $row->account_full . '||' . $row->beneficiary . '||' . $row->bank . '||' . $row->no_rek;
        }else{
            $returnValue = $row->account_id . '||' . $row->account_full ;
        }

        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}


function refreshInvoiceCurahPayment($invoiceCurahId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    
    $sql = "SELECT i.inv_notim_id,pp.vendor_bank_id, pp.`payment_method`, pp.payment_type, 
    CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE 'Payment' END AS payment_method2,
    CASE WHEN pp.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS payment_type2,pp.total_ppn_amount, pp.total_pph_amount,
    pp.stockpile_id, s.stockpile_name, pp.remarks, pp.idPP, pp.total_qty as qty, pp.price, pp.grand_total AS amount, pp.termin,
    pp.invoice_no, DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoice_date, pp.tax_invoice
    FROM invoice_notim i	
    LEFT JOIN pengajuan_payment pp ON pp.`idPP` = i.idPP
    LEFT JOIN stockpile s ON s.stockpile_id = pp.stockpile_id
			WHERE i.inv_notim_id = {$invoiceCurahId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
echo "AA".$sql;
if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		if($row->payment_method == 3){
			$paymentMethod = 1;
		}else{
			$paymentMethod = $row->payment_method;
		}
        if($row->payment_method == 2){
            $termin = $row->termin;
        }else{
            $termin = 100;
        }
		$returnValue = $row->inv_notim_id . '||' . $row->stockpile_id . '||' . $row->stockpile_name . '||' . $paymentMethod  
                        . '||' . $row->payment_method2  . '||' . $row->payment_type  . '||' . $row->payment_type2  . '||' . 0 . '||' . 'HO' 
                        . '||' . $row->vendor_bank_id . '||' . $row->remarks .  '||' . $row->idPP . '||' . $row->qty . '||' . $row->price 
                        . '||' . $row->amount . '||' . $termin . '||' . $row->ppn_amount . '||' . $row->pph_amount
                        . '||' . $row->invoice_no. '||' . $row->invoice_date . '||' . $row->tax_invoice ;        
        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}
function refreshInvoiceOBPayment($invoiceOBId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    
    $sql = "SELECT i.inv_notim_id,pp.vendor_bank_id, pp.`payment_method`, pp.payment_type,
            CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE 'Payment' END AS payment_method2, 
            CASE WHEN pp.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS payment_type2,pp.total_ppn_amount, pp.total_pph_amount,
            pp.stockpile_id, s.stockpile_name, pp.remarks,  pp.idPP, pp.total_qty, pp.price, pp.grand_total AS amount, pp.termin,
            pp.invoice_no, DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoice_date, pp.tax_invoice
            FROM invoice_notim i	
            LEFT JOIN pengajuan_payment pp ON pp.`idPP` = i.idPP
            LEFT JOIN stockpile s ON s.stockpile_id = pp.stockpile_id
			WHERE i.inv_notim_id = {$invoiceOBId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		if($row->payment_method == 3){
			$paymentMethod = 1;
		}else{
			$paymentMethod = $row->payment_method;
		}

        if($row->payment_method == 2){
            $termin = $row->termin;
        }else{
            $termin = 100;
        }
		$returnValue = $row->inv_notim_id . '||' . $row->stockpile_id . '||' . $row->stockpile_name . '||' . $paymentMethod  . '||' . $row->payment_method2  . '||' . $row->payment_type  . '||' . $row->payment_type2  . '||' . 0 
                        . '||' . 'HO' . '||' . $row->vendor_bank_id . '||' . $row->remarks . '||' . $row->idPP . '||' . $row->total_qty 
                        . '||' . $row->price . '||' . $row->amount . '||' . $termin . '||' . $row->total_ppn_amount 
                        . '||' . $row->total_pph_amount . '||' . $row->invoice_no. '||' . $row->invoice_date . '||' . $row->tax_invoice ;
        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}
function refreshInvoiceHandlingPayment($invoiceHandlingId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    
    $sql = "SELECT i.inv_notim_id,pp.vendor_bank_id, pp.`payment_method`, pp.payment_type,
            CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE 'Payment' END AS payment_method2,
            CASE WHEN pp.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS payment_type2,pp.total_ppn_amount as ppn_amount, pp.total_pph_amount as pph_amount,
            pp.stockpile_id, s.stockpile_name, pp.remarks,  pp.idPP, pp.total_qty as qty, pp.price, pp.grand_total AS amount, pp.termin,
            pp.invoice_no, DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoice_date, pp.tax_invoice
            FROM invoice_notim i	
            LEFT JOIN pengajuan_payment pp ON pp.`idPP` = i.idPP
            LEFT JOIN stockpile s ON s.stockpile_id = pp.stockpile_id
			WHERE i.inv_notim_id = {$invoiceHandlingId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		if($row->payment_method == 3){
			$paymentMethod = 1;
		}else{
			$paymentMethod = $row->payment_method;
		}
        if($row->payment_method == 2){
            $termin = $row->termin;
        }else{
            $termin = 100;
        }
		$returnValue = $row->inv_notim_id . '||' . $row->stockpile_id . '||' . $row->stockpile_name . '||' . $paymentMethod  
                    . '||' . $row->payment_method2  . '||' . $row->payment_type  . '||' . $row->payment_type2  . '||' . 0 
                    . '||' . 'HO' . '||' . $row->vendor_bank_id . '||' . $row->remarks . '||' . $row->idPP .  '||' . $row->qty 
                    . '||' . $row->price . '||' . $row->amount . '||' . $termin . '||' . $row->ppn_amount . '||' . $row->pph_amount
                    . '||' . $row->invoice_no. '||' . $row->invoice_date . '||' . $row->tax_invoice ;        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}
function refreshInvoiceOAPayment($invoiceOAId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    
    $sql = "SELECT i.inv_notim_id, pp.vendor_bank_id, pp.`payment_method`, pp.payment_type,
            CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE 'Payment' END AS payment_method2,
            CASE WHEN pp.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS payment_type2,pp.total_ppn_amount, pp.total_pph_amount,
            pp.stockpile_id, s.stockpile_name, pp.remarks, pp.idPP, pp.total_qty, pp.price, pp.grand_total AS amount, pp.termin,
            pp.invoice_no, DATE_FORMAT(pp.invoice_date, '%d/%m/%Y') AS invoice_date, pp.tax_invoice
            FROM invoice_notim i
            LEFT JOIN pengajuan_payment pp ON pp.`idPP` = i.idPP
            LEFT JOIN stockpile s ON s.stockpile_id = pp.stockpile_id
			WHERE i.inv_notim_id = {$invoiceOAId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;
if ($result->num_rows == 1) {
        $row = $result->fetch_object();
		if($row->payment_method == 3){
			$paymentMethod = 1;
		}else{
			$paymentMethod = $row->payment_method;
		}

        if($row->payment_method == 2){
            $termin = $row->termin;
        }else{
            $termin = 100;
        }
		$returnValue = $row->inv_notim_id . '||' . $row->stockpile_id . '||' . $row->stockpile_name . '||' . $paymentMethod  . '||' . $row->payment_method2  
                        . '||' . $row->payment_type  . '||' . $row->payment_type2  . '||' . 0 . '||' . 'HO' . '||' . $row->vendor_bank_id . '||' . $row->remarks 
                        .  '||' . $row->idPP . '||' . $row->total_qty . '||' . $row->price 
                        . '||' . $row->amount . '||' .  $termin . '||' . $row->total_ppn_amount . '||' . $row->total_pph_amount . '||' . $row->invoice_no
                        . '||' . $row->invoice_date . '||' . $row->tax_invoice;
        //echo $row->stockpile_contract_id;
    }
    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function refreshInvoiceCurah($refreshInvoiceCurah, $paymentMethod)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT  pp.vendor_bank_id AS invoiceBankId, t.`slip_no`, c.`po_no`, 
    CASE WHEN pp.payment_method = 2 THEN pp.total_qty ELSE pc.`qty` END AS quantity, 
    CASE WHEN pp.payment_method = 2 THEN pp.price ELSE pc.`price` END AS unit_price ,
    CASE WHEN pp.payment_method = 2 THEN pp.total_dpp ELSE ROUND(pc.dpp,10) END AS dpp,
    CASE WHEN pp.payment_method = 2 THEN pp.total_ppn_amount ELSE ROUND(pc.dpp * (pc.ppn_value/100),10) END AS ppn,
    CASE WHEN pp.payment_method = 2 THEN pp.total_pph_amount ELSE ROUND(pc.dpp * (pc.pph_value/100),10) END AS pph,
    CASE WHEN pp.payment_method = 2 THEN pp.grand_total ELSE (ROUND(pc.dpp,10) + ROUND(pc.dpp * (pc.ppn_value/100),10) - ROUND(pc.dpp * (pc.pph_value/100),10)) END AS amount,
    COALESCE((SELECT SUM(pdp.settle_amount) FROM pengajuan_payment_dp pdp WHERE pdp.idpp = ip.idpp),0) AS totalDp
     FROM invoice_notim ip
    LEFT JOIN pengajuan_payment pp ON pp.`idPP` = ip.idPP
    LEFT JOIN payment_curah pc ON pc.inv_notim_id = ip.`inv_notim_id`
    LEFT JOIN `transaction` t ON t.`transaction_id` = pc.transaction_id
    LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
    LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
    LEFT JOIN vendor v ON v.`vendor_id` = ip.vendor_id
WHERE ip.inv_notim_id = {$refreshInvoiceCurah} ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Slip No</th><th>PO No.</th><th>Qty</th><th>Price</th><th>DPP</th><th>PPN </th><th>PPh</th><th>Total Amount</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalQty = 0;
        $totalDpp = 0;
       // $totalSusut = 0;
		$totalPPN = 0;
		$totalPPh = 0;
		$downPayment = 0;
		$grandTotal = 0;
        

        $iddp = array();
        while ($row = mysqli_fetch_array($result)) {
            // while($row = $result->fetch_object()) {
            
            $returnValue .= '<tr>';

            $returnValue .= '<td >' . $row['slip_no'] . '</td>';
            $returnValue .= '<td>' . $row['po_no'] . '</td>';
			
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['quantity'], 2, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['unit_price'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['dpp'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['ppn'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['pph'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['amount'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_qty_claim'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_price_claim'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_amount'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['total_amount'], 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
			
		$totalQty = $totalQty + $row['quantity'];
        $totalDpp = $totalDpp + $row['dpp'];
       // $totalSusut = $totalSusut + $row['shrink_amount'];
		$totalPPN = $totalPPN + $row['ppn'];
		$totalPPh = $totalPPh + $row['pph'];
		$totalAmount = $totalAmount + $row['amount'];
		$invoiceBankId = $row['invoiceBankId'];
		$invoiceCurrencyId = $row['currency'];
		$invoiceKurs = $row['kurs'];
        $downPayment = $row['totalDp'];


        }

        $returnValue .= '</tbody>';
        $returnValue .= '<tfoot>';
        
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total Quantity</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalQty, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total DPP</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalDpp, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		/*$returnValue .= '<tr>';
        $returnValue .= '<td colspan="11" style="text-align: right;">Total Susut</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalSusut, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';*/
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total PPN</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total PPh</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Down Payment</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';


        $grandTotal = $totalAmount - $downPayment;
        


        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="ppnInvoice" name="ppnInvoice" value="' . round($totalPPN, 10) . '" />';
        $returnValue .= '<input type="hidden" id="pphInvoice" name="pphInvoice" value="' . round($totalPPh, 10) . '" />';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalDpp, 10) . '" />';
		$returnValue .= '<input type="hidden" id="totalAmount" name="totalAmount" value="' . round($totalAmount, 10) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . round($downPayment , 10). '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 10) . '" />';
		$returnValue .= '<input type="hidden" id="invoiceBankId" name="invoiceBankId" value="' . $invoiceBankId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceCurrencyId" name="invoiceCurrencyId" value="' . $invoiceCurrencyId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceKurs" name="invoiceKurs" value="' . $invoiceKurs . '" />';
		
        $returnValue .= '</div>';
    }

    echo $returnValue;
}
function refreshInvoiceHandling($invoiceHandlingId, $paymentMethod)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT COALESCE((SELECT SUM(pdp.settle_amount) FROM pengajuan_payment_dp pdp WHERE pdp.idpp = ip.idpp),0) AS totalDp,
            pp.vendor_bank_id AS invoiceBankId, t.`slip_no`, c.`po_no`, 
            CASE WHEN pp.payment_method = 2 THEN pp.price ELSE a.price END AS handling_price,
            CASE WHEN pp.payment_method = 2 THEN pp.total_qty ELSE a.qty END AS handling_quantity,
            CASE WHEN pp.payment_method = 2 THEN pp.total_dpp ELSE ROUND(a.dpp,10) END AS dpp,
            CASE WHEN pp.payment_method = 2 THEN pp.total_ppn_amount ELSE ROUND((a.dpp) * (vh.ppn/100),10) END AS ppn,
            CASE WHEN pp.payment_method = 2 THEN pp.total_pph_amount ELSE ROUND((a.dpp) * (vh.pph/100),10) END AS pph,
            CASE WHEN pp.payment_method = 2 THEN pp.grand_total ELSE 
            (ROUND(a.dpp,10) + ROUND((a.dpp) * (vh.ppn/100),10) - ROUND((a.dpp) * (vh.pph/100),10)) END AS amount
            FROM invoice_notim ip
            INNER JOIN pengajuan_payment pp ON pp.`idPP` = ip.`idPP`
            LEFT JOIN payment_handling a ON a.`inv_notim_id` = ip.`inv_notim_id`
            LEFT JOIN `transaction` t ON t.`transaction_id` = a.`transaction_id`
            LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
            LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
            LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = ip.vendorHandlingid
            WHERE ip.inv_notim_id = {$invoiceHandlingId} ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Slip No</th><th>PO No.</th><th>Qty</th><th>Price</th><th>DPP</th><th>PPN </th><th>PPh</th><th>Total Amount</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalQty = 0;
        $totalDpp = 0;
       // $totalSusut = 0;
		$totalPPN = 0;
		$totalPPh = 0;
		$downPayment = 0;
		$grandTotal = 0;
        

        $iddp = array();
        while ($row = mysqli_fetch_array($result)) {
			
            // while($row = $result->fetch_object()) {
            
            $returnValue .= '<tr>';

            $returnValue .= '<td >' . $row['slip_no'] . '</td>';
            $returnValue .= '<td>' . $row['po_no'] . '</td>';
			
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['handling_quantity'], 2, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['handling_price'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['dpp'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['ppn'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['pph'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['amount'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_qty_claim'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_price_claim'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_amount'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['total_amount'], 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
			
		$totalQty = $totalQty + $row['handling_quantity'];
        $totalDpp = $totalDpp + $row['dpp'];
       // $totalSusut = $totalSusut + $row['shrink_amount'];
		$totalPPN = $totalPPN + $row['ppn'];
		$totalPPh = $totalPPh + $row['pph'];
		$totalAmount = $totalAmount + $row['amount'];
		$invoiceBankId = $row['invoiceBankId'];
		$invoiceCurrencyId = $row['currency'];
		$invoiceKurs = $row['kurs'];
		$downPayment = $row['totalDp'];

        }
		
        $returnValue .= '</tbody>';
        $returnValue .= '<tfoot>';
        
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total Quantity</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalQty, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total DPP</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalDpp, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		/*$returnValue .= '<tr>';
        $returnValue .= '<td colspan="11" style="text-align: right;">Total Susut</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalSusut, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';*/
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total PPN</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total PPh</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Down Payment</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';


        $grandTotal = $totalAmount - $downPayment;
        


        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="ppnInvoice" name="ppnInvoice" value="' . round($totalPPN, 10) . '" />';
        $returnValue .= '<input type="hidden" id="pphInvoice" name="pphInvoice" value="' . round($totalPPh, 10) . '" />';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalDpp, 10) . '" />';
		$returnValue .= '<input type="hidden" id="totalAmount" name="totalAmount" value="' . round($totalAmount, 10) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . round($downPayment , 10). '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 10) . '" />';
		$returnValue .= '<input type="hidden" id="invoiceBankId" name="invoiceBankId" value="' . $invoiceBankId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceCurrencyId" name="invoiceCurrencyId" value="' . $invoiceCurrencyId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceKurs" name="invoiceKurs" value="' . $invoiceKurs . '" />';
		
        $returnValue .= '</div>';
    }

    echo $returnValue;
}
function refreshInvoiceOB($invoiceOBId, $paymentMethod)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT  pp.vendor_bank_id AS invoiceBankId, t.`slip_no`, c.`po_no`, 
    CASE WHEN pp.payment_method  = 2 THEN pp.price ELSE a.total_amount END AS unloading_price,
    CASE WHEN pp.payment_method = 2 THEN pp.total_dpp ELSE ROUND(a.dpp,10) END AS dpp,
    CASE WHEN pp.payment_method  = 2 THEN pp.total_qty ELSE t.quantity END AS send_weight,
    CASE WHEN pp.payment_method  = 2 THEN pp.total_ppn_amount ELSE ROUND((a.dpp) * (a.ppn_value/100),10) END AS ppn,
    CASE WHEN pp.payment_method  = 2 THEN pp.total_pph_amount ELSE ROUND((a.dpp) * (a.pph_value/100),10) END AS pph,
    CASE WHEN pp.payment_method  = 2 THEN pp.grand_total ELSE 
    (ROUND(a.dpp,10) + ROUND((a.dpp) * (a.ppn_value/100),10) - ROUND((a.dpp) * (a.pph_value/100),10)) END AS amount,
    COALESCE((SELECT round(SUM(pdp.settle_amount),10) FROM pengajuan_payment_dp pdp WHERE pdp.idpp = ip.idpp),0) AS totalDp

    FROM invoice_notim ip
    INNER JOIN pengajuan_payment pp ON pp.`idPP` = ip.`idPP`
    LEFT JOIN payment_ob a ON a.`inv_notim_id` = ip.`inv_notim_id`
    LEFT JOIN `transaction` t ON t.`transaction_id` = a.`transaction_id`
    LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
    LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
    LEFT JOIN labor l ON l.`labor_id` = ip.laborId
WHERE ip.inv_notim_id = {$invoiceOBId} ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Slip No</th><th>PO No.</th><th>Qty</th><th>Price</th><th>DPP</th><th>PPN </th><th>PPh</th><th>Total Amount</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalQty = 0;
        $totalDpp = 0;
       // $totalSusut = 0;
		$totalPPN = 0;
		$totalPPh = 0;
		$downPayment = 0;
		$grandTotal = 0;
        

        $iddp = array();
        while ($row = mysqli_fetch_array($result)) {
            // while($row = $result->fetch_object()) {
			$dppAmount = 0;
            $qty = $row['send_weight'];
			$price = $row['unloading_price'];
		//	$dppAmount = $qty * $price;
             $dppAmount = $row['dpp'];

            $returnValue .= '<tr>';

            $returnValue .= '<td >' . $row['slip_no'] . '</td>';
            $returnValue .= '<td>' . $row['po_no'] . '</td>';
			
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['send_weight'], 2, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['unloading_price'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($dppAmount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['ppn'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['pph'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['amount'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_qty_claim'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_price_claim'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_amount'], 2, ".", ",") . '</td>';
			//$returnValue .= '<td style="text-align: right; ">' . number_format($row['total_amount'], 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
			
		$totalQty = $totalQty + $row['send_weight'];
        $totalDpp = $totalDpp + $dppAmount;
       // $totalSusut = $totalSusut + $row['shrink_amount'];
		$totalPPN = $totalPPN + $row['ppn'];
		$totalPPh = $totalPPh + $row['pph'];
		$totalAmount = $totalAmount + $row['amount'];
		$invoiceBankId = $row['invoiceBankId'];
		$invoiceCurrencyId = $row['currency'];
		$invoiceKurs = $row['kurs'];
		$downPayment = $row['totalDp'];
        }

        $returnValue .= '</tbody>';
        $returnValue .= '<tfoot>';
        
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total Quantity</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalQty, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total DPP</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalDpp, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		/*$returnValue .= '<tr>';
        $returnValue .= '<td colspan="11" style="text-align: right;">Total Susut</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalSusut, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';*/
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total PPN</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Total PPh</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Down Payment</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';


        $grandTotal = ($totalAmount-$downPayment);
        // echo " EA " . $grandTotal . " | " . $totalAmount;
        


        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="ppnInvoice" name="ppnInvoice" value="' . round($totalPPN, 10) . '" />';
        $returnValue .= '<input type="hidden" id="pphInvoice" name="pphInvoice" value="' . round($totalPPh, 10) . '" />';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalDpp, 10) . '" />';
		$returnValue .= '<input type="hidden" id="totalAmount" name="totalAmount" value="' . round($totalAmount, 10) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . round($downPayment , 10). '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 10) . '" />';
		$returnValue .= '<input type="hidden" id="invoiceBankId" name="invoiceBankId" value="' . $invoiceBankId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceCurrencyId" name="invoiceCurrencyId" value="' . $invoiceCurrencyId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceKurs" name="invoiceKurs" value="' . $invoiceKurs . '" />';
		
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function refreshInvoiceOA($invoiceOAId, $paymentMethod)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pp.vendor_bank_id AS invoiceBankId, t.`slip_no`, c.`po_no`, 
            CASE WHEN pp.payment_method = 2 THEN pp.price ELSE t.freight_price END AS freight_price,
            CASE WHEN pp.payment_method = 2 THEN pp.total_qty ELSE payo.qty END AS freight_quantity, 
            t.freight_price AS price,
        CASE WHEN pp.payment_method = 2 THEN pp.total_dpp ELSE ROUND(payo.dpp,10) END AS dpp,
        CASE WHEN pp.payment_method = 2 THEN pp.total_dpp ELSE ROUND(payo.total_amount,10) END AS dpp_shrink,
        CASE WHEN pp.payment_method = 2 THEN pp.total_ppn_amount ELSE ROUND((payo.total_amount) * (payo.ppn_value/100),10) END AS ppn,
        CASE WHEN pp.payment_method = 2 THEN pp.total_pph_amount ELSE ROUND((payo.total_amount) * (payo.pph_value/100),10) END AS pph,
        CASE WHEN pp.payment_method = 2 THEN pp.total_dpp ELSE (payo.dpp) END AS amount,
        -- ((ROUND(payo.amount,10) + ROUND((payo.amount) * (f.ppn/100),10) - ROUND((payo.amount) * (f.pph/100),10))) END AS amount,
        COALESCE(ts.`trx_shrink_claim`,0) AS shrink_price_claim,    
        payo.additional_shrink as hsw_amt_claim, payo.shrink as shrink_amount, -- COALESCE(ts.amt_claim,0) AS shrink_amount, COALESCE(hsw.amt_claim) as hsw_amt_claim,
        ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
        WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
        WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
        WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)ELSE 0 END,10) AS shrink_qty_claim,
        CASE WHEN pp.payment_method = 2  THEN pp.grand_total ELSE ROUND((payo.total_amount + (payo.total_amount * (f.ppn/100))) - (payo.total_amount * (f.pph/100)),10) END AS total_amount,
        -- ((ROUND(payo.amount,10) + ROUND((payo.amount) * (f.ppn/100),10) - ROUND((payo.amount) * (f.pph/100),10)) - COALESCE(ts.amt_claim,0)) END AS total_amount,
        pp.idPP, 
        COALESCE((SELECT SUM(pdp.settle_amount) FROM pengajuan_payment_dp pdp WHERE pdp.idpp = ip.idpp),0) AS dpAmount
        FROM invoice_notim ip
        LEFT JOIN pengajuan_payment pp ON pp.`idPP` = ip.idPP
        LEFT JOIN payment_oa payo ON payo.idpp = ip.idpp
        LEFT JOIN `transaction` t ON t.transaction_id = payo.transaction_id
        LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
        LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
        LEFT JOIN freight f ON f.`freight_id` = ip.freightId
        LEFT JOIN transaction_shrink_weight ts ON t.transaction_id = ts.transaction_id
        LEFT JOIN transaction_additional_shrink hsw ON t.transaction_id = hsw.transaction_id
        WHERE ip.inv_notim_id = {$invoiceOAId} ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
							<tr>
								<th>Slip No</th>
								<th>PO No.</th>
								<th>Qty</th>
								<th>Price</th>
								<th>Amount</th>
								<th>Shrink Qty Claim</th>
								<th>Shrink Price Claim</th>
								<th>Shrink Amount</th>
                                <th>Additional Shrink</th>
								<th>Total DPP</th>
							</tr>
						</thead>';
        $returnValue .= '<tbody>';
								//<th>PPN </th>
								//<th>PPh</th>
								//<th>Total Amount</th>

        $totalQty = 0;
        $totalDpp = 0;
        $totalSusut = 0;
		$totalPPN = 0;
		$totalPPh = 0;
		$downPayment = 0;
		$grandTotal = 0;
        $iddp = array();
        while ($row = mysqli_fetch_array($result)) {
            // while($row = $result->fetch_object()) {
            $amount1 = $row['amount'] + $row['shrink_amount'];
            $returnValue .= '<tr>';

            $returnValue .= '<td >' . $row['slip_no'] . '</td>';
            $returnValue .= '<td>' . $row['po_no'] . '</td>';
			
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['freight_quantity'], 0, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['freight_price'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['freight_quantity']*$row['freight_price'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_amount']/$row['shrink_price_claim'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_price_claim'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['shrink_amount'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['hsw_amt_claim'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['dpp_shrink'], 2, ".", ",") . '</td>';
          /*  $returnValue .= '<td style="text-align: right; ">' . number_format($row['ppn'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['pph'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['total_amount'], 2, ".", ",") . '</td>';*/
            $returnValue .= '</tr>';
			
		$totalQty = $totalQty + $row['freight_quantity'];
        $totalDpp = $totalDpp + $row['dpp'];
      //  $totaldpp_shrink = $totaldpp_shrink + $row['dpp_shrink'];
        $totalSusut = $totalSusut + $row['shrink_amount'];
        $totalAddSusut = $totalAddSusut + $row['hsw_amt_claim'];
		$totalPPN = $totalPPN + $row['ppn'];
		$totalPPh = $totalPPh + $row['pph'];
		$totalAmount = $totalAmount + $row['total_amount'];
		$invoiceBankId = $row['invoiceBankId'];
		$invoiceCurrencyId = $row['currency'];
		$invoiceKurs = $row['kurs'];
		$downPayment = $row['dpAmount'];
        }

        $returnValue .= '</tbody>';
        $returnValue .= '<tfoot>';
        
	// 	$returnValue .= '<tr>';
    //    $returnValue .= '<td colspan="9" style="text-align: right;">Total Quantity</td>';
    //    $returnValue .= '<td style="text-align: right;">' . number_format($totalQty, 2, ".", ",") . '</td>';
    //    $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
       $returnValue .= '<td colspan="9" style="text-align: right;">Total DPP</td>';
       $returnValue .= '<td style="text-align: right;">' . number_format($totalDpp, 2, ".", ",") . '</td>';
       $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
       $returnValue .= '<td colspan="9" style="text-align: right;">Total Susut</td>';
       $returnValue .= '<td style="text-align: right;">' . number_format($totalSusut + $totalAddSusut , 2, ".", ",") . '</td>';
       $returnValue .= '</tr>';

       $returnValue .= '<tr>';
       $returnValue .= '<td colspan="9" style="text-align: right;">Total Amount</td>';
       $returnValue .= '<td style="text-align: right;">' . number_format( $totalDpp - ($totalSusut + $totalAddSusut ), 2, ".", ",") . '</td>';
       $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="9" style="text-align: right;">Total PPN</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="9" style="text-align: right;">Total PPh</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="9" style="text-align: right;">Down Payment</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
		
        $grandTotal = $totalAmount - $downPayment;
		
		if($grandTotal < 0){
			$grandTotal = 0;
		}
		//$bankId = $row['bankId'];
        

        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="9" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="ppnInvoice" name="ppnInvoice" value="' . round($totalPPN, 10) . '" />';
        $returnValue .= '<input type="hidden" id="pphInvoice" name="pphInvoice" value="' . round($totalPPh, 10) . '" />';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalDpp, 10) . '" />';
		$returnValue .= '<input type="hidden" id="totalAmount" name="totalAmount" value="' . round($totalAmount, 10) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . round($downPayment , 10). '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 10) . '" />';
		$returnValue .= '<input type="hidden" id="invoiceBankId" name="invoiceBankId" value="' . $invoiceBankId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceCurrencyId" name="invoiceCurrencyId" value="' . $invoiceCurrencyId . '" />';
		$returnValue .= '<input type="hidden" id="invoiceKurs" name="invoiceKurs" value="' . $invoiceKurs . '" />';
		$returnValue .= '<input type="hidden" id="test" name="test" value="' . $invoiceOAId . '" />';

		
        $returnValue .= '</div>';
    }

    echo $returnValue;
}


function getInvoice($paymentFor, $gvId)
{
    global $myDatabase;
    $returnValue = '';


    $sql = "SELECT DISTINCT(i.invoice_id) , i.invoice_no   
            FROM invoice i
            LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id`
            WHERE id.general_vendor_id = {$gvId} AND i.company_id = {$_SESSION['companyId']} AND i.payment_status = 0 AND i.invoice_status = 0
            ORDER BY i.invoice_no ASC
";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows >= 1) {
        while ($row = $result->fetch_object()) {
            if ($row->invoice_id > 0) {
                //if($row->contract_amount  > $row->paid_amount)  {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->invoice_id . '||' . $row->invoice_no;
                } else {
                    $returnValue = $returnValue . '{}' . $row->invoice_id . '||' . $row->invoice_no;
                }
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getInvoiceNo($currentYearMonth)
{
    global $myDatabase;

    /*$sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($resultVendor !== false && $resultVendor->num_rows == 1) {
        $rowVendor = $resultVendor->fetch_object();
        $vendorCode = $rowVendor->vendor_code;
    }*/

    $checkInvoiceNo = 'INV/JPJ/' . $currentYearMonth;
    /* if($contractSeq != "") {
        $poNo = $checkInvoiceNo .'/'. $contractSeq;
    } else {*/
    $sql = "SELECT invoice_no FROM invoice WHERE company_id = {$_SESSION['companyId']} AND invoice_no LIKE '{$checkInvoiceNo}%' ORDER BY invoice_id DESC LIMIT 1";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        $splitInvoiceNo = explode('/', $rowInvoice->invoice_no);
        $lastExplode = count($splitInvoiceNo) - 1;
        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
        $InvoiceNo = $checkInvoiceNo . '/' . $nextInvoiceNo;
    } else {
        $InvoiceNo = $checkInvoiceNo . '/1';

    }

    echo $InvoiceNo;
}

function getAccountInvoice($invoiceType)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($invoiceType == 7) {
        $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
    }

    $sql = "SELECT acc.account_id, CONCAT(acc.account_name) AS account_full
            FROM account acc {$joinProperty}
            WHERE acc.status = 0 AND acc.account_type = {$invoiceType} AND acc.account_no != 210105";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->account_id . '||' . $row->account_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->account_id . '||' . $row->account_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}
function getAccountInvoicePrediksi($shipmentId1, $prediksiDetailId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if($prediksiDetailId == 0){
    $sql = "SELECT acc.account_id , CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,acc.account_name)  AS account_full
            FROM accrue_prediction ap
            LEFT JOIN accrue_prediction_detail apd ON apd.prediction_id = ap.prediction_id
            INNER JOIN account acc ON acc.account_id = apd.account_id
            WHERE ap.shipment_id = {$shipmentId1} AND apd.status IN (0,2) AND journal_status != 2";
    }else{
        $sql = "SELECT acc.account_id , CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,acc.account_name)  AS account_full
                FROM accrue_prediction ap
                LEFT JOIN accrue_prediction_detail apd ON apd.prediction_id = ap.prediction_id
                INNER JOIN account acc ON acc.account_id = apd.account_id
        WHERE ap.shipment_id = {$shipmentId1} AND apd.status = 1 AND apd.prediction_detail_id = {$prediksiDetailId} AND journal_status != 2";
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->account_id . '||' . $row->account_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->account_id . '||' . $row->account_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getGVprediksi($shipmentId, $accountId, $prediksiDetailId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if($prediksiDetailId == 0){
    $sql = "  SELECT gv.general_vendor_id, CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,gv.general_vendor_name) as gVendorName, tb.id as costId, CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,tb.tipe_biaya) as tipeBiaya
            FROM accrue_prediction ap
            LEFT JOIN accrue_prediction_detail apd ON apd.prediction_id = ap.prediction_id
            INNER JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
            LEFT JOIN mst_tipe_biaya tb ON tb.id = apd.cost_name
            WHERE ap.shipment_id = {$shipmentId} AND apd.account_id = {$accountId} AND apd.journal_status != 2 ";
    }else{
        $sql = "  SELECT gv.general_vendor_id, CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,gv.general_vendor_name) as gVendorName, tb.id as costId, CONCAT(SUBSTRING(apd.`generate_code_detail`, 15, 15), '-' ,tb.tipe_biaya) as tipeBiaya
                    FROM accrue_prediction ap
                    LEFT JOIN accrue_prediction_detail apd ON apd.prediction_id = ap.prediction_id
                    INNER JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
                    LEFT JOIN mst_tipe_biaya tb ON tb.id = apd.cost_name
                WHERE ap.shipment_id = {$shipmentId} AND apd.account_id = {$accountId} AND apd.prediction_detail_id = {$prediksiDetailId} AND apd.journal_status != 2 ";

    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->general_vendor_id . '||' . $row->gVendorName . '||' . $row->costId . '||' . $row->tipeBiaya;
            } else {
                $returnValue = $returnValue . '{}' . $row->general_vendor_id . '||' . $row->gVendorName .  '||' . $row->costId . '||' . $row->tipeBiaya;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}


function getPoInvoice($accountId, $stockpileId2)
{
    global $myDatabase;
    $returnValue = '';

    $sqlAcc = "SELECT account_no FROM account WHERE account_id = {$accountId}";
    $resultAcc = $myDatabase->query($sqlAcc, MYSQLI_STORE_RESULT);
    if ($resultAcc->num_rows == 1) {
        $rowAcc = $resultAcc->fetch_object();

        $accNo = $rowAcc->account_no;
    }

    if ($accNo == 520900 || $accNo == 521000) {
        $sql = "SELECT c.contract_id, c.po_no 
                FROM contract c
                LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
                WHERE c.invoice_status = 0 AND c.contract_status != 2 AND sc.`stockpile_id` = {$stockpileId2}
                ORDER BY c.contract_id DESC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    } else {
        $sql = "SELECT c.contract_id, c.po_no 
                FROM contract c
                LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
                WHERE c.contract_status != 2 AND sc.`stockpile_id` = {$stockpileId2}
                ORDER BY c.contract_id DESC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->po_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getPO_OKS_AKT($gvId)
{
    global $myDatabase;
    $returnValue = '';

        // $sql = "SELECT c.contract_id, c.`contract_no`, c.po_no, ok.`oks_akt_id`, ok.`general_vendor_id`, ok.`vendor_id` 
        //     FROM oks_akt ok
        //     INNER JOIN contract c ON ok.`vendor_id`= c.`vendor_id`
        //     WHERE c.contract_status != 2 AND ok.`general_vendor_id` = {$gvId}
        //     AND c.`contract_id` NOT IN (SELECT contract_id FROM temp_oks_akt WHERE  contract_id=c.`contract_id` AND ok.oks_akt_id = id_oks_akt)
        //     GROUP BY c.`po_no` ORDER BY c.contract_id DESC";
        $sql = "SELECT c.contract_id, c.po_no FROM contract c
                LEFT JOIN oks_akt ok ON ok.vendor_id = c.`vendor_id`
                WHERE contract_id NOT IN (SELECT contract_id FROM temp_oks_akt WHERE contract_id = c.`contract_id` AND ok.oks_akt_id = id_oks_akt AND status = 1)
                AND ok.general_vendor_id = {$gvId} ORDER BY contract_id DESC";
                // AND c.vendor_id = {$pksId} ORDER BY contract_id DESC";
            //  -- AND ok.`general_vendor_id` = {$gvId} ORDER BY contract_id DESC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  // echo $sql;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {

            if ($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->po_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getContractPO($currentYearMonth, $contractType, $vendorId, $contractSeq)
{
    global $myDatabase;

    $sql = "SELECT * FROM vendor WHERE vendor_id = {$vendorId}";
    $resultVendor = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultVendor !== false && $resultVendor->num_rows == 1) {
        $rowVendor = $resultVendor->fetch_object();
        $vendorCode = $rowVendor->vendor_code;
    }

    $checkPoNo = $contractType . '-' . $vendorCode . '-' . $currentYearMonth;
    if ($contractSeq != "") {
        $poNo = $checkPoNo . '-' . $contractSeq;
    } else {
        $sql = "SELECT po_no FROM contract WHERE company_id = {$_SESSION['companyId']} AND po_no LIKE '{$checkPoNo}%' ORDER BY contract_id DESC LIMIT 1";
        $resultPo = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($resultPo->num_rows == 1) {
            $rowPo = $resultPo->fetch_object();
            $splitPoNo = explode('-', $rowPo->po_no);
            $lastExplode = count($splitPoNo) - 1;
            $nextPoNo = ((float)$splitPoNo[$lastExplode]) + 1;
            $poNo = $checkPoNo . '-' . $nextPoNo;
        } else {
            $poNo = $checkPoNo . '-1';
        }
    }

    echo $poNo;
}

function getGeneratedContract($contract_no)
{
    global $myDatabase;

    $sql = "SELECT LPAD(RIGHT(contract_no, 1) + 1, 1, '0') AS next_id FROM contract WHERE contract_no LIKE '{$contract_no}%' ORDER BY contract_id DESC LIMIT 1";
    $resultContract = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultContract !== false && $resultContract->num_rows == 1) {
        $rowContract = $resultContract->fetch_object();
        $nextContractNo = $rowContract->next_id;
        // $lastExplode = count($splitContractNo) - 1;
        // $nextContractNo = ((float) $splitContractNo[$lastExplode]) + 1;
        $coNo = $contract_no . '-' . $nextContractNo;
    } else {
        $coNo = $contract_no . '-1';
    }

    echo $coNo;
}


function getUnloadingCost($stockpileId, $currentDate, $unloadingCostId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = "";

    if ($unloadingCostId == '') {
        $whereProperty = " AND DATE_FORMAT(entry_date, '%d/%m/%Y %H:%i:%s') <= STR_TO_DATE('{$currentDate}', '%d/%m/%Y %H:%i:%s') ";
    } elseif ($unloadingCostId != 'NONE') {
        $whereProperty = " AND unloading_cost_id = {$unloadingCostId} ";
    }

    $sql = "SELECT DISTINCT(v.vehicle_id) AS vehicle_id, v.vehicle_name, 
                (SELECT unloading_cost_id FROM unloading_cost 
                    WHERE vehicle_id = v.vehicle_id
                    AND stockpile_id = {$stockpileId}
                    {$whereProperty}
                    ORDER BY entry_date DESC LIMIT 1
                ) AS unloading_cost_id
            FROM vehicle v
            INNER JOIN unloading_cost uc
                ON uc.vehicle_id = v.vehicle_id
            WHERE uc.stockpile_id = {$stockpileId}
            AND uc.company_id = {$_SESSION['companyId']}
			AND uc.status = 0
            ORDER BY v.vehicle_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->unloading_cost_id . '||' . $row->vehicle_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->unloading_cost_id . '||' . $row->vehicle_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}


function getVendor($stockpileId, $newVendorId)
{
    global $myDatabase;
    $returnValue = '';
    $unionSql = '';

    if ($newVendorId != 0 || $newVendorId != '') {
        $unionSql = " UNION SELECT v.vendor_id, v.vendor_name
                    FROM vendor v WHERE v.vendor_id = {$newVendorId}";
    }

    $sql = "SELECT DISTINCT(con.vendor_id), CONCAT(v.`vendor_code`, ' - ', v.vendor_name) AS vendor_name
            FROM stockpile_contract sc
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            INNER JOIN vendor v
                ON v.vendor_id = con.vendor_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.company_id = {$_SESSION['companyId']}
            {$unionSql}
			AND v.active = 1
            ORDER BY vendor_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSpBlockData($stockpileId, $ggl, $rsb, $rg, $un, $newblock)
{
    global $myDatabase;
    $returnValue = '';
    $unionSql = '';
    $whereProperty = '';

    // if ($newblock != 0 || $newblock != '') {
    //     $unionSql = " UNION SELECT spb.sp_block_id, spb.sp_block
    //                 FROM MST_SP_Block spb WHERE spb.sp_block_id = {$newblock}";
    // }

    if($rg == 1){ //RSB + GGL
        //echo "TEST 1";
        $whereProperty = "AND ggl=1 AND rsb = 1";
    }else if($rsb == 1){ //RSB
        $whereProperty = "AND rsb = 1 AND ggl = 0";
    }else if($ggl == 1 ){ //GGL
        $whereProperty = " AND ggl = 1 AND rsb = 0";
    }else if($un == 1){ //UNCERTIFIED
        $whereProperty = "AND ggl = 0 AND rsb = 0";
        //echo "TEST2";
    }

    $sql = "SELECT DISTINCT(spb.sp_block_id) as sp_block_id, 
            CONCAT(CASE WHEN spb.ggl = 1 AND spb.rsb = 0 THEN 'GGL' 
                        WHEN spb.rsb = 1 AND spb.ggl = 0 THEN 'RSB' 
                        WHEN spb.rsb = 1 AND spb.ggl = 1 THEN '(RSB & GGL)' 
                        WHEN spb.rsb = 0 AND spb.ggl = 0 THEN 'Uncertified'  ELSE '-' 
                    END, ' - ', sp_block) AS block_name
            FROM MST_SP_Block spb
            WHERE spb.stockpile_id = {$stockpileId} {$whereProperty}
            ORDER BY sp_block ASC";
          // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->sp_block_id . '||' . $row->block_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->sp_block_id . '||' . $row->block_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSupplier()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT vendor_id, vendor_name FROM vendor ORDER BY vendor_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getFreightCost($stockpileId, $vendorId, $currentDate, $freightCostId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = "";
    if ($trxDate == '') {
        $wheretrx = '';
    } else {
        $wheretrx = " AND active_from <= STR_TO_DATE('{$trxDate}','%d/%m/%Y') ";
    }


    if ($freightCostId == '') {
        $whereProperty = " AND vendor_id = {$vendorId} ";
    } else {
        $whereProperty = " AND freight_cost_id = {$freightCostId} ";
    }
    if ($vendorId == 38) {
        $sql = "SELECT DISTINCT(f.freight_id) AS freight_id, CONCAT(f.freight_supplier, ' - ', v.vendor_code, ' - ', f.freight_code, ' - ', FORMAT(fc.`price_converted`,2)) AS freight_code, 
(SELECT freight_cost_id FROM freight_cost WHERE stockpile_id = fc.stockpile_id AND vendor_id = fc.vendor_id AND price_converted = fc.price_converted AND freight_id = fc.freight_id ORDER BY freight_cost_id  DESC LIMIT 1) AS freight_cost_id
            FROM freight f
            INNER JOIN freight_cost fc
                ON fc.freight_id = f.freight_id
            INNER JOIN vendor v
                ON v.vendor_id = fc.vendor_id
            WHERE fc.stockpile_id = {$stockpileId}
            AND fc.vendor_id = {$vendorId}
            AND fc.company_id = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY f.freight_code DESC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->freight_cost_id . '||' . $row->freight_code;
                } else {
                    $returnValue = $returnValue . '{}' . $row->freight_cost_id . '||' . $row->freight_code;
                }
            }
        }
    } else {
        $sql = "SELECT DISTINCT(f.freight_id) AS freight_id, CONCAT(f.freight_supplier, '-', v.vendor_code, '-', f.freight_code) AS freight_code, 
                (SELECT freight_cost_id FROM freight_cost 
                    WHERE freight_id = f.freight_id
					AND stockpile_id = {$stockpileId}
                    {$whereProperty}
					{$wheretrx}
                    ORDER BY entry_date DESC LIMIT 1
                ) AS freight_cost_id
            FROM freight f
            INNER JOIN freight_cost fc
                ON fc.freight_id = f.freight_id
            INNER JOIN vendor v
                ON v.vendor_id = fc.vendor_id
            WHERE fc.stockpile_id = {$stockpileId}
            AND fc.vendor_id = {$vendorId}
            AND fc.company_id = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY f.freight_code ASC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->freight_cost_id . '||' . $row->freight_code;
                } else {
                    $returnValue = $returnValue . '{}' . $row->freight_cost_id . '||' . $row->freight_code;
                }
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getSales($customerId, $stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT s.sales_id, s.sales_no
            FROM sales s 
            WHERE customer_id = {$customerId}
			AND stockpile_id = {$stockpileId}
            AND sales_status <> 1 
            AND company_id = {$_SESSION['companyId']}
            ORDER BY s.sales_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->sales_id . '||' . $row->sales_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->sales_id . '||' . $row->sales_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getShipment($salesId, $shipmentId)
{
    global $myDatabase;
    $returnValue = '';

    if ($shipmentId == '' || $shipmentId == 0) {
        $sql = "SELECT sh.shipment_id, sh.shipment_code
                FROM shipment sh WHERE sh.sales_id = {$salesId} AND sh.shipment_status = 0 ORDER BY sh.shipment_id ASC";
    } else {
        $sql = "SELECT sh.shipment_id, sh.shipment_code
                FROM shipment sh WHERE sales_id = {$salesId} ORDER BY sh.shipment_id ASC";
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_code;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_code;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getShipmentPayment($customerId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT s.sales_id, s.sales_no, sh.shipment_no 
            FROM sales s INNER JOIN shipment sh ON sh.sales_id = s.sales_id WHERE s.customer_id = {$customerId} 
			AND sh.`payment_id` IS NULL ORDER BY s.sales_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->sales_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->sales_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getStockpileContractTransaction($stockpileId, $vendorId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sc.stockpile_contract_id, con.po_no
            FROM stockpile_contract sc
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.vendor_id = {$vendorId}
            AND con.company_id = {$_SESSION['companyId']}
			
            ORDER BY con.po_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->po_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getStockpileContract($stockpileId, $vendorId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sc.stockpile_contract_id, con.po_no
            FROM stockpile_contract sc
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.vendor_id = {$vendorId}
            AND con.company_id = {$_SESSION['companyId']}
			AND con.contract_status != 2
            ORDER BY con.po_no DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->po_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->po_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getContractDetail($stockpileContractId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id =  {$stockpileContractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $contractId = $row->contract_id;

        $sql = "SELECT con.contract_no, con.contract_type, con.vendor_id,
                FORMAT(((SELECT COALESCE(SUM(sc.quantity),0) FROM stockpile_contract sc WHERE sc.contract_id = con.contract_id) - (SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = con.contract_id)) - 
                (SELECT CASE WHEN c.contract_type = 'C' THEN COALESCE(SUM(t.quantity), 0)
                ELSE COALESCE(SUM(t.send_weight), 0) END 
                FROM TRANSACTION t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.`stockpile_contract_id`
                LEFT JOIN contract c ON c.contract_id = sc.contract_id
                WHERE sc.`contract_id` = {$contractId}), 2) AS quantity_available
            FROM stockpile_contract sc
            LEFT JOIN contract con
                ON con.contract_id = sc.contract_id
            WHERE sc.stockpile_contract_id = {$stockpileContractId}";
            echo $sql;
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $returnValue = $row->contract_type . '||' . $row->contract_no . '||' . $row->quantity_available . '||' . $row->vendor_id;
        }
    }
    echo $returnValue;
}

function getFreightDetail($freightCostId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT tax.tax_category, tax.tax_value, f.pph_tax_id,CONCAT(cur.currency_code, ' ', FORMAT(fc.price, 2)) AS price,
            shrink_tolerance_kg AS tolerance, shrink_claim as claim, 
            shrink_tolerance_persen as persen
            FROM freight_cost fc
            INNER JOIN currency cur
                ON cur.currency_id = fc.currency_id
            INNER JOIN freight f
		        ON f.freight_id = fc.freight_id
	        INNER JOIN tax tax
		        ON f.pph_tax_id = tax.tax_id
            WHERE fc.freight_cost_id = {$freightCostId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo " e -> " . $sql;
    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->price . '||' . 
                        $row->tolerance. '||' . 
                        $row->claim. '||' . 
                        $row->persen. '||' .
                        $row->tax_category. '||' .
                        $row->tax_value;
    }

    echo $returnValue;
}


function getUnloadingDetail($unloadingCostId)
{
    global $myDatabase;
    $returnValue = '';

    //REPLACE(REPLACE(REPLACE(FORMAT(uc.price, 2), '.', '@'), ',', '.'), '@', ',')
    $sql = "SELECT CONCAT(cur.currency_code, ' ', FORMAT(uc.price, 2)) AS price
            FROM unloading_cost uc
            INNER JOIN currency cur
                ON cur.currency_id = uc.currency_id
            WHERE uc.unloading_cost_id = {$unloadingCostId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->price;
    }

    echo $returnValue;
}

function getShipmentDetail($shipmentId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT cust.customer_name, FORMAT(s.quantity - (SELECT COALESCE(SUM(quantity), 0) FROM shipment WHERE sales_id = s.sales_id), 2) AS quantity_available
            FROM sales s
            INNER JOIN customer cust
                ON cust.customer_id = s.customer_id
            WHERE s.sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = {$shipmentId})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $returnValue = $row->customer_name . '||' . $row->quantity_available;
    }

    echo $returnValue;
}

function getCustomer()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT cust.customer_id, cust.customer_name
            FROM customer cust ORDER BY cust.customer_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->customer_id . '||' . $row->customer_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->customer_id . '||' . $row->customer_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getLabor()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT l.labor_id, l.labor_name
            FROM labor l ORDER BY l.labor_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->labor_id . '||' . $row->labor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->labor_id . '||' . $row->labor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getGeneralVendor()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT * FROM general_vendor 
                    ORDER BY general_vendor_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->general_vendor_id . '||' . $row->general_vendor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->general_vendor_id . '||' . $row->general_vendor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getAccount($paymentFor, $paymentMethod, $paymentType)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($paymentFor == 0) {
        if ($paymentMethod == 1) {
            if ($paymentType == 1) {
                $whereProperty = " AND acc.account_no in (210102,150120) ";
            } elseif ($paymentType == 2) {
                $whereProperty = " AND acc.account_no in (210102) ";
            }
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_no in (130001, 210102) ";
        }
    } elseif ($paymentFor == 1) {
        if ($paymentType == 1) {
            //Sales
            if ($paymentMethod == 1) {
                $whereProperty = " AND acc.account_no in (120000, 210211) ";
            } elseif ($paymentMethod == 2) {
                $whereProperty = " AND acc.account_no in (210200) ";
            }
        } elseif ($paymentType == 2) {
            //Curah
            if ($paymentMethod == 1) {
                $whereProperty = " AND acc.account_no in (210101) ";
            } elseif ($paymentMethod == 2) {
                $whereProperty = " AND acc.account_no in (130002) ";
            }
        }
    } elseif ($paymentFor == 2) {
        if ($paymentMethod == 1) {
            $whereProperty = " AND acc.account_no in (210103) ";
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_no in (130003) ";
        }
    } elseif ($paymentFor == 3) {
        if ($paymentMethod == 1) {
            $whereProperty = " AND acc.account_no in (210104) ";
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_no in (130004) ";
        }
    } elseif ($paymentFor == 7) {
        $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
    } elseif ($paymentFor == 8) {
        $whereProperty = " AND acc.account_no in (210105) ";
    } elseif ($paymentFor == 9) {
        //$whereProperty = " AND acc.account_no in (210106,130006) ";
        if ($paymentMethod == 1) {
            $whereProperty = " AND acc.account_no in (210106) ";
        } elseif ($paymentMethod == 2) {
            $whereProperty = " AND acc.account_no in (130006) ";
        }
    }

    $sql = "SELECT acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
            FROM account acc {$joinProperty}
            WHERE acc.status = 0 AND acc.account_type = {$paymentFor} {$whereProperty}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->account_id . '||' . $row->account_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->account_id . '||' . $row->account_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getBank()
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full 
            FROM bank b
            INNER JOIN currency cur
                ON cur.currency_id = b.currency_id
            ORDER BY b.bank_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->bank_id . '||' . $row->bank_full;
            } else {
                $returnValue = $returnValue . '{}' . $row->bank_id . '||' . $row->bank_full;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function setSlip($stockpileContractId, $checkedSlips)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT t.*
            FROM transaction t
            WHERE t.stockpile_contract_id = {$stockpileContractId}
            AND t.payment_id IS NULL";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th></th><th>Slip No.</th><th>Quantity</th><th>Currency</th><th>Unit Price</th><th>Total</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        while ($row = $result->fetch_object()) {
            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $row->stockpile_contract_id . ');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $row->stockpile_contract_id . ');" checked /></td>';
                    $totalPrice = $totalPrice + ($row->unit_price * $row->quantity);
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlip(' . $row->stockpile_contract_id . ');" /></td>';
            }
            $returnValue .= '<td>' . $row->slip_no . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->quantity, 0, ".", ",") . '</td>';
            $returnValue .= '<td>IDR</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->unit_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format(($row->unit_price * $row->quantity), 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="5" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment FROM payment WHERE stockpile_contract_id = {$stockpileContractId} AND payment_method = 2 AND payment_status = 0";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $downPayment = $row->down_payment;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="5">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 0, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

            $grandTotal = $totalPrice - $downPayment;
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="5">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . $totalPrice . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . ($totalPrice - $downPayment) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function getPoNoCurah($stockpileId, $vendorId, $stockpileContractId)
{
    global $myDatabase;
    $returnValue = '';

    $sql2 = "SELECT cpd.*, con.contract_no, vc.vendor_curah_name, vc.vendor_curah_address, sc.stockpile_contract_id
        FROM stockpile_contract sc
        left join contract con ON sc.contract_id = con.contract_id
        left join contract_pks_detail cpd on con.contract_id = cpd.contract_id
        left join vendor_curah vc on cpd.vendor_curah_id = vc.vendor_curah_id
         WHERE sc.stockpile_id = {$stockpileId} 
         and sc.stockpile_contract_id = $stockpileContractId
         AND con.vendor_id = {$vendorId}
            AND con.company_id = {$_SESSION['companyId']}
			AND con.contract_status != 2
        ORDER BY cpd.entry_date DESC";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
    if ($result2->num_rows > 0) {
        while ($row2 = $result2->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row2->contract_pks_detail_id . '||' . $row2->vendor_curah_name;
            } else {
                $returnValue = $returnValue . '{}' . $row2->contract_pks_detail_id . '||' . $row2->vendor_curah_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
}

function getStockpileContractPayment($stockpileId, $vendorId, $paymentType)
{
    global $myDatabase;
    $returnValue = '';
    $paymentStatus = '';


    if ($paymentType != 1) {
        echo 'b';

        $sql = "SELECT DISTINCT(sc.stockpile_contract_id),con.po_no, con.price_converted * con.quantity AS contract_amount, v.ppn, v.pph,
                COALESCE((SELECT SUM(amount_converted) FROM payment WHERE stockpile_contract_id = sc.stockpile_contract_id AND payment_status = 0), 0) AS paid_amount
            FROM stockpile_contract sc
            LEFT JOIN transaction t
                ON t.stockpile_contract_id = sc.stockpile_contract_id
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
			INNER JOIN vendor v 
                ON con.vendor_id = v.vendor_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.vendor_id = {$vendorId}
            AND con.company_id = {$_SESSION['companyId']}
			AND con.payment_status = 0
			AND con.contract_status != 2
            ORDER BY con.po_no ASC";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {


                if ($row->contract_amount + ($row->contract_amount * ($row->ppn / 100)) > $row->paid_amount) {
                    //if($row->contract_amount  > $row->paid_amount)  {
                    if ($returnValue == '') {
                        $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->po_no;
                    } else {
                        $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->po_no;
                    }
                }

            }
        }
    } else {

        echo 'a';
        $sql = "SELECT sc.*, c.*
				FROM stockpile_contract sc
				LEFT JOIN contract c ON sc.`contract_id` = c.`contract_id`
				WHERE sc.`stockpile_id` = {$stockpileId}
				AND c.`vendor_id` = {$vendorId}
				AND c.company_id = {$_SESSION['companyId']}
				AND c.`adjustment` <> 0
				AND c.`adjustment_acc` != 52
				AND (SELECT COUNT(*) FROM payment WHERE payment_type = 1 AND payment_status = 0 AND stockpile_contract_id = sc.`stockpile_contract_id`) = 0";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {


                //if($row->contract_amount  > $row->paid_amount)  {
                if ($returnValue == '') {
                    $returnValue = '~' . $row->stockpile_contract_id . '||' . $row->po_no;
                } else {
                    $returnValue = $returnValue . '{}' . $row->stockpile_contract_id . '||' . $row->po_no;
                }


            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
        //echo $sql;
    }

    echo $returnValue;
}

function getVendorPayment($stockpileId, $contractType)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT DISTINCT(con.vendor_id), CONCAT(v.vendor_name,' - ', v.vendor_code) AS vendor_name
            FROM stockpile_contract sc
            LEFT JOIN transaction t
                ON t.stockpile_contract_id = sc.stockpile_contract_id
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            INNER JOIN vendor v
                ON v.vendor_id = con.vendor_id
            WHERE sc.stockpile_id = {$stockpileId}
            AND con.contract_type = '{$contractType}'
            AND t.payment_id IS NULL
            AND con.company_id = {$_SESSION['companyId']}
			AND v.active = 1
			AND (con.return_shipment IS NULL OR con.return_shipment = 0)
            ORDER BY vendor_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getFreightPayment($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT DISTINCT(fc.freight_id), CONCAT(f.freight_code,' - ', f.freight_supplier) AS freight_supplier
            FROM freight_cost fc
            LEFT JOIN transaction t
                ON t.freight_cost_id = fc.freight_cost_id
            INNER JOIN freight f
                ON f.freight_id = fc.freight_id
            INNER JOIN vendor v
                ON v.vendor_id = fc.vendor_id
            WHERE fc.stockpile_id = {$stockpileId}
            AND t.fc_payment_id IS NULL
            AND fc.company_id = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY freight_supplier ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->freight_id . '||' . $row->freight_supplier;
            } else {
                $returnValue = $returnValue . '{}' . $row->freight_id . '||' . $row->freight_supplier;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getLaborPayment($stockpileId)
{
    global $myDatabase;
    $returnValue = '';

//    $sql = "SELECT DISTINCT(t.labor_id), l.labor_name
//            FROM transaction t
//            INNER JOIN labor l
//                ON l.labor_id = t.labor_id
//            INNER JOIN unloading_cost uc
//                ON uc.unloading_cost_id = t.unloading_cost_id
//            WHERE uc.stockpile_id = {$stockpileId}
//            AND t.uc_payment_id IS NULL
//            AND uc.company_id = {$_SESSION['companyId']}
//            ORDER BY labor_name ASC";
    $sql = "SELECT DISTINCT(l.labor_id), l.labor_name
            FROM labor l
            LEFT JOIN transaction t
                ON l.labor_id = t.labor_id AND t.uc_payment_id IS NULL
            LEFT JOIN unloading_cost uc
                ON uc.unloading_cost_id = t.unloading_cost_id 
			WHERE l.active = 1 AND uc.stockpile_id = {$stockpileId} AND uc.company_id = {$_SESSION['companyId']}
			AND t.`notim_status` = 0 AND t.`slip_retur` IS NULL
            ORDER BY labor_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->labor_id . '||' . $row->labor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->labor_id . '||' . $row->labor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function refreshSummary($stockpileContractId, $paymentMethod, $ppn, $pph, $paymentType)
{
    global $myDatabase;
    $returnValue = '';
    if ($paymentType != 1) {
        $sql = "SELECT con.quantity, con.contract_no, cur.currency_code, con.price, con.price * con.quantity AS total_price,
					(
						SELECT COALESCE(
							SUM(amount)
						, 0) 
						FROM payment 
						WHERE stockpile_contract_id = sc.stockpile_contract_id
						AND payment_method = 2 AND payment_status = 0
						AND company_id = {$_SESSION['companyId']}
					) AS down_payment,
					(
						SELECT COALESCE(
							SUM(amount)
						, 0) 
						FROM payment 
						WHERE stockpile_contract_id = sc.stockpile_contract_id
						AND payment_method = 1 AND payment_type = 2 AND payment_status = 0
						AND company_id = {$_SESSION['companyId']}
					) AS payment,
					v.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
					v.pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value
				FROM stockpile_contract sc
				INNER JOIN contract con
					ON con.contract_id = sc.contract_id
				INNER JOIN currency cur
					ON cur.currency_id = con.currency_id
				INNER JOIN vendor v
					ON v.vendor_id = con.vendor_id
				LEFT JOIN tax txppn
					ON txppn.tax_id = v.ppn_tax_id
				LEFT JOIN tax txpph
					ON txpph.tax_id = v.pph_tax_id
				WHERE sc.stockpile_contract_id = {$stockpileContractId}
				
				AND con.company_id = {$_SESSION['companyId']}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows == 1) {
            $row = $result->fetch_object();

            $dppTotalPrice = 0;
            if ($row->pph_tax_id == 0) {
                $dppTotalPrice = $row->total_price;
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = $row->total_price / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->total_price;
                }
            }

            $totalPPN = 0;
            if ($row->ppn_tax_id != 0) {
                $totalPPN = $dppTotalPrice * ($row->ppn_tax_value / 100);
            }

            if ($ppn == 'NONE') {
                $ppnValue = $totalPPN;
            } elseif ($ppn != $totalPPN) {
                $ppnValue = $ppn;
            } else {
                $ppnValue = $totalPPN;
            }

            $totalPPh = 0;
            if ($row->pph_tax_id != 0) {
                $totalPPh = $dppTotalPrice * ($row->pph_tax_value / 100);
            }

            if ($pph == 'NONE') {
                $pphValue = $totalPPh;
            } elseif ($pph != $totalPPh) {
                $pphValue = $pph;
            } else {
                $pphValue = $totalPPh;
            }


            $returnValue = '<div class="span12 lightblue">';
            $returnValue .= '<p style="text-align: center; font-weight: bold;">Contract No: ' . $row->contract_no . '</p>';
            $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
            $returnValue .= '<thead><tr><th>Quantity</th><th>Currency</th><th>Unit Price</th><th>Total</th></tr></thead>';
            $returnValue .= '<tbody>';
            $returnValue .= '<tr>';
            $returnValue .= '<td style="text-align: right; width: 25%;">' . number_format($row->quantity, 2, ".", ",") . '</td>';
            $returnValue .= '<td>' . $row->currency_code . '</td>';
            $returnValue .= '<td style="text-align: right; width: 25%;">' . number_format($row->price, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 25%;">' . number_format($row->total_price, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '</tbody>';
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">DPP</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">Down Payment</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->down_payment, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">Payment</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->payment, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            if ($paymentMethod == 1) {
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="3" style="text-align: right;">PPN</td>';
                $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($ppnValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', ' . $paymentMethod . ', this, document.getElementById(\'pph\'));" /></td>';
                $returnValue .= '</tr>';
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="3" style="text-align: right;">PPh</td>';
                $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($pphValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', ' . $paymentMethod . ', document.getElementById(\'ppn\'), this);" /></td>';
                $returnValue .= '</tr>';
            } else {
                if ($ppnValue > 0) {
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="3" style="text-align: right;">PPN</td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($ppnValue, 2, ".", ",") . '</td>';
                    $returnValue .= '</tr>';
                    //                $returnValue .= '<input type="hidden" id="ppn" name="ppn" value="'. $ppnValue .'" />';
                } else {
                    //                $returnValue .= '<input type="hidden" id="ppn" name="ppn" value="0" />';
                }

                if ($pphValue > 0) {
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="3" style="text-align: right;">PPh</td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($pphValue, 2, ".", ",") . '</td>';
                    $returnValue .= '</tr>';
                    //                $returnValue .= '<input type="hidden" id="pph" name="pph" value="'. $pphValue .'" />';
                } else {
                    //                $returnValue .= '<input type="hidden" id="pph" name="pph" value="0" />';
                }
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">Pembulatan</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '</tfoot>';
            $returnValue .= '</table>';
            $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $row->down_payment . '" />';
            $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . ((round($dppTotalPrice, 0) + round($ppnValue, 0)) - round($pphValue, 0)) . '" />';
            $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . ((round($dppTotalPrice, 0) + round($ppnValue, 0)) - round($pphValue, 0) - $row->down_payment - $row->payment) . '" />';
            $returnValue .= '</div>';
        }
    } else {
        $sql = "SELECT c.`quantity`, c.`contract_no`, c.price_converted, c.adjustment_notes, c.`adjustment`, c.`adjustment_ppn`, cur.currency_code,
				ROUND(c.`price_converted` * (SELECT COALESCE(SUM(adjustment),0) FROM contract_adjustment WHERE contract_id = c.contract_id),2) AS dpp,
				CASE WHEN (SELECT adjustment_ppn FROM contract_adjustment WHERE contract_id = c.contract_id) = 1 THEN (SELECT ppn FROM vendor WHERE vendor_id = c.`vendor_id`)
				ELSE 0 END AS ppn_value
				FROM stockpile_contract sc
				LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
				LEFT JOIN currency cur ON cur.currency_id = c.`currency_id`
				WHERE sc.`stockpile_contract_id` = {$stockpileContractId}
				AND c.company_id = {$_SESSION['companyId']}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows == 1) {
            $row = $result->fetch_object();

            /*$dppTotalPrice = 0;
			if($row->pph_tax_id == 0) {
				$dppTotalPrice = $row->total_price;
			} else {
				if($row->pph_tax_category == 1) {
					$dppTotalPrice = $row->total_price / ((100 - $row->pph_tax_value) / 100);
				} else {
					$dppTotalPrice = $row->total_price;
				}
			}*/

            $totalPPN = 0;
            if ($row->ppn_value != 0) {
                $totalPPN = $row->dpp * ($row->ppn_value / 100);
            }
            $total = $row->dpp + $totalPPN;

            if ($ppn == 'NONE') {
                $ppnValue = $totalPPN;
            } elseif ($ppn != $totalPPN) {
                $ppnValue = $ppn;
            } else {
                $ppnValue = $totalPPN;
            }

            $pphValue = 0;


            $returnValue = '<div class="span12 lightblue">';
            $returnValue .= '<p style="text-align: center; font-weight: bold;">Contract No: ' . $row->contract_no . '</p>';
            $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
            $returnValue .= '<thead><tr><th>Quantity</th><th>Currency</th><th>Unit Price</th><th>Adjustment Notes</th><th>Quantity Adjustment</th><th>DPP</th><th>PPN</th><th>Total</th></tr></thead>';
            $returnValue .= '<tbody>';
            $returnValue .= '<tr>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->quantity, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: left; width: 10%;">' . $row->currency_code . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->price_converted, 4, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: left; width: 30%;">' . $row->adjustment_notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->adjustment, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->dpp, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($total, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '</tbody>';
            /*$returnValue .= '<tfoot>';
			$returnValue .= '<tr>';
			$returnValue .= '<td colspan="3" style="text-align: right;">DPP</td>';
			$returnValue .= '<td style="text-align: right;">'. number_format($dppTotalPrice, 2, ".", ",") .'</td>';
			$returnValue .= '</tr>';
			$returnValue .= '<tr>';
			$returnValue .= '<td colspan="3" style="text-align: right;">Down Payment</td>';
			$returnValue .= '<td style="text-align: right;">'. number_format($row->down_payment, 2, ".", ",") .'</td>';
			$returnValue .= '</tr>';
			$returnValue .= '<tr>';
			$returnValue .= '<td colspan="3" style="text-align: right;">Payment</td>';
			$returnValue .= '<td style="text-align: right;">'. number_format($row->payment, 2, ".", ",") .'</td>';
			$returnValue .= '</tr>';*/

            if ($paymentMethod == 1) {
                //$returnValue .= '<tr>';
                //$returnValue .= '<td colspan="3" style="text-align: right;">PPN</td>';
                $returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($ppnValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', ' . $paymentMethod . ', this, document.getElementById(\'pph\'));" />';
                //$returnValue .= '</tr>';
                //$returnValue .= '<tr>';
                //$returnValue .= '<td colspan="3" style="text-align: right;">PPh</td>';
                $returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($pphValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', ' . $paymentMethod . ', document.getElementById(\'ppn\'), this);" />';
                //$returnValue .= '</tr>';
            } else {
                if ($ppnValue > 0) {
                    //$returnValue .= '<tr>';
                    //$returnValue .= '<td colspan="3" style="text-align: right;">PPN</td>';
                    //$returnValue .= '<td style="text-align: right;">'. number_format($ppnValue, 2, ".", ",") .'</td>';
                    //$returnValue .= '</tr>';
                    //                $returnValue .= '<input type="hidden" id="ppn" name="ppn" value="'. $ppnValue .'" />';
                } else {
                    //                $returnValue .= '<input type="hidden" id="ppn" name="ppn" value="0" />';
                }

                if ($pphValue > 0) {
                    //$returnValue .= '<tr>';
                    //$returnValue .= '<td colspan="3" style="text-align: right;">PPh</td>';
                    //$returnValue .= '<td style="text-align: right;">'. number_format($pphValue, 2, ".", ",") .'</td>';
                    //$returnValue .= '</tr>';
                    //                $returnValue .= '<input type="hidden" id="pph" name="pph" value="'. $pphValue .'" />';
                } else {
                    //                $returnValue .= '<input type="hidden" id="pph" name="pph" value="0" />';
                }
            }
            /*$returnValue .= '<tr>';
			$returnValue .= '<td colspan="3" style="text-align: right;">Grand Total</td>';
			$returnValue .= '<td style="text-align: right;">'. number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 2, ".", ",") .'</td>';
			$returnValue .= '</tr>';
			$returnValue .= '<tr>';
			$returnValue .= '<td colspan="3" style="text-align: right;">Pembulatan</td>';
			$returnValue .= '<td style="text-align: right;">'. number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 0, ".", ",") .'</td>';
			$returnValue .= '</tr>';
			$returnValue .= '</tfoot>';*/
            $returnValue .= '</table>';
            //$returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="'. $row->down_payment .'" />';
            //$returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="'. ((round($dppTotalPrice, 0) + round($ppnValue, 0)) - round($pphValue, 0)) .'" />';
            $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($total, 0) . '" />';
            $returnValue .= '</div>';
        }
    }

    echo $returnValue;
}

function setExchangeRate($bankId, $currencyId, $journalCurrencyId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT b.currency_id, cur.currency_code
            FROM bank b
            INNER JOIN currency cur
                ON cur.currency_id = b.currency_id
            WHERE b.bank_id = {$bankId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $row = $result->fetch_object();
        $bankCurrencyId = $row->currency_id;
        $bankCurrencyCode = $row->currency_code;
    }

    if ($currencyId != 0) {
        $sql = "SELECT cur.currency_id, cur.currency_code
                FROM currency cur
                WHERE cur.currency_id = {$currencyId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $currencyCode = $row->currency_code;
        }
    } else {
        $currencyId = $bankCurrencyId;
    }

    $returnValue = '|' . $bankCurrencyId . '|' . $currencyId . '|' . $journalCurrencyId;

    echo $returnValue;
}

function setSlipCurah($stockpileId, $vendorId, $contractCurah, $checkedSlips, $checkedSlipsDP, $paymentFrom1, $paymentTo1) {
    global $myDatabase;
    $returnValue = '';
	
	if($checkedSlips == ''){
	for ($i = 0; $i < sizeof($contractCurah); $i++) {
                        if($contractCurahs == '') {
                            $contractCurahs .=  $contractCurah[$i];
                        } else {
                            $contractCurahs .= ','. $contractCurah[$i];
                        }
                    }
	}else{
		$contractCurahs = $contractCurah;
	}

    $sql = "SELECT t.*, con.vendor_id,con.contract_no,con.po_no,
                v.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value
            FROM transaction t
            INNER JOIN stockpile_contract sc
                ON sc.stockpile_contract_id = t.stockpile_contract_id
            INNER JOIN contract con
                ON con.contract_id = sc.contract_id
            INNER JOIN vendor v
                ON v.vendor_id = con.vendor_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = v.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.curah_tax_id
            WHERE con.contract_type = 'C'
            AND con.vendor_id = {$vendorId}
			AND sc.stockpile_id = {$stockpileId}
			AND con.contract_id IN ({$contractCurahs})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo1', '%d/%m/%Y')
            AND t.payment_id IS NULL";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAllCurah(this)" name="checkedSlips[]" /></th><th>Slip No.</th></th><th>Transaction Date</th><th>PO No.</th><th>Contract No.</th><th>Quantity</th><th>Currency</th><th>Unit Price</th><th>Total</th><th>DPP</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if($row->pph_tax_id == 0) {
                $dppTotalPrice = $row->unit_price * $row->quantity;
            } else {
                if($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($row->unit_price * $row->quantity) / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->unit_price * $row->quantity;
                }
            }

//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }

            $returnValue .= '<tr>';
            if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="'. $row->transaction_id .'" onclick="checkSlipCurah('.$stockpileId.','. $row->vendor_id .', '. "'". $contractCurahs ."'".',\'NONE\', \'NONE\', \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="'. $row->transaction_id .'" onclick="checkSlipCurah('.$stockpileId.','. $row->vendor_id .', '. "'". $contractCurahs ."'".',\'NONE\', \'NONE\', \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" checked /></td>';
                    $totalPrice = $totalPrice + $dppTotalPrice;
					
					$totalPrice2 = $totalPrice2 + $dppTotalPrice;

                    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="'. $row->transaction_id .'" onclick="checkSlipCurah('.$stockpileId.','. $row->vendor_id .', '. "'". $contractCurahs ."'".',\'NONE\', \'NONE\', \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">'. $row->slip_no .'</td>';
			$returnValue .= '<td style="width: 15%;">'. $row->transaction_date .'</td>';
			$returnValue .= '<td style="width: 15%;">'. $row->po_no .'</td>';
			$returnValue .= '<td style="width: 20%;">'. $row->contract_no .'</td>';
            $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($row->quantity, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="width: 2%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 5%;">'. number_format($row->unit_price, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">'. number_format(($row->unit_price * $row->quantity), 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 25%;">'. number_format($dppTotalPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`, p.ppn_amount ,p.pph_amount, v.pph, c.`po_no`, c.`contract_no`,v.ppn 
                    FROM payment p
                    LEFT JOIN contract c ON c.`contract_id` = p.`curahContract` 
                    LEFT JOIN vendor v ON v.vendor_id = p.vendor_id
                    WHERE p.vendor_id = {$vendorId} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            $downPayment = 0;
			
			if ($result->num_rows > 0) {
			while($row = $result->fetch_object()) {
				
			//$dp = $row->amount_converted * ((100 - $row->pph)/100);
                    $dpC = $row->amount_converted;
                    $dpPPN = $row->amount_converted * ($row->ppn/100);
                    $dpPPh = $row->amount_converted * ($row->pph/100);
                    $dp = ($row->amount_converted + $dpPPN) - $dpPPh ;
            //$downPayment = $dp;
			
				$returnValue .= '<tr>';
                $returnValue .= '<td colspan="5" style="text-align: right;">Down Payment</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->payment_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
				
				if($checkedSlipsDP != '') {
                $posDP = strpos($checkedSlipsDP, $row->payment_id);
                //echo $row->payment_id;
                if($posDP === false) {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="c2" value="'. $row->payment_id .'" onclick="checkSlipCurahDP('. $stockpileId .', '. $vendorId .', '. $contractCurahs .',\'NONE\', \'NONE\', \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" /></td>';
                } else {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="c2" value="'. $row->payment_id .'" onclick="checkSlipCurahDP('. $stockpileId .', '. $vendorId .', '. $contractCurahs .',\'NONE\', \'NONE\', \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" checked /></td>';
                    $downPayment = $downPayment + $dp; 
					
					$downPaymentC = $downPaymentC + $dpC; 
                    
           
                }
            } else {
					$returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="c2" value="'. $row->payment_id .'" onclick="checkSlipCurahDP('. $stockpileId .', '. $vendorId .', '. $contractCurahs .',\'NONE\', \'NONE\', \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" /></td>';
            }
                $returnValue .= '<td style="text-align: right;">'. number_format($dp, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';
            
        }
    }

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

//            $totalPpn = ($ppn/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="'. number_format($totalPPN, 2, ".", ",") .'" onblur="checkSlipCurah('.$stockpileId.','. $vendorId .',  '. "'". $contractCurah ."'".',this, document.getElementById(\'pph\'), \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="'. number_format($totalPPh, 2, ".", ",") .'" onblur="checkSlipCurah('.$stockpileId.','. $vendorId .',  '. "'". $contractCurah ."'".',document.getElementById(\'ppn\'), this, \''. $paymentFrom1 .'\', \''. $paymentTo1 .'\');" /></td>';
            $returnValue .= '</tr>';

            $grandTotal = ($totalPrice + $totalPPN) - $totalPPh - $downPayment;
			$grandTotal1 = $totalPrice2 - $downPaymentC;
            $totalPrice = ($totalPrice + $totalPPN) - $totalPPh;
			$totalPrice2 = $totalPrice2;
            if($grandTotal < 0 && $downPayment > 0) {
                $grandTotal = 0;
				$downPaymentC = $totalPrice2;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="'. round($totalPrice, 2) .'" />';
		$returnValue .= '<input type="hidden" id="totalPrice2" name="totalPrice2" value="'. round($totalPrice2, 2) .'" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="'. $downPayment .'" />';
		$returnValue .= '<input type="hidden" id="downPaymentHC" name="downPaymentHC" value="'. $downPaymentC .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function setSlipFreight($stockpileId, $freightId, $contractFreight, $checkedSlips, $checkedSlipsDP, $ppn, $pph, $paymentFrom, $paymentTo) {
    global $myDatabase;
    $returnValue = '';
	$whereProperty = '';
	//$vendorFreightId[] = array();
	if($checkedSlips == ''){
	for ($i = 0; $i < sizeof($contractFreight); $i++) {
                        if($contractFreights == '') {
                            $contractFreights .=  $contractFreight[$i];
                        } else {
                            $contractFreights .= ','. $contractFreight[$i];
                        }
                    }
	}else{
		$contractFreights = $contractFreight;
	}
	/*
	//echo $vendorFreightIds ;
	
	if($vendorFreightIds != 0){
	$whereProperty = "AND t.vendor_id IN ({$vendorFreightIds})";
	}*/

    $sql = "SELECT t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`, 
               
			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
				
				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
				
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                
				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim
            FROM TRANSACTION t
            LEFT JOIN freight_cost fc
                ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.fc_tax_id
			LEFT JOIN vendor v
				ON fc.vendor_id = v.vendor_id
			LEFT JOIN stockpile_contract sc
		        ON sc.stockpile_contract_id = t.stockpile_contract_id
			LEFT JOIN contract con
				ON con.contract_id = sc.contract_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
			LEFT JOIN transaction_shrink_weight ts
				ON t.transaction_id = ts.transaction_id
            WHERE fc.freight_id = {$freightId}
			AND sc.stockpile_id = {$stockpileId}
			{$whereProperty}
			AND sc.contract_id IN ({$contractFreights})
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND ( t.fc_payment_id IS NULL OR t.fc_payment_id != 0 ) AND freight_price != 0 and t.adj_oa IS NULL
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
		$returnValue .= '<form id = "frm1">';
		//$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th><th>Slip No.</th><th>Transaction Date</th><th>PO No.</th><th>Vendor Code</th><th>Vehicle No</th><th>Quantity</th><th>Freight Cost / kg</th><th>Amount</th><th>Shrink Qty Claim</th><th>Shrink Price Claim</th><th>Shrink Amount</th><th>Total Amount</th></tr></thead>';
        $returnValue .= '<tbody>';
        
        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
			if($row->freight_rule == 1){
				$fp = $row->freight_price * $row->send_weight;
				$fq = $row->send_weight;
			}else{
				$fp = $row->freight_price * $row->freight_quantity;
				$fq = $row->freight_quantity;
			}
            
			if($row->transaction_date >= '2015-10-05'&& $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
			    $dppTotalPrice = $fp;
				$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
			  } else{ 
			  if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $fp;
				$dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
            }else {
					if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  && $row->stockpile_id == 1){
					$dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
					$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
			} else {
                if($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
					$dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
				}else {
                  $dppTotalPrice = $fp;
				  $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
               }
            }
		
		}
	}
            $freightPrice = 0;
            if($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
				$freightPrice = $fp;
			}else{
				$freightPrice = $fp;
			}
            
			$amountPrice = $dppTotalPrice - $dppShrinkPrice;
//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//            
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }
            
            $returnValue .= '<tr>';
            if($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);
                
                if($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="'. $row->transaction_id .'" onclick="checkSlipFreight('. $stockpileId .', '. $row->freight_id .', '. "'". $contractFreights ."'".', \'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="'. $row->transaction_id .'" onclick="checkSlipFreight('. $stockpileId .', '. $row->freight_id .', '. "'". $contractFreights ."'".', \'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\');" checked /></td>';
                    
					
					$totalPrice = $totalPrice + $amountPrice;
					
					$totalPrice2 = $totalPrice2 + $amountPrice;
                    
                    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="'. $row->transaction_id .'" onclick="checkSlipFreight('. $stockpileId .', '. $row->freight_id .', '. "'". $contractFreights ."'".', \'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">'. $row->slip_no .'</td>';
			$returnValue .= '<td style="width: 10%;">'. $row->transaction_date .'</td>';
			$returnValue .= '<td style="width: 10%;">'. $row->po_no .'</td>';
			$returnValue .= '<td style="width: 10%;">'. $row->vendor_code .'</td>';
			$returnValue .= '<td style="width: 10%;">'. $row->vehicle_no .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($fq, 0, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($row->freight_price, 0, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($freightPrice, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($row->qtyClaim, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($row->trx_shrink_claim, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">'. number_format($dppShrinkPrice, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right; width: 20%;">'. number_format($amountPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
        }
        
        $returnValue .= '</tbody>';
        if($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
           

			$sql = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`,p.ppn_amount ,p.pph_amount, vh.pph, c.`po_no`, c.`contract_no`, vh.ppn 
FROM payment p LEFT JOIN freight vh ON p.`freight_id` = vh.`freight_id`
LEFT JOIN contract c ON c.`contract_id` = p.`freightContract` 
WHERE p.freight_id = {$freightId} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            $downPayment = 0;
			
			if ($result->num_rows > 0) {
			while($row = $result->fetch_object()) {
				
			$dpFC = $row->amount_converted;
			$dpPPN = $row->amount_converted * ($row->ppn/100);
			$dpPPh = $row->amount_converted * ($row->pph/100);
			$dp = ($row->amount_converted + $dpPPN) - $dpPPh ;
            //$downPayment = $dp;
			
				$returnValue .= '<tr>';
                $returnValue .= '<td colspan="8" style="text-align: right;">Down Payment</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->payment_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
				$returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
				
				if($checkedSlipsDP != '') {
                $posDP = strpos($checkedSlipsDP, $row->payment_id);
                //echo $row->payment_id;
                if($posDP === false) {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipFreightDP('. $stockpileId .', '. $freightId .', '. $contractFreights .',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
                } else {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipFreightDP('. $stockpileId .', '. $freightId .', '. $contractFreights .',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\');" checked /></td>';
                    $downPayment = $downPayment + $dp; 
					$downPaymentFC = $downPaymentFC + $dpFC; 
                    
           
                }
            } else {
					$returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipFreightDP('. $stockpileId .', '. $freightId .', '. $contractFreights .',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
            }
                $returnValue .= '<td style="text-align: right;">'. number_format($dp, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';
            
        }
    }
            /*$sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, f.pph, f.ppn, p.stockpile_location, f.freight_id
FROM payment p LEFT JOIN freight f ON p.`freight_id` = f.`freight_id`
WHERE p.freight_id = {$freightId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            $downPayment = 0;
            if($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
				$dp = $row->down_payment * ((100 - $row->pph)/100);
				$fc_ppn_dp = $row->down_payment * ($row->ppn/100);
				$downPayment = $dp + $fc_ppn_dp;
				
				//if($row->freight_id == 312 && $row->stockpile_location != 8){
				//	$downPayment = 0;
				//}
                
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="12" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                $returnValue .= '</tr>';
            }*/
            
//            $ppnDB = 0;
//            $pphDB = 0;
//            $sql = "SELECT freight_id, ppn, pph FROM freight WHERE freight_id = {$freightId}";
//            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($result->num_rows == 1) {
//                $row = $result->fetch_object();
//                $ppnDB = $row->ppn;
//                $pphDB = $row->pph;
//            }
//            
//            $ppnDBAmount = ($ppnDB/100) * $totalPrice;
//            $pphDBAmount = ($pphDB/100) * $totalPrice;
            
            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;
            
            if($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }
            
//            $totalPpn = ($ppnValue/100) * $totalPrice;
           $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="'. number_format($totalPpn, 2, ".", ",") .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', '. "'". $contractFreights ."'".', this, document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="'. number_format($totalPph, 2, ".", ",") .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', '. "'". $contractFreights ."'".', document.getElementById(\'ppn\'), this, \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
            $returnValue .= '</tr>';
            
            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
			$grandTotal1 = $totalPrice2 - $downPaymentFC;
			//$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


            $totalPrice = ($totalPrice + $totalPpn) - $totalPph;
			$totalPrice2 = $totalPrice2;

            if($grandTotal < 0 && $downPayment > 0) {
                $grandTotal = 0;
				$downPaymentFC = $totalPrice2;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
            
            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
	    $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="'. round($totalPrice, 2) .'" />';
		$returnValue .= '<input type="hidden" id="totalPrice2" name="totalPrice2" value="'. round($totalPrice2, 2) .'" />';
		//$returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="vendorFreights" id="vendorFreights" value="'. $vendorFreightIds .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', this, document.getElementById(\'ppn\'), document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" />';
		$returnValue .= '<input type="hidden" id="fc_ppn_dp" name="fc_ppn_dp" value="'. $fc_ppn_dp .'" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="'. $downPayment .'" />';
		$returnValue .= '<input type="hidden" id="downPaymentFC" name="downPaymentFC" value="'. $downPaymentFC .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }
    
    echo $returnValue;
}

function setSlipUnloading($stockpileId, $laborId, $checkedSlips, $ppn, $pph, $paymentFromUP, $paymentToUP)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT t.*,
                l.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value
            FROM `transaction` t
            INNER JOIN labor l
                ON l.labor_id = t.labor_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = l.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.uc_tax_id
            WHERE t.labor_id = {$laborId}
            AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.uc_payment_id IS NULL AND t.unloading_cost_id IS NOT NULL AND t.adj_ob IS NULL
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromUP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToUP', '%d/%m/%Y')
			
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAllUC(this)" name="checkedSlips[]" /></th><th>Slip No.</th><th>Transaaction Date</th><th>Quantity</th><th>Currency</th><th>Unloading Cost</th><th>Total</th><th>DPP</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $row->unloading_price;
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = $row->unloading_price / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->unloading_price;
                }
            }

//            if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
//                $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
//            }
//
//            if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
//                $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
//            }

            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" checked /></td>';
                    $totalPrice = $totalPrice + $dppTotalPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 20%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row->quantity, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 5%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->unloading_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row->unloading_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment FROM payment WHERE labor_id = {$laborId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $downPayment = $row->down_payment;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="7" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

//            $ppnDB = 0;
//            $pphDB = 0;
//            $sql = "SELECT labor_id, ppn, pph FROM labor WHERE labor_id = {$laborId}";
//            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//            if($result->num_rows == 1) {
//                $row = $result->fetch_object();
//                $ppnDB = $row->ppn;
//                $pphDB = $row->pph;
//            }

//            $ppnDBAmount = ($ppnDB/100) * $totalPrice;
//            $pphDBAmount = ($pphDB/100) * $totalPrice;

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            if ($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif ($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if ($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif ($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }

//            $totalPpn = ($ppn/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', this, document.getElementById(\'ppn\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', document.getElementById(\'pph\'), this, \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            $returnValue .= '</tr>';


            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;

            $totalPrice = ($totalPrice + $totalPpn) - $totalPph;
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function setSlipShipment($salesId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sh.*, cs.ppn_tax_id, cs.ppn
            FROM shipment sh INNER JOIN sales sl ON sh.sales_id = sl.sales_id
            INNER JOIN customer cs ON sl.customer_id = cs.customer_id 
            WHERE sh.sales_id = {$salesId}
            AND sh.payment_id IS NULL";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $sql = "SELECT s.currency_id, cur.currency_code
                FROM sales s
                INNER JOIN currency cur
                    ON cur.currency_id = s.currency_id
                WHERE s.sales_id = {$salesId}";
        $resultSales = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        $rowSales = $resultSales->fetch_object();

        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Shipment Code</th><th>Accumulated Down Payment (' . $rowSales->currency_code . ')</th><th>Down Payment</th></tr></thead>';
        $returnValue .= '<tbody>';

        while ($row = $result->fetch_object()) {
            $returnValue .= '<tr>';
            $returnValue .= '<td>' . $row->shipment_code . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->dp_amount, 0, ".", ",") . '</td>';

            if ($row->ppn_tax_id != 0) {
                $returnValue .= '<td><input type="text" class="dpSales" onkeyup="hitungPPN();" name="downPayment' . $row->shipment_id . '" id="downPayment' . $row->shipment_id . '" /></td>';
                $returnValue .= '<td><input type="text" class="ppnSales" name="ppnSales' . $row->shipment_id . '" id="ppnSales' . $row->shipment_id . '"  readonly/></td>';
            } else {
                $returnValue .= '<td><input type="text" class="dpSales" name="downPayment' . $row->shipment_id . '" id="downPayment' . $row->shipment_id . '" /></td>';
                $returnValue .= '<td></td>';
            }

            $returnValue .= '<input type="hidden" name="shipmentId' . $row->shipment_id . '" id="shipmentId' . $row->shipment_id . '" value="' . $row->shipment_id . '" />';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        $returnValue .= '</table>';
        $returnValue .= '</div>';
        $returnValue .= '|' . $rowSales->currency_id;
    }

    echo $returnValue;
}

function refreshSummaryShipment($salesId, $checkedSlips, $ppn, $pph)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT sh.*, sl.`price_converted`
            FROM shipment sh LEFT JOIN payment p ON sh.`sales_id` = p.`sales_id`
            LEFT JOIN sales sl ON sh.`sales_id` = sl.`sales_id`
            WHERE sh.sales_id = {$salesId}
            AND sh.shipment_status != 0
            AND sh.payment_id IS NULL";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $sql = "SELECT s.currency_id, cur.currency_code, s.customer_id
                FROM sales s
                INNER JOIN currency cur
                    ON cur.currency_id = s.currency_id
                WHERE s.sales_id = {$salesId}";
        $resultSales = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        $rowSales = $resultSales->fetch_object();

        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th></th><th>Shipment Code</th><th>Quantity (KG)</th><th>COGS Amount (' . $rowSales->currency_code . ')</th><th>Down Payment (' . $rowSales->currency_code . ')</th><th>Invoice Amount (' . $rowSales->currency_code . ')</th><th>Total (' . $rowSales->currency_code . ')</th></tr></thead>';
        $returnValue .= '<tbody>';

        while ($row = $result->fetch_object()) {
            if ($result->num_rows > 0) {
                $dpAmount = $row->dp_amount - $row->ppn_amount;
                $invoiceAmount = $row->quantity * $row->price_converted;
            }
            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->shipment_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->shipment_id . '" onclick="checkSlipShipment(' . $row->sales_id . ', \'NONE\', \'NONE\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->shipment_id . '" onclick="checkSlipShipment(' . $row->sales_id . ', \'NONE\', \'NONE\');" checked /></td>';
                    $totalPrice = $totalPrice + ($invoiceAmount - $dpAmount);
                    $totalInvoice = $totalInvoice + $invoiceAmount - $dpAmount;
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->shipment_id . '" onclick="checkSlipShipment(' . $row->sales_id . ', \'NONE\', \'NONE\');" /></td>';
            }
            $returnValue .= '<td>' . $row->shipment_no . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->quantity, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($row->cogs_amount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($dpAmount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($invoiceAmount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format(($invoiceAmount - $dpAmount), 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';

            $ppnDB = 0;
            $pphDB = 0;
            $sql = "SELECT customer_id, ppn, pph FROM customer WHERE customer_id = {$rowSales->customer_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if ($result->num_rows == 1) {
                $row = $result->fetch_object();
                $ppnDB = $row->ppn;
                $pphDB = $row->pph;
            }

            $ppnDBAmount = ($ppnDB / 100) * $totalInvoice;
            $pphDBAmount = ($pphDB / 100) * $totalInvoice;

            if ($ppn == 'NONE') {
                $totalPpn = $ppnDBAmount;
            } elseif ($ppn != $ppnDBAmount) {
                $totalPpn = $ppn;
            } else {
                $totalPpn = $ppnDBAmount;
            }

            if ($pph == 'NONE') {
                $totalPph = $pphDBAmount;
            } elseif ($pph != $pphDBAmount) {
                $totalPph = $pph;
            } else {
                $totalPph = $pphDBAmount;
            }

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipShipment(' . $salesId . ', this, document.getElementById(\'pph\'));" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipShipment(' . $salesId . ', document.getElementById(\'ppn\'), this);" /></td>';
            $returnValue .= '</tr>';

            $grandTotal = ($totalPrice + $totalPpn) - $totalPph;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
        $returnValue .= '|' . $rowSales->currency_id;
    }

    echo $returnValue;
}


function getVendorReport($paymentType, $stockpileId)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';

    if ($paymentType == 1) {
        // PKS
        if ($stockpileId != '' && $stockpileId != 0) {
            $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
        }

        $sql = "SELECT DISTINCT v.vendor_id AS vendor_id, v.vendor_name AS vendor_name
                FROM vendor v
                INNER JOIN contract con
                    ON con.vendor_id = v.vendor_id
                INNER JOIN stockpile_contract sc
                    ON sc.contract_id = con.contract_id
                WHERE 1=1 {$whereProperty}
				";
    } elseif ($paymentType == 2) {
        // FC
        if ($stockpileId != '' && $stockpileId != 0) {
            $whereProperty .= " AND fc.stockpile_id = {$stockpileId} ";
        }

        $sql = "SELECT DISTINCT f.freight_id AS vendor_id, f.freight_supplier AS vendor_name
                FROM freight f
                INNER JOIN freight_cost fc
                    ON fc.freight_id = f.freight_id
                INNER JOIN transaction t
                    ON t.freight_cost_id = fc.freight_cost_id
                INNER JOIN stockpile_contract sc
                    ON sc.stockpile_contract_id = t.stockpile_contract_id
                INNER JOIN contract con
                    ON con.contract_id = sc.contract_id
                WHERE 1=1 {$whereProperty}
				";
    } elseif ($paymentType == 3) {
        // UC
        if ($stockpileId != '' && $stockpileId != 0) {
            $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
        }

        $sql = "SELECT DISTINCT l.labor_id AS vendor_id, l.labor_name AS vendor_name
                FROM labor l
                INNER JOIN transaction t
                    ON t.labor_id = l.labor_id
                INNER JOIN stockpile_contract sc
                    ON sc.stockpile_contract_id = t.stockpile_contract_id
                INNER JOIN contract con
                    ON con.contract_id = sc.contract_id
                WHERE 1=1 {$whereProperty} ";

    } elseif ($paymentType == 4) {
        // OTHER
        if ($stockpileId != '' && $stockpileId != 0) {
            $whereProperty .= " AND p.stockpile_location = {$stockpileId} ";
        }

        $sql = "SELECT DISTINCT gv.general_vendor_id AS vendor_id, gv.general_vendor_name AS vendor_name
                FROM general_vendor gv
                INNER JOIN payment p
                    ON p.general_vendor_id = gv.general_vendor_id 
                INNER JOIN stockpile s
                    ON p.stockpile_location = s.stockpile_id
               
                WHERE 1=1 {$whereProperty} ";
    }

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->vendor_id . '||' . $row->vendor_name;
            } else {
                $returnValue = $returnValue . '{}' . $row->vendor_id . '||' . $row->vendor_name;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getGeneralVendorTax($generalVendorId, $amount, $tax_pph1)
{
    global $myDatabase;
    $returnValue = '';
	
    $sql = "SELECT *
            FROM general_vendor
            WHERE general_vendor_id = {$generalVendorId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn = $row->ppn;
        $pph = $row->pph;
        $ppnID = $row->ppn_tax_id;
        $pphID = $row->pph_tax_id;

        $ppnAmount = ($ppn / 100) * $amount;
        $pphAmount = ($pph / 100) * $amount;
		
		$totalAmountPPN = ($ppnAmount + $amount) - $tax_pph1;

        $returnValue = '|' . number_format($ppnAmount, 10, ".", ",") . '|' . number_format($pphAmount, 10, ".", ",") . '|' . $ppnID . '|' . $pphID . '|' .number_format($totalAmountPPN, 10, ".", ",");
    } else {
        $returnValue = '|0|0|0|0';
    }

    echo $returnValue;
}

function setPaymentLocation()
{
    global $myDatabase;
    $htmlValue = '';
    $returnValue = '';

    $sqlAcc = "SELECT stockpile_id FROM user WHERE user_id = {$_SESSION['userId']}";
    $resultAcc = $myDatabase->query($sqlAcc, MYSQLI_STORE_RESULT);
    if ($resultAcc->num_rows == 1) {
        $rowAcc = $resultAcc->fetch_object();

        $stockpileID = $rowAcc->stockpile_id;

    }

    $sql = "SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
            FROM user_stockpile us
            INNER JOIN stockpile s
                ON s.stockpile_id = us.stockpile_id
            WHERE us.user_id = {$_SESSION['userId']}
            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 1) {
        $htmlValue = "|2|";
        $htmlValue = $htmlValue . "<SELECT tabindex='50' name='paymentLocation' id='paymentLocation'>";
        if (isset($_SESSION['payment']) && $_SESSION['payment']['paymentLocation']) {
            if ($_SESSION['payment']['paymentLocation'] == 0) {
                $returnValue = "<option value='0' selected>Head Office</option>";
            }
        } else {
            $returnValue = "<option value='0'>Head Office</option>";
        }

        while ($row = $result->fetch_object()) {
            if (isset($_SESSION['payment']) && $_SESSION['payment']['paymentLocation']) {
                if ($_SESSION['payment']['paymentLocation'] == $row->stockpile_id) {
                    $returnValue = $returnValue . "<option value='" . $row->stockpile_id . "' selected>" . $row->stockpile_full . "</option>";
                }
            } else {
                $returnValue = $returnValue . "<option value='" . $row->stockpile_id . "'>" . $row->stockpile_full . "</option>";
            }
        }
        $htmlValue = $htmlValue . $returnValue;
        $htmlValue = $htmlValue . "</SELECT>";
    } elseif ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<input type='hidden' name='paymentLocation' id='paymentLocation' value='" . $row->stockpile_id . "' />";
    } else {
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<input type='hidden' name='paymentLocation' id='paymentLocation' value='0' />";
    }

    echo $htmlValue;
}

function setStockpileLocation()
{
    global $myDatabase;
    $htmlValue = '';
    $returnValue = '';


    $sql = "SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
            FROM user_stockpile us
            INNER JOIN stockpile s
                ON s.stockpile_id = us.stockpile_id
WHERE us.user_id = {$_SESSION['userId']} 
            ORDER BY s.stockpile_code ASC, s.stockpile_name ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 1) {
        $htmlValue = "|2|";
        $htmlValue = $htmlValue . "<SELECT tabindex='50' name='stockpileLocation' id='stockpileLocation'>";
        if (isset($_SESSION['payment']) && $_SESSION['payment']['stockpileLocation']) {
            if ($_SESSION['payment']['stockpileLocation'] == 0) {
                $returnValue = "<option value='10' selected>Head Office</option>";
            }
        } else {
            $returnValue = "<option value='10'>Head Office</option>";
        }

        while ($row = $result->fetch_object()) {
            if (isset($_SESSION['payment']) && $_SESSION['payment']['stockpileLocation']) {
                if ($_SESSION['payment']['stockpileLocation'] == $row->stockpile_id) {
                    $returnValue = $returnValue . "<option value='" . $row->stockpile_id . "' selected>" . $row->stockpile_full . "</option>";
                }
            } else {
                $returnValue = $returnValue . "<option value='" . $row->stockpile_id . "'>" . $row->stockpile_full . "</option>";
            }
        }
        $htmlValue = $htmlValue . $returnValue;
        $htmlValue = $htmlValue . "</SELECT>";
    } elseif ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<input type='hidden' name='stockpileLocation' id='stockpileLocation' value='" . $row->stockpile_id . "' />";
    } else {
        $htmlValue = "|1|";
        $htmlValue = $htmlValue . "<input type='hidden' name='stockpileLocation' id='stockpileLocation' value='0' />";
    }

    // echo $sql;
    echo $htmlValue;
}

function setCurrencyId($paymentType, $accountId, $salesId)
{
    global $myDatabase;
    $returnValue = '';

    if ($paymentType == 2 || $paymentType == 1 && $salesId == '') {
        $sql = "SELECT b.currency_id FROM bank b WHERE b.account_id = {$accountId}";
    } elseif ($paymentType == 1) {
        $sql = "SELECT sl.currency_id FROM sales sl WHERE sl.sales_id = {$salesId}";
    }
    echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $row = $result->fetch_object();
        $currencyId = $row->currency_id;
    }

    $returnValue = '|' . $currencyId;

    echo $sql;
    echo $returnValue;
}


function getMonth($date)
{
    global $myDatabase;
    $sql = "SELECT month('$date') as month";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();

    echo $row->month;
}

function getWeek($date)
{
    global $myDatabase;

    $sql = "SELECT concat(DATE_FORMAT('$date','%Y-%m'), '-','01') as date";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $row = $result->fetch_object();

    $sql2 = "SELECT WEEK('$date') - WEEK('$row->date') as week";
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
    $row2 = $result2->fetch_object();

    echo $row2->week;
}

function getValidateInvoiceNo($invoiceNo)
{
    global $myDatabase;
	$returnValue = '';
    $sql = "SELECT * FROM payment 
    WHERE invoice_no = '{$invoiceNo}'";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        $noInv = 1;
    } 
    $returnValue = $noInv;
    echo $returnValue;
}

function setPengajuanGeneralDetail()
{
    global $myDatabase;
    $returnValue = '';
    $transactionType = $_POST['transactionType'];
    $generalVendorId = $_POST['generalVendorId'];
    $pgId = '0';

    if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
        $pgId = $_POST['pgId'];
        $sql = "SELECT id.pgd_id, mh.kode_mutasi, id.po_detail_id,
                CASE WHEN id.type = 4 THEN 'Loading'
                WHEN id.type = 5 THEN 'Umum'
                WHEN id.type = 6 THEN 'HO' ELSE '' END AS `type2`, 
                CASE WHEN id.poId IS NOT NULL THEN c.po_no
                WHEN id.shipment_id IS NOT NULL THEN  sh.shipment_no
                ELSE '' END AS po_shipment, s.stockpile_name, id.notes, cur.currency_code, id.exchange_rate,
                id.qty, id.price, id.termin, id.amount, id.ppn, id.pph, id.tamount, gv.general_vendor_name, gv.pph AS gv_pph, gv.ppn AS gv_ppn,
                pg.transaction_type, id.general_vendor_id as gvId, id.tamount_converted
                FROM pengajuan_general_detail id 
                LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
                LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
                LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
                LEFT JOIN contract c ON c.contract_id = id.poId
                LEFT JOIN currency cur ON cur.currency_id = id.currency_id
                LEFT JOIN mutasi_detail md ON id.mutasi_detail_id = md.mutasi_detail_id
                LEFT JOIN mutasi_header mh ON md.mutasi_header_id = mh.mutasi_header_id
                LEFT JOIN pengajuan_general pg ON id.pg_id = pg.pengajuan_general_id
                WHERE id.pg_id = {$_POST['pgId']} ORDER BY id.pgd_id ASC";
               
    } else {
        $sql = "SELECT id.pgd_id,mh.kode_mutasi,
                CASE WHEN id.type = 4 THEN 'Loading'
                WHEN id.type = 5 THEN 'Umum'
                WHEN id.type = 6 THEN 'HO' ELSE '' END AS `type2`, 
                CASE WHEN id.poId IS NOT NULL THEN c.po_no
                WHEN id.shipment_id IS NOT NULL THEN  sh.shipment_no
                ELSE '' END AS po_shipment, s.stockpile_name, id.notes, cur.currency_code, id.exchange_rate,
                id.qty, id.price, id.termin, id.amount, id.ppn, id.pph, id.tamount, id.tamount_converted, gv.general_vendor_name, gv.pph AS gv_pph, gv.ppn AS gv_ppn
                FROM pengajuan_general_detail id 
                LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
                LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
                LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
                LEFT JOIN contract c ON c.contract_id = id.poId
                LEFT JOIN currency cur ON cur.currency_id = id.currency_id
                LEFT JOIN mutasi_detail md ON id.mutasi_detail_id = md.mutasi_detail_id
                LEFT JOIN mutasi_header mh ON md.mutasi_header_id = mh.mutasi_header_id
                WHERE id.pg_id IS NULL AND id.entry_by = {$_SESSION['userId']} ORDER BY id.pgd_id ASC";
             
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "invoiceDetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th>Action</th>
                                <th>Type</th>
                                <th>Vendor</th>
                                <th>No Reference</th>
                                <th>Remark (SP)</th>
                                <th>Notes</th>
                                <th>Qty</th>
                                <th>Currency(Rate)</th>
                                <th>Unit Price</th>
                                <th>Termin</th>
                                <th>Amount</th>
                                <th>PPN</th>
                                <th>PPh</th>
                                <th>Down Payment</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        while ($row = $result->fetch_object()) {

            $returnValue .= '<tr>';
            $sqlDP = "SELECT SUM(idp.amount_payment) AS down_payment, 
                        SUM(idp.ppn_value) AS ppn, SUM(idp.pph_value) AS pph
                        /* SUM(CASE WHEN id.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END) AS ppn, */
                        /* SUM(CASE WHEN id.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END) AS pph, */
                    FROM invoice_dp idp 
                    LEFT JOIN invoice_detail id ON id.`pgd_id` = idp.`invoice_detail_dp`
                    LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
                    LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
                    WHERE idp.status = 0 AND idp.pengajuan_detail_id = {$row->pgd_id}";
            $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
            if ($resultDP !== false && $resultDP->num_rows == 1) {
                $rowDP = $resultDP->fetch_object();

                if ($rowDP->ppn == 0) {
                    $dp_ppn = 0;
                } else {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    $dp_ppn = $rowDP->ppn;
                }

                if ($rowDP->pph == 0) {
                    $dp_pph = 0;
                } else {
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $dp_pph = $rowDP->pph;
                }


                if ($rowDP->down_payment != 0) {
                    //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
                    //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
                    $downPayment = ($rowDP->down_payment + $dp_ppn) - $dp_pph;
                } else {
                    $downPayment = 0;
                }
            }
            // $termin = $row->termin;
            // $ppn = $row->ppn * $termin / 100;
            // $pph = $row->pph;
            // $amount = $row->amount * $termin / 100;
            // $tamount1 = $amount + $ppn  - $pph;
            $tamount = $row->tamount_converted - $downPayment;
            $totalPrice += $tamount;
            // echo " TEST " . $amount;

            if (isset($row->po_shipment) && $row->po_shipment != '') {
                $noReference = $row->po_shipment;
            } else {
                $noReference = $row->kode_mutasi;
            }
            if($row->po_detail_id == ''){
                $returnValue .= '<td style="text-align: right; width: 8%;"><a href="#" id="update|invoice|' . $row->pgd_id . '" role="button" title="Edit" onclick="editDetail(' . $row->pgd_id . ', '.$transactionType.', '.$generalVendorId.', '.$pgId.');">
                                            <img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a> | 
                                        <a href="#" id="delete|invoice|' . $row->pgd_id . '" role="button" title="Delete" onclick="deleteInvoiceDetail(' . $row->pgd_id . ', '.$pgId.');">
                                            <img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a> 
                                        </td>';
            }else{
                $returnValue .= '<td style="width: 8%;"></td>';
            }
            $returnValue .= '<td style="width: 8%;">' . $row->type2 . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->general_vendor_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $noReference . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->qty, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->currency_code . '(' . number_format($row->exchange_rate, 0, ".", ",") . ')' . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->termin, 0, ".", ",") . '%</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->amount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->ppn, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->pph, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($downPayment, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($tamount, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';


        $grandTotal = $totalPrice;

        $returnValue .= '<tfoot>';
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="14" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        // $returnValue .= '<input type="hidden" id="pph2" name="pph2" value="'. round($total_pph, 2) .'" />';
        // $returnValue .= '<input type="hidden" id="ppn2" name="ppn2" value="'. round($total_ppn, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        //$returnValue .= '</div>';
    }

    echo $returnValue;
}
function setPlanPayDateGeneral($todayDate){
    global $myDatabase;

    $sql = "CALL PlanPayDate('{$todayDate}')";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    while ($row = $result->fetch_object()){
        $date = $row->tglBayar;
    }
    $newDate = date_create($date);
    echo date_format($newDate,"d/m/Y");
}