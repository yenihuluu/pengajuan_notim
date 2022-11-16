<?php

if(!isset($_SESSION['isLogin']) && $_SESSION['isLogin'] == 0) {
    header('location: '. $urlProtocol .'://'. $urlHost .'/login.php');
    exit();
}
?>
