<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


// PATH

require_once '../assets/include/path_variable.php';


// Session

require_once PATH_INCLUDE . DS . 'session_variable.php';


// Initiate DB connection

require_once PATH_INCLUDE . DS . 'db_init.php';


$whereProperty = '';

 $periodFrom = '';
 $periodTo = '';
 $tipePengajuan = '';

 if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty1 .= " AND a.request_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereProperty2 .= " AND a.urgent_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty3 .= " AND a.request_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty4 .= " AND a.plan_payment_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty1 .= " AND a.request_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $whereProperty2 .= " AND a.urgent_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $whereProperty3 .= " AND a.request_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
    $whereProperty4 .= " AND a.plan_payment_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty1 .= " AND a.request_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty2 .= " AND a.urgent_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty3 .= " AND a.request_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    $whereProperty4 .= " AND a.plan_payment_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if(isset($_POST['tipePengajuan']) && $_POST['tipePengajuan'] != '') {
    $tipePengajuan = $_POST['tipePengajuan'];
for ($i = 0; $i < sizeof($tipePengajuan); $i++) {
                        if($tipe_pengajuan == '') {
                            $tipe_pengajuan .= "'". $tipePengajuan[$i] ."'";
                        } else {
                            $tipe_pengajuan .= ','. "'". $tipePengajuan[$i] ."'";
                        }
                    }

        $whereProperty5 .= " AND a.type_pengajuan IN ($tipe_pengajuan) ";
			}
?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/logbook-report.php', {
                    periodFrom: $('input[id="periodFrom"]').val(),
					periodTo: $('input[id="periodTo"]').val()
					//paymentSchedule: $('input[id="paymentSchedule"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

        $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'PG') {
               e.preventDefault();

                //$("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/detail-pg.php', {pgId: menu[1]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            } else if (menu[0] == 'PP') {
               e.preventDefault();

                //$("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/detail-pp.php', {pgId: menu[1]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            } else if (menu[0] == 'PI') {
               e.preventDefault();

                //$("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/detail-pi.php', {pgId: menu[1]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            } else if (menu[0] == 'PK') {
               e.preventDefault();

                //$("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/detail-pk.php', {pgId: menu[1]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            }
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/logbook-report-xls.php">

    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>"/>
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>"/>
    <input type="hidden" id="tipePengajuan" name="tipePengajuan" value="<?php echo $tipe_pengajuan; ?>"/>
	<!--<input type="hidden" id="paymentSchedule" name="paymentSchedule" value="<?php //echo $paymentSchedule; ?>"/>-->

    <button class="btn btn-success">Download XLS</button>

</form>

<?php

$sqla = "SELECT * FROM (SELECT a.pengajuan_no, CASE WHEN a.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 'General' AS type_pengajuan, a.request_date AS pengajuan_system, a.pengajuan_email_date AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpileId) AS stockpile, (SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT vendor_name FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) AS vendor,
(SELECT gvb.bank_name FROM general_vendor_bank gvb LEFT JOIN pengajuan_general_detail pgd ON gvb.gv_bank_id = pgd.gv_bank_id WHERE pgd.pg_id = a.pengajuan_general_id LIMIT 1) AS bank, 
a.remarks AS keterangan, (SELECT SUM(amount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS dpp,
(SELECT SUM(ppn_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS ppn,
(SELECT SUM(pph_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS pph,
(SELECT SUM(tamount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS totalAmount, a.file
FROM pengajuan_general a WHERE a.status_pengajuan != 2 AND a.status_pengajuan != 5 AND (SELECT currency_id FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) != 1 {$whereProperty1}
UNION ALL
SELECT CONCAT('PP/',a.idPP) AS pengajuan_no, CASE WHEN a.urgent_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 
(CASE WHEN a.payment_for = 1 THEN 'Curah' WHEN a.payment_for = 2 THEN 'Freight Cost/OA' WHEN a.payment_for = 3 THEN 'Unloading/OB' WHEN a.payment_for = 9 THEN 'Handling Cost' ELSE '' END) AS type_pengajuan ,
a.entry_date AS pengajuan_system, a.email_date AS pengajuan_email,
a.urgent_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile_id) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.user) AS pic_pengajuan,
(CASE WHEN a.vendor_id IS NOT NULL THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
WHEN a.freight_id IS NOT NULL THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freight_id)
WHEN a.vendor_handling_id IS NOT NULL THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendor_handling_id)
WHEN a.labor_id IS NOT NULL THEN (SELECT labor_name FROM labor WHERE labor_id = a.labor_id) ELSE '' END) AS vendor, a.bank,a.remarks AS keterangan, CASE WHEN a.vendor_id IS NOT NULL THEN (a.qty * a.price) 
WHEN a.labor_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount)
WHEN a.vendor_handling_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount) ELSE a.dpp END AS dpp,
 a.ppn_amount, a.pph_amount, a.amount AS totalAmount, a.file
FROM pengajuan_payment a WHERE a.currency_id != 1 AND a.dp_status != 2 AND a.dp_status != 5 {$whereProperty2}
UNION ALL
SELECT CONCAT('PI/',a.pengajuan_interalTF_id) AS pengajuan_no, CASE WHEN a.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END `status`, 'Internal Transfer' AS type_pengajuan, a.entry_date AS pengajuan_system, '' AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS vendor, (SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS bank,a.remarks AS keterangan, a.amount AS dpp, '0' AS ppn, '0' AS pph, a.amount AS totalAmount, a.file 
FROM pengajuan_internaltf a WHERE a.amount = 0 AND a.status != 2 {$whereProperty3}) a
WHERE 1=1 {$whereProperty5}
ORDER BY a.type_pengajuan ASC, a.pengajuan_no ASC";

$resulta = $myDatabase->query($sqla, MYSQLI_STORE_RESULT);

?>



<table class="table table-bordered table-striped" style="font-size: 8pt;" id="contentTableA">

    <thead>

    <tr>

        <th>No</th>
        <th>No. Pengajuan</th>
        <th>Status</th>


        <th>Tipe Pengajuan</th>

        <th>Input Pengajuan</th>

        <th>Email Pengajuan</th>

        <th>Request Payment</th>

        <th>Stockpile</th>

        <th>PIC Pengajuan</th>

        <th>Vendor</th>

        <th>Bank Vendor</th>

        <th>Keterangan Pengajuan</th>

        <th>DPP(USD)</th>

        <th>PPN(USD)</th>

        <th>PPh(USD)</th>

        <th>Total Pembayaran(USD)</th>
        <th>Dokumen</th>


    </tr>

    </thead>

    <tbody>

    <?php

    if ($resulta === false) {

        echo 'wrong query';
        echo $sqla;
    } else {
        $no = 1;
        while ($rowa = $resulta->fetch_object()) {
			
				$dppTotal1 = $dppTotal1 + $rowa->dpp;
				$ppnTotal1 = $ppnTotal1 + $rowa->ppn;
				$pphTotal1 = $pphTotal1 + $rowa->pph;
				$TotalAmount1 = $TotalAmount1 + $rowa->totalAmount;

            ?>


            <tr>

                <td><?php echo $no; ?></td>
                <td style="text-align: left;"><?php echo $rowa->pengajuan_no; ?></td>
                <td style="text-align: left;"><?php echo $rowa->status; ?></td>

             

                <td style="text-align: left;"><?php echo $rowa->type_pengajuan; ?></td>

                <td style="text-align: left;"><?php echo $rowa->pengajuan_system; ?></td>

                <td style="text-align: left;"><?php echo $rowa->pengajuan_email; ?></td>

                <td style="text-align: left;"><?php echo $rowa->request_payment; ?></td>

                <td style="text-align: left;"><?php echo $rowa->stockpile; ?></td>

                <td style="text-align: left;"><?php echo $rowa->pic_pengajuan; ?></td>

                <td style="text-align: left;"><?php echo $rowa->vendor; ?></td>

                <td style="text-align: left;"><?php echo $rowa->bank; ?></td>

                <td style="text-align: left;"><?php echo $rowa->keterangan; ?></td>

                <td style="text-align: right;"><?php echo number_format($rowa->dpp, 2, ".", ","); ?> </td>

                <td style="text-align: right;"><?php echo number_format($rowa->ppn, 2, ".", ","); ?></td>

                <td style="text-align: right;"><?php echo number_format($rowa->pph, 2, ".", ","); ?></td>

                <td style="text-align: right;"><?php echo number_format($rowa->totalAmount, 2, ".", ","); ?></td>

                <td style="text-align: left;"><a href="<?php echo $rowa->file;?>" target="_blank" role="button" title="view file">View Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>
            </tr>

            <?php
			$no++;
        }

    }

    ?>

    </tbody>
<tfoot>
	<?php
/*
	$sqlPph = "SELECT tx.tax_category, f.pph FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id WHERE freight_id = {$freightIds}";
                $resultPph = $myDatabase->query($sqlPph, MYSQLI_STORE_RESULT);   
                if($resultPph !== false && $resultPph->num_rows > 0) {
                    while($rowPph = $resultPph->fetch_object()) {
							
								$pph = $dpp * ($rowPph->pph/100);
							
					}
				}
	
	$grandTotal = $dpp - $pph;*/
	?>
	<!--<tr>
	<td colspan="14" style="text-align: right;">Sub Total</td>
	<td style="text-align: right;"><?php // echo number_format($dpp, 2, ".", ","); ?></td>
	</tr>
	<tr>
	<td colspan="14" style="text-align: right;">PPh</td>
	<td style="text-align: right;"><?php //echo number_format($pph, 2, ".", ","); ?></td>
	</tr>-->
	<tr>
	<td colspan="13" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($dppTotal1, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($ppnTotal1, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($pphTotal1, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($TotalAmount1, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
</table>
<?php

$sql = "SELECT * FROM (SELECT a.pengajuan_no, CASE WHEN a.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 'General' AS type_pengajuan, a.request_date AS pengajuan_system, a.pengajuan_email_date AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpileId) AS stockpile, (SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT vendor_name FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) AS vendor,
(SELECT gvb.bank_name FROM general_vendor_bank gvb LEFT JOIN pengajuan_general_detail pgd ON gvb.gv_bank_id = pgd.gv_bank_id WHERE pgd.pg_id = a.pengajuan_general_id LIMIT 1) AS bank, 
a.remarks AS keterangan, (SELECT SUM(amount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS dpp,
(SELECT SUM(ppn_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS ppn,
(SELECT SUM(pph_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS pph,
(SELECT SUM(tamount_converted) FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id) AS totalAmount, a.file, 'PG' AS tipe, a.pengajuan_general_id AS pgId
FROM pengajuan_general a WHERE a.status_pengajuan != 2 AND a.status_pengajuan != 5 AND (SELECT currency_id FROM pengajuan_general_detail WHERE pg_id = a.pengajuan_general_id LIMIT 1) = 1 {$whereProperty1}
UNION ALL
SELECT CONCAT('PP/',a.idPP) AS pengajuan_no, CASE WHEN a.urgent_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS `status`, 
(CASE WHEN a.payment_for = 1 THEN 'Curah' WHEN a.payment_for = 2 THEN 'Freight Cost/OA' WHEN a.payment_for = 3 THEN 'Unloading/OB' WHEN a.payment_for = 9 THEN 'Handling Cost' ELSE '' END) AS type_pengajuan ,
 a.entry_date AS pengajuan_system, a.email_date AS pengajuan_email,
a.urgent_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile_id) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.user) AS pic_pengajuan,
(CASE WHEN a.vendor_id IS NOT NULL THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
WHEN a.freight_id IS NOT NULL THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freight_id)
WHEN a.vendor_handling_id IS NOT NULL THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendor_handling_id)
WHEN a.labor_id IS NOT NULL THEN (SELECT labor_name FROM labor WHERE labor_id = a.labor_id) ELSE '' END) AS vendor, a.bank,a.remarks AS keterangan, 
CASE WHEN a.vendor_id IS NOT NULL THEN (a.qty * a.price) 
WHEN a.labor_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount) 
WHEN a.vendor_handling_id IS NOT NULL THEN (a.amount + a.pph_amount - a.ppn_amount) ELSE a.dpp END AS dpp,
 a.ppn_amount, a.pph_amount, a.amount AS totalAmount, a.file, 'PP' AS tipe, a.idPP AS pgId
FROM pengajuan_payment a WHERE a.currency_id = 1 AND a.dp_status != 2 AND a.dp_status != 5 {$whereProperty2}
UNION ALL
SELECT CONCAT('PI/',a.pengajuan_interalTF_id) AS pengajuan_no, CASE WHEN a.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END `status`, 'Internal Transfer' AS type_pengajuan, a.entry_date AS pengajuan_system, '' AS pengajuan_email,
a.request_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS vendor, (SELECT bank_name FROM bank WHERE bank_id = a.bank_id) AS bank,a.remarks AS keterangan, a.amount AS dpp, '0' AS ppn, '0' AS pph, a.amount AS totalAmount, a.file, 'PI' AS tipe, a.pengajuan_interalTF_id AS pgId  
FROM pengajuan_internaltf a WHERE a.status != 2 {$whereProperty3}
UNION ALL
SELECT CONCAT('PK/',a.purchasing_id) AS pengajuan_no, CASE WHEN a.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END `status`, 'PKS Kontrak' AS type_pengajuan , a.entry_date AS pengajuan_system, '' AS pengajuan_email,
a.plan_payment_date AS request_payment, (SELECT stockpile_name FROM stockpile WHERE stockpile_id = a.stockpile_id) AS stockpile,(SELECT user_name FROM `user` WHERE user_id = a.entry_by) AS pic_pengajuan,
(CASE WHEN a.vendor_id = 0 THEN a.tempVendor ELSE (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id) END) AS vendor,
(SELECT vb.bank_name FROM vendor_bank vb WHERE vendor_id = a.vendor_id LIMIT 1) AS bank, 
(SELECT CASE WHEN ppn = 0 THEN 'NON PKP' ELSE '' END FROM vendor WHERE vendor_id = a.vendor_id) AS keterangan, 
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN ((a.quantity * a.price)/1.11) 
 WHEN a.ppn = 2 THEN (a.quantity * a.price) ELSE ((a.quantity * a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id))END ) AS dpp,
(CASE WHEN a.vendor_id = 0 AND a.ppn = 1 THEN ((a.quantity * a.price) - ((a.quantity * a.price)/1.11))
WHEN a.vendor_id = 0 AND a.ppn = 2 THEN (((a.quantity * a.price)*1.11) - (a.quantity * a.price)) 
WHEN a.ppn = 2 THEN COALESCE((a.quantity * a.price) * (SELECT ppn/100 FROM vendor WHERE vendor_id = a.vendor_id),0) ELSE COALESCE((a.quantity * a.price) - 
((a.quantity * a.price) / (SELECT (100+ppn)/100 FROM vendor WHERE vendor_id = a.vendor_id)),0) END) AS ppn, '0' AS ppn,
(CASE WHEN a.ppn = 1 AND a.vendor_id = 0 THEN (a.quantity * a.price)
WHEN a.ppn = 2 AND a.vendor_id = 0 THEN ((a.quantity * a.price) + (((a.quantity * a.price)*1.11) - (a.quantity * a.price)) )
WHEN a.ppn = 2 THEN (a.quantity * a.price) + ((a.quantity * a.price) * COALESCE((SELECT ppn/100 FROM vendor WHERE vendor_id = a.vendor_id),0)) ELSE (a.quantity * a.price) END) AS totalAmount,
(CASE WHEN a.import2 IS NOT NULL THEN a.import2 ELSE a.upload_file END) AS `file`, 'PK' AS tipe, a.purchasing_id AS pgId
FROM purchasing a WHERE a.`type` = 1 AND a.price > 0 AND a.status != 1 AND a.logbook_status = 0 {$whereProperty4}) a
WHERE 1=1 {$whereProperty5}
ORDER BY a.type_pengajuan ASC, a.pengajuan_no ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>



<table class="table table-bordered table-striped" style="font-size: 8pt;" id="contentTable">

    <thead>

    <tr>

        <th>No</th>
        <th>No Pengajuan</th>
        <th>Status</th>


        <th>Tipe Pengajuan</th>

        <th>Input Pengajuan</th>

        <th>Email Pengajuan</th>

        <th>Request Payment</th>

        <th>Stockpile</th>

        <th>PIC Pengajuan</th>

        <th>Vendor</th>

        <th>Bank Vendor</th>

        <th>Keterangan Pengajuan</th>

        <th>DPP(IDR)</th>

        <th>PPN(IDR)</th>

        <th>PPh(IDR)</th>

        <th>Total Pembayaran(IDR)</th>

        <th>Dokumen</th>


    </tr>

    </thead>

    <tbody>

    <?php

    if ($result === false) {

        echo 'wrong query';
        echo $sql;
    } else {
        $no = 1;
        while ($row = $result->fetch_object()) {
			
				$dppTotal = $dppTotal + $row->dpp;
				$ppnTotal = $ppnTotal + $row->ppn;
				$pphTotal = $pphTotal + $row->pph;
				$TotalAmount = $TotalAmount + $row->totalAmount;

            ?>


            <tr>

                <td><?php echo $no; ?></td>
                <td style="text-align: left;"><a href="#" id="<?php echo $row->tipe; ?>|<?php echo $row->pgId; ?>" role="button"><?php echo $row->pengajuan_no; ?></a></td>
                <td style="text-align: left;"><?php echo $row->status; ?></td>

             

                <td style="text-align: left;"><?php echo $row->type_pengajuan; ?></td>

                <td style="text-align: left;"><?php echo $row->pengajuan_system; ?></td>

                <td style="text-align: left;"><?php echo $row->pengajuan_email; ?></td>

                <td style="text-align: left;"><?php echo $row->request_payment; ?></td>

                <td style="text-align: left;"><?php echo $row->stockpile; ?></td>

                <td style="text-align: left;"><?php echo $row->pic_pengajuan; ?></td>

                <td style="text-align: left;"><?php echo $row->vendor; ?></td>

                <td style="text-align: left;"><?php echo $row->bank; ?></td>

                <td style="text-align: left;"><?php echo $row->keterangan; ?></td>

                <td style="text-align: right;"><?php echo number_format($row->dpp, 2, ".", ","); ?> </td>

                <td style="text-align: right;"><?php echo number_format($row->ppn, 2, ".", ","); ?></td>

                <td style="text-align: right;"><?php echo number_format($row->pph, 2, ".", ","); ?></td>

                <td style="text-align: right;"><?php echo number_format($row->totalAmount, 2, ".", ","); ?></td>
                <td style="text-align: left;"><a href="<?php echo $row->file;?>" target="_blank" role="button" title="view file">View Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>
                
            </tr>

            <?php
			$no++;
        }

    }

    ?>

    </tbody>
<tfoot>
	<?php
/*
	$sqlPph = "SELECT tx.tax_category, f.pph FROM freight f LEFT JOIN tax tx ON tx.tax_id = f.pph_tax_id WHERE freight_id = {$freightIds}";
                $resultPph = $myDatabase->query($sqlPph, MYSQLI_STORE_RESULT);   
                if($resultPph !== false && $resultPph->num_rows > 0) {
                    while($rowPph = $resultPph->fetch_object()) {
							
								$pph = $dpp * ($rowPph->pph/100);
							
					}
				}
	
	$grandTotal = $dpp - $pph;*/
	?>
	<!--<tr>
	<td colspan="14" style="text-align: right;">Sub Total</td>
	<td style="text-align: right;"><?php // echo number_format($dpp, 2, ".", ","); ?></td>
	</tr>
	<tr>
	<td colspan="14" style="text-align: right;">PPh</td>
	<td style="text-align: right;"><?php //echo number_format($pph, 2, ".", ","); ?></td>
	</tr>-->
	<tr>
	<td colspan="13" style="text-align: right;">Grand Total</td>
	<td style="text-align: right;"><?php echo number_format($dppTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($ppnTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($pphTotal, 2, ".", ","); ?></td>
	<td style="text-align: right;"><?php echo number_format($TotalAmount, 2, ".", ","); ?></td>
	</tr>
	</tfoot>
</table>
<div id="addDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
        <form id="detailForm" method="post" style="margin: 0px;" action="reports/detailEndStock-xls.php">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>
                <h3 id="addDetailModalLabel">Detail</h3>
            </div>
           
           
            <div class="modal-body" id="addDetailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>
				<!--<button class="btn btn-success">Download XLS</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
    </div>