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

$customerId = '';

// If ID is in the parameter
if(isset($_POST['customerId']) && $_POST['customerId'] != '') {
    
    $customerId = $_POST['customerId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addCBank').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addCBankModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addCBankModalForm').load('forms/c-bank.php', {customerId: $('input[id="generalCustomerId"]').val()});
            });
            
            $("#addCBankForm").validate({
                rules: {
                    bankName: "required",
					branch: "required",
					accountNo: "required",
					beneficiary: "required"
                },

                messages: {
                    stockpileId: "Bank Name is a required field.",
					branch: "Bracnh is a required field.",
					accountNo: "Account No is a required field.",
					beneficiary: "Beneficiary is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addCBankForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addCBankModal').modal('hide');
                                    $('#c-bank-data').load('tabs/c-bank-data.php', {customerId: $('input[id="generalCustomerId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/c-bank.php', { customerId: <?php echo $customerId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addCBankModal" id="addCBank" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Bank</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addCBankModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addCBankModalLabel" aria-hidden="true">
        <form id="addCBankForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeaddCBankModal">×</button>
                <h3 id="addCBankModalLabel">Add Bank</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalcustomerId" id="modalcustomerId" value="<?php echo $customerId; ?>" />
            <input type="hidden" name="action" id="action" value="c_bank_data" />
            <div class="modal-body" id="addCBankModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeaddCBankModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>User is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

