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
$poNo = '';

// If ID is in the parameter
if(isset($_POST['contractId']) && $_POST['contractId'] != '') {
    
    $contractId = $_POST['contractId'];
    
    $sql = "SELECT * FROM contract WHERE contract_id = {$contractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $poNo = $row->po_no;
    }
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addContractCondition').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsgA").hide();
                $('#addContractConditionModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addContractConditionModalForm').load('forms/contract-condition.php', {contractId: $('input[id="generalContractId"]').val()});
            });
            
            $("#addContractConditionForm").validate({
                rules: {
                    categoryId: "required",
                    rule: "required"
                },

                messages: {
                    categoryId: "Category is a required field.",
                    rule: "Rule is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addContractConditionForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addContractConditionModal').modal('hide');
                                    $('#contract-condition-data').load('tabs/contract-condition-data.php', {contractId: $('input[id="generalContractId"]').val()});
                                    
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                } else {
                                    //show error message
                                    document.getElementById('modalErrorMsgA').innerHTML = returnVal[2];
                                    $("#modalErrorMsgA").show();
                                }
                            }
                        }
                    });
                }
            });
        
            $('#tabContent3a').load('contents/contract-condition.php', { contractId: <?php echo $contractId; ?> }, iAmACallbackFunction3a);

            
        });

        function iAmACallbackFunction3a() {
            $('#tabContent3a').fadeIn();
        }
    </script>
    
    <h4>PO No.: <?php echo $poNo; ?></h4>
    
    <a href="#addContractConditionModal" id="addContractCondition" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Condition</a>
    
    <div id="tabContent3a">
        
    </div>
    
    <div id="addContractConditionModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addContractConditionModalLabel" aria-hidden="true">
        <form id="addContractConditionForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddContractConditionModal">×</button>
                <h3 id="addContractConditionModalLabel">Add/Edit Condition</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsgA" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalContractId" id="modalContractId" value="<?php echo $contractId; ?>" />
            <input type="hidden" name="action" id="action" value="contract_condition_data" />
            <div class="modal-body" id="addContractConditionModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddContractConditionModal">Close</button>
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

