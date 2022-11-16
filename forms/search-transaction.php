<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$transactionId = $myDatabase->real_escape_string($_POST['transactionId']);
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

$sql = "SELECT t.*, DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date2, 
            DATE_FORMAT(t.unloading_date, '%d/%m/%Y') AS unloading_date2, 
            DATE_FORMAT(t.loading_date, '%d/%m/%Y') AS loading_date2,
			DATE_FORMAT(transaction_date, '%Y-%m') AS edit_date,
		    CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transactionType2,
            CASE WHEN t.transaction_type = 1 THEN sc.stockpile_id ELSE sl.stockpile_id END AS stockpile_id, 
            con.vendor_id, t.vendor_id AS supplier_id, sl.sales_id, sl.customer_id, sh.payment_id AS sh_payment_id,
			sl.sales_no,sh.shipment_no, cust.customer_name,
			FORMAT(sl.quantity - (SELECT COALESCE(SUM(quantity), 0) FROM shipment WHERE sales_id = sl.sales_id), 2) AS quantity_available,
            format(sum(d.qty_rsb),2) as qtyRSB, 
            format(sum(d.qty_ggl),2) as qtyGGL,
            format(sum(d.qty_rsb_ggl),2) as qtyRG,
            format(sum(d.qty_uncertified),2) as qtyUN, 
            -- format(sum(d.qty_uncertified),2) AS qtyUncertified,
            (sum(d.qty_rsb) + sum(d.qty_ggl) + sum(d.qty_rsb_ggl) +  sum(d.qty_uncertified)) AS sendWeight2
        FROM transaction t 
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN delivery d 
            ON d.shipment_id = t.shipment_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = t.shipment_id
        LEFT JOIN sales sl
            ON sl.sales_id = sh.sales_id
		LEFT JOIN customer cust 
			ON cust.customer_id = sl.customer_id
        WHERE t.transaction_id = {$transactionId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;
if($result->num_rows == 1) {
    $row = $result->fetch_object();
    $stockpileId = $row->stockpile_id;
	$slipNo = $row->slip_no;
    $transactionType = $row->transaction_type;
	$transactionType2 = $row->transactionType2;
    $unloadingDate = $row->unloading_date2;
    $loadingDate = $row->loading_date2;
    $vendorId = $row->vendor_id;
    $unloadingCostId = $row->unloading_cost_id;
    $freightCostId = $row->freight_cost_id;
	$handlingCostId = $row->handling_cost_id;
    $stockpileContractId = $row->stockpile_contract_id;
    $laborId = $row->labor_id;
    $supplierId = $row->supplier_id;
    $vehicleNo = $row->vehicle_no;
    $driver = $row->driver;
    $permitNo = $row->permit_no;
    $block = $row->block;
    $ggl = $row->ggl;
    $rsb = $row->rsb;
    $rsb_ggl = $row->rsb_ggl;
    $uncertified =$row->uncertified;
    $sendWeight2 =  number_format($row->sendWeight2, 2, ".", ",");
  
   // $sendWeight2 =  number_format($row->sendWeight2, 2, ".", ",");

    $qtyRG = $row->qtyRG;
    $qtyGGL = $row->qtyGGL;
    $qtyRSB =$row->qtyRSB;
    $qtyUN = $row->qtyUN;

    // $sendWeight2 =  number_format($row->sendWeight2, 2, ".", ",");
    $sendWeight = $row->send_weight;
    $brutoWeight = $row->bruto_weight;
    $tarraWeight = $row->tarra_weight;
    $nettoWeight = $row->netto_weight;
    $notes = $row->notes;
    $notes2 = $row->notes;
    $transactionDate2 = $row->transaction_date2;
    $customerId = $row->customer_id;
	$customerName = $row->customer_name;
    $salesId = $row->sales_id;
	$salesNo = $row->sales_no;
    $shipmentId = $row->shipment_id;
	$shipmentCode = $row->shipment_no;
    $quantity = $row->quantity;
	$quantityAvailable = $row->quantity_available;
    $isTaxable = $row->is_taxable;
    $ppn = $row->ppn;
    $pph = $row->pph;
	$edit_date = $row->edit_date;
    
    if($row->delivery_status != 0 || $row->payment_id != '' || $row->fc_payment_id != '' || $row->uc_payment_id != '' || $row->sh_payment_id != '') {
        //$disableProperty = " disabled ";	
    }
	
	
	if($date_now != $edit_date && $allowEditNotim){
		$readOnly = '';
	}else if($date_now != $edit_date){
		$readOnly = 'readonly';
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

<input type="hidden" name="block1" id="block1" value = <?php echo $block ?>>

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
//        $('.select2combobox').selectize({
//            allowEmptyOption: true,
//            create: true,
//            sortField: 'text'
//        });

        $('#availabel_rsb').number(true, 2);
        $('#availabel_ggl').number(true, 2);
        $('#availabel_uncertified').number(true, 2);
        $('#availabel_rsb_ggl').number(true, 2);

        // getAvailableQty_RSB(<?php echo $stockpileId ?>); 
        // getAvailableQty_ggl(<?php echo $stockpileId ?>);
        // getAvailableQty_RG(<?php echo $stockpileId ?>);
        // getAvailableQty_uncertified(<?php echo $stockpileId ?>);
        getAvailableQtyAll(<?php echo $stockpileId ?>);


        $('#rsb_ggl').attr("readonly", true);
        $('#rsb1').attr("readonly", true);
        $('#ggl1').attr("readonly", true);
        $('#uncertified1').attr("readonly", true);

        $('#qty_uncertified').number(true, 2);


        
        if(document.getElementById('transactionType').value == 1) {
            $('#inTransaction').show();
            $('#outTransaction').hide();
        } else if(document.getElementById('transactionType').value == 2) {
            $('#inTransaction').hide();
            $('#outTransaction').show();
        }
        
        <?php
        if($transactionType == 1) {
            ?>
            if(document.getElementById('stockpileId').value != '') {
                setUnloading(1, $('input[id="stockpileId"]').val(), <?php if($unloadingCostId != '') { echo $unloadingCostId; } else { echo "'NONE'"; } ?>);
                <?php
                if($unloadingCostId != '') {
                ?>
                setUnloadingDetail(<?php echo $unloadingCostId; ?>);
                <?php
                }
                ?>
                setVendor(1, $('input[id="stockpileId"]').val(), <?php echo $vendorId; ?>);
                setVendorGGL_SRB(<?php echo $vendorId; ?>);
                // setSpBlock(1, $('input[id="stockpileId"]').val(), <?php echo $block; ?>);
                resetContract(' Contract ');
                resetFreight(' Contract ');
				resetHandling(' Contract ');
            }
                    
            resetFreight(' ');
            resetContract(' ');
			resetHandling(' ');
            setFreight(1, $('input[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php if($freightCostId != '') { echo $freightCostId; } else { echo "'NONE'"; } ?>);
         

            <?php
            if($freightCostId != '') {
            ?>
            setFreightDetail(<?php echo $freightCostId; ?>);
            <?php
            }
            ?>
			setHandling(1, $('input[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php if($handlingCostId != '') { echo $handlingCostId; } else { echo "'NONE'"; } ?>);
            <?php
            if($handlingCostId != '') {
            ?>
            setHandlingDetail(<?php echo $handlingCostId; ?>);
            <?php
            }
            ?>
            setContract(1, $('input[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $stockpileContractId; ?>);
            setContractDetail(<?php echo $stockpileContractId; ?>);
            <?php
        } if($transactionType == 2) {
            
            ?>
            // setShipmentGGL_SRB1(<?php echo $shipmentId; ?>);
            //setSales(1, $('select[id="customerId"]').val(), <?php echo $salesId; ?>);
            //setShipment(1, <?php echo $salesId; ?>, <?php echo $shipmentId; ?>);
            //setShipmentDetail(<?php echo $shipmentId; ?>);
            <?php
            
        }
        ?>
        
        $('#showTransaction').click(function(e){
            e.preventDefault();
            
            $("#transactionContainer").show();
            $("#hideTransaction").show();
            $("#printTransaction").show();
            $("#showTransaction").hide();
        });
        
        $('#hideTransaction').click(function(e){
            e.preventDefault();
            
            $("#transactionContainer").hide();
            $("#showTransaction").show();
            $("#printTransaction").hide();
            $("#hideTransaction").hide();
        });
        
        $('#printTransaction').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#transactionContainer").printThis();
//            $("#transactionContainer").hide();
        });
        
        $('#insertModal').on('hidden', function () {
            // do something…
            if(document.getElementById('vendorId').value == 'INSERT') {
                setVendor(0, $('input[id="stockpileId"]').val(), 0);
                // setSpBlock(0, $('input[id="stockpileId"]').val(), 0);
            } else if(document.getElementById('unloadingCostId').value == 'INSERT') {
                setUnloading(0, $('input[id="stockpileId"]').val(), 0);
            } else if(document.getElementById('laborId').value == 'INSERT') {
                setLabor(0, 0);
            } else if(document.getElementById('stockpileContractId').value == 'INSERT') {
                setContract(0, $('input[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if(document.getElementById('freightCostId').value == 'INSERT') {
                setFreight(0, $('input[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if(document.getElementById('supplierId').value == 'INSERT') {
                setSupplier(0, 0);
            } else if(document.getElementById('customerId').value == 'INSERT') {
                setCustomer(0, 0);
            } else if(document.getElementById('salesId').value == 'INSERT') {
                setSales(0, $('select[id="customerId"]').val(), 0);
            }
        });
        
        $('#transactionType').change(function() {
            resetVehicle();
            resetVendor();
            resetFreight(' Stockpile ');
            resetFreightDetail();
			resetHandling(' Stockpile ');
            resetUnloadingDetail();
            resetContract(' Stockpile ');
            resetContractDetail();
            resetSales(' Buyer ');
            resetShipment(' Buyer ');
            resetShipmentDetail();
            
//            alert(document.getElementById('transactionType').value);
            document.getElementById('stockpileId').value = '';
            document.getElementById('shipmentId').value = '';
            
            $('#inTransaction').hide();
            $('#outTransaction').hide();
            
            if(document.getElementById('transactionType').value == 1) {
                $('#inTransaction').show();
                $('#outTransaction').hide();
            } else if(document.getElementById('transactionType').value == 2) {
                $('#inTransaction').hide();
                $('#outTransaction').show();
            }
        });
        
        $('#stockpileId').change(function() {
//            $('#clientId').val('');
            resetVehicle();
            resetVendor();
            resetFreight(' Stockpile ');
            resetFreightDetail();
			resetHandling(' Stockpile ');
            resetHandlingDetail();
            resetContract(' Stockpile ');
            resetContractDetail();
            
            if(document.getElementById('stockpileId').value != '') {
                setUnloading(0, $('input[id="stockpileId"]').val(), 0);
                setVendor(0, $('input[id="stockpileId"]').val(), 0);
                resetContract(' Contract ');
                resetFreight(' Contract ');
				resetHandling(' Contract ');
            }
        });
        
        $('#customerId').change(function() {
            resetSales(' Buyer ');
            resetShipment(' Sales ');
            resetShipmentDetail();
            
            if(document.getElementById('customerId').value != '' && document.getElementById('customerId').value != 'INSERT') {
                resetSales(' ');
                setSales(0, $('select[id="customerId"]').val(), 0);
            } else if(document.getElementById('customerId').value != '' && document.getElementById('customerId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-customer.php', {});
            } 
        });
        
        $('#salesId').change(function() {
            resetShipment(' Sales ');
            resetShipmentDetail();
            
            if(document.getElementById('salesId').value != '' && document.getElementById('salesId').value != 'INSERT') {
                resetShipment(' ');
                setShipment(0, $('select[id="salesId"]').val(), 0);
            } else if(document.getElementById('salesId').value != '' && document.getElementById('salesId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-sales.php', {customerId: $('select[id="customerId"]').val()});
            }
        });
        
//        $('#shipmentId').change(function() {
//            if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value == 'INSERT') {
//                $("#modalErrorMsgInsert").hide();
//                $('#insertModal').modal('show');
//                $('#insertModalForm').load('forms/transaction-shipment.php', {});
//            }
//        });
        
        $('#vendorId').change(function() {
            resetFreight(' Contract ');
            resetFreightDetail();
            resetContract(' Contract ');
            resetContractDetail();
            document.getElementById('supplierId').value = '';
//            $('#supplierId').attr("disabled", true);
            
            if(document.getElementById('vendorId').value != '' && document.getElementById('vendorId').value != 'INSERT') {
                resetFreight(' ');
                resetContract(' ');
                setFreight(0, $('input[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
                setContract(0, $('input[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if(document.getElementById('vendorId').value != '' && document.getElementById('vendorId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-vendor.php', {});
            } 
        });
        
        $('#stockpileContractId').change(function() {
            resetContractDetail();
            if(document.getElementById('stockpileContractId').value != '' && document.getElementById('stockpileContractId').value != 'INSERT') {
                setContractDetail($('select[id="stockpileContractId"]').val());
            } else if(document.getElementById('stockpileContractId').value != '' && document.getElementById('stockpileContractId').value == 'INSERT') {
//                resetFreight(' Contract ');
//                resetFreightDetail();
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-contract.php', {stockpileId: $('input[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } 
        });
        
        $('#shipmentId').change(function() {
            if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value != 'INSERT') {
                setShipmentDetail($('select[id="shipmentId"]').val());
            } else if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value == 'INSERT') {
                resetShipmentDetail();
                
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-shipment.php', {salesId: $('select[id="salesId"]').val()});
            } else {
                resetShipmentDetail();
            }
        });
        
        $('#unloadingCostId').change(function() {
            resetUnloadingDetail();
            if(document.getElementById('unloadingCostId').value != '' && document.getElementById('unloadingCostId').value != 'INSERT') {
                setUnloadingDetail($('select[id="unloadingCostId"]').val());
            } else if(document.getElementById('unloadingCostId').value != '' && document.getElementById('unloadingCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-unloading.php', {stockpileId: $('input[id="stockpileId"]').val()});
            } 
        });
        
        $('#freightCostId').change(function() {
            resetFreightDetail();
            if(document.getElementById('freightCostId').value != '' && document.getElementById('freightCostId').value != 'INSERT') {
                setFreightDetail($('select[id="freightCostId"]').val());
            } else if(document.getElementById('freightCostId').value != '' && document.getElementById('freightCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-freight.php', {stockpileId: $('input[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } 
        });
        $('#handlingCostId').change(function() {
            resetHandlingDetail();
            if(document.getElementById('handlingCostId').value != '' && document.getElementById('handlingCostId').value != 'INSERT') {
                setHandlingDetail($('select[id="handlingCostId"]').val());
            } else if(document.getElementById('handlingCostId').value != '' && document.getElementById('handlingCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-handling.php', {stockpileId: $('input[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } 
        });
        $('#laborId').change(function() {
            if(document.getElementById('laborId').value != '' && document.getElementById('laborId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-labor.php', {});
            } 
        });
        
        $('#supplierId').change(function() {
            if(document.getElementById('supplierId').value != '' && document.getElementById('supplierId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-supplier.php', {});
            } 
        });
        
        $("#transactionDataForm").validate({
            rules: {
                stockpileId: "required",
                transactionType: "required",
                loadingDate: "required",
                stockpileContractId: "required",
                vehicleNo: "required",
                driver: "required",
                unloadingDate: "required",
                unloadingCostId: "required",
                permitNo: "required",
                freightCostId: "required",
				handlingCostId: "required",
                sendWeight: "required",
                brutoWeight: "required",
                tarraWeight: "required",
                block: "required",
                vendorId: "required",
                transactionDate2: "required",
                sendWeight2: "required",
                blWeight: "required",
                vehicleNo2: "required",
                shipmentId: "required"
            },
            messages: {
                stockpileId: "Stockpile is a required field.",
                transactionType: "Type is a required field.",
                loadingDate: "Loading Date is a required field.",
                stockpileContractId: "PO No. is a required field.",
                vehicleNo: "Vehicle No. is a required field.",
                driver: "Driver is a required field.",
                unloadingDate: "Unloading Date is a required field.",
                unloadingCostId: "Vehicle is a required field.",
                permitNo: "Permit No. is a required field.",
                freightCostId: "Supplier Freight is a required field.",
				handlingCostId: "Handling Cost is a required field.",
                sendWeight: "Sent Weight is a required field.",
                brutoWeight: "Bruto Weight is a required field.",
                tarraWeight: "Tarra Weight is a required field.",
                block: "Block is a required field.",
                vendorId: "Contract Name is a required field.",
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
                                $('#pageContent').load('views/transaction.php', {}, iAmACallbackFunction);
                            } 
                            $('#submitButton').attr("disabled", false);
                        }
                    }
                });
            }
        });
        
        $("#insertForm").validate({
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                var resultData = returnVal[3].split('~');
                                
                                if (resultData[0] == 'CONTRACT') {
                                    setContract(1, $('input[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
//                                    resetFreight(' ');
//                                    setFreight(0, resultData[1], 0);
                                    setContractDetail(resultData[1]);
                                } else if (resultData[0] == 'UNLOADING') {
                                    setUnloading(1, $('input[id="stockpileId"]').val(), resultData[1]);
                                    setUnloadingDetail(resultData[1]);
                                } else if (resultData[0] == 'FREIGHT') {
                                    setFreight(1, $('input[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
                                    setFreightDetail(resultData[1]);
                                } else if (resultData[0] == 'SALES') {
                                    setSales(1, $('select[id="customerId"]').val(), resultData[1]);
                                    resetShipment(' ');
                                    setShipment(0, resultData[1], 0);
                                } else if (resultData[0] == 'SHIPMENT') {
                                    setShipment(1, $('select[id="salesId"]').val(), resultData[1]);
                                    setShipmentDetail(resultData[1]);
                                } else if (resultData[0] == 'VENDOR') {
                                    setVendor(1, $('input[id="stockpileId"]').val(), resultData[1]);
                                    // setSpBlock(1, $('input[id="stockpileId"]').val(), resultData[1]);
                                    resetFreight(' ');
                                    resetContract(' ');
                                    setFreight(1, $('input[id="stockpileId"]').val(), resultData[1], 0);
                                    setContract(1, $('input[id="stockpileId"]').val(), resultData[1], 0);
                                } else if (resultData[0] == 'CUSTOMER') {
                                    setCustomer(1, resultData[1]);
                                    setShipment(0, resultData[1], 0);
                                } else if (resultData[0] == 'LABOR') {
                                    setLabor(1, resultData[1]);
                                } else if (resultData[0] == 'SUPPLIER') {
                                    setSupplier(1, resultData[1]);
                                }
                                
                                $('#insertModal').modal('hide');
                            } else {
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
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
    
    function resetVehicle() {
        document.getElementById('unloadingCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('unloadingCostId').options.add(x);
        
        $("#unloadingCostId").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }
    
    function setUnloading(type, stockpileId, unloadingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getUnloadingCost',
                    stockpileId: stockpileId,
                   // unloadingCostId: unloadingCostId
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
//                    if(returnValLength > 0) {
                        document.getElementById('unloadingCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('unloadingCostId').options.add(x);
                        
                        $("#unloadingCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
//                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('unloadingCostId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('unloadingCostId').options.add(x);
                    }

                    <?php
                    if($allowUnloading) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('unloadingCostId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#unloadingCostId').find('option').each(function(i,e){
                            if($(e).val() == unloadingCostId){
                                $('#unloadingCostId').prop('selectedIndex',i);
                                
                                $("#unloadingCostId").select2({
                                    width: "100%",
                                    placeholder: unloadingCostId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetVendor() {
        document.getElementById('vendorId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('vendorId').options.add(x);
        
        $("#vendorId").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }
    
    function setVendor(type, stockpileId, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendor',
                    stockpileId: stockpileId,
                    newVendorId: vendorId
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

                    <?php
                    if($allowVendor) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('vendorId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
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
    
    function resetFreight(text) {
        document.getElementById('freightCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightCostId').options.add(x);
        
        $("#freightCostId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function setFreight(type, stockpileId, vendorId, freightCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightCost',
                    stockpileId: stockpileId,
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
                        document.getElementById('freightCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightCostId').options.add(x);
                        
                        $("#freightCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('freightCostId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightCostId').options.add(x);
                    }

                    <?php
                    if($allowFreight) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('freightCostId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#freightCostId').find('option').each(function(i,e){
                            if($(e).val() == freightCostId){
                                $('#freightCostId').prop('selectedIndex',i);
                                
                                $("#freightCostId").select2({
                                    width: "100%",
                                    placeholder: freightCostId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetFreightDetail() {
        $('#labelFreightCost').hide();
        document.getElementById('labelFreightCost').innerHTML = '';
    }
    function resetHandling(text) {
        document.getElementById('handlingCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('handlingCostId').options.add(x);
        
        $("#handlingCostId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function setHandling(type, stockpileId, vendorId, handlingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getHandlingCost',
                    stockpileId: stockpileId,
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
                        document.getElementById('handlingCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('handlingCostId').options.add(x);
                        
                        $("#handlingCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('handlingCostId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('handlingCostId').options.add(x);
                    }

                    <?php
                   // if($allowFreight) {
                    ?>
//                    if(returnValLength > 0) {
                     //   var x = document.createElement('option');
                     //   x.value = 'INSERT';
                     //   x.text = '-- Insert New --';
                     //   document.getElementById('freightCostId').options.add(x);
//                    }                  
                    <?php
                  //  }
                    ?>
                                        
                    if(type == 1) {
                        $('#handlingCostId').find('option').each(function(i,e){
                            if($(e).val() == handlingCostId){
                                $('#handlingCostId').prop('selectedIndex',i);
                                
                                $("#handlingCostId").select2({
                                    width: "100%",
                                    placeholder: handlingCostId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetHandlingDetail() {
        $('#labelHandlingCost').hide();
        document.getElementById('labelHandlingCost').innerHTML = '';
    }
    function resetUnloadingDetail() {
        $('#labelUnloadingCost').hide();
        document.getElementById('labelUnloadingCost').innerHTML = '';
    }
    
    function resetContract(text) {
        document.getElementById('stockpileContractId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('stockpileContractId').options.add(x);
        
        $("#stockpileContractId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function setContract(type, stockpileId, vendorId, stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getStockpileContract',
                    stockpileId: stockpileId,
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

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }

                    <?php
                    if($allowContract) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('stockpileContractId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#stockpileContractId').find('option').each(function(i,e){
                            if($(e).val() == stockpileContractId){
                                $('#stockpileContractId').prop('selectedIndex',i);
                                
                                $("#stockpileContractId").select2({
                                    width: "100%",
                                    placeholder: stockpileContractId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetShipment(text) {
        document.getElementById('shipmentId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId').options.add(x);
        
        $("#shipmentId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function resetSales(text) {
        document.getElementById('salesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('salesId').options.add(x);
        
        $("#salesId").select2({
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function resetContractDetail() {
        $('#labelQuantityContract').hide();
        document.getElementById('labelQuantityContract').innerHTML = '';
        document.getElementById('contractNo').value = '';
        document.getElementById('supplierId').value = '';
//        $('#supplierId').attr("disabled", true);
        
        $("#supplierId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }
    
    function setContractDetail(stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getContractDetail',
                    stockpileContractId: stockpileContractId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');
                    
                    if(returnVal[0] == 'C') {
//                        $('#supplierId').attr("disabled", false);
//                        setSupplier(0, 0);
                    } else if(returnVal[0] == 'P') {
                        $('#labelQuantityContract').show();
                        document.getElementById('labelQuantityContract').innerHTML = 'Quantity balance is ' + returnVal[2] + ' KG';
//                        $('#supplierId').attr("disabled", true);
                    }

                    document.getElementById('contractNo').value = returnVal[1];
                }
            }
        });
    }
    
    function setFreightDetail(freightCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightDetail',
                    freightCostId: freightCostId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    $('#labelFreightCost').show();
                    document.getElementById('labelFreightCost').innerHTML = 'Freight cost/KG is ' + data;
                }
            }
        });
    }
	
	function setHandlingDetail(handlingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getHandlingDetail',
                    handlingCostId: handlingCostId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    $('#labelHandlingCost').show();
                    document.getElementById('labelHandlingCost').innerHTML = 'Handling cost/KG is ' + data;
                }
            }
        });
    }
    
    function setUnloadingDetail(unloadingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getUnloadingDetail',
                    unloadingCostId: unloadingCostId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    $('#labelUnloadingCost').show();
                    document.getElementById('labelUnloadingCost').innerHTML = 'Unloading cost is ' + data;
                }
            }
        });
    }
    
    function getNettoWeight(brutoWeight, tarraWeight) {
        if(brutoWeight.value != '' && tarraWeight.value != '') {
            document.getElementById('nettoWeight').value = brutoWeight.value.replace(",", "") - tarraWeight.value.replace(",", "");
        } else {
            document.getElementById('nettoWeight').value = '';
        }
    }
    
    function resetCustomer() {
        document.getElementById('customerId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('customerId').options.add(x);
        
        $("#customerId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }
    
    function setCustomer(type, customerId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getCustomer'
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
                        document.getElementById('customerId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('customerId').options.add(x);
                        
                        $("#customerId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('customerId').options.add(x);
                    }

                    <?php
                    if($allowCustomer) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('customerId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#customerId').find('option').each(function(i,e){
                            if($(e).val() == customerId){
                                $('#customerId').prop('selectedIndex',i);
                                
                                $("#customerId").select2({
                                    width: "100%",
                                    placeholder: customerId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setLabor(type, laborId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getLabor'
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
                        document.getElementById('laborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborId').options.add(x);
                        
                        $("#laborId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }

                    <?php
                    if($allowLabor) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('laborId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#laborId').find('option').each(function(i,e){
                            if($(e).val() == laborId){
                                $('#laborId').prop('selectedIndex',i);
                                
                                $("#laborId").select2({
                                    width: "100%",
                                    placeholder: laborId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setSales(type, customerId, salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSales',
                customerId: customerId
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
                        document.getElementById('salesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('salesId').options.add(x);
                        
                        $("#salesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('salesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('salesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#salesId').find('option').each(function(i,e){
                            if($(e).val() == salesId){
                                $('#salesId').prop('selectedIndex',i);
                                
                                $("#salesId").select2({
                                    width: "100%",
                                    placeholder: salesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setShipment(type, salesId, shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipment',
                salesId: salesId,
                shipmentId: shipmentId
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
                        document.getElementById('shipmentId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId').options.add(x);
                        
                        $("#shipmentId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId').options.add(x);
                    }
                }
            }
        });
    }
    
    function resetShipmentDetail() {
//        document.getElementById('customerName').value = '';
        document.getElementById('quantityAvailable').value = '';
    }
    
    function setShipmentDetail(shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipmentDetail',
                    shipmentId: shipmentId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');

//                    document.getElementById('customerName').value = returnVal[0];
                    document.getElementById('quantityAvailable').value = returnVal[1];
                }
            }
        });
    }
    
    function setSupplier(type, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSupplier',
                    newVendorId: vendorId
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
                        document.getElementById('supplierId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('supplierId').options.add(x);
                        
                        $("#supplierId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('supplierId').options.add(x);
                    }

                    <?php
                    if($allowSupplier) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('supplierId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#supplierId').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#supplierId').prop('selectedIndex',i);
                                
                                $("#supplierId").select2({
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
    
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/search-transaction.php', {}, iAmACallbackFunction);
    }

    function setVendorGGL_SRB(vendor_id) {
       
       //if(amount != '') {
           $.ajax({
               url: 'get_data.php',
               method: 'POST',
               data: { action: 'getVendorGGL_SRB',
                       vendor_id: vendor_id
               },
               success: function(data){
                   var returnVal = data.split('|');
                   if(parseInt(returnVal[0])!=0)	//if no errors
                   {
                       document.getElementById('rsb').value = returnVal[1];
                       document.getElementById('ggl').value = returnVal[2];
                       $('#ggl_rsb').show();
                       document.getElementById('ggl_rsb').innerHTML = returnVal[3];
                    
                       setSpBlock(1, $('input[id="stockpileId"]').val(),  returnVal[2], returnVal[1], <?php echo $block ?>);
                   }
               }
           });
       //}
   }

   function resetSpBlock() {
        document.getElementById('block').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('block').options.add(x);
        
        $("#block").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }

   function setSpBlock(type, stockpileId, ggl, rsb, block) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSpBlockData',
                    stockpileId: stockpileId,
                    newblock: block,
                    ggl: ggl,
                    rsb: rsb
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
                    if(returnValLength >= 0) {
                        document.getElementById('block').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('block').options.add(x);

                        $("#block").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('block').options.add(x);
                    }

                                        
                    if(type == 1) {
                        $('#block').find('option').each(function(i,e){
                            if($(e).val() == block){
                                $('#block').prop('selectedIndex',i);

                                $("#block").select2({
                                    width: "100%",
                                    placeholder: block
                                });
                            }
                        });
                    }
                }
            }
        });
    }

//    function setShipmentGGL_SRB1(shipmentId) {
       
//        //if(amount != '') {
//            $.ajax({
//                url: 'get_data.php',
//                method: 'POST',
//                data: { action: 'getShipmentGGL_SRB1',
//                     shipmentId: shipmentId
//                },
//                success: function(data){
//                    var returnVal = data.split('|');
//                    if(parseInt(returnVal[0])!=0)	//if no errors
//                    {
//                        document.getElementById('rsb').value = returnVal[1];
//                        document.getElementById('ggl').value = returnVal[2];
//                        $('#ggl_rsb_shipment').show();
//                        document.getElementById('ggl_rsb_shipment').innerHTML = returnVal[3];
//                     //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
//                    }
//                }
//            });
//        //}
//    }

//    function getAvailableQty_RSB(stockpileId) {
//         $.ajax({
//            url: 'get_data.php',
//            method: 'POST',
//            data: { action: 'getAvailableQty_RSB',
//                 stockpileId: stockpileId
//             },
//            success: function(data){
//                var returnVal = data.split('|');
//                if(parseInt(returnVal[0])!=0)	//if no errors
//                 {
//                    document.getElementById('availabel_rsb').innerHTML = returnVal[1];
//                     //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
//                }
//            }
//        });
//     }

//     function getAvailableQty_ggl(stockpileId) {
//         $.ajax({
//            url: 'get_data.php',
//            method: 'POST',
//            data: { action: 'getAvailableQty_ggl',
//                 stockpileId: stockpileId
//             },
//            success: function(data){
//                var returnVal = data.split('|');
//                if(parseInt(returnVal[0])!=0)	//if no errors
//                 {
//                    document.getElementById('availabel_ggl').innerHTML = returnVal[1];
//                     //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
//                }
//            }
//        });
//     }

//     function getAvailableQty_RG(stockpileId) {
//         $.ajax({
//            url: 'get_data.php',
//            method: 'POST',
//            data: { action: 'getAvailableQty_RG',
//                 stockpileId: stockpileId
//             },
//            success: function(data){
//                var returnVal = data.split('|');
//                if(parseInt(returnVal[0])!=0)	//if no errors
//                 {
//                    document.getElementById('availabel_rsb_ggl').innerHTML = returnVal[1];
//                     //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
//                }
//            }
//        });
//     }

//     function getAvailableQty_uncertified(stockpileId) {
//         $.ajax({
//            url: 'get_data.php',
//            method: 'POST',
//            data: { action: 'getAvailableQty_uncertified',
//                 stockpileId: stockpileId
//             },
//            success: function(data){
//                var returnVal = data.split('|');
//                if(parseInt(returnVal[0])!=0)	//if no errors
//                 {
//                    document.getElementById('availabel_uncertified').innerHTML = returnVal[1];
//                     //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
//                }
//            }
//        });
//     }
function getAvailableQtyAll(stockpileId) {
        $.ajax({
           url: 'get_data.php',
           method: 'POST',
           data: { action: 'getAvailableQtyAll',
                stockpileId: stockpileId
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

<form method="post" id="transactionDataForm">
    <input type="hidden" name="action" id="action" value="update_transaction_data" />
    <input type="hidden" name="transactionId" id="transactionId" value="<?php echo $transactionId; ?>" />
    <input type="hidden" placeholder="DD/MM/YYYY" id="transactionDate" name="transactionDate" value="<?php echo $transactionDate2; ?>" >
    <input type="hidden" name="rsb" id="rsb" />
    <input type="hidden" name="ggl" id="ggl" />
    <input type="hidden"  id="rsb_ggl" name="rsb_ggl" value="<?php echo $rsb_ggl; ?>" > 
    <input type="hidden"  id="uncertified" name="uncertified" value="<?php echo $uncertified; ?>" > 

    <div class="row-fluid" style="margin-bottom: 7px;">
		<div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
         
			<input type="text" readonly class="span12" tabindex="15" id="transactionType2" name="transactionType2" value="<?php echo $transactionType2; ?>">
			<input type="hidden" readonly class="span12" tabindex="15" id="transactionType" name="transactionType" value="<?php echo $transactionType; ?>">
        </div>
        <div class="span2 lightblue">
            <label>Slip No <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
			  <input type="text" readonly class="span12" tabindex="15" id="slipNo" name="slipNo" value="<?php echo $slipNo; ?>">
			  <input type="hidden" readonly class="span12" tabindex="15" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>">
        </div>
        
    </div>
    
    <div id="inTransaction">
    
        <div class="row-fluid" style="margin-bottom: 7px;">   
            <div class="span2 lightblue">
                <label>Contract Name <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "vendorId", "vendor_id", "vendor_name", 
                    "", 2, "select2combobox100", 2);
                ?>
                <span class="help-block" id="ggl_rsb" style="display: none;"></span>

            </div>
            <div class="span2 lightblue">
                <label>PO No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "stockpileContractId", "stockpile_contract_id", "po_no", 
                    "", 13, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelQuantityContract" style="display: none;"></span>
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Receive Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="unloadingDate" name="unloadingDate" value="<?php echo $unloadingDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
            <div class="span2 lightblue">
                <label>Supplier</label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("SELECT vendor_id, vendor_name FROM vendor ORDER BY vendor_name ASC", $supplierId, "", "supplierId", "vendor_id", "vendor_name", 
                    "", 14, "select2combobox100", 1, "", $allowSupplier);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Loading Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="4" id="loadingDate" name="loadingDate" value="<?php echo $loadingDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
            <div class="span2 lightblue">
                <label>Contract No.</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="15" id="contractNo" name="contractNo">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Vehicle <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "unloadingCostId", "unloading_cost_id", "vehicle_name", 
                    "", 5, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelUnloadingCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Vehicle No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="vehicleNo" name="vehicleNo" value="<?php echo $vehicleNo; ?>">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Supplier Freight <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "freightCostId", "freight_cost_id", "freight_full", 
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelFreightCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Driver <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="17" id="driver" name="driver" value="<?php echo $driver; ?>">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Unloading Org <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                if($laborId == '') {
                    $laborId = 'NONE';
                }
                createCombo("SELECT * FROM labor ORDER BY labor_name ASC", $laborId, "", "laborId", "labor_id", "labor_name", 
                    "", 7, "select2combobox100", 3, "", $allowLabor);
                ?>
                <span class="help-block" id="labelFreightCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Delivery Notes No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="18" id="permitNo" name="permitNo" value="<?php echo $permitNo; ?>">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Handling Cost <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "handlingCostId", "handling_cost_id", "handling_full", 
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelHandlingCost" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>Stockpile Block <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
        //         createCombo("SELECT sp_block_id, CONCAT(CASE WHEN ggl = 1 AND rsb = 0 THEN 'GGL' 
        //                                                 WHEN rsb = 1 AND ggl = 0 THEN 'RSB' 
        //                                                 WHEN rsb = 1 AND ggl = 1 THEN '(RSB & GGL)' ELSE 'Uncertified' 
        //                                                 END, ' - ', sp_block) AS block_name 
        //                             FROM mst_sp_block 
        //                             WHERE stockpile_id = {$stockpileId} and ggl = {$ggl} and rsb = {$rsb} ORDER BY block_name ASC", $block, "", "block", "sp_block_id", "block_name", 
        //                      "", 14, "select2combobox100", 1, "", "");

        //                     echo "SELECT sp_block_id, CONCAT(CASE WHEN ggl = 1 AND rsb = 0 THEN 'GGL' 
        //                     WHEN rsb = 1 AND ggl = 0 THEN 'RSB' 
        //                     WHEN rsb = 1 AND ggl = 1 THEN '(RSB & GGL)' ELSE 'Uncertified' 
        //                     END, ' - ', sp_block) AS block_name 
        // FROM mst_sp_block 
        // WHERE stockpile_id = {$stockpileId} and ggl = {$ggl} and rsb = {$rsb} ORDER BY block_name ASC";
                         
                 createCombo("", "", "", "block", "sp_block_id", "block_name", 
                     "", 6);
                   
                // createCombo("SELECT 'NONE' id, 'NONE' info UNION 
                //             SELECT 'A' id, 'A' info UNION 
                //             SELECT 'B' id, 'B' info UNION 
                //             SELECT 'C' id, 'C' info UNION 
                //             SELECT 'D' id, 'D' info UNION 
                //             SELECT 'E' id, 'E' info UNION 
                //             SELECT 'F' id, 'F' info UNION 
                //             SELECT 'G' id, 'G' info", $block, "", "block", "id", "info", 
                //             "", 19, "select2combobox50");
                ?>
            </div>
        </div>
        <hr>
        <div class="row-fluid">  
            <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>Sent Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" <?php echo $readOnly; ?> class="span12" tabindex="29" id="sendWeight" name="sendWeight" value="<?php echo number_format($sendWeight, 2, '.', ','); ?>">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>Bruto Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" <?php echo $readOnly; ?> class="span12" tabindex="30" id="brutoWeight" name="brutoWeight" onblur="getNettoWeight(this, document.getElementById('tarraWeight'));" value="<?php echo number_format($brutoWeight, 2, '.', ','); ?>">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <!--<label>Unloading Cost</label>-->
            </div>
            <div class="span4 lightblue">
                <!--<label id="labelUnloadingCost"></label>-->
            </div>
            <div class="span2 lightblue">
                <label>Tarra Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" <?php echo $readOnly; ?> class="span12" tabindex="31" id="tarraWeight" name="tarraWeight" onblur="getNettoWeight(document.getElementById('brutoWeight'), this);" value="<?php echo number_format($tarraWeight, 2, '.', ','); ?>">
            </div>
        </div>
        <hr>
        <div class="row-fluid">  
            <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div>
            <div class="span2 lightblue">
                <label>Netto Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="32" id="nettoWeight" name="nettoWeight" value="<?php echo number_format($nettoWeight, 2, '.', ','); ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes" name="notes"><?php echo $notes; ?></textarea>
            </div>
        </div>
    
    </div>
    
    <div id="outTransaction" style="display: none;">
        
        <div class="row-fluid">   
            <div class="span2 lightblue">
                <label>Transaction Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="transactionDate2" name="transactionDate2" value="<?php echo $transactionDate2; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
            <div class="span2 lightblue">
                <label>Buyer</label>
            </div>
            <div class="span4 lightblue">
                <?php
                //createCombo("SELECT cust.customer_id, cust.customer_name
                           // FROM customer cust ORDER BY cust.customer_name ASC", $customerId, "", "customerId", "customer_id", "customer_name", 
                           // "", 11, "select2combobox100", 1, "", $allowCustomer);
                ?>
				<input type="text"  readonly class="span12" tabindex="4" id="customerName" name="customerName" value="<?php echo $customerName; ?>">
				<input type="hidden"  readonly class="span12" tabindex="4" id="customerId" name="customerId" value="<?php echo $customerId; ?>">
            </div>
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Vessel Name <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="3" id="vehicleNo2" name="vehicleNo2" value="<?php echo $vehicleNo; ?>">
            </div>
            <div class="span2 lightblue">
                <label>Sales Agreement No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                //createCombo("", "", "", "salesId", "sales_id", "sales_no", 
                //            "", 12, "select2combobox100", 1, "", $allowSales);
                ?>
				<input type="text"  readonly class="span12" tabindex="4" id="salesNo" name="salesNo" value="<?php echo $salesNo; ?>">
				<input type="hidden"  readonly class="span12" tabindex="4" id="salesId" name="salesId" value="<?php echo $salesId; ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Stockpile Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text"  readonly class="span12" tabindex="4" id="sendWeight2" name="sendWeight2" value="<?php echo $sendWeight2; ?>">
            </div>
            <div class="span2 lightblue">
                <label>Shipment Code <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                //createCombo("", "", "", "shipmentId", "shipment_id", "shipment_no", 
                //            "", 13, "select2combobox100", 1, "", $allowShipment);
                ?>
				<input type="text"  readonly class="span12" tabindex="4" id="shipmentCode" name="shipmentCode" value="<?php echo $shipmentCode; ?>">
				<input type="hidden"  readonly class="span12" tabindex="4" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId; ?>">
                <!-- <span class="help-block" id="ggl_rsb_shipment" style="display: none;"></span> -->

            </div>
        </div>
        
           <!-- RSB -->
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Available Qty<span style="color: red;">*</span></label>
            </div>
            <div class="span2 lightblue">
                <label>RSB : <span id="availabel_rsb"></span></label>
            </div>
            <div class="span2 lightblue">
                <label>GGL : <span id="availabel_ggl" ></span></label>
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
                <label>Uncertified : <span id="availabel_uncertified" ></span></label>
            </div>
            <div class="span2 lightblue">
                <label>RSB-GGL : <span id="availabel_rsb_ggl" ></span></label>
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
                             SELECT '1' as id, 'YES' as info;", $rsb_ggl, '', "rsb_ggl", "id", "info", 
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
                <input type="text" readonly class="span12" tabindex="5" id="blWeight" name="blWeight" value="<?php echo $quantity; ?>">
            </div>
            <div class="span2 lightblue">
                <label>Quantity Agreed (KG)</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="14" id="quantityAvailable" name="quantityAvailable" value="<?php echo $quantityAvailable; ?>">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes2" name="notes2"><?php echo $notes2; ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="row-fluid">
        <div class="span12 lightblue">
            <!-- <button class="btn btn-primary" <?php echo $disableProperty; ?>>Update</button> -->
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

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
