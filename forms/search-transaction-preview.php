<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$transactionId = $myDatabase->real_escape_string($_POST['temp_transactionId']);
$date = new DateTime();
$transactionDate = $date->format('d/m/Y');
$transactionDate2 = $date->format('d/m/Y');
$unloadingDate = $date->format('d/m/Y');
$transactionType = 1;
$allowUnloading = false;
$allowContract = false;
$allowFreight = false;
$allowSales = false;
$allowShipment = false;
$allowVendor = false;
$allowCustomer = false;
$allowLabor = false;
$allowSupplier = false;
$allowHandling = false;
$date_now = date('Y-m');
$readOnly = '';

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 7) {
            $allowUnloading = true;
        } elseif($row->module_id == 8) {
            $allowContract = true;
        } elseif($row->module_id == 9) {
            $allowFreight = true;
			$allowHandling = true;
        } elseif($row->module_id == 10) {
            $allowSales = true;
        } elseif($row->module_id == 11) {
            $allowVendor = true;
        } elseif($row->module_id == 12) {
            $allowCustomer = true;
        } elseif($row->module_id == 13) {
            $allowLabor = true;
        } elseif($row->module_id == 16) {
            $allowSupplier = true;
        } elseif($row->module_id == 21) {
            $allowEditNotim = true;
        }
    }
}

$sql = "SELECT sp.stockpile_name, sp.stockpile_id, sp.stockpile_code, sh.shipment_code, s.`sales_no`, s.sales_id, c.`customer_name`,
        DATE_FORMAT(tt.transaction_date, '%d/%m/%Y') AS transactionDate,
        DATE_FORMAT(sh.shipment_date, '%d/%m/%Y') AS shipmentDate,
        (SELECT user_name FROM USER WHERE user_id = tt.`entry_by`) AS entryBy, 
        CASE WHEN sum(td.qty_rsb) <> 0 THEN 1 ELSE 0 END AS rsb1,
        CASE WHEN sum(td.qty_ggl) <> 0 THEN 1 ELSE 0 END AS ggl1,
        CASE WHEN sum(td.qty_rsb_ggl) <> 0 THEN 1 ELSE 0 END AS rsb_ggl1, 
        CASE WHEN sum(td.qty_uncertified) <> 0 THEN 1 ELSE 0 END AS uncertified1, 
        format(sum(td.qty_rsb),2) as qtyRSB, format(sum(td.qty_ggl),2) as qtyGGL, 
        format(sum(td.qty_rsb_ggl),2) as qtyRG,
        format(sum(td.qty_uncertified),2) as qtyUN, 
        (sum(td.qty_rsb) + sum(td.qty_ggl) + sum(td.qty_rsb_ggl) + sum(td.qty_uncertified)) AS sendWeight2,
         td.quantity AS sendWeight1, s.quantity AS salesQuantity,
        CASE WHEN tt.transaction_type = 2 THEN 'OUT' ELSE NULL END AS transactionType_text, 
        tt.* FROM temp_transaction tt
        LEFT JOIN shipment sh ON sh.`shipment_id` = tt.`shipment_id`
        LEFT JOIN sales s ON s.`sales_id` = sh.`sales_id`
        LEFT JOIN customer c ON c.`customer_id` = s.`customer_id`
        LEFT JOIN stockpile sp ON sp.stockpile_id = s.stockpile_id
        LEFT JOIN temp_delivery td ON td.temp_transaction_id = tt.temp_transaction_id 
        WHERE tt.temp_transaction_id = {$transactionId}
        GROUP BY tt.temp_transaction_id";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows == 1) {
    $row = $result->fetch_object();
    $stockpileId = $row->stockpile_id;
    $stockpile_text = $row->stockpile_name;
    $stockpile_code = $row->stockpile_code;
    $transactionType = $row->transaction_type;
    $transactionType_text = $row->transactionType_text;
    $transactionDate2 = $row->transactionDate;
    $customerId = $row->customer_id;
    $customer_text = $row->customer_name;
    $vehicleNo2 = $row->vehicle_no;
    $salesId = $row->sales_id;
    $sales_text = $row->sales_no;
    $shipmentId = $row->shipment_id;
    $shipment_text = $row->shipment_code;
    $shipmentDate = $row->shipmentDate;

    $ggl = $row->ggl1;
    $rsb = $row->rsb1;
    $rsb_ggl = $row->rsb_ggl1;
    $uncertified =$row->uncertified1;

    $cancelRemarks = $row->cancel_remarks;

    $qtyRG = $row->qtyRG;
    $qtyGGL = $row->qtyGGL;
    $qtyRSB =$row->qtyRSB;
    $qtyUN =$row->qtyUN;
    $sendWeight2 =  number_format($row->sendWeight2, 2, ".", ",");
    $qtyAgreed = number_format($row->salesQuantity, 2, ".", ","); 

    // if($row->rsb == 0 & $row->ggl == 0){
    //    
    // }else{
    //     $sendWeight2 =  number_format($row->sendWeight2, 2, ".", ",");
    // }

    $rsb_text = $row->rsbText;
    $ggl_text = $row->gglText;
    $blWeight = number_format($row->quantity, 2, ".", ",");
    $quantityAvailable = $row->quantity;
    $notes2 = $row->notes;	
    $status3 = $row->status;
    $sertifictionType = $row->sertifictionType;
    
    if($row->status == 2){
        $disableProperty = 'disabled';
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT  style='width: 150px;' class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == 'NONE') {
            echo "<option value='NONE' selected>NONE</option>";
        } else {
            echo "<option value='NONE'>NONE</option>";
        }
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    
    $(document).ready(function(){	//executed after the page has loaded
        $('.combobox').combobox();
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });

        $('#availabel_rsb').number(true, 2);
        $('#availabel_ggl').number(true, 2);
        $('#availabel_uncertified').number(true, 2);
        $('#availabel_rsb_ggl').number(true, 2);

        getAvailableQtyAll(<?php echo $stockpileId ?>, $('input[id="transactionDate2"]').val()); 


        $('#rsb_ggl1').attr("readonly", true);
        $('#rsb1').attr("readonly", true);
        $('#ggl1').attr("readonly", true);
        $('#uncertified1').attr("readonly", true);




        $("#transactionDataForm").validate({
            rules: {
                stockpileId: "required",
                transactionType: "required",
                vehicleNo: "required",
                transactionDate2: "required",
                sendWeight2: "required",
                blWeight: "required",
                vehicleNo2: "required",
                shipmentId: "required"
            },
            messages: {
                stockpileId: "Stockpile is a required field.",
                transactionType: "Type is a required field.",
                vehicleNo: "Vehicle No. is a required field.",
                transactionDate2: "Transaction Date is a required field.",
                sendWeight2: "Stockpile Weight is a required field.",
                blWeight: "BL Weight is a required field.",
                vehicleNo2: "Vessel Name is a required field.",
                shipmentId: "Sales Agreement No. is a required field."
            },
            submitHandler: function(form) {
                $('#submitButton').attr("disabled", true);
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    _method: 'INSERT',
                    data: $("#transactionDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[3]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                $('#pageContent').load('views/transaction_preview.php', {}, iAmACallbackFunction);
                            } 
                            $('#submitButton').attr("disabled", false);
                        }
                    }
                });
            }
        });
        
    });
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/transaction_preview.php', {}, iAmACallbackFunction);
    }

    
    function canceled() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'transaction_data',
                _method: 'CANCEL',
                tempTransactionId: document.getElementById('tempTransactionId').value,
                shipmentId: document.getElementById('shipmentId').value,
                 reject_remarks: document.getElementById('reject_remarks').value
            },
            success: function (data) {
                console.log(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[3]) != 0)	//if no errors
                {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert(returnVal[2]);

                    // if (returnVal[1] == 'OK') {
                        $('#pageContent').load('views/transaction_preview.php', {}, iAmACallbackFunction);
                  //  }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }
    
    // function getAvailableQty_RSB(stockpileId) {
    //     $.ajax({
    //        url: 'get_data.php',
    //        method: 'POST',
    //        data: { action: 'getAvailableQty_RSB',
    //             stockpileId: stockpileId
    //         },
    //        success: function(data){
    //            var returnVal = data.split('|');
    //            if(parseInt(returnVal[0])!=0)	//if no errors
    //             {
    //                document.getElementById('availabel_rsb').innerHTML = returnVal[1];
    //                 //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
    //            }
    //        }
    //    });
    // }

    // function getAvailableQty_ggl(stockpileId) {
    //     $.ajax({
    //        url: 'get_data.php',
    //        method: 'POST',
    //        data: { action: 'getAvailableQty_ggl',
    //             stockpileId: stockpileId
    //         },
    //        success: function(data){
    //            var returnVal = data.split('|');
    //            if(parseInt(returnVal[0])!=0)	//if no errors
    //             {
    //                document.getElementById('availabel_ggl').innerHTML = returnVal[1];
    //                 //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
    //            }
    //        }
    //    });
    // }

    // function getAvailableQty_RG(stockpileId) {
    //     $.ajax({
    //        url: 'get_data.php',
    //        method: 'POST',
    //        data: { action: 'getAvailableQty_RG',
    //             stockpileId: stockpileId
    //         },
    //        success: function(data){
    //            var returnVal = data.split('|');
    //            if(parseInt(returnVal[0])!=0)	//if no errors
    //             {
    //                document.getElementById('availabel_rsb_ggl').innerHTML = returnVal[1];
    //                 //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
    //            }
    //        }
    //    });
    // }

    // function getAvailableQty_uncertified(stockpileId) {
    //     $.ajax({
    //        url: 'get_data.php',
    //        method: 'POST',
    //        data: { action: 'getAvailableQty_uncertified',
    //             stockpileId: stockpileId
    //         },
    //        success: function(data){
    //            var returnVal = data.split('|');
    //            if(parseInt(returnVal[0])!=0)	//if no errors
    //             {
    //                document.getElementById('availabel_uncertified').innerHTML = returnVal[1];
    //                 //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
    //            }
    //        }
    //    });
    // }

    function getAvailableQtyAll(stockpileId, transactionDate) {
        $.ajax({
           url: 'get_data.php',
           method: 'POST',
           data: { action: 'getAvailableQtyAll',
                stockpileId: stockpileId,
                transactionDate: transactionDate
            },
           success: function(data){
               var returnVal = data.split('|');
               if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    document.getElementById('availabel_rsb').innerHTML = returnVal[1];
                   document.getElementById('availabel_ggl').innerHTML = returnVal[2];
                   document.getElementById('availabel_rsb_ggl').innerHTML = returnVal[3];
                   document.getElementById('availabel_uncertified').innerHTML = returnVal[4];
                    //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
               }
           }
       });
    }


</script>

<!--<h4>Transaction Form</h4>-->
<form method="post" action="reports/temp-traceability-report-xls.php">
    <input type="hidden" id="shipmentId1" name="shipmentId1" value="<?php echo $shipmentId; ?>" />
    <input type="hidden" id="salesId1" name="salesId1" value="<?php echo $salesId; ?>" />
    <input type="hidden" id="shipmentCode" name="shipmentCode" value="<?php echo $shipment_text   ; ?>" />
    <input type="hidden"  id="shipmentDate" name="shipmentDate" value="<?php echo $shipmentDate; ?>" >
    <input type="hidden"  id="stockpileCode" name="stockpileCode" value="<?php echo $stockpile_code; ?>" >
    <input type="hidden"  id="stockpileName" name="stockpileName" value="<?php echo $stockpile_text; ?>" >
    <input type="hidden"  id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" >


    <input type="hidden"  id="rsb" name="rsb" value="<?php echo $rsb; ?>" >
    <input type="hidden"  id="ggl" name="ggl" value="<?php echo $ggl; ?>" > 
    <input type="hidden"  id="rsb_ggl" name="rsb_ggl" value="<?php echo $rsb_ggl; ?>" > 
    <input type="hidden"  id="uncertified" name="uncertified" value="<?php echo $uncertified; ?>" > 
    <button class="btn btn-success" <?php echo $disableProperty; ?>>Download XLS</button>
</form>

<form method="post" id="transactionDataForm">
    <input type="hidden" name="action" id="action" value="transaction_data" />
    <input type="hidden" name="_method" value="INSERT">
    <input type="hidden" name="tempTransactionId" id="tempTransactionId" value="<?php echo $transactionId; ?>" />
    <input type="hidden" placeholder="DD/MM/YYYY" id="transactionDate" name="transactionDate" value="<?php echo $transactionDate2; ?>" >
    
    <?php if($status3 == 2){ ?>
        <span style="color: red;"><center><b>CANCELED</center></b></span> 
    <?php } ?>
    <hr>
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span2 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
                <input type="text" readonly name="stockpile_text" id="stockpile_text" value="<?php echo $stockpile_text ?>"/>
                <input type="hidden" name="stockpileId" id="stockpileId" value="<?php echo $stockpileId ?>"/>
        </div>
        <div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" readonly name="transactionType_text" id="transactionType_text" value="<?php echo $transactionType_text ?>"/>
            <input type="hidden" name="transactionType" id="transactionType" value="<?php echo $transactionType ?>"/>
        </div>
    </div>
    
    <div class="row-fluid">   
        <div class="span2 lightblue">
            <label>Transaction Date <span style="color: red;">*</span></label>
        </div>
            <div class="span4 lightblue">
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="2" id="transactionDate2" name="transactionDate2" value="<?php echo $transactionDate2 ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
            <div class="span2 lightblue">
                <label>Buyer</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly name="customer_name" id="customer_name" value="<?php echo $customer_text ?>"/>
                <input type="hidden" name="customerId" id="customerId" value="<?php echo $customerId ?>"/>
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Vessel Name <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="3" id="vehicleNo2" name="vehicleNo2" value="<?php echo $vehicleNo2 ?>">
            </div>
            <div class="span2 lightblue">
                <label>Sales Agreement No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly name="sales_text" id="sales_text" value="<?php echo $sales_text ?>"/>
                <input type="hidden" name="salesId" id="salesId" value="<?php echo $salesId ?>"/>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Stockpile Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="4" id="sendWeight2" name="sendWeight2" value="<?php echo $sendWeight2 ?>">
            </div>
            <div class="span2 lightblue">
                <label>Shipment Code <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="4" id="shipment_text" name="shipment_text" value="<?php echo $shipment_text ?>">
                <input type="hidden" class="span12" tabindex="4" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId ?>">

            </div>
        </div>

           <!-- RSB -->
           <div class="row-fluid">
            <div class="span2 lightblue">
                <label >Available Qty<span style="color: red;">*</span></label>
            </div>
            <div class="span2 lightblue">
                <label style="color: blue;">RSB : <span id="availabel_rsb"  style="color: blue;"></span></label>
            </div>
            <div class="span2 lightblue">
                <label style="color: blue;">GGL : <span id="availabel_ggl" style="color: blue;" ></span></label>
            </div>

            <div class="span2 lightblue">
                <label>RSB <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                    createCombo("SELECT 0 as id, 'NONE' as info UNION
                                SELECT 1 as id, 'YES' as info;", $rsb, '', "rsb1", "id", "info", 
                        "", 6);
                ?>
            </div>
            <div  class="span2 lightblue">
                <input type="text" class="span12" readonly tabindex="5" id="qty_rsb" name="qty_rsb" value="<?php echo $qtyRSB ?>">
            </div>
        </div>
        
        <!-- GGL -->
        <div class="row-fluid">
            <div class="span2 lightblue">
               
            </div>
            <div class="span2 lightblue">
                <label style="color: blue;">Uncertified : <span id="availabel_uncertified" style="color: blue;"></span></label>
            </div>
            <div class="span2 lightblue">
                <label style="color: blue;">RSB-GGL : <span id="availabel_rsb_ggl" style="color: blue;"></span></label>
            </div>
            <div class="span2 lightblue">
                <label>GGL <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                createCombo("SELECT '0' as id, 'NONE' as info UNION
                             SELECT '1' as id, 'YES' as info;", $ggl, '', "ggl1", "id", "info", 
                    "", 6);
                ?>

            </div>
            <div  class="span2 lightblue">
                <input type="text" class="span12" readonly tabindex="5" id="qty_ggl" name="qty_ggl" value="<?php echo $qtyGGL ?>">
            </div>
        </div>

          <!-- RSB-GGL -->
          <div class="row-fluid">
            <div class="span2 lightblue">
               
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>RSB & GGL <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                createCombo("SELECT '0' as id, 'NONE' as info UNION
                             SELECT '1' as id, 'YES' as info;", $rsb_ggl, '', "rsb_ggl1", "id", "info", 
                    "", 6);
                ?>
            </div>
            <div  class="span2 lightblue">
                <input type="text" class="span12" readonly tabindex="5" id="qty_RG" name="qty_RG" value="<?php echo $qtyRG ?>">
            </div>
        </div>

        <!-- UNCERTIFIED -->
        <div class="row-fluid">
            <div class="span2 lightblue">
               
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>Uncertified <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                createCombo("SELECT '0' as id, 'NONE' as info UNION
                             SELECT '1' as id, 'YES' as info;", $uncertified, '', "uncertified1", "id", "info", 
                    "", 6);
                ?>
            </div>
            <div  class="span2 lightblue">
                <input type="text" class="span12" readonly tabindex="5" id="qty_uncertified" name="qty_uncertified" value="<?php echo $qtyUN ?>">
            </div>
        </div>


        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>B/L Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="5" id="blWeight" name="blWeight" value="<?php echo $blWeight; ?>">
            </div>
            <div class="span2 lightblue">
                <label>Quantity Agreed (KG)</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="14" id="quantityAvailable" name="quantityAvailable" value = "<?php echo $qtyAgreed ?>">
				<input type="hidden" readonly class="span12" tabindex="14" id="idSuratTugas" name="idSuratTugas">
            </div>
        </div>
		<div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" readonly rows="3" tabindex="40" id="notes2" name="notes2"><?php echo $notes2 ?></textarea>
            </div>
        </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Accept</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Remarks</label>
        <textarea class="span12" <?php echo $disableProperty; ?> rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $cancelRemarks; ?></textarea>
    </div>

</div>

<div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger"  <?php echo $disableProperty; ?> id="canceled" onclick= "canceled()" style="margin: 0px;">Cancel</button>
            <!-- <button class="btn btn-danger" onclick="cancelPG()">CANCEL</button> -->
        </div>
</div> 

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" >
    <form id="insertForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="insertModalLabel">Insert New</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="insertModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
