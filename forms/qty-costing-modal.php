<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$tempID = $_POST['tempID'];
$type = '';
$accountId = '';
$spRemark = '';
$generalVendorId = '';
$notes = '';
$qty = '';
$price = '';
$termin = '';
$amount = '';
$shipmentId = '';
$poId = '';
$joinProperty = '';
$readonly = '';

// <editor-fold defaultstate="collapsed" desc="Functions">


if(isset($_POST['exRateCosting']) && $_POST['exRateCosting'] != '') {
    $exRateCosting = $_POST['exRateCosting'];
}

$sql = "SELECT tmc.*, tb.tipe_biaya as qtyType, 
        gv.general_vendor_name as gvendor,  
        u.uom_type as uom,
        CASE WHEN tmc.qty_type_id = 1 THEN 'Vessel' WHEN tmc.qty_type_id = 2 THEN 'Tongkang' ELSE 'Timbangan' END as qtytypetext
        FROM temp_mst_costing  tmc
        LEFT JOIN mst_tipe_biaya tb ON tb.id = tmc.nama_biaya 
        LEFT JOIN general_vendor gv ON gv.general_vendor_id = tmc.general_vendor_id
        LEFT JOIN uom u ON u.idUOM = tmc.uom_id 
        WHERE tmc.id = {$tempID}";
       // echo  $sql;
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if ($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    $qty = $row->qty_value;
    $code = $row->generate_code;
    $qtyType = $row->qtyType;
    $gvendor = $row->gvendor;
    $uom = $row->uom;
    $maxused = number_format($row->maxused, 0, ".", ",");
    $minused = number_format($row->minused, 0, ".", ",");
    $price = number_format($row->price, 0, ".", ",");
    $total = number_format($row->total_amount, 0, ".", ",");
    $exrate = $row->kurs;
    $inrupiah = $row->rupiah;
    $qtytypetext = $row->qtytypetext;
    $accountId = $row->account_id;
    if($row->qty_type_id == 1){
        $readonly = 'readonly';
    }
    if($row->currency != 1){
        $exchangeRate = $exRateCosting;
    }else if($row->currency == 1){
        $exchangeRate = $exrate;
    }
}

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT style='width: 150%;' class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }
    
    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";
        
        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }
    
    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }
    
    echo "</SELECT>";
}

?>
<script type="application/javascript">
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
        $('#qty').number(true, 2);

        $('#qty').change(function() {
            if(document.getElementById('qty').value != '') {
                hitungCosting($('input[id="qty"]').val(), <?php echo $tempID ?>);
            }
        });
    });

    function hitungCosting(qty, tempID) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'hitungCosting',
                    qty: qty,
                    tempID: tempID,
            },
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[3])>0)	//if no errors
                {
                    document.getElementById('maxused').value = returnVal[1];
                    document.getElementById('minused').value = returnVal[2];
                    document.getElementById('total').value = returnVal[3];
                    document.getElementById('inrupiah').value = returnVal[4];
                }else{
                    alertify.alert(returnVal[2]);
                }
            }
        });
    }



</script>

<div id="addDetailCostingForms">
    <input type="hidden" name="action" id="action" value="update_qty_costing">
    <input type="hidden" name="tempID" value="<?php echo $tempID ?>">
    <input type="hidden" id="exrate" name="exrate" value="<?php echo $exchangeRate ?>" >
    <input type="hidden" readonly id="inrupiah" name="inrupiah" value="<?php echo $inrupiah ?>">

    <input type="hidden" name="_method" value="UPDATE">

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Code</label>
            <input type="text" class="span12" tabindex="" id="code" name="code" value="<?php echo $code ?>" readonly>
        </div>
        <div class="span3 lightblue">
            <label>Qty Type</label>
            <input type="text" class="span12" tabindex="" id="qtyType" name="qtyType" value="<?php echo $qtyType ?>" readonly>
        </div>

        <div class="span4 lightblue">
            <label>General Vendor</label>
            <input type="text" class="span12" tabindex="" id="gvendor" name="gvendor" value="<?php echo $gvendor ?>" readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Max Charge</label>
            <input type="text" tabindex="" id="maxused" name="maxused" value="<?php echo $maxused ?>" readonly>
        </div>
        <div class="span3 lightblue">
            <label>Min Charge</label>
            <input type="text"  tabindex="" id="minused" name="minused" value="<?php echo $minused ?>" readonly>
        </div>
        <div class="span1 lightblue" style="width: 4%;" >
            <label>UOM</label>
            <input type="text" style="width: 90%;"  id="uom" name="uom" value="<?php echo $uom ?>" readonly>
        </div>
        <div class="span1 lightblue" style="width: 8%;"  >
            <label>Qty Type</label>
            <input type="text" style="width: 105%;" id="qtytypetext" name="qtytypetext" value="<?php echo $qtytypetext ?>" readonly>
        </div>
       
    </div>
    <div class="row-fluid">
        <div class="span1 lightblue" style="width: 25%;">
            <label>Account Name  <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT DISTINCT(a.account_id), CONCAT(a.account_no, ' - ', a.account_name) as fullName, a.* FROM account a 
                            WHERE a.account_type = 4 order by a.account_id ASC", $accountId, '', "accountId", "account_id", "fullName", 
                "", 21, "select2combobox100");
            ?>
        </div>
        <div class="span1 lightblue"  style="width: 8%;">
            <label>Qty</label>
            <input type="text" id="qty"  style="width: 100%;"  name="qty" value="<?php echo $qty ?>" <?php echo $readonly ?>>
        </div>
        <div class="span2 lightblue" style="width: 12%;">
            <label>Price/MT</label>
            <input type="text"  style="width: 100%;"  id="price" name="price" value="<?php echo $price ?>" readonly>
        </div>
        <div class="span2 lightblue">
            <label>Total Amount</label>
            <input type="text"    id="total"  style="width: 115%;" name="total" value="<?php echo $total ?>" readonly>
        </div>
    </div>
</div>
