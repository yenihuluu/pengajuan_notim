<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';



$readonlyProperty = '';
$readonlyProperty2 = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

$po_pks_id = '';
//$contractType = '';
$contractNo = '';
$spbNo = '';
$vendorId = '';
$currencyId = '';
$price = '';
$quantity = '';
$exchangeRate = '';
//$contractSeq = '';
$notes = '';
$notes2 = '';
$contract = '';
$spb = '';
$finalStatus = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['po_pks_id']) && $_POST['po_pks_id'] != '') {

    $po_pks_id = $_POST['po_pks_id'];

    //$readonlyProperty2 = ' readonly ';
    //$disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT po.*
            FROM po_pks po
            WHERE po.po_pks_id = {$po_pks_id}
            ORDER BY po.po_pks_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        //$contractType = $rowData->contract_type;
        $contractNo = $rowData->contract_no;
        $vendorId = $rowData->vendor_id;
        $currencyId = $rowData->currency_id;
        $price = $rowData->price;
        $quantity = $rowData->quantity;
        $exchangeRate = $rowData->exchange_rate;
		$po_pks_id = $rowData->po_pks_id;
        $stockpileId = $rowData->stockpile_id;
		$notes = $rowData->notes;
    $notes2 = $rowData->notes2;
		$lockContract =  $rowData->lock_contract;
		$spbNo =  $rowData->spb_no;
		$contract = $rowData->contract;
		$spb = $rowData->spb;
    $finalStatus = $rowData->final_status;

		if($lockContract == 1){
			$readonlyProperty = ' readonly ';
		}
    }
    $sql2 = "SELECT c.`payment_status`
            FROM po_pks po
            LEFT JOIN po_contract poc ON po.`po_pks_id` = poc.`po_pks_id`
            LEFT JOIN contract c ON c.`contract_id` = poc.`contract_id`
            WHERE po.po_pks_id = {$po_pks_id}
            GROUP BY po.`po_pks_id`
            ORDER BY po.po_pks_id ASC
            ";
//            echo $sql;
    $resultData2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    if($resultData2 !== false && $resultData2->num_rows > 0) {
        $rowData2 = $resultData2->fetch_object();
        $paymentStatus = $rowData2->paymentStatus;

		if($paymentStatus == 1){
			$readonlyProperty2 = ' readonly ';
		}
    }
    // </editor-fold>

} else {
    //$generatedPoNo = "";
    if(isset($_SESSION['po_pks'])) {
       // $contractType = $_SESSION['contract']['contractType'];
        $spbNo = $_SESSION['contract']['spbNo'];
		      $contractNo = $_SESSION['contract']['contractNo'];
        $vendorId = $_SESSION['contract']['vendorId'];
        $currencyId = $_SESSION['contract']['currencyId'];
        $price = $_SESSION['contract']['price'];
        //$quantity = $_SESSION['contract']['quantity'];
        $exchangeRate = $_SESSION['contract']['exchangeRate'];
        $stockpileId = $_SESSION['contract']['stockpileId'];
        //$contractSeq = $_SESSION['contract']['contractSeq'];
		      $notes = $_SESSION['contract']['notes'];
          $notes2 = $_SESSION['contract']['notes2'];
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
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
        if(strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function(){

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $('#quantity').number(true, 2);

        $('#price').number(true, 10);

		$('#uploadFile').hide();
  setVendorPOPKS(1, 0);
	setStockpilePOPKS(1, 0);

        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }

        jQuery.validator.addMethod("indonesianDate", function(value, element) {
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });

        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });





	function resetVendorPOPKS() {
        document.getElementById('vendorId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('vendorId').options.add(x);

        $("#vendorId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }
	function setVendorPOPKS(type, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorPOPKS',
                    vendorId: vendorId
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('vendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorId').options.add(x);

                        $("#vendorId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorId').options.add(x);
                    }



                    if(type == 1) {
                        $('#vendorId').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#vendorId').prop('selectedIndex',i);

                                $("#vendorId").select2({
                                    width: "100%",
                                    placeholder: vendorId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

	function resetStockpilePOPKS() {
        document.getElementById('stockpileId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('stockpileId').options.add(x);

        $("#stockpileId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }
	function setStockpilePOPKS(type, stockpileId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getStockpilePOPKS',
                    stockpileId: stockpileId
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('stockpileId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileId').options.add(x);

                        $("#stockpileId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileId').options.add(x);
                    }



                    if(type == 1) {
                        $('#stockpileId').find('option').each(function(i,e){
                            if($(e).val() == stockpileId){
                                $('#stockpileId').prop('selectedIndex',i);

                                $("#stockpileId").select2({
                                    width: "100%",
                                    placeholder: stockpileId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

	    /*$("#PoDataForm").validate({
            rules: {
                //contractType: "required",
                vendorId: "required",
                currencyId: "required",
                exchangeRate: "required",
                price: "required",
                quantity: "required",
                stockpileId: "required",
				imagefile: "required"
            },
            messages: {
                //contractType: "Contract Type is a required field.",
                vendorId: "Vendor is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                price: "Price is a required field.",
                quantity: "Quantity is a required field.",
                stockpileId: "Stockpile is a required field.",
                imagefile: "imagefile is a required field."
            },
            submitHandler: function(form) {

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#PoDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);

                            if (returnVal[1] == 'OK') {
                                document.getElementById('po_pks_id').value = returnVal[3];

								//$('#dataContent').load('views/po-pks.php', { contractId: returnVal[3] }, iAmACallbackFunction2);
                               $('#dataContent').load('contents/po-pks.php', { po_pks_id: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            }
                        }
                    }
                });
            }
        });*/
		$('#PoDataForm').on('submit', function(e) {
            e.preventDefault();
			$.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#importButton').attr("disabled", true);
            $('#closeImportButton').attr("disabled", true);
			 $('#importModal').modal('hide');
                $('#dataContent').load('contents/po-pks.php', {}, iAmACallbackFunction2);

            //$.blockUI({ message: '<h4>Please wait...</h4>' });

            $(this).ajaxSubmit({
                success:  showResponse //call function after success
            });
        });
    });
function showResponse(responseText, statusText, xhr, $form)  {
        // for normal html responses, the first argument to the success callback
        // is the XMLHttpRequest object's responseText property

        // if the ajaxSubmit method was passed an Options Object with the dataType
        // property set to 'xml' then the first argument to the success callback
        // is the XMLHttpRequest object's responseXML property

        // if the ajaxSubmit method was passed an Options Object with the dataType
        // property set to 'json' then the first argument to the success callback
        // is the json data object returned by the server

//        alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
//            '\n\nThe output div should have already been updated with the responseText.');

        var returnVal = responseText.split('|');
//        alert(returnVal);
        if(parseInt(returnVal[3])!=0)	//if no errors
        {
//            alert(responseText);
            alertify.set({ labels: {
                ok     : "OK"
            } });
            alertify.alert(returnVal[2]);
            if(returnVal[1] == 'OK') {
                //show success message
                $('#importModal').modal('hide');
                $('#dataContent').load('contents/po-pks.php', {}, iAmACallbackFunction2);

//                document.getElementById('successMsg').innerHTML = returnVal[2];
//                $("#successMsg").show();

            } else {
                //show error message
//                document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
//                $("#modalErrorMsg").show();
                $('#importButton').attr("disabled", false);
                $('#closeImportButton').attr("disabled", false);
            }
        }

    }
</script>

<script type="text/javascript">

    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 2
        });
    });
</script>

<form method="post" enctype="multipart/form-data" id="PoDataForm" action="./data_processing.php">
    <input type="hidden" name="action" id="action" value="po_pks_mutasi_data" />
    <input type="hidden" name="po_pks_id" id="po_pks_id" value="<?php echo $po_pks_id; ?>" />
    <div class="row-fluid" style="margin-bottom: 7px;">
		<div class="span4 lightblue">
        <label>Contract No. <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="contractNo" name="contractNo" value="<?php echo $contractNo; ?>">
        </div>
    </div>
	<div class="row-fluid" style="margin-bottom: 7px;">
		<div class="span4 lightblue">
        <label>SPB No. <span style="color: red;">*</span></label>
            <input type="text" class="span12"  tabindex="" id="spbNo" name="spbNo" value="<?php echo $spbNo; ?>">
        </div>
    </div>

	<div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span4 lightblue">
        <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorId", "vendor_id", "vendor_full",
                    "", 2, "select2combobox100");
            ?>
        </div>

        <div class="span4 lightblue">
		<label>Stockpile<span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "stockpileId", "stockpile_id", "stockpile_full",
                    "", 2, "select2combobox100");
            ?>
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">


        <div class="span4 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $price; ?>">
        </div>
		<div class="span2 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code",
                    "", "", "select2combobox100");
            ?>
        </div>
        <div class="span2 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Quantity (KG) <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
        </div>

        <div class="span4 lightblue">
        </div>
    </div>

	<div class="row-fluid">
        <div class="span8 lightblue">
            <label>Notes <span style="color: red;"></span></label>
            <textarea class="span12" rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>

    </div>
	<div id="uploadFile">
       <label class="control-label" for="imagefile">Upload File (Acceptable format is PDF file)<span style="color: red;">*</span></label>
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="input-append">
                <div class="uneditable-input" style="min-width: 200px;">
                    <i class="icon-file fileupload-exists"></i>
                    <span class="fileupload-preview"></span>
                </div>
                <span class="btn btn-file">
                    <span class="fileupload-new">Select file</span>
                    <span class="fileupload-exists">Change</span>
                    <input type="file" name="imagefile" id="imagefile" />
                </span>
                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="importButton">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
