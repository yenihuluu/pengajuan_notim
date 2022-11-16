<?php

// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection 
require_once PATH_INCLUDE.DS.'db_init.php';

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');

//request-data.php (folder Tabs)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'request_data'){
//UPDATE & INSERT
    $return_value = '';
    $boolNew = false;

    $requestID = $myDatabase->real_escape_string($_POST['requestID']);
    $req_name = $myDatabase->real_escape_string($_POST['req_name']);
    $req_priority = $myDatabase->real_escape_string($_POST['req_priority']);
    $req_type = $myDatabase->real_escape_string($_POST['req_type']);
    $req_detail = $myDatabase->real_escape_string($_POST['req_detail']);

    if ($requestID == '') {
        $boolNew = true;
    }

    if($req_name != '' && $req_priority != '' && $req_type != ''){
         if ($boolNew) {
              $sql = "SELECT * FROM `trx_pengajuan` WHERE UPPER(nama_pengajuan) = UPPER('{$req_name}')";
          } else {
              $sql = "SELECT * FROM `trx_pengajuan` WHERE UPPER(nama_pengajuan) = UPPER('{$req_name}') AND idtrx_pengajuan <> {$requestID}";
          }
          $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if($result !== false && $result->num_row == 0){
            if($boolNew){
                $sql = "INSERT INTO `trx_pengajuan` (
                    `nama_pengajuan`, `prioritas`, `idmaster_tipe`, `detail`, `idmaster_user`) VALUES ("
                    . "'{$req_name}', '{$req_priority}', '{$req_type}', '{$req_detail}', {$_SESSION['userId']})"; //STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s')
            }else{
                $sql = "UPDATE `trx_pengajuan` SET "
                . "nama_pengajuan = '{$req_name}', "
                . "prioritas = '{$req_priority}', "
                . "idmaster_tipe = '{$req_type}', "
                . "detail = '{$req_detail}' "
                . "WHERE idtrx_pengajuan = {$requestID}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


            if($result != false) {
                if($boolNew) {
                    $requestID = $myDatabase->insert_id;
                }
            $return_value = '|OK|Data request has successfully inserted/updated.|'. $requestID .'|';
            }else {
                $return_value = '|FAIL|Insert/update request failed.||';
            }

        }else{
            $return_value = '|FAIL|Data request already exists.||';
        }
    }else{
        $return_value = '|FAIL|Please fill the required fields.||';
    }
}

//DELETE delete_request
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_request'){
    
    $return_value = '';
    $requestID = $myDatabase->real_escape_string($_POST['requestID']);

    if($requestID != ''){
        $sql = "DELETE FROM `trx_pengajuan` WHERE idtrx_pengajuan = {$requestID}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result !== false){
            $return_value = '|OK|Data Request has successfully deleted.|';
        }else{$return_value = '|FAIL|Delete data Request failed.|';}
    }else{$return_value = '|FAIL|Record not found.|';}
}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'shipment_cost_data'){
    $return_value = '';
    $boolNew = false;
    $exchangeRate1 = '';

    $shipmentCostID = $myDatabase->real_escape_string($_POST['shipmentCostID']);
    $stockpileID = $myDatabase->real_escape_string($_POST['stockpileID']);
    $accountID = $myDatabase->real_escape_string($_POST['accountID']);
    $tipe1 = $myDatabase->real_escape_string($_POST['tipe1']);
    $exchangeRate1 = $myDatabase->real_escape_string($_POST['exchangeRate1']);
    $vendorType = $myDatabase->real_escape_string($_POST['vendorType']);
    $currencyId = $myDatabase->real_escape_string($_POST['currencyId']);
    $priceMT = $myDatabase->real_escape_string($_POST['priceMT']);
    $flatPrice = $myDatabase->real_escape_string($_POST['flatPrice']);
    $amount = $myDatabase->real_escape_string($_POST['amount']);
    $qty = $myDatabase->real_escape_string($_POST['qty']);
    $qtyValue = $myDatabase->real_escape_string($_POST['qtyValue']);
    $shipmentCode = $myDatabase->real_escape_string($_POST['shipmentCode']);

    if ($shipmentCostID == '') {
        $boolNew = true;
    }

    if($exchangeRate1 == ''){ //utk Currency IDR default 1
        $exchangeRate1 = 1 ;
    }

     //Get Vendor
     if (isset($vendorType) && $vendorType == 'Pks') {
        $vendor = $myDatabase->real_escape_string($_POST['vendorNamePks']);
    } elseif (isset($vendorType) && $vendorType == 'General') {
        $vendor = $myDatabase->real_escape_string($_POST['vendorNameGeneral']);
    } elseif (isset($vendorType) && $vendorType == 'Freight') {
        $vendor = $myDatabase->real_escape_string($_POST['vendorNameFreight']);
    } elseif (isset($vendorType) && $vendorType == 'Labor') {
        $vendor = $myDatabase->real_escape_string($_POST['vendorNameLabor']);
    } elseif (isset($vendorType) && $vendorType == 'Handling') {
        $vendor = $myDatabase->real_escape_string($_POST['vendorNameHandling']);
    } elseif (isset($vendorType) && $vendorType == 'PettyCash') {
        $vendor = $myDatabase->real_escape_string($_POST['vendorNamaPettyCash']);
    } else {
        $vendor = 'NULL';
    }

    $vendor = explode("-", $vendor);
    $vendorId = $vendor[0];
    $vendorName = $vendor[1];
    
    if($stockpileID != '' && $accountID != ''  && $currencyId != '' && $vendorType != '' ){
        if($result !== false && $result->num_row == 0){
            
            if($boolNew){
                $sql = "INSERT INTO master_shipmentcost(account_id, tipe, allVendor, vendorName, exchangeRate, currencyID, price_MT, flat_Price, stockpile_id, vendor_Type) 
                VALUES ({$accountID}, '{$tipe1}', {$vendorId}, '{$vendorName}', {$exchangeRate1}, {$currencyId}, {$priceMT}, '{$flatPrice}', {$stockpileID}, '{$vendorType}')";  

          /*  if($result != false) {
                 if($qty == 2 || $qty == 3 || $qty == 1){
                    $ShipmentCost = "SELECT shipmentCost_id FROM `master_shipmentcost` 
                        WHERE shipmentCost_id IN (SELECT MAX(shipmentCost_id) 
                        FROM `master_shipmentcost`)";
                        $resultShipmentCost = $myDatabase->query($ShipmentCost, MYSQLI_STORE_RESULT);
                        if($resultShipmentCost !== false && $resultShipmentCost->num_rows == 1) {
                            $rowShipmentCost = $resultShipmentCost->fetch_object();
                            $IdshipmentCost =  $rowShipmentCost->shipmentCost_id;
                        }
                     if($qty == 2){
                         $sql = "UPDATE master_shipmentcost set send_weight = {$qtyValue} WHERE shipmentCost_id = {$IdshipmentCost};";
                     }else if($qty == 3){
                         $sql = "UPDATE master_shipmentcost set netto_weight = {$qtyValue}  WHERE shipmentCost_id = {$IdshipmentCost};";
                     }else{
                         $sql = "UPDATE master_shipmentcost set qty_others = {$qtyValue}  WHERE shipmentCost_id = {$IdshipmentCost};";
                     }
                     $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                 } 
                }else{
                    $return_value = '|FAIL|Insert/update Shipment Cost failed.||';
                }*/
                 

            }else{
                $sql = "UPDATE master_shipmentcost
                        SET account_id = {$accountID}, 
                            tipe= '{$tipe1}', 
                            allvendor = {$vendorId}, 
                            vendorName = '{$vendorName}', 
                            exchangeRate= {$exchangeRate1}, 
                            currencyID= {$currencyId}, 
                            price_MT= {$priceMT}, 
                            flat_Price= {$flatPrice}, 
                            stockpile_id= {$stockpileID}, 
                            vendor_Type= '{$vendorType}'
                        WHERE shipmentCost_id = {$shipmentCostID};";
                        
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
           

            if($result != false) {
                if($boolNew) {
                    $shipmentCostID = $myDatabase->insert_id;
                }
                $return_value = '|OK|Data Shipment Cost has successfully inserted/updated.|'. $shipmentCostID .'|';
            }else {
            $return_value = '|FAIL|Insert/update Shipment Cost failed.||';
            }
        }
    }else{
        $return_value = '|FAIL|Please fill the required fields.||';
    }
        
} elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_shipment_cost'){
    
    $return_value = '';
    $shipmentCostID = $myDatabase->real_escape_string($_POST['shipmentCostID']);

    if($shipmentCostID != ''){
        $sql = "DELETE FROM `master_shipmentcost` WHERE shipmentCost_id = {$shipmentCostID}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result !== false){
            $return_value = '|OK|Data Request has successfully deleted.|';
        }else{$return_value = '|FAIL|Delete data Request failed.|';}
    }else{$return_value = '|FAIL|Record not found.|';}
}

echo $return_value;


