<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$stockpileId = '';
$journalType = '';
$purchaseType = '';
$periodFrom = '';
$periodTo = '';
$dateField = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND sc.stockpile_id = {$stockpileId} ";
}

if(isset($_POST['journalType']) && $_POST['journalType'] != '') {
    $journalType = $_POST['journalType'];
}

if(isset($_POST['purchaseType']) && $_POST['purchaseType'] != '') {
    $purchaseType = $_POST['purchaseType'];
    $whereProperty .= " AND con.contract_type = '{$purchaseType}' ";
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

if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
    $dateField = 't.transaction_date';
} elseif($journalType == 'FREIGHT' || $journalType == 'UNLOADING') {
    $dateField = 't.unloading_date';
}

$sql = "SELECT s.stockpile_name, DATE_FORMAT({$dateField}, '%d %b %Y') AS transaction_date2, l.labor_name,
                t.slip_no, CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2,
                con.po_no, CONCAT(f.freight_code, '-', v2.vendor_code) AS freight_code, f.freight_supplier,
                v1.vendor_name, t.send_weight, t.netto_weight, t.freight_quantity, t.quantity, t.shrink, t.unit_price, t.freight_price, t.unloading_price, vh.vehicle_name
            FROM transaction t
            LEFT JOIN stockpile_contract sc
                ON sc.stockpile_contract_id = t.stockpile_contract_id
            LEFT JOIN stockpile s
                ON s.stockpile_id = sc.stockpile_id
            LEFT JOIN contract con
                ON con.contract_id = sc.contract_id
            LEFT JOIN vendor v1
                ON v1.vendor_id = con.vendor_id
            LEFT JOIN freight_cost fc
                ON fc.freight_cost_id = t.freight_cost_id
            LEFT JOIN freight f
                ON f.freight_id = fc.freight_id
            LEFT JOIN vendor v2
                ON v2.vendor_id = fc.vendor_id
            LEFT JOIN unloading_cost uc
                ON uc.unloading_cost_id = t.unloading_cost_id
            LEFT JOIN vehicle vh
                ON vh.vehicle_id = uc.vehicle_id
			LEFT JOIN labor l
				ON t.labor_id = l.labor_id
            WHERE 1=1
            AND t.transaction_type = 1 {$whereProperty} 
            AND t.company_id = {$_SESSION['companyId']}
			ORDER BY t.slip_no ASC
            LIMIT 10 ";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th rowspan="2">Area</th>
            <th rowspan="2">Transaction Date</th>
            <th rowspan="2">Slip No</th>
            <th rowspan="2">Purchase Type</th>
            <th rowspan="2">PO No.</th>
            <?php
            if($journalType == 'FREIGHT' || $journalType == 'UNLOADING') {
            ?>
            <th rowspan="2">Jenis Kendaraan</th>
            <?php
            }
            ?>
			 <th rowspan="2">PKS SOURCE</th>
            <th rowspan="2">LABOR</th>
            <th rowspan="2">SUPPLIER FREIGHT</th>
           
            <th colspan="6">
                <?php
                if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
                    echo 'Product (PKS)';
                } elseif($journalType == 'FREIGHT') {
                    echo 'FREIGHT COST';
                } elseif($journalType == 'UNLOADING') {
                    echo 'UNLOADING COST';
                }
                ?>
            </th>
        </tr>
        <tr>
            <th>Berat Kirim (kg)</th>
            <th>Berat Netto (kg)</th>
            <th>Inventory (kg)</th>
            <th>Berat Susut (kg)</th>
            <th>
                <?php
                if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
                    echo 'Price /kg';
                } elseif($journalType == 'FREIGHT') {
                    echo 'FREIGHT COST /KG';
                } elseif($journalType == 'UNLOADING') {
                    echo 'TOTAL Unloading Cost';
                }
                ?>
            </th>
            <?php
            if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
            ?>
            <th>Amount</th>
            <?php
            } elseif($journalType == 'FREIGHT') {
            ?>
            <th>Total Freight Cost</th>
            <?php
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
                ?>
        <tr>
            <td><?php echo $row->stockpile_name; ?></td>
            <td><?php echo $row->transaction_date2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->contract_type2; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <?php
            if($journalType == 'FREIGHT' || $journalType == 'UNLOADING') {
            ?>
            <td><?php echo $row->vehicle_name; ?></td>
            <?php
            }
            ?>
			<td><?php echo $row->vendor_name; ?></td>
            <td><?php echo $row->labor_name; ?></td>
            <td><?php echo $row->freight_supplier; ?></td>
            
            <td style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <td style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?></td>
            <td style="text-align: right;">
                <?php 
                if($journalType == 'PURCHASE' || $journalType == 'SHRINK') {
                    echo number_format($row->unit_price, 2, ".", ","); 
                } elseif($journalType == 'FREIGHT') {
                    echo number_format($row->freight_price, 2, ".", ",");
                } elseif($journalType == 'UNLOADING') {
                    echo number_format($row->unloading_price, 2, ".", ",");
                }
                ?>
            </td>
            <?php
            if($journalType == 'PURCHASE' || $journalType == 'SHRINK' || $journalType == 'FREIGHT') {
            ?>
            <td style="text-align: right;">
                <?php 
                if($journalType == 'PURCHASE') { 
                    echo number_format($row->quantity * $row->unit_price, 2, ".", ","); 
                } elseif($journalType == 'SHRINK') { 
                    echo number_format($row->shrink * $row->unit_price, 2, ".", ","); 
                } elseif($journalType == 'FREIGHT') {
                    echo number_format($row->freight_quantity * $row->freight_price, 2, ".", ",");
                }
                ?>
            </td>
            <?php
            }
            ?>
        </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>

<form method="post" action="reports/purchase-report-xls.php">
    <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileId; ?>" />
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <input type="hidden" id="journalType" name="journalType" value="<?php echo $journalType; ?>" />
    <input type="hidden" id="purchaseType" name="purchaseType" value="<?php echo $purchaseType; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>