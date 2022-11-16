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
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('my');

switch ($_POST['action']) {
    case "setStockpileLocation":
        setStockpileLocation($_POST['stockpileId'], $_POST['idPP']);;
    break;
    case "getVendorBank":
        getVendorBank($_POST['vendorId'], $_POST['paymentFor']);
    break;
    case "refreshSummary":
        refreshSummary($_POST['stockpileContractId'], $_POST['ppn'], $_POST['pph'], $_POST['paymentType']);
    break;
    case "setSlip":
        setSlip($_POST['stockpileContractId'], $_POST['checkedSlips']);
    break;
    case "setSlipCurah":
        setSlipCurah($_POST['stockpileId'], $_POST['vendorId'], $_POST['contractCurah'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom1'], $_POST['paymentTo1'], $_POST['idPP']);
    break;
    case "setSlipCurah_settle":
        setSlipCurah_settle($_POST['stockpileId'], $_POST['vendorId'], $_POST['settle'], $_POST['contractCurah'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromCur'], $_POST['paymentToCur'], $_POST['idPP']);
        break;
    case "getCurahTax":
        getCurahTax($_POST['vendorIdCurahDp']);
    break;
    case "getStockpileContractPayment":
        getStockpileContractPayment($_POST['stockpileId'], $_POST['vendorId'], $_POST['paymentType']);
    break;
    case "getVendorBankDetail":
        getVendorBankDetail($_POST['vendorBankId'], $_POST['paymentFor']);
    break;
    case "getVendorPayment":
        getVendorPayment($_POST['stockpileId']);
    break;
    
    case "setSlipDp":
        setSlipDp($_POST['idPP']);
    break;

	 case "setSlipFreight_1":
        setSlipFreight_1($_POST['stockpileId_1'], $_POST['freightId_1'], $_POST['contractFreight'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromFP'], $_POST['paymentToFP'], $_POST['idPP']);
    break;
	case "getVendorFreight":
        getVendorFreight($_POST['stockpileId']);
    break;
    case "getSupplier":
        getSupplier($_POST['stockpileId'], $_POST['freightId']);
    break;
    case "getNoKontrak":
        getNoKontrak($_POST['vendorFreightIdDp']);
    break;
    case "getContractFreight":
        getContractFreight($_POST['stockpileId'], $_POST['freightId']);
    break;
    case "getContractFreightDp":
        getContractFreightDp($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorId'] );
    break;
    case "getFreightTax":
        getFreightTax($_POST['freightId']);
    break;
    case "setSlipFreight_settle":
        setSlipFreight_settle($_POST['stockpileId'], $_POST['freightId'], $_POST['settle'], $_POST['contractFreight'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['idPP']);
    break;
    case "setSlipFreight_settleApprove":
        setSlipFreight_settleApprove($_POST['stockpileId'], $_POST['freightId'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['idPP']);
    break;
    case "setSlipFreight_approve":
        setSlipFreight_approve($_POST['stockpileId'], $_POST['freightId'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['idPP']);
    break;
    case "getBankDetail":
        getBankDetail($_POST['bankVendor'], $_POST['paymentFor']);
    break;

    case "getContractHandling":
        getContractHandling($_POST['stockpileId'],$_POST['vendorHandlingId']);
    break;
    case "getHandlingTax":
        getHandlingTax($_POST['vendorHandlingDp']);
    break;
    case "getHandlingPayment":
        getHandlingPayment($_POST['stockpileId']);
    break;
    case "setSlipHandling":
        setSlipHandling($_POST['stockpileId'], $_POST['vendorHandlingId'], $_POST['contractHandling'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromHP'], $_POST['paymentToHP'], $_POST['idPP']);
    break;
    case "setSlipHandling_settleApprove":
        setSlipHandling_settleApprove($_POST['stockpileId'], $_POST['vendorHandlingId'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['idPP']);
    break;
    case "setSlipHandling_settle":
        setSlipHandling_settle($_POST['stockpileId'], $_POST['vendorHandlingId'], $_POST['settle'], $_POST['contractHandling'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromHP'], $_POST['paymentToHP'], $_POST['idPP']);
        break;
    case "getLaborPayment":
        getLaborPayment($_POST['stockpileId']);
    break;
    case "getLaborTax":
        getLaborTax($_POST['laborDp']);
    break;
    case "setSlipUnloading_settle":
        setSlipUnloading_settle($_POST['stockpileId'], $_POST['laborId'], $_POST['settle'], $_POST['checkedSlips'], $_POST['checkedSlipsDP'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromUP'], $_POST['paymentToUP'], $_POST['idPP']);
    break;
    case "setSlipUnloading_settleApprove":
        setSlipUnloading_settleApprove($_POST['stockpileId'], $_POST['laborId'], $_POST['paymentFromUP'], $_POST['paymentToUP'], $_POST['idPP']);
    break;
    case "setSlipUnloading":
        setSlipUnloading($_POST['stockpileId'], $_POST['laborId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromUP'], $_POST['paymentToUP'], $_POST['idPP']);
    break;
    case "setSlipPengajuanUnloading":
        setSlipPengajuanUnloading($_POST['stockpileId'], $_POST['laborId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromUP'], $_POST['paymentToUP'], $_POST['idPP']);
    break;
    case "setSlipPengajuanCurah_copy":
        setSlipPengajuanCurah_copy($_POST['stockpileId'], $_POST['vendorId'], $_POST['checkedSlips'], $_POST['paymentFrom1'], $_POST['paymentTo1'], $_POST['idPP']);
    break;
    case "getInvoice_notim":
        getInvoice_notim($_POST['idPP'], $currentYearMonth);
    break;
    case "getValidateInvoiceNo":
        getValidateInvoiceNo($_POST['invoiceNo']);
    break;
    case "setStockpileLocation_GET":
        setStockpileLocation_GET($_POST['stockpileId'], $_POST['idPP']);
    break;
    case "setExchangeRate":
        setExchangeRate($_POST['bankId'], $_POST['currencyId'], $_POST['journalCurrencyId']);
    break;
    case "getBankITF":
        getBankITF($_POST['stockpileLocation'], $_POST['newbank']);
    break;
    case "setPlanPayDate":
        setPlanPayDate($todayDate);
    break;
    case "getContractCurah":
        getContractCurah($_POST['stockpileId'],$_POST['vendorId']);
    break;
    case "updatePengajuanPayment":
        updatePengajuanPayment($_POST['idPP'], $_POST['paymentFor']);
    break;
}

function updatePengajuanPayment($idPP, $paymentFor) {
    global $myDatabase;
    $returnValue = '';
    $totalQty = 0;
    $totalShrink = 0;
    $totalDpp = 0;
    $totalAmount = 0;
    $totalPPn = 0;
    $totalPPh = 0;
    $grandTotal = 0;
    $totalGrandTotal = 0;
    $tempPPn = 0;
    $tempPPh = 0;

    if($paymentFor == 2){
        $sql = "SELECT po.*, pp.settlement_status FROM payment_oa po 
                LEFT JOIN pengajuan_payment pp ON pp.idPP = po.idPP 
                WHERE po.idpp = {$idPP} AND pp.settlement_status = 1";
    }else if($paymentFor == 9){
        $sql = "SELECT ph.*, pp.settlement_status FROM payment_handling ph 
        LEFT JOIN pengajuan_payment pp ON pp.idPP = ph.idPP 
        WHERE ph.idpp = {$idPP} AND pp.settlement_status = 1";
    }else if($paymentFor == 3){
        $sql = "SELECT ob.*, pp.settlement_status FROM payment_ob ob 
        LEFT JOIN pengajuan_payment pp ON pp.idPP = ob.idPP 
        WHERE ob.idpp = {$idPP} AND pp.settlement_status = 1";
    }else if($paymentFor == 1){
        $sql = "SELECT cur.*, pp.settlement_status FROM payment_curah cur 
        LEFT JOIN pengajuan_payment pp ON pp.idPP = cur.idPP 
        WHERE cur.idpp = {$idPP} AND pp.settlement_status = 1";
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    $count = $result->num_rows;

   // echo $sql;
    
    if ($result->num_rows > 0 && $count > 0) {
        while($row = $result->fetch_object()) {
         

            $settle = $row->settlement_status;
            $totalQty = $totalQty + $row->qty;
            $totalShrink = $totalShrink + $row->shrink;
            $totalShrink2 = $totalShrink2 + $row->additional_shrink;
            $totalDpp = $totalDpp + $row->dpp;
            $totalAmount = $totalAmount + $row->total_amount;

            $tempPPn = $row->ppn_value;
            $tempPPh = $row->pph_value;

            // echo " | A " .$tempPPh;

            // $totalPPn = $totalPPN + $tempPPn;
            // $totalPPh = $totalPPh + $tempPPh;

            // $grandTotal = ($totalAmount + $totalPPn) - $totalPPh;
            // $totalGrandTotal = $totalGrandTotal + $grandTotal;
          
        }

        $totalPPn = $totalAmount * ($tempPPn/100);
        $totalPPh = $totalAmount * ($tempPPh/100);
        $grandTotal = ($totalAmount + $totalPPn) - $totalPPh;

        $update = "UPDATE pengajuan_payment SET total_qty = $totalQty, total_shrink = $totalShrink, total_shrink2 = $totalShrink2, total_dpp = $totalDpp, "
                    . " total_amount = $totalAmount, total_ppn_amount = $totalPPn, total_pph_amount = $totalPPh, grand_total = 0"
                    . " WHERE idPP = {$idPP}";
        $result1 = $myDatabase->query($update, MYSQLI_STORE_RESULT);
    }

    echo $returnValue;
}



function getContractCurah($stockpileId, $vendorId) {
    global $myDatabase;
    $returnValue = '';
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

function setPlanPayDate($todayDate){
    global $myDatabase;
    $returnValue = '';

    $sql = "CALL PlanPayDate('{$todayDate}')";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    while ($row = $result->fetch_object()){
        $date = $row->tglBayar;
    }
    $newDate = date_create($date);
   // echo date_format($newDate,"d/m/Y");
    $returnValue ='|' . date_format($newDate,"d/m/Y");
    
    echo $returnValue;
}

function getVendorBank($vendorId, $paymentFor)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $joinProperty = '';

    if ($paymentFor == 0 || $paymentFor == 1) {//PKS / Curah
        $sql = "SELECT v_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM vendor_bank WHERE vendor_id = {$vendorId}";

    } else if ($paymentFor == 2) {//Freight
        $sql = "SELECT f_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM freight_bank WHERE freight_id = {$vendorId}";

    } else if ($paymentFor == 3) {//Labor
        $sql = "SELECT l_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM labor_bank WHERE labor_id = {$vendorId}";

    } else if ($paymentFor == 8 || $paymentFor == 10) {//Invoice
        $sql = "SELECT gv_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM general_vendor_bank WHERE general_vendor_id = {$vendorId}";

    } else if ($paymentFor == 9) {//vendor_hanndling
        $sql = "SELECT vh_bank_id AS vBankId, CONCAT(bank_name,' - ',account_no) AS bank_name FROM vendor_handling_bank WHERE vendor_handling_id = {$vendorId}";

    }


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

//KONTRAK (PO No.)
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
				AND c.`adjustment_acc` != 52";
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

function refreshSummary($stockpileContractId, $ppn, $pph, $paymentType) //bagaimana caranya data yg sudah di select di insert ke table baru?
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
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
						AND payment_method = 1 AND payment_status = 0
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

        //KONTRAK
        if ($result->num_rows == 1) { 
            $row = $result->fetch_object();

            $dppTotalPrice = 0;
            if ($row->pph_tax_id == 0) { //kontrak, 
                $dppTotalPrice = $row->total_price;
            } else { //ga kepake
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = $row->total_price / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->total_price;
                }
            }

            $totalPPN = 0;
            if ($row->ppn_tax_id != 0) { //kontrak
                $totalPPN = $dppTotalPrice * ($row->ppn_tax_value / 100); 
            }

            if ($ppn == 'NONE') { //$ppn dari parameter
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
            $returnValue .= '<thead>
                             <tr>   
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                </tr></thead>';
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

            // if ($paymentMethod == 1) {
                // Testing
                // $returnValue .= '<tr>';
                // $returnValue .= '<td colspan="3" style="text-align: right;">Testing</td>';
                // $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="test" id="test" value="' . $stockpileContractId. '" /></td>';
                // $returnValue .= '</tr>';

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="3" style="text-align: right;">PPN</td>';
                $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($ppnValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', this, document.getElementById(\'pph\'));" /></td>';
                $returnValue .= '</tr>';
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="3" style="text-align: right;">PPh</td>';
                $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($pphValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', document.getElementById(\'ppn\'), this);" /></td>';
                $returnValue .= '</tr>';
            // } else {
            //     if ($ppnValue > 0) {
            //         $returnValue .= '<tr>';
            //         $returnValue .= '<td colspan="3" style="text-align: right;">PPN</td>';
            //         $returnValue .= '<td style="text-align: right;">' . number_format($ppnValue, 2, ".", ",") . '</td>';
            //         $returnValue .= '</tr>';
            //     } 

            //     if ($pphValue > 0) {
            //         $returnValue .= '<tr>';
            //         $returnValue .= '<td colspan="3" style="text-align: right;">PPh</td>';
            //         $returnValue .= '<td style="text-align: right;">' . number_format($pphValue, 2, ".", ",") . '</td>';
            //         $returnValue .= '</tr>';
                    
            //     } 
            // }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="3" style="text-align: right;">Pembulatan</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 0, ".", ",") . '</td>';
            $return_val .= '<input type="hidden" value="|'. number_format((($dppTotalPrice + round($ppnValue, 2)) - round($pphValue, 2) - $row->down_payment - $row->payment), 0, ".", ",") . '|" />';  
            $returnValue .= '</tr>';
            $returnValue .= '</tfoot>';
            $returnValue .= '</table>';

            // dibawah ini untuk apa?
            $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $row->down_payment . '" />';
            $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $row->quantity . '" />';
            $returnValue .= '<input type="hidden" id="price" name="price" value="' . $row->price . '" />';
            $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dppTotalPrice . '" />';
            $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . ((round($dppTotalPrice, 0) + round($ppnValue, 0)) - round($pphValue, 0)) . '" />'; //digunakan untuk $totalprice di data-processing-Ppayment
            $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . ((round($dppTotalPrice, 0) + round($ppnValue, 0)) - round($pphValue, 0) - $row->down_payment - $row->payment) . '" />';
            $returnValue .= '<input type="hidden" id="currencyId" name="currencyId" value="1" />';            
            $returnValue .= '</div>';
        }
    } else {
        $sql = "SELECT c.`quantity`, c.`contract_no`, c.price_converted, c.adjustment_notes, c.`adjustment`, c.`adjustment_ppn`, cur.currency_code,
				ROUND(c.`price_converted` * c.`adjustment`,2) AS dpp,
				CASE WHEN c.`adjustment_ppn` = 1 THEN (SELECT ppn FROM vendor WHERE vendor_id = c.`vendor_id`)
				ELSE 0 END AS ppn_value
				FROM stockpile_contract sc
				LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
				LEFT JOIN currency cur ON cur.currency_id = c.`currency_id`
				WHERE sc.`stockpile_contract_id` = {$stockpileContractId}
				AND c.company_id = {$_SESSION['companyId']}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result->num_rows == 1) {
            $row = $result->fetch_object();
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
            $returnValue .= '<thead>
                            <tr>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Adjustment Notes</th>
                                <th>Quantity Adjustment</th>
                                <th>DPP</th>
                                <th>PPN</th>
                                <th>Total</th>
                            </tr>
                            </thead>';
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


            // if ($paymentMethod == 1) {
                $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $row->quantity . '" />';
                $returnValue .= '<input type="hidden" id="price" name="price" value="' . $row->price . '" />';
                $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dppTotalPrice . '" />';
                $returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($ppnValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', this, document.getElementById(\'pph\'));" />';
                $returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($pphValue, 2, ".", ",") . '" onblur="refreshSummary(' . $stockpileContractId . ', document.getElementById(\'ppn\'), this);" />';
            // } 
            $returnValue .= '</table>';
            $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $row->quantity . '" />';
            $returnValue .= '<input type="hidden" id="price" name="price" value="' . $row->price . '" />';
            $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dppTotalPrice . '" />';
            $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($total, 0) . '" />';
            $returnValue .= '</div>';
        }
    }

  //  $returnValue .= '<input type="hidden" value="|'. number_format((1), 0, ".", ",") . '|" />';  
    echo $returnValue;
    echo $return_val;
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
            if ($checkedSlips != '') { //untuk checklist
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

function getVendorPayment($stockpileId)
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
            AND con.contract_type = 'C'
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


// CURAH
function setSlipCurah($stockpileId, $vendorId, $contractCurah, $checkedSlips, $ppn, $pph, $paymentFrom1, $paymentTo1, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $whereProperty = '';
    $boolCheked = false;
    $whereContractIds = '';

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

    if ($contractCurahs != '' && $idPP == '') {
        $whereContractIds = "AND sc.contract_id IN ({$contractCurahs})";   
    }

        //jika Contract yg dipilih sebelumnya ALL
    if($contractCurah == '' && $idPP != ''){
        $pksAll = "SELECT contract_id FROM stockpile_contract sc 
                            INNER JOIN transaction t ON t.stockpile_contract_id = sc.stockpile_contract_id 
                            LEFT JOIN payment_curah cur ON cur.transaction_id = t.transaction_id
                    WHERE cur.idPP = {$idPP} GROUP BY contract_id";
        $resultpksall = $myDatabase->query($pksAll, MYSQLI_STORE_RESULT);
        if ($resultpksall !== false && $resultpksall->num_rows > 0) {
            while( $rowcon = $resultpksall->fetch_object()){
                $sqlpks = "SELECT * FROM pengajuan_pks_contract WHERE idPP = {$idPP} AND contract_id = {$rowcon->contract_id}";
                $resultpks = $myDatabase->query($sqlpks, MYSQLI_STORE_RESULT);
              //  echo $resultpks->num_rows;

                if ($resultpks->num_rows == 0) {
                    $sql1 = "INSERT INTO pengajuan_pks_contract(idPP, contract_id) values ({$idPP}, {$rowcon->contract_id})";
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                }
            }
        }
    }

    if($idPP == ''){
        $whereProperty = "AND t.company_id = {$_SESSION['companyId']}
                        AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                          AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo1', '%d/%m/%Y')
                          -- AND t.payment_id IS NULL 
                          AND t.curah_payment_status <> 1";
    }else if($idPP != ''){
        $selectPaymentC = "pc.qty, pc.dpp, pc.total_amount as totalAmount, pc.ppn_id, pc.ppn_value, pc.pph_id, pc.pph_value, pp.total_dpp,  
                             pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND pc.idPP = {$idPP}";
        $wherePengajuan = " LEFT JOIN payment_curah pc ON pc.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = pc.idpp ";
    }

    $sql = "SELECT {$selectPaymentC} t.*, con.vendor_id,con.contract_no,con.po_no, v.vendor_name,
                v.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
                (SELECT max(total_amount) FROM payment_curah WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_curah,
                (SELECT max(qty) FROM payment_curah WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS qty_curah
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
            {$wherePengajuan}
            WHERE con.contract_type = 'C'
            AND con.vendor_id = {$vendorId}
            AND sc.stockpile_id = {$stockpileId}
            {$whereContractIds}
            {$whereProperty}
            {$whereIdPP}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<input type="hidden" id="contractIds" name="contractIds" value="' . $contractCurahs . '" />';  //khusus  kode vendor_ID
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onClick="checkAllCurah(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th></th>
                                <th>Transaction Date</th>
                                <th>PO No.</th><th>Contract No.</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                         </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $totalQty = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $unitPrice = 0;
            $curahQty = 0;
            $vendorName = '';

            $unitPrice = round($row->unit_price,10);
            $curahQty = $row->quantity - $row->qty_curah;
            $vendorName = $row->vendor_name;
            // echo $unitPrice;
            if ($row->pph_tax_id == 0) {
                $dppTotalPrice = round(($unitPrice * $curahQty),10);
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = round(($unitPrice * $curahQty) / ((100 - $row->pph_tax_value) / 100),10);
                } else {
                    $dppTotalPrice = round(($unitPrice * $curahQty),10);
                }
            }
            $returnValue .= '<tr>';
            if($idPP == ''){
                if ($checkedSlips != '') {
                    $boolCheked = true;
                    $pos = strpos($checkedSlips, $row->transaction_id);

                    if ($pos === false) {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ','. "'". $contractCurahs ."'".', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
                    } 
                    else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ','. "'". $contractCurahs ."'".', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" checked /></td>';
                        $totalPrice = $totalPrice + $dppTotalPrice;

                        if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                            $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                            if($row->ppn_tax_value > 0){
                                $ppntext = round($row->ppn_tax_value,2) .'%';
                            }else{
                                $ppntext = '';
                            }
                        }

                        if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                            $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                            if($row->pph_tax_value > 0){
                                $pphtext = round($row->pph_tax_value,2) .'%';
                            }else{
                                $pphtext = '';
                            }
                        }
                        $totalQty = ($totalQty + $row->quantity);
                    }
                } else {
                    $boolCheked = false;
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ','. "'". $contractCurahs ."'".', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
                }
            }else{
                $boolCheked = true;
                $dppTotalPrice = $row->totalAmount;
                $totalPrice = $row->total_amount;
                $totalPPN = $row->total_ppn_amount;
                $totalPPh = $row->total_pph_amount;
                $grandTotal = $row->grand_total;
                if ($row->ppn_id != 0 && $row->ppn_id != '') {
                    if($row->ppn_id > 0){
                        $ppntext = round($row->ppn_value,2) .'%';
                       
                    }else{
                        $ppntext = '';
                    }
                }
                if ($row->pph_id != 0 && $row->pph_id != '') {
                    if($row->pph_value > 0){
                        $pphtext = round($row->pph_value,2) .'%';
                    }else{
                        $pphtext = '';
                    }
                }
                $returnValue .= '<td><input type="hidden" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ','. "'". $contractCurahs ."'".', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';

            }
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->contract_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 3%;">' . number_format($curahQty, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 2%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 5%;">' . number_format($row->unit_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 25%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($boolCheked) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">PPN '.$ppntext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" readonly class="span12" readonly name="totalPPn" id="totalPPn" value="' . number_format($totalPPN, 2, ".", ",") . '" onblur="checkSlipCurah(' . $stockpileId . ',' . $vendorId . ', '. "'". $contractCurahs ."'".', this, document.getElementById(\'pph\'), \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '</tr>';
         // $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8"  style="text-align: right;">PPh'.$pphtext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" readonly class="span12" readonly name="totalPPh" id="totalPPh" value="' . number_format($totalPPh, 2, ".", ",") . '" onblur="checkSlipCurah(' . $stockpileId . ',' . $vendorId . ', '. "'". $contractCurahs ."'".', document.getElementById(\'ppn\'), this, \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '</tr>';

            $grandTotal = ($totalPrice + $totalPPN) - $totalPPh ;

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';

        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />';
        $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />';

        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  

        $returnValue .= '</div>';
    }

   // $return_val = '|' . $grandTotal;
   echo $returnValue;
    echo $return_val;
}

function setSlipCurah_settle($stockpileId, $vendorId, $settle, $contractCurah, $checkedSlips, $checkedSlipsDP, $ppn, $pph, $paymentFromCur, $paymentToCur, $idPP) {
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $whereContractIds = '';
    $boolCheked = false;
    $selectPaymentC = '';
    $whereIdPP = '';
    $wherePengajuan = '';
    $whereProperty = '';
	
  //  echo "SS " . $settle;
	if($checkedSlips == ''){
        for ($i = 0; $i < sizeof($contractCurah); $i++) {
            if($contractIds == '') {
                $contractIds .=  $contractCurah[$i];
            } else {
                $contractIds .= ','. $contractCurah[$i];
            }
        }
	}else{
		$contractIds = $contractCurah;
	}

    if ($contractIds != 0 && $idPP == '') {
        $whereContractIds = "	AND sc.contract_id IN ({$contractIds})";
    }

    //jika Contract yg dipilih sebelumnya ALL
    if($contractCurah == '' && $idPP != ''){
        $pksAll = "SELECT contract_id FROM stockpile_contract sc 
                            INNER JOIN transaction t ON t.stockpile_contract_id = sc.stockpile_contract_id 
                            LEFT JOIN payment_curah cur ON cur.transaction_id = t.transaction_id
                    WHERE cur.idPP = {$idPP} GROUP BY contract_id";
        $resultpksall = $myDatabase->query($pksAll, MYSQLI_STORE_RESULT);
        if ($resultpksall !== false && $resultpksall->num_rows > 0) {
            while( $rowcon = $resultpksall->fetch_object()){
                $sqlpks = "SELECT * FROM pengajuan_pks_contract WHERE idPP = {$idPP} AND contract_id = {$rowcon->contract_id}";
                $resultpks = $myDatabase->query($sqlpks, MYSQLI_STORE_RESULT);
              //  echo $resultpks->num_rows;

                if ($resultpks->num_rows == 0) {
                    $sql1 = "INSERT INTO pengajuan_pks_contract(idPP, contract_id) values ({$idPP}, {$rowcon->contract_id})";
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                }
            }
        }
    }

    if($idPP != ''){ 
        $selectPaymentC = "pc.qty, pc.dpp, pc.total_amount as totalAmount, pc.ppn_id, pc.ppn_value, pc.pph_id, pc.pph_value, pp.total_dpp,  
                            pp.total_amount as pengajuanAmount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND pc.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_curah pc ON pc.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = pc.idpp ";
    }else if($idPP == ''){
        $whereProperty = "   AND t.company_id = {$_SESSION['companyId']}
                            AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromCur', '%d/%m/%Y') AND STR_TO_DATE('$paymentToCur', '%d/%m/%Y')
                            -- AND t.payment_id IS NULL 
                            AND t.curah_payment_status <> 1
                            AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))";
        // $wherePengajuan = " LEFT JOIN payment_ pao ON pao.transaction_id = t.transaction_id AND pao.status = 0";
    }

    $sql = "SELECT {$selectPaymentC} t.*, con.vendor_id,con.contract_no,con.po_no,
            v.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
            txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
            (SELECT max(total_amount) FROM payment_curah WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_curah,
            (SELECT max(qty) FROM payment_curah WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS qty_curah
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
        {$wherePengajuan}
        WHERE con.contract_type = 'C'
        AND con.vendor_id = {$vendorId}
        AND sc.stockpile_id = {$stockpileId}
        {$whereContractIds}
        {$whereProperty}
        {$whereIdPP}";
      //  echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<input type="hidden" id="contractIds" name="contractIds" value="' . $contractIds . '" />';  //khusus  kode vendor_ID
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onClick="checkAllCurah(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th></th>
                                <th>Transaction Date</th>
                                <th>PO No.</th><th>Contract No.</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                         </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $totalQty = 0;
        $price = 0;
        // START WHILE
        while($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $unitPrice = 0;
            $curahQty = 0;

            $unitPrice = round($row->unit_price,10);
            $curahQty = $row->quantity - $row->qty_curah;

            if ($row->pph_tax_id == 0) {
                $dppTotalPrice = round(($unitPrice * $curahQty),10);
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = round(($unitPrice * $curahQty) / ((100 - $row->pph_tax_value) / 100),10);
                } else {
                    $dppTotalPrice = round(($unitPrice * $curahQty),10);
                }
            }
			
    $dppPrice_dppShrink = $dppTotalPrice;

    $returnValue .= '<tr>';
    // SELECTED NOTIM
        if($idPP == ''){
            if($checkedSlips != '') {
                $boolCheked = true;
                $pos = strpos($checkedSlips, $row->transaction_id);
                if($pos === false) {
                    $returnValue .= '<td style="width: 1%;"><input   type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipCurah1('. $stockpileId .', '. $row->vendor_id .', '. "'".$settle ."'" .', '.  "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" /></td>';
                } else {
                    $returnValue .= '<td style="width: 1%;"><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipCurah1('. $stockpileId .', '. $row->vendor_id .', '. "'".$settle ."'" .', '. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" checked /></td>';
                    $totalPrice = $totalPrice + $dppTotalPrice;
                    // $totalPrice2 = $totalPrice2 + $dppTotalPrice;
                            
                    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                        if($row->ppn_tax_value > 0){
                            $ppntext = round($row->ppn_tax_value,2) .'%';
                        }else{
                            $ppntext = '';
                        }
                    }

                    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                        if($row->pph_tax_value > 0){
                            $pphtext = round($row->pph_tax_value,2) .'%';
                        }else{
                            $pphtext = '';
                        }
                    }
                        $totalQty = ($totalQty + $curahQty);
                    }
            } else {
                $boolCheked = false;
                    $returnValue .= '<td style="width: 1%;" ><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipCurah1('. $stockpileId .', '. $row->vendor_id .', '. "'".$settle ."'" .', '. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" /></td>';
            }
        }else{
            $boolCheked = true;
            $curahQty = $row->qty;
            $dppTotalPrice = $row->totalAmount;
            $totalPrice = $row->pengajuanAmount;
            $totalPPN = $row->total_ppn_amount;
            $totalPPh = $row->total_pph_amount;
            $grandTotal = $row->grand_total;

            if ($row->ppn_id != 0 && $row->ppn_id != '') {
                if($row->ppn_id > 0){
                    $ppntext = round($row->ppn_value,10) .'%';
                   
                }else{
                    $ppntext = '';
                }
            }
            if ($row->pph_id != 0 && $row->pph_id != '') {
                if($row->pph_value > 0){
                    $pphtext = round($row->pph_value,10) .'%';
                }else{
                    $pphtext = '';
                }
            }
            $returnValue .= '<td style="width: 1%;" ></td>';
           
        }
        $returnValue .= '<td style="width: 5%;">'. $row->slip_no .'</td>';
		$returnValue .= '<td style="width: 5%;">'. $row->transaction_date .'</td>';
		$returnValue .= '<td style="width: 5%;">'. $row->po_no .'</td>';
		$returnValue .= '<td style="width: 5%;">'. $row->contract_no .'</td>';
        $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($curahQty, 0, ".", ",") .'</td>';
        $returnValue .= '<td style="width: 2%;">IDR</td>';
        $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($row->unit_price, 2, ".", ",") .'</td>';
        $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($dppTotalPrice, 2, ".", ",") .'</td>';
        $returnValue .= '</tr>';
    }
        $returnValue .= '</tbody>';
    // END WHILE
    
        // SELECTED DP1
        if($boolCheked) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Total DPP</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
               
            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;
            
            if($idPP == ''){
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
            }
            
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">PPN '.$ppntext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPn" id="totalPPn" value="' . number_format($totalPPN, 2, ".", ",") . '" onblur="checkSlipCurah1(' . $stockpileId . ', ' . $vendorId . ',' . "'" . $contractIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFromCur . '\', \'' . $paymentToCur . '\', '. $idPP .');" /></td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">PPh '.$pphtext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPh" id="totalPPh" value="' . number_format($totalPPh, 2, ".", ",") . '" onblur="checkSlipCurah1(' . $stockpileId . ', ' . $vendorId . ',' .  "'". $contractIds."'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFromCur . '\', \'' . $paymentToCur . '\', '. $idPP .');" /></td>';
            $returnValue .= '</tr>';

//---------------------------------------Down Payment-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------          
        if($idPP == ''){
			$sqlAA = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`, p.ppn_amount ,p.pph_amount, v.pph, c.`po_no`, c.`contract_no`,v.ppn,
                                (SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code, 
                    (SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
                    (SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,
                    CASE WHEN p.payment_location = 0 THEN 'HOF'
                            ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) 
                        END AS payment_location

                        FROM payment p 
                        LEFT JOIN contract c ON c.`contract_id` = p.`curahContract` 
                        LEFT JOIN vendor v ON v.vendor_id = p.vendor_id
                        WHERE p.vendor_id = {$vendorId} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
            $resultAA = $myDatabase->query($sqlAA, MYSQLI_STORE_RESULT);
            $DpCount = $result->num_rows;
           // echo $sqlAA;

			if ($resultAA->num_rows > 0) {
                $totalDownPayment = 0;
                $totalDppDpHC = 0;
                while($row = $resultAA->fetch_object()) {
                    $dppDpHC = 0;
                    $downPayment = 0;
                    // $dpHC = $row->amount_converted;
                    $dppDpCur = $row->amount_converted;
                    $dpPPn = $dppDpCur * ($row->ppn/100);
                    $dpPPh = $dppDpCur * ($row->pph/100);
                    // $dp = ($dppDpHC + $dpPPn) - $dpPPh ;
                    $downPayment = ($dppDpCur + $dpPPn) - $dpPPh ;

                    #PAYMENT VOUCHER
                    $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
                            
                    if($row->bank_type == 1) {
                        $voucherCode .= ' - B';
                    } elseif($row->bank_type == 2) {
                        $voucherCode .= ' - P';
                    } elseif($row->bank_type == 3) {
                        $voucherCode .= ' - CAS';
                    }
                    
                    if($row->bank_type != 3) {
                    if($row->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                        }
                    }
                    
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="3" style="text-align: right;">Down Payment</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $voucherCode .' # '.$row->payment_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. number_format($dppDpCur, 2, ".", ",").'</td>';
                    
                    //SELECTED DP 2
                    if($checkedSlipsDP != '') {
                        $posDP = strpos($checkedSlipsDP, $row->payment_id);
                        if($posDP === false) {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipCurahDP('. $stockpileId .', '. $vendorId .',  '. "'".$settle ."'" .','. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" /></td>';
                        } else {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipCurahDP('. $stockpileId .', '. $vendorId .', '. "'".$settle ."'" .', '. "'". $contractIds."'".',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" checked /></td>';
                            $totalDownPayment = $totalDownPayment + $downPayment; 
                            $totalDppDpCur = $totalDppDpCur + $dppDpCur; 
                            if($dppDpCur > $totalPrice){
                                $disabled = 'disabled';
                            } else{
                                $disabled = '';
                            }
                        }
                    } else {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipCurahDP('. $stockpileId .', '. $vendorId .',  '. "'".$settle ."'" .','. "'". $contractIds."'".',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" /></td>';
                    }
                    $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                    $returnValue .= '</tr>';
                }
            }
            
            // by Yeni
            
            if($settle == 0 ){
                $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $totalDownPayment;
                $DppGrandTotal = $totalPrice - $totalDppDpCur;
			}else if($settle == 1){
                $grandTotal = 0;
			}
            
            // by Yeni
            if($grandTotal < 0 && $totalDownPayment > 0 || ($grandTotal > 0 && $totalDownPayment > 0 && $settle == 2)) {
                $grandTotal = 0;
				// $downPaymentHC = $totalPrice2;
            }

            if($DpCount > 0){ //settle != 2 => 
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="6" style="text-align: right;">Settlement Only ?</td>';
                if($settle == 1) {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle_Curah('. $stockpileId .', '. $vendorId .', '. "'".$settle ."'" .', '."'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');" checked/></td>';
                } else {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" '. $disabled.' id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle_Curah('. $stockpileId .', '. $vendorId .', '. "'".$settle ."'" .', '. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromCur .'\', \''. $paymentToCur .'\', '. $idPP .');"  /></td>';                    
                }
                $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>'; //for settlement 50%
            }else{
                $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>'; //for settlemetn 100%
            }
        }else{ // VIEW DATA
            $sql = "SELECT ppd.*, CASE WHEN pp.settlement_status = 0 THEN 'No' ELSE 'Yes' END AS settle FROM pengajuan_payment_dp ppd 
                    LEFT JOIN pengajuan_payment pp ON pp.idPP = ppd.idPP WHERE pp.idPP = {$idPP}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
           // echo $sql;
            $DpCount = $result->num_rows;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_object()) {
                    $tempSettle = $row->settle;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="4" style="text-align: right;">Down Payment</td>';
                    $returnValue .= '<td  style="text-align: right;">'.$row->voucher_code.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. number_format(($row->dpp_dp_settle), 2, ".", ",").'</td>';
                    $returnValue .= '<td style="text-align: right;">'. number_format(($row->settle_amount), 2, ".", ",") .'</td>';
                    $returnValue .= '</tr>';
                 //   $returnValue .= '</tr>';
                }
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right; color:red;">Settlement Only ?</td>';
            $returnValue .= '<td colspan="1" style="text-align: right;">'. $tempSettle.'</td>';
            $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>';
        }

            // $returnValue .= '<tr>';
            // $returnValue .= '<td colspan="9" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
            
            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
	    $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="'. round($totalPrice, 2) .'" />';
		$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '<input type="hidden" id="DppGrandTotal" name="DppGrandTotal" value="'. $DppGrandTotal .'" />';
		$returnValue .= '<input type="hidden" id="totalDownPayment" name="totalDownPayment" value="'. $totalDownPayment .'" />';
        $returnValue .= '<input type="hidden" id="totalDppDpCur" name="totalDppDpCur" value="'. round($totalDppDpCur, 2) .'" />';
        $returnValue .= '<input type="hidden" id="settle" name="settle" value="'. $settle .'" />';
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />'; 
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  

        $returnValue .= '</div>';
    }
    echo $returnValue;
    echo $return_val;
}


function getCurahTax($vendorIdCurahDp)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT ROUND(v.ppn,2) AS cppn, ROUND(v.pph,2) AS cpph, v.ppn_tax_id, v.pph_tax_id  
            FROM vendor v WHERE v.vendor_id = {$vendorIdCurahDp}";
    echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn = $row->cppn;
        $pph = $row->cpph;
        $ppnID = $row->ppn_tax_id;
        $pphID = $row->pph_tax_id;

        // $ppnAmount = round(($ppn / 100) * $amount,2);
        // $pphAmount = round(($pph / 100) * $amount,2);

        $returnValue = '|' . number_format($ppn, 2, ".", ",") . '|' . number_format($pph, 2, ".", ",") . '|' . $ppnID . '|' . $pphID;

    } else {
        $returnValue = '|0|0|0|0';
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

function getVendorFreight($stockpileId) //PENGAJUAN PUNYA
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
    echo $sql;
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

function getSupplier($stockpileId, $freightId)
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
           // echo $sql;
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

function getContractFreight($stockpileId, $freightId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT c.* FROM contract c
            LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
            LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
            LEFT JOIN freight_cost vhc ON vhc.`freight_cost_id` = t.`freight_cost_id`
            WHERE vhc.`stockpile_id` = {$stockpileId} AND vhc.`freight_id` = {$freightId} AND t.`fc_payment_id` IS NULL
            GROUP BY t.stockpile_contract_id";
           // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
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

function getContractFreightDp($stockpileId, $freightId, $vendorId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT c.* FROM contract c
            LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
            LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id`
            LEFT JOIN freight_cost vhc ON vhc.`freight_cost_id` = t.`freight_cost_id`
            WHERE vhc.`stockpile_id` = {$stockpileId} AND vhc.`freight_id` = {$freightId} AND c.vendor_id = {$vendorId} AND t.`fc_payment_id` IS NULL
            GROUP BY t.stockpile_contract_id";
            // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
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

function getFreightTax($freightId)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT round(ppn,2) as fppn, round(pph,2) as fpph, ppn_tax_id, pph_tax_id FROM freight WHERE freight_id = {$freightId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn = $row->fppn;
        $pph = $row->fpph;
        $ppnID = $row->ppn_tax_id;
        $pphID = $row->pph_tax_id;

        // $ppnAmount = round(($ppn / 100) * $amount,2);
        // $pphAmount = round(($pph / 100) * $amount,2);

        $returnValue = '|' . number_format($ppn, 2, ".", ",") . '|' . number_format($pph, 2, ".", ",") . '|' . $ppnID . '|' . $pphID;

    } else {
        $returnValue = '|0|0|0|0';
    }

    echo $returnValue;
}

function getNoKontrak($vendorFreightIdDp)
{
    global $myDatabase;
    $returnValue = '';
    $tempWhereProperty = '';
    
    $sql = "SELECT DISTINCT(con.contract_id), con.contract_no
            FROM contract con
            LEFT JOIN vendor v ON v.vendor_id = con.vendor_id
            WHERE v.`vendor_id` = {$vendorFreightIdDp}
            ORDER BY con.contract_id ASC";
            //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->contract_id . '||' . $row->contract_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->contract_id . '||' . $row->contract_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    
    //$returnValue = '~ 1'.$sql;
    echo $returnValue;
}

function setSlipDp($idpp)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT pp.*
            FROM pengajuan_payment pp
            WHERE pp.idpp = {$idpp}";
      //  echo $sql;
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
								<th>DPP</th>
								<th>PPN </th>
								<th>PPh</th>
								<th>Total Amount</th>
							</tr>
						</thead>';
        $returnValue .= '<tbody>';

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
            
            $returnValue .= '<tr>';

            $returnValue .= '<td >' . $row['slip_no'] . '</td>';
            $returnValue .= '<td>' . $row['po_no'] . '</td>';
			
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['total_qty'], 2, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['price'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['total_dpp'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['total_ppn_amount'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['total_pph_amount'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['grand_total'], 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
			
		$totalQty = $totalQty + $row['total_qty'];
        $totalDpp = $totalDpp + $row['total_dpp'];
		$totalPPN = $totalPPN + $row['total_ppn_amount'];
		$totalPPh = $totalPPh + $row['total_pph_amount'];
		$totalAmount = $totalAmount + $row['grand_total'];
		// $invoiceBankId = $row['vendor_bank_id'];
		// $invoiceCurrencyId = $row['currency_id'];
        }

        $returnValue .= '</tbody>';
        $returnValue .= '<tfoot>';
		
        $grandTotal = $totalAmount;

        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';

        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';		
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function setSlipFreight_1($stockpileId_1, $freightId_1, $contractFreight, $checkedSlips, $ppn, $pph, $paymentFromFP, $paymentToFP, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $whereContractIds = '';
    $boolCheked = false;
    $selectPaymentOA = '';
    $whereIdPP = '';
    $whereProperty = '';
    $return_val = '';
    $wherePengajuan = '';

    if ($checkedSlips == '') {
        for ($i = 0; $i < sizeof($contractFreight); $i++) {
            if ($contractIds == '') {
                $contractIds .= $contractFreight[$i];
            } else {
                $contractIds .= ',' . $contractFreight[$i];
            }
        }
    } else {
        $contractIds = $contractFreight;
    }

    if ($contractIds != 0 && $idPP == '') {
        $whereContractIds = "AND sc.contract_id IN ({$contractIds})";
    }

    //jika Contract yg dipilih sebelumnya ALL
    if($contractFreight == '' && $idPP != ''){
        $pksAll = "SELECT contract_id FROM stockpile_contract sc 
                            INNER JOIN transaction t ON t.stockpile_contract_id = sc.stockpile_contract_id 
                            LEFT JOIN payment_oa pao ON pao.transaction_id = t.transaction_id
                    WHERE pao.idPP = {$idPP} GROUP BY contract_id";
        $resultpksall = $myDatabase->query($pksAll, MYSQLI_STORE_RESULT);
        if ($resultpksall !== false && $resultpksall->num_rows > 0) {
            while( $rowcon = $resultpksall->fetch_object()){
                $sqlpks = "SELECT * FROM pengajuan_pks_contract WHERE idPP = {$idPP} AND contract_id = {$rowcon->contract_id}";
                $resultpks = $myDatabase->query($sqlpks, MYSQLI_STORE_RESULT);
              //  echo $resultpks->num_rows;

                if ($resultpks->num_rows == 0) {
                    $sql1 = "INSERT INTO pengajuan_pks_contract(idPP, contract_id) values ({$idPP}, {$rowcon->contract_id})";
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                }
            }
        }
    }

    if($idPP != ''){ 
        $selectPaymentOA = "pao.qty, pao.dpp, pao.shrink as dppShrink, pao.additional_shrink, pao.total_amount as totalAmount, pao.ppn_id, pao.ppn_value, pao.pph_id, pao.pph_value, pp.total_dpp, pp.total_shrink,  pp.total_shrink2,
                            pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, pp.price, ";
        $whereIdPP = " AND pao.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_oa pao ON pao.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = pao.idpp ";
    }else if($idPP == ''){
        $whereProperty = "AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId_1})
                          AND t.company_id = {$_SESSION['companyId']}
                        --   AND t.fc_payment_id IS NULL 
                          AND freight_price != 0 and (t.adj_oa IS NULL OR t.adj_oa = 0)
                          AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                          AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromFP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToFP', '%d/%m/%Y')
                       --   AND t.fc_payment_id IS NULL 
                          AND t.fc_payment_status <> 1";
        // $wherePengajuan = " LEFT JOIN payment_oa pao ON pao.transaction_id = t.transaction_id AND pao.status = 0";
    }

    //start edit by eva
    $sql = "SELECT {$selectPaymentOA} COALESCE(hsw.amt_claim) as hsw_amt_claim, COALESCE(ts.amt_claim,0) AS amt_claim, 
                 sc.stockpile_id, f.freight_rule, txpph.tax_id AS pph_tax_id, 
                txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
                    ts.`trx_shrink_claim`,
                ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
                    WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
                    WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1
                    WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                        ELSE 0 END,10) AS qtyClaim, 
                fc.freight_id, con.po_no, 
                    f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                    v.vendor_code,
                v.vendor_name,
                (SELECT MAX(total_amount) FROM payment_oa WHERE transaction_id = t.transaction_id AND payment_method = 3 AND STATUS = 0) AS split_oa,
                (SELECT max(qty) FROM payment_oa WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS qty_oa,
                (SELECT max(shrink) FROM payment_oa WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_shrink,
                (SELECT max(additional_shrink) FROM payment_oa WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_shrink2,
                f.freight_supplier,
                t.*
            FROM TRANSACTION t
            LEFT JOIN freight_cost fc ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight f ON f.freight_id = fc.freight_id
            LEFT JOIN tax txppn ON txppn.tax_id = f.ppn_tax_id
            LEFT JOIN tax txpph ON txpph.tax_id = t.fc_tax_id
            LEFT JOIN vendor v ON fc.vendor_id = v.vendor_id
            LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id
            LEFT JOIN contract con ON con.contract_id = sc.contract_id
            LEFT JOIN stockpile s ON s.`stockpile_id` = sc.`stockpile_id`
            LEFT JOIN transaction_shrink_weight ts ON t.transaction_id = ts.transaction_id
            LEFT JOIN transaction_additional_shrink hsw ON t.transaction_id = hsw.transaction_id
            {$wherePengajuan}
            WHERE fc.freight_id = {$freightId_1}
			AND sc.stockpile_id = {$stockpileId_1} 
            {$whereContractIds}
			{$whereProperty} 
            {$whereIdPP}
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    //end edit by eva
    //echo $sql;

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        $returnValue .= '<input type="hidden" id="contractIds" name="contractIds" value="' . $contractIds . '" />';  //khusus  kode vendor_ID
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th>
                                <th>Transaction Date</th>
                                <th>PO No.</th>
                                <th>Vendor Code</th>
                                <th>Vendor Name</th>
                                <th>Vehicle No</th>
                                <th>Quantity</th>
                                <th>Freight Cost / kg</th>
                                <th>Amount</th>
                                <th>Shrink Qty Claim</th>
                                <th>Shrink Price Claim</th>
                                <th>Shrink Amount</th>
                                <th>Additional Shrink</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

            $totalAllPrice = 0;
            $totalPPN = 0;
            $totalPPh = 0;
            $totaldppShrink = 0;
            //start add by eva
            $totaldppAddShrink = 0;
            $totalAllShrink = 0;
            //end add by eva
            $totalDpp = 0;
            $totalQty = 0;
            $tempPrice = 0;
            while ($row = $result->fetch_object()) {
                $dppTotalPrice = 0;
                $dppShrinkPrice = 0;
                $qtyShrink = 0;
                $priceShrink = 0;
                $vendorName = '';

                $vendorName = $row->freight_supplier;
                if ($row->freight_rule == 1) {
                    $fq = $row->send_weight - $row->qty_oa;
                    $fp = ($row->freight_price * $fq) ;
                } else {
                    $fq = ($row->freight_quantity - $row->qty_oa);
                    $fp = ($row->freight_price * $fq);
                }

                if($row->hsw_amt_claim != '' && $row->hsw_amt_claim > 0){
                    $tempShrink2 = $row->hsw_amt_claim;
                }else{
                    $tempShrink2 = 0;
                }

                if($row->transaction_date >= '2015-10-05'&& $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
                    $dppTotalPrice = $fp;
                    // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) - $row->split_shrink;
                    $dppShrinkPrice = $row->amt_claim - $row->split_shrink;
                    $hswAmtClaim = $tempShrink2 - $row->split_shrink2; //susut Luar biasa
                }else{ 
                    if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                        $dppTotalPrice = $fp;
                        // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim)-$row->split_shrink;
                        $dppShrinkPrice = $row->amt_claim - $row->split_shrink;
                        $hswAmtClaim = $tempShrink2 - $row->split_shrink2; //susut Luar biasa
                    }else{
                        if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  && $row->stockpile_id == 1){
                            $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100); 
                            // $dppShrinkPrice = (($row->qtyClaim * $row->trx_shrink_claim)-$row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                            $dppShrinkPrice = ($row->amt_claim - $row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                            $hswAmtClaim = ($tempShrink2 - $row->split_shrink2) / ((100 - $row->pph_tax_value) / 100);; //susut Luar biasa
                        } else {
                            if($row->pph_tax_category == 1) {
                                $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                                // $dppShrinkPrice = (($row->qtyClaim * $row->trx_shrink_claim) - $row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                                $dppShrinkPrice = ($row->amt_claim - $row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                                $hswAmtClaim = ($tempShrink2 - $row->split_shrink2)  / ((100 - $row->pph_tax_value) / 100); //susut Luar biasa
            
            
                            }else {
                                $dppTotalPrice = $fp;
                                // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) - $row->split_shrink;
                                $dppShrinkPrice = ($row->amt_claim) - $row->split_shrink;
                                $hswAmtClaim = $tempShrink2 - $row->split_shrink2; //susut Luar biasa
                            }
                        }
                    }
                }

                $freightPrice = 0;
                if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) { //jambi
                    $freightPrice = $fp;
                } else {
                    $freightPrice = $fp;
                }

                //start edit by eva
                // $hswAmtClaim = $row->hsw_amt_claim;
                $dppPrice_dppShrink = $dppTotalPrice - ($dppShrinkPrice + $hswAmtClaim);
                //end edit by eva
                //$dppPrice_dppShrink = $dppTotalPrice - $dppShrinkPrice;
                $priceShrink = $row->trx_shrink_claim;
                $qtyShrink = $dppShrinkPrice/$row->trx_shrink_claim;
            
                $returnValue .= '<tr>';
                if($idPP == ''){
                    if ($checkedSlips != '') {
                        $pos = strpos($checkedSlips, $row->transaction_id);
                        $boolCheked = true; 
                        if ($pos === false) {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" /></td>';
                        } else {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" checked /></td>';
                            $totalAllPrice = $totalAllPrice + $dppPrice_dppShrink;
                            $totaldppShrink = $totaldppShrink + $dppShrinkPrice;
                            //Start add by eva
                            $totaldppAddShrink = $totaldppAddShrink + $hswAmtClaim;
                            $totalAllShrink = $totaldppShrink + $totaldppAddShrink;
                            //end add by eva
                            $totalDpp = $totalDpp + $fp;

                            if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                                $totalPPN = ($totalAllPrice * ($row->ppn_tax_value / 100));
                                if($row->ppn_tax_value > 0){
                                    $ppntext = round($row->ppn_tax_value,2) .'%';
                                   
                                }else{
                                    $ppntext = '';
                                }
                            }

                            if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                                $totalPPh = ($totalAllPrice * ($row->pph_tax_value / 100));
                                if($row->pph_tax_value > 0){
                                    $pphtext = round($row->pph_tax_value,2) .'%';
                                }else{
                                    $pphtext = '';
                                }
                            }

                            $totalQty = ($totalQty + $fq);
                            
                        }
                    } else {
                        $boolCheked = false; 
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" /></td>';
                    }
                }else if($idPP != ''){
                    $boolCheked = true; 
                    $fq = $row->qty;
                    $freightPrice = $row->dpp;
                    $dppShrinkPrice = $row->dppShrink;
                    $qtyShrink = $dppShrinkPrice/$priceShrink;
                    $dppPrice_dppShrink = $row->totalAmount;
                    $totalDpp = $row->total_dpp;
                    $totaldppShrink = $row->total_shrink;
                    $totaldppAddShrink = $row->total_shrink2;
                    $totalAllPrice = $row->total_amount;
                    $totalPpn = $row->total_ppn_amount;
                    $totalPph = $row->total_pph_amount;
                    $grandTotal = $row->grand_total;
                    $tempPrice = $row->price;
                    //start add by eva
                    $totalAllShrink = $totaldppShrink + $totaldppAddShrink;
                    //$totalAllShrink = $row->total_shrink;
                    $totalQty = $totalQty + $row->qty;
                    //end add by eva

                    if ($row->ppn_id != 0 && $row->ppn_id != '') {
                        if($row->ppn_id > 0){
                            $ppntext = round($row->ppn_value,2) .'%';
                           
                        }else{
                            $ppntext = '';
                        }
                    }
                    if ($row->pph_id != 0 && $row->pph_id != '') {
                        if($row->pph_value > 0){
                            $pphtext = round($row->pph_value,2) .'%';
                        }else{
                            $pphtext = '';
                        }
                    }
                    $returnValue .= '<td><input type="hidden" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" /></td>';
                }

                $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->vendor_name . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->vehicle_no . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($fq, 0, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_price, 2, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($freightPrice, 2, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($qtyShrink, 2, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->trx_shrink_claim, 2, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppShrinkPrice, 2, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($hswAmtClaim, 2, ".", ",") . '</td>';
                // End Add by Eva
                $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppPrice_dppShrink, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }
            
            $returnValue .= '</tbody>';
            if($boolCheked) {
                    // Start Add by Eva
                    // $returnValue .= '<tr>';
                    // $returnValue .= '<td colspan="14" style="text-align: right;">Total Quantity </td>';
                    // $returnValue .= '<td style="text-align: right;">' . number_format($totalQty, 2, ".", ",") . '</td>';
                    // $returnValue .= '</tr>';
                    // End Add by Eva
                    
                    $returnValue .= '<tfoot>';
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">Total Dpp </td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($totalDpp, 2, ".", ",") . '</td>';
                    $returnValue .= '</tr>';

                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">Total Shrink</td>';
                    //$returnValue .= '<td style="text-align: right;">' . number_format($totaldppShrink, 2, ".", ",") . '</td>';
                    //start edit by eva
                    $returnValue .= '<td style="text-align: right;">' . number_format($totalAllShrink, 2, ".", ",") . '</td>';
                    //end edit by eva
                    $returnValue .= '</tr>';

                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">Total Amount</td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($totalAllPrice, 2, ".", ",") . '</td>';
                    $returnValue .= '</tr>';

                    if($idPP == ''){
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
                    }

        // $totalPpn = ($ppnValue/100) * $totalPrice;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">PPn '.$ppntext.'</td>';
                    $returnValue .= '<td><input type="text" style="text-align: right;" readonly class="span12" readonly name="totalPPn" id="totalPPn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $freightId_1 . ',' . "'" . $contractIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" /></td>';
                    $returnValue .= '</tr>';
        //$totalPph = ($pphValue/100) * $totalPrice;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">PPh '.$pphtext.'</td>';
                    $returnValue .= '<td><input type="text" style="text-align: right;" readonly class="span12"  readonly name="totalPPh" id="totalPPh" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $freightId_1 . ',' . "'" . $contractIds . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" /></td>';
                    $returnValue .= '</tr>';

                    //edited by alan
                    $grandTotal = ($totalAllPrice + $totalPpn) - $totalPph;

                    if ($grandTotal < 0) {
                        $grandTotal = 0;
                    }
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">Grand Total</td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>'; 
                    $returnValue .= '</tr>';

                    $returnValue .= '</tfoot>';
                }
            $returnValue .= '</table>';
            $returnValue .= '</form>';
            $returnValue .= '<input type="hidden" id="totalAllPrice" name="totalAllPrice" value="' . round($totalAllPrice, 2) . '" />';
            $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
            $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />'; 
            $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|' . $contractIds .'|" />';   //untuk mengisi nilai Amount otomatis
            $returnValue .= '<input type="hidden" id="totaldppShrink" name="totaldppShrink" value="' . round($totaldppShrink, 2) . '" />';
            $returnValue .= '<input type="hidden" id="totaldppShrink2" name="totaldppShrink2" value="' . round($totaldppAddShrink, 2) . '" />';
            $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalDpp, 2) . '" />';
            
            $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />';
            $returnValue .= '<input type="hidden" id="settle" name="settle" value="0" />';
            $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />';

            
            //start add by eva
            //end add by eva

            $returnValue .= '</div>';
        }
    echo $returnValue;
    echo $return_val;
}

function setSlipFreight_settle($stockpileId, $freightId, $settle, $contractFreight, $checkedSlips, $checkedSlipsDP, $ppn, $pph, $paymentFrom, $paymentTo, $idPP) {
    global $myDatabase;
    $returnValue = '';
	$whereProperty = '';
    $return_val = '';
    $selectPaymentOA = '';
    $whereContractIds = '';
    $whereIdPP = '';
    $boolCheked = false; 
    $wherePengajuan = '';


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

    if ($contractIds != 0 && $idPP == '') {
        $whereContractIds = "AND sc.contract_id IN ({$contractFreights})";
    }

    //jika kontrak yg dipilih sebelumnya ALL
    if($contractFreight == '' && $idPP != ''){
        $pksAll = "SELECT contract_id FROM stockpile_contract sc 
                            INNER JOIN transaction t ON t.stockpile_contract_id = sc.stockpile_contract_id 
                            LEFT JOIN payment_oa pao ON pao.transaction_id = t.transaction_id
                    WHERE pao.idPP = {$idPP} GROUP BY contract_id";
        $resultpksall = $myDatabase->query($pksAll, MYSQLI_STORE_RESULT);
        if ($resultpksall !== false && $resultpksall->num_rows > 0) {
            while( $rowcon = $resultpksall->fetch_object()){
                $sqlpks = "SELECT * FROM pengajuan_pks_contract WHERE idPP = {$idPP} AND contract_id = {$rowcon->contract_id}";
                $resultpks = $myDatabase->query($sqlpks, MYSQLI_STORE_RESULT);
                if ($resultpks->num_rows == 0) {
                    $sql1 = "INSERT INTO pengajuan_pks_contract(idPP, contract_id) values ({$idPP}, {$rowcon->contract_id})";
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                }
            }
        }
    }

    if($idPP != ''){ 
        $selectPaymentOA = "pao.qty, pao.dpp, pao.shrink as dppShrink, pao.additional_shrink, pao.total_amount as totalAmount, pao.ppn_id, pao.ppn_value, pao.pph_id, pao.pph_value, pp.total_dpp, pp.total_shrink,  pp.total_shrink2,
                            pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND pao.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_oa pao ON pao.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = pao.idpp ";
    }else if($idPP == ''){
        $whereProperty = "AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                        AND t.company_id = {$_SESSION['companyId']}
                        AND freight_price != 0 and (t.adj_oa IS NULL OR t.adj_oa = 0)
                        AND t.fc_payment_status <> 1
                        AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                        AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')";
    }
    
    //start edit by eva
    $sql = "SELECT {$selectPaymentOA} COALESCE(hsw.amt_claim) as hsw_amt_claim, COALESCE(ts.amt_claim,0) AS amt_claim,  t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value, 
                txpph.tax_id AS pph_tax_id, txpph.tax_value AS pph_tax_value, txpph.tax_category AS pph_tax_category, f.freight_supplier,
                ts.`trx_shrink_claim`, v.vendor_code, v.vendor_name,
			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
				    WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
				    WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
				    WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
                (SELECT MAX(total_amount) FROM payment_oa WHERE transaction_id = t.transaction_id AND payment_method = 3 AND STATUS = 0) AS split_oa,
                (SELECT max(qty) FROM payment_oa WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS qty_oa,
                (SELECT max(shrink) FROM payment_oa WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_shrink,
                (SELECT max(additional_shrink) FROM payment_oa WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_shrink2

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
            LEFT JOIN transaction_additional_shrink hsw ON t.transaction_id = hsw.transaction_id
            {$wherePengajuan}
            WHERE fc.freight_id = {$freightId}
			AND sc.stockpile_id = {$stockpileId}
            {$whereContractIds}
			{$whereProperty} 
            {$whereIdPP}
			ORDER BY t.slip_no ASC";
        //    echo $sql;
           //end edit by eva
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
		$returnValue .= '<form id = "frm1">';
        $returnValue .= '<input type="hidden" id="contractIds" name="contractIds" value="' . $contractFreights . '" />';  //khusus  kode vendor_ID
		//$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th>
                                <th>Transaction Date</th>
                                <th>PO No.</th>
                                <th>Vendor Code</th>
                                <th>Vehicle No</th>
                                <th>Quantity</th>
                                <th>Freight Cost / kg</th>
                                <th>Amount</th>
                                <th>Shrink Qty Claim</th>
                                <th>Shrink Price Claim</th>
                                <th>Shrink Amount</th>
                                <th>Additional Shrink</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';
        
        $totalAllPrice = 0;
        $totalPPn = 0;
        $totalPPh = 0;
        $totalDpp = 0;
        $qty = 0;
        $price = 0;
        $totaldppShrink = 0;
        //start add by eva
        $totaldppAddShrink = 0;
        $totalAllShrink = 0;
        //end add by eva
        $DpCount = 0;
    // START WHILE
        while($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $dppShrinkPrice = 0;
            $qtyShrink = 0;
            $priceShrink = 0;
            $vendorName = '';

            $vendorName = $row->freight_supplier;
          
			if($row->freight_rule == 1){
                $fq = $row->send_weight - $row->qty_oa;
                // echo " OA  " . $row->qty_oa;
				$fp = ($row->freight_price * $fq) ;
				
			}else{
                $fq = ($row->freight_quantity - $row->qty_oa);
				$fp = ($row->freight_price * $fq);	
               
			}

            if($row->hsw_amt_claim != '' && $row->hsw_amt_claim > 0){
                $tempShrink2 = $row->hsw_amt_claim;
            }else{
                $tempShrink2 = 0;
            }

			if($row->transaction_date >= '2015-10-05'&& $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
			    $dppTotalPrice = $fp;
                $dppShrinkPrice = $row->amt_claim -$row->split_shrink;
                $hswAmtClaim = $tempShrink2 - $row->split_shrink2; //susut Luar biasa
            }else{ 
			    if($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                    $dppTotalPrice = $fp;
                    // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim)-$row->split_shrink;
                    $dppShrinkPrice = $row->amt_claim -$row->split_shrink;
                    $hswAmtClaim = $tempShrink2 - $row->split_shrink2; //susut Luar biasa
                }else{
                    if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05'  && $row->stockpile_id == 1){
                        $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                        $dppShrinkPrice = ($row->amt_claim -$row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                        $hswAmtClaim = ($tempShrink2 - $row->split_shrink2) / ((100 - $row->pph_tax_value) / 100); //susut Luar biasa
                    } else {
                        if($row->pph_tax_category == 1) {
                            $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                            // $dppShrinkPrice = (($row->qtyClaim * $row->trx_shrink_claim) - $row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                            $dppShrinkPrice = ($row->amt_claim - $row->split_shrink) / ((100 - $row->pph_tax_value) / 100);
                            $hswAmtClaim = ($tempShrink2 - $row->split_shrink2)  / ((100 - $row->pph_tax_value) / 100); //susut Luar biasa
                        }else {
                            $dppTotalPrice = $fp;
                            // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) - $row->split_shrink;
                            $dppShrinkPrice = $row->amt_claim - $row->split_shrink;
                            $hswAmtClaim = $tempShrink2 - $row->split_shrink2; //susut Luar biasa
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
            
            //start edit by eva
            //$dppPrice_dppShrink = $dppTotalPrice - $dppShrinkPrice;
            // $hswAmtClaim = $row->hsw_amt_claim;
            $dppPrice_dppShrink = $dppTotalPrice - ($dppShrinkPrice + $hswAmtClaim);
            //end edit by eva
            $priceShrink = $row->trx_shrink_claim;
            $qtyShrink = $dppShrinkPrice/$row->trx_shrink_claim;
           // echo " | A " .$qtyShrink;

            $returnValue .= '<tr>';
            
            //SELECTED NOTIM
            if($idPP == ''){
                if($checkedSlips != '') {
                    $pos = strpos($checkedSlips, $row->transaction_id);
                    $boolCheked = true; 
                    if($pos === false) {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="'. $row->transaction_id .'" onclick="checkSlipFreight('. $stockpileId .', '. $row->freight_id .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".', \'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
                    } else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="'. $row->transaction_id .'" onclick="checkSlipFreight('. $stockpileId .', '. $row->freight_id .', '. "'".$settle ."'".', '. "'". $contractFreights ."'".', \'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" checked /></td>';
                        $totalAllPrice = $totalAllPrice + $dppPrice_dppShrink;	
                        $totaldppShrink = $totaldppShrink + $dppShrinkPrice;
                        //Start add by eva
                        $totaldppAddShrink = $totaldppAddShrink + $hswAmtClaim;
                        $totalAllShrink = $totaldppShrink + $totaldppAddShrink;
                        //end add by eva
                        $totalDpp = $totalDpp + $fp;

                        if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                            $totalPPn = ($totalAllPrice * ($row->ppn_tax_value / 100));
                            if($row->ppn_tax_value > 0){
                                $ppntext = round($row->ppn_tax_value,2) .'%';
                            
                            }else{
                                $ppntext = '';
                            }
                        }

                        if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                            $totalPPh = ($totalAllPrice * ($row->pph_tax_value / 100));
                            if($row->pph_tax_value > 0){
                                $pphtext = round($row->pph_tax_value,2) .'%';
                            }else{
                                $pphtext = '';
                            }
                        }

                        $totalQty = $totalQty + $fq;
                        // $tempPPN = ($row->ppn_tax_value / 100);
                        // $tempPPH =  ($row->pph_tax_value / 100);
                    }
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="'. $row->transaction_id .'" onclick="checkSlipFreight('. $stockpileId .', '. $row->freight_id .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".', \'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
                }
            }else if($idPP != ''){
                $totalAllShrink = 0;
                $boolCheked = true; 
                $fq = $row->qty;
                $freightPrice = $row->dpp;
                $dppShrinkPrice = $row->dppShrink;
                $hswAmtClaim = $row->additional_shrink;
                $qtyShrink = $dppShrinkPrice/$priceShrink;
                $dppPrice_dppShrink = $row->totalAmount;
                $totalDpp = $row->total_dpp;
                $totaldppShrink = $row->total_shrink;
                $totaldppAddShrink = $row->total_shrink2;
                $totalAllPrice = $row->total_amount;
                $totalPPn = $row->total_ppn_amount;
                $totalPPh = $row->total_pph_amount;
                $grandTotal = $row->grand_total;
                //start add by eva
                $totalAllShrink = $totaldppShrink + $totaldppAddShrink;
                //$totalAllShrink = $row->total_shrink;
                $totalQty = $totalQty + $row->qty;
                //end add by eva

                if ($row->ppn_id != 0 && $row->ppn_id != '') {
                    if($row->ppn_id > 0){
                        $ppntext = round($row->ppn_value,2) .'%';
                       
                    }else{
                        $ppntext = '';
                    }
                }
                if ($row->pph_id != 0 && $row->pph_id != '') {
                    if($row->pph_value > 0){
                        $pphtext = round($row->pph_value,2) .'%';
                    }else{
                        $pphtext = '';
                    }
                }
                $returnValue .= '<td><input type="hidden" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFromFP . '\', \'' . $paymentToFP . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">'. $row->slip_no .'</td>';
            $returnValue .= '<td style="width: 10%;">'. $row->transaction_date .'</td>';
            $returnValue .= '<td style="width: 10%;">'. $row->po_no .'</td>';
            $returnValue .= '<td style="width: 10%;">'. $row->vendor_code .'</td>';
            $returnValue .= '<td style="width: 10%;">'. $row->vehicle_no .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($fq, 0, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($row->freight_price, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($freightPrice, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($qtyShrink, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">'. number_format($row->trx_shrink_claim, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">'. number_format($dppShrinkPrice, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($hswAmtClaim, 2, ".", ",") . '</td>';
            
            // End Add by Eva
            $returnValue .= '<td style="text-align: right; width: 20%;">'. number_format($dppPrice_dppShrink, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
        }
        $returnValue .= '</tbody>';
    // END WHILE
			
        //SELECTED DP 1 => jika yg dichecklist slip-no nya
        if($boolCheked) {
			//$totalPrice1 = 0;
            // Start Add by Eva
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Total Quantity </td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalQty, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            // End Add by Eva
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Total Dpp</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalDpp, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Total Shrink</td>';
            // Start edit by Eva
            //$returnValue .= '<td style="text-align: right;">'. number_format($totaldppShrink, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalAllShrink, 2, ".", ",") .'</td>';
            // End edit by Eva
            $returnValue .= '</tr>';

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalAllPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
			
            if($idPP == ''){
                $ppnDBAmount = $totalPPn;
                $pphDBAmount = $totalPPh;
                
                if($ppn == 'NONE') {
                    $totalPPn = $ppnDBAmount;
                } elseif($ppn != $ppnDBAmount) {
                    $totalPPn = $ppn;
                } else {
                    $totalPPn = $ppnDBAmount;
                }

                if($pph == 'NONE') {
                    $totalPPh = $pphDBAmount;
                } elseif($pph != $pphDBAmount) {
                    $totalPPh = $pph;
                } else {
                    $totalPPh = $pphDBAmount;
                }
            }
         
			$returnValue .= '<tr>';
			$returnValue .= '<td colspan="13" style="text-align: right;">PPn '.$ppntext.'</td>';
			$returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="totalPPn" readonly  id="totalPPn" value="'. number_format($totalPPn, 2, ".", ",") .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', '. "'".$settle ."'".', '. "'". $contractFreights ."'".', this, document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
			$returnValue .= '</tr>';

			$returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">PPh '.$pphtext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="totalPPh" id="totalPPh" value="'. number_format($totalPPh, 2, ".", ",") .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".', document.getElementById(\'ppn\'), this, \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
			$returnValue .= '</tr>';
           
            
        //--------------------------------------------------------------------Down Payment------------------------------------------------------------------------------------------------------------------------------------------------            
            if($idPP == ''){ //INPUT DATA
                $sql = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`,p.ppn_amount ,p.pph_amount, vh.pph, c.`po_no`, c.`contract_no`, vh.ppn,
                        (SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code, 
                        (SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
                        (SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,
                        CASE WHEN p.payment_location = 0 THEN 'HOF'
                                ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) 
                            END AS payment_location
                        FROM payment p LEFT JOIN freight vh ON p.`freight_id` = vh.`freight_id`
                        LEFT JOIN contract c ON c.`contract_id` = p.`freightContract` 
                        WHERE p.freight_id = {$freightId} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                $DpCount = $result->num_rows;

                $totalDownPayment = 0;
                $totalDppDpFC = 0;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_object()) {
                        $downPayment = 0;
                        $DppDpFC = 0;
                        $DppDpFC = $row->amount_converted;
                        $dpPPn = $row->amount_converted * ($row->ppn/100);
                        $dpPPh = $row->amount_converted * ($row->pph/100);
                        $downPayment = ($DppDpFC + $dpPPn) - $dpPPh ;

                        #PAYMENT VOUCHER
                        $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
                            
                        if($row->bank_type == 1) {
                            $voucherCode .= ' - B';
                        } elseif($row->bank_type == 2) {
                            $voucherCode .= ' - P';
                        } elseif($row->bank_type == 3) {
                            $voucherCode .= ' - CAS';
                        }
                        
                        if($row->bank_type != 3) {
                            if($row->payment_type == 1) {
                                $voucherCode .= 'RV';
                            } else {
                                $voucherCode .= 'PV';
                            }
                        }
                        //   echo $voucherCode .' # '. $rowData->payment_no; 
                        //END
                    
                        $returnValue .= '<tr>';
                        $returnValue .= '<td colspan="8" style="text-align: right;">Down Payment</td>';
                        $returnValue .= '<td  style="text-align: right;">'.$voucherCode .' # '. $row->payment_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. number_format($DppDpFC, 2, ".", ",").'</td>';

                        //SELECTED DP 2
                        if($checkedSlipsDP != '') { //jika yg di checklist bagian nilai DP nya
                            $posDP = strpos($checkedSlipsDP, $row->payment_id);
                            if($posDP === false) {
                                $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipFreightDP('. $stockpileId .', '. $freightId .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
                            } else {
                                $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipFreightDP('. $stockpileId .', '. $freightId .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" checked /></td>';
                                $totalDownPayment = $totalDownPayment + $downPayment; 
                                $totalDppDpFC = $totalDppDpFC + $DppDpFC;  
                                if($totalDownPayment > $totalAllPrice){
                                    $disabled = 'disabled';
                                } else{
                                    $disabled = '';
                                }
                               
                            }
                        } else {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipFreightDP('. $stockpileId .', '. $freightId .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
                        }
                        $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                        $returnValue .= '</tr>';
                    }
                }
                // $grandTotal = ($totalAllPrice + $totalPPn) - $totalPPh - $totalDownPayment;
                if($settle == 0 ){
                    $grandTotal = ($totalAllPrice + $totalPPn) - $totalPPh - $totalDownPayment;
                    $DppGrandTotal = $totalAllPrice - $totalDppDpFC;
                }else if($settle == 1){
                    $grandTotal = 0;
                }


                if($grandTotal < 0 && $totalDownPayment > 0 || ($grandTotal > 0 && $totalDownPayment > 0 && $settle == 2)) {
                    $grandTotal = 0;
                }
            
                if($DpCount > 0){ //settle != 2 
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="11" style="text-align: right; color:red;">Settlement Only ?</td>';
                    if($settle == 1) {
                        $returnValue .= '<td style="text-align: center;"><input type="checkbox"  name="checkedSettle" id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle('. $stockpileId .', '. $freightId .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" checked /></td>';                    
                    } else {
                        $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" '. $disabled.'  id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle('. $stockpileId .', '. $freightId .', '. "'".$settle ."'" .', '. "'". $contractFreights ."'".',\'NONE\', \'NONE\', \''. $paymentFrom .'\', \''. $paymentTo .'\', '. $idPP .');" /></td>';
                    }
                    $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>'; //for settlement 50%
                }else{
                    $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>'; //for settlemetn 100%
                }
            }else{ // VIEW DATA
                $sql = "SELECT ppd.*, CASE WHEN pp.settlement_status = 0 THEN 'No' ELSE 'Yes' END AS settle FROM pengajuan_payment_dp ppd 
                        LEFT JOIN pengajuan_payment pp ON pp.idPP = ppd.idPP WHERE pp.idPP = {$idPP}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
               // echo $sql;
                $DpCount = $result->num_rows;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_object()) {
                        $tempSettle = $row->settle;
                        $returnValue .= '<tr>';
                        $returnValue .= '<td colspan="9" style="text-align: right;">Down Payment</td>';
                        $returnValue .= '<td  style="text-align: right;">'.$row->voucher_code.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. number_format(($row->dpp_dp_settle), 2, ".", ",").'</td>';
                        $returnValue .= '<td style="text-align: right;">'. number_format(($row->settle_amount), 2, ".", ",") .'</td>';
                        $returnValue .= '</tr>';
                     //   $returnValue .= '</tr>';
                    }
                }
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="11" style="text-align: right; color:red;">Settlement Only ?</td>';
                $returnValue .= '<td colspan="1" style="text-align: right;">'. $tempSettle.'</td>';
                $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>';
            }

            $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
                
            $returnValue .= '</tfoot>';
        }

        $returnValue .= '</table>';
	    $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalDpp, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totaldppShrink" name="totaldppShrink" value="'. round($totaldppShrink, 2) .'" />';
        $returnValue .= '<input type="hidden" id="totalAllPrice" name="totalAllPrice" value="'. round($totalAllPrice, 2) .'" />'; // Dpp - Shrink
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. $grandTotal .'" />'; //nilai ((dpp - shrink) + totalPPn - totalPPh - totalDownPayment)
        $returnValue .= '<input type="hidden" id="DppGrandTotal" name="DppGrandTotal" value="' . $DppGrandTotal . '" />'; //nilai ((dpp-shrink) - totalDppDpFc) 
		$returnValue .= '<input type="hidden" id="totalDownPayment" name="totalDownPayment" value="'. round($totalDownPayment, 2) .'" />'; //nilai Downpayment include ppn,pph
		$returnValue .= '<input type="hidden" id="totalDppDpFC" name="totalDppDpFC" value="'. $totalDppDpFC .'" />'; //total Dpp Downpayment
		$returnValue .= '<input type="hidden" id="settle" name="settle" value="'. $settle .'" />'; 
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />'; 
        $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />';
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
        //start add by eva
        $returnValue .= '<input type="hidden" id="totaldppShrink2" name="totaldppShrink2" value="' . round($totaldppAddShrink, 2) . '" />';
        //end add by eva

        $returnValue .= '</div>';
    }
    
    echo $returnValue;
    echo $return_val;
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

//HANDLING DP
function getContractHandling($stockpileId, $vendorHandlingId) {
    global $myDatabase;
    $returnValue = '';

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
echo $sql;
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

function getHandlingTax($vendorHandlingDp)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT round(ppn,2) as hppn, round(pph,2) as hpph, ppn_tax_id, pph_tax_id FROM vendor_handling WHERE vendor_handling_id = {$vendorHandlingDp}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn = $row->hppn;
        $pph = $row->hpph;
        $ppnID = $row->ppn_tax_id;
        $pphID = $row->pph_tax_id;

        // $ppnAmount = round(($ppn / 100) * $amount,2);
        // $pphAmount = round(($pph / 100) * $amount,2);

        $returnValue = '|' . number_format($ppn, 2, ".", ",") . '|' . number_format($pph, 2, ".", ",") . '|' . $ppnID . '|' . $pphID;

    } else {
        $returnValue = '|0|0|0|0';
    }

    echo $returnValue;
}




function setSlipHandling_settle($stockpileId, $vendorHandlingId, $settle, $contractHandling, $checkedSlips, $checkedSlipsDP, $ppn, $pph, $paymentFromHP, $paymentToHP, $idPP) {
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $whereContractIds = '';
    $boolCheked = false;
    $selectPaymentH = '';
    $whereIdPP = '';
    $wherePengajuan = '';
    $whereProperty = '';
	
  //  echo "SS " . $settle;
	if($checkedSlips == ''){
        for ($i = 0; $i < sizeof($contractHandling); $i++) {
            if($contractIds == '') {
                $contractIds .=  $contractHandling[$i];
            } else {
                $contractIds .= ','. $contractHandling[$i];
            }
        }
	}else{
		$contractIds = $contractHandling;
	}

    if ($contractIds != 0 && $idPP == '') {
        $whereContractIds = "	AND sc.contract_id IN ({$contractIds})";
    }

        //jika Contract yg dipilih sebelumnya ALL
    if($contractHandling == '' && $idPP != ''){
        $pksAll = "SELECT contract_id FROM stockpile_contract sc 
                            INNER JOIN transaction t ON t.stockpile_contract_id = sc.stockpile_contract_id 
                            LEFT JOIN payment_handling ph ON ph.transaction_id = t.transaction_id
                    WHERE ph.idPP = {$idPP} GROUP BY contract_id";
        $resultpksall = $myDatabase->query($pksAll, MYSQLI_STORE_RESULT);
        if ($resultpksall !== false && $resultpksall->num_rows > 0) {
            while( $rowcon = $resultpksall->fetch_object()){
                $sqlpks = "SELECT * FROM pengajuan_pks_contract WHERE idPP = {$idPP} AND contract_id = {$rowcon->contract_id}";
                $resultpks = $myDatabase->query($sqlpks, MYSQLI_STORE_RESULT);
              //  echo $resultpks->num_rows;

                if ($resultpks->num_rows == 0) {
                    $sql1 = "INSERT INTO pengajuan_pks_contract(idPP, contract_id) values ({$idPP}, {$rowcon->contract_id})";
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                }
            }
        }
    }

    if($idPP != ''){ 
        $selectPaymentH = "ph.qty, ph.dpp, ph.total_amount as totalAmount, ph.ppn_id, ph.ppn_value, ph.pph_id, ph.pph_value, pp.total_dpp,  
                            pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND ph.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_handling ph ON ph.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = ph.idpp ";
    }else if($idPP == ''){
        $whereProperty = "  AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                            AND t.company_id = {$_SESSION['companyId']}
                             -- AND  t.hc_payment_id IS NULL
                            AND t.hc_payment_status <> 1 AND vhc.price != 0 AND t.handling_quantity != 0
                            AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                            AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromHP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToHP', '%d/%m/%Y')";
        // $wherePengajuan = " LEFT JOIN payment_ pao ON pao.transaction_id = t.transaction_id AND pao.status = 0";
    }

    $sql = "SELECT {$selectPaymentH} t.*, vh.vendor_handling_name, sc.stockpile_id, vhc.vendor_handling_id, con.po_no, vh.vendor_handling_rule,
                vh.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                -- CASE WHEN t.fc_payment_status = 2 THEN (SELECT max(amount) FROM payment_handling WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) ELSE 0 END AS split_handling,
                -- CASE WHEN t.fc_payment_status = 2 THEN (SELECT max(qty) FROM payment_handling WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) ELSE 0 END AS qty_hc
                (SELECT max(total_amount) FROM payment_handling WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_handling,
                (SELECT max(qty) FROM payment_handling WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS qty_hc
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
                {$wherePengajuan}
            WHERE vhc.vendor_handling_id = {$vendorHandlingId}
			AND sc.stockpile_id = {$stockpileId}
		{$whereContractIds}
        {$whereProperty}
        {$whereIdPP}
			ORDER BY t.slip_no ASC";
      //  echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
		$returnValue .= '<form id = "frm10">';
        $returnValue .= '<input type="hidden" id="contractIds" name="contractIds" value="' . $contractIds . '" />';  //khusus  kode vendor_ID
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th><INPUT type="checkbox" onchange="checkAllHandling(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th>
                                <th>Transaction Date</th>
                                <th>PO No.</th>
                                <th>Vendor Code</th>
                                <th>Quantity</th>
                                <th>Handling Cost / kg</th>
                                <th>Total</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';
        
        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $totalQty = 0;
        $price = 0;
        // START WHILE
        while($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $vendorName = '';
            $hq = $row->handling_quantity - $row->qty_hc;
			$hp = ($row->handling_price * $hq );
            $vendorName = $row->vendor_handling_name;
			
			
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

    $dppPrice_dppShrink = $dppTotalPrice;

    $returnValue .= '<tr>';
    // SELECTED NOTIM
        if($idPP == ''){
            if($checkedSlips != '') {
                $boolCheked = true;
                $pos = strpos($checkedSlips, $row->transaction_id);
                if($pos === false) {
                    $returnValue .= '<td style="width: 1%;"><input   type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipHandling_1('. $stockpileId .', '. $row->vendor_handling_id .', '. "'".$settle ."'" .', '.  "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" /></td>';
                } else {
                    $returnValue .= '<td style="width: 1%;"><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipHandling_1('. $stockpileId .', '. $row->vendor_handling_id .', '. "'".$settle ."'" .', '. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" checked /></td>';
                    $totalPrice = $totalPrice + $dppTotalPrice;
                    // $totalPrice2 = $totalPrice2 + $dppTotalPrice;
                            
                    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                        if($row->ppn_tax_value > 0){
                            $ppntext = round($row->ppn_tax_value,2) .'%';
                        }else{
                            $ppntext = '';
                        }
                    }

                    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                        if($row->pph_tax_value > 0){
                            $pphtext = round($row->pph_tax_value,2) .'%';
                        }else{
                            $pphtext = '';
                        }
                    }
                        $totalQty = ($totalQty + $hq);
                        // by YENI
                        // $dpp = $dpp + (($row->handling_price * $row->handling_quantity)- $row->split_oa);
                        // $qty = $qty + $row->quantity;
                        // $price = $row->handling_price;    

                        // $tempPPN = ($row->ppn_tax_value / 100);
                        // $tempPPH =  ($row->pph_tax_value / 100);
                    }
            } else {
                $boolCheked = false;
                    $returnValue .= '<td style="width: 1%;" ><input type="checkbox" name="checkedSlips[]" id="hc" value="'. $row->transaction_id .'" onclick="checkSlipHandling_1('. $stockpileId .', '. $row->vendor_handling_id .', '. "'".$settle ."'" .', '. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" /></td>';
            }
        }else{
            $boolCheked = true;
            $hq = $row->qty;
            $dppPrice_dppShrink = $row->totalAmount;
            $dppTotalPrice = $row->total_dpp;
            $totalPrice = $row->total_amount;
            $totalPpn = $row->total_ppn_amount;
            $totalPph = $row->total_pph_amount;
            $grandTotal = $row->grand_total;

            if ($row->ppn_id != 0 && $row->ppn_id != '') {
                if($row->ppn_id > 0){
                    $ppntext = round($row->ppn_value,2) .'%';
                   
                }else{
                    $ppntext = '';
                }
            }
            if ($row->pph_id != 0 && $row->pph_id != '') {
                if($row->pph_value > 0){
                    $pphtext = round($row->pph_value,2) .'%';
                }else{
                    $pphtext = '';
                }
            }
            $returnValue .= '<td style="width: 1%;" ></td>';
           
        }
        $returnValue .= '<td style="width: 5%;">'. $row->slip_no .'</td>';
		$returnValue .= '<td style="width: 5%;">'. $row->transaction_date .'</td>';
		$returnValue .= '<td style="width: 5%;">'. $row->po_no .'</td>';
		$returnValue .= '<td style="width: 5%;">'. $row->vendor_code .'</td>';
        $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($hq, 0, ".", ",") .'</td>';
        $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($row->handling_price, 2, ".", ",") .'</td>';
        $returnValue .= '<td style="text-align: right; width: 3%;">'. number_format($dppPrice_dppShrink, 2, ".", ",") .'</td>';
        $returnValue .= '</tr>';
    }
        $returnValue .= '</tbody>';
    // END WHILE
    
        // SELECTED DP1
        if($boolCheked) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Total DPP</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
               
            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;
            
            if($idPP == ''){
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
            }
            
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPN '.$ppntext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPn" id="totalPPn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ',' . "'" . $contractIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\', '. $idPP .');" /></td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPh '.$pphtext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPh" id="totalPPh" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ',' .  "'". $contractIds."'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\', '. $idPP .');" /></td>';
            $returnValue .= '</tr>';

//---------------------------------------Down Payment-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------          
        if($idPP == ''){
			$sqlAA = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`, p.ppn_amount ,p.pph_amount, vh.pph as vhpph, vh.ppn as vhppn, c.`po_no`, c.`contract_no`,vh.ppn,
                    (SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code, 
                    (SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
                    (SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,
                    CASE WHEN p.payment_location = 0 THEN 'HOF'
                            ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) 
                        END AS payment_location
                    FROM payment p LEFT JOIN vendor_handling vh ON p.`vendor_handling_id` = vh.`vendor_handling_id`
                    LEFT JOIN contract c ON c.`contract_id` = p.`handlingContract` 
                    WHERE p.vendor_handling_id = {$vendorHandlingId} AND p.payment_method = 2 AND p.payment_status = 0 AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
            $resultAA = $myDatabase->query($sqlAA, MYSQLI_STORE_RESULT);
            $DpCount = $result->num_rows;

			if ($resultAA->num_rows > 0) {
                $totalDownPayment = 0;
                $totalDppDpHC = 0;
                while($row = $resultAA->fetch_object()) {
                    $dppDpHC = 0;
                    $downPayment = 0;
                    // $dpHC = $row->amount_converted;
                    $dppDpHC = $row->amount_converted;
                    $dpPPn = $dppDpHC * ($row->vhppn/100);
                    $dpPPh = $dppDpHC * ($row->vhpph/100);
                    // $dp = ($dppDpHC + $dpPPn) - $dpPPh ;
                    $downPayment = ($dppDpHC + $dpPPn) - $dpPPh ;

                    #PAYMENT VOUCHER
                    $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
                            
                    if($row->bank_type == 1) {
                        $voucherCode .= ' - B';
                    } elseif($row->bank_type == 2) {
                        $voucherCode .= ' - P';
                    } elseif($row->bank_type == 3) {
                        $voucherCode .= ' - CAS';
                    }
                    
                    if($row->bank_type != 3) {
                    if($row->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                        }
                    }
                    
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="2" style="text-align: right;">Down Payment</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $voucherCode .' # '.$row->payment_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. number_format($dppDpHC, 2, ".", ",").'</td>';
                    
                    //SELECTED DP 2
                    if($checkedSlipsDP != '') {
                        $posDP = strpos($checkedSlipsDP, $row->payment_id);
                        if($posDP === false) {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipHandlingDP('. $stockpileId .', '. $vendorHandlingId .',  '. "'".$settle ."'" .','. "'". $contractIds."'" .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" /></td>';
                        } else {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipHandlingDP('. $stockpileId .', '. $vendorHandlingId .', '. "'".$settle ."'" .', '. "'". $contractIds."'".',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" checked /></td>';
                            $totalDownPayment = $totalDownPayment + $downPayment; 
                            $totalDppDpHC = $totalDppDpHC + $dppDpHC; 
                            if($totalDownPayment > $totalPrice){
                                $disabled = 'disabled';
                            } else{
                                $disabled = '';
                            }
                        }
                    } else {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="hc2" value="'. $row->payment_id .'" onclick="checkSlipHandlingDP('. $stockpileId .', '. $vendorHandlingId .',  '. "'".$settle ."'" .','. "'". $contractIds."'".',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" /></td>';
                    }
                    $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                    $returnValue .= '</tr>';
                }
            }
            
            // by Yeni
            
            if($settle == 0 ){
                $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $totalDownPayment;
                $DppGrandTotal = $totalPrice - $totalDppDpHC;
			}else if($settle == 1){
                $grandTotal = 0;
			}
            
            // by Yeni
            if($grandTotal < 0 && $totalDownPayment > 0 || ($grandTotal > 0 && $totalDownPayment > 0 && $settle == 2)) {
                $grandTotal = 0;
				// $downPaymentHC = $totalPrice2;
            }

            if($DpCount > 0){ //settle != 2 => 
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="5" style="text-align: right;">Settlement Only ?</td>';
                if($settle == 1) {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle_handling('. $stockpileId .', '. $vendorHandlingId .', '. "'".$settle ."'" .', '. $contractIds .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');" checked/></td>';
                } else {
                    $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" '. $disabled.' id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle_handling('. $stockpileId .', '. $vendorHandlingId .', '. "'".$settle ."'" .', '. $contractIds .',\'NONE\', \'NONE\', \''. $paymentFromHP .'\', \''. $paymentToHP .'\', '. $idPP .');"  /></td>';                    
                }
                $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>'; //for settlement 50%
            }else{
                $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>'; //for settlemetn 100%
            }
        }else{ // VIEW DATA
            $sql = "SELECT ppd.*, CASE WHEN pp.settlement_status = 0 THEN 'No' ELSE 'Yes' END AS settle FROM pengajuan_payment_dp ppd 
                    LEFT JOIN pengajuan_payment pp ON pp.idPP = ppd.idPP WHERE pp.idPP = {$idPP}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
           // echo $sql;
            $DpCount = $result->num_rows;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_object()) {
                    $tempSettle = $row->settle;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="3" style="text-align: right;">Down Payment</td>';
                    $returnValue .= '<td  style="text-align: right;">'.$row->voucher_code.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. number_format(($row->dpp_dp_settle), 2, ".", ",").'</td>';
                    $returnValue .= '<td style="text-align: right;">'. number_format(($row->settle_amount), 2, ".", ",") .'</td>';
                    $returnValue .= '</tr>';
                 //   $returnValue .= '</tr>';
                }
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="5" style="text-align: right; color:red;">Settlement Only ?</td>';
            $returnValue .= '<td colspan="1" style="text-align: right;">'. $tempSettle.'</td>';
            $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>';
        }

            // $returnValue .= '<tr>';
            // $returnValue .= '<td colspan="9" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';
            
            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
	    $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="'. round($totalPrice, 2) .'" />';
		$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '<input type="hidden" id="DppGrandTotal" name="DppGrandTotal" value="'. $DppGrandTotal .'" />';
		$returnValue .= '<input type="hidden" id="totalDownPayment" name="totalDownPayment" value="'. $totalDownPayment .'" />';
        $returnValue .= '<input type="hidden" id="totalDppDpHC" name="totalDppDpHC" value="'. round($totalDppDpHC, 2) .'" />';
        $returnValue .= '<input type="hidden" id="settle" name="settle" value="'. $settle .'" />';
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />'; 
        $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />'; 
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  

        $returnValue .= '</div>';
    }
    echo $returnValue;
    echo $return_val;
}

function setSlipHandling($stockpileId, $vendorHandlingId, $contractHandling, $checkedSlips, $ppn, $pph, $paymentFromHP, $paymentToHP, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $whereProperty = '';
    $boolCheked = false;
    $selectPaymentH = '';
    $whereIdPP = '';
    $whereProperty = '';
    $return_val = '';
    $wherePengajuan = '';

    if ($checkedSlips == '') {
        for ($i = 0; $i < sizeof($contractHandling); $i++) {
            if ($contractIds == '') {
                $contractIds .= $contractHandling[$i];
            } else {
                $contractIds .= ',' . $contractHandling[$i];
            }
        }
    } else {
        $contractIds = $contractHandling;
    }
    // echo " A " .$contractIds;

    if ($contractIds != 0 && $idPP == '') {
            $whereContractIds = "AND sc.contract_id IN ({$contractIds})";
    }

    //jika Contract yg dipilih sebelumnya ALL
    if($contractHandling == '' && $idPP != ''){
        $pksAll = "SELECT contract_id FROM stockpile_contract sc 
                            INNER JOIN transaction t ON t.stockpile_contract_id = sc.stockpile_contract_id 
                            LEFT JOIN payment_handling ph ON ph.transaction_id = t.transaction_id
                    WHERE ph.idPP = {$idPP} GROUP BY contract_id";
        $resultpksall = $myDatabase->query($pksAll, MYSQLI_STORE_RESULT);
        if ($resultpksall !== false && $resultpksall->num_rows > 0) {
            while( $rowcon = $resultpksall->fetch_object()){
                $sqlpks = "SELECT * FROM pengajuan_pks_contract WHERE idPP = {$idPP} AND contract_id = {$rowcon->contract_id}";
                $resultpks = $myDatabase->query($sqlpks, MYSQLI_STORE_RESULT);
              //  echo $resultpks->num_rows;

                if ($resultpks->num_rows == 0) {
                    $sql1 = "INSERT INTO pengajuan_pks_contract(idPP, contract_id) values ({$idPP}, {$rowcon->contract_id})";
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                }
            }
        }
    }

    if($idPP != ''){ 
        $selectPaymentH = "ph.qty, ph.dpp, ph.total_amount as totalAmount, ph.ppn_id, ph.ppn_value, ph.pph_id, ph.pph_value, pp.total_dpp,  
                            pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND ph.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_handling ph ON ph.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = ph.idpp ";
    }else if($idPP == ''){
        $whereProperty = " AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                            AND t.company_id = {$_SESSION['companyId']} 
                            AND vhc.price != 0 AND t.handling_quantity != 0 AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                            AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromHP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToHP', '%d/%m/%Y')
                            -- AND  t.hc_payment_id IS NULL 
                            AND t.hc_payment_status <> 1";
        // $wherePengajuan = " LEFT JOIN payment_ pao ON pao.transaction_id = t.transaction_id AND pao.status = 0";
    }

    $sql = "SELECT {$selectPaymentH} t.*, vh.vendor_handling_name, sc.stockpile_id, vhc.vendor_handling_id, con.po_no, vh.vendor_handling_rule,
                    vh.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                    txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                    (SELECT max(total_amount) FROM payment_handling WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_handling,
                    (SELECT max(qty) FROM payment_handling WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS qty_hc
           
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
            {$wherePengajuan}
            WHERE vhc.vendor_handling_id = {$vendorHandlingId}
			AND sc.stockpile_id = {$stockpileId}
            {$whereContractIds}
            {$whereProperty}
            {$whereIdPP}
			ORDER BY t.slip_no ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// echo $sql;
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm10">';
        $returnValue .= '<input type="hidden" id="contractIds" name="contractIds" value="' . $contractIds . '" />';  //khusus  kode vendor_ID
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr>
                                <th><input type="checkbox"  stylle="width:5%" onchange="checkAll(this)" name="checkedSlips[]" />
                                    </th><th>Slip No.</th>
                                    <th>Transaction Date</th>
                                    <th>PO No.</th>
                                    <th>Vendor Code</th>
                                    <th>Quantity</th>
                                    <th>Handling Cost / kg</th>
                                    <th>Total</th>
                                </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $totalQty = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $vendorName = '';

            $vendorName = $row->vendor_handling_name;
            $hq = $row->handling_quantity - $row->qty_hc;
			$hp = ($row->handling_price * $hq );

            // $hp = ($row->handling_price * $row->handling_quantity)-$row->split_handling;
            // $hq = $row->handling_quantity;

            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
                $dppTotalPrice = $hp;
            } else {
                if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                    $dppTotalPrice = $hp;
                } else {
                    if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                        $dppTotalPrice = ($hp) / ((100 - $row->pph_tax_value) / 100);
                    } else {
                        if ($row->pph_tax_category == 1) {
                            $dppTotalPrice = ($hp) / ((100 - $row->pph_tax_value) / 100);
                        } else {
                            $dppTotalPrice = $hp;
                        }
                    }

                }
            }

            $returnValue .= '<tr>';
            if($idPP == ''){
                if ($checkedSlips != '') {
                    $boolCheked = true;
                    $pos = strpos($checkedSlips, $row->transaction_id);
                        if ($pos === false) {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \''  . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
                        } else {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \''  . $paymentFromHP . '\', \'' . $paymentToHP . '\');" checked /></td>';
                            $totalPrice = $totalPrice + $dppTotalPrice;

                            if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                                $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                                if($row->ppn_tax_value > 0){
                                    $ppntext = round($row->ppn_tax_value,2) .'%';
                                }else{
                                    $ppntext = '';
                                }
                            }

                            if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                                $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                                if($row->pph_tax_value > 0){
                                    $pphtext = round($row->pph_tax_value,2) .'%';
                                }else{
                                    $pphtext = '';
                                }
                            }

                            $totalQty = ($totalQty + $hq);
                        }
                    } else {
                        $boolCheked = false;
                        $returnValue .= '<td style="width:2%;"><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \''  . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
                    }
            }else{
                $boolCheked = true;
                $hq = $row->qty;
                $dppPrice_dppShrink = $row->totalAmount;
                $dppTotalPrice = $row->dpp;
                $totalPrice = $row->total_amount;
                $totalPpn = $row->total_ppn_amount;
                $totalPph = $row->total_pph_amount;
                $grandTotal = $row->grand_total;

                if ($row->ppn_id != 0 && $row->ppn_id != '') {
                    if($row->ppn_id > 0){
                        $ppntext = round($row->ppn_value,2) .'%';
                       
                    }else{
                        $ppntext = '';
                    }
                }
                if ($row->pph_id != 0 && $row->pph_id != '') {
                    if($row->pph_value > 0){
                        $pphtext = round($row->pph_value,2) .'%';
                    }else{
                        $pphtext = '';
                    }
                }
               $returnValue .= '<td><input type="hidden" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ',' . "'" . $contractIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" checked /></td>';
               
            }

            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($hq, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->handling_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if($boolCheked) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Total DPP</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            if($idPP == ''){
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
            }

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPN '.$ppntext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPn" id="totalPPn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ',' . "'" . $contractIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPh '.$pphtext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPh" id="totalPPh" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ',' . "'" . $contractIds . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            $returnValue .= '</tr>';

            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph;

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />';
        $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />';

        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
        $returnValue .= '</div>';
    }

    echo $returnValue;
    echo $return_val;
}

//UNLOADING
function getLaborPayment($stockpileId)
{
    global $myDatabase;
    $returnValue = '';


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
//UNLOADING Dp
function getLaborTax($laborDp)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT round(ppn,2) as lppn, round(pph,2) as lpph, ppn_tax_id, pph_tax_id FROM labor WHERE labor_id = {$laborDp}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   // echo $sql;

    if ($result->num_rows == 1) {
        $row = $result->fetch_object();
        $ppn = $row->lppn;
        $pph = $row->lpph;
        $ppnID = $row->ppn_tax_id;
        $pphID = $row->pph_tax_id;

        // $ppnAmount = round(($ppn / 100) * $amount,2);
        // $pphAmount = round(($pph / 100) * $amount,2);

        $returnValue = '|' . number_format($ppn, 2, ".", ",") . '|' . number_format($pph, 2, ".", ",") . '|' . $ppnID . '|' . $pphID;

    } else {
        $returnValue = '|0|0|0|0';
    }
    // $returnValue = $ppn;
    echo $returnValue;
}
// UNLOADING DP

//UNLOADING SETTLE
function setSlipUnloading_settle($stockpileId, $laborId, $settle, $checkedSlips, $checkedSlipsDP, $ppn, $pph, $paymentFromUP, $paymentToUP, $idPP) {
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $boolcontinue = false;
    $whereProperty = '';
    $boolCheked = false;
    $selectPaymentU = '';
    $whereIdPP = '';
    $wherePengajuan = '';


    if($idPP != ''){ 
        $selectPaymentU = "po.qty, po.dpp, po.total_amount as totalAmount, po.ppn_id, po.ppn_value, po.pph_id, po.pph_value, pp.total_dpp,  
                            pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND po.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_ob po ON po.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = po.idpp ";
    }else if($idPP == ''){
        $whereProperty = "AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                            AND t.company_id = {$_SESSION['companyId']}
                            -- AND t.uc_payment_id IS NULL 
                            AND t.unloading_cost_id IS NOT NULL AND (t.adj_ob IS NULL or t.adj_ob = 0) AND t.uc_payment_status <> 1
                            AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                            AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromUP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToUP', '%d/%m/%Y')";
        // $wherePengajuan = " LEFT JOIN payment_ pao ON pao.transaction_id = t.transaction_id AND pao.status = 0";
    }

    $sql = "SELECT {$selectPaymentU} t.*, l.labor_name,
            l.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
            txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
            (SELECT total_amount FROM payment_ob WHERE transaction_id = t.transaction_id AND payment_method = 3 AND STATUS = 0
                ORDER BY payment_ob_id DESC LIMIT 1) AS split_ob

        FROM `transaction` t
        INNER JOIN labor l
            ON l.labor_id = t.labor_id
        LEFT JOIN tax txppn
            ON txppn.tax_id = l.ppn_tax_id
        LEFT JOIN tax txpph
            ON txpph.tax_id = t.uc_tax_id
        {$wherePengajuan}
        WHERE t.labor_id = {$laborId}
        {$whereProperty}
        {$whereIdPP}
        ORDER BY t.slip_no ASC";
       
// echo $sql;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th style="width: 1%;"><center><INPUT type="checkbox" onchange="checkAllUC(this)" name="checkedSlips[]" /></center></th>
                                <th>Slip No.</th>
                                <th>Transaaction Date</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unloading Cost</th>
                                <th>Total</th>
                                
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $totalQty = 0;

        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $unloadingPrice = 0;
            $vendorName = '';

            $vendorName = $row->labor_name;
            if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $row->unloading_price - $row->split_ob;
                $unloadingPrice = $dppTotalPrice;
               // echo " A " . $row->split_ob;
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($row->unloading_price - $row->split_ob) / ((100 - $row->pph_tax_value) / 100);
                    $unloadingPrice = $dppTotalPrice;
                } else {
                    $dppTotalPrice = $row->unloading_price - $row->split_ob;
                    $unloadingPrice = $dppTotalPrice;
                    $dpp = $dppTotalPrice;
                }
            }

            $returnValue .= '<tr>';
            if($idPP == ''){
                if ($checkedSlips != '') {
                    $pos = strpos($checkedSlips, $row->transaction_id);
                    $boolCheked = true;
                    if ($pos === false) {
                        $returnValue .= '<td style="width: 1%;"><center><input type="checkbox" name="checkedSlips[]" id="uc" value="'. $row->transaction_id .'" onclick="checkSlipOB('. $stockpileId .', '. $row->labor_id .', '. "'".$settle ."'" . ',\'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\', '. $idPP .');" /></center></td>';
                    } else {
                        $returnValue .= '<td style="width: 1%;"><center><input type="checkbox" name="checkedSlips[]" id="uc" value="'. $row->transaction_id .'" onclick="checkSlipOB('. $stockpileId .', '. $row->labor_id .', '. "'".$settle ."'". ', \'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\', '. $idPP .');" checked /></center></td>';
                        $totalPrice = $totalPrice + $dppTotalPrice;
                        // $totalPrice2 = $totalPrice2 + $dppTotalPrice;

                        if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                            $totalPPn = ($totalPrice * ($row->ppn_tax_value / 100));
                            if($row->ppn_tax_value > 0){
                                $ppntext = round($row->ppn_tax_value,2) .'%';
                            }else{
                                $ppntext = '';
                            }
                        }

                        if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                            $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                            if($row->pph_tax_value > 0){
                                $pphtext = round($row->pph_tax_value,2) .'%';
                            }else{
                                $pphtext = '';
                            }
                        }

                        $totalQty = ($totalQty + $row->quantity);
                    }
                } else {
                    $returnValue .= '<td style="width: 1%;"><center><input type="checkbox" name="checkedSlips[]" id="uc" value="'. $row->transaction_id .'" onclick="checkSlipOB('. $stockpileId .', '. $row->labor_id .', '. "'".$settle ."'" . ', \'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\', '. $idPP .');" /></center></td>';
                }
            }else{
                $boolCheked = true;
                $dppTotalPrice = $row->totalAmount;
                $unloadingPrice = $dppTotalPrice;
                $totalPrice = $row->total_amount;
                $totalPPn = $row->total_ppn_amount;
                $totalPPh = $row->total_pph_amount;
                $grandTotal = $row->grand_total;

                if ($row->ppn_id != 0 && $row->ppn_id != '') {
                    if($row->ppn_id > 0){
                        $ppntext = round($row->ppn_value,2) .'%';
                       
                    }else{
                        $ppntext = '';
                    }
                }
                if ($row->pph_id != 0 && $row->pph_id != '') {
                    if($row->pph_value > 0){
                        $pphtext = round($row->pph_value,2) .'%';
                    }else{
                        $pphtext = '';
                    }
                }

                $returnValue .= '<td><input style="width: 1%;" type="hidden" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 1%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 1%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="text-align: right; width: 1%;">' . number_format($row->quantity, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 1%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 1%;">' . number_format($unloadingPrice, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 1%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }
        //END WHILE

        $returnValue .= '</tbody>';
//SELECTED DP 1
        if($boolCheked) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">'. number_format($totalPrice, 2, ".", ",") .'</td>';
            $returnValue .= '</tr>';

            $ppnDBAmount = $totalPPn;
            $pphDBAmount = $totalPPh;
            
            if($idPP == ''){
                if($ppn == 'NONE') {
                    $totalPPn = $ppnDBAmount;
                } elseif($ppn != $ppnDBAmount) {
                    $totalPPn = $ppn;
                } else {
                    $totalPPn = $ppnDBAmount;
                }

                if($pph == 'NONE') {
                    $totalPPh = $pphDBAmount;
                } elseif($pph != $pphDBAmount) {
                    $totalPPh = $pph;
                } else {
                    $totalPPh = $pphDBAmount;
                }
            }

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPn '.$ppntext .'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPn" id="totalPPn" value="'. number_format($totalPPn, 2, ".", ",") .'" onblur="checkSlipOB('. $stockpileId .', '. $laborId .', '. "'".$settle ."'".', this, document.getElementById(\'pph\'), \''. $paymentFromUP .'\', \''. $paymentToUP .'\', '. $idPP .');" /></td>';
            $returnValue .= '</tr>';

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPh '.$pphtext .'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPh" id="totalPPh" value="'. number_format($totalPPh, 2, ".", ",") .'" onblur="checkSlipOB('. $stockpileId .', '. $laborId .', '. "'".$settle ."'" .', document.getElementById(\'ppn\'), this, \''. $paymentFromUP .'\', \''. $paymentToUP .'\','. $idPP .');" /></td>';
            $returnValue .= '</tr>';
           
//----------------------------------DOWN PAYMENT---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            if($idPP == ''){
                $sql = "SELECT p.payment_id, p.`payment_no`, p.`amount_converted`,p.ppn_amount ,p.pph_amount, 
                        l.pph AS lpph, l.ppn AS lppn, c.`po_no`, c.`contract_no`,
                        (SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code, 
                        (SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
                        (SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,
                        CASE WHEN p.payment_location = 0 THEN 'HOF'
                                ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) 
                            END AS payment_location
                        FROM payment p 
                        LEFT JOIN labor l ON p.labor_id = l.`labor_id`
                        LEFT JOIN contract c ON c.`contract_id` = p.`freightContract`
                        WHERE p.labor_id = {$laborId} AND p.payment_method = 2 AND p.payment_status = 0 
                        AND p.payment_date > '2016-07-31' AND p.amount_converted > 0";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                $DpCount = $result->num_rows;
                
                if ($result->num_rows > 0) {
                    $totalDownPayment = 0;
                    $totalDppDpUC = 0;
                    while($row = $result->fetch_object()) {
                        $dppDpUC = 0;
                        $downPayment = 0;

                        $dppDpUC = $row->amount_converted;
                        $dpPPn = $dppDpUC * ($row->lppn/100);
                        $dpPPh = $dppDpUC * ($row->lpph/100);
                        $downPayment = ($dppDpUC + $dpPPn) - $dpPPh ;

                        // $dp = $dpFC;

                        #PAYMENT VOUCHER
                        $voucherCode = $row->payment_location .'/'. $row->bank_code .'/'. $row->pcur_currency_code;
                        
                        if($row->bank_type == 1) {
                            $voucherCode .= ' - B';
                        } elseif($row->bank_type == 2) {
                            $voucherCode .= ' - P';
                        } elseif($row->bank_type == 3) {
                            $voucherCode .= ' - CAS';
                        }
                        
                        if($row->bank_type != 3) {
                            if($row->payment_type == 1) {
                                $voucherCode .= 'RV';
                            } else {
                                $voucherCode .= 'PV';
                            }
                        }
                        $returnValue .= '<tr>';
                        $returnValue .= '<td colspan="1" style="text-align: right;">Down Payment</td>';
                        $returnValue .= '<td  style="text-align: right;">'.  $voucherCode .' # '.$row->payment_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                        $returnValue .= '<td  style="text-align: right;">'. number_format($dppDpUC, 2, ".", ",").'</td>';


                            //SELECTED DP 2
                        if($checkedSlipsDP != '') {
                            $posDP = strpos($checkedSlipsDP, $row->payment_id);
                            if($posDP === false) {
                                $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="uc2" value="'. $row->payment_id .'" onclick="checkSlipUnloadingDP('. $stockpileId .', '. $laborId .', '. "'".$settle ."'" .',\'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\','. $idPP .');" /></td>';
                            } else {
                                $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="uc2" value="'. $row->payment_id .'" onclick="checkSlipUnloadingDP('. $stockpileId .', '. $laborId .', '. "'".$settle ."'" .',\'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\','. $idPP .');" checked /></td>';
                                $totalDownPayment = $totalDownPayment + $downPayment; 
                                $totalDppDpUC = $totalDppDpUC + $dppDpUC; 
                                if($dppDpUC > $totalPrice){
                                    $disabled = 'disabled';
                                } else{
                                    $disabled = '';
                                }
                            }
                        } else {
                            $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSlipsDP[]" id="fc2" value="'. $row->payment_id .'" onclick="checkSlipUnloadingDP('. $stockpileId .', '. $laborId .', '. "'".$settle ."'" .',\'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\','. $idPP .');" /></td>';
                        }
                        $returnValue .= '<td style="text-align: right;">'. number_format($downPayment, 2, ".", ",") .'</td>';
                        $returnValue .= '</tr>';
                    }
                }
                
                if($settle == 0 ){
                    $grandTotal = ($totalPrice + $totalPPn) - $totalPPh - $totalDownPayment;
                    $DppGrandTotal = $totalPrice - $totalDppDpUC;                    
                }else{
                    $grandTotal = 0;
                }

                // $totalPrice2 = $totalPrice2;

                if($grandTotal < 0 && $totalDownPayment > 0 || ($grandTotal > 0 && $totalDownPayment > 0 && $settle == 2)) {
                    $grandTotal = 0;
                    // $downPaymentFC = $totalPrice2;
                }

            if($DpCount > 0){ 
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="4" style="text-align: right;">Settlement Only ?</td>';
                    if($settle == 1) {
                        $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" id="checkedSettle" value="'. $row->payment_id .'" onclick="checkSettle_unloading('. $stockpileId .', '. $laborId .', '. "'".$settle ."'" .',\'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\','. $idPP .');" checked/></td>';
                    } else {
                        $returnValue .= '<td style="text-align: center;"><input type="checkbox" name="checkedSettle" id="checkedSettle" '. $disabled.' value="'. $row->payment_id .'" onclick="checkSettle_unloading('. $stockpileId .', '. $laborId .', '. "'".$settle ."'" .',\'NONE\', \'NONE\', \''. $paymentFromUP .'\', \''. $paymentToUP .'\','. $idPP .');"  /></td>';                    
                    }        
                $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>'; //for settlement 50%
            }else{
                $returnValue .= '<td colspan="6" style="text-align: right;">Grand Total</td>'; //for settlemetn 100%
            }
        }else{ // VIEW DATA
            $sql = "SELECT ppd.*, CASE WHEN pp.settlement_status = 0 THEN 'No' ELSE 'Yes' END AS settle FROM pengajuan_payment_dp ppd 
                    LEFT JOIN pengajuan_payment pp ON pp.idPP = ppd.idPP WHERE pp.idPP = {$idPP}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
           // echo $sql;
            $DpCount = $result->num_rows;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_object()) {
                    $tempSettle = $row->settle;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="2" style="text-align: right;">Down Payment</td>';
                    $returnValue .= '<td  style="text-align: right;">'.$row->voucher_code.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->po_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. $row->contract_no.'</td>';
                    $returnValue .= '<td  style="text-align: right;">'. number_format(($row->dpp_dp_settle), 2, ".", ",").'</td>';
                    $returnValue .= '<td style="text-align: right;">'. number_format(($row->settle_amount), 2, ".", ",") .'</td>';
                    $returnValue .= '</tr>';
                 //   $returnValue .= '</tr>';
                }
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="4" style="text-align: right; color:red;">Settlement Only ?</td>';
            $returnValue .= '<td colspan="1" style="text-align: right;">'. $tempSettle.'</td>';
            $returnValue .= '<td colspan="1" style="text-align: right;">Grand Total</td>';
        }

        $returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
        $returnValue .= '</tr>';    
        $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '<input type="hidden" id="DppGrandTotal" name="DppGrandTotal" value="' . $DppGrandTotal . '" />';
        $returnValue .= '<input type="hidden" id="totalDownPayment" name="totalDownPayment" value="' . round($totalDownPayment, 2) . '" />';
		$returnValue .= '<input type="hidden" id="settle" name="settle" value="'. $settle .'" />';
        $returnValue .= '<input type="hidden" id="totalDppDpHC" name="totalDppDpHC" value="' . round($totalDppDpHC, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />';
        $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />';
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
        $returnValue .= '</div>';
        
    }

    echo $returnValue;
    echo $return_val;
}
//UNLOADING PAYMENT
function setSlipUnloading($stockpileId, $laborId, $checkedSlips, $ppn, $pph, $paymentFromUP, $paymentToUP, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $boolCheked = false;
    $whereProperty = '';
    $selectPaymentU = '';
    $whereIdPP = '';
    $wherePengajuan = '';

    if($idPP != ''){ 
        $selectPaymentU = "po.qty, po.dpp, po.total_amount as totalAmount, po.ppn_id, po.ppn_value, po.pph_id, po.pph_value, pp.total_dpp,  
                            pp.total_amount, pp.total_ppn_amount, pp.total_pph_amount, pp.grand_total, ";
        $whereIdPP = " AND po.idPP = {$idPP} ";
        $wherePengajuan = " LEFT JOIN payment_ob po ON po.transaction_id = t.transaction_id
                            LEFT JOIN pengajuan_payment pp ON pp.idPP = po.idpp ";
    }else if($idPP == ''){
        $whereProperty = "AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                            AND t.company_id = {$_SESSION['companyId']}
                            AND t.unloading_cost_id IS NOT NULL AND t.uc_payment_status <> 1 
                            AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
                            AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromUP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToUP', '%d/%m/%Y')
                            -- AND t.uc_payment_id IS NULL 
                            AND t.uc_payment_status <> 1";
        // $wherePengajuan = " LEFT JOIN payment_ pao ON pao.transaction_id = t.transaction_id AND pao.status = 0";
    }

    $sql = "SELECT {$selectPaymentU} t.*, l.labor_name, l.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
                -- CASE WHEN t.fc_payment_status = 2 THEN (SELECT MAX(amount) FROM payment_ob WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) ELSE 0 END AS split_ob,
                (SELECT MAX(total_amount) FROM payment_ob WHERE transaction_id = t.transaction_id and payment_method = 3 and status = 0) AS split_ob
            FROM `transaction` t
            INNER JOIN labor l
                ON l.labor_id = t.labor_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = l.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.uc_tax_id   
            {$wherePengajuan}
            WHERE t.labor_id = {$laborId}
          {$whereProperty}
          {$whereIdPP}
			ORDER BY t.transaction_date ASC";
         //   echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead>
                            <tr>
                                <th><INPUT type="checkbox" onchange="checkAllUC(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th>
                                <th>Transaaction Date</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unloading Cost</th>
                                <th>Total</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $totalQty = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            $vendorName = '';

            $vendorName = $row->labor_name;
            if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $row->unloading_price - $row->split_ob;
                
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($row->unloading_price - $row->split_ob) / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = ($row->unloading_price - $row->split_ob);
                }
            }

            $returnValue .= '<tr>';
            if($idPP == ''){
                if ($checkedSlips != '') {
                    $boolCheked = true;
                    $pos = strpos($checkedSlips, $row->transaction_id);

                    if ($pos === false) {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
                    } else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" checked /></td>';
                        $totalPrice = $totalPrice + $dppTotalPrice;

                        if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                            $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                            if($row->ppn_tax_value > 0){
                                $ppntext = round($row->ppn_tax_value,2) .'%';
                            }else{
                                $ppntext = '';
                            }
                        }

                        if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                            $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                            if($row->pph_tax_value > 0){
                                $pphtext = round($row->pph_tax_value,2) .'%';
                            }else{
                                $pphtext = '';
                            }
                        }

                        $totalQty = ($totalQty + $row->quantity);
                      //  $dpp = $dpp + ceil($row->unloading_price * $row->quantity);
                    }
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
                }
            }else{
                $boolCheked = true;
                $dppTotalPrice = $row->totalAmount;
                $totalPrice = $row->total_amount;
                $totalPpn = $row->total_ppn_amount;
                $totalPph = $row->total_pph_amount;
                $grandTotal = $row->grand_total;

                if ($row->ppn_id != 0 && $row->ppn_id != '') {
                    if($row->ppn_id > 0){
                        $ppntext = round($row->ppn_value,2) .'%';
                       
                    }else{
                        $ppntext = '';
                    }
                }
                if ($row->pph_id != 0 && $row->pph_id != '') {
                    if($row->pph_value > 0){
                        $pphtext = round($row->pph_value,2) .'%';
                    }else{
                        $pphtext = '';
                    }
                }

                $returnValue .= '<td><input type="hidden" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 20%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($row->quantity, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 5%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($dppTotalPrice, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($boolCheked) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            if($idPP == ''){
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
            }

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPn '. $ppntext. '</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12"readonly  name="totalPPn" id="totalPPn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', this, document.getElementById(\'ppn\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            $returnValue .= '</tr>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPh '.$pphtext.'</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" readonly name="totalPPh" id="totalPPh" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', document.getElementById(\'pph\'), this, \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            $returnValue .= '</tr>';


            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalDpp" name="totalDpp" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '<input type="hidden" id="totalQty" name="totalQty" value="' . $totalQty . '" />';
        $returnValue .= '<input type="hidden" id="vendorName" name="vendorName" value="' . $vendorName . '" />';
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
        $returnValue .= '</div>';
    }

    echo $returnValue;
    echo $return_val;
}

function setStockpileLocation_GET($stockpileId, $idPP)
{
    global $myDatabase;
    $htmlValue = '';
    $returnValue = '';

    if($idPP != ''){
        $sql = "SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
        FROM user_stockpile us
        INNER JOIN stockpile s
            ON s.stockpile_id = us.stockpile_id
        WHERE s.stockpile_id = {$stockpileId}
        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC LIMIT 1";
         $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if ($result->num_rows > 0) {
            $htmlValue = "|2|";
            $htmlValue = $htmlValue . "<SELECT tabindex='50' name='stockpileLocation' id='stockpileLocation'>";

            while ($row = $result->fetch_object()) {
                if (isset($_SESSION['payment']) && $_SESSION['payment']['stockpileLocation']) {
                    if ($_SESSION['payment']['stockpileLocation'] == $row->stockpile_id) {
                        $returnValue = "<label >Stockpile Location</option>";
                        $returnValue = $returnValue . "<option value='" . $row->stockpile_id . "' selected>" . $row->stockpile_full . "</option>";
                    }
                } else {
                    $returnValue = $returnValue . "<option value='" . $row->stockpile_id . "'>" . $row->stockpile_full . "</option>";
                }
            }
            $htmlValue = $htmlValue . $returnValue;
            $htmlValue = $htmlValue . "</SELECT>";
        }
        echo $htmlValue;

    }
}

function setSlipPengajuanCurah_copy($stockpileId, $vendorId, $checkedSlips, $paymentFrom1, $paymentTo1, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
  
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
                LEFT JOIN payment_curah pac
		            ON pac.`transaction_id` = t.`transaction_id`
                WHERE con.contract_type = 'C'
                AND con.vendor_id = {$vendorId}
                AND sc.stockpile_id = {$stockpileId}
                AND t.company_id = {$_SESSION['companyId']}
                AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo1', '%d/%m/%Y')
                AND (t.posting_status = 2 OR t.posting_status IS NULL)
                AND pac.`idPP` = {$idPP}";
               // echo $sql;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        // <th><INPUT type="checkbox" onchange="checkAllCurah(this)" name="checkedSlips[]" /></th>

        $returnValue .= '<thead>
                            <tr>
                                <th>Slip No.</th>
                                <th>Transaction Date</th>
                                <th>PO No.</th>
                                <th>Contract No.</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>DPP</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if ($row->pph_tax_id == 0) {
                $dppTotalPrice = ceil($row->unit_price * $row->quantity);
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = ceil(($row->unit_price * $row->quantity) / ((100 - $row->pph_tax_value) / 100));
                } else {
                    $dppTotalPrice = ceil($row->unit_price * $row->quantity);
                }
            }

        //    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
        //        $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
        //    }

        //    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
        //        $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
        //    }

            $returnValue .= '<tr>';
           
                    $totalPrice = $totalPrice + $dppTotalPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                // $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\', \'' . $row->ppayment_id . '\' );" checked /></td>';
              //  $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 15%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->contract_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 3%;">' . number_format($row->quantity, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 2%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 5%;">' . number_format($row->unit_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format(($row->unit_price * $row->quantity), 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 25%;">' . number_format($dppTotalPrice, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment FROM payment WHERE vendor_id = {$vendorId} AND payment_method = 2 AND payment_status = 0";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $downPayment = $row->down_payment;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="8">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 0, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

        //    $ppnDB = 0;
        //    $pphDB = 0;
        //    $sql = "SELECT vendor_id, ppn, pph FROM vendor WHERE vendor_id = {$vendorId}";
        //    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        //    if($result->num_rows == 1) {
        //        $row = $result->fetch_object();
        //        $ppnDB = $row->ppn;
        //        $pphDB = $row->pph;
        //    }

        //    $ppnDBAmount = ($ppnDB/100) * $totalPrice;
        //    $pphDBAmount = ($pphDB/100) * $totalPrice;

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            /*if($ppn == 'NONE') {
                $totalPPN = $ppnDBAmount;
            } elseif($ppn != $ppnDBAmount) {
                $totalPPN = $ppn;
            } else {
                $totalPPN = $ppnDBAmount;
            }*/

            /*if($pph == 'NONE') {
                $totalPPh = $pphDBAmount;
            } elseif($pph != $pphDBAmount) {
                $totalPPh = $pph;
            } else {
                $totalPPh = $pphDBAmount;
            }*/

        //    $totalPpn = ($ppn/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPPN, 0, ".", ",") . '" onblur="checkSlipCurah(' . $stockpileId . ',' . $vendorId . ', this, document.getElementById(\'pph\'), \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '</tr>';
        //    $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPPh, 0, ".", ",") . '" onblur="checkSlipCurah(' . $stockpileId . ',' . $vendorId . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '</tr>';

            $grandTotal = ($totalPrice + $totalPPN) - $totalPPh - $downPayment;
            $totalPrice = ($totalPrice + $totalPPN) - $totalPPh;
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 0) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 0) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}

function getInvoice_notim($idPP, $currentYearMonth)
{
    global $myDatabase;

    $sqlz = "SELECT tt.code_type_transaction, 
                    CASE WHEN payment_method = 1 THEN 'P'
                        WHEN payment_method = 2 THEN 'DP'
                        WHEN payment_method = 3 THEN 'FP'
                    ELSE NULL END as tipePayment
     FROM pengajuan_payment pp
    LEFT JOIN type_transaction tt ON tt.type_transaction_id = pp.payment_for
    WHERE idPP = {$idPP}";
    $resultx= $myDatabase->query($sqlz, MYSQLI_STORE_RESULT);
    if($resultx !== false){
        $rowx = $resultx->fetch_object();
        $code = $rowx->code_type_transaction;
        $tipePayment = $rowx->tipePayment;
    }

    $checkInvoiceNo = $tipePayment.'/PGJ/JPJ/'. $currentYearMonth .'/'. $code;

    $sql = "SELECT tt.code_type_transaction, inv.type_transaction_id, inv.inv_notim_no as invNotim FROM invoice_notim inv
    LEFT JOIN type_transaction tt ON tt.type_transaction_id = inv.type_transaction_id
    WHERE inv.inv_notim_no like '{$checkInvoiceNo}%' ORDER BY inv.inv_notim_id DESC LIMIT 1";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        $test = $rowInvoice->invNotim;
        $splitInvoiceNo = explode('/', $rowInvoice->invNotim);
        // $lastExplode = $splitInvoiceNo;
        $lastExplode = count($splitInvoiceNo) - 1;
        // $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]);
        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
        $pengajuanNo = $checkInvoiceNo . '/' . $nextInvoiceNo ;
    } else{
        $pengajuanNo = $checkInvoiceNo . '/1' ;
    }

    // $returnValue = '|' . $pengajuanNo ;
    $returnValue = '|' . $pengajuanNo ;
    echo $returnValue;
}

function getValidateInvoiceNo($invoiceNo)
{
    global $myDatabase;
    $sql = "SELECT * FROM pengajuan_payment pp
    WHERE invoice_no = '{$invoiceNo}'";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        $noInv = 1;
    } 
    $returnValue = $noInv;
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

function getBankITF($stockpileLocation, $newbank)
{
    global $myDatabase;
    $returnValue = '';
    $unionSql = '';
    $whereProperty = '';

    $sql = "SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full
                FROM bank b
             INNER JOIN currency cur
                 ON cur.currency_id = b.currency_id WHERE cur.currency_id = 1 and b.stockpile_id = {$stockpileLocation}
             ORDER BY b.bank_name ASC, cur.currency_code ASC";
         //   echo $sql;
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
