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


$invId = $myDatabase->real_escape_string($_POST['invId']);
$totalPrice = '';
$sql = "SELECT inv.inv_notim_no, pp.invoice_no, pp.invoice_date, pp.tax_invoice, sp.stockpile_name AS payment_location,
CASE WHEN pp.payment_method = '1' THEN 'Payment' 
	WHEN pp.payment_method = '2' THEN 'Down Payment' ELSE NULL END AS Payment, 
CASE WHEN pp.payment_type = 2 THEN 'OUT' ELSE 'IN' END AS tipe, 
CASE WHEN pp.payment_for = 0 THEN 'PKS Kontrak' 
	WHEN pp.payment_for = 1 THEN 'Local Sales' 
	WHEN pp.payment_for = 2 THEN 'Freight Cost' 
	WHEN pp.payment_for = 9 THEN 'Handling Cost' 
	WHEN pp.payment_for = 3 THEN 'Unloading Cost' ELSE NULL END AS payment_For,
pp.periodeFrom, pp.periodeTo, sl.stockpile_name AS stockpile_location, fr.customer_name, fb.bank_name, fb.beneficiary, fb.account_no,
CASE WHEN pp.urgent_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END AS urgentType, pp.remarks, inv.file1, inv.idPP, inv.invoice_status          
FROM invoice_sales inv 
LEFT JOIN pengajuan_payment_sales pp ON inv.idPP = pp.idPP
LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
LEFT JOIN stockpile sl ON sl.stockpile_id = pp.stockpile_location
LEFT JOIN customer fr ON fr.customer_id = pp.customer_id
LEFT JOIN customer_bank fb ON fb.cust_bank_id = pp.vendor_bank_id 
WHERE inv.inv_notim_id = {$invId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_object();
    $invoice_status = $row->invoice_status;
     /*$paymentStatus = $row->payment_status;
	 $invoiceStatus = $row->invoice_status;
	 $mutasi_status = $row->mutasi_status;
	 $edit_date = $row->edit_date;
	 $invoice_date2 = $row->invoice_date2;*/
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
		
		/*$("#returnInvoice").validate({
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
                data: 'action=jurnal_invoice_return&invoiceId=<?php //echo $row->invoice_id; ?>',
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
		
	    $('#jurnalAccrue').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_accrue',
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
        });*/

        $("#returnInvoiceSales").validate({
			rules: {
                returnInvoiceSalesDate: "required"
            },
            messages: {
                returnInvoiceSalesDate: "Return Date is a required field."
            },
			submitHandler: function(form) {
				$('#returnButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this Invoice?", function(form) {
                    if (form) {

            $.blockUI({ message: '<h4>Please wait...</h4>' });
			$('#loading').css('visibility','visible');
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnInvoiceSales").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('invId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-invoice-ls.php', { invId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#returnButton').attr("disabled", false);
                        }
                    }
                });

                $('#loading').css('visibility','hidden');
			}
                    return false;
		});
		}
        });

        $('#jurnalInvoiceSales').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_invoice_sales',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-invoice-ls.php', {idPP: <?php echo $row->idPP; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });


    });
    
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/invoice-notim-sales.php', {}, iAmACallbackFunction);
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
            <td width="24%"><?php echo $row->inv_notim_no; ?></td>
            
            
        </tr>
		<tr>
            <td width="24%"><b>Original Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_no; ?></td>
            <td width="24%"><b>Stockpile Location</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_location; ?></td>
            
        </tr>
        <tr>
            <td width="24%"><b>Invoice Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->invoice_date; ?></td>
            <td width="24%"><b>Customer</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->customer_name; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Tax Invoice No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->tax_invoice; ?></td>
            <td width="24%"><b>Bank Account</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->account_no; ?></td>
        </tr>
        <tr>
          
			<td width="24%"><b>Payment Location</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->payment_location; ?></td>
            <td width="24%"><b>Bank Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->bank_name; ?></td>
        </tr>
        <tr>
              <td width="24%"><b>Period From</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->periodeFrom; ?></td>
            <td width="24%"><b>Beneficiary</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->beneficiary; ?></td>
        </tr>
		 <tr>
              <td width="24%"><b>Period To</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->periodeTo; ?></td>
            <td width="24%"><b>Documents</b></td>
            <td width="2%">:</td>
            <td width="24%"><a href="<?php echo $row->file1 ?>" target="_blank">View Documents<img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></td>
        </tr>
		 
		<?php
        if($invoice_status == 2) {
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
	$sql = "SELECT t.*, con.customer_id,sc.`shipment_no`,con.`price_converted` AS salesPrice,
                v.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                txpph.tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value
            FROM TRANSACTION t
            LEFT JOIN shipment sc
                ON sc.shipment_id = t.shipment_id
            LEFT JOIN sales con
                ON con.sales_id = sc.sales_id
            LEFT JOIN customer v
                ON v.customer_id = con.customer_id
            LEFT JOIN tax txppn
                ON txppn.tax_id = v.ppn_tax_id
            LEFT JOIN tax txpph
                ON txpph.tax_id = t.curah_tax_id
            LEFT JOIN pengajuan_sales ps ON ps.`transaction_id` = t.`transaction_id`
            WHERE ps.`idPP` = {$row->idPP}
			ORDER BY t.transaction_date ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$totalPrice = 0;
$totalPPN = 0;
$totalPPh = 0;
$dppTotalPrice = 0;
	?>
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                
                <th>Slip No</th>
                <th>Transaction Date</th>
                <th>Shipment Code</th>
                
                <th>Vehicle No</th>
                <th>Qty</th>
                <th>Unit Price</th>
				<th>Amount</th>
				<th>PPN</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
         <tr>
        <?php
		if($result !== false && $result->num_rows > 0) {
		 while($row = $result->fetch_object()) {
			 
		if($row->pph_tax_id == 0) {
                $dppTotalPrice = $row->salesPrice * $row->quantity;
            } else {
                if($row->pph_tax_category == 1) {
                    $dppTotalPrice = ($row->salesPrice * $row->quantity) / ((100 - $row->pph_tax_value) / 100);
                } else {
                    $dppTotalPrice = $row->salesPrice * $row->quantity;
                }
            }
			
			if($row->ppn_tax_id != 'NULL'){
				 $ppn = ($row->salesPrice * $row->quantity) * ($row->ppn_tax_value/100);
			}else{
				 $ppn = 0;
			}
			$salesPrice = $row->salesPrice;
			$amountPrice = $dppTotalPrice + $ppn;
			$totalPrice = $totalPrice + $amountPrice;
            
	 ?>
           
               
                <td><?php echo $row->slip_no;?></td>
                <td><?php echo $row->transaction_date;?></td>
                <td><?php echo $row->shipment_no;?></td>
               
                <td><?php echo $row->vehicle_no;?></td>
                 <td style="text-align: right;"><?php echo number_format($row->quantity, 2, ".", ",");?></td>
                <td style="text-align: right;"><?php echo number_format($row->salesPrice, 3, ".", ",");?></td>
				<td style="text-align: right;"><?php echo number_format($dppTotalPrice, 3, ".", ",");?></td>
				<td style="text-align: right;"><?php echo number_format($ppn, 3, ".", ",");?></td>
                <td style="text-align: right;"><?php echo number_format($amountPrice, 2, ".", ",");?></td>
                
            
            </tr>
          <?php
		}
}
?>
        </tbody>
        <tfoot>
        <tr>
        <td colspan="8" style="text-align: right;">Grand Total</td>
        <td style="text-align: right;"><?php echo number_format($totalPrice, 2, ".", ",")?></td>
       
        </tr>
        </tfoot>
    </table>
    
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printInvoiceDetail">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>
        <?php if($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 213) {
?>
        <button class="btn btn-warning" id="jurnalInvoiceSales">JP</button>
        <?php
}?>
  </div>
</div>
<?php if($invoice_status != 2) {
?>
<form method="post" id="returnInvoiceSales">
<input type="hidden" name="action" id="action" value="return_invoice_sales" />
<input type="hidden" name="invId" id="invId" value="<?php echo $invId; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnInvoiceSalesDate" name="returnInvoiceSalesDate"  data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" id="returnButton">Return</button>
</div>
</div>
</form>
<?php
  }  // </editor-fold>
}
?>
