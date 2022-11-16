<?php
// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_INCLUDE . DS . 'Bcrypt.php';

global $myDatabase;

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$currentYearMonth = $date->format('ym');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('y');

if (isset($_POST['action']) && $_POST['action'] == 'get_pengajuan_general_detail') {
    // <editor-fold defaultstate="collapsed" desc="get_pengajuan_general_detail">
    $returnValue = '';
	if ($_POST['invoiceMethod'] == 2){
    $sql = "SELECT a.*,b.type, b.account_id, b.adv_detail_id,
    CASE WHEN b.type = 4 THEN 'Loading'
    WHEN b.type = 5 THEN 'Umum'
    WHEN b.type = 6 THEN 'HO' ELSE '' END AS `type2`, '' AS shipment_no, g.jenis_benefit AS items, b.keterangan AS notes,
    c.general_vendor_name, d.account_name, e.stockpile_name, b.unit_price, b.qty,b.amount AS total_amount, 0 AS advance , f.uom_type, b.uom
    FROM perdin_adv_settle a
    LEFT JOIN perdin_adv_detail b  ON a.sa_id = b.sa_id
    LEFT JOIN general_vendor c ON c.general_vendor_id = a. id_user
    LEFT JOIN account d ON d.account_id = b.account_id
    LEFT JOIN stockpile e ON e.stockpile_id = a.stockpile_id
    LEFT JOIN uom f ON f.idUOM = b.uom
    LEFT JOIN perdin_benefit g ON g.benefit_id = b.benefit_id
WHERE a.`sa_id` = {$_POST['pgId']} ORDER BY a.`sa_id` ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	}else{
	$sql = "SELECT a.*,b.settle_id, b.settlementType, b.settle_id, b.sa_id AS sa_id2, b.user_to,
CASE WHEN b.settlementType = 4 THEN 'Loading'
WHEN b.settlementType = 5 THEN 'Umum'
WHEN b.settlementType = 6 THEN 'HO' ELSE '' END AS `type2`, f.shipment_no, b.items, b.notes,
c.general_vendor_name, d.account_name, e.stockpile_name, b.qty, b.price AS unit_price, b.amount AS total_amount, 
(SELECT SUM(amount) FROM perdin_dp WHERE settle_id = b.settle_id) AS advance, h.uom_type, b.uom
FROM perdin_adv_settle a
LEFT JOIN perdin_settle_detail b  ON a.sa_id = b.sa_id
LEFT JOIN general_vendor c ON c.general_vendor_id = a. id_user
LEFT JOIN account d ON d.account_id = b.accountId
LEFT JOIN stockpile e ON e.stockpile_id = a.stockpile_id
LEFT JOIN shipment f ON f.shipment_id = b.shipment_id
LEFT JOIN uom h ON h.idUOM = b.uom
			WHERE a.`sa_id` = {$_POST['pgId']} ORDER BY a.`sa_id` ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);	
	}

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "invoiceDetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Type</th><th>Account Name</th><th>Shipment Code</th><th>Remark (SP)</th><th>Items</th><th>Notes</th><th>Qty</th><th>Unit Price</th><th>Total Amount</th><th>Action</th>';
        
        $returnValue .= '</tr></thead>';
        $returnValue .= '<tbody>';
		
		$total = 0;
		$advance = 0;
        $totalPrice = 0;
        $count = 0;
        while ($row = $result->fetch_object()) {
            $returnValue .= '<tr>';

            $termin = 100;
            $ppn = 0;
            $pph = 0;
            //$amount = $row->amount * $termin / 100;
            $tamount = $row->total_amount;
            $totalPrice += $tamount;
			$advance += $row->advance;
            //$returnValue .= '<td style="text-align: right; width: 8%;">';
            //$returnValue .= '<a href="#" id="update|pg|' . $row->pgd_id . '" role="button" title="Edit" onclick="editDetail(' . $row->pgd_id . ');"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>';
//            $returnValue .= '<a href="#" id="delete|pg|' . $row->pgd_id . '" role="button" title="Delete" onclick="deleteInvoiceDetail(' . $row->pgd_id . ');"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>';
            //$returnValue .= '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->type2 . '</td>';
            
            $returnValue .= '<td style="width: 8%;">' . $row->account_name . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->shipment_no . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->stockpile_name . '</td>';
			$returnValue .= '<td style="width: 8%;">' . $row->items . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->qty, 2, ".", ",") . '  '.$row->uom_type .'</td>';
            //$returnValue .= '<td style="text-align: right; width: 8%;">' . $row->currency_code . '(' . number_format($row->exchange_rate, 0, ".", ",") . ')' . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->unit_price, 2, ".", ",") . '</td>';
            //$returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->termin, 0, ".", ",") . '%</td>';
            //$returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($amount, 2, ".", ",") . '</td>';
           // $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($ppn, 2, ".", ",") . '</td>';
           // $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($pph, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($tamount, 2, ".", ",") . '</td>';
			if ($_POST['invoiceMethod'] == 2){
			//$returnValue .= '<td style="text-align: center; width: 8%;"></td>';
            $returnValue .= '<td style="text-align: center; width: 8%;">
			<a href="#" id="edit|adv|'. $row->adv_detail_id .'" role="button" title="Edit" onclick="editAdvDetail('. $row->adv_detail_id .');"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
			</td>';
			}else{
			$returnValue .= '<td style="text-align: center; width: 8%;">
			<a href="#" id="edit|settle|'. $row->settle_id .'" role="button" title="Edit" onclick="editSettleDetail('. $row->settle_id .','. $row->sa_id2 .','. $row->user_to .','. $row->origin .');"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
			</td>';
			}
            $returnValue .= '</tr>';
            $count = $count+1;
        }

        $returnValue .= '</tbody>';
		
		if ($_POST['invoiceMethod'] == 1){
			
			$total = $totalPrice;
			$totalAdvance = $advance;
			$grandTotal = $totalPrice - $advance;
		}else{
			$grandTotal = $totalPrice;
		}

        $returnValue .= '<tfoot>';
		
		if ($_POST['invoiceMethod'] == 1){
		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="8" style="text-align: right;">Total</td>';
        $returnValue .= '<td  style="text-align: right;">' . number_format($total, 2, ".", ",") . '</td>';
		$returnValue .= '<td ></td>';
        $returnValue .= '</tr>';

		$returnValue .= '<tr>';
        $returnValue .= '<td colspan="8" style="text-align: right;">Advance</td>';
        $returnValue .= '<td  style="text-align: right;">' . number_format($advance, 2, ".", ",") . '</td>';
		$returnValue .= '<td ></td>';
        $returnValue .= '</tr>';
			
		}
		
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="8" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td  style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '<td ></td>';
      
        $returnValue .= '</tr>';
        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        // $returnValue .= '<input type="hidden" id="pph2" name="pph2" value="'. round($total_pph, 2) .'" />';
        // $returnValue .= '<input type="hidden" id="ppn2" name="ppn2" value="'. round($total_ppn, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
    }

    echo $returnValue;
    // </editor-fold>
}