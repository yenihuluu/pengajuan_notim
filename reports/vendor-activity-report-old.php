<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$joinPaymentProperty = '';
$stockpileId = '';
$transactionType = '';
$purchaseType = '';
$vendorId = '';
$periodFrom = '';
$periodTo = '';
$dateField = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
}

if(isset($_POST['transactionType']) && $_POST['transactionType'] != '') {
    $transactionType = $_POST['transactionType'];
    
    if($transactionType == 1) {
        $transactionName = 'PKS';
        $joinPaymentProperty = 't.payment_id';
    } elseif($transactionType == 2) {
        $transactionName = 'Freight Cost';
        $joinPaymentProperty = 't.fc_payment_id';
    } elseif($transactionType == 3) {
        $transactionName = 'Unloading Cost';
        $joinPaymentProperty = 't.uc_payment_id';
    }
}

if(isset($_POST['purchaseType']) && $_POST['purchaseType'] != '') {
    $purchaseType = $_POST['purchaseType'];
    $whereProperty .= " AND con.contract_type = {$purchaseType} ";
}

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.unloading_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty .= " AND t.unloading_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty .= " AND t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}

$sql = "SELECT t.*,
            DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
            s.stockpile_name,  
            con.po_no, 
            con.contract_no, 
            vh.vehicle_name,
            t.vehicle_no,
            DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2,
            DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
            CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2,
            CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code,
            v1.vendor_name, 
            v3.vendor_name AS supplier,
            CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
            DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date2, p.payment_no,
            CONCAT(b.bank_name, ' - ', b.bank_account_no) AS bank_full,
            CASE WHEN p.payment_id IS NULL THEN 'OS' ELSE 'PAID' END AS payment_status,
            p.amount_converted AS payment_amount
        FROM transaction t
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = t.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v1
            ON v1.vendor_id = con.vendor_id
        LEFT JOIN unloading_cost uc
            ON uc.unloading_cost_id = t.unloading_cost_id
        LEFT JOIN vehicle vh
            ON vh.vehicle_id = uc.vehicle_id
        LEFT JOIN freight_cost fc
            ON fc.freight_cost_id = t.freight_cost_id
        LEFT JOIN freight f
            ON f.freight_id = fc.freight_id
        LEFT JOIN vendor v2
            ON v2.vendor_id = fc.vendor_id
        LEFT JOIN vendor v3
            ON v3.vendor_id = t.vendor_id
        LEFT JOIN payment p
            ON p.payment_id = {$joinPaymentProperty}
        LEFT JOIN bank b
            ON b.bank_id = p.bank_id
        WHERE 1=1
        AND t.transaction_type = 1 {$whereProperty} LIMIT 10";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">Area</th>
            <th rowspan="2">Transaction Date</th>
            <th rowspan="2">Slip No</th>
            <th rowspan="2">COA No</th>
            <th rowspan="2">Transaction Type</th>
            <th rowspan="2">Purchase Type</th>
            <th rowspan="2">CONTRACT NAME</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">CONTRACT NO</th>
            <th rowspan="2">PKS SOURCE</th>
            <th rowspan="2">JENIS KENDARAAN</th>
            <th rowspan="2">NO. POLISI</th>
            <th colspan="6">Product (PKS)</th>
            <th colspan="6">PAYMENT STATUS</th>
            <th>CONTROL</th>
        </tr>
        <tr>
            <th>Berat Kirim (kg)</th>
            <th>Berat Netto (kg)</th>
            <th>Inventory (kg)</th>
            <th>Berat Susut (kg)</th>
            <th>
                <?php
                if($transactionType == 1) {
                    echo 'Price /kg';
                } elseif($transactionType == 2) {
                    echo 'FREIGHT COST /KG';
                } elseif($transactionType == 3) {
                    echo 'Unloading Cost';
                }
                ?>
            </th>
            <th>Amount</th>
            <th>PAID/OS</th>
            <th>PAYMENT DATE</th>
            <th>VOUCHER NO</th>
            <th>PAYMENT SOURCE</th>
            <th>CHQ NO</th>
            <th>CHQ AMOUNT</th>
            <th>UNDER (OVER) PAYMENT</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
            while($row = $result->fetch_object()) {
                ?>
        <tr>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->transaction_date2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td></td>
            <td><?php echo $transactionName; ?></td>
            <td><?php echo $row->contract_type2; ?></td>
            <td><?php echo $row->supplier; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo $row->contract_no; ?></td>
            <td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->vehicle_name; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?></td>
            <td style="text-align: right;">
                <?php
                if($transactionType == 1) {
                    echo number_format($row->unit_price, 2, ".", ",");
                } elseif($transactionType == 2) {
                    echo number_format($row->freight_price, 2, ".", ",");
                } elseif($transactionType == 3) {
                    echo number_format($row->unloading_price, 2, ".", ",");
                }
                ?>
            </td>
            <td style="text-align: right;">
                <?php
                if($transactionType == 1) {
                    echo number_format($row->quantity * $row->unit_price, 2, ".", ",");
                } elseif($transactionType == 2) {
                    echo number_format($row->quantity * $row->freight_price, 2, ".", ",");
                } elseif($transactionType == 3) {
                    echo number_format($row->unloading_price, 2, ".", ",");
                }
                ?>
            </td>
            <td><?php echo $row->payment_status; ?></td>
            <td><?php echo $row->payment_date2; ?></td>
            <td><?php echo $row->payment_no; ?></td>
            <td><?php echo $row->bank_full; ?></td>
            <td></td>
            <td><?php echo number_format($row->payment_amount, 2, ".", ","); ?></td>
            <td></td>
        </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>