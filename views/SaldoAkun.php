<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Saldo Awal Akun';

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#dataSearch').load('searchs/SaldoAkun.php');
		
		
		
		$("#SaldoForm").validate({
                rules: {
                   //vehicleId: "required",
                    //currencyId: "required",
                    //price: "required"
                },

                messages: {
                    //vehicleId: "Vehicle is a required field.",
                    //currencyId: "Currency is a required field.",
                    //price: "Price/KG is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#SaldoForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addStockpileUnloadingModal').modal('hide');
                                    $('#stockpile-unloading-data').load('tabs/stockpile-unloading-data.php', {stockpileId: $('input[id="generalStockpileId"]').val()});
                                    
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                } else {
                                    //show error message
                                    document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
                                    $("#modalErrorMsg").show();
                                }
                            }
                        }
                    });
                }
            });
    });
</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<div class="alert fade in alert-success" id="successMsgAll" style="display:none;">
    Success Message
</div>
<div class="alert fade in alert-error" id="errorMsgAll" style="display:none;">
    Error Message
</div>

<div id="dataSearch">
    
</div>

<div id="dataContent">
    
</div>

<div id="addSaldoModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="saldoModalLabel" aria-hidden="true">
        <form id="SaldoForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeSaldoModal">×</button>
                <h3 id="addSaldoModalLabel">Journal Account</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <!--<input type="hidden" name="modalTransactionId" id="modalTransactionId" value="<?php //echo $rowData->transaction_id; ?>" />-->
            <input type="hidden" name="action" id="action" value="SaldoAkun" />
            <div class="modal-body" id="addSaldoModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeSaldoModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
