<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
$allowReturn = false;
if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 31) {
            $allowReturn = true;
        }
	}
}

$transactionId = $myDatabase->real_escape_string($_POST['transactionId']);

$sql = "SELECT t.*, DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
			DATE_FORMAT(t.unloading_date, '%d/%m/%Y') AS unloading_date3,
            DATE_FORMAT(t.entry_date, '%d %b %Y %H:%i:%s') AS entry_date2, u.user_name,
            con.contract_no, v.vendor_name, 
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CASE WHEN t.transaction_type = 1 THEN con.po_no ELSE sh.shipment_code END AS po_no,
            CASE WHEN t.transaction_type = 1 THEN s.stockpile_name ELSE s2.stockpile_name END AS stockpile_name,
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2, vh.vehicle_name,
            f.freight_code, fc.price AS freight_cost, uc.price AS unloading_cost,
            sl.sales_no, cust.customer_name, SUBSTR(slip_retur,19,1) AS retur
        FROM transaction t 
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v
            ON v.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        INNER JOIN user u
            ON u.user_id = t.entry_by
        WHERE 1=1
        
		AND SUBSTR(t.slip_no,1,3) IN (SELECT a.stockpile_code FROM stockpile a LEFT JOIN user_stockpile  b ON a.stockpile_id = b.stockpile_id WHERE b.user_id = {$_SESSION['userId']})
        AND t.company_id = {$_SESSION['companyId']}
        AND t.transaction_id = {$transactionId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('#printTransaction2').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#transactionContainer2").printThis();
//            $("#transactionContainer").hide();
        });
		
		$('#jurnalNotim').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_notim&transactionId=<?php echo $row->transaction_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-transaction.php', {transactionId: <?php echo $row->transaction_id; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
		$('#jurnalMemorial').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_memorial&transactionId=<?php echo $row->transaction_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-transaction.php', {transactionId: <?php echo $row->transaction_id; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
		$('#susutNotim').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=susut_notim&transactionId=<?php echo $row->transaction_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/print-transaction.php', {transactionId: <?php echo $row->transaction_id; ?>}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
		$("#returnNotimOut").validate({
			submitHandler: function(form) {
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this notim?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnNotimOut").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('transactionId2').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-transaction.php', { transactionId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
			}
                    return false;
		});
		}
        });
		$("#returnNotimIn").validate({
			submitHandler: function(form) {
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this notim?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnNotimIn").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('transactionId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/print-transaction.php', { transactionId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
			}
                    return false;
		});
		}
        });
    });
    
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/search-transaction.php', {}, iAmACallbackFunction);
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


<div id="transactionContainer2">
    <?php
    if($row->transaction_type == 1) {
    ?>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Stockpile</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_name; ?></td>
            <td width="24%"><b>Type</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->transaction_type2; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Slip No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->slip_no; ?></td>
            <td width="24%"><b>PO No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->po_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Receive Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->unloading_date2; ?></td>
            <td width="24%"><b>Contract Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vendor_name; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Vehicle No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vehicle_no; ?></td>
            <td width="24%"><b>Supplier</b></td>
            <td width="2%">:</td>
            <td width="24%"></td>
        </tr>
        <tr>
            <td width="24%"><b>Driver</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->driver; ?></td>
            <td width="24%"><b>Contract No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->contract_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Loading Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->loading_date2; ?></td>
            <td width="24%"><b>Delivery Notes No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->permit_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Vehicle</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vehicle_name; ?></td>
            <td width="24%"><b>Sent Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"><b>Supplier Freight</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->freight_code; ?></td>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Bruto Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->bruto_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Tarra Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->tarra_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Netto Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
		<tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Shrink</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?> Kg</div></td>
        </tr>
    </table>
    <br/>
    
    <?php
    } else {
    ?>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Stockpile</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_name; ?></td>
            <td width="24%"><b>Type</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->transaction_type2; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Slip No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->slip_no; ?></td>
            <td width="24%"><b>Shipment Code</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->po_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Transaction Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->transaction_date2; ?></td>
            <td width="24%"><b>Buyer</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->customer_name; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Vessel Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vehicle_no; ?></td>
            <td width="24%"><b>Sales Agreement No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->sales_no; ?></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Stockpile Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>B/L Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?> Kg</div></td>
        </tr>
    </table>
    <br>
    <?php
    }
    ?>
    <!--<br/>-->
	<table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <?php
        if($row->notim_status == 1) {
            echo '<tr><td colspan="6" style="font-size: 14pt; font_weight: bold; color: red; text-align: center;">Returned</td></tr>';
        }
        ?>
    </table>
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td <?php if($row->notes == '') echo 'style="height: 40px;"'; ?>><?php echo $row->notes; ?></td>
            </tr>
        </tbody>
    </table>
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Driver</th>
                <th>Scaler</th>
                <th>Acknowledge</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 33%; height: 40px;"></td>
                <td style="width: 33%; height: 40px;"></td>
                <td style="width: 33%; height: 40px;"></td>
            </tr>
        </tbody>
    </table>
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printTransaction2">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>
		<?php if($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 213) {
?>
<button class="btn btn-warning" id="jurnalNotim">JP</button>
<button class="btn btn-warning" id="susutNotim">SN</button>
<button class="btn btn-warning" id="jurnalMemorial">JM</button>

<?php
}
?>
</div>
</div>
<hr>

<?php if($allowReturn && $row->transaction_type == 2 && $row->notim_status == 0 && $row->retur != 'R' && $row->fc_payment_id == '' && $row->uc_payment_id == '' && $row->hc_payment_id == '' && $row->payment_id == ''){?>

<form method="post" id="returnNotimOut">
<input type="hidden" name="action" id="action" value="return_notim_out" />
<input type="hidden" name="transactionId2" id="transactionId2" value="<?php echo $row->transaction_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnOutDate" name="returnOutDate" value="<?php echo $row->unloading_date3; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" >Return Notim Out</button>
</div>

</div>
</form>

<?php } else if($allowReturn && $row->transaction_type == 1 && $row->notim_status == 0 && $row->retur != 'R' && $row->fc_payment_id == '' && $row->uc_payment_id == '' && $row->hc_payment_id == '' && $row->payment_id == ''){?>

<form method="post" id="returnNotimIn">
<input type="hidden" name="action" id="action" value="return_notim_in" />
<input type="hidden" name="transactionId" id="transactionId" value="<?php echo $row->transaction_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnInDate" name="returnInDate" value="<?php echo $row->unloading_date3; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" >Return Notim In</button>
</div>
</div>
</form>

<?php 
}  // </editor-fold>
}
?>
