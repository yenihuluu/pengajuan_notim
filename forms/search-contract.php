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

$allowLock = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 19) {
            $allowLock = true;
        }
    }
}

$sql = "SELECT c.*, v.*, s.stockpile_name, co.company_name, cur.currency_code,
CASE WHEN c.contract_id IS NOT NULL THEN (SELECT  GROUP_CONCAT(s.stockpile_name)
FROM stockpile s INNER JOIN stockpile_contract sc ON s.stockpile_id = sc.stockpile_id WHERE sc.contract_id = c.contract_id)
ELSE '-' END AS stockpile_sharing,
(SELECT COALESCE(SUM(t.send_weight),0) FROM `transaction` t LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = c.contract_id AND (t.notim_status = 0 OR t.slip_retur IS NULL)) AS received, DATE_FORMAT(c.entry_date, '%d/%m/%Y') AS contractDate 
FROM stockpile_contract sc
		LEFT JOIN contract c ON sc.contract_id = c.contract_id
		LEFT JOIN stockpile s ON sc.stockpile_id = s.stockpile_id
		LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
		LEFT JOIN company co ON co.company_id = c.company_id
		LEFT JOIN currency cur ON cur.`currency_id` = c.`currency_id`
        WHERE 1=1
        AND c.contract_id = {$_POST['contractId']}  LIMIT 1";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
?>


<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $('#printContract').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#contractContainer").printThis();
//            $("#transactionContainer").hide();
        });

    });
	$('#lockContract').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=lock_contract&contractId=<?php echo $row->contract_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });

  $('#unlockContract').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=unlock_contract&contractId=<?php echo $row->contract_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
	$('#jurnalContract').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_contract&contractId=<?php echo $row->contract_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/search-contract.php', {contractId: <?php echo $row->contract_id; ?>}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
		$('#jurnalStockTransit').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_stock_transit',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/search-contract.php', {contractId: <?php echo $row->contract_id; ?>}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
//update surya reject contract-------------------------------------------------------------------------------------------------
  /*      $('#rejectContract').click(function(e){
                  $.ajax({
                      url: './data_processing.php',
                      method: 'POST',
                      data: 'action=jurnal_reject_contract&contractId=<?php echo $row->contract_id; ?>',
                      success: function(data) {
                          var returnVal = data.split('|');

                          if (parseInt(returnVal[4]) != 0)	//if no errors
                          {
                              alertify.set({ labels: {
                                  ok     : "OK"
                              } });
                              alertify.alert(returnVal[2]);

                              if (returnVal[1] == 'OK') {
                                  $('#dataContent').load('forms/search-contract.php', {contractId: <?php echo $row->contract_id; ?>}, iAmACallbackFunction2);

                              }
                          }
                      }
                  });
              });*/
//-----------------------------------------------------------------------------------------------------------------------------------------
	$("#openPO").validate({
			rules: {
                openpo: "required"
            },
            messages: {
                openpo: "This is a required field."
            },
			submitHandler: function(form) {
				$('#openInvoice').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to OPEN this Contract?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#openPO").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('contractId').value = returnVal[3];
                                //$('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);
                                //$('#dataContent').load('forms/search-contract.php', { contractId: returnVal[3] }, iAmACallbackFunction2);
                                $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);

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
		
	$("#rejectContract").validate({
			rules: {
                rejectContractDate: "required"
            },
            messages: {
                rejectContractDate: "Reject Date is a required field."
            },
			submitHandler: function(form) {
				$.blockUI({ message: '<h4>Please wait...</h4>' }); 
				$('#rejectButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this Contract?", function(form) {
                    if (form) {
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#rejectContract").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('contractId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/search-contract.php', { contractId: returnVal[3] }, iAmACallbackFunction2);

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
    $('#aksesInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=akses_invoice&contractId=<?php echo $row->contract_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
	$('#closeInvoice').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=close_invoice&contractId=<?php echo $row->contract_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/contract.php', {}, iAmACallbackFunction);
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

<div id="contractContainer">
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td colspan="6" style="text-align: left; font-size: 12pt; font-weight: 600;">
                PT. JATIM PROPERTINDO JAYA
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 12pt; font-weight: 600;">
                KONTRAK JUAL - BELI
            </td>
        </tr>

    </table>
    <br/>

    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="20%"><b>No PO</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->po_no; ?></td>
            <td width="20%"><b>Nama Bank</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->bank_name; ?></td>
        </tr>
        <tr>
            <td width="20%"><b>No Kontrak</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->contract_no; ?></td>
            <td width="20%"><b>No Rekening</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->account_no; ?></td>
        </tr>
        <tr>
        <td width="20%"><b>Vendor</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->vendor_name; ?></td>
            <td width="20%"><b>Nama Rekening</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->beneficiary; ?></td>
         </tr>
         <tr>

            <td width="20%"><b>NPWP</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->npwp; ?></td>

        </tr>
        <tr>
        	<td width="20%"><b></b></td>
            <td width="2%"></td>
            <td width="28%"></td>
            <td width="20%"><b>Alamat</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->vendor_address; ?></td>

        </tr>


    <?php } ?>

    </table>
    <br/>
    <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>

                <th>Stockpile</th>
                <th>Stockpile Sharing</th>
                <th>Quantity</th>
                <th>Harga (<?php echo $row->currency_code; ?>)</th>
                <th>Nilai Pembayaran (<?php echo $row->currency_code; ?>)</th>
            </tr>
        </thead>
        <tbody>

            <tr>

                <td><?php echo $row->stockpile_name; ?></td>
                <td><?php echo $row->stockpile_sharing; ?></td>
                <td><?php echo number_format($row->quantity, 2, ".", ","); ?></td>
                <td><?php echo number_format($row->price_converted, 10, ".", ","); ?></td>
                <td><?php echo number_format($row->price_converted * $row->quantity, 2, ".", ","); ?></td>
            </tr>

        </tbody>

    </table>
   <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>

                <th>NOTES</th>

            </tr>
        </thead>
        <tbody>

            <tr>

                <td><?php echo $row->notes; ?></td>

            </tr>

        </tbody>

    </table>


    <!--<br/>-->

            </td>
        </tr>
    </table>
</div>

<?php
if($boolBack) {
?>
<button class="btn" type="button" onclick="back()">Back</button>
<?php
}
?>
<button class="btn btn-info" id="printContract">Print</button>

<?php
if($row->contract_status != 2 && $row->payment_status != 1 && $row->received == 0) {
?>
<form method="post" id="rejectContract">
<input type="hidden" name="action" id="action" value="jurnal_reject_contract" />
<input type="hidden" name="contractId" id="contractId" value="<?php echo $row->contract_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Reject Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="rejectContractDate" name="rejectContractDate" data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" id="rejectButton" >Reject</button>
</div>
</div>
</form>
<?php
}
?>
<!--<button class="btn btn-success" id="unlockContract">Unlock</button>-->
<?php //} ?>
<?php if($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 213) {?>
<button class="btn btn-warning" id="jurnalContract">JC</button>
<button class="btn btn-warning" id="jurnalStockTransit">JST</button>
<?php if($row->invoice_status == 1){?>
<!--<button class="btn btn-success" id="aksesInvoice">Open Invoice</button>-->
<?php }else{?>
<!--<button class="btn btn-warning" id="closeInvoice">Close Invoice</button>-->
<?php } ?>
<?php if($row->open_po == 0){?>
<form method="post" id="openPO">
<input type="hidden" name="action" id="action" value="open_po_invoice" />
<input type="hidden" name="contractId" id="contractId" value="<?php echo $row->contract_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>OPEN PO INVOICE<span style="color: red;">*</span></label>
<input type="number" style="text-align: center;" id="openpo" name="openpo" value="<?php echo $row->open_po; ?>" />
</br>
<button class="btn btn-warning" id="openInvoice" >OPEN</button>
</div>
</div>
</form>
<?php
}
}
