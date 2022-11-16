<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';


// <editor-fold defaultstate="collapsed" desc="Variable for Mutasi Detail">

$mutasiId = $_POST['mutasiId'];
$mutasiDetailId = '';
$kodeMutasi = '';
$tipeBiaya = '';
$vendorType = '';
$vendorName = '';
$qty = '';
$price = '';
$total = '';
$pph = '';
$ppn = '';
$ppnID = '';
$pphID = '';
$accountId = '';
$terminId = '';
$generalVendorId = '';
$currencyId = '';
$exchangeRate = '';
$terminPercentage = '';

// </editor-fold>
if (isset($mutasiId) && $mutasiId != '') {

    $sql = "SELECT mh.* FROM mutasi_header mh  WHERE mh.mutasi_header_id = {$mutasiId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $kodeMutasi = $rowData->kode_mutasi;
    }
}

// If ID is in the parameter
if (isset($_POST['mutasiDetailId']) && $_POST['mutasiDetailId'] != '') {
    $actionType = 'UPDATE';
    $mutasiDetailId = $_POST['mutasiDetailId'];

    $sql = "SELECT md.*, mh.kode_mutasi, CONCAT(md.general_vendor_id, '-', md.vendor) as vendor_name, td.percentage,  c.currency_id
            FROM mutasi_detail md 
            LEFT JOIN mutasi_header as mh ON mh.mutasi_header_id = md.mutasi_header_id
            LEFT JOIN termin_detail as td ON td.id = md.termin_detail_id
            LEFT JOIN currency as c on md.currency_id = c.currency_id
            WHERE md.mutasi_detail_id = {$mutasiDetailId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $mutasiDetailId = $rowData->mutasi_detail_id;
        $kodeMutasi = $rowData->kode_mutasi;
        $tipeBiaya = $rowData->tipe_biaya;
        $qty = $rowData->qty;
        $ppn = $rowData->ppn;
        $pph = $rowData->pph;
        $vendorType = $rowData->vendor_type;
        $vendorName = $rowData->vendor_name;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $price = $rowData->price;
        $total = $rowData->total;
        $accountId = $rowData->account_id;
        $terminPercentage = $rowData->percentage;
        $generalVendorId = $rowData->general_vendor_id;
    }

} else {
    $actionType = 'INSERT';
}

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";

    if ($empty == 1) {
        echo "<option value='NULL'>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Category --</option>";
    } else if ($empty == 3) {
        echo "<option value='NULL'>-- Please Select PPH--</option>";
        echo "<option value='NULL'>NONE</option>";
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
    $('#currencyId').change(function () {
        let currency = document.getElementById("currencyId").value;
        if (currency == 'NULL' || currency == '1'){
            document.getElementById("rate").value = 1;
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
    });

    function parseCurrency(num) {
        return parseFloat(num.replace(/,/g, ''));
    }

    $('#pphVendorId').change(function () {
        console.log(document.getElementById('pphVendorId').value);
    });
    $('#rate').change(function () {
        let rate = document.getElementById("rate").value;
        let qty = document.getElementById("qty").value;
        let price = document.getElementById("price").value;
        let total = parseCurrency(qty) * parseCurrency(price) * parseCurrency(rate);
        document.getElementById("total").value = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    });
    $('#price').change(function () {
        let rate = document.getElementById("rate").value;
        let qty = document.getElementById("qty").value;
        let price = document.getElementById("price").value;
        let total = parseCurrency(qty) * parseCurrency(price) * parseCurrency(rate);
        document.getElementById("total").value = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    });
    $('#qty').change(function () {
        let rate = document.getElementById("rate").value;
        let qty = document.getElementById("qty").value;
        let price = document.getElementById("price").value;
        let total = parseCurrency(qty) * parseCurrency(price) * parseCurrency(rate);
        document.getElementById("total").value = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    });

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
        $('#qty').number(true, 2);
        $('#price').number(true, 9);
        $('#total').number(true, 2);
        $('#rate').number(true, 2);


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


    $('#vendorNamePks').change(function () {
        document.getElementById("ppnVendor").classList.remove("hidden");
        document.getElementById("pphVendor").classList.remove("hidden");
        getPPNPPH();
    });
    $('#vendorNameGeneral').change(function () {
        document.getElementById("ppnVendor").classList.remove("hidden");
        document.getElementById("pphVendor").classList.add("hidden");
        document.getElementById("pphVendorList").classList.remove("hidden");
        getPPNPPH();
    });
    $('#vendorNameFreight').change(function () {
        document.getElementById("ppnVendor").classList.remove("hidden");
        document.getElementById("pphVendor").classList.remove("hidden");
        getPPNPPH();
    });
    $('#vendorLabor').change(function () {
        document.getElementById("ppnVendor").classList.remove("hidden");
        document.getElementById("pphVendor").classList.remove("hidden");
        getPPNPPH();
    });
    $('#vendorHandling').change(function () {
        document.getElementById("ppnVendor").classList.remove("hidden");
        document.getElementById("pphVendor").classList.remove("hidden");
        getPPNPPH();
    });
    $('#vendorPettyCash').change(function () {
        document.getElementById("ppnVendor").classList.remove("hidden");
        document.getElementById("pphVendor").classList.remove("hidden");
        getPPNPPH();
    });

    function getPPNPPH() {
        const vendorType = document.getElementById('vendorType').value;
        if (vendorType == 'Pks') {
            var vendorName = document.getElementById("vendorNamePks").value;
        } else if (vendorType == 'Freight') {
            var vendorName = document.getElementById("vendorNameFreight").value;
        } else if (vendorType === 'General') {
            var vendorName = document.getElementById("vendorNameGeneral").value;
            var vendor = vendorName.split('-');
            setGeneralVendorPPH(0, vendor[0])
            //Set Vendor PPN
            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: {
                    action: 'getGeneralVendorPPN',
                    generalVendorId: vendor[0],
                },
                success: function (data) {
                    console.log(data);
                    var returnVal = data.split('-');
                    document.getElementById('ppn').value = returnVal[0];
                    document.getElementById('ppnTaxId').value = returnVal[1];
                }
            });
            console.log(vendor[0]);
        } else if (vendorType === 'Labor') {
            var vendorName = document.getElementById("vendorNameLabor").value;
        } else if (vendorType === 'Handling') {
            var vendorName = document.getElementById("vendorNameHandling").value;
        } else if (vendorType === 'PettyCash') {
            var vendorName = document.getElementById("vendorNamePettyCash").value;
        }

        //Set PPN & PPH Jika Vendor Bukan General
        if (vendorType != 'General') {
            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: {
                    action: 'getPPNPPH',
                    vendorType: vendorType,
                    vendorName: vendorName,
                },
                success: function (data) {
                    var returnVal = data.split('-');
                    console.log(returnVal[3]);
                    document.getElementById('ppn').value = returnVal[0];
                    document.getElementById('pph').value = returnVal[1];
                    document.getElementById('ppnTaxId').value = returnVal[2];
                    document.getElementById('pphTaxId').value = returnVal[3];
                }
            });
        }

    }

    function setGeneralVendorPPH(type, generalVendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getGeneralVendorPPh',
                generalVendorId: generalVendorId,
            },
            success: function (data) {
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
                        document.getElementById('pphVendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = 'NULL';
                        x.text = '-- Please Select --';
                        document.getElementById('pphVendorId').options.add(x);
						x.value = 'NULL';
						x.text = 'NONE';
						document.getElementById('pphVendorId').options.add(x);

                        $("#invCategory").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('pphVendorId').options.add(x);
                    }

                    <?php
                    if($allowContract) {
                    ?>
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('pphVendorId').options.add(x);
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#pphVendorId').find('option').each(function (i, e) {
                            if ($(e).val() == pphVendorId) {
                                $('#invCategory').prop('selectedIndex', i);
                            }
                        });
                    }
                }
            }
        });
    }

    function checkPPN() {
        var checkedPPN = document.getElementById('checkedPPN');
        // var generalVendorId = document.getElementById('vendorNameGeneral').value;
        if (checkedPPN.checked != true) {
            document.getElementById('ppn').value = 0;
        } else {
            getPPNPPH();
        }
    }

</script>


<input type="hidden" name="action" id="action" value="mutasi_detail"/>
<input type="hidden" name="actionType" id="actionType" value="<?php echo $actionType ?>"/>
<input type="hidden" name="mutasiId" id="mutasiId" value="<?php echo $_POST['mutasiId']; ?>"/>
<input type="hidden" name="mutasiDetailId" id="mutasiDetailId" value="<?php echo $mutasiDetailId ?>"/>

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Kode Mutasi</label>
        <input type="text" placeholder="Input Tipe Biaya" tabindex="" id="kodeMutasi" name="kodeMutasi"
               class="span12"
               value="<?php echo $kodeMutasi; ?>" disabled>
    </div>
    <div class="span6 lightblue">
        <label>Tipe Biaya<span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT id, tipe_biaya FROM master_biaya_mutasi ORDER BY entry_date DESC", $tipeBiaya, '', "tipeBiaya", "id", "tipe_biaya",
            "", 21, 'select2combobox100');
        ?>
    </div>
</div>
<div class="row-fluid">

    <div class="span6 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code",
                    "", "", "select2combobox100");
            ?>
    </div>
   <div class="span6 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="rate" name="exchangeRate" value="<?php echo number_format($exchangeRate, 0, ".", ","); ?>">
   </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Qty<span style="color: red;">*</span></label>
        <input type="text" placeholder="Input Quantity/KG" tabindex="" id="qty" name="qty"
               class="span12"
               value="<?php echo number_format($qty, 0, ".", ","); ?>">
    </div>
    <div class="span6 lightblue">
        <label>Price<span style="color: red;">*</span></label>
        <input type="text" placeholder="Input Price/KG" tabindex="" id="price" name="price"
               class="span12"
               value="<?php echo number_format($price, 0, ".", ","); ?>">
    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Total</label>
        <input type="text" placeholder="Input Total" tabindex="" id="total" name="total"
               class="span12"
               value="<?php echo number_format($total, 0, ".", ","); ?>">
    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <label>COA <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT account_id, CONCAT(account_no, ' - ', account_name) as coa FROM account where account_type = 4 ORDER BY account_name ASC", $accountId, '', "accountId", "account_id", "coa",
            "", 21, 'select2combobox100');
        ?>
    </div>
    <div class="span6 lightblue">
        <label>Termin <span style="color: red;">*</span></label>
        <?php
        if ($actionType == 'INSERT') {
            createCombo("SELECT * FROM master_termin ORDER BY name ASC", $terminId, '', "terminId", "id", "name",
                "", 21, 'span12');
            ?>
        <?php } else { ?>
            <input type="text" class="span12" value="<?php echo $terminPercentage . '%'; ?>" disabled>
            <input type="hidden" id="terminPercentage" name="terminPercentage" value="<?php echo $terminPercentage; ?>">
        <?php } ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span6 lightblue">
        <label>Vendor Type <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT '1' as id, 'Pks' as info UNION
                    SELECT '2' as id, 'General' as info UNION
                    SELECT '3' as id, 'Freight' as info  UNION
                    SELECT '4' as id, 'Labor' as info UNION
                    SELECT '5' as id, 'Handling' as info UNION
                    SELECT '6' as id, 'PettyCash' as info;", $vendorType, '', "vendorType", "info", "info",
            "", 21, 'span12');
        ?>
    </div>
    <div class="span6 lightblue">
        <div class="hidden" id="vendorPks">
            <label>Vendor Pks</label>
            <?php
            $sql = "SELECT CONCAT(vendor_id,'-',vendor_name) as vendor_name_id, vendor_name FROM vendor ORDER BY vendor_name ASC";
            createCombo($sql, $vendorName, '', "vendorNamePks", "vendor_name_id", "vendor_name",
                "", 21, 'select2combobox100');
            ?>
        </div>
        <div class="hidden" id="vendorGeneral">
            <label>Vendor General</label>
            <?php
            $sql = "SELECT general_vendor_name,CONCAT(general_vendor_id,'-',general_vendor_name) as general_vendor_name_id FROM general_vendor ORDER BY general_vendor_name ASC";
            createCombo($sql, $vendorName, '', "vendorNameGeneral", "general_vendor_name_id", "general_vendor_name",
                "", 21, 'select2combobox100');
            ?>
        </div>
        <div class="hidden" id="vendorFreight">
            <label>Vendor Freight</label>
            <?php
            $sql = "SELECT freight_supplier, CONCAT(freight_id,'-',freight_supplier) as freight_supplier_id FROM freight ORDER BY freight_supplier ASC";
            createCombo($sql, $vendorName, '', "vendorNameFreight", "freight_supplier_id", "freight_supplier",
                "", 21, 'select2combobox100');
            ?>
        </div>
        <div class="hidden" id="vendorLabor">
            <label>Vendor Labor</label>
            <?php
            $sql = "SELECT labor_name, CONCAT(labor_id,'-',labor_name) as labor_name_id FROM labor ORDER BY labor_name ASC";
            createCombo($sql, $vendorName, '', "vendorNameLabor", "labor_name_id", "labor_name",
                "", 21, 'select2combobox100');
            ?>
        </div>
        <div class="hidden" id="vendorHandling">
            <label>Vendor Handling</label>
            <?php
            $sql = "SELECT vendor_handling_name,CONCAT(vendor_handling_id,'-',vendor_handling_name) as vendor_handling_name_id FROM vendor_handling ORDER BY vendor_handling_name ASC";
            createCombo($sql, $vendorName, '', "vendorNameHandling", "vendor_handling_name_id", "vendor_handling_name",
                "", 21, 'select2combobox100');
            ?>
        </div>
        <div class="hidden" id="vendorPettyCash">
            <label>Vendor PettyCash</label>
            <?php
            $sql = "SELECT vendor_name,CONCAT(vendor_pc_id,'-',vendor_name) as vendor_name_id FROM vendor_pettycash ORDER BY vendor_name ASC";
            createCombo($sql, $vendorName, '', "vendorNamePettyCash", "vendor_name_id", "vendor_name",
                "", 21, 'select2combobox100');
            ?>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span6 lightblue">
        <div class="hidden" id="ppnVendor">
            <label>PPN</label>
            <input type="text" readonly class="span12" tabindex="" id="ppn" name="ppn"
                   value="<?php echo $ppn; ?>">
            <input type="checkbox" name="checkedPPN" id="checkedPPN" onclick="checkPPN()" checked/>
        </div>
    </div>
    <div class="span6 lightblue">
        <div class="hidden" id="pphVendor">
            <label>PPH</label>
            <input type="text" readonly class="span12" tabindex="" id="pph" name="pph"
                   value="<?php echo $pph; ?>">
        </div>
        <div class="hidden" id="pphVendorList">
            <label>PPh</label>
            <?php createCombo("", $generalVendorId, "", "pphVendorId", "id", "info", "", "", "select2combobox100", 3); ?>
        </div>
    </div>
</div>
<input type="hidden" class="span12" tabindex="" id="ppnTaxId" name="ppnTaxId" value="">
<input type="hidden" class="span12" tabindex="" id="pphTaxId" name="pphTaxId" value="">


