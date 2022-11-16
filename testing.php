function setSlipFreight_1($stockpileId_1, $freightId_1, $checkedSlips, $ppn, $pph, $paymentFrom_1, $paymentTo_1)
{
    global $myDatabase;
    $returnValue = '';
    $whereProperty = '';
    $whereProperty1 = '';
    $return_val = '';

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
            WHERE fc.freight_id = {$freightId_1}
			AND sc.stockpile_id = {$stockpileId_1}
		--	{$whereProperty}
			AND t.stockpile_contract_id IN (SELECT stockpile_contract_id FROM stockpile_contract WHERE stockpile_id = {$stockpileId_1})
            AND t.company_id = {$_SESSION['companyId']}
			AND t.fc_payment_id IS NULL AND freight_price != 0
			AND t.transaction_date BETWEEN STR_TO_DATE('$paymentFrom_1', '%d/%m/%Y') AND STR_TO_DATE('$paymentTo_1', '%d/%m/%Y')
			AND (t.posting_status = 2 OR t.posting_status IS NULL)
            AND (t.fc_ppayment_id IS NULL AND t.payment_id IS NULL)
          --  AND fc.`freight_cost_id` = {$contractPKHOA}
			ORDER BY t.slip_no ASC";
        // echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "frm1">';
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
                                <th>Total Amount</th>
                            </tr>
                        </thead>';
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
                    $fp = $row->freight_price * $row->quantity; // fc.200 * t.7978.1
                    $fq = $row->freight_quantity; //7978,10
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
            
                $returnValue .= '<tr>';
                if ($checkedSlips != '') {
                    $pos = strpos($checkedSlips, $row->transaction_id);

                    if ($pos === false) {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
                    } else {
                        $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" checked /></td>';
                        $totalPrice = $totalPrice + $amountPrice;

                        if ($row->ppn_tax_id != 0 && $row->ppn_tax_id != '') {
                            $totalPPN = ($totalPrice * ($row->ppn_tax_value / 100));
                        }

                        if ($row->pph_tax_id != 0 && $row->pph_tax_id != '') {
                            $totalPPh = ($totalPrice * ($row->pph_tax_value / 100));
                        }

                        $quanty = ($quanty + $row->freight_quantity);
                        $unitprice = $row->freight_price;
                        $dpp = $dpp + ceil($row->freight_price * $row->freight_quantity);
                    }
                } else {
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="fc" value="' . $row->transaction_id . '" onclick="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $row->freight_id . ', \'NONE\', \'NONE\', \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
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
                $returnValue .= '<td style="text-align: right; width: 20%;">' . number_format($amountPrice, 2, ".", ",") . '</td>';
                $returnValue .= '</tr>';
            }

            $returnValue .= '</tbody>';
            if ($checkedSlips != '') {
                $returnValue .= '<tfoot>';
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="13" style="text-align: right;">Total</td>';
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
                $returnValue .= '<td colspan="13" style="text-align: right;">PPN</td>';
                $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="ppn" id="ppn" value="' . number_format($totalPpn, 2, ".", ",") . '" onblur="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $freightId_1 . ', this, document.getElementById(\'pph\'), \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
                $returnValue .= '</tr>';
    // $totalPph = ($pphValue/100) * $totalPrice;
                $returnValue .= '<tr>';
                $returnValue .= '<td colspan="13" style="text-align: right;">PPh</td>';
                $returnValue .= '<td><input type="text" style="text-align: right;" class="span12" name="pph" id="pph" value="' . number_format($totalPph, 2, ".", ",") . '" onblur="checkSlipFreight_1(' . $stockpileId_1 . ', ' . $freightId_1 . ', document.getElementById(\'ppn\'), this, \'' . $paymentFrom_1 . '\', \'' . $paymentTo_1 . '\');" /></td>';
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
                $returnValue .= '<td colspan="13" style="text-align: right;">Down Payment</td>';
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
                $returnValue .= '<td colspan="13" style="text-align: right;">Grand Total</td>';
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