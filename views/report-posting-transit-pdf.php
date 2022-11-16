<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Report Posting Stock Transit';
$date = new DateTime();
//$paymentDate = $date->format('d/m/Y');

$kodeMutasi = '';
if (isset($_POST['kodeMutasi']) && $_POST['kodeMutasi'] != '') {
    $kodeMutasi = $_POST['kodeMutasi'];
    $totalSendWeight = $_POST['totalSendWeight'];

   ///TABLE PALING BAWAH
    $sql1 = $myDatabase->query("select mh.*, sum(t.netto_weight) as total_netto, t.* from mutasi_header mh 
    left join `transaction` t on t.mutasi_id = mh.mutasi_header_id 
    where mh.mutasi_header_id = {$kodeMutasi}", MYSQLI_STORE_RESULT);
    $rowDatas = $sql1->fetch_object();
    $stockpileId = $rowDatas->stockpile_to;
    $totalNettoWeight = $rowDatas->total_netto;
   // $inventoryValue = $rowData->inventory_value;
    $mutasiValueTon = $rowDatas->mutasi_amount;
    //$unitCost = $rowData->unit_price;
    //$shrink = $rowData->shrink_amount;
    //$shrinkValueTon = $rowData->shrink_price;

    $sql = "select st.*, st.send_weight as send_weightST,st.netto_weight as netto_weightST, CONCAT (s.stockpile_code, ' - ', s.stockpile_name) as stockpile_from, CONCAT (ss.stockpile_code, ' - ', ss.stockpile_name) as stockpile_to, c.contract_no as contract_no, c.price,
            mh.kode_mutasi as destination_code, t.mutasi_amount, t.unit_cost as unit_costTrx, t.inventory_value as inventory_valueTrx, t.shrink as shrinkTrx, t.netto_weight as netto_weightTrx, t.shrink_price as shrink_priceTrx, t.shrink_amount as shrink_amountTrx
from stock_transit st
left join mutasi_header mh on st.mutasi_header_id = mh.mutasi_header_id
left join `transaction` t on t.mutasi_id = mh.mutasi_header_id
left join stockpile s on mh.stockpile_from = s.stockpile_id
left join stockpile ss on mh.stockpile_to = ss.stockpile_id
left join stockpile_contract sc on sc.stockpile_contract_id = st.stockpile_contract_id
left join contract c on sc.contract_id = c.contract_id
left join `transaction` t1 on sc.stockpile_contract_id = t.stockpile_contract_id
where mh.mutasi_header_id = {$kodeMutasi} and st.status = 1
and t.notim_status != 1 and t.slip_retur is null
AND SUBSTR(st.`kode_stock_transit`,29,2) != '-s'
group by st.stock_transit_id";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//    echo $sql;


    //HEADER DIATAS
    $sqlHeader = "select st.*, CONCAT (s.stockpile_code, ' - ', s.stockpile_name) as stockpile_from, CONCAT (ss.stockpile_code, ' - ', ss.stockpile_name) as stockpile_to, c.contract_no as contract_no, c.price, mh.kode_mutasi as destination_code, mh.status as status_mutasi,
(SELECT  DATE_FORMAT(MAX(tt.transaction_date), '%Y-%m-%d') from transaction_timbangan tt
left join mutasi_header mh on tt.mutasi_header_id = mh.mutasi_header_id) as transaction_date
from stock_transit st left join mutasi_header mh on st.mutasi_header_id = mh.mutasi_header_id 
left join stockpile s on mh.stockpile_from = s.stockpile_id
left join stockpile ss on mh.stockpile_to = ss.stockpile_id
left join stockpile_contract sc on sc.stockpile_contract_id = st.stockpile_contract_id
left join contract c on sc.contract_id = c.contract_id
where mh.mutasi_header_id = {$kodeMutasi} and st.status = 1";
    $resultHeader = $myDatabase->query($sqlHeader, MYSQLI_STORE_RESULT);

    //TABLE MUTASI DEYAIL
    $sql2 = "SELECT md.*, mbm.tipe_biaya, mh.kode_mutasi, mh.total AS total_mutasi, td.percentage AS termin_percentage, id.mutasi_detail_id, 
            id.amount_converted-COALESCE(a.dp,0) AS amount_converted, id.invoice_detail_id, i.invoice_no, id.notes, DATE_FORMAT(i.invoice_date, '%Y-%m-%d') AS invoice_date, 
            DATE_FORMAT(p.payment_date, '%Y-%m-%d') AS payment_date, p.payment_no, p.payment_id,
(SELECT CONCAT(ROUND(id.termin,0),'% / ',a.percentage,'%') FROM termin_detail a LEFT JOIN mutasi_detail b ON a.id = b.termin_detail_id WHERE b.mutasi_detail_id = id.mutasi_detail_id) AS termin_percentage2
        FROM mutasi_detail md
        LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = md.mutasi_header_id
        LEFT JOIN currency AS c ON md.currency_id = c.currency_id
        LEFT JOIN master_biaya_mutasi mbm ON md.biaya_mutasi_id = mbm.id
        LEFT JOIN termin_detail td ON md.termin_detail_id = td.id 
        LEFT JOIN master_termin mt ON mt.id = td.termin_id
        LEFT JOIN invoice_detail id ON md.mutasi_detail_id = id.mutasi_detail_id
        LEFT JOIN invoice i ON i.invoice_id = id.invoice_id
        RIGHT JOIN payment p ON p.invoice_id = i.invoice_id  AND p.payment_status != 1 AND i.payment_status != 2
        LEFT JOIN
        (
         SELECT invoice_detail_id, SUM(amount_payment) AS dp
        FROM invoice_dp
        GROUP BY invoice_detail_id
        ) AS a ON a.invoice_detail_id = id.`invoice_detail_id`
         WHERE md.mutasi_header_id = {$kodeMutasi} ORDER BY md.general_vendor_id ASC";
    $rowData = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);

    //VIEW JOURNAL
    $allowViewJurnal = false;
    $sqlUser = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
    $resultUser = $myDatabase->query($sqlUser, MYSQLI_STORE_RESULT);
    if ($resultUser->num_rows > 0) {
        while ($row = $resultUser->fetch_object()) {
            if ($row->module_id == 25) {
                $allowViewJurnal = true;
            }
        }
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
                    document.getElementById('totalSendWeight').value = returnVal[1];
                    $('#pageContent').load('views/report-posting-transit.php', {
                        kodeMutasi: $('select[id="kodeMutasi"]').val(),
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
                document.getElementById('totalSendWeight').value = returnVal[1];
                $('#pageContent').load('views/posting-transit-pdf.php', {
                    kodeMutasi: $('select[id="kodeMutasi"]').val(),
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
                            $('#pageContent').load('views/report-posting-transit.php', {}, iAmACallbackFunction);
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

<script type="text/javascript">
    $(document).ready(function () {

        $(".kodeMutasi").select2({
            width: "100%"
        });
    });


</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#btnPrint').click(function (e) {
            e.preventDefault();
            $("#reportMutasiContainer").printThis();
        });
    });

    function back() {
        $('#pageContent').load('views/report-posting-transit.php', {}, iAmACallbackFunction);
    }
</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>
<input type="hidden" name="stockpileId" id="stockpileId" value="<?php echo $stockpileId ?>"/>

<?php
if ($resultHeader !== false && $resultHeader->num_rows > 0) {
$rowTemp = $resultHeader->fetch_object(); ?>
<div id="reportMutasiContainer">
    <div id="headerMutasi">
        <table width="100%" style="table-layout:fixed; font-size: 9pt;">
            <tr>
                <td colspan="6" style="text-align: left; font-size: 12pt; font-weight: 600;">
                    PT. JATIM PROPERTINDO JAYA
                </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; font-size: 12pt; font-weight: 600;">TRANSFER STOCK REPORT
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" style="table-layout:fixed; font-size: 9pt;">
            <tr>
                <td width="10%"><b>Stockpile From</b></td>
                <td width="2%">:</td>
                <td width="27%"><?php echo $rowTemp->stockpile_from; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td width="10%"><b>Stockpile To</b></td>
                <td width="2%">:</td>
                <td width="27%"><?php echo $rowTemp->stockpile_to; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td width="10%"><b>Mutasi Code</b></td>
                <td width="2%">:</td>
                <td width="27%"><?php echo $rowTemp->destination_code; ?></td>
                <td></td>
                <td></td>
            </tr>
			<tr>
                <td width="10%"><b>Status</b></td>
                <td width="2%">:</td>
                <?php if ($rowTemp->status_mutasi == 0) { ?>
                    <td width="27%">NOT POSTED</td>
                <?php } else { ?>
                    <td width="27%">POSTED</td>
                <?php } ?>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <br>
    <?php } ?>

    <div class="row-fluid">
        <table class="table table-bordered table-striped" style="font-size: 8pt;">
            <thead>
            <tr>
                <th>Vendor</th>
                <th>Tipe Biaya</th>
                <th>Currency</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
				<th>In Invoice</th>
                <th>Invoice Amount</th>
                <th>Keterangan Transaksi</th>
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
                        'invoice_no' => $row['invoice_no'],
                        'notes' => $row['notes'],
                        'invoice_date' => $row['invoice_date'],
                        'payment_date' => $row['payment_date'],
                        'payment_no' => $row['payment_no'],
                        'termin_percentage2' => $row['termin_percentage2'],
                    ];
                }
                foreach ($data as $key => $row) {
                    $subtotal_inv += $row['amount_converted'];
                    $totalMutasiCost += $row['amount_converted'];
                    ?>
                    <tr>
                        <td><?php echo $row['vendor']; ?></td>
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
						<?php if ($row['mutasi_detail_id'] != '') { ?>
                            <td>YES</td>
                        <?php
                        } else { ?>
                            <td>NO</td> 
                        <?php
                        } ?>
                        <td><?php echo number_format($row['amount_converted'], 2, ".", ","); ?></td>
                        <td><?php echo $row['notes']; ?></td>
                        <td><?php echo $row['invoice_no']; ?></td>
                        <td><?php echo $row['invoice_date']; ?></td>
                        <td><?php echo $row['payment_date']; ?></td>
                        <td><?php echo $row['payment_no']; ?></td>
                        <td><?php echo $row['termin_percentage2']; ?></td>
                    </tr>
                    <?php if ($data[$key + 1]['general_vendor_id'] != $row['general_vendor_id']) { ?>
                        <tr>
                            <td><strong>Sub Total</strong></td>
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
                    <td><strong><?php echo number_format($totalMutasiCost, 2, ".", ","); ?></strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Mutasi Cost</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong><?php echo number_format($mutasiValueTon, 2, ".", ","); ?></strong></td>
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
                    <td colspan="17" style="text-align: center">
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
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 7pt;">
            <thead>
            <tr>
                <th>Stockpile From</th>
                <th>Stockpile To</th>
                <th>Tipe Biaya</th>
                <th>Contract No</th>
                <th>Send Weight Pabric (KG)</th>
                <th>Netto Weight Draft Survey (KG)</th>
                <th style="background-color: lightgray;">Unit Price (Rp/KG)</th>
                <th style="background-color: yellow">PKS Amount</th>
                <th style="background-color: lightgray">Mutasi Amount (Rp/KG)</th>
                <th style="background-color: yellow">Mutasi Cost</th>
                <th>Netto Stockpile (KG)</th>
                <th style="background-color: yellow">Inventory Value Amount</th>
                <th style="background-color: lightgray">Unit Cost (Rp/KG)</th>
                <th style="background-color: lightgray;">Shrink Price (Rp/KG)</th>
                <th>Shrink (KG)</th>
                <th>Loading Date</th>
                <th>Transaction Date</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($resultData !== false && $resultData->num_rows > 0) {
                $gtSendWeight = 0;
                $gtNettoWeight = 0;
                $gtPksAmount = 0;
                $gtMutasiCost = 0;
                $gtNettoStockpile = 0;
                $gtInventoryValue = 0;
                $gtUnitCost = 0;
                while ($rowData = $resultData->fetch_object()) {

                    $unitPrice = $rowData->price;
                    $sendWeightTransit = $rowData->send_weightST;
                    $nettoTransit = $rowData->netto_weightST;

//                    $nettoStockpile = $sendWeightTransit * $totalNettoWeight / $totalSendWeight;
                    $unitCost = $rowData->unit_costTrx;
                    $nettoStockpile = $rowData->netto_weightTrx;
                    $shrink = $rowData->shrinkTrx;
                    $shrinkValueTon = $rowData->shrink_priceTrx;
//                    $shrink = $sendWeightTransit - $nettoStockpile;
                    if ($shrink <= 0) {
                        $shrink = 0;
                    } else {
                        $shrink;
                    }
                    $shrinkAmount = $rowData->shrink_amountTrx;
//                    $shrinkValueTon = bcdiv($shrinkAmount, $nettoStockpile, 3);
                    $unitCost = $rowData->unit_costTrx;
                    $inventoryValue = $rowData->inventory_valueTrx;
                    $pksAmount = $unitPrice * $nettoTransit;
                    $mutasiCost = $mutasiValueTon * $sendWeightTransit;

                    //Grand Total
                    $gtSendWeight += $rowData->send_weightST;
                    $gtNettoWeight += $rowData->netto_weightST;
                    $gtPksAmount += $pksAmount;
                    $gtMutasiCost += $mutasiCost;
                    $gtNettoStockpile += $nettoStockpile;
                    $gtInventoryValue += $inventoryValue;
                    $gtUnitCost += $unitCost;
                    ?>
                    <tr>
                        <td><?php echo $rowData->stockpile_from; ?></td>
                        <td><?php echo $rowData->stockpile_to; ?></td>
                        <?php if ($rowData->contract_type == 'P') {
                            $tipebiaya = 'PKS' ?>
                            <td><?php echo $tipebiaya; ?></td>
                        <?php } else {
                            $tipebiaya = 'Curah' ?>
                            <td><?php echo $tipebiaya; ?></td>
                        <?php } ?>
                        <td><?php echo $rowData->contract_no; ?></td>
                        <td><?php echo number_format($sendWeightTransit, 2, ".", ",");; ?></td>
                        <td><?php echo number_format($rowData->netto_weightST, 2, ".", ",");; ?></td>
                         <td style="background-color: lightgray"><?php echo number_format($unitPrice, 3, ".", ",");; ?></td>
                        <td style="background-color: yellow"><?php echo number_format($pksAmount, 2, ".", ",");; ?></td>
                        <td style="background-color: lightgray"><?php echo number_format($mutasiValueTon, 3, ".", ",");; ?></td>
                        <td style="background-color: yellow"><?php echo number_format($mutasiCost, 3, ".", ",");; ?></td>
                        <td><?php echo number_format($nettoStockpile, 2, ".", ",");; ?></td>
                        <td style="background-color: yellow"><?php echo number_format($inventoryValue, 3, ".", ",");; ?></td>
                        <td style="background-color: lightgray"><?php echo number_format($unitCost, 3, ".", ",");; ?></td>
                        <td style="background-color: lightgray"><?php echo number_format($shrinkValueTon, 2, ".", ",");; ?></td>
                        <td><?php echo number_format($shrink, 2, ".", ",");; ?></td>
                        <td><?php echo $rowData->loading_date; ?></td>
                        <td><?php echo $rowData->transaction_date; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="4" style="text-align: center; vertical-align: middle; "><strong>TOTAL</strong></td>
                    <td style="font-weight: bold"><?php echo number_format($gtSendWeight, 2, ".", ",");; ?></td>
                    <td style="font-weight: bold"><?php echo number_format($gtNettoWeight, 2, ".", ",");; ?></td>
                    <td style="background-color: lightgray"></td>
                    <td style="background-color: yellow; font-weight: bold"><?php echo number_format($gtPksAmount, 3, ".", ",");; ?></td>
                    <td style="background-color: lightgray"></td>
                    <td style="background-color: yellow; font-weight: bold"><?php echo number_format($gtMutasiCost, 3, ".", ",");; ?></td>
                    <td style="font-weight: bold"><?php echo number_format($gtNettoStockpile, 2, ".", ",");; ?></td>
                    <td style="background-color: yellow; font-weight: bold"><?php echo number_format($gtInventoryValue, 3, ".", ",");; ?></td>
                    <td style="background-color: lightgray; font-weight: bold"><?php echo number_format($gtUnitCost, 3, ".", ",");; ?></td>
                    <td style="background-color: lightgray"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php } else {
                ?>
                <tr>
                    <td colspan="18" style="text-align: center">
                        No data to be shown.
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>


    <table width="100%" class="table table-bordered table-striped">
        <tr>
            <td width="50%">
                <b id="totalSendWeight">Total Send Weight
                    : </b> <?php echo number_format($totalSendWeight, 0, ".", ","); ?> KG
            </td>
            <td width="50%">
                <b id="totalNettoWeight">Total Netto Weight Stockpile
                    : </b> <?php echo number_format($totalNettoWeight, 0, ".", ","); ?> KG
            </td>
        </tr>
    </table>
</div>

<div class="row-fluid">
    <div class="span2 lightblue">
        <!--        <button class="btn btn-primary" onclick="submitData()">Submit</button>-->
        <button class="btn btn-warning" id="btnPrint">Print</button>
        <button class="btn btn-info" onclick="back()">Back</button>
    </div>
</div>