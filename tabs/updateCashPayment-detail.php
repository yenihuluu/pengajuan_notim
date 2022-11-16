<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$paymentId = '';
//$paymentNo = '';

// If ID is in the parameter
if(isset($_POST['paymentId']) && $_POST['paymentId'] != '') {
    
    $paymentId = $_POST['paymentId'];
    
    /*$sql = "SELECT * FROM pay WHERE invoice_id = {$paymentId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $invoiceNo = $row->invoice_no;
    }*/
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
           /* $('#addStockpileFreight').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addStockpileFreightModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addStockpileFreightModalForm').load('forms/stockpile-freight.php', {stockpileId: $('input[id="generalStockpileId"]').val()});
            });*/
            
            $("#updateCashPaymentDetailForm").validate({
                rules: {
                    accountId: "required",
                    notes: "required"
                },

                messages: {
                    accountId: "Account is a required field.",
                    notes: "Notes is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#updateCashPaymentDetailForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#updateCashPaymentDetailModal').modal('hide');
                                    $('#updateCashPayment-detail').load('tabs/updateCashPayment-detail.php', {paymentId: $('input[id="generalPaymentId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/updateCashPayment-detail.php', { paymentId: <?php echo $paymentId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <!--<h4>Invoice No: <?php //echo $invoiceNo; ?></h4>-->
    
    <!--<a href="#addStockpileFreightModal" id="addStockpileFreight" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style=" margin-bottom: 5px;" /> Add Freight Cost</a>-->
    
    <div id="tabContent3">
        
    </div>
    
    <div id="updateCashPaymentDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="updateCashPaymentDetailModalLabel" aria-hidden="true">
        <form id="updateCashPaymentDetailForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeUpdateCashPaymentDetailModal">×</button>
                <h3 id="updateCashPaymentDetailModalLabel">Update Cash Payment Detail</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalPaymentId" id="modalPaymentId" value="<?php echo $paymentId; ?>" />
            <input type="hidden" name="action" id="action" value="update_cash_payment_detail" />
            <div class="modal-body" id="updateCashPaymentDetailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeUpdateCashPaymentDetailModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>data is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

