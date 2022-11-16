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

$freightId = '';
$freightCostId = '';

// If ID is in the parameter
if(isset($_POST['freightId']) && $_POST['freightId'] != '') {
    
    $freightId = $_POST['freightId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addFreightCost').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addFreightCostModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addFreightCostModalForm').load('forms/freight-cost-local-sales.php', {freightId: $('input[id="generalFreightId"]').val()});
            });
            
            $("#addFreightCostForm").validate({
                rules: {
                    freightId: "required",
                    masterGroupId: "required",
					username: "required",
					password: "required",
                },

                messages: {
                    freightId: "Freight is a required field.",
                    masterGroupId: "Group is a required field.",
					username: "Username is a required field.",
					password: "Password is a required field.",
				
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addFreightCostForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addFreightCostModal').modal('hide');
                                    $('#freight-cost-local-sales-data').load('tabs/freight-cost-local-sales-data.php', {freightId: $('input[id="generalFreightId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/freight-cost-local-sales.php', { freightId: <?php echo $freightId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addFreightCostModal" id="addFreightCost" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Freight Cost</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addFreightCostModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addFreightCostModalLabel" aria-hidden="true">
        <form id="addFreightCostForm" method="post" style="margin: 0px;">
<!--            <input type="hidden" name="freightCostId" id="freightCostId" value="--><?php //echo $freightCostId; ?><!--" />-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddFreightCostModal">×</button>
                <h3 id="addFreightCostModalLabel">Add Freight Cost</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <!-- <input type="hidden" name="FreightId" id="FreightId" value="<?php echo $freightId; ?>" /> -->
            <input type="hidden" name="action" id="action" value="freight_cost_local_sales_data" />
            <div class="modal-body" id="addFreightCostModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddFreightCostModal">Close</button>
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

