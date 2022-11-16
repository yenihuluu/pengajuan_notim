<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowReturnInvoice = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 49) {
            $allowReturnInvoice = true;
        }
    }
}


$invoiceId = $myDatabase->real_escape_string($_POST['invoiceId']);
$totalPrice = '';
$sql = "SELECT i.*,  DATE_FORMAT(i.invoice_date, '%d %b %Y') AS invoice_date,
			DATE_FORMAT(i.invoice_date, '%d/%m/%Y') AS invoice_date2,
            DATE_FORMAT(i.input_date, '%d %b %Y') AS input_date,
			DATE_FORMAT(i.request_date, '%d %b %Y') AS request_date,
			DATE_FORMAT(i.tax_date, '%d %b %Y') AS tax_date,
            DATE_FORMAT(i.entry_date, '%d %b %Y %H:%i:%s') AS entry_date2,
			DATE_FORMAT(i.input_date, '%Y-%m') AS edit_date,
			s.stockpile_name, c.po_no,
            COALESCE((SELECT mh.status FROM mutasi_header mh LEFT JOIN mutasi_detail md ON mh.mutasi_header_id = md.mutasi_header_id 
LEFT JOIN invoice_detail id ON md.mutasi_detail_id = id.mutasi_detail_id WHERE id.invoice_id = i.invoice_id LIMIT 1),0) AS mutasi_status,
CASE WHEN pg.pengajuan_no IS NOT NULL THEN pg.pengajuan_no ELSE '-' END AS pengajuanNo
    
       FROM invoice i
	   LEFT JOIN stockpile s
	   ON i.stockpileId = s.stockpile_id
	   LEFT JOIN stockpile_contract sc
	   ON sc.stockpile_contract_id = i.po_id
	   LEFT JOIN contract c
	   ON c.contract_id = sc.contract_id
	    LEFT JOIN pengajuan_general pg 
		ON pg.invoice_id = i.invoice_id
       WHERE i.company_id = {$_SESSION['companyId']}
       AND i.invoice_id = {$invoiceId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_object();
     $paymentStatus = $row->payment_status;
	 $invoiceStatus = $row->invoice_status;
	 $mutasi_status = $row->mutasi_status;
	 $edit_date = $row->edit_date;
	 $invoice_date2 = $row->invoice_date2;
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('#printInvoiceDetail').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#invoiceDetail").printThis();
//            $("#transactionContainer").hide();
        });
     /*$('#returnInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=return_invoice&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-invoice.php', {invoiceId: <?php echo $row->invoice_id; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });*/
		
		$("#returnInvoice").validate({
			rules: {
                returnInvoiceDate: "required"
            },
            messages: {
                returnInvoiceDate: "Return Date is a required field."
            },
			submitHandler: function(form) {
				$('#returnButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this invoice?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnInvoice").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('invoiceId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-invoice.php', { invoiceId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#returnButton').attr("disabled", false);
                        }
                    }
                });
			}
                    return false;
		});
		}
        });
		
		$('#jurnalInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
    
	$('#jurnalReturn').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice_return&invoiceId=<?php echo $row->invoice_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
    });
    
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/invoice.php', {}, iAmACallbackFunction);
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


<div id="invoiceDetail">
   
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_no; ?></td>
            <td width="24%"><b>Stockpile</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_name; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Original Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_no2; ?></td>
            <td width="24%"><b>Invoice Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_date; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Tax Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_tax; ?></td>
            <td width="24%"><b>Tax Invoice Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->tax_date; ?></td>
        </tr>
        <tr>
          
			<td width="24%"><b>Pengajuan No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->pengajuanNo; ?></td>
            <td width="24%"><b>Request Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->request_date; ?></td>
        </tr>
        <tr>
              <td width="24%"><b>PO No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->po_no; ?></td>
            <td width="24%"><b>Input Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->input_date; ?></td>
        </tr>
		
		<?php
        if($row->payment_status == 2) {
            echo '<tr><td colspan="6" style="font-size: 14pt; font_weight: bold; color: red; text-align: center;">Returned</td></tr>';
        }
        ?>
    </table>
    
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td <?php if($row->remarks == '') echo 'style="height: 40px;"'; ?>><?php echo $row->remarks; ?></td>
            </tr>
        </tbody>
    </table>
    <!--<br/>-->
    <?php
	$sql = "SELECT id.invoice_detail_id,
CASE WHEN id.type = 4 THEN 'Loading'
WHEN id.type = 5 THEN 'Umum'
WHEN id.type = 6 THEN 'HO' ELSE '' END AS TYPE, a.account_name, sh.shipment_no, s.stockpile_name, id.notes, 
id.qty, id.price, id.termin, id.amount, id.ppn, id.pph, id.tamount, gv.general_vendor_name, gv.pph AS gv_pph, gv.ppn AS gv_ppn,
CASE WHEN idUOM IS NOT NULL THEN (SELECT uom_type FROM uom WHERE idUOM = id.idUOM) ELSE '-' END AS uom,
(SELECT SUM(idp.amount_payment) FROM invoice_dp idp WHERE id.`invoice_detail_id` = idp.`invoice_detail_id` AND idp.status = 0) AS down_payment,
(SELECT CASE WHEN id.`ppn` != 0 THEN SUM(idp.amount_payment) * (ppn.`tax_value`/100) ELSE 0 END FROM invoice_dp idp WHERE id.`invoice_detail_id` = idp.`invoice_detail_id` AND idp.status = 0) AS ppnDP,
(SELECT CASE WHEN id.`pph` != 0 THEN SUM(idp.amount_payment) * (pph.`tax_value`/100) ELSE 0 END FROM invoice_dp idp WHERE id.`invoice_detail_id` = idp.`invoice_detail_id`AND idp.status = 0) AS pphDP
FROM invoice_detail id LEFT JOIN account a ON id.account_id = a.account_id
LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
WHERE id.invoice_id = {$invoiceId} ORDER BY id.invoice_detail_id ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


 
	?>
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                
                <th>Account</th>
                <th>Vendor</th>
                <th>Reference Code</th>
                <th>Remark (SP)</th>
                <th>Notes</th>
                <th>Qty</th>
                <th>Unit Price</th>
				<th>Termin</th>
                <th>Amount</th>
                <th>PPN</th>
                <th>PPh</th>
				<th>Down Payment</th>
				
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
         <tr>
        <?php
		if($result !== false && $result->num_rows > 0) {
		 while($row = $result->fetch_object()) {
			 
		
			/*$sqlDP = "SELECT SUM(idp.amount_payment) AS down_payment,
SUM(CASE WHEN id.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END) AS ppn,
SUM(CASE WHEN id.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END) AS pph
FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.`invoice_detail_id`
LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
WHERE idp.status = 0 AND id.invoice_id = {$invoiceId}";
			
		
    		$resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
			if($resultDP !== false && $resultDP->num_rows == 1) {
				 $rowDP = $resultDP->fetch_object();*/
		
			if($row->ppnDP == 0){
				$dp_ppn = 0;
			}else{
				//$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
				$dp_ppn = $row->ppnDP;
			}
			
			if($row->pphDP == 0){
				$dp_pph = 0;
			}else{
				//$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
				$dp_pph = $row->pphDP;
			}
			if($row->down_payment != 0){
				 //$dp_ppn = $rowDP->down_payment * ($row->gv_ppn/100);
				 //$dp_pph = $rowDP->down_payment * ($row->gv_pph/100);
				 $downPayment = ($row->down_payment + $dp_ppn) - $dp_pph;
			}else{
				 $downPayment = 0;
			}
		//	}
			
		$tamount1 = $row->amount + $row->ppn - $row->pph;
		$tamount = $tamount1 - $downPayment;		
		$totalPrice = $totalPrice + $tamount;
	 ?>
           
               
                <td><?php echo $row->account_name;?></td>
                <td><?php echo $row->general_vendor_name;?></td>
                <td><?php echo $row->shipment_no;?></td>
                <td><?php echo $row->stockpile_name;?></td>
                <td><?php echo $row->notes;?></td>
                 <td><?php echo number_format($row->qty, 3, ".", ",") .' '. $row->uom;?></td>
                <td><?php echo number_format($row->price, 3, ".", ",");?></td>
				<td><?php echo number_format($row->termin, 0, ".", ",");?>%</td>
                <td><?php echo number_format($row->amount, 2, ".", ",");?></td>
                <td><?php echo number_format($row->ppn, 2, ".", ",");?></td>
                <td><?php echo number_format($row->pph, 2, ".", ",");?></td>
				<td><?php echo number_format($downPayment, 2, ".", ",");?></td>
				
                <td><?php echo number_format($tamount, 2, ".", ",");?></td>
                
            
            </tr>
          <?php
		}
}
?>
        </tbody>
        <tfoot>
        <tr>
        <td colspan="11" style="text-align: right;">Grand Total</td>
        <td colspan="3" style="text-align: right;"><?php echo number_format($totalPrice, 2, ".", ",")?></td>
       
        </tr>
        </tfoot>
    </table>
    
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printInvoiceDetail">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>
		 <?php
$date_now = date('Y-m');
if(($paymentStatus == 0 || $paymentStatus == 2) && $mutasi_status == 0 && $invoiceStatus == 0 && $allowReturnInvoice) {
?>
<form method="post" id="returnInvoice">
<input type="hidden" name="action" id="action" value="return_invoice" />
<input type="hidden" name="invoiceId" id="invoiceId" value="<?php echo $invoiceId; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnInvoiceDate" name="returnInvoiceDate"  data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" id="returnButton">Return</button>
</div>
</div>
</form>
<?php
}
 ?>

<?php if($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 213) {
?>
<button class="btn btn-warning" id="jurnalInvoice">JI</button>
<button class="btn btn-warning" id="jurnalReturn">JR</button>
<?php
} 
?>  
  </div>
</div>

<?php
    // </editor-fold>
}
?>
