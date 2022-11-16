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

$date = new DateTime();

// <editor-fold defaultstate="collapsed" desc="Variable for Sales Data">

$salesId = '';
$salesNo = '';
$salesDate = $date->format('d/m/Y');
$salesType = 1;
$customerId = '';
$stockpileId = '';
$destination = '';
$notes = '';
$currencyId = '';
$exchangeRate = '';
$price = '';
$quantity = '';
$totalShipment = '';
$bkp_jkp = '';
$barang = '';
$peb_fp_no = '';
$boolEdit = true;

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['salesId']) && $_POST['salesId'] != '') {
    
    $salesId = $_POST['salesId'];
    
    
    
    // <editor-fold defaultstate="collapsed" desc="Query for Sales Data">
    
    $sql = "SELECT sl.*, sh.shipment_no, DATE_FORMAT(sl.sales_date, '%d/%m/%Y') AS sales_date2, DATE_FORMAT(sl.shipment_date, '%m/%Y') AS shipment_date2,  
			CASE WHEN DATE_FORMAT(sl.peb_fp_date, '%d/%m/%Y') = '00/00/0000' THEN ''
			ELSE DATE_FORMAT(sl.peb_fp_date, '%d/%m/%Y') END AS pebDate,
			DATE_FORMAT(sl.peb_fp_date, '%m/%Y') AS peb_fp_date2, sh.quantity AS qty_bl
            FROM sales sl LEFT JOIN shipment sh ON sl.sales_id = sh.sales_id
			LEFT JOIN account a ON sl.account_id = a.account_id
            WHERE sl.sales_id = {$salesId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$shipmentNo = $rowData->shipment_no;
        $salesNo = $rowData->sales_no;
        $salesDate = $rowData->sales_date2;
        $salesType = $rowData->sales_type;
        $customerId = $rowData->customer_id;
        $stockpileId = $rowData->stockpile_id;
		$accountId = $rowData->account_id;
        $destination = $rowData->destination;
        $notes = $rowData->notes;
        $currencyId = $rowData->currency_id;
        $price = $rowData->price;
        $quantity = $rowData->quantity;
		$qty_bl = $rowData->qty_bl;
        $exchangeRate = $rowData->exchange_rate;
        $totalShipment = $rowData->total_shipment;
		$shipmentDate = $rowData->shipment_date2;
		$bkp_jkp = $rowData->bkp_jkp;
		$barang = $rowData->barang;
		$peb_fp_no = $rowData->peb_fp_no;
		$peb_fp_date = $rowData->peb_fp_date;
		$pebDate = $rowData->pebDate;
		$salesStatus = $rowData->sales_status;
		$usedStatus = $rowData->used_status;
		$returnStatus = $rowData->return_status;
        $rsb = $rowData->rsb;
        $ggl = $rowData->ggl;

		
		if($usedStatus == 1){
		$readonlyProperty = ' readonly ';
		$boolEdit = false;
		}
    }
    
    // </editor-fold>
    
} else {
    if(isset($_SESSION['sales'])) {
		$shipmentNo = $_SESSION['sales']['shipmentNo'];
        $salesNo = $_SESSION['sales']['salesNo'];
        $salesDate = $_SESSION['sales']['salesDate'];
        $salesType = $_SESSION['sales']['salesType'];
        $customerId = $_SESSION['sales']['customerId'];
		$accountId = $_SESSION['sales']['accountId'];
        $stockpileId = $_SESSION['sales']['stockpileId'];
        $destination = $_SESSION['sales']['destination'];
        $notes = $_SESSION['sales']['notes'];
        $currencyId = $_SESSION['sales']['currencyId'];
        $exchangeRate = $_SESSION['sales']['exchangeRate'];
        $price = $_SESSION['sales']['price'];
        $quantity = $_SESSION['sales']['quantity'];
        $totalShipment = $_SESSION['sales']['totalShipment'];
		$shipmentDate = $_SESSION['sales']['shipmentDate'];
		$bkp_jkp = $_SESSION['sales']['bkp_jkp'];
		$barang = $_SESSION['sales']['barang'];
		$peb_fp_no = $_SESSION['sales']['peb_fp_no'];
		$pebDate = $_SESSION['sales']['pebDate'];
		
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
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $('#quantity').number(true, 2);
		$('#qty_bl').number(true, 2);
        
        $('#price').number(true, 10);
       
        if(document.getElementById('customerId').value == 'INSERT') {
            $('#customerDetail').show();
            $('#customerDetail2').show();
            $('#customerDetail3').show();
        } else {
            $('#customerDetail').hide();
            $('#customerDetail2').hide();
            $('#customerDetail3').hide();
        }
        
        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
        
//        $('#exchangeRate').number(true, 2);
        
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
		
		$('#salesType').change(function() {
            if(document.getElementById('salesType').value == 3) {
                $('#vendorLangsir').show();
				$('#stockpileLangsir').show();
            } else {
                $('#vendorLangsir').hide();
				$('#stockpileLangsir').hide();
            }
        });
        
        $('#customerId').change(function() {
            if(document.getElementById('customerId').value == 'INSERT') {
                $('#customerDetail').show();
                $('#customerDetail2').show();
                $('#customerDetail3').show();
            } else {
                $('#customerDetail').hide();
                $('#customerDetail2').hide();
                $('#customerDetail3').hide();
            }
        });
		
		$('#stockpileId').change(function() {
            
			if(document.getElementById('stockpileId').value != '') {
                
				setStockpileContract($('select[id="stockpileId"]').val());
			}else{
				
			}
        });
		
		 $('#UpdatePriceRate').click(function(e){
            
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addSalesModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addSalesModalForm').load('forms/priceRate-sales.php', {salesId: $('input[id="salesId"]').val()});
            });
			
		$('#cancelSales').click(function(e){
                  $.ajax({
                      url: './data_processing.php',
                      method: 'POST',
                      data: 'action=cancel_sales&sales_Id=<?php echo $rowData->sales_id; ?>',
                      success: function(data) {
                          var returnVal = data.split('|');

                          if (parseInt(returnVal[4]) != 0)	//if no errors
                          {
                              alertify.set({ labels: {
                                  ok     : "OK"
                              } });
                              alertify.alert(returnVal[2]);

                              if (returnVal[1] == 'OK') {
                                  $('#dataContent').load('contents/sales.php', {}, iAmACallbackFunction2);

                              }
                          }
                      }
                  });
              });
			  
		$('#returnSales').click(function(e){
			$.blockUI({ message: '<h4>Please wait...</h4>' }); 
                  $.ajax({
                      url: './data_processing.php',
                      method: 'POST',
                      data: 'action=return_sales&sales_Id=<?php echo $rowData->sales_id; ?>',
                      success: function(data) {
                          var returnVal = data.split('|');

                          if (parseInt(returnVal[4]) != 0)	//if no errors
                          {
                              alertify.set({ labels: {
                                  ok     : "OK"
                              } });
                              alertify.alert(returnVal[2]);

                              if (returnVal[1] == 'OK') {
                                  $('#dataContent').load('contents/sales.php', {}, iAmACallbackFunction2);

                              }
                          }
                      }
                  });
              });
        
        $("#salesDataForm").validate({
            rules: {
                shipmentNo: "required",
				salesNo: "required",
                salesDate: "required",
				shipmentDate: "required",
                salesType: "required",
                customerId: "required",
                stockpileId: "required",
                currencyId: "required",
                exchangeRate: "required",
                price: "required",
                quantity: "required"
            },
            messages: {
				shipmentNo: "Shipment Code is a required field.",
                salesNo: "Sales Agreement No. is a required field.",
                salesDate: "Sales Agreement Date is a required field.",
				shipmentDate: "Shipment Date is a required field.",
                salesType: "Type is a required field.",
                customerId: "Buyer is a required field.",
                stockpileId: "Loading is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                price: "Price/KG is a required field.",
                quantity: "Quantity (KG) is a required field."
            },
            submitHandler: function(form) {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#salesDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalSalesId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/sales.php', { salesId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
		
		 $("#addSalesForm").validate({
            rules: {
                priceUpdate: "required",
				exchangeRateUpdate: "required"
            },
            messages: {
				priceUpdate: "Price/KG is a required field.",
                exchangeRateUpdate: "Exchange Rate is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#addSalesForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalSalesId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/sales.php', { salesId: returnVal[3] }, iAmACallbackFunction2);
								$('#addSalesModal').modal('hide');
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
            startView: 0
        });
		$('.datepicker2').datepicker({
            minViewMode: 1,
            todayHighlight: true,
            autoclose: true,
            startView: 1
        });
    });
	
	function setStockpileContract(stockpileId) {

			$.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getStockpileContractShipment',
					stockpileId:stockpileId
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
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
                        document.getElementById('stockpileContractId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileContractId').options.add(x);

                        $("#stockpileContractId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
					
					var x = document.createElement('option');
                    x.value = 0;
                    x.text = 'NONE';
                    document.getElementById('stockpileContractId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }



                    
                }
				//setContract(contract);
            }

        });

		}
</script>

<form method="post" id="salesDataForm">
    <input type="hidden" name="action" id="action" value="sales_data" />
    <input type="hidden" name="salesId" id="salesId" value="<?php echo $salesId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Shipment Code<span style="color: red;">*</span></label>
            <input type="text" class="span6" <?php echo $readonlyProperty; ?> tabindex="1" id="shipmentNo" name="shipmentNo" value="<?php echo $shipmentNo; ?>" maxlength="50">
        </div>
        <div class="span4 lightblue">
           <label>Account <span style="color: red;">*</span></label>
            <?php
			if($boolEdit){
            createCombo("SELECT acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
                    FROM account acc where acc.account_type IN (0,1) and acc.account_no like '400%' ORDER BY acc.account_name ASC", $accountId, '', "accountId", "account_id", "account_full", 
                    "", 6, "select2combobox100", 1, "", false);
			}else{
			createCombo("SELECT acc.account_id, CONCAT(acc.account_no, ' - ', acc.account_name) AS account_full
				FROM account acc WHERE acc.account_id = {$accountId}", $accountId, '', "accountId", "account_id", "account_full", 
				"", 6, "select2combobox100", "", "", false);
			}
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Sales Agreement No. <span style="color: red;">*</span></label>
            <input type="text" class="span6" <?php echo $readonlyProperty; ?> tabindex="1" id="salesNo" name="salesNo" value="<?php echo $salesNo; ?>" maxlength="50">
        </div>
        <div class="span4 lightblue">
            <label>Buyer <span style="color: red;">*</span></label>
            <?php
			if($boolEdit){
            createCombo("SELECT cust.customer_id, cust.customer_name 
                    FROM customer cust ORDER BY cust.customer_name ASC", $customerId, '', "customerId", "customer_id", "customer_name", 
                    "", 6, "select2combobox100", 1, "", false);
			}else{
			createCombo("SELECT cust.customer_id, cust.customer_name 
                    FROM customer cust ORDER BY cust.customer_name ASC", $customerId, '', "customerId", "customer_id", "customer_name", 
                    "", 6, "select2combobox100", "", "", false);
			}
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid" id="customerDetail" style="display: none;">
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
            <label>Buyer Name <span style="color: red;">*</span></label>
            <input type="text" class="span10" tabindex="7" id="customerName" name="customerName" value="<?php if(isset($_SESSION['contract'])) { echo $_SESSION['sales']['customerName']; } ?>">
        </div>
        <div class="span4 lightblue">
            <label>Tax ID <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="8" id="npwp" name="npwp" value="<?php if(isset($_SESSION['contract'])) { echo $_SESSION['sales']['npwp']; } ?>">
        </div>
    </div>
    <div class="row-fluid" id="customerDetail2" style="margin-bottom: 7px; display: none;">
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
            <label>PPN <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 1", $_SESSION['sales']['ppn'], '', "ppn", "tax_value", "tax_name", 
                "", 9, "select2combobox75", 3);
            ?>
        </div>
        <div class="span4 lightblue">
            <label>PPh <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM tax WHERE tax_type = 2", $_SESSION['sales']['pph'], '', "pph", "tax_value", "tax_name", 
                "", 10, "select2combobox75", 3);
            ?>
        </div>
    </div>
    <div class="row-fluid" id="customerDetail3" style="display: none;">
        <div class="span4 lightblue">
        </div>
        <div class="span8 lightblue">
            <label>Customer Address <span style="color: red;">*</span></label>
            <textarea class="span12" rows="3" tabindex="11" id="customerAddress" name="customerAddress"><?php if(isset($_SESSION['contract'])) { echo $_SESSION['sales']['customerName']; } ?></textarea>
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Sales Agreement Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="salesDate" name="salesDate" value="<?php echo $salesDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		<div class="span3 lightblue">
            <label>Shipment Month <span style="color: red;">*</span></label>
            <input type="text" placeholder="MM/YYYY" tabindex="" id="shipmentDate" name="shipmentDate" value="<?php echo $shipmentDate; ?>" data-date-format="mm/yyyy" class="datepicker2" >
        </div>
        <div class="span2 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
			if($boolEdit){
            createCombo("SELECT cur.currency_id, cur.currency_code
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", 12, "select2combobox100","", "", false);
			}else{
			 createCombo("SELECT cur.currency_id, cur.currency_code
                    FROM currency cur
                    WHERE currency_id = {$currencyId}", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", 12, "select2combobox100","", "", false);
			}
            ?>
        </div>
        <div class="span2 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="13" <?php echo $readonlyProperty; ?> id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div>
        <div class="span2 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Commit' as info UNION
                    SELECT '2' as id, 'Direct' as info UNION
					SELECT '3' as id, 'Langsir' as info;", $salesType, '', "salesType", "id", "info", 
                "", 4, "select2combobox100");
            ?>
        </div>
		<div class="span2 lightblue" id="stockpileLangsir" style="display: none;">
            <label>Stockpile Tujuan <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileLangsir, "", "stockpileLangsir", "stockpile_id", "stockpile_full", 
                    "", 4, "select2combobox100");
            ?>
        </div>
		<div class="span4 lightblue" id="vendorLangsir" style="display: none;">
            <label>Vendor Langsir<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT vendor_id, vendor_name FROM vendor WHERE active = 1;", $vendorLangsir, '', "vendorLangsir", "vendor_id", "vendor_name", 
                "", 3, "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span12" <?php echo $readonlyProperty; ?> tabindex="14" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div class="span3 lightblue">
            <label>RSB <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'Yes' as info;", $rsb, '', "rsb", "id", "info", 
                    "", "", "select2combobox100", 3);
                ?>
        </div>
        <div class="span2 lightblue">
            <label>GGL <span style="color: red;">*</span></label>
                <?php
                createCombo("SELECT '1' as id, 'Yes' as info;", $ggl, '', "ggl", "id", "info", 
                    "", "", "select2combobox100", 3);
                ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Loading <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                    "", 4, "select2combobox75");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Quantity Prediction(KG) <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="15" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
        </div>
        <div class="span4 lightblue">
		<label>Quantity B/L(KG) <span style="color: red;">*</span></label>
            <input type="text" readonly class="span12" tabindex="15" id="qty_bl" name="qty_bl" value="<?php echo $qty_bl; ?>">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Destination</label>
            <input type="text" class="span12" tabindex="5" id="destination" name="destination" value="<?php echo $destination; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Total Shipment <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="16" id="totalShipment" name="totalShipment" value="<?php echo $totalShipment; ?>">
            <input type="hidden" name="oldTotalShipment" id="oldTotalShipment" value="<?php echo $totalShipment; ?>" />
        </div>
        <div class="span4 lightblue">
		<label>Contract No <span style="color: red;">(PILIH "NONE" JIKA TIDAK ADA)</span></label>
		 <?php
                createCombo("", "", "", "stockpileContractId", "stockpile_contract_id", "po_no",
                    "", 13, "select2combobox100", 2);
                ?>
        </div>
    </div>
	<div class="row-fluid">   
        <div class="span4 lightblue">
            <label>BKP / JKP</label>
            <?php
            createCombo("SELECT product_id, product_name FROM product", $bkp_jkp, "", "bkp_jkp", "product_id", "product_name", 
                    "", 4, "select2combobox75");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>PEB No</label>
            <input type="text" class="span12" tabindex="5" id="peb_fp_no" name="peb_fp_no" value="<?php echo $peb_fp_no; ?>">
        </div>
        <div class="span4 lightblue">
            <label>PEB Date <span style="color: red;">*</span></label>
           <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="pebDate" name="pebDate" value="<?php echo $pebDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>
	
    <div class="row-fluid">  
        <div class="span8 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="3" tabindex="20" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
			<?php
			if($salesId != '' && $salesStatus != 3){
			?>
			<button class="btn btn-warning" id="UpdatePriceRate">Update Price & Rate</button>
			<?php }
			?>
			
        </div>
    </div>
</form>
<?php
if($salesId != '' && $salesStatus == 0){
			?>
<div class="row-fluid">  
        <button class="btn btn-warning" id="cancelSales">Cancel Sales</button>
</div>
	<?php }?>
	<br>
<?php if($returnStatus == 0){?>
			
<div class="row-fluid">  
        <button class="btn btn-warning" id="returnSales">Return Sales</button>
	</div>
<?php }?>
<div id="addSalesModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addSalesModalLabel" aria-hidden="true">
        <form id="addSalesForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddSalesModal">×</button>
                <h3 id="addSalesModalLabel">Update Price & Rate</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           
            <input type="hidden" name="action" id="action" value="update_price_rate_sales" />
            <div class="modal-body" id="addSalesModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAddSalesModal">Close</button>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
