<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$boolBack = true;
$boolInsert = false;


if(isset($_POST['direct']) && $_POST['direct'] == 1) {
    $boolBack = false;
    $boolInsert = true;
}

$invNotim = $_POST['InvNotim'];

$sql = "SELECT CASE WHEN pp.payment_method = 2 THEN 'Down Payment' ELSE NULL END AS paymentMethod, 
        ty.type_transaction_name AS paymentFor, sp.stockpile_name, f.freight_supplier, lab.labor_name, vh.vendor_handling_name, 
        v.vendor_name AS vendor,  invn.file1,
        CONCAT(vb.bank_name,' - ',vb.account_no) AS vbank, 
        CONCAT(lbank.bank_name,' - ',lbank.account_no) AS lbank, CONCAT(fb.bank_name,' - ',fb.account_no) AS fbank, 
        CONCAT(vhb.bank_name,' - ',vhb.account_no) AS vhbank, DATE_FORMAT(pp.urgent_payment_date, '%d/%m/%Y') AS tglReguest, 
        DATE_FORMAT(pp.periodeFrom, '%d/%m/%Y') AS dateFrom, DATE_FORMAT(pp.periodeTo, '%d/%m/%Y') AS dateTo, 
        DATE_FORMAT(invn.entry_date, '%d/%m/%Y') AS invoiceDate1, invn.payment_id as payid, pp.* 
        FROM invoice_notim invn 
        INNER JOIN pengajuan_payment pp ON pp.idPP = invn.idpp
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
        WHERE invn.inv_notim_id = {$invNotim}";
       // echo $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;

if($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    $paymentID = $row->payid;
?>


<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $('#printSettle').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#settleContainer").printThis();
            //$("#transactionContainer").hide();
        });
    });

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/search-payment.php', {}, iAmACallbackFunction);
    }
	
	$(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>

<div id="settleContainer">
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td colspan="6" style="text-align: left; font-size: 12pt; font-weight: 600;">
                PT. JATIM PROPERTINDO JAYA
            </td>
        </tr>

        <tr><td colspan="6" style="text-align: center; font-size: 12pt; font-weight: 600;">SETTLEMENT DATA</td></tr>
    </table>
    <br/>
    <?php
//    if($row->payment_type == 2) {
    ?>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="20%"><b>Supplier</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->freight_supplier; ?></td>

            <td width="20%"><b>Tanggal Invoice</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->invoice_date; ?></td>
        </tr>

        <tr>
            <td width="20%"><b>No. Invoice/Kwitansi</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->invoice_no; ?></td>
            <td width="20%"><b>Tax Invoice</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->tax_invoice; ?></td>
        </tr>
        <tr>

            <td width="20%"><b>Cheque No</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->cheque_no; ?></td>

            <td width="20%"><b>Stockpile Location</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->stockpile_name; ?></td>

        </tr>
        <tr>
            <td width="20%"><b>Type</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->pMethod .' - '. $row->payment_type2 .' - '. $row->account_type2; ?></td>
        </tr>

        <?php
        if($row->payment_status == 1) {
            echo '<tr><td colspan="6" style="font-size: 14pt; font_weight: bold; color: red; text-align: center;">Returned</td></tr>';
        }
        ?>
    </table>
    <br/>
	<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <tr>
    <td width="10%"><b>Remarks</b></td>
    <td><?php if($row->stockpile_contract_id != '' || $row->vendor_id != '' || $row->freight_id != '' || $row->vendor_handling_id != '' || $row->labor_id != '' || $row->sales_id != '' || $row->invoice_id != ''){
					echo $row->remarks;
				}else{
					echo $row->remarks;
				}
				?>
     </td>
     </tr>
     </table>
    <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>

                <th>Slip No</th>
                <th>Transaction Date</th>
                <th>PO No</th>
                <th>Vendor Code</th>
                <th>Vehicle No</th>
				<th>Quantity</th>
                <th>Freight Cost/Kg</th>
                <th>Amount</th>
                <th>Shrink Qty Claim</th>
                <th>Shrink Price Claim</th>
                <th>Shrink Amount</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $downPayment = 0;
                $sql1 = "SELECT poa.amount AS settleAmount, round(poa.qty,2) AS quantity1, t.*, sc.stockpile_id, fc.freight_id, con.po_no, f.freight_rule,
                            f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, v.vendor_code,
                            ts.`trx_shrink_claim`, 
                            ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
                            WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
                            WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                            WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                            ELSE 0 END,10) AS qtyClaim,
                            CASE WHEN t.fc_payment_status = 2 THEN (SELECT MAX(amount) FROM payment_oa WHERE transaction_id = t.transaction_id) ELSE 0 END AS split_oa
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
                        INNER JOIN payment_oa poa 
                            ON poa.transaction_id = t.transaction_id
                        WHERE poa.inv_notim_dp = {$invNotim}
                        ORDER BY t.slip_no ASC";

                      //  echo $sql1;
                        
            $resultDetail = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);

            $total = 0;
            while($rowDetail = $resultDetail->fetch_object()) {
                $dppTotalPrice = 0;
                if($row->freight_rule == 1){
                    $fp =  $rowDetail->settleAmount;
                    $fq = $rowDetail->send_weight;
                }else{
                    $fp =$rowDetail->settleAmount;
                    $fq = $rowDetail->quantity1;
                }

                if($rowDetail->transaction_date >= '2015-10-05'&& $rowDetail->stockpile_id == 1 && ($rowDetail->pph_tax_id == 0 || $rowDetail->pph_tax_id == '')) {
                    $dppTotalPrice = $fp;
                    $dppShrinkPrice = $rowDetail->qtyClaim * $rowDetail->trx_shrink_claim;
                }else{ 
                    if($rowDetail->pph_tax_id == 0 || $rowDetail->pph_tax_id == '') {
                        $dppTotalPrice = $fp;
                        $dppShrinkPrice = $rowDetail->qtyClaim * $rowDetail->trx_shrink_claim;
                    }else{
                        if ($rowDetail->pph_tax_category == 1 && $rowDetail->transaction_date >= '2015-10-05'  && $row->stockpile_id == 1){
                            $dppTotalPrice = ($fp) / ((100 - $rowDetail->pph_tax_value) / 100);
                            $dppShrinkPrice = ($rowDetail->qtyClaim * $rowDetail->trx_shrink_claim) / ((100 - $rowDetail->pph_tax_value) / 100);
                        } else {
                            if($rowDetail->pph_tax_category == 1) {
                                $dppTotalPrice = ($fp) / ((100 - $rowDetail->pph_tax_value) / 100);
                                $dppShrinkPrice = ($rowDetail->qtyClaim * $rowDetail->trx_shrink_claim) / ((100 - $rowDetail->pph_tax_value) / 100);
                            }else {
                                $dppTotalPrice = $fp;
                                $dppShrinkPrice = $rowDetail->qtyClaim * $rowDetail->trx_shrink_claim;
                            }
                        }
                    }
                }

                $freightPrice = 0;
                if($rowDetail->transaction_date >= '2015-10-05' && $rowDetail->stockpile_id == 1) {
                    $freightPrice = $fp;
                }else{
                    $freightPrice = $fp;
                }
                    
                $amountPrice = $dppTotalPrice - $dppShrinkPrice;

                $totalPrice = $totalPrice + $amountPrice;	
                $totalPrice2 = $totalPrice2 + $amountPrice;
                if($rowDetail->ppn_tax_id != 0 && $rowDetail->ppn_tax_id != '') {
                    $totalPPN = ($totalPrice * ($rowDetail->ppn_tax_value / 100));
                }
                if($rowDetail->pph_tax_id != 0 && $rowDetail->pph_tax_id != '') {
                    $totalPPh = ($totalPrice * ($rowDetail->pph_tax_value / 100));
                    echo 'TEST2';
                }
    
                // by YENI
                if($rowDetail->freight_rule == 1){
                    $dpp = $dpp + (($rowDetail->freight_price * $rowDetail->send_weight) - $rowDetail->settleAmount);
                    $qty = $qty + $rowDetail->send_weight;
                }else{
                    $dpp = $dpp + (($rowDetail->freight_price * $rowDetail->quantity)- $rowDetail->settleAmount);
                    $qty = $qty + $rowDetail->quantity;
                }
                $price = $rowDetail->freight_price;
            ?>
            <tr>
                <td><?php echo $rowDetail->slip_no;?></td>
                <td><?php echo $rowDetail->transaction_date; ?></td>
                <td><?php echo $rowDetail->po_no; ?></td>
                <td><?php echo $rowDetail->vendor_code; ?></td>
                <td><?php echo $rowDetail->vehicle_no; ?></td>
                <td><?php echo $fq ?></td>
				<td><?php echo number_format($rowDetail->freight_price, 0, ".", ",")?></td>
                <td><?php echo number_format($freightPrice, 2, ".", ",");?></td>
                <td><?php echo number_format($rowDetail->qtyClaim, 2, ".", ",");?></td>
                <td><?php echo number_format($row->trx_shrink_claim, 2, ".", ",");?></td>
                <td><?php echo number_format($dppShrinkPrice, 2, ".", ",");?></td>
                <td><?php echo number_format($amountPrice, 2, ".", ",");?></td>
            </tr>
            <?php
           }
            ?>
        <!-- END WHILE -->
        </tbody>

			<tr>
                <td colspan="11" style="text-align: right;">Total</td>
                <td><div style="text-align: right;"><?php echo number_format($totalPrice, 2, ".", ","); ?></div></td>
            </tr>
      
            <tr>
                <td colspan="11" style="text-align: right;">Total PPN</td>
                <td><div style="text-align: right;"><?php echo number_format($totalPPN, 2, ".", ","); ?></div></td>
            </tr>
            <tr>
                <td colspan="11" style="text-align: right;">Total PPh</td>
                <td><div style="text-align: right;"><?php echo number_format($totalPPh, 2, ".", ","); ?></div></td>
            </tr>
            <?php
                $sqlx = "SELECT sum(settle_amount) as DP FROM pengajuan_payment_dp where payment_id = {$paymentID} and status = 1";
                //echo $sqlx;
                $result = $myDatabase->query($sqlx, MYSQLI_STORE_RESULT);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_object();
                    $downPayment = $row->DP;
                }
                $total = ($totalPrice + $totalPPN - $totalPPh) - $downPayment;
                $total = 0;
            ?>
            <tr>
                <td colspan="11" style="text-align: right;">Down Payment</td>
                <td><div style="text-align: right;"><?php echo number_format($downPayment, 2, ".", ","); ?></div></td>
            </tr>

            <tr>
                <td colspan="11" style="text-align: right;">Total</td>
                <td><div style="text-align: right;"><?php echo number_format($total, 2, ".", ","); ?></div></td>
            </tr>

    </table>
    <?php
    } 
    ?>
</div>

<?php
if($boolBack) {
?>
<button class="btn" type="button" onclick="back()">Back</button>
<?php
}
?>

