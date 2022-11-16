<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

//$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Logbook Data">

$shipmentCostID = '';
$accountID = '';
$stockpileID ='';
$tipe1 = '';
$currencyId ='';
$exchangeRate1 ='';
$priceMT = '';
$flatPrice = '';
$vendorName='';
$vendorType = '';
// </editor-fold>

if(isset($_POST['shipmentCostID']) && $_POST['shipmentCostID'] != ''){

    $shipmentCostID = $_POST['shipmentCostID'];
    //$readonlyProperty = 'readonly';

    $sql = "SELECT a.*, CONCAT(a.allVendor, '-', a.vendorName) AS vendorName
            FROM master_shipmentcost a
            WHERE a.shipmentCost_id = {$shipmentCostID}
            ORDER BY a.shipmentCost_id ASC";

    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $accountID = $rowData->account_id;
        $tipe1 = $rowData->tipe;
        $exchangeRate1 = $rowData->exchangeRate;
        $currencyId = $rowData->currencyID;
        $priceMT = $rowData->price_MT;
        $flatPrice = $rowData->flat_Price;
        $stockpileID = $rowData->stockpile_id;
        $vendorType = $rowData->vendor_Type;
        $vendorName = $rowData->vendorName;
        // if($qty == 2){
        //     $qtyValue = $rowData->send_weight;
        // }else if($qty == 3){
        //     $qtyValue = $rowData->netto_weight;
        // }else{
        //     $qtyValue = $rowData->qty_others;
        // }
    }
}



//COMBOBOX
function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        echo "<option value='NONE'>NONE</option>";
    }

    if ($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if ($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }

    echo "</SELECT>";
}
?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#shipmentCostDataForm").validate({ //shipmentCostDataForm -> FORM id
            rules: {
                stockpileID: "required",
                accountID: "required",
                vendorType: "required",
                vendorName: "required",
                currencyId: "required",
                priceMT: "required",
                amount: "required"
            },
            messages: {
                stockpileID: "Stockpile ID  is a required field.",
                accountID: "Biaya is a required field.",
                vendorType: "Vendor Type is a required field.",
                vendorName: "Vendor Name is a required field.",
                currencyId: "Currency Value is a required field.",
                priceMT: "Price/MT Value is a required field.",
                amount: "Total Amoutn is a required field."
            },

            submitHandler: function(form) { //call if klik SUBMIT button
                $.ajax({
                    url: './request_processing.php',  
                    method: 'POST',
                    data: $("#shipmentCostDataForm").serialize(), //shipmentCostDataForm -> form id
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalshipmentCostID').value = returnVal[3]; //generalRequestID -> forms/add-shipment-cost.php
                                
                                $('#dataContent').load('views/shipment-cost.php', { shipmentCostID: returnVal[3] }, iAmACallbackFunction2);
                            } 
                        }
                    }
                });
            }
        });
    });

</script>
<!-- BATAS -->

<!--START VENDOR -->
<script>
    $('#vendorType').change(function () {
        let vendorType = document.getElementById("vendorType").value;
        if (vendorType == 'Pks') {
            document.getElementById("vendorPks").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType == 'Freight') {
            document.getElementById("vendorFreight").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");

        } else if (vendorType === 'General') {
            document.getElementById("vendorGeneral").classList.remove("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'Labor') {
            document.getElementById("vendorLabor").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'Handling') {
            document.getElementById("vendorHandling").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorPettyCash").classList.add("hidden");
        } else if (vendorType === 'PettyCash') {
            document.getElementById("vendorPettyCash").classList.remove("hidden");
            document.getElementById("vendorGeneral").classList.add("hidden");
            document.getElementById("vendorFreight").classList.add("hidden");
            document.getElementById("vendorPks").classList.add("hidden");
            document.getElementById("vendorLabor").classList.add("hidden");
            document.getElementById("vendorHandling").classList.add("hidden");
        }
    });

    $(function () {

        <?php if ($vendorType != ''){?>
        let vendorType = document.getElementById("vendorType").value;
        if (vendorType == 'Pks') {
            document.getElementById("vendorPks").classList.remove("hidden");
        } else if (vendorType == 'Freight') {
            document.getElementById("vendorFreight").classList.remove("hidden");
        } else if (vendorType === 'General') {
            document.getElementById("vendorGeneral").classList.remove("hidden");
        } else if (vendorType === 'Labor') {
            document.getElementById("vendorLabor").classList.remove("hidden");
        } else if (vendorType === 'Handling') {
            document.getElementById("vendorHandling").classList.remove("hidden");
        } else if (vendorType === 'PettyCash') {
            document.getElementById("vendorPettyCash").classList.remove("hidden");
        }
        <?php } ?>
        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });
    });

   
</script>
<!-- END VENDOR -->

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

        //FUNGSI ENABLE and DISABLE
        if (document.getElementById('exchangeRate1').value == '') {
            $("#exchangeRate1").prop("disabled", true);
        }
        if (document.getElementById('flatPrice').value == '' || document.getElementById('priceMT').value == '') {
            $('#flatPrice').val(0); //default Value
            $('#priceMT').val(0); // default Value
        }

        
        $("#currencyId").change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $("#exchangeRate1").prop("disabled", true);
                $('#exchangeRate1').val(1);
                sum();
                $('#amount').number(true, 10);
                } else {
                    $("#exchangeRate1").prop("disabled", false);  
                    sum(); //call fungsi sum setiap kali change
                    $('#amount').number(true, 10);
                }
        });
    //END FUNGSI ENABLE and DISABLE
</script>


<form method="post" id="shipmentCostDataForm">
    <input type="hidden" name="action" id="action" value="shipment_cost_data"/>
    <input type="hidden" name="shipmentCostID" id="shipmentCostID" value="<?php echo $shipmentCostID; ?>"/>

    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
             createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileID, "", "stockpileID", "stockpile_id", "stockpile_name", "",  1, 'select2combobox100');
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Biaya <span style="color: red;">*</span></label>
            <?php
             createCombo("SELECT account_id, account_name FROM account", $accountID, "", "accountID", "account_id", "account_name", "",  1, 'select2combobox100');
            ?>
        </div>
    </div>

    <div class="row-fluid" style="margin-bottom: 7px;">  

        <div class="span3 lightblue">
            <label>Type</label>
            <input type="text" placeholder="Input Type" tabindex="" id="tipe1" name="tipe1" class="span12" value="<?php echo $tipe1; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Vendor Type <span style="color: red;">*</span></label>
            <?php
                    createCombo("SELECT '1' as id, 'Pks' as info UNION
                    SELECT '2' as id, 'General' as info UNION
                    SELECT '3' as id, 'Freight' as info  UNION
                    SELECT '4' as id, 'Labor' as info UNION
                    SELECT '5' as id, 'Handling' as info UNION
                    SELECT '6' as id, 'PettyCash' as info;", $vendorType, '', "vendorType", "info", "info",
            "", 21, 'select2combobox100');
            ?>
        </div>

        <div class="span3 lightblue">
            <div class="hidden" id="vendorPks">
                <label>Vendor Pks <span style="color: red;">*</span></label>
                <?php
                $sql = "SELECT CONCAT(vendor_id,'-',vendor_name) as vendor_name_id, vendor_name FROM vendor ORDER BY vendor_name ASC";
                createCombo($sql, $vendorName, '', "vendorNamePks", "vendor_name_id", "vendor_name",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="hidden" id="vendorGeneral">
                <label>Vendor General <span style="color: red;">*</span></label>
                <?php
                $sql = "SELECT general_vendor_name,CONCAT(general_vendor_id,'-',general_vendor_name) as general_vendor_name_id FROM general_vendor ORDER BY general_vendor_name ASC";
                createCombo($sql, $vendorName, '', "vendorNameGeneral", "general_vendor_name_id", "general_vendor_name",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="hidden" id="vendorFreight">
                <label>Vendor Freight <span style="color: red;">*</span></label>
                <?php
                $sql = "SELECT freight_supplier, CONCAT(freight_id,'-',freight_supplier) as freight_supplier_id FROM freight ORDER BY freight_supplier ASC";
                createCombo($sql, $vendorName, '', "vendorNameFreight", "freight_supplier_id", "freight_supplier",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="hidden" id="vendorLabor">
                <label>Vendor Labor <span style="color: red;">*</span></label>
                <?php
                $sql = "SELECT labor_name, CONCAT(labor_id,'-',labor_name) as labor_name_id FROM labor ORDER BY labor_name ASC";
                createCombo($sql, $vendorName, '', "vendorNameLabor", "labor_name_id", "labor_name",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="hidden" id="vendorHandling">
                <label>Vendor Handling <span style="color: red;">*</span></label>
                <?php
                $sql = "SELECT vendor_handling_name,CONCAT(vendor_handling_id,'-',vendor_handling_name) as vendor_handling_name_id FROM vendor_handling ORDER BY vendor_handling_name ASC";
                createCombo($sql, $vendorName, '', "vendorNameHandling", "vendor_handling_name_id", "vendor_handling_name",
                    "", 21, 'select2combobox100');
                ?>
            </div>
            <div class="hidden" id="vendorPettyCash">
                <label>Vendor PettyCash <span style="color: red;">*</span></label>
                <?php
                $sql = "SELECT vendor_name,CONCAT(vendor_pc_id,'-',vendor_name) as vendor_name_id FROM vendor_pettycash ORDER BY vendor_name ASC";
                createCombo($sql, $vendorName, '', "vendorNamePettyCash", "vendor_name_id", "vendor_name",
                    "", 21, 'select2combobox100');
                ?>
            </div>
        </div>

    <div class="row-fluid" style="margin-bottom: 7px;">   
        
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">   
        
        </div>
        <div class="row-fluid" style="margin-bottom: 7px;">  
            <div class="span3 lightblue">
                <label>Currency <span style="color: red;">*</span></label>
                    <?php
                    createCombo("SELECT cur.*
                            FROM currency cur
                            ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                            "", 1, 'select2combobox100');
                    ?>
            </div>
            <div class="span3 lightblue" id="exchangeRate">
                    <label>Exchange Rate to IDR</label>
                    <input type="text" class="span12" tabindex="" id="exchangeRate1" name="exchangeRate1" value="<?php echo $exchangeRate1; ?>" />
            </div>
        </div>

    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
            <label>Price/MT <span style="color: red;">*</span></label>
            <input type="text" class="span10"  tabindex="" id="priceMT" name="priceMT" value="<?php echo $priceMT; ?>" />
        </div>
        <div class="span3 lightblue">
            <label>Flat Price/Additional</label>
            <input type="text" class="span10"  tabindex="" id="flatPrice" name="flatPrice" value="<?php echo $flatPrice; ?>" />
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>