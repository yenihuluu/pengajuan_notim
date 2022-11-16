<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';

$module = '';
//$year= {$module1};
/*
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND a.gl_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND a.gl_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND a.gl_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}*/
if(isset($_POST['module']) && $_POST['module'] != '') {
    $module1 = $_POST['module'];
    $module2 = $module1 - 1;
   // $whereProperty .= " AND a.general_ledger_module = '{$module}' ";
    
}


$sql = "SELECT b.*, (bengkulu + dumai + jakarta + jambi + maredan + padang + rengat + sampit + tanjung_buton + tayan) AS grand_total 
FROM (SELECT a.NoAkun, a.NamaAkun, 
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Bengkulu') ELSE '0' END AS bengkulu,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Dumai') ELSE '0' END AS dumai,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Jakarta') ELSE '0' END AS jakarta,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Jambi') ELSE '0' END AS jambi,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Maredan') ELSE '0' END AS maredan,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Padang' ) ELSE '0' END AS padang,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Rengat') ELSE '0' END AS rengat,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Sampit') ELSE '0' END AS sampit,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Tanjung Buton') ELSE '0' END AS tanjung_buton,
CASE WHEN a.NoAkun IS NOT NULL THEN (SELECT COALESCE(SUM(COALESCE(g.Dr,0) - COALESCE(g.Cr,0)),0) AS total FROM gl g WHERE g.NoAkun = a.NoAkun AND g.Date BETWEEN '{$module1}-01-01 00:00:00' AND '{$module1}-12-31 23:59:00' AND g.StockpileLocation = 'Tayan') ELSE '0' END AS tayan
FROM gl a WHERE 
a.NoAkun LIKE '40%' OR a.NoAkun LIKE '51%' OR a.NoAkun LIKE '52%' OR a.NoAkun LIKE '53%' OR a.NoAkun LIKE '60%' 
OR a.NoAkun LIKE '61%' OR a.NoAkun LIKE '62%' OR a.NoAkun LIKE '70%'
GROUP BY a.NoAkun) b WHERE 1=1 ORDER BY b.NoAkun ASC";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


?>

<form method="post" action="reports/pl-sp-report-xls.php">
 
 <input type="hidden" id="module1" name="module1" value="<?php echo $module1; ?>" />
   
    <button class="btn btn-success">Download XLS</button>

</form>


<table class="table table-bordered table-striped" style="font-size: 8pt;">

    <thead>

        <tr>

            <th>No Akun</th>

            <th>Nama Akun</th>

            <th>Bengkulu</th>

            <th>Dumai</th>

            <th>Jakarta</th>

            <th>Jambi</th>

            <th>Maredan</th>

            <th>Padang</th>

            <th>Rengat</th>
            
            <th>Sampit</th>

            <th>Tanjung Buton</th>

            <th>Tayan</th>

            <th>Grand Total</th>


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
			
            <td style="text-align: left;"><?php echo $row->NoAkun; ?></td>

            <td style="text-align: left;"><?php echo $row->NamaAkun; ?></td>

            <td style="text-align: right;"><?php echo number_format($row->bengkulu, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->dumai, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->jakarta, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->jambi, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->maredan, 0, ".", ",") ?></td>
           
            <td style="text-align: right;"><?php echo number_format($row->padang, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->rengat, 0, ".", ",") ?></td>
            
			<td style="text-align: right;"><?php echo number_format($row->sampit, 0, ".", ",") ?></td>
			
            <td style="text-align: right;"><?php echo number_format($row->tanjung_buton, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->tayan, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->grand_total, 0, ".", ","); ?></td>


        </tr>

                <?php

            }

        }

        ?>

    </tbody>

</table>