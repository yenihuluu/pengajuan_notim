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

$vendorHandlingId = '';

// If ID is in the parameter
if(isset($_POST['vendorHandlingId']) && $_POST['vendorHandlingId'] != '') {
    
    $vendorHandlingId = $_POST['vendorHandlingId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addVhBank').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addVhBankModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addVhBankModalForm').load('forms/vh-bank.php', {vendorHandlingId: $('input[id="generalVendorHandlingId"]').val()});
            });
            
            $("#addVhBankForm").validate({
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
                        data: $("#addVhBankForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addVhBankModal').modal('hide');
                                    $('#vh-bank-data').load('tabs/vh-bank-data.php', {vendorHandlingId: $('input[id="generalVendorHandlingId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/vh-bank.php', { vendorHandlingId: <?php echo $vendorHandlingId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addVhBankModal" id="addVhBank" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Bank</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addVhBankModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addVhBankModalLabel" aria-hidden="true">
        <form id="addVhBankForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddVhBankModal">×</button>
                <h3 id="addVhBankModalLabel">Add Bank</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalVendorHandlingId" id="modalVendorHandlingId" value="<?php echo $vendorHandlingId; ?>" />
            <input type="hidden" name="action" id="action" value="vh_bank_data" />
            <div class="modal-body" id="addVhBankModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddVhBankModal">Close</button>
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

