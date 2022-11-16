<!-- File yang diubah Di Folder 'reports', file yang berubah ada dibawah comment 'updated' -->

<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';

$dateProperty = '';

$periodFrom = '';

$periodTo = '';

$amount = '';

$gudang = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    
    $dateProperty = " AND transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
    
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND t.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

    $dateProperty = " AND transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

    $dateProperty = " AND transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if(isset($_POST['gudang']) && $_POST['gudang'] != '') {
    $gudang = $_POST['gudang'];
    for ($i = 0; $i < sizeof($gudang); $i++) {
        if($stockpile_id == '') {
            $stockpile_id .= "'". $gudang[$i] ."'";
        } else {
            $stockpile_id .= ','. "'". $gudang[$i] ."'";
        }
    }
    $whereProperty .= " AND s.stockpile_id IN ({$stockpile_id})";

    $stockpile_name = array();
    
    $sql2 = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpile_id})";
    //echo $sql2;
    
    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
        if($result2 !== false && $result2->num_rows > 0){
            while($row2 = mysqli_fetch_array($result2)){
            $stockpile_name[] = $row2['stockpile_name'];
                    
            $stockpile_names =  "'" . implode("','", $stockpile_name) . "'";
            $spName = implode($stockpile_name);		
        }
	}
}

// updated : di '$sql' ada tambahan query case as claim untuk kolom claims

$sql = "SELECT v.vendor_name AS pks,
            SUM(t.send_weight) AS tonase_pengiriman_mt,
            COUNT(DISTINCT(CASE WHEN t.t_timbangan > 0 THEN t.t_timbangan  END)) AS ritase_pengiriman,
           -- COUNT(t.transaction_id) AS ritase_pengiriman,
            s.stockpile_name AS gudang,
        CASE
            WHEN v.rsb = 1 AND v.ggl = 0 THEN 'ISCC'
            WHEN v.ggl = 1 AND v.rsb = 0 THEN 'GGL'
            WHEN v.rsb = 1 AND v.ggl = 1 THEN 'ISCC $ GGL'
            ELSE 'Uncertified'
        END AS claim
        FROM TRANSACTION t 
        LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id
        LEFT JOIN contract c ON sc.contract_id = c.contract_id
        LEFT JOIN vendor v ON c.vendor_id = v.vendor_id
        LEFT JOIN stockpile s ON sc.stockpile_id = s.stockpile_id
        WHERE t.slip_no NOT IN (
            SELECT LEFT(slip_retur,17) AS retur FROM TRANSACTION
            WHERE slip_retur IS NOT NULL {$dateProperty}
        ) AND t.slip_retur IS NULL 
        AND ( t.mutasi_id IS NULL OR t.mutasi_id = '0')
        AND c.langsir = 0  AND t.transaction_type = 1
        {$whereProperty}
        GROUP BY v.vendor_id, s.stockpile_id
        ORDER BY v.vendor_name, s.stockpile_name";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;
?>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
       
    <form class="form-horizontal" method="post" action="reports/sustain-report-xls.php" >
        <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	<input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		<input type="hidden" id="stockpile_id" name="stockpile_id" value="<?php echo $stockpile_id; ?>" />
            <div class="control-group">
                <label class="control-label" for="module_name2">Period</label>
                <div class="controls">
                    <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $periodFrom .' - '. $periodTo; ?>" />
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="stockpile_name">Stockpile</label>
                <div class="controls">
                    <input type="text" readonly id="stockpile_name" name="stockpile_name" value="<?php echo $stockpile_names; ?>" />
                </div>
            </div>
            <div class="control-group">
               
                <div class="controls">
                    <button class="btn btn-success">Download XLS</button>
                   
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    
                </div>
            </div>
        </form>
    </div>
</div>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
			<th>No</th>
            <th>PKS</th>
            <th>Tonase Pengiriman (MT)</th>
            <th>Ritase Pengiriman</th>
            <th>Stockpile</th>
            <!-- updated : kolom tambahan 'Claims'-->
            <th>Claims</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			$no = 1;
			while($row = $result->fetch_object()) {
		?>	
			
		<tr>
			<td><?php echo $no; ?></td>
            <td><?php echo $row->pks; ?></td>
            <td><?php echo $row->tonase_pengiriman_mt; ?></td>
            <td><?php echo $row->ritase_pengiriman; ?></td>
            <td><?php echo $row->gudang; ?></td>
            <!-- updated : kolom tambahan 'Claims'-->
            <td><?php echo $row->claim; ?></td>
		</tr>

            <?php
                $no++;
            }
		}
        
        ?>
    </tbody>
</table>
