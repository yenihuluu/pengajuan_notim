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
$quantity = '';

// If ID is in the parameter
if(isset($_POST['contractId']) && $_POST['contractId'] != '') {
    
    $contractId = $_POST['contractId'];
    
    $sql = "SELECT * FROM contract WHERE contract_id = {$contractId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $poNo = $row->po_no;
        $quantity = $row->quantity;
    }
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addContractStockpile').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addContractStockpileModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addContractStockpileModalForm').load('forms/contract-stockpile.php', {contractId: $('input[id="generalContractId"]').val()});
            });
            
            $("#addContractStockpileForm").validate({
                rules: {
                    stockpileId: "required",
                    quantity: "required"
                },

                messages: {
                    stockpileId: "Stockpile is a required field.",
                    quantity: "Quantity is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addContractStockpileForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addContractStockpileModal').modal('hide');
                                    $('#contract-stockpile-data').load('tabs/contract-stockpile-data.php', {contractId: $('input[id="generalContractId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/contract-stockpile.php', { contractId: <?php echo $contractId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <h4>PO No.: <?php echo $poNo; ?>, Quantity (KG): <?php echo number_format($quantity, 2, ".", ","); ?></h4>
    
    <a href="#addContractStockpileModal" id="addContractStockpile" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Stockpile</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addContractStockpileModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addContractStockpileModalLabel" aria-hidden="true">
        <form id="addContractStockpileForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddContractStockpileModal">×</button>
                <h3 id="addContractStockpileModalLabel">Add/Edit Stockpile</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalContractId" id="modalContractId" value="<?php echo $contractId; ?>" />
            <input type="hidden" name="action" id="action" value="contract_stockpile_data" />
            <div class="modal-body" id="addContractStockpileModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddContractStockpileModal">Close</button>
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

