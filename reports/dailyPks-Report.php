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
//$whereSalesProperty = '';
//$whereAvailableProperty = '';
//$stockpileId = '';
$periodTo = '';
/*
if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
}*/

if(isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereBalanceProperty .= " AND a.unloading_date < STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereDeliveriesProperty .= " AND t.unloading_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
	$whereShipmentProperty .=  " AND d.delivery_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    $whereLessProperty .= " AND t.unloading_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
    
    //$whereSalesProperty .= " AND sl.shipment_date <= DATE_SUB(STR_TO_DATE('{$periodTo}', '%d/%m/%Y'), INTERVAL 1 DAY) ";
}

?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/dailyPks-report.php', {
                    periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>
<form method="post" action="reports/dailyPks-report-xls.php">
    
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th style="text-align:center;">Description</th>
            <?php
            $sqlHead = "SELECT s.stockpile_name
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultHead = $myDatabase->query($sqlHead, MYSQLI_STORE_RESULT);
            if($resultHead->num_rows > 0) {
                while($rowHead = $resultHead->fetch_object()) {
                    echo '<th>'. strtoupper($rowHead->stockpile_name) .'</th>';
                }
            }
            ?>
            <th>Total</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Balance Previous Report</td>
           <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(a.quantity), 0) AS quantity, COALESCE(SUM(a.shrink), 0) AS shrink
                                    FROM (
                                        SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.quantity END AS quantity,
											CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END AS shrink,
                                            CASE WHEN t.transaction_type = 1 THEN t.unloading_date ELSE t.transaction_date END AS unloading_date
                                        FROM `transaction` t
                                        LEFT JOIN stockpile_contract sc
                                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                                        LEFT JOIN shipment sh
                                            ON sh.shipment_id = t.shipment_id
                                        LEFT JOIN sales sl
                                            ON sl.sales_id = sh.sales_id    
                                        WHERE 1=1 
                                        AND (sc.stockpile_id = {$rowBody->stockpile_id} OR sl.stockpile_id = {$rowBody->stockpile_id})
                                        AND t.company_id = {$_SESSION['companyId']}
                                    ) a
                                    WHERE 1=1 {$whereBalanceProperty}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty = $rowContent->quantity - $rowContent->shrink;
                        echo '<td style="text-align: right;">'. number_format($qty, 0, ".", ",") .'</td>';
                        $total = $total + $qty;
                    } else {
                        echo '<td style="text-align: right;">0</td>';
                    }
                }
            }
            echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td>';
			echo '<td></td></tr>';
            ?>
           
        </tr>
        <tr>
            <td>Plus : Deliveries</td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(t.quantity), 0) AS quantity
                                    FROM `transaction` t
                                    INNER JOIN stockpile_contract sc
                                        ON sc.stockpile_contract_id = t.stockpile_contract_id
                                    WHERE 1=1 {$whereDeliveriesProperty}
                                    AND t.transaction_type = 1
                                    AND t.company_id = {$_SESSION['companyId']}
                                    AND sc.stockpile_id = {$rowBody->stockpile_id}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_plus = $rowContent->quantity;
                        echo '<td style="text-align: right;">'. number_format($rowContent->quantity, 0, ".", ",") .'</td>';
                        $total = $total + $rowContent->quantity;
                    } else {
                        echo '<td style="text-align: right;">0</td>';
                    }
                }
            }
            echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Less : Shipments</td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                     $sqlContent = "SELECT less_shipment
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						
						echo '<td style="text-align: right;">('. number_format($rowContent->less_shipment, 0, ".", ",") .')</td>';
						$total = $total + $rowContent->less_shipment;
					}else{
                        echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
            echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Less : Local Sales</td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                     $sqlContent = "SELECT less_local
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						
						echo '<td style="text-align: right;">('. number_format($rowContent->less_local, 0, ".", ",") .')</td>';
						$total = $total + $rowContent->less_local;
					}else{
                        echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
            echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Less : Susut (Moisture Loss)</td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                     $sqlContent = "SELECT less_susut
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						
						echo '<td style="text-align: right;">('. number_format($rowContent->less_susut, 0, ".", ",") .')</td>';
						$total = $total + $rowContent->less_susut;
					}else{
                        echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
            echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>+/- Other Adjustment</td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;">(0)</td>';
                    
                }
            }
            echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td style="text-align:center;"><b>Total</b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(SUM(a.quantity), 0) AS quantity, COALESCE(SUM(a.shrink), 0) AS shrink
                                    FROM (
                                        SELECT CASE WHEN t.transaction_type = 1 THEN t.quantity ELSE -1 * t.quantity END AS quantity,
											CASE WHEN t.transaction_type = 2 THEN t.shrink ELSE 0 END AS shrink,
                                            CASE WHEN t.transaction_type = 1 THEN t.unloading_date ELSE t.transaction_date END AS unloading_date
                                        FROM `transaction` t
                                        LEFT JOIN stockpile_contract sc
                                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                                        LEFT JOIN shipment sh
                                            ON sh.shipment_id = t.shipment_id
                                        LEFT JOIN sales sl
                                            ON sl.sales_id = sh.sales_id    
                                        WHERE 1=1 
                                        AND (sc.stockpile_id = {$rowBody->stockpile_id} OR sl.stockpile_id = {$rowBody->stockpile_id})
                                        AND t.company_id = {$_SESSION['companyId']}
                                    ) a
                                    WHERE 1=1 {$whereBalanceProperty}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty = $rowContent->quantity - $rowContent->shrink;
					}else{
						$qty = 0;
					}
					
					 $sqlContent = "SELECT COALESCE(SUM(t.quantity), 0) AS quantity
                                    FROM `transaction` t
                                    INNER JOIN stockpile_contract sc
                                        ON sc.stockpile_contract_id = t.stockpile_contract_id
                                    WHERE 1=1 {$whereDeliveriesProperty}
                                    AND t.transaction_type = 1
                                    AND t.company_id = {$_SESSION['companyId']}
                                    AND sc.stockpile_id = {$rowBody->stockpile_id}";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_plus = $rowContent->quantity;
					}else{
						$qty_plus = 0;
					}
					
					 $sqlContent = "SELECT (less_shipment + less_local + less_susut) AS stock_less
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_less = $rowContent->stock_less;
					}else{
						$stock_less = 0;
					}
					
					$qty_total = ($qty + $qty_plus) - $stock_less;
					if($qty_total < 0){
						$qty_total1 = $qty_total * -1;
					echo '<td style="text-align: right;"><b>('. number_format($qty_total1, 0, ".", ",") .')</b></td>';
					}else{
					echo '<td style="text-align: right;"><b>'. number_format($qty_total, 0, ".", ",") .'</b></td>';
					}
					$total = $total + $qty_total;
                }
			
            }
				if($total < 0){
					$total1 = $total * -1;
           	 echo '<td style="text-align: right;"><b>'. number_format($total1, 0, ".", ",") .'</b></td>';
				}else{
			 echo '<td style="text-align: right;"><b>'. number_format($total, 0, ".", ",") .'</b></td>';
				}
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Screened Stock</td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT screened_stock
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						
						echo '<td style="text-align: right;">'. number_format($rowContent->screened_stock, 0, ".", ",") .'</td>';
						$total = $total + $rowContent->screened_stock;
					}else{
                        echo '<td style="text-align: right;">0</td>';
					}
                }
            }
            echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Screened + Sprayed Stock</td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT sprayed_stock
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						
						echo '<td style="text-align: right;">'. number_format($rowContent->sprayed_stock, 0, ".", ",") .'</td>';
						$total = $total + $rowContent->sprayed_stock;
					}else{
                        echo '<td style="text-align: right;">0</td>';
					}
                }
            }
            echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Unscreened Stock</td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT unscreened_stock
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						
						echo '<td style="text-align: right;">'. number_format($rowContent->unscreened_stock, 0, ".", ",") .'</td>';
						$total = $total + $rowContent->unscreened_stock;
					}else{
                        echo '<td style="text-align: right;">0</td>';
					}
                }
            }
            echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td style="text-align:center;"><b>Total Inventory Available</b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT (screened_stock + sprayed_stock + unscreened_stock) AS stock_total
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_total = $rowContent->stock_total;
						echo '<td style="text-align: right;"><b>'. number_format($stock_total, 0, ".", ",") .'</b></td>';
						 $total = $total + $stock_total;
					} else{
						echo '<td style="text-align: right;"><b>0</b></td>';
					}
                }
            }
           	echo '<td style="text-align: right;">'. number_format($total, 0, ".", ",") .'</td>';
			echo '<td></td></tr>';
            ?>
        </tr>
           
        <tr>
            <td>Committed Shipment for January <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-01' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for February <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-02' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for March <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-03' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for April <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-04' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for May <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-05' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for June <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-06' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for July <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-07' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for August <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-08' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for September <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-09' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for October <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-10' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for November <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-11' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td>Committed Shipment for December <?php echo $currentYear?></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-12' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
						echo '<td style="text-align: right;">('. number_format($qty_sales, 0, ".", ",") .')</td>';
						 $total = $total + $qty_sales;
					} else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                }
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 echo '<td></td></tr>';
            ?>
        </tr>
        <tr>
            <td style="text-align:center;"><b>Available Inventory</b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                     $sqlContent = "SELECT (screened_stock + sprayed_stock + unscreened_stock) AS stock_total
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_total = $rowContent->stock_total;
					}else{
						$stock_total = 0;
					}
					
					$sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y') = '{$currentYear}' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
					}else{
						$qty_sales = 0;
					}
					
					$stock_available = $stock_total - $qty_sales;
					
					if($stock_available < 0){
						$stock_available1 = $stock_available * -1;
					echo '<td style="text-align: right;"><b>('. number_format($stock_available1, 0, ".", ",") .')</b></td>';
					}else{
					echo '<td style="text-align: right;"><b>'. number_format($stock_available, 0, ".", ",") .'</b></td>';
					}
					$total = $total + $stock_available;
                }
			
            }
				if($total < 0){
					$total1 = $total * -1;
           	 		echo '<td style="text-align: right;"><b>('. number_format($total1, 0, ".", ",") .')</b></td>';
				}else{
					echo '<td style="text-align: right;"><b>'. number_format($total, 0, ".", ",") .'</b></td>';
				}
			 echo '<td></td></tr>';
            ?>
        </tr>
         <tr>
            <td style="text-align:center;"><b>COMMITTED SHIPMENTS</b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
			
            ?>
        </tr>
        <tr>
          <td><b>January <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-01'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes1 = $row->notes;
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                     $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-01'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes1; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>February <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
       $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-02'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes2 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-02'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes2; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>March <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-03'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes3 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-03'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes3; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>April <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
       $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-04'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes4 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-04'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes4; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>May <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-05'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes5 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-05'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes5; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>June <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-06'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes6 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-06'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes6; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>July <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
       $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-07' ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes7 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-07'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes7; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>August <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-08'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes8 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                   $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-08'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes8; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>September <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-09'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes9 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-09'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>October <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-10'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes1o = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-10'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes10; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>November <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-11'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes11 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-11'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes11; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
          <td><b>December <?php echo $currentYear?></b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    
                        echo '<td style="text-align: right;"></td>';
                    
                }
            }
            echo '<td style="text-align: right;"></td>';
			echo '<td style="text-align: right;"></td></tr>';
            ?>
        </tr>
        <?php
        $sql = "SELECT sl.destination, sa.notes FROM sales sl LEFT JOIN sales_add sa ON sa.sales_id = sl.sales_id WHERE DATE_FORMAT(sl.shipment_date, '%Y-%m') = '{$currentYear}-12'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result->num_rows > 0) {
            while($row = $result->fetch_object()) {
               		$destination = $row->destination;
                	$notes12 = $row->notes;
                
                ?>
        <tr>
            <td><?php echo $row->destination; ?></td>
             <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                    $sqlContent = "SELECT COALESCE(quantity,0) AS qty, sales_status FROM sales WHERE destination = '{$destination}' AND stockpile_id = {$rowBody->stockpile_id} AND DATE_FORMAT(shipment_date, '%Y-%m') = '{$currentYear}-12'";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows > 0) {
                        while($rowContent = $resultContent->fetch_object()) {
						$qty = $rowContent->qty;
						$sales_status = $rowContent->sales_status;
						
						} 
						if($qty != 0 && $sales_status == 0){	
							echo '<td style="text-align: right;">('. number_format($qty, 0, ".", ",") .')</td>';
							$total = $total + $qty;
						}elseif($qty != 0 && $sales_status == 1) {
							echo '<td style="text-align: center;">DONE</td>';
							
						}
					}else{
						echo '<td style="text-align: right;">(0)</td>';
					}
                
				}
            }
           	 echo '<td style="text-align: right;">('. number_format($total, 0, ".", ",") .')</td>';
			 //echo '<td></td></tr>';
			 ?>
             <td><?php echo $notes12; ?></td>
        </tr> 
                <?php
            }
        }
        ?>
        <tr>
            <td style="text-align:center;"><b>Balance</b></td>
            <?php
            $total = 0;
            $sqlBody = "SELECT s.stockpile_id
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_id ASC";
            $resultBody = $myDatabase->query($sqlBody, MYSQLI_STORE_RESULT);
            if($resultBody->num_rows > 0) {
                while($rowBody = $resultBody->fetch_object()) {
                     $sqlContent = "SELECT (screened_stock + sprayed_stock + unscreened_stock) AS stock_total
								   FROM sales_add WHERE stockpile_id = {$rowBody->stockpile_id} AND entry_date = STR_TO_DATE('{$periodTo}', '%d/%m/%Y')";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$stock_total = $rowContent->stock_total;
					}else{
						$stock_total = 0;
					}
					
					$sqlContent = "SELECT SUM(quantity) AS qty_sales FROM sales WHERE DATE_FORMAT(shipment_date, '%Y') = '{$currentYear}' AND stockpile_id = {$rowBody->stockpile_id} AND sales_status = 0";
                    $resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
                    if($resultContent->num_rows == 1) {
                        $rowContent = $resultContent->fetch_object();
						$qty_sales = $rowContent->qty_sales;
					}else{
						$qty_sales = 0;
					}
					
					$stock_available = $stock_total - $qty_sales;
					if($stock_available < 0){
						$stock_available1 = $stock_available * -1;
					echo '<td style="text-align: right;"><b>('. number_format($stock_available1, 0, ".", ",") .')</b></td>';
					}else{
					echo '<td style="text-align: right;"><b>'. number_format($stock_available, 0, ".", ",") .'</b></td>';
					}
					$total = $total + $stock_available;
                }
			
            }
				if($total < 0){
					$total1 = $total * -1;
           	 		echo '<td style="text-align: right;"><b>('. number_format($total1, 0, ".", ",") .')</b></td>';
				}else{
					echo '<td style="text-align: right;"><b>'. number_format($total, 0, ".", ",") .'</b></td>';
				}
			 echo '<td></td></tr>';
            ?>
            
        </tr>
    </tbody>
</table>

