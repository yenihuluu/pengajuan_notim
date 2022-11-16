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


// <editor-fold defaultstate="collapsed" desc="Variable for Logbook Data">

$pLogbookId = '';
$stockpileId = '';
$noRekening = '';
$vendorType = '';
$vendorName = '';
$ppn = '';
$pph = '';
$dpp = '';
$staus = '';
$taxRemark = '';
$remark = '';
$keteranganIncomplete = '';
$file = '';
$qty = '';
$cabangBank = '';
$namaAkunBank = '';
$hargaQty = '';
$masterBankId = '';
// </editor-fold>

// If ID is in the parameter
if (isset($_POST['pLogbookId']) && $_POST['pLogbookId'] != '') {

    $pLogbookId = $_POST['pLogbookId'];
    // <editor-fold defaultstate="collapsed" desc="Query for Logbook Data">

    $sql = "SELECT  pl.* FROM pengajuan_logbook pl WHERE id = {$pLogbookId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();

        $stockpileId = $rowData->stockpile_id;
        $noRekening = $rowData->no_rek;;
        $vendorType = $rowData->vendor_type;
        $vendorName = $rowData->vendor;
        $ppn = $rowData->ppn;
        $pph = $rowData->pph;
        $dpp = $rowData->dpp;
        $status = $rowData->status;
        $taxRemark = $rowData->tax_remark;
        $remark = $rowData->remark;
        $keteranganIncomplete = $rowData->keterangan_incomplete;
        $file = $rowData->file;
        $qty = $rowData->qty;
        $masterBankId = $rowData->master_bank_id;
        $cabangBank = $rowData->cabang_bank;
        $namaAkunBank = $rowData->nama_akun_bank;
        $hargaQty = $rowData->harga_qty;
    }
    // </editor-fold>
    $actionType = 'UPDATE';
} else {
    $actionType = 'INSERT';

}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Category --</option>";
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

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
</script>

<script type="text/javascript">

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

    $('#qty').change(function () {
        let qty = document.getElementById('qty').value;
        let hargaQty = document.getElementById('hargaQty').value;
        document.getElementById('dpp').value = qty * hargaQty;
    });

    $('#hargaQty').change(function () {
        let qty = document.getElementById('qty').value;
        let hargaQty = document.getElementById('hargaQty').value;
        document.getElementById('dpp').value = qty * hargaQty;
    });

    $('#ppn').change(function () {
        var qty = document.getElementById('qty').value;
        var hargaQty = document.getElementById('hargaQty').value;
        var ppn = document.getElementById('ppn').value;
        var pph = document.getElementById('pph').value;
        var dpp = qty * hargaQty;
        var total = parseInt(dpp) + parseInt(ppn);

        document.getElementById('total').value = total - parseInt(pph);
    });

    $('#pph').change(function () {
        var qty = document.getElementById('qty').value;
        var hargaQty = document.getElementById('hargaQty').value;
        var ppn = document.getElementById('ppn').value;
        var pph = document.getElementById('pph').value;
        var dpp = qty * hargaQty;
        var total = parseInt(dpp) + parseInt(ppn);

        document.getElementById('total').value = total - parseInt(pph);
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

        $("#vendorName").select2({
            width: "100%"
        });
        $("#vendorNameGeneral").select2({
            width: "100%"
        });
        $("#vendorNameFreight").select2({
            width: "100%"
        });
        $("#masterBankId").select2({
            width: "100%"
        });
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 2
        });

    });
    //SUBMIT FORM
    $("#logbookDataForm").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: './data_processing.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                var returnVal = data.split('|');

                if (parseInt(returnVal[3]) != 0)	//if no errors
                {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert(returnVal[2]);

                    if (returnVal[1] == 'OK') {
                        $('#pageContent').load('views/pengajuan-logbook.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
    $("#logbookDataForm").validate({
        rules: {
            noRekening: "required",
            stockpileId: "required",
            cabangBank: 'required',
            namaAkunBank: 'required',
            ppn: "required",
            pph: "required",
            dpp: 'required',
            qty: "required",
            hargaQty: "required",
            vendorType: "required",
            total: "required",
            noInvoice: "required"

        },
        messages: {
            noRekening: "No Rekening is a required field.",
            stockpileId: "Stockpile is a required field.",
            cabangBank: "Cabang Bank is a required field.",
            namaAkunBank: "Nama Akun Bank is a required field.",
            ppn: "PPN is a required field.",
            pph: "PPN is a required field.",
            dpp: "DPP is a required field.",
            qty: "Qty is a required field.",
            hargaQty: "Harga Qty is a required field.",
            vendorType: "Vendor Type is a required field.",
            total: "Total is a required field.",
            noInvoice: "No Invoice is a required field.",

        },
        submitHandler: function (form) {
            $('#submitButton').attr("disabled", true);
        }
    });

    function statusIncomplete() {
        var status = document.getElementById('incomplete');
        // var generalVendorId = document.getElementById('vendorNameGeneral').value;
        if (status.checked != true) {
            document.getElementById("keterangan").classList.add("hidden");
        } else {
            document.getElementById("keterangan").classList.remove("hidden");
        }
    }
</script>

<form method="post" id="logbookDataForm" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="pengajuan_logbook_data"/>
    <input type="hidden" name="actionType" id="actionType" value="<?php echo $actionType; ?>"/>
    <input type="hidden" name="pLogbookId" id="pLogbookId" value="<?php echo $pLogbookId; ?>"/>
    <input type="hidden" name="status" id="status" value="<?php echo $status; ?>"/>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>No Rekening</label>
            <input type="text" placeholder="Input No Rekening" tabindex="" id="noRekening" name="noRekening"
                   class="span12"
                   value="<?php echo $noRekening; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full",
                "", 1, "span12");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>No Invoice/Kwitansi Vendor</label>
            <input type="text" placeholder="Input No Invoice/Kwitansi Vendor" tabindex="" id="noInvoice"
                   name="noInvoice"
                   class="span12"
                   value="<?php echo $noInvoice; ?>">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
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
        <div class="span4 lightblue">
            <div class="hidden" id="vendorPks">
                <label>Vendor Pks</label>
                <?php
                createCombo("SELECT * FROM vendor ORDER BY vendor_name ASC", $vendorName, '', "vendorName", "vendor_name", "vendor_name",
                    "", 21, 'span12');
                ?>
            </div>
            <div class="hidden" id="vendorGeneral">
                <label>Vendor General</label>
                <?php
                createCombo("SELECT * FROM general_vendor ORDER BY general_vendor_name ASC", $vendorName, '', "vendorNameGeneral", "general_vendor_name", "general_vendor_name",
                    "", 21, 'span12');
                ?>
            </div>
            <div class="hidden" id="vendorFreight">
                <label>Vendor Freight</label>
                <?php
                createCombo("SELECT * FROM freight ORDER BY freight_supplier ASC", $vendorName, '', "vendorNameFreight", "freight_supplier", "freight_supplier",
                    "", 21, 'span12');
                ?>
            </div>
            <div class="hidden" id="vendorLabor">
                <label>Vendor Labor</label>
                <?php
                createCombo("SELECT * FROM labor ORDER BY labor_name ASC", $vendorName, '', "vendorLabor", "labor_name", "labor_name",
                    "", 21, 'span12');
                ?>
            </div>
            <div class="hidden" id="vendorHandling">
                <label>Vendor Handling</label>
                <?php
                createCombo("SELECT * FROM vendor_handling ORDER BY vendor_handling_name ASC", $vendorName, '', "vendorHandling", "vendor_handling_name", "vendor_handling_name",
                    "", 21, 'span12');
                ?>
            </div>
            <div class="hidden" id="vendorPettyCash">
                <label>Vendor PettyCash</label>
                <?php
                createCombo("SELECT * FROM vendor_pettycash ORDER BY vendor_name ASC", $vendorName, '', "vendorPettyCash", "vendor_name", "vendor_name",
                    "", 21, 'span12');
                ?>
            </div>
        </div>
    </div>

    <!--    Master Bank-->
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Bank<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT master_bank_id,bank_name FROM master_bank ORDER BY bank_name ASC", $masterBankId, "", "masterBankId", "master_bank_id", "bank_name",
                "", "", "span12");
            ?>
        </div>
        <div class="span4 lightblue">
            <label>Cabang Bank</label>
            <input type="text" placeholder="Input Cabang Bank" tabindex="" id="cabangBank" name="cabangBank"
                   class="span12"
                   value="<?php echo $cabangBank; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Nama Akun Bank</label>
            <input type="text" placeholder="Input Akun Bank" tabindex="" id="namaAkunBank" name="namaAkunBank"
                   class="span12"
                   value="<?php echo $namaAkunBank; ?>">
        </div>
    </div>


    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Qty</label>
            <input type="number" placeholder="Input Qty" tabindex="" id="qty" name="qty"
                   class="span12"
                   value="<?php echo $qty; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Harga Qty</label>
            <input type="number" placeholder="Input Harga Qty" tabindex="" id="hargaQty" name="hargaQty"
                   class="span12"
                   value="<?php echo $hargaQty; ?>">
        </div>
        <div class="span4 lightblue">
            <label>DPP</label>
            <input type="number" placeholder="Input DPP" tabindex="" id="dpp" name="dpp"
                   class="span12"
                   value="<?php echo $dpp; ?>">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>PPN</label>
            <input type="number" placeholder="Input PPN" tabindex="" id="ppn" name="ppn"
                   class="span12"
                   value="<?php echo $ppn; ?>">
        </div>
        <div class="span4 lightblue">
            <label>PPH</label>
            <input type="number" placeholder="Input PPH" tabindex="" id="pph" name="pph"
                   class="span12"
                   value="<?php echo $pph; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Total</label>
            <input type="number" placeholder="Input Total" tabindex="" id="total" name="total"
                   class="span12"
                   value="<?php echo $total; ?>">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Upload File</label>
            <input type="file" placeholder="File" tabindex="" id="file" name="file"
                   class="span12"
                   value="<?php echo $file; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Tax Remark</label>
            <textarea class="span12" name="taxRemark"><?php echo $taxRemark; ?></textarea>
        </div>
        <div class="span4 lightblue">
            <label>Keterangan</label>
            <textarea class="span12" name="remark"><?php echo $remark; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
