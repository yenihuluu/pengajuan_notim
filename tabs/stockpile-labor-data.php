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

$stockpileId = '';
$stockpileName = '';

// If ID is in the parameter
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    
    $stockpileId = $_POST['stockpileId'];
    
    $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $stockpileName = $row->stockpile_name;
    }
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#addStockpileLabor').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addStockpileLaborModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addStockpileLaborModalForm').load('forms/stockpile-labor.php', {stockpileId: $('input[id="generalStockpileId"]').val()});
            });
            
            $("#addStockpileLaborForm").validate({
                rules: {
                    laborId: "required",
					status: "required"
                },

                messages: {
                    laborId: "Labor is a required field.",
					status: "Status is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addStockpileLaborForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addStockpileLaborModal').modal('hide');
                                    $('#stockpile-labor-data').load('tabs/stockpile-labor-data.php', {stockpileId: $('input[id="generalStockpileId"]').val()});
                                    
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
        
            $('#tabContent3a').load('contents/stockpile-labor.php', { stockpileId: <?php echo $stockpileId; ?> }, iAmACallbackFunction3a);

            
        });

        function iAmACallbackFunction3a() {
            $('#tabContent3a').fadeIn();
        }
    </script>
    
    <h4>Stockpile: <?php echo $stockpileName; ?></h4>
    
    <a href="#addStockpileLaborModal" id="addStockpileLabor" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Labor Stockpile</a>
    
    <div id="tabContent3a">
        
    </div>
    
    <div id="addStockpileLaborModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addStockpileLaborModalLabel" aria-hidden="true">
        <form id="addStockpileLaborForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddStockpileLaborModal">×</button>
                <h3 id="addStockpileLaborModalLabel">Add/Edit Labor Stockpile</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalStockpileId" id="modalStockpileId" value="<?php echo $stockpileId; ?>" />
            <input type="hidden" name="action" id="action" value="stockpile_labor_data" />
            <div class="modal-body" id="addStockpileLaborModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddStockpileLaborModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>Stockpile is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

