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
if (isset($_POST['kodeMutasi']) && $_POST['kodeMutasi'] != '') {
    $kodeMutasi = $_POST['kodeMutasi'];
    $totalNettoWeight = $_POST['totalNettoWeight'];
    $totalSendWeight = $_POST['totalSendWeight'];

    $sql1 = $myDatabase->query("select * from mutasi_header where mutasi_header_id = {$kodeMutasi}", MYSQLI_STORE_RESULT);
    $rowData = $sql1->fetch_object();
    $stockpileId = $rowData->stockpile_to;

    $sql = "select st.*, CONCAT (s.stockpile_code, ' - ', s.stockpile_name) as stockpile_from, CONCAT (ss.stockpile_code, ' - ', ss.stockpile_name) as stockpile_to, c.contract_no as contract_no, c.price, mh.kode_mutasi as destination_code,
(SELECT  DATE_FORMAT(MAX(tt.transaction_date), '%Y-%m-%d') from transaction_timbangan tt left join mutasi_header mh on tt.mutasi_header_id = mh.mutasi_header_id) as transaction_date
from stock_transit st left join mutasi_header mh on st.mutasi_header_id = mh.mutasi_header_id 
left join stockpile s on mh.stockpile_from = s.stockpile_id
left join stockpile ss on mh.stockpile_to = ss.stockpile_id
left join stockpile_contract sc on sc.stockpile_contract_id = st.stockpile_contract_id
left join contract c on sc.contract_id = c.contract_id
where mh.mutasi_header_id = {$kodeMutasi} and st.status = 0";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    $sql2 = "SELECT md.*, mbm.tipe_biaya, mh.kode_mutasi, mh.total as total_header, td.percentage as termin_percentage, id.mutasi_detail_id, 
            id.amount_converted, i.invoice_no, DATE_FORMAT(i.invoice_date, '%Y-%m-%d') as invoice_date, DATE_FORMAT(p.payment_date, '%Y-%m-%d') as payment_date, p.payment_no,
(SELECT CONCAT(ROUND(id.termin,0),'% / ',a.percentage,'%') FROM termin_detail a LEFT JOIN mutasi_detail b ON a.id = b.termin_detail_id WHERE b.mutasi_detail_id = id.mutasi_detail_id) as termin_percentage2
        FROM mutasi_detail md
        LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = md.mutasi_header_id
        LEFT JOIN master_biaya_mutasi mbm on md.biaya_mutasi_id = mbm.id
        LEFT JOIN termin_detail td ON md.termin_detail_id = td.id 
        LEFT JOIN master_termin mt ON mt.id = td.termin_id
        LEFT JOIN invoice_detail id on md.mutasi_detail_id = id.mutasi_detail_id
        LEFT JOIN invoice i on i.invoice_id = id.invoice_id
        LEFT JOIN payment p on p.invoice_id = i.invoice_id
        WHERE md.mutasi_header_id = {$kodeMutasi}
        ";
    $rowData = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    $sqlTax = "SELECT total FROM mutasi_header WHERE mutasi_header_id = $kodeMutasi";
    $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);
    if ($resultTax->num_rows == 1) {
        $sqlTax = $resultTax->fetch_object();
        $mutasi_value = $sqlTax->total;
        $mutasiValueTon = $mutasi_value / $totalSendWeight;
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
                    document.getElementById('totalNettoWeight').value = returnVal[1];
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
                document.getElementById('totalNettoWeight').value = returnVal[1];
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
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: {
                    action: 'posting_transit_data',
                    kodeMutasi: document.getElementById('kodeMutasi').value,
                    totalSendWeight: document.getElementById('totalSendWeight').value,
                    totalNettoWeight: document.getElementById('totalNettoWeight').value,
                    stockpileId: document.getElementById('stockpileId').value,
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
            "", 1, "select2combobox100");
        ?>
    </div>
</div>
<br>

<div class="row-fluid">
    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
        <tr>
            <th>Mutasi Code</th>
            <th>Tipe Biaya</th>
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
        if ($rowData !== false && $resultData->num_rows > 0) {
            while ($row = $rowData->fetch_object()) {
                ?>
                <tr>
                <td><?php echo $row->kode_mutasi; ?></td>
                <td><?php echo $row->tipe_biaya; ?></td>
                <td><?php echo number_format($row->qty, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->price, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->total, 0, ".", ","); ?></td>
                <td><?php echo $row->termin_percentage; ?>%</td>
                <td><?php echo number_format($row->total_per_termin, 0, ".", ","); ?></td>
                <?php if ($row->mutasi_detail_id != '') { ?>
                    <td>YES</td>
                    <?php
                } else { ?>
                    <td>NO</td>
                    <?php
                } ?>
                <td><?php echo number_format($row->amount_converted, 0, ".", ","); ?></td>
                <td><?php echo $row->invoice_no; ?></td>
                <td><?php echo $row->invoice_date; ?></td>
                <td><?php echo $row->payment_date; ?></td>
                <td><?php echo $row->payment_no; ?></td>
                <td><?php echo $row->termin_percentage2; ?></td>
                <?php
            }
        } else {
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
            <th>Unit Price (Rp)</th>
            <th>Netto Stockpile (KG)</th>
            <th>Shrink Price (Rp)</th>
            <th>Shrink (KG)</th>
            <th>Shrink Amount (Rp)</th>
            <th>Unit Cost (Rp)</th>
            <th>Mutasi Amount (Rp/kg)</th>
            <th>Inventory Value (KG)</th>
            <th>Loading Date</th>
            <th>Transaction Date</th>

        </thead>
        <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                $unitCost = $rowData->price;
                $sendWeightTransit = $rowData->send_weight;
                $nettoTransit = $rowData->netto_weight;

                $nettoStockpile = $sendWeightTransit * $totalNettoWeight / $totalSendWeight;
                $shrink = $sendWeightTransit - $nettoStockpile;
                if ($shrink <= 0) {
                    $shrink = 0;
                } else {
                    $shrink;
                }
                $shrinkAmount = $shrink * ($mutasiValueTon + $unitCost);
                $shrinkValueTon = $shrinkAmount / $nettoStockpile;
                $unitPrice = $unitCost + $mutasiValueTon + $shrinkValueTon;
                $inventoryValue = $nettoStockpile * $unitPrice;
                ?>
                <tr>
                    <input type="checkbox" name="stockTransitId" value="<?php echo $rowData->stock_transit_id ?>"
                           checked class="hidden">
                    <td><?php echo $rowData->stockpile_from; ?></td>
                    <td><?php echo $rowData->stockpile_to; ?></td>
                    <td><?php echo $rowData->contract_no; ?></td>
                    <td><?php echo number_format($rowData->send_weight, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($rowData->netto_weight, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($unitCost, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($nettoStockpile, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($shrinkValueTon, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($shrink, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($shrinkAmount, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($unitPrice, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($mutasiValueTon, 0, ".", ",");; ?></td>
                    <td><?php echo number_format($inventoryValue, 0, ".", ",");; ?></td>
                    <td><?php echo $rowData->loading_date; ?></td>
                    <td><?php echo $rowData->transaction_date; ?></td>

                </tr>
                <?php
            }
        } else {
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