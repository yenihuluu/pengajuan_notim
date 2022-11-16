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
		$lockContract =  $rowData->lock_contract;
		$spbNo =  $rowData->spb_no;
		
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
        $quantity = $_SESSION['contract']['quantity'];
        $exchangeRate = $_SESSION['contract']['exchangeRate'];
        $stockpileId = $_SESSION['contract']['stockpileId'];
        //$contractSeq = $_SESSION['contract']['contractSeq'];
		$notes = $_SESSION['contract']['notes'];
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
        
      /*  if(document.getElementById('vendorId').value == 'INSERT') {
            $('#vendorDetail').show();
            $('#vendorDetail2').show();
            $('#vendorDetail3').show();
        } else {
            $('#vendorDetail').hide();
            $('#vendorDetail2').hide();
            $('#vendorDetail3').hide();
        }
        */
		
		/*
        <?php
        if($generatedPoNo == "") {
        ?>
        if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                success: function(data) {
                    document.getElementById('generatedPoNo').value = data;
                }
            });
        } else {
            document.getElementById('generatedPoNo').value = "";
        }
        <?php
        }    
        ?>
        */
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
        
        <?php
        if($generatedPoNo == "") {
        ?>
     /*   $('#contractSeq').change(function() {
            if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });
        
        $('#contractType').change(function() {
            if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });*/
        <?php
        }
        ?>
        /*
        $('#vendorId').change(function() {
            if(document.getElementById('vendorId').value == 'INSERT') {
                $('#vendorDetail').show();
                $('#vendorDetail2').show();
                $('#vendorDetail3').show();
                
                document.getElementById('generatedPoNo').value = "";
            } else {
                $('#vendorDetail').hide();
                $('#vendorDetail2').hide();
                $('#vendorDetail3').hide();
                
                <?php
                if($generatedPoNo == "") {
                ?>
                if(document.getElementById('contractType').value != "" && document.getElementById('vendorId').value != "") {
                    $.ajax({
                        url: './get_data.php',
                        method: 'POST',
                        data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                        success: function(data) {
                            document.getElementById('generatedPoNo').value = data;
                        }
                    });
                } else {
                    document.getElementById('generatedPoNo').value = "";
                }
                <?php
                }
                ?>
            }
        });
        */
	    $("#PoDataForm").validate({
            rules: {
                //contractType: "required",
                vendorId: "required",
                currencyId: "required",
                exchangeRate: "required",
                price: "required",
                quantity: "required",
                stockpileId: "required"
            },
            messages: {
                //contractType: "Contract Type is a required field.",
                vendorId: "Vendor is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                price: "Price is a required field.",
                quantity: "Quantity is a required field.",
                stockpileId: "Stockpile is a required field."
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
        });
    });
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

<form method="post" id="PoDataForm">
    <input type="hidden" name="action" id="action" value="po_pks_data" />
    <input type="hidden" name="po_pks_id" id="po_pks_id" value="<?php echo $po_pks_id; ?>" />
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span4 lightblue">
           <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT v.vendor_id, CONCAT(v.vendor_name, ' - ', v.vendor_code) AS vendor_full
                        FROM vendor v WHERE v.active = 1 ORDER BY v.vendor_name", $vendorId, "", "vendorId", "vendor_id", "vendor_full", 
                "", "", "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
        <label>Contract No. <span style="color: red;">*</span></label>
            <input type="text" class="span12" <?php echo $readonlyProperty; ?> tabindex="" id="contractNo" name="contractNo" value="<?php echo $contractNo; ?>">
        </div>
        <div class="span4 lightblue">
		<label>SPB No. <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="spbNo" name="spbNo" value="<?php echo $spbNo; ?>">
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span4 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                    "", "", "select2combobox75");
            ?>
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
        <div class="span4 lightblue">
        </div>
    </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Quantity (KG) <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" <?php echo $readonlyProperty2; ?> id="price" name="price" value="<?php echo $price; ?>">
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
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
