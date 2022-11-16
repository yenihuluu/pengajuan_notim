<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$date = new DateTime();
$currentDate = $date->format('d-M-Y');
$currentDate2 = $date->format('Y-m-d');
$boolConnection = checkInternetConnection();
$connectionText = "You're not connected to the Internet.";

if($boolConnection) {
    $connectionText = "You're connected to the Internet.";
}

$_SESSION['menu_name'] = 'Dashboard';

//unset($_SESSION['transaction']);

?>

<script type="text/javascript">
//    $(document).ajaxStop($.unblockUI);
    
    /*$(document).ready(function(){
        $('#downloadButton').click(function (e){
//            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            
            e.preventDefault();
            
            $('#downloadButton').attr("disabled", true);
            $.ajax({
                url: './sync_test.php',
                method: 'POST',
                data: "action=download",
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/dashboard.php', {}, iAmACallbackFunction);
                        } 
                        $('#downloadButton').attr("disabled", false);
                    }
                }
            });
        });
        
        $('#uploadButton').click(function (e){
//            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            
            e.preventDefault();
            
            $('#uploadButton').attr("disabled", true);
            $.ajax({
                url: './sync_test.php',
                method: 'POST',
                data: "action=upload",
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/dashboard.php', {}, iAmACallbackFunction);
                        } 
                        $('#uploadButton').attr("disabled", false);
                    }
                }
            });
        });
    });*/
</script>




<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th style="text-align: center">Stockpile</th>
			<th style="text-align: center">Last Updated</th>
			<th style="text-align: center">Balance Last Inventory</th>
			<th style="text-align: center">Send Weight Daily</th>
			<th style="text-align: center">Netto Weight Daily</th>
        </tr>
    </thead>
    <tbody>
        <?php
			$total = 0;
           $sqlContent = "SELECT (SELECT stockpile_name FROM stockpile WHERE stockpile_code = SUBSTRING(t.slip_no,1,3)) AS stockpile,
DATE_FORMAT(MAX(t.`entry_date`), '%d %b %Y %H:%i:%s') AS last_date,
ROUND(SUM(CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.`quantity` END) -
SUM(CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END),2) AS qty_available,
ROUND(SUM(CASE WHEN t.`unloading_date` = DATE_FORMAT(NOW(),'%Y-%m-%d') THEN t.`send_weight` ELSE 0 END),2) AS send_weight,
ROUND(SUM(CASE WHEN t.`unloading_date` = DATE_FORMAT(NOW(),'%Y-%m-%d') THEN t.`quantity` ELSE 0 END),2) AS netto_weight
FROM `transaction` t
LEFT JOIN stockpile s ON s.`stockpile_code` = SUBSTRING(t.slip_no,1,3)
LEFT JOIN user_stockpile us ON s.stockpile_id = us.`stockpile_id`
WHERE us.`user_id` = {$_SESSION['userId']} 
GROUP BY SUBSTRING(t.slip_no,1,3)";
            $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
             if($resultContent->num_rows > 0) {
				while($rowContent = $resultContent->fetch_object()) {
                 
					?>
						
			<tr>
            <td style="text-align: center"><?php echo $rowContent->stockpile?></td>
			<td style="text-align: center"><?php echo $rowContent->last_date?></td>
			<td style="text-align: right"><?php echo number_format($rowContent->qty_available, 2, ".", ",");?></td>
			<td style="text-align: right"><?php echo number_format($rowContent->send_weight, 2, ".", ",");?></td>
			<td style="text-align: right"><?php echo number_format($rowContent->netto_weight, 2, ".", ",");?></td>
			<?php
				$total = $total + $rowContent->qty_available;
				$totalSend = $totalSend + $rowContent->send_weight;
				$totalNetto = $totalNetto + $rowContent->netto_weight;
                    } 
			 }
			 
			 ?>
			 <tr>
			 <td colspan=2 style="text-align: center">TOTAL</td>
			 <td style="text-align: right"><?php echo number_format($total, 2, ".", ",");?></td>
			 <td style="text-align: right"><?php echo number_format($totalSend, 2, ".", ",");?></td>
			 <td style="text-align: right"><?php echo number_format($totalNetto, 2, ".", ",");?></td>
			 </tr>
			</tbody>
			</table>
			
            