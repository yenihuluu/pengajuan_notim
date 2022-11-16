<?php

require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection local
require_once PATH_INCLUDE.DS.'db_init.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init_server.php';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$currentYearMonth = $date->format('ym');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('y');


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
    // <editor-fold defaultstate="collapsed" desc="download">
    
    $return_value = '';
    $syncMessage = '';
    $addMessage = '';
    
    // <editor-fold defaultstate="collapsed" desc="module">
    
    $sqlServer = "SELECT * FROM module";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {    
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM module WHERE module_id = {$rowServer->module_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO module VALUES ({$rowServer->module_id}, '{$rowServer->module_name}', '{$rowServer->module_description}', "
                        . "{$rowServer->active}, {$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE module SET "
                        . "module_name = '{$rowServer->module_name}', "
                        . "module_description = '{$rowServer->module_description}', "
                        . "active = {$rowServer->active}, "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE module_id = {$rowServer->module_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM module WHERE module_id = {$rowLocal->module_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
//                $sql = "DELETE FROM user_module WHERE module_id = {$rowLocal->module_id}";
//                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                $sql = "DELETE FROM module WHERE module_id = {$rowLocal->module_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(module_id), 0) AS next_id FROM `module`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `module` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'Module: '. $addMessage .'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="stockpile">
    
    $sqlServer = "SELECT * FROM stockpile";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$rowServer->stockpile_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO stockpile VALUES ({$rowServer->stockpile_id}, '{$rowServer->stockpile_code}', '{$rowServer->stockpile_name}', "
                        . "'{$rowServer->stockpile_address}', {$rowServer->freight_weight_rule}, {$rowServer->active}, {$rowServer->entry_by}, "
                        . "STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE stockpile SET "
                        . "stockpile_code = '{$rowServer->stockpile_code}', "
                        . "stockpile_name = '{$rowServer->stockpile_name}', "
                        . "stockpile_address = '{$rowServer->stockpile_address}', "
                        . "freight_weight_rule = {$rowServer->freight_weight_rule}, "
                        . "active = {$rowServer->active}, "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE stockpile_id = {$rowServer->stockpile_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM stockpile WHERE stockpile_id = {$rowLocal->stockpile_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
//                $sql = "DELETE FROM user_stockpile WHERE stockpile_id = {$rowLocal->stockpile_id}";
//                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                $sql = "DELETE FROM stockpile WHERE stockpile_id = {$rowLocal->stockpile_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(stockpile_id), 0) AS next_id FROM `stockpile`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `stockpile` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'Stockpile: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="user">
    
    $sqlServer = "SELECT * FROM `user`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `user` WHERE user_id = {$rowServer->user_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `user` VALUES ({$rowServer->user_id}, '{$rowServer->user_email}', '{$rowServer->user_password}', "
                        . "'{$rowServer->user_name}', '{$rowServer->user_phone}', {$rowServer->active}, '{$rowServer->user_salt}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `user` SET "
                        . "user_email = '{$rowServer->user_email}', "
                        . "user_password = '{$rowServer->user_password}', "
                        . "user_name = '{$rowServer->user_name}', "
                        . "user_phone = '{$rowServer->user_phone}', "
                        . "active = {$rowServer->active}, "
                        . "user_salt = '{$rowServer->user_salt}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE user_id = {$rowServer->user_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `user` WHERE user_id = {$rowLocal->user_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `user` WHERE user_id = {$rowLocal->user_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(user_id), 0) AS next_id FROM `user`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `user` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'User: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="account">
    
    $sqlServer = "SELECT * FROM `account`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `account` WHERE account_id = {$rowServer->account_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `account` VALUES ({$rowServer->account_id}, {$rowServer->account_type}, '{$rowServer->account_no}', "
                        . "'{$rowServer->account_name}', {$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `account` SET "
                        . "account_type = {$rowServer->account_type}, "
                        . "account_no = '{$rowServer->account_no}', "
                        . "account_name = '{$rowServer->account_name}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE account_id = {$rowServer->account_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `account` WHERE account_id = {$rowLocal->account_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `account` WHERE account_id = {$rowLocal->account_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(account_id), 0) AS next_id FROM `account`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `account` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'Account: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="bank">
    
    $sqlServer = "SELECT * FROM `bank`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `bank` WHERE bank_id = {$rowServer->bank_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `bank` VALUES ({$rowServer->bank_id}, '{$rowServer->bank_name}', '{$rowServer->bank_account_no}', "
                        . "'{$rowServer->bank_account_name}', {$rowServer->currency_id}, {$rowServer->opening_balance}, "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `bank` SET "
                        . "bank_name = '{$rowServer->bank_name}', "
                        . "bank_account_no = '{$rowServer->bank_account_no}', "
                        . "bank_account_name = '{$rowServer->bank_account_name}', "
                        . "currency_id = {$rowServer->currency_id}, "
                        . "opening_balance = {$rowServer->opening_balance}, "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE bank_id = {$rowServer->bank_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `bank` WHERE bank_id = {$rowLocal->bank_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `bank` WHERE bank_id = {$rowLocal->bank_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) { 
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(bank_id), 0) AS next_id FROM `bank`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `bank` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'Bank: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="category">
    
    $sqlServer = "SELECT * FROM `category`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `category` WHERE category_id = {$rowServer->category_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `category` VALUES ({$rowServer->category_id}, '{$rowServer->category_name}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `category` SET "
                        . "category_name = '{$rowServer->category_name}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE category_id = {$rowServer->category_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `category` WHERE category_id = {$rowLocal->category_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `category` WHERE category_id = {$rowLocal->category_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(category_id), 0) AS next_id FROM `category`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `category` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'Category: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="currency">
    
    $sqlServer = "SELECT * FROM `currency`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `currency` WHERE currency_id = {$rowServer->currency_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `currency` VALUES ({$rowServer->currency_id}, '{$rowServer->currency_code}', "
                        . "'{$rowServer->currency_name}', {$rowServer->is_country_currency}, {$rowServer->is_purchase_currency}, "
                        . "{$rowServer->is_sales_currency}, {$rowServer->is_report_currency}, "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `currency` SET "
                        . "currency_code = '{$rowServer->currency_code}', "
                        . "currency_name = '{$rowServer->currency_name}', "
                        . "is_country_currency = {$rowServer->is_country_currency}, "
                        . "is_purchase_currency = {$rowServer->is_purchase_currency}, "
                        . "is_sales_currency = {$rowServer->is_sales_currency}, "
                        . "is_report_currency = {$rowServer->is_report_currency}, "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE currency_id = {$rowServer->currency_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `currency` WHERE currency_id = {$rowLocal->currency_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `currency` WHERE currency_id = {$rowLocal->currency_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(currency_id), 0) AS next_id FROM `currency`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `currency` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'Currency: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="customer">
    
    $sqlServer = "SELECT * FROM `customer`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `customer` WHERE customer_id = {$rowServer->customer_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `customer` VALUES ({$rowServer->customer_id}, '{$rowServer->customer_name}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `customer` SET "
                        . "customer_name = '{$rowServer->customer_name}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE customer_id = {$rowServer->customer_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `customer` WHERE customer_id = {$rowLocal->customer_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `customer` WHERE customer_id = {$rowLocal->customer_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $warnCount = $warnCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(customer_id), 0) AS next_id FROM `customer`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `customer` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Syn failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Customer: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="freight">
    
    $sqlServer = "SELECT * FROM `freight`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `freight` WHERE freight_id = {$rowServer->freight_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `freight` VALUES ({$rowServer->freight_id}, '{$rowServer->freight_code}', '{$rowServer->freight_supplier}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `freight` SET "
                        . "freight_code = '{$rowServer->freight_code}', "
                        . "freight_supplier = '{$rowServer->freight_supplier}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE freight_id = {$rowServer->freight_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `freight` WHERE freight_id = {$rowLocal->freight_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `freight` WHERE freight_id = {$rowLocal->freight_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $warnCount = $warnCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(freight_id), 0)  AS next_id FROM `freight`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `freight` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Freight: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="labor">
    
    $sqlServer = "SELECT * FROM `labor`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `labor` WHERE labor_id = {$rowServer->labor_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `labor` VALUES ({$rowServer->labor_id}, '{$rowServer->labor_name}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `labor` SET "
                        . "labor_name = '{$rowServer->labor_name}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE labor_id = {$rowServer->labor_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `labor` WHERE labor_id = {$rowLocal->labor_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `labor` WHERE labor_id = {$rowLocal->labor_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $warnCount = $warnCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(labor_id), 0) AS next_id FROM `labor`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `labor` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Labor: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="vehicle">
    
    $sqlServer = "SELECT * FROM `vehicle`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `vehicle` WHERE vehicle_id = {$rowServer->vehicle_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `vehicle` VALUES ({$rowServer->vehicle_id}, '{$rowServer->vehicle_name}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `vehicle` SET "
                        . "vehicle_name = '{$rowServer->vehicle_name}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE vehicle_id = {$rowServer->vehicle_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `vehicle` WHERE vehicle_id = {$rowLocal->vehicle_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `vehicle` WHERE vehicle_id = {$rowLocal->vehicle_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $warnCount = $warnCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(vehicle_id), 0) AS next_id FROM `vehicle`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `vehicle` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Vehicle: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="vendor">
    
    $sqlServer = "SELECT * FROM `vendor`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `vendor` WHERE vendor_id = {$rowServer->vendor_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `vendor` VALUES ({$rowServer->vendor_id}, '{$rowServer->vendor_code}', '{$rowServer->vendor_name}', "
                        . "'{$rowServer->vendor_address}', "
                        . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                        . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            } else {
                $sql = "UPDATE `vendor` SET "
                        . "vendor_code = '{$rowServer->vendor_code}', "
                        . "vendor_name = '{$rowServer->vendor_name}', "
                        . "vendor_address = '{$rowServer->vendor_address}', "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE vendor_id = {$rowServer->vendor_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `vendor` WHERE vendor_id = {$rowLocal->vendor_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `vendor` WHERE vendor_id = {$rowLocal->vendor_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $warnCount = $warnCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(vendor_id), 0) AS next_id FROM `vendor`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `vendor` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Vendor: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="user_module">
    
    $sqlServer = "SELECT * FROM `user_module`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `user_module` WHERE user_module_id = {$rowServer->user_module_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `user_module` VALUES ({$rowServer->user_module_id}, {$rowServer->user_id}, {$rowServer->module_id})";
            } else {
                $sql = "UPDATE `user_module` SET "
                        . "user_id = '{$rowServer->user_id}', "
                        . "module_id = '{$rowServer->module_id}' "
                        . "WHERE user_module_id = {$rowServer->user_module_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `user_module` WHERE user_module_id = {$rowLocal->user_module_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `user_module` WHERE user_module_id = {$rowLocal->user_module_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(user_module_id), 0) AS next_id FROM `user_module`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `user_module` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'User Module: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="user_stockpile">
    
    $sqlServer = "SELECT * FROM `user_stockpile`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    
    if($resultServer->num_rows >= $resultLocal->num_rows) {
        while($rowServer = $resultServer->fetch_object()) {
            $sql = "SELECT * FROM `user_stockpile` WHERE user_stockpile_id = {$rowServer->user_stockpile_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `user_stockpile` VALUES ({$rowServer->user_stockpile_id}, {$rowServer->user_id}, {$rowServer->stockpile_id})";
            } else {
                $sql = "UPDATE `user_stockpile` SET "
                        . "user_id = '{$rowServer->user_id}', "
                        . "stockpile_id = '{$rowServer->stockpile_id}' "
                        . "WHERE user_stockpile_id = {$rowServer->user_stockpile_id}";
            }
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            
            if($result !== false) {
                $syncCount = $syncCount + 1;
            } else {
                $failCount = $failCount + 1;
            }
        }
    } else {
        while($rowLocal = $resultLocal->fetch_object()) {
            $sql = "SELECT * FROM `user_stockpile` WHERE user_stockpile_id = {$rowLocal->user_stockpile_id}";
            $resultServer = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
            if($resultServer->num_rows == 0) {
                $sql = "DELETE FROM `user_stockpile` WHERE user_stockpile_id = {$rowLocal->user_stockpile_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result === false) {
                    $failCount = $failCount + 1;
                }
            } else {
                $syncCount = $syncCount + 1;
            }
        }
    }
    
    $sqlNext = "SELECT COALESCE(MAX(user_stockpile_id), 0) AS next_id FROM `user_stockpile`";
    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
    $rowNext = $resultNext->fetch_object();
    $nextId = $rowNext->next_id;
    $nextAuto = $nextId + 1;
    
    $sql = "alter table `user_stockpile` auto_increment = {$nextAuto}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    }
    
    if(($syncCount == $totalServer) && $failCount > 0) {
        $addMessage .= " with {$failCount} warning";
    } elseif(($syncCount != $totalServer) && $failCount > 0) {
        $addMessage = "Sync failed with {$failCount} failure";
    }
    
    $syncMessage .= 'User Stockpile: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="contract">
    
    $sqlServer = "SELECT * FROM `contract` WHERE vendor_id IS NOT NULL";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $contractIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($contractIds == "") {
                $contractIds = $rowServer->contract_id;
            } else {
                $contractIds .= ",". $rowServer->contract_id;
            }
            
            $boolContinue = true;
            
            $sql = "SELECT c.* "
                    . "FROM `contract` c "
                    . "WHERE 1=1 "
//                    . "AND c.contract_id = {$rowServer->contract_id} "
                    . "AND c.contract_type = '{$rowServer->contract_type}' AND c.po_no = '{$rowServer->po_no}' "
                    . "AND c.contract_no = '{$rowServer->contract_no}' AND c.vendor_id = {$rowServer->vendor_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `contract` WHERE contract_id = {$rowServer->contract_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `contract` (contract_id, contract_type, po_no, contract_no, vendor_id, currency_id, exchange_rate, "
                            . "price, price_converted, quantity, payment_status, entry_by, entry_date, sync_by, sync_date) VALUES ({$rowServer->contract_id}, "
                            . "'{$rowServer->contract_type}', '{$rowServer->po_no}', '{$rowServer->contract_no}', {$rowServer->vendor_id}, "
                            . "{$rowServer->currency_id}, {$rowServer->exchange_rate}, {$rowServer->price}, {$rowServer->price_converted}, "
                            . "{$rowServer->quantity}, {$rowServer->payment_status}, "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                            . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) { 
                        $syncCount = $syncCount + 1;
                    }  else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $sqlNext = "SELECT COALESCE(MAX(contract_id), 0) + 1 AS next_id FROM `contract`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `contract` SET contract_id = {$nextId} WHERE contract_id = {$rowServer->contract_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `contract` (contract_id, contract_type, po_no, contract_no, vendor_id, currency_id, exchange_rate, "
                                . "price, price_converted, quantity, payment_status, entry_by, entry_date, sync_by, sync_date) VALUES ({$rowServer->contract_id}, "
                                . "'{$rowServer->contract_type}', '{$rowServer->po_no}', '{$rowServer->contract_no}', {$rowServer->vendor_id}, "
                                . "{$rowServer->currency_id}, {$rowServer->exchange_rate}, {$rowServer->price}, {$rowServer->price_converted}, "
                                . "{$rowServer->quantity}, {$rowServer->payment_status}, "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'), "
                                . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        
                        if($result !== false) { 
                            $syncCount = $syncCount + 1;
                        }  else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `contract` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $row = $result->fetch_object();
                
                $sql = "UPDATE `contract` SET "
                        . "contract_id = {$rowServer->contract_id}, "
                        . "currency_id = {$rowServer->currency_id}, "
                        . "exchange_rate = {$rowServer->exchange_rate}, "
                        . "price = {$rowServer->price}, "
                        . "price_converted = {$rowServer->price_converted}, "
                        . "quantity = {$rowServer->quantity}, "
                        . "payment_status = {$rowServer->payment_status}, "
                        . "sync_by = {$_SESSION['userId']}, "
                        . "sync_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE contract_id = {$row->contract_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($contractIds != "") {
//        $sql = "DELETE FROM stockpile_contract WHERE contract_id IN ({$contractIds})";
//        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//        
//        if($result === false) {
//            $warnCount = $warnCount + 1;
//        }
//        
        $sql = "DELETE FROM `contract` WHERE vendor_id IS NOT NULL AND contract_id  NOT IN ({$contractIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(contract_id), 0) AS next_id FROM `contract`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `contract` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Contract: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="stockpile contract">
    
    $sqlServer = "SELECT * FROM `stockpile_contract`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $resultLocal = $myDatabase->query($sqlServer, MYSQLI_STORE_RESULT);
    
    $totalServer = $resultServer->num_rows;
    $totalLocal = $resultLocal->num_rows;
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $stockpileContractIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($stockpileContractIds == "") {
                $stockpileContractIds = $rowServer->stockpile_contract_id;
            } else {
                $stockpileContractIds .= ",". $rowServer->stockpile_contract_id;
            }
            
            $sql = "SELECT * "
                    . "FROM `stockpile_contract` "
                    . "WHERE 1=1 "
                    . "AND stockpile_contract_id = {$rowServer->stockpile_contract_id} AND stockpile_id = {$rowServer->stockpile_id} "
                    . "AND contract_id = {$rowServer->contract_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `stockpile_contract` WHERE stockpile_contract_id = {$rowServer->stockpile_contract_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `stockpile_contract` (stockpile_contract_id, stockpile_id, contract_id, quantity, "
                            . "entry_by, entry_date) VALUES ({$rowServer->stockpile_contract_id}, {$rowServer->stockpile_id}, "
                            . "{$rowServer->contract_id}, {$rowServer->quantity}, "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) { 
                        $syncCount = $syncCount + 1;
                    }  else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $sqlNext = "SELECT COALESCE(MAX(stockpile_contract_id), 0) + 1 AS next_id FROM `stockpile_contract`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `stockpile_contract` SET stockpile_contract_id = {$nextId} WHERE stockpile_contract_id = {$rowServer->stockpile_contract_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `stockpile_contract` (stockpile_contract_id, stockpile_id, contract_id, quantity, "
                                . "entry_by, entry_date) VALUES ({$rowServer->stockpile_contract_id}, {$rowServer->stockpile_id}, "
                                . "{$rowServer->contract_id}, {$rowServer->quantity}, "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        
                        if($result !== false) { 
                            $syncCount = $syncCount + 1;
                        }  else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `stockpile_contract` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $sql = "UPDATE `stockpile_contract` SET "
                        . "quantity = {$rowServer->quantity} "
                        . "WHERE stockpile_contract_id = {$rowServer->stockpile_contract_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($stockpileContractIds != "") {
        $sql = "DELETE FROM `stockpile_contract` WHERE stockpile_contract_id  NOT IN ({$stockpileContractIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(stockpile_contract_id), 0) AS next_id FROM `stockpile_contract`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `stockpile_contract` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Stockpile Contract: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="freight cost">
    
    $sqlServer = "SELECT * FROM `freight_cost`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $totalServer = $resultServer->num_rows;
    
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $freightCostIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($freightCostIds == "") {
                $freightCostIds = $rowServer->freight_cost_id;
            } else {
                $freightCostIds .= ",". $rowServer->freight_cost_id;
            }
            
            $sql = "SELECT * FROM `freight_cost` "
                    . "WHERE freight_cost_id = {$rowServer->freight_cost_id} AND freight_id = {$rowServer->freight_id} "
                    . "AND stockpile_id = {$rowServer->stockpile_id} AND vendor_id = {$rowServer->vendor_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `freight_cost` WHERE freight_cost_id = {$rowServer->freight_cost_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `freight_cost` (freight_cost_id, freight_id, stockpile_id, vendor_id, currency_id, exchange_rate, "
                            . "price, price_converted, payment_notes, remarks, entry_by, entry_date) VALUES ({$rowServer->freight_cost_id}, "
                            . "{$rowServer->freight_id}, {$rowServer->stockpile_id}, {$rowServer->vendor_id}, {$rowServer->currency_id}, "
                            . "{$rowServer->exchange_rate}, {$rowServer->price}, {$rowServer->price_converted}, "
                            . "'{$rowServer->payment_notes}', '{$rowServer->remarks}', "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $sqlNext = "SELECT COALESCE(MAX(freight_cost_id), 0) + 1 AS next_id FROM `freight_cost`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `freight_cost` SET freight_cost_id = {$nextId} WHERE freight_cost_id = {$rowServer->freight_cost_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `freight_cost` (freight_cost_id, freight_id, stockpile_id, vendor_id, currency_id, exchange_rate, "
                                . "price, price_converted, payment_notes, remarks, entry_by, entry_date) VALUES ({$rowServer->freight_cost_id}, "
                                . "{$rowServer->freight_id}, {$rowServer->stockpile_id}, {$rowServer->vendor_id}, {$rowServer->currency_id}, "
                                . "{$rowServer->exchange_rate}, {$rowServer->price}, {$rowServer->price_converted}, "
                                . "'{$rowServer->payment_notes}', '{$rowServer->remarks}', "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        
                        if($result !== false) {
                            $syncCount = $syncCount + 1;
                        } else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `freight_cost` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $sql = "UPDATE `freight_cost` SET "
                        . "currency_id = {$rowServer->currency_id}, "
                        . "exchange_rate = {$rowServer->exchange_rate}, "
                        . "price = {$rowServer->price}, "
                        . "price_converted = {$rowServer->price_converted}, "
                        . "payment_notes = '{$rowServer->payment_notes}', "
                        . "remarks = '{$rowServer->remarks}', "
                        . "modify_by = {$_SESSION['userId']}, "
                        . "modify_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE freight_cost_id = {$rowServer->freight_cost_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($freightCostIds != "") {
        $sql = "DELETE FROM freight_cost WHERE freight_cost_id NOT IN ({$freightCostIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(freight_cost_id), 0) AS next_id FROM `freight_cost`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `freight_cost` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Freight Cost: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="unloading cost">
    
    $sqlServer = "SELECT * FROM `unloading_cost`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $totalServer = $resultServer->num_rows;
    
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $unloadingCostIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($unloadingCostIds == "") {
                $unloadingCostIds = $rowServer->unloading_cost_id;
            } else {
                $unloadingCostIds .= ",". $rowServer->unloading_cost_id;
            }
            
            $sql = "SELECT * FROM `unloading_cost` "
                    . "WHERE unloading_cost_id = {$rowServer->unloading_cost_id} AND vehicle_id = {$rowServer->vehicle_id} "
                    . "AND stockpile_id = {$rowServer->stockpile_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `unloading_cost` WHERE unloading_cost_id = {$rowServer->unloading_cost_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `unloading_cost` (unloading_cost_id, vehicle_id, stockpile_id, currency_id, exchange_rate, "
                            . "price, price_converted, entry_by, entry_date) VALUES ({$rowServer->unloading_cost_id}, "
                            . "{$rowServer->vehicle_id}, {$rowServer->stockpile_id}, {$rowServer->currency_id}, "
                            . "{$rowServer->exchange_rate}, {$rowServer->price}, {$rowServer->price_converted}, "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $sqlNext = "SELECT COALESCE(MAX(unloading_cost_id), 0) + 1 AS next_id FROM `unloading_cost`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `unloading_cost` SET unloading_cost_id = {$nextId} WHERE unloading_cost_id = {$rowServer->unloading_cost_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `unloading_cost` (unloading_cost_id, vehicle_id, stockpile_id, currency_id, exchange_rate, "
                                . "price, price_converted, entry_by, entry_date) VALUES ({$rowServer->unloading_cost_id}, "
                                . "{$rowServer->vehicle_id}, {$rowServer->stockpile_id}, {$rowServer->currency_id}, "
                                . "{$rowServer->exchange_rate}, {$rowServer->price}, {$rowServer->price_converted}, "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $syncCount = $syncCount + 1;
                        } else {
                            $failCount = $failCount + 1;
                        }
                        
                        $sql = "alter table `unloading_cost` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $sql = "UPDATE `unloading_cost` SET "
                        . "currency_id = {$rowServer->currency_id}, "
                        . "exchange_rate = {$rowServer->exchange_rate}, "
                        . "price = {$rowServer->price}, "
                        . "price_converted = {$rowServer->price_converted}, "
                        . "modify_by = {$_SESSION['userId']}, "
                        . "modify_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') "
                        . "WHERE unloading_cost_id = {$rowServer->unloading_cost_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) { 
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($unloadingCostIds != "") {
        $sql = "DELETE FROM unloading_cost WHERE unloading_cost_id NOT IN ({$unloadingCostIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(unloading_cost_id), 0) AS next_id FROM `unloading_cost`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `unloading_cost` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Unloading Cost: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="sales">
    
    $sqlServer = "SELECT * FROM `sales`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $totalServer = $resultServer->num_rows;
    
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $salesIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($salesIds == "") {
                $salesIds = $rowServer->sales_id;
            } else {
                $salesIds .= ",". $rowServer->sales_id;
            }
            
            $sql = "SELECT sl.* "
                    . "FROM `sales` sl "
                    . "WHERE 1=1 "
//                    . "AND sl.sales_id = {$rowServer->sales_id} "
                    . "AND sl.sales_no = '{$rowServer->sales_no}' AND sl.sales_type = {$rowServer->sales_type} "
                    . "AND sl.customer_id = {$rowServer->customer_id} AND stockpile_id = {$rowServer->stockpile_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `sales` WHERE sales_id = {$rowServer->sales_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `sales` (sales_id, sales_no, sales_date, sales_type, customer_id, stockpile_id, loading, destination, "
                            . "notes, currency_id, exchange_rate, price, price_converted, quantity, total_shipment, sales_status, entry_by, entry_date) "
                            . "VALUES ({$rowServer->sales_id}, '{$rowServer->sales_no}', STR_TO_DATE('{$rowServer->sales_date}', '%Y-%m-%d %H:%i:%s'), "
                            . "{$rowServer->sales_type}, {$rowServer->customer_id}, {$rowServer->stockpile_id}, '{$rowServer->loading}', "
                            . "'{$rowServer->destination}', '{$rowServer->notes}', {$rowServer->currency_id}, {$rowServer->exchange_rate}, "
                            . "{$rowServer->price}, {$rowServer->price_converted}, {$rowServer->quantity}, {$rowServer->total_shipment}, "
                            . "{$rowServer->sales_status}, {$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $sqlNext = "SELECT COALESCE(MAX(sales_id), 0) + 1 AS next_id FROM `sales`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `sales` SET sales_id = {$nextId} WHERE sales_id = {$rowServer->sales_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `sales` (sales_id, sales_no, sales_date, sales_type, customer_id, stockpile_id, loading, destination, "
                                . "notes, currency_id, exchange_rate, price, price_converted, quantity, total_shipment, sales_status, entry_by, entry_date) "
                                . "VALUES ({$rowServer->sales_id}, '{$rowServer->sales_no}', STR_TO_DATE('{$rowServer->sales_date}', '%Y-%m-%d %H:%i:%s'), "
                                . "{$rowServer->sales_type}, {$rowServer->customer_id}, {$rowServer->stockpile_id}, '{$rowServer->loading}', "
                                . "'{$rowServer->destination}', '{$rowServer->notes}', {$rowServer->currency_id}, {$rowServer->exchange_rate}, "
                                . "{$rowServer->price}, {$rowServer->price_converted}, {$rowServer->quantity}, {$rowServer->total_shipment}, "
                                . "{$rowServer->sales_status}, {$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        
                        if($result !== false) {
                            $syncCount = $syncCount + 1;
                        } else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `sales` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $row = $result->fetch_object();
                
                $sql = "UPDATE `sales` SET "
                        . "sales_id = {$rowServer->sales_id}, "
                        . "loading = '{$rowServer->loading}', "
                        . "destination = '{$rowServer->destination}', "
                        . "notes = '{$rowServer->notes}', "
                        . "currency_id = {$rowServer->currency_id}, "
                        . "exchange_rate = {$rowServer->exchange_rate}, "
                        . "price = {$rowServer->price}, "
                        . "price_converted = {$rowServer->price_converted}, "
                        . "quantity = {$rowServer->quantity}, "
                        . "total_shipment = {$rowServer->total_shipment}, "
                        . "sales_status = {$rowServer->sales_status} "
                        . "WHERE sales_id = {$row->sales_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($salesIds != "") {
        $sql = "DELETE FROM `sales` WHERE sales_id NOT IN ({$salesIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(sales_id), 0) AS next_id FROM `sales`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `sales` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Sales: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="shipment">
    
    $sqlServer = "SELECT * FROM `shipment`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $totalServer = $resultServer->num_rows;
    
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $shipmentIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($shipmentIds == "") {
                $shipmentIds = $rowServer->shipment_id;
            } else {
                $shipmentIds .= ",". $rowServer->shipment_id;
            }
            
            $sql = "SELECT * "
                    . "FROM `shipment` "
                    . "WHERE shipment_id = {$rowServer->shipment_id} "
                    . "AND shipment_code = '{$rowServer->shipment_code}' AND sales_id = {$rowServer->sales_id} ";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `shipment` WHERE shipment_id = {$rowServer->shipment_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `shipment` (shipment_id, shipment_code, sales_id, entry_by, entry_date) "
                            . "VALUES ({$rowServer->shipment_id}, '{$rowServer->shipment_code}', {$rowServer->sales_id}, "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $sqlNext = "SELECT COALESCE(MAX(shipment_id), 0) + 1 AS next_id FROM `shipment`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `shipment` SET shipment_id = {$nextId} WHERE shipment_id = {$rowServer->shipment_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `shipment` (shipment_id, shipment_code, sales_id, entry_by, entry_date) "
                                . "VALUES ({$rowServer->shipment_id}, '{$rowServer->shipment_code}', {$rowServer->sales_id}, "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        
                        if($result !== false) {
                            $syncCount = $syncCount + 1;
                        } else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `shipment` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                if($rowServer->payment_id != "") {
                    $sql = "UPDATE `shipment` SET "
                            . "payment_id = {$rowServer->payment_id} "
                            . "WHERE shipment_id = {$rowServer->shipment_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {
                    $syncCount = $syncCount + 1;
                }
            }
        }
    } 
    
    if($shipmentIds != "") {
        $sql = "DELETE FROM shipment WHERE shipment_id NOT IN ({$shipmentIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(shipment_id), 0) AS next_id FROM `shipment`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `shipment` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    
    $syncMessage .= 'Shipment: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="payment">
    
    $sqlServer = "SELECT * FROM `payment`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $totalServer = $resultServer->num_rows;
    
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $paymentIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($paymentIds == "") {
                $paymentIds = $rowServer->payment_id;
            } else {
                $paymentIds .= ",". $rowServer->payment_id;
            }
            
            $stockpileContractId = "NULL";
            $vendorId = "NULL";
            $salesId = "NULL";
            $freightId = "NULL";
            $laborId = "NULL";
            
            if($rowServer->stockpile_contract_id != "") {
                $stockpileContractId = $rowServer->stockpile_contract_id;
            }
            
            if($rowServer->vendor_id != "") {
                $vendorId = $rowServer->vendor_id;
            }
            
            if($rowServer->sales_id != "") {
                $salesId = $rowServer->sales_id;
            }
            
            if($rowServer->freight_id != "") {
                $freightId = $rowServer->freight_id;
            }
            
            if($rowServer->labor_id != "") {
                $laborId = $rowServer->labor_id;
            }
            
            $sql = "SELECT * "
                    . "FROM `payment` "
                    . "WHERE 1=1 "
//                    . "AND payment_id = {$rowServer->payment_id} "
                    . "AND payment_no = '{$rowServer->payment_no}' AND payment_type = {$rowServer->payment_type} "
                    . "AND payment_method = {$rowServer->payment_method} AND bank_id = {$rowServer->bank_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `payment` WHERE payment_id = {$rowServer->payment_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `payment` (payment_id, payment_no, payment_date, payment_type, payment_method, account_id, bank_id, "
                            . "stockpile_contract_id, vendor_id, sales_id, freight_id, labor_id, payment_notes, remarks, currency_id, "
                            . "exchange_rate, amount, amount_converted, original_amount, original_amount_converted, entry_by, entry_date) "
                            . "VALUES ({$rowServer->payment_id}, '{$rowServer->payment_no}', STR_TO_DATE('{$rowServer->payment_date}', '%Y-%m-%d %H:%i:%s'), "
                            . "{$rowServer->payment_type}, {$rowServer->payment_method}, {$rowServer->account_id}, {$rowServer->bank_id}, "
                            . "{$stockpileContractId}, {$vendorId}, {$salesId}, {$freightId}, {$laborId}, '{$rowServer->payment_notes}', "
                            . "'{$rowServer->remarks}', {$rowServer->currency_id}, {$rowServer->exchange_rate}, {$rowServer->amount}, "
                            . "{$rowServer->amount_converted}, {$rowServer->original_amount}, {$rowServer->original_amount_converted}, "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {    
                    $sqlNext = "SELECT COALESCE(MAX(payment_id), 0) + 1 AS next_id FROM `payment`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $checkPaymentNo = $currentYearMonth;
                    $sql = "SELECT LPAD(RIGHT(slip_no, 10) + 1, 5, '0') AS next_id FROM payment WHERE payment_no LIKE '{$checkPaymentNo}%'";
                    $resultPaymentNo = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    if($resultPaymentNo->num_rows == 0) {
                        $sql = "SELECT LPAD(1, 5, '0') AS next_id FROM payment LIMIT 1";
                        $resultPaymentNo = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    }
                    $rowPaymentNo = $resultPaymentNo->fetch_object();
                    $nextPaymentNo = $rowPaymentNo->next_id;
                    $paymentNo = $checkPaymentNo .'-'. $nextPaymentNo;
                    
                    $sql = "UPDATE `payment` SET payment_no = '{$paymentNo}', payment_id = {$nextId} WHERE payment_id = {$rowServer->payment_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `payment` (payment_id, payment_no, payment_date, payment_type, payment_method, account_id, bank_id, "
                                . "stockpile_contract_id, vendor_id, sales_id, freight_id, labor_id, payment_notes, remarks, currency_id, "
                                . "exchange_rate, amount, amount_converted, original_amount, original_amount_converted, entry_by, entry_date) "
                                . "VALUES ({$rowServer->payment_id}, '{$rowServer->payment_no}', STR_TO_DATE('{$rowServer->payment_date}', '%Y-%m-%d %H:%i:%s'), "
                                . "{$rowServer->payment_type}, {$rowServer->payment_method}, {$rowServer->account_id}, {$rowServer->bank_id}, "
                                . "{$stockpileContractId}, {$vendorId}, {$salesId}, {$freightId}, {$laborId}, '{$rowServer->payment_notes}', "
                                . "'{$rowServer->remarks}', {$rowServer->currency_id}, {$rowServer->exchange_rate}, {$rowServer->amount}, "
                                . "{$rowServer->amount_converted}, {$rowServer->original_amount}, {$rowServer->original_amount_converted}, "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $syncCount = $syncCount + 1;
                        } else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `payment` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $row = $result->fetch_object();
                
                $sql = "UPDATE `payment` SET "
                        . "payment_id = {$rowServer->payment_id}, "
                        . "payment_date = {$rowServer->payment_id}, "
                        . "account_id = {$rowServer->account_id}, "
                        . "stockpile_contract_id = {$stockpileContractId}, "
                        . "vendor_id = {$vendorId}, "
                        . "sales_id = {$salesId}, "
                        . "freight_id = {$freightId}, "
                        . "labor_id = {$laborId}, "
                        . "payment_notes = '{$rowServer->payment_notes}', "
                        . "remarks = '{$rowServer->remarks}', "
                        . "currency_id = {$rowServer->currency_id}, "
                        . "exchange_rate = {$rowServer->exchange_rate}, "
                        . "amount = {$rowServer->amount}, "
                        . "amount_converted = {$rowServer->amount_converted}, "
                        . "original_amount = {$rowServer->original_amount}, "
                        . "original_amount_converted = {$rowServer->original_amount_converted} "
                        . "WHERE payment_id = {$row->payment_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($paymentIds != "") {
        $sql = "DELETE FROM payment WHERE payment_id NOT IN ({$paymentIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result !== false) {
            $warnCount = $warnCount + 1;
        }
        
        $sqlPayment = "SELECT payment_id, GROUP_CONCAT(transaction_id) AS transaction_ids FROM `transaction` WHERE payment_id IN ({$paymentIds}) GROUP by payment_id";
        $resultPayment = $myDatabaseServer->query($sqlPayment, MYSQLI_STORE_RESULT);
        if($resultPayment->num_rows > 0) {
            while($rowPayment = $resultPayment->fetch_object()) {
                $sql = "UPDATE `transaction` SET payment_id = {$rowPayment->payment_id} WHERE transaction_id IN ($rowPayment->transaction_ids)";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $warnCount = $warnCount + 1;
                }
            }
        }
        
        $sqlPayment = "SELECT payment_id, GROUP_CONCAT(transaction_id) AS transaction_ids FROM `transaction` WHERE fc_payment_id IN ({$paymentIds}) GROUP by payment_id";
        $resultPayment = $myDatabaseServer->query($sqlPayment, MYSQLI_STORE_RESULT);
        if($resultPayment->num_rows > 0) {
            while($rowPayment = $resultPayment->fetch_object()) {
                $sql = "UPDATE `transaction` SET fc_payment_id = {$rowPayment->payment_id} WHERE transaction_id IN ($rowPayment->transaction_ids)";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $warnCount = $warnCount + 1;
                }
            }
        }
        
        $sqlPayment = "SELECT payment_id, GROUP_CONCAT(transaction_id) AS transaction_ids FROM `transaction` WHERE uc_payment_id IN ({$paymentIds}) GROUP by payment_id";
        $resultPayment = $myDatabaseServer->query($sqlPayment, MYSQLI_STORE_RESULT);
        if($resultPayment->num_rows > 0) {
            while($rowPayment = $resultPayment->fetch_object()) {
                $sql = "UPDATE `transaction` SET uc_payment_id = {$rowPayment->payment_id} WHERE transaction_id IN ($rowPayment->transaction_ids)";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $warnCount = $warnCount + 1;
                }
            }
        }
        
        $sqlPayment = "SELECT payment_id, GROUP_CONCAT(shipment_id) AS shipment_ids FROM `shipment` WHERE payment_id IN ({$paymentIds}) GROUP by payment_id";
        $resultPayment = $myDatabaseServer->query($sqlPayment, MYSQLI_STORE_RESULT);
        if($resultPayment->num_rows > 0) {
            while($rowPayment = $resultPayment->fetch_object()) {
                $sql = "UPDATE `shipment` SET payment_id = {$rowPayment->payment_id} WHERE shipment_id IN ($rowPayment->shipment_ids)";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $warnCount = $warnCount + 1;
                }
            }
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(payment_id), 0) AS next_id FROM `payment`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `payment` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Payment: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="payment detail">
    
    $sqlServer = "SELECT * FROM `payment_detail`";
    $resultServer = $myDatabaseServer->query($sqlServer, MYSQLI_STORE_RESULT);
    $totalServer = $resultServer->num_rows;
    
    $syncCount = 0;
    $failCount = 0;
    $warnCount = 0;
    $paymentDetailIds = "";
    if($resultServer->num_rows > 0) {
        while($rowServer = $resultServer->fetch_object()) {
            if($paymentDetailIds == "") {
                $paymentDetailIds = $rowServer->payment_detail_id;
            } else {
                $paymentDetailIds .= ",". $rowServer->payment_detail_id;
            }
            
            $sql = "SELECT * "
                    . "FROM `payment_detail` "
                    . "WHERE payment_detail_id = {$rowServer->payment_detail_id} "
                    . "AND payment_id = {$rowServer->payment_id} AND shipment_id = {$rowServer->shipment_id}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if($result->num_rows == 0) {
                $sql = "SELECT * FROM `payment_detail` WHERE payment_detail_id = {$rowServer->payment_detail_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result->num_rows == 0) {
                    $sql = "INSERT INTO `payment_detail` (payment_detail_id, payment_id, amount, amount_converted, shipment_id, entry_by, entry_date) "
                            . "VALUES ({$rowServer->payment_detail_id}, {$rowServer->payment_id}, {$rowServer->amount}, "
                            . "{$rowServer->amount_converted}, {$rowServer->shipment_id}, "
                            . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $syncCount = $syncCount + 1;
                    } else {
                        $failCount = $failCount + 1;
                    }
                } else {    
                    $sqlNext = "SELECT COALESCE(MAX(payment_detail_id), 0) + 1 AS next_id FROM `payment_detail`";
                    $resultNext = $myDatabase->query($sqlNext, MYSQLI_STORE_RESULT);
                    $rowNext = $resultNext->fetch_object();
                    $nextId = $rowNext->next_id;
                    $nextAuto = $nextId + 1;
                    
                    $sql = "UPDATE `payment_detail` SET payment_detail_id = {$nextId} WHERE payment_detail_id = {$rowServer->payment_detail_id}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result !== false) {
                        $sql = "INSERT INTO `payment_detail` (payment_detail_id, payment_id, amount, amount_converted, shipment_id, entry_by, entry_date) "
                                . "VALUES ({$rowServer->payment_detail_id}, {$rowServer->payment_id}, {$rowServer->amount}, "
                                . "{$rowServer->amount_converted}, {$rowServer->shipment_id}, "
                                . "{$rowServer->entry_by}, STR_TO_DATE('{$rowServer->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        
                        if($result !== false) {
                            $syncCount = $syncCount + 1;
                        } else {
                            $failCount = $failCount + 1;
                        }

                        $sql = "alter table `payment_detail` auto_increment = {$nextAuto}";
                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    } else {
                        $failCount = $failCount + 1;
                    }
                }
            } else {
                $sql = "UPDATE `payment_detail` SET "
                        . "amount = {$rowServer->amount}, "
                        . "amount_converted = {$rowServer->amount_converted} "
                        . "WHERE payment_detail_id = {$rowServer->payment_detail_id}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                
                if($result !== false) {
                    $syncCount = $syncCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
        }
    } 
    
    if($paymentDetailIds != "") {
        $sql = "DELETE FROM payment_detail WHERE payment_detail_id NOT IN ({$paymentDetailIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        
        if($result === false) {
            $warnCount = $warnCount + 1;
        }
    }
    
    if($warnCount == 0) {
        $sqlNext = "SELECT COALESCE(MAX(payment_detail_id), 0) AS next_id FROM `payment_detail`";
        $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
        $rowNext = $resultNext->fetch_object();
        $nextId = $rowNext->next_id;
        $nextAuto = $nextId + 1;

        $sql = "alter table `payment_detail` auto_increment = {$nextAuto}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
    
    if($syncCount == $totalServer) {
        $addMessage = "Sync success";
    } else {
        $addMessage = "Sync failed";
    }
    
    if($warnCount > 0) {
        $addMessage .= " with {$warnCount} warning (need to upload from local)";
    }
    
    if($failCount > 0) {
        $addMessage .= " with {$failCount} failure";
    }
    
    $syncMessage .= 'Payment Detail: '.$addMessage.'.<br/>';
    
    // </editor-fold>
    
    $return_value = '|OK|'. $syncMessage .'|';
    
    echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'upload') {
    
    
    $return_value = '';
    $syncMessage = 'Sync success';
    $addMessage = '';
    
    
    $sqlLocal = "SELECT t.sync_status, t.slip_no, t.product_id, t.transaction_type, t.stockpile_contract_id, con.contract_type, con.po_no, con.contract_no, "
            . "con.vendor_id AS con_vendor_id, vcon.vendor_code AS con_vendor_code, vcon.vendor_name AS con_vendor_name, vcon.vendor_address AS con_vendor_address, "
            . "sc.contract_id AS sc_contract_id, "
            . "sc.stockpile_id AS sc_stockpile_id, con.currency_id AS con_currency_id, con.exchange_rate AS con_exchange_rate, con.price AS con_price, "
            . "con.price_converted AS con_price_converted, con.quantity AS con_quantity, con.payment_status AS con_payment_status, sc.quantity AS sc_quantity, "
            . "t.shipment_id, sh.shipment_code, sh.sales_id, sl.sales_no, sl.sales_date, sl.sales_type, sl.customer_id, sl.stockpile_id AS sl_stockpile_id, "
            . "cust.customer_name, sl.loading AS sl_loading, sl.destination AS sl_destination, sl.notes AS sl_notes, sl.currency_id AS sl_currency_id, "
            . "sl.exchange_rate AS sl_exchange_rate, sl.price AS sl_price, sl.price_converted AS sl_price_converted, sl.quantity AS sl_quantity, "
            . "sl.total_shipment, sl.sales_status, sh.shipment_date, sh.dp_amount AS sh_dp_amount, sh.cogs_amount AS sh_cogs_amount, sh.invoice_amount AS sh_invoice_amount, "
            . "sh.quantity AS sh_quantity, sh.shipment_status, sh.payment_id AS sh_payment_id, "
            . "t.labor_id, l.labor_name, t.unloading_cost_id, uc.vehicle_id, v.vehicle_name, uc.stockpile_id AS uc_stockpile_id, uc.currency_id AS uc_currency_id, "
            . "uc.exchange_rate AS uc_exchange_rate, uc.price AS uc_price, uc.price_converted AS uc_price_converted, t.freight_cost_id, fc.freight_id, "
            . "f.freight_code, f.freight_supplier, fc.stockpile_id AS fc_stockpile_id, fc.vendor_id AS fc_vendor_id, vfc.vendor_code AS vfc_vendor_code, "
            . "vfc.vendor_name AS vfc_vendor_name, vfc.vendor_address AS vfc_vendor_address, fc.currency_id AS fc_currency_id, fc.exchange_rate AS fc_exchange_rate, "
            . "fc.price AS fc_price, fc.price_converted AS fc_price_converted, fc.payment_notes AS fc_payment_notes, fc.remarks AS fc_remarks, "
            . "t.vendor_id AS t_vendor_id, vt.vendor_code AS t_vendor_code, vt.vendor_name AS t_vendor_name, t.transaction_date, t.loading_date, "
            . "t.vehicle_no, t.unloading_date, t.permit_no, t.send_weight, t.bruto_weight, t.tarra_weight, t.netto_weight, t.notes AS t_notes, "
            . "t.driver, t.freight_quantity, t.quantity AS t_quantity, t.shrink, t.freight_price AS t_freight_price, t.unloading_price AS t_unloading_price, "
            . "t.unit_price, t.inventory_value, t.block, t.delivery_status, t.payment_id AS t_payment_id, t.fc_payment_id, t.uc_payment_id, "
            . "t.entry_by, t.entry_date, t.transaction_id "
            . "FROM `transaction` t "
            . "LEFT JOIN stockpile_contract sc "
            . "ON sc.stockpile_contract_id = t.stockpile_contract_id "
            . "LEFT JOIN `contract` con "
            . "ON con.contract_id = sc.contract_id "
            . "LEFT JOIN vendor vcon "
            . "ON vcon.vendor_id = con.vendor_id "
            . "LEFT JOIN shipment sh "
            . "ON sh.shipment_id = t.shipment_id "
            . "LEFT JOIN sales sl "
            . "ON sl.sales_id = sh.sales_id "
            . "LEFT JOIN customer cust "
            . "ON cust.customer_id = sl.customer_id "
            . "LEFT JOIN labor l "
            . "ON l.labor_id = t.labor_id "
            . "LEFT JOIN unloading_cost uc "
            . "ON uc.unloading_cost_id = t.unloading_cost_id "
            . "LEFT JOIN vehicle v "
            . "ON v.vehicle_id = uc.vehicle_id "
            . "LEFT JOIN freight_cost fc "
            . "ON fc.freight_cost_id = t.freight_cost_id "
            . "LEFT JOIN freight f "
            . "ON f.freight_id = fc.freight_id "
            . "LEFT JOIN vendor vfc "
            . "ON vfc.vendor_id = fc.vendor_id "
            . "LEFT JOIN vendor vt "
            . "ON vt.vendor_id = t.vendor_id "
            . "WHERE t.sync_status != 1";
    $resultLocal = $myDatabase->query($sqlLocal, MYSQLI_STORE_RESULT);
    
    if($resultLocal->num_rows > 0) {
        while($rowLocal = $resultLocal->fetch_object()) {
            $stockpileContractId = "NULL";
            $laborId = "NULL";
            $unloadingCostId = "NULL";
            $freightCostId = "NULL";
            $supplierId = "NULL";
            $shipmentId = "NULL";
            $paymentId = "NULL";
            $fcPaymentId = "NULL";
            $ucPaymentId = "NULL";
            
            $boolStockpileContract = false;
            $boolLabor = false;
            $boolVehicle = false;
            $boolUnloadingCost = false;
            $boolFreightVendor = false;
            $boolFreight = false;
            $boolFreightCost = false;
            $boolSupplier = false;
            $boolContractVendor = false;
            $boolContract = false;
            $boolCustomer = false;
            $boolSales = false;
            $boolShipment = false;
            $boolDelivery = false;
            
            if($rowLocal->transaction_type == 1) {
                //IN
                
                // <editor-fold defaultstate="collapsed" desc="contract vendor">

                $sql = "SELECT vendor_id FROM vendor "
                        . "WHERE vendor_code = '{$rowLocal->con_vendor_code}'";
                $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                if($result->num_rows == 0) {
                    $sql = "SELECT * from vendor "
                            . "WHERE vendor_id = {$rowLocal->con_vendor_id}";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "INSERT INTO vendor (vendor_id, vendor_code, vendor_name, vendor_address, entry_by, entry_date) VALUES ("
                                . "{$rowLocal->con_vendor_id}, '{$rowLocal->con_vendor_code}', '{$rowLocal->con_vendor_name}', '{$rowLocal->con_vendor_address}', "
                                . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $boolContractVendor = true;
                            $conVendorId = $rowLocal->con_vendor_id;
                        } else {
                            // error
                        }
                    } else {
                        $sql = "INSERT INTO vendor (vendor_code, vendor_name, vendor_address, entry_by, entry_date) VALUES ("
                                . "'{$rowLocal->con_vendor_code}', '{$rowLocal->con_vendor_name}', '{$rowLocal->con_vendor_address}', "
                                . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $boolContractVendor = true;
                            $conVendorId = $myDatabaseServer->insert_id;

                            $sqlNext = "SELECT COALESCE(MAX(vendor_id), 0) + 1 AS next_id FROM `vendor`";
                            $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                            $rowNext = $resultNext->fetch_object();
                            $nextId = $rowNext->next_id;
                            $nextAuto = $nextId + 1;

                            $sql = "UPDATE vendor SET vendor_id = {$conVendorId} WHERE vendor_id = {$rowLocal->con_vendor_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $sql = "alter table `vendor` auto_increment = {$nextAuto}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            }
                        } else {
                            // error
                        }
                    }
                } else {
                    $boolContractVendor = true;

                    $row = $result->fetch_object();

                    if($rowLocal->con_vendor_id == $row->vendor_id) {
                        $conVendorId = $rowLocal->con_vendor_id;
                    } else {
                        $conVendorId = $row->vendor_id;
                    }
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="contract">

                if($boolContractVendor) {
                    $sql = "SELECT contract_id FROM contract "
                            . "WHERE contract_no = '{$rowLocal->contract_no}' "
                            . "AND po_no = '{$rowLocal->po_no}' AND vendor_id = {$conVendorId} ";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * from contract "
                                . "WHERE contract_id = {$rowLocal->sc_contract_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO contract (contract_id, contract_type, po_no, contract_no, vendor_id, currency_id, "
                                    . "exchange_rate, price, price_converted, quantity, payment_status, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->sc_contract_id}, '{$rowLocal->contract_type}', '{$rowLocal->po_no}', '{$rowLocal->contract_no}', "
                                    . "{$conVendorId}, {$rowLocal->con_currency_id}, {$rowLocal->con_exchange_rate}, {$rowLocal->con_price}, "
                                    . "{$rowLocal->con_price_converted}, {$rowLocal->con_quantity}, {$rowLocal->con_payment_status}, "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolContract = true;
                                $contractId = $rowLocal->sc_contract_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO contract (contract_type, po_no, contract_no, vendor_id, currency_id, "
                                    . "exchange_rate, price, price_converted, quantity, payment_status, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->contract_type}', '{$rowLocal->po_no}', '{$rowLocal->contract_no}', "
                                    . "{$conVendorId}, {$rowLocal->con_currency_id}, {$rowLocal->con_exchange_rate}, {$rowLocal->con_price}, "
                                    . "{$rowLocal->con_price_converted}, {$rowLocal->con_quantity}, {$rowLocal->con_payment_status}, "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolContract = true;
                                $contractId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(contract_id), 0) + 1 AS next_id FROM `contract`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE contract SET contract_id = {$contractId} WHERE contract_id = {$rowLocal->sc_contract_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `contract` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolContract = true;

                        $row = $result->fetch_object();

                        if($rowLocal->sc_contract_id == $row->contract_id) {
                            $contractId = $rowLocal->sc_contract_id;
                        } else {
                            $contractId = $row->contract_id;
                        }
                    }
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="stockpile contract">

                if($boolContract) {
                    $sql = "SELECT stockpile_contract_id FROM stockpile_contract "
                            . "WHERE stockpile_id = {$rowLocal->sc_stockpile_id} "
                            . "AND contract_Id = {$contractId}";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * FROM stockpile_contract "
                                . "WHERE stockpile_contract_id = {$rowLocal->stockpile_contract_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO stockpile_contract (stockpile_contract_id, stockpile_id, contract_id, quantity, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->stockpile_contract_id}, {$rowLocal->sc_stockpile_id}, "
                                    . "{$contractId}, {$rowLocal->sc_quantity}, "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolStockpileContract = true;
                                $stockpileContractId = $rowLocal->stockpile_contract_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO stockpile_contract (stockpile_id, contract_id, quantity, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->sc_stockpile_id}, "
                                    . "{$contractId}, {$rowLocal->sc_quantity}, "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolStockpileContract = true;
                                $stockpileContractId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(stockpile_contract_id), 0) + 1 AS next_id FROM `stockpile_contract`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE stockpile_contract SET stockpile_contract_id = {$stockpileContractId} "
                                . "WHERE stockpile_contract_id = {$rowLocal->stockpile_contract_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `stockpile_contract` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolStockpileContract = true;

                        $row = $result->fetch_object();

                        if($rowLocal->stockpile_contract_id == $row->stockpile_contract_id) {
                            $stockpileContractId = $rowLocal->stockpile_contract_id;
                        } else {
                            $stockpileContractId = $row->stockpile_contract_id;
                        }
                    }
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="labor">

                if($rowLocal->labor_id != '') {
                    $sql = "SELECT labor_id "
                            . "FROM labor "
                            . "WHERE labor_name = '{$rowLocal->labor_name}'";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 1) {
                        // flag ok
                        $laborId = $rowLocal->labor_id;
                        $boolLabor = true;
                    } else {
                        $sql = "SELECT labor_id FROM labor WHERE labor_id = {$rowLocal->labor_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO labor (labor_id, labor_name, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->labor_id}, '{$rowLocal->labor_name}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $laborId = $rowLocal->labor_id;
                                $boolLabor = true;
                            } else {
                                /// error
                            }
                        } else {
                            $sql = "INSERT INTO labor (labor_name, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->labor_name}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $laborId = $myDatabaseServer->insert_id;
                                $boolLabor = true;

                                $sqlNext = "SELECT COALESCE(MAX(labor_id), 0) + 1 AS next_id FROM `labor`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE labor SET labor_id = {$laborId} "
                                . "WHERE labor_id = {$rowLocal->labor_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `labor` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                /// error
                            }
                        }
                    }
                } else {
                    $boolLabor = true;
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="vehicle">

                if($rowLocal->unloading_cost_id != '') {
                    $sql = "SELECT vehicle_id FROM vehicle "
                            . "WHERE vehicle_name = '{$rowLocal->vehicle_name}'";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * from vehicle "
                                . "WHERE vehicle_id = {$rowLocal->vehicle_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO vehicle (vehicle_id, vehicle_name, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->vehicle_id}, '{$rowLocal->vehicle_name}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolVehicle = true;
                                $vehicleId = $rowLocal->vehicle_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO vehicle (vehicle_name, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->vehicle_name}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolVehicle = true;
                                $vehicleId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(vehicle_id), 0) + 1 AS next_id FROM `vehicle`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE vehicle SET vehicle_id = {$vehicleId} WHERE vehicle_id = {$rowLocal->vehicle_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `vehicle` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolVehicle = true;

                        $row = $result->fetch_object();

                        if($rowLocal->vehicle_id == $row->vehicle_id) {
                            $vehicleId = $rowLocal->vehicle_id;
                        } else {
                            $vehicleId = $row->vehicle_id;
                        }
                    }
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="unloading cost">

                if($rowLocal->unloading_cost_id != '' && $boolVehicle) {
                    $sql = "SELECT uc.unloading_cost_id "
                            . "FROM unloading_cost uc "
                            . "WHERE uc.vehicle_id = {$vehicleId}  AND uc.stockpile_id = {$rowLocal->uc_stockpile_id} "
                            . "AND uc.currency_id = {$rowLocal->uc_currency_id} AND uc.exchange_rate = {$rowLocal->uc_exchange_rate} "
                            . "AND uc.price = {$rowLocal->uc_price} AND uc.price_converted = {$rowLocal->uc_price_converted}";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * FROM unloading_cost "
                                . "WHERE unloading_cost_id = {$rowLocal->unloading_cost_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO unloading_cost (unloading_cost_id, vehicle_id, stockpile_id, currency_id, exchange_rate, "
                                    . "price, price_converted, entry_by, entry_date) "
                                    . "VALUES ({$rowLocal->unloading_cost_id}, {$vehicleId}, {$rowLocal->uc_stockpile_id}, "
                                    . "{$rowLocal->uc_currency_id}, {$rowLocal->uc_exchange_rate}, "
                                    . "{$rowLocal->uc_price}, {$rowLocal->uc_price_converted}, "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolUnloadingCost = true;
                                $unloadingCostId = $rowLocal->unloading_cost_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO unloading_cost (vehicle_id, stockpile_id, currency_id, exchange_rate, "
                                    . "price, price_converted, entry_by, entry_date) "
                                    . "VALUES ({$vehicleId}, {$rowLocal->uc_stockpile_id}, "
                                    . "{$rowLocal->uc_currency_id}, {$rowLocal->uc_exchange_rate}, "
                                    . "{$rowLocal->uc_price}, {$rowLocal->uc_price_converted}, "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolUnloadingCost = true;
                                $unloadingCostId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(unloading_cost_id), 0) + 1 AS next_id FROM `unloading_cost`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE unloading_cost SET unloading_cost_id = {$unloadingCostId} "
                                . "WHERE unloading_cost_id = {$rowLocal->unloading_cost_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `unloading_cost` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolUnloadingCost = true;

                        $row = $result->fetch_object();

                        if($rowLocal->unloading_cost_id == $row->unloading_cost_id) {
                            $unloadingCostId = $rowLocal->unloading_cost_id;
                        } else {
                            $unloadingCostId = $row->unloading_cost_id;
                        }
                    }
                } else {
                    $boolUnloadingCost = true;
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="freight vendor">

                if($rowLocal->freight_cost_id != '' ) {
                    $sql = "SELECT vendor_id FROM vendor "
                            . "WHERE vendor_code = '{$rowLocal->vfc_vendor_code}'";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * from vendor "
                                . "WHERE vendor_id = {$rowLocal->fc_vendor_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO vendor (vendor_id, vendor_code, vendor_name, vendor_address, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->fc_vendor_id}, '{$rowLocal->vfc_vendor_code}', '{$rowLocal->vfc_vendor_name}', '{$rowLocal->vfc_vendor_address}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolFreightVendor = true;
                                $fcVendorId = $rowLocal->fc_vendor_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO vendor (vendor_code, vendor_name, vendor_address, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->vfc_vendor_code}', '{$rowLocal->vfc_vendor_name}', '{$rowLocal->vfc_vendor_address}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolFreightVendor = true;
                                $fcVendorId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(vendor_id), 0) + 1 AS next_id FROM `vendor`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE vendor SET vendor_id = {$fcVendorId} WHERE vendor_id = {$rowLocal->fc_vendor_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `vendor` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolFreightVendor = true;

                        $row = $result->fetch_object();

                        if($rowLocal->fc_vendor_id == $row->vendor_id) {
                            $fcVendorId = $rowLocal->fc_vendor_id;
                        } else {
                            $fcVendorId = $row->vendor_id;
                        }
                    }
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="freight">

                if($rowLocal->freight_cost_id != '' ) {

                    $sql = "SELECT freight_id FROM freight "
                            . "WHERE freight_code = '{$rowLocal->freight_code}' AND freight_supplier = '{$rowLocal->freight_supplier}'";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * from freight "
                                . "WHERE freight_id = {$rowLocal->freight_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO freight (freight_id, freight_code, freight_supplier, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->freight_id}, '{$rowLocal->freight_code}', '{$rowLocal->freight_supplier}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolFreight = true;
                                $freightId = $rowLocal->freight_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO freight (freight_code, freight_supplier, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->freight_code}', '{$rowLocal->freight_supplier}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolFreight = true;
                                $freightId = $rowLocal->freight_id;

                                $sqlNext = "SELECT COALESCE(MAX(freight_id), 0) + 1 AS next_id FROM `freight`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE freight SET freight_id = {$freightId} WHERE freight_id = {$rowLocal->freight_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `freight` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolFreight = true;

                        $row = $result->fetch_object();

                        if($rowLocal->freight_id == $row->freight_id) {
                            $freightId = $rowLocal->freight_id;
                        } else {
                            $freightId = $row->freight_id;
                        }
                    }
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="freight cost">

                if($rowLocal->freight_cost_id != '' && $boolFreightVendor && $boolFreight) {
                    $sql = "SELECT fc.freight_cost_id "
                            . "FROM freight_cost fc "
                            . "WHERE fc.freight_id = {$freightId} "
                            . "AND fc.stockpile_id = {$rowLocal->fc_stockpile_id} AND fc.vendor_id = {$fcVendorId} "
                            . "AND fc.currency_id = {$rowLocal->fc_currency_id} AND fc.exchange_rate = {$rowLocal->fc_exchange_rate} "
                            . "AND fc.price = {$rowLocal->fc_price} AND fc.price_converted = {$rowLocal->fc_price_converted}";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT freight_cost_id FROM freight_cost "
                                . "WHERE freight_cost_id = {$rowLocal->freight_cost_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO freight_cost (freight_cost_id, freight_id, stockpile_id, vendor_id, currency_id, "
                                    . "exchange_rate, price, price_converted, payment_notes, remarks, entry_by, entry_date) "
                                    . "VALUES ({$rowLocal->freight_cost_id}, {$freightId}, {$rowLocal->fc_stockpile_id}, "
                                    . "{$fcVendorId}, {$rowLocal->fc_currency_id}, {$rowLocal->fc_exchange_rate}, "
                                    . "{$rowLocal->fc_price}, {$rowLocal->fc_price_converted}, '{$rowLocal->fc_payment_notes}', "
                                    . "'{$rowLocal->fc_remarks}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolFreightCost = true;
                                $freightCostId = $rowLocal->freight_cost_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO freight_cost (freight_id, stockpile_id, vendor_id, currency_id, "
                                    . "exchange_rate, price, price_converted, payment_notes, remarks, entry_by, entry_date) "
                                    . "VALUES ({$freightId}, {$rowLocal->fc_stockpile_id}, "
                                    . "{$fcVendorId}, {$rowLocal->fc_currency_id}, {$rowLocal->fc_exchange_rate}, "
                                    . "{$rowLocal->fc_price}, {$rowLocal->fc_price_converted}, '{$rowLocal->fc_payment_notes}', "
                                    . "'{$rowLocal->fc_remarks}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolFreightCost = true;
                                $freightCostId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(freight_cost_id), 0) + 1 AS next_id FROM `freight_cost`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE freight_cost SET freight_cost_id = {$freightCostId} "
                                . "WHERE freight_cost_id = {$rowLocal->freight_cost_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `freight_cost` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolFreightCost = true;

                        $row = $result->fetch_object();

                        if($rowLocal->freight_cost_id == $row->freight_cost_id) {
                            $freightCostId = $rowLocal->freight_cost_id;
                        } else {
                            $freightCostId = $row->freight_cost_id;
                        }
                    }
                } else {
                    $boolFreightCost = true;
                }

                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="supplier">

                if($rowLocal->t_vendor_id != '') {
                    $sql = "SELECT vendor_id FROM vendor "
                            . "WHERE vendor_code = '{$rowLocal->t_vendor_code}'";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * from vendor "
                                . "WHERE vendor_id = {$rowLocal->t_vendor_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO vendor (vendor_id, vendor_code, vendor_name, vendor_address, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->t_vendor_id}, '{$rowLocal->t_vendor_code}', '{$rowLocal->t_vendor_name}', '{$rowLocal->t_vendor_address}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolSupplier = true;
                                $supplierId = $rowLocal->t_vendor_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO vendor (vendor_code, vendor_name, vendor_address, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->t_vendor_code}', '{$rowLocal->t_vendor_name}', '{$rowLocal->t_vendor_address}', "
                                    . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $boolSupplier = true;
                                $supplierId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(vendor_id), 0) + 1 AS next_id FROM `vendor`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE vendor SET vendor_id = {$supplierId} WHERE vendor_id = {$rowLocal->t_vendor_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `vendor` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                    } else {
                        $boolSupplier = true;

                        $row = $result->fetch_object();

                        if($rowLocal->t_vendor_id == $row->vendor_id) {
                            $supplierId = $rowLocal->t_vendor_id;
                        } else {
                            $supplierId = $row->vendor_id;
                        }
                    }
                } else {
                    $boolSupplier = true;
                }

                // </editor-fold>
                
                if($rowLocal->sync_status == 0) {
                    
                    // insert transaction
                    
                    if($boolStockpileContract && $boolLabor && $boolUnloadingCost && $boolFreightCost && $boolSupplier) {
                        if($rowLocal->t_payment_id != '') {
                            $paymentId = $rowLocal->t_payment_id;
                        }
                        
                        if($rowLocal->fc_payment_id != '') {
                            $fcPaymentId = $rowLocal->fc_payment_id;
                        }
                        
                        if($rowLocal->uc_payment_id != '') {
                            $ucPaymentId = $rowLocal->uc_payment_id;
                        }
                        
                        $sql = "INSERT INTO `transaction` (slip_no, product_id, stockpile_contract_id, shipment_id, transaction_date, loading_date, "
                                . "vehicle_no, labor_id, unloading_cost_id, unloading_date, freight_cost_id, permit_no, transaction_type, vendor_id, "
                                . "send_weight, bruto_weight, tarra_weight, netto_weight, notes, driver, freight_quantity, quantity, shrink, freight_price, "
                                . "unloading_price, unit_price, inventory_value, block, delivery_status, payment_id, fc_payment_id, uc_payment_id, "
                                . "sync_status, entry_by, entry_date) VALUES ("
                                . "'{$rowLocal->slip_no}', {$rowLocal->product_id}, {$stockpileContractId}, {$shipmentId}, STR_TO_DATE('{$rowLocal->transaction_date}', '%Y-%m-%d'), "
                                . "STR_TO_DATE('{$rowLocal->loading_date}', '%Y-%m-%d'), '{$rowLocal->vehicle_no}', {$laborId}, {$unloadingCostId}, "
                                . "STR_TO_DATE('{$rowLocal->unloading_date}', '%Y-%m-%d'), {$freightCostId}, '{$rowLocal->permit_no}', {$rowLocal->transaction_type}, "
                                . "{$supplierId}, {$rowLocal->send_weight}, {$rowLocal->bruto_weight}, {$rowLocal->tarra_weight}, {$rowLocal->netto_weight}, "
                                . "'{$rowLocal->t_notes}', '{$rowLocal->driver}', {$rowLocal->freight_quantity}, {$rowLocal->t_quantity}, {$rowLocal->shrink}, "
                                . "{$rowLocal->t_freight_price}, {$rowLocal->t_unloading_price}, {$rowLocal->unit_price}, {$rowLocal->inventory_value}, "
                                . "'{$rowLocal->block}', {$rowLocal->delivery_status}, {$paymentId}, {$fcPaymentId}, {$ucPaymentId}, 1, "
                                . "{$rowLocal->entry_by}, STR_TO_DATE('{$rowLocal->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $transactionId = $myDatabaseServer->insert_id;

                            $sql = "UPDATE `transaction` SET transaction_id = {$transactionId}, sync_status = 1 WHERE transaction_id = {$rowLocal->transaction_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        }
                    } else {
                        // error
                    }
                } elseif($rowLocal->sync_status == 2) {
                    
                    // update transaction
                    
                    if($boolStockpileContract && $boolLabor && $boolUnloadingCost && $boolFreightCost && $boolSupplier) {
                        if($rowLocal->t_payment_id != '') {
                            $paymentId = $rowLocal->t_payment_id;
                        }
                        
                        if($rowLocal->fc_payment_id != '') {
                            $fcPaymentId = $rowLocal->fc_payment_id;
                        }
                        
                        if($rowLocal->uc_payment_id != '') {
                            $ucPaymentId = $rowLocal->uc_payment_id;
                        }
                        
                        $sql = "UPDATE `transaction` SET "
                                . "stockpile_contract_id = {$stockpileContractId}, "
                                . "transaction_date = STR_TO_DATE('{$rowLocal->transaction_date}', '%Y-%m-%d'), "
                                . "loading_date = STR_TO_DATE('{$rowLocal->loading_date}', '%Y-%m-%d'), "
                                . "vehicle_no = '{$rowLocal->vehicle_no}', "
                                . "labor_id = {$laborId}, "
                                . "unloading_cost_id = {$unloadingCostId}, "
                                . "unloading_date = STR_TO_DATE('{$rowLocal->unloading_date}', '%Y-%m-%d'), "
                                . "freight_cost_id = {$freightCostId}, "
                                . "permit_no = '{$rowLocal->permit_no}', "
                                . "vendor_id = {$supplierId}, "
                                . "send_weight = {$rowLocal->send_weight}, "
                                . "bruto_weight = {$rowLocal->bruto_weight}, "
                                . "tarra_weight = {$rowLocal->tarra_weight}, "
                                . "netto_weight = {$rowLocal->netto_weight}, "
                                . "notes = '{$rowLocal->t_notes}', "
                                . "driver = '{$rowLocal->driver}', "
                                . "freight_quantity = {$rowLocal->freight_quantity}, "
                                . "quantity = {$rowLocal->t_quantity}, "
                                . "shrink = {$rowLocal->shrink}, "
                                . "freight_price = {$rowLocal->t_freight_price}, "
                                . "unloading_price = {$rowLocal->t_unloading_price}, "
                                . "unit_price = {$rowLocal->unit_price}, "
                                . "inventory_value = {$rowLocal->inventory_value}, "
                                . "block = '{$rowLocal->block}', "
                                . "delivery_status = {$rowLocal->delivery_status}, "
                                . "payment_id = {$paymentId}, "
                                . "fc_payment_id = {$fcPaymentId}, "
                                . "uc_payment_id = {$ucPaymentId}, "
                                . "sync_status = 1, "
                                . "modify_by = {$rowLocal->modify_by}, "
                                . "modify_date = STR_TO_DATE('{$rowLocal->modify_date}', '%Y-%m-%d %H:%i:%s') "
                                . "WHERE transaction_id = {$rowLocal->transaction_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $sql = "UPDATE `transaction` SET sync_status = 1 WHERE transaction_id = {$rowLocal->transaction_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        }
                    } else {
                        // error
                    }
                }
            } elseif($rowLocal->transaction_type == 2) {
                //OUT
                
                // <editor-fold defaultstate="collapsed" desc="sales customer">

                $sql = "SELECT customer_id FROM customer "
                        . "WHERE customer_name = '{$rowLocal->customer_name}'";
                $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                if($result->num_rows == 0) {
                    $sql = "SELECT * from customer "
                            . "WHERE customer_id = {$rowLocal->customer_id}";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "INSERT INTO customer (customer_id, customer_name, entry_by, entry_date) VALUES ("
                                . "{$rowLocal->customer_id}, '{$rowLocal->customer_name}', "
                                . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $boolCustomer = true;
                            $customerId = $rowLocal->customer_id;
                        } else {
                            // error
                        }
                    } else {
                        $sql = "INSERT INTO customer (customer_name, entry_by, entry_date) VALUES ("
                                . "'{$rowLocal->customer_name}', "
                                . "{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $boolCustomer = true;
                            $customerId = $myDatabaseServer->insert_id;

                            $sqlNext = "SELECT COALESCE(MAX(customer_id), 0) + 1 AS next_id FROM `customer`";
                            $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                            $rowNext = $resultNext->fetch_object();
                            $nextId = $rowNext->next_id;
                            $nextAuto = $nextId + 1;

                            $sql = "UPDATE customer SET customer_id = {$customerId} WHERE vendor_id = {$rowLocal->customer_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $sql = "alter table `customer` auto_increment = {$nextAuto}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            }
                        } else {
                            // error
                        }
                    }
                } else {
                    $boolCustomer = true;

                    $row = $result->fetch_object();

                    if($rowLocal->customer_id == $row->customer_id) {
                        $customerId = $rowLocal->customer_id;
                    } else {
                        $customerId = $row->customer_id;
                    }
                }

                // </editor-fold>
                
                // <editor-fold defaultstate="collapsed" desc="sales">

                if($boolCustomer) {
                    $sql = "SELECT sales_id FROM sales "
                            . "WHERE sales_no = '{$rowLocal->sales_no}' "
                            . "AND customer_id = {$customerId}";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                    if($result->num_rows == 0) {
                        $sql = "SELECT * from sales "
                                . "WHERE sales_id = {$rowLocal->sales_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO sales (sales_id, sales_no, sales_date, sales_type, customer_id, stockpile_id, loading, destination, "
                                    . "notes, currency_id, exchange_rate, price, price_converted, quantity, total_shipment, sales_status, "
                                    . "entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->sales_id}, '{$rowLocal->sales_no}', STR_TO_DATE('{$rowLocal->sales_date}', '%Y-%m-%d %H:%i:%s'), "
                                    . "{$rowLocal->sales_type}, {$customerId}, {$rowLocal->sl_stockpile_id}, '{$rowLocal->sl_loading}', "
                                    . "'{$rowLocal->sl_destination}', '{$rowLocal->sl_notes}', {$rowLocal->sl_currency_id}, {$rowLocal->sl_exchange_rate}, "
                                    . "{$rowLocal->sl_price}, {$rowLocal->sl_price_converted}, {$rowLocal->sl_quantity}, {$rowLocal->total_shipment}, "
                                    . "{$rowLocal->sales_status}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
//                                $boolSales = true;
                                $salesId = $rowLocal->sales_id;
                            } else {
                                // error
                            }
                        } else {
                            $sql = "INSERT INTO sales (sales_no, sales_date, sales_type, customer_id, stockpile_id, loading, destination, "
                                    . "notes, currency_id, exchange_rate, price, price_converted, quantity, total_shipment, sales_status, "
                                    . "entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->sales_no}', STR_TO_DATE('{$rowLocal->sales_date}', '%Y-%m-%d %H:%i:%s'), "
                                    . "{$rowLocal->sales_type}, {$customerId}, {$rowLocal->sl_stockpile_id}, '{$rowLocal->sl_loading}', "
                                    . "'{$rowLocal->sl_destination}', '{$rowLocal->sl_notes}', {$rowLocal->sl_currency_id}, {$rowLocal->sl_exchange_rate}, "
                                    . "{$rowLocal->sl_price}, {$rowLocal->sl_price_converted}, {$rowLocal->sl_quantity}, {$rowLocal->total_shipment}, "
                                    . "{$rowLocal->sales_status}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
//                                $boolSales = true;
                                $salesId = $myDatabaseServer->insert_id;

                                $sqlNext = "SELECT COALESCE(MAX(sales_id), 0) + 1 AS next_id FROM `sales`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE sales SET sales_id = {$salesId} WHERE sales_id = {$rowLocal->sales_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `sales` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                // error
                            }
                        }
                        
                        // add shipment
                        $sql = "SELECT * FROM shipment WHERE sales_id = {$salesId}";
                        $resultShip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                        $totalShipment = $resultShip->num_rows;
                        $syncShipment = 0;
                        
                        if($resultShip->num_rows > 0) {
                            while($rowShip = $resultShip->fetch_object()) {
                                $sql = "SELECT * FROM shipment "
                                        . "WHERE shipment_id = {$rowShip->shipment_id} AND shipment_code = '{$rowShip->shipment_code}'";
                                $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                                
                                $shipmentDate = "NULL";
                                $dpAmount = "NULL";
                                $cogsAmount = "NULL";
                                $invoiceAmount = "NULL";
                                $quantity = "NULL";
                                $paymentId = "NULL";
                                
                                if($rowShip->shipment_date != '') {
                                    $shipmentDate = $rowShip->shipment_date;
                                }
                                
                                if($rowShip->dp_amount != '') {
                                    $dpAmount = $rowShip->dp_amount;
                                }
                                
                                if($rowShip->cogs_amount != '') {
                                    $cogsAmount = $rowShip->cogs_amount;
                                }
                                
                                if($rowShip->invoice_amount != '') {
                                    $invoiceAmount = $rowShip->invoice_amount;
                                }
                                
                                if($rowShip->quantity != '') {
                                    $quantity = $rowShip->quantity;
                                }
                                
                                if($rowShip->payment_id != '') {
                                    $paymentId = $rowShip->payment_id;
                                }
                                
                                if($result->num_rows == 0) {
                                    $sql = "SELECT * FROM shipment WHERE shipment_id = {$rowShip->shipment_id}";
                                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                                    
                                    if($result->num_rows == 0) {
                                        $sql = "INSERT INTO shipment (shipment_id, shipment_code, shipment_date, sales_id, dp_amount, cogs_amount, "
                                                . "invoice_amount, quantity, shipment_status, payment_id, entry_by, entry_date) VALUES ("
                                                . "{$rowShip->shipment_id}, '{$rowShip->shipment_code}', STR_TO_DATE('{$shipmentDate}', '%Y-%m-%d %H:%i:%s'), "
                                                . "{$salesId}, {$dpAmount}, {$cogsAmount}, {$invoiceAmount}, {$quantity}, {$rowShip->shipment_status}, "
                                                . "{$paymentId}, {$rowShip->entry_by}, STR_TO_DATE('{$rowShip->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                                        
                                        if($result !== false) {
                                            $syncShipment = $syncShipment + 1;
                                        } 
                                    } else {
                                        $sql = "INSERT INTO shipment (shipment_code, shipment_date, sales_id, dp_amount, cogs_amount, "
                                                . "invoice_amount, quantity, shipment_status, payment_id, entry_by, entry_date) VALUES ("
                                                . "'{$rowShip->shipment_code}', STR_TO_DATE('{$shipmentDate}', '%Y-%m-%d %H:%i:%s'), "
                                                . "{$salesId}, {$dpAmount}, {$cogsAmount}, {$invoiceAmount}, {$quantity}, {$rowShip->shipment_status}, "
                                                . "{$paymentId}, {$rowShip->entry_by}, STR_TO_DATE('{$rowShip->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                                        
                                        if($result !== false) {
                                            $newShipmentId = $myDatabaseServer->insert_id;
                                            
                                            $sqlNext = "SELECT COALESCE(MAX(shipment_id), 0) + 1 AS next_id FROM `shipment`";
                                            $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                            $rowNext = $resultNext->fetch_object();
                                            $nextId = $rowNext->next_id;
                                            $nextAuto = $nextId + 1;

                                            $sql = "UPDATE shipment SET shipment_id = {$newShipmentId} WHERE shipment_id = {$rowShip->shipment_id}";
                                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                            if($result !== false) {
                                                $sql = "alter table `shipment` auto_increment = {$nextAuto}";
                                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                            }
                                            
                                            $syncShipment = $syncShipment + 1;
                                        }
                                    }
                                } else {
                                    $syncShipment = $syncShipment + 1;
                                }
                            }
                        }
                        
                        if($totalShipment == $syncShipment) {
                            $boolSales = true;
                        }
                    } else {
                        $boolSales = true;

                        $row = $result->fetch_object();

                        if($rowLocal->sales_id == $row->sales_id) {
                            $salesId = $rowLocal->sales_id;
                        } else {
                            $salesId = $row->sales_id;
                        }
                    }
                }

                // </editor-fold>
                
                // <editor-fold defaultstate="collapsed" desc="shipment">
                
                if($boolSales) {
                    $sql = "SELECT shipment_id FROM shipment WHERE shipment_code = '{$rowLocal->shipment_code}'";
                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                    
                    if($result->num_rows == 0) {
                        $sql = "SELECT shipment_id FROM shipment WHERE shipment_id = {$rowLocal->shipment_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                        
                        $shipmentDate = "NULL";
                        $dpAmount = "NULL";
                        $cogsAmount = "NULL";
                        $invoiceAmount = "NULL";
                        $quantity = "NULL";
                        $paymentId = "NULL";

                        if($rowLocal->shipment_date != '') {
                            $shipmentDate = $rowLocal->shipment_date;
                        }

                        if($rowLocal->sh_dp_amount != '') {
                            $dpAmount = $rowLocal->sh_dp_amount;
                        }

                        if($rowLocal->sh_cogs_amount != '') {
                            $cogsAmount = $rowLocal->sh_cogs_amount;
                        }

                        if($rowLocal->sh_invoice_amount != '') {
                            $invoiceAmount = $rowLocal->sh_invoice_amount;
                        }

                        if($rowLocal->sh_quantity != '') {
                            $quantity = $rowLocal->sh_quantity;
                        }

                        if($rowLocal->sh_payment_id != '') {
                            $paymentId = $rowLocal->sh_payment_id;
                        }

                        if($result->num_rows == 0) {
                            $sql = "INSERT INTO shipment (shipment_id, shipment_code, shipment_date, sales_id, dp_amount, cogs_amount, "
                                    . "invoice_amount, quantity, shipment_status, payment_id, entry_by, entry_date) VALUES ("
                                    . "{$rowLocal->shipment_id}, '{$rowLocal->shipment_code}', STR_TO_DATE('{$shipmentDate}', '%Y-%m-%d %H:%i:%s'), "
                                    . "{$salesId}, {$dpAmount}, {$cogsAmount}, {$invoiceAmount}, {$quantity}, {$rowLocal->shipment_status}, "
                                    . "{$paymentId}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $shipment_id = $rowLocal->shipment_id;
                                $boolShipment = true;
                            } else {
                                /// error
                            }
                        } else {
                            $sql = "INSERT INTO shipment (shipment_code, shipment_date, sales_id, dp_amount, cogs_amount, "
                                    . "invoice_amount, quantity, shipment_status, payment_id, entry_by, entry_date) VALUES ("
                                    . "'{$rowLocal->shipment_code}', STR_TO_DATE('{$shipmentDate}', '%Y-%m-%d %H:%i:%s'), "
                                    . "{$salesId}, {$dpAmount}, {$cogsAmount}, {$invoiceAmount}, {$quantity}, {$rowLocal->shipment_status}, "
                                    . "{$paymentId}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                            if($result !== false) {
                                $shipment_id = $myDatabaseServer->insert_id;
                                $boolShipment = true;

                                $sqlNext = "SELECT COALESCE(MAX(shipment_id), 0) + 1 AS next_id FROM `shipment`";
                                $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                $rowNext = $resultNext->fetch_object();
                                $nextId = $rowNext->next_id;
                                $nextAuto = $nextId + 1;

                                $sql = "UPDATE shipment SET shipment_id = {$shipmentId} "
                                . "WHERE shipment_id = {$rowLocal->shipment_id}";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {
                                    $sql = "alter table `shipment` auto_increment = {$nextAuto}";
                                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                }
                            } else {
                                /// error
                            }
                        }
                    } else {
                        $boolShipment = true;
                        
                        $row = $result->fetch_object();

                        if($rowLocal->shipment_id == $row->shipment_id) {
                            $shipmentId = $rowLocal->shipment_id;
                        } else {
                            $shipmentId = $row->shipment_id;
                        }
                    }
                }
                
                // </editor-fold>
                
                if($rowLocal->sync_status == 0) {
                    if($boolCustomer && $boolSales && $boolShipment) {
                        $sql = "INSERT INTO `transaction` (slip_no, product_id, shipment_id, transaction_date, "
                                . "vehicle_no, transaction_type, send_weight, quantity, shrink, inventory_value, "
                                . "sync_status, entry_by, entry_date) VALUES ("
                                . "'{$rowLocal->slip_no}', {$rowLocal->product_id}, {$shipmentId}, STR_TO_DATE('{$rowLocal->transaction_date}', '%Y-%m-%d'), "
                                . "'{$rowLocal->vehicle_no}', {$rowLocal->transaction_type}, {$rowLocal->send_weight}, {$rowLocal->t_quantity}, {$rowLocal->shrink}, "
                                . "{$rowLocal->inventory_value}, 1, {$rowLocal->entry_by}, STR_TO_DATE('{$rowLocal->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $transactionId = $myDatabaseServer->insert_id;

                            $sql = "UPDATE `transaction` SET transaction_id = {$transactionId}, sync_status = 1 WHERE transaction_id = {$rowLocal->transaction_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            
                            $boolDelivery = true;
                        }
                    }
                } elseif($rowLocal->sync_status == 2) {
                    if($boolCustomer && $boolSales && $boolShipment) {
                        $sql = "UPDATE `transaction` SET "
                                . "shipment_id = {$shipmentId}, "
                                . "transaction_date = STR_TO_DATE('{$rowLocal->transaction_date}', '%Y-%m-%d'), "
                                . "vehicle_no = '{$rowLocal->vehicle_no}', "
                                . "send_weight = {$rowLocal->send_weight}, "
                                . "quantity = {$rowLocal->t_quantity}, "
                                . "shrink = {$rowLocal->shrink}, "
                                . "inventory_value = {$rowLocal->inventory_value}, "
                                . "sync_status = 1, "
                                . "modify_by = {$rowLocal->modify_by}, "
                                . "modify_date = STR_TO_DATE('{$rowLocal->modify_date}', '%Y-%m-%d %H:%i:%s') "
                                . "WHERE transaction_id = {$rowLocal->transaction_id}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                        if($result !== false) {
                            $sql = "UPDATE `transaction` SET sync_status = 1 WHERE transaction_id = {$rowLocal->transaction_id}";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            
                            $boolDelivery = true;
                        }
                    }
                }
                
                if($boolDelivery) {
                    $sql = "SELECT * FROM delivery WHERE shipment_id = {$shipmentId}";
                    $resultDelivery = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                    if($resultDelivery->num_rows > 0) {
                        $deliveryIds = '';
                        while($rowDelivery = $resultDelivery->fetch_object()) {
                            if($deliveryIds == '') {
                                $deliveryIds = $rowDelivery->delivery_id;
                            } else {
                                $deliveryIds .= ','. $rowDelivery->delivery_id;
                            }

                            $sql = "SELECT * "
                                    . "FROM `delivery` "
                                    . "WHERE delivery_id = {$rowDelivery->delivery_id} "
                                    . "AND shipment_id = {$rowDelivery->shipment_id} "
                                    . "AND transaction_id = {$rowDelivery->transaction_id}";
                            $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                            if($result->num_rows == 0) {
                                $sql = "SELECT * FROM `delivery` WHERE delivery_id = {$rowDelivery->delivery_id}";
                                $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                                if($result->num_rows == 0) {
                                    $sql = "INSERT INTO `delivery` (delivery_id, shipment_id, transaction_id, delivery_date, "
                                            . "percent_taken, quantity, inventory_value, delivery_value, entry_by, entry_date) "
                                            . "VALUES ({$rowDelivery->delivery_id}, {$rowDelivery->shipment_id}, {$rowDelivery->transaction_id}, "
                                            . "STR_TO_DATE('{$rowDelivery->delivery_date}', '%Y-%m-%d %H:%i:%s'), {$rowDelivery->percent_taken}, "
                                            . "{$rowDelivery->quantity}, {$rowDelivery->inventory_value}, {$rowDelivery->delivery_value}, "
                                            . "{$rowDelivery->entry_by}, STR_TO_DATE('{$rowDelivery->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                                    if($result !== false) {

                                    } else {

                                    }
                                } else {
                                    $sqlNext = "SELECT COALESCE(MAX(delivery_id), 0) + 1 AS next_id FROM `delivery`";
                                    $resultNext = $myDatabaseServer->query($sqlNext, MYSQLI_STORE_RESULT);
                                    $rowNext = $resultNext->fetch_object();
                                    $nextId = $rowNext->next_id;
                                    $nextAuto = $nextId + 1;

                                    $sql = "UPDATE `delivery` SET delivery_id = {$nextId} WHERE delivery_id = {$rowDelivery->delivery_id}";
                                    $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                                    if($result !== false) {
                                        $sql = "INSERT INTO `delivery` (delivery_id, shipment_id, transaction_id, delivery_date, "
                                                . "percent_taken, quantity, inventory_value, delivery_value, entry_by, entry_date) "
                                                . "VALUES ({$rowDelivery->delivery_id}, {$rowDelivery->shipment_id}, {$rowDelivery->transaction_id}, "
                                                . "STR_TO_DATE('{$rowDelivery->delivery_date}', '%Y-%m-%d %H:%i:%s'), {$rowDelivery->percent_taken}, "
                                                . "{$rowDelivery->quantity}, {$rowDelivery->inventory_value}, {$rowDelivery->delivery_value}, "
                                                . "{$rowDelivery->entry_by}, STR_TO_DATE('{$rowDelivery->entry_date}', '%Y-%m-%d %H:%i:%s'))";
                                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                                        if($result !== false) {

                                        } else {

                                        }

                                        $sql = "alter table `delivery` auto_increment = {$nextAuto}";
                                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                                    } else {

                                    }
                                }
                            } else {
                                $sql = "UPDATE `delivery` SET "
                                        . "delivery_date = STR_TO_DATE('{$rowDelivery->delivery_date}', '%Y-%m-%d %H:%i:%s'), "
                                        . "percent_taken = {$rowDelivery->percent_taken}, "
                                        . "quantity = {$rowDelivery->quantity}, "
                                        . "inventory_value = {$rowDelivery->inventory_value}, "
                                        . "delivery_value = {$rowDelivery->delivery_value} "
                                        . "WHERE delivery_id = {$rowDelivery->delivery_id}";
                                $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);

                                if($result !== false) {

                                } else {

                                }
                            }
                        }

                        $sql = "DELETE FROM delivery WHERE delivery_id NOT IN ({$deliveryIds}) AND shipment_id = {$shipmentId}";
                        $result = $myDatabaseServer->query($sql, MYSQLI_STORE_RESULT);
                    }
                }
            }
        }
        
        // alter auto_increment local
    }
    
    $return_value = '|OK|'. $syncMessage .'|';
    
    echo $return_value;
    
}