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

$vendorId = '';

// If ID is in the parameter
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    
    $generalVendorId = $_POST['vendorId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addGeneralVendorBank').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addGeneralVendorBankModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addGeneralVendorBankModalForm').load('forms/general-vendor-bank.php', {generalVendorId: $('input[id="generalVendorId"]').val()});
            });
            
            $("#addGeneralVendorBankForm").validate({
                rules: {
                    bankName: "required",
					branch: "required",
					accountNo: "required",
					beneficiary: "required",
					active : "required",
					customerType: "required",
					customerResiden: "required"
                },

                messages: {
                    stockpileId: "Bank Name is a required field.",
					branch: "Bracnh is a required field.",
					accountNo: "Account No is a required field.",
					beneficiary: "Beneficiary is a required field.",
					 active : "Status is a required field",
					customerType: "Customer Type is a required field",
					customerResiden: "Customer Residen is a required field"
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addGeneralVendorBankForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addGeneralVendorBankModal').modal('hide');
                                    $('#general-vendor-bank-data').load('tabs/general-vendor-bank-data.php', {vendorId: $('input[id="generalVendorId"]').val()});
                                    
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
        
            $('#tabContent3a').load('contents/general-vendor-bank.php', { generalVendorId: <?php echo $generalVendorId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3a').fadeIn();
        }
    </script>
    
    <a href="#addGeneralVendorBankModal" id="addGeneralVendorBank" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Bank</a>
    
    <div id="tabContent3a">
        
    </div>
    
    <div id="addGeneralVendorBankModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addGeneralVendorBankModalLabel" aria-hidden="true">
        <form id="addGeneralVendorBankForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddGeneralVendorBankModalx">×</button>
                <h3 id="addGeneralVendorBankModalLabel">Add Bank</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalGeneralVendorId" id="modalGeneralVendorId" value="<?php echo $generalVendorId; ?>" />
            <input type="hidden" name="action" id="action" value="general_vendor_bank_data" />
            <div class="modal-body" id="addGeneralVendorBankModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddGeneralVendorBankModal">Close</button>
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

