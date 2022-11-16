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

$contractId = '';

// If ID is in the parameter
if(isset($_POST['contractId']) && $_POST['contractId'] != '') {

    $contractId = $_POST['contractId'];

    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded

            $('#addContractPks').click(function(e){

                e.preventDefault();

                $("#modalErrorMsgB").hide();
                $('#addContractPksModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addContractPksModalForm').load('forms/contract-pks-detail.php', {contractId: $('input[id="generalContractId"]').val()});
            });
			
			

            $("#addContractPksForm").validate({
                rules: {
                    vendorCurahId: "required",
                },

                messages: {
                    vendorCurahId: "Vendor is a required field.",
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addContractPksForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addContractPksModal').modal('hide');
                                    $('#contract-pks-detail-data').load('tabs/contract-pks-detail-data.php', {contractId: $('input[id="generalContractId"]').val()});

                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                } else {
                                    //show error message
                                    document.getElementById('modalErrorMsgB').innerHTML = returnVal[2];
                                    $("#modalErrorMsgB").show();
                                }
                            }
                        }
                    });
                }
            });

            $('#tabContent3b').load('contents/contract-pks-detail.php', { contractId: <?php echo $contractId; ?> }, iAmACallbackFunction3b);


        });

        function iAmACallbackFunction3b() {
            $('#tabContent3b').fadeIn();
        }
    </script>
    <a href="#addContractPksModal" id="addContractPks" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Vendor</a>

    <div id="tabContent3b">

    </div>

    <div id="addContractPksModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addContractPksModalLabel" aria-hidden="true">
        <form id="addContractPksForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddContractPksModal">×</button>
                <h3 id="addContractPksModalLabel">Add/Edit Vendor Detail</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsgB" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalContractPksId" id="modalContractPksId" value="<?php echo $contractId; ?>" />
            <input type="hidden" name="action" id="action" value="contract_pks_detail_data" />
            <div class="modal-body" id="addContractPksModalForm">

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddContractPksModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
	
    <?php

} else {

    ?>

    <div class="alert fade in alert-error">
        <b>Error:</b><br/>Contract is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

