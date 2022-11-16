<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('Y-m');


$whereBalanceProperty = '';
$whereDeliveriesProperty = '';
$whereShipmentProperty = '';
$whereLessProperty = '';

$periodTo = '';
$periodFrom = '';
$temp = '';
$baseOn = '';
$Periode = '';

if(isset($_POST['periodTo']) && $_POST['periodFrom'] != '' && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $periodFrom = $_POST['periodFrom'];
    $newDate = date("Y", strtotime($periodTo));  
    $whereBalanceProperty .= " AND a.unloading_date < STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereDeliveriesProperty .= " AND t.unloading_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$whereShipmentProperty .=  " AND d.delivery_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereLessProperty .= " AND t.unloading_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    
    //$whereSalesProperty .= " AND sl.shipment_date <= DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
}

if(isset($_POST['baseOn']) && $_POST['baseOn'] != '') {
    $baseOn = $_POST['baseOn'];
        $temp = $periodFrom.'-'.$periodTo;
        if($baseOn == "Daily"){$Periode = "Tanggal";} else if($baseOn == "Monthly"){$Periode = "Bulan";}
}
?>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    
    <div class="offset3 span3">
        <form class="form-horizontal" method="post" action="reports/summary-stock-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
         <input type="hidden" id="baseOn" name="baseOn" value="<?php echo $baseOn; ?>" />
            <!-- <div class="control-group">
                <label class="control-label" for="module_name2">Date</label>
                <div class="controls">
                  <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $temp; ?>" />
                </div>
            </div>
            <div class="control-group"> -->
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
            <th rowspan="2" style="text-align:center;"><?php echo $Periode; ?></th>
            <?php
            $sqlHead = "SELECT stockpile_name, stockpile_code
                    FROM stockpile
                    ORDER BY stockpile_code ASC";
            $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
            if($resultHead->num_rows > 0) {
                while($rowHead = $resultHead->fetch_object()) {
                    echo '<th  colspan="4" style="text-align:center;">'. strtoupper($rowHead->stockpile_name) .'</th>';

                }
            }
            ?>
        </tr>
        
        <tr>
                <?php
                $sqlHead = "SELECT stockpile_name, stockpile_code
                    FROM stockpile
                    ORDER BY stockpile_code ASC";
                $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
                if($resultHead->num_rows > 0) {
                    while($rowHead = $resultHead->fetch_object()) {
                        echo '<th>Qty (MT)</th>';
                        echo '<th>Jumlah Hari</th>';
                        echo '<th>Jumlah Mobil (transaksi)</th>';
                        echo '<th>Jumlah Mobil (timbangan)</th>';
                    }
                }     
                ?>
        </tr>
    </thead>

<tbody>
 <?php
 if($baseOn == "Daily"){
    $sqlBody = "CALL sp_summary_stock_Daily('{$periodFrom}', '{$periodTo}')";
	echo $sqlBody;
 }else if($baseOn == "Monthly")
 {
    $sqlBody = "CALL sp_summary_stock_Month('{$periodFrom}', '{$periodTo}')";

 }

$resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
    $tempMonth = '';
    if($resultBody->num_rows > 0){
        while($rowBody =$resultBody->fetch_object()){
            echo '<tr>';
            echo '<td style="text-align: right;">'.$rowBody->Periode.'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyBat, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayBat, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBATNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBATTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyBen, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayBen, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBENotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBENTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyBUN, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayBUN, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBUNNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBUNTim, 0, ".", ",") .'</td>';

            
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyBUT, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayBUT, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBUTNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleBUTTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyDUM, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayDUM, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleDUMNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleDUMTim, 0, ".", ",") .'</td>';
            
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyHO, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayHO, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleHOTNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleHOTim, 0, ".", ",") .'</td>';
            
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyJAM, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayJAM, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleJAMNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleJAMTim, 0, ".", ",") .'</td>';
           
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyMAR, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayMAR, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleMARNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleMARTim, 0, ".", ",") .'</td>';
           
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyPAD, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayPAD, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePADNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePADTim, 0, ".", ",") .'</td>';
            
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyPAL, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayPAL, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePALNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePALTim, 0, ".", ",") .'</td>';
            
            echo '<td style="text-align: right;">'. number_format($rowBody->qtyPEK, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayPEK, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePEKNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePEKTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyPON, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayPON, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePONNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehiclePONTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyREN, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayREN, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleRENNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleRENTim, 0, ".", ",") .'</td>';

            
            echo '<td style="text-align: right;">'. number_format($rowBody->qtySAM, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDaySAM, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleSAMNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleSAMTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtySMR, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDaySMR, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleSMRNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleSMRTim, 0, ".", ",") .'</td>';

            echo '<td style="text-align: right;">'. number_format($rowBody->qtyTYN, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->CountDayTYN, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleTYNNotim, 0, ".", ",") .'</td>';
            echo '<td style="text-align: right;">'. number_format($rowBody->countVehicleTYNTim, 0, ".", ",") .'</td>';
            echo '</tr>';
        }
    }
?>
</tbody>  
</table>

