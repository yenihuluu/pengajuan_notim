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


$sa_id = $myDatabase->real_escape_string($_POST['sa_id']);
$totalPrice = '';
$advance = '';
$sql = "SELECT a.*, b.nama, c.stockpile_name AS origin2, d.stockpile_name AS destination2, CONCAT(g.level,' ',f.divisi) AS jabatan, h.stockpile_name,i.general_vendor_name, j.user_name
		FROM perdin_surat a
		LEFT JOIN perdin_user b ON a.id_user = b.id_user
		LEFT JOIN stockpile c ON c.stockpile_id = a.origin
		LEFT JOIN stockpile d ON d.stockpile_id = a.destination
		LEFT JOIN general_vendor i ON i.general_vendor_id = a.id_user
		LEFT JOIN perdin_level g ON g.level_id = i.level_id 
		LEFT JOIN perdin_divisi f ON f.div_id = i.div_id
		LEFT JOIN stockpile h ON h.stockpile_id = a.stockpile_id
		LEFT JOIN user j ON j.user_id = a.entry_by
		WHERE sa_id = {$sa_id}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows > 0) {
    $row = $result->fetch_object();
	//$sa_method = $row->sa_method;
	$origin2 = $row->origin2;
	$tanggal = $row->tanggal;
    $stockpile_name = $row->stockpile_name;
	//$docs = $row->docs;
	$user_name = $row->user_name;
	// $mutasi_status = $row->mutasi_status;
	// $edit_date = $row->edit_date;
	// $invoice_date2 = $row->invoice_date2;
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('#printPerdin').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#perdin").printThis();

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
			submitHandler: function(form) {
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
        });*/
		
		/*$("#uploadAdvancePerdin").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
               // _method: 'INSERT',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0) //if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/perdin-adv_settle.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });*/
		
		
		
		
		
    });
    
    $('#cancel').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=cancel_perdin_surat&sa_id=<?php echo $row->sa_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/perdin-surat.php', {}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
    
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/perdin-surat.php', {}, iAmACallbackFunction);
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


<div id="perdin">
<br>
	<br>
	<br>
	<br>
   <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td colspan="6" style="text-align: center; font-size: 14pt; font-weight: 600; ">
                PT. JATIM PROPERTINDO JAYA
            </td>
        </tr>
         <!-- <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">JL. MELUR NO.15 A Rt/Rw 04/04, PADANG TERUBUK, SENAPELAN,</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">KOTAMADYA  PEKANBARU, RIAU, INDONESIA 28155</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">TELP.+62-761-44633 FAX.+62-761-44622</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt; ">E-MAIL : support@jatimpropertindo.com</td></tr>-->
    </table>
	<hr style="border: solid; border-width: 2px;">
	
	<table width="100%" style="table-layout:fixed; font-size: 9pt;">
        
          <tr><td colspan="6" style="text-align: center; font-size: 10pt; font-weight: 600;" >SURAT TUGAS PERJALANAN DINAS</td></tr>
		  <tr><td colspan="6" style="text-align: center; font-size: 10pt;">NO : <?php echo $row->sa_no; ?></td></tr>
		  
    </table>
	<br>
	<br>
	<table width="50%" style="table-layout:fixed; font-size: 9pt;">
		
		<tr>
		
		<td >Yang bertanda tangan dibawah ini, dengan ini memberi tugas kepada :</td>
		</tr>
	</table>
	<br>
    <table width="50%" style="table-layout:fixed; font-size: 9pt;">
	
		
        <tr>
			<td width="5%"></td>
            <td width="15%"><b>Nama</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->general_vendor_name; ?></td>
    
        </tr>
        <!--<tr>
			<td width="5%"></td>
            <td width="10%"><b>Jabatan</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php //echo $row->jabatan; ?></td>
            
        </tr>-->
        <tr>
			<td width="5%"></td>
            <td width="10%"><b>Lokasi Asal</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->origin2; ?></td>
            
        </tr>
        <tr>
			<td width="5%"></td>
            <td width="10%"><b>Lokasi Tujuan</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->destination2; ?></td>
            
        </tr>
        <tr>
			<td width="5%"></td>
            <td width="10%"><b>Tanggal Berangkat</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->date_from; ?></td>
            
        </tr>
		<tr>
			<td width="5%"></td>
            <td width="10%"><b>Tanggal Kembali</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->date_to; ?></td>
            
        </tr>
		<tr>
			<td width="5%"></td>
            <td width="10%"><b>Tugas / Keperluan</b></td>
            <td width="2%">:</td>
            <td width="10%"><?php echo $row->remarks; ?></td>
            
        </tr>
		
		
    </table>
    
   <br>
    <?php
	
	/*if($row->sa_method == 2){
	$sql = "SELECT a.*, b.`jenis_benefit` AS notes, a.amount AS price, '' AS qty, '' AS advance, a.keterangan, a.hari
			FROM perdin_adv_detail a
			LEFT JOIN perdin_benefit b ON a.benefit_id = b.benefit_id
			WHERE a.sa_id = {$sa_id}";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	
	}else{
	
	$sql = "SELECT a.notes, CONCAT(ROUND(a.qty,2), ' (',  b.`uom_type`, ')') AS qty, a.price, a.amount,
			COALESCE((SELECT SUM(amount) FROM perdin_dp WHERE settle_id = a.settle_id),0) AS advance
			FROM perdin_settle_detail a
			LEFT JOIN uom b ON b.`idUOM` = a.`uom`
			WHERE a.sa_id = {$sa_id}";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);	
	
	}*/	

 
	?>
    
   
	<table width="75%" style="table-layout:fixed; font-size: 9pt;">
		
		<tr>
		
		<td >Setibanya ditempat tujuan harap melapor kepada Pimpinan Perwakilan Perusahaan.</td>
		</tr>
	</table>
	<table width="75%" style="table-layout:fixed; font-size: 9pt;">
		
		<tr>
		
		<td >Demikian surat tugas ini diberikan kepada yang bersangkutan untuk dipergunakan seperlunya.																						
</td>
		</tr>
	</table>
	<br>
	<table width="100%">
        <tr>
            <td width="45%">
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
                    <tr>
                        <td width="28%"><b>Dikeluarkan di</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $stockpile_name; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Tanggal</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $tanggal; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Dibuat Oleh</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $user_name; ?></td>
                    </tr>
                </table>
            </td>
            <td width="55%">
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt; height: 186px;">
                    <thead>
                        <tr>
                            <th style="vertical-align: top; height: 30px; text-align: center;">Pemberi Tugas </th>
                            <th style="vertical-align: top; height: 30px; text-align: center;">Penerima Tugas </th>
                            <th style="vertical-align: top; height: 30px; text-align: center;">Menyetujui</th>

							
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          
                            <td style="width: 25%; height: 40px;"></td>
                            <td style="width: 25%; height: 40px;"></td>
							<td style="width: 25%; height: 40px;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<hr>

<div class="row-fluid">
    <div class="span12 lightblue">
        <button class="btn btn-primary" id="printPerdin">Print</button>
        <button class="btn" type="button" onclick="back()">Back</button>
		<?php if($row->status == 0){ ?>
		<button class="btn btn-warning" id="cancel">Cancel</button>
		<?php } ?>
  </div>
</div>

<hr>
<br>


<?php
    // </editor-fold>
}
?>

