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
            
            $('#addStockpileFreight').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addStockpileFreightModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addStockpileFreightModalForm').load('forms/stockpile-freight.php', {stockpileId: $('input[id="generalStockpileId"]').val()}); //inputan propertinya di panggil di sini
            });
            
            $("#addStockpileFreightForm").validate({
                rules: {
                    freightId: "required",
                    currencyId: "required",
                    price: "required",
                    vendorId: "required"
                },

                messages: {
                    freightId: "Freight is a required field.",
                    currencyId: "Currency is a required field.",
                    price: "Price/KG is a required field.",
                    vendorId: "Vendor is a required field."
                },

                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        data: $("#addStockpileFreightForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addStockpileFreightModal').modal('hide');
                                    $('#stockpile-freight-data').load('tabs/stockpile-freight-data.php', {stockpileId: $('input[id="generalStockpileId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/stockpile-freight.php', { stockpileId: <?php echo $stockpileId; ?> }, iAmACallbackFunction3);
            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
    
    <h4>Stockpile: <?php echo $stockpileName; ?></h4>
    
    <a href="#addStockpileFreightModal" id="addStockpileFreight" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Freight Cost</a>
    
    <div id="tabContent3">
        
    </div>
    
    <div id="addStockpileFreightModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addStockpileFreightModalLabel" aria-hidden="true">
        <form id="addStockpileFreightForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddStockpileFreightModal">×</button>
                <h3 id="addStockpileFreightModalLabel">Add/Edit Freight Cost</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modalStockpileId" id="modalStockpileId" value="<?php echo $stockpileId; ?>" />
            <input type="hidden" name="action" id="action" value="stockpile_freight_data" />
            <div class="modal-body" id="addStockpileFreightModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddStockpileFreightModal">Close</button>
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

