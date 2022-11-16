<?php

require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'switch') {
    $return_value = '';
    
    if($_SESSION['companyId'] == 2) {
        $_SESSION['companyId'] = 1;
    } else {
        $_SESSION['companyId'] = 2;
    }
    
    echo $return_value = 'OK';
}