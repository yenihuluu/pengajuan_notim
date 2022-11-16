<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$stockpileProperty = '';
$stockpileId = '';
$periodOf = '';
$month = '';
$year = '';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
    $whereProperty .= " AND t.stockpile_contract_id IN (SELECT sc.stockpile_contract_id FROM stockpile_contract sc WHERE sc.stockpile_id = {$stockpileId}) ";
}

if(isset($_POST['periodOf']) && $_POST['periodOf'] != '') {
    $periodOf = $_POST['periodOf'];
    $splitPeriodOf = explode("/", $periodOf);
    $month = $splitPeriodOf[0];
    $year = $splitPeriodOf[1];
    
    $whereProperty .= " AND MONTH(t.unloading_date) = {$month} ";
    $whereProperty .= " AND YEAR(t.unloading_date) = {$year} ";
}

$sql = "SELECT day FROM calendar ORDER BY day ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/daily-summary-report.php', {
                   periodOf: $('input[id="periodOf"]').val()

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th>DATE</th>
            <?php
            $sqlHead = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN (
                            SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']}
                        ) ORDER BY stockpile_name";
            $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
            if($resultHead->num_rows > 0) {
                while($rowHead = $resultHead->fetch_object()) {
                    echo '<th>'. strtoupper($rowHead->stockpile_name) .'</th>';
                }
            }
            ?>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while($row = $result->fetch_object()) {
            $whereRowProperty = '';
            $total = 0;
            echo '<tr><td>'. $row->day .'</td>';
            $whereRowProperty = " AND DAY(t.unloading_date) = {$row->day} ";
            $whereRowProperty .= $whereProperty;
            
            $sqlBody = "SELECT stockpile_id, stockpile_name FROM stockpile WHERE stockpile_id IN (
                            SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']}
                        ) ORDER BY stockpile_name";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $stockpileProperty = " AND t.stockpile_contract_id IN (SELECT sc.stockpile_contract_id FROM stockpile_contract sc WHERE sc.stockpile_id = {$rowBody->stockpile_id}) ";
                    
                    $sqlContent = "SELECT COALESCE(SUM(t.quantity), 0) AS quantity
                                FROM `transaction` t
                                WHERE 1=1 AND t.company_id = {$_SESSION['companyId']} {$whereRowProperty} {$stockpileProperty}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
                        echo '<td style="text-align: right;">'. number_format($rowContent->quantity, 0, ".", ",") .'</td>';
                        $total = $total + $rowContent->quantity;
                    } else {
                        echo '<td style="text-align: right;">0</td>';
                    }
                }
            }
            echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td></tr>';
        }
        ?>
    </tbody>
</table>

<form method="post" id="downloadxls" action="reports/daily-summary-report-xls.php">
    <input type="hidden" id="periodOf" name="periodOf" value="<?php echo $periodOf; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>