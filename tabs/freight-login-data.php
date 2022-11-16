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
$freightLoginId = '';

// If ID is in the parameter
if(isset($_POST['freightId']) && $_POST['freightId'] != '') {
    
    $freightId = $_POST['freightId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addFreightLogin').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addFreightLoginModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addFreightLoginModalForm').load('forms/freight-Login.php', {freightId: $('input[id="generalFreightId"]').val()});
            });
            
            $("#addFreightLoginForm").validate({
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
                        data: $("#addFreightLoginForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addFreightLoginModal').modal('hide');
                                    $('#freight-login-data').load('tabs/freight-login-data.php', {freightId: $('input[id="generalFreightId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/freight-login.php', { freightId: <?php echo $freightId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <a href="#addFreightLoginModal" id="addFreightLogin" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add User</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addFreightLoginModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addFreightLoginModalLabel" aria-hidden="true">
        <form id="addFreightLoginForm" method="post" style="margin: 0px;">
<!--            <input type="hidden" name="freightLoginId" id="freightLoginId" value="--><?php //echo $freightLoginId; ?><!--" />-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddFreightLoginModal">×</button>
                <h3 id="addFreightLoginModalLabel">Add User</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <!-- <input type="hidden" name="FreightId" id="FreightId" value="<?php echo $freightId; ?>" /> -->
            <input type="hidden" name="action" id="action" value="freight_login_data" />
            <div class="modal-body" id="addFreightLoginModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddFreightLoginModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <div id="EditFreightLoginModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="EditFreightLoginModalLabel" aria-hidden="true">
        <form id="EditFreightLoginForm" method="post" style="margin: 0px;" action="./data_processing.php">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeEditFreightLoginModal">×</button>
                <h3 id="EditFreightLoginModalLabel">Edit Freight Login</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <div class="modal-body" id="EditFreightLoginModalForm">

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeEditFreightLoginModal">Close</button>
                <button id="submitButton" class="btn btn-primary">Submit</button>
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

