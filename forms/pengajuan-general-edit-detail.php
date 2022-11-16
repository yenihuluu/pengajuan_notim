<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$pgdId = $_POST['pgdId'];
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

// <editor-fold defaultstate="collapsed" desc="Functions">


$sql = "SELECT * FROM pengajuan_general_detail WHERE pgd_id = {$pgdId}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if ($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    $type = $row->type;
    $accountId = $row->account_id;
    $spRemark = $row->stockpile_remark;
    $generalVendorId = $row->general_vendor_id;
    $notes = $row->notes;
    $pphTaxId = $row->pphID;
    $ppnTaxId = $row->ppnID;
    $qty = $row->qty;
    $price = $row->price;
    $termin = $row->termin;
    $amount = $row->amount;
    $shipmentId = $row->shipment_id;
    $poId = $row->poId;
    $invoiceType = $row->type;

// <editor-fold defaultstate="collapsed" desc="GET LIST PO NO">

    //     $sqlAcc = "SELECT account_no FROM account WHERE account_id = {$accountId}";
    //     $resultAcc = $myDatabase->query($sqlAcc, MYSQLI_STORE_RESULT);
    //     if ($resultAcc->num_rows == 1) {
    //         $rowAcc = $resultAcc->fetch_object();

    //         $accNo = $rowAcc->account_no;

    //     }

    //     if ($accNo == 520900 || $accNo == 521000) {
    //         $sqlPONO = "SELECT c.contract_id, c.po_no
    // FROM contract c
    // LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
    // WHERE c.invoice_status = 0 AND c.contract_status != 2 AND sc.`stockpile_id` = {$spRemark}
    // ORDER BY c.contract_id DESC";

    //     } else {

    //         $sqlPONO = "SELECT c.contract_id, c.po_no
    // FROM contract c
    // LEFT JOIN stockpile_contract sc ON sc.`contract_id` = c.`contract_id`
    // WHERE c.contract_status != 2 AND sc.`stockpile_id` = {$spRemark}
    // ORDER BY c.contract_id DESC";
    //     }
// </editor-fold>

}
if ($invoiceType == 7) {
    $joinProperty = " INNER JOIN bank b ON b.account_id = acc.account_id ";
}
// </editor-fold>

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    if ($sql != '') {
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    } else {
        $result = false;
    }
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

        <?php if(isset($type) && $type != '') { ?>
        document.getElementById("accounts").classList.remove("hidden");
        //setAccount(1, <?php //echo $type ?>//, <?php //echo $accountId ?>//);
        <?php } ?>
        <?php if($spRemark != '' && $accountId !=''){ ?>
            resetPoNo(' ');
            setPoNo(0, <?php echo $accountId ?>, <?php echo $spRemark ?>, 0);
        <?php } ?>
    });

    $('#invoiceType').change(function () {
        if (document.getElementById('invoiceType').value != '') {
            document.getElementById("accounts").classList.remove("hidden");
            resetAccount(' ');
            setAccount(0, $('select[id="invoiceType"]').val(), 0);
        } else {
            document.getElementById("accounts").classList.add("hidden");
            resetAccount(' Invoice Type ');
        }
    });

    $('#stockpileId2').change(function () {
        if (document.getElementById('stockpileId2').value != '') {
            setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        } else {
            resetShipmentCode(' Shipment Code ');
        }
    });

    $('#shipmentId1').change(function () {
        console.log(document.getElementById('shipmentId1').value);
    });

    $('#accountId').change(function () {
        if (document.getElementById('accountId').value != '') {
            resetPoNo(' ');
            resetShipmentCode(' Shipment Code ');
            setPoNo(0, $('select[id="accountId"]').val(), $('select[id="stockpileId2"]').val(), 0);
            setShipmentCode(0, $('select[id="stockpileId2"]').val(), 0);
        } else {
            resetPoNo(' PO NO ');
            resetShipmentCode(' Shipment Code ');
        }
    });

    function resetShipmentCode(text) {
        document.getElementById('shipmentId1').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId1').options.add(x);
    }

    function setShipmentCode(type, stockpileId2, shipmentId1) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getShipmentInvoice',
                stockpileId2: stockpileId2

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
                        document.getElementById('shipmentId1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId1').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId1').options.add(x);
                    }

                    if (type == 1) {
                        $('#shipmentId1').find('shipmentId1').each(function (i, e) {
                            if ($(e).val() == shipmentId1) {
                                $('#shipmentId1').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

    function resetPoNo(text) {
        document.getElementById('poId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('poId').options.add(x);
    }

    function setPoNo(type, accountId, stockpileId2) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPoInvoice',
                accountId: accountId,
                stockpileId2: stockpileId2

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
                        document.getElementById('poId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('poId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('poId').options.add(x);
                    }

                    if (type == 1) {
                        $('#poId').find('poId').each(function (i, e) {
                            if ($(e).val() == poId) {
                                $('#poId').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

    function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }

    function setAccount(type, invoiceType, accountId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getAccountInvoice',
                invoiceType: invoiceType

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
                        document.getElementById('accountId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('accountId').options.add(x);
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('accountId').options.add(x);
                    }
                    console.log(type);
                    if (type == 1) {
                        $('#accountId').find('accountId').each(function (i, e) {
                            if ($(e).val() == accountId) {
                                $('#accountId').prop('selectedIndex', i);
                            }
                        });
                    }

                }
            }
        });
    }

</script>
<div id="addAccountForms">
    <input type="hidden" name="action" id="action" value="pengajuan_general_detail">
    <input type="hidden" name="pgdId" value="<?php echo $pgdId ?>">
    <input type="hidden" name="_method" value="UPDATE">

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Type</label>
            <?php createCombo("SELECT '4' as id, 'Loading' as info UNION
                    SELECT '5' as id, 'Umum' as info UNION
                    SELECT '6' as id, 'HO' as info;", $type, "", "invoiceType", "id", "info", "", "", "select2combobox100", 1); ?>
        </div>
        <div id="accounts" class="span4 lightblue hidden">
            <label>Account</label>
            <?php createCombo("SELECT acc.account_id, CONCAT(acc.account_name) AS account_full
            FROM account acc {$joinProperty}
            WHERE acc.status = 0 AND acc.account_type = {$invoiceType} AND acc.account_no != 210105", $accountId, "", "accountId", "account_id", "account_full", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue">
            <label>Method</label>
            <!--            --><?php
            //            createCombo("SELECT '1' as id, 'IN' as info UNION
            //                    SELECT '2' as id, 'OUT' as info;", $invMethod, "", "invMethod", "id", "info",
            //                "", "", "select2combobox100", 1);
            //            ?>
            <input type="text" value="OUT" readonly>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Remark (Stockpile)</label>
            <?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $spRemark, "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1); ?>
        </div>
        <div class="span4 lightblue">
            <label>PO No.</label>
            <?php createCombo("", $poId, "", "poId", "contract_id", "po_no", "", "", "select2combobox100", 4); ?>
        </div>
        <div class="span4 lightblue">
            <label>Shipment Code</label>
            <?php createCombo("SELECT a.shipment_id, CONCAT(a.shipment_no, ' - ', SUBSTR(a.`shipment_code`,-1)) AS shipment_no, b.`stockpile_id`  
FROM shipment a LEFT JOIN sales b ON a.`sales_id` = b.`sales_id` WHERE b.`sales_status` != 4 AND b.stockpile_id = {$spRemark} ORDER BY shipment_id DESC", $shipmentId, "", "shipmentId1", "shipment_id", "shipment_no", "", "", "select2combobox100", 4); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Qty</label>
            <input type="text" class="span12" tabindex="" id="qty" name="qty" value="<?php echo $qty ?>" readonly>
        </div>
        <div class="span3 lightblue">
            <label>Unit Price</label>
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $price ?>" readonly>
        </div>
        <div class="span2 lightblue">
            <label>Termin (%)</label>
            <input type="text" class="span12" tabindex="" id="termin" name="termin" min="0" max="100"
                   value="<?php echo $termin ?>" readonly>
        </div>
        <div class="span4 lightblue">
            <label>Amount</label>
            <input type="text" readonly class="span12" tabindex="" id="amount" name="amount"
                   value="<?php echo $amount ?>">

        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Notes</label>
            <textarea class="span12" rows="1" tabindex="" id="notes" name="notes"> <?php echo $notes ?></textarea>
        </div>
    </div>

</div>
