<?php
error_rePOrting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'PODetail';

$POMethod = 2;
$accountId = '';
$POType = '';
$generatedPONo = '';
$generalVendorId = '';
$pph1 = 0;
$ppnPO1 = '';
$ppnPOID = '';
$pphID = '';
$pphstatus = 0;
$itemId = '';
$stockpileId = '';
$shipmentId = '';
$signId = '';
$groupitemId = '';
$requestDate = '';
$podID = '';
$method_ = "INSERT";
$pphId = '';

if (isset ($_POST['generalVendorId']) && $_POST['generalVendorId'] != '') {

    $generalVendorId = $_POST['generalVendorId'];
}
if (isset ($_POST['requestDate']) && $_POST['requestDate'] != '') {

    $requestDate = $_POST['requestDate'];
}
if (isset ($_POST['generatedPONo']) && $_POST['generatedPONo'] != '') {

    $generatedPONo = $_POST['generatedPONo'];
}
if (isset ($_POST['detail_poId']) && $_POST['detail_poId'] != '') {

    $podID = $_POST['detail_poId'];
}

if (isset($_POST['POId']) && $_POST['POId'] != '') {

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT *
            FROM PO_detail
            WHERE no_PO = {$generatedPONo}
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
    }
}

if (isset ($_POST['POMethod']) && $_POST['POMethod'] != '') {

    $POMethod = $_POST['POMethod'];
}

if($podID != ''){
    $sql = "SELECT * FROM PO_detail WHERE idpo_detail = {$podID}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_object();
        $stockpileId = $row->stockpile_id;
        $shipmentId = $row->shipment_id;
        $itemId = $row->item_id;
        $qty = $row->qty;
        $price = $row->harga;
        $termin = $row->termin;
        $amount = $row->amount;
        $remarks = $row->notes;
        $pphId = $row->pph_id;
        $method_ = "UPDATE";
    }
}
/*
else {
    $generatedPONo = "";
    if(isset($_SESSION['PO'])) {

    }
}
*/

if (isset($_SESSION['PODetail'])) {
    $POType = $_SESSION['PODetail']['POType'];
    $generatedPONo = $_SESSION['PODetail']['generatedPONo'];
}
// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {

        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if ($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
    } else if ($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
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
        if (strtoupper($setvalue) == "INSERT") {
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
    $(document).ready(function () {
        $("select.select2combobox100").select2({
            width: "100%"
        });

        $("select.select2combobox50").select2({
            width: "50%"
        });
        
        $("select.select2combobox75").select2({
            width: "75%"
        });


        <?php if($podID != ''){ ?>
                getGeneralVendorTax(<?php echo $generalVendorId ?>, <?php echo $amount ?>);
                setPPh(1,<?php echo $generalVendorId; ?>, <?php echo $pphId ?>);
        <?php } ?>

        <?php if($generalVendorId != '' && $POMethod == 1) {?>
            setInvoiceDP(<?php echo  $generalVendorId ?>, '', '<?php echo  $method_ ?>');
        <?php } ?>
        
        $('#ppnPO1').number(true, 10);
        $('#qty').number(true, 10);price
        $('#price').number(true, 10);
       
        sum();

        $("#qty, #price, #termin").on("keydown keyup", function () {
            sum();
            $('#amount').number(true, 10);
            if (document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
                getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
            }
        });

        if (document.getElementById('generatedPONo').value != "") {
            document.getElementById('POId').value = document.getElementById('generatedPONo').value;
        } else {
            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: {
                    action: 'getPONo'
                },
                success: function (data) {
                    if (data != '') {
                        document.getElementById('generatedPONo').value = data;
                        $('#addPO').hide();

                    }
                }
            });
        }

        $('#POType').change(function () {
            if (document.getElementById('POType').value != '') {
                resetAccount(' ');
                setAccount(0, $('select[id="POType"]').val(), 0);
            } else {
                resetAccount(' PO Type ');
            }
        });
       

        $('#generalVendorId').change(function () {
            $('#IDP').hide();

            if (document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
                getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
            }
        });

        if (document.getElementById('generalVendorId').value != '' && (document.getElementById('pod_id').value == '' || document.getElementById('pod_id').value == 0) ) {
            getGeneralVendorTax(1, $('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
            setPPh(1,<?php echo $generalVendorId; ?>, <?php echo $pphId ?>);
        }
    });

    function resetGeneralVendorTax() {
        document.getElementById('ppnPO1').value = 0;
        document.getElementById('ppnPOID').value = 0;
    }

    function getGeneralVendorTax(generalVendorId, amount) {
        if (amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: {
                    action: 'getGeneralVendorTax',
                    generalVendorId: generalVendorId,
                    amount: amount
                },
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[0]) != 0)	//if no errors
                    {
                        var checkbox = document.getElementById('ppnpostatus1');
                        if (checkbox.checked != true) {
                            document.getElementById('ppnPO1').value = '0';
                        } else {
                            document.getElementById('ppnPO1').value = returnVal[1];
                        }

                        var checkbox = document.getElementById('pphstatus1');
                        document.getElementById('ppnPOID').value = returnVal[3];
                    }
                }
            });
        } else {
            document.getElementById('ppnPO1').value = '0';
            document.getElementById('ppnPOID').value = '0';
        }
    }

    function setPPh(type, generalVendorId, pphId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGeneralVendorPPh',
                generalVendorId: generalVendorId

            },
            success: function (data) {
                //alert(data);
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('pphTaxId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('pphTaxId').options.add(x);
                    }

                    var x = document.createElement('option');
                    x.value = 0;
                    x.text = 'NONE';
                    document.getElementById('pphTaxId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('pphTaxId').options.add(x);
                    }

                    if (type == 1) {
                        $('#pphTaxId').find('option').each(function(i,e){
                            if($(e).val() == pphId){
                                $('#pphTaxId').prop('selectedIndex',i);        
                                    $("#pphTaxId").select2({
                                        width: "100%",
                                        placeholder: pphId
                                });
                            }
                        });
                    }

                }
            }
        });
    }

    function checkppnpostatus() {
        var checkbox = document.getElementById('ppnpostatus1');
        if (checkbox.checked != true) {
            document.getElementById('ppnpostatus').value = 0;
        } else {
            document.getElementById("ppnPO1").attributes["type"].value = "text";
        }
    }

    function sum() {
        var result = 0;
        var num1 = document.getElementById('qty').value.replace(new RegExp(",", "g"), "");
        var num2 = document.getElementById('price').value.replace(new RegExp(",", "g"), "");
        var num3 = document.getElementById('ppnPO1').value;
        var num4 = document.getElementById('termin').value;
        var result = (parseFloat(num1) * parseFloat(num2) * (parseFloat(num4) / 100));
        document.getElementById('amount').value = result;
    }

    function setInvoiceDP(generalVendorId, ppn1, method) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setInvoiceDP',
                generalVendorId: generalVendorId,
                ppn1: ppn1,
                method: method
            },
            success: function (data) {
                if (data != '') {
                    $('#IDP').show();
                    document.getElementById('IDP').innerHTML = data;
                }
            }
        });
    }
</script>

<input type="hidden" id="action" name="action" value="PO_detail">
<input type="hidden" class="span12" readonly id="generalVendorId" name="generalVendorId" value="<?php echo $generalVendorId; ?>">
<input type="hidden" class="span12" readonly id="requestDate" name="requestDate" value="<?php echo $requestDate; ?>">
<input type="hidden" id="method_" name="method_" value="<?php echo $method_; ?>">
<input type="hidden" id="pod_id" name="pod_id" value="<?php echo $podID; ?>">
<input type="hidden" id="POMethod" name="POMethod" value = "<?php echo $POMethod ?>">

<div class="row-fluid">
    <div class="span3 lightblue">
        <input type="hidden" class="span12" readonly id="generatedPONo" name="generatedPONo"
               value="<?php echo $generatedPONo; ?>">
    </div>

</div>
<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span3 lightblue">
        <label>Stockpile <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
        ?>
    </div>
    <div class="span1 lightblue">
    </div>

    <div class="span3 lightblue"></div>
</div>
<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span6 lightblue">
        <label>Shipment No <span style="color: red;"></span></label>
        <?php
        createCombo("SELECT shipment_id, shipment_no
                        FROM shipment", $shipmentId, $readonlyProperty, "shipmentId", "shipment_id", "shipment_no",
            "", "", "select2combobox100", 1, "", true);
        ?>
    </div>
</div>
<!--   <div class="row-fluid" style="margin-bottom: 7px;">-->
<!--           <div class="span6 lightblue">-->
<!--          <label>Group Item<span style="color: red;">*</span></label>-->
<!--          --><?php
//            createCombo("SELECT *
//                        FROM master_groupitem", $groupitemId, $readonlyProperty, "groupitemId", "idmaster_groupitem", "group_name",
//                "", "", "select2combobox100", 1, "", true);
//            ?>
<!--        </div>-->
<!--      </div>-->
<div class="row-fluid">
    <div class="span3 lightblue">
        <label>Item <span style="color: red;">*</span> </label>
        <span class="span15 lightblue">
	  <?php
      createCombo("SELECT CONCAT(i.item_name,' - ',group_name,' - ',u.uom_type) AS name, idmaster_item FROM master_item i
LEFT JOIN master_groupitem mg ON mg.idmaster_groupitem = i.group_itemid
LEFT JOIN uom u ON u.idUOM = i.uom_id", $itemId, "", "itemId", "idmaster_item", "name", "", "", "select2combobox100", 1);
      ?>
	  </span></div>

    <div class="span3 lightblue">
        <label>Qty<span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="" id="qty" name="qty" value = "<?php echo $qty ?>">
    </div>
    <div class="span3 lightblue">
        <label>Unit Price <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="" id="price" name="price" value = "<?php echo $price ?>">
    </div>
    <div class="span2 lightblue">
        <label>Termin %<span style="color: red;">*</span></label>
        <input type="text" class="span10" tabindex="" id="termin" name="termin" min="0" max="100" value = "<?php echo $termin ?>">
    </div>
   
</div>
<div class="row-fluid">
    <div class="span3 lightblue">
        <label>Amount</label>
        <input type="text" readonly class="span12" tabindex="" id="amount" name="amount"  value = "<?php echo $amount ?>">

    </div>
    <div class="span2 lightblue">
        <label>PPN</label>
        <input type="text" class="span12" tabindex="" readonly id="ppnPO1" name="ppnPO1"
               value="<?php echo $_SESSION['PODetail']['ppnPO1']; ?>">
        <input type="hidden" class="span12" id="ppnPOID" name="ppnPOID"
               value="<?php echo $_SESSION['PODetail']['ppnPOID']; ?>">

        <input name="ppnpostatus1" type="checkbox" id="ppnpostatus1" onclick="checkppnpostatus()" checked="checked">
        <label for="checkbox">Yes </label>
        <span class="span4 lightblue">
			<input type="hidden" class="span12" readonly id="ppnpostatus" name="ppnpostatus" value="1">
    </div>

    <div class="span3 lightblue">
        <label>PPh<span style="color: red;">*</span></label>
        <?php createCombo("", $pphTaxId, "", "pphTaxId", "id", "info", "", "", "select2combobox100", 4); ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span12" rows="1" tabindex="" id="notes" name="notes"><?php echo $remarks ?></textarea>
    </div>
</div>

<div class="row-fluid" id="IDP" style="display: none;">
    Invoice DP
</div>
