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
if(isset($_POST['codeCosting_id']) && $_POST['codeCosting_id'] != '') {
    
    $codeCosting_id = $_POST['codeCosting_id'];
    
    $sql = "SELECT sp.stockpile_name FROM header_costing hc INNER JOIN stockpile sp ON sp.stockpile_id = hc.loading_port  WHERE hc.header_costing_id = {$codeCosting_id}";
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
            
            $('#addDetailCosting').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addDetailCostingModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailCostingModalForm').load('forms/detail-costing-modal.php', {codeCosting_id: $('input[id="generalCodeCostingId"]').val()}); //FORM INPUTAN data saat modal Muncul
            });
            
            $("#addDetailCostingForm").validate({
                rules: {
                    stockpileId: "required",
                    generalVendor: "required",
                    maxCharge: "required",
                    maxChargeP: "required",
                    priceType: "required",
                    uom1: "required",
                    cost: "required",
                    currency: "required",
                    exchangeRate: "required",
                    priceMT: "required",
                    qtyType: "required",
                    accountName: "required"
                },
                messages: {
                    stockpileId: "Stockpile Code is a required field.",
                    generalVendor: "General Vendor is a required field.",
                    maxCharge: "required field.",
                    maxChargeP: "required field.",
                    priceType: "Price Type is a required field.",
                    uom1: "UOM is a required field.",
                    cost: "Biaya is a required field.",
                    currency: "Currency is a required field.",
                    exchangeRate: "Exchange Rate is a required field.",
                    priceMT: "Price / MT is a required field.",
                    qtyType: "Qty Type is a required field.",
                    accountName: "account Type is a required field."
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: './data_processing.php',
                        method: 'POST',
                        _method: 'INSERT',
                        data: $("#addDetailCostingForm").serialize(),
                        success: function(data){
                            var returnVal = data.split('|');
    //                        alert(data);
                            if(parseInt(returnVal[3])!=0)	//if no errors
                            {
                                //alert(msg);
                                if(returnVal[1] == 'OK') {
                                    //show success message
                                    $('#addDetailCostingModal').modal('hide');
                                    $('#detail_costing_tabs').load('tabs/detail_costing_tabs.php', {codeCosting_id: $('input[id="generalCodeCostingId"]').val()});
                                    
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
        
            $('#tabContent3').load('contents/job-costing-contents.php', { codeCosting_id: <?php echo $codeCosting_id; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }

        function back() {
            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#pageContent').load('views/job-costing-views.php', {}, iAmACallbackFunction);
        }
    </script>
    
    <h4>Loading port : <?php echo $stockpileName; ?></h4>
    
  
    <button class="btn" type="button" onclick="back()">Back</button>
    <!-- <button type="button" class="btn btn-light"><a href="#" role="button" onclick="back()" style="color: black;"> Back</a> </button>  -->
    <button type="button" class="btn btn-primary"> <a href="#addDetailCostingModal" id="addDetailCosting" role="button" style="color: white;"> Add Detail Costing</a> </button>
    
    <div id="tabContent3">
        
    </div>
    
    <!-- CALL MODAL -->
    <div id="addDetailCostingModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addDetailCostingModalLabel"
         aria-hidden="true" style="width:1200px; height:600px; margin-left:-600px;" data-keyboard="false" data-backdrop="static">
        <form id="addDetailCostingForm" method="post" style="margin: 0px;"> <!-- Penghubung ke data_procesing nya -->
            <input type="hidden" name="_method" id="_method" value="INSERT">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddDetailCostingModal">×</button>
                <h3 id="addDetailCostingModalLabel">Add/Edit Detail Costing</h3> <!-- title saat modal kebuka -->
              

            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
            <input type="hidden" name="modaldetailCosting" id="modaldetailCosting" value="<?php echo $codeCosting_id; ?>" />
            <input type="hidden" name="action" id="action" value="job_costing_data" /> <!-- dipanggil di data_processing.php -->
            <div class="modal-body" id="addDetailCostingModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddDetailCostingModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <?php
    
} else {

    ?>
    
    <div class="alert fade in alert-error">
        <b>Error:</b><br/>Costing Detail is not exist!
    </div>

    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

