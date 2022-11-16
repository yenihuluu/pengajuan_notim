<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('ym');

switch ($_POST['action']) {
    case "getShipmentCost":
        getShipmentCost($_POST['stockpileID'], $_POST['newShipmentCode']);
        break;
    case "getQtyValue":
        //getQtyValue($_POST['shipmentCode']);
        getQtyValue($_POST['qty'], $_POST['shipmentCode']);
    break;
}

function getShipmentCost($stockpileID, $newShipmentCode)
{
    global $myDatabase;
    $returnValue = '';
    $unionSql = '';

    if ($newShipmentCode != 0 || $newShipmentCode != '') {
        $unionSql = " UNION SELECT SELECT a.shipment_id, a.shipment_no 
                        FROM shipment a WHERE a.shipment_id = {$newShipmentCode}";
    }

    $sql = "SELECT a.shipment_id, a.shipment_no FROM shipment a
            INNER JOIN sales b ON b.sales_id = a.sales_id
            INNER JOIN stockpile c  ON  c.stockpile_id = b.stockpile_id where c.stockpile_id = {$stockpileID}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->shipment_id . '||' . $row->shipment_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->shipment_id . '||' . $row->shipment_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }

    echo $returnValue;
}

function getQtyValue($qty, $shipmentCode)
{
    global $myDatabase;
    $returnValue = '';

    if($qty == 2){
        $sql = "SELECT send_weight FROM TRANSACTION WHERE shipment_id = {$shipmentCode}";
    }elseif($qty == 3){
        $sql = "SELECT netto_weight FROM TRANSACTION WHERE shipment_id = {$shipmentCode}";
        
    }else{
        $returnValue = 0;
    }
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
    if ($result->num_rows == 1) {
            $row = $result->fetch_object();
            $returnValue = $row->send_weight . '||' . $row->netto_weight;
        }
    echo $returnValue;
}
