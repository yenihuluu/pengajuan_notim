<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';

$period = '';
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
if(isset($_POST['period']) && $_POST['period'] != '') {
    $period = $_POST['period'];
   // $module2 = $module1 - 1;
   // $whereProperty .= " AND a.general_ledger_module = '{$module}' ";
    
}


$sql = "CALL `SP_Balancesheet_Period` ('{$period}')";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/bs-report.php', {
                    period: document.getElementById('period').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" id="downloadxls" action="reports/bs-report-xls.php">

    <input type="hidden" id="period" name="period" value="<?php echo $period; ?>" />

    <button class="btn btn-success">Download XLS</button>

</form>


<table class="table table-bordered table-striped" style="font-size: 8pt;">

    <thead>

        <tr>

            <th>No Akun</th>

            <th>Nama Akun</th>

            <th>Januari</th>

            <th>Februari</th>

            <th>Maret</th>

            <th>April</th>

            <th>Mei</th>

            <th>Juni</th>
            
            <th>Juli</th>

            <th>Agustus</th>

            <th>September</th>

            <th>Oktober</th>

            <th>November</th>

            <th>Desember</th>
        </tr>

    </thead>

    <tbody>

        <?php

        if($result === false) {

            echo 'wrong query';
			echo $sql;
        } else {

            while($row = $result->fetch_object()) {

		
                ?>
    

        <tr>
			
            <td style="text-align: left;"><?php echo $row->account_no; ?></td>

            <td style="text-align: left;"><?php echo $row->account_name; ?></td>

            <td style="text-align: right;"><?php echo number_format($row->jan, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->feb, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->mar, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->apr, 0, ".", ",") ?></td>
           
            <td style="text-align: right;"><?php echo number_format($row->mei, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->jun, 0, ".", ",") ?></td>
            
			<td style="text-align: right;"><?php echo number_format($row->jul, 0, ".", ",") ?></td>
			
            <td style="text-align: right;"><?php echo number_format($row->agt, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->sep, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->okt, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->nov, 0, ".", ",") ?></td>

            <td style="text-align: right;"><?php echo number_format($row->des, 0, ".", ",") ?></td>
        </tr>

                <?php

            }

        }

        ?>

    </tbody>

</table>