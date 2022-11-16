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

$userId = '';

// If ID is in the parameter
if(isset($_POST['userId']) && $_POST['userId'] != '') {
    
    $userId = $_POST['userId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addUserStockpile').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addUserStockpileModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addUserStockpileModalForm').load('forms/user-stockpile.php', {userId: $('input[id="generalUserId"]').val()});
            });
            
            $("#addUserStockpileForm").validate({
                rules: {
                    stockpileId: "required"
                },

                messages: {
                    stockpileId: "Stockpile is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addUserStockpileForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addUserStockpileModal').modal('hide');
                                    $('#user-stockpile-data').load('tabs/user-stockpile-data.php', {userId: $('input[id="generalUserId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/user-stockpile.php', { userId: <?php echo $userId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addUserStockpileModal" id="addUserStockpile" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Stockpile</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addUserStockpileModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addUserStockpileModalLabel" aria-hidden="true">
        <form id="addUserStockpileForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddUserStockpileModal">×</button>
                <h3 id="addUserStockpileModalLabel">Add Stockpile</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalUserId" id="modalUserId" value="<?php echo $userId; ?>" />
            <input type="hidden" name="action" id="action" value="user_stockpile_data" />
            <div class="modal-body" id="addUserStockpileModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddUserStockpileModal">Close</button>
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

