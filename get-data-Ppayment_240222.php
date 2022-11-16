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
        setSlipCurah($_POST['stockpileId'], $_POST['vendorId'], $_POST['checkedSlips'], $_POST['paymentFrom1'], $_POST['paymentTo1'], $_POST['idPP']);
    break;
    case "getStockpileContractPayment":
        getStockpileContractPayment($_POST['stockpileId'], $_POST['vendorId'], $_POST['paymentType']);
    break;
    case "getVendorBankDetail":
        getVendorBankDetail($_POST['vendorBankId'], $_POST['paymentFor']);
    break;
    case "getValidationPKHOA":
        getValidationPKHOA($_POST['contractPKHOA']);
    break;
    // case "getPriceDp":
    //     getPriceDp($_POST['contractPKHOA1'], $_POST['paymentFor'] );
    // break;
    case "getVendorPayment":
        getVendorPayment($_POST['stockpileId'], $_POST['contractType']);
    break;
    case "getFreightPayment":
        getFreightPayment($_POST['stockpileId']);
    break;
	 case "setSlipFreight_1":
        setSlipFreight_1($_POST['stockpileId_1'], $_POST['freightId_1'], $_POST['vendorFreightId_1'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom_1'], $_POST['paymentTo_1'], $_POST['idPP']);
    break;
	case "getFreightPayment_1":
        getFreightPayment_1($_POST['stockpileId']);
    break;
    case "getDpKontrakPKHOA":
        getDpKontrakPKHOA($_POST['stockpileId'], $_POST['freightIdFcDp']);
    break;
    case "getKontrakPKHOA":
        getKontrakPKHOA($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorFreightId']);
    break;
    case "getKontrakPKHOA1":
        getKontrakPKHOA1($_POST['stockpileIdFcDp'], $_POST['freightIdFcDp'], $_POST['vendorFreightIdDp']);
    break;
    case "getVendorFreight":
        getVendorFreight($_POST['stockpileId'], $_POST['freightId']);
    break;
    case "setSlipFreight":
        setSlipFreight($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorFreightId'], $_POST['contractPKHOA'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo']);
    break;
    case "setSlipFreight_copy":
        setSlipFreight_copy($_POST['stockpileId'], $_POST['freightId'], $_POST['vendorFreightId'], $_POST['contractPKHOA'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFrom'], $_POST['paymentTo'], $_POST['idPP']);
    break;
    case "getBankDetail":
        getBankDetail($_POST['bankVendor'], $_POST['paymentFor']);
    break;
    case "getHandlingPayment":
        getHandlingPayment($_POST['stockpileId']);
    break;
    case "setSlipHandling":
        setSlipHandling($_POST['stockpileId'], $_POST['vendorHandlingId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromHP'], $_POST['paymentToHP'], $_POST['idPP']);
    break;
    case "setSlipHandling_copy":
        setSlipHandling_copy($_POST['stockpileId'], $_POST['vendorHandlingId'], $_POST['checkedSlips'], $_POST['ppn'], $_POST['pph'], $_POST['paymentFromHP'], $_POST['paymentToHP'], $_POST['idPP']);
    break;
    case "getLaborPayment":
        getLaborPayment($_POST['stockpileId']);
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
    case "setStockpileLocation_GET":
        setStockpileLocation_GET($_POST['stockpileId'], $_POST['idPP']);
    break;
	    case "getBankITF":
        getBankITF($_POST['stockpileLocation'], $_POST['newbank']);
    break;
	    case "getContractHandlingDp":
        getContractHandlingDp($_POST['stockpileId'],$_POST['vendorHandlingId']);
    break;
    case "getHandlingTax":
        getHandlingTax($_POST['vendorHandlingDp']);
    break;
    case "getNoKontrak":
        getNoKontrak($_POST['vendorFreightIdDp']);
    break;
    case "getFreightTax":
        getFreightTax($_POST['freightId']);
    break;
    case "setSlipDp":
        setSlipDp($_POST['idPP']);
    break;
    case "getLaborTax":
        getLaborTax($_POST['laborDp']);
    break;


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

function setSlipDp($idpp)
{
    global $myDatabase;
    $returnValue = '';

    $sql = "SELECT t.`slip_no`, c.`po_no`,
            COALESCE(ts.`trx_shrink_claim`,0) AS shrink_price_claim, COALESCE(ts.amt_claim,0) AS shrink_amount,
            ROUND((pp.dpp + pp.ppn_amount) - pp.pph_amount,2) AS amount,
            ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
                    WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
                    WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                    WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)ELSE 0 END,10) AS shrink_qty_claim,
            pp.*
            FROM pengajuan_payment pp
            LEFT JOIN payment_oa payo ON payo.idpp = pp.idpp
            LEFT JOIN `transaction` t ON t.transaction_id = payo.transaction_id
            LEFT JOIN stockpile_contract sc ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
            LEFT JOIN contract c ON c.`contract_id` = sc.`contract_id`
            LEFT JOIN freight f ON f.`freight_id` = pp.freight_id
            LEFT JOIN transaction_shrink_weight ts ON t.transaction_id = ts.transaction_id
            WHERE pp.idpp = {$idpp} ORDER BY t.slip_no ASC";
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
			
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['qty'], 2, ".", ",") .' '. $row['uom']. '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['price'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['dpp'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['ppn_amount'], 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; ">' . number_format($row['pph_amount'], 2, ".", ",") . '</td>';
			$returnValue .= '<td style="text-align: right; ">' . number_format($row['amount'], 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
			
		$totalQty = $totalQty + $row['qty'];
        $totalDpp = $totalDpp + $row['dpp'];
		$totalPPN = $totalPPN + $row['ppn_amount'];
		$totalPPh = $totalPPh + $row['pph_amount'];
		$totalAmount = $totalAmount + $row['amount'];
		$invoiceBankId = $row['vendor_bank_id'];
		$invoiceCurrencyId = $row['currency_id'];
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


function setStockpileLocation($stockpileId, $idPP)
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

    } else {
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
        } 
        // elseif ($result->num_rows == 1) {
        //     $row = $result->fetch_object();
        //     $htmlValue = "|1|";
        //     $htmlValue = $htmlValue . "<input type='hidden' name='stockpileLocation' id='stockpileLocation' value='" . $row->stockpile_id . "' />";
        // } 

        elseif ($result->num_rows == 1) {
            $htmlValue = "|2|";
            $htmlValue = $htmlValue . "<SELECT tabindex='50' name='stockpileLocation' id='stockpileLocation'>";

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
        }
        else {
            $htmlValue = "|1|";
            $htmlValue = $htmlValue . "<input type='hidden' name='stockpileLocation' id='stockpileLocation' value='0' />";
        }
    }
    echo $htmlValue;
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
function setSlipCurah($stockpileId, $vendorId, $checkedSlips, $paymentFrom1, $paymentTo1, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $whereProperty = '';
    $boolcontinue = false;

    if($idPP == ''){
        $whereProperty = "AND (t.ppayment_id IS NULL AND t.payment_id IS NULL )";
    }else if($idPP != ''){
        $whereProperty = " AND (t.ppayment_id = {$idPP} AND t.payment_id IS NULL)";
    
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
            AND t.company_id = {$_SESSION['companyId']}
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo1', '%d/%m/%Y')
			AND (t.posting_status = 2 OR t.posting_status IS NULL)
            AND t.payment_id IS NULL
            {$whereProperty}
            ORDER BY t.slip_no ASC";
            //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        // $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="test" id="test" value="' . $checkedSlips . '" /></td>'; //KHUSUS TESTING NILAI TRANSAKSI_ID
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onClick="checkAllCurah(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th></th>
                                <th>Transaction Date</th>
                                <th>PO No.</th><th>Contract No.</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>DPP</th></tr>
                         </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $quanty = 0;
        $unitprice = 0;
        $dpp = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            if ($row->pph_tax_id == 0) {
                $dppTotalPrice = round(($row->unit_price * $row->quantity), 2); //ceil di hapus
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = round(($row->unit_price * $row->quantity),2) / round((100 - $row->pph_tax_value) / 100, 2); //ceil dihapus
                } else {
                    $dppTotalPrice = round(($row->unit_price * $row->quantity), 2); //ceil dihapus
                }
            }
           

            $returnValue .= '<tr>';
            if($idPP == ''){
                if ($checkedSlips != '') {
                    $pos = strpos($checkedSlips, $row->transaction_id);

                    if ($pos === false) {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
                    } 
                    else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" checked /></td>';
                        $totalPrice = $totalPrice + $dppTotalPrice;

                        if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                            $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                        }

                        if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                            $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                        }
                        $quanty = ($quanty + $row->quantity);
                        $unitprice = $row->unit_price;
                        $dpp = $dppTotalPrice;
                        
                    }
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
                }
            }else{
                $totalPrice = $totalPrice + $dppTotalPrice;

                if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                    $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                }

                if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                    $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                }
                $quanty = ($quanty + $row->quantity);
                $unitprice = $row->unit_price;
                $dpp = $dppTotalPrice;
                $returnValue .= '<td><input type="hidden" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipCurah(' . $stockpileId . ',' . $row->vendor_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';

            }
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

        if ($checkedSlips != '' && $idPP == '') {
            $boolcontinue = true;
        }else if($checkedSlips == '' && $idPP != ''){
            $boolcontinue = true;
        }

        $returnValue .= '</tbody>';
        if ($boolcontinue) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment FROM payment WHERE vendor_id = {$vendorId} AND payment_method = 2 AND payment_status = 0";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $downPayment = $row->down_payment;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="9">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 0, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

            $ppnDBAmount = $totalPPN;
            $pphDBAmount = $totalPPh;

            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPPN, 0, ".", ",") . '" onblur="checkSlipCurah(' . $stockpileId . ',' . $vendorId . ', this, document.getElementById(\'pph\'), \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '</tr>';
         // $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPPh, 0, ".", ",") . '" onblur="checkSlipCurah(' . $stockpileId . ',' . $vendorId . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom1 . '\', \'' . $paymentTo1 . '\');" /></td>';
            $returnValue .= '</tr>';

            $grandTotal = ($totalPrice + $totalPPN) - $totalPPh - $downPayment;
            $totalPrice = ($totalPrice + $totalPPN) - $totalPPh;
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 0, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 0) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 0) . '" />';

        $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $quanty . '" />';
        $returnValue .= '<input type="hidden" id="price" name="price" value="' . $unitprice . '" />'; 
        $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dpp . '" />'; 
        $returnValue .= '</div>';
    }

   // $return_val = '|' . $grandTotal;
   $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
   echo $returnValue;
    echo $return_val;
}

// function getPriceDp($contractPKHOA1, $paymentFor)
// {
//     global $myDatabase;
//     $returnValue = '';

//     if ($paymentFor == 2) { //Freight
//         $sql = "SELECT price_converted FROM freight_cost WHERE freight_cost_id = {$contractPKHOA1}";
//         $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//     } 
//     //elseif ($paymentFor == 3) { //Unloading
//     //     $sql = "SELECT *
//     //         FROM labor_bank
//     //         WHERE l_bank_id = {$vendorBankId}";
//     //     $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//     // } elseif ($paymentFor == 6 || $paymentFor == 8 || $paymentFor == 10) { //HO / Invoice
//     //     $sql = "SELECT *
//     //         FROM general_vendor_bank
//     //         WHERE gv_bank_id = {$vendorBankId}";
//     //     $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//     // } elseif ($paymentFor == 9) { //vendor_hanndling
//     //     $sql = "SELECT *
//     //         FROM vendor_handling_bank
//     //         WHERE vh_bank_id = {$vendorBankId}";
//     //     $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//     // }
//     if ($result->num_rows == 1) {
//         $row = $result->fetch_object();
//         $priceFreight = $row->price_converted;

//         $returnValue = '|' . $priceFreight;

//     } else {
//         $returnValue = '|-';
//     }

//     echo $returnValue;
// }

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

function getValidationPKHOA($contractPKHOA)
{
    global $myDatabase;
    $returnValue = '';
        $sql = "SELECT  DISTINCT(fc.freight_cost_id), fc.contract_pkhoa , pp.payment_status
                FROM freight_cost fc
                LEFT JOIN pengajuan_payment pp ON pp.`freight_cost_id` = fc.`freight_cost_id`
                WHERE fc.`freight_cost_id` = {$contractPKHOA}
                ORDER BY fc.freight_cost_id ASC ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
   
    if ($result->num_rows >= 1) {
        $row = $result->fetch_object();
        $paymentStatus = $row->payment_status;

        $returnValue = '|' . $paymentStatus;

    } else {
        $returnValue = '|-';
    }

   // $returnValue = '|' .$sql;
    echo $returnValue;
}

//FREIGHT
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

function getFreightPayment_1($stockpileId)
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

// if (isset($_POST['checkedSlips'])) {                                            
//     $checks = $_POST['checkedSlips'];
//     for ($i = 0; $i < sizeof($checks); $i++) {
//         if($slipNos == '') {
//             $slipNos .= $checks[$i];
//         } else {
//             $slipNos .= ','. $checks[$i];
                    
//         }
//     }
// }

function getKontrakPKHOA($stockpileId, $freightId, $vendorFreightId)
{
    global $myDatabase;
    $returnValue = '';
    $tempWhereProperty = '';
    
    if (isset($_POST['vendorFreightId'])) {                                            
        $array = $_POST['vendorFreightId'];
            for ($i = 0; $i < sizeof($array); $i++) {
                if($vendorFreightIdss == '') {
                    $vendorFreightIdss .= $array[$i];
                } else {
                    $vendorFreightIdss .= ','. $array[$i];
                            
                }
            }
        if($vendorFreightIdss > 0){
            $temptempWhereProperty =  "AND v.vendor_id IN ({$vendorFreightIdss})";
        }
    }

    $sql = " SELECT DISTINCT(fc.freight_cost_id), fc.contract_pkhoa 
            FROM freight_cost fc
            LEFT JOIN vendor v ON v.vendor_id = fc.vendor_id
            LEFT JOIN freight f ON f.freight_id = fc.freight_id
            WHERE fc.stockpile_id  = {$stockpileId}
            {$temptempWhereProperty}
			AND f.`freight_id` = {$freightId}
            AND fc.`company_id` = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY fc.freight_cost_id ASC";
           //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->freight_cost_id . '||' . $row->contract_pkhoa;
            } else {
                $returnValue = $returnValue . '{}' . $row->freight_cost_id . '||' . $row->contract_pkhoa;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    
    //$returnValue = '~ 1'.$sql;
    echo $returnValue;
}


function getKontrakPKHOA1($stockpileIdFcDp, $freightIdFcDp, $vendorFreightIdDp)
{
    global $myDatabase;
    $returnValue = '';
    $tempWhereProperty = '';
    
    // if (isset($_POST['vendorFreightIdDp'])) {                                            
    //     $array = $_POST['vendorFreightIdDp'];
    //         for ($i = 0; $i < sizeof($array); $i++) {
    //             if($vendorFreightIdDpss == '') {
    //                 $vendorFreightIdDpss .= $array[$i];
    //             } else {
    //                 $vendorFreightIdDpss .= ','. $array[$i];
                            
    //             }
    //         }
    //     if($vendorFreightIdDpss > 0){
    //         $temptempWhereProperty =  "AND v.vendor_id IN ({$vendorFreightIdDpss})";
    //     }
    // }
    

    $sql = " SELECT DISTINCT(fc.freight_cost_id), fc.contract_pkhoa 
            FROM freight_cost fc
            LEFT JOIN vendor v ON v.vendor_id = fc.vendor_id
            LEFT JOIN freight f ON f.freight_id = fc.freight_id
            WHERE fc.stockpile_id  = {$stockpileIdFcDp}
            -- {$temptempWhereProperty}
            AND v.vendor_id = {$vendorFreightIdDp}
			AND f.`freight_id` = {$freightIdFcDp}
            AND fc.`company_id` = {$_SESSION['companyId']}
			AND f.active = 1
            ORDER BY fc.freight_cost_id ASC";
           //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->freight_cost_id . '||' . $row->contract_pkhoa;
            } else {
                $returnValue = $returnValue . '{}' . $row->freight_cost_id . '||' . $row->contract_pkhoa;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    
    //$returnValue = '~ 1'.$sql;
    echo $returnValue;
}

function setSlipFreight_1($stockpileId_1, $freightId_1, $vendorFreightId_1, $checkedSlips, $ppn, $pph, $paymentFrom_1, $paymentTo_1, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $whereProperty1 = '';
    $return_val = '';
    $boolcontinue = false;
    $whereProperty2 = '';

    if ($checkedSlips == '') {
        for ($i = 0; $i < sizeof($vendorFreightId_1); $i++) {
            if ($vendorFreightIds == '') {
                $vendorFreightIds .= $vendorFreightId_1[$i];
            } else {
                $vendorFreightIds .= ',' . $vendorFreightId_1[$i];
            }
        }
    } else {
        $vendorFreightIds = $vendorFreightId_1;
    }

    if ($vendorFreightIds != 0 and $idPP == '') {
        $whereProperty2 = "AND t.vendor_id IN ({$vendorFreightIds})";
    }

if($idPP == ''){
    $whereProperty = " AND (t.fc_ppayment_id IS NULL AND t.payment_id IS NULL)";
}else if($idPP != ''){
    $whereProperty = " AND (t.fc_ppayment_id = {$idPP} AND t.payment_id IS NULL)";

}
    $sql = "SELECT fc.`contract_pkhoa`, COALESCE(hsw.amt_claim) as hsw_amt_claim, COALESCE(ts.amt_claim,0) AS amt_claim, 
            t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`,

			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1

				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim, v.vendor_name
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
            WHERE fc.freight_id = {$freightId_1}
			AND sc.stockpile_id = {$stockpileId_1}
            {$whereProperty2}
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId_1})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.fc_payment_id IS NULL AND freight_price != 0
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom_1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo_1', '%d/%m/%Y')
			AND (t.posting_status = 2 OR t.posting_status IS NULL)
            {$whereProperty}
			ORDER BY t.slip_no ASC";

            
  //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        $returnValue .= '<input type="hidden" id="vFid" name="vFid" value="' . $vendorFreightIds . '" />';  //khusus  kode vendor_ID
        // $returnValue .= '<input type="hidden" id="pkhoa" name="pkhoa" value="' . $contractPKHOA . '" />';  //khusus  kode freight_cost_id
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
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

            $totalPrice = 0;
            //start add by Eva
            $totalDpp = 0;
            $totalqty = 0;
            $totalSrink = 0;
            //end add by Eva
            $totalPPN = 0;
            $totalPPh = 0;
            while ($row = $result->fetch_object()) {
                $dppTotalPrice = 0;
                if ($row->freight_rule == 1) {
                    $fp = $row->freight_price * $row->send_weight;
                    $dpp = $fp;
                    $fq = $row->send_weight;
                } else {
                    $fp = $row->freight_price * $row->freight_quantity; // fc.200 * t.7978.1
                    $fq = $row->freight_quantity; //7978,10
                    $dpp = $fp;
                }

                if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1 && ($row->pph_tax_id == 0 || $row->pph_tax_id == '')) {
                    $dppTotalPrice = $fp;
                    // $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                    $dppShrinkPrice = $row->amt_claim;
				    $hsw_amt_claim = $row->hsw_amt_claim;
                } else {
                    if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                        $dppTotalPrice = $fp;
                      //  $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                        $dppShrinkPrice = $row->amt_claim;
				        $hsw_amt_claim = $row->hsw_amt_claim;
                    } else {
                        if ($row->pph_tax_category == 1 && $row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                            $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                            // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                            $dppShrinkPrice = ($row->amt_claim) / ((100 - $row->pph_tax_value) / 100);
                            $hsw_amt_claim = ($row->hsw_amt_claim) / ((100 - $row->pph_tax_value) / 100);
        
                        } else {
                            if ($row->pph_tax_category == 1) {
                                $dppTotalPrice = ($fp) / ((100 - $row->pph_tax_value) / 100);
                                // $dppShrinkPrice = ($row->qtyClaim * $row->trx_shrink_claim) / ((100 - $row->pph_tax_value) / 100);
                                $dppShrinkPrice = ($row->amt_claim) / ((100 - $row->pph_tax_value) / 100);
                                $hsw_amt_claim = ($row->hsw_amt_claim) / ((100 - $row->pph_tax_value) / 100);
            
                            } else {
                                $dppTotalPrice = $fp;
                                // $dppShrinkPrice = $row->qtyClaim * $row->trx_shrink_claim;
                                $dppShrinkPrice = $row->amt_claim;
                                $hsw_amt_claim = $row->hsw_amt_claim;
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

                $amountPrice = $dppTotalPrice - ($dppShrinkPrice + $hsw_amt_claim);
            
                $returnValue .= '<tr>';
                if($idPP == ''){
                    if ($checkedSlips != '') {
                        $pos = strpos($checkedSlips, $row->transaction_id);

                        if ($pos === false) {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
                        } else {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" checked /></td>';
                            $totalPrice = $totalPrice + $amountPrice;

                            if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                                $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                            }

                            if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                                $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                            }

                            $quanty = ($quanty + $fq);
                            $unitprice = $row->freight_price;
                            $dpp = $dpp + ceil($freightPrice);
                            $totalShrink = $totalShrink + $dppShrinkPrice + $hsw_amt_claim;
                        }
                    } else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
                    }
                }else if($idPP != ''){
                    $totalPrice = $totalPrice + $amountPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }

                    $quanty = ($quanty + $fq);
                    $unitprice = $row->freight_price;
                    // $dpp = $dpp + ceil($row->freight_price * $row->freight_quantity);
                    $returnValue .= '<td><input type="hidden" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ',' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';

                }
                $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->vendor_name . '</td>';
                $returnValue .= '<td style="width: 10%;">' . $row->vehicle_no . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($fq, 0, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_price, 0, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($freightPrice, 2, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qtyClaim, 0, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->trx_shrink_claim, 0, ".", ",") . '</td>';
                $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppShrinkPrice, 2, ".", ",") . '</td>';
                if($hsw_amt_claim != '' || $hsw_amt_claim != 0){
                    //Additional Shrink
                    $returnValue .= '<td style="width: 10%;">' . number_format($hsw_amt_claim, 2, ".", ",") . '</td>';
                }else{
                    $returnValue .= '<td style="width: 10%;">' . '-' . '</td>';
                }
                $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($amountPrice, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

            if ($checkedSlips != '' && $idPP == '') {
                $boolcontinue = true;
            }else if($checkedSlips == '' && $idPP != ''){
                $boolcontinue = true;
            }

            $returnValue .= '</tbody>';
                if ($boolcontinue) {
                    $returnValue .= '<tfoot>';
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">Total</td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
                    $returnValue .= '</tr>';

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

        // $totalPpn = ($ppnValue/100) * $totalPrice;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">PPN</td>';
                    $returnValue .= '<td><input type="text" readonly style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $freightId_1 . ',' . "'" . $vendorFreightIds . "'" . ', this, document.getElementById(\'pph\'), \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
                    $returnValue .= '</tr>';
        // $totalPph = ($pphValue/100) * $totalPrice;
                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">PPh</td>';
                    $returnValue .= '<td><input type="text" readonly style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $freightId_1 . ',' . "'" . $vendorFreightIds . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
                    $returnValue .= '</tr>';

                    $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, pp.pph, pp.stockpile_location, f.freight_id
                    FROM payment p LEFT JOIN freight f ON p.`freight_id` = f.`freight_id`
                    WHERE p.freight_id = {$freightId_1} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";

                    $downPayment = 0;
                        if ($result !== false && $result->num_rows == 1) {
                            $row = $result->fetch_object();
                            $dp = $row->down_payment * ((100 - $row->pph) / 100);
                            $fc_ppn_dp = $row->down_payment * ($row->ppn / 100);
                            $downPayment = $dp + $fc_ppn_dp;
                        }

                    $returnValue .= '<tr>';
                    $returnValue .= '<td colspan="14" style="text-align: right;">Down Payment</td>';
                    $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                    $returnValue .= '</tr>';

                    //edited by alan
                    $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
                    //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


                    $totalPrice = ($totalPrice + $totalPpn) - $totalPph;

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
            $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
            $returnValue .= '<input type="hidden" id="totalPrice1" name="totalPrice1" value="' . round($amountPrice, 2) . '" />';
            //$returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="vendorFreights" id="vendorFreights" value="'. $vendorFreightIds .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', this, document.getElementById(\'ppn\'), document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" />';
            $returnValue .= '<input type="hidden" id="fc_ppn_dp" name="fc_ppn_dp" value="' . $fc_ppn_dp . '" />';
            $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
            $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
            $returnValue .= '<input type="hidden" id="ppnOA" name="ppnOA" value="' . $totalPpn . '" />';
            $returnValue .= '<input type="hidden" id="pphOA" name="pphOA" value="' . $totalPph . '" />';

            $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $quanty . '" />';
            $returnValue .= '<input type="hidden" id="price" name="price" value="' . $unitprice . '" />'; 
            $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dpp . '" />';
            $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
            $returnValue .= '</div>';
        }
  // $returnValue = $contractPKHOAids;
    echo $returnValue;
    echo $return_val;
}

function getDpKontrakPKHOA($stockpileId, $freightIdFcDp, $vendorFreightId)
{
    global $myDatabase;
    $returnValue = '';
    $tempWhereProperty = '';
    
    // if (isset($_POST['vendorFreightId'])) {                                            
    //     $array = $_POST['vendorFreightId'];
    //         for ($i = 0; $i < sizeof($array); $i++) {
    //             if($vendorFreightIdss == '') {
    //                 $vendorFreightIdss .= $array[$i];
    //             } else {
    //                 $vendorFreightIdss .= ','. $array[$i];
                            
    //             }
    //         }
    //     if($vendorFreightIdss > 0){
    //         $temptempWhereProperty =  "AND v.vendor_id IN ({$vendorFreightIdss})";
    //     }
       
    // }

    // $sql = "SELECT DISTINCT(fc.freight_cost_id), fc.contract_pkhoa 
    //         FROM freight_cost fc
    //         LEFT JOIN freight f ON f.freight_id = fc.freight_id
    //         LEFT JOIN pengajuan_payment pp on pp.freight_cost_id = fc.freight_cost_id
    //         WHERE fc.stockpile_id  = {$stockpileId}
    //         -- {$temptempWhereProperty}
	// 		AND f.`freight_id` = {$freightIdFcDp}
    //         AND fc.`company_id` = {$_SESSION['companyId']}
	// 		AND f.active = 1
    //         AND pp.payment_status IS NULL
    //         ORDER BY fc.freight_cost_id ASC";

    $sql = "SELECT MAX(DISTINCT(fc.freight_cost_id)) as freight_cost_id, fc.contract_pkhoa, 
            CONCAT(f.freight_code,' - ', f.freight_supplier) AS freight_supplier,
            fc.active_from, fc.entry_date
            FROM freight_cost fc
            LEFT JOIN freight f ON f.freight_id = fc.freight_id
            LEFT JOIN pengajuan_payment pp ON pp.freight_cost_id = fc.freight_cost_id
            WHERE fc.stockpile_id  = {$stockpileId}
            -- {$temptempWhereProperty}
			AND f.`freight_id` = {$freightIdFcDp}
            AND fc.`company_id` = {$_SESSION['companyId']}
			AND f.active = 1
            AND pp.payment_status IS NULL
            AND active_from IS NOT NULL
            ORDER BY fc.freight_cost_id ASC";
           //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->freight_cost_id . '||' . $row->contract_pkhoa;
            } else {
                $returnValue = $returnValue . '{}' . $row->freight_cost_id . '||' . $row->contract_pkhoa;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    
    //$returnValue = '~ 1'.$sql;
    echo $returnValue;
}

function setSlipFreight($stockpileId, $freightId, $vendorFreightId, $contractPKHOA, $checkedSlips, $ppn, $pph, $paymentFrom, $paymentTo)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $whereProperty1 = '';
    $return_val = '';
    //$vendorFreightId[] = array();
    if ($checkedSlips == '') {
        for ($i = 0; $i < sizeof($vendorFreightId); $i++) {
            if ($vendorFreightIds == '') {
                $vendorFreightIds .= $vendorFreightId[$i];
            } else {
                $vendorFreightIds .= ',' . $vendorFreightId[$i];
            }
        }

        // for ($a = 0; $a < sizeof($contractPKHOA); $a++) {
        //     if ($contractPKHOAids == '') {
        //         $contractPKHOAids .= $contractPKHOA[$a];
        //     } else {
        //         $contractPKHOAids .= ',' . $contractPKHOA[$a];
        //     }
        // }

    } else {
        $vendorFreightIds = $vendorFreightId;
        // $contractPKHOAids = $contractPKHOA;
    }

    //echo $vendorFreightIds ;

    if ($vendorFreightIds != 0) {
        $whereProperty = "AND t.vendor_id IN ({$vendorFreightIds})";
    }
    // if ($contractPKHOA != "") {
    //     $whereProperty1 = "AND fc.`freight_cost_id` = {$contractPKHOA}";
    // }

    $sql = "SELECT fc.`contract_pkhoa`, t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`,

			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1

				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
				ROUND(t.`freight_price` * t.`freight_quantity`,10)  AS dpp,
				ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10)  AS ppn,
				ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10)  AS pph,
				((ROUND(t.`freight_price` * t.`freight_quantity`,10) + ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10) - ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10))) AS amount,
				((ROUND(t.`freight_price` * t.`freight_quantity`,10) + ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10) - ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10)) - COALESCE(ts.amt_claim,0)) AS total_amount,
				COALESCE(ts.amt_claim,0) AS shrink_amount
				
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
            AND (t.fc_ppayment_id IS NULL AND t.payment_id IS NULL )
            AND fc.`freight_cost_id` = {$contractPKHOA}
			ORDER BY t.slip_no ASC";
        /// echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    $sqlValidation = "SELECT STATUS FROM pengajuan_dp where status = 0 and freight_cost_id = {$contractPKHOA} order by pengajuan_dp_id desc limit 1";
    $resultValidation =  $myDatabase->query($sqlValidation, MYSQLI_STORE_RESULT);
        if($resultValidation !== false && $resultValidation->num_rows == 1){
            $row1 = $resultValidation->fetch_object();
            $tempStatus = $row1->status;
        }else{
            $tempStatus = 1;
        }
if($tempStatus == 1){
    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        $returnValue .= '<input type="hidden" id="vFid" name="vFid" value="' . $vendorFreightIds . '" />';  //khusus  kode vendor_ID
        // $returnValue .= '<input type="hidden" id="pkhoa" name="pkhoa" value="' . $contractPKHOA . '" />';  //khusus  kode freight_cost_id
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th>
                                <th>Slip No.</th><th>Transaction Date</th>
                                <th>PO No.</th><th>Vendor Code</th>
                                <th>Vehicle No</th>
                                <th>Quantity</th>
                                <th>Freight Cost / kg</th>
                                <th>Amount</th>
                                <th>Shrink Qty Claim</th>
                                <th>Shrink Price Claim</th>
                                <th>Shrink Amount</th>
                                <th>Total Amount</th>
                                </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;
            /*if ($row->freight_rule == 1) {
                $fp = $row->freight_price * $row->send_weight;
                $fq = $row->send_weight;
            } else {
                $fp = $row->freight_price * $row->quantity;
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

            $amountPrice = $dppTotalPrice - $dppShrinkPrice;*/
			$dppShrinkPrice = 0;
           
            $returnValue .= '<tr>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->transaction_id);

                if ($pos === false) {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ','."'". $contractPKHOA . "'". ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ','. "'". $contractPKHOA . "'". ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" checked /></td>';


                    $totalPrice = $totalPrice + $row->dpp;
					$totalPpn = $totalPpn + $row->ppn;
					$totalPph = $totalPph + $row->pph;
					$totalSusut = $totalSusut + $row->shrink_amount;
					$totalAmount = $totalAmount + $row->total_amount;
					

                    /*if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }*/

                    $quanty = ($quanty + $row->freight_quantity);
                    $unitprice = $row->freight_price;
                    $dpp = $dpp + $row->dpp;
					$dpp1 = $dpp;
                }
            } else {
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', '. "'".$contractPKHOA . "'". ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vehicle_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($fq, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_price, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->dpp, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qtyClaim, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->trx_shrink_claim, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row->shrink_amount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row->total_amount, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Total DPP</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
			$dppTotal = $totalPrice;

            /*$ppnDBAmount = $row->ppn;
            $pphDBAmount = $row->pph;

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
            }*/
			
			//$totalPpn = $row->ppn;
            //$totalPph = $row->pph;

//            $totalPpn = ($ppnValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ','. "'".$contractPKHOA. "'". ', this, document.getElementById(\'pph\'), \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ','."'" . $contractPKHOA . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            $returnValue .= '</tr>';

              // $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, pp.pph, pp.stockpile_location, f.freight_id
            // FROM payment p LEFT JOIN freight f ON p.`freight_id` = f.`freight_id`
            // WHERE p.freight_id = {$freightId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
       
            //SATELMEN DOWN PAYMENT
            $sql = "SELECT idpp, freight_cost_id, (downPayment - amount_payment) AS sisaDp, STATUS, downPayment, amount_payment FROM pengajuan_dp
                    WHERE freight_cost_id = {$contractPKHOA} AND STATUS = 3  ORDER BY pengajuan_dp_id DESC LIMIT 1";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                if ($result !== false && $result->num_rows == 1) {
                    $row = $result->fetch_object();
                    if($row->sisaDp == 0 || $row->sisaDp < 0){
                        $downPayment = 0;
                    }else{
                        $downPayment = $row->sisaDp;
                    }
                }else{
                    $sql = "SELECT COALESCE(SUM(pp.amount), 0) AS down_payment, f.pph, f.ppn, pp.stockpile_id, f.freight_id, fc.freight_cost_id 
                    FROM pengajuan_payment pp 
                    LEFT JOIN freight_cost fc ON pp.`freight_cost_id` = fc.`freight_cost_id`
                    LEFT JOIN freight f ON pp.freight_id = f.freight_id
                    WHERE pp.freight_cost_id = {$contractPKHOA} AND pp.payment_method = 2 AND pp.payment_status = 3";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                    $downPayment = 0;
                    if ($result !== false && $result->num_rows == 1) {
                        $row = $result->fetch_object();
                        $dp = $row->down_payment * ((100 - $row->pph) / 100);
                        $fc_ppn_dp = $row->down_payment * ($row->ppn / 100);
                        $downPayment = $dp + $fc_ppn_dp;
                    }
                }
           

                //TESTING 
                // $returnValue .= '<tr>';
                // $returnValue .= '<td colspan="12" style="text-align: right;">TESTING</td>';
                // $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="test" id="test" value="' . $checkedSlips . '" /></td>'; //KHUSUS TESTING BUAT TAU BERAPA ID TRANSAKSI YG AKAN DIKIRIM
                // $returnValue .= '</tr>';
				
			$returnValue .= '<tr>';
            $returnValue .= '<td colspan="12" style="text-align: right;">Total Susut</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalSusut, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="12" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';

            //}



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
		$returnValue .= '<input type="hidden" id="totalPrice1" name="totalPrice1" value="' . round($dpp1, 2) . '" />';

        //$returnValue .= '<input type="hidden" style="text-align: right;" class="span12" name="vendorFreights" id="vendorFreights" value="'. $vendorFreightIds .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', this, document.getElementById(\'ppn\'), document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" />';
        $returnValue .= '<input type="hidden" id="fc_ppn_dp" name="fc_ppn_dp" value="' . $fc_ppn_dp . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';

        $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $quanty . '" />';
        $returnValue .= '<input type="hidden" id="price" name="price" value="' . $unitprice . '" />'; 
        $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dpp . '" />';
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
        $returnValue .= '</div>';
    }

} else if($tempStatus == 0) {
    $returnValue = '<label style="color: red;">Data belum selesai di payment/diproses</label>';
}
    
  // $returnValue = $contractPKHOAids;
    echo $returnValue;
    echo $return_val;
}

function setSlipFreight_copy($stockpileId, $freightId, $vendorFreightId, $contractPKHOA, $checkedSlips, $ppn, $pph, $paymentFrom, $paymentTo, $idPP)
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
	
	if($contractPKHOA != ''){
        $sql = "SELECT fc.`contract_pkhoa`,  COALESCE(hsw.amt_claim) as hsw_amt_claim, COALESCE(ts.amt_claim,0) AS shrink_amount, t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`,

			    ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1

				WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1

				WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
				ROUND(t.`freight_price` * t.`freight_quantity`,10)  AS dpp,
				ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10)  AS ppn,
				ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10)  AS pph,
				((ROUND(t.`freight_price` * t.`freight_quantity`,10) + ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10) - ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10))) AS amount,
				((ROUND(t.`freight_price` * t.`freight_quantity`,10) + ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10) - ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10)) - COALESCE(ts.amt_claim,0)) AS total_amount,
                    fc.file1 as document
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

                WHERE fc.freight_id = {$freightId}
                AND sc.stockpile_id = {$stockpileId}
             
                AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                AND t.company_id = {$_SESSION['companyId']}
                AND t.fc_payment_id IS NULL AND freight_price != 0
                AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')
                AND (t.posting_status = 2 OR t.posting_status IS NULL)
                and t.fc_ppayment_id = {$idPP}
                AND fc.`freight_cost_id` = {$contractPKHOA}
                ORDER BY t.slip_no ASC";
			}else if($contractPKHOA == ''){
				$sql = "SELECT fc.`contract_pkhoa`,COALESCE(hsw.amt_claim) AS hsw_amt_claim, COALESCE(ts.amt_claim,0) AS shrink_amount,  t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                ts.`trx_shrink_claim`,

                ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1

                WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg

                WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1

                WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
                ROUND(t.`freight_price` * t.`freight_quantity`,10)  AS dpp,
                ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10)  AS ppn,
                ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10)  AS pph,
                ((ROUND(t.`freight_price` * t.`freight_quantity`,10) + ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10) - ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10))) AS amount,
                ((ROUND(t.`freight_price` * t.`freight_quantity`,10) + ROUND((t.`freight_price` * t.`freight_quantity`) * (f.ppn/100),10) - ROUND((t.`freight_price` * t.`freight_quantity`) * (f.pph/100),10)) - COALESCE(ts.amt_claim,0)) AS total_amount,
fc.file1 AS document
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
					WHERE fc.freight_id = {$freightId}
					AND sc.stockpile_id = {$stockpileId}
					AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
					AND t.company_id = {$_SESSION['companyId']}
					AND t.fc_payment_id IS NULL AND freight_price != 0
					AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo', '%d/%m/%Y')
					AND (t.posting_status = 2 OR t.posting_status IS NULL)
					and t.fc_ppayment_id = {$idPP}
					ORDER BY t.slip_no ASC";
				
			}
		
                // echo $vendorFreightIds;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        // <th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th> (ada di atas Action)
        $returnValue .= '<thead>
                            <tr>
                                <th>Action</th>
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

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
		$tempGrossUp = 0;
		$tempTotalPph = 0;
		$tempTotalPpn = 0;
		$tempTotalDpp = 0;
		$tempTaxCategori = 0;
		$tempPphTaxValue =0;
		$tempPpnTaxValue = 0;
        while ($row = $result->fetch_object()) {
            
        
            /*$dppTotalPrice = 0;
            if ($row->freight_rule == 1) {
                $fp = $row->freight_price * $row->send_weight;
                $fq = $row->send_weight;
            } else {
                $fp = $row->freight_price * $row->quantity;
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
        //    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
        //        $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
        //    }

        //    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
        //        $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
        //    }

            $returnValue .= '<tr>';
                    $totalPrice = $totalPrice + $amountPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                // $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight(' . $stockpileId . ', ' . $row->freight_id . ', ' . "'" . $vendorFreightIds . "'" . ', \'NONE\', \'NONE\', \'' . $paymentFrom . '\', \'' . $paymentTo . '\', \'' . $row->fc_ppayment_id . '\');" checked /></td>';*/
			
					$totalPrice = $totalPrice + $row->dpp;
					$totalPpn = $totalPpn + $row->ppn;
					$totalPph = $totalPph + $row->pph;
				//	$totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
					//$totalPph = $totalPph + $row->pph;
					$totalSusut = $totalSusut + ($row->shrink_amount + $row->hsw_amt_claim);
					$totalAmount = $totalAmount + ($row->total_amount-$row->hsw_amt_claim);
					$tempDpp = $totalPrice - $totalSusut;
					$tempTaxCategori = $row->pph_tax_category;
					$tempPphTaxValue = $row->pph_tax_value;
					$tempPpnTaxValue = $row->ppn_tax_value;
					//$testing = ($row->pph_tax_value);
					//add by Yeni perhitungan tanpa gross up
					if($row->pph_tax_category == 0){
						$tempTotalPpn= ($tempDpp * ($row->ppn_tax_value/100));
						$totalPpn = $tempTotalPpn;
						$tempTotalPph = ($tempDpp * ($row->pph_tax_value/100));
						$totalPph = $tempTotalPph;
					}
					
            
            $returnValue .= '<td style="width: 10%;"> <div style="text-align: center;"> 
                                <a href=" ' . $row->document . '" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" />
                                </a></div></td>';
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vehicle_no . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_quantity, 1, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->freight_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->dpp, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->qtyClaim, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->trx_shrink_claim, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($row->shrink_amount, 2, ".", ",") . '</td>';
            if($row->hsw_amt_claim != '' || $row->hsw_amt_claim != 0){
                //Additional Shrink
                $returnValue .= '<td style="width: 10%;">' . number_format($row->hsw_amt_claim, 2, ".", ",") . '</td>';
            }else{
                $returnValue .= '<td style="width: 10%;">' . '-' . '</td>';
            }

            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format(($row->total_amount - $row->hsw_amt_claim), 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }
		
		//add by yeni perhitungan dengan nilai Gross Up
		if ($tempTaxCategori == 1){
			$tempGrossUp = ((100-$tempPphTaxValue)/100);
			$tempTotalDpp = $totalPrice/$tempGrossUp;
			$totalPrice = $tempTotalDpp;
			$totalSusut = $totalSusut/$tempGrossUp;
			$tempDpp = $totalPrice - $totalSusut;
			$totalPph = ($tempDpp * ($tempPphTaxValue/100)); 
			$totalPpn = ($tempDpp * ($tempPpnTaxValue/100));
		}
	
        $returnValue .= '</tbody>';

            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Total DPP</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';


            /*$ppnDBAmount = $totalPPN;
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

//            $totalPpn = ($ppnValue/100) * $totalPrice;*/
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="'. number_format($totalPpn, 2, ".", ",") .'" onblur="checkSlipFreight('. $stockpileId .', '. $freightId .', '. "'". $contractFreights ."'".', this, document.getElementById(\'pph\'), \''. $paymentFrom .'\', \''. $paymentTo .'\');" /></td>';
            $returnValue .= '</tr>';
//            $totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $freightId . ', ' . "'" . $vendorFreightIds . "'" . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom . '\', \'' . $paymentTo . '\');" /></td>';
            $returnValue .= '</tr>';

            // $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, f.pph, p.stockpile_location, f.freight_id
            // FROM payment p LEFT JOIN freight f ON p.`freight_id` = f.`freight_id`
            // WHERE p.freight_id = {$freightId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
           //SATELMEN DOWN PAYMENT
          $sql = "SELECT idpp, freight_cost_id, (downPayment - amount_payment) AS sisaDp, STATUS, downPayment, amount_payment FROM pengajuan_dp
                    WHERE freight_cost_id = {$contractPKHOA} AND STATUS = 3  ORDER BY pengajuan_dp_id DESC LIMIT 1";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                if ($result !== false && $result->num_rows == 1) {
                    $row = $result->fetch_object();
                    if($row->sisaDp == 0 || $row->sisaDp < 0){
                        $downPayment = 0;
                    }else{
                        $downPayment = $row->sisaDp;
                    }
                }else{
                    $sql = "SELECT COALESCE(SUM(pp.amount), 0) AS down_payment, f.pph, f.ppn, pp.stockpile_id, f.freight_id, fc.freight_cost_id 
                    FROM pengajuan_payment pp 
                    LEFT JOIN freight_cost fc ON pp.`freight_cost_id` = fc.`freight_cost_id`
                    LEFT JOIN freight f ON pp.freight_id = f.freight_id
                    WHERE pp.freight_cost_id = {$contractPKHOA} AND pp.payment_method = 2 AND pp.payment_status = 3";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                    $downPayment = 0;
                    if ($result !== false && $result->num_rows == 1) {
                        $row = $result->fetch_object();
                        $dp = $row->down_payment * ((100 - $row->pph) / 100);
                        $fc_ppn_dp = $row->down_payment * ($row->ppn / 100);
                        $downPayment = $dp + $fc_ppn_dp;
                    }
                }

                //if($row->freight_id == 312 && $row->stockpile_location != 8){
                //	$downPayment = 0;
                //}
				
				$returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Total Susut</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalSusut, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="13" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            //}

            //edited by alan
           // $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;
			//edit by Yeni
			$grandTotal = ($tempDpp + $totalPpn) - $totalPph - $downPayment;


            //$totalPrice = ($totalPrice + $totalPpn) - $totalPph;

            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        //}
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

//HANDLING
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

function setSlipHandling_copy($stockpileId, $vendorHandlingId, $checkedSlips, $ppn, $pph, $paymentFromHP, $paymentToHP, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  
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
                AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
                AND t.company_id = {$_SESSION['companyId']}
                AND t.hc_payment_id IS NULL AND vhc.price != 0 AND t.handling_quantity != 0
                AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromHP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToHP', '%d/%m/%Y')
                AND (t.posting_status = 2 OR t.posting_status IS NULL) and t.hc_ppayment_id = {$idPP}
                ORDER BY t.slip_no ASC";
               // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm10">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        // <th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" /></th> (letaknya sebelum slip No)
        $returnValue .= '<thead>
                            <tr>
                                <th>Slip No.</th><th>Transaction Date</th>
                                <th>PO No.</th><th>Vendor Code</th>
                                <th>Quantity</th>
                                <th>Currency</th>
                                <th>Handling Cost / kg</th>
                                <th>Total</th><th>DPP</th>
                            </tr>
                        </thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        while ($row = $result->fetch_object()) {
            $dppTotalPrice = 0;

            $hp = $row->handling_price * $row->handling_quantity;
            $hq = $row->handling_quantity;

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
            $handlingPrice = 0;
            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                $handlingPrice = $hp;
            } else {
                $handlingPrice = $hp;
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
                    // $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\', \'' . $row->hc_ppayment_id . '\');" checked /></td>';
                // $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($hq, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 5%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->handling_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($handlingPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';
        // if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            // $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, vh.pph
            // FROM payment p LEFT JOIN vendor_handling vh ON p.`vendor_handling_id` = vh.`vendor_handling_id`
            // WHERE p.vendor_handling_id = {$vendorHandlingId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $sql = "SELECT COALESCE(SUM(pp.amount), 0) AS down_payment, vh.pph
                    FROM pengajuan_payment pp 
                    LEFT JOIN vendor_handling vh ON pp.`vendor_handling_id` = vh.`vendor_handling_id`
                    WHERE pp.vendor_handling_id = {$vendorHandlingId}
                    AND payment_method = 2 
                    AND payment_status = 1";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $dp = $row->down_payment * ((100 - $row->pph) / 100);
                $downPayment = $dp;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="8" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

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

        //    $totalPpn = ($ppnValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ', this, document.getElementById(\'pph\'), \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            $returnValue .= '</tr>';
        //$totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ', document.getElementById(\'ppn\'), this, \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            $returnValue .= '</tr>';

            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


            $totalPrice = ($totalPrice + $totalPpn) - $totalPph;

            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        // }
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
}


function setSlipHandling($stockpileId, $vendorHandlingId, $checkedSlips, $ppn, $pph, $paymentFromHP, $paymentToHP, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $whereProperty = '';
    $boolcontinue = false;

    if($idPP == ''){
        $whereProperty = " AND (t.hc_ppayment_id IS NULL AND t.hc_payment_id IS NULL)";
    }else if($idPP != ''){
        $whereProperty = " AND (t.hc_ppayment_id = {$idPP} AND t.hc_payment_id IS NULL)";
    
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
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId})
            AND t.company_id = {$_SESSION['companyId']}
			AND vhc.price != 0 AND t.handling_quantity != 0
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromHP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToHP', '%d/%m/%Y')
			AND (t.posting_status = 2 OR t.posting_status IS NULL)
            {$whereProperty}
			ORDER BY t.slip_no ASC";
          //  echo $idPP;
           // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm10">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th><INPUT type="checkbox" onchange="checkAll(this)" name="checkedSlips[]" />
                                    </th><th>Slip No.</th>
                                    <th>Transaction Date</th>
                                    <th>PO No.</th>
                                    <th>Vendor Code</th>
                                    <th>Quantity</th>
                                    <th>Currency</th>
                                    <th>Handling Cost / kg</th>
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

            $hp = $row->handling_price * $row->handling_quantity;
            $hq = $row->handling_quantity;
            $dpp = $hp;

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
            $handlingPrice = 0;
            if ($row->transaction_date >= '2015-10-05' && $row->stockpile_id == 1) {
                $handlingPrice = $hp;
            } else {
                $handlingPrice = $hp;
            }

        //    if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
        //        $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
        //    }

        //    if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
        //        $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
        //    }

            $returnValue .= '<tr>';
            if($idPP == ''){
                if ($checkedSlips != '') {
                    $pos = strpos($checkedSlips, $row->transaction_id);
                        if ($pos === false) {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
                        } else {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" checked /></td>';
                            $totalPrice = $totalPrice + $dppTotalPrice;

                            if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                                $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                            }

                            if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                                $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                            }

                            $quanty = ($quanty + $row->quantity);
                            $unitprice = $row->handling_price;
                        // $dpp = $dpp + ceil($row->handling_price * $row->quantity);

                        }
                    } else {
                            $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
                        }
            }else{
                $totalPrice = $totalPrice + $dppTotalPrice;

                if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                     $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                }

                if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                    $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                }

                $quanty = ($quanty + $row->quantity);
                $unitprice = $row->handling_price;
               // $dpp = $dpp + ceil($row->handling_price * $row->quantity);
               $returnValue .= '<td><input type="hidden" name="checkedSlips[]" id="hc" value="' . $row->transaction_id . '" onclick="checkSlipHandling(' . $stockpileId . ', ' . $row->vendor_handling_id . ', \'NONE\', \'NONE\', \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            }
            $returnValue .= '<td style="width: 10%;">' . $row->slip_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->transaction_date . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->po_no . '</td>';
            $returnValue .= '<td style="width: 10%;">' . $row->vendor_code . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($hq, 0, ".", ",") . '</td>';
            $returnValue .= '<td style="width: 5%;">IDR</td>';
            $returnValue .= '<td style="text-align: right; width: 10%;">' . number_format($row->handling_price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($handlingPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        
        if ($checkedSlips != '' && $idPP == '') {
            $boolcontinue = true;
        }else if($checkedSlips == '' && $idPP != ''){
            $boolcontinue = true;
        }

        $returnValue .= '</tbody>';
        if ($boolcontinue) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            // $sql = "SELECT COALESCE(SUM(p.amount_converted), 0) AS down_payment, vh.pph
            // FROM payment p LEFT JOIN vendor_handling vh ON p.`vendor_handling_id` = vh.`vendor_handling_id`
            // WHERE p.vendor_handling_id = {$vendorHandlingId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $sql = "SELECT COALESCE(SUM(pp.amount), 0) AS down_payment, vh.pph
                    FROM pengajuan_payment pp 
                    LEFT JOIN vendor_handling vh ON pp.`vendor_handling_id` = vh.`vendor_handling_id`
                    WHERE pp.vendor_handling_id = {$vendorHandlingId}
                    AND payment_method = 2 
                    AND payment_status = 1";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $dp = $row->down_payment * ((100 - $row->pph) / 100);
                $downPayment = $dp;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="9" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

        //    $ppnDB = 0;
        //    $pphDB = 0;
        //    $sql = "SELECT freight_id, ppn, pph FROM freight WHERE freight_id = {$freightId}";
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

        //    $totalPpn = ($ppnValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ', this, document.getElementById(\'pph\'), \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            $returnValue .= '</tr>';
        //$totalPph = ($pphValue/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">PPh</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight(' . $stockpileId . ', ' . $vendorHandlingId . ', document.getElementById(\'ppn\'), this, \'' . $paymentFromHP . '\', \'' . $paymentToHP . '\');" /></td>';
            $returnValue .= '</tr>';

            //edited by alan
            $grandTotal = ($totalPrice + $totalPpn) - $totalPph - $downPayment;
            //$grandTotal = ($totalPrice + $totalPpn) - $totalPph;


            $totalPrice = ($totalPrice + $totalPpn) - $totalPph;

            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="9" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        }
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';

        $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $quanty . '" />';
        $returnValue .= '<input type="hidden" id="price" name="price" value="' . $unitprice . '" />'; 
        $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dpp . '" />'; 
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
                ON uc.unloading_cost_id = t.unloading_cost_id AND uc.stockpile_id = {$stockpileId} AND uc.company_id = {$_SESSION['companyId']}
			WHERE l.active = 1
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

function setSlipUnloading($stockpileId, $laborId, $checkedSlips, $ppn, $pph, $paymentFromUP, $paymentToUP, $idPP)
{
    global $myDatabase;
    $returnValue = '';
    $return_val = '';
    $boolcontinue = false;
    $whereProperty = '';

    if($idPP == ''){
        $whereProperty = " AND (t.uc_ppayment_id IS NULL AND t.uc_payment_id IS NULL)";
    }else if($idPP != ''){
        $whereProperty = " AND (t.uc_ppayment_id = {$idPP} AND t.uc_payment_id IS NULL)";
    
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
			AND t.unloading_cost_id IS NOT NULL
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromUP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToUP', '%d/%m/%Y')
			AND (t.posting_status = 2 OR (t.posting_status IS NULL OR t.posting_status = 0))
            {$whereProperty}
			ORDER BY t.transaction_date ASC";
            //echo $sql;
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
                $dpp = $dppTotalPrice;
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = $row->unloading_price / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->unloading_price;
                    $dpp = $dppTotalPrice;
                }
            }

            // if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
            //     $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
            // }

            // if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
            //     $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
            // }

            $returnValue .= '<tr>';
            if($idPP == ''){
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

                        $quanty = ($quanty + $row->quantity);
                        $unitprice = $row->unloading_price;
                      //  $dpp = $dpp + ceil($row->unloading_price * $row->quantity);
                    }
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
                }
            }else{
                $totalPrice = $totalPrice + $dppTotalPrice;

                if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                    $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                }

                if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                    $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                }

                $quanty = ($quanty + $row->quantity);
                $unitprice = $row->unloading_price;
               // $dpp = $dpp + ceil($row->unloading_price * $row->quantity);
                $returnValue .= '<td><input type="hidden" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';

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


        if ($checkedSlips != '' && $idPP == '') {
            $boolcontinue = true;
        }else if($checkedSlips == '' && $idPP != ''){
            $boolcontinue = true;
        }

        $returnValue .= '</tbody>';
        if ($boolcontinue) {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

        // $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment 
        //           FROM payment WHERE labor_id = {$laborId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
        $sql = "SELECT COALESCE(SUM(amount), 0) AS down_payment 
                FROM pengajuan_payment 
                WHERE labor_id = {$laborId} AND payment_method = 2 
                AND payment_status = 1";
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

        //    $ppnDB = 0;
        //    $pphDB = 0;
        //    $sql = "SELECT labor_id, ppn, pph FROM labor WHERE labor_id = {$laborId}";
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

        //    $totalPpn = ($ppn/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="7" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', this, document.getElementById(\'ppn\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            $returnValue .= '</tr>';
        //    $totalPph = ($pph/100) * $totalPrice;
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

        $returnValue .= '<input type="hidden" id="qty" name="qty" value="' . $quanty . '" />';
        $returnValue .= '<input type="hidden" id="price" name="price" value="' . $unitprice . '" />'; 
        $returnValue .= '<input type="hidden" id="dpp" name="dpp" value="' . $dpp . '" />'; 
        $return_val .= '<input type="hidden" value="|' . number_format($grandTotal, 2, ".", ",") . '|" />';  
        $returnValue .= '</div>';
    }

    echo $returnValue;
    echo $return_val;
}

function setSlipPengajuanUnloading($stockpileId, $laborId, $checkedSlips, $ppn, $pph, $paymentFromUP, $paymentToUP, $idPP)
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
                AND t.uc_payment_id IS NULL AND t.unloading_cost_id IS NOT NULL
                AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFromUP', '%d/%m/%Y') AND STR_TO_DATE('$paymentToUP', '%d/%m/%Y')
                AND (t.posting_status = 2 OR t.posting_status IS NULL)
                and t.uc_ppayment_id = {$idPP}
                ORDER BY t.transaction_date ASC";
  
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        //<th><INPUT type="checkbox" onchange="checkAllUC(this)" name="checkedSlips[]" /></th>
        $returnValue .= '<thead>
                            <tr>
                                <th>Slip No.</th><th>Transaaction Date</th><th>Quantity</th>
                                <th>Currency</th>
                                <th>Unloading Cost</th>
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
            if ($row->pph_tax_id == 0 || $row->pph_tax_id == '') {
                $dppTotalPrice = $row->unloading_price;
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = $row->unloading_price / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->unloading_price;
                }
            }

            // if($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
            //     $totalPPN = $totalPPN + ($dppTotalPrice * ($row->ppn_tax_value / 100));
            // }

            // if($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
            //     $totalPPh = $totalPPh + ($dppTotalPrice * ($row->pph_tax_value / 100));
            // }

            $returnValue .= '<tr>';
   
                    $totalPrice = $totalPrice + $dppTotalPrice;

                    if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                        $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                    }

                    if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                        $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                    }
                
                // $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\', \'' . $row->uc_ppayment_id . '\');" checked /></td>';

                // $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" value="' . $row->transaction_id . '" onclick="checkSlipUnloading(' . $stockpileId . ', ' . $row->labor_id . ', \'NONE\', \'NONE\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            
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
        // if ($checkedSlips != '') {
            $returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($totalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $sql = "SELECT COALESCE(SUM(amount_converted), 0) AS down_payment FROM payment WHERE labor_id = {$laborId} AND payment_method = 2 AND payment_status = 0 AND payment_date > '2016-07-31'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $downPayment = 0;
            if ($result !== false && $result->num_rows == 1) {
                $row = $result->fetch_object();
                $downPayment = $row->down_payment;

                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="6" style="text-align: right;">Down Payment</td>';
                $returnValue .= '<td style="text-align: right;">' . number_format($downPayment, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

        //    $ppnDB = 0;
        //    $pphDB = 0;
        //    $sql = "SELECT labor_id, ppn, pph FROM labor WHERE labor_id = {$laborId}";
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

        //    $totalPpn = ($ppn/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPN</td>';
            $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipUnloading(' . $stockpileId . ', ' . $laborId . ', this, document.getElementById(\'ppn\', \'' . $paymentFromUP . '\', \'' . $paymentToUP . '\');" /></td>';
            $returnValue .= '</tr>';
        //    $totalPph = ($pph/100) * $totalPrice;
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="6" style="text-align: right;">PPh</td>';
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
            $returnValue .= '<td colspan="6" style="text-align: right;">Grand Total</td>';
            $returnValue .= '<td style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';

            $returnValue .= '</tfoot>';
        // }
        $returnValue .= '</table>';
        $returnValue .= '<input type="hidden" id="totalPrice" name="totalPrice" value="' . round($totalPrice, 2) . '" />';
        $returnValue .= '<input type="hidden" id="downPayment" name="downPayment" value="' . $downPayment . '" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
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
                WHERE con.contract_type = 'C'
               -- AND con.vendor_id = {$vendorId}
                AND sc.stockpile_id = {$stockpileId}
                AND t.company_id = {$_SESSION['companyId']}
                AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo1', '%d/%m/%Y')
                AND (t.posting_status = 2 OR t.posting_status IS NULL)
                AND t.ppayment_id = {$idPP}";
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
                $dppTotalPrice = round(($row->unit_price * $row->quantity),2);
            } else {
                if ($row->pph_tax_category == 1) {
                    $dppTotalPrice = round(($row->unit_price * $row->quantity),2) / round((100 - $row->pph_tax_value) / 100, 2);
                } else {
                    $dppTotalPrice = round(($row->unit_price * $row->quantity),2);
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
    $checkInvoiceNo = 'PGJ/JPJ/';
    $sql = "SELECT tt.code_type_transaction, pp.invoice_no FROM pengajuan_payment pp
    LEFT JOIN type_transaction tt ON tt.type_transaction_id = pp.payment_for
    WHERE idPP = {$idPP}";
    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultInvoice->num_rows == 1) {
        $rowInvoice = $resultInvoice->fetch_object();
        $splitInvoiceNo = explode('/', $rowInvoice->invoice_no);
        $code = $rowInvoice->code_type_transaction;
        $lastExplode = count($splitInvoiceNo) - 1;
        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
        $pengajuanNo = $checkInvoiceNo . '/' . $code . '/' .$currentYearMonth . '/'. $nextInvoiceNo ;
    } 
    $returnValue = '|' . $pengajuanNo;
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

