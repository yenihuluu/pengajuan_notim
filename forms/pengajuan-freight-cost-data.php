<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

$pFreightCostId = '';
$freightId = '';
$vendorId = '';
$stockpileId = '';
$currencyId = '';
$price = '';
$paymentNotes = '';
$remarks = '';
$modifyBy = '';
$modifyDate = '';
$exchangeRate = '';
$shrink_tolerance_kg = '';
$shrink_tolerance_persen = '';
$shrink_claim = '';
$active_from = '';
$contractPKHOA = '';
$syaratPembayaran = '';
$caraPembayaran = '';

// </editor-fold>

if (isset($_POST['pFreightCostId']) && $_POST['pFreightCostId'] != '') {
    $pFreightCostId = $_POST['pFreightCostId'];

    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">

    $sql = "SELECT fc.*, DATE_FORMAT(fc.modify_date, '%d %b %Y %H:%i:%s') AS modify_date2, u.user_name
            FROM pengajuan_freight_cost fc
            LEFT JOIN user u
                ON u.user_id = fc.modify_by
            WHERE fc.p_freight_cost_id = {$pFreightCostId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $freightId = $rowData->freight_id;
        $currencyId = $rowData->currency_id;
        $exchangeRate = $rowData->exchange_rate;
        $vendorId = $rowData->vendor_id;
        $price = $rowData->price;
        $paymentNotes = $rowData->payment_notes;
        $contractPKHOA = $rowData->contract_no_pks;
        $remarks = $rowData->remarks;
        $modifyBy = $rowData->user_name;
        $modifyDate = $rowData->modify_date2;
        $shrink_tolerance_kg = $rowData->shrink_tolerance_kg;
        $shrink_tolerance_persen = $rowData->shrink_tolerance_persen;
        $shrink_claim = $rowData->shrink_claim;
        $active_from = $rowData->active_from;
        $stockpileId = $rowData->stockpile_id;
        $syaratPembayaran = $rowData->syarat_pembayaran;
        $caraPembayaran = $rowData->cara_pembayaran;
        $file = $rowData->file1;
    }

    // </editor-fold>
}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if ($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
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

        if (document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRateFreight').hide();
        } else {
            $('#exchangeRateFreight').show();
        }

        $('#currencyId').change(function () {
            if (document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateFreight').hide();
            } else {
                $('#exchangeRateFreight').show();
            }
        });

        $('#closeImportButton').click(function (e) {
            e.preventDefault();

            $('#importButton').attr("disabled", true);
            $('#closeImportButton').attr("disabled", true);

            $('#importModal').modal('hide');
            $('#dataContent').load('contents/pengajuan-freight-cost.php', {}, iAmACallbackFunction2);
        });

        // $('#importForm').on('submit', function (e) {

        //     e.preventDefault();


        //     $('#importButton').attr("disabled", true);
        //     $('#closeImportButton').attr("disabled", true);
        //     //$.blockUI({ message: '<h4>Please wait...</h4>' });

        //     $(this).ajaxSubmit({
        //         success: showResponse //call function after success
        //     });
        // });


    });

    function showResponse(responseText, statusText, xhr, $form) {


        var returnVal = responseText.split('|');
//        alert(returnVal);
        if (parseInt(returnVal[3]) != 0)	//if no errors
        {
//            alert(responseText);
            alertify.set({
                labels: {
                    ok: "OK"
                }
            });
            alertify.alert(returnVal[2]);
            if (returnVal[1] == 'OK') {
                //show success message
                $('#importModal').modal('hide');
                $('#dataContent').load('views/pengajuan-freight-cost.php', {}, iAmACallbackFunction2);
            } else {
                //show error message
                $('#importButton').attr("disabled", false);
                $('#closeImportButton').attr("disabled", false);
            }
        }

    }

    function approve() {
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'approve_pengajuan_freight_cost',
                pFreightCostId: $('input[id="pFreightCostId"]').val(),
				approvedDate : $('input[id="approvedDate"]').val()
            },
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
                        $('#dataContent').load('views/pengajuan-freight-cost.php', {}, iAmACallbackFunction);
                    }
                }
            }
        });
    }
	
	function submitNotes() {
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'approve_pengajuan_freight_cost',
                pFreightCostId: $('input[id="pFreightCostId"]').val(),
				notesApproval : document.getElementById('notesApproval').value,
                actionType : 'submitNotesApproval'
            },
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
                        $('#dataContent').load('views/pengajuan-freight-cost.php', {}, iAmACallbackFunction);
                    }
                }
            }
        });
    }

    function cancel() {
        $.ajax({
            url: './irvan.php',
            method: 'POST',
            data: {
                action: 'cancel_pengajuan_freight_cost',
                pFreightCostId: $('input[id="pFreightCostId"]').val()
            },
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
                        $('#pengajuan-freight-cost-data').load('views/pengajuan-freight-cost.php', {}, iAmACallbackFunction);
                    }
                }
            }
        });
    }

    function iAmACallbackFunction() {
        $("#pengajuan-freight-cost-data").fadeIn("slow");
    }
</script>

    <input type="hidden" name="action" id="action" value="approve_freight_cost"/>
    <input type="hidden" id="pFreightCostId" name="pFreightCostId" value="<?php echo $pFreightCostId; ?>">
    <div class="row-fluid">
        <label>Stockpile <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                FROM user_stockpile us
                INNER JOIN stockpile s
                    ON s.stockpile_id = us.stockpile_id
                WHERE us.user_id = {$_SESSION['userId']}
                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "$stockpileId", "disabled", "stockpileId", "stockpile_id", "stockpile_full",
            "", 1, "select2combobox100");
        ?>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Freight <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT f.freight_id, CONCAT(f.freight_code, ' - ', f.freight_supplier) AS freight_full
                FROM freight f WHERE f.active = 1
                ORDER BY f.freight_code ASC, f.freight_supplier ASC", $freightId, "disabled", "freightId", "freight_id", "freight_full",
                "", 1, "select2combobox100");
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT v.vendor_id, CONCAT(v.vendor_name, ' (', v.vendor_code, ')') AS vendor_full
                    FROM vendor v WHERE v.active = 1 ORDER BY v.vendor_name", $vendorId, 'disabled', "vendorId", "vendor_id", "vendor_full",
                "", 2, "select2combobox100");
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", $currencyId, "disabled", "currencyId", "currency_id", "currency_code",
                "", 3, "span12");
            ?>
        </div>
        <div class="span8 lightblue" id="exchangeRateFreight" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span9" tabindex="4" id="exchangeRate" name="exchangeRate"
                   value="<?php echo $exchangeRate; ?>" disabled>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Price/KG <span style="color: red;">*</span></label>
            <input type="text" class="span6" tabindex="5" id="price" name="price" value="<?php echo $price; ?>"
                   disabled>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Active From <span style="color: red;">*</span></label>
            <input type="date" class="span6" tabindex="6" id="active_from" name="active_from"
                   value="<?php echo $active_from; ?>" disabled>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4 lightblue">
            <label>Shrink Tolerance(KG) <span style="color: red;"></span></label>
            <input type="text" class="span12" tabindex="7" id="shrink_tolerance_kg" name="shrink_tolerance_kg"
                   value="<?php echo $shrink_tolerance_kg; ?>" disabled>
        </div>
        <div class="span4 lightblue">
            <label>Shrink Tolerance(%) <span style="color: red;"></span></label>
            <input type="text" class="span12" tabindex="7" id="shrink_tolerance_persen" name="shrink_tolerance_persen"
                   value="<?php echo $shrink_tolerance_persen; ?>" disabled>
        </div>
        <div class="span4 lightblue">
            <label>Shrink Claim <span style="color: red;"></span></label>
            <input type="text" class="span12" tabindex="7" id="shrink_claim" name="shrink_claim"
                   value="<?php echo $shrink_claim; ?>" disabled>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Payment Notes</label>
            <input type="text" class="span10" tabindex="6" id="paymentNotes" name="paymentNotes"
                   value="<?php echo $paymentNotes; ?>" disabled>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>No Kontrak</label>
            <input type="text" class="span10" tabindex="6" id="contractPKHOA" name="contractPKHOA"
                   value="<?php echo $contractPKHOA; ?>" disabled>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Remarks</label>
            <textarea class="span10" rows="3" tabindex="7" id="remarks"
                      name="remarks" disabled><?php echo $remarks; ?></textarea>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <p>Cara Pembayaran</p>
        </div>
        <input type="radio" id="transfer" name="caraPembayaran" value="1" <?php if ($caraPembayaran == 1) {
            echo 'checked';
        } ?> disabled>
        <label for="transfer">Transfer</label><br>
        <input type="radio" id="cash" name="caraPembayaran" value="2" <?php if ($caraPembayaran == 2) {
            echo 'checked';
        } ?> disabled>
        <label for="cash">Cash</label><br>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Syarat Pembayaran</label>
            <textarea class="span10" rows="3" tabindex="7" id="syaratPembayaran"
                      name="syaratPembayaran" disabled><?php echo $syaratPembayaran; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>File</label>
            <a href="<?php echo $file ?>" target="_blank">File</a>
        </div>
    </div>

    <br>
    <?php if ($modifyBy != '') { ?>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Modified on <?php echo $modifyDate; ?> by <?php echo $modifyBy; ?></label>
            </div>
        </div>
    <?php } ?>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Approve Date <span style="color: red;">*</span></label>
            <input type="date" class="span6" tabindex="6" id="approvedDate" name="approvedDate">
        </div>
    </div>
	
	 <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Notes for Approval</label>
            <textarea class="span10" rows="3" tabindex="7" id="notesApproval"
                      name="notesApproval" ><?php echo $notesForApproval; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <button class="btn btn-success" id="importButton" onclick= "approve()">Approve</button>
		<button class="btn btn-info" id="importButton" onclick= "submitNotes()">Submit Notes</button>
        <button class="btn btn-warning" id="cancel" onclick= "cancel()" style="margin: 0px;">Cancel</button>
        <button class="btn btn-inverse" id="closeImportButton">Close</button>
    </div>

</form>