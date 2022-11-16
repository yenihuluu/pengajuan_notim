<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Posting Stock Transit';
$date = new DateTime();
//$paymentDate = $date->format('d/m/Y');

$kodeMutasi = '';
$tglPosting = '';
$unloadingDate = '';

if (isset($_POST['kodeMutasi']) && $_POST['kodeMutasi'] != '') {
    $kodeMutasi = $_POST['kodeMutasi'];
    $totalNettoWeight = $_POST['totalNettoWeight'];
    $totalSendWeight = $_POST['totalSendWeight'];

    $sql1 = $myDatabase->query("select * from mutasi_header where mutasi_header_id = {$kodeMutasi}", MYSQLI_STORE_RESULT);
    $rowData = $sql1->fetch_object();
    $stockpileId = $rowData->stockpile_to;

    //table bawah per contract
    $sql = "select st.*, CONCAT (s.stockpile_code, ' - ', s.stockpile_name) as stockpile_from, CONCAT (ss.stockpile_code, ' - ', ss.stockpile_name) as stockpile_to, c.contract_no as contract_no, c.price, mh.kode_mutasi as destination_code,
    (SELECT  DATE_FORMAT(MAX(tt.transaction_date), '%Y-%m-%d') from transaction_timbangan tt left join mutasi_header mh on tt.mutasi_header_id = mh.mutasi_header_id) as transaction_date
    from stock_transit st left join mutasi_header mh on st.mutasi_header_id = mh.mutasi_header_id 
    left join stockpile s on mh.stockpile_from = s.stockpile_id
    left join stockpile ss on mh.stockpile_to = ss.stockpile_id
    left join stockpile_contract sc on sc.stockpile_contract_id = st.stockpile_contract_id
    left join contract c on sc.contract_id = c.contract_id
    where mh.mutasi_header_id = {$kodeMutasi} and st.status = 0";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    //table atas mutasi_detail
    $sql2 = "SELECT md.*, mbm.tipe_biaya, mh.kode_mutasi, mh.total as total_header, td.percentage as termin_percentage, id.mutasi_detail_id, 
            id.amount_converted, i.invoice_no, DATE_FORMAT(i.invoice_date, '%Y-%m-%d') as invoice_date, DATE_FORMAT(p.payment_date, '%Y-%m-%d') as payment_date, p.payment_no,
        (SELECT CONCAT(ROUND(id.termin,0),'% / ',FORMAT(a.percentage,2),'%') FROM termin_detail a LEFT JOIN mutasi_detail b ON a.id = b.termin_detail_id WHERE b.mutasi_detail_id = id.mutasi_detail_id) as termin_percentage2
        FROM mutasi_detail md
        LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = md.mutasi_header_id
        LEFT JOIN currency as c on md.currency_id = c.currency_id
        LEFT JOIN master_biaya_mutasi mbm on md.biaya_mutasi_id = mbm.id
        LEFT JOIN termin_detail td ON md.termin_detail_id = td.id 
        LEFT JOIN master_termin mt ON mt.id = td.termin_id
        LEFT JOIN invoice_detail id on md.mutasi_detail_id = id.mutasi_detail_id
        LEFT JOIN invoice i on i.invoice_id = id.invoice_id
        RIGHT JOIN payment p on p.invoice_id = i.invoice_id  and p.payment_status != 1 AND i.payment_status != 2
        WHERE md.mutasi_header_id = {$kodeMutasi} ORDER BY md.general_vendor_id ASC";
    $rowData = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    // $sqlTax = "SELECT total FROM mutasi_header WHERE mutasi_header_id = $kodeMutasi";
	$sqlTax = "SELECT sum(id.amount_converted) as total
        FROM mutasi_detail md
        LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = md.mutasi_header_id
        LEFT JOIN currency as c on md.currency_id = c.currency_id
        LEFT JOIN master_biaya_mutasi mbm on md.biaya_mutasi_id = mbm.id
        LEFT JOIN termin_detail td ON md.termin_detail_id = td.id 
        LEFT JOIN master_termin mt ON mt.id = td.termin_id
        LEFT JOIN invoice_detail id on md.mutasi_detail_id = id.mutasi_detail_id
        LEFT JOIN invoice i on i.invoice_id = id.invoice_id
        RIGHT JOIN payment p on p.invoice_id = i.invoice_id  and p.payment_status != 1 AND i.payment_status != 2
        WHERE md.mutasi_header_id = {$kodeMutasi} ORDER BY md.general_vendor_id ASC";
    $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);
    if ($resultTax->num_rows == 1) {
        $sqlTax = $resultTax->fetch_object();
        $mutasi_value = $sqlTax->total;
        $mutasiValueTon = $mutasi_value/$totalSendWeight;
    }

}


// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if ($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if ($empty == 4) {
        echo "<option value=''>-- All --</option>";
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
    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });
        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox60").select2({
            width: "60%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });
    });

    $(document).ready(function () {
        $('#kodeMutasi').change(function () {
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: {
                    action: 'posting_transit',
                    type: 'calculateNettoSendwWeight',
                    kodeMutasi: $('select[id="kodeMutasi"]').val(),
                    stockpileId: document.getElementById('stockpileId').value,

                },
                success: function (data) {
                    var returnVal = data.split('|');
                    document.getElementById('totalNettoWeight').value = returnVal[0];
                    document.getElementById('totalSendWeight').value = returnVal[1];
					$.blockUI({message: '<h4>Please wait...</h4>'});
                    $('#pageContent').load('views/posting-transit.php', {
                        kodeMutasi: $('select[id="kodeMutasi"]').val(),
                        totalNettoWeight: returnVal[0],
                        totalSendWeight: returnVal[1],
                    }, iAmACallbackFunction);
                    $('#submitButton2').attr("disabled", false);
                }
            });

        });
    });

    $(document).ready(function () {
        $("#addFreightLoginForm").validate({
            rules: {
                tglPosting: "required",
            },

            messages: {
                tglPosting: "Freight is a required field.",
            }
        });
    });

    function print() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'posting_transit',
                type: 'calculateNettoSendwWeight',
                kodeMutasi: $('select[id="kodeMutasi"]').val(),
                stockpileId: document.getElementById('stockpileId').value,
            },
            success: function (data) {
                var returnVal = data.split('|');
                document.getElementById('totalNettoWeight').value = returnVal[0];
                document.getElementById('totalSendWeight').value = returnVal[1];
                $('#pageContent').load('views/posting-transit-pdf.php', {
                    kodeMutasi: $('select[id="kodeMutasi"]').val(),
                    totalNettoWeight: returnVal[0],
                    totalSendWeight: returnVal[1],
                }, iAmACallbackFunction);
                $('#submitButton2').attr("disabled", false);
            }
        });
    }

    function submitData() {
        var r = confirm("Are You Sure?");
        if (r == true) {
            var stockTransitId = [];
            $.each($("input[name='stockTransitId']:checked"), function () {
                stockTransitId.push($(this).val());
            });
			 $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: {
                    action: 'posting_transit_data',
                    kodeMutasi: document.getElementById('kodeMutasi').value,
                    totalSendWeight: document.getElementById('totalSendWeight').value,
                    totalNettoWeight: document.getElementById('totalNettoWeight').value,
                    stockpileId: document.getElementById('stockpileId').value,
                    tglPosting: document.getElementById('tglPosting').value,
                    unloadingDate: document.getElementById('unloadingDate').value,
                    stockTransitId: stockTransitId.join(","),
                },
                success: function (data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {

                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/posting-transit.php', {}, iAmACallbackFunction);
                        }

                        $('#submitButton2').attr("disabled", false);
                    }
                }
            });
        } else {
            alertify.set({
                labels: {
                    ok: "OK"
                }
            });
            alertify.alert('Canceled');
        }
    }

</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>
<input type="hidden" name="stockpileId" id="stockpileId" value="<?php echo $stockpileId ?>"/>

<div class="row-fluid">
    <div class="span2 lightblue">
        <label>Mutasi Code<span style="color: red;">*</span></label>
    </div>
    <div class="span4 lightblue">
        <?php
        createCombo("SELECT * from mutasi_header where status = 0", $kodeMutasi, "", "kodeMutasi", "mutasi_header_id", "kode_mutasi",
            "", 1, "select2combobox60");
        ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span2 lightblue">
        <label>Posting Date<span style="color: red;">*</span></label>
    </div>
    <div class="span5 lightblue">
        <input type="text" placeholder="DD/MM/YYYY" id="tglPosting" name="tglPosting"
               value="<?php echo $tglPosting; ?>" data-date-format="yyyy/mm/dd" class="datepicker">
    </div>
</div>
<div class="row-fluid">
    <div class="span2 lightblue">
        <label>Unloading Date<span style="color: red;">*</span></label>
    </div>
    <div class="span5 lightblue">
        <input type="text" placeholder="DD/MM/YYYY" id="unloadingDate" name="unloadingDate"
               value="<?php echo $unloadingDate; ?>" data-date-format="yyyy/mm/dd" class="datepicker">
    </div>
</div>
<br>

<div class="row-fluid">
    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
        <tr>
            <th>Mutasi Code</th>
            <th>Vendor</th>
            <th>Tipe Biaya</th>
            <th>Currency</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
            <th>Termin Percentage</th>
            <th>Total per termin</th>
            <th>In Invoice</th>
            <th>Invoice Amount</th>
            <th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Payment Date</th>
            <th>Payment No</th>
            <th>Paid Termin</th>

        </tr>
        </thead>
        <tbody>
        <?php
        if ($rowData !== false && $rowData->num_rows > 0) {
            $data = [];
            while ($row = $rowData->fetch_array()) {
                $data [] = [
                    'mutasi_detail_id' => $row['mutasi_detail_id'],
                    'mutasi_header_id' => $row['mutasi_header_id'],
                    'total_mutasi' => $row['total_mutasi'],
                    'vendor' => $row['vendor'],
                    'vendor_type' => $row['vendor_type'],
                    'currency_id' => $row['currency_id'],
                    'exchange_rate' => $row['exchange_rate'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'total' => $row['total'],
                    'total_per_termin' => $row['total_per_termin'],
                    'account_id' => $row['account_id'],
                    'termin_detail_id' => $row['termin_detail_id'],
                    'ppnId' => $row['ppnId'],
                    'ppn' => $row['ppn'],
                    'ppn_converted' => $row['ppn_converted'],
                    'pphId' => $row['pphId'],
                    'pph' => $row['pph'],
                    'pph_converted' => $row['pph_converted'],
                    'general_vendor_id' => $row['general_vendor_id'],
                    'status' => $row['status'],
                    'entry_by' => $row['entry_by'],
                    'entry_date' => $row['entry_date'],
                    'tipe_biaya' => $row['tipe_biaya'],
                    'kode_mutasi' => $row['kode_mutasi'],
                    'termin_percentage' => $row['termin_percentage'],
                    'amount_converted' => $row['amount_converted'],
                    'invoice_detail_id' => $row['invoice_detail_id'],
                    'invoice_no' => $row['invoice_no'],
                    'notes' => $row['notes'],
                    'invoice_date' => $row['invoice_date'],
                    'payment_id' => $row['payment_id'],
                    'payment_date' => $row['payment_date'],
                    'payment_no' => $row['payment_no'],
                    'termin_percentage2' => $row['termin_percentage2'],
                ];
            }
            foreach ($data as $key => $row) {
                $subtotal_inv += $row['amount_converted'];
				$totalMutasiCost += $row['amount_converted'];
				$totalBiayaTerdaftar += $row['total_per_termin'];
                ?>
                <tr>
                    <td><?php echo $row['kode_mutasi']; ?></td>
                    <td><?php echo $row['vendor']; ?> </td>
                    <td><?php echo $row['tipe_biaya']; ?></td>
                    <?php if ($row['currency_id'] == '1') { ?>
                        <td>IDR</td>
                        <?php
                    } else { ?>
                        <td>USD</td>
                        <?php
                    } ?>
                    <td><?php echo number_format($row['qty'], 2, ".", ","); ?></td>
                    <td><?php echo number_format($row['price'], 2, ".", ","); ?></td>
                    <td><?php echo number_format($row['total'], 2, ".", ","); ?></td>
                    <td><?php echo number_format($row['termin_percentage'], 2, ".", ","); ?></td>
                    <td><?php echo number_format($row['total_per_termin'], 2, ".", ","); ?></td>
                    <?php if ($row['mutasi_detail_id'] != '') { ?>
                        <td>YES</td>
                        <?php
                    } else { ?>
                        <td>NO</td>
                        <?php
                    } ?>
                    <td><?php echo number_format($row['amount_converted'], 2, ".", ","); ?></td>
                    <td><?php echo $row['invoice_no']; ?></td>
                    <td><?php echo $row['invoice_date']; ?></td>
                    <td><?php echo $row['payment_date']; ?></td>
                    <td><?php echo $row['payment_no']; ?></td>
                    <td><?php echo $row['termin_percentage2']; ?></td>
                </tr>
                <?php if ($data[$key + 1]['general_vendor_id'] != $row['general_vendor_id']) { ?>
                    <tr>
                        <td style="text-align: center; vertical-align: middle; "><strong>Sub Total</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong><?php echo number_format($subtotal_inv, 2, ".", ","); ?></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php $subtotal_inv = 0;
                } ?>
                <?php } ?>
				<tr>
                    <td style="font-weight: bold">Total Mutasi Cost</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
					<td></td>
                    <td></td>
                    <td><strong><?php echo number_format($totalMutasiCost, 2, ".", ","); ?></strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
				<tr>
                    <td style="font-weight: bold">Total Biaya Terdaftar</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
					<td><strong><?php echo number_format($totalMutasiCost, 2, ".", ","); ?></strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
            </tr>
        <?php } else {
            ?>
            <tr>
                <td colspan="16" style="text-align: center">
                    No data to be shown.
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>

<div class="row-fluid">
    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
        <tr>
            <th>Stockpile From</th>
            <th>Stockpile To</th>
            <th>Contract No</th>
            <th>Send Weight Pabric (KG)</th>
            <th>Netto Weight Draft Survey (KG)</th>
            <th style="background-color: lightgray;">Unit Price (Rp/KG)</th>
            <th>Netto Stockpile (KG)</th>
            <th style="background-color: lightgray;">Shrink Price (Rp/KG)</th>
            <th>Shrink (KG)</th>
            <th>Shrink Amount (Rp)</th>
            <th style="background-color: lightgray">Mutasi Amount (Rp/KG)</th>
            <th style="background-color: lightgray">Unit Cost (Rp/KG)</th>
            <th>Inventory Value (KG)</th>
            <th>Loading Date</th>
            <th>Transaction Date</th>
        </thead>
        <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            //Grand Total
            $gtSendWeight = 0;
            $gtNettoWeight = 0;
            $gtPksAmount = 0;
            $gtMutasiCost = 0;
            $gtNettoStockpile = 0;
            $gtInventoryValue = 0;

            while ($rowData = $resultData->fetch_object()) {
                $unitPrice = $rowData->price;
                $sendWeightTransit = $rowData->send_weight;
                $nettoTransit = $rowData->netto_weight;

                $nettoStockpile = $sendWeightTransit * $totalNettoWeight / $totalSendWeight;
                $shrink = $sendWeightTransit - $nettoStockpile;
                if ($shrink <= 0) {
                    $shrink = 0;
                } else {
                    $shrink;
                }
                $shrinkAmount = $shrink * ($mutasiValueTon + $unitPrice);
                $shrinkValueTon = $shrinkAmount / $nettoStockpile;
                $unitCost = $unitPrice + $mutasiValueTon + $shrinkValueTon;
                if($totalSendWeight > $totalNettoWeight){
                    $inventoryValue = $nettoStockpile * $unitCost;
                } else {
                    $inventoryValue = $totalSendWeight * $unitCost;
                }

                //Grand Total
                $gtSendWeight += $rowData->send_weight;
                $gtNettoWeight += $rowData->netto_weight;
                $gtNettoStockpile += $nettoStockpile;
                $gtInventoryValue += $inventoryValue;
                ?>
                <tr>
                    <input type="checkbox" name="stockTransitId" value="<?php echo $rowData->stock_transit_id ?>"
                           checked class="hidden">
                    <td><?php echo $rowData->stockpile_from; ?></td>
                    <td><?php echo $rowData->stockpile_to; ?></td>
                    <td><?php echo $rowData->contract_no; ?></td>
                    <td><?php echo number_format($rowData->send_weight, 2, ".", ",");; ?></td>
                    <td><?php echo number_format($rowData->netto_weight, 2, ".", ",");; ?></td>
                    <td style="background-color: lightgray"><?php echo number_format($unitPrice, 3, ".", ",");; ?></td>
                    <td><?php echo number_format($nettoStockpile, 2, ".", ",");; ?></td>
                    <td style="background-color: lightgray"><?php echo number_format($shrinkValueTon, 3, ".", ",");; ?></td>
                    <td><?php echo number_format($shrink, 3, ".", ",");; ?></td>
                    <td><?php echo number_format($shrinkAmount, 3, ".", ",");; ?></td>
                    <td style="background-color: lightgray"><?php echo number_format($mutasiValueTon, 3, ".", ",");; ?></td>
                    <td style="background-color: lightgray"><?php echo number_format($unitCost, 3, ".", ",");; ?></td>
                    <td><?php echo number_format($inventoryValue, 3, ".", ",");; ?></td>
                    <td><?php echo $rowData->loading_date; ?></td>
                    <td><?php echo $rowData->transaction_date; ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="3" style="text-align: center; vertical-align: middle; "><strong>TOTAL</strong></td>
                <td style="font-weight: bold"><?php echo number_format($gtSendWeight, 2, ".", ",");; ?></td>
                <td style="font-weight: bold"><?php echo number_format($gtNettoWeight, 2, ".", ",");; ?></td>
                <td></td>
                <td style="font-weight: bold"><?php echo number_format($gtNettoStockpile, 2, ".", ",");; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold"><?php echo number_format($gtInventoryValue, 3, ".", ",");; ?></td>
                <td></td>
                <td></td>
            </tr>
        <?php } else {
            ?>
            <tr>
                <td colspan="15" style="text-align: center">
                    No data to be shown.
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>

<div class="row-fluid">
    <div class="span2 lightblue">
        <label>Total Send Weight (KG)<span style="color: red;">*</span></label>
    </div>
    <div class="span4 lightblue">
        <input type="text" class="span12" tabindex="" id="totalSendWeight" name="totalSendWeight"
               value="<?php echo $totalSendWeight ?>"/>
    </div>
    <div class="span2 lightblue">
        <label>Total Netto Weight Stockpile (KG)<span style="color: red;">*</span></label>
    </div>
    <div class="span4 lightblue">
        <input type="text" class="span12" tabindex="" id="totalNettoWeight" name="totalNettoWeight"
               value="<?php echo $totalNettoWeight ?>"/>
    </div>
</div>

<div class="row-fluid">
    <div class="span2 lightblue">
        <button class="btn btn-primary" onclick="submitData()">Submit</button>
        <button class="btn btn-warning" onclick="print()">Print</button>
    </div>
</div>