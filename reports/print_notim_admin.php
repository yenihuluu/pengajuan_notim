<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';
	$namaFile = "notim.xls";

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'DOWLNOAD NOTA TIMBANG REPORT',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

// Function penanda awal file (Begin Of File) Excel

function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}

// Function penanda akhir file (End Of File) Excel

function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}

// Function untuk menulis data (angka) ke cell excel

function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}

// Function untuk menulis data (text) ke cell excel

function xlsWriteLabel($Row, $Col, $Value ) {
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}

// header file excel

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");

// header untuk nama file
header("Content-Disposition: attachment;filename=".$namaFile."");

header("Content-Transfer-Encoding: binary ");

// memanggil function penanda awal file excel
xlsBOF();

// ------ membuat kolom pada excel --- //
$y=0;
$x=0;
// mengisi pada cell A1 (baris ke-0, kolom ke-0)

// -------- menampilkan data --------- //

$whereProperty = '';
$whereProperty2='';
$sumProperty = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
//$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
$stockpileIds = $_POST['stockpileIds'];
$vendorIds = $_POST['vendorIds'];
$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$stockpileName = 'All ';
$periodFull = '';

// <editor-fold defaultstate="collapsed" desc="Query">

if ($stockpileIds !== '') {
   // $stockpileId = $_POST['stockpileId'];
    $stockpile_name = array();
	$stockpile_code = array();
	$stockpileNames = '';
	$stockpileCodes = '';
    $sql = "SELECT stockpile_code, stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
		$stockpile_code[] = $row['stockpile_code'];

	/*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                        if($stockpile_names == '') {
                            $stockpile_names .= "'". $stockpile_name[$i] ."'";
                        } else {
                            $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                        }
                    }*/

	$stockpileNames =  "'" . implode("','", $stockpile_name) . "'";
	$stockpileCodes =  "'" . implode("','", $stockpile_code) . "'";

	}
}

    $whereProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpileCodes}) ";
    $sumProperty .= " AND SUBSTRING(t.slip_no,1,3) IN ({$stockpileCodes}) ";

//    $whereProperty .= " AND t.slip_no like '{$stockpileId}%' ";
//    $sumProperty .= " AND t.slip_no like '{$stockpileId}%' ";

//    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
//    $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    $rowStockpile = $resultStockpile->fetch_object();
    //$stockpileName = $row->stockpile_name . " ";
}

if($vendorIds !== '') {

    $whereProperty2 .= $_POST['vendorIds'];

}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $sumProperty .= " AND IF(t.transaction_type = 1, t.unloading_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'), t.transaction_date < STR_TO_DATE('{$periodFrom}', '%d/%m/%Y')) ";
    $boolBalanceBefore = true;
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
}

$sql = "SELECT t.*,
            DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
			DATE_FORMAT(t.modify_date, '%d %b %Y') AS modify_date,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,
            CASE WHEN t.transaction_type = 1 THEN con.po_no ELSE sh.shipment_code END AS po_no,
            CASE WHEN t.transaction_type = 1 THEN con.contract_no ELSE sl.sales_no END AS contract_no,
            CASE WHEN t.transaction_type = 1 THEN vh.vehicle_name ELSE t.vehicle_no END AS vehicle_name,
            CASE WHEN t.transaction_type = 1 THEN t.vehicle_no ELSE '' END AS vehicle_no,
            CASE WHEN t.transaction_type = 1 THEN DATE_FORMAT(t.unloading_date, '%d %b %Y') ELSE DATE_FORMAT(t.transaction_date, '%d %b %Y') END AS unloading_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code, f.freight_id, f.freight_rule,
            v1.vendor_name, hv.vendor_handling_id, hv.vendor_handling_name, hv.vendor_handling_rule, hv.pph_tax_id AS hc_pph_id, hv.pph AS hc_pph, hvtx.tax_category AS hc_pph_category,
            CASE WHEN t.transaction_type = 1 THEN v3.vendor_name ELSE cust.customer_name END AS supplier,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1*t.send_weight END AS quantity2,
			con.price_converted AS pks_price ,fc.price AS fc_price, ftx.tax_id AS fc_pph_id, ftx.tax_value AS fc_pph, ftx.tax_category AS fc_pph_category,
			CASE WHEN t.slip_retur LIKE '%-R' THEN uc.price * -1 ELSE uc.price END AS uc_price,
			utx.tax_value AS uc_pph, utx.tax_category AS uc_pph_category, u.user_name,
			CASE WHEN t.transaction_type = 1 THEN (SELECT shi.shipment_no FROM shipment shi LEFT JOIN delivery d ON d.shipment_id = shi.shipment_id WHERE d.transaction_id = t.transaction_id LIMIT 1 )
		 ELSE sh.shipment_no END AS shipment_no2,
		 fp.payment_no AS fPayment, up.payment_no AS uPayment, hp.payment_no AS hPayment,s.`shrink_claim`,
		 ROUND(CASE WHEN s.shrink_tolerance_kg > 0 AND (t.shrink - s.shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - s.shrink_tolerance_kg
                WHEN s.shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > s.shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)
                ELSE 0 END,10) AS qtyClaim,
                fc.contract_pkhoa

        FROM TRANSACTION t
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
		LEFT JOIN tax ftx
	    	ON ftx.tax_id = t.fc_tax_id
		LEFT JOIN tax utx
	    	ON utx.tax_id = t.uc_tax_id
		LEFT JOIN USER u
			ON u.user_id = t.modify_by
		LEFT JOIN vendor_handling_cost vhc
			ON vhc.handling_cost_id = t.handling_cost_id
		LEFT JOIN vendor_handling hv
			ON hv.vendor_handling_id = vhc.vendor_handling_id
		LEFT JOIN tax hvtx ON hv.pph_tax_id = hvtx.tax_id
	LEFT JOIN payment fp ON fp.payment_id = t.fc_payment_id
	LEFT JOIN payment up ON up.payment_id = t.uc_payment_id
	LEFT JOIN payment hp ON hp.payment_id = t.hc_payment_id
        WHERE 1=1
        AND t.company_id = {$_SESSION['companyId']}
        {$whereProperty} {$whereProperty2} ORDER BY t.slip_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


//echo $sql;

//xlsWriteLabel($y,$x,$sql);



//xlsWriteLabel($y+1,$x,$sql);

//echo 'test';
xlsWriteLabel($y, $x ,'Tanggal');
xlsWriteLabel($y, $x+1 ,'Stockpile');
xlsWriteLabel($y, $x+2 ,'Slip');
xlsWriteLabel($y, $x+3 ,'No pol');
xlsWriteLabel($y, $x+4 ,'Kendaraan');
xlsWriteLabel($y, $x+5 ,'Nama OA');
xlsWriteLabel($y, $x+6 ,'Nama PKS');
xlsWriteLabel($y, $x+7 ,'Kontrak PKHOA');
xlsWriteLabel($y, $x+8 ,'Kontrak PKS');
xlsWriteLabel($y, $x+9 ,'Berat Kirim');
xlsWriteLabel($y, $x+10 ,'Berat Netto');
xlsWriteLabel($y, $x+11 ,'Freight Qty');
xlsWriteLabel($y, $x+12 ,'Harga OA');
xlsWriteLabel($y, $x+13 ,'Total OA');
xlsWriteLabel($y, $x+14 ,'Susut');
$y =$y + 1;

while ($row = $result->fetch_object()) {

	if($row->transaction_type == 2){
		if($row->quantity < 0){
			$quantity = $row->quantity * -1;
		}else{
			$quantity = '-' .$row->quantity;
		}
	}else{
		$quantity = $row->quantity;
	}

	if($row->contract_type2 == 'Curah' && $row->transaction_type == 1){
		$shrink = 0;
	}else{
		$shrink = $row->shrink;
	}

	if($row->freight_rule == 1){
		$fp = $row->send_weight * $row->freight_price;
	}else{
		$fp = $row->quantity * $row->freight_price;
	}

	if($row->vendor_handling_rule == 1){
		$hp = $row->send_weight * $row->handling_price;
	}else{
		$hp = $row->quantity * $row->handling_price;
	}

	if($row->freight_cost_id != 0 && $row->fc_pph_id != 0 && $row->fc_pph_category == 1){
		$fc = $fp;
		$fc_shrink = ($row->qtyClaim * $row->shrink_claim) / ((100 - $row->fc_pph) / 100);
		$fcTotal = $fc / ((100 - $row->fc_pph) / 100);
		$fc_total = $fcTotal - $fc_shrink;
	}elseif($row->freight_cost_id != 0){
		$fc_shrink = ($row->qtyClaim * $row->shrink_claim);
		$fcTotal = $fp;
		$fc_total = $fp - $fc_shrink;
	}else{
		$fc_shrink = 0;
		$fcTotal = 0;
		$fc_total = 0;
	}


			xlsWriteLabel($y, $x ,$row->unloading_date2);
			xlsWriteLabel($y, $x+1 ,$row->stockpile_name);
			xlsWriteLabel($y, $x+2 ,$row->slip_no);
			xlsWriteLabel($y, $x+3 ,$row->vehicle_no);
			xlsWriteLabel($y, $x+4 ,$row->vehicle_name);
			xlsWriteLabel($y, $x+5 ,$row->freight_code);
			xlsWriteLabel($y, $x+6 ,$row->vendor_name);
			xlsWriteLabel($y, $x+7 ,$row->contract_pkhoa);
			xlsWriteLabel($y, $x+8 ,$row->contract_no);
			xlsWriteNumber($y, $x+9 ,$row->send_weight);
			xlsWriteNumber($y, $x+10 ,$row->netto_weight);
			xlsWriteNumber($y, $x+11 ,$row->freight_quantity);
			xlsWriteNumber($y, $x+12 ,$row->freight_price);
			xlsWriteNumber($y, $x+13 ,$fcTotal);
			xlsWriteLabel($y, $x+14 ,$fc_shrink);

			$y =$y + 1;
	}

// memanggil function penanda akhir file excel
xlsEOF();
exit();

?>
