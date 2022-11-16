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

$logbookCategoryId = '';
$logbookSubCategoryId = '';

// If ID is in the parameter
if(isset($_POST['logbookCategoryId']) && $_POST['logbookCategoryId'] != '') {

    $logbookCategoryId = $_POST['logbookCategoryId'];

    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded

            $('#addLogbookSubCategory').click(function(e){

                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addLogbookSubCategoryModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addLogbookSubCategoryModalForm').load('forms/logbook-sub-category.php', {logbookCategoryId: $('input[id="generalLogbookCategoryId"]').val()});
            });

            $("#addLogbookSubCategoryForm").validate({
                rules: {
					name: "required",
                },

                messages: {
					name: "Sub Category is a required field.",

                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addLogbookSubCategoryForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addLogbookSubCategoryModal').modal('hide');
                                    $('#logbook-sub-category-data').load('tabs/logbook-sub-category-data.php', {logbookCategoryId: $('input[id="generalLogbookCategoryId"]').val()});

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

            $('#tabContent3').load('contents/logbook-sub-category.php', { logbookCategoryId: <?php echo $logbookCategoryId; ?> }, iAmACallbackFunction3);


        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>

    <a href="#addLogbookSubCategory" id="addLogbookSubCategory" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Sub Category</a>

    <div id="tabContent3">

    </div>

    <div id="addLogbookSubCategoryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addLogbookSubCategoryModalLabel" aria-hidden="true">
        <form id="addLogbookSubCategoryForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddLogbookSubCategoryModal">×</button>
                <h3 id="addLogbookSubCategoryModalLabel">Add Sub Category</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="action" id="action" value="logbook_sub_category_data" />
            <div class="modal-body" id="addLogbookSubCategoryModalForm">

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeLogbookSubCategoryModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <div id="EditLogbookSubCategoryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="EditLogbookSubCategoryModalLabel" aria-hidden="true">
        <form id="EditLogbookSubCategoryForm" method="post" style="margin: 0px;" action="./data_processing.php">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeEditLogbookSubCategoryModal">×</button>
                <h3 id="EditLogbookSubCategoryModalLabel">Edit Sub Category</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <div class="modal-body" id="EditLogbookSubCategoryModalForm">

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeEditLogbookSubCategoryModal">Close</button>
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

