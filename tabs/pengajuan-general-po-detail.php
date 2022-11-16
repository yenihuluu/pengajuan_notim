<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'Pengajuan General';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Pengajuan General Data">
$inputDate = $date->format('d/m/Y');
$idPOHDR = '';
$termin = '';

// </editor-fold>
if (isset($_POST['idPOHDR']) && $_POST['idPOHDR'] != '') {
    $termin = $_POST['termin'];
    $idPOHDR = $_POST['idPOHDR'];

    $transaksiPO = $_POST['transaksiPO'];
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT p.*,DATE_FORMAT(p.tanggal,'%d/%m/%Y') as date ,s.*FROM po_hdr p 
            LEFT JOIN stockpile s ON p.stockpile_id = s.stockpile_id 
            WHERE idpo_hdr = $idPOHDR";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $requestDate = $rowData->date;
        $stockpile = $rowData->stockpile_name;
        $stockpileId = $rowData->stockpile_id;
        $noPO = $rowData->no_po;
    }
    // </editor-fold>

    $sqlPODetail = "SELECT CONCAT(pd.qty,' ','') AS qty, harga, keterangan, pd.amount, i.item_name,
			(CASE WHEN pd.pphstatus = 1 THEN pd.pph ELSE 0 END) AS pph,
			(CASE WHEN pd.ppnstatus = 1 THEN pd.ppn ELSE 0 END) AS ppn,
    		(pd.amount+(CASE WHEN pd.ppnstatus = 1 THEN pd.ppn ELSE 0 END)-(CASE WHEN pd.pphstatus = 1 THEN pd.pph ELSE 0 END)) AS grandtotal,
            u.uom_type,s.`stockpile_name`, sh.`shipment_no`,
            (SELECT SUM(pgd.termin) FROM pengajuan_general_detail pgd WHERE pgd.po_detail_id = pd.idpo_detail) AS total_termin,
            (SELECT SUM(id.tamount_converted) FROM invoice_detail id WHERE id.pgd_id = pgd.pgd_id) AS paid
			
			FROM po_detail pd
			LEFT JOIN master_item i ON i.idmaster_item = pd.item_id
            LEFT JOIN uom u ON u.idUOM = i.uom_id
			LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
            LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
            LEFT JOIN pengajuan_general_detail pgd ON pgd.po_detail_id = pd.idpo_detail
			WHERE no_po = '{$noPO}' ORDER BY pd.entry_date ASC";
    $resultPODetail = $myDatabase->query($sqlPODetail, MYSQLI_STORE_RESULT);



}

// <editor-fold defaultstate="collapsed" desc="Functions">


// </editor-fold>

?>

        <?php if (isset($idPOHDR) && $idPOHDR != '') { ?>
            PO detail
            <table width="100%" class="table table-bordered table-striped" style="font-size: 8pt;">
                <thead>
                <tr>
                    <th>Shipment Code</th>
                    <th>Stockpile</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>VAT</th>
                    <th>WHT</th>
                    <th>PAID</th>
                    <th>Pengajuan Amount</th>
                    <th>Available Amount</th>
                    <th>Total Amount PO</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php
                    if ($resultPODetail !== false && $resultPODetail->num_rows > 0) {
                    $totalPaid = 0;
                    $totalPAmount = 0;
                    $totalAvailableAmount = 0;
                    while ($row = $resultPODetail->fetch_object()) {
                    $amount = $row->amount;
                    $totalPrice = $totalPrice + $amount;
                    $tpph = $row->pph;
                    $tppn = $row->ppn;
                    $paid = $row->paid;
                    $tgtotal = $row->grandtotal;
                    $tamount = $amount + $tppn - $tpph;
                    $AvailAmount = $tamount - $paid;
                    $totalpph = $totalpph + $tpph;
                    $totalppn = $totalppn + $tppn;
                    $totalall = $totalall + $tamount;

                    $pAmount = $tamount * $termin / 100;
                    $totalPAmount += $pAmount;
                    $totalPaid += $paid;
                    $totalAvailableAmount += $AvailAmount;

                    ?>
                    <td><?php echo $row->shipment_no; ?></td>
                    <td><?php echo $row->stockpile_name; ?></td>
                    <td><?php echo $row->qty; ?></td>
                    <td><?php echo number_format($row->harga, 2, ".", ","); ?></td>
                    <td><?php echo $row->item_name; ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->amount * $termin / 100, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($tppn * $termin / 100, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($tpph * $termin / 100, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($paid, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($pAmount, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($AvailAmount, 2, ".", ","); ?></td>
                    <td style="text-align: right;"><?php echo number_format($tamount, 2, ".", ","); ?></td>

                    <input type="hidden" name="grandTotal"
                           value="<?php echo number_format($tamount, 2, ".", ","); ?>">
                </tr>
                <?php }
                } ?>
                </tbody>
                <tfoot>

                <tr>
                    <td colspan="5" style="text-align: right;"> Grand Total</td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalPrice * $termin / 100, 2, ".", ","); ?></td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalppn * $termin / 100, 2, ".", ","); ?></td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalpph * $termin / 100, 2, ".", ","); ?></td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalPaid, 2, ".", ","); ?></td>
                    <td colspan="1" style="text-align: right;">
                        <?php echo number_format($totalPAmount, 2, ".", ","); ?>
                    </td>
                    <td colspan="1"
                        style="text-align: right;"><?php echo number_format($totalAvailableAmount, 2, ".", ","); ?></td>
                    <td colspan="1" style="text-align: right;"><?php echo number_format($totalall, 2, ".", ","); ?></td>

                </tr>

                <tr>
                </tr>
                </tfoot>
            </table>
        <?php } ?>