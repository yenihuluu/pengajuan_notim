<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$sumProperty = '';
$periodFrom = '';
$periodTo = '';
$balanceBefore = 0;
$boolBalanceBefore = false;
$stockpileId = '';
$stockpileIds = '';

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

$sql = "INSERT INTO user_access (user_id,access,access_date) VALUES ({$_SESSION['userId']},'VIEW TIMBANGAN',STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodFrom'];
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $periodFrom = $_POST['periodTo'];
    $whereProperty .= " AND IF(t.transaction_type = 1, t.unloading_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), t.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y')) ";
}

$sql = "SELECT s.stockpile_id,s.stockpile_name,COALESCE(stscan.scanSuratTugas,0) AS scanSuratTugas ,
COALESCE(stNoScan.suratTugasTanpaScan,0) AS suratTugasTanpaScan,
COALESCE(stSystem.SuratTugastoSystem,0)AS SuratTugastoSystem,COALESCE(timbangan.tiketTimbang,0) AS tiketTimbang,
 COALESCE(timbanganSys.timbanganToSystem,0) AS timbanganToSystem,COALESCE(timbangMan.timbanganmanual,0) AS timbanganmanual,
 COALESCE(timbangan.tiketTimbang,0)-COALESCE(timbanganSys.timbanganToSystem,0) as selisih,
COALESCE(rejected.rejectst,0) AS rejectST
 FROM stockpile s
 LEFT JOIN(
 SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS scanSuratTugas
 FROM surattugas st INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
 WHERE st.terima_entry_date BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))*1000 AND UNIX_TIMESTAMP(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))*1000+86400000
 AND st.status_srtgs <> 2 GROUP BY s.`stockpile_id`,s.`stockpile_name` )AS stscan ON stscan.stockpile_id=s.`stockpile_id`
 LEFT JOIN(
 SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS suratTugasTanpaScan
 FROM surattugas st INNER JOIN transaction_timbangan tt ON tt.`counter`= st.counter
 INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
 WHERE st.terima_entry_date =0 AND st.kirim_entry_date = 0 AND st.status_srtgs <> 2
 AND tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
 GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS stNoScan ON stNoScan.stockpile_id=s.`stockpile_id`
 LEFT JOIN
 ( SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS SuratTugastoSystem
 FROM surattugas st INNER JOIN transaction_timbangan tt ON tt.`counter`= st.counter
 INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
 WHERE st.terima_entry_date BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))*1000 AND UNIX_TIMESTAMP(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))*1000+86400000
 AND tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
 AND st.status_srtgs <> 2 GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS stSystem ON stSystem.stockpile_id=s.`stockpile_id`
 LEFT JOIN(
 SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(tt.`transaction_id`) AS tiketTimbang
 FROM transaction_timbangan tt INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=tt.`stockpile_contract_id`
 INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
  WHERE tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
  GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS timbangan ON timbangan.stockpile_id=s.`stockpile_id`
  LEFT JOIN(
  SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(tt.`transaction_id`) AS timbanganToSystem
  FROM transaction_timbangan tt INNER JOIN `transaction` t ON t.`t_timbangan`=tt.`transaction_id`
  INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=tt.`stockpile_contract_id`
  INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
  WHERE tt.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
  AND (t.`t_timbangan` IS NOT NULL OR t.`t_timbangan`<>0)
  AND t.`slip_retur` IS NULL GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS timbanganSys ON timbanganSys.stockpile_id=s.`stockpile_id`
  LEFT JOIN(
  SELECT s.`stockpile_id`,s.`stockpile_name`,COALESCE(COUNT(t.`transaction_id`),0) AS timbanganmanual
  FROM `transaction` t INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=t.`stockpile_contract_id`
  INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
  WHERE t.`transaction_date` BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y')
  AND t.`t_timbangan` =0 AND t.`slip_retur` IS NULL GROUP BY s.`stockpile_id`,s.`stockpile_name` ) AS timbangMan ON timbangMan.stockpile_id=s.`stockpile_id`
  LEFT JOIN( 
	SELECT s.`stockpile_id`,s.`stockpile_name`,COUNT(st.idsurattugas) AS rejectST 
	FROM surattugas st INNER JOIN stockpile_contract sc ON sc.`stockpile_contract_id`=st.`stockpile_contract_id` 
	INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id` 
	WHERE st.terima_entry_date BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$periodFrom}', '%d/%m/%Y'))*1000 AND UNIX_TIMESTAMP(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'))*1000+86400000 AND st.status_srtgs = 2 
	GROUP BY s.`stockpile_id`,s.`stockpile_name` )AS rejected ON rejected.stockpile_id=s.`stockpile_id`
  WHERE timbangan.tiketTimbang IS NOT NULL
  GROUP BY s.`stockpile_id`,s.`stockpile_name`";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//echo $sql;
?>

<form method="post" action="reports/timbangan-xls.php">
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>Stockpile</th>
            <th>Tiket Timbang</th>
            <th>Notim Timbangan</th>
            <th>Selisih</th>
            <th>Notim Manual</th>
            <th>Scan</th>
            <th>W/O Scan</th>
            <th>ST System</th>
			<th>Reject ST</th>
        </tr>
    </thead>
    <tbody>
      <?php
      if($result === false) {
          echo $sql;
      } else {

                  while($row2 = $result->fetch_object()) {

              ?>
        <tr>
            <td><?php echo $row2->stockpile_name; ?></td>
            <td><?php echo $row2->tiketTimbang; ?></td>
            <td><?php echo $row2->timbanganToSystem; ?></td>
            <td><?php echo $row2->selisih; ?></td>
            <td><?php echo $row2->timbanganmanual; ?></td>
            <td><?php echo $row2->scanSuratTugas; ?></td>
            <td><?php echo $row2->suratTugasTanpaScan; ?></td>
            <td><?php echo $row2->SuratTugastoSystem; ?></td>			
            <td><?php echo $row2->rejectST; ?></td>
        </tr>
                <?php
              }
        }
        ?>
    </tbody>
</table>
