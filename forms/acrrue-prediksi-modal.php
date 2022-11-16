<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Vendor Data">


$prediksiId = '';
$qtyVessel  = 0;
$status = '';
$headerCostingID = '';


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['prediksiId']) && $_POST['prediksiId'] != '') {
    $prediksiId = $_POST['prediksiId'];
}

if(isset($_POST['qtyVessel']) && $_POST['qtyVessel'] != '') {
    $qtyVessel = $_POST['qtyVessel'];
}

if(isset($_POST['exRateCosting']) && $_POST['exRateCosting'] != '') {
    $exRateCosting = $_POST['exRateCosting'];
}

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
}

if(isset($_POST['headerCostingID']) && $_POST['headerCostingID'] != '') {
    $headerCostingID = $_POST['headerCostingID'];
}

if(isset($_POST['kodeprediksi']) && $_POST['kodeprediksi'] != '') {
    $kodeprediksi = $_POST['kodeprediksi'];
}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    $(document).ready(function(){
        resetaddNewData();
        addNewData($('input[id="headerCostingID"]').val(), $('input[id="stockpileId"]').val(), $('input[id="qtyVessel"]').val(),  $('input[id="prediksiId"]').val(), $('input[id="exRateCosting"]').val(),  $('input[id="kodeprediksi"]').val());
    });

    function resetaddNewData(text) {
        document.getElementById('mstcosting1').innerHTML = '';
    }

    function addNewData(headerCostingID, stockpileId, qtyVessel, prediksiId, exRateCosting, kodeprediksi) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'addNewData',
                    headerCostingID: headerCostingID,
                    stockpileId: stockpileId,
                    qtyVessel: qtyVessel,
                    prediksiId: prediksiId,
                    exRateCosting: exRateCosting,
                    kodeprediksi: kodeprediksi
            },
            success: function(data){
                if(data != '') {
                    $('#divmstcosting1').show();
                    document.getElementById('mstcosting1').innerHTML = data;
                }
            }   
        });
    }

    function checkCostingModal() {
        $('.checkA').change(function (e) {
            document.getElementById('checkB' + this.value).disabled = this.checked;
        });

    }

    function checkAllModal(all)
    {

            var tempCheck = document.getElementsByName("checkedCostingModal[id][]");
            var count = tempCheck.length;
            if (all.checked) {
                for (a=0;a<count;a++)
                {
                    tempCheck[a].checked=true;
                }
            }else{
                for (a=0;a<count;a++)
                {
                    tempCheck[a].checked=false;
                }
            }
      
    }
</script>

<input type="hidden" id="action" name="action" value="add_new_prediksi_data">
    <input type="hidden" name="headerCostingID" id="headerCostingID" value="<?php echo $headerCostingID; ?>" />
    <input type="hidden" name="stockpileId" id="stockpileId" value="<?php echo $stockpileId; ?>" />
    <input type="hidden" name="qtyVessel" id="qtyVessel" value="<?php echo $qtyVessel; ?>" />
    <input type="hidden" name="exRateCosting" id="exRateCosting" value="<?php echo $exRateCosting; ?>" />
    <input type="hidden" name="kodeprediksi" id="kodeprediksi" value="<?php echo $kodeprediksi; ?>" />

    <input type="hidden" name="status" id="status" value="1" />
    <input type="hidden" name="prediksiId" id="prediksiId" value="<?php echo $prediksiId; ?>" />

    <div class="row-fluid" id = "divmstcosting1" style="display: none;">  
        <h4>Detail Costing</h4>
        <div class="row-fluid" id="mstcosting1">
            detail costing
        </div>
    </div>


